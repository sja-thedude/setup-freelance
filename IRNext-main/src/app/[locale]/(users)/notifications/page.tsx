'use client'

import style from 'public/assets/css/profile.module.scss'
import React, { useState, useEffect } from 'react';
import { useI18n } from '@/locales/client';
import Cookies from 'js-cookie';
import {useRouter} from "next/navigation";
import {FontAwesomeIcon} from "@fortawesome/react-fontawesome";
import {faAngleLeft} from "@fortawesome/free-solid-svg-icons";
import { api } from "@/utils/axios";
import InfiniteScroll from 'react-infinite-scroll-component';
import moment from 'moment';
import { useAppSelector } from '@/redux/hooks'
import { useGetWorkspaceDataByIdQuery } from '@/redux/services/workspace/workspaceDataApi'
import * as locales from "@/config/locales";

export default function Profile() {
    const workspaceId = useAppSelector((state) => state.workspaceData.globalWorkspaceId)
    const workspaceToken = useAppSelector((state) => state.workspaceData.globalWorkspaceToken)
    const { data: apiDataToken } = useGetWorkspaceDataByIdQuery({id: workspaceId})
    const apiData = apiDataToken?.data?.setting_generals;
    const color = useAppSelector((state) => state.workspaceData.globalWorkspaceColor)
    const trans = useI18n();
    const tokenLoggedInCookie = Cookies.get('loggedToken');

    const [notifications, setNotifications] = useState<any[]>([]);
    const [readNotifications, setReadNotifications] = useState<any[]>([]);
    const router = useRouter();
    const [nextPage, setNextPage] = useState(2);
    const language = Cookies.get('Next-Locale') ?? locales.LOCALE_FALLBACK;

    //paginate
    const fetchMoreData = () => {
        const response = api.get(`notifications?limit=15&page=${nextPage}`, {
            headers: {
                'Authorization': `Bearer ${tokenLoggedInCookie}`,
                'App-Token': workspaceToken,
                'Content-Language': language
            }
        }).then(res => {
            const json = res.data;
            setNextPage(nextPage + 1);
            if (json.data) {
                const newData = notifications.concat(json.data.data);
                setNotifications(newData);
                return notifications;
            } else {
                return notifications;
            }
        }).catch(error => {
            // console.log(error)
        });
    }

    //read notification
    const handleReadClick = (id: number) => {
        const response = api.get(`notifications/read?id=${id}`, {
            headers: {
                'Authorization': `Bearer ${tokenLoggedInCookie}`,
                'Content-Language': language
            }
        }).then(res => {
            const json = res.data;
            if (json.success) {
                const newData = readNotifications.concat([id]);
                setReadNotifications(newData);
                return json.data;
            }
        }).catch(error => {
            // console.log(error)
        });
    };

    //intial data
    useEffect(() => {
        const response = api.get(`notifications?limit=15&page=1`, {
            headers: {
                'Authorization': `Bearer ${tokenLoggedInCookie}`,
                'App-Token': workspaceToken,
                'Content-Language': language
            }
        }).then(res => {
            const json = res.data;
            if (json.data) {
                setNotifications(json.data.data);
                return json.data;
            } else {
                return [];
            }
        }).catch(error => {
            // console.log(error)
        });
    }, []);

     const formatDate = (time?: string, timeFormat?: string, outputFormat?: string) => {
         let hourOffset = new Date().getTimezoneOffset() / 60;

         if (hourOffset < 0) {
             return moment(time, timeFormat).add(-hourOffset, 'hours').format(outputFormat);
         } else {
             return moment(time, timeFormat).subtract(hourOffset, 'hours').format(outputFormat);
         }
     }

    return (
        <>
            <div className={style['navbar']}>
                <div className={style['notification-text']} style={{ fontSize: '24px', display:"flex", background: color }}>
                    <FontAwesomeIcon style={{ padding: '3px 3px 3px 0px' }} icon={faAngleLeft} className={style['style-icon']} onClick={() => router.back()} />
                    <div className={`ps-1`}>
                           { trans('notification') }
                    </div>
                </div>
            </div>
            <div className={ `container-fluid ${style['menu-profile']}`} style={{ marginBottom: '70px'}}>
                <div className={style['profile-info']}>
                    <InfiniteScroll
                        style={{ minHeight: '100vh'}}
                        dataLength={notifications.length}
                        next={() => fetchMoreData()}
                        hasMore={true}
                        loader={<> </>}
                    >
                        {
                            notifications.length == 0 ? (
                                <div className={`${style['empty']}`}>{trans('no-items')}</div>
                            ) : (
                                    notifications != null && notifications.map((notification, index) => (
                                    <div key={index} onClick={() => handleReadClick(notification.id)}>
                                        {
                                            index != 0 && (<hr className={style['line']}></hr>)
                                        }

                                        <div  className={`row ${style['profile-info-item']} p-0`} data-bs-toggle="modal"
                                             data-bs-target={`#detal-notification-${notification.id}`}>
                                            <div className={`col-sm-11 col-11 pe-0`}>
                                                <div className={style['title-info-item']}>
                                                    { notification.title }
                                                </div>
                                                <div className={style['description-info-item']} dangerouslySetInnerHTML={{ __html: notification.description.replace(/\n/g, "<br />") }} >
                                                </div>
                                            </div>

                                            {
                                                notification.status ||
                                                readNotifications.includes(notification.id) ? (<></>) : (
                                                    <div className={`col-sm-1 col-1 p-0`}>
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="6" height="6" viewBox="0 0 6 6" fill="none">
                                                            <circle cx="3" cy="3" r="3" fill={color}/>
                                                        </svg>
                                                    </div>
                                                )
                                            }
                                        </div>
                                        <div className="d-flex">
                                            <div
                                                className="modal"
                                                id={`detal-notification-${notification.id}`}
                                            >
                                                <div className="modal-dialog">
                                                    <div className={`modal-content ${style['modal-content-login']}`}>
                                                        <div className="modal-body pt-1" >
                                                            <div className={`mx-auto`} style={{ textAlign: 'center' }}>
                                                                <svg xmlns="http://www.w3.org/2000/svg" width="101" height="3" viewBox="0 0 101 3" fill="none">
                                                                    <path d="M2 1.5H99" stroke="#E1E1E1" strokeWidth="3" strokeLinecap="round" />
                                                                </svg>
                                                            </div>
                                                            <div className={`${style['detail-title']} pt-2`}>
                                                                { notification.title }
                                                            </div>
                                                            <div className={style['detail-datetime']}>
                                                                { formatDate(notification.sent_time, 'YYYY-MM-DD hh:mm:ss', 'DD/MM/YYYY ['+ trans('on') +'] hh:mm A') }
                                                            </div>
                                                            <div className={style['detail-description']}  dangerouslySetInnerHTML={{ __html: notification.description.replace(/\n/g, "<br />") }}>
                                                            </div>
                                                            <div className={style['btn-yes-logout']} data-type="button"
                                                                 style={{ position:'sticky', bottom: '0' }}
                                                                 data-bs-dismiss="modal"
                                                                 aria-label="Close">
                                                                { trans('close') }
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                ))
                            )
                        }

                    </InfiniteScroll>
                </div>
            </div>
        </>
    );
}