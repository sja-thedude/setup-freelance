"use client";

import variables from '/public/assets/css/notfound.module.scss'
import { useI18n } from '@/locales/client'
import React from "react";
import { useAppSelector } from '@/redux/hooks'

export default function NotFoundDesktop() {
    // Get coupons list
    const trans = useI18n()
    const color = useAppSelector((state) => state.workspaceData.globalWorkspaceColor)

    return (
        <>
            <div className={variables.page_not_found}>
                <div className={variables.in}>
                    <div className={variables.icon}>
                        <svg xmlns="http://www.w3.org/2000/svg" width="208" height="209" viewBox="0 0 208 209" fill="none">
                            <path d="M89.1799 33.9532L15.7733 156.5C14.2598 159.121 13.459 162.092 13.4505 165.119C13.442 168.146 14.2262 171.122 15.725 173.751C17.2237 176.38 19.3849 178.572 21.9934 180.107C24.6019 181.641 27.5669 182.467 30.5933 182.5H177.407C180.433 182.467 183.398 181.641 186.006 180.107C188.615 178.572 190.776 176.38 192.275 173.751C193.774 171.122 194.558 168.146 194.549 165.119C194.541 162.092 193.74 159.121 192.227 156.5L118.82 33.9532C117.275 31.4061 115.1 29.3002 112.504 27.8387C109.908 26.3772 106.979 25.6094 104 25.6094C101.021 25.6094 98.0921 26.3772 95.4962 27.8387C92.9004 29.3002 90.725 31.4061 89.1799 33.9532V33.9532Z" stroke={color} strokeWidth="9" strokeLinecap="round" strokeLinejoin="round" />
                            <path d="M104 78.5V113.167" stroke={color} strokeWidth="9" strokeLinecap="round" strokeLinejoin="round" />
                            <path d="M104 147.834H104.087" stroke={color} strokeWidth="9" strokeLinecap="round" strokeLinejoin="round" />
                        </svg>
                    </div>
                    <div className={variables.info}>
                        <h1>{trans('invalid-desktop-title')}</h1>
                        <p>{trans('invalid-table-desktop-description')}</p>
                    </div>
                </div>
            </div>
        </>
    )
}
