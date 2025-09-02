'use client'

import style from 'public/assets/css/profile.module.scss'
import React, { useState, useEffect } from 'react';
import { useI18n } from '@/locales/client';
import Cookies from 'js-cookie';
import { useRouter } from "next/navigation";
import { useFormik } from 'formik';
import * as Yup from 'yup';
import 'react-toastify/dist/ReactToastify.css';
import { Button, Grid, InputAdornment, TextField } from '@mui/material';
import { createTheme, ThemeProvider } from "@mui/material/styles";
import { confirmNewPassword } from '@/services/confirm_new_password';
import { Modal } from "react-bootstrap";
import { useAppSelector } from '@/redux/hooks'
import { useGetWorkspaceDataByIdQuery } from '@/redux/services/workspace/workspaceDataApi'

export default function ResetPassword({ togglePopup, token, email }: { togglePopup: any, token: string, email: string; }) {
    const workspaceId = useAppSelector((state) => state.workspaceData.globalWorkspaceId)
    const { data: apiDataToken } = useGetWorkspaceDataByIdQuery({ id: workspaceId })
    const apiData = apiDataToken?.data?.setting_generals;
    const color = useAppSelector((state) => state.workspaceData.globalWorkspaceColor)
    const invalid = style['invalid'];
    const router = useRouter();
    const trans = useI18n();
    const language = Cookies.get('Next-Locale');
    const [show, setShow] = useState(false);
    const [isSuccess, setIsSuccess] = useState(false);

    const handleClose = () => {
        togglePopup();
        setShow(false);
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
        password: Yup.string().required(trans('required')),
        password_confirmation: Yup.string()
            .required(trans('required'))
            .oneOf([Yup.ref('password'), ''], trans('lang_password_must_match')),

    });

    const [isVisible, setIsVisible] = useState(false);
    const [errorMessage, setErrorMessage] = useState<any | null>(null);
    const [apiErrors, setApiErrors] = useState<any | null>(null);

    const formik = useFormik({
        initialValues: {
            password: '',
            password_confirmation: '',
        },
        validationSchema,
        onSubmit: async (values) => {
            if(isPasswordValid && isPasswordConfirmationValid) {
                try {
                    const decodedEmail = email.includes('%') ? decodeURIComponent(email.replace(/\+/g, '%2B')) : email;
                    const apiData = await confirmNewPassword({
                        token: token,
                        email: decodedEmail,
                        password: values.password,
                        password_confirmation: values.password_confirmation,
                    });
    
                    if (apiData.success) {
                        setIsSuccess(true);
                    } else {
                        setApiErrors(apiData?.errors);
                        setErrorMessage(apiData?.data?.message);
                        setIsVisible(true);
                    }
                } catch (error: any) {
                    setApiErrors(error.response.data.data);
                    const errors = Object.values(error.response.data.data);
                    const lastErrorMessage = errors[errors.length - 1];
                    setErrorMessage(lastErrorMessage);
                }
            }            
        },
        enableReinitialize: true,
        validateOnMount: true,
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

    const [isPasswordValid, setIsPasswordValid] = useState(true); // Store the input value
    const [isPasswordConfirmationValid, setIsPasswordConfirmationValid] = useState(true); // Store the input value

    // message error check
    const handleRegisterClick = () => {
        if (formik.values.password !== formik.values.password_confirmation) {
            setErrorMessage(trans('password-not-match'));
            setIsPasswordValid(false);
            setIsPasswordConfirmationValid(false);
        }

        if (formik.values.password.length < 6) {
            setErrorMessage(trans('password-min-length'));
            setIsPasswordValid(false);
        }

        if (formik.values.password_confirmation.length < 6) {
            setErrorMessage(trans('password-min-length'));
            setIsPasswordConfirmationValid(false);
        }

        if (formik.values.password === '' || formik.values.password_confirmation === '') {
            if (formik.values.password === '') {
                setIsPasswordValid(false);
            }
            if (formik.values.password_confirmation === '') {
                setIsPasswordConfirmationValid(false);
            }

            setIsVisible(true);
            setErrorMessage(trans('missing-fields'));
        }
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
                            height: '48px',
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
                            height: '42px',
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
                            padding: '10px',
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
                            <div className="close-popup text-828282" onClick={() => {
                                const query = new URLSearchParams(window.location.search);
                                if (query.size > 0) {
                                    router.push('/?login=true')
                                } else {
                                    router.push('/?login=true')
                                }
                                handleClose();
                            }} style={workspaceId ? {} : {
                                fontFamily: "SF Compact Display",
                                fontSize: '16px',
                                fontStyle: 'normal',
                                fontWeight: '790',
                                lineHeight: '19.09px',
                                color: '#676767',
                            }}>
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="25" viewBox="0 0 24 25" fill="none">
                                    <path d="M14 17L10 12.5L14 8" stroke="#808080" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" />
                                </svg>
                                <div className="mt-1">{trans('back')}</div>
                            </div>
                            <div className="text-center" style={{ marginTop: '180px' }}>
                                <svg xmlns="http://www.w3.org/2000/svg" width="134" height="134" viewBox="0 0 134 134" fill="none">
                                    <path d="M122.833 61.8634V67.0001C122.827 79.0401 118.928 90.7553 111.719 100.399C104.51 110.042 94.3768 117.096 82.8308 120.51C71.2849 123.924 58.9448 123.514 47.6509 119.341C36.357 115.169 26.7144 107.457 20.1613 97.357C13.6082 87.2566 10.4956 75.3084 11.2878 63.2945C12.08 51.2805 16.7345 39.8445 24.5571 30.692C32.3798 21.5395 42.9515 15.1609 54.6955 12.5075C66.4395 9.85414 78.7266 11.0681 89.7243 15.9684" stroke={workspaceId ? (color ?? '#D87833') : '#ABA765'} strokeWidth="4" strokeLinecap="round" strokeLinejoin="round" />
                                    <path d="M122.833 22.3333L67 78.2224L50.25 61.4724" stroke={workspaceId ? (color ?? '#D87833') : '#ABA765'} strokeWidth="4" strokeLinecap="round" strokeLinejoin="round" />
                                </svg>

                                {
                                    workspaceId ?
                                        (
                                            <>
                                                <div className="text-center px-3 pt-4 pb-1 profile-title">{trans('password-changed')}</div>
                                                <div className="text-center px-3 profile-description text-828282">
                                                    {trans('new-password-success')}
                                                </div>
                                                <div role="button" onClick={() => {
                                                    const query = new URLSearchParams(window.location.search);
                                                    if (query.size > 0) {
                                                        router.push('/?login=true')
                                                    } else {
                                                        router.push('/?login=true')
                                                    }
                                                    handleClose();
                                                }} style={{ color: color }}
                                                    className={`${style['footer-login-text-desk']} text-center pt-3 mt-2 px-3 text-uppercase`}>
                                                    {trans('back-to-login')}
                                                </div>
                                            </>
                                        )
                                        :
                                        (
                                            <>
                                                <div className="res-mobile">
                                                    <div className="text-center font-bold px-3 pt-4 pb-1 profile-title"
                                                        style={{
                                                            fontWeight: '790',
                                                            fontSize: '24px',
                                                            lineHeight: '28.64px',
                                                            color: '#404040'
                                                        }}
                                                    >{trans('password-changed')}
                                                    </div>
                                                    <div className="text-center px-3 profile-description text-828282"
                                                        style={{
                                                            fontFamily: "SF Compact Display Medium",
                                                            fontWeight: '556',
                                                            fontSize: '18px',
                                                            lineHeight: '25.35px',
                                                            color: '#828282'
                                                        }}
                                                    >
                                                        {trans('new-password-success')}
                                                    </div>
                                                    <div role="button" onClick={() => {
                                                        const query = new URLSearchParams(window.location.search);
                                                        if (query.size > 0) {
                                                            router.push('/?login=true')
                                                        } else {
                                                            router.push('/?login=true')
                                                        }
                                                        handleClose();
                                                    }} style={{
                                                        fontSize: '16px',
                                                        fontWeight: '790',
                                                        lineHeight: '19.09px',
                                                        color: '#ABA765',
                                                        textTransform: 'uppercase',
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
                                                        }}
                                                    >{trans('password-changed')}
                                                    </div>
                                                    <div className="text-center px-3 profile-description text-828282"
                                                        style={{
                                                            fontFamily: "SF Compact Display",
                                                            fontWeight: '556',
                                                            fontSize: '18px',
                                                            lineHeight: '25.35px',
                                                            color: '#828282'
                                                        }}
                                                    >
                                                        {trans('new-password-success')}
                                                    </div>
                                                    <div role="button" onClick={() => {
                                                        const query = new URLSearchParams(window.location.search);
                                                        if (query.size > 0) {
                                                            router.push('/?login=true')
                                                        } else {
                                                            router.push('/?login=true')
                                                        }
                                                        handleClose();
                                                    }} style={{
                                                        fontFamily: 'SF Compact Display',
                                                        fontSize: '16px',
                                                        fontWeight: '790',
                                                        lineHeight: '19.09px',
                                                        color: '#ABA765',
                                                        textTransform: 'uppercase',
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
                            <div className="close-popup" onClick={() => {
                                const query = new URLSearchParams(window.location.search);
                                if (query.size > 0) {
                                    router.push('/?login=true')
                                } else {
                                    router.push('/?login=true')
                                }
                                handleClose();
                            }} style={workspaceId ? {} : {
                                fontFamily: "SF Compact Display",
                                fontSize: '16px',
                                fontStyle: 'normal',
                                fontWeight: '790',
                                lineHeight: '19.09px',
                                color: '#828282',
                            }}>
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="25" viewBox="0 0 24 25" fill="none" style={{ minWidth: '21px' }}>
                                    <path d="M14 17L10 12.5L14 8" stroke="#808080" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" />
                                </svg>
                                <div className="mt-1">{trans('back')}</div>
                            </div>
                            {errorMessage && (
                                <div style={{ position: "relative" }}>
                                    <div className={`px-3 pb-1 my-3`} style={{ position: 'absolute', width: '100%' }}>
                                        <div className={`${style['error-message']} px-3`}>
                                            <svg className="me-2" xmlns="http://www.w3.org/2000/svg" width="21" height="21" viewBox="0 0 21 21" fill="none">
                                                <path d="M9.00368 3.37756L1.59243 15.7501C1.43962 16.0147 1.35877 16.3147 1.35792 16.6203C1.35706 16.9258 1.43623 17.2263 1.58755 17.4918C1.73887 17.7572 1.95706 17.9785 2.22042 18.1334C2.48378 18.2884 2.78313 18.3717 3.08868 18.3751H17.9112C18.2167 18.3717 18.5161 18.2884 18.7794 18.1334C19.0428 17.9785 19.261 17.7572 19.4123 17.4918C19.5636 17.2263 19.6428 16.9258 19.6419 16.6203C19.6411 16.3147 19.5602 16.0147 19.4074 15.7501L11.9962 3.37756C11.8402 3.1204 11.6206 2.90779 11.3585 2.76023C11.0964 2.61267 10.8007 2.53516 10.4999 2.53516C10.1992 2.53516 9.90347 2.61267 9.64138 2.76023C9.3793 2.90779 9.15966 3.1204 9.00368 3.37756V3.37756Z" stroke="#E03009" strokeWidth="1.5" strokeLinecap="round" strokeLinejoin="round" />
                                                <path d="M10.5 7.875V11.375" stroke="#E03009" strokeWidth="1.5" strokeLinecap="round" strokeLinejoin="round" />
                                                <path d="M10.5 14.875H10.5088" stroke="#E03009" strokeWidth="1.5" strokeLinecap="round" strokeLinejoin="round" />
                                            </svg>
                                            {errorMessage}
                                        </div>
                                    </div>
                                </div>
                            )}

                            {
                                workspaceId ?
                                    (
                                        <>
                                            <div className="text-center px-3 profile-title" style={{ marginTop: '165px' }}>{trans('change-password')}</div>
                                            <div className="text-center text-828282 px-3 profile-description">
                                                {trans('enter-change-password')}
                                            </div>
                                        </>
                                    )
                                    :
                                    (
                                        <>
                                            <div className="res-mobile">
                                                <div className="text-center font-bold px-3 profile-title"
                                                    style={{
                                                        marginTop: '165px',
                                                        fontSize: '24px',
                                                        fontWeight: '790',
                                                        lineHeight: '29px',
                                                        textAlign: 'center',
                                                        color: '#404040'
                                                    }}>
                                                    {trans('change-password')}
                                                </div>
                                                <div className="text-center text-828282 px-3 profile-description"
                                                    style={{
                                                        fontFamily: 'SF Compact Display Medium',
                                                        fontSize: '18px',
                                                        fontWeight: '556',
                                                        lineHeight: '25.35px',
                                                        textAlign: 'center',
                                                        color: '#828282'
                                                    }}
                                                >
                                                    {trans('enter-change-password')}
                                                </div>
                                            </div>
                                            <div className="res-desktop">
                                                <div className="text-center px-3 profile-title"
                                                    style={{
                                                        marginTop: '165px',
                                                        fontFamily: 'SF Compact Display',
                                                        fontSize: '24px',
                                                        fontWeight: '790',
                                                        lineHeight: '29px',
                                                        textAlign: 'center',
                                                        color: '#404040'
                                                    }}>
                                                    {trans('change-password')}
                                                </div>
                                                <div className="text-center text-828282 px-3 profile-description"
                                                    style={{
                                                        fontFamily: 'SF Compact Display',
                                                        fontSize: '18px',
                                                        fontWeight: '556',
                                                        lineHeight: '25.35px',
                                                        textAlign: 'center',
                                                        color: '#828282'
                                                    }}
                                                >
                                                    {trans('enter-change-password')}
                                                </div>
                                            </div>
                                        </>
                                    )
                            }
                            <div className={`${style['menu-profile']} mb-0`}>
                                <div className={style['detail-profile']}>
                                    <ThemeProvider theme={theme}>
                                        <form onSubmit={formik.handleSubmit} method={'POST'}>
                                            <Grid container spacing={2} style={{ justifyContent: 'center' }}>
                                                <Grid item xs={12}>
                                                    <TextField
                                                        className={`${style.texting} ${formik.touched.password && Boolean(formik.errors.password || (apiErrors && apiErrors.password)) || !isPasswordValid ? invalid : ''}`}
                                                        fullWidth
                                                        id="password"
                                                        name="password"
                                                        style={{ backgroundColor: '#FFFFFF' }}
                                                        type={showPassword ? "text" : "password"}
                                                        placeholder={trans('password')}
                                                        variant="outlined"
                                                        value={formik.values.password}
                                                        onChange={formik.handleChange}
                                                        onBlur={formik.handleBlur}
                                                        error={formik.touched.password && Boolean(formik.errors.password || (apiErrors && apiErrors.password))  || !isPasswordValid}
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
                                                            && !isPasswordConfirmationValid ? invalid : ''}`}
                                                        fullWidth
                                                        id="password_confirmation"
                                                        name="password_confirmation"
                                                        style={{ backgroundColor: '#FFFFFF' }}
                                                        type={showRepeatPassword ? "text" : "password"}
                                                        placeholder={trans('password-confirm')}
                                                        variant="outlined"
                                                        value={formik.values.password_confirmation}
                                                        onChange={formik.handleChange}
                                                        onBlur={formik.handleBlur}
                                                        error={formik.touched.password_confirmation && Boolean(formik.errors.password_confirmation || (apiErrors && apiErrors.password_confirmation)) && !isPasswordConfirmationValid}
                                                        helperText={""}
                                                        onKeyUp={() => { setIsPasswordConfirmationValid(true) }}
                                                        InputProps={{
                                                            style: { color: !isPasswordConfirmationValid || (apiErrors && apiErrors.isPasswordConfirmationValid) ? '#E03009' : '#413E38' },
                                                            endAdornment: (
                                                                <InputAdornment position="end">
                                                                    {showRepeatPassword
                                                                        ? <svg onClick={handleClickShowRepeatPassword} xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                                                            <path d="M1 12C1 12 5 4 12 4C19 4 23 12 23 12C23 12 19 20 12 20C5 20 1 12 1 12Z" stroke={!isPasswordConfirmationValid || (formik.touched.password_confirmation && Boolean(formik.errors.password_confirmation || (apiErrors && apiErrors.password_confirmation)))  && !isPasswordConfirmationValid ? "#E03009" : "#888888"} strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" />
                                                                            <path d="M12 15C13.6569 15 15 13.6569 15 12C15 10.3431 13.6569 9 12 9C10.3431 9 9 10.3431 9 12C9 13.6569 10.3431 15 12 15Z" stroke={!isPasswordConfirmationValid || (formik.touched.password_confirmation && Boolean(formik.errors.password_confirmation || (apiErrors && apiErrors.password_confirmation))) && !isPasswordConfirmationValid ? "#E03009" : "#888888"} strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" />
                                                                            <line x1={5.378} y1={1.318} x2={19.318} y2={23.622} stroke={!isPasswordConfirmationValid || (formik.touched.password_confirmation && Boolean(formik.errors.password_confirmation || (apiErrors && apiErrors.password_confirmation))) && !isPasswordConfirmationValid ? "#E03009" : "#888888"} strokeWidth={2} strokeLinecap='round' strokeLinejoin='round' />
                                                                        </svg>
                                                                        : <svg onClick={handleClickShowRepeatPassword} xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                                                            <path d="M1 12C1 12 5 4 12 4C19 4 23 12 23 12C23 12 19 20 12 20C5 20 1 12 1 12Z" stroke={!isPasswordConfirmationValid || (formik.touched.password_confirmation && Boolean(formik.errors.password_confirmation || (apiErrors && apiErrors.password_confirmation))) && !isPasswordConfirmationValid ? "#E03009" : "#888888"} strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" />
                                                                            <path d="M12 15C13.6569 15 15 13.6569 15 12C15 10.3431 13.6569 9 12 9C10.3431 9 9 10.3431 9 12C9 13.6569 10.3431 15 12 15Z" stroke={!isPasswordConfirmationValid || (formik.touched.password_confirmation && Boolean(formik.errors.password_confirmation || (apiErrors && apiErrors.password_confirmation))) && !isPasswordConfirmationValid ? "#E03009" : "#888888"} strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" />
                                                                        </svg>
                                                                    }
                                                                </InputAdornment>
                                                            )
                                                        }}
                                                    />
                                                </Grid>

                                                {
                                                    workspaceId ?
                                                        (
                                                            <>
                                                                <Grid item xs={12} className={`d-flex justify-content-center mt-2`} style={{ margin: 'auto' }}>
                                                                    <Button type="submit" onClick={handleRegisterClick}>
                                                                        <div style={{ background: color }}
                                                                            className={`${style['save-button']}`}>{trans('change-password')}</div>
                                                                    </Button>
                                                                </Grid>
                                                            </>
                                                        )
                                                        :
                                                        (
                                                            <>
                                                                <Grid item xs={12} className={`d-flex justify-content-center mt-2`} style={{ margin: 'auto' }}>
                                                                    <Button type="submit" onClick={handleRegisterClick}>
                                                                        <div style={{ color: '#FFFFFF', background: '#ABA765', fontFamily: 'SF Compact Display', fontWeight: '790', fontSize: '16px', lineHeight: '19.09px' }}
                                                                            className={`${style['save-button']}`}>{trans('change-password')}</div>
                                                                    </Button>
                                                                </Grid>
                                                            </>
                                                        )
                                                }
                                            </Grid>
                                        </form>
                                    </ThemeProvider>
                                </div>
                            </div>
                            {
                                workspaceId ?
                                    (
                                        <>
                                            <div role="button"
                                                style={{ color: color }}
                                                onClick={() => {
                                                    const query = new URLSearchParams(window.location.search);
                                                    if (query.size > 0) {
                                                        router.push('/?login=true')
                                                    } else {
                                                        router.push('/?login=true')
                                                    }
                                                    handleClose();
                                                }}
                                                className={`${style['footer-login-text']} ${style['footer-reset-text']} text-center px-3 text-uppercase`}>
                                                {trans('back-to-login')}
                                            </div>
                                        </>
                                    )
                                    :
                                    (
                                        <>
                                            <div role="button"
                                                onClick={() => {
                                                    const query = new URLSearchParams(window.location.search);
                                                    if (query.size > 0) {
                                                        router.push('/?login=true')
                                                    } else {
                                                        router.push('/?login=true')
                                                    }
                                                    handleClose();
                                                }}
                                                style={{
                                                    fontFamily: 'SF Compact Display',
                                                    fontSize: '16px',
                                                    fontWeight: '790',
                                                    lineHeight: '19.09px',
                                                    color: '#ABA765',
                                                    textTransform: 'uppercase',
                                                    letterSpacing: '2px'
                                                }}
                                                className={`${style['footer-login-text']} ${style['footer-reset-text']} text-center px-3 text-uppercase`}>
                                                {trans('back-to-login')}
                                            </div>
                                        </>
                                    )
                            }
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