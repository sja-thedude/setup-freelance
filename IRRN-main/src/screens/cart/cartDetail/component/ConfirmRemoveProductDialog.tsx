import React, { useCallback } from 'react';

import {
    InteractionManager,
    StyleSheet,
} from 'react-native';

import ButtonComponent from '@src/components/ButtonComponent';
import DialogComponent from '@src/components/DialogComponent';
import TextComponent from '@src/components/TextComponent';
import useDimens, { DimensType } from '@src/hooks/useDimens';
import useThemeColors from '@src/themes/useThemeColors';
import { useTranslation } from 'react-i18next';

interface ModalProps {
    isShow: boolean,
    hideModal: () => void,
    handleRemoveProduct: () => void,
}

const ConfirmRemoveProductDialog = ({ isShow, hideModal, handleRemoveProduct }: ModalProps) => {
    const { themeColors } = useThemeColors();
    const Dimens = useDimens();
    const styles = stylesF(Dimens);
    const { t } = useTranslation();

    const handleReturnPress = useCallback(() => {
        hideModal();
        InteractionManager.runAfterInteractions(() => {
            handleRemoveProduct();

        });
    }, [handleRemoveProduct, hideModal]);

    return (
        <DialogComponent
            hideModal={hideModal}
            isVisible={isShow}
        >
            <TextComponent style={styles.textTitle}>
                {t('cart_confirm_delete')}
            </TextComponent>

            <ButtonComponent
                title={t('btn_remove_cart_item')}
                style={styles.textAButton}
                onPress={handleReturnPress}
            />
            <ButtonComponent
                title={t('text_no_cancel')}
                style={styles.textBButton}
                styleTitle={{ color: themeColors.color_primary }}
                onPress={hideModal}
            />
        </DialogComponent>
    );
};

export default ConfirmRemoveProductDialog;

const stylesF = (Dimens: DimensType) => StyleSheet.create({
    textBButton: {
        width: '70%',
        alignSelf: 'center',
        backgroundColor: 'transparent'
    },
    textAButton: {
        width: '70%',
        alignSelf: 'center',
        marginTop: Dimens.H_34,
    },
    textTitle: {
        fontSize: Dimens.FONT_24,
        fontWeight: '700',
        textAlign: 'center',
        marginTop: Dimens.H_16,
        marginHorizontal: Dimens.W_24,
    },
});