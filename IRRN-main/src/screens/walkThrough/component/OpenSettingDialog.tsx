import React, { useCallback } from 'react';

import { StyleSheet } from 'react-native';
import { openSettings } from 'react-native-permissions';

import ButtonComponent from '@src/components/ButtonComponent';
import DialogComponent from '@src/components/DialogComponent';
import TextComponent from '@src/components/TextComponent';
import useDimens, { DimensType } from '@src/hooks/useDimens';
import useThemeColors from '@src/themes/useThemeColors';
import { logWarning } from '@src/utils/logger';
import { useTranslation } from 'react-i18next';

interface ModalProps {
    isShow: boolean,
    hideModal: () => void
}

const OpenSettingModal = ({ isShow, hideModal }: ModalProps) => {
    const Dimens = useDimens();
    const styles = stylesF(Dimens);
    const { t } = useTranslation();

    const { themeColors } = useThemeColors();

    const openSetting = useCallback(() => {
        hideModal();
        openSettings().catch(() => {
            logWarning('Cannot open settings');
        });
    }, [hideModal]);

    return (
        <DialogComponent
            hideModal={hideModal}
            isVisible={isShow}
        >
            <TextComponent style={styles.textTitle}>
                {t('open_setting_to_grant_permission')}
            </TextComponent>
            <ButtonComponent
                title={t('yes_goto_setting')}
                style={styles.textAButton}
                onPress={openSetting}
            />
            <ButtonComponent
                title={t('text_no_cancel')}
                styleTitle={{ color: themeColors.color_primary }}
                style={[styles.textCButton, { backgroundColor: 'transparent' }]}
                onPress={hideModal}
            />
        </DialogComponent>
    );
};

export default OpenSettingModal;

const stylesF = (Dimens: DimensType) => StyleSheet.create({
    textCButton: {
        width: '75%',
        alignSelf: 'center',
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
    },
});