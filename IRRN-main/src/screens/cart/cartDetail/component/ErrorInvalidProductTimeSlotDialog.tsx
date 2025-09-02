import React, { useCallback } from 'react';

import { StyleSheet } from 'react-native';

import ButtonComponent from '@src/components/ButtonComponent';
import DialogComponent from '@src/components/DialogComponent';
import TextComponent from '@src/components/TextComponent';
import useDimens, { DimensType } from '@src/hooks/useDimens';
import useThemeColors from '@src/themes/useThemeColors';
import { useTranslation } from 'react-i18next';

interface ModalProps {
    isShow: boolean,
    hideModal: () => void,
    message: string,
    popupCloseText: string,
    updateErrorMsg: Function,
    handlePrevStep: Function,
    updateCartInfo: Function,
    invalidDateTimeSlotProducts: Array<number>,
}

const ErrorInvalidProductTimeSlotDialog = ({ isShow, hideModal, message, popupCloseText, updateErrorMsg , handlePrevStep, updateCartInfo, invalidDateTimeSlotProducts }: ModalProps) => {
    const { themeColors } = useThemeColors();
    const Dimens = useDimens();
    const styles = stylesF(Dimens);
    const { t } = useTranslation();

    const handleReturnPress = useCallback(() => {
        updateErrorMsg(t('cart_product_are_not_available'));
        updateCartInfo({ invalidDateTimeSlotProducts: invalidDateTimeSlotProducts });
        handlePrevStep();
        hideModal();
    }, [handlePrevStep, hideModal, invalidDateTimeSlotProducts, t, updateCartInfo, updateErrorMsg]);

    return (
        <DialogComponent
            hideModal={hideModal}
            isVisible={isShow}
        >
            <TextComponent style={styles.textTitle}>
                {t('text_oops')}
            </TextComponent>

            <TextComponent style={styles.textMsg}>
                {message}
            </TextComponent>

            <ButtonComponent
                title={t('text_remove_product')}
                style={styles.textAButton}
                onPress={handleReturnPress}
            />
            <ButtonComponent
                title={popupCloseText}
                style={styles.textBButton}
                styleTitle={{ color: themeColors.color_primary }}
                onPress={hideModal}
            />
        </DialogComponent>
    );
};

export default ErrorInvalidProductTimeSlotDialog;

const stylesF = (Dimens: DimensType) => StyleSheet.create({
    textAButton: {
        width: '60%',
        alignSelf: 'center',
        marginTop: Dimens.H_34,
    },
    textBButton: {
        width: '60%',
        alignSelf: 'center',
        backgroundColor: 'transparent'
    },
    textTitle: {
        fontSize: Dimens.FONT_24,
        fontWeight: '700',
        textAlign: 'center',
        marginTop: Dimens.H_16,
        marginHorizontal: Dimens.W_24,
    },
    textMsg: {
        fontSize: Dimens.FONT_18,
        textAlign: 'center',
        marginTop: Dimens.H_16,
    },
});