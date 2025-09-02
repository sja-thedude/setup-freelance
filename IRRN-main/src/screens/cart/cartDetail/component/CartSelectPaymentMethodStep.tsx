import React, {
    forwardRef,
    memo,
    useCallback,
    useEffect,
    useImperativeHandle,
    useMemo,
    useState,
} from 'react';

import { useTranslation } from 'react-i18next';
import {
    StyleSheet,
    View,
} from 'react-native';
import Animated, {
    Layout,
    SlideInRight,
    SlideOutRight,
} from 'react-native-reanimated';
import {
    useEffectOnce,
    useTimeoutFn,
} from 'react-use';

import {
    BancontactIcon,
    CashIcon,
    IdealIcon,
    MasterCardIcon,
    RadioButtonCheckedIcon,
    RadioButtonUnCheckedIcon,
    VisaIcon,
} from '@src/assets/svg';
import ShadowView from '@src/components/ShadowView';
import TextComponent from '@src/components/TextComponent';
import Toast from '@src/components/toast/Toast';
import TouchableComponent from '@src/components/TouchableComponent';
import {
    CART_DISCOUNT_TYPE,
    ORDER_TYPE,
    RESTAURANT_EXTRA_TYPE,
} from '@src/configs/constants';
import {
    useAppDispatch,
    useAppSelector,
} from '@src/hooks';
import useCallAPI from '@src/hooks/useCallAPI';
import useDimens, { DimensType } from '@src/hooks/useDimens';
import { SCREENS } from '@src/navigation/config/screenName';
import NavigationService from '@src/navigation/NavigationService';
import { GroupDetailModel } from '@src/network/dataModels/GroupDetailModel';
import { OrderDetailModel } from '@src/network/dataModels/OrderDetailModel';
import { PaymentMethodModel } from '@src/network/dataModels/PaymentMethodModel';
import { RestaurantDetailModel, } from '@src/network/dataModels/RestaurantDetailModel';
import {
    createOrderService,
    getMolliePaymentLinkService,
} from '@src/network/services/orderServices';
import {
    getDetailGroupService,
    getPaymentMethodService,
    getRestaurantDetailService,
} from '@src/network/services/restaurantServices';
import { LoadingActions } from '@src/redux/toolkit/actions/loadingActions';
import { StorageActions } from '@src/redux/toolkit/actions/storageActions';
import useThemeColors from '@src/themes/useThemeColors';
import {
    getOrderPrices,
    handleOpenLink,
} from '@src/utils';

import { CartInfo } from '../CartScreen';

interface IProps {
    setDisableNextButton: Function,
    updateErrorMsg: Function,
    cartInfo: CartInfo,
    updateCartInfo: Function,
    handlePrevStep: Function,
    showPaymentTimeOutPopup: Function,
}

const PAYMENT_METHOD_TYPE = {
    MOLLIE: 0,
    INVOICE: 3,
    CASH: 2
};

const CartSelectPaymentMethodStep = forwardRef<any, IProps>(({ cartInfo, updateCartInfo, setDisableNextButton, handlePrevStep, showPaymentTimeOutPopup }, ref: any) => {
    const { themeColors } = useThemeColors();
    const Dimens = useDimens();
    const styles = stylesF(Dimens);

    const dispatch = useAppDispatch();
    const { t } = useTranslation();

    const groupData = useAppSelector((state) => state.storageReducer.cartProducts.groupFilter.groupData);
    const orderType = useAppSelector((state) => state.storageReducer.cartProducts.type);
    const cartProducts = useAppSelector((state) => state.storageReducer.cartProducts.data);
    const cartRestaurant = useAppSelector((state) => state.storageReducer.cartProducts.restaurant);
    const cartDeliveryInfo = useAppSelector((state) => state.storageReducer.cartProducts.deliveryInfo);
    const discountInfo = useAppSelector((state) => state.storageReducer.cartProducts.discountInfo);
    const { isServiceCostOn, isServiceCostAlwaysCharge, serviceCost, serviceCostAmount } = useAppSelector((state) => state.storageReducer.cartProducts.serviceCostInfo || {});

    const [groupDetail, setGroupDetail] = useState<GroupDetailModel>();
    const [restaurantPaymentMethod, setRestaurantPaymentMethod] = useState<Array<PaymentMethodModel>>([]);

    const [selectedMethod, setSelectedMethod] = useState(cartInfo.payment_method);

    const productsWithDiscount = useMemo(() => {
        const { productsWithDiscount } = getOrderPrices(
                cartProducts,
                cartDeliveryInfo.deliveryFee,
                discountInfo,
                groupData,
                orderType,
                isServiceCostOn,
                isServiceCostAlwaysCharge,
                serviceCost,
                serviceCostAmount
        );
        return productsWithDiscount;
    }, [cartDeliveryInfo.deliveryFee, cartProducts, discountInfo, groupData, isServiceCostAlwaysCharge, isServiceCostOn, orderType, serviceCost, serviceCostAmount]);

    const paymentMethod = useMemo(() => {

        let mollie: any = {
            id: restaurantPaymentMethod.find((item) => item.type === PAYMENT_METHOD_TYPE.MOLLIE)?.id,
            type: PAYMENT_METHOD_TYPE.MOLLIE,
            title: `${t('payment_method_bancontact')}, ${t('payment_method_ideal')}, ${t('payment_method_mastercard')}, ${t('payment_method_visa')}`
        };

        let invoice: any = {
            id: null,
            type: PAYMENT_METHOD_TYPE.INVOICE,
            title: t('payment_method_op_factuur')
        };

        let cash: any = {
            id: null,
            type: PAYMENT_METHOD_TYPE.CASH,
            title: t('payment_method_cash')
        };

        switch (orderType) {
            case ORDER_TYPE.GROUP_ORDER:
                {
                    if (groupDetail?.payment_mollie === 0) {
                        mollie = false;
                    }

                    if (groupDetail?.payment_factuur === 0) {
                        invoice = false;
                    }

                    if (groupDetail?.payment_cash === 0) {
                        cash = false;
                    }
                }
                break;
            case ORDER_TYPE.TAKE_AWAY:
                {
                    if (restaurantPaymentMethod[0]?.takeout === false) {
                        mollie = false;
                    }

                    invoice = false;

                    if (restaurantPaymentMethod[2]?.takeout === false) {
                        cash = false;
                    }
                }
                break;
            case ORDER_TYPE.DELIVERY:
                {
                    if (restaurantPaymentMethod[0]?.delivery === false) {
                        mollie = false;
                    }

                    invoice = false;

                    if (restaurantPaymentMethod[2]?.delivery === false) {
                        cash = false;
                    }
                }
                break;

            default:
                break;
        }

        return [mollie, invoice, cash].filter(Boolean);

    }, [groupDetail?.payment_cash, groupDetail?.payment_factuur, groupDetail?.payment_mollie, orderType, restaurantPaymentMethod, t]);

    const { callApi: getPaymentMethod } = useCallAPI(
            getPaymentMethodService,
            undefined,
            useCallback((data: any) => {
                setRestaurantPaymentMethod(data.data);
            }, [])
    );

    const { callApi: getDetailGroup } = useCallAPI(
            getDetailGroupService,
            undefined,
            useCallback((data: GroupDetailModel) => {
                setGroupDetail(data);
                updateCartInfo({ type: data.type, group_id: data.id });
            }, [updateCartInfo])
    );

    const { callApi: getMolliePaymentLink } = useCallAPI(
            getMolliePaymentLinkService,
            undefined,
            useCallback((data: any) => {
                // open mollie payment site
                handleOpenLink(data.url);
            }, [])
    );

    const { callApi: createOrder } = useCallAPI(
            createOrderService,
            useCallback(() => {
                dispatch(LoadingActions.showGlobalLoading(true));
            }, [dispatch]),
            useCallback((data: OrderDetailModel) => {
                if (Number(data.total_price) === 0)  {
                    // navigate to success screen
                    NavigationService.navigate(SCREENS.ORDER_SUCCESS_SCREEN, { orderId: data.id });
                } else {
                    if (selectedMethod === PAYMENT_METHOD_TYPE.MOLLIE) {
                        getMolliePaymentLink({
                            total_price: data.total_price,
                            order_id: data.id
                        });
                        setTimeout(() => {
                            dispatch(StorageActions.setStorageProcessingOrder(data.id));
                        }, 1000);
                    } else {
                        // navigate to success screen
                        NavigationService.navigate(SCREENS.ORDER_SUCCESS_SCREEN, { orderId: data.id });
                    }
                }
            }, [dispatch, getMolliePaymentLink, selectedMethod])
    );

    const { callApi: getRestaurantDetail } = useCallAPI(
            getRestaurantDetailService,
            useCallback(() => {
                dispatch(LoadingActions.showGlobalLoading(true));
            }, [dispatch]),
            useCallback((data: RestaurantDetailModel) => {

                if (orderType !== ORDER_TYPE.GROUP_ORDER) {
                    const isAdminServiceCostOn = data?.extras?.find((item) => item.type === RESTAURANT_EXTRA_TYPE.SERVICE_COST)?.active;
                    const isWorkspaceServiceCostOn = data?.setting_preference?.service_cost_set;
                    const isServiceCostOn = isAdminServiceCostOn && isWorkspaceServiceCostOn;
                    const isServiceCostAlwaysCharge = data?.setting_preference?.service_cost_always_charge;

                    const serviceCost = Number(data?.setting_preference?.service_cost || 0);
                    const serviceCostAmount = Number(data?.setting_preference?.service_cost_amount || 0);

                    // save service cost info
                    dispatch(StorageActions.setStorageServiceCost({
                        isServiceCostOn,
                        isServiceCostAlwaysCharge,
                        serviceCost,
                        serviceCostAmount,
                    }));
                }

                if (data.is_online) {
                    createOrder({
                        ...cartInfo,
                        timeSlots: undefined,
                        invalidDateTimeSlotProducts: undefined,
                        invalidDeliveryProducts: undefined,
                        invalidAvailableProducts: undefined,
                        invalidOptionProducts: undefined,

                        group_id: orderType === ORDER_TYPE.GROUP_ORDER ? cartInfo.group_id : undefined,

                        date_time: `${cartInfo.date} ${orderType === ORDER_TYPE.GROUP_ORDER ? groupDetail?.receive_time : cartInfo.time}`,
                        address: cartDeliveryInfo.deliveryAddress.address || undefined,
                        lat: cartDeliveryInfo.deliveryAddress.lat || undefined,
                        lng: cartDeliveryInfo.deliveryAddress.lng || undefined,
                        address_type: cartDeliveryInfo.deliveryAddress.addressType || undefined,
                        setting_delivery_condition_id: cartDeliveryInfo.settingDeliveryConditionId || undefined,
                        coupon_code: discountInfo.discountType === CART_DISCOUNT_TYPE.COUPON_DISCOUNT ? discountInfo.discount?.code : undefined,
                        coupon_id:  discountInfo.discountType === CART_DISCOUNT_TYPE.COUPON_DISCOUNT ? discountInfo.discount?.id : undefined,
                        reward_id: discountInfo.discountType === CART_DISCOUNT_TYPE.LOYALTY_DISCOUNT ? discountInfo.discount?.id : undefined,

                        items: cartProducts.map((product, pIndex) => {
                            const availableDiscount = !!discountInfo.applicableProducts.find((ap) => ap.id === product.id) || false;
                            const discount = availableDiscount ? productsWithDiscount.find((ap) => ap.id === product.id && ap.cartPosition === pIndex)?.discount || undefined : undefined;
                            const redeemHistoryId = (availableDiscount && !!discount) ? discountInfo.discountType === CART_DISCOUNT_TYPE.LOYALTY_DISCOUNT ? discountInfo.discount?.redeem_id : undefined : undefined;
                            const couponId = (availableDiscount && !!discount) ? discountInfo.discountType === CART_DISCOUNT_TYPE.COUPON_DISCOUNT ? discountInfo.discount?.id : undefined : undefined;
                            return {
                                product_id: product.id,
                                quantity: product.quantity,
                                available_discount: availableDiscount,
                                discount: discount,
                                redeem_history_id: redeemHistoryId,
                                coupon_id: couponId,
                                options: product.options?.map((op) => {
                                    const iSelectedMaster = op.items.find((io) => io.master);

                                    if (iSelectedMaster) {
                                        return {
                                            option_id: op.id,
                                            option_items: [{ option_item_id: iSelectedMaster.id }]
                                        };
                                    }

                                    return {
                                        option_id: op.id,
                                        option_items: op.items.map((it) => ({
                                            option_item_id: it.id
                                        }))
                                    };
                                })
                            };
                        })
                    });
                } else {
                    Toast.showToast(t('cart_order_restaurant_is_closing'));
                }
            }, [cartDeliveryInfo.deliveryAddress.address, cartDeliveryInfo.deliveryAddress.addressType, cartDeliveryInfo.deliveryAddress.lat, cartDeliveryInfo.deliveryAddress.lng, cartDeliveryInfo.settingDeliveryConditionId, cartInfo, cartProducts, createOrder, discountInfo.applicableProducts, discountInfo.discount?.code, discountInfo.discount?.id, discountInfo.discount?.redeem_id, discountInfo.discountType, dispatch, groupDetail?.receive_time, orderType, productsWithDiscount, t]),
    );

    const [_isReady, cancelTimeOut] = useTimeoutFn(() => {
        if (orderType !== ORDER_TYPE.GROUP_ORDER) {
            handlePrevStep();
            showPaymentTimeOutPopup();
            updateCartInfo({ date: null, time: null, timeSlots: [] });
        }
    }, 60000 * 5);

    useEffectOnce(() => {
        getPaymentMethod({
            restaurant_id: cartRestaurant?.id
        });
        if (orderType === ORDER_TYPE.GROUP_ORDER) {
            getDetailGroup({
                group_id: groupData?.id
            });
        } else {
            updateCartInfo({ type: orderType });
        }
    });

    useEffect(() => {
        setDisableNextButton(selectedMethod === null);
        return () => {
            setDisableNextButton(false);
        };
    }, [selectedMethod, setDisableNextButton]);

    const handleNext = useCallback(() => {
        cancelTimeOut();
        getRestaurantDetail({
            restaurant_id: cartRestaurant.id
        });
    }, [cancelTimeOut, cartRestaurant.id, getRestaurantDetail]);

    useImperativeHandle(ref, () => ({
        handleNext
    }), [handleNext]);

    return (
        <Animated.ScrollView
            entering={SlideInRight.duration(500)}
            exiting={SlideOutRight.duration(500)}
            layout={Layout.duration(500)}
            showsVerticalScrollIndicator={false}
            style={styles.scrollView}
        >
            <View style={styles.mainContainer}>
                {paymentMethod.map((item, index) => (

                    <TouchableComponent
                        key={index}
                        onPress={() => {
                            setSelectedMethod(item.type);
                            updateCartInfo({ payment_method: item.type, setting_payment_id: item.id });
                        }}
                        style={styles.itemContainer}
                    >
                        <ShadowView
                            style={styles.shadowStyle}
                        >
                            <View
                                style={[styles.itemWrapper, { backgroundColor: themeColors.color_card_background }]}
                            >
                                <View style={{ flexDirection: 'row',  }}>
                                    {selectedMethod === item.type ? (
                                                <RadioButtonCheckedIcon
                                                    width={Dimens.W_20}
                                                    height={Dimens.W_20}
                                                    stroke={themeColors.color_primary}
                                                    fill={themeColors.color_primary}
                                                />
                                            ) : (
                                                <RadioButtonUnCheckedIcon
                                                    width={Dimens.W_20}
                                                    height={Dimens.W_20}
                                                />
                                            )}
                                    <View style={{ marginLeft: Dimens.W_8 }}>
                                        <TextComponent style={styles.title}>
                                            {item.title}
                                        </TextComponent>
                                        {item.type === PAYMENT_METHOD_TYPE.CASH && (
                                            <CashIcon
                                                width={Dimens.H_30}
                                                height={Dimens.H_30}
                                            />
                                        )}

                                        {item.type === PAYMENT_METHOD_TYPE.MOLLIE && (
                                            <View style={styles.methodContainer}>
                                                <BancontactIcon
                                                    width={Dimens.H_46}
                                                    height={Dimens.H_32}
                                                />
                                                <IdealIcon
                                                    width={Dimens.H_34}
                                                    height={Dimens.H_32}
                                                />
                                                <MasterCardIcon
                                                    width={Dimens.H_50}
                                                    height={Dimens.H_50}
                                                />
                                                <VisaIcon
                                                    width={Dimens.H_62}
                                                    height={Dimens.H_20}
                                                />
                                            </View>
                                        )}

                                        {item.type === PAYMENT_METHOD_TYPE.INVOICE && (
                                            <View style={{ height: Dimens.H_30 }}/>
                                        )}
                                    </View>
                                </View>

                            </View>
                        </ShadowView>
                    </TouchableComponent>
                ))}
            </View>
        </Animated.ScrollView>
    );
});

export default memo(CartSelectPaymentMethodStep);

const stylesF = (Dimens: DimensType) => StyleSheet.create({
    shadowStyle: { shadowColor: 'rgba(0, 0, 0, 0.05)', shadowOffset: { width: 0, height: Dimens.H_6 }, shadowRadius: Dimens.H_15 },
    methodContainer: {
        flexDirection: 'row',
        alignItems: 'center',
        justifyContent: 'space-between',
    },
    title: { fontSize: Dimens.FONT_16, marginBottom: Dimens.H_12 },
    itemWrapper: {
        width: '100%',
        minHeight: Dimens.SCREEN_HEIGHT / 10,
        paddingHorizontal: Dimens.W_10,
        paddingVertical: Dimens.H_15,
        borderRadius: Dimens.RADIUS_6,
    },
    itemContainer: {
        marginBottom: Dimens.H_24,
        paddingHorizontal: Dimens.W_16,
    },
    mainContainer: { flex: 1, paddingBottom: Dimens.H_16, paddingTop: Dimens.H_6 },
    scrollView: {
        paddingVertical: Dimens.H_16,
        marginHorizontal: -Dimens.W_16,
    },
});