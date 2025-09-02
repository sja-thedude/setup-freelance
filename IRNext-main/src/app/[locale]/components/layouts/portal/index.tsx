"use client"

import { useEffect } from 'react'
import { useI18n } from '@/locales/client'
import { useAppSelector, useAppDispatch } from '@/redux/hooks'
import { ToastContainer, toast, Slide } from 'react-toastify'
import 'react-toastify/dist/ReactToastify.css'
import { toggleAddToCartSuccess, rootToggleAddToCartSuccess } from '@/redux/slices/cartSlice'
import useMediaQuery from '@mui/material/useMediaQuery'
import CookiePopup from "@/app/[locale]/components/portal/cookie-popup";

export default function PortalLayout({children}: {children: React.ReactNode}) {
    const trans = useI18n()
    const dispatch = useAppDispatch()
    const addToCartSuccess = useAppSelector((state) => state.cart.addToCartSuccess)
    const rootAddToCartSuccess = useAppSelector((state) => state.cart.rootAddToCartSuccess)
    const isMobile = useMediaQuery('(max-width: 1279px)');
    
    useEffect(() => {
        if(addToCartSuccess == true || rootAddToCartSuccess == true) {
            if(addToCartSuccess == true) {
                dispatch(toggleAddToCartSuccess())
            } else {
                dispatch(rootToggleAddToCartSuccess())
            }
            if(isMobile) {
                toast(trans('cart.product_added_success'), {
                    position: toast.POSITION.BOTTOM_CENTER,
                    autoClose: 1000,
                    hideProgressBar: true,
                    closeOnClick: true,
                    closeButton: false,
                    transition: Slide,
                    className: 'add-to-cart-success'
                })
            }
        }
    }, [
        addToCartSuccess,
        dispatch,
        trans
    ])

    return (
        <>
            <CookiePopup />

            <main>
                {children}
                <ToastContainer />
            </main>
        </>
    )
}