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
    Keyboard,
    StyleSheet,
    View,
} from 'react-native';
import Animated, {
    Easing,
    FadeInDown,
    FadeOut,
    Layout,
    SlideInLeft,
    SlideOutLeft,
} from 'react-native-reanimated';

import { KeyboardAwareScrollView, } from '@pietile-native-kit/keyboard-aware-scrollview';
import {
    CautionIcon,
    CloseRoundIcon,
    PlusIcon2,
    SubTrackIcon,
    TrashIcon,
} from '@src/assets/svg';
import InputComponent from '@src/components/InputComponent';
import TextComponent from '@src/components/TextComponent';
import TouchableComponent from '@src/components/TouchableComponent';
import {
    CART_DISCOUNT_TYPE,
    DEFAULT_CURRENCY,
    ORDER_TYPE,
} from '@src/configs/constants';
import {
    useAppDispatch,
    useAppSelector,
} from '@src/hooks';
import useBoolean from '@src/hooks/useBoolean';
import useDimens, { DimensType } from '@src/hooks/useDimens';
import { StorageActions } from '@src/redux/toolkit/actions/storageActions';
import { ProductInCart } from '@src/redux/toolkit/slices/storageSlice';
import useThemeColors from '@src/themes/useThemeColors';
import {
    calculateProductPrice,
    checkAvailableProductForGroup,
    getOrderPrices,
} from '@src/utils';
import formatCurrency from '@src/utils/currencyFormatUtil';

import { CartInfo } from '../CartScreen';
import ConfirmRemoveProductDialog from './ConfirmRemoveProductDialog';
import CouponInput from './CouponInput';

const AnimatedKeyboardAwareScrollView = Animated.createAnimatedComponent(KeyboardAwareScrollView);

interface IProps {
    updateErrorMsg: Function,
    cartInfo: CartInfo,
    updateCartInfo: Function,
    handleNextStep: Function,
    setDisableNextButton: Function,
    updateAutoApplyLoyaltyDiscount: Function,
}

const CartProductStep = forwardRef<any, IProps>(({ updateErrorMsg, updateCartInfo, cartInfo, handleNextStep, setDisableNextButton, updateAutoApplyLoyaltyDiscount }, ref: any) => {
    const { themeColors } = useThemeColors();
    const Dimens = useDimens();
    const styles = stylesF(Dimens);

    const dispatch = useAppDispatch();
    const { t } = useTranslation();

    const [selectedProductIndex, setSelectedProductIndex] = useState<number>();

    const [isShowConfirmRemoveProduct, showConfirmRemoveProduct, hideConfirmRemoveProduct] = useBoolean(false);

    const cartProducts = useAppSelector((state) => state.storageReducer.cartProducts.data);
    const groupData = useAppSelector((state) => state.storageReducer.cartProducts.groupFilter.groupData);
    const orderType = useAppSelector((state) => state.storageReducer.cartProducts.type);
    const cartDeliveryFee = useAppSelector((state) => state.storageReducer.cartProducts.deliveryInfo.deliveryFee);
    const discountInfo = useAppSelector((state) => state.storageReducer.cartProducts.discountInfo);
    const { isServiceCostOn, isServiceCostAlwaysCharge, serviceCost, serviceCostAmount } = useAppSelector((state) => state.storageReducer.cartProducts.serviceCostInfo || {});

    useEffect(() => {
        if (orderType === ORDER_TYPE.DELIVERY) {
            const { subTotal } = getOrderPrices(
                    cartProducts,
                    cartDeliveryFee,
                    discountInfo,
                    groupData,
                    orderType,
                    isServiceCostOn,
                    isServiceCostAlwaysCharge,
                    serviceCost,
                    serviceCostAmount
            );
            setDisableNextButton(Number(subTotal) < Number(cartDeliveryFee?.price_min));
        } else {
            setDisableNextButton(false);
        }
    }, [cartDeliveryFee, cartDeliveryFee?.price_min, cartProducts, discountInfo, groupData, isServiceCostAlwaysCharge, isServiceCostOn, orderType, serviceCost, serviceCostAmount, setDisableNextButton]);

    const checkProductError = useCallback((product: ProductInCart, index: number) => {
        let isError = cartInfo.invalidAvailableProducts.includes(product.id);

        if (!isError) {
            if (orderType === ORDER_TYPE.GROUP_ORDER) {
                const { isNotDelivery, isNotForSale } = checkAvailableProductForGroup(groupData, product);
                isError = isNotForSale || isNotDelivery;
            }

            if (!isError) {
                isError = cartInfo.invalidDateTimeSlotProducts.includes(product.id);
                if (orderType === ORDER_TYPE.DELIVERY && !isError) {
                    isError = cartInfo.invalidDeliveryProducts.includes(product.id);
                }
            }
        }

        if (!isError) {
            isError = cartInfo.invalidOptionProducts.map((p) => p.id).includes(product.id)
            && cartInfo.invalidOptionProducts.map((p) => p.cartPosition).includes(index);
        }

        return isError;
    }, [cartInfo.invalidAvailableProducts, cartInfo.invalidDateTimeSlotProducts, cartInfo.invalidDeliveryProducts, cartInfo.invalidOptionProducts, groupData, orderType]);

    const checkAnyError = useCallback(() => {
        let isError = false;
        let errorMsg = '';

        cartProducts.map((product) => {
            if (!isError) {
                isError = cartInfo.invalidAvailableProducts.includes(product.id);
                errorMsg = t('cart_product_are_not_available');
            }
        });

        if (!isError) {
            if (orderType === ORDER_TYPE.GROUP_ORDER) {
                cartProducts.map((product) => {
                    const { isNotDelivery, isNotForSale } = checkAvailableProductForGroup(groupData, product);
                    if (!isError) {
                        isError = isNotForSale || isNotDelivery;
                        errorMsg = isNotForSale ? t('cart_group_product_are_not_available') : isNotDelivery ? t('cart_product_is_not_available_for_now') : '';
                    }
                });
            }

            if (!isError) {
                cartProducts.map((product) => {
                    if (!isError) {
                        isError = cartInfo.invalidDateTimeSlotProducts.includes(product.id);
                        errorMsg = t('cart_product_are_not_available');

                        if (orderType === ORDER_TYPE.DELIVERY && !isError) {
                            isError = cartInfo.invalidDeliveryProducts.includes(product.id);
                            errorMsg = t('cart_product_is_not_available_for_now');
                        }
                    }
                });
            }
        }

        if (!isError) {
            cartProducts.map((product) => {
                if (!isError) {
                    isError = cartInfo.invalidOptionProducts.map((p) => p.id).includes(product.id);
                    errorMsg = t('cart_product_and_options_are_not_available');
                }
            });
        }

        if (isError) {
            updateErrorMsg(errorMsg);
        } else {
            updateErrorMsg('');
        }
    }, [cartInfo.invalidAvailableProducts, cartInfo.invalidDateTimeSlotProducts, cartInfo.invalidDeliveryProducts, cartInfo.invalidOptionProducts, cartProducts, groupData, orderType, t, updateErrorMsg]);

    useEffect(() => {
        checkAnyError();
    }, [checkAnyError]);

    const handleNext = useCallback(() => {
        handleNextStep();
    }, [handleNextStep]);

    useImperativeHandle(ref, () => ({
        handleNext
    }), [handleNext]);

    const handleChangeProductQuantity = useCallback((index: number, quantity: number) => {
        Keyboard.dismiss();
        if (quantity > 0) {
            dispatch(StorageActions.changeProductCartQuantity({ productIndex: index, quantity: quantity }));
        }
    }, [dispatch]);

    const handleRemoveProduct = useCallback(() => {
        // update invalid product if any
        if (selectedProductIndex) {
            const selectedProductId = cartProducts[selectedProductIndex].id;
            const isErrorProduct = cartInfo.invalidDateTimeSlotProducts.includes(selectedProductId);

            if (isErrorProduct) {
                const products = cartProducts.filter((p) => p.id === selectedProductId);
                if (products.length === 1) {
                    updateCartInfo({ invalidDateTimeSlotProducts: cartInfo.invalidDateTimeSlotProducts.filter((i) => i !== selectedProductId) });
                }
            }

        }

        dispatch(StorageActions.removeProductItemCart({ productIndex: selectedProductIndex }));
    }, [cartInfo.invalidDateTimeSlotProducts, cartProducts, dispatch, selectedProductIndex, updateCartInfo]);

    const handleClearDisCount = useCallback((autoApplyLoyaltyDiscount: boolean) => {
        updateAutoApplyLoyaltyDiscount(autoApplyLoyaltyDiscount);

        // clear current discount
        dispatch(StorageActions.clearStorageDiscount());
    }, [dispatch, updateAutoApplyLoyaltyDiscount]);

    const renderProductItem = useMemo(() => cartProducts.map((item, index) => {
        const isError = checkProductError(item, index);
        const itemPrice = calculateProductPrice(item);

        return (
            <Animated.View
                entering={FadeInDown}
                layout={Layout.easing(Easing.bounce).delay(index * 100)}
                exiting={FadeOut}
                key={`${index}${item.id}`}
                style={[styles.itemContainer, { borderBottomColor: themeColors.color_common_line }]}
            >
                <View style={styles.itemWrapper}>
                    <TextComponent
                        numberOfLines={2}
                        style={[styles.productName, { color: isError ? themeColors.color_error : themeColors.color_text }]}
                    >
                        {item.name}
                    </TextComponent>

                    <View style={styles.leftContainer}>
                        <View style={[styles.quantityContainer, { borderColor: themeColors.color_common_line }]}>
                            <TouchableComponent
                                hitSlop={Dimens.DEFAULT_HIT_SLOP}
                                onPress={() => handleChangeProductQuantity(index, item.quantity - 1)}
                                style={[styles.subTrackBtn, { borderRightColor: themeColors.color_common_line }]}
                            >
                                <SubTrackIcon
                                    stroke={themeColors.color_primary}
                                    width={Dimens.W_9}
                                    height={Dimens.W_16}
                                />
                            </TouchableComponent>

                            <TextComponent style={styles.quantityText}>
                                {item.quantity}
                            </TextComponent>

                            <TouchableComponent
                                hitSlop={Dimens.DEFAULT_HIT_SLOP}
                                onPress={() => handleChangeProductQuantity(index, item.quantity + 1)}
                                style={[styles.plusBtn, { borderLeftColor: themeColors.color_common_line }]}
                            >
                                <PlusIcon2
                                    stroke={themeColors.color_primary}
                                    width={Dimens.W_10}
                                    height={Dimens.W_14}
                                />
                            </TouchableComponent>
                        </View>

                        <TextComponent style={[styles.priceText, { color: isError ? themeColors.color_error : themeColors.color_text }]}>
                            {`${formatCurrency(itemPrice.toFixed(2), DEFAULT_CURRENCY)[2]}${itemPrice.toFixed(2)}`} {''}
                        </TextComponent>

                        <View style={styles.deleteContainer}>
                            <TouchableComponent
                                hitSlop={Dimens.DEFAULT_HIT_SLOP}
                                onPress={() => {
                                    showConfirmRemoveProduct();
                                    setSelectedProductIndex(index);
                                }}
                                style={styles.deleteIcon}
                            >
                                <TrashIcon
                                    stroke={isError ? themeColors.color_error : themeColors.color_primary}
                                    width={Dimens.W_14}
                                    height={Dimens.W_14}
                                />
                            </TouchableComponent>

                            {isError ? (
                                <CautionIcon
                                    width={Dimens.W_15}
                                    height={Dimens.W_15}
                                />
                            ) : (
                                <View style={styles.placeholderView}/>
                            )}

                        </View>
                    </View>
                </View>

                {item.options?.length ? (
                    <View style={{ marginVertical: Dimens.H_4 }}>
                        {item.options?.filter((x) => x.items.length > 0).map((option, oIndex) => {
                            let label = option.is_ingredient_deletion ? `- ${t('cart_item_zonder')}` : '-';
                            const iSelectedMaster = option.items.find((io) => io.master);

                            if (iSelectedMaster) {
                                label = label + ' ' + iSelectedMaster.name;
                            } else {
                                option?.items.map((itemOption, idx) => {
                                    label = label + `${idx === 0 ? '' : ','} ${itemOption.name}`;
                                });
                            }

                            return (
                                <TextComponent
                                    key={oIndex}
                                    style={[styles.optionText, { color: isError ? themeColors.color_error : themeColors.color_common_subtext }]}
                                >
                                    {label}
                                </TextComponent>
                            );
                        })}
                    </View>
                ) : null}

            </Animated.View>
        );
    }), [Dimens.DEFAULT_HIT_SLOP, Dimens.H_4, Dimens.W_10, Dimens.W_14, Dimens.W_15, Dimens.W_16, Dimens.W_9, cartProducts, checkProductError, handleChangeProductQuantity, showConfirmRemoveProduct, styles.deleteContainer, styles.deleteIcon, styles.itemContainer, styles.itemWrapper, styles.leftContainer, styles.optionText, styles.placeholderView, styles.plusBtn, styles.priceText, styles.productName, styles.quantityContainer, styles.quantityText, styles.subTrackBtn, t, themeColors.color_common_line, themeColors.color_common_subtext, themeColors.color_error, themeColors.color_primary, themeColors.color_text]);

    const renderPrice = useMemo(() => {
        const { subTotal, groupDiscount, couponDiscount, loyaltyDiscount, deliveryFee, total, serviceCostPrice } = getOrderPrices(
                cartProducts,
                cartDeliveryFee,
                discountInfo,
                groupData,
                orderType,
                isServiceCostOn,
                isServiceCostAlwaysCharge,
                serviceCost,
                serviceCostAmount
        );
        const discountType = discountInfo?.discountType;

        return (
            <Animated.View
                layout={Layout.duration(500)}
                style={{ marginTop: Dimens.H_10 }}
            >
                {(discountType || orderType === ORDER_TYPE.DELIVERY) ? (
                    <View style={styles.totalContainer}>
                        <TextComponent style={[styles.subTotalTitle, { color: themeColors.color_common_description_text }]}>
                            {t('text_subtotal')}
                        </TextComponent>
                        <View style={styles.priceContainer}>
                            <TextComponent style={[styles.subTotalTitle, { color: themeColors.color_common_description_text }]}>
                                {`${formatCurrency(subTotal, DEFAULT_CURRENCY)[2]}${subTotal}`}
                            </TextComponent>
                            <TouchableComponent
                                style={styles.xIcon}
                            >
                                <View
                                    style={styles.placeXIcon}
                                />
                            </TouchableComponent>
                        </View>
                    </View>
                ) : null}

                {discountType === CART_DISCOUNT_TYPE.LOYALTY_DISCOUNT ? (
                    <View style={styles.totalContainer}>
                        <TextComponent style={[styles.subTotalTitle, { color: themeColors.color_common_description_text }]}>
                            {t('text_reward_discount')}
                        </TextComponent>
                        <View style={styles.priceContainer}>
                            <TextComponent style={[styles.subTotalTitle, { color: themeColors.color_common_description_text }]}>
                                {`- ${formatCurrency(loyaltyDiscount, DEFAULT_CURRENCY)[2]}${loyaltyDiscount}`}
                            </TextComponent>
                            <TouchableComponent
                                hitSlop={Dimens.DEFAULT_HIT_SLOP_SMALL}
                                onPress={() => handleClearDisCount(true)}
                                style={styles.xIcon}
                            >
                                <CloseRoundIcon
                                    width={Dimens.W_14}
                                    height={Dimens.W_14}
                                />
                            </TouchableComponent>
                        </View>
                    </View>
                ) : null}

                {discountType === CART_DISCOUNT_TYPE.GROUP_DISCOUNT ? (
                    <View style={styles.totalContainer}>
                        <TextComponent style={[styles.subTotalTitle, { color: themeColors.color_common_description_text }]}>
                            {t('text_group_discount')}
                        </TextComponent>
                        <View style={styles.priceContainer}>
                            <TextComponent style={[styles.subTotalTitle, { color: themeColors.color_common_description_text }]}>
                                {`- ${formatCurrency(groupDiscount, DEFAULT_CURRENCY)[2]}${groupDiscount}`}
                            </TextComponent>
                            <TouchableComponent
                                style={styles.xIcon}
                            >
                                <View
                                    style={styles.placeXIcon}
                                />
                            </TouchableComponent>
                        </View>
                    </View>
                ) : null}

                {discountType === CART_DISCOUNT_TYPE.COUPON_DISCOUNT ? (
                    <View style={styles.totalContainer}>
                        <TextComponent style={[styles.subTotalTitle, { color: themeColors.color_common_description_text }]}>
                            {t('text_coupon_discount')}
                        </TextComponent>
                        <View style={styles.priceContainer}>
                            <TextComponent style={[styles.subTotalTitle, { color: themeColors.color_common_description_text }]}>
                                {`- ${formatCurrency(couponDiscount, DEFAULT_CURRENCY)[2]}${couponDiscount}`}
                            </TextComponent>
                            <TouchableComponent
                                hitSlop={Dimens.DEFAULT_HIT_SLOP_SMALL}
                                onPress={() => handleClearDisCount(false)}
                                style={styles.xIcon}
                            >
                                <CloseRoundIcon
                                    width={Dimens.W_14}
                                    height={Dimens.W_14}
                                />
                            </TouchableComponent>
                        </View>
                    </View>
                ) : null}

                {(isServiceCostOn && orderType !== ORDER_TYPE.GROUP_ORDER) && (
                    <View style={styles.totalContainer}>
                        <TextComponent style={[styles.subTotalTitle, { color: themeColors.color_common_description_text }]}>
                            {t('Servicekost')}
                        </TextComponent>
                        <View style={styles.priceContainer}>
                            <TextComponent style={[styles.subTotalTitle, { color: themeColors.color_common_description_text }]}>
                                {`${formatCurrency(serviceCostPrice)[2]}${serviceCostPrice}`}
                            </TextComponent>
                            <TouchableComponent
                                style={styles.xIcon}
                            >
                                <View
                                    style={styles.placeXIcon}
                                />
                            </TouchableComponent>
                        </View>
                    </View>
                )}

                {orderType === ORDER_TYPE.DELIVERY && (
                    <View style={styles.totalContainer}>
                        <TextComponent style={[styles.subTotalTitle, { color: themeColors.color_common_description_text }]}>
                            {t('cart_deliver_fee')}
                        </TextComponent>
                        <View style={styles.priceContainer}>
                            <TextComponent style={[styles.subTotalTitle, { color: themeColors.color_common_description_text }]}>
                                {`${formatCurrency(deliveryFee, DEFAULT_CURRENCY)[2]}${deliveryFee}`}
                            </TextComponent>
                            <TouchableComponent
                                style={styles.xIcon}
                            >
                                <View
                                    style={styles.placeXIcon}
                                />
                            </TouchableComponent>
                        </View>
                    </View>
                )}

                <View style={styles.mainTotal}>
                    <TextComponent style={styles.totalTitle}>
                        {t('text_total')}
                    </TextComponent>
                    <View style={styles.priceContainer}>
                        <TextComponent style={styles.totalValue}>
                            {`${formatCurrency(total, DEFAULT_CURRENCY)[2]}${total}`}
                        </TextComponent>
                        <TouchableComponent
                            style={styles.xIcon}
                        >
                            <View
                                style={styles.placeXIcon}
                            />
                        </TouchableComponent>
                    </View>
                </View>
            </Animated.View>
        );
    }, [Dimens.DEFAULT_HIT_SLOP_SMALL, Dimens.H_10, Dimens.W_14, cartDeliveryFee, cartProducts, discountInfo, groupData, handleClearDisCount, isServiceCostAlwaysCharge, isServiceCostOn, orderType, serviceCost, serviceCostAmount, styles.mainTotal, styles.placeXIcon, styles.priceContainer, styles.subTotalTitle, styles.totalContainer, styles.totalTitle, styles.totalValue, styles.xIcon, t, themeColors.color_common_description_text]);

    const renderBottom = useMemo(() => (
        <Animated.View
            layout={Layout.duration(500)}
            style={styles.bottomContainer}
        >
            <CouponInput/>
            <InputComponent
                containerStyle={styles.inputNoteContainer}
                style={styles.inputNote}
                autoCapitalize={'none'}
                multiline
                maxLength={100}
                placeholder={t('text_comment')}
                borderInput={themeColors.color_common_line}
                value={cartInfo.note}
                onChangeText={(text) => {
                    updateCartInfo({ note: text });
                }}
            />
        </Animated.View>
    ), [cartInfo.note, styles.bottomContainer, styles.inputNote, styles.inputNoteContainer, t, themeColors.color_common_line, updateCartInfo]);

    return (
        <>
            <AnimatedKeyboardAwareScrollView
                entering={SlideInLeft.duration(500)}
                exiting={SlideOutLeft.duration(500)}
                layout={Layout.duration(500)}
                showsVerticalScrollIndicator={false}
                keyboardShouldPersistTaps={'handled'}
                extraHeight={0}
            >
                <Animated.View
                    layout={Layout.duration(500)}
                >
                    {renderProductItem}
                    {renderPrice}
                    {renderBottom}
                </Animated.View>
            </AnimatedKeyboardAwareScrollView>
            <ConfirmRemoveProductDialog
                isShow={isShowConfirmRemoveProduct}
                hideModal={hideConfirmRemoveProduct}
                handleRemoveProduct={handleRemoveProduct}
            />
        </>

    );
});

export default memo(CartProductStep);

const stylesF = (Dimens: DimensType) => StyleSheet.create({
    placeXIcon: { width: Dimens.W_14 },
    xIcon: { marginLeft: Dimens.W_3 },
    priceContainer: { flexDirection: 'row', alignItems: 'center' },
    bottomContainer: { marginVertical: Dimens.H_16 },
    subTotalTitle: { fontSize: Dimens.FONT_12 },
    totalValue: { fontSize: Dimens.FONT_16, fontWeight: '700' },
    totalTitle: { fontSize: Dimens.FONT_16, fontWeight: '700' },
    mainTotal: {
        flexDirection: 'row',
        alignItems: 'center',
        justifyContent: 'space-between',
    },
    totalContainer: {
        flexDirection: 'row',
        alignItems: 'center',
        justifyContent: 'space-between',
        marginBottom: Dimens.H_2,
    },
    optionText: {
        marginLeft: Dimens.W_10,
        marginVertical: Dimens.H_2,
        fontSize: Dimens.FONT_13,
        flex: 1.5,
    },
    deleteIcon: { marginRight: Dimens.W_2 },
    placeholderView: { width: Dimens.W_15, height: Dimens.W_15 },
    deleteContainer: {
        flexDirection: 'row',
        alignItems: 'center',
        justifyContent: 'flex-end',
    },
    priceText: {
        fontSize: Dimens.FONT_14,
        fontWeight: '500',
        flex: 1,
        textAlign: 'center',
    },
    plusBtn: { borderLeftWidth: 1, paddingHorizontal: Dimens.W_5 },
    quantityText: {
        fontSize: Dimens.FONT_11,
        fontWeight: '700',
        marginHorizontal: Dimens.W_8,
        minWidth: Dimens.W_10,
        textAlign: 'center',
    },
    subTrackBtn: { borderRightWidth: 1, paddingHorizontal: Dimens.W_5 },
    quantityContainer: {
        flexDirection: 'row',
        alignItems: 'center',
        borderRadius: Dimens.RADIUS_10,
        borderWidth: 1,
        paddingVertical: Dimens.H_3,
        marginHorizontal: Dimens.W_10,
    },
    leftContainer: {
        flex: 2,
        flexDirection: 'row',
        alignItems: 'center',
        justifyContent: 'space-between',
    },
    productName: { fontSize: Dimens.FONT_14, flex: 1.5, fontWeight: '500' },
    itemWrapper: { flexDirection: 'row', alignItems: 'center' },
    itemContainer: { borderBottomWidth: 1, paddingVertical: Dimens.H_6 },
    inputNote: {
        fontSize: Dimens.FONT_15,
        paddingTop: Dimens.H_10,
        textAlignVertical: 'top',
    },
    inputNoteContainer: { height: Dimens.H_110 },
});