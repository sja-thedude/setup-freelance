'use client'

import style from 'public/assets/css/datetime-list.module.scss'
import { useI18n } from '@/locales/client'
import PaymentSelection from '@/app/[locale]/components/ordering/cart/selfOrderingSteps/payment-selection'
import React, {useEffect} from "react";
import { useSelector } from "react-redux";
import { useGetCouponsQuery } from '@/redux/services/couponsApi';
import { selectCouponData } from "@/redux/slices/couponSlice";
import OrderOverview from '@/app/[locale]/components/ordering/cart/steps/order-overview'
import TableOverview from '@/app/[locale]/components/ordering/cart/selfOrderingSteps/table-overview'
import { useAppSelector, useAppDispatch } from '@/redux/hooks'
import _ from 'lodash'
import { manualChangeOrderTypeDesktop } from '@/redux/slices/cartSlice'

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

export default function SelfOrderingCart(props: any) {
    let { navbarHeight, workspace, apiData, color, workspaceId, step, setIsDeliveryType, handleActive, origin, isExistRedeem } = props
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

    useEffect(() => {
        if (activeStep) {
            handleActive(parseInt(activeStep));
        }
    }, [activeStep]);

    useEffect(() => {
        dispatch(manualChangeOrderTypeDesktop(false))
    }, []);

    return (
        <div className="cart-box" style={origin== 'desktop' ? {paddingLeft:'1px' , paddingRight:'1px'} : {}}>
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
                        origin={origin} />
                )
            }

            {
                step == 2 && (
                    <div className={`${style['step-card']}`}>
                        <TableOverview
                            color={color}
                            workspaceId={workspaceId}
                            workspace={workspace}
                            type={type}
                            activeStep={handleActive}/>
                    </div>
                )
            }

            {
                step == 3 && (
                    <div className={`${style['step-card']}`}>
                        <PaymentSelection
                            color={color}
                            workspaceId={workspaceId}
                            workspace={workspace}
                            type={type}
                            activeStep={handleActive} />
                    </div>
                )
            }
        </div>
    )
}

