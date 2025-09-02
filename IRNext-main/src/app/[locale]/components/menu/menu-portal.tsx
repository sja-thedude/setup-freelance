"use client"

import style from "public/assets/css/portal.module.scss";
import { useI18n } from '@/locales/client'
import {usePathname, useRouter} from 'next/navigation'
import _ from 'lodash'
import React from "react";

const text = style['text-menu'];

export default function Menu() {
    const color = '#B5B268';
    const routerPath = usePathname()
    const router = useRouter()
    const trans = useI18n()
    let activeMenu = 'home'

    if (_.includes(routerPath, '/search')) {
        activeMenu = 'search'
    } else if (_.includes(routerPath, '/profile')) {
        activeMenu = 'account'
    }

    const handleHome = () => {
        router.push(`/`);
    }

    const handleSearch = () => {
        router.push(`/search`);
    }

    const handleAccount = () => {
       router.push(`/profile/show`);
    }

    return (
        <>
            <div className={style['menu-portal']}>
                <div className={style['subMenu']}>
                    <div style={{ textDecoration: 'none' }}>
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" onClick= {handleHome}>
                            <path d="M4 7.65L12.5 1L21 7.65V18.1C21 18.6039 20.801 19.0872 20.4468 19.4435C20.0925 19.7998 19.6121 20 19.1111 20H5.88889C5.38792 20 4.90748 19.7998 4.55324 19.4435C4.19901 19.0872 4 18.6039 4 18.1V7.65Z" stroke={activeMenu == 'home' ? color : '#413E38'} strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" />
                            <path d="M10 20V10H15V20" stroke={activeMenu == 'home' ? color : '#413E38'} strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"
                            />
                        </svg>
                        <p className={`${text}`} style={{ color: (activeMenu == 'home') ? color : '#413E38' }}>{trans('home')}</p>
                    </div>
                </div>
                <div className={style['subMenu']}>
                    <div style={{ textDecoration: 'none' }}>
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" onClick= {handleSearch}>
                            <path d="M10.7885 19C15.1218 19 18.6347 15.4183 18.6347 11C18.6347 6.58172 15.1218 3 10.7885 3C6.45523 3 2.94238 6.58172 2.94238 11C2.94238 15.4183 6.45523 19 10.7885 19Z" stroke={activeMenu == 'search' ? color : '#413E38'} strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"/>
                            <path d="M20.5962 20.9999L16.3298 16.6499" stroke={activeMenu == 'search' ? color : '#413E38'} strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"/>
                        </svg>
                        <p className={`${text} mt-1`} style={{ color: (activeMenu == 'search') ? color : '#413E38' }}>{trans('portal.search')}</p>
                    </div>
                </div>
                <div className={style['subMenu']}>
                    <div style={{ textDecoration: 'none', textAlign: 'center' }} onClick= {handleAccount}>
                        <svg width="18" height="20" viewBox="0 0 18 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M17 19V17C17 15.9391 16.5786 14.9217 15.8284 14.1716C15.0783 13.4214 14.0609 13 13 13H5C3.93913 13 2.92172 13.4214 2.17157 14.1716C1.42143 14.9217 1 15.9391 1 17V19" stroke={activeMenu == 'account' ? color : '#413E38'} strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" />
                            <path d="M9 9C11.2091 9 13 7.20914 13 5C13 2.79086 11.2091 1 9 1C6.79086 1 5 2.79086 5 5C5 7.20914 6.79086 9 9 9Z" stroke={activeMenu == 'account' ? color : '#413E38'} strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" />
                        </svg>

                        <p className={`${text} mt-1`} style={{ color: (activeMenu == 'account') ? color : '#413E38' }}>{trans('account')}</p>
                    </div>
                </div>
            </div>
        </>
    )
}
