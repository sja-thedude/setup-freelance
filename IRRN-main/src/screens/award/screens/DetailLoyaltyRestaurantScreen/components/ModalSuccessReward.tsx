import React, {
    FC,
    memo,
} from 'react';

import dayjs from 'dayjs';
import { useTranslation } from 'react-i18next';
import {
    StatusBar,
    StyleSheet,
    useWindowDimensions,
    View,
} from 'react-native';
import Modal from 'react-native-modal';

import { SuccessModalIcon } from '@src/assets/svg';
import TextComponent from '@src/components/TextComponent';
import { Colors } from '@src/configs';
import useDimens, { DimensType } from '@src/hooks/useDimens';
import useThemeColors from '@src/themes/useThemeColors';

import { DataSuccessProps } from './ViewDetailReward';

interface IProps {
    onClose?: () => void;
    isVisible?: boolean;
    dataSuccess?: DataSuccessProps;
}

const ModalSuccessReward: FC<IProps> = ({
    onClose,
    isVisible,
    dataSuccess,
}) => {
    const { height, width } = useWindowDimensions();
    const { t } = useTranslation();
    const { themeColors } = useThemeColors();
    const Dimens = useDimens();
    const styles = stylesF(Dimens);

    return (
        <Modal
            deviceHeight={height + (StatusBar.currentHeight || 0)}
            deviceWidth={width}
            statusBarTranslucent
            hideModalContentWhileAnimating
            useNativeDriverForBackdrop
            onBackdropPress={onClose}
            onBackButtonPress={onClose}
            onSwipeComplete={onClose}
            swipeDirection={['down']}
            style={styles.modal}
            isVisible={isVisible}
        >
            <View
                style={[
                    styles.container,
                    { backgroundColor: themeColors?.color_card_background },
                ]}
            >
                <View style={styles.viewDash}>
                    <View style={styles.viewDashLine} />
                </View>

                <SuccessModalIcon
                    width={Dimens.H_106}
                    height={Dimens.H_106}
                />

                <TextComponent
                    style={StyleSheet.flatten([
                        styles.textHeader,
                        { color: themeColors?.color_text },
                    ])}
                >
                    {t('text_redeemed_successfully')}
                </TextComponent>

                <TextComponent
                    style={StyleSheet.flatten([
                        styles.textDesc,
                        { color: themeColors?.color_text },
                    ])}
                >
                    {t('text_desc_redeem_success')}
                </TextComponent>

                <TextComponent
                    numberOfLines={1}
                    style={StyleSheet.flatten([
                        styles.textDesc,
                        { color: themeColors?.color_primary },
                    ])}
                >
                    {dayjs(dataSuccess?.time).format('DD/MM/YYYY')} -{' '}
                    {dataSuccess?.email}
                </TextComponent>

                <TextComponent
                    style={StyleSheet.flatten([
                        styles.textDesc,
                        { color: themeColors?.color_text },
                    ])}
                >
                    {t('text_desc2_redeem_success')}
                </TextComponent>
            </View>
        </Modal>
    );
};

const stylesF = (Dimens: DimensType) => StyleSheet.create({
    modal: { margin: 0, justifyContent: 'flex-end' },
    container: {
        borderTopLeftRadius: Dimens.RADIUS_30,
        borderTopRightRadius: Dimens.RADIUS_30,
        paddingBottom: Dimens.COMMON_BOTTOM_PADDING * 2,
        alignItems: 'center',
        paddingHorizontal: Dimens.W_40,
    },
    viewDash: {
        alignItems: 'center',
        justifyContent: 'center',
        marginTop: Dimens.H_10,
        marginBottom: Dimens.H_30,
    },
    viewDashLine: {
        width: Dimens.W_90,
        height: Dimens.H_5,
        borderRadius: Dimens.RADIUS_10,
        backgroundColor: Colors.COLOR_DASH,
    },
    textHeader: {
        fontSize: Dimens.FONT_24,
        fontWeight: '700',
        textAlign: 'center',
        marginTop: Dimens.H_30,
    },
    textDesc: {
        marginTop: Dimens.H_25,
        fontSize: Dimens.FONT_16,
        fontWeight: '400',
        textAlign: 'center',
    },
});

export default memo(ModalSuccessReward);
