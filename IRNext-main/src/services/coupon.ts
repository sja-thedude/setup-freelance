import { api } from "@/utils/axios"; 
import { notFound } from 'next/navigation';
import  { AxiosError } from 'axios';
import { cloneDeep, sortBy } from "lodash";

export async function getCouponsList({ workspaceId }: { workspaceId: number }) {
    try {
        const response = await api.get(`coupons?workspace_id=${workspaceId}`);
    
        if (!response) {
            return notFound()
        }

        return response?.data?.data
    } catch (error ) {
        const err = error as AxiosError

         if (err instanceof Error) {
            if (err.response && err.response.status === 404) {
                return notFound()
            } else {
                return null;
            }
        }
    }
}

export function applyCouponIncludeVatLogic(
    cartData: any, 
    orderDataType: any,
    cartCoupon: any, 
    cartValidCouponProductIds: any,
    groupOrder: any,
    groupOrderNowSlice: any,
    currentAvailableDiscount: any,
    cartRedeemId: any
) {
    const orderTypesList: any = [
        'take_out',
        'delivery',
        'in_house',
    ];

    const cartDataClone = cloneDeep(cartData);
    const itemsConvert = sortBy(cartDataClone, ['product.data.vat.'+ orderTypesList[orderDataType]])?.map((item: any) => {
        let discount = 0;
        let available_discount = false;

        if (cartCoupon && cartCoupon.code) {
          if (cartValidCouponProductIds && cartValidCouponProductIds.includes(String(item.productId))) {
            available_discount = true;
          }
        } else if (cartValidCouponProductIds && cartValidCouponProductIds.includes(item.productId)) {
          available_discount = true;
        }
        
        if (!available_discount) {
          if (groupOrder && groupOrderNowSlice) {
            available_discount = groupOrder.discount_type > 0 ? true : false;
          }
        }            

        const totolPriceItem = item.basePrice * item.productTotal;
        if (available_discount && currentAvailableDiscount > 0) {
            if (totolPriceItem >= currentAvailableDiscount) {
                discount = currentAvailableDiscount;
                currentAvailableDiscount -= discount;
            } else {
                discount = totolPriceItem;
                currentAvailableDiscount -= discount;
            }
        }
     
        return {
            product_id: item.productId,
            quantity: item.productTotal,
            available_discount: (groupOrder && groupOrderNowSlice) ? ((groupOrder?.discount_type > 0) ? true : 'false') : available_discount,
            discount: available_discount ? discount : null,
            redeem_history_id: cartCoupon && !cartCoupon?.code ? cartRedeemId : null,
            coupon_id: cartCoupon && cartCoupon?.code && cartValidCouponProductIds && cartValidCouponProductIds.includes(String(item.productId)) ? cartCoupon?.id : null,
            options: item.optionItemsStore?.map((op: any) => {
                const iSelectedMaster = op.optionItems.find((io: any) => io.master);

                if (iSelectedMaster) {
                    return {
                        option_id: op.optionId,
                        option_items: [{ option_item_id: iSelectedMaster.id }]
                    };
                }

                return {
                    option_id: op.optionId,
                    option_items: op.optionItems.map((it: any) => ({
                        option_item_id: it.id
                    }))
                };
            })
        };
    });

    const newArr: any = [];

    cartData?.map((item: any) => {
        const itemConvert = itemsConvert.find((it: any) => {
            if (it.product_id !== item.productId) return false;
            if (it.options.length !== item.optionItemsStore.length) return false;
            return it.options.every((opt: any, index: number) => {
                const itemOpt = item.optionItemsStore[index];
                if (opt.option_id !== itemOpt.optionId) return false;
                if (compareOptionItemsWithOrigin(opt.option_items, itemOpt.optionItems) === false) return false;
                return opt.option_items.every((optItem: any) => 
                    itemOpt.optionItems.find((io: any) => io.id === optItem.option_item_id)
                );
            });
        });
        if (itemConvert) {
            newArr.push(itemConvert);
        }
    });

    return newArr;
}

export function compareOptionItemsWithOrigin(searchItems: any, originItems: any) {
    if (searchItems.length !== originItems.length) {
        let flag = false;

        searchItems.map((item: any) => {
            const itemDoubleCheck = originItems.find((i: any) => i.id === item.option_item_id)

            if(itemDoubleCheck?.master === true) {
                flag = true;
            }
        });

        return flag;
    }

    return true;
}