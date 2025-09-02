"use client"

import variables from '/public/assets/css/step.module.scss'
import { useI18n } from '@/locales/client'
import {Formik, Field, Form,} from 'formik';
import React from "react";
import style from "../../../../../../../public/assets/css/datetime-list.module.scss";
import * as Yup from "yup";
import { addInfoSelfOrder } from '@/redux/slices/cartSlice'
import { useAppSelector, useAppDispatch } from '@/redux/hooks'
import { api } from "@/utils/axios";
import moment from 'moment';
import _, { set } from 'lodash'

const TableOverview = (props: any) => {
    const trans = useI18n();
    const color = props.color
    const selfOrderingCart = useAppSelector((state) => state.cart.selfOrderingData)

    //default value cash
    const sendActiveStep = props.activeStep
    const markStepReversed = props.stepReversed
    const invalid = variables['invalid'];
    const dispatch = useAppDispatch()
    let cartInfoSelfOrder: any = useAppSelector((state) => state.cart.dataInfoSelfOrder);

    const DATE_FORMAT = 'YYYY-MM-DD';
    const TIME_FORMAT = 'HH:mm';
    const validationSchema = Yup.object().shape({
        full_name: Yup.string().required(trans('required')).matches(/^((?!@).)*$/, trans('lang_phone_valid_message')),
        email: Yup.string().email(trans('job.message_invalid_email')),
    });

    return(
        <>
            <div className={variables.table_ordering_step2}>
                <h2>{trans('first-name')} & {trans('last-name')}</h2>

                <Formik
                    initialValues={{
                        full_name: cartInfoSelfOrder?.full_name ? cartInfoSelfOrder?.full_name : '',
                        email: cartInfoSelfOrder?.email ? cartInfoSelfOrder?.email : '',
                    }}
                    validationSchema={validationSchema}
                    validateOnChange={false}
                    validateOnBlur={false}
                    enableReinitialize={true}
                    onSubmit={async (values) => {
                        // Validate available timeslot to go to step 3
                        const res = await api.get(`products/validate_available_timeslot?from=mobile&date=${moment().format(DATE_FORMAT)}&time=${moment().format(TIME_FORMAT)}&${selfOrderingCart.map((cartItem: any) => `product_id[]=${cartItem.productId}`).join('&')}`);
                        const available = res.data?.data || []
                        if (_.includes(available, false)) {
                            sendActiveStep(1)
                            markStepReversed(true)
                        } else {
                            dispatch(addInfoSelfOrder({
                                full_name: values?.full_name,
                                email: values?.email,
                            }))
                            sendActiveStep(3);
                        }
                    }}
                >
                    {({handleChange, handleBlur, values, errors} : {handleChange: any, handleBlur: any, values: any, errors: any}) => (
                        <Form>
                            <div className={variables.error_message}>{errors.full_name ?? ''}</div>
                            <Field id="step_number"
                                   className={`${errors.full_name ? invalid : ''}`}
                                   onChange={(e: any) => {
                                       errors.full_name ? errors.full_name = false : '';
                                       handleChange(e);
                                   }}
                                   onBlur={handleBlur}
                                   variant="outlined"
                                   autoComplete="off"
                                   value={values.full_name}
                                   style={{color: color, marginBottom: "23px"}}
                                   name="full_name"
                                   placeholder="Dirk Peeters" />

                            <div className={variables.error_message}>{errors.email ?? ''}</div>
                            <Field
                                id="step_email"
                                name="email"
                                variant="outlined"
                                className={`${errors.email ? invalid : ''} mt-0`}
                                onChange={(e: any) => {
                                    errors.email ? errors.email = false : '';
                                    handleChange(e);
                                }}
                                onBlur={handleBlur}
                                value={values.email}
                                style={{color: "#404040"}}
                                placeholder={trans('step2_email')}
                            />

                            <p>{trans('step2_text2')}</p>

                            <button disabled={errors.email || errors.full_name ? true : false}
                                    className={errors.email || errors.full_name || !values.full_name
                                        ? variables.btn_disable
                                        : variables['active-btn']}
                                    type="submit">{trans('step2_button')}
                            </button>
                        </Form>
                    )}
                </Formik>
            </div>
            <div className="row mt-4">
                <div className="col-sm-12 col-12">
                    <div className={style.steps}>
                        <div className={style['step-item']} onClick={() => sendActiveStep(1)}>
                            <div className={style['step-number']} style={window.innerWidth < 1280 ? { color: color, borderColor: color } : {background: color}}>1</div>
                            <div className={style['step-name']} style={{ color: color }}>{trans('cart.step_overview')}</div>
                        </div>
                        <div className={style['step-item']} onClick={() => sendActiveStep(2)}>
                            <div className={style['step-number']} style={window.innerWidth < 1280 ? { color: color, borderColor: color } : {background: color}}>2</div>
                            <div className={style['step-name']} style={{ color: color }}>{trans('cart.facts')}</div>
                        </div>
                        <div className={style['step-item']}>
                            <div className={style['step-number']}>3</div>
                            <div className={style['step-name']}>{trans('cart.step_payment_method')}</div>
                        </div>
                    </div>
                </div>
            </div>
            <style>{`
                .active {
                    color: #FFFFFF;
                    background: ${color}!important;
                  }`}
            </style>
        </>
    )
}

export default TableOverview;
