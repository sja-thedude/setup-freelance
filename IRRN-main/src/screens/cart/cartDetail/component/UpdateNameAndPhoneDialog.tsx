import React, {
    useCallback,
    useMemo,
    useState,
} from 'react';

import {
    Keyboard,
    StyleSheet,
    View,
} from 'react-native';
import Animated, {
    FadeIn,
    FadeOut,
} from 'react-native-reanimated';

import ButtonComponent from '@src/components/ButtonComponent';
import DialogComponent from '@src/components/DialogComponent';
import InputComponent from '@src/components/InputComponent';
import TextComponent from '@src/components/TextComponent';
import { Colors } from '@src/configs';
import {
    MAX_PHONE_LENGTH,
    PHONE_MASK,
} from '@src/configs/constants';
import { useAppSelector } from '@src/hooks';
import useBoolean from '@src/hooks/useBoolean';
import useCallAPI from '@src/hooks/useCallAPI';
import useDimens, { DimensType } from '@src/hooks/useDimens';
import { UserDataModel } from '@src/network/dataModels';
import {
    getUserProfileService,
    updateUserProfileService,
} from '@src/network/services/profileServices';
import { updateUserData } from '@src/network/util/authUtility';
import PhoneInputComponent from '@src/screens/auth/register/components/PhoneInputComponent';
import useThemeColors from '@src/themes/useThemeColors';
import {
    removePhoneLeadingZero,
    validateEmail,
    validatePhone,
} from '@src/utils';
import formatWithMask from '@src/utils/maskFormat/formatWithMask';
import { useTranslation } from 'react-i18next';

interface ModalProps {
    isShow: boolean,
    hideModal: () => void,
    checkSuggestionProduct: () => void,
}

const UpdateNameAndPhoneDialog = ({ isShow, hideModal, checkSuggestionProduct }: ModalProps) => {
    const { themeColors } = useThemeColors();
    const Dimens = useDimens();
    const styles = stylesF(Dimens);
    const { t } = useTranslation();

    const userData = useAppSelector((state) => state.userDataReducer.userData);

    const [isShowToast, showToast, hideToast] = useBoolean(false);

    const [name, setName] = useState('');
    const [phone, setPhone] = useState('');
    const [email, setEmail] = useState('');
    const [toastText, setToastText] = useState('');

    const [firstNameError, setFirstNameError] = useState(false);
    const [phoneError, setPhoneError] = useState(false);
    const [emailError, setEmailError] = useState(false);

    const requiredName = useMemo(() => !userData?.first_name || userData?.first_name?.includes('@') || /\d/.test(userData?.first_name), [userData?.first_name]);
    const requiredPhone = useMemo(() => !userData?.gsm, [userData?.gsm]);
    const requiredEmail = useMemo(() => !userData?.email || !validateEmail(userData?.email), [userData?.email]);

    const justRequiredName = useMemo(() => requiredName && !requiredPhone && !requiredEmail, [requiredEmail, requiredName, requiredPhone]);
    const justRequiredPhone = useMemo(() => requiredPhone && !requiredName && !requiredEmail, [requiredEmail, requiredName, requiredPhone]);
    const justRequiredEmail = useMemo(() => requiredEmail && !requiredName && !requiredPhone, [requiredEmail, requiredName, requiredPhone]);

    const requiredNameAndPhone = useMemo(() => requiredName && requiredPhone, [requiredName, requiredPhone]);
    const requiredNameAndEmail = useMemo(() => requiredName && requiredEmail, [requiredName, requiredEmail]);
    const requiredEmailAndPhone = useMemo(() => requiredEmail && requiredPhone, [requiredEmail, requiredPhone]);
    const requiredAtLeast2Field = useMemo(() => requiredNameAndPhone || requiredNameAndEmail || requiredEmailAndPhone, [requiredEmailAndPhone, requiredNameAndEmail, requiredNameAndPhone]);
    const requiredAllField = useMemo(() => requiredName && requiredPhone && requiredEmail, [requiredEmail, requiredName, requiredPhone]);

    const message = useMemo(() =>
        t('Vul de ontbrekende gegevens in om uw account te vervolledigen.')
        // if (requiredAtLeast2Field) {
        //     return t('Vul de ontbrekende gegevens aan om uw account te vervolledigen.');
        // }

    // if (justRequiredName) {
    //     return t('text_description_update_first_name');
    // }

    // if (justRequiredPhone) {
    //     return t('text_description_update_gsm');
    // }

    // if (justRequiredEmail) {
    //     return t('Vul uw e-mailadres in om uw account te vervolledigen');
    // }
    , [t]);

    const disableSave = useMemo(() => {
        if (requiredAllField) {
            if (!name.trim()) {
                return true;
            }

            if (phone?.length <= 3) {
                return true;
            }

            if (!email.trim()) {
                return true;
            }
        }

        if (requiredAtLeast2Field) {
            if (requiredNameAndPhone) {
                if (phone?.length <= 3 || !name.trim()) {
                    return true;
                }
                return false;
            }

            if (requiredNameAndEmail) {
                if (!email.trim() || !name.trim()) {
                    return true;
                }
                return false;
            }

            if (requiredEmailAndPhone) {
                if (!email.trim() || phone?.length <= 3) {
                    return true;
                }
                return false;
            }
        }

        if (justRequiredName) {
            if (!name.trim()) {
                return true;
            }
            return false;
        }

        if (justRequiredPhone) {
            if (phone?.length <= 3) {
                return true;
            }
            return false;
        }

        if (justRequiredEmail) {
            if (!email.trim()) {
                return true;
            }
            return false;
        }

    }, [email, justRequiredEmail, justRequiredName, justRequiredPhone, name, phone?.length, requiredAllField, requiredAtLeast2Field, requiredEmailAndPhone, requiredNameAndEmail, requiredNameAndPhone]);

    const handleDismissModal = useCallback(() => {
        hideModal();
        setName('');
        setPhone('');
        setEmail('');
        setToastText('');
        setFirstNameError(false);
        setPhoneError(false);
        setEmailError(false);
    }, [hideModal]);

    const { callApi: getUserData, loading: loadingProfile } = useCallAPI(
            getUserProfileService,
            undefined,
            useCallback((data: UserDataModel) => {
                updateUserData(data);
                handleDismissModal();
                checkSuggestionProduct();
            }, [checkSuggestionProduct, handleDismissModal])
    );

    const { callApi: updateUserProfile, loading } = useCallAPI(
            updateUserProfileService,
            undefined,
            useCallback(() => {
                getUserData();
            }, [getUserData])
    );

    const handleShowToast = useCallback((text: string) => {
        setToastText(text);
        showToast();
        setTimeout(() => {
            hideToast();
        }, 1500);
    }, [hideToast, showToast]);

    const validateField = useCallback(() => {
        setFirstNameError(false);
        setPhoneError(false);
        setEmailError(false);

        if (requiredAllField) {
            const nameError = name.includes('@') || /\d/.test(name || '') || name?.trim() === '';
            const phoneError = !validatePhone(phone);
            const emailError = !validateEmail(email);

            if (nameError) {
                setFirstNameError(true);
            }

            if (phoneError) {
                setPhoneError(true);
            }

            if (emailError) {
                setEmailError(true);
            }

            if ((nameError && phoneError && emailError) || (nameError && phoneError) || (nameError && emailError) || (phoneError && emailError)) {
                handleShowToast(t('Vul de ontbrekende gegevens in om uw account te vervolledigen.'));
            } else if (nameError) {
                handleShowToast(t('text_description_update_first_name'));
            } else if (phoneError) {
                handleShowToast(t('message_phone_error'));
            } else if (emailError) {
                handleShowToast(t('Vul uw e-mailadres in om uw account te vervolledigen.'));
            }

            return !nameError && !phoneError && !emailError;
        }

        if (requiredAtLeast2Field) {
            if (requiredNameAndPhone) {
                const nameError = name.includes('@') || /\d/.test(name || '') || name?.trim() === '';
                const phoneError = !validatePhone(phone);

                if (nameError) {
                    setFirstNameError(true);
                }

                if (phoneError) {
                    setPhoneError(true);
                }

                if (nameError && phoneError) {
                    handleShowToast(t('Vul de ontbrekende gegevens in om uw account te vervolledigen.'));
                } else if (nameError) {
                    handleShowToast(t('text_description_update_first_name'));
                } else if (phoneError) {
                    handleShowToast(t('message_phone_error'));
                }

                return !nameError && !phoneError;
            }

            if (requiredNameAndEmail) {
                const nameError = name.includes('@') || /\d/.test(name || '') || name?.trim() === '';
                const emailError = !validateEmail(email);

                if (nameError) {
                    setFirstNameError(true);
                }

                if (emailError) {
                    setEmailError(true);
                }

                if (nameError && emailError) {
                    handleShowToast(t('Vul de ontbrekende gegevens in om uw account te vervolledigen.'));
                } else if (nameError) {
                    handleShowToast(t('text_description_update_first_name'));
                } else if (emailError) {
                    handleShowToast(t('Vul uw e-mailadres in om uw account te vervolledigen.'));
                }

                return !nameError && !emailError;
            }

            if (requiredEmailAndPhone) {
                const phoneError = !validatePhone(phone);
                const emailError = !validateEmail(email);

                if (phoneError) {
                    setPhoneError(true);
                }

                if (emailError) {
                    setEmailError(true);
                }

                if (phoneError && emailError) {
                    handleShowToast(t('Vul de ontbrekende gegevens in om uw account te vervolledigen.'));
                } else if (phoneError) {
                    handleShowToast(t('message_phone_error'));
                } else if (emailError) {
                    handleShowToast(t('Vul uw e-mailadres in om uw account te vervolledigen.'));
                }

                return !phoneError && !emailError;
            }

        }

        if (justRequiredName) {
            const nameError = name.includes('@') || /\d/.test(name || '') || name?.trim() === '';
            if (nameError) {
                handleShowToast(t('text_description_update_first_name'));
                setFirstNameError(true);
                return false;
            }

            return true;
        }

        if (justRequiredPhone) {
            const phoneError = !validatePhone(phone);
            if (phoneError) {
                handleShowToast(t('message_phone_error'));
                setPhoneError(true);
                return false;
            }

            return true;
        }

        if (justRequiredEmail) {
            const emailError = !validateEmail(email);
            if (emailError) {
                handleShowToast(t('Vul uw e-mailadres in om uw account te vervolledigen.'));
                setEmailError(true);
                return false;
            }

            return true;
        }

        return false;

    }, [email, handleShowToast, justRequiredEmail, justRequiredName, justRequiredPhone, name, phone, requiredAllField, requiredAtLeast2Field, requiredEmailAndPhone, requiredNameAndEmail, requiredNameAndPhone, t]);

    const handleSave = useCallback(() => {
        setPhone(removePhoneLeadingZero(phone));
        const isValid = validateField();

        if (isValid) {
            Keyboard.dismiss();
            updateUserProfile({
                first_name: requiredName ? name : userData?.first_name,
                gsm: requiredPhone ? removePhoneLeadingZero(phone) : userData?.gsm,
                email: requiredEmail ? email : userData?.email,
                required_only_gsm: 1
            });
        }
    }, [email, name, phone, requiredEmail, requiredName, requiredPhone, updateUserProfile, userData?.email, userData?.first_name, userData?.gsm, validateField]);

    return (
        <DialogComponent
            avoidKeyboard
            hideModal={handleDismissModal}
            isVisible={isShow}
            onSwipeComplete={handleDismissModal}
            onBackdropPress={handleDismissModal}
            containerStyle={{ paddingHorizontal: Dimens.W_24 }}
        >
            <TextComponent style={styles.textTitle}>
                {t('vervolledig_uw_account')}
            </TextComponent>

            {requiredName && (
                <InputComponent
                    error={firstNameError}
                    containerStyle={styles.input}
                    borderInput={themeColors.color_common_line}
                    placeholder={t('hint_first_name')}
                    value={name}
                    onChangeText={(text) => {
                        setName(text);
                        setFirstNameError(false);
                    }}
                />
            )}

            {requiredPhone && (
                <>
                    <PhoneInputComponent
                        error={phoneError}
                        keyboardType="phone-pad"
                        containerStyle={styles.input}
                        borderInput={themeColors.color_common_line}
                        autoCapitalize={'none'}
                        placeholder={t('text_mobiel_telefoonnummer')}
                        value={phone}
                        onChangeText={(text) => {
                            setPhoneError(false);

                            const { masked, unmasked } = formatWithMask({
                                text: text,
                                mask: PHONE_MASK,
                            });

                            if (unmasked.length <= MAX_PHONE_LENGTH) {
                                setPhone(masked);
                            }
                        }}
                    />
                    <TextComponent style={[styles.optionText]}>
                        {t('text_phone_vb')}
                    </TextComponent>
                </>
            )}

            {requiredEmail && (
                <InputComponent
                    error={emailError}
                    keyboardType="email-address"
                    containerStyle={styles.input}
                    borderInput={themeColors.color_common_line}
                    autoCapitalize={'none'}
                    placeholder={t('hint_email')}
                    value={email}
                    onChangeText={(text) => {
                        setEmailError(false);
                        setEmail(text);
                    }}
                />
            )}

            <TextComponent style={[styles.textMsg]}>
                {message}
            </TextComponent>

            <ButtonComponent
                disabled={disableSave}
                loading={loading || loadingProfile}
                title={t('text_save')}
                style={styles.textAButton}
                onPress={handleSave}
            />

            {isShowToast && (
                <Animated.View
                    entering={FadeIn}
                    exiting={FadeOut}
                    style={styles.toastContainer}
                >
                    <View style={styles.toastStyle}>
                        <TextComponent
                            style={styles.toastTextStyle}
                        >
                            {toastText}
                        </TextComponent>
                    </View>
                </Animated.View>
            )}

        </DialogComponent>
    );
};

export default UpdateNameAndPhoneDialog;

const stylesF = (Dimens: DimensType) => StyleSheet.create({
    toastContainer: { position: 'absolute', alignSelf: 'center', bottom: Dimens.COMMON_BOTTOM_PADDING * 3 },
    textAButton: {
        width: '60%',
        alignSelf: 'center',
        marginTop: Dimens.H_34,
    },
    textTitle: {
        fontSize: Dimens.FONT_24,
        fontWeight: '700',
        marginTop: Dimens.H_16,
    },
    textMsg: {
        fontSize: Dimens.FONT_14,
        marginTop: Dimens.H_16,
        fontWeight: '300'
    },
    input: {
        marginTop: Dimens.H_20,
    },
    optionText: {
        fontSize: Dimens.FONT_12,
        marginTop: Dimens.H_5,
        marginLeft: Dimens.W_16,
        fontWeight: '300'
    },
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