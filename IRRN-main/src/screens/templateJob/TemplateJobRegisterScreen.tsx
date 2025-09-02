import React, {
    useCallback,
    useRef,
    useState,
} from 'react';

import { useTranslation } from 'react-i18next';
import {
    StyleSheet,
    View,
} from 'react-native';

import { KeyboardAwareScrollView } from '@pietile-native-kit/keyboard-aware-scrollview';
import { useRoute } from '@react-navigation/native';
import ButtonComponent from '@src/components/ButtonComponent';
import {
    ExpandableTextComponent,
    ExpandableTextInterface,
    OnChangeInterface,
    OnReadyInterface,
} from '@src/components/expandableText/ExpandableTextComponent';
import BackButton from '@src/components/header/BackButton';
import HeaderComponent from '@src/components/header/HeaderComponent';
import InputComponent from '@src/components/InputComponent';
import TextComponent from '@src/components/TextComponent';
import Toast from '@src/components/toast/Toast';
import TouchableComponent from '@src/components/TouchableComponent';
import { Colors } from '@src/configs';
import {
    MAX_PHONE_LENGTH,
    PHONE_MASK,
} from '@src/configs/constants';
import { useAppSelector } from '@src/hooks';
import useCallAPI from '@src/hooks/useCallAPI';
import useDimens, { DimensType } from '@src/hooks/useDimens';
import { TemplateJobRegisterScreenProps } from '@src/navigation/NavigationRouteProps';
import { registerJobService } from '@src/network/services/restaurantServices';
import useThemeColors from '@src/themes/useThemeColors';
import {
    removePhoneLeadingZero,
    validateEmail,
    validatePhone,
} from '@src/utils';
import formatWithMask from '@src/utils/maskFormat/formatWithMask';

import PhoneInputComponent from '../auth/register/components/PhoneInputComponent';

const TemplateJobRegisterScreen = () => {
    const Dimens = useDimens();
    const styles = stylesF(Dimens);

    const { themeColors } = useThemeColors();
    const { t } = useTranslation();

    const workspaceId = useAppSelector((state) => state.storageReducer.templateWorkspaceDetail?.id);

    const { params } = useRoute<TemplateJobRegisterScreenProps>();
    const { data } = params;
    const [collapsed, setCollapsed] = useState(true);

    const [nameError, setNameError] = useState(false);
    const [emailError, setEmailError] = useState(false);
    const [phoneError, setPhoneError] = useState(false);
    const [noteError, setNoteError] = useState(false);

    const [formData, setFormData] = useState({
        name: '',
        email: '',
        phone: '',
        note: '',
    });

    const controllerRef = useRef<ExpandableTextInterface>();

    const setController = useCallback((controller: ExpandableTextInterface) => {
        controllerRef.current = controller;
    }, []);

    const toggle = useCallback(() => {
        controllerRef.current?.toggle();
    }, []);

    const onChange = useCallback(({ isCollapsed }: OnChangeInterface | OnReadyInterface) => {
        setCollapsed(isCollapsed);
    }, []);

    const onReady = useCallback(({ isCollapsed }: OnChangeInterface | OnReadyInterface) => {
        setCollapsed(isCollapsed);
    }, []);

    const { callApi: registerJob, loading } = useCallAPI(
            registerJobService,
            undefined,
            useCallback((_data: any, message: string) => {
                Toast.showToast(message);
                setFormData({
                    name: '',
                    email: '',
                    phone: '',
                    note: '',
                });
            }, [])
    );

    const validateField = useCallback(() => {
        const { name, email, phone, note } = formData;

        if (!name.trim() || !phone.trim()  || phone === '+32' || phone === '+31' || !email.trim() || !note.trim() ) {
            Toast.showToast(t('message_input_all_field'));
            setNameError(!name.trim());
            setPhoneError(!phone.trim() || phone.trim() === '+32' || phone.trim() === '+31');
            setEmailError(!email.trim());
            setNoteError(!note.trim());

            return false;
        }

        if (!validateEmail(email)) {
            Toast.showToast(t('message_email_error'));
            setEmailError(true);
            return false;
        }

        if (!validatePhone(phone)) {
            Toast.showToast(t('message_phone_error'));
            setPhoneError(true);
            return false;
        }

        return true;

    }, [formData, t]);

    const register = useCallback(() => {
        setFormData({ ...formData, phone: removePhoneLeadingZero(formData.phone) });

        const allFieldsValid = validateField();

        if (allFieldsValid) {
            const { name, email, phone, note } = formData;

            registerJob({
                workspace_id: workspaceId,
                params: {
                    name: name,
                    email: email,
                    phone: removePhoneLeadingZero(phone),
                    content: note,
                }
            });
        }
    }, [formData, registerJob, validateField, workspaceId]);

    return (
        <View style={{ flex: 1 }}>
            <HeaderComponent >
                <View style={styles.header}>
                    <BackButton/>
                    <TextComponent style={styles.headerText}>
                        {t('template_jobs_label')}
                    </TextComponent>
                </View>
            </HeaderComponent>

            <KeyboardAwareScrollView
                showsVerticalScrollIndicator={false}
                contentContainerStyle={styles.scrollView}
            >
                <TextComponent style={styles.title}>
                    {data.title}
                </TextComponent>

                <ExpandableTextComponent
                    numberOfLines={6}
                    controller={setController}
                    onChange={onChange}
                    onReady={onReady}
                >
                    <TextComponent style={[styles.desc, { color: themeColors.color_common_description_text }]}>
                        {data.content}
                    </TextComponent>
                </ExpandableTextComponent>

                {collapsed ? (
                    <TouchableComponent
                        onPress={toggle}
                    >
                        <TextComponent style={[styles.showMore, { color: themeColors.color_primary }]}>
                            {t('template_job_show_more')}
                        </TextComponent>
                    </TouchableComponent>
                ) : null}

                <View style={styles.inputsContainer}>
                    <InputComponent
                        error={nameError}
                        containerStyle={styles.input}
                        placeholder={t('template_label_full_name')}
                        value={formData.name}
                        onBlur={() => {}}
                        onChangeText={(text) => {
                            setNameError(false);
                            setFormData({ ...formData, name: text });
                        }}
                    />

                    <InputComponent
                        error={emailError}
                        containerStyle={styles.input}
                        placeholder={t('hint_email')}
                        value={formData.email}
                        keyboardType="email-address"
                        autoCapitalize={'none'}
                        onBlur={() => {}}
                        onChangeText={(text) => {
                            setEmailError(false);
                            setFormData({ ...formData, email: text });
                        }}
                    />

                    <PhoneInputComponent
                        error={phoneError}
                        containerStyle={styles.input}
                        placeholder={t('template_label_gsm')}
                        value={formData.phone}
                        keyboardType="phone-pad"
                        autoCapitalize={'none'}
                        onBlur={() => {}}
                        onChangeText={(text) => {
                            setPhoneError(false);

                            const { masked, unmasked } = formatWithMask({
                                text: text,
                                mask: PHONE_MASK,
                            });

                            if (unmasked.length <= MAX_PHONE_LENGTH) {
                                setFormData({ ...formData, phone: masked });
                            }
                        }}
                    />

                    <InputComponent
                        multiline
                        error={noteError}
                        textAlignVertical={'top'}
                        containerStyle={styles.noteInputContainer}
                        style={styles.noteInput}
                        placeholder={t('hint_note')}
                        value={formData.note}
                        onBlur={() => {}}
                        onChangeText={(text) => {
                            setNoteError(false);
                            setFormData({ ...formData, note: text });
                        }}
                    />

                    <ButtonComponent
                        loading={loading}
                        title={t('template_jobs_submit')}
                        style={styles.button}
                        onPress={register}
                    />
                </View>
            </KeyboardAwareScrollView>
        </View>
    );
};

export default TemplateJobRegisterScreen;

const stylesF = (Dimens: DimensType) => StyleSheet.create({
    button: { marginTop: Dimens.H_24, marginHorizontal: Dimens.W_16 },
    noteInput: { marginTop: Dimens.H_16 },
    noteInputContainer: { marginTop: Dimens.H_12, height: Dimens.H_150 },
    inputsContainer: {
        marginTop: Dimens.H_10,
        paddingHorizontal: Dimens.W_20,
        paddingBottom: Dimens.H_100,
    },
    scrollView: {
        paddingBottom: Dimens.H_10,
        paddingTop: Dimens.H_38,
        paddingHorizontal: Dimens.W_14,
    },
    header: { flexDirection: 'row', alignItems: 'center' },
    headerText: {
        color: Colors.COLOR_WHITE,
        fontSize: Dimens.FONT_26,
        fontWeight: '700',
    },
    title: {
        fontSize: Dimens.FONT_24,
        fontWeight: '700',
        marginBottom: Dimens.H_16,
    },
    desc: {
        fontSize: Dimens.FONT_16,
        fontWeight: '400',
    },
    showMore: {
        fontSize: Dimens.FONT_16,
        fontWeight: '400',
        marginTop: Dimens.H_6,
    },

    input: {
        marginTop: Dimens.H_12,
    },
});