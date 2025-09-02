'use client'
import React, { useState, useEffect } from 'react';
import { useLoginMutation } from '@/redux/services/authApi';
import { useI18n } from '@/locales/client'
import variables from '/public/assets/css/login.module.scss'
import 'public/assets/css/popup.scss';
import { FontAwesomeIcon } from "@fortawesome/react-fontawesome";
import { faChevronLeft } from "@fortawesome/free-solid-svg-icons";
import Link from 'next/link';
import { useRouter } from 'next/navigation'
import Cookies from 'js-cookie';
import GoogleLogin from 'react-google-login';
import { gapi } from 'gapi-script';
import { api } from "@/utils/axios";
import FacebookLogin from 'react-facebook-login/dist/facebook-login-render-props';
import { ToastContainer, toast, Slide } from 'react-toastify';
import 'react-toastify/dist/ReactToastify.css';
import AppleLogin from 'react-apple-login';
import { useAppSelector } from '@/redux/hooks'
import { useGetWorkspaceDataByIdQuery } from '@/redux/services/workspace/workspaceDataApi';
import {handleLoginToken} from "@/utils/axiosRefreshToken";
import { TERMS_CONDITIONS_LINK, REGEX_NUMBER_CHECK } from '@/config/constants';

const content = variables['content'];
const copy = variables['copy-right'];
const nunet = variables['nunet'];
const btnDark = `btn btn-dark ${variables['btn-dark']}`;
const welcome = variables['welcome'];
const welcome_portal = variables['welcome-portal'];
const introduce = variables['introduce'];
const loginWith = variables['login-with'];
const forget = variables['forget-password'];
const customInput = variables['custom-input'];
const invalid = variables['invalid'];
const invalidPortalLogin = variables['invalid-portal-login'];
const line = variables['line'];
const dashLine = variables['dash-line']
const dashLinePortalLogin = variables['dash-line-portal-login'];
const eye = variables['eye'];
const term = variables['term']

export default function IntroTables() {
    // Trong functional component
    const workspaceId = useAppSelector((state) => state.workspaceData.globalWorkspaceId)
    const { data: apiDataToken } = useGetWorkspaceDataByIdQuery({ id: workspaceId })
    const apiData = apiDataToken?.data?.setting_generals;
    const apiPhoto = apiDataToken?.data?.photo;
    const trans = useI18n()
    const [isPasswordVisible, setPasswordVisibility] = useState(false);
    const [showBack, setShowBack] = useState(false);
    const [onclickBack, setOnclickBack] = useState(false);
    const [isPasswordFilled, setPasswordFilled] = useState(false);
    const language = Cookies.get('Next-Locale') ?? 'nl';

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
                        router.push("/profile/edit");
                    } else if (!userData.gsm) {
                        router.push("/profile/edit");
                    } else {
                        const query = new URLSearchParams(window.location.search);
                        if (query.get('account') === 'true') {
                            router.push("/profile/portal/show");
                        } else {
                            router.push("/search");
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

        if (errorMessage || !isPasswordValid || !isEmailValid) {
            // Tắt toàn bộ toast cũ
            toast.dismiss();
            // Hiển thị toast
            toast(errorMessage || trans('missing-fields'), {
                position: toast.POSITION.BOTTOM_CENTER,
                autoClose: 1500,
                hideProgressBar: true,
                closeOnClick: true,
                closeButton: false,
                transition: Slide,
                className: 'message',
            });
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
        if (errorMessage || !isPasswordValid || !isEmailValid) {
            // turn off all prev toast
            toast.dismiss();
            // display toast
            toast(errorMessage || trans('missing-fields'), {
                position: toast.POSITION.BOTTOM_CENTER,
                autoClose: 1500,
                hideProgressBar: true,
                closeOnClick: true,
                closeButton: false,
                transition: Slide,
                className: 'message',
            });
        }
    }, [errorMessage]);

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
        api.post(`login/social`, {
            'provider': provider,
            'access_token': response?.accessToken,
            'workspace_id': workspaceId,
        }, {
            headers: {
                'Content-Language': language
            }
        }).then(res => {
            const userData = res.data.data;

            // Set cookie 'loggedToken' with value 'token'
            handleLoginToken(userData.token);

            if (userData?.first_login && (userData.first_name.includes('@') || REGEX_NUMBER_CHECK.test(userData.first_name) || !userData.gsm)) {
                history.pushState({}, "show profile", "/profile/portal/show");
                window.location.href = '/profile/edit';
            } else {
                const query = new URLSearchParams(window.location.search);
                if (query.get('account') === 'true') {
                    router.push("/profile/portal/show");
                } else {
                    router.push("/search");
                }
            }
        }).catch(err => {
            // console.log(err);
        });
    }

    const onFailure = (response: any) => {
        // console.log('FAILED', response);
    };

    return (
        <>
            {
                <div className='container'>
                    <div className={`${variables.login} row justify-content-center`} style={{ backgroundColor: '#B5B268', height: '100%' }}>
                        <div> <FontAwesomeIcon
                            icon={faChevronLeft}
                            onClick={() => router.back()}
                            style={{ position: 'absolute', top: '20px', color: 'white', cursor: 'pointer', pointerEvents: 'auto', width: '20px', height: '20px' }}
                        /></div>
                        <div className={`col-md-6 d-flex justify-content-center mt-5`}>
                            <svg xmlns="http://www.w3.org/2000/svg" width="113" height="79" viewBox="0 0 113 79" fill="none">
                                <g clipPath="url(#clip0_320_91182)">
                                    <path d="M4.41859 46.7064C4.40993 46.9936 4.28914 47.2659 4.08223 47.4647C3.87532 47.6634 3.59884 47.7728 3.31231 47.7692H1.08649C0.951362 47.7667 0.818115 47.7369 0.69469 47.6817C0.571265 47.6265 0.46019 47.547 0.368083 47.4478C0.275976 47.3486 0.204724 47.2319 0.158571 47.1045C0.112419 46.9771 0.0923117 46.8418 0.0994477 46.7064L0.0332031 22.3834C0.0349403 22.1039 0.146469 21.8363 0.343624 21.6386C0.540779 21.4409 0.807679 21.3291 1.08649 21.3273H3.31231C3.59822 21.322 3.87471 21.4298 4.08181 21.6275C4.28892 21.8252 4.40993 22.0968 4.41859 22.3834V46.7064Z" fill="white"/>
                                    <path d="M19.2506 25.5916V46.4939C19.2506 47.1581 19.2506 47.8223 18.1973 47.8223H15.8986C14.8784 47.8223 14.8453 47.1581 14.8453 46.4939V25.5916H8.4726C7.31994 25.5916 7.14771 25.3591 7.14771 24.303V22.6159C7.14771 21.9517 7.14771 21.3274 8.20099 21.3274H25.7757C26.7958 21.3274 26.8621 21.9517 26.8621 22.6159V24.303C26.8621 25.3591 26.7296 25.5916 25.5769 25.5916H19.2506Z" fill="white"/>
                                    <path d="M30.3267 30.613C30.0444 30.6043 29.7765 30.4858 29.5798 30.2824C29.3832 30.079 29.2733 29.8069 29.2734 29.5237V22.2175C29.2752 21.9379 29.3867 21.6703 29.5839 21.4726C29.781 21.2749 30.0479 21.1631 30.3267 21.1614H32.3141C32.5935 21.1631 32.8611 21.2747 33.0593 21.4722C33.2575 21.6697 33.3705 21.9373 33.374 22.2175V29.5237C33.3724 29.8075 33.261 30.0797 33.0633 30.2828C32.8656 30.486 32.597 30.6044 32.3141 30.613H30.3267Z" fill="white"/>
                                    <path d="M53.0686 46.1818C51.5847 47.5102 49.4781 48.0615 47.1066 48.0615H44.5959C43.2347 48.0692 41.8937 47.7308 40.6981 47.0781C39.5026 46.4253 38.4916 45.4794 37.7595 44.3287C37.3104 43.6837 36.9971 42.9537 36.8387 42.1833C36.6543 41.3104 36.5764 40.4183 36.6068 39.5265V39.593C36.6068 38.9288 36.64 38.2978 37.6667 38.2978H39.9058C40.1401 38.2819 40.3719 38.3551 40.5548 38.5028C40.7377 38.6505 40.8584 38.8619 40.8929 39.0948C40.9964 39.4366 41.0302 39.7957 40.9922 40.1509C40.9922 40.3169 40.9922 40.6158 41.0585 41.0409C41.2501 41.8372 41.703 42.5458 42.3445 43.0529C42.9861 43.56 43.779 43.8362 44.5959 43.8372H47.1066C49.0939 43.8372 50.7964 42.7479 51.0813 41.0011C51.1197 40.7816 51.1418 40.5596 51.1475 40.3369C51.1475 39.9051 51.2138 38.9155 50.6507 38.4173C49.7299 37.6269 47.716 37.2948 45.3511 36.7369C41.1313 35.7738 36.5803 34.4853 36.5803 30.055V29.5968C36.5393 28.8296 36.6062 28.0604 36.7791 27.3119H36.7459C37.129 25.5128 38.1175 23.9006 39.5456 22.7456C40.9737 21.5907 42.7545 20.9633 44.5893 20.9688H47.1C49.0449 20.9857 50.9268 21.6623 52.4393 22.8883C53.9232 24.0772 54.844 26.3555 54.7446 28.8661C54.7115 29.5303 54.7115 30.1547 53.6913 30.1547H51.5185C51.2412 30.1479 50.9772 30.0345 50.7811 29.8379C50.585 29.6413 50.4719 29.3766 50.4652 29.0986C50.3658 26.289 49.2132 25.233 47.1066 25.233H44.5959C43.7586 25.2433 42.9491 25.5359 42.2978 26.0637C41.6465 26.5914 41.1913 27.3235 41.0055 28.1421V28.1089C40.8661 28.5264 40.8417 28.974 40.935 29.4042C41.0283 29.8344 41.2357 30.2314 41.5354 30.5532C41.922 30.9621 42.4119 31.2582 42.9531 31.41L46.7422 32.4727C48.7296 33.0307 50.134 33.396 50.962 33.6949C51.7756 33.9677 52.5504 34.345 53.2673 34.8174C54.0206 35.3522 54.6269 36.0692 55.0299 36.9017C55.4329 37.7343 55.6196 38.6554 55.5726 39.5797C55.5726 42.0837 54.5525 44.8601 53.0686 46.1818Z" fill="white"/>
                                    <path d="M12.825 52.5581C14.2954 52.5987 15.694 53.2047 16.7311 54.2508C17.7682 55.2968 18.3644 56.7026 18.3961 58.1772C18.3961 60.6946 17.0712 62.1624 15.1634 63.4377C15.8258 65.2842 16.4883 67.0045 17.1507 68.8576C18.2106 71.7601 19.297 74.7026 20.4629 77.645C20.8273 78.5416 20.3305 78.9734 19.4428 78.9734H16.9454C16.7052 78.9855 16.4685 78.9122 16.277 78.7664C16.0855 78.6205 15.9515 78.4115 15.8987 78.1763L11.1556 65.5565C8.87677 66.7454 6.86956 67.8679 4.53113 69.0901V77.7114C4.53113 78.3756 4.53113 78.9999 3.47122 78.9999H1.08641C0.950131 78.9982 0.815538 78.9695 0.690381 78.9154C0.565223 78.8613 0.451974 78.7829 0.357154 78.6847C0.262333 78.5866 0.187815 78.4706 0.137888 78.3434C0.0879611 78.2163 0.0636127 78.0805 0.0662446 77.9438L0 53.6208C0.00857433 53.3371 0.126604 53.0678 0.329241 52.8696C0.531879 52.6714 0.803316 52.5597 1.08641 52.5581H12.825ZM4.48476 56.9551V64.1617C7.08817 62.8731 12.9574 59.9639 13.6464 58.8481C13.7749 58.6504 13.8438 58.4198 13.8451 58.1839C13.8399 57.8539 13.7045 57.5393 13.4687 57.3091C13.2328 57.0788 12.9156 56.9516 12.5865 56.9551H4.48476Z" fill="white"/>
                                    <path d="M24.1593 79.0001C24.019 79.0078 23.8786 78.9858 23.7474 78.9354C23.6161 78.885 23.4969 78.8075 23.3975 78.7079C23.2982 78.6082 23.2208 78.4887 23.1706 78.3571C23.1204 78.2255 23.0984 78.0848 23.106 77.9441L23.0398 53.621C23.0396 53.4763 23.0688 53.3331 23.1258 53.2001C23.1827 53.0671 23.2662 52.9471 23.371 52.8476C23.4758 52.7481 23.5998 52.6711 23.7354 52.6214C23.8709 52.5716 24.0152 52.5502 24.1593 52.5583H40.058C41.1444 52.5583 41.1444 53.2225 41.1444 53.8867V55.7C41.1444 56.8889 40.9788 56.9885 39.8196 56.9885H27.4914V62.8401H38.4682C38.6101 62.8372 38.7511 62.8637 38.8824 62.9178C39.0138 62.9719 39.1325 63.0526 39.2314 63.1548C39.3302 63.257 39.407 63.3786 39.4569 63.5118C39.5068 63.6451 39.5288 63.7873 39.5215 63.9294V66.1811C39.5288 66.3232 39.5068 66.4654 39.4569 66.5987C39.407 66.7319 39.3302 66.8535 39.2314 66.9557C39.1325 67.0579 39.0138 67.1386 38.8824 67.1927C38.7511 67.2469 38.6101 67.2733 38.4682 67.2704H27.4914V74.6364H40.2833C41.4359 74.6364 41.6082 74.736 41.6082 75.9249V77.7116C41.6082 78.3758 41.6082 79.04 40.5151 79.04L24.1593 79.0001Z" fill="white"/>
                                    <path d="M64.211 79.0001H61.8394C61.1769 79.0001 61.0113 78.6348 60.7795 78.1432L57.7852 69.4489L49.6835 73.0621L47.8684 78.1565C47.6366 78.6812 47.5703 79.0133 46.782 79.0133H44.4966C44.2141 79.0135 43.9427 78.9033 43.7398 78.7061C43.537 78.509 43.4188 78.2404 43.4102 77.9573C43.4102 77.8244 50.962 55.5139 51.8166 53.435C52.0484 52.9368 52.2141 52.5715 52.8699 52.5715H55.7714C56.4338 52.5715 56.5663 53.0033 56.7916 53.435L65.2311 77.5587C65.2439 77.6913 65.2439 77.8247 65.2311 77.9573C65.2464 78.0962 65.2307 78.2369 65.1852 78.369C65.1396 78.5011 65.0653 78.6214 64.9677 78.7212C64.87 78.821 64.7515 78.8978 64.6206 78.946C64.4897 78.9943 64.3498 79.0127 64.211 79.0001ZM56.202 65.0519C55.8045 63.9958 55.5395 63.3382 55.4402 62.9729C55.3408 62.6076 55.2149 62.3087 55.1156 61.95C54.8837 61.2858 54.685 60.6216 54.3206 59.5656L51.6709 67.0843L56.202 65.0519Z" fill="white"/>
                                    <path d="M78.0559 52.5581C80.8292 52.5633 83.4877 53.6684 85.4512 55.6321C87.4147 57.5958 88.5235 60.2586 88.5358 63.0392V68.5255C88.5235 71.3055 87.4144 73.9676 85.4508 75.9302C83.4871 77.8928 80.8285 78.9965 78.0559 78.9999H68.7287C68.5883 79.0076 68.448 78.9855 68.3167 78.9352C68.1855 78.8848 68.0662 78.8073 67.9669 78.7077C67.8675 78.608 67.7902 78.4885 67.74 78.3569C67.6897 78.2253 67.6677 78.0846 67.6754 77.9439L67.6091 53.6208C67.6244 53.3336 67.7491 53.0632 67.9574 52.8655C68.1657 52.6677 68.4418 52.5577 68.7287 52.5581H78.0559ZM72.021 56.9551V74.6029H78.0559C79.6635 74.5909 81.2024 73.948 82.3428 72.812C83.4833 71.676 84.1346 70.1372 84.157 68.5255V63.0392C84.1363 61.4263 83.4858 59.8859 82.3451 58.7484C81.2045 57.611 79.6646 56.9671 78.0559 56.9551H72.021Z" fill="white"/>
                                    <path d="M107.979 52.99C108.081 52.8517 108.215 52.7402 108.37 52.665C108.524 52.5899 108.695 52.5532 108.866 52.5583H111.834C111.978 52.5398 112.124 52.5546 112.261 52.6015C112.398 52.6484 112.522 52.7261 112.624 52.8288C112.727 52.9314 112.804 53.0561 112.851 53.1935C112.898 53.3308 112.912 53.477 112.894 53.621C112.858 53.952 112.719 54.2632 112.497 54.511C112.497 54.5442 112.463 54.5442 112.463 54.5774L103.991 66.7057V77.7115C103.991 78.3757 103.991 79.0001 102.904 79.0001H100.659C99.6385 79.0001 99.6054 78.3757 99.6054 77.7115V66.6725L90.8479 54.2121C90.738 54.0237 90.6915 53.8047 90.7154 53.5878C90.6928 53.448 90.7035 53.3049 90.7467 53.1701C90.7899 53.0353 90.8642 52.9127 90.9637 52.8123C91.0633 52.7119 91.1851 52.6365 91.3192 52.5923C91.4534 52.5482 91.596 52.5365 91.7355 52.5583H94.6702C94.8587 52.5505 95.046 52.5924 95.2134 52.6798C95.3808 52.7672 95.5224 52.897 95.6241 53.0564L101.791 62.2091C105.216 56.8955 107.263 53.8534 108.018 52.9103L107.979 52.99Z" fill="white"/>
                                    <path d="M83.0443 11.0988C83.1905 11.2442 83.367 11.3553 83.561 11.4243C83.7529 11.4986 83.9586 11.5303 84.1639 11.5173H85.7868V14.8383C86.2969 14.8383 86.807 14.7918 87.3171 14.7918C87.8272 14.7918 88.2511 14.7918 88.7215 14.8383V11.5173H90.3312C90.5365 11.5293 90.7419 11.4976 90.9341 11.4243C91.1266 11.3522 91.3024 11.2415 91.4508 11.0988C91.7471 10.8084 91.9163 10.4118 91.9211 9.99628C91.9197 9.79055 91.8769 9.58723 91.7952 9.3985C91.7149 9.20902 91.5978 9.03743 91.4508 8.8937C91.3024 8.7511 91.1266 8.64035 90.9341 8.56825C90.7419 8.49497 90.5365 8.46328 90.3312 8.47526H84.1639C83.9586 8.46224 83.7529 8.49397 83.561 8.56825C83.367 8.63725 83.1905 8.74839 83.0443 8.8937C82.8953 9.03717 82.776 9.20871 82.6932 9.3985C82.6138 9.5877 82.5733 9.79101 82.574 9.99628C82.5738 10.2015 82.6143 10.4047 82.6932 10.5941C82.776 10.7838 82.8953 10.9554 83.0443 11.0988Z" fill="#4A494A"/>
                                    <path d="M96.7502 5.77858C97.0857 5.77838 97.4073 5.64459 97.6445 5.40663L100.884 2.15869C101.12 1.92176 101.253 1.60041 101.253 1.26534C101.253 0.930269 101.12 0.608922 100.884 0.371991C100.648 0.13506 100.327 0.00195313 99.9928 0.00195313C99.6587 0.00195312 99.3382 0.13506 99.1018 0.371991L95.8625 3.61993C95.6883 3.79728 95.57 4.02207 95.5223 4.26633C95.4747 4.51058 95.4997 4.76351 95.5943 4.99361C95.6889 5.22371 95.849 5.42082 96.0545 5.5604C96.26 5.69997 96.5019 5.77585 96.7502 5.77858Z" fill="#4A494A"/>
                                    <path d="M98.2473 13.4236C98.2491 13.7589 98.3831 14.0799 98.6202 14.3164C98.8573 14.5528 99.1782 14.6856 99.5126 14.6856H104.09C104.424 14.6856 104.744 14.5526 104.98 14.316C105.216 14.0793 105.349 13.7583 105.349 13.4236C105.349 13.0889 105.216 12.7679 104.98 12.5312C104.744 12.2946 104.424 12.1616 104.09 12.1616H99.5126C99.1782 12.1616 98.8573 12.2944 98.6202 12.5308C98.3831 12.7673 98.2491 13.0883 98.2473 13.4236Z" fill="#4A494A"/>
                                    <path d="M70.4842 14.6921H75.0617C75.2351 14.7047 75.4093 14.6814 75.5733 14.6235C75.7373 14.5656 75.8876 14.4745 76.0149 14.3557C76.1422 14.237 76.2438 14.0933 76.3132 13.9334C76.3826 13.7736 76.4185 13.6012 76.4185 13.4268C76.4185 13.2525 76.3826 13.08 76.3132 12.9202C76.2438 12.7604 76.1422 12.6166 76.0149 12.4979C75.8876 12.3792 75.7373 12.288 75.5733 12.2302C75.4093 12.1723 75.2351 12.1489 75.0617 12.1615H70.4842C70.3108 12.1489 70.1367 12.1723 69.9726 12.2302C69.8086 12.288 69.6583 12.3792 69.531 12.4979C69.4037 12.6166 69.3021 12.7604 69.2327 12.9202C69.1633 13.08 69.1274 13.2525 69.1274 13.4268C69.1274 13.6012 69.1633 13.7736 69.2327 13.9334C69.3021 14.0933 69.4037 14.237 69.531 14.3557C69.6583 14.4745 69.8086 14.5656 69.9726 14.6235C70.1367 14.6814 70.3108 14.7047 70.4842 14.6921Z" fill="#4A494A"/>
                                    <path d="M76.8437 5.40663C77.0809 5.64459 77.4026 5.77838 77.738 5.77858C77.9863 5.77585 78.2282 5.69997 78.4337 5.5604C78.6393 5.42082 78.7993 5.22371 78.8939 4.99361C78.9885 4.76351 79.0136 4.51058 78.9659 4.26633C78.9182 4.02207 78.7999 3.79728 78.6257 3.61993L75.393 0.371991C75.276 0.254674 75.1371 0.161614 74.9842 0.0981228C74.8313 0.0346316 74.6675 0.00195313 74.502 0.00195313C74.3365 0.00195312 74.1727 0.0346316 74.0198 0.0981227C73.8669 0.161614 73.728 0.254674 73.611 0.371991C73.494 0.489307 73.4012 0.628582 73.3379 0.781863C73.2745 0.935144 73.2419 1.09943 73.2419 1.26534C73.2419 1.43125 73.2745 1.59554 73.3379 1.74882C73.4012 1.9021 73.494 2.04137 73.611 2.15869L76.8437 5.40663Z" fill="#4A494A"/>
                                    <path d="M111.748 43.2196H89.3706V40.2971H111.423C111.377 39.5333 111.298 38.7695 111.178 38.0123C111.059 37.2551 110.907 36.5046 110.721 35.7673C110.536 35.03 110.311 34.2928 110.059 33.5688C109.807 32.8448 109.509 32.1408 109.191 31.4433C108.873 30.7459 108.529 30.0751 108.124 29.4175C107.72 28.76 107.316 28.0891 106.872 27.4914C106.429 26.8936 105.952 26.2759 105.448 25.698C104.945 25.1202 104.415 24.5689 103.858 24.0442C103.302 23.5194 102.726 23.0213 102.123 22.5497C101.52 22.0781 100.897 21.6397 100.255 21.2213C99.6121 20.8029 98.9297 20.4243 98.2673 20.0789C97.6048 19.7335 96.8894 19.4147 96.1806 19.1291C95.4718 18.8435 94.7563 18.5977 94.021 18.3785C93.2857 18.1593 92.5504 17.9866 91.8018 17.8405C91.0533 17.6944 90.2981 17.5815 89.5363 17.5084C88.7744 17.4354 88.0126 17.3955 87.2508 17.3955C86.489 17.3955 85.7206 17.4287 84.9588 17.5018C84.1964 17.5684 83.4379 17.6749 82.6866 17.8206C81.938 17.9601 81.1961 18.1394 80.4607 18.352C79.7244 18.563 78.9992 18.8113 78.2879 19.0959C77.5791 19.3748 76.8769 19.6936 76.1946 20.039C75.5134 20.386 74.8501 20.7673 74.2073 21.1814C73.5448 21.5933 72.9354 22.0316 72.3259 22.5099C71.7165 22.9881 71.1401 23.4796 70.5837 24.0043C70.0272 24.529 69.4973 25.0803 68.9872 25.6582C68.4771 26.236 68.0068 26.8338 67.5563 27.4581C66.6604 28.7119 65.8821 30.0459 65.2311 31.4433C64.9065 32.1075 64.615 32.8448 64.3567 33.5688C64.0983 34.2928 63.8797 35.0234 63.6942 35.7739C63.5088 36.5245 63.3498 37.2684 63.2305 38.0256C63.1113 38.7828 63.0318 39.5466 62.992 40.3171H84.5613V43.2395H62.7403C62.5753 43.2395 62.412 43.2722 62.2597 43.3357C62.1073 43.3992 61.969 43.4923 61.8527 43.6096C61.7363 43.7268 61.6442 43.866 61.5817 44.0191C61.5192 44.1722 61.4874 44.3361 61.4883 44.5015V46.4941C61.4883 46.8271 61.6202 47.1464 61.855 47.3818C62.0898 47.6172 62.4083 47.7495 62.7403 47.7495H111.761C112.095 47.7477 112.414 47.6142 112.649 47.3779C112.885 47.1416 113.018 46.8216 113.02 46.4875V44.5015C113.024 44.3328 112.993 44.165 112.931 44.0083C112.868 43.8516 112.775 43.7091 112.656 43.5894C112.537 43.4698 112.396 43.3753 112.24 43.3118C112.084 43.2483 111.916 43.2169 111.748 43.2196ZM78.5198 24.2501C74.6626 26.0353 71.6266 29.2241 70.0272 33.1703C69.946 33.4049 69.794 33.6084 69.5921 33.7524C69.3903 33.8964 69.1488 33.9739 68.9011 33.974C68.7679 33.9752 68.6356 33.9527 68.5102 33.9075C68.212 33.8048 67.9664 33.5878 67.8274 33.304C67.6883 33.0201 67.6671 32.6926 67.7683 32.3932C69.5797 27.7677 73.1319 24.0415 77.6586 22.0183C77.8184 21.9497 77.9913 21.917 78.1651 21.9224C78.3389 21.9279 78.5094 21.9713 78.6646 22.0498C78.8199 22.1283 78.9561 22.2398 79.0639 22.3767C79.1716 22.5135 79.2482 22.6723 79.2882 22.842C79.3443 23.1297 79.2981 23.428 79.1578 23.6852C79.0174 23.9424 78.7917 24.1423 78.5198 24.2501Z" fill="#4A494A"/>
                                </g>
                                <defs>
                                    <clipPath id="clip0_320_91182">
                                        <rect width="113" height="79" fill="white"/>
                                    </clipPath>
                                </defs>
                            </svg>
                        </div>
                        <div className="col-8 text-center ps-2 pe-2 mt-3">
                            <h1 className={welcome} style={{ marginBottom: '0' }}>{trans('lang_welcome_to_its_ready')}</h1>
                        </div>
                        <div className="row text-center ps-3 pe-3 mb-2">
                            <h1 className={introduce} style={{ padding: '0 51px' }}> {trans('lang_login_of_account')}  </h1>
                        </div>
                        <div className="row ps-3 pe-3">
                            <form onSubmit={handleSubmit}>
                                <div className={`${variables.input}`}>
                                    <input
                                        type="text"
                                        className={`${customInput} ${isEmailValid ? '' : invalidPortalLogin} form-control`}
                                        id="email"
                                        placeholder={trans('email-field')}
                                        style={{
                                            height: '50px',
                                            flex: 1,
                                            marginRight: '0',
                                            outline: 'none',
                                            boxShadow: 'none',
                                        }}
                                        onChange={handleEmailChange}
                                        onKeyUp={() => { setEmailValid(true) }}
                                    />
                                </div>
                                <div>
                                    <div className="input-group">
                                        <input
                                            type={isPasswordVisible ? 'text' : 'password'}
                                            className={`${customInput} ${isPasswordValid ? '' : invalidPortalLogin} form-control`}
                                            id="password"
                                            placeholder={trans('password')}
                                            style={{ height: '50px', flex: 1, marginRight: '0', borderRight: 'none', boxShadow: 'none' }}
                                            onChange={handlePasswordChange}
                                            onKeyUp={() => { setPasswordValid(true) }}
                                        />
                                        {
                                            isPasswordFilled && (
                                                <button
                                                    className={`${eye} ${isPasswordValid ? '' : invalidPortalLogin} btn`}
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
                                                    { isPasswordVisible
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
                                <div className="row justify-content-center mt-3" style={{paddingRight: '12px'}}>
                                    <div className='d-flex justify-content-center' style={{ width: '58%' }}>
                                        <button type="submit" className={btnDark} onClick={handleLoginClick}>{trans('login')}</button>
                                    </div>
                                    <div className="col-sm-5 col-5 d-flex justify-content-center text-center">
                                        <a className={forget} href="/user/reset_password" style={{ textDecoration: 'none', color: 'white' }}>{trans('forget-password')}</a>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div className={`${line} ps-4 pe-4 text-center mt-2`}>
                            <span className={`${dashLine}`}/>&nbsp;&nbsp;
                            <span style={{ marginTop: '5px' }}>{trans('lang_of')}</span>
                            &nbsp;&nbsp;<span className={`${dashLine}`}/>
                        </div>
                        <div className={`${loginWith} text-center`}>{trans('login-with')}</div>
                        <div className={`${variables.socialMedia} row d-flex mb-4`}>
                            <GoogleLogin
                                clientId={process.env.NEXT_PUBLIC_GOOGLE_CLIENT_ID ?? ''}
                                render={renderProps => (
                                    <div onClick={renderProps.onClick} className='col-sm-3 col-3 d-grid justify-content-center' style={{ backgroundColor: 'white', borderRadius: '50%', width: '50px', height: '50px' }}>
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
                                appId={process.env.NEXT_PUBLIC_FACEBOOK_APP_ID}
                                callback={responseFacebook}
                                isMobile={false}
                                render={(renderProps: any) => (
                                    <div onClick={renderProps.onClick} className='col-sm-3 col-3 d-grid justify-content-center' style={{ backgroundColor: 'white', borderRadius: '50%', width: '50px', height: '50px' }}>
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
                                    <div onClick={renderProps.onClick} className='col-sm-3 col-3 d-grid justify-content-center' style={{ backgroundColor: 'white', borderRadius: '50%', width: '50px', height: '50px' }}>
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
                    </div>
                    <div className="row text-center d-flex justify-content-center" style={{ backgroundColor: '#413E38' }}>
                        <div className={`${content} d-flex flex-column align-items-center justify-content-center`} style={{ backgroundColor: '#322F28' }}>
                            <Link href={'/user/register/portal'} style={{ textDecoration: 'none', color: 'white' }}>{trans('register')}</Link>
                        </div>
                        <div className={`${nunet} mt-4`} onClick={() => router.back()} style={{ opacity: '0.5'}}>
                            {trans('not-now')}
                        </div>
                        <div className={`${copy} mt-4`}>
                            {trans('terms')}
                            <Link className={`${term}`}
                                  style={{ textDecoration: 'none' }}
                                  href={TERMS_CONDITIONS_LINK}
                                  target="_blank"
                            >
                                {trans('terms-and-conditions')}
                            </Link>
                        </div>
                    </div>
                    <ToastContainer />
                </div>
            }
        </>
    );
};
