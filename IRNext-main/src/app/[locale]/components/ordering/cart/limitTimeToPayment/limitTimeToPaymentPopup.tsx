"use client"

import { useI18n } from '@/locales/client'
import { memo } from 'react'
import { Modal } from 'react-bootstrap'
import 'public/assets/css/modal.scss'
import { changeCartLimitTimeToPayment } from '@/redux/slices/cartSlice'
import { useAppDispatch, useAppSelector } from '@/redux/hooks'
import style from 'public/assets/css/datetime-list.module.scss'

function LimitTimeToPaymentPopup() {
    const trans = useI18n()
    const dispatch = useAppDispatch()
    const cartLimitTimeToPayment = useAppSelector<any>((state) => state.cart.cartLimitTimeToPayment);
    const handleClose = () => {
        dispatch(changeCartLimitTimeToPayment(false))
    }

    return (
        <>
            <Modal className="modal-popup res-mobile"
                size="lg"
                show={cartLimitTimeToPayment}
                onHide={handleClose}
                aria-labelledby="contained-modal-title-vcenter"
                centered>
                <Modal.Body>
                    <div className={`mx-auto text-center`}>
                        <svg xmlns="http://www.w3.org/2000/svg" width="101" height="3" viewBox="0 0 101 3" fill="none">
                            <path d="M2 1.5H99" stroke="#E1E1E1" strokeWidth="3" strokeLinecap="round" />
                        </svg>
                    </div>
                    <div className={style['modal-content-title']}>
                        {trans('opps')}
                    </div>
                    <div className={`${style['modal-content-text']} ps-4 pe-4`}>
                        {trans('limit_time_to_payment')}
                    </div>
                    <div onClick={() => handleClose()} className={style['btn-yes']} data-bs-dismiss="modal"  aria-label="Close">
                        {trans('back')}
                    </div>
                </Modal.Body>
            </Modal>
        </>
    )
}

export default memo(LimitTimeToPaymentPopup)
