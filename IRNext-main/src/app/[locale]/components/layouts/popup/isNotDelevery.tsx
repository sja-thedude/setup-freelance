'use client'
import { useEffect, useState } from 'react';
import { Button, Modal } from 'react-bootstrap';
import 'public/assets/css/popup.scss';
import { useI18n } from '@/locales/client'
import Cookies from 'js-cookie';
import { ToastContainer, toast, Slide } from 'react-toastify';
import 'react-toastify/dist/ReactToastify.css';
import { useAppSelector, useAppDispatch } from '@/redux/hooks'
import { changeType, changeTypeFlag, rootAddToCart, rootToggleAddToCartSuccess, changeRootCartItemTmp, addCloseDetail } from '@/redux/slices/cartSlice'
import { useRouter } from 'next/navigation'

export default function IsNotDelevery({ toggleIsNotDelivery, storeId, rootCartItemTmp, errorType }: { toggleIsNotDelivery: any, storeId: any, rootCartItemTmp: any, errorType: any }) {
    const [show, setShow] = useState(false);
    const trans = useI18n()
    const language = Cookies.get('Next-Locale');
    const dispatch = useAppDispatch()
    const router = useRouter()
    const handleAddToCart = () => {
        dispatch(changeType(1))
        if (rootCartItemTmp != null) {
            dispatch(rootAddToCart(rootCartItemTmp))
            dispatch(rootToggleAddToCartSuccess())
            dispatch(changeRootCartItemTmp(null))
        }
        if (storeId > 0) {
            setShow(false);
            dispatch(changeTypeFlag(false))
            dispatch(addCloseDetail(true))
            dispatch(changeType(3))
        }
    }
    const handleClose = () => {
        toggleIsNotDelivery(); // Thêm console.log ở đây
        setShow(false);
    };

    const handleShow = () => setShow(true);

    useEffect(() => {
        // const hasShownPopup = localStorage.getItem('hasShownPopup');
        const hasShownPopup = false;

        if (!hasShownPopup) {
            setShow(true);
        }
    }, []);

    return (
        <>
            <Modal show={show} onHide={handleClose}
                aria-labelledby="contained-modal-title-vcenter"
                centered id='delivery-check'
            >
                <div className={`mx-auto`} style={{ alignItems: 'center' }}>
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
                                {(() => {
                                    switch (errorType) {
                                        case 1:
                                            return trans('cart.delivery_product_not_shipping');
                                        case 2:
                                            return trans('cart.delivery_product_not_sale_pop');
                                        default:
                                            return '';
                                    }
                                })()}
                            </p>
                        </div>
                    </div>
                    <div className="row mb-3">
                        <div className="col-sm-12 col-12 text-center">
                            <button onClick={() => handleAddToCart()}
                                className="itr-btn-primary min-w-168" type="button">
                                {trans('cart.back')}
                            </button>
                        </div>
                    </div>
                </Modal.Body>
            </Modal>
            <ToastContainer />
        </>
    );
}
