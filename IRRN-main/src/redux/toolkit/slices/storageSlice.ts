import { sortBy } from 'lodash';

import { createSlice } from '@reduxjs/toolkit';
import {
    LOCALES,
    ORDER_TYPE,
} from '@src/configs/constants';
import { UserDataModel } from '@src/network/dataModels';
import { CouponDetailModal } from '@src/network/dataModels/CouponDetailModal';
import { DeliveryConditionModel } from '@src/network/dataModels/DeliveryConditionModel';
import { GooglePlaceAddressResultModel, } from '@src/network/dataModels/GooglePlaceAddressResultModel';
import { GroupAppDetailModel } from '@src/network/dataModels/GroupAppDetailModel';
import { GroupDetailModel } from '@src/network/dataModels/GroupDetailModel';
import { RewardData } from '@src/network/dataModels/MyRedeemModel';
import { ProductDetailModel } from '@src/network/dataModels/ProductDetailModel';
import { ProductOptionModel } from '@src/network/dataModels/ProductOptionModel';
import { RestaurantDetailModel } from '@src/network/dataModels/RestaurantDetailModel';
import { WorkspaceSettingModel } from '@src/network/dataModels/WorkspaceSettingModel';
import { compareProductsOptions } from '@src/utils';

export interface ProductInCart extends ProductDetailModel {
    quantity: number,
    options: Array<ProductOptionModel> | null,
    discount?: number | undefined,
    cartPrice?: number | undefined,
    cartPosition?: number | undefined,
    isError?: boolean,
}

interface State {
    showWalkThrough: boolean,
    userData: UserDataModel,
    recentAddress: Array<GooglePlaceAddressResultModel>,
    cartProducts: {
        data: Array<ProductInCart>,
        type: number, // for filter product by order type
        groupFilter: { // for filter product by group
            filterByDeliverable: boolean,
            groupData: GroupDetailModel | null
        },
        restaurant: {
            id: number | null,
            name: string,
            title: string,
        },
        deliveryInfo: {
            deliveryAddress: {
                address: string | null,
                addressType: number | null,
                lat: number | null,
                lng: number | null
            },
            settingDeliveryConditionId: number | null,
            deliveryFee: DeliveryConditionModel | null
        },
        discountInfo: {
            discountType: string | null,
            discount: RewardData & {redeem_id: number} & GroupDetailModel & CouponDetailModal | null,
            applicableProducts: Array<ProductInCart>,
        },
        serviceCostInfo: {
            isServiceCostOn?: boolean,
            isServiceCostAlwaysCharge?: boolean,
            serviceCost?: number,
            serviceCostAmount?: number,
        }
    },
    onlinePaymentProcessingOrder: number | null,
    templateWorkspaceDetail: RestaurantDetailModel | null,
    templateWorkspaceSetting: WorkspaceSettingModel | null,
    groupAppDetail: GroupAppDetailModel | null,
    showTooltipTime: number,
    language: string,
    workspaceLanguages: string[]
}

const initialState: State = {
    showWalkThrough: true,
    userData: null,
    recentAddress: [],
    cartProducts: {
        data: [],
        type: ORDER_TYPE.TAKE_AWAY,
        groupFilter: {
            filterByDeliverable: false,
            groupData: null
        },
        restaurant: {
            id: null,
            name: '',
            title: '',
        },
        deliveryInfo: {
            deliveryAddress: {
                address: null,
                addressType: null,
                lat: null,
                lng: null
            },
            settingDeliveryConditionId: null,
            deliveryFee: null
        },
        discountInfo: {
            discountType: null,
            discount: null,
            applicableProducts: [],
        },
        serviceCostInfo: {
            isServiceCostOn: false,
            isServiceCostAlwaysCharge: false,
            serviceCost: 0,
            serviceCostAmount: 0,
        }
    },
    onlinePaymentProcessingOrder: null,
    templateWorkspaceDetail: null,
    templateWorkspaceSetting: null,
    groupAppDetail: null,
    showTooltipTime: 0,
    language: LOCALES.NL,
    workspaceLanguages: ['nl'],
};

const storageSlice = createSlice({
    name: 'storage',
    initialState,
    reducers: {
        setShowWalkThrough: (state, { payload }) => {
            state.showWalkThrough = payload;
        },
        setStorageUserData: (state, { payload }) => {
            state.userData = payload;
        },
        removeStorageUserData: (state) => {
            state.userData = null;
        },
        setStorageAddress: (state, { payload }) => {
            state.recentAddress = payload;
        },

        setStorageProductsCart: (state, { payload } : {payload: ProductInCart}) => {
            const productsInCart = state.cartProducts.data;
            let newData = state.cartProducts.data;

            // soft product's option && option item by default order
            const sortedOptions = sortBy(payload.options || [], 'optionOrder');
            const orderedPayload = { ...payload, options: sortedOptions.map((op) => ({
                ...op,
                items: sortBy(op.items || [], 'order')
            })) };

            if (productsInCart.length === 0) {
                newData = [orderedPayload];
            } else {
                // find all the same products
                const alreadyInCart = productsInCart.filter((item) => item.id === orderedPayload.id);

                if (alreadyInCart.length === 0) {
                    newData = [...productsInCart, orderedPayload];
                } else {
                    // check if product options are the same
                    const isSameOptions = alreadyInCart.find((p) => compareProductsOptions(p.options, orderedPayload.options));

                    if (isSameOptions) {
                        // if same options, increase quantity of product in cart
                        newData = productsInCart.map((item) => {
                            if (item.cartPosition === isSameOptions.cartPosition) {
                                return {
                                    ...item,
                                    quantity: item.quantity + orderedPayload.quantity,
                                    options: orderedPayload.options,
                                    price: orderedPayload.price,
                                };
                            }

                            return item;
                        });
                    } else {
                        // add new item to the cart
                        newData = [...productsInCart, orderedPayload];
                    }
                }
            }

            state.cartProducts.data = newData.map((p, index) => ({ ...p, cartPosition: index }));
            state.cartProducts.restaurant.id = orderedPayload.workspace.id;
            state.cartProducts.restaurant.name = orderedPayload.workspace.title;
            state.cartProducts.restaurant.title = orderedPayload.workspace.title;
        },
        setStorageMultiProductsCart: (state, { payload } : {payload: ProductInCart[]}) => {
            if (payload.length) {
                // soft product's option item by default order
                const orderedPayload = payload.map((p, index) => {
                    const sortedOptions = sortBy(p.options || [], 'optionOrder');
                    return {
                        ...p,
                        cartPosition: index,
                        options: sortedOptions.map((op) => ({
                            ...op,
                            items: sortBy(op.items || [], 'order')
                        }))
                    };
                }
                );

                state.cartProducts.data = orderedPayload;
                state.cartProducts.restaurant.id = orderedPayload[0].workspace.id;
                state.cartProducts.restaurant.name = orderedPayload[0].workspace.title;
                state.cartProducts.restaurant.title = orderedPayload[0].workspace.title;
            }
        },
        updateProductCartNewPrice: (state, { payload }) => {
            const productsInCart = state.cartProducts.data;
            let newData = state.cartProducts.data;

            const { productId, newPrice } = payload;

            newData = productsInCart.map((item) => {
                if (productId === item.id && newPrice !== item.price) {
                    return {
                        ...item,
                        price: newPrice,
                    };
                }

                return item;
            });

            state.cartProducts.data = newData;
        },
        changeProductCartQuantity: (state, { payload }) => {
            const productsInCart = state.cartProducts.data;
            let newData = state.cartProducts.data;

            const { productIndex, quantity } = payload;

            newData = productsInCart.map((item, index) => {
                if (productIndex === index) {
                    return {
                        ...item,
                        quantity: quantity
                    };
                }

                return item;
            });

            state.cartProducts.data = newData;
        },
        removeProductItemCart: (state, { payload }) => {
            const productsInCart = state.cartProducts.data;
            let newData = state.cartProducts.data;

            const { productIndex } = payload;

            newData = productsInCart.filter((item, index) => productIndex !== index);

            if (newData.length === 0) {
                state.cartProducts = initialState.cartProducts;
            } else {
                state.cartProducts.data = newData.map((p, index) => ({ ...p, cartPosition: index }));
            }
        },
        setStorageCartType: (state, { payload }) => {
            state.cartProducts.type = payload;
        },
        clearStorageCartType: (state) => {
            state.cartProducts.type = initialState.cartProducts.type;
        },
        setStorageGroupFilter: (state, { payload }) => {
            state.cartProducts.groupFilter.groupData = payload.data;
            state.cartProducts.groupFilter.filterByDeliverable = payload.filterByDeliverable;
        },
        clearStorageGroupFilter: (state) => {
            state.cartProducts.groupFilter = initialState.cartProducts.groupFilter;
        },
        setStorageDeliveryInfo: (state, { payload }) => {
            state.cartProducts.deliveryInfo.deliveryAddress.address = payload.address;
            state.cartProducts.deliveryInfo.deliveryAddress.addressType = payload.addressType;
            state.cartProducts.deliveryInfo.deliveryAddress.lat = payload.lat;
            state.cartProducts.deliveryInfo.deliveryAddress.lng = payload.lng;
            state.cartProducts.deliveryInfo.settingDeliveryConditionId = payload.settingDeliveryConditionId;
            state.cartProducts.deliveryInfo.deliveryFee = payload.deliveryFee;
        },
        clearStorageDeliveryInfo: (state) => {
            state.cartProducts.deliveryInfo = initialState.cartProducts.deliveryInfo;
        },
        setStorageProcessingOrder: (state, { payload }) => {
            state.onlinePaymentProcessingOrder = payload;
        },
        clearStorageProcessingOrder: (state) => {
            state.onlinePaymentProcessingOrder = initialState.onlinePaymentProcessingOrder;
        },
        setStorageDiscount: (state, { payload }) => {
            state.cartProducts.discountInfo.discountType = payload.discountType;
            state.cartProducts.discountInfo.discount = payload.discount;
        },
        setStorageDiscountProducts: (state, { payload }) => {
            state.cartProducts.discountInfo.applicableProducts = payload;
        },
        clearStorageDiscount: (state) => {
            state.cartProducts.discountInfo = initialState.cartProducts.discountInfo;
        },

        clearStorageProductsCart: (state) => {
            state.cartProducts = initialState.cartProducts;
        },

        setStorageWorkspaceDetail: (state, { payload }) => {
            state.templateWorkspaceDetail = payload;
        },
        setStorageWorkspaceSetting: (state, { payload }) => {
            state.templateWorkspaceSetting = payload;
        },

        setStorageGroupAppDetail: (state, { payload }) => {
            state.groupAppDetail = payload;
        },

        setStorageTooltipTime: (state, { payload }) => {
            state.showTooltipTime = payload;
        },

        setStorageLanguage: (state, { payload }) => {
            state.language = payload;
        },

        setStorageWorkspaceLanguages: (state, { payload }) => {
            state.workspaceLanguages = payload;
        },

        setStorageServiceCost: (state, { payload }) => {
            state.cartProducts.serviceCostInfo = payload;
        },
        clearStorageServiceCost: (state) => {
            state.cartProducts.serviceCostInfo = initialState.cartProducts.serviceCostInfo;
        },

    },
});

export default storageSlice;
