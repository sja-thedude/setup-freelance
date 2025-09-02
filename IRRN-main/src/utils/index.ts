import { TFunction } from 'i18next';
import {
    isEmpty,
    isEqual,
    isNull,
    isUndefined,
    orderBy,
    sortBy,
} from 'lodash';
import {
    Linking,
    Platform,
} from 'react-native';
import Config from 'react-native-config';
import InAppBrowser from 'react-native-inappbrowser-reborn';

import Toast from '@src/components/toast/Toast';
import {
    CART_DISCOUNT_TYPE,
    EMAIL_REGEX,
    IS_ANDROID,
    MAX_PHONE_LENGTH,
    MIN_PHONE_LENGTH,
    ORDER_TYPE,
    PAYMENT_METHOD,
    PAYMENT_STATUS,
    VALUE_DISCOUNT_TYPE,
    VAT_TYPE,
} from '@src/configs/constants';
import { DeliveryConditionModel } from '@src/network/dataModels/DeliveryConditionModel';
import { GroupDetailModel } from '@src/network/dataModels/GroupDetailModel';
import { ProductDetailModel } from '@src/network/dataModels/ProductDetailModel';
import { ProductOptionModel } from '@src/network/dataModels/ProductOptionModel';
import { ProductInCart } from '@src/redux/toolkit/slices/storageSlice';

import { logError } from './logger';

export const isEven = (num: number) => num % 2 === 0;

export const formatPrice = (num: string | number = '') => {
    if (!num) {
        return '0';
    }

    num = Number(num) % 1 !== 0 ? Number(num)?.toFixed(2) : num;
    num = String(num);

    if (typeof num === 'number' || typeof num === 'string') {
        num = num.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1,');
    }
    return num;
};

export function nFormatter(num: number | string, digits: number) {
    if (!num) {
        return '0';
    }

    num = Number(num);

    var si = [
        { value: 1, symbol: '' },
        { value: 1e3, symbol: 'k' },
        { value: 1e6, symbol: 'tr' },
        { value: 1e9, symbol: 'G' },
        { value: 1e12, symbol: 'T' },
        { value: 1e15, symbol: 'P' },
        { value: 1e18, symbol: 'E' },
    ];
    var rx = /\.0+$|(\.[0-9]*[1-9])0+$/;
    var i;
    for (i = si.length - 1; i > 0; i--) {
        if (num >= si[i].value) {
            break;
        }
    }
    return (num / si[i].value).toFixed(digits).replace(rx, '$1') + ' ' + si[i].symbol;
}

export const capitalizeFirstLetter = (text: string) => text.charAt(0).toUpperCase() + text?.toLowerCase().slice(1);

export const callPhone = (text?: string) => Linking.openURL(`tel:${text}`);

export const convertDateYYYYMMDD = (value: string | number) => {
    value = String(value);
    const year = value.substring(0, 4);
    const month = value.substring(4, 6);
    const day = value.substring(6, 8);

    const date = new Date(Number(year), Number(month) - 1, Number(day));

    return date;
};

export const checkUrlImage = (url: string) => url.match(/\.(jpeg|jpg|gif|png|heic)$/) != null;
export const checkUrlFile = (url: string) =>
    url.match(/\.(doc|docx|xls|xlsx|ppt|pptx|pdf|txt|csv|jpeg|jpg|gif|png|heic)$/) != null;
export const getFileNameUrl = (url: string) => url?.split('/')?.pop()?.split('#')[0]?.split('?')[0];

export const handleOpenLink = async (url: string) => {
    try {
        Linking.openURL(url);
    } catch (error) {
        Toast.showToast(error);
    }
};

export const checkIsValue = (value?: number | string) => value !== undefined && value !== null && value !== '';

export const isEmptyOrUndefined = (value: any) => isEmpty(value) || isUndefined(value) || isNull(value);

export const validatePhone = (phone: any) => {
    if (!phone || !phone.startsWith('+')) {
        return false;
    }

    // remove all / and + character
    let phoneNumb = phone.split('/').join('').split('+').join('');

    // remove all leading zero
    while (phoneNumb.charAt(2) === '0') {
        phoneNumb = phoneNumb.replace('0', '');
    }
    if (phoneNumb.length > MAX_PHONE_LENGTH || phoneNumb.length < MIN_PHONE_LENGTH) {
        return false;
    }

    return true;
};

export const getPhoneAreaCode = (phone: any) => {
    if (!phone) {
        return '+32';
    }

    if (phone.startsWith('+31')) {
        return '+31';
    }

    return '+32';
};

export const removePhoneLeadingZero = (phone: any) => {
    if (!phone) {
        return '';
    }

    try {
        let phoneNumb = phone;
        if (phone.charAt(3) === '0') {
            while (phoneNumb.charAt(3) === '0') {
                phoneNumb = phoneNumb.split('');

                phoneNumb.splice(3, 1);
                phoneNumb = phoneNumb.join('');
            }

            return phoneNumb;
        }
    } catch (error) {
        logError('removePhoneLeadingZero', error);
    }

    return phone;
};

export const validateEmail = (email: any) => EMAIL_REGEX.test(email);

export const compareArrays = (a: any, b:any) => isEqual(a, b);

export   const convertHexToRGBA = (hexCode: string, opacity = 1) => {
    let hex = hexCode.replace('#', '');

    if (hex.length === 3) {
        hex = `${hex[0]}${hex[0]}${hex[1]}${hex[1]}${hex[2]}${hex[2]}`;
    }

    const r = parseInt(hex.substring(0, 2), 16);
    const g = parseInt(hex.substring(2, 4), 16);
    const b = parseInt(hex.substring(4, 6), 16);

    /* Backward compatibility for whole number based opacity values. */
    if (opacity > 1 && opacity <= 100) {
        opacity = opacity / 100;
    }

    return `rgba(${r},${g},${b},${opacity})`;
};

export const openInAppBrowser = async (url = '') => {
    if (url) {
        try {
            if ((await InAppBrowser.isAvailable())) {
                await InAppBrowser.open(url, {
                    // iOS Properties
                    dismissButtonStyle: 'done',
                    readerMode: false,
                    animated: true,
                    modalPresentationStyle: 'fullScreen',
                    modalTransitionStyle: 'coverVertical',
                    modalEnabled: true,
                    enableBarCollapsing: false,
                    // Android Properties
                    showTitle: false,
                    enableUrlBarHiding: true,
                    enableDefaultShare: false,
                    forceCloseOnRedirection: false,
                });
            } else {
                Linking.canOpenURL(url).then((isSupport) => {
                    if (isSupport) {
                        Linking.openURL(url);
                    }
                });
            }
        } catch (error) {
            Linking.canOpenURL(url).then((isSupport) => {
                if (isSupport) {
                    Linking.openURL(url);
                }
            });
        }
    }
};

export const getOrderType = (type?: number, t?: TFunction) => {
    switch (type) {
        case ORDER_TYPE.DELIVERY:
            return t && t('text_delivery') || '';
        case ORDER_TYPE.TAKE_AWAY:
            return t && t('text_pick_up') || '';
        case ORDER_TYPE.GROUP_ORDER:
            return t && t('options_group_orders') || '';
        default:
            return '';
    }
};

export const getPaymentStatus = (status?: number, method?: number, isTestAccount?: boolean, t?: TFunction) => {
    if (isTestAccount && method === PAYMENT_METHOD.MOLLIE && status !== PAYMENT_STATUS.PAID) {
        return t && t('text_order_paid') || '';
    } else {
        switch (status) {
            case PAYMENT_STATUS.PENDING:
                return t && t('text_to_pay') || '';
            case PAYMENT_STATUS.PAID:
                return t && t('text_order_paid') || '';
            case PAYMENT_STATUS.CANCEL:
                return t && t('text_order_cancel') || '';
            case PAYMENT_STATUS.FAILED:
                return t && t('text_order_failed') || '';
            default:
                return t && t('text_to_pay') || '';
        }
    }
};

export const getPaymentMethod = (method?: number, t?: TFunction) => {
    switch (method) {
        case PAYMENT_METHOD.MOLLIE:
            return t && t('payment_method_online') || '';
        case PAYMENT_METHOD.INVOICE:
            return t && t('payment_method_op_factuur') || '';
        case PAYMENT_METHOD.CASH:
            return t && t('payment_method_cash') || '';
        default:
            return '';
    }
};

export const getOrderSortData = (t: TFunction) => [
    {
        title: t('text_sort_standard'),
        type: 1
    },
    {
        title: t('text_sort_price_low_to_high'),
        type: 2
    },
    {
        title: t('text_sort_name_a_to_z'),
        type: 3
    },
];

export const openMap = (lat: number, lng: number, label: string) => {
    const scheme = Platform.select({
        ios: 'maps:0,0?q=',
        android: 'geo:0,0?q=',
    });

    const latLng = `${lat},${lng}`;

    const url = IS_ANDROID ? `${scheme}${latLng}(${label})` : `${scheme}${label}@${latLng}`;

    const googleMapURL = `comgooglemaps://?q=${lat},${lng}`;

    if (IS_ANDROID) {
        Linking.openURL(url);
    } else {
        Linking.canOpenURL(googleMapURL)
                .then((canOpen) => {
                    if (canOpen) {
                        Linking.openURL(googleMapURL);
                    } else {
                        Linking.openURL(url);
                    }
                });
    }
};

export const calculateProductPrice = (product: ProductInCart) => {
    let itemPrice = Number(product.price);

    product.options?.filter((op) => op.items.length > 0).map((option) => {
        const iSelectedMaster = option.items.find((io) => io.master);

        if (iSelectedMaster) {
            itemPrice = itemPrice + Number(iSelectedMaster.price);
        } else {
            option.items.map((i) => {
                itemPrice = itemPrice + Number(i.price);
            });
        }

    });

    itemPrice = itemPrice * product.quantity;

    return itemPrice;
};

const calculateDiscount = (discountValue: number, discountValueType: number, applicableProducts: Array<ProductInCart>, vatType: string) => {
    let originDiscount = discountValue || 0;
    let recentDiscount = originDiscount;
    let productsWithDiscount: Array<ProductInCart> = [];

    if (originDiscount > 0) {
        const sortedApplicableProducts = orderBy(
                applicableProducts,
                [vatType === VAT_TYPE.DELIVERY ? 'vat.delivery' : 'vat.take_out', 'cartPrice'],
                ['asc', 'desc']
        );

        if (discountValueType ===  VALUE_DISCOUNT_TYPE.PERCENTAGE) {
            let totalProductsPrice = 0;
            applicableProducts?.map((product) => {
                totalProductsPrice = totalProductsPrice + (product.cartPrice || 0);
            });

            originDiscount = (totalProductsPrice / 100) * (discountValue || 0);
            recentDiscount = originDiscount;
        }

        productsWithDiscount = sortedApplicableProducts.map((p) => {
            if (recentDiscount > 0) {
                recentDiscount = recentDiscount - (p.cartPrice || 0);
                return {
                    ...p,
                    discount: recentDiscount >= 0 ? p.cartPrice : (p.cartPrice || 0) - (recentDiscount * -1)
                };
            }

            return p;
        });
    }

    if (recentDiscount <= 0) {
        return {
            originDiscount: originDiscount,
            productsWithDiscount: productsWithDiscount
        };
    } else {
        return {
            originDiscount: originDiscount - recentDiscount,
            productsWithDiscount: productsWithDiscount
        };

    }
};

export const getOrderPrices = (
        cartProducts?: Array<ProductInCart>,
        deliveryFee?: DeliveryConditionModel | null,
        discountInfo?: any,
        groupData?: GroupDetailModel | null,
        orderType?: number,
        isServiceCostOn?: boolean,
        isServiceCostAlwaysCharge?: boolean,
        serviceCost?: number,
        serviceCostAmount?: number,
) => {
    let subTotal = 0;
    let groupDiscount = 0;
    let loyaltyDiscount = 0;
    let couponDiscount = 0;
    let deliveryPrice = Number(deliveryFee?.price || 0);
    let deliveryFree = Number(deliveryFee?.free || 0);
    let serviceCostPrice = 0;
    let total = 0;

    let mProductsWithDiscount: Array<ProductInCart> = [];

    cartProducts?.map((product) => {
        subTotal = subTotal + calculateProductPrice(product);
    });

    if (discountInfo) {
        const { discountType, discount, applicableProducts } = discountInfo;

        if (applicableProducts.length > 0) {
            let addedPriceApplicableProducts = applicableProducts.map((p: ProductInCart) => ({
                ...p,
                cartPrice: calculateProductPrice(p)
            }));

            switch (discountType) {
                case CART_DISCOUNT_TYPE.GROUP_DISCOUNT:
                    {
                        const vatType = groupData?.type === ORDER_TYPE.TAKE_AWAY ? VAT_TYPE.TAKE_OUT : VAT_TYPE.DELIVERY;
                        const { originDiscount, productsWithDiscount } = calculateDiscount(
                            discount?.discount_type === VALUE_DISCOUNT_TYPE.PERCENTAGE ? discount?.percentage : discount?.discount,
                            discount?.discount_type,
                            addedPriceApplicableProducts,
                            vatType
                        );

                        groupDiscount = originDiscount;
                        mProductsWithDiscount = productsWithDiscount;
                    }
                    break;

                case CART_DISCOUNT_TYPE.LOYALTY_DISCOUNT:
                    {
                        const vatType = orderType === ORDER_TYPE.TAKE_AWAY ? VAT_TYPE.TAKE_OUT : VAT_TYPE.DELIVERY;
                        const { originDiscount, productsWithDiscount } = calculateDiscount(
                            discount?.discount_type === VALUE_DISCOUNT_TYPE.PERCENTAGE ? discount?.percentage : Number(discount?.reward || 0),
                            discount?.discount_type,
                            addedPriceApplicableProducts,
                            vatType
                        );

                        loyaltyDiscount = originDiscount;
                        mProductsWithDiscount = productsWithDiscount;
                    }
                    break;

                case CART_DISCOUNT_TYPE.COUPON_DISCOUNT:
                    {
                        const vatType = orderType === ORDER_TYPE.TAKE_AWAY ? VAT_TYPE.TAKE_OUT : VAT_TYPE.DELIVERY;
                        const { originDiscount, productsWithDiscount } = calculateDiscount(
                            discount?.discount_type === VALUE_DISCOUNT_TYPE.PERCENTAGE ? discount?.percentage : Number(discount?.discount || 0),
                            discount?.discount_type,
                            addedPriceApplicableProducts,
                            vatType
                        );

                        couponDiscount = originDiscount;
                        mProductsWithDiscount = productsWithDiscount;
                    }
                    break;

                default:
                    break;
            }
        }

    }

    if (subTotal >= deliveryFree) {
        deliveryPrice = 0;
    }

    if (isServiceCostOn && orderType !== ORDER_TYPE.GROUP_ORDER) {
        if (isServiceCostAlwaysCharge) {
            serviceCostPrice = serviceCost || 0;
        } else {
            if (subTotal < (serviceCostAmount || 0)) {
                serviceCostPrice = serviceCost || 0;
            } else {
                serviceCostPrice = 0;
            }
        }
    }

    total = subTotal - groupDiscount - couponDiscount - loyaltyDiscount + deliveryPrice + serviceCostPrice;

    return {
        subTotal: subTotal.toFixed(2),
        loyaltyDiscount: loyaltyDiscount.toFixed(2),
        groupDiscount: groupDiscount.toFixed(2),
        couponDiscount: couponDiscount.toFixed(2),
        deliveryFee: deliveryPrice.toFixed(2),
        total: total > 0 ? total.toFixed(2) : '0.00',
        serviceCostPrice: serviceCostPrice.toFixed(2),
        productsWithDiscount: mProductsWithDiscount
    };
};

export const checkAvailableProductForGroup = (groupData: GroupDetailModel | null, product: ProductInCart | ProductDetailModel) => {
    let isNotForSale: any = false;

    if (groupData?.is_product_limit !== 0) {
        isNotForSale = groupData && (groupData?.type === ORDER_TYPE.TAKE_AWAY || groupData?.type === ORDER_TYPE.DELIVERY) && groupData?.products.findIndex((prod) => prod.id === product?.id) < 0;
    }
    const isNotDelivery = (groupData?.type === ORDER_TYPE.DELIVERY) && !product?.category.available_delivery;

    return { isNotForSale, isNotDelivery };
};

export const compareProductsOptions = (options1: any, options2: any) => {
    const opt1 = options1.map((o: ProductOptionModel) => ({
        id: o.id,
        items: sortBy(o.items.map((oi) => ({ id: oi.id })), 'id')
    }));

    const opt2 = options2.map((o: ProductOptionModel) => ({
        id: o.id,
        items: sortBy(o.items.map((oi) => ({ id: oi.id })), 'id')
    }));

    return isEqual(sortBy(opt1, 'id'), sortBy(opt2, 'id'));
};

export const isTemplateApp = () => Config.ENV_TEMPLATE_APP === 'true';

export const isGroupApp = () => Config.ENV_GROUP_APP === 'true';

export const isTemplateOrGroupApp = () => isTemplateApp() || isGroupApp();

export const isGeneralApp = () => !isTemplateOrGroupApp();
