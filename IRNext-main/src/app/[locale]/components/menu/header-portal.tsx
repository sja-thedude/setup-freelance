"use client"

import variables from '/public/assets/css/portal.module.scss';
import variablesMenu from '/public/assets/css/menu.module.scss';
import 'public/assets/css/common.scss';
import { useI18n } from '@/locales/client'
import * as config from "@/config/constants";
import { useRouter, usePathname } from 'next/navigation'
import {includes as _includes} from 'lodash'
import { useAppSelector } from '@/redux/hooks'
import React, { useEffect, useRef, useState } from "react";
import { api } from "@/utils/axios";
import { useSelector } from "react-redux";
import Cookies from "js-cookie";
import Image from "next/image";
import moment from 'moment';
import { getDay } from 'date-fns';
import { rootCartDatetime, addStepCategory, changeTypeNotActiveErrorMessage, changeTypeNotActiveErrorMessageContent } from '@/redux/slices/cartSlice'
import { useAppDispatch } from '@/redux/hooks'
import { selectApiProfileData } from "@/redux/slices/profileSlice";
import Link from 'next/link'
import Profile from "@/app/[locale]/components/users/profile";
import RegisterConfirm from "@/app/[locale]/components/users/registerConfirm";
import Login from "@/app/[locale]/components/users/login";
import styled from "styled-components";
import NavigationPortalPopup from "@/app/[locale]/components/portal/navigation-portal-popup";
import { checkOrderTypeActive } from "@/services/workspace";
import useQueryEditProfileParam from '@/hooks/useQueryParam';
import SwitchLangDesktop from "@/app/[locale]/components/share/switchLangDesktop";

const CustomScrollbar = styled.div`
    width: 609px;
    max-height: calc(139px * 4);
    overflow-y: auto;
    scrollbar-width: thin;
    scrollbar-color: ${props => props.color} #E3E3E3;
    &::-webkit-scrollbar {
        width: 5px;
        height: 95px;
    }

    /* Handle khi rê chuột qua */
    &::-webkit-scrollbar-thumb {
        background: ${props => props.color};
    }

    /* Track */
    &::-webkit-scrollbar-track {
        background: #E3E3E3;
    }

    /* Handle khi hover */
    &::-webkit-scrollbar-thumb:hover {
        background: #555;
    }
`;

const CustomScrollbarHover = styled.div`
    opacity: 1;
    visibility: visible;
    width: 429px;
    position: absolute;
    top: 0px;
    right: 100%;
    transition: 0.3s;
    min-height: calc(139px * 4);
    max-height: calc(139px * 4);
    overflow-y: auto;
    padding-left: 20px;
    
    padding-top: 20px;
    scrollbar-width: thin;
    scrollbar-color: ${props => props.color} #E3E3E3;

    &::-webkit-scrollbar {
        width: 5px;
        height: 95px;
    }

    /* Handle khi rê chuột qua */
    &::-webkit-scrollbar-thumb {
        background: ${props => props.color};
    }

    /* Track */
    &::-webkit-scrollbar-track {
        background: #E3E3E3;
    }

    /* Handle khi hover */
    &::-webkit-scrollbar-thumb:hover {
        background: #555;
    }

    @media screen and (max-width: 1190px){
        width: 300px;
    }
`;

const CustomScrollbarNotification = styled.div`
    width: 600px;
    position: absolute;
    right:0px;
    top: 40px;
    overflow-y: auto;
    max-height: calc(236px * 3);
    border-radius: 2px;
    background: #FFFFFF;
    box-shadow: -5px 40px 40px 2px rgba(80, 80, 80, 0.15);

    scrollbar-width: thin;
    scrollbar-color: ${props => props.color} #E3E3E3;

    &::-webkit-scrollbar {
        width: 5px;
        height: 95px;
    }

    /* Handle khi rê chuột qua */
    &::-webkit-scrollbar-thumb {
        background: ${props => props.color};
    }

    /* Track */
    &::-webkit-scrollbar-track {
        background: #E3E3E3;
    }

    /* Handle khi hover */
    &::-webkit-scrollbar-thumb:hover {
        background: #555;
    }

    @media screen and (max-width: 1190px){
        width: 600px;
    }
`;

export default function HeaderPortal(props: any) {
    const { handleRedirect } = props
    const globalLocale = useAppSelector((state) => state.auth.globalLocale)
    const currentLanguage = Cookies.get('Next-Locale') ?? globalLocale
    const color = '#B5B268';
    const routerPath = usePathname()
    const router = useRouter()
    const trans = useI18n()
    let activeMenu = 'search'

    if (_includes(routerPath, '/home')) {
        activeMenu = 'home'
    } else if (_includes(routerPath, '/profile')) {
        activeMenu = 'account'
    } else if (_includes(routerPath, '/terms-and-conditions')) {
        activeMenu = 'terms-and-conditions'
    } else if (_includes(routerPath, '/privacy-policy')) {
        activeMenu = 'privacy-policy'
    } else if (_includes(routerPath, '/cookie-policy')) {
        activeMenu = 'cookie-policy'
    }

    // Check logged token
    const tokenLoggedInCookie = Cookies.get('loggedToken');

    // Get api data
    const workspaceToken = useAppSelector((state) => state.workspaceData.globalWorkspaceToken)
    const workspaceId = useAppSelector((state) => state.workspaceData.globalWorkspaceId)
    const dispatch = useAppDispatch();
    const [isProfileOpen, setIsProfileOpen] = useState<any | null>(false);
    const [isRegisterConfirmOpen, setIsRegisterConfirmOpen] = useState<any | null>(false);
    const [isLoginOpen, setIsLoginOpen] = useState<any | null>(false);
    const [isNavigationOpen, setIsNavigationOpen] = useState<any | null>(false);

    const togglePopupRegisterConfirm = () => {
        setIsRegisterConfirmOpen(!isRegisterConfirmOpen);
    }

    const togglePopupNavigation = () => {
        setPopupHistoryOrder(false);
        setToggleReadNotifications(false);
        setIsNavigationOpen(!isNavigationOpen);
    }

    useEffect(() => {
        dispatch(changeTypeNotActiveErrorMessage(false));
        const hasRefreshData = sessionStorage.getItem('hasRefreshData');

        if (!hasRefreshData) {
            dispatch(rootCartDatetime(null));
            dispatch(addStepCategory(1));
            sessionStorage.setItem('hasRefreshData', 'true');
        }
    }, [])

    const query = new URLSearchParams(window.location.search);

    useEffect(() => {
        if (query.get('registerConfirm') === 'true') {
            setIsRegisterConfirmOpen(true);
        }
        if (query.get('login') === 'true') {
            router.push(window.location.href.replace('?login=true', ''));
            router.push(window.location.href.replace('&login=true', ''));
        }
    }, []);

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

    const togglePopupLogin = () => {
        setIsLoginOpen(!isLoginOpen);
    }

    if (_includes(routerPath, '/home')) {
        activeMenu = 'home'
    } else if (_includes(routerPath, '/account')) {
        activeMenu = 'account'
    }

    //slice data informatin user
    var apiSliceProfile = useSelector(selectApiProfileData);

    //get infomation user
    const [infoUser, setInfoUser] = useState({
        avatar: '',
        fullname: '',
        photo: '',
    });

    const language = Cookies.get('Next-Locale') ?? 'nl';

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

    useEffect(() => {
        if(apiSliceProfile){
            if(apiSliceProfile.data.first_name && !apiSliceProfile.data.last_name){
                setInfoUser({
                    avatar: apiSliceProfile.data.first_name.substring(0, 1),
                    fullname: apiSliceProfile.data.first_name,
                    photo: apiSliceProfile.data.photo,
                })
            }else if(apiSliceProfile.data.last_name && !apiSliceProfile.data.first_name){
                setInfoUser({
                    avatar: apiSliceProfile.data.last_name.substring(0, 1),
                    fullname: apiSliceProfile.data.last_name,
                    photo: apiSliceProfile.data.photo,
                })
            }else{
                setInfoUser({
                    avatar: apiSliceProfile.data.first_name.substring(0, 1) + apiSliceProfile.data.last_name.substring(0, 1),
                    fullname: apiSliceProfile.data.first_name + ' ' + apiSliceProfile.data.last_name,
                    photo: apiSliceProfile.data.photo,
                })
            }
        }
    }, [apiSliceProfile]);

    const togglePopup = () => {
        setIsProfileOpen(!isProfileOpen);
    }

    const formatDate = (time?: string, timeFormat?: string, outputFormat?: string) => {
        let hourOffset = new Date().getTimezoneOffset() / 60;

        if (hourOffset < 0) {
            return moment(time, timeFormat).locale(language).add(-hourOffset, 'hours').format(outputFormat);
        } else {
            return moment(time, timeFormat).locale(language).subtract(hourOffset, 'hours').format(outputFormat);
        }
    }

    function getWeekName(date: any) {
        const weekDays = trans('weekName').split(', ');
        const dayOfWeek = getDay(date);
        const weekName = weekDays[dayOfWeek];
        return weekName;
    }

    const [orderDetailsCache, setOrderDetailsCache] = useState<{ [key: string]: any }>({});
    const [getHistoryHover, setHistoryHover] = useState<any | null>(null);
    const handleOrderHover = async (orderId: any) => {
        if (!orderDetailsCache[orderId]) {
            try {
                const response = await api.get(`orders/${orderId}`, {
                    headers: {
                        'Authorization': `Bearer ${tokenLoggedInCookie}`,
                        'App-Token': workspaceToken,
                        'Content-Language': language
                    }
                });

                const json = response.data;
                if (json.data) {
                    const updatedCache = { ...orderDetailsCache };
                    updatedCache[orderId] = json.data;
                    setOrderDetailsCache(updatedCache);
                    setHistoryHover(json.data);
                }
            } catch (error) {
                console.error(`Lỗi khi lấy thông tin đơn hàng cho ID đơn hàng ${orderId}:`, error);
            }
        } else {
            setHistoryHover(orderDetailsCache[orderId]);
        }
    };

    //get history order
    const [getHistory, setHistory] = useState<any | null>(null);
    const [popupHistoryOrder, setPopupHistoryOrder] = useState<any | null>(false);
    const toggleHistoryOrder = () => {
        if (popupHistoryOrder) {
            // Prevent duplicate show
            return;
        }

        setPopupHistoryOrder(true);

        if (isNavigationOpen) {
            // Hide navigation popup
            setIsNavigationOpen(false);
        }

        setTimeout(function(){
            setLoadingHistory(false);
        },1000);

        if(popupHistoryOrder === false){
            api.get(`orders/history?limit=15&page=1`, {
                headers: {
                    'Authorization': `Bearer ${tokenLoggedInCookie}`,
                    'App-Token': workspaceToken,
                    'Content-Language': language
                }
            }).then(res => {
                const json = res.data;
                
                if (json.data) {
                    const filteredData = json?.data?.data.filter((item:any) => (item.payment_method === 0 || item.payment_method_display === "Mollie") && item.status === 2);
                    // Thêm vào các bản ghi không có mollie
                    json?.data?.data.forEach((item:any) => {
                        if (item.payment_method !== 0 && item.payment_method_display !=="Mollie") {
                            filteredData.push(item);
                        }
                    });
                    filteredData.sort((prev:any, next:any) => next.id - prev.id);
                    setHistory(filteredData);
                }
            }).catch(error => {
                // console.log(error)
            });
        }else{
            setLoadingHistory(true);
        }
    }

    const toggleHistoryOrderHide = () => {
        setPopupHistoryOrder(false);
    }

    const [popupHistoryHover, setPopupHistoryHover] = useState<any | null>(false);
    const showPopupHistoryHoverEnter = () => {
        setPopupHistoryHover(true);
    }
    const showPopupHistoryHoverLeave = () => {
        setPopupHistoryHover(false);
    }

    const [getLoadingHistory, setLoadingHistory] = useState<any | null>(true);
    const [getCountHistory, setCountHistory] = useState<any | null>(2);
    const scrollableDivRef = useRef<HTMLDivElement | null>(null);

    const handleScroll = () => {
        if (
            scrollableDivRef.current &&
            scrollableDivRef.current.scrollTop + scrollableDivRef.current.clientHeight ===
            scrollableDivRef.current.scrollHeight
        ) {
            const response = api.get(`orders/history?limit=15&page=`+getCountHistory+`&workspace_id=${workspaceId}`, {
                headers: {
                    'Authorization': `Bearer ${tokenLoggedInCookie}`,
                    'App-Token': workspaceToken,
                    'Content-Language': language
                }
            }).then(res => {
                const json = res.data;
                if (json.data) {
                    const updatedHistory = [...getHistory, ...json.data.data];
                    setHistory(updatedHistory);
                } else {
                    return [];
                }
            }).catch(error => {
                // console.log(error)
            });
            setCountHistory(getCountHistory + 1);
        }
    };

    useEffect(() => {
        const divElement = scrollableDivRef.current;

        if (divElement) {
            divElement.addEventListener('scroll', handleScroll);
        }

        return () => {
            if (divElement) {
                divElement.removeEventListener('scroll', handleScroll);
            }
        };
    }, []);

    const paymentStatus = [
        trans('unknown'),
        trans('pending'),
        trans('paid'),
        trans('cancelled'),
        trans('failed'),
        trans('expired'),
    ];

    const paymentMethods = [
        trans('online-method'),
        null,
        trans('cash'),
        trans('on-invoice')
    ];

    const [readNotifications, setReadNotifications] = useState<any[]>([]);
    //read notification
    const handleReadClick = (id: number) => {
        setToggleReadNotifications(true)
        api.get(`/notifications/read?id=${id}`, {
            headers: {
                'Authorization': `Bearer ${tokenLoggedInCookie}`,
                'Content-Language': language
            }
        }).then(res => {
            const json = res.data;
            if (json.success) {
                const newData = readNotifications.concat([id]);
                setReadNotifications(newData);

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
                        return json.data;
                    } else {
                        return [];
                    }
                }).catch(error => {
                    // console.log(error)
                });
            }
        }).catch(error => {
            // console.log(error)
        });
    };

    const [getCountNotifications, setCountNotifications] = useState<any | null>(2);
    const scrollableDivRefNotifications = useRef<HTMLDivElement | null>(null);

    const handleScrollNotifications = () => {
        if (processingToggle) return;
        if (
            scrollableDivRefNotifications.current &&
            scrollableDivRefNotifications.current.scrollTop + scrollableDivRefNotifications.current.clientHeight ===
            scrollableDivRefNotifications.current.scrollHeight
        ) {
            api.get(`/notifications?limit=15&page=`+getCountNotifications, {
                headers: {
                    'Authorization': `Bearer ${tokenLoggedInCookie}`,
                    'App-Token': workspaceToken,
                    'Content-Language': language
                }
            }).then(res => {
                const json = res.data;
                if (json.data) {
                    const updatedNotifications = [...notifications, ...json.data.data];
                    setNotifications(updatedNotifications);
                } else {
                    return [];
                }
            }).catch(error => {
                // console.log(error)
            });
            setCountNotifications(getCountNotifications + 1);
        }
    };

    const [notifications, setNotifications] = useState<any[]>([]);
    const [toggleNotifications, setToggleReadNotifications] = useState<any | null>(false);
    const [processingToggle, setProcessingToggle] = useState<any | null>(false);

    const handleToggleNotifications = () => {
        if (processingToggle || toggleNotifications) {
            // Prevent duplicate show
            return;
        }

        setProcessingToggle(true);
        setToggleReadNotifications(true);

        if (isNavigationOpen) {
            // Hide navigation popup
            setIsNavigationOpen(false);
        }

        api.get(`/notifications?limit=15&page=1`, {
            headers: {
                'Authorization': `Bearer ${tokenLoggedInCookie}`,
                'App-Token': workspaceToken,
                'Content-Language': language
            }
        }).then(res => {
            const json = res.data;
            if (json.data) {
                setNotifications(json.data.data);
            } else {
                return [];
            }
        }).catch(error => {
            // console.log(error)
        }).finally(() => {
            setProcessingToggle(false);
        });
    }

    const handleToggleNotificationsHide = () => {
        setProcessingToggle(false);
        setToggleReadNotifications(false);
    }

    const getTimeAgo = (sentTime: any) => {
        const currentTime: any = new Date();
        const notificationTime: any = new Date(sentTime);
        const diffInDays: any = Math.floor((currentTime - notificationTime) / (1000 * 60 * 60 * 24));

        if (diffInDays === 0) {
            return trans('day-name');
        } else if (diffInDays === 1) {
            return '1 '+trans('day-name-continue');
        } else if (diffInDays <= 7) {
            return `${diffInDays} `+trans('days-name-continue');
        } else {
            const diffInWeeks = Math.floor(diffInDays / 7);
            return `${diffInWeeks} `+trans('week-name');
        }
    };

    // count unread notification
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

    const handleReorder = async (orderid: any) => {
        const orderDetailFetch = await api.get(`orders/${orderid}`, {
            headers: {
                'Content-Language': language,
                'Authorization': `Bearer ${tokenLoggedInCookie}`,
            }
        });
        const orderDetails = orderDetailFetch?.data;        
        const data: any = orderDetails?.data;
        let reorderUrl = `https://${data?.workspace?.slug}.${config.WEBSITE_DOMAIN}/${currentLanguage}/category/products`;

        if(data?.group?.active == 1 || !data?.group) {
            dispatch(changeTypeNotActiveErrorMessageContent(''));
            const validateTypeStatus = await checkOrderTypeActive(workspaceId, orderDetails?.data, tokenLoggedInCookie);
    
            if(validateTypeStatus == true) {
                dispatch(changeTypeNotActiveErrorMessage(false));
                reorderUrl += '?action=reorder&portal=1&origin=home&order_id=' + (data ? data.id : 0);
            } else {
                dispatch(changeTypeNotActiveErrorMessage(true));
            }
        } else {
            dispatch(changeTypeNotActiveErrorMessage(true));
            dispatch(changeTypeNotActiveErrorMessageContent(trans('cart.group_inactive')));
        }
        
        window.open(reorderUrl, '_blank');
        return;
    }

    const handleReadAll = () => {
        api.get(`/notifications/read`, {
            headers: {
                'Authorization': `Bearer ${tokenLoggedInCookie}`,
                'Content-Language': language
            }
        }).then(res => {
            if (res.data.success) {
                setReadNotifications(notifications.map((item: any) => item.id));
                setUnreadCount(0);
            }
        }).catch(error => {
            console.log(error)
        });
    };

    return (
        <>
            <nav className={`${variables.header_navbar} navbar navbar-expand-lg`}>
                <div className="container-fluid">
                    <div className={`${variables.header_logo}`}>
                        <Link href={handleRedirect ? handleRedirect : '/'} 
                            className={`${variables.navbarBrand}`}>
                            <Image
                                alt={'portal-logo'}
                                src={'/img/logo-itready.png'}
                                width={82}
                                height={57}
                                sizes="100vw"
                                style={{ objectFit: "cover" }}
                                className={`${variables.logo}`}
                            />
                        </Link>
                    </div>

                    <div className={`${variables.header_info_group} res-desktop navbar-nav mb-2 mb-lg-0`} >
                        <a className={`${variables.header_info_item}`} href="https://b2b.itsready.be/" target="_blank">
                            <svg width="32" height="31" viewBox="0 0 32 31" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M23.7499 27.1249V28.4166C24.4633 28.4166 25.0416 27.8383 25.0416 27.1249H23.7499ZM8.24993 27.1249H6.95826C6.95826 27.8383 7.53656 28.4166 8.24993 28.4166V27.1249ZM12.1249 7.74993C11.4116 7.74993 10.8333 8.32823 10.8333 9.04159C10.8333 9.75495 11.4116 10.3333 12.1249 10.3333V7.74993ZM13.4166 10.3333C14.13 10.3333 14.7083 9.75495 14.7083 9.04159C14.7083 8.32823 14.13 7.74993 13.4166 7.74993V10.3333ZM12.1249 11.6249C11.4116 11.6249 10.8333 12.2032 10.8333 12.9166C10.8333 13.63 11.4116 14.2083 12.1249 14.2083V11.6249ZM13.4166 14.2083C14.13 14.2083 14.7083 13.63 14.7083 12.9166C14.7083 12.2032 14.13 11.6249 13.4166 11.6249V14.2083ZM18.5833 11.6249C17.8699 11.6249 17.2916 12.2032 17.2916 12.9166C17.2916 13.63 17.8699 14.2083 18.5833 14.2083V11.6249ZM19.8749 14.2083C20.5883 14.2083 21.1666 13.63 21.1666 12.9166C21.1666 12.2032 20.5883 11.6249 19.8749 11.6249V14.2083ZM18.5833 15.4999C17.8699 15.4999 17.2916 16.0782 17.2916 16.7916C17.2916 17.505 17.8699 18.0833 18.5833 18.0833V15.4999ZM19.8749 18.0833C20.5883 18.0833 21.1666 17.505 21.1666 16.7916C21.1666 16.0782 20.5883 15.4999 19.8749 15.4999V18.0833ZM12.1249 15.4999C11.4116 15.4999 10.8333 16.0782 10.8333 16.7916C10.8333 17.505 11.4116 18.0833 12.1249 18.0833V15.4999ZM13.4166 18.0833C14.13 18.0833 14.7083 17.505 14.7083 16.7916C14.7083 16.0782 14.13 15.4999 13.4166 15.4999V18.0833ZM18.5833 7.74993C17.8699 7.74993 17.2916 8.32823 17.2916 9.04159C17.2916 9.75495 17.8699 10.3333 18.5833 10.3333V7.74993ZM19.8749 10.3333C20.5883 10.3333 21.1666 9.75495 21.1666 9.04159C21.1666 8.32823 20.5883 7.74993 19.8749 7.74993V10.3333ZM10.3166 5.16659H21.6833V2.58326H10.3166V5.16659ZM22.4583 5.94159V27.1249H25.0416V5.94159H22.4583ZM23.7499 25.8333H8.24993V28.4166H23.7499V25.8333ZM9.54159 27.1249V5.94159H6.95826V27.1249H9.54159ZM21.6833 5.16659C22.0662 5.16659 22.2759 5.1676 22.4265 5.1799C22.562 5.19097 22.5325 5.20444 22.4583 5.16659L23.6311 2.86483C23.2805 2.68619 22.932 2.62927 22.6368 2.60515C22.3566 2.58225 22.0236 2.58326 21.6833 2.58326V5.16659ZM25.0416 5.94159C25.0416 5.6012 25.0426 5.26823 25.0198 4.98804C24.9956 4.69278 24.9386 4.34437 24.76 3.99378L22.4583 5.16659C22.4204 5.09231 22.4338 5.0629 22.445 5.19841C22.4572 5.349 22.4583 5.55857 22.4583 5.94159H25.0416ZM22.4583 5.16659L24.76 3.99378C24.5124 3.5077 24.1171 3.11249 23.6311 2.86483L22.4583 5.16659ZM10.3166 2.58326C9.9762 2.58326 9.64323 2.58225 9.36304 2.60515C9.06778 2.62927 8.71937 2.68619 8.36878 2.86483L9.54159 5.16659C9.46731 5.20444 9.4379 5.19097 9.57341 5.1799C9.724 5.1676 9.93357 5.16659 10.3166 5.16659V2.58326ZM9.54159 5.94159C9.54159 5.55857 9.5426 5.349 9.5549 5.19841C9.56597 5.0629 9.57944 5.09231 9.54159 5.16659L7.23983 3.99378C7.06119 4.34437 7.00427 4.69278 6.98015 4.98804C6.95725 5.26823 6.95826 5.6012 6.95826 5.94159H9.54159ZM8.36878 2.86483C7.88269 3.11249 7.48749 3.50769 7.23983 3.99378L9.54159 5.16659L8.36878 2.86483ZM12.1249 10.3333H13.4166V7.74993H12.1249V10.3333ZM12.1249 14.2083H13.4166V11.6249H12.1249V14.2083ZM18.5833 14.2083H19.8749V11.6249H18.5833V14.2083ZM18.5833 18.0833H19.8749V15.4999H18.5833V18.0833ZM12.1249 18.0833H13.4166V15.4999H12.1249V18.0833ZM18.5833 10.3333H19.8749V7.74993H18.5833V10.3333ZM17.2916 23.2499V27.1249H19.8749V23.2499H17.2916ZM14.7083 27.1249V23.2499H12.1249V27.1249H14.7083ZM15.9999 21.9583C16.7133 21.9583 17.2916 22.5365 17.2916 23.2499H19.8749C19.8749 21.1098 18.1401 19.3749 15.9999 19.3749V21.9583ZM15.9999 19.3749C13.8598 19.3749 12.1249 21.1098 12.1249 23.2499H14.7083C14.7083 22.5365 15.2865 21.9583 15.9999 21.9583V19.3749Z" fill="#1E1E1E"/>
                            </svg>
                            {trans('portal.trader')}?
                        </a>
                        
                        <SwitchLangDesktop/>

                        {!tokenLoggedInCookie ?
                            (
                                <>
                                    <div id="btnPopupLogin" className={`${variables.login_link}`} onClick={togglePopupLogin} style={{ cursor: 'pointer'}} role="button">
                                        <div className={`${variables.header_info_item}`} aria-current="page">
                                            {trans('log-in')}
                                            <svg width="26" height="27" viewBox="0 0 26 27" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M16.25 3.75H20.5833C21.158 3.75 21.7091 3.97827 22.1154 4.3846C22.5217 4.79093 22.75 5.34203 22.75 5.91667V21.0833C22.75 21.658 22.5217 22.2091 22.1154 22.6154C21.7091 23.0217 21.158 23.25 20.5833 23.25H16.25" stroke="#1E1E1E" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"/>
                                                <path d="M10.8333 18.9166L16.2499 13.4999L10.8333 8.08325" stroke="#1E1E1E" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"/>
                                                <path d="M16.25 13.5H3.25" stroke="#1E1E1E" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"/>
                                            </svg>
                                        </div>
                                    </div>
                                </>
                            )
                            :
                            <div className={`${variables.header_info_group} navbar-nav mb-2 mb-lg-0`}>
                                <a id="btn-notification-header"
                                   className={`${variables.header_info_item} ${variables.header_info_item_email} ${toggleNotifications ? variables.active : ''}`}
                                   style={{position: 'relative', lineHeight: '50px'}}
                                   onMouseEnter={handleToggleNotifications}
                                   onMouseLeave={handleToggleNotificationsHide}
                                   onClick={handleToggleNotifications}>
                                    <svg width="27" height="23" viewBox="0 0 27 23" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd" clip-rule="evenodd" d="M2.80666 2.94059C3.04096 2.69087 3.35842 2.54471 3.67907 2.54471H23.1539C23.4745 2.54471 23.792 2.69087 24.0263 2.94059L13.4165 9.17653L2.80666 2.94059ZM0.224372 2.79217C0.223116 2.79427 0.221865 2.79639 0.22062 2.79851C0.0759448 3.04466 0.0300346 3.32156 0.0709472 3.58324C0.061597 3.69178 0.0568303 3.80139 0.0568303 3.91182V19.2418C0.0568303 21.2483 1.63063 22.9846 3.67907 22.9846H23.1539C25.2023 22.9846 26.7761 21.2483 26.7761 19.2418V3.91182C26.7761 3.80139 26.7714 3.69179 26.762 3.58324C26.8029 3.32157 26.757 3.04466 26.6123 2.79851C26.6111 2.79639 26.6098 2.79427 26.6086 2.79216C26.1518 1.30046 24.8075 0.168945 23.1539 0.168945H3.67907C2.02547 0.168945 0.681167 1.30046 0.224372 2.79217ZM24.4004 5.47646V19.2418C24.4004 20.0458 23.7833 20.6089 23.1539 20.6089H3.67907C3.04971 20.6089 2.43259 20.0458 2.43259 19.2418V5.47646L12.8146 11.5785C13.1861 11.7969 13.6468 11.7969 14.0184 11.5785L24.4004 5.47646Z" fill="#1E1E1E"/>
                                    </svg>

                                    {unreadCount && unreadCount > 0 ? <span style={{ backgroundColor: color, color: "#FFFFFF" }}>{unreadCount ?? 12}</span> : null}

                                    {(!notifications || notifications.length <= 0)
                                        ?
                                        (
                                            toggleNotifications && <div
                                                className={`${variablesMenu.header_info_history_empty}`} style={{ zIndex: '1000' }}>{trans('notification-empty')}</div>
                                        )
                                        :
                                        <CustomScrollbarNotification color={color ? color : '#413E38'}  ref={scrollableDivRefNotifications} onScroll={handleScrollNotifications} style={{ zIndex: '1000' }}>
                                            <div className={`${variablesMenu.header_notification}`}>
                                                <a onClick={handleReadAll} style={{ color: color ?? '#D87833' }}>{trans('notification-read-all')}</a>

                                                {toggleNotifications && <ul className="header_notification_list">
                                                    {notifications && notifications.map((notification, index) => (
                                                        <li className={notification.status || readNotifications.includes(notification.id) ? '' :`${variablesMenu.active}`} key={index} onClick={() => handleReadClick(notification.id)}>
                                                            {
                                                                notification.status ||
                                                                readNotifications.includes(notification.id) ? (<></>) : (
                                                                    <div className={`${variablesMenu.dot}`} style={{ backgroundColor: color ?? '#D87833' }}></div>
                                                                )
                                                            }

                                                            <div className={`${variablesMenu.date}`}>{getTimeAgo(notification.sent_time)}</div>
                                                            <div className={`${variablesMenu.title}`} style={{ textAlign: 'left' }}>{ notification.title }</div>
                                                            <div className={`${variablesMenu.description}`} style={{ textAlign: 'left' }} dangerouslySetInnerHTML={{ __html: notification.description.replace(/\n/g, '<br>') }}></div>
                                                        </li>
                                                    ))}
                                                </ul>}
                                            </div>
                                        </CustomScrollbarNotification>
                                    }
                                </a>
                                <a id="btn-history-order-header"
                                   className={`${variables.header_info_item} ${popupHistoryOrder && variables.active}`}
                                   style={{position: 'relative', lineHeight: '50px'}}
                                   onMouseEnter={toggleHistoryOrder}
                                   onMouseLeave={toggleHistoryOrderHide}
                                   onClick={toggleHistoryOrder}>
                                    <svg width="29" height="29" viewBox="0 0 29 29" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M6.08636 12.2795C6.08636 9.76845 6.95704 7.33504 8.55006 5.39394C10.1431 3.45284 12.3599 2.12415 14.8227 1.63426C17.2856 1.14437 19.8421 1.5236 22.0566 2.70732C24.2712 3.89104 26.0069 5.80601 26.9678 8.12596C27.9288 10.4459 28.0556 13.0273 27.3267 15.4302C26.5977 17.8332 25.0581 19.9091 22.9703 21.3042C20.8824 22.6993 18.3753 23.3272 15.8763 23.0811C13.3773 22.835 11.041 21.73 9.26537 19.9544" stroke="#1E1E1E" strokeWidth="2.41196"/>
                                        <path d="M16.457 7.93799V13.7267L20.3162 15.6563" stroke="#1E1E1E" strokeWidth="2.41196" strokeLinecap="round" strokeLinejoin="round"/>
                                        <path d="M4.98821 15.152C5.46016 15.6289 6.23054 15.6289 6.70249 15.152L8.82526 13.0073C9.57895 12.2458 9.03954 10.9529 7.96812 10.9529H3.72259C2.65117 10.9529 2.11175 12.2458 2.86544 13.0073L4.98821 15.152Z" fill="#1E1E1E"/>
                                    </svg>

                                    {(!getHistory || getHistory.length <= 0)
                                        ?
                                        (
                                            popupHistoryOrder && <div className={`${variablesMenu.header_info_history_empty}`}>{trans('order-text-empty')}</div>
                                        )
                                        : (
                                            <>
                                                {
                                                    popupHistoryOrder &&
                                                    <div className={`${variablesMenu.header_info_history}`} style={{ zIndex: '1000' }}>
                                                        <CustomScrollbar color={color ? color : '#413E38'} ref={scrollableDivRef} onScroll={handleScroll}>
                                                            {getLoadingHistory && <div className={`${variablesMenu.header_info_history_loading}`}></div>}
                                                            <div className={`${variablesMenu.header_info_history_insider}`}>
                                                                {getHistory && getHistory.map((item: any, index: any) => (
                                                                    <div className={`${variablesMenu.header_info_history_group}`} key={index}  onMouseEnter={() => { handleOrderHover(item.id); showPopupHistoryHoverEnter();}} onMouseLeave={() => { handleOrderHover(item.id); showPopupHistoryHoverLeave();}}>
                                                                        <div className={`${variablesMenu.header_info_history_item}`}>
                                                                            <div className={`${variablesMenu.header_info_history_name}`}>
                                                                                <div className={`${variablesMenu.name}`}>
                                                                                    <span>{trans('order-text')}</span>
                                                                                    <span style={{color: color ? color : '#413E38'}}>#{item?.group !== null ? 'G' : ''}{item?.code}{(!!item.group && item?.extra_code) ? `-${item?.extra_code}` : ''}</span>
                                                                                </div>
                                                                                <div className={`${variablesMenu.price}`}>€{item?.total_price}</div>
                                                                            </div>
                                                                            <p style={{ textAlign: 'left' }}>{getWeekName(new Date(item?.date_time))} {formatDate(item?.date_time, 'YYYY-MM-DD hh:mm:ss', 'DD MMMM yyyy')}</p>
                                                                            <div className={`${variablesMenu.header_info_history_restaurant_name}`}>{ item.workspace.name }</div>
                                                                        </div>
                                                                        <div className={`${variablesMenu.header_info_history_item}`}>
                                                                            <div className={`${variablesMenu.header_info_history_item_bottom}`}  onClick={() => handleReorder(item?.id)}>
                                                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="25" viewBox="0 0 24 25" fill="none" > <path d="M1 4.18359V10.1836H7" stroke={color ? color : '#413E38'} strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" /> <path d="M3.51 15.1835C4.15839 17.0239 5.38734 18.6037 7.01166 19.6849C8.63598 20.7661 10.5677 21.2901 12.5157 21.178C14.4637 21.0659 16.3226 20.3237 17.8121 19.0633C19.3017 17.8029 20.3413 16.0925 20.7742 14.1899C21.2072 12.2873 21.0101 10.2955 20.2126 8.51464C19.4152 6.73379 18.0605 5.26034 16.3528 4.31631C14.6451 3.37228 12.6769 3.00881 10.7447 3.28066C8.81245 3.55251 7.02091 4.44496 5.64 5.82354L1 10.1835" stroke={color ? color : '#413E38'} strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" /> </svg>

                                                                                {trans('reorder')}
                                                                            </div>

                                                                            <div className={`${variablesMenu.header_info_history_item_bottom}`}>
                                                                                <svg xmlns="http://www.w3.org/2000/svg" width="26" height="27" viewBox="0 0 26 27" fill="none">
                                                                                    <path d="M9.75 19.6836L16.25 13.1836L9.75 6.68359" stroke="#C4C4C4" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"/>
                                                                                </svg>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                ))}
                                                            </div>
                                                        </CustomScrollbar>

                                                        {popupHistoryHover &&
                                                        <CustomScrollbarHover color={color ? color : '#413E38'} onMouseEnter={() => {showPopupHistoryHoverEnter();}} onMouseLeave={() => {showPopupHistoryHoverLeave();}}>
                                                            <div className={`${variablesMenu.hover_history}`}>
                                                                <div>
                                                                    <div className={`${variablesMenu.list_product}`}>
                                                                        {getHistoryHover && getHistoryHover?.items.map((item: any, index: any) => (
                                                                            <ul key={index}>
                                                                                <li>
                                                                                    <span>{item?.quantity}x {item?.product?.name}</span>
                                                                                    {item?.options?.map((option: any, index: number) => (
                                                                                        <p key={index}>
                                                                                            -{option?.option?.is_ingredient_deletion == true ? (" " + trans('with-out')) : null}
                                                                                            {option?.option_items?.map((optionItem: any, index: number) => (
                                                                                                option?.option_items?.length > 1
                                                                                                    ? optionItem?.option_item.name + (index !== option.option_items.length - 1 ? ", " : "")
                                                                                                    : " " + optionItem?.option_item.name
                                                                                            ))}
                                                                                        </p>
                                                                                    ))}
                                                                                </li>
                                                                                <li>€{item?.subtotal}</li>
                                                                            </ul>
                                                                        ))}
                                                                    </div>

                                                                    {(getHistoryHover?.ship_price && Number(getHistoryHover?.ship_price) > 0) ||
                                                                    (getHistoryHover?.coupon_discount && getHistoryHover?.coupon_discount > 0) ||
                                                                    (getHistoryHover?.redeem_discount && getHistoryHover?.redeem_discount > 0) ||
                                                                    (getHistoryHover?.group_discount && getHistoryHover?.group_discount > 0)
                                                                        ? (
                                                                            <div className={`${variablesMenu.list_product}`}>
                                                                                <ul>
                                                                                    <li>{trans('subtotal')}</li>
                                                                                    <li>€{getHistoryHover?.subtotal}</li>
                                                                                </ul>

                                                                                {
                                                                                    getHistoryHover?.ship_price && Number(getHistoryHover?.ship_price) > 0 && <ul>
                                                                                        <li>{trans('delivery-cost')}</li>
                                                                                        <li>€{getHistoryHover?.ship_price}</li>
                                                                                    </ul>
                                                                                }
                                                                                {
                                                                                    getHistoryHover?.coupon_discount && getHistoryHover?.coupon_discount > 0 && <ul>
                                                                                        <li>{trans('coupon-discount')}</li>
                                                                                        <li>- €{getHistoryHover?.coupon_discount}</li>
                                                                                    </ul>
                                                                                }
                                                                                {
                                                                                    getHistoryHover?.redeem_discount && getHistoryHover?.redeem_discount > 0 && <ul>
                                                                                        <li>{trans('redeem-discount')}</li>
                                                                                        <li>- €{getHistoryHover?.redeem_discount}</li>
                                                                                    </ul>
                                                                                }
                                                                                {
                                                                                    getHistoryHover?.group_discount && getHistoryHover?.group_discount > 0 && <ul>
                                                                                        <li>{trans('group-discount')}</li>
                                                                                        <li>- €{getHistoryHover?.group_discount}</li>
                                                                                    </ul>
                                                                                }

                                                                            </div>
                                                                        ) : null}

                                                                    <div className={`${variablesMenu.total_products}`}>
                                                                        <ul>
                                                                            <li>
                                                                                <span>{formatDate(getHistoryHover?.date_time, 'YYYY-MM-DD hh:mm:ss', 'DD/MM/YYYY HH:mm')}</span>
                                                                                <span>{trans('total')} <span style={{color: color ? color : '#413E38'}}>€{getHistoryHover?.total_price}</span></span>
                                                                            </li>

                                                                            <li>
                                                                                <span>{trans('payment-status')}</span>
                                                                                <strong>{getHistoryHover?.status ? paymentStatus[getHistoryHover?.status] : paymentStatus[0]}</strong>
                                                                            </li>

                                                                            <li>
                                                                                <span>{trans('payment-method')}</span>
                                                                                <strong>{getHistoryHover?.payment_method == 0 ? (paymentMethods[0]) : getHistoryHover?.payment_method ? paymentMethods[getHistoryHover?.payment_method] : ''}</strong>
                                                                            </li>

                                                                            {getHistoryHover?.note && (
                                                                                <li>
                                                                                    <span>{trans('comments')}</span>
                                                                                    <strong>{getHistoryHover?.note}</strong>
                                                                                </li>
                                                                            )}

                                                                            {
                                                                                (getHistoryHover?.type === 0 && getHistoryHover?.group === null)
                                                                                    ? (
                                                                                        <li>
                                                                                            <span>{trans('order-type')}</span>
                                                                                            <strong>{trans('take-out')}</strong>
                                                                                        </li>
                                                                                    )
                                                                                    : (getHistoryHover?.type === 0 && getHistoryHover?.group !== null)
                                                                                        ?
                                                                                        (
                                                                                            <li>
                                                                                                <span>{trans('group')}</span>
                                                                                                <strong>{getHistoryHover?.group?.name}</strong>
                                                                                            </li>
                                                                                        )
                                                                                        : getHistoryHover?.type === 1 && getHistoryHover?.group === null
                                                                                            ?
                                                                                            (
                                                                                                <li>
                                                                                                    <span>{trans('address')}</span>
                                                                                                    <strong>{getHistoryHover?.address}</strong>
                                                                                                </li>
                                                                                            )
                                                                                            : (getHistoryHover?.type === 1 && getHistoryHover?.group !== null)
                                                                                                ?
                                                                                                (
                                                                                                    <>
                                                                                                        <li>
                                                                                                            <span>{trans('address')}</span>
                                                                                                            <strong>{getHistoryHover?.group?.address_display}</strong>
                                                                                                        </li>
                                                                                                        <li>
                                                                                                            <span>{trans('group')}</span>
                                                                                                            <strong>{getHistoryHover?.group?.name}</strong>
                                                                                                        </li>
                                                                                                    </>
                                                                                                )
                                                                                                : getHistoryHover?.type === 2
                                                                                                    ? ""
                                                                                                    : ""
                                                                            }
                                                                        </ul>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </CustomScrollbarHover>
                                                        }
                                                    </div>
                                                }
                                            </>
                                        )
                                    }
                                </a>
                                <a className={`${variables.header_info_item} ${popupHistoryOrder && variables.active}`} onClick={togglePopupNavigation}>
                                    <svg width="40" height="35" viewBox="0 0 40 35" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M10 10.5H27" stroke="#1E1E1E" strokeWidth="2" strokeLinecap="round"/>
                                        <path d="M10 17.5H30" stroke="#1E1E1E" strokeWidth="2" strokeLinecap="round"/>
                                        <path d="M10 24.5H26.5" stroke="#1E1E1E" strokeWidth="2" strokeLinecap="round"/>
                                    </svg>
                                </a>
                            </div>
                        }
                    </div>
                    <div className={`${variables.header_info_group} res-mobile navbar-nav mb-2 mb-lg-0`} >
                        <a className={`${variables.header_info_item} ${popupHistoryOrder && variables.active}`} onClick={togglePopupNavigation}>
                            <svg width="22" height="19" viewBox="0 0 22 19" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M1 1.8667H18" stroke="#1E1E1E" strokeWidth="2" strokeLinecap="round"/>
                                <path d="M1 9.82886H21" stroke="#1E1E1E" strokeWidth="2" strokeLinecap="round"/>
                                <path d="M1 17.7913H17.5" stroke="#1E1E1E" strokeWidth="2" strokeLinecap="round"/>
                            </svg>
                        </a>
                    </div>
                </div>
            </nav>
            {isProfileOpen && <Profile togglePopup={() => togglePopup()} />}
            {isRegisterConfirmOpen && <RegisterConfirm togglePopup={() => togglePopupRegisterConfirm()} />}
            {isLoginOpen && <Login togglePopup={() => togglePopupLogin()} from={null} />}
            {isNavigationOpen && <NavigationPortalPopup togglePopup={() => togglePopupNavigation()} />}
        </>
    )
}
