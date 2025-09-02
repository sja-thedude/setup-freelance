import React, { FC } from 'react';

import {
    StatusBar,
    StyleSheet,
    View,
    ViewStyle,
} from 'react-native';
import Modal, { ModalProps } from 'react-native-modal';

import useDimens, { DimensType } from '@src/hooks/useDimens';
import useThemeColors from '@src/themes/useThemeColors';

interface DialogComponentProps extends Partial<ModalProps> {
    hideModal?: () => void,
    containerStyle?: ViewStyle,
}

const DialogComponent: FC<DialogComponentProps> = ({ hideModal, containerStyle, ...rest }) => {
    const { themeColors } = useThemeColors();
    const Dimens = useDimens();
    const styles = stylesF(Dimens);

    return (
        <Modal
            useNativeDriverForBackdrop
            swipeDirection={'down'}
            onSwipeComplete={hideModal}
            style={styles.modal}
            deviceHeight={Dimens.SCREEN_HEIGHT + (StatusBar.currentHeight || 0)}
            statusBarTranslucent
            backdropOpacity={0.6}
            propagateSwipe
            animationOutTiming={300}
            animationInTiming={300}
            onBackButtonPress={hideModal}
            onBackdropPress={hideModal}
            {...rest}
        >
            <View style={[styles.contentContainer, containerStyle, { backgroundColor: themeColors.color_dialog_background }]}>
                <View style={[styles.viewHeader, { backgroundColor: themeColors.color_dialog_background }]}>
                    <View style={[styles.viewDash, { backgroundColor: themeColors.color_dash }]} />
                </View>
                {rest.children}
            </View>
        </Modal>
    );
};

export default DialogComponent;

const stylesF = (Dimens: DimensType) => StyleSheet.create({
    contentContainer: {
        paddingHorizontal: Dimens.W_18,
        paddingTop: Dimens.H_24,
        paddingBottom: Dimens.COMMON_BOTTOM_PADDING * 2,
        borderTopLeftRadius: Dimens.H_32,
        borderTopRightRadius: Dimens.H_32,
    },
    modal: { margin: 0, justifyContent: 'flex-end' },
    viewHeader: {
        alignItems: 'center',
        justifyContent: 'center',
        marginBottom: Dimens.H_8,
    },
    viewDash: {
        height: Dimens.H_4,
        width: Dimens.H_100,
        borderRadius: Dimens.RADIUS_4,
    },
});