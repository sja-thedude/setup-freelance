import React, {
    useCallback,
    useEffect,
    useMemo,
    useState,
} from 'react';

import { isEqual } from 'lodash';
import {
    StyleSheet,
    View,
} from 'react-native';
import {
    useEffectOnce,
    useUpdateEffect,
} from 'react-use';

import { KeyboardAwareScrollView } from '@pietile-native-kit/keyboard-aware-scrollview';
import { useRoute } from '@react-navigation/native';
import { Images } from '@src/assets/images';
import { AddressIcon } from '@src/assets/svg';
import ButtonComponent from '@src/components/ButtonComponent';
import BackButton from '@src/components/header/BackButton';
import HeaderComponent from '@src/components/header/HeaderComponent';
import ImageSelectComponent from '@src/components/ImageSelect/ImageSelectComponent';
import InputComponent from '@src/components/InputComponent';
import TextComponent from '@src/components/TextComponent';
import Toast from '@src/components/toast/Toast';
import { Colors } from '@src/configs';
import {
    MAX_PHONE_LENGTH,
    PHONE_MASK,
} from '@src/configs/constants';
import {
    useAppDispatch,
    useAppSelector,
} from '@src/hooks';
import useCallAPI from '@src/hooks/useCallAPI';
import useDimens, { DimensType } from '@src/hooks/useDimens';
import { SCREENS } from '@src/navigation/config/screenName';
import { EditProfileScreenProps } from '@src/navigation/NavigationRouteProps';
import NavigationService from '@src/navigation/NavigationService';
import { UserDataModel } from '@src/network/dataModels';
import {
    changeUserAvatarService,
    getUserProfileService,
    removeUserAvatarService,
    updateUserProfileService,
} from '@src/network/services/profileServices';
import { updateUserData } from '@src/network/util/authUtility';
import { LoadingActions } from '@src/redux/toolkit/actions/loadingActions';
import useThemeColors from '@src/themes/useThemeColors';
import {
    removePhoneLeadingZero,
    validateEmail,
    validatePhone,
} from '@src/utils';
import formatWithMask from '@src/utils/maskFormat/formatWithMask';

import PhoneInputComponent from '../auth/register/components/PhoneInputComponent';
import { AddressType } from '../home/component/HomeHeader';
import { useTranslation } from 'react-i18next';

const EditProfileScreen = () => {
    const { themeColors } = useThemeColors();
    const Dimens = useDimens();
    const styles = stylesF(Dimens);
    const { t } = useTranslation();

    const { params } = useRoute<EditProfileScreenProps>();
    const { fromCart } = params;

    const userData = useAppSelector((state) => state.userDataReducer.userData);

    const dispatch = useAppDispatch();

    const originInfo = useMemo(() => ({
        firstName: userData?.first_name || '',
        lastName: userData?.last_name || '',
        email: userData?.email || '',
        phone: userData?.gsm ? userData?.gsm.replace('/', '') : '+32',
        address: userData?.address || '',
        lat: userData?.lat || null,
        lng: userData?.lng || null,
    }), [userData?.address, userData?.email, userData?.first_name, userData?.gsm, userData?.last_name, userData?.lat, userData?.lng]);

    const [info, setInfo] = useState<typeof originInfo>(originInfo);

    const [firstNameError, setFirstNameError] = useState(false);
    const [lastNameError, setLastNameError] = useState(false);
    const [emailError, setEmailError] = useState(false);
    const [phoneError, setPhoneError] = useState(false);

    const updateInfo = useCallback((newInfo: any) => {
        setInfo((state) => ({
            ...state,
            ...newInfo
        }));
    }, []);

    const { callApi: getUserData } = useCallAPI(
            getUserProfileService,
            undefined,
            useCallback((data: UserDataModel) => {
                updateUserData(data);
            }, [])
    );

    const { callApi: changeUserAvatar } = useCallAPI(
            changeUserAvatarService,
            useCallback(() => {
                dispatch(LoadingActions.showGlobalLoading(true));
            }, [dispatch]),
            useCallback((data: any, message: any) => {
                Toast.showToast(message);
                getUserData();
            }, [getUserData])
    );

    const { callApi: removeUserAvatar } = useCallAPI(
            removeUserAvatarService,
            useCallback(() => {
                dispatch(LoadingActions.showGlobalLoading(true));
            }, [dispatch]),
            useCallback((data: any, message: any) => {
                Toast.showToast(message);
                getUserData();
            }, [getUserData])
    );

    const { callApi: updateUserProfile } = useCallAPI(
            updateUserProfileService,
            useCallback(() => {
                dispatch(LoadingActions.showGlobalLoading(true));
            }, [dispatch]),
            useCallback((data: any, message: any) => {
                Toast.showToast(message);
                getUserData();
                if (fromCart) {
                    NavigationService.goBack();
                    NavigationService.navigate(SCREENS.CART_TAB_SCREEN);
                } else {
                    NavigationService.goBack();
                }
            }, [fromCart, getUserData])
    );

    const handleUploadPhoto = useCallback((image: any) => {
        const formData = new FormData();
        formData.append('photo', { uri: image.path, name: 'image', type: image.mime });
        changeUserAvatar(formData);
    }, [changeUserAvatar]);

    const handleDeletePhoto = useCallback(() => {
        removeUserAvatar();
    }, [removeUserAvatar]);

    const validateField = useCallback((showToast: boolean, returnResult: boolean, info?: typeof originInfo ) => {
        const firstName = info?.firstName;
        const lastName = info?.lastName;
        const email = info?.email;
        const phone = info?.phone;

        setFirstNameError(false);
        setLastNameError(false);
        setEmailError(false);
        setPhoneError(false);

        if (!firstName?.trim() || !lastName?.trim() || !email?.trim() || !phone?.trim() || !phone?.replace('+31', '').trim() || !phone?.replace('+32', '').trim()) {
            showToast && Toast.showToast(t('message_input_all_field'));
            setFirstNameError(!firstName?.trim());
            setLastNameError(!lastName?.trim());
            setEmailError(!email?.trim());
            setPhoneError( !phone?.trim() || !phone?.replace('+31', '').trim() || !phone?.replace('+32', '').trim());

            if (returnResult) {
                return false;
            }
        }

        if (firstName?.includes('@') || /\d/.test(firstName || '')) {
            showToast && Toast.showToast(t('message_first_name_include_at'));
            setFirstNameError(true);

            if (returnResult) {
                return false;
            }
        }

        if (!validateEmail(email)) {
            showToast && Toast.showToast(t('message_email_error'));
            setEmailError(true);
            if (returnResult) {
                return false;
            }
        }

        if (!validatePhone(phone)) {
            showToast && Toast.showToast(t('message_phone_error'));
            setPhoneError(true);
            if (returnResult) {
                return false;
            }
        }

        if (returnResult) {
            return true;
        }

    }, [t]);

    const saveUserInfo = useCallback(() => {
        updateInfo({ phone: removePhoneLeadingZero(info.phone) });
        const isValid = validateField(true, true, info);

        if (isValid) {
            updateUserProfile({
                first_name: info.firstName,
                last_name: info.lastName,
                email: info.email,
                gsm: removePhoneLeadingZero(info.phone),
                address: info.address,
                lat: info.lat,
                lng: info.lng,
            });
        }
    }, [info, updateInfo, updateUserProfile, validateField]);

    const disableSave = useMemo(() =>
        isEqual(info, originInfo)
        // if (isEqual(info, originInfo)) {
        //     return true;
        // }

    // const { firstName, lastName, email, phone } = info;
    // if (EMAIL_REGEX.test(email) === false || firstName.includes('@')) {
    //     return true;
    // }
    // return isEmptyOrUndefined(firstName.trim()) || isEmptyOrUndefined(lastName.trim()) || isEmptyOrUndefined(email.trim()) || isEmptyOrUndefined(phone.trim());
    , [info, originInfo]);

    const handleSelectAddress = useCallback((newAddress: AddressType) => {
        updateInfo({ address: newAddress.address, lat: `${newAddress.lat}`, lng: `${newAddress.lng}` });
    }, [updateInfo]);

    const selectAddress = useCallback(() => {
        NavigationService.navigate(SCREENS.SELECT_ADDRESS_SCREEN, { onSelectAddress: handleSelectAddress });
    }, [handleSelectAddress]);

    // validate when open screen
    useEffectOnce(() => {
        validateField(false, false, info);
        getUserData().then((res) => {
            if (res.success) {
                const newInfo: typeof info = {
                    firstName: res.data.first_name || '',
                    lastName: res.data.last_name || '',
                    email: res.data.email || '',
                    phone: res.data.gsm ? res.data.gsm.replace('/', '') : '+32',
                    address: res.data.address || '',
                    lat: res.data.lat || null,
                    lng: res.data.lng || null,
                };
                validateField(false, false, newInfo);
            }
        });
    });

    useEffect(() => {
        updateInfo({
            firstName: userData?.first_name || '',
            lastName: userData?.last_name || '',
            email: userData?.email || '',
            phone: userData?.gsm ? userData?.gsm.replace('/', '') : '+32',
            address: userData?.address || '',
            lat: userData?.lat || null,
            lng: userData?.lng || null,
        });
    }, [updateInfo, userData?.address, userData?.email, userData?.first_name, userData?.gsm, userData?.last_name, userData?.lat, userData?.lng]);

    // validate when update info from cart
    useUpdateEffect(() => {
        setTimeout(() => {
            const newInfo: typeof info = {
                firstName: userData?.first_name || '',
                lastName: userData?.last_name || '',
                email: userData?.email || '',
                phone: userData?.gsm ? userData?.gsm.replace('/', '') : '+32',
                address: userData?.address || '',
                lat: userData?.lat || null,
                lng: userData?.lng || null,
            };
            validateField(false, false, newInfo);
        }, 2000);
    }, [userData?.first_name, userData?.last_name, userData?.email, userData?.gsm, userData?.address, userData?.lat, userData?.lng, validateField]);

    return (
        <View style={{ flex: 1 }}>
            <HeaderComponent >
                <View style={{ flexDirection: 'row', alignItems: 'center' }}>
                    {!fromCart && (
                        <BackButton/>
                    )}
                    <TextComponent style={styles.headerText}>
                        {t('text_change_profile')}
                    </TextComponent>
                </View>
            </HeaderComponent>
            <KeyboardAwareScrollView
                bounces={false}
                showsVerticalScrollIndicator={false}
            >
                <ImageSelectComponent
                    defaultImage={Images.defaultUserAvatar}
                    source={{ uri: userData?.photo }}
                    style={styles.avatar}
                    containerStyle={styles.avatarContainer}
                    onSelectImage={(image: any) => handleUploadPhoto(image)}
                    onDeleteImage={handleDeletePhoto}
                />

                <View style={{ paddingHorizontal: Dimens.W_36 }}>
                    <InputComponent
                        error={firstNameError}
                        containerStyle={styles.input}
                        placeholder={t('hint_first_name')}
                        value={info.firstName}
                        onChangeText={(text) => {
                            updateInfo({ firstName: text });
                            setFirstNameError(false);
                        }}
                    />
                    <InputComponent
                        error={lastNameError}
                        containerStyle={styles.input}
                        placeholder={t('hint_last_name')}
                        value={info.lastName}
                        onChangeText={(text) => {
                            updateInfo({ lastName: text });
                            setLastNameError(false);
                        }}
                    />
                    <InputComponent
                        error={emailError}
                        keyboardType="email-address"
                        containerStyle={styles.input}
                        autoCapitalize={'none'}
                        placeholder={t('hint_email')}
                        value={info.email}
                        onChangeText={(text) => {
                            updateInfo({ email: text });
                            setEmailError(false);
                        }}
                    />
                    <PhoneInputComponent
                        error={phoneError}
                        keyboardType="phone-pad"
                        containerStyle={styles.input}
                        autoCapitalize={'none'}
                        placeholder={t('text_mobiel_telefoonnummer')}
                        value={info.phone}
                        onChangeText={useCallback((text) => {
                            if (text.length > 3) {
                                setPhoneError(false);
                            }
                            const { masked, unmasked } = formatWithMask({
                                text: text,
                                mask: PHONE_MASK,
                            });

                            if (unmasked.length <= MAX_PHONE_LENGTH) {
                                updateInfo({ phone: masked });
                            }
                        }, [updateInfo])}
                    />
                    <TextComponent style={styles.optionText}>
                        {t('text_phone_vb')}
                    </TextComponent>

                    <InputComponent
                        containerStyle={styles.input}
                        autoCapitalize={'none'}
                        placeholder={t('hint_address')}
                        leftIcon={(
                            <AddressIcon
                                width={Dimens.W_18}
                                height={Dimens.H_22}
                                stroke={themeColors.color_text}
                            />
                        )}
                        value={info.address}
                        onChangeText={() => {
                        }}
                        inputPress={selectAddress}
                    />
                    <TextComponent style={styles.optionText}>
                        {t('options_name_optioneel')}
                    </TextComponent>
                </View>

                <ButtonComponent
                    disabled={disableSave}
                    title={t('text_save')}
                    style={{ marginHorizontal: Dimens.W_80, marginTop: Dimens.H_40 }}
                    onPress={saveUserInfo}
                />
            </KeyboardAwareScrollView>
        </View>
    );
};

export default EditProfileScreen;

const stylesF = (Dimens: DimensType) => StyleSheet.create({
    optionText: {
        fontSize: Dimens.FONT_12,
        marginTop: Dimens.H_2,
        marginLeft: Dimens.W_16,
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
    },
    input: {
        marginTop: Dimens.H_12,
    },
});