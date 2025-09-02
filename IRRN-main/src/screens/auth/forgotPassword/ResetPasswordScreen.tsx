import React, {
    useCallback,
    useState,
} from 'react';

import {
    Keyboard,
    StyleSheet,
    View,
} from 'react-native';
import Animated, { FadeInRight } from 'react-native-reanimated';

import { KeyboardAwareScrollView, } from '@pietile-native-kit/keyboard-aware-scrollview';
import { useRoute } from '@react-navigation/native';
import { RegisterSuccessIcon } from '@src/assets/svg';
import ButtonComponent from '@src/components/ButtonComponent';
import InputComponent from '@src/components/InputComponent';
import TextComponent from '@src/components/TextComponent';
import Toast from '@src/components/toast/Toast';
import { Colors } from '@src/configs';
import { MIN_PASSWORD_LENGTH } from '@src/configs/constants';
import useCallAPI from '@src/hooks/useCallAPI';
import useDimens, { DimensType } from '@src/hooks/useDimens';
import useIsUserLoggedIn from '@src/hooks/useIsUserLoggedIn';
import { SCREENS } from '@src/navigation/config/screenName';
import { ResetPasswordScreenProps } from '@src/navigation/NavigationRouteProps';
import NavigationService from '@src/navigation/NavigationService';
import { resetPasswordService } from '@src/network/services/authServices';
import useThemeColors from '@src/themes/useThemeColors';
import { isGroupApp } from '@src/utils';
import { useTranslation } from 'react-i18next';

const ResetPasswordScreen = () => {
    const { themeColors } = useThemeColors();
    const Dimens = useDimens();
    const styles = stylesF(Dimens);
    const { t } = useTranslation();

    const isUserLoggedIn = useIsUserLoggedIn();

    const { params } = useRoute<ResetPasswordScreenProps>();
    const { token, email } = params;

    const [password, setPassword] = useState<string>('');
    const [rePassword, setRePassword] = useState<string>('');

    const [passwordError, setPasswordError] = useState(false);
    const [rePasswordError, setRePasswordError] = useState(false);

    const [resetPasswordSuccess, setResetPasswordSuccess] = useState(false);

    const { callApi: resetPassword, loading } = useCallAPI(
            resetPasswordService,
            undefined,
            useCallback(() => {
                setResetPasswordSuccess(true);
            }, [])
    );

    const validateField = useCallback(() => {

        if (!password || !rePassword) {
            Toast.showToast(t('message_input_all_field'));
            setPasswordError(!password);
            setRePasswordError(!rePassword);
            return false;
        }

        if (password.length < MIN_PASSWORD_LENGTH) {
            Toast.showToast(t('message_password_min_length'));
            setPasswordError(true);
            return false;
        }

        if (password !== rePassword) {
            Toast.showToast(t('message_password_not_match'));
            setPasswordError(true);
            setRePasswordError(true);
            return false;
        } else {
            setPasswordError(false);
            setRePasswordError(false);
        }

        return true;

    }, [password, rePassword, t]);

    const validate = useCallback(() => {
        Keyboard.dismiss();
        const allFieldsValid = validateField();

        if (allFieldsValid) {
            resetPassword(
                    {
                        email: email,
                        token: token,
                        password: password,
                        password_confirmation: rePassword,
                    }
            );
        }
    }, [email, password, rePassword, resetPassword, token, validateField]);

    const handleGotoLogin = useCallback(() => {
        NavigationService.navigate(SCREENS.BOTTOM_TAB_SCREEN);
        if (!isUserLoggedIn) {
            NavigationService.navigate(SCREENS.LOGIN_SCREEN);
        }
    }, [isUserLoggedIn]);

    return (
        <View
            style={[styles.mainContainer, { backgroundColor: isGroupApp() ? themeColors.group_color : themeColors.color_primary }]}
        >
            <KeyboardAwareScrollView
                bounces={false}
                showsVerticalScrollIndicator={false}
                keyboardShouldPersistTaps="handled"
            >
                <View
                    style={styles.successContainer}
                >
                    <TextComponent style={styles.headerSuccessText}>
                        {t(resetPasswordSuccess ? 'text_title_password_changed' : 'text_title_change_password')}
                    </TextComponent>

                    {resetPasswordSuccess ? (
                        <Animated.View
                            entering={FadeInRight}
                            style={styles.animateView}
                        >
                            <RegisterSuccessIcon
                                width={Dimens.H_100}
                                height={Dimens.H_100}
                            />
                            <ButtonComponent
                                loading={loading}
                                title={t('text_login')}
                                style={styles.button}
                                onPress={handleGotoLogin}
                            />
                        </Animated.View>
                    ) : (
                        <View style={{ width: '100%' }}>
                            <InputComponent
                                backgroundInput={Colors.COLOR_WHITE}
                                borderInput={Colors.COLOR_WHITE}
                                textColorInput={Colors.COLOR_DEFAULT_TEXT_INPUT}
                                placeholderTextColor={Colors.COLOR_INPUT_PLACE_HOLDER}
                                errorBackgroundInput={Colors.COLOR_INPUT_ERROR_BACKGROUND}
                                eyeColor={Colors.COLOR_DEFAULT_TEXT_INPUT}
                                containerStyle={styles.input}
                                autoCapitalize={'none'}
                                placeholder={t('hint_new_password')}
                                secureTextEntry
                                value={password}
                                onChangeText={(text) => {
                                    setPassword(text);
                                    setPasswordError(false);
                                }}
                                error={passwordError}
                            />

                            <InputComponent
                                backgroundInput={Colors.COLOR_WHITE}
                                borderInput={Colors.COLOR_WHITE}
                                textColorInput={Colors.COLOR_DEFAULT_TEXT_INPUT}
                                placeholderTextColor={Colors.COLOR_INPUT_PLACE_HOLDER}
                                errorBackgroundInput={Colors.COLOR_INPUT_ERROR_BACKGROUND}
                                eyeColor={Colors.COLOR_DEFAULT_TEXT_INPUT}
                                containerStyle={styles.input}
                                autoCapitalize={'none'}
                                placeholder={t('hint_new_password_repeat')}
                                secureTextEntry
                                value={rePassword}
                                onChangeText={(text) => {
                                    setRePassword(text);
                                    setRePasswordError(false);
                                }}
                                error={rePasswordError}
                            />

                            <ButtonComponent
                                loading={loading}
                                title={t('text_change_password')}
                                style={styles.button}
                                onPress={validate}
                            />
                        </View>
                    )}
                </View>
            </KeyboardAwareScrollView>
        </View>
    );
};

export default ResetPasswordScreen;

const stylesF = (Dimens: DimensType) => StyleSheet.create({
    animateView: { flex: 1, alignItems: 'center' },
    button: {
        backgroundColor: Colors.COLOR_BUTTON_DEFAULT,
        marginTop: Dimens.H_30,
        marginHorizontal: Dimens.H_40,
    },
    successDesc: { textAlign: 'center', flex: 0, marginBottom: Dimens.H_32 },
    successContainer: {
        flex: 1,
        marginTop: Dimens.H_38,
        paddingHorizontal: Dimens.W_40,
        alignItems: 'center',
    },
    header: {
        flexDirection: 'row',
        alignItems: 'center',
        paddingHorizontal: Dimens.W_20,
    },
    mainContainer: { flex: 1, paddingTop: Dimens.COMMON_HEADER_PADDING },
    conditionText: {
        color: Colors.COLOR_WHITE,
        fontSize: Dimens.FONT_16,
        flex: 1,
        marginLeft: Dimens.W_8,
    },
    headerSuccessText: {
        color: Colors.COLOR_WHITE,
        fontSize: Dimens.FONT_26,
        fontWeight: '700',
        textAlign: 'center',
        marginBottom: Dimens.H_36,
    },
    input: {
        flex: 1,
        marginTop: Dimens.H_12,
    },
});