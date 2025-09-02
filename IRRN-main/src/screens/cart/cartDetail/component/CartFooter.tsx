import React, {
    FC,
    memo,
    useMemo,
} from 'react';

import { useTranslation } from 'react-i18next';
import {
    StyleSheet,
    View,
} from 'react-native';
import Animated, { Layout } from 'react-native-reanimated';

import ButtonComponent from '@src/components/ButtonComponent';
import TextComponent from '@src/components/TextComponent';
import TouchableComponent from '@src/components/TouchableComponent';
import { Colors } from '@src/configs';
import {
    DEFAULT_CURRENCY,
    ORDER_TYPE,
} from '@src/configs/constants';
import { useAppSelector } from '@src/hooks';
import useDimens, { DimensType } from '@src/hooks/useDimens';
import useThemeColors from '@src/themes/useThemeColors';
import { isEmptyOrUndefined } from '@src/utils';
import formatCurrency from '@src/utils/currencyFormatUtil';

interface IProps {
    errorMsg?: string,
    currentStep: number,
    disableNextButton: boolean,
    handleNextButtonClick: any,
    handleSelectStep: Function,
}

const CartFooter: FC<IProps> = ({ errorMsg, currentStep, disableNextButton, handleNextButtonClick, handleSelectStep }) => {
    const { themeColors } = useThemeColors();
    const Dimens = useDimens();
    const styles = stylesF(Dimens);

    const { t } = useTranslation();

    const cartOrderType = useAppSelector((state) => state.storageReducer.cartProducts.type);
    const cartDeliveryFee = useAppSelector((state) => state.storageReducer.cartProducts.deliveryInfo.deliveryFee);
    const cartRestaurant = useAppSelector((state) => state.storageReducer.cartProducts.restaurant);
    const orderType = useAppSelector((state) => state.storageReducer.cartProducts.type);
    const { isServiceCostOn, isServiceCostAlwaysCharge, serviceCost, serviceCostAmount } = useAppSelector((state) => state.storageReducer.cartProducts.serviceCostInfo || {});

    const renderServiceCostMessage = useMemo(() => !errorMsg && isServiceCostOn && orderType !== ORDER_TYPE.GROUP_ORDER && currentStep === 0 ? (
        isServiceCostAlwaysCharge ? (
            <TextComponent style={[styles.textMsg, { color: themeColors.color_common_description_text }]}>
                {cartRestaurant?.title || cartRestaurant?.name} {t('hanteert een servicekost van')}
                <TextComponent style={[styles.textMsg, { color: themeColors.color_common_description_text, fontWeight: '700' }]}>
                    {` ${formatCurrency(serviceCost, DEFAULT_CURRENCY)[2]}${serviceCost} `}
                </TextComponent>
            </TextComponent>
        ) : (
            <TextComponent style={[styles.textMsg, { color: themeColors.color_common_description_text }]}>
                {cartRestaurant?.title || cartRestaurant?.name} {t('hanteert een servicekost van')}
                <TextComponent style={[styles.textMsg, { color: themeColors.color_common_description_text, fontWeight: '700' }]}>
                    {` ${formatCurrency(serviceCost, DEFAULT_CURRENCY)[2]}${serviceCost} `}
                </TextComponent>
                {t('tot een bestelbedrag van')}
                <TextComponent style={[styles.textMsg, { color: themeColors.color_common_description_text, fontWeight: '700' }]}>
                    {` ${formatCurrency(serviceCostAmount, DEFAULT_CURRENCY)[2]}${serviceCostAmount}`}
                </TextComponent>
            </TextComponent>
        )
    ) : null, [cartRestaurant?.name, cartRestaurant?.title, currentStep, errorMsg, isServiceCostAlwaysCharge, isServiceCostOn, orderType, serviceCost, serviceCostAmount, styles.textMsg, t, themeColors.color_common_description_text]);

    const renderDeliveryFeeMessage = useMemo(() => !errorMsg && orderType === ORDER_TYPE.DELIVERY && currentStep === 0 ? (
        <TextComponent style={[styles.textMsg, { color: themeColors.color_common_description_text }]}>
            {cartRestaurant?.title || cartRestaurant?.name} {t('cart_validate_minimum_order_deliver')}
            <TextComponent style={[styles.textMsg, { color: themeColors.color_common_description_text, fontWeight: '700' }]}>
                {` ${formatCurrency(cartDeliveryFee?.price_min || 0, DEFAULT_CURRENCY)[2]}${cartDeliveryFee?.price_min || 0} `}
            </TextComponent>
            {t('cart_exclude_delivery_fee')}
        </TextComponent>
    ) : null, [cartDeliveryFee?.price_min, cartRestaurant?.name, cartRestaurant?.title, currentStep, errorMsg, orderType, styles.textMsg, t, themeColors.color_common_description_text]);

    const renderErrorMessage = useMemo(() => errorMsg ? (
            <TextComponent style={[styles.textError, { color: themeColors.color_error }]}>
                {errorMsg}
            </TextComponent>
        ) : null, [errorMsg, styles.textError, themeColors.color_error]);

    const renderStepIndicator = useMemo(() => (
        <View style={styles.stepContainer}>
            <TouchableComponent
                onPress={() => handleSelectStep(0)}
                style={styles.stepTouch}
            >
                <View style={[styles.stepButtonContainer, { backgroundColor: currentStep >= 0 ? themeColors.color_primary : themeColors.color_dot_inactive }]}>
                    <TextComponent style={styles.stepBtnText}>
                                1
                    </TextComponent>
                </View>
                <TextComponent style={[styles.stepBtnTitle, { color: currentStep >= 0 ? themeColors.color_primary : themeColors.color_dot_inactive }]}>
                    {t('cart_title')}
                </TextComponent>
            </TouchableComponent>

            <TouchableComponent
                disabled={currentStep === 0}
                onPress={() => handleSelectStep(1)}
                style={styles.stepTouch}
            >
                <View style={[styles.stepButtonContainer, { backgroundColor: currentStep >= 1 ? themeColors.color_primary : themeColors.color_dot_inactive }]}>
                    <TextComponent style={styles.stepBtnText}>
                                2
                    </TextComponent>
                </View>
                <TextComponent style={[styles.stepBtnTitle, { color: currentStep >= 1 ? themeColors.color_primary : themeColors.color_dot_inactive }]}>
                    {cartOrderType === ORDER_TYPE.GROUP_ORDER ? t('shopping_cart_date') : t('shopping_cart_date_time')}
                </TextComponent>
            </TouchableComponent>

            <TouchableComponent
                disabled={currentStep === 0 || currentStep === 1}
                onPress={() => handleSelectStep(2)}
                style={styles.stepTouch}
            >
                <View style={[styles.stepButtonContainer, { backgroundColor: currentStep === 2 ? themeColors.color_primary : themeColors.color_dot_inactive }]}>
                    <TextComponent style={styles.stepBtnText}>
                                3
                    </TextComponent>
                </View>
                <TextComponent style={[styles.stepBtnTitle, { color: currentStep === 2 ? themeColors.color_primary : themeColors.color_dot_inactive }]}>
                    {t('shopping_cart_payment_method')}
                </TextComponent>
            </TouchableComponent>
        </View>
    ), [cartOrderType, currentStep, handleSelectStep, styles.stepBtnText, styles.stepBtnTitle, styles.stepButtonContainer, styles.stepContainer, styles.stepTouch, t, themeColors.color_dot_inactive, themeColors.color_primary]);

    const renderNextButton = useMemo(() => (
        <ButtonComponent
            disabled={!isEmptyOrUndefined(errorMsg) || disableNextButton}
            title={t('text_further')}
            style={styles.btnNext}
            styleTitle={{ fontWeight: '600' }}
            onPress={handleNextButtonClick}
        />
    ), [disableNextButton, errorMsg, handleNextButtonClick, styles.btnNext, t]);
    return (
        <Animated.View
            layout={Layout.duration(500)}
        >
            {renderNextButton}
            {renderErrorMessage}
            {renderServiceCostMessage}
            {renderDeliveryFeeMessage}
            {renderStepIndicator}
        </Animated.View>
    );
};

export default memo(CartFooter);

const stylesF = (Dimens: DimensType) => StyleSheet.create({
    stepContainer: {
        flexDirection: 'row',
        alignItems: 'center',
        justifyContent: 'space-between',
        marginTop: Dimens.H_12,
    },
    textError: { fontSize: Dimens.FONT_12, textAlign: 'center' },
    textMsg: { fontSize: Dimens.FONT_12, textAlign: 'center' },
    btnNext: {
        width: '50%',
        alignSelf: 'center',
        height: Dimens.W_46,
        marginTop: Dimens.H_20,
        marginBottom: Dimens.H_10,
    },
    stepTouch: { alignItems: 'center', flex: 1 },
    stepBtnTitle: {
        textAlign: 'center',
        fontSize: Dimens.FONT_10,
        fontWeight: '700',
        marginTop: Dimens.H_2,
    },
    stepBtnText: {
        textAlign: 'center',
        fontSize: Dimens.FONT_14,
        fontWeight: '600',
        color: Colors.COLOR_WHITE,
    },
    stepButtonContainer: {
        justifyContent: 'center',
        borderRadius: Dimens.H_999,
        width: Dimens.W_20,
        height: Dimens.W_20,
    },
});