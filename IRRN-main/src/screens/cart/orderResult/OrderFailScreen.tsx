import React, { useCallback } from 'react';

import {
    StyleSheet,
    View,
} from 'react-native';
import Animated, { FadeInRight } from 'react-native-reanimated';
import { useEffectOnce } from 'react-use';

import { FailIcon } from '@src/assets/svg';
import ButtonComponent from '@src/components/ButtonComponent';
import HeaderComponent from '@src/components/header/HeaderComponent';
import TextComponent from '@src/components/TextComponent';
import { Colors } from '@src/configs';
import { useAppDispatch } from '@src/hooks';
import useDimens, { DimensType } from '@src/hooks/useDimens';
import { SCREENS } from '@src/navigation/config/screenName';
import NavigationService from '@src/navigation/NavigationService';
import { StorageActions } from '@src/redux/toolkit/actions/storageActions';
import useThemeColors from '@src/themes/useThemeColors';
import { useTranslation } from 'react-i18next';

const OrderFailScreen = () => {
    const { themeColors } = useThemeColors();
    const Dimens = useDimens();
    const styles = stylesF(Dimens);
    const { t } = useTranslation();

    const dispatch = useAppDispatch();

    const handleGoback = useCallback(() => {
        NavigationService.navigate(SCREENS.BOTTOM_TAB_SCREEN);
    }, []);

    useEffectOnce(() => {
        dispatch(StorageActions.clearStorageProcessingOrder());
    });

    return (
        <View
            style={[styles.container, { backgroundColor: themeColors.color_app_background }]}
        >
            <HeaderComponent >
                <TextComponent style={styles.headerText}>
                    {t('text_failed')}
                </TextComponent>
            </HeaderComponent>
            <Animated.View
                style={styles.animationContainer}
                entering={FadeInRight}
            >
                <FailIcon
                    width={Dimens.H_100}
                    height={Dimens.H_100}
                />

                <TextComponent style={{ ...styles.headerSuccessText, color: themeColors.color_text }}>
                    {t('text_title_confirm_error')}
                </TextComponent>
                <TextComponent style={{ ...styles.conditionText, color: themeColors.color_text }}>
                    {t('text_desc_confirm_error')}
                </TextComponent>

                <ButtonComponent
                    title={t('text_return')}
                    style={styles.button}
                    onPress={handleGoback}
                />
            </Animated.View>

        </View>
    );
};

export default OrderFailScreen;

const stylesF = (Dimens: DimensType) => StyleSheet.create({
    button: {
        backgroundColor: Colors.COLOR_BUTTON_DEFAULT,
        marginTop: Dimens.H_30,
        width: Dimens.W_100,
    },
    animationContainer: {
        flex: 1,
        marginTop: Dimens.H_38,
        paddingHorizontal: Dimens.W_26,
        alignItems: 'center',
    },
    container: { flex: 1 },
    conditionText: {
        color: Colors.COLOR_WHITE,
        fontSize: Dimens.FONT_16,
        marginLeft: Dimens.W_8,
        textAlign: 'center',
        marginBottom: Dimens.H_32,
    },
    headerSuccessText: {
        color: Colors.COLOR_WHITE,
        fontSize: Dimens.FONT_20,
        fontWeight: '700',
        textAlign: 'center',
        marginBottom: Dimens.H_38,
        marginTop: Dimens.H_38,
    },
    headerText: {
        color: Colors.COLOR_WHITE,
        fontSize: Dimens.FONT_26,
        fontWeight: '700',
    },
});