'use client'

import style from 'public/assets/css/profile.module.scss'
import React, { useState, useEffect } from 'react';
import { useI18n } from '@/locales/client';
import Cookies from 'js-cookie';
import 'react-toastify/dist/ReactToastify.css';
import { Button } from '@mui/material';
import { Modal } from "react-bootstrap";
import {useAppDispatch, useAppSelector} from '@/redux/hooks'
import { useGetWorkspaceDataByIdQuery } from '@/redux/services/workspace/workspaceDataApi'
import {api} from "@/utils/axios";
import {logout} from "@/redux/slices/authSlice";
import variables from "assets/assets/css/menu.module.scss";
import Link from "next/link";
import Login from "@/app/[locale]/components/users/login";
import Register from "@/app/[locale]/components/users/register";
import Profile from "@/app/[locale]/components/users/profile";
import {usePathname} from "next/navigation";
import {handleLogoutToken} from "@/utils/axiosRefreshToken";
import { TERMS_CONDITIONS_LINK, PRIVACY_POLICY_LINK } from '@/config/constants';
import SwitchLangMobile from "@/app/[locale]/components/share/switchLangMobile";

export default function NavigationPortalPopup({ togglePopup }: { togglePopup: any }) {
    const tokenLoggedInCookie = Cookies.get('loggedToken');
    const dispatch = useAppDispatch();
    const workspaceToken = useAppSelector((state) => state.workspaceData.globalWorkspaceToken)
    const workspaceId = useAppSelector((state) => state.workspaceData.globalWorkspaceId)
    const { data: apiDataToken } = useGetWorkspaceDataByIdQuery({id: workspaceId})
    const apiData = apiDataToken?.data?.setting_generals;
    const color = useAppSelector((state) => state.workspaceData.globalWorkspaceColor)
    const trans = useI18n();
    const language = Cookies.get('Next-Locale');
    const [show, setShow] = useState(false);

    const routerPath = usePathname();
    let activeMenu = 'home';

    if (routerPath === '/') {
        activeMenu = 'home';
    } else if (routerPath.includes('/contacts')) {
        activeMenu = 'contacts';
    } else if (routerPath.includes('/terms-and-conditions')) {
        activeMenu = 'terms-and-conditions';
    } else if (routerPath.includes('/privacy-policy')) {
        activeMenu = 'privacy-policy';
    } else if (routerPath.includes('/cookie-policy')) {
        activeMenu = 'cookie-policy';
    } else if (routerPath.includes('/search')) {
        activeMenu = 'search';
    }

    const handleClose = () => {
        togglePopup();
        setShow(false);
    };
    const handleShow = () => setShow(true);
    const globalLocale = useAppSelector((state) => state.auth.globalLocale)
    const currentLanguage = language ?? globalLocale
    const [isRegisterOpen, setIsRegisterOpen] = useState<any | null>(false);
    const [isLoginOpen, setIsLoginOpen] = useState<any | null>(false);
    const [isProfileOpen, setIsProfileOpen] = useState<any | null>(false);

    const togglePopupRegister = () => {
        setIsRegisterOpen(!isRegisterOpen);
    }

    const togglePopupLogin = () => {
        setIsLoginOpen(!isLoginOpen);
    }

    const togglePopupProfile = () => {
        setIsProfileOpen(!isProfileOpen);
    }

    const [unreadCount, setUnreadCount] = useState(0);

    useEffect(() => {
        tokenLoggedInCookie && api.get(`notifications`, {
            headers: {
                'Authorization': `Bearer ${tokenLoggedInCookie}`,
                'App-Token': workspaceToken,
                'Content-Language': language
            }
        }).then(res => {
            const json = res.data;

            if (json.data) {
                setUnreadCount(json.data.total_unread);
            }
        })
    }, []);

    useEffect(() => {
        const hasShownPopup = false;

        if (!hasShownPopup) {
            setShow(true);
            localStorage.setItem('hasShownPopup', 'true');
        }
    }, []);

    const [infoUser, setInfoUser] = useState({
        avatar: '',
        fullname: '',
        photo: '',
    });

    useEffect(() => {
        tokenLoggedInCookie && api.get(`profile`, {
            headers: {
                'Authorization': `Bearer ${tokenLoggedInCookie}`,
                'Content-Language': language
            }
        }).then(res => {
            const json = res.data;
            if(json.data.first_name && !json.data.last_name){
                setInfoUser({
                    avatar: json.data.first_name.substring(0, 1),
                    fullname: json.data.first_name,
                    photo: json.data.photo,
                })
            }else if(json.data.last_name && !json.data.first_name){
                setInfoUser({
                    avatar: json.data.last_name.substring(0, 1),
                    fullname: json.data.last_name,
                    photo: json.data.photo,
                })
            }else{
                setInfoUser({
                    avatar: json.data.first_name.substring(0, 1) + json.data.last_name.substring(0, 1),
                    fullname: json.data.first_name + ' ' + json.data.last_name,
                    photo: json.data.photo,
                })
            }
        }).catch(error => {
            // console.log(error)
        });

    }, []);

    const handleLogoutClick = async () => {
        dispatch(logout());
        // Remove cookie 'loggedToken'
        handleLogoutToken();
        window.location.reload();
    };

    /**
     * Redirect to order history page
     * @param {string} type Type is Mobile or Desktop (default)
     */
    const viewOrderHistory = (type: string = '') => {
        if (type === 'mobile') {
            window.location.href = '/orders/history';
        } else {
            let btnHistoryOrderHeader = document.getElementById('btn-history-order-header');
            if (btnHistoryOrderHeader) {
                btnHistoryOrderHeader.click();
            }
        }
    }

    /**
     * Redirect to order history page
     * @param {string} type Type is Mobile or Desktop (default)
     */
    const viewMessages = (type: string = '') => {
        if (type === 'mobile') {
            window.location.href = '/message-center';
        } else {
            let btnNotificationHeader = document.getElementById('btn-notification-header');
            if (btnNotificationHeader) {
                btnNotificationHeader.click();
            }
        }
    }

    return (
        <>
            <Button onClick={handleShow} style={{ display: 'none' }}></Button>

            <Modal show={show} onHide={handleClose}
                   animation={false}
                   id='modal-profile'
            >
                <div className="res-mobile">
                    <Modal.Body style={{ paddingLeft : '0', paddingRight: '0'}}>
                        <div className="close-popup text-828282" onClick={() => handleClose()}
                             style={ workspaceId ? {} : {
                                 fontFamily: "SF Compact Display",
                                 fontSize: '16px',
                                 fontStyle: 'normal',
                                 fontWeight: '790',
                                 lineHeight: 'normal',
                                 letterSpacing: '1.44px',
                                 paddingLeft: '10px',
                                 paddingRight: '10px',
                                 paddingTop: '0',
                                 float: 'right',
                             }}>
                            <svg width="27" height="27" viewBox="0 0 27 27" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M19.8455 6.61597L6.61523 19.8463" stroke="#1E1E1E" strokeWidth="2.20505" strokeLinecap="round" strokeLinejoin="round"/>
                                <path d="M6.61523 6.61597L19.8455 19.8463" stroke="#1E1E1E" strokeWidth="2.20505" strokeLinecap="round" strokeLinejoin="round"/>
                            </svg>
                        </div>
                        <div style={{ marginTop: '50px', marginLeft: '30px', marginBottom: '50px'}}>
                            <div className={style['navigation-title-mobile']}>Navigatie</div>
                            <div className={style['navigation-options-mobile']}>
                                <Link href="/" style={{ fontWeight: activeMenu === 'home' ? '790' : '457', fontFamily: activeMenu === 'home' ? 'SF Compact Display Bold' : 'SF Compact Display' }}>{trans('home')}</Link>
                                <Link href="/search" style={{ fontWeight: activeMenu === 'search' ? '790' : '457', fontFamily: activeMenu === 'search' ? 'SF Compact Display Bold' : 'SF Compact Display' }}>{trans('portal.find-dealer')}</Link>
                                <Link href="https://b2b.itsready.be/" target="_blank" style={{ fontWeight: '457', fontFamily: 'SF Compact Display' }}>{trans('portal.traders-website')}</Link>
                                <Link href="/contacts" style={{ fontWeight: activeMenu === 'contacts' ? '790' : '457', fontFamily: activeMenu === 'contacts' ? 'SF Compact Display Bold' : 'SF Compact Display' }}>{trans('contact-us')}</Link>
                                <Link href={TERMS_CONDITIONS_LINK} target="_blank" style={{ fontWeight: activeMenu === 'terms-and-conditions' ? '790' : '457', fontFamily: activeMenu === 'terms-and-conditions' ? 'SF Compact Display Bold' : 'SF Compact Display' }}>{trans('terms-and-conditions')}</Link>
                                <Link href={PRIVACY_POLICY_LINK} target="_blank" style={{ fontWeight: activeMenu === 'privacy-policy' ? '790' : '457', fontFamily: activeMenu === 'privacy-policy' ? 'SF Compact Display Bold' : 'SF Compact Display' }}>{trans('privacy-policy')}</Link>
                                <Link href="https://b2b.itsready.be/cookies" target="_blank" style={{ fontWeight: activeMenu === 'cookie-policy' ? '790' : '457', fontFamily: activeMenu === 'cookie-policy' ? 'SF Compact Display Bold' : 'SF Compact Display' }}>{trans('portal.cookie-policy')}</Link>
                            </div>
                        </div>

                        { !tokenLoggedInCookie ?
                            (
                                <>
                                    <div className="text-center" style={{ marginLeft: '30px', marginRight: '30px', display: 'flex', flexDirection: 'column', gap: '23px', marginBottom: '50px'}}>
                                        <div className={`${style['navigation-login-button']}`} onClick={togglePopupLogin}>{trans('login-nl')}</div>
                                        <div className={`${style['navigation-register-button']}`} onClick={togglePopupRegister}>{trans('register-btn')}</div>
                                    </div>
                                    <div style={{
                                        display: 'flex',
                                        justifyContent: 'space-between',
                                        alignItems: 'center',
                                        margin: '0 25px 150px 25px',
                                        position: 'relative',
                                    }}>
                                        <div style={{ display: 'flex', flexDirection: 'column', gap: '13px' }}>
                                            <a className={`${variables.header_info_item}`} href="https://b2b.itsready.be/" target="_blank"
                                               style={{
                                                   fontWeight: '556',
                                                   fontFamily: 'SF Compact Display Medium',
                                                   fontSize: '14px',
                                                   color: '#1E1E1E',
                                                   textTransform: 'none',
                                                   textDecoration: 'none',
                                                   display : 'flex',
                                                   alignItems: 'center',
                                                   gap: '7px',
                                               }}>
                                                <svg width="15" height="21" viewBox="0 0 15 21" fill="none" xmlns="http://www.w3.org/2000/svg" style={{ marginLeft: '5px'}}>
                                                    <path d="M13.6853 19.7996V20.8377C14.2586 20.8377 14.7234 20.3729 14.7234 19.7996H13.6853ZM1.22839 19.7996H0.190313C0.190313 20.3729 0.65508 20.8377 1.22839 20.8377V19.7996ZM4.34262 4.22848C3.76931 4.22848 3.30454 4.69325 3.30454 5.26656C3.30454 5.83986 3.76931 6.30463 4.34262 6.30463V4.22848ZM5.38069 6.30463C5.95402 6.30463 6.41877 5.83986 6.41877 5.26656C6.41877 4.69325 5.95402 4.22848 5.38069 4.22848V6.30463ZM4.34262 7.34271C3.76931 7.34271 3.30454 7.80747 3.30454 8.38078C3.30454 8.95411 3.76931 9.41886 4.34262 9.41886V7.34271ZM5.38069 9.41886C5.95402 9.41886 6.41877 8.95411 6.41877 8.38078C6.41877 7.80747 5.95402 7.34271 5.38069 7.34271V9.41886ZM9.533 7.34271C8.95967 7.34271 8.49492 7.80747 8.49492 8.38078C8.49492 8.95411 8.95967 9.41886 9.533 9.41886V7.34271ZM10.5711 9.41886C11.1444 9.41886 11.6091 8.95411 11.6091 8.38078C11.6091 7.80747 11.1444 7.34271 10.5711 7.34271V9.41886ZM9.533 10.4569C8.95967 10.4569 8.49492 10.9217 8.49492 11.495C8.49492 12.0683 8.95967 12.5331 9.533 12.5331V10.4569ZM10.5711 12.5331C11.1444 12.5331 11.6091 12.0683 11.6091 11.495C11.6091 10.9217 11.1444 10.4569 10.5711 10.4569V12.5331ZM4.34262 10.4569C3.76931 10.4569 3.30454 10.9217 3.30454 11.495C3.30454 12.0683 3.76931 12.5331 4.34262 12.5331V10.4569ZM5.38069 12.5331C5.95402 12.5331 6.41877 12.0683 6.41877 11.495C6.41877 10.9217 5.95402 10.4569 5.38069 10.4569V12.5331ZM9.533 4.22848C8.95967 4.22848 8.49492 4.69325 8.49492 5.26656C8.49492 5.83986 8.95967 6.30463 9.533 6.30463V4.22848ZM10.5711 6.30463C11.1444 6.30463 11.6091 5.83986 11.6091 5.26656C11.6091 4.69325 11.1444 4.22848 10.5711 4.22848V6.30463ZM2.88931 2.15233H12.0244V0.0761772H2.88931V2.15233ZM12.6472 2.77517V19.7996H14.7234V2.77517H12.6472ZM13.6853 18.7615H1.22839V20.8377H13.6853V18.7615ZM2.26646 19.7996V2.77517H0.190313V19.7996H2.26646ZM12.0244 2.15233C12.3322 2.15233 12.5006 2.15314 12.6217 2.16302C12.7306 2.17192 12.7069 2.18274 12.6472 2.15233L13.5898 0.302467C13.3081 0.158901 13.028 0.113153 12.7907 0.0937724C12.5655 0.0753674 12.2979 0.0761772 12.0244 0.0761772V2.15233ZM14.7234 2.77517C14.7234 2.50161 14.7242 2.23401 14.7058 2.00884C14.6864 1.77154 14.6406 1.49153 14.4971 1.20978L12.6472 2.15233C12.6168 2.09263 12.6276 2.06899 12.6365 2.1779C12.6464 2.29893 12.6472 2.46735 12.6472 2.77517H14.7234ZM12.6472 2.15233L14.4971 1.20978C14.2981 0.819128 13.9804 0.501508 13.5898 0.302467L12.6472 2.15233ZM2.88931 0.0761772C2.61575 0.0761772 2.34815 0.0753674 2.12297 0.0937724C1.88568 0.113153 1.60567 0.158901 1.32391 0.302467L2.26646 2.15233C2.20676 2.18274 2.18313 2.17192 2.29203 2.16302C2.41306 2.15314 2.58149 2.15233 2.88931 2.15233V0.0761772ZM2.26646 2.77517C2.26646 2.46735 2.26727 2.29893 2.27716 2.1779C2.28605 2.06899 2.29688 2.09263 2.26646 2.15233L0.416603 1.20978C0.273037 1.49153 0.227289 1.77154 0.207908 2.00884C0.189503 2.23401 0.190313 2.50161 0.190313 2.77517H2.26646ZM1.32391 0.302467C0.933253 0.501508 0.615644 0.819118 0.416603 1.20978L2.26646 2.15233L1.32391 0.302467ZM4.34262 6.30463H5.38069V4.22848H4.34262V6.30463ZM4.34262 9.41886H5.38069V7.34271H4.34262V9.41886ZM9.533 9.41886H10.5711V7.34271H9.533V9.41886ZM9.533 12.5331H10.5711V10.4569H9.533V12.5331ZM4.34262 12.5331H5.38069V10.4569H4.34262V12.5331ZM9.533 6.30463H10.5711V4.22848H9.533V6.30463ZM8.49492 16.6854V19.7996H10.5711V16.6854H8.49492ZM6.41877 19.7996V16.6854H4.34262V19.7996H6.41877ZM7.45684 15.6473C8.03017 15.6473 8.49492 16.1121 8.49492 16.6854H10.5711C10.5711 14.9654 9.17683 13.5712 7.45684 13.5712V15.6473ZM7.45684 13.5712C5.73686 13.5712 4.34262 14.9654 4.34262 16.6854H6.41877C6.41877 16.1121 6.88351 15.6473 7.45684 15.6473V13.5712Z" fill="#1E1E1E"/>
                                                </svg>
                                                {trans('portal.trader')}?
                                            </a>
                                            <a className={`${variables.header_info_item}`}
                                               style={{
                                                   fontWeight: '556',
                                                   fontFamily: 'SF Compact Display Medium',
                                                   fontSize: '14px',
                                                   color: '#1E1E1E',
                                                   textTransform: 'none',
                                                   textDecoration: 'none',
                                                   display : 'flex',
                                                   alignItems: 'center',
                                                   gap: '5px',
                                               }}>
                                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M12 22C17.5228 22 22 17.5228 22 12C22 6.47715 17.5228 2 12 2C6.47715 2 2 6.47715 2 12C2 17.5228 6.47715 22 12 22Z" stroke="#1E1E1E" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"/>
                                                    <path d="M2 12H22" stroke="#1E1E1E" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"/>
                                                    <path d="M12 2C14.5013 4.73835 15.9228 8.29203 16 12C15.9228 15.708 14.5013 19.2616 12 22C9.49872 19.2616 8.07725 15.708 8 12C8.07725 8.29203 9.49872 4.73835 12 2Z" stroke="#1E1E1E" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"/>
                                                </svg>
                                                { trans('language') }
                                            </a>
                                        </div>

                                        <div className={`d-flex`} style={{ position: 'absolute', right: '0', bottom: '0'}}>
                                            <SwitchLangMobile/>
                                        </div>
                                    </div>
                                </>
                            )
                            :
                            (
                                <>
                                    <div className={`${style['navigation-title-mobile']}`} style={{
                                        marginLeft: '30px',
                                        marginRight: '35px',
                                        marginBottom: '15px',
                                        display: 'flex',
                                        alignItems: 'center',
                                    }}>
                                        {trans('lang_account')}
                                    </div>
                                    <div className="text-center" style={{
                                        marginLeft: '30px',
                                        marginRight: '35px',
                                        marginBottom: '20px',
                                        display: 'flex',
                                        alignItems: 'center',
                                        justifyContent: 'space-between'
                                    }}>
                                        <div style={{ display: 'flex', flexDirection: 'row', alignItems: 'center' }}>
                                            { tokenLoggedInCookie &&
                                                <div className={`${variables.home_avatar}`} style={{ display: 'flex', alignItems: 'center' }}>
                                                    {infoUser.photo ?
                                                        <span>
                                                            <img src={infoUser?.photo} alt={infoUser.avatar}
                                                                 style={{
                                                                     display: 'flex',
                                                                     borderRadius: '100%',
                                                                     backgroundColor: '#ABA765',
                                                                     width: '50px',
                                                                     height: '50px',
                                                                     justifyContent: 'center',
                                                                     alignItems: 'center',
                                                                     fontFamily: 'SF Compact Display Medium',
                                                                     fontSize: '20px',
                                                                     fontWeight: '556',
                                                                     lineHeight: '24px',
                                                                     color: '#FFFFFF'
                                                                 }}/>
                                                        </span>
                                                        :
                                                        <span style={{
                                                            display: 'flex',
                                                            borderRadius: '100%',
                                                            backgroundColor: '#ABA765',
                                                            width: '50px',
                                                            height: '50px',
                                                            justifyContent: 'center',
                                                            alignItems: 'center',
                                                            fontFamily: 'SF Compact Display Medium',
                                                            fontSize: '20px',
                                                            fontWeight: '556',
                                                            lineHeight: '24px',
                                                            color: '#FFFFFF'
                                                        }}>
                                                            {infoUser.avatar}
                                                        </span>
                                                    }
                                                    <span style={{
                                                        marginLeft: '10px',
                                                        fontFamily: 'SF Compact Display Medium',
                                                        fontSize: '16px',
                                                        fontWeight: '556',
                                                        lineHeight: '19px',
                                                        textAlign: 'left',
                                                        color: '#404040'
                                                    }}>{infoUser.fullname}</span>
                                                </div>
                                            }
                                        </div>

                                        <a className={`${variables.header_info_item}`} href="#" onClick={handleLogoutClick} style={{ display: 'flex', alignItems: 'center' }}>
                                            <svg xmlns="http://www.w3.org/2000/svg" width="26" height="26" viewBox="0 0 26 26" fill="none">
                                                <path d="M10 22H6C5.46957 22 4.96086 21.7893 4.58579 21.4142C4.21071 21.0391 4 20.5304 4 20V6C4 5.46957 4.21071 4.96086 4.58579 4.58579C4.96086 4.21071 5.46957 4 6 4H10" stroke="#404040" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" />
                                                <path d="M17 18L22 13L17 8" stroke="#404040" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" />
                                                <path d="M22 13H10" stroke="#404040" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" />
                                            </svg>
                                        </a>
                                    </div>
                                    <div className="text-center" style={{
                                        marginLeft: '30px',
                                        marginRight: '35px',
                                        marginBottom: '20px',
                                        display: 'flex',
                                        gap: '10px',
                                        alignItems: 'center',
                                        position: 'relative'
                                    }}>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="23" height="19" viewBox="0 0 23 19" fill="none">
                                            <path fillRule="evenodd" clipRule="evenodd" d="M2.36481 2.25731C2.54923 2.09737 2.78905 2 3.0495 2H19.4441C19.7046 2 19.9444 2.09737 20.1288 2.25731L11.2468 7.23067L2.36481 2.25731ZM0.145021 2.12091C0.139085 2.13071 0.133293 2.14066 0.12765 2.15073C0.014783 2.35231 -0.0207931 2.57618 0.0112785 2.78819C0.00392955 2.87421 0.000179118 2.96123 0.000179118 3.0491V15.3437C0.000179118 17.0231 1.3702 18.3928 3.0495 18.3928H19.4441C21.1234 18.3928 22.4934 17.0231 22.4934 15.3437V3.0491C22.4934 2.96123 22.4897 2.87421 22.4823 2.78819C22.5144 2.57619 22.4788 2.35231 22.366 2.15073C22.3603 2.14066 22.3545 2.13071 22.3486 2.1209C21.9543 0.892963 20.8 0 19.4441 0H3.0495C1.69358 0 0.539303 0.892964 0.145021 2.12091ZM20.4934 4.34533V15.3437C20.4934 15.9183 20.019 16.3928 19.4441 16.3928H3.0495C2.47456 16.3928 2.00018 15.9183 2.00018 15.3437V4.34533L10.7582 9.2493C11.0618 9.41926 11.4318 9.41926 11.7354 9.2493L20.4934 4.34533Z" fill="#404040" />
                                        </svg>
                                        {
                                            unreadCount && unreadCount > 0
                                                ?
                                                <span style={{
                                                    backgroundColor: '#ABA765',
                                                    borderRadius: '25px',
                                                    width: 'auto',
                                                    height: 'auto',
                                                    position: 'absolute',
                                                    top: '-8px',
                                                    left: '15px',
                                                    fontFamily: 'Roboto',
                                                    fontSize: '9.55px',
                                                    fontWeight: '790',
                                                    lineHeight: '11px',
                                                    textAlign: 'center',
                                                    color: '#FFFFFF',
                                                    padding: '1px 5px'
                                                }}>
                                                    {unreadCount}
                                                </span>
                                                :
                                                null
                                        }
                                        <div style={{
                                            fontWeight: '556',
                                            fontFamily: 'SF Compact Display Medium',
                                            fontSize: '14px',
                                            color: '#1E1E1E',
                                            textTransform: 'none',
                                            textDecoration: 'none',
                                            display : 'flex',
                                            alignItems: 'center',
                                            gap: '5px',
                                        }} onClick={() => viewMessages('mobile')}>
                                            {trans('notification')}
                                        </div>
                                    </div>
                                    <div className="text-center" style={{
                                        marginLeft: '30px',
                                        marginRight: '35px',
                                        marginBottom: '20px',
                                        display: 'flex',
                                        gap: '10px',
                                        alignItems: 'center',
                                    }} onClick={() => viewOrderHistory('mobile')}>
                                        <svg width="24" height="23" viewBox="0 0 24 23" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M4.85589 9.71206C4.85589 7.68982 5.55707 5.73015 6.83996 4.16694C8.12285 2.60373 9.90808 1.53371 11.8915 1.13919C13.8748 0.744677 15.9336 1.05007 17.7171 2.00335C19.5005 2.95662 20.8983 4.49879 21.6722 6.36709C22.446 8.23539 22.5482 10.3142 21.9611 12.2494C21.3741 14.1845 20.1343 15.8563 18.4528 16.9798C16.7714 18.1033 14.7524 18.609 12.74 18.4108C10.7275 18.2126 8.84595 17.3227 7.41601 15.8927" stroke="#1E1E1E" strokeWidth="1.9424"/>
                                            <path d="M13.2079 6.21558V10.8773L16.3157 12.4313" stroke="#1E1E1E" strokeWidth="1.9424" strokeLinecap="round" strokeLinejoin="round"/>
                                            <path d="M3.97137 12.0255C4.35144 12.4095 4.97185 12.4095 5.35192 12.0255L7.06143 10.2983C7.66839 9.685 7.23399 8.64385 6.37115 8.64385H2.95214C2.0893 8.64385 1.6549 9.685 2.26186 10.2983L3.97137 12.0255Z" fill="#1E1E1E"/>
                                        </svg>
                                        <div style={{
                                            fontWeight: '556',
                                            fontFamily: 'SF Compact Display Medium',
                                            fontSize: '14px',
                                            color: '#1E1E1E',
                                            textTransform: 'none',
                                            textDecoration: 'none',
                                            display : 'flex',
                                            alignItems: 'center',
                                            gap: '5px',
                                        }}>
                                            {trans('order-history')}
                                        </div>
                                    </div>
                                    <div className="text-center" style={{
                                        marginLeft: '30px',
                                        marginRight: '35px',
                                        marginBottom: '20px',
                                        display: 'flex',
                                        gap: '13px',
                                        alignItems: 'center',
                                    }} onClick={togglePopupProfile}>
                                        <svg width="21" height="21" viewBox="0 0 21 21" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M1.74988 18.3749H6.99988C7.23194 18.3749 7.4545 18.2827 7.6186 18.1186C7.78269 17.9546 7.87488 17.732 7.87488 17.4999C7.87488 17.2679 7.78269 17.0453 7.6186 16.8812C7.4545 16.7171 7.23194 16.6249 6.99988 16.6249H2.687C2.89933 15.1679 3.62862 13.8358 4.74172 12.872C5.85482 11.9081 7.27746 11.3768 8.74988 11.3749C9.78017 11.3839 10.7811 11.0321 11.5793 10.3805C12.3774 9.72889 12.9224 8.8186 13.1199 7.80737C13.3173 6.79613 13.1547 5.74769 12.6603 4.84373C12.1659 3.93978 11.3708 3.23729 10.4128 2.85801C9.45484 2.47873 8.39435 2.44657 7.41514 2.7671C6.43594 3.08763 5.59973 3.74064 5.05145 4.61298C4.50316 5.48531 4.27736 6.52198 4.41316 7.54332C4.54896 8.56466 5.03782 9.5063 5.795 10.2051C4.34268 10.7942 3.09893 11.8029 2.22256 13.1022C1.34619 14.4016 0.876992 15.9327 0.874878 17.4999C0.874878 17.732 0.967065 17.9546 1.13116 18.1186C1.29525 18.2827 1.51781 18.3749 1.74988 18.3749ZM8.74988 4.37493C9.26905 4.37493 9.77657 4.52888 10.2083 4.81732C10.6399 5.10576 10.9764 5.51573 11.1751 5.99539C11.3737 6.47504 11.4257 7.00284 11.3244 7.51204C11.2232 8.02124 10.9731 8.48897 10.606 8.85609C10.2389 9.2232 9.77119 9.47321 9.26199 9.57449C8.75279 9.67578 8.22499 9.62379 7.74533 9.42511C7.26568 9.22643 6.85571 8.88998 6.56727 8.4583C6.27883 8.02662 6.12488 7.51911 6.12488 6.99993C6.12488 6.30374 6.40144 5.63606 6.89372 5.14377C7.38601 4.65149 8.05368 4.37493 8.74988 4.37493ZM17.681 8.13131C17.5169 7.96727 17.2944 7.87512 17.0624 7.87512C16.8304 7.87512 16.6078 7.96727 16.4438 8.13131L10.975 13.6001C10.8794 13.6964 10.8072 13.8135 10.7641 13.9422L9.67038 17.2234C9.62659 17.3549 9.61465 17.4949 9.63555 17.6319C9.65644 17.7689 9.70958 17.8989 9.79057 18.0114C9.87157 18.1238 9.97811 18.2154 10.1014 18.2786C10.2247 18.3418 10.3613 18.3748 10.4999 18.3749C10.5938 18.3748 10.6872 18.3597 10.7764 18.3303L14.0576 17.2366C14.1866 17.1935 14.3037 17.121 14.3998 17.0248L19.8685 11.5561C20.0325 11.392 20.1247 11.1694 20.1247 10.9374C20.1247 10.7054 20.0325 10.4829 19.8685 10.3188L17.681 8.13131ZM13.306 15.6414L11.8806 16.1166L12.3558 14.6912L17.0624 9.98718L18.0126 10.9374L13.306 15.6414Z" fill="#1E1E1E"/>
                                        </svg>
                                        <div style={{
                                            fontWeight: '556',
                                            fontFamily: 'SF Compact Display Medium',
                                            fontSize: '14px',
                                            color: '#1E1E1E',
                                            textTransform: 'none',
                                            textDecoration: 'none',
                                            display : 'flex',
                                            alignItems: 'center',
                                            gap: '5px',
                                        }}>
                                            {trans('lang_profile_edit')}
                                        </div>
                                    </div>
                                    <div className="text-center" style={{
                                        marginLeft: '30px',
                                        marginRight: '35px',
                                        marginBottom: '100px',
                                        display: 'flex',
                                        alignItems: 'center',
                                        justifyContent: 'space-between',
                                        position: 'relative'
                                    }}>
                                        <div style={{ display: 'flex', flexDirection: 'column', gap: '13px' }}>
                                            <a className={`${variables.header_info_item}`} href="https://b2b.itsready.be/" target="_blank"
                                               style={{
                                                   fontWeight: '556',
                                                   fontFamily: 'SF Compact Display Medium',
                                                   fontSize: '14px',
                                                   color: '#1E1E1E',
                                                   textTransform: 'none',
                                                   textDecoration: 'none',
                                                   display : 'flex',
                                                   alignItems: 'center',
                                                   gap: '15px',
                                               }}>
                                                <svg width="15" height="21" viewBox="0 0 15 21" fill="none" xmlns="http://www.w3.org/2000/svg" style={{ marginLeft: '3px'}}>
                                                    <path d="M13.6854 19.7996V20.8377C14.2587 20.8377 14.7235 20.3729 14.7235 19.7996H13.6854ZM1.22851 19.7996H0.190435C0.190435 20.3729 0.655202 20.8377 1.22851 20.8377V19.7996ZM4.34274 4.22848C3.76943 4.22848 3.30466 4.69325 3.30466 5.26656C3.30466 5.83986 3.76943 6.30463 4.34274 6.30463V4.22848ZM5.38081 6.30463C5.95414 6.30463 6.41889 5.83986 6.41889 5.26656C6.41889 4.69325 5.95414 4.22848 5.38081 4.22848V6.30463ZM4.34274 7.34271C3.76943 7.34271 3.30466 7.80747 3.30466 8.38078C3.30466 8.95411 3.76943 9.41886 4.34274 9.41886V7.34271ZM5.38081 9.41886C5.95414 9.41886 6.41889 8.95411 6.41889 8.38078C6.41889 7.80747 5.95414 7.34271 5.38081 7.34271V9.41886ZM9.53312 7.34271C8.95979 7.34271 8.49504 7.80747 8.49504 8.38078C8.49504 8.95411 8.95979 9.41886 9.53312 9.41886V7.34271ZM10.5712 9.41886C11.1445 9.41886 11.6093 8.95411 11.6093 8.38078C11.6093 7.80747 11.1445 7.34271 10.5712 7.34271V9.41886ZM9.53312 10.4569C8.95979 10.4569 8.49504 10.9217 8.49504 11.495C8.49504 12.0683 8.95979 12.5331 9.53312 12.5331V10.4569ZM10.5712 12.5331C11.1445 12.5331 11.6093 12.0683 11.6093 11.495C11.6093 10.9217 11.1445 10.4569 10.5712 10.4569V12.5331ZM4.34274 10.4569C3.76943 10.4569 3.30466 10.9217 3.30466 11.495C3.30466 12.0683 3.76943 12.5331 4.34274 12.5331V10.4569ZM5.38081 12.5331C5.95414 12.5331 6.41889 12.0683 6.41889 11.495C6.41889 10.9217 5.95414 10.4569 5.38081 10.4569V12.5331ZM9.53312 4.22848C8.95979 4.22848 8.49504 4.69325 8.49504 5.26656C8.49504 5.83986 8.95979 6.30463 9.53312 6.30463V4.22848ZM10.5712 6.30463C11.1445 6.30463 11.6093 5.83986 11.6093 5.26656C11.6093 4.69325 11.1445 4.22848 10.5712 4.22848V6.30463ZM2.88943 2.15233H12.0245V0.0761772H2.88943V2.15233ZM12.6473 2.77517V19.7996H14.7235V2.77517H12.6473ZM13.6854 18.7615H1.22851V20.8377H13.6854V18.7615ZM2.26659 19.7996V2.77517H0.190435V19.7996H2.26659ZM12.0245 2.15233C12.3323 2.15233 12.5008 2.15314 12.6218 2.16302C12.7307 2.17192 12.707 2.18274 12.6473 2.15233L13.5899 0.302467C13.3082 0.158901 13.0281 0.113153 12.7908 0.0937724C12.5656 0.0753674 12.298 0.0761772 12.0245 0.0761772V2.15233ZM14.7235 2.77517C14.7235 2.50161 14.7243 2.23401 14.706 2.00884C14.6865 1.77154 14.6408 1.49153 14.4972 1.20978L12.6473 2.15233C12.6169 2.09263 12.6277 2.06899 12.6367 2.1779C12.6465 2.29893 12.6473 2.46735 12.6473 2.77517H14.7235ZM12.6473 2.15233L14.4972 1.20978C14.2982 0.819128 13.9805 0.501508 13.5899 0.302467L12.6473 2.15233ZM2.88943 0.0761772C2.61587 0.0761772 2.34827 0.0753674 2.12309 0.0937724C1.8858 0.113153 1.60579 0.158901 1.32403 0.302467L2.26659 2.15233C2.20689 2.18274 2.18325 2.17192 2.29215 2.16302C2.41318 2.15314 2.58161 2.15233 2.88943 2.15233V0.0761772ZM2.26659 2.77517C2.26659 2.46735 2.2674 2.29893 2.27728 2.1779C2.28618 2.06899 2.297 2.09263 2.26659 2.15233L0.416725 1.20978C0.273159 1.49153 0.227411 1.77154 0.20803 2.00884C0.189625 2.23401 0.190435 2.50161 0.190435 2.77517H2.26659ZM1.32403 0.302467C0.933376 0.501508 0.615766 0.819118 0.416725 1.20978L2.26659 2.15233L1.32403 0.302467ZM4.34274 6.30463H5.38081V4.22848H4.34274V6.30463ZM4.34274 9.41886H5.38081V7.34271H4.34274V9.41886ZM9.53312 9.41886H10.5712V7.34271H9.53312V9.41886ZM9.53312 12.5331H10.5712V10.4569H9.53312V12.5331ZM4.34274 12.5331H5.38081V10.4569H4.34274V12.5331ZM9.53312 6.30463H10.5712V4.22848H9.53312V6.30463ZM8.49504 16.6854V19.7996H10.5712V16.6854H8.49504ZM6.41889 19.7996V16.6854H4.34274V19.7996H6.41889ZM7.45697 15.6473C8.03029 15.6473 8.49504 16.1121 8.49504 16.6854H10.5712C10.5712 14.9654 9.17695 13.5712 7.45697 13.5712V15.6473ZM7.45697 13.5712C5.73698 13.5712 4.34274 14.9654 4.34274 16.6854H6.41889C6.41889 16.1121 6.88364 15.6473 7.45697 15.6473V13.5712Z" fill="#1E1E1E"/>
                                                </svg>
                                                {trans('portal.trader')}?
                                            </a>
                                            <a className={`${variables.header_info_item}`}
                                               style={{
                                                   fontWeight: '556',
                                                   fontFamily: 'SF Compact Display Medium',
                                                   fontSize: '14px',
                                                   color: '#1E1E1E',
                                                   textTransform: 'none',
                                                   textDecoration: 'none',
                                                   display : 'flex',
                                                   alignItems: 'center',
                                                   gap: '10px',
                                               }}>
                                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M12 22C17.5228 22 22 17.5228 22 12C22 6.47715 17.5228 2 12 2C6.47715 2 2 6.47715 2 12C2 17.5228 6.47715 22 12 22Z" stroke="#1E1E1E" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"/>
                                                    <path d="M2 12H22" stroke="#1E1E1E" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"/>
                                                    <path d="M12 2C14.5013 4.73835 15.9228 8.29203 16 12C15.9228 15.708 14.5013 19.2616 12 22C9.49872 19.2616 8.07725 15.708 8 12C8.07725 8.29203 9.49872 4.73835 12 2Z" stroke="#1E1E1E" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"/>
                                                </svg>
                                                { trans('language') }
                                            </a>
                                        </div>

                                        <div className={`d-flex`} style={{ position: 'absolute', right: '0', bottom: '0'}}>                                           
                                            <SwitchLangMobile/>
                                        </div>
                                    </div>
                                </>
                            )
                        }

                        {isRegisterOpen && <Register togglePopup={() => togglePopupRegister()} />}
                        {isLoginOpen && <Login togglePopup={() => togglePopupLogin()} from={null} />}
                    </Modal.Body>
                </div>
                <div className="res-desktop">
                    <Modal.Body style={{ paddingLeft : '0', paddingRight: '0'}}>
                        <div className="close-popup" onClick={() => handleClose()}
                             style={ workspaceId ? {} :{
                                 fontFamily: "SF Compact Display",
                                 fontSize: '16px',
                                 fontStyle: 'normal',
                                 fontWeight: '790',
                                 lineHeight: 'normal',
                                 letterSpacing: '1.44px',
                                 paddingLeft: '20px',
                                 color: '#676767'
                             }}>
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="25" viewBox="0 0 24 25" fill="none">
                                <path d="M14 17L10 12.5L14 8" stroke="#676767" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"/>
                            </svg>
                            <div className="mt-1">{ trans('back') }</div>
                        </div>

                        <div className={`${style['navigation-title-desktop']}`}> {trans('lang_welcome_back')},
                            { tokenLoggedInCookie ?
                                <span>{ ' ' + infoUser.fullname}</span>
                                : null
                            }
                        </div>

                        <div>
                            <div className={`${style['navigation-options']}`} style={{ gap: '14px'}} onClick={() => viewMessages()}>
                                <svg width="27" height="24" viewBox="0 0 27 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M2.79678 3.68075C3.01902 3.47976 3.31015 3.35818 3.62244 3.35818H23.0973C23.4096 3.35818 23.7007 3.47976 23.9229 3.68075L13.3599 9.65224L2.79678 3.68075ZM0.17159 3.52122C0.165605 3.53113 0.159743 3.54116 0.154008 3.55131C0.0178702 3.79212 -0.0250804 4.06025 0.0135922 4.31404C0.00473018 4.41752 0.000208378 4.52216 0.000208378 4.62777V19.3726C0.000208378 21.3696 1.61694 23.0179 3.62244 23.0179H23.0973C25.1028 23.0179 26.7195 21.3696 26.7195 19.3726V4.62777C26.7195 4.52215 26.715 4.41752 26.7061 4.31404C26.7448 4.06025 26.7019 3.79213 26.5657 3.55131C26.56 3.54116 26.5541 3.53113 26.5481 3.52122C26.0819 2.05657 24.7162 0.982422 23.0973 0.982422H3.62244C2.00348 0.982422 0.637859 2.05658 0.17159 3.52122ZM24.3438 6.17198V19.3726C24.3438 20.0787 23.7696 20.6422 23.0973 20.6422H3.62244C2.95016 20.6422 2.37597 20.0787 2.37597 19.3726V6.17198L12.7753 12.0509C13.138 12.2559 13.5817 12.2559 13.9444 12.0509L24.3438 6.17198Z" fill="#1E1E1E"/>
                                </svg>

                                <div className={`${style['navigation-description']}`}> {trans('lang_report')} </div>

                                { unreadCount && unreadCount > 0 ?
                                    <span style={{
                                        backgroundColor: color ?? '#B5B268',
                                        fontFamily: 'Roboto',
                                        color: "#FFFFFF",
                                        fontWeight: '656',
                                        fontSize: '16px',
                                        borderRadius: '25px',
                                        display: 'flex',
                                        width: 'auto',
                                        height: 'auto',
                                        gap: '14px',
                                        justifyContent: 'center',
                                        padding: '0 5px',
                                    }}> { unreadCount ?? 12} </span> : null}
                            </div>
                            <div className={`${style['navigation-options']}`} style={{ gap: '13px'}} onClick={() => viewOrderHistory()}>
                                <svg width="29" height="26" viewBox="0 0 29 26" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M6.02971 13.0002C6.02972 10.4892 6.9004 8.05575 8.49342 6.11465C10.0864 4.17355 12.3032 2.84485 14.7661 2.35497C17.2289 1.86508 19.7854 2.2443 22 3.42802C24.2146 4.61174 25.9502 6.52672 26.9112 8.84666C27.8721 11.1666 27.9989 13.748 27.27 16.1509C26.5411 18.5539 25.0015 20.6298 22.9136 22.0249C20.8257 23.42 18.3187 24.0479 15.8197 23.8018C13.3207 23.5557 10.9843 22.4507 9.20873 20.6751" stroke="#1E1E1E" strokeWidth="2.41196"/>
                                    <path d="M16.4004 8.65869V14.4474L20.2595 16.377" stroke="#1E1E1E" strokeWidth="2.41196" strokeLinecap="round" strokeLinejoin="round"/>
                                    <path d="M4.93157 15.8727C5.40352 16.3496 6.1739 16.3496 6.64585 15.8727L8.76862 13.728C9.52231 12.9665 8.9829 11.6736 7.91148 11.6736H3.66595C2.59453 11.6736 2.05511 12.9665 2.8088 13.728L4.93157 15.8727Z" fill="#1E1E1E"/>
                                </svg>

                                <div className={`${style['navigation-description']}`}> {trans('lang_order_history')} </div>
                            </div>
                            <div className={`${style['navigation-options']}`} style={{ gap: '17px'}} onClick={togglePopupProfile}>
                                <svg width="24" height="20" viewBox="0 0 24 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M1.16634 19.7499H7.66634C7.95366 19.7499 8.22921 19.6358 8.43237 19.4326C8.63554 19.2294 8.74967 18.9539 8.74967 18.6666C8.74967 18.3793 8.63554 18.1037 8.43237 17.9005C8.22921 17.6974 7.95366 17.5832 7.66634 17.5832H2.32659C2.58947 15.7793 3.4924 14.1301 4.87053 12.9367C6.24865 11.7434 8.01001 11.0855 9.83301 11.0832C11.1086 11.0943 12.3479 10.6587 13.336 9.85202C14.3242 9.04529 14.999 7.91827 15.2435 6.66627C15.4879 5.41426 15.2866 4.11618 14.6745 2.997C14.0624 1.87782 13.078 1.00807 11.8919 0.538485C10.7058 0.068903 9.39283 0.0290838 8.18048 0.425929C6.96813 0.822774 5.93283 1.63127 5.254 2.7113C4.57517 3.79133 4.2956 5.07483 4.46374 6.33935C4.63187 7.60387 5.23712 8.76971 6.17459 9.63483C4.37648 10.3643 2.83659 11.6131 1.75156 13.2218C0.666539 14.8305 0.0856255 16.7261 0.0830078 18.6666C0.0830078 18.9539 0.197144 19.2294 0.400309 19.4326C0.603473 19.6358 0.879023 19.7499 1.16634 19.7499ZM9.83301 2.41658C10.4758 2.41658 11.1042 2.60719 11.6386 2.9643C12.1731 3.32142 12.5896 3.829 12.8356 4.42286C13.0816 5.01672 13.146 5.67019 13.0206 6.30062C12.8952 6.93106 12.5856 7.51016 12.1311 7.96468C11.6766 8.4192 11.0975 8.72873 10.4671 8.85413C9.83661 8.97953 9.18315 8.91517 8.58929 8.66919C7.99543 8.4232 7.48785 8.00664 7.13073 7.47218C6.77362 6.93772 6.58301 6.30937 6.58301 5.66658C6.58301 4.80463 6.92542 3.97798 7.53491 3.36848C8.1444 2.75899 8.97105 2.41658 9.83301 2.41658ZM20.8906 7.06733C20.6874 6.86424 20.4119 6.75014 20.1247 6.75014C19.8374 6.75014 19.5619 6.86424 19.3588 7.06733L12.5879 13.8382C12.4696 13.9575 12.3802 14.1024 12.3268 14.2617L10.9727 18.3242C10.9185 18.487 10.9037 18.6603 10.9296 18.8299C10.9554 18.9995 11.0212 19.1606 11.1215 19.2998C11.2218 19.439 11.3537 19.5524 11.5064 19.6306C11.659 19.7089 11.8281 19.7498 11.9997 19.7499C12.116 19.7497 12.2315 19.7311 12.342 19.6947L16.4045 18.3405C16.5641 18.2872 16.7092 18.1974 16.8281 18.0783L23.5989 11.3075C23.802 11.1043 23.9161 10.8288 23.9161 10.5416C23.9161 10.2543 23.802 9.97882 23.5989 9.77566L20.8906 7.06733ZM15.4739 16.3656L13.7092 16.9538L14.2974 15.1891L20.1247 9.36508L21.3012 10.5416L15.4739 16.3656Z" fill="#1E1E1E"/>
                                </svg>

                                <div className={`${style['navigation-description']}`}> {trans('lang_profile_edit')} </div>
                            </div>
                            <div className={`${style['navigation-options']}`} style={{ gap: '12px'}} onClick={handleLogoutClick}>
                                <svg width="30" height="30" viewBox="0 0 30 30" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M11.2396 25.1152H6.74392C6.14776 25.1152 5.57601 24.8783 5.15447 24.4568C4.73292 24.0352 4.49609 23.4635 4.49609 22.8673V7.13259C4.49609 6.53643 4.73292 5.96469 5.15447 5.54314C5.57601 5.12159 6.14776 4.88477 6.74392 4.88477H11.2396" stroke="#1E1E1E" strokeWidth="2.24782" strokeLinecap="round" strokeLinejoin="round"/>
                                    <path d="M19.1064 20.6195L24.726 14.9999L19.1064 9.38037" stroke="#1E1E1E" strokeWidth="2.24782" strokeLinecap="round" strokeLinejoin="round"/>
                                    <path d="M24.7262 15H11.2393" stroke="#1E1E1E" strokeWidth="2.24782" strokeLinecap="round" strokeLinejoin="round"/>
                                </svg>

                                <div className={`${style['navigation-description']}`}> {trans('lang_logout')} </div>
                            </div>
                        </div>
                        {isProfileOpen && <Profile togglePopup={() => togglePopupProfile()} />}
                    </Modal.Body>
                </div>
            </Modal>
        </>
    );
}