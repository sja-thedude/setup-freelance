'use client'

import style from 'public/assets/css/profile.module.scss'
import React, { useState, useEffect } from 'react';
import { useI18n } from '@/locales/client';
import Cookies from 'js-cookie';
import { useRouter } from "next/navigation";
import { useFormik } from 'formik';
import * as Yup from 'yup';
import 'react-toastify/dist/ReactToastify.css';
import { Button, Grid, TextField } from '@mui/material';
import { createTheme, ThemeProvider } from "@mui/material/styles";
import { InputAdornment } from '@mui/material';
import Select from '@mui/material/Select';
import MenuItem from '@mui/material/MenuItem';
import { api } from "@/utils/axios";
import { Modal } from "react-bootstrap";
import { gapi } from "gapi-script";
import GoogleLogin from "react-google-login";
import FacebookLogin from "react-facebook-login/dist/facebook-login-render-props";
import AppleLogin from 'react-apple-login';
import { REGEX_NUMBER_CHECK, VALIDATION_PHONE_MAX , VALIDATION_PHONE_MIN } from "@/config/constants";
import { useAppSelector } from '@/redux/hooks'
import { useGetWorkspaceDataByIdQuery } from '@/redux/services/workspace/workspaceDataApi'
import { handleLoginToken } from '@/utils/axiosRefreshToken';
import { TERMS_CONDITIONS_LINK, PRIVACY_POLICY_LINK } from '@/config/constants';
import useQueryEditProfileParam from '@/hooks/useQueryParam';
import { ORIGIN_NEXT } from '@/config/constants';

export default function Register({ togglePopup }: { togglePopup: any }) {
    const workspaceId = useAppSelector((state) => state.workspaceData.globalWorkspaceId)
    const { data: apiDataToken } = useGetWorkspaceDataByIdQuery({ id: workspaceId })
    const apiData = apiDataToken?.data?.setting_generals;
    const color = useAppSelector((state) => state.workspaceData.globalWorkspaceColor)
    const invalid = style['invalid'];
    const normaling = style['normaling'];
    const router = useRouter();
    const trans = useI18n();
    const language = Cookies.get('Next-Locale');
    const [show, setShow] = useState(false);
    const [isSuccess, setIsSuccess] = useState(false);
    const queryEditProfile = useQueryEditProfileParam();

    const handleClose = () => {
        togglePopup();
        setShow(false);
        const query = new URLSearchParams(window.location.search);
        if (queryEditProfile === true) {
            router.push(window.location.href.replace('&editProfile=true', ''));
            router.push(window.location.href.replace('?editProfile=true', ''));
        }
    };

    const handleShow = () => setShow(true);

    useEffect(() => {
        // const hasShownPopup = localStorage.getItem('hasShownPopup');
        const hasShownPopup = false;

        if (!hasShownPopup) {
            setShow(true);
            localStorage.setItem('hasShownPopup', 'true');
        }
    }, []);

    const validationSchema = Yup.object().shape({
        first_name: Yup.string().required(trans('required')),
        last_name: Yup.string().required(trans('required')),
        gsm: Yup.string().required(trans('required')),
        email: Yup.string().required(trans('required')).email(trans('lang_email_valid_message')),
        password: Yup.string().required(trans('required')),
        password_confirmation: Yup.string().required(trans('required')),
    });

    const [isVisible, setIsVisible] = useState(false);
    const [errorMessage, setErrorMessage] = useState<any | null>(null);
    const [apiErrors, setApiErrors] = useState<any | null>(null);

    const formik = useFormik({
        initialValues: {
            first_name: '',
            last_name: '',
            gsm: '',
            email: '',
            password: '',
            password_confirmation: '',
        },
        validationSchema,
        onSubmit: async (values) => {
            // Only subit when the term and condition is clicked
            if (values.password.length >= 6 && values.password_confirmation.length >= 6 && !isFirstIconVisible && values.password === values.password_confirmation) {
                try {
                    const headers = {
                        'Content-Language': language,
                    };
                    const response = await api.post('register', {
                        email: values.email,
                        first_name: values.first_name,
                        gsm: selectedCountry + values.gsm,
                        last_name: values.last_name,
                        password: values.password,
                        password_confirmation: values.password_confirmation,
                        origin: ORIGIN_NEXT
                        // add more data if needed
                    }, { headers });
                    if ('data' in response) {
                        setIsSuccess(true);
                    }
                } catch (error: any) {
                    setApiErrors(error.response.data.data);
                    setErrorMessage(error.response.data?.message);
                }
            }
        },
        // enableReinitialize: true,
        // initialTouched: {
        //     first_name:true,
        //     last_name: true,
        //     gsm: true,
        //     email: true,
        // },
        // validateOnMount: true,
    });

    // eye on password
    const [showPassword, setShowPassword] = useState(false);

    const handleClickShowPassword = () => {
        setShowPassword(!showPassword);
    };

    // eye on password
    const [showRepeatPassword, setShowRepeatPassword] = useState(false);
    const handleClickShowRepeatPassword = () => {
        setShowRepeatPassword(!showRepeatPassword);
    };

    const [selectedCountry, setSelectedCountry] = useState("+32"); // Initial value for the country select
    const [inputValue, setInputValue] = useState(""); // Store the input value
    const [isFirstNameValid, setIsFirstNameValid] = useState(true); // Store the input value
    const [isEmailValid, setIsEmailValid] = useState(true); // Store the input value
    const [isLastNameValid, setIsLastNameValid] = useState(true); // Store the input value
    const [isPasswordValid, setIsPasswordValid] = useState(true); // Store the input value
    const [isPasswordConfirmationValid, setIsPasswordConfirmationValid] = useState(true); // Store the input value
    const [isGsmValid, setIsGsmValid] = useState(true); // Store the input value


    const handleCountryChange = (event: any) => {
        let gsmValue = formik.values.gsm

        formik.setFieldValue('gsm', gsmValue);
        setSelectedCountry(event.target.value);
    };

    const handleInputChange = (event: any) => {
        let value = event.target.value;
        setInputValue(value);
        formik.handleChange(event);
        setIsEmailValid(true)
    };

    const handleGsmChange = (event: any) => {
        let newValue = event.target.value;
        // Remove characters that are not numbers
        const sanitizedValue = newValue.replace(/\D/g, '');
        newValue = sanitizedValue;

        if (parseInt(selectedCountry + newValue) >= VALIDATION_PHONE_MAX) {
            // Limit the phone number length
            newValue = newValue.substring(0, (VALIDATION_PHONE_MAX - selectedCountry.length));
        }

        if (apiErrors && apiErrors.gsm) {
            apiErrors.gsm = false;
        }
        // Update the value in the formik state
        setIsGsmValid(true);
        formik.setFieldValue('gsm', newValue);
    };

    const [isButtonClicked, setIsButtonClicked] = useState(false);

    // message error check
    const handleRegisterClick = () => {

        setIsPasswordValid(true);
        setIsPasswordConfirmationValid(true);
        if (isFirstIconVisible) {
            setIsButtonClicked(true);
            setIsVisible(true);
            setErrorMessage(trans('accpet-terms'));
        }

        if (checkEmailValid(formik.values.email)) {
        } else {
            setIsEmailValid(false);
            if (formik.values.email) {
                setErrorMessage(trans('format-email'));
            }
        }

        if (formik.values.password.length < 6) {
            setErrorMessage(trans('password-min-length'));
            setIsPasswordValid(false);
        }

        if (formik.values.password_confirmation.length < 6) {
            setErrorMessage(trans('password-min-length'));
            setIsPasswordConfirmationValid(false);
        }

        if (formik.values.password !== formik.values.password_confirmation) {
            setErrorMessage(trans('password-not-match'));
            setIsPasswordValid(false);
            setIsPasswordConfirmationValid(false);
        }

        if(formik.values.gsm.length < 9){
            setErrorMessage(trans('format-gsm'))
            setIsGsmValid(false);
        }

        if (formik.values.first_name === '' || formik.values.last_name === '' || formik.values.gsm === '' || formik.values.email === '' || formik.values.password === '' || formik.values.password_confirmation === '') {
            if (formik.values.first_name === '') {
                setIsFirstNameValid(false);
            }
            if (formik.values.last_name === '') {
                setIsLastNameValid(false);
            }
            if (formik.values.gsm === '') {
                setIsGsmValid(false);
            }
            if (formik.values.email === '') {
                setIsEmailValid(false);
            }
            if (formik.values.password === '') {
                setIsPasswordValid(false);
            }
            if (formik.values.password_confirmation === '') {
                setIsPasswordConfirmationValid(false);
            }

            setIsVisible(true);
            setErrorMessage(trans('missing-fields'));
        }

        if (formik.values.gsm.startsWith('0')) {
            formik.values.gsm = formik.values.gsm.substring(1);
            formik.setFieldValue('gsm', formik.values.gsm);
        }
    }
    // toggle icon check
    const [isFirstIconVisible, setIsFirstIconVisible] = useState(true);

    const toggleIcon = () => {
        setIsFirstIconVisible(!isFirstIconVisible);
    };

    function checkEmailValid(email: string) {
        // Sử dụng regex để kiểm tra định dạng email
        const emailRegex = /^[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,}$/i;
        return emailRegex.test(email);
    }

    // change default theme of formik
    const theme = createTheme({
        components: {
            // Inputs
            MuiOutlinedInput: {
                styleOverrides: {
                    root: {
                        // dont show increment or decrement buttons in number input
                        '& input::-webkit-outer-spin-button, & input::-webkit-inner-spin-button':
                        {
                            display: 'none',
                        },
                        '& input[type=number]': {
                            MozAppearance: 'textfield',
                        },
                        // remove outline
                        "& .MuiOutlinedInput-notchedOutline": {
                            borderRadius: '6px',
                            border: '1px solid var(--Cart-stroke, #D1D1D1)',
                            height: '42px',
                        },
                        "& .MuiSelect-icon": {
                            right: '0',
                        },
                        "& .MuiSelect-select": {
                            paddingRight: '22px!important',
                            paddingLeft: '5px!important',
                            "& .MuiOutlinedInput-notchedOutline": {
                                borderTopRightRadius: '0',
                                borderBottomRightRadius: '0',
                            }
                        },
                        "&.Mui-focused": {
                            "& .MuiOutlinedInput-notchedOutline": {
                                border: '1px solid var(--Cart-stroke, #D1D1D1)',
                            }
                        },
                        "& .MuiOutlinedInput-root": {
                            backgroundColor: '#e6e6e6',
                            position: 'absolute',
                            left: '0',
                            bottom: '0px',
                            height: '37px',
                            zIndex: '100',
                            borderTopRightRadius: '0',
                            borderBottomRightRadius: '0'
                        },
                        "& #gsm": {
                            marginLeft: '60px',
                        },
                        "& .MuiGrid-root": {
                            width: `100%`,
                        },
                        "& .MuiInputBase-input": {
                            padding: '7px 9px',
                        },
                        "& .MuiButtonBase-root": {
                            width: '100%!important',
                            padding: '0px',
                        }
                    },
                }
            },
        }
    });

    useEffect(() => {
        function start() {
            gapi.client.init({
                clientId: process.env.NEXT_PUBLIC_GOOGLE_CLIENT_ID,
                scope: 'email',
            });
        }
        gapi.load('client:auth2', start);

        const expires = new Date();
        expires.setMonth(expires.getMonth() + 1); // survive for 1 month
        Cookies.set('currentLinkLogin', window.location.href, { expires });
    }, []);

    const onSuccess = (response: any) => {
        responseLoginToken(response, 'google');
    };

    const responseApple = (response: any) => {
        responseLoginToken(response, 'apple');
    };

    const responseFacebook = (response: any) => {
        responseLoginToken(response, 'facebook');
    };

    const responseLoginToken = (response: any, provider: string) => {
        let data = api.post(`login/social`, {
            'provider': provider,
            'access_token': provider == "apple" ? response.authorization?.id_token : response?.accessToken,
            'workspace_id': workspaceId,
        }).then(res => {
            const userData = res.data.data;

            // Set cookie 'loggedToken' with value 'token'
            handleLoginToken(userData.token);

            if (userData?.first_login && (userData.first_name.includes('@') || REGEX_NUMBER_CHECK.test(userData.first_name) || !userData.gsm)) {
                const query = new URLSearchParams(window.location.search);
                if (query.size > 0) {
                    window.location.href = window.location.href + "&editProfile=true";
                } else {
                    window.location.href = window.location.href + "?editProfile=true";
                }
            } else {
                window.location.href = '/';
            }
        }).catch(err => {
            // console.log(err);
        });
    }
    const onFailure = (response: any) => {
        // console.log('FAILED', response);
    };

    const [open, setOpen] = useState(false);
    const flagDesktopChangeType = useAppSelector<any>((state: any) => state.flagDesktopChangeType.data);
    return (
        <>
            <Button onClick={handleShow} style={{ display: 'none' }}></Button>

            <Modal show={show} onHide={handleClose}
                animation={false}
                id='modal-profile'
            >
                {
                    isSuccess ? (
                        <Modal.Body>
                            <div className="close-popup text-828282" onClick={() => handleClose()}
                                style={workspaceId ? {} : {
                                    fontFamily: "SF Compact Display",
                                    fontSize: '16px',
                                    fontStyle: 'normal',
                                    fontWeight: '790',
                                    lineHeight: 'normal',
                                    letterSpacing: '1.44px',
                                    color: '#676767',
                                }}>
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="25" viewBox="0 0 24 25" fill="none">
                                    <path d="M14 17L10 12.5L14 8" stroke="#676767" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" />
                                </svg>
                                <div className="mt-1">{trans('back')}</div>
                            </div>
                            <div className="text-center" style={{ marginTop: '180px' }}>
                                <svg xmlns="http://www.w3.org/2000/svg" width="134" height="134" viewBox="0 0 134 134" fill="none">
                                    <path d="M122.833 11.1667L61.4167 72.5834" stroke={workspaceId ? (color ?? '#D87833') : ('#ABA765')} strokeWidth="4" strokeLinecap="round" strokeLinejoin="round" />
                                    <path d="M122.833 11.1667L83.7501 122.833L61.4167 72.5834L11.1667 50.2501L122.833 11.1667Z" stroke={workspaceId ? (color ?? '#D87833') : ('#ABA765')} strokeWidth="4" strokeLinecap="round" strokeLinejoin="round" />
                                </svg>

                                {workspaceId ?
                                    (
                                        <>
                                            <div className="text-center px-3 pt-4 pb-1 profile-title">{trans('email-confirmation')}</div>
                                            <div className="text-center px-3 profile-description text-828282">
                                                {trans('register-check')}
                                            </div>
                                            <div role="button" onClick={() => handleClose()} style={{ color: color ?? '#ABA765' }}
                                                className={`${style['footer-login-text-desk']} text-center pt-3 mt-2 px-3 text-uppercase`}>
                                                {trans('back-to-login')}
                                            </div>
                                        </>
                                    )
                                    :
                                    (
                                        <>
                                            <div className="res-mobile">
                                                <div className="text-center px-3 pt-4 pb-1 profile-title font-bold"
                                                    style={{
                                                        fontWeight: '790',
                                                        fontSize: '24px',
                                                        lineHeight: '28.64px',
                                                        color: '#404040'
                                                    }}>
                                                    {trans('email-confirmation')}</div>
                                                <div className="text-center px-3 profile-description text-828282"
                                                    style={{
                                                        fontFamily: "SF Compact Display Medium",
                                                        fontWeight: '556',
                                                        fontSize: '18px',
                                                        lineHeight: '25.35px',
                                                        color: '#828282'
                                                    }}>
                                                    {trans('register-check')}
                                                </div>
                                                <div role="button" onClick={() => handleClose()}
                                                    style={{
                                                        color: color ?? '#ABA765',
                                                        fontWeight: '790',
                                                        lineHeight: '19.09px',
                                                        fontSize: '16px',
                                                        letterSpacing: '2px'
                                                    }}
                                                    className={`${style['footer-login-text-desk']} font-bold text-center pt-3 mt-2 px-3 text-uppercase`}>
                                                    {trans('back-to-login')}
                                                </div>
                                            </div>
                                            <div className="res-desktop">
                                                <div className="text-center px-3 pt-4 pb-1 profile-title"
                                                    style={{
                                                        fontFamily: "SF Compact Display",
                                                        fontWeight: '790',
                                                        fontSize: '24px',
                                                        lineHeight: '28.64px',
                                                        color: '#404040'
                                                    }}>
                                                    {trans('email-confirmation')}</div>
                                                <div className="text-center px-3 profile-description text-828282"
                                                    style={{
                                                        fontFamily: "SF Compact Display",
                                                        fontWeight: '556',
                                                        fontSize: '18px',
                                                        lineHeight: '25.35px',
                                                        color: '#828282'
                                                    }}>
                                                    {trans('register-check')}
                                                </div>
                                                <div role="button" onClick={() => handleClose()}
                                                    style={{
                                                        color: color ?? '#ABA765',
                                                        fontFamily: "SF Compact Display",
                                                        fontWeight: '790',
                                                        lineHeight: '19.09px',
                                                        fontSize: '16px',
                                                        letterSpacing: '2px'
                                                    }}
                                                    className={`${style['footer-login-text-desk']} text-center pt-3 mt-2 px-3 text-uppercase`}>
                                                    {trans('back-to-login')}
                                                </div>
                                            </div>
                                        </>
                                    )
                                }
                            </div>
                        </Modal.Body>
                    ) : (
                        <Modal.Body>
                            <div className={`close-popup text-828282`} onClick={() => handleClose()}
                                style={workspaceId ? {
                                    marginTop: flagDesktopChangeType ? '55px' : ''
                                } : {
                                    fontFamily: "SF Compact Display",
                                    fontSize: '16px',
                                    fontStyle: 'normal',
                                    fontWeight: '790',
                                    lineHeight: 'normal',
                                    letterSpacing: '1.44px'
                                }}>
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="25" viewBox="0 0 24 25" fill="none">
                                    <path d="M14 17L10 12.5L14 8" stroke="#676767" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" />
                                </svg>
                                <div className={`mt-1`}>{trans('back')}</div>
                            </div>
                            {errorMessage && (
                                <div className={`px-3 pb-1 my-3`}>
                                    <div className={`${style['error-message']}`}>
                                        <svg className="me-2" xmlns="http://www.w3.org/2000/svg" width="21" height="21" viewBox="0 0 21 21" fill="none">
                                            <path d="M9.00368 3.37756L1.59243 15.7501C1.43962 16.0147 1.35877 16.3147 1.35792 16.6203C1.35706 16.9258 1.43623 17.2263 1.58755 17.4918C1.73887 17.7572 1.95706 17.9785 2.22042 18.1334C2.48378 18.2884 2.78313 18.3717 3.08868 18.3751H17.9112C18.2167 18.3717 18.5161 18.2884 18.7794 18.1334C19.0428 17.9785 19.261 17.7572 19.4123 17.4918C19.5636 17.2263 19.6428 16.9258 19.6419 16.6203C19.6411 16.3147 19.5602 16.0147 19.4074 15.7501L11.9962 3.37756C11.8402 3.1204 11.6206 2.90779 11.3585 2.76023C11.0964 2.61267 10.8007 2.53516 10.4999 2.53516C10.1992 2.53516 9.90347 2.61267 9.64138 2.76023C9.3793 2.90779 9.15966 3.1204 9.00368 3.37756V3.37756Z" stroke="#E03009" strokeWidth="1.5" strokeLinecap="round" strokeLinejoin="round" />
                                            <path d="M10.5 7.875V11.375" stroke="#E03009" strokeWidth="1.5" strokeLinecap="round" strokeLinejoin="round" />
                                            <path d="M10.5 14.875H10.5088" stroke="#E03009" strokeWidth="1.5" strokeLinecap="round" strokeLinejoin="round" />
                                        </svg>
                                        {errorMessage}
                                    </div>
                                </div>
                            )}

                            {
                                workspaceId ?
                                    (
                                        <>
                                            <div className="text-center p-3 profile-title">{trans('register-account')}</div>
                                            <div className="text-center text-828282 px-3 profile-description">
                                                {trans('create-account-1')}
                                                <span style={{ color: color ?? '#ABA765' }} onClick={() => { window.open('https://b2b.itsready.be/', '_blank') }} role='button'>{trans('create-account-2')}</span>
                                                {trans('create-account-3')}
                                            </div>
                                        </>
                                    )
                                    :
                                    (
                                        <>
                                            <div className="res-mobile">
                                                <div className="text-center p-3 profile-title font-bold" style={{fontSize: "24px", fontWeight: "790", color: "#404040" }}>{trans('register-account')}</div>
                                                <div className="text-center px-3 profile-description" style={{ fontFamily: "SF Compact Display Medium", fontSize: "18px", fontWeight: "556", color: "#676767" }}>
                                                    {trans('create-account-1')}
                                                    <span style={{ color: color ?? '#ABA765' }} onClick={() => { window.open('https://b2b.itsready.be/', '_blank') }} role='button'>{trans('create-account-2')}</span>
                                                    {trans('create-account-3')}
                                                </div>
                                            </div>
                                            <div className="res-desktop">
                                                <div className="text-center p-3 profile-title" style={{ fontFamily: "SF Compact Display", fontSize: "24px", fontWeight: "790", color: "#404040" }}>{trans('register-account')}</div>
                                                <div className="text-center px-3 profile-description" style={{ fontFamily: "SF Compact Display", fontSize: "18px", fontWeight: "556", color: "#676767" }}>
                                                    {trans('create-account-1')}
                                                    <span style={{ color: color ?? '#ABA765' }} onClick={() => { window.open('https://b2b.itsready.be/', '_blank') }} role='button'>{trans('create-account-2')}</span>
                                                    {trans('create-account-3')}
                                                </div>
                                            </div>

                                        </>
                                    )
                            }
                            <div className="res-mobile">
                                <div className={`${style['menu-profile']} mb-0`} style={{ padding: '15px' }}>
                                    <div className={style['detail-profile']}>
                                        <ThemeProvider theme={theme}>
                                            <form onSubmit={formik.handleSubmit} method={'POST'}>
                                                <Grid container spacing={2} style={{ justifyContent: 'center' }}>
                                                    <Grid item xs={12} sm={12}>
                                                        <TextField
                                                            className={`${style.texting} ${formik.touched.first_name && (Boolean(formik.errors.first_name) || (apiErrors && apiErrors.first_name)) ? invalid : ''}`}
                                                            fullWidth
                                                            id="first_name"
                                                            name="first_name"
                                                            placeholder={trans('first-name')}
                                                            variant="outlined"
                                                            value={formik.values.first_name}
                                                            onChange={formik.handleChange}
                                                            onBlur={formik.handleBlur}
                                                            error={formik.touched.first_name && Boolean(formik.errors.first_name || (apiErrors && apiErrors?.first_name))}
                                                            style={workspaceId ? {} : { border: '1px solid #CDCDCD !important', borderRadius: '6px !important' }}
                                                        />
                                                    </Grid>
                                                    <Grid item xs={12} sm={12}>
                                                        <TextField
                                                            className={`${style.texting} ${formik.touched.last_name && (Boolean(formik.errors.last_name) || (apiErrors && apiErrors.last_name)) ? invalid : ''}`}
                                                            fullWidth
                                                            id="last_name"
                                                            name="last_name"
                                                            placeholder={trans('last-name')}
                                                            variant="outlined"
                                                            value={formik.values.last_name}
                                                            onChange={formik.handleChange}
                                                            onBlur={formik.handleBlur}
                                                            error={formik.touched.last_name && Boolean(formik.errors.last_name || (apiErrors && apiErrors?.last_name))}
                                                            style={workspaceId ? {} : { border: '1px solid #CDCDCD !important', borderRadius: '6px !important' }}
                                                        />
                                                    </Grid>

                                                    <Grid item xs={12}>
                                                        <TextField
                                                            type="text"
                                                            className={`${style.texting} ${formik.touched.gsm && (Boolean(formik.errors.gsm) || (apiErrors && apiErrors.gsm)) || !isGsmValid ? invalid : ''}`}
                                                            fullWidth
                                                            id="gsm"
                                                            name="gsm"
                                                            placeholder={trans('mobile')}
                                                            variant="outlined"
                                                            value={formik.values.gsm}
                                                            onChange={handleGsmChange}
                                                            onBlur={formik.handleBlur}
                                                            error={formik.touched.gsm && Boolean(formik.errors.gsm || (apiErrors && apiErrors.gsm))}
                                                            style={workspaceId ? {} : { border: '1px solid #CDCDCD !important', borderRadius: '6px !important' }}
                                                            InputProps={{
                                                                style: { color: !isGsmValid || (apiErrors && apiErrors.isGsmValid) ? '#D94B2C' : '#413E38' ,paddingRight: '0'},
                                                                startAdornment: (
                                                                    <InputAdornment position="start">
                                                                        <Select
                                                                            open={open}
                                                                            onOpen={(e) => {
                                                                                e.preventDefault();
                                                                                setTimeout(() => {
                                                                                    (document.activeElement as HTMLElement).blur();
                                                                                    setOpen(true);
                                                                                }, 0);
                                                                            }}
                                                                            onClose={() => setOpen(false)}
                                                                            value={selectedCountry == "+31" ? "+31" : "+32"}
                                                                            onChange={handleCountryChange}
                                                                        >
                                                                            <MenuItem className={`${style.customMenuItem}`} value="+32">
                                                                                <div className='d-flex ps-2'><svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" shapeRendering="geometricPrecision" textRendering="geometricPrecision" imageRendering="optimizeQuality" fillRule="evenodd" clipRule="evenodd" viewBox="0 0 203.55 141.6"><g fillRule="nonzero"><path fill="#ED2939" d="M203.55 11.19v119.22c0 6.16-5.04 11.19-11.19 11.19H11.19C5.05 141.6.02 136.59 0 130.45V11.15C.02 5.01 5.05 0 11.19 0h181.17c6.15 0 11.19 5.03 11.19 11.19z" /><path fill="#FAE042" d="M135.7 0v141.6H11.19C5.05 141.6.02 136.59 0 130.45V11.15C.02 5.01 5.05 0 11.19 0H135.7z" /><path d="M67.85 0v141.6H11.19C5.05 141.6.02 136.59 0 130.45V11.15C.02 5.01 5.05 0 11.19 0h56.66z" /></g></svg>
                                                                                    <div className={`${style.country}`}>+32</div></div>
                                                                            </MenuItem>
                                                                            <MenuItem className={`${style.customMenuItem} px-1`} value="+31">
                                                                                <div className='d-flex ps-2'>
                                                                                    <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" shapeRendering="geometricPrecision" textRendering="geometricPrecision" imageRendering="optimizeQuality" fillRule="evenodd" clipRule="evenodd" viewBox="0 0 43.06 29.96"><g fillRule="nonzero"><path fill="#21468B" d="M43.06 20v7.59c0 1.3-1.06 2.37-2.37 2.37H2.37C1.06 29.96 0 28.89 0 27.59V20h43.06z" /><path fill="#fff" d="M43.06 20H0V2.37C0 1.06 1.06 0 2.37 0h38.32c1.31 0 2.37 1.06 2.37 2.37V20z" /><path fill="#AE1C28" d="M43.06 9.96H0V2.37C0 1.06 1.06 0 2.37 0h38.32c1.31 0 2.37 1.06 2.37 2.37v7.59z" /></g></svg>
                                                                                    <div className={`${style.country}`}>+31</div></div>
                                                                            </MenuItem>
                                                                        </Select>

                                                                    </InputAdornment>
                                                                ),
                                                            }}
                                                        />
                                                    </Grid>

                                                    <Grid item xs={12}>
                                                        <TextField
                                                            className={`${style.texting} ${formik.touched.email && (Boolean(formik.errors.email) || (apiErrors && apiErrors.email)) && !isEmailValid ? invalid : ''}`}
                                                            fullWidth
                                                            id="email"
                                                            name="email"
                                                            placeholder={trans('email')}
                                                            variant="outlined"
                                                            value={formik.values.email}
                                                            onChange={() => { handleInputChange(event); apiErrors && apiErrors.email ? apiErrors.email = null : '' }}
                                                            onBlur={formik.handleBlur}
                                                            error={formik.touched.email && Boolean(formik.errors.email || (apiErrors && apiErrors?.email)) && !isEmailValid}
                                                            style={workspaceId ? {} : { border: '1px solid #CDCDCD !important', borderRadius: '6px !important' }}
                                                        />
                                                    </Grid>

                                                    <Grid item xs={12}>
                                                        <TextField
                                                            className={`${style.texting} ${formik.touched.password && Boolean(formik.errors.password || (apiErrors && apiErrors.password)) || !isPasswordValid ? invalid : ''}`}
                                                            fullWidth
                                                            id="password"
                                                            name="password"
                                                            type={showPassword ? "text" : "password"}
                                                            placeholder={trans('password')}
                                                            variant="outlined"
                                                            value={formik.values.password}
                                                            onChange={formik.handleChange}
                                                            onBlur={formik.handleBlur}
                                                            error={formik.touched.password && Boolean(formik.errors.password || (apiErrors && apiErrors.password))}
                                                            style={workspaceId ? { backgroundColor: '#FFFFFF' } : { border: '1px solid #CDCDCD !important', borderRadius: '6px !important', backgroundColor: '#FFFFFF' }}
                                                            onKeyUp={() => { setIsPasswordValid(true) }}
                                                            InputProps={{
                                                                style: { color: !isPasswordValid || (apiErrors && apiErrors.isPasswordValid) ? '#D94B2C' : '#413E38' },
                                                                endAdornment: (
                                                                    <InputAdornment position="end">
                                                                        {
                                                                            showPassword
                                                                                ? <svg onClick={handleClickShowPassword} xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                                                                    <path d="M1 12C1 12 5 4 12 4C19 4 23 12 23 12C23 12 19 20 12 20C5 20 1 12 1 12Z" stroke={formik.touched.password && Boolean(formik.errors.password || (apiErrors && apiErrors.password)) || !isPasswordValid ? "#E03009" : "#888888"} strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" />
                                                                                    <path d="M12 15C13.6569 15 15 13.6569 15 12C15 10.3431 13.6569 9 12 9C10.3431 9 9 10.3431 9 12C9 13.6569 10.3431 15 12 15Z" stroke={formik.touched.password && Boolean(formik.errors.password || (apiErrors && apiErrors.password)) || !isPasswordValid ? "#E03009" : "#888888"} strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" />
                                                                                    <line x1={5.378} y1={1.318} x2={19.318} y2={23.622} stroke={formik.touched.password && Boolean(formik.errors.password || (apiErrors && apiErrors.password)) || !isPasswordValid ? "#E03009" : "#888888"} strokeWidth={2} strokeLinecap='round' strokeLinejoin='round' />
                                                                                </svg>
                                                                                : <svg onClick={handleClickShowPassword} xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                                                                    <path d="M1 12C1 12 5 4 12 4C19 4 23 12 23 12C23 12 19 20 12 20C5 20 1 12 1 12Z" stroke={formik.touched.password && Boolean(formik.errors.password || (apiErrors && apiErrors.password)) || !isPasswordValid ? "#E03009" : "#888888"} strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" />
                                                                                    <path d="M12 15C13.6569 15 15 13.6569 15 12C15 10.3431 13.6569 9 12 9C10.3431 9 9 10.3431 9 12C9 13.6569 10.3431 15 12 15Z" stroke={formik.touched.password && Boolean(formik.errors.password || (apiErrors && apiErrors.password)) || !isPasswordValid ? "#E03009" : "#888888"} strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" />
                                                                                </svg>
                                                                        }

                                                                    </InputAdornment>
                                                                )
                                                            }}
                                                        />
                                                    </Grid>
                                                    <Grid item xs={12}>
                                                        <TextField
                                                            className={`${style.texting} ${!isPasswordConfirmationValid || (formik.touched.password_confirmation
                                                                && Boolean(formik.errors.password_confirmation || (apiErrors && apiErrors.password_confirmation)))
                                                                ? invalid : ''}`}
                                                            fullWidth
                                                            id="password_confirmation"
                                                            name="password_confirmation"
                                                            style={workspaceId ? { backgroundColor: '#FFFFFF' } : { border: '1px solid #CDCDCD !important', borderRadius: '6px !important', backgroundColor: '#FFFFFF' }}
                                                            type={showRepeatPassword ? "text" : "password"}
                                                            placeholder={trans('password-confirm')}
                                                            variant="outlined"
                                                            value={formik.values.password_confirmation}
                                                            onChange={formik.handleChange}
                                                            onBlur={formik.handleBlur}
                                                            error={formik.touched.password_confirmation && Boolean(formik.errors.password_confirmation || (apiErrors && apiErrors.password_confirmation))}
                                                            helperText={""}
                                                            onKeyUp={() => { setIsPasswordConfirmationValid(true) }}
                                                            InputProps={{
                                                                style: { color: !isPasswordConfirmationValid || (apiErrors && apiErrors.isPasswordConfirmationValid) ? '#E03009' : '#413E38' },
                                                                endAdornment: (
                                                                    <InputAdornment position="end">
                                                                        {showRepeatPassword
                                                                            ? <svg onClick={handleClickShowRepeatPassword} xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                                                                <path d="M1 12C1 12 5 4 12 4C19 4 23 12 23 12C23 12 19 20 12 20C5 20 1 12 1 12Z" stroke={!isPasswordConfirmationValid || (formik.touched.password_confirmation && Boolean(formik.errors.password_confirmation || (apiErrors && apiErrors.password_confirmation))) ? "#E03009" : "#888888"} strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" />
                                                                                <path d="M12 15C13.6569 15 15 13.6569 15 12C15 10.3431 13.6569 9 12 9C10.3431 9 9 10.3431 9 12C9 13.6569 10.3431 15 12 15Z" stroke={!isPasswordConfirmationValid || (formik.touched.password_confirmation && Boolean(formik.errors.password_confirmation || (apiErrors && apiErrors.password_confirmation))) ? "#E03009" : "#888888"} strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" />
                                                                                <line x1={5.378} y1={1.318} x2={19.318} y2={23.622} stroke={!isPasswordConfirmationValid || (formik.touched.password_confirmation && Boolean(formik.errors.password_confirmation || (apiErrors && apiErrors.password_confirmation))) ? "#E03009" : "#888888"} strokeWidth={2} strokeLinecap='round' strokeLinejoin='round' />
                                                                            </svg>
                                                                            : <svg onClick={handleClickShowRepeatPassword} xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                                                                <path d="M1 12C1 12 5 4 12 4C19 4 23 12 23 12C23 12 19 20 12 20C5 20 1 12 1 12Z" stroke={!isPasswordConfirmationValid || (formik.touched.password_confirmation && Boolean(formik.errors.password_confirmation || (apiErrors && apiErrors.password_confirmation))) ? "#E03009" : "#888888"} strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" />
                                                                                <path d="M12 15C13.6569 15 15 13.6569 15 12C15 10.3431 13.6569 9 12 9C10.3431 9 9 10.3431 9 12C9 13.6569 10.3431 15 12 15Z" stroke={!isPasswordConfirmationValid || (formik.touched.password_confirmation && Boolean(formik.errors.password_confirmation || (apiErrors && apiErrors.password_confirmation))) ? "#E03009" : "#888888"} strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" />
                                                                            </svg>
                                                                        }
                                                                    </InputAdornment>
                                                                )
                                                            }}
                                                        />
                                                    </Grid>
                                                    <div className="ms-3" style={{ marginTop: '40px' }}>
                                                        <div className='d-flex align-items-center '>
                                                            <div className="me-2" onClick={toggleIcon}>
                                                                {isFirstIconVisible ? (
                                                                    <svg width="25" height="25" viewBox="0 0 25 25" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                        <circle cx="12.5" cy="12.5" r="11.5" stroke={isButtonClicked ? 'red' : (color ?? '#ABA765')} strokeWidth="2" />
                                                                    </svg>
                                                                ) : (
                                                                    <svg width="25" height="25" viewBox="0 0 25 25" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                        <circle cx="12.5" cy="12.5" r="11.5" fill={(color ?? '#ABA765')} stroke={(color ?? '#ABA765')} strokeWidth="2" />
                                                                        <path d="M19.5 7.25L9.875 16.875L5.5 12.5" stroke="white" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" />
                                                                    </svg>
                                                                )}
                                                            </div>
                                                            <div className={`${style['term-register']}`}
                                                                style={{
                                                                    fontFamily: 'SF Compact Display',
                                                                    fontSize: '14px',
                                                                    lineHeight: '17px',
                                                                    color: '#4F4F4F',
                                                                    fontWeight: '457',
                                                                }}>
                                                                <span>{trans('agree') + " "}</span>
                                                                <span role="button" onClick={() => { window.open(TERMS_CONDITIONS_LINK, "_blank") }} className={style['underline-register']}>{trans('term-condition') + " "}</span>
                                                                <span>{trans('and') + " "}</span>
                                                                <span role="button" onClick={() => { window.open(PRIVACY_POLICY_LINK, "_blank") }} className={style['underline-register']}>{trans('privacy-policy')}</span>.
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <Grid item xs={12} className={`d-flex justify-content-center`} style={{ margin: 'auto', marginTop: '8px' }}>
                                                        {
                                                            workspaceId ?
                                                                (
                                                                    <>
                                                                      <Button type="submit" onClick={handleRegisterClick}>
                                                                                <div style={{ background: color }}
                                                                                    className={`${style['save-button']}}`}>{trans('register-btn')}</div>
                                                                            </Button>
                                                                    </>
                                                                ) : (
                                                                    <>
                                                                 
                                                                            <Button type="submit" onClick={handleRegisterClick}>
                                                                                <div style={{
                                                                                    fontFamily: 'SF Compact Display',
                                                                                    backgroundColor: '#ABA765',
                                                                                    borderRadius: '80px',
                                                                                    margin: 'auto',
                                                                                    width: '153px',
                                                                                    textTransform: 'none'
                                                                                }}
                                                                                    className={`${style['save-button']} ${Object.keys(formik.errors).length != 0 ? `${style['btn-disable']}` : ``}`}>{trans('register-btn')}</div>
                                                                            </Button>
                                           
                                                                    </>
                                                                )
                                                        }
                                                    </Grid>
                                                </Grid>
                                            </form>
                                            <div style={{ position: "relative", marginTop: '25px' }}>
                                                <div style={workspaceId ? {} : { border: '1px solid #CDCDCD' }} className={`${style['line-break-register']}`}>
                                                </div>
                                                <div className={`${style['text-break-register']}`}
                                                    style={{
                                                        fontFamily: 'SF Compact Display',
                                                        color: '#4F4F4F',
                                                        fontSize: '14px',
                                                        lineHeight: '17px',
                                                    }}>
                                                    {trans('other-register')}
                                                </div>
                                            </div>

                                            <div className={`${style['social-register']}`}>
                                                {(apiDataToken?.data?.facebook_enabled > 0 || !workspaceId) && (
                                                    <FacebookLogin
                                                        appId={process.env.NEXT_PUBLIC_FACEBOOK_APP_ID}
                                                        callback={responseFacebook}
                                                        isMobile={false}
                                                        render={(renderProps: any) => (
                                                            <div onClick={renderProps.onClick}
                                                                style={{ width: "fit-content" }}
                                                                className={`${style['social-login-btn']}`}>
                                                                <svg xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" width="50" height="50" viewBox="0 0 48 48">
                                                                    <linearGradient id="Ld6sqrtcxMyckEl6xeDdMa_uLWV5A9vXIPu_gr1" x1="9.993" x2="40.615" y1="9.993" y2="40.615" gradientUnits="userSpaceOnUse">
                                                                        <stop offset="0" stopColor="#2aa4f4"></stop>
                                                                        <stop offset="1" stopColor="#007ad9"></stop>
                                                                    </linearGradient>
                                                                    <path fill="url(#Ld6sqrtcxMyckEl6xeDdMa_uLWV5A9vXIPu_gr1)" d="M24,4C12.954,4,4,12.954,4,24s8.954,20,20,20s20-8.954,20-20S35.046,4,24,4z"></path>
                                                                    <path fill="#fff" d="M26.707,29.301h5.176l0.813-5.258h-5.989v-2.874c0-2.184,0.714-4.121,2.757-4.121h3.283V12.46 c-0.577-0.078-1.797-0.248-4.102-0.248c-4.814,0-7.636,2.542-7.636,8.334v3.498H16.06v5.258h4.948v14.452 C21.988,43.9,22.981,44,24,44c0.921,0,1.82-0.084,2.707-0.204V29.301z"></path>
                                                                </svg>
                                                            </div>
                                                        )}
                                                    />)}
                                                {(apiDataToken?.data?.google_enabled > 0 || !workspaceId) && (
                                                    <GoogleLogin
                                                        clientId={process.env.NEXT_PUBLIC_GOOGLE_CLIENT_ID ?? ''}
                                                        render={renderProps => (
                                                            <div onClick={renderProps.onClick}
                                                                style={{ width: "fit-content", padding: '0 34px' }}
                                                                className={`${style['social-login-btn']}`}>
                                                                <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 40 40" fill="none">
                                                                    <g clipPath="url(#clip0_5742_1363)">
                                                                        <path d="M39.575 20.4501C39.575 19.1334 39.4583 17.8834 39.2583 16.6667H20.425V24.1834H31.2083C30.725 26.6501 29.3083 28.7334 27.2083 30.1501V35.1501H33.6417C37.4083 31.6667 39.575 26.5334 39.575 20.4501Z" fill="#4285F4" />
                                                                        <path d="M20.425 40C25.825 40 30.3417 38.2 33.6417 35.15L27.2083 30.15C25.4083 31.35 23.125 32.0833 20.425 32.0833C15.2083 32.0833 10.7917 28.5666 9.20832 23.8167H2.57498V28.9667C5.85832 35.5 12.6083 40 20.425 40Z" fill="#34A853" />
                                                                        <path d="M9.20832 23.8168C8.79165 22.6168 8.57499 21.3334 8.57499 20.0001C8.57499 18.6668 8.80832 17.3834 9.20832 16.1834V11.0334H2.57499C1.20832 13.7334 0.424988 16.7668 0.424988 20.0001C0.424988 23.2334 1.20832 26.2668 2.57499 28.9668L9.20832 23.8168Z" fill="#FBBC05" />
                                                                        <path d="M20.425 7.91667C23.375 7.91667 26.0083 8.93334 28.0916 10.9167L33.7916 5.21667C30.3416 1.98334 25.825 0 20.425 0C12.6083 0 5.85831 4.5 2.57498 11.0333L9.20832 16.1833C10.7916 11.4333 15.2083 7.91667 20.425 7.91667Z" fill="#EA4335" />
                                                                    </g>
                                                                    <defs>
                                                                        <clipPath id="clip0_5742_1363">
                                                                            <rect width="40" height="40" fill="white" />
                                                                        </clipPath>
                                                                    </defs>
                                                                </svg>
                                                            </div>
                                                        )}
                                                        buttonText="Login"
                                                        onSuccess={onSuccess}
                                                        onFailure={onFailure}
                                                        cookiePolicy={'single_host_origin'}
                                                    />)}

                                                {(apiDataToken?.data?.apple_enabled > 0 || !workspaceId) && (
                                                    <AppleLogin
                                                        clientId={process.env.NEXT_PUBLIC_APPLE_CLIENT_ID ?? ''}
                                                        redirectURI={window.location.origin}
                                                        responseType="id_token code"
                                                        responseMode="fragment"
                                                        usePopup={true}
                                                        // scope="name email"
                                                        callback={responseApple}
                                                        render={renderProps => (
                                                            <div onClick={renderProps.onClick}
                                                                style={{ width: "fit-content" }}
                                                                className={`${style['social-login-btn']}`}>
                                                                <svg xmlns="http://www.w3.org/2000/svg" width="50" height="41" viewBox="0 0 33 41" fill="none">
                                                                    <g clipPath="url(#clip0_5618_3513)">
                                                                        <path d="M31.524 13.8801C31.292 14.0601 27.196 16.3681 27.196 21.5001C27.196 27.4361 32.408 29.5361 32.564 29.5881C32.54 29.7161 31.736 32.4641 29.816 35.2641C28.104 37.7281 26.316 40.1881 23.596 40.1881C20.876 40.1881 20.176 38.6081 17.036 38.6081C13.976 38.6081 12.888 40.2401 10.4 40.2401C7.912 40.2401 6.176 37.9601 4.18 35.1601C1.868 31.8721 0 26.7641 0 21.9161C0 14.1401 5.056 10.0161 10.032 10.0161C12.676 10.0161 14.88 11.7521 16.54 11.7521C18.12 11.7521 20.584 9.91214 23.592 9.91214C24.732 9.91214 28.828 10.0161 31.524 13.8801ZM22.164 6.62014C23.408 5.14414 24.288 3.09614 24.288 1.04814C24.288 0.764141 24.264 0.476141 24.212 0.244141C22.188 0.320141 19.78 1.59214 18.328 3.27614C17.188 4.57214 16.124 6.62014 16.124 8.69614C16.124 9.00814 16.176 9.32014 16.2 9.42014C16.328 9.44414 16.536 9.47214 16.744 9.47214C18.56 9.47214 20.844 8.25614 22.164 6.62014Z" fill="black" />
                                                                    </g>
                                                                    <defs>
                                                                        <clipPath id="clip0_5618_3513">
                                                                            <rect width="32.56" height="40" fill="white" transform="translate(0 0.244141)" />
                                                                        </clipPath>
                                                                    </defs>
                                                                </svg>
                                                            </div>
                                                        )}
                                                    />)}
                                            </div>
                                        </ThemeProvider>
                                    </div>
                                </div>
                            </div>
                            <div className="res-desktop">
                                <div className={`${style['menu-profile']} mb-0`}>
                                    <div className={style['detail-profile']}>
                                        <ThemeProvider theme={theme}>
                                            <form onSubmit={formik.handleSubmit} method={'POST'}>
                                                <Grid container spacing={2} style={{ justifyContent: 'center' }}>
                                                    <Grid item xs={12} sm={12}>
                                                        <TextField
                                                            className={`${style.texting} ${formik.touched.first_name && (Boolean(formik.errors.first_name) || (apiErrors && apiErrors.first_name)) ? invalid : normaling}`}
                                                            fullWidth
                                                            id="first_name"
                                                            name="first_name"
                                                            placeholder={trans('first-name')}
                                                            variant="outlined"
                                                            value={formik.values.first_name}
                                                            onChange={formik.handleChange}
                                                            onBlur={formik.handleBlur}
                                                            error={formik.touched.first_name && Boolean(formik.errors.first_name || (apiErrors && apiErrors?.first_name))}
                                                            style={workspaceId ? {} : { border: '1px solid #CDCDCD !important', borderRadius: '6px !important' }}
                                                        />
                                                    </Grid>
                                                    <Grid item xs={12} sm={12}>
                                                        <TextField
                                                            className={`${style.texting} ${formik.touched.last_name && (Boolean(formik.errors.last_name) || (apiErrors && apiErrors.last_name)) ? invalid : normaling}`}
                                                            fullWidth
                                                            id="last_name"
                                                            name="last_name"
                                                            placeholder={trans('last-name')}
                                                            variant="outlined"
                                                            value={formik.values.last_name}
                                                            onChange={formik.handleChange}
                                                            onBlur={formik.handleBlur}
                                                            error={formik.touched.last_name && Boolean(formik.errors.last_name || (apiErrors && apiErrors?.last_name))}
                                                            style={workspaceId ? {} : { border: '1px solid #CDCDCD !important', borderRadius: '6px !important' }}
                                                        />
                                                    </Grid>

                                                    <Grid item xs={12}>
                                                        <TextField
                                                            type="text"
                                                            className={`${style.texting} ${formik.touched.gsm && (Boolean(formik.errors.gsm) || (apiErrors && apiErrors.gsm)) || !isGsmValid ? invalid : normaling}`}
                                                            fullWidth
                                                            id="gsm"
                                                            name="gsm"
                                                            placeholder={trans('mobile')}
                                                            variant="outlined"
                                                            value={formik.values.gsm}
                                                            onChange={handleGsmChange}
                                                            onBlur={formik.handleBlur}
                                                            error={formik.touched.gsm && Boolean(formik.errors.gsm || (apiErrors && apiErrors.gsm))}
                                                            style={workspaceId ? {} : { border: '1px solid #CDCDCD !important', borderRadius: '6px !important' }}
                                                            InputProps={{
                                                                style: { color: !isGsmValid || (apiErrors && apiErrors.isGsmValid) ? '#D94B2C' : '#413E38' ,paddingRight: '0'},
                                                                startAdornment: (
                                                                    <InputAdornment position="start">
                                                                        <Select
                                                                            open={open}
                                                                            onOpen={(e) => {
                                                                                e.preventDefault();
                                                                                setTimeout(() => {
                                                                                    (document.activeElement as HTMLElement).blur();
                                                                                    setOpen(true);
                                                                                }, 0);
                                                                            }}
                                                                            onClose={() => setOpen(false)}
                                                                            value={selectedCountry == "+31" ? "+31" : "+32"}
                                                                            onChange={handleCountryChange}
                                                                        >
                                                                            <MenuItem className={`${style.customMenuItem}`} value="+32">
                                                                                <div className='d-flex ps-2'><svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" shapeRendering="geometricPrecision" textRendering="geometricPrecision" imageRendering="optimizeQuality" fillRule="evenodd" clipRule="evenodd" viewBox="0 0 203.55 141.6"><g fillRule="nonzero"><path fill="#ED2939" d="M203.55 11.19v119.22c0 6.16-5.04 11.19-11.19 11.19H11.19C5.05 141.6.02 136.59 0 130.45V11.15C.02 5.01 5.05 0 11.19 0h181.17c6.15 0 11.19 5.03 11.19 11.19z" /><path fill="#FAE042" d="M135.7 0v141.6H11.19C5.05 141.6.02 136.59 0 130.45V11.15C.02 5.01 5.05 0 11.19 0H135.7z" /><path d="M67.85 0v141.6H11.19C5.05 141.6.02 136.59 0 130.45V11.15C.02 5.01 5.05 0 11.19 0h56.66z" /></g></svg>
                                                                                    <div className={`${style.country}`}>+32</div></div>
                                                                            </MenuItem>
                                                                            <MenuItem className={`${style.customMenuItem} px-1`} value="+31">
                                                                                <div className='d-flex ps-2'>
                                                                                    <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" shapeRendering="geometricPrecision" textRendering="geometricPrecision" imageRendering="optimizeQuality" fillRule="evenodd" clipRule="evenodd" viewBox="0 0 43.06 29.96"><g fillRule="nonzero"><path fill="#21468B" d="M43.06 20v7.59c0 1.3-1.06 2.37-2.37 2.37H2.37C1.06 29.96 0 28.89 0 27.59V20h43.06z" /><path fill="#fff" d="M43.06 20H0V2.37C0 1.06 1.06 0 2.37 0h38.32c1.31 0 2.37 1.06 2.37 2.37V20z" /><path fill="#AE1C28" d="M43.06 9.96H0V2.37C0 1.06 1.06 0 2.37 0h38.32c1.31 0 2.37 1.06 2.37 2.37v7.59z" /></g></svg>
                                                                                    <div className={`${style.country}`}>+31</div></div>
                                                                            </MenuItem>
                                                                        </Select>

                                                                    </InputAdornment>
                                                                ),
                                                            }}
                                                        />
                                                    </Grid>

                                                    <Grid item xs={12}>
                                                        <TextField
                                                            className={`${style.texting} ${formik.touched.email && (Boolean(formik.errors.email) || (apiErrors && apiErrors.email)) && !isEmailValid  ? invalid : normaling}`}
                                                            fullWidth
                                                            id="email"
                                                            name="email"
                                                            placeholder={trans('email')}
                                                            variant="outlined"
                                                            value={formik.values.email}
                                                            onChange={() => { handleInputChange(event); apiErrors && apiErrors.email ? apiErrors.email = null : '' }}
                                                            onBlur={formik.handleBlur}
                                                            error={formik.touched.email && Boolean(formik.errors.email || (apiErrors && apiErrors?.email))  && !isEmailValid}
                                                            style={workspaceId ? {} : { border: '1px solid #CDCDCD !important', borderRadius: '6px !important' }}
                                                        />
                                                    </Grid>

                                                    <Grid item xs={12}>
                                                        <TextField
                                                            className={`${style.texting} ${formik.touched.password && Boolean(formik.errors.password || (apiErrors && apiErrors.password)) || !isPasswordValid ? invalid : normaling}`}
                                                            fullWidth
                                                            id="password"
                                                            name="password"
                                                            type={showPassword ? "text" : "password"}
                                                            placeholder={trans('password')}
                                                            variant="outlined"
                                                            value={formik.values.password}
                                                            onChange={formik.handleChange}
                                                            onBlur={formik.handleBlur}
                                                            error={formik.touched.password && Boolean(formik.errors.password || (apiErrors && apiErrors.password))}
                                                            style={workspaceId ? { backgroundColor: '#FFFFFF' } : { border: '1px solid #CDCDCD !important', borderRadius: '6px !important', backgroundColor: '#FFFFFF' }}
                                                            onKeyUp={() => { setIsPasswordValid(true) }}
                                                            InputProps={{
                                                                style: { color: !isPasswordValid || (apiErrors && apiErrors.isPasswordValid) ? '#D94B2C' : '#413E38' },
                                                                endAdornment: (
                                                                    <InputAdornment position="end">
                                                                        {
                                                                            showPassword
                                                                                ? <svg onClick={handleClickShowPassword} xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                                                                    <path d="M1 12C1 12 5 4 12 4C19 4 23 12 23 12C23 12 19 20 12 20C5 20 1 12 1 12Z" stroke={formik.touched.password && Boolean(formik.errors.password || (apiErrors && apiErrors.password)) || !isPasswordValid ? "#E03009" : "#888888"} strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" />
                                                                                    <path d="M12 15C13.6569 15 15 13.6569 15 12C15 10.3431 13.6569 9 12 9C10.3431 9 9 10.3431 9 12C9 13.6569 10.3431 15 12 15Z" stroke={formik.touched.password && Boolean(formik.errors.password || (apiErrors && apiErrors.password)) || !isPasswordValid ? "#E03009" : "#888888"} strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" />
                                                                                    <line x1={5.378} y1={1.318} x2={19.318} y2={23.622} stroke={formik.touched.password && Boolean(formik.errors.password || (apiErrors && apiErrors.password)) || !isPasswordValid ? "#E03009" : "#888888"} strokeWidth={2} strokeLinecap='round' strokeLinejoin='round' />
                                                                                </svg>
                                                                                : <svg onClick={handleClickShowPassword} xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                                                                    <path d="M1 12C1 12 5 4 12 4C19 4 23 12 23 12C23 12 19 20 12 20C5 20 1 12 1 12Z" stroke={formik.touched.password && Boolean(formik.errors.password || (apiErrors && apiErrors.password)) || !isPasswordValid ? "#E03009" : "#888888"} strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" />
                                                                                    <path d="M12 15C13.6569 15 15 13.6569 15 12C15 10.3431 13.6569 9 12 9C10.3431 9 9 10.3431 9 12C9 13.6569 10.3431 15 12 15Z" stroke={formik.touched.password && Boolean(formik.errors.password || (apiErrors && apiErrors.password)) || !isPasswordValid ? "#E03009" : "#888888"} strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" />
                                                                                </svg>
                                                                        }

                                                                    </InputAdornment>
                                                                )
                                                            }}
                                                        />
                                                    </Grid>
                                                    <Grid item xs={12}>
                                                        <TextField
                                                            className={`${style.texting} ${!isPasswordConfirmationValid || (formik.touched.password_confirmation
                                                                && Boolean(formik.errors.password_confirmation || (apiErrors && apiErrors.password_confirmation)))
                                                                ? invalid : normaling}`}
                                                            fullWidth
                                                            id="password_confirmation"
                                                            name="password_confirmation"
                                                            style={workspaceId ? { backgroundColor: '#FFFFFF' } : { border: '1px solid #CDCDCD !important', borderRadius: '6px !important', backgroundColor: '#FFFFFF' }}
                                                            type={showRepeatPassword ? "text" : "password"}
                                                            placeholder={trans('password-confirm')}
                                                            variant="outlined"
                                                            value={formik.values.password_confirmation}
                                                            onChange={formik.handleChange}
                                                            onBlur={formik.handleBlur}
                                                            error={formik.touched.password_confirmation && Boolean(formik.errors.password_confirmation || (apiErrors && apiErrors.password_confirmation))}
                                                            helperText={""}
                                                            onKeyUp={() => { setIsPasswordConfirmationValid(true) }}
                                                            InputProps={{
                                                                style: { color: !isPasswordConfirmationValid || (apiErrors && apiErrors.isPasswordConfirmationValid) ? '#E03009' : '#413E38' },
                                                                endAdornment: (
                                                                    <InputAdornment position="end">
                                                                        {showRepeatPassword
                                                                            ? <svg onClick={handleClickShowRepeatPassword} xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                                                                <path d="M1 12C1 12 5 4 12 4C19 4 23 12 23 12C23 12 19 20 12 20C5 20 1 12 1 12Z" stroke={!isPasswordConfirmationValid || (formik.touched.password_confirmation && Boolean(formik.errors.password_confirmation || (apiErrors && apiErrors.password_confirmation))) ? "#E03009" : "#888888"} strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" />
                                                                                <path d="M12 15C13.6569 15 15 13.6569 15 12C15 10.3431 13.6569 9 12 9C10.3431 9 9 10.3431 9 12C9 13.6569 10.3431 15 12 15Z" stroke={!isPasswordConfirmationValid || (formik.touched.password_confirmation && Boolean(formik.errors.password_confirmation || (apiErrors && apiErrors.password_confirmation))) ? "#E03009" : "#888888"} strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" />
                                                                                <line x1={5.378} y1={1.318} x2={19.318} y2={23.622} stroke={!isPasswordConfirmationValid || (formik.touched.password_confirmation && Boolean(formik.errors.password_confirmation || (apiErrors && apiErrors.password_confirmation))) ? "#E03009" : "#888888"} strokeWidth={2} strokeLinecap='round' strokeLinejoin='round' />
                                                                            </svg>
                                                                            : <svg onClick={handleClickShowRepeatPassword} xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                                                                <path d="M1 12C1 12 5 4 12 4C19 4 23 12 23 12C23 12 19 20 12 20C5 20 1 12 1 12Z" stroke={!isPasswordConfirmationValid || (formik.touched.password_confirmation && Boolean(formik.errors.password_confirmation || (apiErrors && apiErrors.password_confirmation))) ? "#E03009" : "#888888"} strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" />
                                                                                <path d="M12 15C13.6569 15 15 13.6569 15 12C15 10.3431 13.6569 9 12 9C10.3431 9 9 10.3431 9 12C9 13.6569 10.3431 15 12 15Z" stroke={!isPasswordConfirmationValid || (formik.touched.password_confirmation && Boolean(formik.errors.password_confirmation || (apiErrors && apiErrors.password_confirmation))) ? "#E03009" : "#888888"} strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" />
                                                                            </svg>
                                                                        }
                                                                    </InputAdornment>
                                                                )
                                                            }}
                                                        />
                                                    </Grid>
                                                    <div className="ms-3" style={{ marginTop: '40px' }}>
                                                        <div className='d-flex align-items-center '>
                                                            <div className="me-2" onClick={toggleIcon}>
                                                                {isFirstIconVisible ? (
                                                                    <svg width="25" height="25" viewBox="0 0 25 25" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                        <circle cx="12.5" cy="12.5" r="11.5" stroke={isButtonClicked ? "red" : (color ?? '#ABA765')} strokeWidth="2" />
                                                                    </svg>
                                                                ) : (
                                                                    <svg width="25" height="25" viewBox="0 0 25 25" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                        <circle cx="12.5" cy="12.5" r="11.5" fill={(color ?? '#ABA765')} stroke={(color ?? '#ABA765')} strokeWidth="2" />
                                                                        <path d="M19.5 7.25L9.875 16.875L5.5 12.5" stroke="white" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" />
                                                                    </svg>
                                                                )}
                                                            </div>
                                                            <div className={`${style['term-register']}`}
                                                                style={{
                                                                    fontFamily: 'SF Compact Display',
                                                                    fontSize: '14px',
                                                                    lineHeight: '17px',
                                                                    color: '#4F4F4F',
                                                                    fontWeight: '457',
                                                                }}>
                                                                <span>{trans('agree') + " "}</span>
                                                                <span role="button" onClick={() => { window.open(TERMS_CONDITIONS_LINK, "_blank") }} className={style['underline-register']}>{trans('term-condition') + " "}</span>
                                                                <span>{trans('and') + " "}</span>
                                                                <span role="button" onClick={() => { window.open(PRIVACY_POLICY_LINK, "_blank") }} className={style['underline-register']}>{trans('privacy-policy')}</span>.
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <Grid item xs={12} className={`d-flex justify-content-center`} style={{ margin: 'auto', marginTop: '8px' }}>
                                                        {
                                                            workspaceId ?
                                                                (
                                                                    <>
                                                                        <Button type="submit" onClick={() => handleRegisterClick()}>
                                                                            <div style={{ background: color }}
                                                                                className={`${style['save-button']} ${Object.keys(formik.errors).length != 0 ? `${style['btn-disable']}` : ``}`}>{trans('register-btn')}</div>
                                                                        </Button>
                                                                    </>
                                                                ) : (
                                                                    <>
                                                                            <Button type="submit" onClick={() => handleRegisterClick()}>
                                                                                <div style={{
                                                                                    fontFamily: 'SF Compact Display',
                                                                                    backgroundColor: '#ABA765',
                                                                                    borderRadius: '80px',
                                                                                    margin: 'auto',
                                                                                    width: '153px',
                                                                                    textTransform: 'none'
                                                                                }}
                                                                                    className={`${style['save-button']} ${Object.keys(formik.errors).length != 0 ? `${style['btn-disable']}` : ``}`}>{trans('register-btn')}</div>
                                                                            </Button>

                                                                    </>
                                                                )
                                                        }
                                                    </Grid>
                                                </Grid>
                                            </form>
                                            <div style={{ position: "relative", marginTop: '25px' }}>
                                                <div style={workspaceId ? {} : { border: '1px solid #CDCDCD' }} className={`${style['line-break-register']}`}>
                                                </div>
                                                <div className={`${style['text-break-register']}`}
                                                    style={{
                                                        fontFamily: 'SF Compact Display',
                                                        color: '#4F4F4F',
                                                        fontSize: '14px',
                                                        lineHeight: '17px',
                                                    }}>
                                                    {trans('other-register')}
                                                </div>
                                            </div>

                                            <div className={`${style['social-register']}`}>
                                                {(apiDataToken?.data?.facebook_enabled > 0 || !workspaceId) && (
                                                    <FacebookLogin
                                                        appId={process.env.NEXT_PUBLIC_FACEBOOK_APP_ID}
                                                        callback={responseFacebook}
                                                        isMobile={false}
                                                        render={(renderProps: any) => (
                                                            <div onClick={renderProps.onClick}
                                                                style={{ width: "fit-content" }}
                                                                className={`${style['social-login-btn']}`}>
                                                                <svg xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" width="50" height="50" viewBox="0 0 48 48">
                                                                    <linearGradient id="Ld6sqrtcxMyckEl6xeDdMa_uLWV5A9vXIPu_gr1" x1="9.993" x2="40.615" y1="9.993" y2="40.615" gradientUnits="userSpaceOnUse">
                                                                        <stop offset="0" stopColor="#2aa4f4"></stop>
                                                                        <stop offset="1" stopColor="#007ad9"></stop>
                                                                    </linearGradient>
                                                                    <path fill="url(#Ld6sqrtcxMyckEl6xeDdMa_uLWV5A9vXIPu_gr1)" d="M24,4C12.954,4,4,12.954,4,24s8.954,20,20,20s20-8.954,20-20S35.046,4,24,4z"></path>
                                                                    <path fill="#fff" d="M26.707,29.301h5.176l0.813-5.258h-5.989v-2.874c0-2.184,0.714-4.121,2.757-4.121h3.283V12.46 c-0.577-0.078-1.797-0.248-4.102-0.248c-4.814,0-7.636,2.542-7.636,8.334v3.498H16.06v5.258h4.948v14.452 C21.988,43.9,22.981,44,24,44c0.921,0,1.82-0.084,2.707-0.204V29.301z"></path>
                                                                </svg>
                                                            </div>
                                                        )}
                                                    />)}
                                                {(apiDataToken?.data?.google_enabled > 0 || !workspaceId) && (
                                                    <GoogleLogin
                                                        clientId={process.env.NEXT_PUBLIC_GOOGLE_CLIENT_ID ?? ''}
                                                        render={renderProps => (
                                                            <div onClick={renderProps.onClick}
                                                                style={{ width: "fit-content", padding: '0 34px' }}
                                                                className={`${style['social-login-btn']}`}>
                                                                <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 40 40" fill="none">
                                                                    <g clipPath="url(#clip0_5742_1363)">
                                                                        <path d="M39.575 20.4501C39.575 19.1334 39.4583 17.8834 39.2583 16.6667H20.425V24.1834H31.2083C30.725 26.6501 29.3083 28.7334 27.2083 30.1501V35.1501H33.6417C37.4083 31.6667 39.575 26.5334 39.575 20.4501Z" fill="#4285F4" />
                                                                        <path d="M20.425 40C25.825 40 30.3417 38.2 33.6417 35.15L27.2083 30.15C25.4083 31.35 23.125 32.0833 20.425 32.0833C15.2083 32.0833 10.7917 28.5666 9.20832 23.8167H2.57498V28.9667C5.85832 35.5 12.6083 40 20.425 40Z" fill="#34A853" />
                                                                        <path d="M9.20832 23.8168C8.79165 22.6168 8.57499 21.3334 8.57499 20.0001C8.57499 18.6668 8.80832 17.3834 9.20832 16.1834V11.0334H2.57499C1.20832 13.7334 0.424988 16.7668 0.424988 20.0001C0.424988 23.2334 1.20832 26.2668 2.57499 28.9668L9.20832 23.8168Z" fill="#FBBC05" />
                                                                        <path d="M20.425 7.91667C23.375 7.91667 26.0083 8.93334 28.0916 10.9167L33.7916 5.21667C30.3416 1.98334 25.825 0 20.425 0C12.6083 0 5.85831 4.5 2.57498 11.0333L9.20832 16.1833C10.7916 11.4333 15.2083 7.91667 20.425 7.91667Z" fill="#EA4335" />
                                                                    </g>
                                                                    <defs>
                                                                        <clipPath id="clip0_5742_1363">
                                                                            <rect width="40" height="40" fill="white" />
                                                                        </clipPath>
                                                                    </defs>
                                                                </svg>
                                                            </div>
                                                        )}
                                                        buttonText="Login"
                                                        onSuccess={onSuccess}
                                                        onFailure={onFailure}
                                                        cookiePolicy={'single_host_origin'}
                                                    />)}
                                                {(apiDataToken?.data?.apple_enabled > 0 || !workspaceId) && (
                                                    <AppleLogin
                                                        clientId={process.env.NEXT_PUBLIC_APPLE_CLIENT_ID ?? ''}
                                                        redirectURI={window.location.origin}
                                                        responseType="id_token code"
                                                        responseMode="fragment"
                                                        usePopup={true}
                                                        // scope="name email"
                                                        callback={responseApple}
                                                        render={renderProps => (
                                                            <div onClick={renderProps.onClick}
                                                                style={{ width: "fit-content" }}
                                                                className={`${style['social-login-btn']}`}>
                                                                <svg xmlns="http://www.w3.org/2000/svg" width="50" height="41" viewBox="0 0 33 41" fill="none">
                                                                    <g clipPath="url(#clip0_5618_3513)">
                                                                        <path d="M31.524 13.8801C31.292 14.0601 27.196 16.3681 27.196 21.5001C27.196 27.4361 32.408 29.5361 32.564 29.5881C32.54 29.7161 31.736 32.4641 29.816 35.2641C28.104 37.7281 26.316 40.1881 23.596 40.1881C20.876 40.1881 20.176 38.6081 17.036 38.6081C13.976 38.6081 12.888 40.2401 10.4 40.2401C7.912 40.2401 6.176 37.9601 4.18 35.1601C1.868 31.8721 0 26.7641 0 21.9161C0 14.1401 5.056 10.0161 10.032 10.0161C12.676 10.0161 14.88 11.7521 16.54 11.7521C18.12 11.7521 20.584 9.91214 23.592 9.91214C24.732 9.91214 28.828 10.0161 31.524 13.8801ZM22.164 6.62014C23.408 5.14414 24.288 3.09614 24.288 1.04814C24.288 0.764141 24.264 0.476141 24.212 0.244141C22.188 0.320141 19.78 1.59214 18.328 3.27614C17.188 4.57214 16.124 6.62014 16.124 8.69614C16.124 9.00814 16.176 9.32014 16.2 9.42014C16.328 9.44414 16.536 9.47214 16.744 9.47214C18.56 9.47214 20.844 8.25614 22.164 6.62014Z" fill="black" />
                                                                    </g>
                                                                    <defs>
                                                                        <clipPath id="clip0_5618_3513">
                                                                            <rect width="32.56" height="40" fill="white" transform="translate(0 0.244141)" />
                                                                        </clipPath>
                                                                    </defs>
                                                                </svg>
                                                            </div>
                                                        )}
                                                    />)}
                                            </div>
                                        </ThemeProvider>
                                    </div>
                                </div>
                            </div>
                            <div role="button" onClick={() => handleClose()} style={{ color: color ?? '#ABA765' }}
                                className={`${style['footer-login-text-desk']} text-center my-3 px-3 text-uppercase`}>
                                {trans('back-to-login')}
                            </div>
                        </Modal.Body>
                    )
                }
            </Modal>
            <style>{`
                .MuiButtonBase-root {
                    width: 100%!important;
                    padding: 0px!important;
                }`}
            </style>
        </>
    );
}