"use client"

import React, { memo, useState, useEffect, useRef, useCallback } from 'react'
import { useI18n } from '@/locales/client'
import Slider from "react-slick";
import "slick-carousel/slick/slick.css";
import "slick-carousel/slick/slick-theme.css";
import style from 'public/assets/css/datetime-list.module.scss'
import { api } from "@/utils/axios";
import DatePicker, { registerLocale } from "react-datepicker";
import "react-datepicker/dist/react-datepicker.css";
import moment from 'moment';
import { addDays, subDays, getDay } from 'date-fns';
import { useAppSelector, useAppDispatch } from '@/redux/hooks'
import { changeRootInvalidProductIds, rootCartDatetime } from '@/redux/slices/cartSlice'
import nl from 'date-fns/locale/nl';
import _ from "lodash";
registerLocale("nl", nl);
import { setGroupOrderData } from '@/redux/slices/groupOrderSlice'
import Cookies from "js-cookie";
import useMediaQuery from '@mui/material/useMediaQuery'
import LimitTimeToPaymentPopup from '@/app/[locale]/components/ordering/cart/limitTimeToPayment/limitTimeToPaymentPopup'
import LimitTimeToPaymentMessage from '@/app/[locale]/components/ordering/cart/limitTimeToPayment/limitTimeToPaymentMessage'

const DatetimeList = (props: any) => {
    const initialRef: any = null;
    const color = props.color
    const type = props.type - 1
    const totalPrice = props.totalPrice
    const sendActiveStep = props.activeStep
    const dispatch = useAppDispatch()
    const [selectedDate, setSelectedDate] = useState<any>('');
    const [selectedTime, setSelectedTime] = useState<any>('');
    const [selectedTimeIndex, setSelectedTimeIndex] = useState<any>('');
    const [activeDays, setActiveDays] = useState<any[]>([]);
    const [holidays, setHolidays] = useState<any[]>([]);
    const [disableDates, setDisableDates] = useState<any[]>([]);
    const [showTime, setShowTime] = useState(false);
    const [openDatePicker, setOpenDatePicker] = useState(false);
    const [dayOrder, setDayOrder] = useState(0);
    const [timeOrder, setTimeOrder] = useState(0);
    const [errorDate, setErrorDate] = useState('');
    const [errorTime, setErrorTime] = useState('');
    const [errorLimitToPayment, setErrorLimitToPayment] = useState(false);
    let [invalidProductIds, setInvalidProductIds] = useState<any>([]);
    const [timeslotsData, setTimeslotsData] = useState<any[]>([]);
    const [timeslotId, setTimeslotId] = useState(initialRef);
    const [settingTimeslotId, setSettingTimeslotId] = useState(initialRef);
    const [todayValidTimeSlot, setTodayValidTimeSlot] = useState([]);
    const workspaceId = useAppSelector((state) => state.workspaceData.globalWorkspaceId)
    const cartLimitTimeToPayment = useAppSelector<any>((state) => state.cart.cartLimitTimeToPayment);
    let cart = useAppSelector((state) => state.cart.rootData)
    const cartProductIds = _.map(cart, 'productId').map(i => Number(i))
    const trans = useI18n()
    const groupOrder = useAppSelector<any>((state) => state.groupOrder.data);
    const groupOrderNowSlice = useAppSelector<any>((state: any) => state.cart.groupOrderSelectedNow);
    const tokenLoggedInCookie = Cookies.get('loggedToken');
    const language = Cookies.get('Next-Locale');
    const sliderRef = useRef<Slider | null>(null);
    const orderTypes = [
        trans('take-away'),
        trans('delivery'),
        trans('group-order'),
    ]
    var settings = {
        //dots: true,
        infinite: false,
        speed: 500,
        slidesToShow: 1,
        slidesToScroll: 1,
        arrows: true,
        className: "cart-custom-carousel",
    };
    Cookies.set('atStep2', 'true')
    let cartDatetime: any = useAppSelector((state) => state.cart.rootCartDatetime);

    useEffect(() => {
        if (cartDatetime?.date && cartDatetime?.time && cartDatetime?.settingTimeslotId) {
            handleSelectedDate(new Date(moment(cartDatetime?.date).format('YYYY-MM-DD')));
            setSelectedTime(cartDatetime?.time);
            setTimeslotId(cartDatetime?.settingTimeslotId);
            setSelectedTimeIndex(cartDatetime?.selectedTimeIndex);
        }
        if (groupOrder && groupOrderNowSlice && groupOrderNowSlice?.id && cartDatetime?.date) {
            handleSelectedDate(new Date(moment(cartDatetime?.date).format('YYYY-MM-DD')));
        }
    }, [
        todayValidTimeSlot
    ]);

    const isMobile = useMediaQuery('(max-width: 1279px)');
    useEffect(() => {
        if (sliderRef.current) {
            goToSlide(Number(cartDatetime?.selectedTimeIndex));
        }
    }, [timeslotsData, cartDatetime?.selectedTimeIndex]);

    const needDisableNextButton = useCallback(() => {
        const today = new Date();
        let flag = false;

        if(!selectedDate) {
            flag = true;
        } else if(!(timeslotId || (groupOrder && groupOrderNowSlice && groupOrderNowSlice?.id && selectedDate))) {
            flag = true;
        } else if(type != 2 && (today.setHours(0, 0, 0, 0) == selectedDate.setHours(0, 0, 0, 0) && (todayValidTimeSlot?.length < 1 || !_.find(todayValidTimeSlot, {id: timeslotId})))) {
            flag = true;
        }

        return flag;
    }, [selectedDate, selectedTime, timeslotId, groupOrder, groupOrderNowSlice, todayValidTimeSlot]);

    const handleNext = async () => {
        invalidProductIds = [];
        setErrorTime('');
        setErrorDate('');
        setErrorLimitToPayment(false);
        if (groupOrderNowSlice && groupOrderNowSlice?.id && workspaceId) {
            const groupOrder = groupOrderNowSlice?.id;
            api.get(`/groups/${groupOrder}`, {
                headers: {
                    'Authorization': `Bearer ${tokenLoggedInCookie}`,
                    'Content-Language': language,
                }
            }).then((groupDetail: any) => {
                if (groupDetail?.status == 200 && groupDetail?.data?.success == true) {
                    const groupDetailData = groupDetail?.data?.data;
                    dispatch(setGroupOrderData(groupDetailData));
                }
            }).catch((error: any) => {
                console.log(error);
            });
        }
        
        if(needDisableNextButton() === false) {
            const res = await api.get(`products/validate_available_timeslot?date=${moment(selectedDate).format(DATE_FORMAT)}&${cartProductIds.map(i => `product_id[]=${i}`).join('&')}`);
            Object.entries(res?.data?.data).filter((status: any) => {
                if (status[1] == false) {
                    if (!invalidProductIds.includes(status[0])) {
                        invalidProductIds.push(status[0]);
                        setErrorDate(trans('invalidate-date'));
                    }
                }
            });
    
            if (invalidProductIds.length == 0) {
                let res = null;
                if (groupOrder && groupOrderNowSlice && groupOrderNowSlice?.id) {
                    res = await api.get(`products/validate_available_timeslot?date=${moment(selectedDate).format(DATE_FORMAT)}&time=${groupOrder?.receive_time?.split(':')[0] + ":" + groupOrder?.receive_time?.split(':')[1]}&${cartProductIds.map(i => `product_id[]=${i}`).join('&')}`);
                } else {
                    res = await api.get(`products/validate_available_timeslot?date=${moment(selectedDate).format(DATE_FORMAT)}&time=${selectedTime?.split(':')[0] + ":" + selectedTime?.split(':')[1]}&${cartProductIds.map(i => `product_id[]=${i}`).join('&')}`);
                }
    
                Object.entries(res?.data?.data).filter((status: any) => {
                    if (status[1] == false) {
                        setErrorTime(trans('invalidate-time'));
                        invalidProductIds.push(status[0]);
                    }
                });
            }
    
            if (invalidProductIds.length > 0) {
                setInvalidProductIds(invalidProductIds);
                dispatch(changeRootInvalidProductIds(invalidProductIds));
    
                if (window.innerWidth < 1280) {
                    document.getElementById("open-back-modal")?.click();
                }
            } else {
                const dataDatetime = {
                    'date': selectedDate,
                    'time': selectedTime,
                    'selectedTimeIndex': selectedTimeIndex,
                    'settingTimeslotId': timeslotId,
                }
                if (groupOrder && groupOrderNowSlice && groupOrderNowSlice?.id) {
                    const dataDatetimeGroup = {
                        'date': selectedDate,
                        'time': "00:00:00",
                        'selectedTimeIndex': selectedTimeIndex,
                        'settingTimeslotId': timeslotId,
                    }
                    dispatch(rootCartDatetime(dataDatetimeGroup));
                } else {
                    dispatch(rootCartDatetime(dataDatetime));
                }
                sendActiveStep(3);
            }   
        }        
    }

    const DATE_FORMAT = 'YYYY-MM-DD';
    const dateTimeFormat = 'YYYY-MM-DD HH:mm:ss';

    useEffect(() => {
        const fetchData = async () => {
            if (workspaceId) {
                const res = await api.get(`workspaces/${workspaceId}/settings/preferences`);
                if (type == 0) {
                    setDayOrder(res?.data?.data?.takeout_day_order);
                    setTimeOrder(res?.data?.data?.takeout_min_time);
                } else if (type == 1) {
                    setDayOrder(res?.data?.data?.delivery_day_order);
                    setTimeOrder(res?.data?.data?.delivery_min_time);
                }
            }
        }

        const fetchDataDayNumber = async () => {
            if (workspaceId) {
                const res = await api.get(`workspaces/${workspaceId}/settings/opening_hours`);
                const result = res?.data?.data.filter((obj: any) => {
                    return obj.type == type;
                });

                if (result[0]) {
                    const resultDays = result[0].timeslots.filter((obj: any) => {
                        if (!obj.start_time && obj?.day_number) {
                            activeDays.push(obj?.day_number);
                        }
                    });
                }
            }
        }

        const fetchDataHoliday = async () => {
            if (workspaceId) {
                const res = await api.get(`workspaces/${workspaceId}/settings/holiday_exceptions`);
                const result = res?.data?.data.filter((obj: any) => {
                    if (obj.start_time && obj.end_time) {
                        holidays.push({
                            start: subDays(new Date(obj.start_time), 1),
                            end: addDays(new Date(obj.end_time), 0)
                        });
                    }
                });
            }
        }

        const fetchDataTimeSlotDays = async () => {
            if (workspaceId) {
                const res = await api.get(`workspaces/${workspaceId}/settings/timeslot_order_days?type=${type}`);
                const result = Object.entries(res?.data?.data).filter((obj: any) => {
                    if (obj[1] == false) {
                        disableDates.push(Date.parse(obj[0]));
                    }
                });
            }
        }

        fetchData();
        fetchDataDayNumber();
        fetchDataHoliday();
        fetchDataTimeSlotDays();
    }, [
        workspaceId
    ])

    const fetchDataSettingTimeslots = async (
        workspaceId: any,
        date: any, 
        type: any, 
        dayOrder: any, 
        timeOrder: any,
        enableValidToday: boolean, 
        enableSettingTimeSlotId: boolean,
        enableSettingTimeSlotDataAll: boolean,
        enableSettingTimeSlotDataToday: boolean
    ) => {
        if (workspaceId) {
            const res = await api.get(`workspaces/${workspaceId}/settings/timeslots?date=${date}&type=${type}&restaurent_id=${workspaceId}`);
            const timeSlotData = res?.data?.data;

            if(enableSettingTimeSlotDataAll) {
                setTimeslotsData(timeSlotData?.timeslots);
            }
                        
            let todayValidTimeSlots;

            if (timeSlotData?.max_mode) {
                const timeSlotApplicableDays = timeSlotData.max_days; // days of week that can apply time slot setting
                const limitBeforeDay: number = timeSlotData.max_before; // number of date before limit time
                const limitBeforeTime = timeSlotData.max_time; // last hour that user can order
                const days = getDaysBetweenDates(moment().format(DATE_FORMAT), moment().add(dayOrder, 'd').format(DATE_FORMAT));
                const applicableDates = days.filter((d) => {
                    const dayOfWeek = moment(d, DATE_FORMAT).format('d');
                    return timeSlotApplicableDays.includes(Number(dayOfWeek));
                })
                    .map((i) => moment(`${i} ${limitBeforeTime}`, dateTimeFormat).format(dateTimeFormat))
                    .filter((x) => {
                        if (limitBeforeDay === 0) {
                            return moment().isAfter(moment(x, dateTimeFormat));
                        } else {
                            return moment(x, dateTimeFormat).diff(moment(), 'h') < limitBeforeDay * 24;
                        }
                    })
                    .map((y) => moment(y, dateTimeFormat).format(DATE_FORMAT));
                todayValidTimeSlots = timeSlotData?.timeslots.filter((i: any) => {
                    const timeSlot = moment(`${moment().format(DATE_FORMAT)} ${i.time}`, dateTimeFormat);
                    return i.type === type && i.active && moment().add(timeOrder, 'm').isBefore(timeSlot);
                });

                const disableDatesMoment = [...applicableDates];
                disableDatesMoment.map((i) => {
                    disableDates.push(Date.parse(i));
                });
            } else {
                todayValidTimeSlots = timeSlotData?.timeslots.filter((i: any) => {
                    const timeSlot = moment(`${moment().format(DATE_FORMAT)} ${i.time}`, dateTimeFormat);
                    return i.type === type && i.active && moment().add(timeOrder, 'm').isBefore(timeSlot);
                });        
            }

            if(enableValidToday) {
                setTodayValidTimeSlot(todayValidTimeSlots);
            }

            if(enableSettingTimeSlotDataToday) {
                setTimeslotsData(todayValidTimeSlots ?? todayValidTimeSlots);
            }   

            if(enableSettingTimeSlotId) {
                setSettingTimeslotId(timeSlotData?.id);
            }            
        }
    }

    useEffect(() => {
        // Validate today timeslot
        fetchDataSettingTimeslots(
            workspaceId,
            moment().format(DATE_FORMAT), 
            type, 
            dayOrder, 
            timeOrder,
            true, 
            true,
            false,
            false
        );
    }, [
        dayOrder, 
        timeOrder
    ])

    const getDaysBetweenDates = function (startDate: string, endDate: string) {
        const now = moment(startDate, DATE_FORMAT).clone();
        const dates = [];
        while (now.isSameOrBefore(moment(endDate, DATE_FORMAT))) {
            dates.push(now.format(DATE_FORMAT));
            now.add(1, 'days');
        }

        return dates;
    };

    const isActiveDay = (date: any) => {
        const day = getDay(date);
        // logic for group Order 
        if (groupOrder && groupOrderNowSlice && groupOrderNowSlice?.id) {
            const inactiveDay = groupOrder.timeslots
                .filter((item: any) => item.status === 0)
                .map((item: any) => item.day_number);
            // logic check if time now is after close_time of group order
            if (moment(date).isSame(moment(), 'day')) {
                const currentTime = moment();
                const closeTime = moment(groupOrder?.close_time, 'HH:mm:ss');
                if (currentTime.isAfter(closeTime)) {
                    return false;
                }
            }
            // logic check if day is in inactiveDay
            if (!inactiveDay.includes(day)) {
                return true;
            } else {
                return false;
            }
        } else {
            if (!activeDays.includes(day)) {
                return true;
            } else {
                return false;
            }
        }
    };

    const handleSelectedDate = (date: any) => {
        const today = new Date();
        setSelectedDate(date);

        if (groupOrder && groupOrderNowSlice && groupOrderNowSlice?.id) {
            setShowTime(false);
        } else {
            setShowTime(true);
        }

        setTimeslotId(null);

        if (selectedDate?.toString().slice(0, 3) != date?.toString().slice(0, 3)) {
            goToSlide(0);
        }

        if (today.setHours(0, 0, 0, 0) == date.setHours(0, 0, 0, 0) && todayValidTimeSlot?.length > 0) {
            fetchDataSettingTimeslots(
                workspaceId,
                moment(date).format('YYYY-MM-DD'), 
                type, 
                dayOrder, 
                timeOrder,
                false, 
                false,
                false,
                true
            );
        } else {
            // Set all slots if today is not selected        
            fetchDataSettingTimeslots(
                workspaceId,
                moment(date).format('YYYY-MM-DD'), 
                type, 
                dayOrder, 
                timeOrder,
                false, 
                false,
                true,
                false
            );
        }
    }

    useEffect(() => {
        if (openDatePicker) {
            document.getElementById('date-picker')?.click()
        } else {
            document.getElementById('cart-container')?.click()
        }
    }, [openDatePicker])

    const goToSlide = (slideIndex: any) => {
        if (sliderRef.current) {
            sliderRef.current.slickGoTo(slideIndex);
        }
    }

    const removeProduct = () => {
        dispatch(changeRootInvalidProductIds(invalidProductIds));
        Cookies.remove('fromDesk')
        sendActiveStep(1);
    };

    const ReactDatePickerInput = React.forwardRef<HTMLInputElement, React.DetailedHTMLProps<React.InputHTMLAttributes<HTMLInputElement>, HTMLInputElement>>
        ((props, ref) => (
            <input ref={ref} {...props} readOnly={true} />
        ))
    ReactDatePickerInput.displayName = 'ReactDatePickerInput';

    useEffect(() => {
        if (Cookies.get('productSuggestion') == 'false') {
            Cookies.set('productSuggestion', 'true')
        }
    }, [Cookies.get('productSuggestion')])

    const startTimeLimit = localStorage.getItem('cartLimitTimeToPayment');

    useEffect(() => {
        if(startTimeLimit) {
            localStorage.removeItem('cartLimitTimeToPayment');
        }
    }, [startTimeLimit])

    useEffect(() => {
        if(cartLimitTimeToPayment === true) {
            setErrorLimitToPayment(true)
        }
    }, [])

    const refCartHeader = useRef<HTMLDivElement>(null);
    const refCartFooter = useRef<HTMLDivElement>(null);

    return (
        <>
            <div className={`${style['datetime-list']}`}>
                <div className="res-mobile">
                    {groupOrderNowSlice && groupOrderNowSlice?.id ? (
                        <h1 className={`${style['group-order']}`} style={{ color: color ? color : 'black' }}>{trans('group-ordering')} {groupOrder ? groupOrder?.name : ''}</h1>
                    ) : (
                        <div className="row">
                            <div className="col-sm-12 col-12">
                                <div className={`${style['type-label']}`}>
                                    {orderTypes[type]}
                                </div>
                            </div>
                        </div>
                    )}
                </div>

                <div ref={refCartHeader} className="cart-navigation cart-header">
                    <div className={`${style.backing} res-desktop mb-3`} style={{ position: "relative" }}>
                        <svg onClick={() => { sendActiveStep(1); Cookies.remove('oppenedSuggest') ; Cookies.set('productSuggestion', 'true'); Cookies.remove('fromDesk') }} xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" style={{ position: "absolute" }}>
                            <path d="M15 18L9 12L15 6" stroke="#888888" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" />
                        </svg>
                        {groupOrderNowSlice && groupOrderNowSlice?.id ? (
                            <h1 className={`${style['group-order']}`} style={{ color: color ? color : 'black' }}>{trans('group-ordering')} {groupOrderNowSlice ? groupOrderNowSlice?.name : ''}</h1>
                        ) : (
                            <div className="row">
                                <div className="col-sm-12 col-12">
                                    <div className={`${style['type-label']}`} style={{ background: color }}>
                                        {orderTypes[type]}
                                    </div>
                                </div>
                            </div>
                        )}
                    </div>
                </div>

                {window.innerWidth >= 1280 && (
                    <>
                        {
                            (errorDate || errorTime || errorLimitToPayment) && (
                                <div className={`row d-flex ${style['datetime-error']} res-desktop`}>
                                    {
                                        (errorDate || errorTime) ? (
                                            <>
                                                <div className={`col-auto`}>
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="21" height="21" viewBox="0 0 21 21" fill="none">
                                                        <path d="M9.00417 3.37756L1.59292 15.7501C1.44011 16.0147 1.35926 16.3147 1.35841 16.6203C1.35755 16.9258 1.43672 17.2263 1.58804 17.4918C1.73936 17.7572 1.95755 17.9785 2.22091 18.1334C2.48427 18.2884 2.78361 18.3717 3.08917 18.3751H17.9117C18.2172 18.3717 18.5166 18.2884 18.7799 18.1334C19.0433 17.9785 19.2615 17.7572 19.4128 17.4918C19.5641 17.2263 19.6433 16.9258 19.6424 16.6203C19.6416 16.3147 19.5607 16.0147 19.4079 15.7501L11.9967 3.37756C11.8407 3.1204 11.621 2.90779 11.359 2.76023C11.0969 2.61267 10.8012 2.53516 10.5004 2.53516C10.1996 2.53516 9.90396 2.61267 9.64187 2.76023C9.37978 2.90779 9.16015 3.1204 9.00417 3.37756V3.37756Z" stroke="#E03009" strokeWidth="1.5" strokeLinecap="round" strokeLinejoin="round" />
                                                        <path d="M10.5 7.875V11.375" stroke="#E03009" strokeWidth="1.5" strokeLinecap="round" strokeLinejoin="round" />
                                                        <path d="M10.5 14.875H10.5088" stroke="#E03009" strokeWidth="1.5" strokeLinecap="round" strokeLinejoin="round" />
                                                    </svg>
                                                </div>
                                                <div className={`col ps-0 ${style['datetime-error-text']}`}>
                                                    <p>{errorDate ? errorDate :
                                                        errorTime ? errorTime : ""
                                                    }</p>
                                                    <div className="d-flex" style={{ textDecoration: "underline" }} role={'button'} onClick={() => removeProduct()}>
                                                        {trans('remove-products')}
                                                    </div>
                                                </div>
                                            </>
                                        ) : (
                                            <LimitTimeToPaymentMessage/>
                                        )
                                    }
                                </div>
                            )   
                        }
                    </>
                )}

                <div className="row">
                    <div className="col-sm-12 col-12">
                        <div className={`${style['datetime-label']} res-mobile`}>
                            {trans('datetime')}
                        </div>
                    </div>
                </div>
                <div className="row">
                    <div className="col-sm-12 col-12 date-picker-custom">
                        <div className={`${style['date-picker']}`}>
                            <DatePicker selected={selectedDate} className={`${style['date-input']}`}
                                dateFormat="EEEE dd MMM yyyy"
                                placeholderText={trans('choose-date')}
                                locale="nl"
                                id="date-picker"
                                disabledKeyboardNavigation
                                includeDateIntervals={
                                    !groupOrderNowSlice
                                        ? todayValidTimeSlot?.length > 0
                                            ? [{ start: subDays(new Date(), 1), end: addDays(new Date(), dayOrder ?? 0) }]
                                            : [{ start: subDays(new Date(), 0), end: addDays(new Date(), dayOrder ?? 0) }]
                                        : [{ start: subDays(new Date(), 1), end: addDays(new Date(), 1000000) }]
                                }
                                customInput={<ReactDatePickerInput />}
                                excludeDateIntervals={holidays}
                                excludeDates={groupOrderNowSlice ? [] : disableDates}
                                filterDate={isActiveDay}
                                style={{ borderRadius: '6px' }}
                                onChange={(date: any) => handleSelectedDate(date)}
                                renderCustomHeader={({
                                    monthDate,
                                    decreaseMonth,
                                    increaseMonth,
                                    prevMonthButtonDisabled,
                                    nextMonthButtonDisabled
                                }: {
                                    monthDate: any,
                                    decreaseMonth: any,
                                    increaseMonth: any,
                                    prevMonthButtonDisabled: any,
                                    nextMonthButtonDisabled: any,
                                }) => {
                                    return (
                                        <div className={style['header-cal']}>
                                            {
                                                monthDate.getMonth() == new Date().getMonth()
                                                    && monthDate.getFullYear() == new Date().getFullYear()
                                                    ? (<> </>)
                                                    : (
                                                        <div
                                                            className={style['header-cal-btn']}
                                                            onClick={decreaseMonth}
                                                        //disabled={prevMonthButtonDisabled}
                                                        >
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 28 28" fill="none">
                                                                <path d="M17 20L11 14L17 8" stroke={color} strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" />
                                                            </svg>
                                                        </div>
                                                    )
                                            }

                                            <div className={style['header-cal-year']}>
                                                {monthDate.toLocaleString("nl", {
                                                    month: "short",
                                                    year: "numeric",
                                                })}
                                            </div>

                                            <div
                                                className={style['header-cal-btn']}
                                                onClick={increaseMonth}
                                            //disabled={nextMonthButtonDisabled}
                                            >
                                                <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 28 28" fill="none">
                                                    <path d="M11 20L17 14L11 8" stroke={color} strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" />
                                                </svg>
                                            </div>
                                        </div>
                                    );
                                }}
                            />

                            <div className={`${style['date-icon']}`} style={{ background: color }} id="date-icon"
                                onClick={() => setOpenDatePicker(!openDatePicker)}>
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                    <path d="M19 4H5C3.89543 4 3 4.89543 3 6V20C3 21.1046 3.89543 22 5 22H19C20.1046 22 21 21.1046 21 20V6C21 4.89543 20.1046 4 19 4Z" stroke="white" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" />
                                    <path d="M16 2V6" stroke="white" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" />
                                    <path d="M8 2V6" stroke="white" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" />
                                    <path d="M3 10H21" stroke="white" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" />
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
                {(showTime && groupOrder) || (!groupOrder && timeslotsData && timeslotsData.length > 0) && (
                    <div className="row">
                        <div className="col-sm-12 col-12" >
                            <div className={`${style['time-group']}`}>
                                <Slider {...settings} ref={sliderRef}>
                                    {
                                        timeslotsData?.filter((item: any) => item.type == type).reduce((a: any[], c, i) => {
                                            if (i % 12 === 0) {
                                                a.push([]);
                                            }
                                            a[a.length - 1].push(c);
                                            return a;
                                        }, []).map((arr, key) => (
                                            <div key={key}>
                                                <div className={` ${style['group-time']}`}>
                                                    {
                                                        <div className={``} style={{ display: 'flex', flexWrap: 'wrap', width: '100%' }}>
                                                            {
                                                                arr.map((item: any, index: number) => (
                                                                    // <div>{item}</div>

                                                                    <div className={`col-xxl-3 col-xl-4 col-lg-4 col-sm-4 col-3 ${style['time-btn']}`}
                                                                        onClick={(totalPrice > Number(item.max_price)
                                                                            || !item.active
                                                                            || Number(item.current_price) > Number(item.max_price)
                                                                            || (totalPrice + Number(item.current_price)) > Number(item.max_price)
                                                                            || Number(item.current_order) >= Number(item.max_order)) ? () => { } : () => { setTimeslotId(item.id); setSelectedTime(item.time); setSelectedTimeIndex(key); }}
                                                                        key={index}>
                                                                        <div className={`${style['time-item']} 
                                                                        ${(totalPrice > Number(item.max_price)
                                                                                || !item.active
                                                                                || Number(item.current_price) > Number(item.max_price)
                                                                                || (totalPrice + Number(item.current_price)) > Number(item.max_price)
                                                                                || Number(item.current_order) >= Number(item.max_order)) && style['time-btn-disable']}`}
                                                                            style={timeslotId === item.id ? { background: color, color: 'white' } : { border: isMobile ? `1px solid ${color}` : '', color: isMobile ? color : '' }}>
                                                                            {item.time.split(':')[0]}:{item.time.split(':')[1]}
                                                                        </div>
                                                                    </div>
                                                                ))
                                                            }
                                                        </div>
                                                    }
                                                </div>
                                            </div>
                                        ))
                                    }
                                </Slider>
                            </div>

                        </div>
                    </div>
                )}
                <div className="row">
                    <div className="col-sm-12 col-12 text-center res-mobile">
                        <button className={`itr-btn-primary ${style['next-step-btn']}`} 
                            onClick={() => handleNext()}
                            style={needDisableNextButton() === false ? {} : { background: '#D1D1D1', color: "white" }} type="button" >{trans('cart.further')}</button>
                    </div>
                </div>
            </div>
            <div ref={refCartFooter} className={`cart-navigation cart-footer`}>
                <div className="row">
                    <div className="col-sm-12 col-12 text-center res-desktop">
                        <button className={`itr-btn-primary ${style['next-step-btn']}`}
                            onClick={() => handleNext()}
                            style={needDisableNextButton() === false
                                ? { background: color }
                                : { background: color, opacity: "0.5", color: "white" }} type="button" >
                            {trans('cart.further')}
                        </button>
                    </div>
                </div>
                <div className="row mt-3">
                    <div className="col-sm-12 col-12">
                        <div className={style.steps}>
                            <div className={style['step-item']} onClick={() => { sendActiveStep(1); Cookies.remove('oppenedSuggest'); Cookies.remove('fromDesk'); Cookies.set('productSuggestion', 'true') }} role={'button'}>
                                <div className={style['step-number']} style={{ color: window.innerWidth > 1280 ? '#FFF' : color, borderColor: color, background: window.innerWidth > 1280 ? color : '' }}>1</div>
                                <div className={style['step-name']} style={{ color: color }}>{trans('cart.step_overview')}</div>
                            </div>
                            <div className={style['step-item']} role={'button'}>
                                <div className={style['step-number']} style={{ color: window.innerWidth > 1280 ? '#FFF' : color, borderColor: color, background: window.innerWidth > 1280 ? color : '' }}>2</div>
                                <div className={style['step-name']} style={{ color: color }}>{trans('date-time')}</div>
                            </div>
                            <div className={style['step-item']}>
                                <div className={style['step-number']}>3</div>
                                <div className={style['step-name']}>{trans('cart.step_payment_method')}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div className="res-mobile" data-bs-toggle="modal" data-bs-target="#backModal" id="open-back-modal">
            </div>
            <div className="d-flex res-mobile">
                <div
                    className="modal"
                    id="backModal"
                >
                    <div className="modal-dialog">
                        <div className={`modal-content ${style['modal-content']}`}>
                            <div className="modal-body pt-0">
                                <div className={`mx-auto text-center`}>
                                    <svg xmlns="http://www.w3.org/2000/svg" width="101" height="3" viewBox="0 0 101 3" fill="none">
                                        <path d="M2 1.5H99" stroke="#E1E1E1" strokeWidth="3" strokeLinecap="round" />
                                    </svg>
                                </div>
                                <div className={style['modal-content-title']}>
                                    {trans('opps')}
                                </div>
                                <div className={style['modal-content-text']}>
                                    {errorDate ? errorDate :
                                        errorTime ? errorTime : ""
                                    }
                                </div>
                                <div className={style['btn-yes']} data-bs-dismiss="modal"
                                    aria-label="Close" onClick={() => removeProduct()}>
                                    {trans('remove-products')}
                                </div>

                                <div
                                    className={style['btn-no']}
                                    data-bs-dismiss="modal"
                                    aria-label="Close"
                                    style={{ color: color }}
                                    onClick={() => { dispatch(changeRootInvalidProductIds([])) }}
                                > {errorDate ? trans('edit-date') :
                                    errorTime ? trans('edit-time') : ""
                                    } </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <style>{`
                .cart-type-item {
                    border: 1px solid ${color};
                    color: ${color};
                }
                .active {
                    color: #FFFFFF;
                    background: ${color}!important;
                  }
                .react-datepicker__day--selected {
                    background: ${color}!important;
                }`}
            </style>
            <LimitTimeToPaymentPopup/>
        </>
    )
}

export default memo(DatetimeList)