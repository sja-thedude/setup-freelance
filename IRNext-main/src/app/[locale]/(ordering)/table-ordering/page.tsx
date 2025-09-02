"use client"

import Image from "next/image";
import variables from '/public/assets/css/intro-table.module.scss'
import { hexToRgb } from "@/utils/rgb";
import { useI18n } from '@/locales/client'
import * as config from "@/config/constants";
import { useAppSelector } from "@/redux/hooks";
import { useGetWorkspaceDataByIdQuery } from "@/redux/services/workspace/workspaceDataApi";
import React, { useEffect, useState } from "react";
import { api } from "@/utils/axios";
import Cookies from "js-cookie";
import {useRouter} from "next/navigation";
import _ from "lodash";
import SwitchLangMobile from "@/app/[locale]/components/share/switchLangMobile";
import useValidateSecurity from "@/hooks/useTableSelfOrderingSecurity";
import {useValidateToTriggerClosedScreen} from "@/hooks/useTableSelfOrderingSecurity";
import {OPENING_HOUR_TABLE_ORDERING_TYPE, EXTRA_SETTING_TABLE_ORDERING_TYPE} from "@/config/constants"
import InvalidSecurity from "@/app/[locale]/components/404/invalid-security";
import IntroBackground from '@/app/[locale]/components/share/introBackground';
import Loading from "@/app/[locale]/components/loading";
const listingItemCheckClassName = variables['listing-item-check'];
const listingItemTextClassName = `${variables['listing-item-text']} d-flex align-items-center`;
const btnDark = `btn btn-dark ${variables['btn-dark']}`;
const buttonOrder = variables['button-order'];
const listing = variables['listing'];

export default function Page() {    
    const language = Cookies.get('Next-Locale') ?? 'nl';
    const tableOrderingCart = useAppSelector((state) => state.cart.data)
    // Trong functional component
    const workspaceToken = useAppSelector((state) => state.workspaceData.globalWorkspaceToken)
    const workspaceId = useAppSelector((state) => state.workspaceData.globalWorkspaceId)
    const { data: apiDataToken } = useGetWorkspaceDataByIdQuery({ id: workspaceId })
    const apiData = apiDataToken?.data?.setting_generals;
    const color = useAppSelector((state) => state.workspaceData.globalWorkspaceColor)
    const trans = useI18n()
    let rgbColor = hexToRgb('#FFFFFF')
    const [isLoading, setIsLoading] = useState(true);
    if (color) {
        rgbColor = hexToRgb(color);
    }

    // Check logged token
    const tokenLoggedInCookie = Cookies.get('loggedToken');
    const [workspaceDataFinal, setWorkspaceDataFinal] = useState<any | null>(null);

    useEffect(() => {
        setTimeout(function () {
            workspaceId && api.get(`workspaces/` + workspaceId, {
                headers: {
                    'Authorization': `Bearer ${tokenLoggedInCookie}`,
                    'Content-Language': language
                }
            }).then(res => {
                const json = res.data;
                setIsLoading(false);
                setWorkspaceDataFinal(json.data);
            }).catch(error => {
                // console.log(error)
            });
        }, 10);
    }, [workspaceId]);

    const [statusEnableTableOrdering, setStatusEnableTableOrdering] = useState<number | null>(null);
    useEffect(() => {
        workspaceDataFinal?.extras.map((item: any) => {
            if (item?.type === 10) {
                if (item.active === true) {
                    setStatusEnableTableOrdering(1)
                }
            }
        });
    }, [workspaceDataFinal]);

    const handleEnableRestaurant = () => {
        if (statusEnableTableOrdering === 0) {
            window.location.href = '/table-ordering';
        } else {
            if (openingHours === 'status_closed') {
                window.location.href = '/table-ordering';
            } else {
                window.location.href = '/table-ordering';
            }
        }
    }

    //check Restaurant closed Table Ordering
    //opening hours
    const statusOpeningHours: Record<string, string> = {
        'status_open': trans('status_open'),
        'status_about_to_close': trans('status_about_to_close'),
        'status_closed': trans('status_closed'),
    };

    const [typeOpeningHours, setTypeOpeningHours] = useState<any | null>(2);
    const [openingHours, setOpeningHours] = useState<any | null>('status_closed');
    const timezone: any = config.TIMEZONE;
    const router = useRouter()
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

    const checkTime = (clicking: any, dataPass: any) => {
        const now: any = new Date(getCurrentDateInTimeZone(timezone));
        const dayName: any = getDayInTimeZone(timezone);
        const dataFinal = dataPass ? dataPass : workspaceDataFinal;

        dataFinal?.setting_open_hours.map((item: any, index: any) => {
            if (item.type === typeOpeningHours) {
                const hasDayName = (obj: any) => obj.day_number === dayName;

                // check if has day name or not
                if (!item.open_time_slots.some(hasDayName)) {
                    setOpeningHours('status_closed');
                    if (clicking) {
                        router.push('/table-ordering')
                    }
                } else {
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
                                if (clicking) {
                                    router.push('/table-ordering/products')
                                } else {
                                    return;
                                }
                            } else if (now >= thirtyMinutesBeforeEnd && now <= endTime) {
                                setOpeningHours('status_about_to_close');
                                if (clicking) {
                                    router.push('/table-ordering/products')
                                } else {
                                    return;
                                }
                            } else {
                                setOpeningHours('status_closed');
                                if (clicking) {
                                    router.push('/table-ordering')
                                }
                            }
                        }
                    })
                }
            }
        })
    };
    useEffect(() => {
        checkTime(null, null);
    }, [workspaceDataFinal]);

    const [showSection, setShowSection] = useState<any | null>(false);
    setTimeout(function () {
        setShowSection(true);
    }, 1500);


    const handleGoTable = () => {
        workspaceId && api.get(`workspaces/` + workspaceId, {
            headers: {
                'Authorization': `Bearer ${tokenLoggedInCookie}`,
                'Content-Language': language
            }
        }).then(res => {
            var json = res.data;
            setWorkspaceDataFinal(json.data);
            json.data?.extras.map((item: any) => {
                if (item?.type === 10) {
                    if (item.active === true) {
                        setStatusEnableTableOrdering(1)
                        checkTime(true, json.data);
                    } else {
                        setStatusEnableTableOrdering(0)
                    }
                }
            });
        }).catch(error => {
            // console.log(error)
        });

    }

    useValidateToTriggerClosedScreen(router, workspaceId, OPENING_HOUR_TABLE_ORDERING_TYPE, EXTRA_SETTING_TABLE_ORDERING_TYPE);

    const validateSecurity = useValidateSecurity();
    if (!validateSecurity) {
        return (<InvalidSecurity />);
    }

    if (isLoading) return <Loading />;
    return (
        <>
            <div style={{ display: !showSection ? 'none' : 'block' }}>
                <SwitchLangMobile origin="home-page"/>
                <IntroBackground position="top" color={color} rgbColor={rgbColor}/>
                <div className={`${variables.heying} heying row relative-index-2 text-center justify-content-center ps-2 pe-2`}>
                    {statusEnableTableOrdering === 0 ? (
                        <div className={`row justify-content-center ${variables.table_ordering_notfound}`}>
                            <div style={{ padding: '0' }}>
                                <h1>{trans('table-ordering-notfound-title')}</h1>
                                <p>{trans('table-ordering-notfound-description')}</p>
                                <div className={`${buttonOrder} ${variables.button} mt-5`}>
                                    <button type="button" className={btnDark} onClick={handleEnableRestaurant}>{trans('table-ordering-notfound-button')}</button>
                                </div>
                            </div>
                        </div>
                    ) : openingHours === 'status_closed' ? (
                        <div className={`row justify-content-center ${variables.table_ordering_notfound}`}>
                            <div style={{ padding: '0' }}>
                                <div className="row justify-content-center mt-1">
                                    <div className={`${variables.introImage}`}>
                                        <Image
                                            alt='intro'
                                            src={workspaceDataFinal ? workspaceDataFinal?.photo : ''}
                                            width={130}
                                            height={130}
                                            style={{ borderRadius: '50%' }}
                                        />
                                    </div>
                                </div>
                                <h1>{trans('table-ordering-notfound-disable-title')}</h1>
                                <p>{trans('table-ordering-notfound-disable-description')}</p>
                                <div className={`${buttonOrder} ${variables.button} mt-5`}>
                                    <button type="button" className={btnDark} onClick={handleEnableRestaurant}>{trans('table-ordering-notfound-button')}</button>
                                </div>
                            </div>
                        </div>
                    ) : true ? (
                        <div className={`${variables.table_ordering_open}`}>
                            <div>
                                <div className={`row justify-content-center mt-1`}>
                                    <div className={`col-md-6 ${variables.introImage}`}>
                                        <Image
                                            alt='intro'
                                            src={workspaceDataFinal ? workspaceDataFinal?.photo : ''}
                                            width={130}
                                            height={130}
                                            style={{ borderRadius: '50%' }}
                                        />
                                    </div>
                                </div>

                                <div className={`${variables.title} mt-2`}>
                                    <h1>{apiData && apiData.title ? trans('welcome') + ' ' + apiData.title : ''}</h1>
                                </div>
                                <div className={variables.description}>
                                    <p> {trans('introduce')}</p>
                                </div>
                                <div className="row justify-content-center">
                                    <div className="col-sm-12 col-xs-12 justify-content-center" style={{ width: 'fit-content' }}>
                                        <div className={`text-center ${listing} ${variables.listing} d-flex align-items-center`}>
                                            <div className={`${listingItemCheckClassName} me-2`}>
                                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="22" viewBox="0 0 20 22" fill="none" className="mb-1">
                                                    <g clipPath="url(#clip0_4169_3274)">
                                                        <path d="M18.3333 5.5L8.24996 15.5833L3.66663 11" stroke={color} strokeWidth="4" strokeLinecap="round" strokeLinejoin="round" />
                                                    </g>
                                                    <defs>
                                                        <clipPath id="clip0_4169_3274">
                                                            <rect width="19.4346" height="22" fill="white" />
                                                        </clipPath>
                                                    </defs>
                                                </svg>
                                            </div>
                                            <div className={listingItemTextClassName}>{trans('view-menu')}</div>
                                        </div>

                                        <div className={`text-center ${listing} ${variables.listing} d-flex align-items-center`}>
                                            <div className={`${listingItemCheckClassName} me-2`}>
                                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="22" viewBox="0 0 20 22" fill="none" className="mb-1">
                                                    <g clipPath="url(#clip0_4169_3274)">
                                                        <path d="M18.3333 5.5L8.24996 15.5833L3.66663 11" stroke={color} strokeWidth="4" strokeLinecap="round" strokeLinejoin="round" />
                                                    </g>
                                                    <defs>
                                                        <clipPath id="clip0_4169_3274">
                                                            <rect width="19.4346" height="22" fill="white" />
                                                        </clipPath>
                                                    </defs>
                                                </svg>
                                            </div>
                                            <div className={listingItemTextClassName}>{trans('make-choice')}</div>
                                        </div>
                                        <div className={`text-center ${listing} ${variables.listing} d-flex align-items-center`}>
                                            <div className={`${listingItemCheckClassName} me-2`}>
                                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="22" viewBox="0 0 20 22" fill="none" className="mb-1">
                                                    <g clipPath="url(#clip0_4169_3274)">
                                                        <path d="M18.3333 5.5L8.24996 15.5833L3.66663 11" stroke={color} strokeWidth="4" strokeLinecap="round" strokeLinejoin="round" />
                                                    </g>
                                                    <defs>
                                                        <clipPath id="clip0_4169_3274">
                                                            <rect width="19.4346" height="22" fill="white" />
                                                        </clipPath>
                                                    </defs>
                                                </svg>
                                            </div>
                                            <div className={listingItemTextClassName}>{trans('enter-tb-number')}</div>
                                        </div>
                                        <div className={`text-center ${listing} ${variables.listing} d-flex align-items-center`}>
                                            <div className={`${listingItemCheckClassName} me-2`}>
                                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="22" viewBox="0 0 20 22" fill="none" className="mb-1">
                                                    <g clipPath="url(#clip0_4169_3274)">
                                                        <path d="M18.3333 5.5L8.24996 15.5833L3.66663 11" stroke={color} strokeWidth="4" strokeLinecap="round" strokeLinejoin="round" />
                                                    </g>
                                                    <defs>
                                                        <clipPath id="clip0_4169_3274">
                                                            <rect width="19.4346" height="22" fill="white" />
                                                        </clipPath>
                                                    </defs>
                                                </svg>
                                            </div>
                                            <div className={listingItemTextClassName}>{trans('chose-payment')}</div>
                                        </div>
                                    </div>
                                </div>

                                <div className={`${buttonOrder} ${variables.buttonOrder} mt-5`} style={{ margin: 'auto' }}>
                                    <div onClick={handleGoTable} >
                                        <a>
                                            <button type="button" className={`${btnDark} text-uppercase`}>
                                                {_.isEmpty(tableOrderingCart) ? trans('start-order') : trans('order_continuous')}
                                            </button>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    ) : (
                        null
                    )}
                </div>
                <IntroBackground position="bottom" color={color} rgbColor={rgbColor}/>
            </div>
        </>
    );
};
