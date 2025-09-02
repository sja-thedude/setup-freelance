'use client'

import React, { useEffect, useState, useRef } from 'react';
import Image from "next/image";
import variables from '/public/assets/css/home.module.scss'
import Link from 'next/link';
import { logout } from "@/redux/slices/authSlice";
import MobileFeatures from '@/app/[locale]/components/workspace/mobile-features/list'
import "react-responsive-carousel/lib/styles/carousel.min.css";
import { Carousel } from 'react-responsive-carousel';
import { useI18n } from '@/locales/client'
import Cookies from 'js-cookie';
import 'slick-carousel/slick/slick.css';
import 'slick-carousel/slick/slick-theme.css';
import { api } from "@/utils/axios";
import Popup from '@/app/[locale]/components/workspace/info/popup'
import Maping from '@/app/[locale]/components/layouts/popup/map';
import CarouselSlider from "@/app/[locale]/components/layouts/website/CarouselSlider";
import Profile from "@/app/[locale]/components/users/profile";
import { useRouter, useSearchParams } from 'next/navigation'
import Login from "@/app/[locale]/components/users/login";
import RegisterConfirm from "@/app/[locale]/components/users/registerConfirm";
import ResetPassword from "@/app/[locale]/components/users/resetPassword";
import { useAppSelector, useAppDispatch } from '@/redux/hooks';
import { useGetWorkspaceDataByIdQuery } from '@/redux/services/workspace/workspaceDataApi'
import 'splide-nextjs/splide/dist/css/themes/splide-default.min.css';
import Loading from "@/app/[locale]/components/loading";
import { handleLogoutToken } from "@/utils/axiosRefreshToken";
import BackToPortal from "@/app/[locale]/components/layouts/website/backToPortal";
import { checkLinkBackToPortal } from '@/utils/common';
import useMediaQuery from '@mui/material/useMediaQuery'
import _ from 'lodash';
import useQueryEditProfileParam from '@/hooks/useQueryParam';
import SwitchLangMobile from "../../components/share/switchLangMobile";
import SwitchLangDesktop from "@/app/[locale]/components/share/switchLangDesktop";
import { useGetApiDataQuery } from '@/redux/services/dataTokenApi';

const recenticon = variables['recent-icon'];
const btnDark = `btn btn-dark ${variables['btn-dark']}`;
const buttonOrder = variables['button-order'];
const logged = variables['logged']
const container = variables['container']

export default function HomeComponent({ isRegisterConfirm, isResetPasswordConfirm, dataResetPassword }: { isRegisterConfirm: boolean, isResetPasswordConfirm: boolean, dataResetPassword: any }) {
    const cart = useAppSelector((state) => state.cart.rootData)
    // Get api data
    const workspaceidRef = useRef(null);


    const router = useRouter()
    const workspaceToken = useAppSelector((state) => state.workspaceData.globalWorkspaceToken)
    const { data: apiSliceData } = useGetApiDataQuery(workspaceToken);

    const dispatch = useAppDispatch();
    const workspaceId = useAppSelector((state) => state.workspaceData.globalWorkspaceId)
    const { data: apiDataToken } = useGetWorkspaceDataByIdQuery({ id: workspaceId })
    const color = useAppSelector((state) => state.workspaceData.globalWorkspaceColor)
    // Check logged token
    const tokenLoggedInCookie = Cookies.get('loggedToken');
    const [workspaceSettingFinal, setWorkspaceSettingFinal] = useState<any | null>(null);
    const language = Cookies.get('Next-Locale') ?? 'nl';

    useEffect(() => {
        workspaceidRef.current && api.get(`workspaces/` + workspaceidRef.current + '/settings', {
            headers: {
                'Authorization': `Bearer ${tokenLoggedInCookie}`,
                'Content-Language': language
            }
        }).then(res => {
            const json = res.data;
            setWorkspaceSettingFinal(json.data.meta);
        }).catch(error => {
            // console.log(error)
        });
    }, [workspaceidRef.current]);

    const [workspaceDataFinal, setWorkspaceDataFinal] = useState<any | null>(null);
    const [isShow, setIsShow] = useState(false);
    useEffect(() => {
        setTimeout(function () {
            workspaceidRef.current && api.get(`workspaces/` + workspaceidRef.current, {
                headers: {
                    'Authorization': `Bearer ${tokenLoggedInCookie}`,
                    'Content-Language': language
                }
            }).then(res => {
                const json = res.data;
                setWorkspaceDataFinal(json.data);
                setIsShow(true);
            }).catch(error => {
                // console.log(error)
            });
        }, 500);
    }, [workspaceidRef.current]);

    const generalAcc = apiDataToken?.data?.setting_preference?.holiday_text;
    const [getHoliday, setHoliday] = useState<any | null>(null);
    const [showGeneralAcc, setShowGeneralAcc] = useState(false);
    useEffect(() => {
        const fetchData = async () => {
            try {
                const res = await api.get(`workspaces/` + workspaceidRef.current + `/settings/holiday_exceptions`, {
                    headers: {
                        'Authorization': `Bearer ${tokenLoggedInCookie}`,
                    }
                });
                const json = res.data;
                const currentTime = new Date();
                const filteredData = json.data.filter((item: any) => {
                    const startTime = new Date(item.start_time + 'T00:00:00');
                    const endTime = new Date(item.end_time + 'T23:59:59');
                    return currentTime >= startTime && currentTime <= endTime;
                });

                setHoliday({
                    status: filteredData.length > 0,
                    data: filteredData
                });
            } catch (error) {
                // console.log(error)
            }
            setTimeout(function () {
                setShowGeneralAcc(true);
            }, 250);

        };

        if (workspaceidRef.current) {
            fetchData();
        }
    }, [workspaceidRef.current]);

    const setWorkspaceID = (newValue: any) => {
        workspaceidRef.current = newValue;
    };
    setWorkspaceID(workspaceId);

    // Get i18n
    const trans = useI18n()
    const [isPopupOpen, setIsPopupOpen] = useState(false);
    const [isPopupOpenDeskTop, setIsPopupOpenDeskTop] = useState(true);
    const [isRegisterConfirmOpen, setIsRegisterConfirmOpen] = useState<any | null>(isRegisterConfirm ?? false);
    const [isResetPasswordConfirmOpen, setIsResetPasswordConfirmOpen] = useState<any | null>(isResetPasswordConfirm ?? false);
    const [isLoginOpen, setIsLoginOpen] = useState<any | null>(false);
    const [hasOrder, setHasOrder] = useState<any | null>(true);
    const isMobile = useMediaQuery('(max-width: 1279px)');

    const togglePopup = () => {
        Cookies.set('setTimePopupHome', 'true', { expires: 1 });
        setIsPopupOpenDeskTop(!isPopupOpenDeskTop);

        if(isMobile) {
            setIsPopupOpen(!isPopupOpen);
        } else {
            setIsProfileOpen(!isProfileOpen);
        }        
    };

    const fetchHistory = async () => {
        const history = await api.get(`orders/history?limit=15&page=1&workspace_id=${workspaceId}`, {
            headers: {
                'Authorization': `Bearer ${tokenLoggedInCookie}`,
                'App-Token': workspaceToken,
                'Content-Language': language
            }
        });

        if(history?.data?.data?.total > 0) {
            setHasOrder(true);
        } else {
            setHasOrder(false);
        }
    }

    useEffect(() => {
        if(tokenLoggedInCookie) {
            fetchHistory();
        }
    }, [
        tokenLoggedInCookie,
        workspaceToken
    ]);

    useEffect(function () {
        const cookieValue = Cookies.get('setTimePopupHome');
        cookieValue && setIsPopupOpenDeskTop(false);
    }, []);

    const query = new URLSearchParams(window.location.search);
    const queryEditProfile = useQueryEditProfileParam();

    useEffect(() => {
        if (queryEditProfile === true) {
            setIsProfileOpen(true);
        }
        if (query.get('login') === 'true') {
            setIsLoginOpen(true);
        }
    }, [
        queryEditProfile, 
        query.get('login')
    ]);

    const togglePopupResetPasswordConfirm = () => {
        setIsResetPasswordConfirmOpen(!isResetPasswordConfirmOpen);
    }

    const togglePopupRegisterConfirm = () => {
        setIsRegisterConfirmOpen(!isRegisterConfirmOpen);
    }

    useEffect(() => {
        if (query.get('registerConfirm') === 'true') {
            setIsRegisterConfirmOpen(true);
        }

        if (query.get('resetPasswordConfirm') === 'true') {
            setIsResetPasswordConfirmOpen(true);
        }
    }, []);

    //get infomation user
    const [infoUser, setInfoUser] = useState({
        avatar: '',
        fullname: '',
        photo: '',
    });

    //logout
    const handleLogoutClick = async () => {
        dispatch(logout());
        // Remove cookie 'loggedToken'
        handleLogoutToken();
        window.location.reload();
    };

    //popup profile
    const [isProfileOpen, setIsProfileOpen] = useState<any | null>(false);
    const togglePopupProfile = () => {
        setIsProfileOpen(!isProfileOpen);
    }

    useEffect(() => {
        tokenLoggedInCookie && api.get(`profile`, {
            headers: {
                'Authorization': `Bearer ${tokenLoggedInCookie}`,
                'Content-Language': language
            }
        }).then(res => {
            const json = res.data;
            if (json.data.first_name && !json.data.last_name) {
                setInfoUser({
                    avatar: json.data.first_name.substring(0, 1),
                    fullname: json.data.first_name,
                    photo: json.data.photo,
                })
            } else if (json.data.last_name && !json.data.first_name) {
                setInfoUser({
                    avatar: json.data.last_name.substring(0, 1),
                    fullname: json.data.last_name,
                    photo: json.data.photo,
                })
            } else {
                setInfoUser({
                    avatar: json.data.first_name.substring(0, 1) + json.data.last_name.substring(0, 1),
                    fullname: json.data.first_name + ' ' + json.data.last_name,
                    photo: json.data.photo,
                })
            }

        }).catch(error => {
            // console.log(error)
        });
    }, [isProfileOpen]);
    //hover profile
    const [isHoverLogout, setHoverLogout] = useState<any | null>(false);
    const toggleHoverLogout = () => {
        setHoverLogout(!isHoverLogout);
    }

    // count unread notification
    const [unreadCount, setUnreadCount] = useState(0);
    useEffect(() => {
        const response = tokenLoggedInCookie && api.get(`notifications`, {
            headers: {
                'Authorization': `Bearer ${tokenLoggedInCookie}`,
                'App-Token': workspaceToken
            }
        }).then(res => {
            const json = res.data;
            if (json.data) {
                setUnreadCount(json.data.total_unread);
                return json.data;
            } else {
                return [];
            }
        }).catch(error => {
            // console.log(error)
        });
    }, [unreadCount]);

    const togglePopupLogin = () => {
        setIsLoginOpen(!isLoginOpen);
    }

    const handleCate = () => {
        router.push(checkLinkBackToPortal("/category/products"));
    }

    const fakeElement = {
        id: 'fake',
        active: true,
        // Thêm các thuộc tính khác nếu cần thiết
    };

    const [slides, setSlides] = useState<any[] | null>(null);

    useEffect(() => {
        if (workspaceSettingFinal && workspaceSettingFinal.length > 0) {
            const slides = [...workspaceSettingFinal];
            slides.push(fakeElement);
            const activeItems = slides.filter(item => item.active === true);
            setSlides(activeItems);
        }
    }, [workspaceSettingFinal]);

    return (
        <>
            {isMobile ? (
                <>
                    {!isShow && <div className="container"><Loading /></div>}
                    <div className={`${variables.home} ${variables.fix_scroll} container justify-content-center ps-2 pe-2  flex-col h-screen overflow-hidden` + `${variables.fix_scroll}`}>                                                
                        <div className={`${variables.shadower} row`}>            
                            <Image
                                className={variables.cutting}
                                alt="intro"
                                src="/img/cutting.png"
                                width={200}
                                height={110}
                                sizes="150vw"
                            />
                            { workspaceDataFinal?.photo && (
                                <div className={`${variables.imageCircle} mt-3`}>
                                    <Image
                                        alt="intro"
                                        src={workspaceDataFinal ? workspaceDataFinal?.photo : ''}
                                        width={110}
                                        height={105}
                                        sizes="100vw"
                                        style={{ borderRadius: "50%" }}
                                    />
                                </div>
                            )}
                        </div>
                        <div className={`row`} style={{ position: 'relative', minHeight: "55vh" }}>
                            <span style={{
                                width: 'fit-content',
                                position: 'absolute',
                                fontSize: '15px',
                                cursor: 'pointer',
                                color: 'white',
                                zIndex: "100",
                                top: "25px",
                                left: '15px',
                            }}>
                                <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg" onClick={togglePopup} className='d-flex '>
                                    <circle cx="10" cy="10" r="9.5" stroke="white" />
                                    <path d="M10.8751 7.244C10.5671 7.244 10.3058 7.13667 10.0911 6.922C9.87644 6.70733 9.76911 6.446 9.76911 6.138C9.76911 5.83 9.87644 5.56867 10.0911 5.354C10.3058 5.13 10.5671 5.018 10.8751 5.018C11.1831 5.018 11.4444 5.13 11.6591 5.354C11.8831 5.56867 11.9951 5.83 11.9951 6.138C11.9951 6.446 11.8831 6.70733 11.6591 6.922C11.4444 7.13667 11.1831 7.244 10.8751 7.244ZM9.92311 15.084C9.47511 15.084 9.11111 14.944 8.83111 14.664C8.56044 14.384 8.42511 13.964 8.42511 13.404C8.42511 13.1707 8.46244 12.8673 8.53711 12.494L9.48911 8H11.5051L10.4971 12.76C10.4598 12.9 10.4411 13.0493 10.4411 13.208C10.4411 13.3947 10.4831 13.53 10.5671 13.614C10.6604 13.6887 10.8098 13.726 11.0151 13.726C11.1831 13.726 11.3324 13.698 11.4631 13.642C11.4258 14.1087 11.2578 14.468 10.9591 14.72C10.6698 14.9627 10.3244 15.084 9.92311 15.084Z" fill="white" />
                                </svg>
                            </span>

                            {(!tokenLoggedInCookie || !hasOrder) && (
                                <SwitchLangMobile origin="home-page"
                                    customArrow="-white" 
                                    customCss={{    
                                        width: 'fit-content',
                                        position: 'absolute',
                                        fontSize: '15px',
                                        cursor: 'pointer',
                                        color: 'white',
                                        zIndex: 100,
                                        top: '25px',
                                        left: '48px'
                                    }}/>
                            )}

                            {getHoliday && getHoliday.status && (
                                <div className={`${variables.holidayMobile} d-flex`}>
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" className={variables.calandarSvg}>
                                        <path d="M19 4H5C3.89543 4 3 4.89543 3 6V20C3 21.1046 3.89543 22 5 22H19C20.1046 22 21 21.1046 21 20V6C21 4.89543 20.1046 4 19 4Z" stroke="white" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" />
                                        <path d="M16 2V6" stroke="white" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" />
                                        <path d="M8 2V6" stroke="white" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" />
                                        <path d="M3 10H21" stroke="white" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" />
                                    </svg>
                                    <div className={`${variables.holidayMobileText} ms-2`}>
                                        <div style={{whiteSpace: "pre-line"}}>
                                            {getHoliday.data[0].description}
                                        </div>
                                    </div>
                                </div>
                            )}

                            {(
                                <>
                                    {
                                        unreadCount > 0 && (
                                            <div style={{ color: color }} className={variables['number-notification']}>{unreadCount}</div>
                                        )
                                    }
                                    {tokenLoggedInCookie && (
                                        <div className={`${recenticon}`}>
                                            <Link href="/function/recent" legacyBehavior >
                                                <svg xmlns="http://www.w3.org/2000/svg" width="23" height="22" viewBox="0 0 23 22" fill="none">
                                                    <path d="M12 6V12L16 14" stroke="white" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" />
                                                    <path fillRule="evenodd" clipRule="evenodd" d="M4.51548 6C6.13 3.58803 8.87951 2 11.9999 2C16.9705 2 20.9999 6.02944 20.9999 11C20.9999 15.9706 16.9705 20 11.9999 20C7.36737 20 3.5523 16.5 3.05486 12H1.04477C1.55 17.6065 6.26188 22 11.9999 22C18.0751 22 22.9999 17.0751 22.9999 11C22.9999 4.92487 18.0751 0 11.9999 0C7.72524 0 4.02006 2.43832 2.19935 6H4.51548Z" fill="white" />
                                                    <path d="M2.81609 8.88327L1.73205 4.3486L6.60965 6.17268L2.81609 8.88327Z" fill="white" />
                                                </svg>
                                            </Link>
                                            <Link href='/notifications'>
                                                <svg className={`ms-2`} width="22" height="18" viewBox="0 0 22 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M21 9H15L13 12H9L7 9H1" stroke="white" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" />
                                                    <path d="M4.45 2.11L1 9V15C1 15.5304 1.21071 16.0391 1.58579 16.4142C1.96086 16.7893 2.46957 17 3 17H19C19.5304 17 20.0391 16.7893 20.4142 16.4142C20.7893 16.0391 21 15.5304 21 15V9L17.55 2.11C17.3844 1.77679 17.1292 1.49637 16.813 1.30028C16.4967 1.10419 16.1321 1.0002 15.76 1H6.24C5.86792 1.0002 5.50326 1.10419 5.18704 1.30028C4.87083 1.49637 4.61558 1.77679 4.45 2.11V2.11Z" stroke="white" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" />
                                                </svg>
                                            </Link>
                                        </div>
                                    )}
                                </>
                            )}
                            <div className={`${tokenLoggedInCookie ? variables.containLogged : variables.contain}`}>
                                <Carousel className={`${tokenLoggedInCookie ? logged : variables.carousel}`} autoPlay={true} interval={10000} infiniteLoop={true} showArrows={false} showStatus={false} showThumbs={false}
                                >
                                    {workspaceDataFinal && workspaceDataFinal.api_gallery.map((item: any, index: number) => (
                                        <div
                                            key={index}
                                            className={`${variables.silde}`}
                                            style={{
                                                backgroundImage: `url('${item.full_path}')`,
                                            }}
                                        >
                                        </div>

                                    ))}
                                </Carousel>
                            </div>
                            <div className={`${variables.information} ms-3 d-flex`}>
                                {workspaceDataFinal ? workspaceDataFinal?.setting_generals?.title : ''}
                            </div>

                            <div className={`${variables.address} ms-3 d-flex`}>
                                {workspaceDataFinal ? workspaceDataFinal?.setting_generals?.subtitle : ''}
                            </div>
                        </div>
                        {/* Hiển thị Popup nếu trạng thái là mở */}
                        {isPopupOpen && (
                            <Maping data={apiSliceData ? apiSliceData.data : apiDataToken?.data} 
                                workspaceId={workspaceId ? workspaceId : ''} 
                                color={workspaceDataFinal ? workspaceDataFinal?.setting_generals?.primary_color : 'black'} 
                                togglePopup={togglePopup}
                                origin="home" 
                            />
                        )}
                        <div className={`row text-center  ${buttonOrder}`}>
                            <div onClick={handleCate} >
                                <button type="button" className={`${btnDark} border-0 text-uppercase`}
                                    style={{
                                        width: "100%",
                                        backgroundColor: color ?? 'white',
                                    }} >
                                    {_.isEmpty(cart) ? trans('start-order') : trans('order_continuous')}                                    
                                </button>
                            </div>
                        </div>

                        <div className={`${variables.notScrolled} row text-center mt-3`} style={{ padding: '0', overflowX: 'auto' }}>
                            {slides && slides.length > 1 && (
                                <div className="d-flex flex-nowrap">
                                    {slides.map((item: any) => (
                                        <div key={item.id} className={`d-inline-block ${container}`}>
                                            {item.id !== 'fake' ? (
                                                <MobileFeatures workspaceDataItem={item} color={color ?? 'white'} data={apiSliceData ? apiSliceData.data : null} />
                                            ) : (
                                                <div style={{ width: '20px', height: '100%' }}>
                                                </div>
                                            )}
                                        </div>
                                    ))}
                                </div>
                            )}
                        </div>
                    </div>
                </>
            ) : (
                <div className={'justify-content-center ps-2 pe-2 ' + `${variables.fix_scroll}`}>
                    <div className={`row`}>
                        <div className={`${variables.home_header}`}>
                            <div className={`${variables.home_logo}`}>
                                <BackToPortal style={{ marginLeft: '30px' }} />

                                {workspaceDataFinal?.photo && (
                                    <Image
                                        alt={workspaceDataFinal ? workspaceDataFinal?.name : 'intro'}
                                        src={workspaceDataFinal ? workspaceDataFinal?.photo : ''}
                                        width={120}
                                        height={120}
                                        sizes="100vw"
                                        style={{ borderRadius: "50%", objectFit: "cover" }}
                                        className={`${variables.logo}`}
                                    />
                                )}

                                <div className={`${variables.info}`}>
                                    <strong>{workspaceDataFinal ? workspaceDataFinal?.setting_generals?.title : ''}</strong>
                                    <span>{workspaceDataFinal ? workspaceDataFinal?.setting_generals.subtitle : ''}</span>
                                </div>
                            </div>
                            <div className={`${variables.login_link} d-flex align-items-center gap-3`}>
                                <SwitchLangDesktop/>

                                {!tokenLoggedInCookie ?
                                    <Link href="#" onClick={togglePopupLogin} onMouseEnter={toggleHoverLogout} onMouseLeave={toggleHoverLogout}>{trans('log-in')}
                                        <span className={`${variables.login_link_span}`}>
                                            {
                                                isHoverLogout ? <svg xmlns="http://www.w3.org/2000/svg" width="26" height="26" viewBox="0 0 26 26" fill="none">
                                                    <path d="M16.25 3.25H20.5833C21.158 3.25 21.7091 3.47827 22.1154 3.8846C22.5217 4.29093 22.75 4.84203 22.75 5.41667V20.5833C22.75 21.158 22.5217 21.7091 22.1154 22.1154C21.7091 22.5217 21.158 22.75 20.5833 22.75H16.25" stroke="#C4C4C4" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" />
                                                    <path d="M10.8335 18.4166L16.2502 13L10.8335 7.58331" stroke="#C4C4C4" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" />
                                                    <path d="M16.25 13H3.25" stroke="#C4C4C4" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" />
                                                </svg> : <svg xmlns="http://www.w3.org/2000/svg" width="26" height="26" viewBox="0 0 26 26" fill="none">
                                                    <path d="M16.25 3.25H20.5833C21.158 3.25 21.7091 3.47827 22.1154 3.8846C22.5217 4.29093 22.75 4.84203 22.75 5.41667V20.5833C22.75 21.158 22.5217 21.7091 22.1154 22.1154C21.7091 22.5217 21.158 22.75 20.5833 22.75H16.25" stroke="white" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" />
                                                    <path d="M10.8335 18.4166L16.2502 13L10.8335 7.58331" stroke="white" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" />
                                                    <path d="M16.25 13H3.25" stroke="white" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" />
                                                </svg>
                                            }
                                        </span>
                                    </Link>
                                    :
                                    <div className={`${variables.home_avatar}`}>
                                        {infoUser.photo
                                            ?
                                            <span onClick={togglePopupProfile} className={`${variables.home_avatar_photo}`}><img src={infoUser.photo} alt={infoUser.avatar} /></span>
                                            :
                                            <span onClick={togglePopupProfile} style={{ background: color ?? '#D87833' }}>{infoUser.avatar}</span>
                                        }
                                        <span className="username-limit" onClick={togglePopupProfile}>{infoUser.fullname}</span>
                                        <Link href="#" onClick={handleLogoutClick} onMouseEnter={toggleHoverLogout} onMouseLeave={toggleHoverLogout}>
                                            {
                                                isHoverLogout ? <svg xmlns="http://www.w3.org/2000/svg" width="26" height="27" viewBox="0 0 26 27" fill="none">
                                                    <path d="M10 22.2215H6C5.46957 22.2215 4.96086 22.0108 4.58579 21.6357C4.21071 21.2606 4 20.7519 4 20.2215V6.2215C4 5.69106 4.21071 5.18236 4.58579 4.80728C4.96086 4.43221 5.46957 4.2215 6 4.2215H10" stroke="#C4C4C4" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" />
                                                    <path d="M17 18.2215L22 13.2215L17 8.2215" stroke="#C4C4C4" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" />
                                                    <path d="M22 13.2215H10" stroke="#C4C4C4" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" />
                                                </svg> : <svg xmlns="http://www.w3.org/2000/svg" width="26" height="27" viewBox="0 0 26 27" fill="none">
                                                    <path d="M10 22.2215H6C5.46957 22.2215 4.96086 22.0108 4.58579 21.6357C4.21071 21.2606 4 20.7519 4 20.2215V6.2215C4 5.69106 4.21071 5.18236 4.58579 4.80728C4.96086 4.43221 5.46957 4.2215 6 4.2215H10" stroke="white" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" />
                                                    <path d="M17 18.2215L22 13.2215L17 8.2215" stroke="white" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" />
                                                    <path d="M22 13.2215H10" stroke="white" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" />
                                                </svg>
                                            }
                                        </Link>
                                    </div>
                                }
                            </div>
                        </div>
                        <div className={`${variables.home_carousel_group}`}>
                            <CarouselSlider workspaceDataFinal={workspaceDataFinal} />
                        </div>

                        <div className={`${variables.home_bottom}`}>
                            <div onClick={handleCate}><p>{trans('home-bottom1')}</p></div>
                            <div onClick={handleCate}><p>{trans('home-bottom2')}</p></div>
                        </div>
                    </div>

                    {/*Open popup when status true*/}
                    {(showGeneralAcc && generalAcc) ? (
                        <div className={`${variables.popupGroupSetting} z-index-1`}>
                            <div className={variables.popupContentOverlay} style={{
                                background: color ?? '#D87833',
                                filter: 'drop-shadow(0px 0px 60px ' + (color ?? 'rgba(216, 120, 51, 0.2)') + ')'
                            }}></div>
                            <Popup data={apiDataToken?.data} workspaceId={workspaceId ? workspaceId : ''} holiday={getHoliday}>
                            </Popup>
                        </div>
                    ) : (
                        getHoliday && getHoliday.status && (
                            <div className={`${variables.popupGroupSetting} z-index-1`}>
                                <div className={variables.popupContentOverlay} style={{
                                    background: color ?? '#D87833',
                                    filter: 'drop-shadow(0px 0px 60px ' + (color ?? 'rgba(216, 120, 51, 0.2)') + ')'
                                }}></div>
                                <Popup data={apiDataToken?.data} workspaceId={workspaceId ? workspaceId : ''} holiday={getHoliday}>
                                </Popup>
                            </div>
                        )
                    )}
                </div>
            )}

            {isProfileOpen && <Profile togglePopup={() => togglePopup()} />}

            {isLoginOpen && <Login togglePopup={() => togglePopupLogin()} from={null} />}

            {isRegisterConfirmOpen && <RegisterConfirm togglePopup={() => togglePopupRegisterConfirm()} />}

            {isResetPasswordConfirmOpen && <ResetPassword togglePopup={() => togglePopupResetPasswordConfirm()} token={dataResetPassword?.token} email={dataResetPassword?.email} />}
        </>
    );
};
