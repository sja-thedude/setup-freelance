import React, { memo } from 'react';

import {
    StyleSheet,
    View,
} from 'react-native';
import Toast from 'react-native-toast-notifications';

import TextComponent from '@components/TextComponent';
import { Colors } from '@configs/index';
import useDimens, { DimensType } from '@src/hooks/useDimens';

import ToastHolder from './Toast';

const CustomToastProvider = () => {
    const Dimens = useDimens();
    const styles = stylesF(Dimens);

    return (
        <Toast
            ref={(ref) => {
                ToastHolder.setToast(ref);
            }}
            animationDuration={200}
            offset={Dimens.COMMON_BOTTOM_PADDING * 2.5}
            renderType={{
                normal: (toast) => (
                    <View
                        style={styles.toastContainerStyle}
                    >
                        <View style={styles.toastStyle}>
                            <TextComponent
                                style={styles.toastTextStyle}
                            >
                                {toast.message}
                            </TextComponent>
                        </View>
                    </View>
                ),
                info: (toast) => (
                    <View
                        style={styles.toastContainerStyle}
                    >
                        <View style={[styles.toastStyle, { backgroundColor: '#2F80ED' }]}>
                            <TextComponent
                                style={styles.toastTextStyle}
                            >
                                {toast.message}
                            </TextComponent>
                        </View>
                    </View>
                ),
                danger: (toast) => (
                    <View
                        style={styles.toastContainerStyle}
                    >
                        <View style={[styles.toastStyle, { backgroundColor: '#DE3B20' }]}>
                            <TextComponent
                                style={styles.toastTextStyle}
                            >
                                {toast.message}
                            </TextComponent>
                        </View>
                    </View>
                ),
                warning: (toast) => (
                    <View
                        style={styles.toastContainerStyle}
                    >
                        <View style={[styles.toastStyle, { backgroundColor: '#FFC52D' }]}>
                            <TextComponent
                                style={styles.toastTextStyle}
                            >
                                {toast.message}
                            </TextComponent>
                        </View>
                    </View>
                ),
                success: (toast) => (
                    <View
                        style={styles.toastContainerStyle}
                    >
                        <View style={[styles.toastStyle, { backgroundColor: '#01875B' }]}>
                            <TextComponent
                                style={styles.toastTextStyle}
                            >
                                {toast.message}
                            </TextComponent>
                        </View>
                    </View>
                )
            }}
        />
    );
};

export default memo(CustomToastProvider);

const stylesF = (Dimens: DimensType) => StyleSheet.create({
    toastContainerStyle: { bottom: 0, width: '100%' },
    toastTextStyle: {
        fontSize: Dimens.FONT_15,
        color: Colors.COLOR_WHITE,
        textAlign: 'center',
    },
    toastStyle: {
        marginHorizontal: Dimens.W_16,
        paddingHorizontal: Dimens.W_10,
        paddingVertical: Dimens.H_12,
        backgroundColor: '#595856',
        borderRadius: Dimens.RADIUS_10,
        minHeight: Dimens.H_50,
        justifyContent: 'center',
        alignItems: 'center',
        minWidth: Dimens.SCREEN_WIDTH / 2,
    },
});