import React from 'react';

import { StyleSheet } from 'react-native';

import DialogComponent from '@src/components/DialogComponent';
import TextComponent from '@src/components/TextComponent';
import { Colors } from '@src/configs';
import useDimens, { DimensType } from '@src/hooks/useDimens';
import useThemeColors from '@src/themes/useThemeColors';

import TouchableComponent from '../TouchableComponent';
import { SELECT_OPTIONS } from './ImageSelectComponent';
import { useTranslation } from 'react-i18next';

interface ModalProps {
    onSelectOption: any,
    showDeleteButton: boolean,
    isShow: boolean,
    hideModal: () => void
}

const SelectPhotoOptionDialog = ({ isShow, hideModal, showDeleteButton, onSelectOption }: ModalProps) => {
    const { themeColors } = useThemeColors();
    const Dimens = useDimens();
    const styles = stylesF(Dimens);
    const { t } = useTranslation();

    return (
        <DialogComponent
            hideModal={hideModal}
            isVisible={isShow}
        >
            <TextComponent style={styles.textTitle}>
                {t('text_choose_avatar')}
            </TextComponent>

            <TouchableComponent
                onPress={() => onSelectOption(SELECT_OPTIONS.FROM_GALLERY)}
                style={[styles.itemContainer, { borderBottomColor: themeColors.color_divider }]}
            >
                <TextComponent style={styles.textItem}>
                    {t('text_pick_from_gallery')}
                </TextComponent>
            </TouchableComponent>

            <TouchableComponent
                onPress={() => onSelectOption(SELECT_OPTIONS.FROM_CAMERA)}
                style={[styles.itemContainer, { borderBottomColor: themeColors.color_divider }]}
            >
                <TextComponent style={styles.textItem}>
                    {t('text_pick_from_camera')}
                </TextComponent>
            </TouchableComponent>

            {showDeleteButton && (
                <TouchableComponent
                    onPress={() => onSelectOption(SELECT_OPTIONS.DELETE_PHOTO)}
                    style={[styles.itemContainer, { borderBottomColor: themeColors.color_divider }]}
                >
                    <TextComponent style={{ ...styles.textItem, color: Colors.COLOR_RED_ERROR }}>
                        {t('text_delete_photo')}
                    </TextComponent>
                </TouchableComponent>
            )}
        </DialogComponent>
    );
};

export default SelectPhotoOptionDialog;

const stylesF = (Dimens: DimensType) => StyleSheet.create({
    itemContainer: { borderBottomWidth: 0.5,
        borderBottomColor: Colors.DIVIDER_COLOR, marginHorizontal: Dimens.W_16 },
    textTitle: {
        fontSize: Dimens.FONT_18,
        fontWeight: '500',
        marginVertical: Dimens.H_16,
        marginHorizontal: Dimens.W_16,
    },
    textItem: {
        fontSize: Dimens.FONT_16,
        paddingVertical: Dimens.H_10,
    },
});