'use client'

import style from 'public/assets/css/datetime-list.module.scss'
import { useI18n } from '@/locales/client'
import CartDatime from '@/app/[locale]/components/ordering/cart/steps/datetime-list'
import PaymentSelection from '@/app/[locale]/components/ordering/cart/steps/payment-selection'
import React, { useEffect, useRef, useState } from "react";
import { useSelector } from "react-redux";
import { useGetCouponsQuery } from '@/redux/services/couponsApi';
import { selectCouponData } from "@/redux/slices/couponSlice";
import OrderOverview from '@/app/[locale]/components/ordering/cart/steps/order-overview'
import cartStyle from 'public/assets/css/cart.module.scss'
import { useAppSelector, useAppDispatch } from '@/redux/hooks'
import Link from 'next/link'
import _ from 'lodash'
import { manualChangeOrderTypeDesktop } from '@/redux/slices/cartSlice'
import { useGetWorkspaceDataByIdQuery } from '@/redux/services/workspace/workspaceDataApi'
import { api } from "@/utils/axios";
import Cookies from 'js-cookie';
import { usePathname } from 'next/navigation'
import useScrollPosition from '@/hooks/useScrollPosition';
import TypeNotActiveErrorMessage from '../../workspace/typeNotActiveErrorMessage'
import {changeTypeNotActiveErrorMessage} from '@/redux/slices/cartSlice'

interface Coupon {
    id: number;
    created_at: string;
    updated_at: string;
    code: string;
    promo_name: string;
    workspace_id: number;
    workspace: {
        id: number;
        name: string;
    };
    max_time_all: number;
    max_time_single: number;
    currency: string;
    discount: string;
    expire_time: string;
    discount_type: number;
    percentage: number;
}

export default function UserWebsiteCart(props: any) {
    let { navbarHeight, workspace, apiData, color, workspaceId, step, setIsDeliveryType, handleActive, origin, isExistRedeem, from } = props
    let cart = useAppSelector((state) => state.cart.rootData)
    let totalPrice = useAppSelector((state) => state.cart.rootCartTotalPrice)
    let invalidProductIds = useAppSelector((state) => state.cart.rootCartInvalidProductIds)
    let type = useAppSelector((state) => state.cart.type)
    const changeOrderTypeDesktopManual = useAppSelector((state) => state.cart.changeOrderTypeDesktop)
    const query = new URLSearchParams(window.location.search);
    const activeStep = query.get('activeStep');
    const { data: couponsData } = useGetCouponsQuery({});
    const apiSliceCoupon = useSelector(selectCouponData);
    const infoCoupons = apiSliceCoupon?.data || couponsData?.data;
    const coupons = infoCoupons?.data.filter((item: Coupon) => item?.workspace_id === apiData?.workspace_id);
    const trans = useI18n()
    const dispatch = useAppDispatch()
    const { data: apiDataToken } = useGetWorkspaceDataByIdQuery({ id: workspaceId })
    const typeNotActiveErrorMessage = useAppSelector<any>((state: any) => state.cart.typeNotActiveErrorMessage);
    const workspaceidRef = useRef(null);
    const pathName = usePathname();
    const setWorkspaceID = (newValue: any) => {
        workspaceidRef.current = newValue;
    };
    const tokenLoggedInCookie = Cookies.get('loggedToken');
    setWorkspaceID(workspaceId);
    const [getHoliday, setHoliday] = useState<any | null>(null);
    const [showGeneralAcc, setShowGeneralAcc] = useState(false);
    const language = Cookies.get('Next-Locale') ?? 'nl';

    useEffect(() => {
        const fetchData = async () => {
            try {
                const res = await api.get(`workspaces/` + workspaceidRef.current + `/settings/holiday_exceptions`, {
                    headers: {
                        'Authorization': `Bearer ${tokenLoggedInCookie}`,
                        'Content-Language': language
                    }
                });
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
            } catch (error) {
                // console.log(error)
            }
            setTimeout(function () {
                setShowGeneralAcc(true);
            }, 250);

        };

        if (workspaceidRef.current) {
            fetchData();
        }
    }, [workspaceidRef.current]);

    const generalAcc = apiDataToken?.data?.setting_preference?.holiday_text;
    useEffect(() => {
        if (activeStep) {
            handleActive(parseInt(activeStep));
        }
    }, [activeStep]);

    useEffect(() => {
        dispatch(manualChangeOrderTypeDesktop(false))
    }, []);

    useEffect(() => {
        dispatch(changeTypeNotActiveErrorMessage(false));
    }, [cart]);

    useEffect(() => {
        if (Cookies.get('currentProductId') && Cookies.get('groupOrderDesktop')) {
            if (!Cookies.get('loggedToken') && !_.isEmpty(cart)) {
                dispatch(manualChangeOrderTypeDesktop(false))
            } else {
                dispatch(manualChangeOrderTypeDesktop(true))
            }
            Cookies.remove('currentProductId');
            Cookies.remove('groupOrderDesktop');
        }
    }, [Cookies.get('currentProductId'), Cookies.get('groupOrderDesktop')]);

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

    return (
        <div className={`cart-box ${origin == 'desktop' ? 'f-right' : ''}`}
            style={origin == 'desktop' ? {
                    width: '356px',
                    position: pathName.includes('loyalties') ? 'relative' : 'absolute',
                    paddingLeft: pathName.includes('loyalties') ? '0' : '1px',
                    paddingRight: pathName.includes('loyalties') ? '0' : '1px',
                    background: '#FFF', 
                    border: !_.isEmpty(cart) || changeOrderTypeDesktopManual === true || (_.isEmpty(cart) && pathName.includes('loyalties')) ? '1px solid #D9D9D9' : '',
                } : {
                    marginTop: '74px',
                    marginBottom: '42px'
                }
            }>
            {changeOrderTypeDesktopManual === true ? (
                <>
                    <OrderOverview
                        cart={cart}
                        color={color}
                        coupons={coupons}
                        workspace={workspace}
                        invalidProductIds={invalidProductIds}
                        activeStep={handleActive}
                        origin={origin}
                        isExistRedeem={isExistRedeem}
                        setIsDeliveryType={setIsDeliveryType}
                        changeOrderTypeDesktopManual={changeOrderTypeDesktopManual}
                        from={from} />
                </>
            ) : (
                <>
                    {_.isEmpty(cart) ? (
                        <>
                            <div className="res-mobile">
                                <div className={cartStyle['empty-box-wrapper']} style={{ top: navbarHeight }}>
                                    <div className={`${cartStyle['cart-box-shadow']} ${cartStyle['empty-box']}`} style={{ top: 'calc(50% - ' + (144 + ((70 - navbarHeight) / 2)) + 'px)' }}>
                                        <p className={`${cartStyle['empty-message']} text-center`}>
                                            {trans('cart.empty_cart')}
                                        </p>
                                        <Link href="/category/products"
                                            className={`d-block width-100 ${cartStyle['gray-button']} text-uppercase mt-3`}>
                                            {trans('cart.view_range')}
                                        </Link>
                                    </div>
                                </div>
                            </div>
                            <div className={`${cartStyle.containing} res-desktop`} style={{
                                border: pathName.includes('loyalties') ? '' : '1px solid #D9D9D9',
                                position: pathName.includes('loyalties') ? 'relative' : 'absolute',
                                height: pathName.includes('loyalties') ? '' : '100vh',
                                bottom: pathName.includes('loyalties') ? '' : '0',
                                zIndex: pathName.includes('loyalties') ? '' : '999',
                                top: pathName.includes('loyalties') ? '' : '0px',
                                width: pathName.includes('loyalties') ? '' : '356px',
                                right: pathName.includes('loyalties') ? '' : '0',
                            }}>
                                {typeNotActiveErrorMessage && (
                                    <TypeNotActiveErrorMessage className="mt-0 pd-lr-10"/>
                                )}

                                {getHoliday && getHoliday.status ? (
                                    <div className={cartStyle.holiIcon}>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" style={{ position: 'absolute', left: '20px', top: '50%', transform: 'translateY(-50%)' }}>
                                            <path d="M12 22C17.5228 22 22 17.5228 22 12C22 6.47715 17.5228 2 12 2C6.47715 2 2 6.47715 2 12C2 17.5228 6.47715 22 12 22Z" stroke={color} strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" />
                                            <path d="M12 16V12" stroke={color} strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" />
                                            <path d="M12 8H12.01" stroke={color} strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" />
                                        </svg>
                                        <div className={cartStyle.holiText}>
                                            {getHoliday?.data[0].description}
                                        </div>
                                    </div>
                                ) : (
                                    showGeneralAcc && generalAcc && (
                                        <div className={cartStyle.holiIcon}>
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" style={{ position: 'absolute', left: '20px', top: '50%', transform: 'translateY(-50%)' }}>
                                                <path d="M12 22C17.5228 22 22 17.5228 22 12C22 6.47715 17.5228 2 12 2C6.47715 2 2 6.47715 2 12C2 17.5228 6.47715 22 12 22Z" stroke={color} strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" />
                                                <path d="M12 16V12" stroke={color} strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" />
                                                <path d="M12 8H12.01" stroke={color} strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" />
                                            </svg>
                                            <div className={cartStyle.holiText}>
                                                {generalAcc}
                                            </div>
                                        </div>
                                    )
                                )
                                }

                                <div className={`${cartStyle['desktop-empty-cart']}`} style={{ marginTop: (showGeneralAcc && generalAcc) || (getHoliday && getHoliday.status)  ? '1.5rem' : '40vh' }}>
                                    {trans('cart.desktop_empty_cart')}
                                </div>
                            </div>
                        </>
                    ) : (
                        <>
                            {
                                step == 1 && (
                                    <OrderOverview
                                        cart={cart}
                                        color={color}
                                        coupons={coupons}
                                        workspace={workspace}
                                        invalidProductIds={invalidProductIds}
                                        activeStep={handleActive}
                                        isExistRedeem={isExistRedeem}
                                        setIsDeliveryType={setIsDeliveryType}
                                        changeOrderTypeDesktopManual={changeOrderTypeDesktopManual}
                                        origin={origin}
                                        from={from} />
                                )
                            }

                            {
                                step == 2 && (
                                    <div className={`${style['step-card']} cart-step`}>
                                        <CartDatime
                                            color={color}
                                            type={type}
                                            totalPrice={totalPrice}
                                            workspace={workspace}
                                            activeStep={handleActive} />
                                    </div>
                                )
                            }

                            {
                                step == 3 && (
                                    <div className={`${style['step-card']} cart-step`}>
                                        <PaymentSelection
                                            color={color}
                                            workspaceId={workspaceId}
                                            workspace={workspace}
                                            type={type}
                                            activeStep={handleActive} />
                                    </div>
                                )
                            }
                        </>
                    )}
                </>
            )}
        </div>
    )
}

