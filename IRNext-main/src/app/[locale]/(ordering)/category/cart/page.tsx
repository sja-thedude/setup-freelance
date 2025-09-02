'use client'

import UserWebsiteCart from "@/app/[locale]/components/ordering/cart/userWebsiteCart"
import { faChevronLeft } from "@fortawesome/free-solid-svg-icons";
import { FontAwesomeIcon } from "@fortawesome/react-fontawesome";
import style from 'public/assets/css/datetime-list.module.scss'
import Menu from "@/app/[locale]/components/menu/menu-plus";
import { useI18n } from '@/locales/client'
import React, { useState, useRef, useEffect } from "react";
import cartStyle from 'public/assets/css/cart.module.scss'
import { useGetWorkspaceDataByIdQuery } from '@/redux/services/workspace/workspaceDataApi'
import { addStepRoot } from '@/redux/slices/cartSlice'
import { useAppSelector, useAppDispatch } from '@/redux/hooks'
import Cookies from 'js-cookie';

export default function Page() {
    const trans = useI18n()
    const step = useAppSelector((state) => state.cart.stepRoot)
    const [navbarHeight, setNavbarHeight] = useState(70)
    const navbarRef = useRef<HTMLInputElement>(null)
    const workspaceId = useAppSelector((state) => state.workspaceData.globalWorkspaceId)
    const { data: apiDataToken } = useGetWorkspaceDataByIdQuery({id: workspaceId})
    const workspace = apiDataToken?.data
    const apiData = apiDataToken?.data?.setting_generals
    const color = apiData?.primary_color
    var [isDeliveryType, setIsDeliveryType] = useState(false);
    const dispatch = useAppDispatch()
    const handleActive = (stepActive: number) => {
        dispatch(addStepRoot(stepActive))
    }

    useEffect(() => {
        if (navbarRef.current != null) {
            setNavbarHeight(navbarRef.current.clientHeight)
        }
    })

    return (
        <div className="row" >
            <div id="cart-container" className={`cart-container ${cartStyle.cart} ${cartStyle['user-website']}`}>
                <div className={`row ${style['navbar']}`} ref={navbarRef}>
                    <div className={style['nav-bar']} style={{ background: color }}>
                        <div className={`text-capitalize d-flex`}>
                            {
                                step != 1 && (
                                    <FontAwesomeIcon icon={faChevronLeft} className={`me-2 my-auto ${style['style-icon']}`} onClick={() => {dispatch(addStepRoot(step - 1)); Cookies.set('productSuggestion' , 'true')}}/>
                                )
                            }
                            <div>
                                <div>{ trans('cart.step_overview') }</div>
                                <div>{ apiDataToken ? apiDataToken?.data?.setting_generals?.title : ''}</div>
                            </div>
                        </div>
                    </div>
                </div>
                <UserWebsiteCart navbarHeight={navbarHeight}
                        workspace={workspace}
                        apiData={apiData} 
                        color={color} 
                        workspaceId={workspaceId} 
                        step={step}
                        isExistRedeem={false}
                        setIsDeliveryType={setIsDeliveryType}
                        handleActive={handleActive} />

                <div style={{ position: 'fixed', bottom: 0, left: 0, width: '100%', zIndex: 100 }}>
                    <Menu />
                </div>
            </div>
        </div>
    )
}