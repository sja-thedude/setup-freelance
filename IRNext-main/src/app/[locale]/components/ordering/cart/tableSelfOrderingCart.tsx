'use client'

import OrderOverview from '@/app/[locale]/components/ordering/cart/tableSelfOrderingSteps/order-overview'
import { changeInCart, cartNote, selfOrderingChangeInCart, selfOrderingCartNote, markStepReversed } from '@/redux/slices/cartSlice'
import Step2 from '@/app/[locale]/components/ordering/cart/tableSelfOrderingSteps/step2';
import Step3 from '@/app/[locale]/components/ordering/cart/tableSelfOrderingSteps/step3';
import Step4 from '@/app/[locale]/components/ordering/cart/tableSelfOrderingSteps/step4';
import style from 'public/assets/css/cart.module.scss';
import React, { useEffect } from 'react'
import { addStepTable, addStepSelfOrdering } from '@/redux/slices/cartSlice'
import { useAppSelector, useAppDispatch } from '@/redux/hooks'
import { useI18n } from '@/locales/client'
import styleStep from "../../../../../../public/assets/css/datetime-list.module.scss";
import TableOverview from '@/app/[locale]/components/ordering/cart/selfOrderingSteps/table-overview'
import PaymentSelection from '@/app/[locale]/components/ordering/cart/selfOrderingSteps/payment-selection'
import TableOrderHeader from '@/app/[locale]/components/layouts/header/tableOrderHeader'
import useValidateSecurity from "@/hooks/useTableSelfOrderingSecurity";
import { useRouter } from "next/navigation";
import {useValidateToTriggerClosedScreen} from "@/hooks/useTableSelfOrderingSecurity";
import {OPENING_HOUR_TABLE_ORDERING_TYPE, OPENING_HOUR_SELF_ORDERING_TYPE, EXTRA_SETTING_TABLE_ORDERING_TYPE, EXTRA_SETTING_SELF_ORDERING_TYPE} from "@/config/constants"
import InvalidSecurity from "../../../components/404/invalid-security";

export default function TableSelfOrderingCart(props: any) {    
    const { origin, color, coupons, workspaceId, workspace } = props
    const router = useRouter();
    const tableOrderingCart = useAppSelector((state) => state.cart.data)
    const selfOrderingCart = useAppSelector((state) => state.cart.selfOrderingData)
    const stepTable = useAppSelector((state) => state.cart.stepTable)
    const step = useAppSelector((state) => state.cart.stepSelfOrdering)
    const trans = useI18n();
    const styleStepNumber: any = { color: color, borderColor: color };
    const styleStepName: any = { color: color };
    const dispatch = useAppDispatch()
    const handleActive = (stepActive: number) => {
        dispatch(addStepSelfOrdering(stepActive))
    }
    const handleStepReversed = (reversed: boolean) => {
        dispatch(markStepReversed(reversed))
    }

    const query = new URLSearchParams(window.location.search);
    const activeStep = query.get('activeStep');

    useEffect(() => {
        if (activeStep) {
            if (origin == 'table_ordering') {
                dispatch(addStepTable(parseInt(activeStep)))
            }

            if (origin == 'self_ordering') {
                dispatch(addStepSelfOrdering(activeStep))
            }
        }
    }, [activeStep]);

    const openingHourType = origin === 'table_ordering' ? OPENING_HOUR_TABLE_ORDERING_TYPE : OPENING_HOUR_SELF_ORDERING_TYPE;
    const extraSettingType = origin === 'table_ordering' ? EXTRA_SETTING_TABLE_ORDERING_TYPE : EXTRA_SETTING_SELF_ORDERING_TYPE;
    useValidateToTriggerClosedScreen(router, workspaceId, openingHourType, extraSettingType);
    
    const validateSecurity = useValidateSecurity();
    if (!validateSecurity) {
        return (<InvalidSecurity />);
    }
    
    return (
        <>
            <TableOrderHeader origin={origin} step={step} workspaceId={workspace?.id ? workspace?.id : ''} />
            <div className="cart-box" style={origin == 'desktop' ? { paddingLeft: '1px', paddingRight: '1px' } : {}}>
                {
                    origin === 'table_ordering' ? (
                        <>
                            {
                                stepTable == 1 && (
                                    <OrderOverview
                                        origin={origin}
                                        color={color}
                                        coupons={coupons}
                                        workspaceId={workspaceId}
                                        activeStep={() => { }}
                                        cart={origin === 'table_ordering' ? tableOrderingCart : selfOrderingCart}
                                        changeInCart={origin === 'table_ordering' ? changeInCart : selfOrderingChangeInCart}
                                        cartNote={origin === 'table_ordering' ? cartNote : selfOrderingCartNote}
                                    />
                                )
                            }

                            {
                                stepTable == 2 && (
                                    <Step2 color={color} activeStep={() => { }} />
                                )
                            }

                            {
                                stepTable == 3 && (
                                    <Step3 color={color} activeStep={() => { }} />
                                )
                            }

                            {
                                stepTable == 4 && (
                                    <Step4 color={color} workspaceId={workspaceId} workspace={workspace} activeStep={() => { }} />
                                )
                            }
                            {tableOrderingCart.length > 0 && (
                                <div className={`${style.messaging} row mt-2`} style={{ marginBottom: '10vh' }}>
                                    <div className="col-sm-12 col-12">
                                        <div className={style.steps}>
                                            <div className={style['step-item']}>
                                                <div className={style['step-number']} style={stepTable && stepTable >= 1 ? styleStepNumber : null} onClick={() => { stepTable > 1 ? dispatch(addStepTable(1)) : '' }}>1</div>
                                                <div className={style['step-name']} style={stepTable && stepTable >= 1 ? styleStepName : null}>{trans('cart.step_overview')}</div>
                                            </div>
                                            <div className={style['step-item']}>
                                                <div className={style['step-number']} style={stepTable && stepTable >= 2 ? styleStepNumber : null} onClick={() => { stepTable > 2 ? dispatch(addStepTable(2)) : '' }}>2</div>
                                                <div className={style['step-name']} style={stepTable && stepTable >= 2 ? styleStepName : null}>{trans('cart.step_table_number')}</div>
                                            </div>
                                            <div className={style['step-item']}>
                                                <div className={style['step-number']} style={stepTable && stepTable >= 3 ? styleStepNumber : null} onClick={() => { stepTable > 3 ? dispatch(addStepTable(3)) : '' }}>3</div>
                                                <div className={style['step-name']} style={stepTable && stepTable >= 3 ? styleStepName : null}>{trans('cart.step_confirmation')}</div>
                                            </div>
                                            <div className={style['step-item']}>
                                                <div className={style['step-number']} style={stepTable && stepTable >= 4 ? styleStepNumber : null}>4</div>
                                                <div className={style['step-name']} style={stepTable && stepTable >= 4 ? styleStepName : null}>{trans('cart.step_payment_method')}</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            )}
                        </>
                    ) : (
                        <>
                            {
                                step == 1 && (
                                    <OrderOverview
                                        origin={origin}
                                        color={color}
                                        coupons={coupons}
                                        activeStep={handleActive}
                                        workspaceId={workspaceId}
                                        cart={origin === 'table_ordering' ? tableOrderingCart : selfOrderingCart}
                                        changeInCart={origin === 'table_ordering' ? changeInCart : selfOrderingChangeInCart}
                                        cartNote={origin === 'table_ordering' ? cartNote : selfOrderingCartNote} />
                                )
                            }
                            {
                                step == 2 && (
                                    <div className={`${styleStep['step-card']}`}>
                                        <TableOverview
                                            color={color}
                                            workspaceId={workspaceId}
                                            workspace={workspace}
                                            stepReversed={handleStepReversed}
                                            activeStep={handleActive} />
                                    </div>
                                )
                            }

                            {
                                step == 3 && (
                                    <div className={`${styleStep['step-card']}`}>
                                        <PaymentSelection
                                            color={color}
                                            workspaceId={workspaceId}
                                            workspace={workspace}
                                            stepReversed={handleStepReversed}
                                            activeStep={handleActive} />
                                    </div>
                                )
                            }
                        </>
                    )
                }
            </div>
        </>
    )
}

