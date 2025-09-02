'use client'

import style from 'public/assets/css/profile.module.scss'
import React, { useState, useEffect } from 'react';
import { useI18n } from '@/locales/client';
import Cookies from 'js-cookie';
import { useFormik } from 'formik';
import * as Yup from 'yup';
import 'react-toastify/dist/ReactToastify.css';
import { Button, Grid, TextField } from '@mui/material';
import { createTheme, ThemeProvider } from "@mui/material/styles";
import { resetPassword } from '@/services/reset_password';
import { Modal } from "react-bootstrap";
import { useAppSelector } from '@/redux/hooks'
import { useGetWorkspaceDataByIdQuery } from '@/redux/services/workspace/workspaceDataApi'

export default function ResetPasswordStep1({ togglePopup }: { togglePopup: any }) {
    const workspaceId = useAppSelector((state) => state.workspaceData.globalWorkspaceId)
    const { data: apiDataToken } = useGetWorkspaceDataByIdQuery({ id: workspaceId })
    const apiData = apiDataToken?.data?.setting_generals;
    const color = useAppSelector((state) => state.workspaceData.globalWorkspaceColor)
    const invalid = style['invalid'];
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
        email: Yup.string().required(trans('required')).email(trans('lang_email_valid_message')),
    });

    const [errorMessage, setErrorMessage] = useState<any | null>(null);
    const [apiErrors, setApiErrors] = useState<any | null>(null);

    const formik = useFormik({
        initialValues: {
            email: '',
        },
        validationSchema,
        onSubmit: async (values) => {
            if (isEmailValid) {
                try {
                    const headers = {
                        'Content-Language': language,
                    };
                    const apiData = await resetPassword({ email: values.email });
                    if (apiData.success) {
                        setIsSuccess(true);
                    } else {
                        setErrorMessage(trans('email-not-exist'));
                    }
                } catch (error: any) {
                    setApiErrors(error.response.data.data);
                    const errors = Object.values(error.response.data.data);
                    const lastErrorMessage = errors[errors.length - 1];
                    setErrorMessage(lastErrorMessage);
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

    const [isEmailValid, setIsEmailValid] = useState(true); // Store the input value

    const handleInputChange = (event: any) => {
        formik.handleChange(event);
    };

    // message error check
    const handleRegisterClick = () => {
        setIsEmailValid(true);
        if (checkEmailValid(formik.values.email)) {
        } else {
            setIsEmailValid(false);
            if (formik.values.email) {
                setErrorMessage(trans('format-email'));
            }
        }

        if (formik.values.email === '') {
            if (formik.values.email === '') {
                setIsEmailValid(false);
            }
            setErrorMessage(trans('required'));
        }
    }
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
                                            <div className="text-center px-3 pt-4 pb-1 profile-title">{trans('email-sent')}</div>
                                            <div className="text-center px-3 profile-description text-828282">
                                                {trans('reset-password-success-message')}
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
                                                <div className="text-center font-bold px-3 pt-4 pb-1 profile-title"
                                                    style={{
                                                        fontWeight: '790',
                                                        fontSize: '24px',
                                                        lineHeight: '28.64px',
                                                        color: '#4F4F4F',
                                                    }}>
                                                    {trans('email-sent')}</div>
                                                <div className="text-center px-4 profile-description"
                                                    style={{
                                                        fontFamily: "SF Compact Display Medium",
                                                        fontWeight: '556',
                                                        fontSize: '18px',
                                                        lineHeight: '25.35px',
                                                        color: '#4F4F4F',
                                                        margin: '0 10px'
                                                    }}>
                                                    {trans('reset-password-success-message')}
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
                                                        color: '#4F4F4F',
                                                    }}>
                                                    {trans('email-sent')}</div>
                                                <div className="text-center px-4 profile-description"
                                                    style={{
                                                        fontFamily: "SF Compact Display",
                                                        fontWeight: '556',
                                                        fontSize: '18px',
                                                        lineHeight: '25.35px',
                                                        color: '#4F4F4F',
                                                        margin: '0 10px'
                                                    }}>
                                                    {trans('reset-password-success-message')}
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
                            <div className="close-popup text-828282" onClick={() => handleClose()}
                                style={workspaceId ? {
                                    marginTop: flagDesktopChangeType ? '55px' : ''
                                } : {
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
                                            <div className="text-center p-3 profile-title" style={{ marginTop: '150px' }}>{trans('reset-password')}</div>
                                            <div className="text-center text-828282 px-3 profile-description">
                                                {trans('recover-subtitle')}
                                            </div>
                                        </>
                                    )
                                    :
                                    (
                                        <>
                                            <div className="res-mobile">
                                                <div className="text-center font-bold p-3 profile-title"
                                                    style={{
                                                        marginTop: '150px',
                                                        fontWeight: "790",
                                                        fontSize: "24px",
                                                        lineHeight: "26.64px",
                                                        color: "#1E1E1E"
                                                    }}>{trans('reset-password')}</div>
                                                <div className="text-center text-828282 px-3 profile-description"
                                                    style={{
                                                        fontFamily: "SF Compact Display Medium",
                                                        fontWeight: "556",
                                                        fontSize: "18px",
                                                        lineHeight: "25.35px",
                                                        color: "#676767"
                                                    }}>
                                                    {trans('recover-subtitle')}
                                                </div>
                                            </div>
                                            <div className="res-desktop">
                                                <div className="text-center p-3 profile-title"
                                                    style={{
                                                        fontFamily: "SF Compact Display",
                                                        marginTop: '150px',
                                                        fontWeight: "790",
                                                        fontSize: "24px",
                                                        lineHeight: "26.64px",
                                                        color: "#1E1E1E"
                                                    }}>{trans('reset-password')}</div>
                                                <div className="text-center text-828282 px-3 profile-description"
                                                    style={{
                                                        fontFamily: "SF Compact Display",
                                                        fontWeight: "556",
                                                        fontSize: "18px",
                                                        lineHeight: "25.35px",
                                                        color: "#676767"
                                                    }}>
                                                    {trans('recover-subtitle')}
                                                </div>
                                            </div>
                                        </>
                                    )
                            }
                            <div className={`${style['menu-profile']} mb-0`}>
                                <div className={style['detail-profile']}>
                                    <ThemeProvider theme={theme}>
                                        <form onSubmit={formik.handleSubmit} method={'POST'}>
                                            <Grid container spacing={2} style={{ justifyContent: 'center', marginTop: '15px' }}>
                                                <Grid item xs={12}>
                                                    <TextField
                                                        className={`${style.texting} ${formik.touched.email && (Boolean(formik.errors.email) || (errorMessage)) && !isEmailValid ? invalid : ''}`}
                                                        fullWidth
                                                        id="email"
                                                        name="email"
                                                        placeholder={trans('email')}
                                                        variant="outlined"
                                                        value={formik.values.email}
                                                        onChange={() => { handleInputChange(event); setErrorMessage(null) ; setIsEmailValid(true); }}
                                                        onBlur={formik.handleBlur}
                                                        error={formik.touched.email && Boolean(formik.errors.email || (errorMessage))}
                                                        style={workspaceId ? {} : { border: '1px solid #CDCDCD !important', borderRadius: '6px !important' }}
                                                    />
                                                </Grid>

                                                {
                                                    workspaceId ?
                                                        (
                                                            <>
                                                                <Grid item xs={12} className={`d-flex justify-content-center`} style={{ margin: 'auto' }}>
                                                                    <Button type="submit" onClick={handleRegisterClick}>
                                                                        <div style={{ background: color }}
                                                                            className={`${style['save-button']} 
                                                                        ${Object.keys(formik.errors).length != 0 || !formik.values.email ? `${style['btn-disable']}` : ``}`}>
                                                                            {trans('reset-password')}
                                                                        </div>
                                                                    </Button>
                                                                </Grid>
                                                            </>
                                                        )
                                                        :
                                                        (
                                                            <>
                                                                <Grid item xs={12} className={`d-flex justify-content-center`} style={{ margin: 'auto', marginTop: '10px' }}>
                                                           
                                                                        <Button type="submit" onClick={handleRegisterClick}>
                                                                            <div style={{ backgroundColor: '#ABA765', borderRadius: '80px', margin: 'auto', width: '277px' }}
                                                                                className={`${style['save-button-portal']} 
                                                                        ${Object.keys(formik.errors).length != 0 || !formik.values.email ? `${style['btn-disable']}` : ``}`}>
                                                                                {trans('reset-password')}
                                                                            </div>
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
                            <div role="button"
                                style={{ color: color ?? '#ABA765' }}
                                onClick={() => handleClose()}
                                className={`${style['footer-login-text-desk']} ${style['footer-reset-text']} text-center px-3 text-uppercase`}>
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