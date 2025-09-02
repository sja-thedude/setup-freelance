import React, { useCallback } from 'react';

import { StyleSheet } from 'react-native';

import ButtonComponent from '@src/components/ButtonComponent';
import DialogComponent from '@src/components/DialogComponent';
import TextComponent from '@src/components/TextComponent';
import useDimens, { DimensType } from '@src/hooks/useDimens';
import { handleLogout } from '@src/network/util/authUtility';
import useThemeColors from '@src/themes/useThemeColors';
import { useTranslation } from 'react-i18next';

interface ModalProps {
    isShow: boolean,
    hideModal: () => void
}

const ConfirmLogoutDialog = ({ isShow, hideModal }: ModalProps) => {
    const Dimens = useDimens();
    const styles = stylesF(Dimens);
    const { t } = useTranslation();

    const { themeColors } = useThemeColors();

    const logUserOut = useCallback(() => {
        hideModal();
        handleLogout();
    }, [hideModal]);

    return (
        <DialogComponent
            hideModal={hideModal}
            isVisible={isShow}
        >
            <TextComponent style={styles.textTitle}>
                {t('text_message_logout')}
            </TextComponent>
            <ButtonComponent
                title={t('text_yes_logout')}
                style={styles.textAButton}
                onPress={logUserOut}
            />
            <ButtonComponent
                title={t('text_no_cancel')}
                styleTitle={{ color: themeColors.color_primary }}
                style={styles.textCButton}
                onPress={hideModal}
            />
        </DialogComponent>
    );
};

export default ConfirmLogoutDialog;

const stylesF = (Dimens: DimensType) => StyleSheet.create({
    textCButton: {
        width: '75%',
        alignSelf: 'center',
        backgroundColor: 'transparent'
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
        marginHorizontal: Dimens.W_24,
    },
});