"use client"

import variables from '/public/assets/css/menu.module.scss'
import { useI18n } from '@/locales/client'
import * as config from "@/config/constants"
import { useRouter, usePathname, useSearchParams } from 'next/navigation'
import _ from 'lodash'
import { useAppSelector, useAppDispatch} from '@/redux/hooks';
import React, { useEffect, useRef, useState , useMemo} from "react";
import { addStepRoot } from '@/redux/slices/cartSlice'
import Profile from "../users/profile";
import Login from "../users/login";
import { api } from "@/utils/axios";
import { handleLogoutToken } from "@/utils/axiosRefreshToken";
import { useSelector } from "react-redux";
import Cookies from "js-cookie";
import Image from "next/image";
import Map from '../layouts/popup/mapHeader';
import RegisterConfirm from "../users/registerConfirm";
import moment from 'moment';
import 'moment/locale/nl';
import { getDay } from 'date-fns';
import styled from 'styled-components';
import {
    rootCartDatetime, addStepCategory, changeTypeFlag, changeType, changeRootInvalidProductIds, rootChangeInCart,
    changeRootCartHistory, changeRootCartTmp, rootCartDeliveryOpen, addGroupOrderSelectedNow, handleTypeBeforeChange,
    changeTypeNotActiveErrorMessage, changeTypeNotActiveErrorMessageContent
} from '@/redux/slices/cartSlice';
import { useGetApiProfileQuery } from '@/redux/services/profileApi';
import { selectApiProfileData } from "@/redux/slices/profileSlice";
import { logout } from "@/redux/slices/authSlice";
import { useGetWorkspaceDataByIdQuery } from '@/redux/services/workspace/workspaceDataApi'
import BackToPortal from '@/app/[locale]/components/layouts/website/backToPortal'
import { removeParam } from "@/utils/common";
import { checkLinkBackToPortal } from '@/utils/common'
import $ from "jquery";
import { setGroupOrderData } from '@/redux/slices/groupOrderSlice'
import useScrollPosition from '@/hooks/useScrollPosition'
import { selectWorkspaceOpenHours } from "@/redux/slices/workspace/workspaceOpenHoursSlice";
import { useGetWorkspaceOpenHoursByIdQuery } from "@/redux/services/workspace/workspaceOpenHoursApi";
import { checkOrderTypeActive } from "@/services/workspace";
import useQueryEditProfileParam from '@/hooks/useQueryParam';
import SwitchLangDesktop from "@/app/[locale]/components/share/switchLangDesktop";

const text = variables.text;
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

export default function MenuPlus() {
    // Get api data
    const workspaceToken = useAppSelector((state) => state.workspaceData.globalWorkspaceToken)
    const dispatch = useAppDispatch();
    const workspaceId = useAppSelector((state) => state.workspaceData.globalWorkspaceId)
    const { data: apiDataToken } = useGetWorkspaceDataByIdQuery({ id: workspaceId })
    const apiData = apiDataToken?.data?.setting_generals;
    const color = useAppSelector((state) => state.workspaceData.globalWorkspaceColor)
    const router = useRouter();
    let cartCoupon: any = useAppSelector((state) => state.cart.coupon);
    // Check logged token
    const tokenLoggedInCookie = Cookies.get('loggedToken');
    const [isProfileOpen, setIsProfileOpen] = useState<any | null>(false);
    const [isRegisterConfirmOpen, setIsRegisterConfirmOpen] = useState<any | null>(false);
    const [isLoginOpen, setIsLoginOpen] = useState<any | null>(false);
    const routerPath = usePathname()
    const trans = useI18n()
    let activeMenu = 'home'
    let cart = useAppSelector((state) => state.cart.rootData)
    const convertStrToNumber = _.map(cart, 'productTotal').map(i => Number(i))
    const cartTotal = _.sum(convertStrToNumber)
    const togglePopup = () => {
        setIsProfileOpen(!isProfileOpen);
    }
    const language = Cookies.get('Next-Locale') ?? 'nl';
    const apiSliceWorkspaceOpenHours = useSelector(selectWorkspaceOpenHours);
    var { data: workspaceOpenHours, isLoading: workspaceLoading, isError: workspaceError } = useGetWorkspaceOpenHoursByIdQuery({ id: workspaceId, lang: language });
    const workspaceOpenHoursFinal = apiSliceWorkspaceOpenHours?.data || workspaceOpenHours?.data;
    const [statusEnableTableOrdering, setStatusEnableTableOrdering] = useState<any | null>(false);
    const [statusEnableSelfOrdering, setStatusEnableSelfOrdering] = useState<any | null>(false);
    const togglePopupRegisterConfirm = () => {
        setIsRegisterConfirmOpen(!isRegisterConfirmOpen);
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
    const groupId = query.get('groupOrder');
    const queryEditProfile = useQueryEditProfileParam();

    useEffect(() => {
        if (query.get('registerConfirm') === 'true') {
            setIsRegisterConfirmOpen(true);
        }

        if (queryEditProfile === true) {
            setIsProfileOpen(true);
        }

        if (query.get('login') === 'true') {
            setIsLoginOpen(true);
        }

        // Reorder from order history
        if (query.get('action') === 'reorder') {
            const orderId = query.get('order_id');
            handleReorder(orderId);
        }
    }, [
        query.get('registerConfirm'), 
        queryEditProfile,
        query.get('login'), 
        query.get('action')
    ]);

    const togglePopupLogin = () => {
        setIsLoginOpen(!isLoginOpen);
    }

    useEffect(() => {
        if (isLoginOpen) {
            Cookies.set('isLoginDesktop', 'true');
        } else {
            Cookies.remove('isLoginDesktop');
        }
    }, [isLoginOpen]);

    if (_.includes(routerPath, '/cart')) {
        activeMenu = 'cart'
    } else if (_.includes(routerPath, '/category/products')) {
        activeMenu = 'products'
    } else if (_.includes(routerPath, '/profile')) {
        activeMenu = 'account'
    } else if (_.includes(routerPath, '/loyalties')) {
        activeMenu = 'loyalty'
    }

    const [workspaceDataFinal, setWorkspaceDataFinal] = useState<any | null>(null);
    useEffect(() => {
        setTimeout(function () {
            workspaceId && api.get(`workspaces/` + workspaceId, {
                headers: {
                    'Authorization': `Bearer ${tokenLoggedInCookie}`,
                    'Content-Language': language,
                }
            }).then(res => {
                const json = res.data;
                setWorkspaceDataFinal(json.data);
            }).catch(error => {
                // console.log(error)
            });
        }, 1000);
    }, [workspaceId]);

    //opening hours
    const statusOpeningHours: Record<string, string> = {
        'status_open': trans('status_open'),
        'status_about_to_close': trans('status_about_to_close'),
        'status_closed': trans('status_closed'),
    };

    const typeValue = useSelector((state: any) => state.cart.type);
    const [typeOpeningHours, setTypeOpeningHours] = useState<any | null>(0);
    const groupOrder = useSelector((state: any) => state.groupOrder.data);
    const [openingHours, setOpeningHours] = useState<any | null>(null);

    useEffect(() => {
        const type_value: any = typeValue ? typeValue - 1 : 0;
        if (type_value >= 2) {
            setTypeOpeningHours(groupOrder ? groupOrder?.type : 0);
        } else {
            setTypeOpeningHours(type_value);
        }
    }, [typeValue]);

    const timezone: any = config.TIMEZONE;
    const getCurrentDateInTimeZone = (timezone: string): string => {
        const currentDate = new Date();
        const options: Intl.DateTimeFormatOptions = {
            timeZone: timezone,
            year: 'numeric',
            month: 'numeric',
            day: 'numeric',
            hour: 'numeric',
            minute: 'numeric',
            second: 'numeric',
        };

        return currentDate.toLocaleString('en-US', options);
    };

    const getDayInTimeZone = (timezone: any) => {
        const currentDate = new Date();
        const options = {
            timeZone: timezone,
        };
        const dateInTimeZone = new Date(currentDate.toLocaleString('en-US', options));

        return dateInTimeZone.getDay();
    };

    useEffect(() => {
        const checkTime = () => {
            const now: any = new Date(getCurrentDateInTimeZone(timezone));
            const dayName: any = getDayInTimeZone(timezone);
            workspaceDataFinal?.setting_open_hours.map((item: any, index: any) => {
                if (item.type === typeOpeningHours) {
                    let isContinue = true;

                    item?.open_time_slots.map((range: any, time_index: any) => {
                        if (range.day_number === dayName) {
                            const startTime = new Date(now);
                            const endTime = new Date(now);
                            const startHoursMinutesSeconds = range.start_time.split(':').map((val: string) => parseInt(val, 10));
                            const endHoursMinutesSeconds = range.end_time.split(':').map((val: string) => parseInt(val, 10));
                            startTime.setHours(startHoursMinutesSeconds[0], startHoursMinutesSeconds[1], startHoursMinutesSeconds[2]);
                            endTime.setHours(endHoursMinutesSeconds[0], endHoursMinutesSeconds[1], endHoursMinutesSeconds[2]);
                            const thirtyMinutesBeforeEnd = new Date(endTime.getTime() - 30 * 60000);

                            if (now >= startTime && now <= endTime && now < thirtyMinutesBeforeEnd) {
                                setOpeningHours('status_open');
                                isContinue = false;
                            } else if (now >= thirtyMinutesBeforeEnd && now <= endTime) {
                                setOpeningHours('status_about_to_close');
                                isContinue = false;
                            } else {
                                if (isContinue) {
                                    setOpeningHours('status_closed');
                                }
                            }
                        }
                    })
                }
            })
            workspaceDataFinal?.extras.map((item: any) => {
                if (item?.type === 10) {
                    if (item.active !== true) {
                        setStatusEnableTableOrdering(false)
                    } else {
                        setStatusEnableTableOrdering(true)
                    }
                } else if (item?.type === 12) {
                    if (item.active !== true) {
                        setStatusEnableSelfOrdering(false)
                    } else {
                        setStatusEnableSelfOrdering(true)
                    }
                }
            });
        };

        checkTime();
    }, [workspaceDataFinal]);

    //slice data informatin user
    useGetApiProfileQuery(tokenLoggedInCookie || '');
    var apiSliceProfile = useSelector(selectApiProfileData);

    useEffect(() => {
        if (apiSliceProfile) {
            if (apiSliceProfile.data?.first_name && !apiSliceProfile.data?.last_name) {
                setInfoUser({
                    avatar: apiSliceProfile.data?.first_name?.substring(0, 1),
                    fullname: apiSliceProfile.data.first_name,
                    photo: apiSliceProfile.data.photo,
                })
            } else if (apiSliceProfile.data?.last_name && !apiSliceProfile.data?.first_name) {
                setInfoUser({
                    avatar: apiSliceProfile.data?.last_name.substring(0, 1),
                    fullname: apiSliceProfile.data.last_name,
                    photo: apiSliceProfile.data.photo,
                })
            } else {
                setInfoUser({
                    avatar: apiSliceProfile.data?.first_name?.substring(0, 1) + apiSliceProfile.data?.last_name?.substring(0, 1),
                    fullname: apiSliceProfile.data.first_name + ' ' + apiSliceProfile.data.last_name,
                    photo: apiSliceProfile.data.photo,
                })
            }
        }
    }, [apiSliceProfile]);

    //get infomation user
    const [infoUser, setInfoUser] = useState({
        avatar: '',
        fullname: '',
        photo: '',
    });

    useEffect(() => {
        tokenLoggedInCookie && api.get(`profile`, {
            headers: {
                'Authorization': `Bearer ${tokenLoggedInCookie}`,
                'Content-Language': language,
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

    }, []);

    //logout
    const handleLogoutClick = async () => {
        dispatch(logout());
        dispatch(addStepRoot(1))
        // Remove cookie 'loggedToken'
        handleLogoutToken();
        router.push('/')
        Cookies.remove('atStep2');
        Cookies.remove('fromDesk');
        Cookies.remove('groupOrderDesktop');
    };

    //popup map
    const [popupMapOpen, setPopupMapOpen] = useState<any | null>(false);
    const showPopupMap = () => {
        setPopupMapOpen(!popupMapOpen);
    }

    //hover loyalty card
    const [isHovered, setIsHovered] = useState<any | null>(false);
    const handleMouseEnter = () => {
        setIsHovered(true);
    };
    const handleMouseLeave = () => {
        setIsHovered(false);
    };

    const searchParams = useSearchParams();
    const handleOpenCat = () => {
        window.location.href = checkLinkBackToPortal('/category/products');
    }

    const handleLoyal = () => {
        if (tokenLoggedInCookie) {
            router.push(checkLinkBackToPortal('/loyalties'));
        } else {
            router.push(`/user/login?loyalties=true`);
        }
    }

    const handleAccount = () => {
        router.replace(`/${language}/profile/show`);
    }

    const handleCate = () => {
        if (cartCoupon) {
            router.push('/category/cart');
        } else {
            window.location.href = '/category/cart';
        }
    }

    const handleHome = () => {
        window.location.href = checkLinkBackToPortal('/');
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
                        'Content-Language': language,
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
        setPopupHistoryOrder(!popupHistoryOrder);

        setTimeout(function () {
            setLoadingHistory(false);
        }, 1000);

        if (popupHistoryOrder === false) {
            const response = api.get(`orders/history?limit=15&page=1&workspace_id=${workspaceId}`, {
                headers: {
                    'Authorization': `Bearer ${tokenLoggedInCookie}`,
                    'App-Token': workspaceToken,
                    'Content-Language': language,
                }
            }).then(res => {
                const json = res.data;
                if (json.data) {
                    const filteredData = json?.data?.data.filter((item: any) => (item.payment_method === 0 || item.payment_method_display === "Mollie") && item.status === 2);
                    // Thêm vào các bản ghi không có mollie
                    json?.data?.data.forEach((item: any) => {
                        if (item.payment_method !== 0 && item.payment_method_display !== "Mollie") {
                            filteredData.push(item);
                        }
                    });
                    filteredData.sort((prev: any, next: any) => next.id - prev.id);
                    setHistory(filteredData);
                    return json.data;
                } else {
                    return [];
                }
            }).catch(error => {
                // console.log(error)
            });
        } else {
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
            const response = api.get(`orders/history?limit=15&page=` + getCountHistory + `&workspace_id=${workspaceId}`, {
                headers: {
                    'Authorization': `Bearer ${tokenLoggedInCookie}`,
                    'App-Token': workspaceToken,
                    'Content-Language': language,
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

    // Set color for  background  if scroll
    const [scrolled, setScrolled] = useState(false);
    const scrolledY = useScrollPosition()

    useEffect(() => {
        if (scrolledY > 0) {
            setScrolled(true);
        } else {
            setScrolled(false);
        }
    }, [scrolledY]);

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
                        'Content-Language': language,
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
            api.get(`/notifications?limit=15&page=` + getCountNotifications, {
                headers: {
                    'Authorization': `Bearer ${tokenLoggedInCookie}`,
                    'App-Token': workspaceToken,
                    'Content-Language': language,
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
        if (processingToggle) return;
        setProcessingToggle(true);
        setToggleReadNotifications(true);

        api.get(`/notifications?limit=15&page=1`, {
            headers: {
                'Authorization': `Bearer ${tokenLoggedInCookie}`,
                'App-Token': workspaceToken,
                'Content-Language': language,
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
            return '1 ' + trans('day-name-continue');
        } else if (diffInDays <= 7) {
            return `${diffInDays} ` + trans('days-name-continue');
        } else {
            const diffInWeeks = Math.floor(diffInDays / 7);
            return `${diffInWeeks} ` + trans('week-name');
        }
    };

    // count unread notification
    const [unreadCount, setUnreadCount] = useState(0);
    useEffect(() => {
        const response = tokenLoggedInCookie && api.get(`notifications`, {
            headers: {
                'Authorization': `Bearer ${tokenLoggedInCookie}`,
                'App-Token': workspaceToken,
                'Content-Language': language,
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

    /**
     * Clear Reorder URL and push state to history
     */
    const clearReorderUrl = () => {
        let newUrl = window.location.href;
        newUrl = removeParam('action', newUrl);
        newUrl = removeParam('order_id', newUrl);

        setTimeout(() => {
            history.pushState({}, '', newUrl);
        }, 500);
    }

    const handleReorder = async (orderId: any) => {
        // Reordering is not working from the page Order confirmation
        // and some other pages because those pages do not have the Cart section.
        // We need to redirect the users back to the Products page in this case to have this work.
        if ($('#cart-container').length === 0) {
            router.push('/category/products');
        }

        try {
            const orderDetailFetch = await api.get(`orders/${orderId}`, {
                headers: {
                    'Authorization': `Bearer ${tokenLoggedInCookie}`,
                    'Content-Language': language,
                }
            });

            const orderDetails = orderDetailFetch?.data;
            
            if(orderDetails?.data?.group?.active == 1 || !orderDetails?.data?.group) {
                dispatch(changeTypeNotActiveErrorMessageContent(''));
                const validateTypeStatus = await checkOrderTypeActive(workspaceId, orderDetails?.data, tokenLoggedInCookie);
            
                if(validateTypeStatus == true) {
                    dispatch(changeTypeNotActiveErrorMessage(false));
    
                    if (orderDetails?.data?.type === 1) {
                        if (orderDetails?.data?.group_id) {
                            dispatch(changeType(3))
                        } else {
                            dispatch(changeType(2))
                        }
                    } else {
                        dispatch(changeType(1))
                        dispatch(setGroupOrderData(null));
                    }
    
                    const data: any = orderDetails?.data?.items.map(async (item: any) => {
                        if (!item) {
                            return null;
                        }
                        const optionItemsStore = item?.options.map((option: any) => {
                            return {
                                'optionId': option?.option?.id,
                                'optionItems': option?.option_items.map((optionItem: any) => {
                                    return optionItem?.option_item
                                })
                            }
                        });
    
                        // Check if the product has options
                        let productOptions: any = [];
                        try {
                            const res: any = await api.get(`products/${item?.product?.id}/options?limit=100&page=1`, {
                                headers: {
                                    'Authorization': `Bearer ${tokenLoggedInCookie}`,
                                    'Content-Language': language,
                                }
                            });
                            if (res && res?.code != 'ERR_NETWORK') {
                                productOptions = res;
                            }
                        } catch (error) {
                            console.log(error);
                        }
                        let optionItemsStoreSort: any = [];
                        productOptions?.data?.data.length > 0 && productOptions?.data?.data.map((option: any) => {
                            optionItemsStore.map((optionItemStore: any) => {
                                if (optionItemStore?.optionId == option?.id) {
                                    optionItemsStoreSort.push(optionItemStore);
                                }
                            })
                        })
                        // Check if the product has options
                        let availableDelivery: any = false;
    
                        try {
                            const result = await api.get(`products/${item?.product?.id}`, {
                                headers: {
                                    'Authorization': `Bearer ${tokenLoggedInCookie}`,
                                    'Content-Language': language,
                                }
                            });
                            availableDelivery = result?.data?.data?.category?.available_delivery;
                        } catch (error) {
                            console.log(error);
                        }
                        // add more field to product
                        const updatedProduct = {
                            ...item?.product,
                            category: {
                                ...item?.product?.category,
                                available_delivery: availableDelivery // add available_delivery 
                            }
                        };
                        return {
                            'basePrice': Number(item?.price),
                            'productId': item?.product?.id,
                            'product': {
                                'data': updatedProduct
                            },
                            'productTotal': item?.quantity,
                            'productOptions': productOptions.length > 0 ? productOptions?.data?.data : [],
                            'optionItemsStore': optionItemsStore,
                        };
                    })
    
                    Promise.all(data).then((values: any) => {
                        // save cart data, if changing type doesn't work , we need rollback data
                        if (orderDetails?.data?.group_id) {
                            onClickChangeType(3, orderDetails);
                            dispatch(rootChangeInCart(values))
                        } else if (orderDetails?.data?.type && orderDetails?.data?.type === 1) {
                            // cartDeliveryOpen = (query.get('action') === 'reorder') ? true : cartDeliveryOpen;
                            if (window.location.href.includes('/cart')) {
                                dispatch(rootCartDeliveryOpen(false));
                            } else {
                                dispatch(rootCartDeliveryOpen(true));
                            }
    
                            dispatch(changeRootCartHistory(cart));
                            dispatch(handleTypeBeforeChange(typeValue));
                            dispatch(rootChangeInCart(values))
                            onClickChangeType(2, orderDetails);
                            dispatch(changeRootCartTmp(values));
                            //handleClickDelivery();
                        } else if (orderDetails?.data?.type === 0) {
                            onClickChangeType(1, orderDetails);
                            dispatch(rootChangeInCart(values))
                        }
                    });
    
                    if (query.get('action') === 'reorder') {
                        // Remove a parameter to the URL to prevent reload page and book order again
                        clearReorderUrl();
                    }
                } else {
                    dispatch(changeTypeNotActiveErrorMessage(true));
                }     
            } else {
                dispatch(changeTypeNotActiveErrorMessage(true));
                dispatch(changeTypeNotActiveErrorMessageContent(trans('cart.group_inactive')));
            }       
        } catch (error) {
            console.log(error);
        }
    }

    const onClickChangeType = async (type: number, orderDetails: any) => {
        dispatch(changeType(type))
        dispatch(changeTypeFlag(false))
        dispatch(changeRootInvalidProductIds(null));
        if (type === 3) {
            dispatch(addGroupOrderSelectedNow(orderDetails?.data?.group));
            if (orderDetails?.data?.group && workspaceId) {
                const groupOrder = orderDetails?.data?.group?.id;
                try {
                    const groupDetail = await api.get(`/groups/${groupOrder}`, {
                        headers: {
                            'Authorization': `Bearer ${tokenLoggedInCookie}`,
                            'Content-Language': language,
                        }
                    });
                    if (groupDetail?.status === 200 && groupDetail?.data?.success === true) {
                        const groupDetailData = groupDetail?.data?.data;
                        dispatch(setGroupOrderData(groupDetailData));
                    }
                } catch (error) {
                    console.log(error);
                }
            }
            // router.push(checkLinkBackToPortal(`${url}`));
        } else {
            if (type === 1) {
                dispatch(addGroupOrderSelectedNow(null))
            }
        }
    }

    const [getHoliday, setHoliday] = useState<any | null>(null);
    useEffect(() => {
        workspaceId && api.get(`workspaces/` + workspaceId + `/settings/holiday_exceptions`, {
            headers: {
                'Authorization': `Bearer ${tokenLoggedInCookie}`,
                'Content-Language': language,
            }
        }).then(res => {

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
        }).catch(error => {
            //console.log(error)
        });
    }, [workspaceId]);

    const handleReadAll = () => {
        api.get(`/notifications/read`, {
            headers: {
                'Authorization': `Bearer ${tokenLoggedInCookie}`,
                'Content-Language': language,
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

    const filteredItems = useMemo(() => {
        return workspaceOpenHoursFinal?.filter((item: any) => {
            if (!item.active) return false;
            if (item.type === 2 && !statusEnableTableOrdering) return false;
            if (item.type === 3 && !statusEnableSelfOrdering) return false;
            return true;
        }) || [];
    }, [workspaceOpenHoursFinal, statusEnableTableOrdering, statusEnableSelfOrdering]);

    return (
        <>
            <div className="res-mobile" style={{ position: 'fixed', bottom: 0, left: 0, width: '100%', zIndex: 100 }}>
                <div className={`${variables.container}`}>
                    <div className={variables.home}>
                        <div style={{ textDecoration: 'none', textAlign: 'center', width: '58px' }} onClick={handleHome}>
                            <div style={{ marginBottom: '-1px', paddingRight: '1px' }}>
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                    <path d="M4 7.65L12.5 1L21 7.65V18.1C21 18.6039 20.801 19.0872 20.4468 19.4435C20.0925 19.7998 19.6121 20 19.1111 20H5.88889C5.38792 20 4.90748 19.7998 4.55324 19.4435C4.19901 19.0872 4 18.6039 4 18.1V7.65Z" stroke={apiData && activeMenu == 'home' ? color : '#413E38'} strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" />
                                    <path d="M10 20V10H15V20" stroke={apiData && activeMenu == 'home' ? color : '#413E38'} strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"
                                    />
                                </svg>
                            </div>
                            <p className={`${text} mt-1 text-uppercase`} style={{ color: (apiData && activeMenu == 'home') ? color : '#413E38' }}>{trans('home')}</p>
                        </div>
                    </div>
                    <div className={variables.menu}>
                        <div style={{ textDecoration: 'none', width: '58px' }} onClick={handleOpenCat}>
                            <div className='d-flex justify-content-center' style={{ paddingTop: '4px' }}>
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="18" viewBox="0 0 20 18" fill="none" style={{ marginBottom: '-1px' }}>
                                    <line x1="19" y1="17" x2="1" y2="17" stroke={apiData && activeMenu == 'products' ? color : '#413E38'} strokeWidth="2" strokeLinecap="round" />
                                    <line x1="19" y1="9" x2="1" y2="9" stroke={apiData && activeMenu == 'products' ? color : '#413E38'} strokeWidth="2" strokeLinecap="round" />
                                    <line x1="19" y1="1" x2="1" y2="1" stroke={apiData && activeMenu == 'products' ? color : '#413E38'} strokeWidth="2" strokeLinecap="round" />
                                </svg>
                            </div>
                            <p className={`${text} mt-1 text-uppercase`} style={{ color: (apiData && activeMenu == 'products') ? color : '#413E38', paddingTop: '4px' }}>{trans('menu')}</p>
                        </div>
                    </div>
                    <div className={`${variables.cart}`} style={{ display: 'flex', alignItems: 'center', justifyContent: 'space-between' }} onClick={handleCate}>
                        {cartTotal > 0 && (
                            <span className="badge" style={{ background: color ?? '#413E38' }}>
                                {cartTotal}
                            </span>
                        )}
                        <div style={{ textDecoration: 'none', textAlign: 'center', width: '58px' }}>
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                <path d="M6 2L3 6V20C3 20.5304 3.21071 21.0391 3.58579 21.4142C3.96086 21.7893 4.46957 22 5 22H19C19.5304 22 20.0391 21.7893 20.4142 21.4142C20.7893 21.0391 21 20.5304 21 20V6L18 2H6Z" stroke={apiData && activeMenu == 'cart' ? color : '#413E38'} strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" />
                                <path d="M3 6H21" stroke={apiData && activeMenu == 'cart' ? color : '#413E38'} strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" />
                                <path d="M16 10C16 11.0609 15.5786 12.0783 14.8284 12.8284C14.0783 13.5786 13.0609 14 12 14C10.9391 14 9.92172 13.5786 9.17157 12.8284C8.42143 12.0783 8 11.0609 8 10" stroke={apiData && activeMenu == 'cart' ? color : '#413E38'} strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" />
                            </svg>
                            <p className={`${text} mt-1 text-uppercase`} style={{ color: (apiData && activeMenu == 'cart') ? color : '#413E38' }}>{trans('shopping-cart')}</p>
                        </div>
                    </div>

                    <div className={variables.loyalty}>
                        <div style={{ textDecoration: 'none', textAlign: 'center', width: '58px' }} onClick={handleLoyal}>
                            <svg width="16" height="24" viewBox="0 0 16 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M8 15C11.866 15 15 11.866 15 8C15 4.13401 11.866 1 8 1C4.13401 1 1 4.13401 1 8C1 11.866 4.13401 15 8 15Z" stroke={(apiData && activeMenu == 'loyalty') ? color : '#413E38'} strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" />
                                <path d="M4.21 13.8899L3 22.9999L8 19.9999L13 22.9999L11.79 13.8799" stroke={(apiData && activeMenu == 'loyalty') ? color : '#413E38'} strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" />
                            </svg>
                            <p className={`${text} mt-1 text-uppercase`} style={{ color: (apiData && activeMenu == 'loyalty') ? color : '#413E38' }}>{trans('loyalty-cart')}</p>
                        </div>
                    </div>

                    <div className={variables.account}>
                        <div style={{ textDecoration: 'none', textAlign: 'center', width: '58px' }} onClick={handleAccount}>
                            <svg width="18" height="20" viewBox="0 0 18 20" fill="none" xmlns="http://www.w3.org/2000/svg" style={{ marginTop: '1px' }}>
                                <path d="M17 19V17C17 15.9391 16.5786 14.9217 15.8284 14.1716C15.0783 13.4214 14.0609 13 13 13H5C3.93913 13 2.92172 13.4214 2.17157 14.1716C1.42143 14.9217 1 15.9391 1 17V19" stroke={apiData && activeMenu == 'account' ? color : '#413E38'} strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" />
                                <path d="M9 9C11.2091 9 13 7.20914 13 5C13 2.79086 11.2091 1 9 1C6.79086 1 5 2.79086 5 5C5 7.20914 6.79086 9 9 9Z" stroke={apiData && activeMenu == 'account' ? color : '#413E38'} strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" />
                            </svg>

                            <p className={`${text} mt-1 text-uppercase`} style={{ color: (apiData && activeMenu == 'account') ? color : '#413E38', paddingTop: '1.5px' }}>{trans('account')}</p>
                        </div>
                    </div>
                </div>
            </div>
            <div className={`${variables.header} header-desktop`} style={{
                position: 'absolute',
                top: 0,
                left: 0,
                width: '100%',
                zIndex: 1003,
                display: window.innerWidth > 1279 ? 'block' : 'none'
            }}>
                <nav className={`${variables.header_navbar} navbar navbar-expand-lg navbar-light`} style={{ backgroundColor: "#FFF" }}>
                    <div className="container-fluid">
                        <div className={`${variables.header_logo}`}>
                            <BackToPortal />
                            <div className={`${variables.navbarBrand}`} onClick={handleHome}>
                                {workspaceDataFinal && workspaceDataFinal?.photo && (
                                    <Image
                                        alt={workspaceDataFinal?.name || 'intro'}
                                        src={workspaceDataFinal?.photo}
                                        width={80}
                                        height={80}
                                        sizes="100vw"
                                        style={{ borderRadius: "50%", objectFit: "cover" }}
                                        className={variables.logo}
                                    />
                                )}

                                <div className={`${variables.info}`}>
                                    <strong>{workspaceDataFinal ? workspaceDataFinal?.setting_generals.title : ''}</strong>
                                    <span>{workspaceDataFinal ? workspaceDataFinal?.setting_generals.subtitle : ''}</span>
                                </div>
                            </div>
                            { (workspaceDataFinal) && (
                                <>
                                    <div className={`${variables.header_info}`}>
                                        <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg" onClick={showPopupMap} onMouseEnter={showPopupMap}>
                                            <circle cx="10" cy="10" r="9.5" stroke={color ?? '#D87833'} />
                                            <path d="M10.8751 7.244C10.5671 7.244 10.3058 7.13667 10.0911 6.922C9.87644 6.70733 9.76911 6.446 9.76911 6.138C9.76911 5.83 9.87644 5.56867 10.0911 5.354C10.3058 5.13 10.5671 5.018 10.8751 5.018C11.1831 5.018 11.4444 5.13 11.6591 5.354C11.8831 5.56867 11.9951 5.83 11.9951 6.138C11.9951 6.446 11.8831 6.70733 11.6591 6.922C11.4444 7.13667 11.1831 7.244 10.8751 7.244ZM9.92311 15.084C9.47511 15.084 9.11111 14.944 8.83111 14.664C8.56044 14.384 8.42511 13.964 8.42511 13.404C8.42511 13.1707 8.46244 12.8673 8.53711 12.494L9.48911 8H11.5051L10.4971 12.76C10.4598 12.9 10.4411 13.0493 10.4411 13.208C10.4411 13.3947 10.4831 13.53 10.5671 13.614C10.6604 13.6887 10.8098 13.726 11.0151 13.726C11.1831 13.726 11.3324 13.698 11.4631 13.642C11.4258 14.1087 11.2578 14.468 10.9591 14.72C10.6698 14.9627 10.3244 15.084 9.92311 15.084Z" fill={color ?? '#D87833'} />
                                        </svg>
                                            <div style={{
                                                display: popupMapOpen ? 'block' : 'none',
                                            }}>
                                                <div id="back-drop-map" onClick={() => { setPopupMapOpen(false) }}></div>
                                                <Map data={apiDataToken ? apiDataToken.data : null} workspaceId={workspaceId ? workspaceId : ''} apiData={apiData ? apiData : ''} color={workspaceDataFinal ? workspaceDataFinal?.setting_generals?.primary_color : 'black'} />
                                            </div>
                                    </div>
                                    <div className={`${variables.header_status} ${openingHours === 'status_open' ? variables.status_open : openingHours === 'status_about_to_close' ? 'status_about_to_close' : 'status_closed'}`}>
                                        {openingHours && filteredItems.length > 0 ? (
                                            <div className="d-flex" style={{ alignItems: "center" }}>
                                                <span
                                                    className={`${variables.dot}`}
                                                    style={
                                                        openingHours === 'status_about_to_close' && (!getHoliday || !getHoliday?.status)
                                                            ? { background: '#D87833' }
                                                            : (openingHours === 'status_closed') || (getHoliday && getHoliday?.status)
                                                                ? { background: 'red' }
                                                                : { background: '#6CCE4A' }
                                                    }
                                                ></span>
                                                {((getHoliday && getHoliday?.status) || openingHours == 'status_closed') ? trans('status_closed') : statusOpeningHours[openingHours]}
                                                <div>&nbsp;</div>
                                                <span className={`${variables.sub_closed}`}>
                                                    {((getHoliday && getHoliday?.status) || openingHours == 'status_closed') && trans('order-ahead')}
                                                </span>
                                            </div>
                                        ) : (
                                            <div className="d-flex" style={{ alignItems: "center" }}>
                                                <span
                                                    className={`${variables.dot}`}
                                                    style={
                                                        { background: 'red' }
                                                    }
                                                ></span>
                                                {trans('status_closed')}
                                                <div>&nbsp;</div>
                                                <span className={`${variables.sub_closed}`}>
                                                    {((getHoliday && getHoliday?.status) || openingHours == 'status_closed') && trans('order-ahead')}
                                                </span>
                                            </div>
                                        )}

                                        {getHoliday && getHoliday?.status && (
                                            <div className={`${variables.header_holiday}`}>
                                                {getHoliday && getHoliday?.data[0] && (
                                                    getHoliday?.data[0]?.start_time === getHoliday?.data[0]?.end_time
                                                        ? formatDate(getHoliday && getHoliday?.data[0]?.start_time, 'YYYY-MM-DD', 'DD/MM')
                                                        : formatDate(getHoliday && getHoliday?.data[0]?.start_time, 'YYYY-MM-DD', 'DD/MM') + ' t.e.m. ' + formatDate(getHoliday && getHoliday?.data[0]?.end_time, 'YYYY-MM-DD', 'DD/MM')
                                                )}
                                            </div>
                                        )}
                                    </div>
                                </>
                            )}
                        </div>

                        <div className={`${variables.header_info_group} navbar-nav mb-2 mb-lg-0`}>
                            {!tokenLoggedInCookie ?
                                (
                                    <>
                                        <div className="mr-15px">
                                            <SwitchLangDesktop/>
                                        </div>
                                        <div id="btnPopupLogin" className={`${variables.login_link}`} onClick={togglePopupLogin}>
                                            <div className={`${variables.header_info_item}`} aria-current="page">
                                                {trans('log-in')}
                                                <svg width="26" height="26" viewBox="0 0 26 26" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M16.25 3.25H20.5833C21.158 3.25 21.7091 3.47827 22.1154 3.8846C22.5217 4.29093 22.75 4.84203 22.75 5.41667V20.5833C22.75 21.158 22.5217 21.7091 22.1154 22.1154C21.7091 22.5217 21.158 22.75 20.5833 22.75H16.25" stroke="#404040" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" />
                                                    <path d="M10.8333 18.4168L16.2499 13.0002L10.8333 7.5835" stroke="#404040" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" />
                                                    <path d="M16.25 13H3.25" stroke="#404040" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" />
                                                </svg>
                                            </div>
                                        </div>
                                    </>
                                ) : (
                                    <div className={`${variables.header_info_group} navbar-nav mb-2 mb-lg-0`}>
                                        <SwitchLangDesktop/>

                                        <div onMouseEnter={handleMouseEnter} onMouseLeave={handleMouseLeave}>
                                            {isHovered
                                                ?
                                                <a className={`${variables.header_info_item} ${variables.header_info_item_label}`}
                                                    aria-current="page" href={checkLinkBackToPortal(groupId ? "/loyalties?groupOrder=" + groupId : "/loyalties")}>
                                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <g id="award">
                                                            <path id="Vector" d="M12 15C15.866 15 19 11.866 19 8C19 4.13401 15.866 1 12 1C8.13401 1 5 4.13401 5 8C5 11.866 8.13401 15 12 15Z" stroke="#404040" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" />
                                                            <path id="Vector_2" d="M8.21 13.8899L7 22.9999L12 19.9999L17 22.9999L15.79 13.8799" stroke="#404040" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" />
                                                        </g>
                                                    </svg>
                                                    <span>{trans('customer_card')}</span>
                                                </a>
                                                :
                                                <a className={`${variables.header_info_item}`} aria-current="page"
                                                    href={checkLinkBackToPortal(groupId ? "/loyalties?groupOrder=" + groupId : "/loyalties")}>
                                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <g id="award">
                                                            <path id="Vector" d="M12 15C15.866 15 19 11.866 19 8C19 4.13401 15.866 1 12 1C8.13401 1 5 4.13401 5 8C5 11.866 8.13401 15 12 15Z" stroke="#404040" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" />
                                                            <path id="Vector_2" d="M8.21 13.8899L7 22.9999L12 19.9999L17 22.9999L15.79 13.8799" stroke="#404040" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" />
                                                        </g>
                                                    </svg>
                                                </a>
                                            }
                                        </div>
                                        <a className={`${variables.header_info_item} ${popupHistoryOrder && variables.active}`} href="#" onMouseEnter={toggleHistoryOrder} onMouseLeave={toggleHistoryOrderHide}>
                                            <img src="/img/order-history.svg" alt="Order history" />

                                            {(!getHistory || getHistory.length <= 0)
                                                ?
                                                (
                                                    popupHistoryOrder && <div className={`${variables.header_info_history_empty}`}>{trans('order-text-empty')}</div>
                                                )
                                                :
                                                <>
                                                    {
                                                        popupHistoryOrder && <div className={`${variables.header_info_history}`}>
                                                            <CustomScrollbar color={color ? color : '#413E38'} ref={scrollableDivRef} onScroll={handleScroll}>
                                                                {getLoadingHistory && <div className={`${variables.header_info_history_loading}`}></div>}
                                                                <div className={`${variables.header_info_history_insider}`}>
                                                                    {getHistory && getHistory.map((item: any, index: any) => (
                                                                        <div className={`${variables.header_info_history_group}`} key={index} onMouseEnter={() => { handleOrderHover(item.id); showPopupHistoryHoverEnter(); }} onMouseLeave={() => { handleOrderHover(item.id); showPopupHistoryHoverLeave(); }}>
                                                                            <div className={`${variables.header_info_history_item}`}>
                                                                                <div className={`${variables.header_info_history_name}`}>
                                                                                    <div className={`${variables.name}`}>
                                                                                        <span>{trans('order-text')}</span>
                                                                                        <span style={{ color: color ? color : '#413E38' }}>#{item?.group !== null ? 'G' : ''}{item?.code}{(!!item.group && item?.extra_code) ? `-${item?.extra_code}` : ''}</span>
                                                                                    </div>
                                                                                    <div className={`${variables.price}`}>€{item?.total_price}</div>
                                                                                </div>
                                                                                <p>{getWeekName(new Date(item?.date_time))} {formatDate(item?.date_time, 'YYYY-MM-DD hh:mm:ss', 'DD MMMM yyyy')}</p>
                                                                            </div>
                                                                            <div className={`${variables.header_info_history_item}`}>
                                                                                <div className={`${variables.header_info_history_item_bottom}`} onClick={() => handleReorder(item?.id)}>
                                                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="25" viewBox="0 0 24 25" fill="none" > <path d="M1 4.18359V10.1836H7" stroke={color ? color : '#413E38'} strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" /> <path d="M3.51 15.1835C4.15839 17.0239 5.38734 18.6037 7.01166 19.6849C8.63598 20.7661 10.5677 21.2901 12.5157 21.178C14.4637 21.0659 16.3226 20.3237 17.8121 19.0633C19.3017 17.8029 20.3413 16.0925 20.7742 14.1899C21.2072 12.2873 21.0101 10.2955 20.2126 8.51464C19.4152 6.73379 18.0605 5.26034 16.3528 4.31631C14.6451 3.37228 12.6769 3.00881 10.7447 3.28066C8.81245 3.55251 7.02091 4.44496 5.64 5.82354L1 10.1835" stroke={color ? color : '#413E38'} strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" /> </svg>
                                                                                    {trans('reorder')}
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    ))}
                                                                </div>
                                                            </CustomScrollbar>

                                                            {popupHistoryHover && <CustomScrollbarHover color={color ? color : '#413E38'} onMouseEnter={() => { showPopupHistoryHoverEnter(); }} onMouseLeave={() => { showPopupHistoryHoverLeave(); }}><div className={`${variables.hover_history}`}>
                                                                <div>
                                                                    <div className={`${variables.list_product}`}>
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
                                                                        (getHistoryHover?.service_cost && Number(getHistoryHover?.service_cost) > 0) ||
                                                                        (getHistoryHover?.coupon_discount && getHistoryHover?.coupon_discount > 0) ||
                                                                        (getHistoryHover?.redeem_discount && getHistoryHover?.redeem_discount > 0) ||
                                                                        (getHistoryHover?.group_discount && getHistoryHover?.group_discount > 0)
                                                                        ? (
                                                                            <div className={`${variables.list_product}`}>
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
                                                                                    getHistoryHover?.service_cost && Number(getHistoryHover?.service_cost) > 0 && <ul>
                                                                                        <li>{trans('cart.service_cost')}</li>
                                                                                        <li>€{getHistoryHover?.service_cost}</li>
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

                                                                    <div className={`${variables.total_products}`}>
                                                                        <ul>
                                                                            <li>
                                                                                <span>{formatDate(getHistoryHover?.date_time, 'YYYY-MM-DD hh:mm:ss', 'DD/MM/YYYY HH:mm')}</span>
                                                                                <span>{trans('total')} <span style={{ color: color ? color : '#413E38' }}>€{getHistoryHover?.total_price}</span></span>
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
                                                            </div></CustomScrollbarHover>}
                                                        </div>
                                                    }
                                                </>
                                            }
                                        </a>
                                        <a className={`${variables.header_info_item} ${variables.header_info_item_email} ${toggleNotifications ? variables.active : ''}`} href="#" onMouseEnter={handleToggleNotifications} onMouseLeave={handleToggleNotificationsHide}>
                                            <svg xmlns="http://www.w3.org/2000/svg" width="23" height="19" viewBox="0 0 23 19" fill="none">
                                                <path fillRule="evenodd" clipRule="evenodd" d="M2.36481 2.25731C2.54923 2.09737 2.78905 2 3.0495 2H19.4441C19.7046 2 19.9444 2.09737 20.1288 2.25731L11.2468 7.23067L2.36481 2.25731ZM0.145021 2.12091C0.139085 2.13071 0.133293 2.14066 0.12765 2.15073C0.014783 2.35231 -0.0207931 2.57618 0.0112785 2.78819C0.00392955 2.87421 0.000179118 2.96123 0.000179118 3.0491V15.3437C0.000179118 17.0231 1.3702 18.3928 3.0495 18.3928H19.4441C21.1234 18.3928 22.4934 17.0231 22.4934 15.3437V3.0491C22.4934 2.96123 22.4897 2.87421 22.4823 2.78819C22.5144 2.57619 22.4788 2.35231 22.366 2.15073C22.3603 2.14066 22.3545 2.13071 22.3486 2.1209C21.9543 0.892963 20.8 0 19.4441 0H3.0495C1.69358 0 0.539303 0.892964 0.145021 2.12091ZM20.4934 4.34533V15.3437C20.4934 15.9183 20.019 16.3928 19.4441 16.3928H3.0495C2.47456 16.3928 2.00018 15.9183 2.00018 15.3437V4.34533L10.7582 9.2493C11.0618 9.41926 11.4318 9.41926 11.7354 9.2493L20.4934 4.34533Z" fill="#404040" />
                                            </svg>
                                            {unreadCount && unreadCount > 0 ? <span style={{ backgroundColor: color ?? '#D87833' }}>{unreadCount}</span> : null}

                                            {(!notifications || notifications.length <= 0)
                                                ?
                                                (
                                                    toggleNotifications && <div
                                                        className={`${variables.header_info_history_empty}`}>{trans('notification-empty')}</div>
                                                )
                                                :
                                                <CustomScrollbarNotification color={color ? color : '#413E38'} ref={scrollableDivRefNotifications} onScroll={handleScrollNotifications}>
                                                    <div className={`${variables.header_notification}`}>
                                                        <a style={{ color: color ?? '#D87833' }} onClick={handleReadAll}>{trans('notification-read-all')}</a>

                                                        {toggleNotifications && <ul className="header_notification_list">
                                                            {notifications && notifications.map((notification, index) => (
                                                                <li className={notification.status || readNotifications.includes(notification.id) ? '' : `${variables.active}`} key={index} onClick={() => handleReadClick(notification.id)}>
                                                                    {
                                                                        notification.status ||
                                                                            readNotifications.includes(notification.id) ? (<></>) : (
                                                                            <div className={`${variables.dot}`} style={{ backgroundColor: color ?? '#D87833' }}></div>
                                                                        )
                                                                    }

                                                                    <div className={`${variables.date}`}>{getTimeAgo(notification.sent_time)}</div>
                                                                    <div className={`${variables.title}`}>{notification.title}</div>
                                                                    <div className={`${variables.description}`} dangerouslySetInnerHTML={{ __html: notification.description.replace(/\n/g, '<br>') }}></div>
                                                                </li>
                                                            ))}
                                                        </ul>}
                                                    </div>
                                                </CustomScrollbarNotification>
                                            }
                                        </a>

                                        <div className={`${variables.header_info_item} ${variables.login_link}`} id="infor-group" onClick={togglePopup}>
                                            {tokenLoggedInCookie && <div className={`${variables.home_avatar}`}>
                                                {infoUser.photo
                                                    ?
                                                    <span className={`${variables.avatar_photo}`}><img src={infoUser?.photo} alt={infoUser.avatar} /></span>
                                                    :
                                                    <span style={{ backgroundColor: color ?? '#D87833' }}>{infoUser.avatar}</span>
                                                }
                                                <span className="username-limit">{infoUser.fullname}</span>
                                            </div>
                                            }
                                        </div>

                                        <a className={`${variables.header_info_item}`} href="#" onClick={handleLogoutClick}>
                                            <svg xmlns="http://www.w3.org/2000/svg" width="26" height="26" viewBox="0 0 26 26" fill="none">
                                                <path d="M10 22H6C5.46957 22 4.96086 21.7893 4.58579 21.4142C4.21071 21.0391 4 20.5304 4 20V6C4 5.46957 4.21071 4.96086 4.58579 4.58579C4.96086 4.21071 5.46957 4 6 4H10" stroke="#404040" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" />
                                                <path d="M17 18L22 13L17 8" stroke="#404040" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" />
                                                <path d="M22 13H10" stroke="#404040" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" />
                                            </svg>
                                        </a>
                                    </div>
                                )
                            }
                        </div>
                    </div>
                </nav>
            </div>
            {isProfileOpen && <Profile togglePopup={() => togglePopup()} />}
            {isRegisterConfirmOpen && <RegisterConfirm togglePopup={() => togglePopupRegisterConfirm()} />}
            {isLoginOpen && <Login togglePopup={() => togglePopupLogin()} from={null} />}
        </>
    )
}
