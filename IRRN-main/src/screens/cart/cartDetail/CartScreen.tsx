import React, {
    useCallback,
    useEffect,
    useMemo,
    useRef,
    useState,
} from 'react';

import { useTranslation } from 'react-i18next';
import {
    Keyboard,
    StyleSheet,
    TouchableWithoutFeedback,
    View,
} from 'react-native';
import Animated, {
    FadeIn,
    FadeInLeft,
    FadeOut,
    FadeOutLeft,
    Layout,
} from 'react-native-reanimated';
import { useDeepCompareEffect } from 'react-use';

import {
    useKeyboard,
    useLayout,
} from '@react-native-community/hooks';
import { useFocusEffect } from '@react-navigation/native';
import ButtonComponent from '@src/components/ButtonComponent';
import BackButton from '@src/components/header/BackButton';
import HeaderComponent from '@src/components/header/HeaderComponent';
import ShadowView from '@src/components/ShadowView';
import TextComponent from '@src/components/TextComponent';
import Toast from '@src/components/toast/Toast';
import TouchableComponent from '@src/components/TouchableComponent';
import { Colors } from '@src/configs';
import {
    CART_DISCOUNT_TYPE,
    DEFAULT_CURRENCY,
    LARGE_PAGE_SIZE,
    ORDER_TYPE,
    RESTAURANT_EXTRA_TYPE,
    VALUE_DISCOUNT_TYPE,
} from '@src/configs/constants';
import {
    useAppDispatch,
    useAppSelector,
} from '@src/hooks';
import useBoolean from '@src/hooks/useBoolean';
import useCallAPI from '@src/hooks/useCallAPI';
import useCheckEmptyCart from '@src/hooks/useCheckEmptyCart';
import useDimens, { DimensType } from '@src/hooks/useDimens';
import useIsUserLoggedIn from '@src/hooks/useIsUserLoggedIn';
import { SCREENS } from '@src/navigation/config/screenName';
import NavigationService from '@src/navigation/NavigationService';
import { UserDataModel } from '@src/network/dataModels';
import { CouponDetailModal } from '@src/network/dataModels/CouponDetailModal';
import { MyRedeemModel } from '@src/network/dataModels/MyRedeemModel';
import { ProductOptionModel } from '@src/network/dataModels/ProductOptionModel';
import { ProductSuggestionModel, } from '@src/network/dataModels/ProductSuggestionModel';
import { RestaurantDetailModel, } from '@src/network/dataModels/RestaurantDetailModel';
import { SettingOpenHourShort, } from '@src/network/dataModels/RestaurantNearbyItemModel';
import { Timeslot } from '@src/network/dataModels/TimeSlotModel';
import {
    validateCouponCodeService,
    validateProductCouponService,
} from '@src/network/services/couponServices';
import {
    checkAvailableProductService,
    getProductDetailService,
    getProductOptionsService,
    getSuggestionProductService,
    validateProductAvailableDeliveryService,
} from '@src/network/services/productServices';
import {
    getDetailGroupService,
    getMyRedeemService,
    getRestaurantDetailService,
    getRestaurantOpeningHourService,
    validateRewardProductService,
} from '@src/network/services/restaurantServices';
import { LoadingActions } from '@src/redux/toolkit/actions/loadingActions';
import { StorageActions } from '@src/redux/toolkit/actions/storageActions';
import { ProductInCart } from '@src/redux/toolkit/slices/storageSlice';
import useThemeColors from '@src/themes/useThemeColors';
import { isTemplateOrGroupApp, validateEmail } from '@src/utils';
import formatCurrency from '@src/utils/currencyFormatUtil';

import CartFooter from './component/CartFooter';
import CartHeader from './component/CartHeader';
import CartProductStep from './component/CartProductStep';
import CartSelectDateStep from './component/CartSelectDateStep';
import CartSelectPaymentMethodStep
    from './component/CartSelectPaymentMethodStep';
import InvalidOpeningHourDialog from './component/InvalidOpeningHourDialog';
import PaymentStepTimeOutDialog from './component/PaymentStepTimeOutDialog';
import SuggestionProductDialog from './component/SuggestionProductDialog';
import UpdateNameAndPhoneDialog from './component/UpdateNameAndPhoneDialog';

export interface CartInfo {
    note: string,
    date: any,
    time: any,
    payment_method: any,
    workspace_id: any,
    date_time: any,
    setting_payment_id: any,
    setting_timeslot_detail_id: any,
    type: any, // if group order => type of group, if individual order => type of order
    group_id: any,
    items: Array<{quantity: number, product_id: number, options: Array<any>}>,
    address: any,
    lat: any,
    lng: any,

    setting_delivery_condition_id: any,
    address_type: any,
    coupon_code: any,
    coupon_id: any,
    reward_id: any,

    timeSlots: Array<Timeslot>,
    invalidDateTimeSlotProducts: Array<number>,
    invalidDeliveryProducts: Array<number>,
    invalidAvailableProducts: Array<number>,
    invalidOptionProducts: Array<{cartPosition: number, id: number}>,
}

const initCartInfo = {
    note: '',
    date: null,
    time: null,
    payment_method: null,
    workspace_id: null,
    date_time: null,
    setting_payment_id: null,
    setting_timeslot_detail_id: null,
    type: null,
    group_id: null,
    items: [],
    address: null,
    lat: null,
    lng: null,

    setting_delivery_condition_id: null,
    address_type: null,
    coupon_code: null,
    coupon_id: null,
    reward_id: null,

    timeSlots: [],
    invalidDateTimeSlotProducts: [],
    invalidDeliveryProducts: [],
    invalidAvailableProducts: [],
    invalidOptionProducts: [],
};

const CartScreen = () => {
    const { themeColors } = useThemeColors();
    const Dimens = useDimens();
    const styles = stylesF(Dimens);

    const { t } = useTranslation();

    const { keyboardShown } = useKeyboard();

    const isEmptyCart = useCheckEmptyCart();
    const isUserLoggedIn = useIsUserLoggedIn();
    const dispatch = useAppDispatch();

    const { onLayout, height } = useLayout();
    const [isShowInvalidOpeningHourPopup, showInvalidOpeningHourPopup, hideInvalidOpeningHourPopup] = useBoolean(false);
    const [isShowSuggestionProductPopup, showSuggestionProductPopup, hideSuggestionProductPopup] = useBoolean(false);
    const [isShowPaymentTimeOutPopup, showPaymentTimeOutPopup, hidePaymentTimeOutPopup] = useBoolean(false);
    const [isShowUpdateInfoPopup, showUpdateInfoPopup, hideUpdateInfoPopup] = useBoolean(false);

    const refProductStep = useRef<any>();
    const refSelectDateStep = useRef<any>();
    const refPaymentMethodStep = useRef<any>();
    const disableAutoApplyLoyaltyDiscount = useRef<any>(false);

    const userData = useAppSelector((state) => state.userDataReducer.userData);
    const cartProducts = useAppSelector((state) => state.storageReducer.cartProducts.data);
    const cartRestaurant = useAppSelector((state) => state.storageReducer.cartProducts.restaurant);
    const orderType = useAppSelector((state) => state.storageReducer.cartProducts.type);
    const groupData = useAppSelector((state) => state.storageReducer.cartProducts.groupFilter.groupData);
    const cartDeliveryFee = useAppSelector((state) => state.storageReducer.cartProducts.deliveryInfo.deliveryFee);
    const discountInfo = useAppSelector((state) => state.storageReducer.cartProducts.discountInfo);

    const [currentRestaurant, setCurrentRestaurant] = useState<RestaurantDetailModel>();
    const [currentStep, setCurrentStep] = useState(0);
    const [errorMsg, setErrorMsg] = useState('');
    const [disableNextButton, setDisableNextButton] = useState(false);
    const [cartInfo, setCartInfo] = useState<CartInfo>(initCartInfo);
    const [suggestionProducts, setSuggestionProducts] = useState<Array<ProductSuggestionModel>>([]);
    const [canApplyLoyaltyDiscount, setCanApplyLoyaltyDiscount] = useState(false);

    const showDeliveryTopMessage = useMemo(() => currentStep === 0 && orderType === ORDER_TYPE.DELIVERY, [currentStep, orderType]);
    const showLoyaltyBottomMessage = useMemo(() => currentStep === 0 && canApplyLoyaltyDiscount && discountInfo.discountType !== CART_DISCOUNT_TYPE.LOYALTY_DISCOUNT, [canApplyLoyaltyDiscount, currentStep, discountInfo.discountType]);

    const updateCartInfo = useCallback((data: any) => {
        setCartInfo((state) => ({ ...state, ...data }));
    }, []);

    const updateCartDiscount = useCallback((applicableProducts: ProductInCart[], discount: any, discountType: string ) => {
        // apply loyalty discount to the cart
        dispatch(StorageActions.setStorageDiscount({
            discountType: discountType,
            discount: discount,
        }));
        // save discount applicable products
        dispatch(StorageActions.setStorageDiscountProducts(applicableProducts));
    }
    , [ dispatch]);

    const updateAutoApplyLoyaltyDiscount = useCallback((autoApply: boolean) => {
        disableAutoApplyLoyaltyDiscount.current = autoApply;
    }, []);

    const { callApi: getRestaurantDetail } = useCallAPI(
            getRestaurantDetailService,
            undefined,
            useCallback((data: RestaurantDetailModel) => {
                setCurrentRestaurant(data);
                updateCartInfo({ workspace_id: data.id });

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

            }, [dispatch, orderType, updateCartInfo]),
    );

    const { callApi: getRestaurantOpeningHour } = useCallAPI(
            getRestaurantOpeningHourService,
            undefined,
            undefined,
            undefined,
            true,
            false
    );

    const { callApi: getDetailGroup } = useCallAPI(
            getDetailGroupService
    );

    const { callApi: getProductDetail } = useCallAPI(
            getProductDetailService,
    );

    const { callApi: checkAvailableProduct } = useCallAPI(
            checkAvailableProductService
    );

    const { callApi: getProductOptions } = useCallAPI(
            getProductOptionsService,
    );

    const { callApi: validateProductAvailableDelivery } = useCallAPI(
            validateProductAvailableDeliveryService
    );

    const { callApi: getSuggestionProduct } = useCallAPI(
            getSuggestionProductService,
            useCallback(() => {
                dispatch(LoadingActions.showGlobalLoading(true));
            }, [dispatch]),
    );

    const { callApi: validateRewardProduct } = useCallAPI(
            validateRewardProductService
    );

    const { callApi: getMyRedeem } = useCallAPI(
            getMyRedeemService,
            undefined,
            undefined,
            undefined,
            false,
            false
    );

    const { callApi: validateProductCoupon } = useCallAPI(
            validateProductCouponService
    );

    const { callApi: validateCouponCode } = useCallAPI(
            validateCouponCodeService
    );

    const checkLoyaltyDiscount = useCallback((applyDiscount: boolean, showLoading: boolean) => {
        if (cartRestaurant?.id && !isEmptyCart && isUserLoggedIn) {
            showLoading && dispatch(LoadingActions.showGlobalLoading(true));
            getMyRedeem({
                restaurant_id: cartRestaurant?.id
            }).then((result) => {
                if (result.success) {
                    const resultData: MyRedeemModel = result.data;
                    validateRewardProduct({
                        restaurant_id: cartRestaurant?.id,
                        reward_id: resultData.reward_data?.id,
                        product_id: cartProducts.map((i) => i.id)
                    }).then((res) => {
                        if (res.success) {
                            const resultProducts = Object.entries(res.data).filter((i) => i[1] === true).map((i) => Number(i[0]));

                            if (resultProducts.length > 0) {
                                if (applyDiscount) {
                                    setCanApplyLoyaltyDiscount(false);
                                    updateCartDiscount(
                                            cartProducts.filter((i) => resultProducts.includes(i.id)),
                                            { ...resultData.reward_data, redeem_id: resultData.id },
                                            CART_DISCOUNT_TYPE.LOYALTY_DISCOUNT,
                                    );

                                } else {
                                    setCanApplyLoyaltyDiscount(true);
                                }

                            } else {
                                setCanApplyLoyaltyDiscount(false);
                                applyDiscount && dispatch(StorageActions.clearStorageDiscount());
                            }
                        } else {
                            setCanApplyLoyaltyDiscount(false);
                            applyDiscount && dispatch(StorageActions.clearStorageDiscount());
                        }
                    });
                } else {
                    setCanApplyLoyaltyDiscount(false);
                    applyDiscount && dispatch(StorageActions.clearStorageDiscount());
                    // applyDiscount && Toast.showToast(result.message);
                }
                disableAutoApplyLoyaltyDiscount.current = false;
            });
        }
    }, [cartProducts, cartRestaurant?.id, dispatch, getMyRedeem, isEmptyCart, isUserLoggedIn, updateCartDiscount, validateRewardProduct]);

    const checkGroupDiscount = useCallback((callback: Function) => {
        getDetailGroup({
            group_id: groupData?.id
        }).then((result) => {
            let applyLoyaltyDiscount = false;

            if (result.success) {
                if (result.data.discount_type !== VALUE_DISCOUNT_TYPE.NO_DISCOUNT) {
                    applyLoyaltyDiscount = false;
                    updateCartDiscount(
                            cartProducts,
                            result.data,
                            CART_DISCOUNT_TYPE.GROUP_DISCOUNT,
                    );

                } else {
                    applyLoyaltyDiscount = true;
                    dispatch(StorageActions.clearStorageDiscount());
                }
            } else {
                applyLoyaltyDiscount = true;
                dispatch(StorageActions.clearStorageDiscount());
            }

            callback(applyLoyaltyDiscount);
        });
    }, [cartProducts, dispatch, getDetailGroup, groupData?.id, updateCartDiscount]);

    const checkCouponDiscount = useCallback(() => {
        validateCouponCode({
            code: discountInfo?.discount?.code,
            workspace_id: cartRestaurant?.id
        }).then((resData) => {
            if (resData.success) {
                const data: CouponDetailModal = resData.data;
                validateProductCoupon({
                    product_id: cartProducts.map((p) => p.id),
                    code: data.code
                }).then((result) => {
                    if (result.success) {
                        const resultProducts = Object.entries(result.data).filter((i) => i[1] === true).map((i) => Number(i[0]));
                        if (resultProducts.length > 0) {
                            updateCartDiscount(
                                    cartProducts.filter((i) => resultProducts.includes(i.id)),
                                    data,
                                    CART_DISCOUNT_TYPE.COUPON_DISCOUNT,
                            );

                        } else {
                            dispatch(StorageActions.clearStorageDiscount());
                            Toast.showToast(t('coupon_discount_does_not_apply_in_cart'));
                        }
                    } else {
                        dispatch(StorageActions.clearStorageDiscount());
                    }
                });
            } else {
                dispatch(StorageActions.clearStorageDiscount());
            }
        });
    }, [cartProducts, cartRestaurant?.id, discountInfo?.discount?.code, dispatch, t, updateCartDiscount, validateCouponCode, validateProductCoupon]);

    // check any discount
    useFocusEffect(
            useCallback(() => {
                if (!isEmptyCart && cartRestaurant?.id) {
                    const discountType = discountInfo?.discountType;

                    if (orderType === ORDER_TYPE.GROUP_ORDER) {
                        if (discountType === null || discountType === CART_DISCOUNT_TYPE.GROUP_DISCOUNT) {
                            const callBack = (applyDiscount: boolean) => {
                                checkLoyaltyDiscount(applyDiscount, false);
                            };
                            checkGroupDiscount(callBack);
                        } else if (discountType === CART_DISCOUNT_TYPE.COUPON_DISCOUNT) {
                            checkCouponDiscount();
                            checkLoyaltyDiscount(false, false);
                        } else {
                            checkLoyaltyDiscount(true, false);
                        }

                    } else {
                        if (discountType === CART_DISCOUNT_TYPE.COUPON_DISCOUNT) {
                            checkCouponDiscount();
                            checkLoyaltyDiscount(false, false);
                        }

                        if (discountType === CART_DISCOUNT_TYPE.LOYALTY_DISCOUNT || discountType === null) {
                            checkLoyaltyDiscount(!disableAutoApplyLoyaltyDiscount.current, false);
                        }
                    }
                }
            }, [isEmptyCart, cartRestaurant?.id, discountInfo?.discountType, orderType, checkLoyaltyDiscount, checkGroupDiscount, checkCouponDiscount])
    );

    // get group detail
    useFocusEffect(
            useCallback(() => {
                if (!isEmptyCart && cartRestaurant?.id) {
                    if (orderType === ORDER_TYPE.GROUP_ORDER) {
                        getDetailGroup({
                            group_id: groupData?.id
                        }).then((result) => {
                            if (result.success) {
                                // update group filter
                                dispatch(StorageActions.setStorageGroupFilter(
                                        {
                                            data: result.data,
                                            filterByDeliverable: result.data.type === ORDER_TYPE.DELIVERY ? true : false,
                                        }
                                ));
                            }
                        });
                    }
                }
            }, [isEmptyCart, cartRestaurant?.id, orderType, getDetailGroup, groupData?.id, dispatch])
    );

    // get restaurant detail
    useFocusEffect(
            useCallback(() => {
                if (!isEmptyCart && cartRestaurant?.id) {
                    getRestaurantDetail({
                        restaurant_id: cartRestaurant?.id
                    });
                }
            }, [isEmptyCart, cartRestaurant?.id, getRestaurantDetail])
    );

    // check available products & available options/option items
    useFocusEffect(
            useCallback(() => {
                if (!isEmptyCart && cartRestaurant?.id) {
                    // check available products
                    checkAvailableProduct({
                        id: cartProducts.map((p) => p.id)
                    }).then((result) => {
                        if (result.success) {
                            const resultInvalidProducts = Object.entries(result.data).filter((i) => i[1] === false);
                            if (resultInvalidProducts.length > 0) {
                                // update cart invalid available product
                                const invalidProduct = resultInvalidProducts.map((p) => p[1] === false ? Number(p[0]) : false).filter(Boolean);
                                updateCartInfo({ invalidAvailableProducts: invalidProduct });
                            } else {
                                updateCartInfo({ invalidAvailableProducts: [] });
                            }

                            const resultValidProducts = Object.entries(result.data).filter((i) => i[1] === true);
                            // get products detail to check if product has new price
                            if (resultValidProducts.length > 0) {
                                const validProduct = resultValidProducts.map((p) => p[1] === true ? Number(p[0]) : false).filter(Boolean);
                                Promise.all(
                                        validProduct?.map((i) => getProductDetail({ product_id: i }))
                                ).then((result: any) => {
                                    result.map((r: any) => {
                                        if (r.success) {
                                            dispatch(StorageActions.updateProductCartNewPrice({ productId: r?.data?.id, newPrice: r?.data?.price }));
                                        }
                                    });
                                });
                            }

                        }
                    });

                    // check available options & option items
                    const hasOptionProducts = cartProducts.filter((i) => i.options && i.options?.length > 0);
                    let invalidOptProducts1: Array<{cartPosition: any, id: number}> = [];

                    Promise.all(
                            hasOptionProducts.map((i) => getProductOptions({ product_id: i.id }))
                    ).then((result: any) => {
                        const newOptionsArr: Array<{options: ProductOptionModel[], product_id: number}> = result.map((op: any, idx: number) => ({
                            options: op.success ? op.data : [],
                            product_id: hasOptionProducts[idx].id,
                            cartPosition: hasOptionProducts[idx].cartPosition
                        }));

                        hasOptionProducts.map((p) => {
                            let currentProductOptions: any = p.options || [];
                            const newProductOptions: Array<ProductOptionModel> = newOptionsArr.find((no) => no.product_id === p.id)?.options || [];

                            currentProductOptions.map((cpo: ProductOptionModel) => {
                                const availableOption = newProductOptions.find((npo) => npo.id === cpo.id);

                                if (!availableOption) {
                                    invalidOptProducts1.push({ cartPosition: p.cartPosition, id: p.id });
                                } else {
                                    cpo.items.map((poi) => {
                                        const availableOptionItem = availableOption?.items.find((it) => it.available && it.id === poi.id);
                                        if (!availableOptionItem) {
                                            invalidOptProducts1.push({ cartPosition: p.cartPosition, id: p.id });
                                        }
                                    });
                                }
                            });
                        });

                        updateCartInfo({ invalidOptionProducts: invalidOptProducts1 });
                    });
                }
            }, [isEmptyCart, cartRestaurant?.id, checkAvailableProduct, cartProducts?.length, updateCartInfo, getProductDetail, dispatch, getProductOptions])
    );

    // check opening hours
    useFocusEffect(
            useCallback(() => {
                if (!isEmptyCart && cartRestaurant?.id) {
                    if (orderType !== ORDER_TYPE.GROUP_ORDER) {
                        getRestaurantOpeningHour({ restaurant_id: cartRestaurant?.id }).then((result) => {
                            if (result.success) {
                                const disableOrderType = result.data.find((i: SettingOpenHourShort) => i.type === orderType && i.active === false);
                                if (disableOrderType) {
                                    showInvalidOpeningHourPopup();
                                } else {
                                    if (orderType === ORDER_TYPE.DELIVERY) {
                                    // check available delivery
                                        validateProductAvailableDelivery({
                                            product_id: cartProducts.map((p) => p.id)
                                        }).then((result) => {
                                            if (result.success) {
                                                const resultProducts = Object.entries(result.data).filter((i) => i[1] === false);
                                                if (resultProducts.length > 0) {
                                                // update cart invalid delivery product
                                                    const invalidProduct = resultProducts.map((p) => p[1] === false ? Number(p[0]) : false).filter(Boolean);
                                                    updateCartInfo({ invalidDeliveryProducts: invalidProduct });
                                                } else {
                                                    updateCartInfo({ invalidDeliveryProducts: [] });
                                                }
                                            }
                                        });
                                    }
                                }
                            }
                        });
                    }

                }
            }, [isEmptyCart, cartRestaurant?.id, orderType, getRestaurantOpeningHour, showInvalidOpeningHourPopup, validateProductAvailableDelivery, cartProducts.length, updateCartInfo])
    );

    // clear data when change order type
    useEffect(() => {
        updateCartInfo({
            date: null,
            time: null,
            payment_method: null,
            setting_payment_id: null,
            setting_timeslot_detail_id: null,
            address: null,
            lat: null,
            lng: null,

            setting_delivery_condition_id: null,
            address_type: null,

            timeSlots: [],
            invalidDateTimeSlotProducts: [],
            invalidDeliveryProducts: [],
        });
    }, [orderType, updateCartInfo]);

    // reset data when change product or discount
    useDeepCompareEffect(() => {
        setCurrentStep(0);

        if (isEmptyCart) {
            setErrorMsg('');
            setCartInfo(initCartInfo);
            setDisableNextButton(false);
            setSuggestionProducts([]);
            setCanApplyLoyaltyDiscount(false);
        }
    }, [cartProducts, discountInfo, isEmptyCart]);

    const handleSelectStep = useCallback((step: number) => {
        setCurrentStep(step);
    }, []);

    const handleNextStep = useCallback(() => {
        if (currentStep < 2) {
            setCurrentStep(currentStep + 1);
        }
    }, [currentStep]);

    const handlePrevStep = useCallback(() => {
        if (currentStep > 0) {
            setCurrentStep(currentStep - 1);
        }
    }, [currentStep]);

    const gotoNextStep = useCallback(() => {
        switch (currentStep) {
            case 0:
                refProductStep.current?.handleNext();
                break;
            case 1:
                refSelectDateStep.current?.handleNext();
                break;
            case 2:
                refPaymentMethodStep.current?.handleNext();
                break;
            default:
                break;
        }
    }, [currentStep]);

    const checkOpeningHour = useCallback((hideLoadingAfterLoad: boolean) => {
        // check opening hours
        if (orderType !== ORDER_TYPE.GROUP_ORDER) {
            dispatch(LoadingActions.showGlobalLoading(true));
            getRestaurantOpeningHour({ restaurant_id: cartRestaurant?.id }).then((result) => {
                hideLoadingAfterLoad && dispatch(LoadingActions.showGlobalLoading(false));
                if (result.success) {
                    const disableOrderType = result.data.find((i: SettingOpenHourShort) => i.type === orderType && i.active === false);
                    if (disableOrderType) {
                        showInvalidOpeningHourPopup();
                    } else {
                        gotoNextStep();
                    }
                }
            });
        } else {
            gotoNextStep();
        }
    }, [cartRestaurant?.id, dispatch, getRestaurantOpeningHour, gotoNextStep, orderType, showInvalidOpeningHourPopup]);

    const checkSuggestionProduct = useCallback(() => {
        const categoryId = cartProducts[0]?.category_id;
        const sameCategory = cartProducts.filter((i) => i.category_id === categoryId);

        const isAllProductsAreSameCategory = sameCategory.length === cartProducts.length;

        if (isAllProductsAreSameCategory && currentStep === 0) {
            // get suggestion products
            getSuggestionProduct({
                category_id: categoryId,
                page: 1,
                limit: LARGE_PAGE_SIZE,
                order_by: 'name',
                sort_by: 'asc',
            }).then((result) => {
                if (result.success && result.data?.data?.length > 0) {
                    setSuggestionProducts(result.data.data);
                    showSuggestionProductPopup();
                } else {
                    checkOpeningHour(true);
                }
            });
        } else {
            checkOpeningHour(true);
        }
    }, [cartProducts, checkOpeningHour, currentStep, getSuggestionProduct, showSuggestionProductPopup]);

    const handleAfterLogin = useCallback((userData: UserDataModel, isSocial: boolean) => {
        if (!isSocial) {
            checkSuggestionProduct();
        } else {
            if (
                !userData?.first_name ||
                userData?.first_name.includes('@') ||
                /\d/.test(userData?.first_name) ||
                !userData.gsm ||
                !userData?.email
                || !validateEmail(userData?.email)
            ) {
                showUpdateInfoPopup();
            } else {
                checkSuggestionProduct();
            }
        }

    }, [checkSuggestionProduct, showUpdateInfoPopup]);

    const handleNextButtonClick = useCallback(() => {
        if (isUserLoggedIn) {
            const firstName = userData?.first_name;
            const phone = userData?.gsm;
            const email = userData?.email;
            if ((
                !firstName ||
                firstName.includes('@') ||
                /\d/.test(firstName) ||
                !phone ||
                !email
                || !validateEmail(email)
            )) {
                showUpdateInfoPopup();
            } else {
                checkSuggestionProduct();
            }
        } else {
            NavigationService.navigate(SCREENS.LOGIN_SCREEN,
                    {
                        callback: ({ userData, isSocial }: {userData: UserDataModel, isSocial: boolean}) => handleAfterLogin(userData, isSocial), fromCart: true
                    });
        }

    }, [checkSuggestionProduct, handleAfterLogin, isUserLoggedIn, showUpdateInfoPopup, userData]);

    const renderScene = useMemo(() => {
        switch (currentStep) {
            case 0:
                return (
                    <CartProductStep
                        ref={refProductStep}
                        setDisableNextButton={setDisableNextButton}
                        updateErrorMsg={setErrorMsg}
                        cartInfo={cartInfo}
                        updateCartInfo={updateCartInfo}
                        handleNextStep={handleNextStep}
                        updateAutoApplyLoyaltyDiscount={updateAutoApplyLoyaltyDiscount}
                    />
                );
            case 1:
                return (
                    <CartSelectDateStep
                        ref={refSelectDateStep}
                        setDisableNextButton={setDisableNextButton}
                        updateErrorMsg={setErrorMsg}
                        cartInfo={cartInfo}
                        updateCartInfo={updateCartInfo}
                        handleNextStep={handleNextStep}
                        handlePrevStep={handlePrevStep}
                    />
                );
            case 2:
                return (
                    <CartSelectPaymentMethodStep
                        ref={refPaymentMethodStep}
                        setDisableNextButton={setDisableNextButton}
                        updateErrorMsg={setErrorMsg}
                        cartInfo={cartInfo}
                        updateCartInfo={updateCartInfo}
                        handlePrevStep={handlePrevStep}
                        showPaymentTimeOutPopup={showPaymentTimeOutPopup}
                    />
                );
        }
    }, [cartInfo, currentStep, handleNextStep, handlePrevStep, showPaymentTimeOutPopup, updateAutoApplyLoyaltyDiscount, updateCartInfo]);

    const renderEmptyCart = useMemo(() => (
        <Animated.View
            entering={FadeIn}
            exiting={FadeOut}
            style={styles.emptyContainer}
        >
            <ShadowView
                style={{ shadowRadius: Dimens.H_40 }}
            >
                <View style={[styles.emptyWrapper, { backgroundColor: themeColors.color_cart_step_background }]}>
                    <TextComponent style={[styles.emptyText, { color: themeColors.color_common_description_text }]}>
                        {t(isTemplateOrGroupApp() ? 'text_shop_bag_empty' : 'text_empty_card_msg')}
                    </TextComponent>
                    <ButtonComponent
                        title={t(isTemplateOrGroupApp() ? 'text_view_assortment' : 'text_search_sell')}
                        style={styles.emptyBtn}
                        onPress={() => NavigationService.navigate(SCREENS.MENU_TAB_SCREEN)}
                    />
                </View>
            </ShadowView>
        </Animated.View>
    ), [Dimens.H_40, styles.emptyBtn, styles.emptyContainer, styles.emptyText, styles.emptyWrapper, t, themeColors.color_cart_step_background, themeColors.color_common_description_text]);

    const renderCartHeader = useMemo(() => (
        <CartHeader
            currentRestaurant={currentRestaurant}
            currentStep={currentStep}
        />
    ), [currentRestaurant, currentStep]);

    const renderCartTopPinnerMessage = useMemo(() => showDeliveryTopMessage ? (
        <Animated.View
            layout={Layout.duration(500)}
            entering={FadeIn}
            exiting={FadeOut}
            style={[styles.pinnerMessageContainer, { backgroundColor: themeColors.color_button_default }]}
        >
            <TextComponent style={styles.pinnerMsgText}>
                {`${t('cart_format_deliver_top_message', { value: `${formatCurrency(cartDeliveryFee?.free || 0, DEFAULT_CURRENCY)[2]}${cartDeliveryFee?.free || 0}` })}`}
            </TextComponent>
        </Animated.View>
    ) : null , [showDeliveryTopMessage, styles.pinnerMessageContainer, styles.pinnerMsgText, themeColors.color_button_default, t, cartDeliveryFee?.free]);

    const renderCartBottomPinnerLoyaltyMessage = useMemo(() => showLoyaltyBottomMessage ? (
        <TouchableComponent
            onPress={() => checkLoyaltyDiscount(true, true)}
            style={[styles.pinnerBottomMessageContainer, { backgroundColor: themeColors.color_button_default }]}
        >
            <TextComponent style={styles.pinnerMsgText}>
                {`${t('cart_apply_the_loyalty_card_discount')}`}
            </TextComponent>
        </TouchableComponent>
    ) : null , [checkLoyaltyDiscount, showLoyaltyBottomMessage, styles.pinnerBottomMessageContainer, styles.pinnerMsgText, t, themeColors.color_button_default]);

    const renderCartFooter = useMemo(() => (
        <CartFooter
            errorMsg={errorMsg}
            currentStep={currentStep}
            disableNextButton={disableNextButton}
            handleNextButtonClick={handleNextButtonClick}
            handleSelectStep={handleSelectStep}
        />
    ), [currentStep, disableNextButton, errorMsg, handleNextButtonClick, handleSelectStep]);

    const styleValues = useMemo(() => {
        let maxHeight;
        let marginTop;
        let marginBottom;

        if (showDeliveryTopMessage) {
            if (showLoyaltyBottomMessage) {
                maxHeight = height - Dimens.H_80;
                marginTop = 0;
                marginBottom = 0;
            } else {
                maxHeight = height - Dimens.H_50;
                marginTop = Dimens.H_30;
                marginBottom = 0;
            }
        } else {
            if (showLoyaltyBottomMessage) {
                maxHeight = height - Dimens.H_50;
                marginTop = 0;
                marginBottom = Dimens.H_30;
            } else {
                maxHeight = height - Dimens.H_20;
                marginTop = 0;
                marginBottom = 0;
            }
        }

        return {
            maxHeight,
            marginTop,
            marginBottom,
        };
    }, [Dimens.H_20, Dimens.H_30, Dimens.H_50, Dimens.H_80, height, showDeliveryTopMessage, showLoyaltyBottomMessage]);

    const renderScreenHeader = useMemo(() => (
        <HeaderComponent >
            <View style={{ flexDirection: 'row' }}>
                {currentStep > 0 && (
                    <Animated.View
                        entering={FadeInLeft}
                        exiting={FadeOutLeft}
                    >
                        <BackButton
                            onPress={handlePrevStep}
                        />
                    </Animated.View>
                )}
                <Animated.View
                    entering={FadeInLeft}
                    exiting={FadeOutLeft}
                >
                    <TextComponent
                        style={styles.headerText}
                    >
                        {t('cart_title')}
                    </TextComponent>
                    {cartRestaurant?.id ? (
                            <TextComponent
                                numberOfLines={1}
                                style={[styles.headerText, { lineHeight: undefined }]}
                            >
                                {cartRestaurant?.title || cartRestaurant?.name}
                            </TextComponent>
                            ) : null
                    }
                </Animated.View>
            </View>
        </HeaderComponent>
    ), [cartRestaurant?.id, cartRestaurant?.name, cartRestaurant?.title, currentStep, handlePrevStep, styles.headerText, t]);

    const renderCart = useMemo(() => (
        <Animated.View
            onLayout={onLayout}
            style={{ flex: 1, justifyContent: 'center' }}
        >
            {renderCartTopPinnerMessage}

            <ShadowView
                style={styles.cartContainer}
            >
                <Animated.View
                    layout={Layout.duration(500)}
                    style={[styles.cartWrapper,
                        {
                            backgroundColor: themeColors.color_cart_step_background,
                            maxHeight: styleValues.maxHeight,
                            marginTop: styleValues.marginTop,
                            marginBottom: styleValues.marginBottom,
                            overflow: 'hidden'
                        }]}
                >
                    {renderCartHeader}
                    {renderScene}
                    {renderCartFooter}
                </Animated.View>
            </ShadowView>

            {renderCartBottomPinnerLoyaltyMessage}
        </Animated.View>
    ), [onLayout, renderCartTopPinnerMessage, styles.cartContainer, styles.cartWrapper, themeColors.color_cart_step_background, styleValues.maxHeight, styleValues.marginTop, styleValues.marginBottom, renderCartHeader, renderScene, renderCartFooter, renderCartBottomPinnerLoyaltyMessage]);

    return (
        <TouchableWithoutFeedback
            disabled={!keyboardShown}
            onPress={() => Keyboard.dismiss()}
            style={{ flex: 1 }}
        >
            <View
                style={{ flex: 1 }}
            >
                {renderScreenHeader}
                {isEmptyCart ?  renderEmptyCart : renderCart}

                <InvalidOpeningHourDialog
                    isShow={isShowInvalidOpeningHourPopup}
                    hideModal={hideInvalidOpeningHourPopup}
                />

                <SuggestionProductDialog
                    isShow={isShowSuggestionProductPopup}
                    hideModal={hideSuggestionProductPopup}
                    suggestionProducts={suggestionProducts}
                    checkOpeningHour={checkOpeningHour}
                />

                <PaymentStepTimeOutDialog
                    isShow={isShowPaymentTimeOutPopup}
                    hideModal={hidePaymentTimeOutPopup}
                />

                <UpdateNameAndPhoneDialog
                    isShow={isShowUpdateInfoPopup}
                    hideModal={hideUpdateInfoPopup}
                    checkSuggestionProduct={checkSuggestionProduct}
                />

            </View>
        </TouchableWithoutFeedback>

    );
};

export default CartScreen;

const stylesF = (Dimens: DimensType) => StyleSheet.create({
    pinnerMsgText: {
        color: Colors.COLOR_WHITE,
        paddingVertical: Dimens.H_8,
        textAlign: 'center',
    },
    pinnerMessageContainer: {
        position: 'absolute',
        top: 0,
        left: 0,
        right: 0,
    },
    pinnerBottomMessageContainer: {
        position: 'absolute',
        bottom: 0,
        left: 0,
        right: 0,
    },
    cartWrapper: {
        paddingHorizontal: Dimens.W_25,
        paddingVertical: Dimens.W_22,
        borderRadius: Dimens.RADIUS_6,
    },
    cartContainer: {
        marginHorizontal: Dimens.W_12,
        shadowOffset: { width: 0, height: Dimens.H_10 }, shadowRadius: Dimens.H_30
    },
    emptyBtn: { marginHorizontal: Dimens.W_10, height: Dimens.W_46 },
    emptyText: {
        fontSize: Dimens.FONT_15,
        textAlign: 'center',
        marginBottom: Dimens.H_36,
    },
    emptyWrapper: {
        borderRadius: Dimens.RADIUS_6,
        paddingHorizontal: Dimens.W_46,
        paddingBottom: Dimens.W_40,
        paddingTop: Dimens.W_54,
    },
    emptyContainer: {
        flex: 1,
        paddingHorizontal: Dimens.W_16,
        justifyContent: 'center',
        alignItems: 'center',
    },
    headerText: {
        color: Colors.COLOR_WHITE,
        fontSize: Dimens.FONT_26,
        fontWeight: '700',
        lineHeight: Dimens.FONT_26,
        marginTop: -Dimens.H_2,
    },
});