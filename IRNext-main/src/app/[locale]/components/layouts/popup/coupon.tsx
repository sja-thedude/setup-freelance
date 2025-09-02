'use client'
import { useEffect, useState } from 'react';
import { Button, Modal } from 'react-bootstrap';
import 'public/assets/css/popup.scss';
import { useI18n } from '@/locales/client'
import { currency } from '@/config/currency'
import { useRouter, usePathname } from 'next/navigation'
import {addCouponToCart, addCouponToCartTable, addCouponToCartSelf} from '@/redux/slices/cartSlice'
import { api } from "@/utils/axios";
import { useAppSelector, useAppDispatch } from '@/redux/hooks'
import Cookies from "js-cookie";
import { ToastContainer, toast, Slide } from 'react-toastify';
import 'react-toastify/dist/ReactToastify.css';
import useMediaQuery from '@mui/material/useMediaQuery'

export default function CouponPopup({ key, color, coupon, toggleCouponPopup }: { key: any, color: any, coupon: any, toggleCouponPopup: any }) {
    const [show, setShow] = useState(false);
    const handleClose = () => {
        setTimeout(() => {
            toggleCouponPopup(key);
        }, 1000);
        setShow(false);
    };
    const isMobile = useMediaQuery('(max-width: 1279px)');
    const handleShow = () => setShow(true);
    const pathName = usePathname()
    const [validCoupon, setValidCoupon] = useState(false);
    const [validCouponTable, setValidCouponTable] = useState(false);
    const [validCouponSelf, setValidCouponSelf] = useState(false);

    useEffect(() => {
        const hasShownPopup = false;

        if (!hasShownPopup) {
            setShow(true);
            localStorage.setItem('hasShownPopup', 'true');
        }
    }, []);
    let cart = useAppSelector((state) => state.cart.rootData)
    let cartTable = useAppSelector((state) => state.cart.data)
    let cartSelf = useAppSelector((state) => state.cart.selfOrderingData)

    const tokenLoggedInCookie = Cookies.get('loggedToken');
    const language = Cookies.get('Next-Locale');
    const trans = useI18n()
    const router = useRouter()
    const dispatch = useAppDispatch()
    const handleAddCoupon = () => {
        if (pathName.includes('table-ordering')) {
            validateCouponProduct(coupon, cartTable);
        } else if (pathName.includes('self-ordering')) {
            validateCouponProduct(coupon, cartSelf);
        } else {
            validateCouponProduct(coupon, cart);
        }
    }

    useEffect(() => {
        if (validCoupon) {
            dispatch(addCouponToCart(coupon));
            router.push("/category/cart")
        }
    }, [validCoupon])

    useEffect(() => {
        if (validCouponTable) {
            dispatch(addCouponToCartTable(coupon));
            router.push("/table-ordering/cart")
            if (pathName.includes('/table-ordering/cart')) {
                window.location.href = '/table-ordering/cart';
            } else {
                router.push("/table-ordering/cart")
            }
        }
    }, [validCouponTable])

    useEffect(() => {
        if (validCouponSelf) {
            dispatch(addCouponToCartSelf(coupon));
            if (pathName.includes('/self-ordering/cart')) {
                window.location.href = '/self-ordering/cart';
            } else {
                router.push("/self-ordering/cart")
            }

        }
    }, [validCouponSelf])

    const validateCouponProduct = (coupon: any, cart: any) => {
        const productIds = cart.map((item: any) => item.productId);
        const couponValue = coupon.code;

        if (productIds.length > 0) {
            // validate product with coupon
            const productIdParams = productIds.map((id: any) => `product_id[]=${id}`).join('&');
            const queryParams = `?${productIdParams}&code=${couponValue}`;

            // Gọi API để kiểm tra coupon
            api.get(`/products/validate_coupon${queryParams}`, {
                headers: {
                    'Authorization': `Bearer ${tokenLoggedInCookie}`,
                    'Content-Language': language,
                }
            })
                .then(product => {
                    if (product?.status == 200 && product?.data?.success == true) {
                        let validCouponProduct = false;
                        if (typeof product?.data?.data !== 'undefined') {
                            // get product that available with coupon
                            for (let pId in product?.data?.data) {
                                if (product?.data?.data[pId] == true) {
                                    validCouponProduct = true;
                                    break;
                                }
                            }
                        }
                        if (!validCouponProduct) {
                            toast.dismiss();
                            if (isMobile) {
                                toast(trans('cart.message_invalid_coupon_product'), {
                                    position: toast.POSITION.BOTTOM_CENTER,
                                    autoClose: 1500,
                                    hideProgressBar: true,
                                    closeOnClick: true,
                                    closeButton: false,
                                    transition: Slide,
                                    className: 'message'
                                });
                                if (pathName.includes('table-ordering')) {
                                    setValidCouponTable(false)
                                } else if (pathName.includes('self-ordering')) {
                                    setValidCouponSelf(false)
                                } else {
                                    setValidCoupon(false)
                                }
                                handleClose()
                            }
                        } else {
                            if(isMobile){
                                toast(trans('cart.message_apply_coupon_successfully'), {
                                    position: toast.POSITION.BOTTOM_CENTER,
                                    autoClose: 1500,
                                    hideProgressBar: true,
                                    closeOnClick: true,
                                    closeButton: false,
                                    transition: Slide,
                                    className: 'message'
                                });
                            }
                            if (pathName.includes('table-ordering')) {
                                setValidCouponTable(true)
                            } else if (pathName.includes('self-ordering')) {
                                setValidCouponSelf(true)
                            } else {
                                setValidCoupon(true)
                            }
                            handleClose()
                        }
                    } else {
                        // console.log(15454)
                    }
                })
                .catch(err => {
                    console.log(err);
                    // Xử lý khi có lỗi xảy ra
                });
        } else {
            if(isMobile){
                toast(trans('cart.message_apply_coupon_failed'), {
                    position: toast.POSITION.BOTTOM_CENTER,
                    autoClose: 1500,
                    hideProgressBar: true,
                    closeOnClick: true,
                    closeButton: false,
                    transition: Slide,
                    className: 'message'
                });
            }
            handleClose()
        }
    }

    return (
        <>
            <Button variant="primary" onClick={handleShow} style={{ display: 'none' }}></Button>

            <Modal show={show} onHide={handleClose}
                aria-labelledby="contained-modal-title-vcenter"
                centered id='coupon'
            >
                <div className={`mx-auto`} style={{ alignItems: 'center' }}>
                    <svg xmlns="http://www.w3.org/2000/svg" width="101" height="3" viewBox="0 0 101 3" fill="none">
                        <path d="M2 1.5H99" stroke="#E1E1E1" strokeWidth="3" strokeLinecap="round" />
                    </svg>
                </div>
                <Modal.Header>
                    <h1 className="text-center">{trans('lang_coupon_code')} : <span className="coupon-code text-uppercase" style={{ color: color ? color : 'black' }}>{coupon ? coupon.code : ''}</span></h1>
                </Modal.Header>
                <Modal.Body>
                    <div className='row d-flex justify-content-center des-contain' >
                        <p className='description'>{coupon ? coupon.promo_name : ''}</p>
                    </div>
                    <div className='row d-flex justify-content-center tail-contain'>
                        <p className='detail'>
                            {trans('coupon-1')}{' '}
                            {coupon?.discount_type == 1 ? (
                                <>
                                    {currency} {coupon ? coupon.discount : ''}
                                </>
                            ) : (
                                `${Math.abs(coupon.percentage).toFixed(2)}%`
                            )}{' '}
                            {trans('coupon-2')}
                        </p>
                    </div>
                    <div className={`go-shop mx-auto mt-2`}>
                        <button
                            type="button"
                            className="btn btn-dark border-0 go-shop-button"
                            onClick={handleAddCoupon}
                        >
                            {trans('go-shop')}
                        </button>
                    </div>
                </Modal.Body>
            </Modal>
            <div className="res-mobile">
                <ToastContainer />
            </div>
        </>
    );
}
