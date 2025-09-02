import React, { useCallback } from 'react';

import { StyleSheet } from 'react-native';

import ButtonComponent from '@src/components/ButtonComponent';
import DialogComponent from '@src/components/DialogComponent';
import TextComponent from '@src/components/TextComponent';
import useDimens, { DimensType } from '@src/hooks/useDimens';
import { RestaurantDetailModel, } from '@src/network/dataModels/RestaurantDetailModel';
import { useTranslation } from 'react-i18next';

interface ModalProps {
    restaurantData?: RestaurantDetailModel,
    isShow: boolean,
    hideModal: () => void
}

const ErrorOutOfDeliveryDistanceDialog = ({ isShow, hideModal, restaurantData }: ModalProps) => {
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
            <TextComponent style={styles.textTitle}>
                {t('text_oops')}
            </TextComponent>

            <TextComponent style={styles.textMsg}>
                <TextComponent style={[styles.textMsg, { fontWeight: '700' }]}>
                    {restaurantData?.setting_generals?.title} {}
                </TextComponent>
                {t('message_error_restaurant_delivery')} {}
                <TextComponent style={[styles.textMsg, { fontWeight: '700' }]}>
                    {t('text_other_location')} {}
                </TextComponent>
                {t('text_or_chose')} {}
                <TextComponent style={[styles.textMsg, { fontWeight: '700' }]}>
                    {t('text_pick_up').toLowerCase()}
                </TextComponent>
            </TextComponent>

            <ButtonComponent
                title={t('text_return')}
                style={styles.textAButton}
                onPress={handleReturnPress}
            />
        </DialogComponent>
    );
};

export default ErrorOutOfDeliveryDistanceDialog;

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
        marginBottom: Dimens.H_16,
        marginHorizontal: Dimens.W_24,
    },
    textMsg: {
        fontSize: Dimens.FONT_18,
        textAlign: 'center',
        marginTop: Dimens.H_16,
    },
});