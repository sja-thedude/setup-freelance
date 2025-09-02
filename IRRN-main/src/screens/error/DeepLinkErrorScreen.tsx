import React, { useCallback } from 'react';

import {
    StyleSheet,
    View,
} from 'react-native';
import Animated, { FadeInRight } from 'react-native-reanimated';

import { FailIcon } from '@src/assets/svg';
import ButtonComponent from '@src/components/ButtonComponent';
import HeaderComponent from '@src/components/header/HeaderComponent';
import TextComponent from '@src/components/TextComponent';
import { Colors } from '@src/configs';
import useDimens, { DimensType } from '@src/hooks/useDimens';
import { SCREENS } from '@src/navigation/config/screenName';
import NavigationService from '@src/navigation/NavigationService';
import useThemeColors from '@src/themes/useThemeColors';
import { useTranslation } from 'react-i18next';

const DeepLinkErrorScreen = () => {
    const { themeColors } = useThemeColors();
    const Dimens = useDimens();
    const styles = stylesF(Dimens);
    const { t } = useTranslation();

    const handleGoback = useCallback(() => {
        NavigationService.navigate(SCREENS.BOTTOM_TAB_SCREEN);
    }, []);

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
                    {t('text_title_reset_password_error')}
                </TextComponent>
                <TextComponent style={{ ...styles.conditionText, color: themeColors.color_text }}>
                    {t('text_desc_reset_password_error')}
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

export default DeepLinkErrorScreen;

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