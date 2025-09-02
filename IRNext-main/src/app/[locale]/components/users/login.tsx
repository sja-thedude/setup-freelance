'use client'

import style from 'public/assets/css/profile.module.scss'
import 'public/assets/css/popup.scss';
import React, { useState, useEffect, useRef } from 'react';
import { useI18n } from '@/locales/client';
import Cookies from 'js-cookie';
import { useRouter } from "next/navigation";
import { useLoginMutation } from '@/redux/services/authApi';
import 'react-toastify/dist/ReactToastify.css';
import { Button, Grid } from '@mui/material';
import { api } from "@/utils/axios";
import { Modal } from "react-bootstrap";
import { gapi } from "gapi-script";
import GoogleLogin from "react-google-login";
import FacebookLogin from "react-facebook-login/dist/facebook-login-render-props";
import AppleLogin from 'react-apple-login';
import Register from "./register";
import ResetPasswordStep1 from "./resetPasswordStep1";
import { useAppSelector } from '@/redux/hooks'
import { useGetWorkspaceDataByIdQuery } from '@/redux/services/workspace/workspaceDataApi'
import { useAppDispatch } from '@/redux/hooks'
import { addOpenLoginDesktop } from '@/redux/slices/cartSlice'
import { handleLoginToken } from "@/utils/axiosRefreshToken";
import { rootCartDeliveryOpen, addFormGroupOpen } from '@/redux/slices/cartSlice'
import { REGEX_NUMBER_CHECK } from '@/config/constants';

const forget = style['forget-password'];
const customInput = style['custom-input-login'];
const invalid = style['invalid'];
const invalidPortal = style['invalid-portal'];
const eye = style['eye'];

export default function Login({ togglePopup, from }: { togglePopup: any, from: any }) {
    const [show, setShow] = useState(false);
    const dispatch = useAppDispatch()
    const handleClose = () => {
        togglePopup();
        setShow(false);
        dispatch(addOpenLoginDesktop(false))
        const query = new URLSearchParams(window.location.search);
        if (query.get('login') === 'true') {
            if (window.location.href.includes('?login=true')) {
                history.pushState({}, "close login popup", window.location.href.replace('?login=true', ''))
            } else {
                history.pushState({}, "close login popup", window.location.href.replace('&login=true', ''))
            }
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

    const workspaceId = useAppSelector((state) => state.workspaceData.globalWorkspaceId)
    const { data: apiDataToken } = useGetWorkspaceDataByIdQuery({ id: workspaceId })
    const apiData = apiDataToken?.data?.setting_generals;
    const color = useAppSelector((state) => state.workspaceData.globalWorkspaceColor)
    const trans = useI18n()
    const [isPasswordVisible, setPasswordVisibility] = useState(false);
    const togglePasswordVisibility = () => {
        setPasswordVisibility(!isPasswordVisible);
    };
    const [isEmailValid, setEmailValid] = useState(true);
    const [isPasswordValid, setPasswordValid] = useState(true);
    const [errorMessage, setErrorMessage] = useState<string | null>(null);
    const [email, setEmail] = useState('');
    const [password, setPassword] = useState('');
    const [login, { isLoading: logLoading, isError: logError, error }] = useLoginMutation();
    const [loggedToken, setLoggedToken] = useState<string | undefined>(undefined);
    const [isVisible, setIsVisible] = useState(false);
    const [isRegisterOpen, setIsRegisterOpen] = useState<any | null>(false);
    const [isResetPasswordOpen, setIsResetPasswordOpen] = useState<any | null>(false);
    const togglePopupRegister = () => {
        setIsRegisterOpen(!isRegisterOpen);
    }
    const flagDesktopChangeType = useAppSelector<any>((state: any) => state.flagDesktopChangeType.data);
    const togglePopupResetPassword = () => {
        setIsResetPasswordOpen(!isResetPasswordOpen);
    }
    const typeBeforeChange: any = useAppSelector((state) => state.cart.typeBeforeChange);
    useEffect(() => {
        // check cookie 'isLoggedIn' khi component được tạo ra
        const tokenLoggedInCookie = Cookies.get('loggedToken');
        if (tokenLoggedInCookie) {
            setLoggedToken(tokenLoggedInCookie);
            router.push("/");
        }
    }, []);

    const handleEmailChange = (e: any) => {
        const { value } = e.target;
        const trimmedEmail = value.replace(/\s+/g, '');
        setEmail(trimmedEmail);
    };

    const handlePasswordChange = (e: any) => {
        const { value } = e.target;
        setPassword(value);
    };

    function checkEmailValid(email: string) {
        // Sử dụng regex để kiểm tra định dạng email
        const emailRegex = /^[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,}$/i;
        return emailRegex.test(email);
    }

    // dont declare event handler (to fix, move it outside!)
    const router = useRouter()
    const handleLoginClick = async () => {
        setErrorMessage(null);
        // check email and password
        const isEmailEmpty = email.trim() === '';
        const isPasswordEmpty = password.trim() === '';
        // Set state isEmailValid and isPasswordValid
        setEmailValid(!isEmailEmpty);
        setPasswordValid(!isPasswordEmpty);

        if (isEmailEmpty || isPasswordEmpty) {
            setErrorMessage(trans('missing-fields'));
        }

        if (checkEmailValid(email)) {
            try {
                // Call the login API with email and password information
                const response = await login({ email, password });
                // Check if there is data in the response before accessing it
                if ('data' in response) {
                    const userData = response?.data?.data;

                    dispatch(addOpenLoginDesktop(false))
                    Cookies.set('fromDesk', from);

                    // Set cookie 'loggedToken' with value 'token'
                    handleLoginToken(userData.token);
                    handleClose();

                    // Check if there is phonenumber and First name in userData
                    // if (!userData['first_name'] || userData['first_name'].includes('@')) {
                    //     const query = new URLSearchParams(window.location.search);
                    //     if (query.size > 0) {
                    //         window.location.href = window.location.href + "&editProfile=true";
                    //     } else {
                    //         window.location.href = window.location.href + "?editProfile=true";
                    //     }
                    // } else if (!userData.gsm) {
                    //     const query = new URLSearchParams(window.location.search);
                    //     if (query.size > 0) {
                    //         window.location.href = window.location.href + "&editProfile=true";
                    //     } else {
                    //         window.location.href = window.location.href + "?editProfile=true";
                    //     }
                    // } else {
                        const query = new URLSearchParams(window.location.search);
                        if (query.get('account') === 'true') {
                            router.push("/profile/show");
                        } else if (query.get('recent')) {
                            router.push("/function/recent");
                        } else if (query.get('favorites')) {
                            router.push("/category/products?liked=true");
                        } else if (query.get('product_suggestion')) {
                            router.push("/table-ordering/cart?open=true");
                        } else if (query.get('group-order')) {
                            Cookies.set('groupOrder', 'true');
                            router.back();
                        } else if (query.get('loyalties') === 'true') {
                            router.push("/loyalties");
                        } else if (query.get('categoryCart') === 'true') {
                            router.push("/category/cart?openSuggest=true");
                        }
                        else {
                            if (!Cookies.get('groupOrderDesktop')) {
                                window.location.reload();
                            }
                        }
                    // }
                } else if ('error' in response) {
                    if ('data' in response.error) {
                        const errorData = response.error.data as { message: string } | undefined;

                        if (errorData) {
                            setErrorMessage(errorData.message);
                            setIsVisible(true);

                            if (response.error.status == 401) {
                                setEmailValid(false);
                                setPasswordValid(false);
                            } else {
                                setEmailValid(false);
                            }
                        }
                    }
                }
            } catch (error) {
                // console.log(error)
                //Handle errors from the API here (error)
            }
        } else {
            setEmailValid(false);

            if (email) {
                setErrorMessage(trans('format-email'));
            }
        }
    };

    // handle submit
    const handleSubmit = (event: any) => {
        event.preventDefault();

        if (!isEmailValid) {
            setEmailValid(false);
        }

        if (!isPasswordValid) {
            setPasswordValid(false);
        }
    };

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

    const responseFacebook = (response: any) => {
        responseLoginToken(response, 'facebook');
    };

    const responseApple = (response: any) => {
        responseLoginToken(response, 'apple');
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
                window.location.reload();
            }
        }).catch(err => {
            // console.log(err);
        });
    }

    const onFailure = (response: any) => {
        // console.log('FAILED', response);
    };

    const handleToping = () => {
        if (flagDesktopChangeType && errorMessage) {
            return '100px';
        } else {
            return '0px';
        }
    }

    const handleGrouping = () => {
        if (Cookies.get('groupOrderDesktop') == 'true') {
            dispatch(rootCartDeliveryOpen(false));
            // dispatch(manualChangeOrderTypeDesktop(false))
            dispatch(addFormGroupOpen(false));
            Cookies.remove('groupOrderDesktop');
        }
        Cookies.remove('step2GroupDesk');
    }
    return (
        <>
            <Button onClick={handleShow} style={{ display: 'none' }}></Button>

            <Modal show={show} onHide={handleClose}
                animation={false}
                id='modal-profile'
            >
                <Modal.Body>
                    <div className="res-mobile">
                        {
                            workspaceId ?
                                (
                                    <>
                                        <div className="close-popup" onClick={() => handleClose()} >
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="17" viewBox="0 0 16 17" fill="none" style={{ marginTop: '1px' }}>
                                                <path d="M12 4.2168L4 12.2168" stroke="#888888" strokeWidth="3" strokeLinecap="round" strokeLinejoin="round" />
                                                <path d="M4 4.2168L12 12.2168" stroke="#888888" strokeWidth="3" strokeLinecap="round" strokeLinejoin="round" />
                                            </svg>
                                            <div className="ms-1">{trans('close')}</div>
                                        </div>
                                    </>
                                ) :
                                (
                                    <>
                                        <div className="close-popup text-828282" onClick={() => handleClose()}
                                            style={workspaceId ? {} : {
                                                fontFamily: "SF Compact Display",
                                                fontSize: '16px',
                                                fontStyle: 'normal',
                                                fontWeight: '790',
                                                lineHeight: 'normal',
                                                letterSpacing: '1.44px',
                                            }}>
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="25" viewBox="0 0 24 25" fill="none">
                                                <path d="M14 17L10 12.5L14 8" stroke="#676767" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" />
                                            </svg>
                                            <div className="mt-1">{trans('back')}</div>
                                        </div>
                                    </>
                                )
                        }
                        {errorMessage && (
                            <div className={`px-3`}>
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
                                        <div className="text-center px-3 pt-3 profile-title"
                                            style={errorMessage ? { marginTop: '10px' } : { marginTop: '100px' }}>
                                            {trans('welcome')}
                                            <span style={{ color: color ?? '#ABA765' }}>
                                                {workspaceId ? ' ' + apiData?.title : ' ' + 'It’s Ready'}
                                            </span>
                                        </div>
                                        <div className="text-center px-5 pt-3 profile-description">
                                            {trans('sub-login-title-1')}
                                            <span style={{ color: color ?? '#ABA765' }} onClick={() => { window.open('https://b2b.itsready.be/', '_blank') }} role='button'>{trans('sub-login-title-2')}</span>
                                            {trans('sub-login-title-3')}!
                                        </div>
                                    </>
                                )
                                :
                                (
                                    <>
                                        <div className="text-center px-3 pt-1 profile-title font-bold"
                                            style={{ color: "#1E1E1E", fontWeight: "790", fontSize: "24px", lineHeight: "28.54px" }}>
                                            {trans('welcome')}
                                            <span style={{ color: color ?? '#ABA765' }}>
                                                {workspaceId ? ' ' + apiData?.title : ' ' + 'It’s Ready'}
                                            </span>
                                        </div>
                                        <div className="text-center px-5 pt-1 profile-description"
                                            style={{ fontFamily: "SF Compact Display Medium", fontWeight: "556", fontSize: "18px", lineHeight: "25.35px", color: "#676767" }}>
                                            {trans('sub-login-title-1')}
                                            <span style={{ color: color ?? '#ABA765' }} onClick={() => { window.open('https://b2b.itsready.be/', '_blank') }} role='button'>{trans('sub-login-title-2')}</span>
                                            {trans('sub-login-title-3')}!
                                        </div>
                                    </>
                                )
                        }
                    </div>
                    <div className="res-desktop" style={{ marginTop: handleToping() }}>
                        {
                            workspaceId ?
                                (
                                    <>
                                        <div className="close-popup" onClick={() => { handleClose(); handleGrouping() }} style={{ position: flagDesktopChangeType ? 'absolute' : 'relative', top: flagDesktopChangeType ? '75px' : '', left: flagDesktopChangeType && window.innerWidth < 1500 ? '20px' : '' }}>
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="17" viewBox="0 0 16 17" fill="none" style={{ marginTop: '1px' }}>
                                                <path d="M12 4.2168L4 12.2168" stroke="#888888" strokeWidth="3" strokeLinecap="round" strokeLinejoin="round" />
                                                <path d="M4 4.2168L12 12.2168" stroke="#888888" strokeWidth="3" strokeLinecap="round" strokeLinejoin="round" />
                                            </svg>
                                            <div className="ms-1">{trans('close')}</div>
                                        </div>
                                    </>
                                ) :
                                (
                                    <>
                                        <div className="close-popup" onClick={() => handleClose()}>
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="17" viewBox="0 0 16 17" fill="none" style={{ marginTop: '1px' }}>
                                                <path d="M12 4.3479L4 12.3479" stroke="#676767" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" />
                                                <path d="M4 4.3479L12 12.3479" stroke="#676767" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" />
                                            </svg>
                                            <div className="ms-1"
                                                style={{
                                                    color: '#676767',
                                                    fontFamily: "SF Compact Display",
                                                    fontSize: '16px',
                                                    fontStyle: 'normal',
                                                    fontWeight: '790',
                                                    lineHeight: 'normal',
                                                    letterSpacing: '1.44px',
                                                }}
                                            >{trans('close')}</div>
                                        </div>
                                    </>
                                )
                        }
                        {errorMessage && (
                            <div className={`px-3`}>
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
                                        <div className="text-center px-3 pt-3 profile-title"
                                            style={flagDesktopChangeType ? (errorMessage ? { marginTop: '10px' } : { marginTop: '100px' }) : {}}>
                                            {trans('welcome')}
                                            <span style={{ color: color ?? '#ABA765' }}>
                                                {workspaceId ? ' ' + apiData?.title : ' ' + 'It’s Ready'}
                                            </span>
                                        </div>
                                        <div className="text-center px-5 pt-3 profile-description">
                                            {trans('sub-login-title-1')}
                                            <span style={{ color: color ?? '#ABA765', cursor: 'pointer' }} onClick={() => { window.open('https://b2b.itsready.be/', '_blank') }} role='button'>{trans('sub-login-title-2')}</span>
                                            {trans('sub-login-title-3')}!
                                        </div>
                                    </>
                                )
                                :
                                (
                                    <>
                                        <div className="text-center px-3 pt-3 profile-title"
                                            style={errorMessage ?
                                                { color: "#1E1E1E", marginTop: flagDesktopChangeType ? '10px' : 'auto', fontFamily: "SF Compact Display", fontWeight: "790", fontSize: "24px", lineHeight: "28.54px", }
                                                : { color: "#1E1E1E", marginTop: flagDesktopChangeType ? '100px' : 'auto', fontFamily: "SF Compact Display", fontWeight: "790", fontSize: "24px", lineHeight: "28.54px" }}>
                                            {trans('welcome')}
                                            <span style={{ color: color ?? '#ABA765' }}>
                                                {workspaceId ? ' ' + apiData?.title : ' ' + 'It’s Ready'}
                                            </span>
                                        </div>
                                        <div className="text-center px-5 pt-3 profile-description"
                                            style={{ fontFamily: "SF Compact Display", fontWeight: "556", fontSize: "18px", lineHeight: "25.35px", color: "#676767" }}>
                                            {trans('sub-login-title-1')}
                                            <span style={{ color: color ?? '#ABA765' }} onClick={() => { window.open('https://b2b.itsready.be/', '_blank') }} role='button'>{trans('sub-login-title-2')}</span>
                                            {trans('sub-login-title-3')}!
                                        </div>
                                    </>
                                )
                        }
                    </div>
                    <div className={`mt-0 ${style['menu-profile']} ${style['menu-profile-popup']}`}>
                        <div className={style['detail-profile']}>
                            <form onSubmit={handleSubmit}>
                                <div className="form-group">
                                    <input
                                        type="text"
                                        className={`${customInput} ${isEmailValid ? '' : (workspaceId ? invalid : invalidPortal)} form-control`}
                                        id="email"
                                        placeholder={trans('email-field')}
                                        onChange={handleEmailChange}
                                        onKeyUp={() => { setEmailValid(true) }}
                                        style={workspaceId ? {} : { border: '1px solid #CDCDCD', borderRadius: '6px' }}
                                    />
                                </div>
                                <div className="form-group" style={{ position: 'relative' }}>
                                    <input
                                        type={isPasswordVisible ? 'text' : 'password'}
                                        className={`${customInput} ${isPasswordValid ? '' : (workspaceId ? invalid : invalidPortal)} form-control`}
                                        id="password"
                                        placeholder={trans('password')}
                                        onChange={handlePasswordChange}
                                        onKeyUp={() => { setPasswordValid(true) }}
                                        style={workspaceId ? {} : { border: '1px solid #CDCDCD', borderRadius: '6px' }}
                                    />
                                    <div
                                        className={`${eye}`}
                                        onClick={togglePasswordVisibility}
                                    >
                                        {
                                            isPasswordVisible
                                                ? <svg xmlns="http://www.w3.org/2000/svg" width="24" height="25" viewBox="0 0 24 25" fill="none">
                                                    <path d="M1 12.6473C1 12.6473 5 4.68262 12 4.68262C19 4.68262 23 12.6473 23 12.6473C23 12.6473 19 20.612 12 20.612C5 20.612 1 12.6473 1 12.6473Z" stroke={isPasswordValid ? "#888888" : "#D94B2C"} strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" />
                                                    <path d="M12 15.6342C13.6569 15.6342 15 14.297 15 12.6474C15 10.9979 13.6569 9.66064 12 9.66064C10.3431 9.66064 9 10.9979 9 12.6474C9 14.297 10.3431 15.6342 12 15.6342Z" stroke={isPasswordValid ? "#888888" : "#D94B2C"} strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" />
                                                    <line x1={5.378} y1={1.318} x2={19.318} y2={23.622} stroke={isPasswordValid ? "#888888" : "#D94B2C"} strokeWidth={2} strokeLinecap='round' strokeLinejoin='round' />
                                                </svg>
                                                : <svg xmlns="http://www.w3.org/2000/svg" width="24" height="25" viewBox="0 0 24 25" fill="none">
                                                    <path d="M1 12.6473C1 12.6473 5 4.68262 12 4.68262C19 4.68262 23 12.6473 23 12.6473C23 12.6473 19 20.612 12 20.612C5 20.612 1 12.6473 1 12.6473Z" stroke={isPasswordValid ? "#888888" : "#D94B2C"} strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" />
                                                    <path d="M12 15.6342C13.6569 15.6342 15 14.297 15 12.6474C15 10.9979 13.6569 9.66064 12 9.66064C10.3431 9.66064 9 10.9979 9 12.6474C9 14.297 10.3431 15.6342 12 15.6342Z" stroke={isPasswordValid ? "#888888" : "#D94B2C"} strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" />
                                                </svg>
                                        }

                                    </div>
                                </div>
                                <div className="row">
                                    <div className="col-sm-12 col-12 text-center">
                                        {workspaceId ? (
                                            <div className={forget} role="button" onClick={togglePopupResetPassword}
                                                style={{
                                                    textTransform: 'uppercase'
                                                }}>
                                                {trans('forget-password')}
                                            </div>
                                        ) : (
                                            <div className={forget} role="button" onClick={togglePopupResetPassword}
                                                style={{
                                                    marginTop: '10px',
                                                    fontFamily: 'SF Compact Display',
                                                    fontWeight: '556px',
                                                    fontSize: '14px',
                                                    lineHeight: '16.71px',
                                                    color: '#676767',
                                                }}>
                                                {trans('forget-password')}
                                            </div>
                                        )}
                                    </div>

                                    { workspaceId ? (
                                        <div className="col-sm-12 col-12 text-center">
                                            <button type="submit" style={{ backgroundColor: color, textTransform: 'uppercase' }} className={`${style['login-button']}`} onClick={handleLoginClick}>{trans('login-nl')}</button>
                                        </div>
                                    ) : (
                                        <div className="col-sm-12 col-12 text-center" style={{ padding: '15px 30px', gap: '10px', fontFamily: 'SF Compact Display', fontWeight: '556', fontSize: '20px', lineHeight: '23.87px' }}>
                                            <button type="submit" style={{ backgroundColor: '#ABA765', borderRadius: '80px', margin: 'auto', width: '153px', color: '#FFFFFF' }} className={`${style['login-button']}`} onClick={handleLoginClick}>{trans('login-nl')}</button>
                                        </div>
                                    )}
                                </div>
                            </form>
                            <div role="button" onClick={togglePopupRegister} style={{ color: color ?? '#ABA765', whiteSpace: 'pre-line' }}
                                className={`row justify-content-center ${style['footer-login-text-desk']} text-uppercase`}>
                                {trans('register')}
                            </div>
                            <div style={{ position: "relative" }}>
                                <div className={`${style['line-break']}`} style={workspaceId ? {} : { border: '1px solid #1E1E1E' }}>
                                </div>
                                <div className={`${style['text-break']}`}>
                                    {trans('of')}
                                </div>
                            </div>

                            <div style={
                                workspaceId ?
                                    { marginTop: '40px' }
                                    :
                                    {
                                        marginTop: '40px',
                                        fontFamily: 'SF Compact Display',
                                        fontWeight: '790',
                                        fontSize: "16px",
                                        lineHeight: "19.09px",
                                        color: "#4F4F4F"
                                    }

                            }>
                                {(apiDataToken?.data?.google_enabled > 0 || !workspaceId) && (
                                    <GoogleLogin
                                        clientId={process.env.NEXT_PUBLIC_GOOGLE_CLIENT_ID ?? ''}
                                        render={renderProps => (
                                            <div onClick={renderProps.onClick}
                                                className={`d-flex align-items-center cursor-pointer ${style['social-login-btn']}`} style={workspaceId ? {} : { border: '2px solid #4F4F4F' }}>
                                                <svg xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" width="50" height="50" viewBox="0 0 48 48">
                                                    <path fill="#FFC107" d="M43.611,20.083H42V20H24v8h11.303c-1.649,4.657-6.08,8-11.303,8c-6.627,0-12-5.373-12-12c0-6.627,5.373-12,12-12c3.059,0,5.842,1.154,7.961,3.039l5.657-5.657C34.046,6.053,29.268,4,24,4C12.955,4,4,12.955,4,24c0,11.045,8.955,20,20,20c11.045,0,20-8.955,20-20C44,22.659,43.862,21.35,43.611,20.083z"></path><path fill="#FF3D00" d="M6.306,14.691l6.571,4.819C14.655,15.108,18.961,12,24,12c3.059,0,5.842,1.154,7.961,3.039l5.657-5.657C34.046,6.053,29.268,4,24,4C16.318,4,9.656,8.337,6.306,14.691z"></path><path fill="#4CAF50" d="M24,44c5.166,0,9.86-1.977,13.409-5.192l-6.19-5.238C29.211,35.091,26.715,36,24,36c-5.202,0-9.619-3.317-11.283-7.946l-6.522,5.025C9.505,39.556,16.227,44,24,44z"></path><path fill="#1976D2" d="M43.611,20.083H42V20H24v8h11.303c-0.792,2.237-2.231,4.166-4.087,5.571c0.001-0.001,0.002-0.001,0.003-0.002l6.19,5.238C36.971,39.205,44,34,44,24C44,22.659,43.862,21.35,43.611,20.083z"></path>
                                                </svg>
                                                <div className={`${style['social-login-text']}`} role="button" style={workspaceId ? {} : { color: '#4F4F4F' }}>
                                                    {trans('login-with') + ' Google'}
                                                </div>
                                            </div>
                                        )}
                                        buttonText="Login"
                                        onSuccess={onSuccess}
                                        onFailure={onFailure}
                                        cookiePolicy={'single_host_origin'}
                                    />)}

                                {(apiDataToken?.data?.facebook_enabled > 0 || !workspaceId) && (
                                    <FacebookLogin
                                        appId={process.env.NEXT_PUBLIC_FACEBOOK_APP_ID}
                                        callback={responseFacebook}
                                        isMobile={false}
                                        render={(renderProps: any) => (
                                            <div onClick={renderProps.onClick}
                                                className={`d-flex align-items-center cursor-pointer ${style['social-login-btn']}`} style={workspaceId ? {} : { border: '2px solid #4F4F4F' }}>
                                                <svg xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" width="50" height="50" viewBox="0 0 48 48">
                                                    <linearGradient id="Ld6sqrtcxMyckEl6xeDdMa_uLWV5A9vXIPu_gr1" x1="9.993" x2="40.615" y1="9.993" y2="40.615" gradientUnits="userSpaceOnUse">
                                                        <stop offset="0" stopColor="#2aa4f4"></stop>
                                                        <stop offset="1" stopColor="#007ad9"></stop>
                                                    </linearGradient>
                                                    <path fill="url(#Ld6sqrtcxMyckEl6xeDdMa_uLWV5A9vXIPu_gr1)" d="M24,4C12.954,4,4,12.954,4,24s8.954,20,20,20s20-8.954,20-20S35.046,4,24,4z"></path>
                                                    <path fill="#fff" d="M26.707,29.301h5.176l0.813-5.258h-5.989v-2.874c0-2.184,0.714-4.121,2.757-4.121h3.283V12.46 c-0.577-0.078-1.797-0.248-4.102-0.248c-4.814,0-7.636,2.542-7.636,8.334v3.498H16.06v5.258h4.948v14.452 C21.988,43.9,22.981,44,24,44c0.921,0,1.82-0.084,2.707-0.204V29.301z"></path>
                                                </svg>
                                                <div className={`${style['social-login-text']}`} role="button" style={workspaceId ? {} : { color: '#4F4F4F' }}>
                                                    {trans('login-with') + ' Facebook'}
                                                </div>
                                            </div>
                                        )}
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
                                                className={`d-flex align-items-center cursor-pointer ${style['social-login-btn']}`} style={workspaceId ? {} : { border: '2px solid #4F4F4F' }}>
                                                <svg xmlns="http://www.w3.org/2000/svg" width="50" height="50" viewBox="0 0 33 41" fill="none">
                                                    <g clipPath="url(#clip0_5618_3513)">
                                                        <path d="M31.524 13.8801C31.292 14.0601 27.196 16.3681 27.196 21.5001C27.196 27.4361 32.408 29.5361 32.564 29.5881C32.54 29.7161 31.736 32.4641 29.816 35.2641C28.104 37.7281 26.316 40.1881 23.596 40.1881C20.876 40.1881 20.176 38.6081 17.036 38.6081C13.976 38.6081 12.888 40.2401 10.4 40.2401C7.912 40.2401 6.176 37.9601 4.18 35.1601C1.868 31.8721 0 26.7641 0 21.9161C0 14.1401 5.056 10.0161 10.032 10.0161C12.676 10.0161 14.88 11.7521 16.54 11.7521C18.12 11.7521 20.584 9.91214 23.592 9.91214C24.732 9.91214 28.828 10.0161 31.524 13.8801ZM22.164 6.62014C23.408 5.14414 24.288 3.09614 24.288 1.04814C24.288 0.764141 24.264 0.476141 24.212 0.244141C22.188 0.320141 19.78 1.59214 18.328 3.27614C17.188 4.57214 16.124 6.62014 16.124 8.69614C16.124 9.00814 16.176 9.32014 16.2 9.42014C16.328 9.44414 16.536 9.47214 16.744 9.47214C18.56 9.47214 20.844 8.25614 22.164 6.62014Z" fill="black" />
                                                    </g>
                                                    <defs>
                                                        <clipPath id="clip0_5618_3513">
                                                            <rect width="32.56" height="40" fill="white" transform="translate(0 0.244141)" />
                                                        </clipPath>
                                                    </defs>
                                                </svg>
                                                <div className={`${style['social-login-text']}`} role="button" style={workspaceId ? {} : { color: '#4F4F4F' }}>
                                                    {trans('login-with') + ' Apple'}
                                                </div>
                                            </div>
                                        )}
                                    />)}
                            </div>
                        </div>
                    </div>                    
                </Modal.Body>
            </Modal>
            <style>{`
                .MuiButtonBase-root {
                    width: 100%!important;
                    padding: 0px!important;
                }`}
            </style>
            {isRegisterOpen && <Register togglePopup={() => togglePopupRegister()} />}
            {isResetPasswordOpen && <ResetPasswordStep1 togglePopup={() => togglePopupResetPassword()} />}
        </>
    );
}