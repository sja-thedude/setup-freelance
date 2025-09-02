import React, { useCallback } from 'react';

import { StyleSheet } from 'react-native';

import ButtonComponent from '@src/components/ButtonComponent';
import DialogComponent from '@src/components/DialogComponent';
import TextComponent from '@src/components/TextComponent';
import useDimens, { DimensType } from '@src/hooks/useDimens';
import NavigationService from '@src/navigation/NavigationService';
import { useTranslation } from 'react-i18next';

interface ModalProps {
    isShow: boolean,
    hideModal: () => void
}

const ErrorNotForSaleDialog = ({ isShow, hideModal }: ModalProps) => {
    const Dimens = useDimens();
    const styles = stylesF(Dimens);
    const { t } = useTranslation();

    const handleReturnPress = useCallback(() => {
        hideModal();
        NavigationService.pop(2);
    }, [hideModal]);

    return (
        <DialogComponent
            hideModal={hideModal}
            isVisible={isShow}
            swipeDirection={undefined}
            onBackdropPress={undefined}
            onBackButtonPress={undefined}
        >
            <TextComponent style={styles.textTitle}>
                {t('text_oops')}
            </TextComponent>

            <TextComponent style={styles.textMsg}>
                {t('reorder_no_grorup_category_support_delivery')}
            </TextComponent>

            <ButtonComponent
                title={t('text_return')}
                style={styles.textAButton}
                onPress={handleReturnPress}
            />
        </DialogComponent>
    );
};

export default ErrorNotForSaleDialog;

const stylesF = (Dimens: DimensType) => StyleSheet.create({
    textAButton: {
        width: '50%',
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
    textMsg: {
        fontSize: Dimens.FONT_18,
        textAlign: 'center',
        marginTop: Dimens.H_16,
    },
});