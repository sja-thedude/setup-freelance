import React, {
    useCallback,
    useMemo,
    useState,
} from 'react';

import { useFormik } from 'formik';
import moment from 'moment';
import {
    InteractionManager,
    Keyboard,
    StyleSheet,
    View,
} from 'react-native';
import * as Yup from 'yup';

import { KeyboardAwareScrollView } from '@pietile-native-kit/keyboard-aware-scrollview';
import { useRoute } from '@react-navigation/native';
import { AppTextIcon } from '@src/assets/svg';
import ButtonComponent from '@src/components/ButtonComponent';
import BackButton from '@src/components/header/BackButton';
import ImageComponent from '@src/components/ImageComponent';
import InputComponent from '@src/components/InputComponent';
import ShadowView from '@src/components/ShadowView';
import TextComponent from '@src/components/TextComponent';
import Toast from '@src/components/toast/Toast';
import { Colors } from '@src/configs';
import {
    EMAIL_REGEX,
    ITS_READY_TERM_AND_CONDITION_LINK,
} from '@src/configs/constants';
import { useAppSelector } from '@src/hooks';
import useCallAPI from '@src/hooks/useCallAPI';
import useDimens, { DimensType } from '@src/hooks/useDimens';
import { SCREENS } from '@src/navigation/config/screenName';
import { LoginScreenScreenProps } from '@src/navigation/NavigationRouteProps';
import NavigationService from '@src/navigation/NavigationService';
import { UserDataModel } from '@src/network/dataModels';
import { loginService } from '@src/network/services/authServices';
import { handleLogin } from '@src/network/util/authUtility';
import useThemeColors from '@src/themes/useThemeColors';
import {
    isGroupApp,
    isTemplateApp,
    isTemplateOrGroupApp,
    openInAppBrowser,
} from '@src/utils';

import SocialComponent from './component/SocialComponent';
import { useTranslation } from 'react-i18next';

const validationSchema = Yup.object({
    email: Yup.string(),
    passWord: Yup.string(),
});

const LoginScreen = () => {
    const { t } = useTranslation();

    const { params } = useRoute<LoginScreenScreenProps>();
    const { callback, fromCart } = params;

    const [emailError, setEmailError] = useState(false);
    const [passwordError, setPasswordError] = useState(false);

    const { themeColors } = useThemeColors();

    const workspaceDetail = useAppSelector((state) => state.storageReducer.templateWorkspaceDetail);
    const groupAppDetail = useAppSelector((state) => state.storageReducer.groupAppDetail);

    const Dimens = useDimens();
    const styles = stylesF(Dimens);

    const initialValues = useMemo(
            () => ({
                email: '',
                passWord: '',
            }),
            [],
    );

    const { handleBlur, handleChange, values } = useFormik({
        initialValues,
        validationSchema,
        enableReinitialize: true,
        onSubmit: () => {
        },

    });

    const { callApi: login, loading } = useCallAPI(
            loginService,
            undefined,
            useCallback((data: UserDataModel) => {
                handleLogin(data);
                NavigationService.goBack();
                InteractionManager.runAfterInteractions(() => {
                    callback && callback(fromCart ? { userData: data, isSocial: false } : undefined);
                });
            }, [callback, fromCart]),
            useCallback((status: any) => {
                if (status === 401) {
                    setEmailError(true);
                    setPasswordError(true);
                    return;
                }

                if (status === 500) {
                    setEmailError(true);
                    return;
                }
            }, []),
    );

    const onLoginClick = useCallback(async () => {
        Keyboard.dismiss();
        if (values.email === '' || values.passWord === '') {
            setEmailError(values.email === '');
            setPasswordError(values.passWord === '');
            Toast.showToast(t('message_input_all_field'));
            return;
        }

        if (EMAIL_REGEX.test(values.email) === false) {
            setEmailError(true);
            Toast.showToast(t('login_email_error'));
            return;
        }

        setEmailError(false);
        setPasswordError(false);
        login(
                {
                    email: values.email,
                    password: values.passWord
                }
        );

    }, [login, t, values.email, values.passWord]);

    const renderLogo = useCallback(() => {
        if (isTemplateOrGroupApp()) {
            return (
                <ShadowView
                    style={{ shadowRadius: Dimens.H_15 }}
                >
                    <ImageComponent
                        resizeMode='cover'
                        source={{ uri: isGroupApp() ? groupAppDetail?.group_restaurant_avatar : workspaceDetail?.photo }}
                        style={styles.resLogo}
                    />
                </ShadowView>
            );
        }

        return (
            <AppTextIcon
                width={Dimens.H_130}
                height={Dimens.H_130 / 1.45}
            />
        );
    }, [Dimens.H_130, Dimens.H_15, groupAppDetail?.group_restaurant_avatar, styles.resLogo, workspaceDetail?.photo]);

    return (
        <KeyboardAwareScrollView
            scrollEnabled={false}
            showsVerticalScrollIndicator={false}
            contentContainerStyle={{ flex: 1 }}
            keyboardShouldPersistTaps={'handled'}
        >
            <View style={styles.container}>
                <View style={[styles.tabContainer, { backgroundColor: isGroupApp() ? themeColors.group_color : themeColors.color_primary }]}>
                    <BackButton style={{ position: 'absolute', left: Dimens.W_24, top: Dimens.COMMON_HEADER_PADDING }}/>
                    {renderLogo()}

                    <TextComponent style={styles.welcomeText}>
                        {t('login_welcome_label', { value: isTemplateApp() ? workspaceDetail?.setting_generals?.title : isGroupApp() ? groupAppDetail?.name : 'It’s Ready', interpolation: { escapeValue: false } })}
                    </TextComponent>
                    <TextComponent style={styles.descText}>
                        {t('login_description')}
                    </TextComponent>
                    <InputComponent
                        keyboardType="email-address"
                        backgroundInput={Colors.COLOR_WHITE}
                        borderInput={Colors.COLOR_WHITE}
                        textColorInput={Colors.COLOR_DEFAULT_TEXT_INPUT}
                        placeholderTextColor={Colors.COLOR_INPUT_PLACE_HOLDER}
                        errorBackgroundInput={Colors.COLOR_INPUT_ERROR_BACKGROUND}
                        containerStyle={styles.inputEmail}
                        autoCapitalize={'none'}
                        placeholder={t('hint_email')}
                        value={values.email}
                        onBlur={handleBlur('email')}
                        onChangeText={(text) => {
                            handleChange('email')(text);
                            setEmailError(false);
                        }}
                        error={emailError}
                    />
                    <InputComponent
                        backgroundInput={Colors.COLOR_WHITE}
                        borderInput={Colors.COLOR_WHITE}
                        textColorInput={Colors.COLOR_DEFAULT_TEXT_INPUT}
                        placeholderTextColor={Colors.COLOR_INPUT_PLACE_HOLDER}
                        errorBackgroundInput={Colors.COLOR_INPUT_ERROR_BACKGROUND}
                        eyeColor={Colors.COLOR_DEFAULT_TEXT_INPUT}
                        containerStyle={styles.inputPassword}
                        autoCapitalize={'none'}
                        placeholder={t('text_password')}
                        secureTextEntry
                        value={values.passWord}
                        onBlur={handleBlur('passWord')}
                        onChangeText={(text) => {
                            handleChange('passWord')(text);
                            setPasswordError(false);
                        }}
                        error={passwordError}
                    />

                    <View style={styles.loginButtonContainer}>
                        <ButtonComponent
                            loading={loading}
                            title={t('text_login')}
                            style={styles.buttonLogin}
                            onPress={onLoginClick}
                        />
                        <ButtonComponent
                            title={t('login_forgot_password')}
                            styleTitle={{ textAlign: 'center' }}
                            style={styles.forgotPassButton}
                            onPress={() => NavigationService.navigate(SCREENS.FORGOT_PASSWORD_SCREEN)}
                        />
                    </View>

                    <SocialComponent
                        callback={callback}
                        fromCart={fromCart}
                        isRegister={false}
                    />

                </View>

                <View style={styles.buttonContainer}>
                    <View>
                        <ButtonComponent
                            style={{ backgroundColor: Colors.COLOR_BUTTON_DEFAULT, height: Dimens.W_46 }}
                            title={t('text_register_now')}
                            onPress={() => NavigationService.navigate(SCREENS.CREATE_ACCOUNT_SCREEN)}
                        />
                        <ButtonComponent
                            title={t('text_not_now')}
                            styleTitle={{ color: Colors.COLOR_WHITE_50 }}
                            style={styles.buttonNotNow}
                            onPress={() => NavigationService.goBack()}
                        />
                    </View>

                    <TextComponent style={styles.textAllRight}>
                        {t('login_term_and_conditions', { value: moment().format('YYYY') })}
                        <TextComponent
                            onPress= {() => openInAppBrowser(ITS_READY_TERM_AND_CONDITION_LINK)}
                            style={styles.textTeamAndCondition}
                        >
                            {t('text_title_term_and_conditions').toLowerCase()}
                        </TextComponent>
                    </TextComponent>

                </View>
            </View>
        </KeyboardAwareScrollView>
    );
};

export default LoginScreen;

const stylesF = (Dimens: DimensType) => StyleSheet.create({
    buttonLogin: { flex: 1, backgroundColor: Colors.COLOR_BUTTON_DEFAULT, height: Dimens.W_46 },
    textTeamAndCondition: {
        fontSize: Dimens.FONT_12,
        color: Colors.COLOR_TEXT_COPY_RIGHT,
        textDecorationLine: 'underline',
    },
    textAllRight: {
        fontSize: Dimens.FONT_12,
        color: Colors.COLOR_TEXT_COPY_RIGHT,
    },
    buttonNotNow: {
        backgroundColor: 'transparent',
        alignSelf: 'center',
    },
    forgotPassButton: { flex: 1, backgroundColor: 'transparent' },
    loginButtonContainer: {
        flexDirection: 'row',
        alignItems: 'center',
        marginTop: Dimens.H_12,
        width: '100%',
    },
    inputPassword: { width: '100%', marginTop: Dimens.H_12, height: Dimens.W_46 },
    inputEmail: { width: '100%', marginTop: Dimens.H_18, height: Dimens.W_46 },
    buttonContainer: {
        flex: 1,
        alignItems: 'center',
        justifyContent: 'space-between',
        paddingBottom: Dimens.COMMON_BOTTOM_PADDING,
        marginTop: Dimens.H_20,
    },
    descText: {
        color: Colors.COLOR_WHITE,
        fontSize: Dimens.FONT_16,
        textAlign: 'center',
        marginTop: Dimens.H_4,
        paddingHorizontal: Dimens.W_16,
    },
    welcomeText: {
        color: Colors.COLOR_WHITE,
        fontSize: Dimens.FONT_26,
        fontWeight: '700',
        marginTop: Dimens.H_16,
        textAlign: 'center'
    },
    textContainer: {
        alignItems: 'center',
        paddingTop: Dimens.H_38,
        paddingHorizontal: Dimens.W_50,
    },
    tabContainer: {
        width: '100%',
        paddingTop: Dimens.COMMON_HEADER_PADDING,
        paddingHorizontal: Dimens.W_36,
        alignItems: 'center',
        borderBottomLeftRadius: Dimens.H_22,
        borderBottomRightRadius: Dimens.H_22,
    },
    container: { flex: 1, backgroundColor: '#413E38' },
    resLogo: {
        width: Dimens.W_120,
        height: Dimens.W_120,
        borderRadius: Dimens.H_130,
    },
});