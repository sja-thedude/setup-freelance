import React, { useCallback } from 'react';

import {
    StyleSheet,
    View,
} from 'react-native';

import ButtonComponent from '@src/components/ButtonComponent';
import DialogComponent from '@src/components/DialogComponent';
import TextComponent from '@src/components/TextComponent';
import useDimens, { DimensType } from '@src/hooks/useDimens';
import { useTranslation } from 'react-i18next';

interface ModalProps {
    isShow: boolean,
    hideModal: () => void,
}

const PaymentStepTimeOutDialog = ({ isShow, hideModal }: ModalProps) => {
    const Dimens = useDimens();
    const styles = stylesF(Dimens);
    const { t } = useTranslation();

    const handleReturnPress = useCallback(() => {
        hideModal();
    }, [hideModal]);

    return (
        <DialogComponent
            hideModal={hideModal}
            isVisible={isShow}
        >
            <View style={styles.container}>
                <TextComponent style={styles.textTitle}>
                    {t('text_oeps')}
                </TextComponent>

                <TextComponent style={styles.textMsg}>
                    {t('cart_order_out_of_time')}
                </TextComponent>
            </View>

            <ButtonComponent
                title={t('text_return')}
                style={styles.textAButton}
                onPress={handleReturnPress}
            />
        </DialogComponent>
    );
};

export default PaymentStepTimeOutDialog;

const stylesF = (Dimens: DimensType) => StyleSheet.create({
    container: { paddingHorizontal: Dimens.W_24 },
    textAButton: {
        width: '60%',
        alignSelf: 'center',
        marginTop: Dimens.H_34,
    },
    textTitle: {
        fontSize: Dimens.FONT_24,
        fontWeight: '700',
        textAlign: 'center',
        marginTop: Dimens.H_10,
        marginHorizontal: Dimens.W_24,
    },
    textMsg: {
        fontSize: Dimens.FONT_18,
        textAlign: 'center',
        marginTop: Dimens.H_24,
    },
});