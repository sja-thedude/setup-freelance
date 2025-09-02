import React, {
    FC,
    useCallback,
    useState,
} from 'react';

import { useFormik } from 'formik';
import head from 'lodash/head';
import { useTranslation } from 'react-i18next';
/* eslint-disable arrow-parens */
/* eslint-disable @typescript-eslint/indent */
/* eslint-disable no-empty */
import {
    InteractionManager,
    ScrollView,
    StyleSheet,
    useWindowDimensions,
    View,
} from 'react-native';
import * as Yup from 'yup';

import { useKeyboard } from '@react-native-community/hooks';
import { useRoute } from '@react-navigation/native';
import ButtonComponent from '@src/components/ButtonComponent';
import InputComponent from '@src/components/InputComponent';
import TextComponent from '@src/components/TextComponent';
import Toast from '@src/components/toast/Toast';
import {
    MAX_PHONE_LENGTH,
    PHONE_MASK,
} from '@src/configs/constants';
import useDimens, { DimensType } from '@src/hooks/useDimens';
import { SelectOrderTypeScreenProps } from '@src/navigation/NavigationRouteProps';
import { createContactWorkspace } from '@src/network/services/productServices';
import PhoneInputComponent from '@src/screens/auth/register/components/PhoneInputComponent';
import useThemeColors from '@src/themes/useThemeColors';
import {
    removePhoneLeadingZero,
    validatePhone,
} from '@src/utils';
import formatWithMask from '@src/utils/maskFormat/formatWithMask';

import BaseDialog from './BaseDialog';

interface IProps {
    onClose?: () => void;
}

const ModalCreateGroup: FC<IProps> = ({ onClose }) => {
    const Dimens = useDimens();
    const styles = stylesF(Dimens);

    const { t } = useTranslation();
    const { themeColors } = useThemeColors();
    const [loading, setLoading] = useState<boolean>(false);
    const { keyboardShown } = useKeyboard();
    const { height } = useWindowDimensions();
    const { params } = useRoute<SelectOrderTypeScreenProps>();
    const { product } = params;

    const initialValues = {
        content: '',
        company_name: '',
        first_name: '',
        address: '',
        phone: '',
        last_name: '',
        email: '',
    };

    const validationSchema = Yup.object({
        first_name: Yup.string().required(
            t('message_error_fill_contact_order'),
        ),
        last_name: Yup.string().required(t('message_error_fill_contact_order')),
        email: Yup.string()
            .required(t('message_error_fill_contact_order'))
            .email(t('message_email_error')),
        phone: Yup.string()
            .required(t('message_error_fill_contact_order'))
            .test('phone_validate', t('message_phone_error'), function(phoneValue) {
                return validatePhone(phoneValue);
            })
    });

    const {
        submitForm,
        handleBlur,
        handleChange,
        errors,
        values,
        touched,
        validateForm,
    } = useFormik({
        initialValues,
        validationSchema,
        validateOnBlur: false,
        validateOnMount: false,
        onSubmit: async valuesForm => {
            try {
                setLoading(true);
                await createContactWorkspace(product?.workspace_id, { ...valuesForm, phone: removePhoneLeadingZero(valuesForm.phone) });
                !!onClose && onClose();
                InteractionManager.runAfterInteractions(() => {
                    Toast.showToast(t('message_contact_order_success'));
                });
            } catch (error) {
            } finally {
                setLoading(false);
            }
        },
    });

    const handleValid = useCallback(async () => {
        try {
            handleChange('phone')(removePhoneLeadingZero(values.phone));
            const response = await validateForm(values);
            const error = head(Object.values(response));
            submitForm();
            if (error) {
                Toast.showToast(error);
            }
        } catch (error) {}
    }, [handleChange, submitForm, validateForm, values]);

    return (
        <View
            style={{
                ...StyleSheet.absoluteFillObject,
                justifyContent: 'flex-end',
                zIndex: 1000,
            }}
        >
            <BaseDialog onSwipeHide={onClose}>
                <TextComponent
                    style={StyleSheet.flatten([
                        styles.textHeader,
                        { color: themeColors?.color_text },
                    ])}
                >
                    {t('text_title_sheet_contact_order')}
                </TextComponent>
                <TextComponent
                    style={StyleSheet.flatten([
                        styles.textDesc,
                        { color: themeColors?.color_common_subtext },
                    ])}
                >
                    {t('text_description_sheet_contact_order')}
                </TextComponent>

                <View
                    style={{
                        maxHeight: keyboardShown ? height / 2.5 : undefined,
                    }}
                >
                    <ScrollView showsVerticalScrollIndicator={false}>
                        <InputComponent
                            containerStyle={StyleSheet.flatten([
                                [styles.inputContainer, styles.mt30],
                            ])}
                            style={styles.input}
                            autoCapitalize={'none'}
                            placeholder={t('hint_first_name')}
                            borderInput={
                                themeColors.color_common_description_text
                            }
                            backgroundInput={'transparent'}
                            value={values?.first_name}
                            onChangeText={handleChange('first_name')}
                            onBlur={handleBlur('first_name')}
                            error={
                                !!touched.first_name && !!errors.first_name
                                    ? errors.first_name
                                    : ''
                            }
                        />

                        <InputComponent
                            containerStyle={StyleSheet.flatten([
                                [styles.inputContainer, styles.mt12],
                            ])}
                            style={styles.input}
                            autoCapitalize={'none'}
                            placeholder={t('hint_last_name')}
                            borderInput={
                                themeColors.color_common_description_text
                            }
                            backgroundInput={'transparent'}
                            value={values?.last_name}
                            onChangeText={handleChange('last_name')}
                            onBlur={handleBlur('last_name')}
                            error={
                                !!touched.last_name && !!errors.last_name
                                    ? errors.last_name
                                    : ''
                            }
                        />

                        <InputComponent
                            containerStyle={StyleSheet.flatten([
                                [styles.inputContainer, styles.mt12],
                            ])}
                            style={styles.input}
                            keyboardType="email-address"
                            autoCapitalize={'none'}
                            placeholder={t('hint_email')}
                            borderInput={
                                themeColors.color_common_description_text
                            }
                            backgroundInput={'transparent'}
                            value={values?.email}
                            onChangeText={handleChange('email')}
                            onBlur={handleBlur('email')}
                            error={
                                !!touched.email && !!errors.email
                                    ? errors.email
                                    : ''
                            }
                        />

                        <InputComponent
                            containerStyle={StyleSheet.flatten([
                                [styles.inputContainer, styles.mt12],
                            ])}
                            style={styles.input}
                            autoCapitalize={'none'}
                            placeholder={t('hint_company_name')}
                            borderInput={
                                themeColors.color_common_description_text
                            }
                            backgroundInput={'transparent'}
                            value={values?.company_name}
                            onChangeText={handleChange('company_name')}
                            onBlur={handleBlur('company_name')}
                            error={
                                !!touched.company_name && !!errors.company_name
                                    ? errors.company_name
                                    : ''
                            }
                        />

                        <PhoneInputComponent
                            alwaysShowBorder
                            containerStyle={StyleSheet.flatten([
                                [styles.inputContainer, styles.mt12],
                            ])}
                            style={styles.input}
                            keyboardType="number-pad"
                            autoCapitalize={'none'}
                            placeholder={t('hint_phone_number')}
                            borderInput={
                                themeColors.color_common_description_text
                            }
                            backgroundInput={'transparent'}
                            value={values?.phone}
                            onChangeText={useCallback((text) => {
                                const { masked, unmasked } = formatWithMask({
                                    text: text,
                                    mask: PHONE_MASK,
                                });

                                if (unmasked.length <= MAX_PHONE_LENGTH) {
                                    handleChange('phone')(masked);
                                }
                            }, [handleChange])}
                            onBlur={handleBlur('phone')}
                            error={
                                !!touched.phone && !!errors.phone
                                    ? errors.phone
                                    : ''
                            }
                        />

                        <InputComponent
                            containerStyle={StyleSheet.flatten([
                                [styles.inputContainer, styles.mt12],
                            ])}
                            style={styles.input}
                            autoCapitalize={'none'}
                            placeholder={t('hint_city')}
                            borderInput={
                                themeColors.color_common_description_text
                            }
                            backgroundInput={'transparent'}
                            value={values?.address}
                            onChangeText={handleChange('address')}
                            onBlur={handleBlur('address')}
                            error={
                                !!touched.address && !!errors.address
                                    ? errors.address
                                    : ''
                            }
                        />

                        <InputComponent
                            containerStyle={StyleSheet.flatten([
                                [styles.inputContainer, styles.mt12],
                            ])}
                            style={styles.input}
                            autoCapitalize={'none'}
                            placeholder={t('hint_note')}
                            borderInput={
                                themeColors.color_common_description_text
                            }
                            backgroundInput={'transparent'}
                            value={values?.content}
                            onChangeText={handleChange('content')}
                            onBlur={handleBlur('content')}
                            error={
                                !!touched.content && !!errors.content
                                    ? errors.content
                                    : ''
                            }
                        />
                    </ScrollView>
                </View>

                <View style={styles.viewFooter}>
                    <ButtonComponent
                        loading={loading}
                        style={{ minWidth: '50%', alignSelf: 'center' }}
                        title={t('text_to_send_message')}
                        onPress={handleValid}
                    />
                </View>
            </BaseDialog>
        </View>
    );
};

const stylesF = (Dimens: DimensType) => StyleSheet.create({
    textHeader: { fontSize: 18, fontWeight: '700' },
    textDesc: { fontSize: 16, fontWeight: '400', marginTop: Dimens.H_2 },
    input: { fontSize: Dimens.FONT_15, padding: 0 },
    inputContainer: { height: Dimens.W_46 },
    mt30: { marginTop: Dimens.H_30 },
    mt12: { marginTop: Dimens.H_12 },
    viewFooter: { alignItems: 'center', marginTop: Dimens.H_15 },
});

export default ModalCreateGroup;
