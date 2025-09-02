'use client'

import style from 'public/assets/css/self-service.module.scss'
import { useI18n } from '@/locales/client'
import Image from "next/image";
import { useRouter } from "next/navigation";
import { useAppSelector } from '@/redux/hooks'
import { useGetWorkspaceDataByIdQuery } from '@/redux/services/workspace/workspaceDataApi'
import { api } from "@/utils/axios"
import variables from '/public/assets/css/intro-table.module.scss'
import { hexToRgb } from "@/utils/rgb";
import Cookies from "js-cookie";
import * as config from "@/config/constants";
import { useEffect, useState } from "react";
import useValidateSecurity from "@/hooks/useTableSelfOrderingSecurity";
import InvalidSecurity from "@/app/[locale]/components/404/invalid-security";
import IntroBackground from '@/app/[locale]/components/share/introBackground';
export default function Page() {    
    const language = Cookies.get('Next-Locale') ?? 'nl';
    // Get workspace info
    const workspaceId = useAppSelector((state) => state.workspaceData.globalWorkspaceId)
    const { data: apiDataToken } = useGetWorkspaceDataByIdQuery({ id: workspaceId })
    const apiData = apiDataToken?.data?.setting_generals;
    const color = useAppSelector((state) => state.workspaceData.globalWorkspaceColor)
    const apiPhoto = apiDataToken?.data?.photo;
    const trans = useI18n();
    const router = useRouter();
    const tokenLoggedInCookie = Cookies.get('loggedToken');
    let rgbColor = hexToRgb('#FFFFFF')

    if (color) {
        rgbColor = hexToRgb(color);
    }

    const [typeOpeningHours, setTypeOpeningHours] = useState<any | null>(2);
    const [openingHours, setOpeningHours] = useState<any | null>('status_closed');
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

    const timezone: any = config.TIMEZONE;
    const getDayInTimeZone = (timezone: any) => {
        const currentDate = new Date();
        const options = {
            timeZone: timezone,
        };
        const dateInTimeZone = new Date(currentDate.toLocaleString('en-US', options));

        return dateInTimeZone.getDay();
    };

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
                setWorkspaceDataFinal(json.data);
            }).catch(error => {
                // console.log(error)
            });
        }, 1000);
    }, [workspaceId]);

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
                        window.location.reload();
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

                            if (now >= startTime && now <= endTime) {
                                setOpeningHours('status_open');
                                if (clicking) {
                                    router.push('/table-ordering/cart')
                                    Cookies.remove('fromTableCart');
                                } else {
                                    return;
                                }
                            } else {
                                setOpeningHours('status_closed');
                                if (clicking) {
                                    window.location.reload();
                                }
                            }
                        }
                    })
                }
            }
        })
    };
    
    const handleAgain = () => {
        workspaceId && api.get(`workspaces/` + workspaceId, {
            headers: {
                'Content-Language': language
            }
        }).then(res => {
            if (Cookies.get('fromTableCart') == 'true') {
                workspaceId && api.get(`workspaces/` + workspaceId, {
                    headers: {
                        'Authorization': `Bearer ${tokenLoggedInCookie}`,
                        'Content-Language': language
                    }
                }).then(res => {
                    var json = res.data;
                    setWorkspaceDataFinal(json.data);
                    let flagCheckTime = true;

                    // validate in admin extra setting
                    json.data?.extras.map((item: any) => {
                        if (item?.type === 10) {
                            if (item.active != true) {
                                flagCheckTime = false;
                                window.location.reload();
                            }
                        }
                    });
                    
                    // validate in manager opening hours setting
                    json.data?.setting_open_hours.map((item: any) => {
                        if (item?.type === 2) {
                            if (item.active != true) {
                                flagCheckTime = false;
                                window.location.reload();
                            }
                        }
                    });

                    if(flagCheckTime === true) {
                        checkTime(true, json.data);
                    }
                }).catch(error => {
                    // console.log(error)
                });

            } else {
                router.push('/table-ordering');
            }
        }).catch(error => {
            // console.log(error)
        });
    }

    const validateSecurity = useValidateSecurity();
    if (!validateSecurity) {
        return (<InvalidSecurity />);
    }
    
    return (
        <>
            <div style={{ display: 'block' }}>
                <IntroBackground position="top" color={color} rgbColor={rgbColor}/>
                <div className={`${variables.heying} heying row  text-center justify-content-center ps-2 pe-2`}>
                    <div className={`row justify-content-center ${variables.table_ordering_notfound}`}>
                        <div className="col-sm-12 col-xs-12">
                            <div className={`${style['logo-confirmation']}`}>
                                <Image
                                    alt='intro'
                                    src={apiPhoto ? apiPhoto : ''}
                                    width={130}
                                    height={130}
                                    style={{ borderRadius: '50%' }}
                                />
                            </div>
                        </div>
                        <div className="col-sm-12 col-xs-12">
                            <div className={`${style['title-confirmation']} mt-4`}>
                                {trans('currently-closed')}
                            </div>
                        </div>
                        <div className="col-sm-12 col-xs-12">
                            <div className={`${style['sub-title-confirmation']}`}>
                                {trans('closed-order-again')}
                            </div>
                        </div>
                        <div className="col-sm-12 col-xs-12">
                            <div className={`${style['btn-confirmation']}`} onClick={() => { handleAgain() }}>
                                {trans('try-again')}
                            </div>
                        </div>
                    </div>
                </div>
                <IntroBackground position="bottom" color={color} rgbColor={rgbColor}/>
            </div>
        </>
    )
}