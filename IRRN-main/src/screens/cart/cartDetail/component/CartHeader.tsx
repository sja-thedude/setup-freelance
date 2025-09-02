import React, {
    FC,
    memo,
    useCallback,
    useMemo,
} from 'react';

import { useTranslation } from 'react-i18next';
import {
    StyleSheet,
    View,
} from 'react-native';
import Animated, { Layout } from 'react-native-reanimated';

import { DIALOG_TYPE, } from '@screens/cart/selectOrderType/SelectOrderTypeScreen';
import TextComponent from '@src/components/TextComponent';
import TouchableComponent from '@src/components/TouchableComponent';
import { Colors } from '@src/configs';
import {
    CART_DISCOUNT_TYPE,
    ORDER_TYPE,
    RESTAURANT_EXTRA_TYPE,
} from '@src/configs/constants';
import {
    useAppDispatch,
    useAppSelector,
} from '@src/hooks';
import useDimens, { DimensType } from '@src/hooks/useDimens';
import { SCREENS } from '@src/navigation/config/screenName';
import NavigationService from '@src/navigation/NavigationService';
import { RestaurantDetailModel, } from '@src/network/dataModels/RestaurantDetailModel';
import { StorageActions } from '@src/redux/toolkit/actions/storageActions';
import useThemeColors from '@src/themes/useThemeColors';

interface IProps {
    currentRestaurant?: RestaurantDetailModel,
    currentStep: number,
}

const CartHeader: FC<IProps> = ({ currentRestaurant, currentStep }) => {
    const { themeColors } = useThemeColors();
    const Dimens = useDimens();
    const styles = stylesF(Dimens);

    const { t } = useTranslation();
    const dispatch = useAppDispatch();

    const groupData = useAppSelector((state) => state.storageReducer.cartProducts.groupFilter.groupData);
    const cartProducts = useAppSelector((state) => state.storageReducer.cartProducts.data);
    const cartOrderType = useAppSelector((state) => state.storageReducer.cartProducts.type);
    const discountInfo = useAppSelector((state) => state.storageReducer.cartProducts.discountInfo);

    const groupOrderOn = useMemo(() => currentRestaurant?.extras.find((item) => item.type === RESTAURANT_EXTRA_TYPE.GROUP_ORDER)?.active, [currentRestaurant]);

    const handleOpenSelectTypePopup = useCallback(() => {
        NavigationService.navigate(SCREENS.SELECT_ORDER_TYPE_SCREEN, { isInCart: true, product: cartProducts[0] });
    }, [cartProducts]);

    const handleSelectTakeOut = useCallback(() => {
        dispatch(StorageActions.clearStorageGroupFilter());
        dispatch(StorageActions.clearStorageDeliveryInfo());
        if (discountInfo?.discountType === CART_DISCOUNT_TYPE.GROUP_DISCOUNT) {
            dispatch(StorageActions.clearStorageDiscount());
        }
        dispatch(StorageActions.setStorageCartType(ORDER_TYPE.TAKE_AWAY));
    }, [discountInfo?.discountType, dispatch]);

    const handleSelectDelivery = useCallback(() => {
        NavigationService.navigate(SCREENS.SELECT_ORDER_TYPE_SCREEN, { isInCart: true, product: cartProducts[0], defaultPopup: DIALOG_TYPE.SELECT_ADDRESS });
    }, [cartProducts]);

    const cartStepTitle = useMemo(() => {
        switch (currentStep) {
            case 0:
                return t('shopping_cart');
            case 1:
                return cartOrderType === ORDER_TYPE.GROUP_ORDER ? t('shopping_cart_date') : t('shopping_cart_date_time');
            case 2:
                return t('shopping_cart_payment_method');
        }
    }, [cartOrderType, currentStep, t]);

    const renderTakeOutType = useMemo(() => (
        <TouchableComponent
            onPress={handleSelectTakeOut}
            disabled={cartOrderType === ORDER_TYPE.TAKE_AWAY}
            style={[styles.typeTakeOutBtn, { backgroundColor: cartOrderType === ORDER_TYPE.TAKE_AWAY ? themeColors.color_button_default : 'transparent' }]}
        >
            <TextComponent style={[styles.typeBtnText, { color: cartOrderType === ORDER_TYPE.TAKE_AWAY ? Colors.COLOR_WHITE : themeColors.color_button_default }]}>
                {t('text_pick_up').toUpperCase()}
            </TextComponent>
        </TouchableComponent>
    ), [cartOrderType, handleSelectTakeOut, styles.typeBtnText, styles.typeTakeOutBtn, t, themeColors.color_button_default]);

    const renderDeliveryType = useMemo(() => (
        <TouchableComponent
            onPress={handleSelectDelivery}
            disabled={cartOrderType === ORDER_TYPE.DELIVERY}
            style={[styles.typeTakeOutBtn, { backgroundColor: cartOrderType === ORDER_TYPE.DELIVERY ? themeColors.color_button_default : 'transparent' }]}
        >
            <TextComponent style={[styles.typeBtnText, { color: cartOrderType === ORDER_TYPE.DELIVERY ? Colors.COLOR_WHITE : themeColors.color_button_default }]}>
                {t('text_delivery').toUpperCase()}
            </TextComponent>
        </TouchableComponent>
    ), [cartOrderType, handleSelectDelivery, styles.typeBtnText, styles.typeTakeOutBtn, t, themeColors.color_button_default]);

    const renderChangeOrderTypeBtn = useMemo(() => currentStep === 0 && groupOrderOn ? (
            <TouchableComponent
                hitSlop={Dimens.DEFAULT_HIT_SLOP}
                onPress={handleOpenSelectTypePopup}
                style={{ alignSelf: 'center' }}
            >
                <TextComponent
                    style={styles.headerTextChangeType}
                >
                    {t('shopping_cart_change_order_type')}
                </TextComponent>
            </TouchableComponent>
        ) : null, [Dimens.DEFAULT_HIT_SLOP, currentStep, groupOrderOn, handleOpenSelectTypePopup, styles.headerTextChangeType, t]);

    const renderCartStepTitle = useMemo(() => (
        <TextComponent
            style={styles.headerPageTitle}
        >
            {cartStepTitle}
        </TextComponent>
    ), [cartStepTitle, styles.headerPageTitle]);

    return (
        <Animated.View
            layout={Layout.duration(500)}
        >
            {cartOrderType === ORDER_TYPE.GROUP_ORDER ? (
                <TextComponent style={[styles.headerGroupName, { color: themeColors.color_primary }]}>
                    {`${t('cart_group_label', { group_name: groupData?.name })}`}
                </TextComponent>
            ) : (
                <View style={[styles.typeContainer, { borderColor: themeColors.color_button_default }]}>
                    {currentStep === 0 ? (
                        <>
                            {renderTakeOutType}
                            {renderDeliveryType}
                        </>
                    ) : (
                        <>
                            {cartOrderType === ORDER_TYPE.TAKE_AWAY ? (
                                renderTakeOutType
                            ) : (
                                renderDeliveryType
                            )}
                        </>
                    )}

                </View>
            )}

            {renderChangeOrderTypeBtn}
            {renderCartStepTitle}

            <View style={[styles.titleUnderLine, { borderTopColor: themeColors.color_common_line }]}/>
        </Animated.View>
    );
};

export default memo(CartHeader);

const stylesF = (Dimens: DimensType) => StyleSheet.create({
    typeBtnText: { textAlign: 'center', fontWeight: '600' },
    typeTakeOutBtn: {
        justifyContent: 'center',
        borderRadius: Dimens.RADIUS_4,
        width: Dimens.SCREEN_WIDTH / 4,
    },
    typeContainer: {
        marginBottom: Dimens.H_6,
        flexDirection: 'row',
        alignSelf: 'center',
        justifyContent: 'center',
        borderRadius: Dimens.RADIUS_6,
        borderWidth: 1,
        height: Dimens.W_40,
        overflow: 'hidden'
    },
    titleUnderLine: {
        width: Dimens.W_46,
        borderTopWidth: 1,
        marginTop: Dimens.H_2,
        marginBottom: Dimens.H_8,
    },
    headerPageTitle: {
        fontSize: Dimens.FONT_16,
        fontWeight: '700',
        marginTop: Dimens.H_16,
    },
    headerTextChangeType: {
        fontSize: Dimens.FONT_12,
        textAlign: 'center',
        textDecorationLine: 'underline',
    },
    headerGroupName: {
        fontSize: Dimens.FONT_18,
        fontWeight: '700',
        textAlign: 'center',
        marginBottom: Dimens.H_3,
    },
});