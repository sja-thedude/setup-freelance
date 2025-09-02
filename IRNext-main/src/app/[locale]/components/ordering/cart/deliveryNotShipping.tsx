'use client'

import React, {useEffect, useState} from 'react'
import { Modal } from 'react-bootstrap'
import { useI18n } from '@/locales/client'
import 'public/assets/css/modal.scss'

export default function DeliveryNotShipping({togglePopup, isShow, workspaceName }: {togglePopup: any, isShow: any, workspaceName: any}) {
    const trans = useI18n()
    const [show, setShow] = useState(false)
    const handleClose = () => {
        togglePopup();
        setShow(false)
    }

    useEffect(() => {
        if (isShow) {
            setShow(true)
        }
    }, [isShow]);

    return (
        <>
            <Modal className="modal-popup"
                show={show}
                onHide={handleClose}
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
                                {trans('cart.delivery_not_shipping_1', {workspaceName: <strong className="font-bold">{workspaceName}</strong>})} 
                                &nbsp;
                                {trans('cart.delivery_not_shipping_2')} 
                                &nbsp;
                                <strong className="font-bold">{trans('cart.delivery_not_shipping_3')}</strong>
                                &nbsp;
                                {trans('cart.delivery_not_shipping_4')} 
                                &nbsp;
                                <strong className="font-bold">{trans('cart.delivery_not_shipping_5')}</strong>
                            </p>
                        </div>
                    </div>
                    <div className="row mb-3">
                        <div className="col-sm-12 col-12 text-center">
                            <button onClick={() => handleClose()}
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