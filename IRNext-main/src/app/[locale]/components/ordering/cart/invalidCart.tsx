'use client'

import React, { useState } from 'react'
import { Modal } from 'react-bootstrap'
import { useI18n } from '@/locales/client'
import { useAppDispatch } from '@/redux/hooks'
import { resetRootCart } from '@/redux/slices/cartSlice'
import 'public/assets/css/modal.scss'

export default function InvalidCart() {
    const trans = useI18n()
    const dispatch = useAppDispatch()
    const [show] = useState(true)

    const resetCart = () => {
        dispatch(resetRootCart())
    }

    return (
        <>
            <Modal className="modal-popup"
                show={show}
                aria-labelledby="contained-modal-title-vcenter"
                centered>
                <div className={`mx-auto text-center`}>
                    <svg xmlns="http://www.w3.org/2000/svg" width="101" height="3" viewBox="0 0 101 3" fill="none">
                        <path d="M2 1.5H99" stroke="#E1E1E1" strokeWidth="3" strokeLinecap="round" />
                    </svg>
                </div>
                <Modal.Body className="types-body">
                    <div className="row mb-2">
                        <div className="col-sm-12 col-12">
                            <h3 className="types-title text-center">
                                {trans('cart.oops')}...
                            </h3>
                        </div>
                    </div>
                    <div className="row mb-2">
                        <div className="col-sm-12 col-12">
                            <p className="modal-normal-text text-center">
                                {trans('cart.product_impossible_contact_admin')}
                            </p>
                        </div>
                    </div>
                    <div className="row mb-3">
                        <div className="col-sm-12 col-12 text-center">
                            <button onClick={() => resetCart()}
                                className="itr-btn-primary min-w-168" type="button">
                                {trans('cart.back')}
                            </button>
                        </div>
                    </div>                
                </Modal.Body>
            </Modal>
        </>
    )
}