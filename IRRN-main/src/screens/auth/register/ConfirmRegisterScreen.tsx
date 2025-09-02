import React, { useCallback } from 'react';

import {
    InteractionManager,
    StyleSheet,
    View,
} from 'react-native';
import Animated, { FadeInRight } from 'react-native-reanimated';

import { useRoute } from '@react-navigation/native';
import { RegisterSuccessIcon } from '@src/assets/svg';
import ButtonComponent from '@src/components/ButtonComponent';
import TextComponent from '@src/components/TextComponent';
import Toast from '@src/components/toast/Toast';
import { Colors } from '@src/configs';
import { useAppSelector } from '@src/hooks';
import useCallAPI from '@src/hooks/useCallAPI';
import useDimens, { DimensType } from '@src/hooks/useDimens';
import useIsUserLoggedIn from '@src/hooks/useIsUserLoggedIn';
import { SCREENS } from '@src/navigation/config/screenName';
import { ConfirmRegisterScreenProps, } from '@src/navigation/NavigationRouteProps';
import NavigationService from '@src/navigation/NavigationService';
import { loginWithTokenService } from '@src/network/services/authServices';
import {
    handleLogin,
    handleLogout,
} from '@src/network/util/authUtility';
import useThemeColors from '@src/themes/useThemeColors';
import {
    isGroupApp,
    isTemplateApp,
} from '@src/utils';
import { useTranslation } from 'react-i18next';

const ConfirmRegisterScreen = () => {
    const { themeColors } = useThemeColors();
    const Dimens = useDimens();
    const styles = stylesF(Dimens);
    const { t } = useTranslation();

    const { params } = useRoute<ConfirmRegisterScreenProps>();
    const { token } = params;

    const isUserLoggedIn = useIsUserLoggedIn();

    const workspaceDetail = useAppSelector((state) => state.storageReducer.templateWorkspaceDetail);
    const groupAppDetail = useAppSelector((state) => state.storageReducer.groupAppDetail);

    const { callApi: loginWithToken, loading } = useCallAPI(
            loginWithTokenService,
            undefined,
            useCallback((data: any, message: any) => {
                Toast.showToast(message);
                InteractionManager.runAfterInteractions(() => {
                    handleLogin(data);
                    NavigationService.navigate(SCREENS.BOTTOM_TAB_SCREEN);
                });
            }, [])
    );

    const onConfirm = useCallback(() => {
        if (isUserLoggedIn) {
            handleLogout().then(() => {
                loginWithToken({
                    verify_token: token,
                });
            });
        } else {
            loginWithToken({
                verify_token: token,
            });
        }
    }, [isUserLoggedIn, loginWithToken, token]);

    return (
        <View
            style={[styles.container, { backgroundColor: isGroupApp() ? themeColors.group_color : themeColors.color_primary }]}
        >

            <Animated.View
                style={styles.animationContainer}
                entering={FadeInRight}
            >
                <TextComponent style={styles.headerSuccessText}>
                    {t('login_welcome_label', { value: isTemplateApp() ? workspaceDetail?.setting_generals?.title : isGroupApp() ? groupAppDetail?.name : 'It’s Ready', interpolation: { escapeValue: false } })}
                </TextComponent>
                <TextComponent style={styles.conditionText}>
                    {t('text_validate_account_success_desc')}
                </TextComponent>
                <RegisterSuccessIcon
                    width={Dimens.H_100}
                    height={Dimens.H_100}
                />

                <ButtonComponent
                    loading={loading}
                    title={t('text_validate_account_action')}
                    style={styles.button}
                    onPress={onConfirm}
                />
            </Animated.View>

        </View>
    );
};

export default ConfirmRegisterScreen;

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
    container: { flex: 1, paddingTop: Dimens.COMMON_HEADER_PADDING },
    conditionText: {
        color: Colors.COLOR_WHITE,
        fontSize: Dimens.FONT_16,
        marginLeft: Dimens.W_8,
        textAlign: 'center',
        marginBottom: Dimens.H_32,
    },
    headerSuccessText: {
        color: Colors.COLOR_WHITE,
        fontSize: Dimens.FONT_26,
        fontWeight: '700',
        textAlign: 'center',
        marginBottom: Dimens.H_6,
    },
});