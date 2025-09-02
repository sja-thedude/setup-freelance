"use client"

import { useI18n } from '@/locales/client'
import { memo, useEffect } from 'react'
import { changeCartLimitTimeToPayment } from '@/redux/slices/cartSlice'
import { useAppDispatch } from '@/redux/hooks'
import style from 'public/assets/css/datetime-list.module.scss'

function LimitTimeToPaymentMessage() {
    const trans = useI18n()
    const dispatch = useAppDispatch()

    useEffect(() => {
        dispatch(changeCartLimitTimeToPayment(false))
    }, [])

    return (
        <>
            <div className={`col-auto`}>
                <svg xmlns="http://www.w3.org/2000/svg" width="21" height="21" viewBox="0 0 21 21" fill="none">
                    <path d="M9.00417 3.37756L1.59292 15.7501C1.44011 16.0147 1.35926 16.3147 1.35841 16.6203C1.35755 16.9258 1.43672 17.2263 1.58804 17.4918C1.73936 17.7572 1.95755 17.9785 2.22091 18.1334C2.48427 18.2884 2.78361 18.3717 3.08917 18.3751H17.9117C18.2172 18.3717 18.5166 18.2884 18.7799 18.1334C19.0433 17.9785 19.2615 17.7572 19.4128 17.4918C19.5641 17.2263 19.6433 16.9258 19.6424 16.6203C19.6416 16.3147 19.5607 16.0147 19.4079 15.7501L11.9967 3.37756C11.8407 3.1204 11.621 2.90779 11.359 2.76023C11.0969 2.61267 10.8012 2.53516 10.5004 2.53516C10.1996 2.53516 9.90396 2.61267 9.64187 2.76023C9.37978 2.90779 9.16015 3.1204 9.00417 3.37756V3.37756Z" stroke="#E03009" strokeWidth="1.5" strokeLinecap="round" strokeLinejoin="round" />
                    <path d="M10.5 7.875V11.375" stroke="#E03009" strokeWidth="1.5" strokeLinecap="round" strokeLinejoin="round" />
                    <path d="M10.5 14.875H10.5088" stroke="#E03009" strokeWidth="1.5" strokeLinecap="round" strokeLinejoin="round" />
                </svg>
            </div>
            <div className={`col ps-0 ${style['datetime-error-text']}`}>
                <p className="mb-0">
                    {trans('limit_time_to_payment')}
                </p>
            </div>
        </>
    )
}

export default memo(LimitTimeToPaymentMessage)
