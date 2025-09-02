import React, { useCallback } from 'react';

import {
    InteractionManager,
    StyleSheet,
} from 'react-native';
import { useDispatch } from 'react-redux';

import ButtonComponent from '@src/components/ButtonComponent';
import DialogComponent from '@src/components/DialogComponent';
import TextComponent from '@src/components/TextComponent';
import useCallAPI from '@src/hooks/useCallAPI';
import useDimens, { DimensType } from '@src/hooks/useDimens';
import { deleteUserService } from '@src/network/services/profileServices';
import { handleLogout } from '@src/network/util/authUtility';
import { LoadingActions } from '@src/redux/toolkit/actions/loadingActions';
import useThemeColors from '@src/themes/useThemeColors';
import { useTranslation } from 'react-i18next';

interface ModalProps {
    isShow: boolean,
    hideModal: () => void
}

const ConfirmDelAccountDialog = ({ isShow, hideModal }: ModalProps) => {
    const Dimens = useDimens();
    const styles = stylesF(Dimens);
    const { t } = useTranslation();

    const { themeColors } = useThemeColors();

    const dispatch = useDispatch();

    const { callApi: deleteUser } = useCallAPI(
            deleteUserService,
            useCallback(() => {
                dispatch(LoadingActions.showGlobalLoading(true));
            }, [dispatch]),
            useCallback(() => {
                handleLogout(true);
            }, [])
    );

    const handleDeleteUser = useCallback(() => {
        hideModal();
        InteractionManager.runAfterInteractions(async () => {
            deleteUser();
        });
    }, [deleteUser, hideModal]);

    return (
        <DialogComponent
            hideModal={hideModal}
            isVisible={isShow}
        >
            <TextComponent style={styles.textTitle}>
                {t('delete_account_popup_title')}
            </TextComponent>
            <ButtonComponent
                title={t('delete_account_popup_cancel')}
                style={styles.textAButton}
                onPress={hideModal}
            />
            <ButtonComponent
                title={t('delete_account_popup_confirm')}
                styleTitle={{ color: themeColors.color_primary }}
                style={styles.textCButton}
                onPress={handleDeleteUser}
            />
        </DialogComponent>
    );
};

export default ConfirmDelAccountDialog;

const stylesF = (Dimens: DimensType) => StyleSheet.create({
    textCButton: {
        width: '75%',
        alignSelf: 'center',
        backgroundColor: 'transparent',
    },
    textAButton: {
        width: '75%',
        alignSelf: 'center',
        marginTop: Dimens.H_34,
    },
    textTitle: {
        fontSize: Dimens.FONT_24,
        fontWeight: '500',
        textAlign: 'center',
        marginTop: Dimens.H_16,
        marginHorizontal: Dimens.W_16,
    },
});