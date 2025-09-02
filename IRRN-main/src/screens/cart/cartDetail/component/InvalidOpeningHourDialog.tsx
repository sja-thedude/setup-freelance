import React, { useCallback } from 'react';

import { StyleSheet } from 'react-native';

import ButtonComponent from '@src/components/ButtonComponent';
import DialogComponent from '@src/components/DialogComponent';
import TextComponent from '@src/components/TextComponent';
import { useAppDispatch } from '@src/hooks';
import useDimens, { DimensType } from '@src/hooks/useDimens';
import { SCREENS } from '@src/navigation/config/screenName';
import NavigationService from '@src/navigation/NavigationService';
import { StorageActions } from '@src/redux/toolkit/actions/storageActions';
import { useTranslation } from 'react-i18next';

interface ModalProps {
    isShow: boolean,
    hideModal: () => void,
}

const InvalidOpeningHourDialog = ({ isShow, hideModal }: ModalProps) => {
    const Dimens = useDimens();
    const styles = stylesF(Dimens);
    const { t } = useTranslation();

    const dispatch = useAppDispatch();

    const handleReturnPress = useCallback(() => {
        hideModal();
        // clear cart
        dispatch(StorageActions.clearStorageProductsCart());

        setTimeout(() => {
            NavigationService.navigate(SCREENS.MENU_TAB_SCREEN);
        }, 300);
    }, [dispatch, hideModal]);

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
                {t('cart_can_not_add_product_to_cart_cuz_restaurant_is_closed')}
            </TextComponent>

            <ButtonComponent
                title={t('text_return')}
                style={styles.textAButton}
                onPress={handleReturnPress}
            />
        </DialogComponent>
    );
};

export default InvalidOpeningHourDialog;

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