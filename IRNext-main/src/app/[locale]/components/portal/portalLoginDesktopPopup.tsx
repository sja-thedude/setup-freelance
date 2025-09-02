'use client'
import React, { useState, useEffect } from 'react';
import { useLoginMutation } from '@/redux/services/authApi';
import { useI18n } from '@/locales/client'
import variables from '/public/assets/css/login.module.scss'
import 'public/assets/css/popup.scss';
import { useRouter } from 'next/navigation'
import Cookies from 'js-cookie';
import GoogleLogin from 'react-google-login';
import { gapi } from 'gapi-script';
import { api } from "@/utils/axios";
import FacebookLogin from 'react-facebook-login/dist/facebook-login-render-props';
import AppleLogin from 'react-apple-login';
import { useAppSelector } from '@/redux/hooks'
import { Modal } from 'react-bootstrap';
import RegisterReady from '@/app/[locale]/components/layouts/popup/registerReady';
import PortalRegister from '@/app/[locale]/components/layouts/popup/portalRegister';
import {handleLoginToken} from "@/utils/axiosRefreshToken";
import { REGEX_NUMBER_CHECK } from '@/config/constants';

const welcome_portal = variables['welcome-portal'];
const loginWith = variables['login-with'];
const forget = variables['forget-password'];
const customInput = variables['custom-input'];
const invalidPortalLoginDesktop = variables['invalid-portal-login-desktop'];
const line = variables['line'];
const dashLinePortalLogin = variables['dash-line-portal-login'];
const eye = variables['eye'];
const errorMessageStyling = variables['portal-login-error-message'];

export default function PortalLoginDesktopPopup({ getToggleLoginPopUp, setToggleLoginPopUp }: { getToggleLoginPopUp?: any, setToggleLoginPopUp?: any }) {
    // Trong functional component
    const workspaceId = useAppSelector((state) => state.workspaceData.globalWorkspaceId)
    const trans = useI18n()
    const [isPasswordVisible, setPasswordVisibility] = useState(false);
    const [showBack, setShowBack] = useState(false);
    const [onclickBack, setOnclickBack] = useState(false);
    const [isPasswordFilled, setPasswordFilled] = useState(false);

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
    const [rememberMe, setRememberMe] = useState(false);
    const handleRememberMeChange = (e:any) => setRememberMe(e.target.checked);

    useEffect(() => {
        const query = new URLSearchParams(window.location.search);
        if (query.get('loyalties') === 'true') {
            setShowBack(true);
        }

        if (showBack && onclickBack) {
            if (query.get('loyalties') === 'true') {
                history.back();
            }
        }
    }, [onclickBack]);

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
        setEmail(value);
    };

    const handlePasswordChange = (e: any) => {
        const { value } = e.target;
        setPassword(value);
        setPasswordFilled(value.length > 0); // Update based on whether input is filled
    };

    function checkEmailValid(email: string) {
        // Sử dụng regex để kiểm tra định dạng email
        const emailRegex = /^[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,}$/i;
        return emailRegex.test(email);
    }

    // dont declare event handler (to fix, move it outside!)
    const router = useRouter()
    const handleLoginClick = async () => {
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

                    // Set cookie 'loggedToken' with value 'token'
                    handleLoginToken(userData.token);

                    // Check if there is phonenumber and First name in userData
                    if (!userData['first_name'] || userData['first_name'].includes('@')) {
                        window.location.reload();
                    } else if (!userData.gsm) {
                        window.location.reload();
                    } else {
                        const query = new URLSearchParams(window.location.search);
                        if (query.get('account') === 'true') {
                            window.location.reload();
                        } else {
                            window.location.reload();
                        }
                    }
                } else if ('error' in response) {
                    if ('data' in response.error) {
                        const errorData = response.error.data as { message: string } | undefined;
                        if (errorData) {
                            setErrorMessage(errorData.message);
                            setIsVisible(true);
                            if (response.error.status == 401) {
                                setEmailValid(false);
                                setPasswordValid(false);
                            } else if (response.error.status == 500) {
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
        responseLoginToken({
            accessToken: response?.authorization?.id_token
        }, 'apple');
    };

    const responseLoginToken = (response: any, provider: string) => {
        let data = api.post(`login/social`, {
            'provider': provider,
            'access_token': response?.accessToken,
            'workspace_id': workspaceId,
        }).then(res => {
            const userData = res.data.data;

            // Set cookie 'loggedToken' with value 'token'
            handleLoginToken(userData.token);

            if (userData?.first_login && (userData.first_name.includes('@') || REGEX_NUMBER_CHECK.test(userData.first_name) || !userData.gsm)) {
                window.location.reload();
            } else {
                const query = new URLSearchParams(window.location.search);
                if (query.get('account') === 'true') {
                    window.location.reload();
                } else {
                    window.location.reload();
                }
            }
        }).catch(err => {
            // console.log(err);
        });
    }

    const onFailure = (response: any) => {
        // console.log('FAILED', response);
    };

    const [showModal, setShowModal] = useState(false);
    const handleCloseModal = () => {
        setToggleLoginPopUp(false);
        setShowModal(false);
    }

    useEffect(() => {
        if (getToggleLoginPopUp) {
            setShowModal(true);
        }
    }, []);

    const [isPopupOpen, setIsPopupOpen] = useState(false);

    const togglePopup = () => {
        setIsPopupOpen(!isPopupOpen);
        setShowModal(false);
    };

    const [isReadyPopupOpen, setIsReadyPopupOpen] = useState(false);
    const toggleReadyPopup = () => {
        setIsReadyPopupOpen(!isReadyPopupOpen);
    }

    const setToggleLoginPopUpHi = () => {
        setShowModal(true);
    }

    useEffect(() => {
        if (showModal) {
            setShowModal(true);
        }
    }, [showModal]);

    return (
        <>
            {
                <Modal aria-labelledby="contained-modal-title-vcenter"
                    show={showModal} onHide={handleCloseModal} centered
                    id="portal-login-modal" className="portal-login-modal"
                >
                    <div className={`${variables.login} login-portal row justify-content-center`}>
                        <div style={{ display: 'flex', justifyContent: 'flex-end', cursor: 'pointer', pointerEvents: 'auto', marginTop: '10px' }}>
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" onClick={handleCloseModal}>
                                <path d="M18 6L6 18" stroke="black" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" />
                                <path d="M6 6L18 18" stroke="black" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" />
                            </svg>
                        </div>
                        <div className="col-8 text-center" style={{ marginTop: '-40px' }}>
                            <h3 className={welcome_portal}>{trans('lang_login')}</h3>
                        </div>
                        <div className="login-portal-fields">
                            <form onSubmit={handleSubmit}>
                                <div className={`${variables.input} email-input-portal`}>
                                    <input
                                        type="text"
                                        className={`${customInput} ${isEmailValid ? '' : invalidPortalLoginDesktop} form-control`}
                                        id="email"
                                        placeholder={trans('email-field')}
                                        style={{
                                            height: '50px',
                                            flex: 1,
                                            marginRight: '0',
                                            outline: 'none',
                                            color: '#8898AA'
                                        }}
                                        onChange={handleEmailChange}
                                        onKeyUp={() => { setEmailValid(true) }}
                                    />
                                </div>
                                <div>
                                    <div className="input-group password-input-portal">
                                        <input
                                            type={isPasswordVisible ? 'text' : 'password'}
                                            className={`${customInput} ${isPasswordValid ? '' : invalidPortalLoginDesktop} ${isPasswordValid ? '' : 'password-input-portal-error'} form-control`}
                                            id="password"
                                            placeholder={trans('password')}
                                            style={{ height: '50px', flex: 1, marginRight: '0', borderRight: 'none', boxShadow: 'none' }}
                                            onChange={handlePasswordChange}
                                            onKeyUp={() => { setPasswordValid(true) }}
                                        />
                                        {
                                            isPasswordFilled && (
                                                <button
                                                    className={`${eye} ${isPasswordValid ? '' : invalidPortalLoginDesktop} btn`}
                                                    type="button"
                                                    onClick={togglePasswordVisibility}
                                                    style={{
                                                        backgroundColor: 'white',
                                                        borderLeft: 'none',
                                                        minWidth: '40px',
                                                        borderBottomRightRadius: '10px',
                                                        borderTopRightRadius: '10px',
                                                    }}
                                                >
                                                    {isPasswordVisible
                                                        ? (
                                                            <svg width="24" height="25" viewBox="0 0 24 25" fill="none" xmlns="http://www.w3.org/2000/svg" >
                                                                <path d="M1 12.4999C1 12.4999 5 4.83325 12 4.83325C19 4.83325 23 12.4999 23 12.4999C23 12.4999 19 20.1666 12 20.1666C5 20.1666 1 12.4999 1 12.4999Z" stroke="#BDBDBD" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" />
                                                                <path d="M12 15.375C13.6569 15.375 15 14.0878 15 12.5C15 10.9122 13.6569 9.625 12 9.625C10.3431 9.625 9 10.9122 9 12.5C9 14.0878 10.3431 15.375 12 15.375Z" stroke="#BDBDBD" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" />
                                                                <line x1="5.378" y1="1.318" x2="19.318" y2="23.622" stroke="#BDBDBD" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" />
                                                            </svg>
                                                        )
                                                        : (
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" >
                                                                <path d="M1 12C1 12 5 4 12 4C19 4 23 12 23 12C23 12 19 20 12 20C5 20 1 12 1 12Z" stroke={isPasswordValid ? "black" : "#D94B2C"} strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" />
                                                                <path d="M12 15C13.6569 15 15 13.6569 15 12C15 10.3431 13.6569 9 12 9C10.3431 9 9 10.3431 9 12C9 13.6569 10.3431 15 12 15Z" stroke={isPasswordValid ? "black" : "#D94B2C"} strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" />
                                                            </svg>
                                                        )
                                                    }
                                                </button>
                                            )
                                        }
                                    </div>
                                </div>
                                <div className="row mt-4">
                                    <div className='d-flex justify-content-start' style={{ width: '45%' }}>
                                        <input
                                            type="checkbox"
                                            checked={rememberMe}
                                            onChange={handleRememberMeChange}
                                            id="rememberMeCheckbox"
                                            style={{ marginRight: '10px' }}
                                        />
                                        <a className={forget} style={{ textDecoration: 'none', color: '#8898AA', fontFamily: 'Open Sans' }}>Onthoud mij</a>
                                    </div>
                                    <div className='d-flex justify-content-end' style={{ width: '55%' }}>
                                        <a className={forget} href="#" style={{ textDecoration: 'none', color: '#ADB5BD', fontFamily: 'Open Sans' }}>{trans('forget-password')}</a>
                                    </div>
                                </div>
                                <div className="row justify-content-center mt-4" style={{ padding: '0 20px' }}>
                                    <div className='d-flex justify-content-center' style={{ width: '50%' }}>
                                        <button type="submit" className='btn login-button-portal' onClick={handleLoginClick}>Inloggen</button>
                                    </div>
                                    <div className='d-flex justify-content-center' style={{ width: '50%' }}>
                                        <div className='btn register-button-portal' onClick={togglePopup} style={{ textDecoration: 'none', color: 'white' }}>Registreren</div>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div className={`${line} ps-4 pe-4 text-center`} >
                            <span className={`${dashLinePortalLogin}`} />&nbsp;&nbsp;
                            <span style={{ marginTop: '5px', color: '#8898AA' }}>OF</span>
                            &nbsp;&nbsp;<span className={`${dashLinePortalLogin}`} />
                        </div>
                        <div className={`${loginWith} text-center`} style={{ color: '#8898AA', marginTop: '-10px' }}>{trans('login-with')}</div>
                        <div className={`${variables.socialMedia} d-flex`} style={{ marginTop: '-10px' }}>
                            <GoogleLogin
                                clientId={process.env.NEXT_PUBLIC_GOOGLE_CLIENT_ID ?? ''}
                                render={renderProps => (
                                    <div onClick={renderProps.onClick} className='col-sm-3 col-3 d-grid justify-content-center'
                                        style={{ borderRadius: '50%', width: '50px', height: '50px', cursor: 'pointer' }}>
                                        <svg xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" width="50" height="50" viewBox="0 0 48 48" style={{ margin: 'auto' }}>
                                            <path fill="#FFC107" d="M43.611,20.083H42V20H24v8h11.303c-1.649,4.657-6.08,8-11.303,8c-6.627,0-12-5.373-12-12c0-6.627,5.373-12,12-12c3.059,0,5.842,1.154,7.961,3.039l5.657-5.657C34.046,6.053,29.268,4,24,4C12.955,4,4,12.955,4,24c0,11.045,8.955,20,20,20c11.045,0,20-8.955,20-20C44,22.659,43.862,21.35,43.611,20.083z"></path><path fill="#FF3D00" d="M6.306,14.691l6.571,4.819C14.655,15.108,18.961,12,24,12c3.059,0,5.842,1.154,7.961,3.039l5.657-5.657C34.046,6.053,29.268,4,24,4C16.318,4,9.656,8.337,6.306,14.691z"></path><path fill="#4CAF50" d="M24,44c5.166,0,9.86-1.977,13.409-5.192l-6.19-5.238C29.211,35.091,26.715,36,24,36c-5.202,0-9.619-3.317-11.283-7.946l-6.522,5.025C9.505,39.556,16.227,44,24,44z"></path><path fill="#1976D2" d="M43.611,20.083H42V20H24v8h11.303c-0.792,2.237-2.231,4.166-4.087,5.571c0.001-0.001,0.002-0.001,0.003-0.002l6.19,5.238C36.971,39.205,44,34,44,24C44,22.659,43.862,21.35,43.611,20.083z"></path>
                                        </svg>
                                    </div>
                                )}
                                buttonText="Login"
                                onSuccess={onSuccess}
                                onFailure={onFailure}
                                cookiePolicy={'single_host_origin'}
                            />

                            <FacebookLogin
                                appId={process.env.NEXT_PUBLIC_FACEBOOK_APP_ID ?? ''}
                                callback={responseFacebook}
                                isMobile={false}
                                render={(renderProps: any) => (
                                    <div onClick={renderProps.onClick} className='col-sm-3 col-3 d-grid justify-content-center'
                                        style={{ borderRadius: '50%', width: '50px', height: '50px', cursor: 'pointer' }}>
                                        <svg xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" width="50" height="50" viewBox="0 0 48 48" style={{ margin: 'auto' }}>
                                            <linearGradient id="Ld6sqrtcxMyckEl6xeDdMa_uLWV5A9vXIPu_gr1" x1="9.993" x2="40.615" y1="9.993" y2="40.615" gradientUnits="userSpaceOnUse"><stop offset="0" stopColor="#2aa4f4"></stop><stop offset="1" stopColor="#007ad9"></stop></linearGradient><path fill="url(#Ld6sqrtcxMyckEl6xeDdMa_uLWV5A9vXIPu_gr1)" d="M24,4C12.954,4,4,12.954,4,24s8.954,20,20,20s20-8.954,20-20S35.046,4,24,4z"></path><path fill="#fff" d="M26.707,29.301h5.176l0.813-5.258h-5.989v-2.874c0-2.184,0.714-4.121,2.757-4.121h3.283V12.46 c-0.577-0.078-1.797-0.248-4.102-0.248c-4.814,0-7.636,2.542-7.636,8.334v3.498H16.06v5.258h4.948v14.452 C21.988,43.9,22.981,44,24,44c0.921,0,1.82-0.084,2.707-0.204V29.301z"></path>
                                        </svg>
                                    </div>
                                )}
                            />

                            <AppleLogin
                                clientId={process.env.NEXT_PUBLIC_APPLE_CLIENT_ID ?? ''}
                                redirectURI={window.location.origin}
                                responseType="id_token code"
                                responseMode="fragment"
                                usePopup={true}
                                callback={responseApple}
                                // scope="name email"
                                render={(renderProps: any) => (
                                    <div onClick={renderProps.onClick} className='col-sm-3 col-3 d-grid justify-content-center'
                                        style={{ borderRadius: '50%', width: '50px', height: '50px', cursor: 'pointer' }}>
                                        <svg style={{ margin: "auto" }} xmlns="http://www.w3.org/2000/svg" width="50" height="41" viewBox="0 0 33 41" fill="none">
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
                            />
                        </div>
                        {errorMessage ? (
                            <div className={`text-center ${errorMessageStyling}`} >
                                {errorMessage}
                            </div>
                        ) : null}
                    </div>
                </Modal>
            }
            {isPopupOpen && (
                <PortalRegister togglePopup={togglePopup} toggleReadyPopup={toggleReadyPopup} setToggleLoginPopUp={setToggleLoginPopUpHi}/>
            )}
            {isReadyPopupOpen && (
                <RegisterReady toggleReadyPopup={toggleReadyPopup} />
            )}
        </>
    );
};
