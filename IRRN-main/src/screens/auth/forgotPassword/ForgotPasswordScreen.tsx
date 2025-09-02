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
import { RegisterSuccessIcon } from '@src/assets/svg';
import ButtonComponent from '@src/components/ButtonComponent';
import BackButton from '@src/components/header/BackButton';
import InputComponent from '@src/components/InputComponent';
import TextComponent from '@src/components/TextComponent';
import Toast from '@src/components/toast/Toast';
import { Colors } from '@src/configs';
import useCallAPI from '@src/hooks/useCallAPI';
import useDimens, { DimensType } from '@src/hooks/useDimens';
import { sendEmailResetPasswordService, } from '@src/network/services/authServices';
import useThemeColors from '@src/themes/useThemeColors';
import {
    isGroupApp,
    validateEmail,
} from '@src/utils';
import { useTranslation } from 'react-i18next';

const ForgotPasswordScreen = () => {
    const { themeColors } = useThemeColors();
    const Dimens = useDimens();
    const styles = stylesF(Dimens);
    const { t } = useTranslation();

    const [email, setEmail] = useState<string>();
    const [emailError, setEmailError] = useState(false);

    const [confirmEmailSuccess, setConfirmEmailSuccess] = useState(false);

    const { callApi: sendEmailResetPassword, loading } = useCallAPI(
            sendEmailResetPasswordService,
            undefined,
            useCallback(() => {
                setConfirmEmailSuccess(true);
            }, [])
    );

    const validateField = useCallback(() => {

        if (!email) {
            Toast.showToast(t('forgot_password_message_empty_email'));
            setEmailError(true);
            return false;
        }

        if (!validateEmail(email)) {
            Toast.showToast(t('message_email_error'));
            setEmailError(true);
            return false;
        }

        return true;

    }, [email, t]);

    const validate = useCallback(() => {
        Keyboard.dismiss();
        const allFieldsValid = validateField();

        if (allFieldsValid) {
            sendEmailResetPassword({
                email: email
            });
        }
    }, [email, sendEmailResetPassword, validateField]);

    return (
        <View
            style={[styles.mainContainer, { backgroundColor: isGroupApp() ? themeColors.group_color : themeColors.color_primary }]}
        >
            <View style={styles.header}>
                <BackButton/>
            </View>

            <KeyboardAwareScrollView
                bounces={false}
                showsVerticalScrollIndicator={false}
                keyboardShouldPersistTaps="handled"
            >
                <View
                    style={styles.successContainer}
                >
                    <TextComponent style={styles.headerSuccessText}>
                        {t(confirmEmailSuccess ? 'text_title_email_confirm_success' : 'text_title_forgot_password')}
                    </TextComponent>
                    <TextComponent style={{ ...styles.conditionText, ...styles.successDesc }}>
                        {t(confirmEmailSuccess ? 'text_desc_email_confirm_success' : 'text_desc_forgot_password')}
                    </TextComponent>

                    {confirmEmailSuccess ? (
                        <Animated.View
                            entering={FadeInRight}
                        >
                            <RegisterSuccessIcon
                                width={Dimens.H_100}
                                height={Dimens.H_100}
                            />
                        </Animated.View>
                    ) : (
                        <View style={{ width: '100%' }}>
                            <InputComponent
                                error={emailError}
                                keyboardType="email-address"
                                containerStyle={styles.input}
                                backgroundInput={Colors.COLOR_WHITE}
                                borderInput={Colors.COLOR_WHITE}
                                textColorInput={Colors.COLOR_DEFAULT_TEXT_INPUT}
                                placeholderTextColor={Colors.COLOR_INPUT_PLACE_HOLDER}
                                errorBackgroundInput={Colors.COLOR_INPUT_ERROR_BACKGROUND}
                                autoCapitalize={'none'}
                                placeholder={t('hint_email')}
                                value={email}
                                onChangeText={(text) => {
                                    setEmail(text);
                                    setEmailError(false);
                                }}
                            />
                            <ButtonComponent
                                loading={loading}
                                title={t('text_title_forgot_password')}
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

export default ForgotPasswordScreen;

const stylesF = (Dimens: DimensType) => StyleSheet.create({
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
        marginBottom: Dimens.H_6,
    },
    input: {
        marginTop: Dimens.H_12,
        flex: 1,
    },
});