import React, { useCallback } from 'react';

import {
    InteractionManager,
    StyleSheet,
    View,
} from 'react-native';

import ButtonComponent from '@src/components/ButtonComponent';
import DialogComponent from '@src/components/DialogComponent';
import TextComponent from '@src/components/TextComponent';
import { useAppDispatch } from '@src/hooks';
import useDimens, { DimensType } from '@src/hooks/useDimens';
import { SCREENS } from '@src/navigation/config/screenName';
import NavigationService from '@src/navigation/NavigationService';
import { StorageActions } from '@src/redux/toolkit/actions/storageActions';
import useThemeColors from '@src/themes/useThemeColors';
import { useTranslation } from 'react-i18next';

interface ModalProps {
    isShow: boolean,
    hideModal: () => void,
}

const ChangeRestaurantAlertDialog = ({ isShow, hideModal }: ModalProps) => {
    const Dimens = useDimens();
    const styles = stylesF(Dimens);
    const { t } = useTranslation();

    const { themeColors } = useThemeColors();
    const dispatch = useAppDispatch();

    const handleReturnPress = useCallback(() => {
        // clear cart
        dispatch(StorageActions.clearStorageProductsCart());
        hideModal();

        InteractionManager.runAfterInteractions(() => {
            NavigationService.navigate(SCREENS.GROUP_APP_HOME_SCREEN);
        });
    }, [dispatch, hideModal]);

    return (
        <DialogComponent
            hideModal={hideModal}
            isVisible={isShow}
        >
            <View style={styles.container}>
                <TextComponent style={styles.textTitle}>
                    {t('group_selecting_change_location_title')}
                </TextComponent>

                <TextComponent style={styles.textMsg}>
                    {t('group_selecting_change_location_still_items')}
                </TextComponent>
            </View>

            <ButtonComponent
                title={t('text_no_cancel')}
                style={styles.textAButton}
                onPress={hideModal}
            />
            <ButtonComponent
                title={t('text_yes_empty_cart')}
                style={styles.textBButton}
                styleTitle={[styles.textBButtonTitle, { color: themeColors.group_color }]}
                onPress={handleReturnPress}
            />
        </DialogComponent>
    );
};

export default ChangeRestaurantAlertDialog;

const stylesF = (Dimens: DimensType) => StyleSheet.create({
    container: { paddingHorizontal: Dimens.W_10 },
    textAButton: {
        width: '75%',
        alignSelf: 'center',
        marginTop: Dimens.H_34,
    },
    textBButton: {
        width: '75%',
        alignSelf: 'center',
        backgroundColor: 'transparent'
    },
    textBButtonTitle: {
    },
    textTitle: {
        fontSize: Dimens.FONT_24,
        fontWeight: '700',
        textAlign: 'center',
        marginTop: Dimens.H_24,
        marginHorizontal: Dimens.W_24,
    },
    textMsg: {
        fontSize: Dimens.FONT_14,
        textAlign: 'center',
        marginTop: Dimens.H_34,
    },
});