import React, {
    useCallback,
    useMemo,
    useState,
} from 'react';

import { useFormik } from 'formik';
import {
    StyleSheet,
    View,
} from 'react-native';
import Animated, { FadeInRight } from 'react-native-reanimated';
import * as Yup from 'yup';

import { KeyboardAwareScrollView } from '@pietile-native-kit/keyboard-aware-scrollview';
import {
    CheckedSquareIcon,
    RegisterSuccessIcon,
    UnCheckedSquareIcon,
} from '@src/assets/svg';
import ButtonComponent from '@src/components/ButtonComponent';
import BackButton from '@src/components/header/BackButton';
import InputComponent from '@src/components/InputComponent';
import TextComponent from '@src/components/TextComponent';
import Toast from '@src/components/toast/Toast';
import TouchableComponent from '@src/components/TouchableComponent';
import { Colors } from '@src/configs';
import {
    ITS_READY_PRIVACY_LINK,
    ITS_READY_TERM_AND_CONDITION_LINK,
    MAX_PHONE_LENGTH,
    MIN_PASSWORD_LENGTH,
    PHONE_MASK,
} from '@src/configs/constants';
import { useAppDispatch } from '@src/hooks';
import useCallAPI from '@src/hooks/useCallAPI';
import useDimens, { DimensType } from '@src/hooks/useDimens';
import { registerService } from '@src/network/services/authServices';
import { LoadingActions } from '@src/redux/toolkit/actions/loadingActions';
import useThemeColors from '@src/themes/useThemeColors';
import {
    isGroupApp,
    openInAppBrowser,
    removePhoneLeadingZero,
    validateEmail,
    validatePhone,
} from '@src/utils';
import formatWithMask from '@src/utils/maskFormat/formatWithMask';

import SocialComponent from '../login/component/SocialComponent';
import PhoneInputComponent from './components/PhoneInputComponent';
import { useTranslation } from 'react-i18next';

const validationSchema = Yup.object({
    firstName: Yup.string(),
    lastName: Yup.string(),
    email: Yup.string(),
    phone: Yup.string(),
    passWord: Yup.string(),
    rePassWord: Yup.string(),
});

const CreateAccountScreen = () => {
    const { themeColors } = useThemeColors();
    const Dimens = useDimens();
    const styles = stylesF(Dimens);
    const { t } = useTranslation();

    const dispatch = useAppDispatch();

    const [firstNameError, setFirstNameError] = useState(false);
    const [lastNameError, setLastNameError] = useState(false);
    const [emailError, setEmailError] = useState(false);
    const [phoneError, setPhoneError] = useState(false);
    const [passwordError, setPasswordError] = useState(false);
    const [rePasswordError, setRePasswordError] = useState(false);

    const [checkedPolicyError, setCheckedPolicyError] = useState(false);
    const [checkedPolicy, setCheckedPolicy] = useState(false);

    const [registerSuccess, setRegisterSuccess] = useState(false);

    const initialValues = useMemo(
            () => ({
                firstName: '',
                lastName: '',
                email: '',
                phone: '',
                passWord: '',
                rePassWord: '',
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

    const { callApi: register, loading } = useCallAPI(
            registerService,
            useCallback(() => {
                dispatch(LoadingActions.showGlobalLoading(true));
            }, [dispatch]),
            useCallback(() => {
                setRegisterSuccess(true);
            }, [])
    );

    const validateField = useCallback(() => {
        const { firstName, lastName, email, phone, passWord, rePassWord } = values;

        setCheckedPolicyError(!checkedPolicy);

        console.log('phone', phone);

        if (!firstName.trim() || !lastName.trim() || !email.trim() || !phone.trim() || phone === '+32' || phone === '+31' || !passWord || !rePassWord) {
            Toast.showToast(t('message_input_all_field'));
            setFirstNameError(!firstName.trim());
            setLastNameError(!lastName.trim());
            setPhoneError(!phone.trim() || phone.trim() === '+32' || phone.trim() === '+31');
            setEmailError(!email.trim());
            setPasswordError(!passWord);
            setRePasswordError(!rePassWord);

            return false;
        }

        if (!validatePhone(phone)) {
            Toast.showToast(t('message_phone_error'));
            setPhoneError(true);
            return false;
        }

        if (!validateEmail(email)) {
            Toast.showToast(t('message_email_error'));
            setEmailError(true);
            return false;
        }

        if (passWord.length < MIN_PASSWORD_LENGTH) {
            Toast.showToast(t('message_password_min_length'));
            setPasswordError(true);
            setRePasswordError(true);
            return false;
        }

        if (passWord !== rePassWord) {
            Toast.showToast(t('message_password_not_match'));
            setPasswordError(true);
            setRePasswordError(true);
            return false;
        } else {
            setPasswordError(false);
            setRePasswordError(false);
        }

        if (!checkedPolicy) {
            Toast.showToast(t('message_certify_error'));
            return false;
        }

        return true;

    }, [checkedPolicy, t, values]);

    const saveUserInfo = useCallback(() => {
        handleChange('phone')(removePhoneLeadingZero(values.phone));

        const allFieldsValid = validateField();

        if (allFieldsValid) {
            register({
                first_name: values.firstName,
                last_name: values.lastName,
                email: values.email,
                password: values.passWord,
                password_confirmation: values.rePassWord,
                gsm: removePhoneLeadingZero(values.phone),
            });
        }
    }, [handleChange, register, validateField, values.email, values.firstName, values.lastName, values.passWord, values.phone, values.rePassWord]);

    return (
        <View
            style={[styles.mainContainer, { backgroundColor: isGroupApp() ? themeColors.group_color : themeColors.color_primary }]}
        >
            <View style={styles.header}>
                <BackButton/>
                {!registerSuccess && (
                    <TextComponent style={styles.headerText}>
                        {t('text_register')}
                    </TextComponent>
                )}
            </View>

            {registerSuccess ? (
                                <Animated.View
                                    style={styles.successContainer}
                                    entering={FadeInRight}
                                >
                                    <TextComponent style={styles.headerSuccessText}>
                                        {t('text_register_success_title')}
                                    </TextComponent>
                                    <TextComponent style={{ ...styles.conditionText, ...styles.successDesc }}>
                                        {t('text_register_success_description')}
                                    </TextComponent>
                                    <RegisterSuccessIcon
                                        width={Dimens.H_100}
                                        height={Dimens.H_100}
                                    />
                                </Animated.View>
                            ) : (
                               <>
                                   <KeyboardAwareScrollView
                                       bounces={false}
                                       showsVerticalScrollIndicator={false}
                                   >
                                       <View style={styles.regDescText}>
                                           <TextComponent style={{ ...styles.conditionText, textAlign: 'center' }}>
                                               {t('text_register_description')}
                                           </TextComponent>

                                           <View style={{ flexDirection: 'row' }}>
                                               <InputComponent
                                                   error={firstNameError}
                                                   containerStyle={{ ...styles.input, marginRight: Dimens.W_6 }}
                                                   backgroundInput={Colors.COLOR_WHITE}
                                                   borderInput={Colors.COLOR_WHITE}
                                                   textColorInput={Colors.COLOR_DEFAULT_TEXT_INPUT}
                                                   placeholderTextColor={Colors.COLOR_INPUT_PLACE_HOLDER}
                                                   errorBackgroundInput={Colors.COLOR_INPUT_ERROR_BACKGROUND}
                                                   placeholder={t('hint_first_name')}
                                                   value={values.firstName}
                                                   onBlur={handleBlur('firstName')}
                                                   onChangeText={(text) => {
                                                       handleChange('firstName')(text);
                                                       setFirstNameError(false);
                                                   }}
                                               />
                                               <InputComponent
                                                   error={lastNameError}
                                                   containerStyle={{ ...styles.input, marginLeft: Dimens.W_6 }}
                                                   backgroundInput={Colors.COLOR_WHITE}
                                                   borderInput={Colors.COLOR_WHITE}
                                                   textColorInput={Colors.COLOR_DEFAULT_TEXT_INPUT}
                                                   placeholderTextColor={Colors.COLOR_INPUT_PLACE_HOLDER}
                                                   errorBackgroundInput={Colors.COLOR_INPUT_ERROR_BACKGROUND}
                                                   placeholder={t('hint_last_name')}
                                                   value={values.lastName}
                                                   onBlur={handleBlur('lastName')}
                                                   onChangeText={(text) => {
                                                       handleChange('lastName')(text);
                                                       setLastNameError(false);
                                                   }}
                                               />
                                           </View>

                                           <PhoneInputComponent
                                               error={phoneError}
                                               keyboardType="phone-pad"
                                               containerStyle={styles.input}
                                               backgroundInput={Colors.COLOR_WHITE}
                                               borderInput={Colors.COLOR_WHITE}
                                               textColorInput={Colors.COLOR_DEFAULT_TEXT_INPUT}
                                               placeholderTextColor={Colors.COLOR_INPUT_PLACE_HOLDER}
                                               errorBackgroundInput={Colors.COLOR_INPUT_ERROR_BACKGROUND}
                                               autoCapitalize={'none'}
                                               placeholder={t('text_mobiel_telefoonnummer')}
                                               value={values.phone}
                                               onBlur={handleBlur('phone')}
                                               onChangeText={(text) => {
                                                   setPhoneError(false);

                                                   const { masked, unmasked } = formatWithMask({
                                                       text: text,
                                                       mask: PHONE_MASK,
                                                   });

                                                   if (unmasked.length <= MAX_PHONE_LENGTH) {
                                                       handleChange('phone')(masked);
                                                   }
                                               }}
                                           />
                                           <TextComponent style={styles.optionText}>
                                               {t('text_phone_vb')}
                                           </TextComponent>

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
                                               value={values.email}
                                               onBlur={handleBlur('email')}
                                               onChangeText={(text) => {
                                                   handleChange('email')(text);
                                                   setEmailError(false);
                                               }}
                                           />
                                           <InputComponent
                                               error={passwordError}
                                               backgroundInput={Colors.COLOR_WHITE}
                                               borderInput={Colors.COLOR_WHITE}
                                               textColorInput={Colors.COLOR_DEFAULT_TEXT_INPUT}
                                               placeholderTextColor={Colors.COLOR_INPUT_PLACE_HOLDER}
                                               errorBackgroundInput={Colors.COLOR_INPUT_ERROR_BACKGROUND}
                                               eyeColor={Colors.COLOR_DEFAULT_TEXT_INPUT}
                                               containerStyle={styles.input}
                                               autoCapitalize={'none'}
                                               placeholder={t('text_password')}
                                               secureTextEntry
                                               value={values.passWord}
                                               onBlur={handleBlur('passWord')}
                                               onChangeText={(text) => {
                                                   handleChange('passWord')(text);
                                                   setPasswordError(false);
                                               }}
                                           />
                                           <InputComponent
                                               error={rePasswordError}
                                               backgroundInput={Colors.COLOR_WHITE}
                                               borderInput={Colors.COLOR_WHITE}
                                               textColorInput={Colors.COLOR_DEFAULT_TEXT_INPUT}
                                               placeholderTextColor={Colors.COLOR_INPUT_PLACE_HOLDER}
                                               errorBackgroundInput={Colors.COLOR_INPUT_ERROR_BACKGROUND}
                                               eyeColor={Colors.COLOR_DEFAULT_TEXT_INPUT}
                                               containerStyle={styles.input}
                                               autoCapitalize={'none'}
                                               placeholder={t('text_password_repeat')}
                                               secureTextEntry
                                               value={values.rePassWord}
                                               onBlur={handleBlur('rePassWord')}
                                               onChangeText={(text) => {
                                                   handleChange('rePassWord')(text);
                                                   setRePasswordError(false);
                                               }}
                                           />

                                           <View style={styles.privacyContainer}>
                                               <TouchableComponent
                                                   onPress={() => setCheckedPolicy(!checkedPolicy)}
                                                   hitSlop={Dimens.DEFAULT_HIT_SLOP}
                                               >
                                                   {checkedPolicy ? (
                                                                <CheckedSquareIcon
                                                                    width={Dimens.W_18}
                                                                    height={Dimens.W_18}
                                                                />
                                                            ) : (
                                                                    <UnCheckedSquareIcon
                                                                        width={Dimens.W_18}
                                                                        height={Dimens.W_18}
                                                                        stroke={checkedPolicyError ? themeColors.color_error : ''}
                                                                    />
                                                                )}

                                               </TouchableComponent>
                                               <TextComponent style={styles.policyText}>
                                                   {`${t('text_request_certify')} `}
                                                   <TextComponent
                                                       onPress= {() => openInAppBrowser(ITS_READY_TERM_AND_CONDITION_LINK)}
                                                       style={styles.underLineText}
                                                   >
                                                       {t('text_title_term_and_conditions').toLocaleLowerCase()}
                                                   </TextComponent>
                                                   {` ${t('text_and')} `}
                                                   <TextComponent
                                                       onPress={() => openInAppBrowser(ITS_READY_PRIVACY_LINK)}
                                                       style={styles.underLineText}
                                                   >
                                                       {t('text_privacybeleid').toLocaleLowerCase()}.
                                                   </TextComponent>
                                               </TextComponent>
                                           </View>

                                           <ButtonComponent
                                               loading={loading}
                                               title={t('text_registreer')}
                                               style={styles.registerButton}
                                               onPress={saveUserInfo}
                                           />

                                           <SocialComponent isRegister/>

                                       </View>
                                   </KeyboardAwareScrollView>
                               </>
                            )
            }

        </View>
    );
};

export default CreateAccountScreen;

const stylesF = (Dimens: DimensType) => StyleSheet.create({
    registerButton: {
        marginHorizontal: Dimens.W_26,
        marginTop: Dimens.H_22,
        marginBottom: Dimens.H_4,
        backgroundColor: Colors.COLOR_BUTTON_DEFAULT,
        height: Dimens.W_46
    },
    underLineText: {
        textDecorationLine: 'underline',
        color: Colors.COLOR_WHITE,
    },
    privacyContainer: {
        flexDirection: 'row',
        marginTop: Dimens.H_12,
        alignItems: 'center',
    },
    regDescText: { paddingHorizontal: Dimens.W_36, marginTop: Dimens.H_3 },
    successDesc: { textAlign: 'center', flex: 0, marginBottom: Dimens.H_32 },
    successContainer: {
        flex: 1,
        marginTop: Dimens.H_38,
        paddingHorizontal: Dimens.W_26,
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
        marginTop: Dimens.H_10,
        marginBottom: Dimens.H_4,
    },
    policyText: {
        color: Colors.COLOR_WHITE,
        fontSize: Dimens.FONT_14,
        flex: 1,
        marginTop: Dimens.H_8,
        marginLeft: Dimens.H_8
    },
    optionText: {
        fontSize: Dimens.FONT_12,
        marginTop: Dimens.H_2,
        marginLeft: Dimens.W_16,
        color: Colors.COLOR_WHITE,
    },
    avatar: {
        width: Dimens.H_100,
        height: Dimens.H_100,
        borderRadius: Dimens.H_100,
    },
    avatarContainer: {
        width: Dimens.H_100,
        height: Dimens.H_100,
        marginVertical: Dimens.H_20,
        alignSelf: 'center',
    },
    headerText: {
        color: Colors.COLOR_WHITE,
        fontSize: Dimens.FONT_26,
        fontWeight: '700',
        textAlign: 'center',
        flex: 1,
        marginRight: Dimens.W_32,
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
        height: Dimens.W_46
    },
});