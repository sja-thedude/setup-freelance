'use client'

import React, { useState, memo, useMemo, useEffect } from 'react'
import { Modal } from 'react-bootstrap'
import 'public/assets/css/modal.scss'
import { useI18n } from '@/locales/client'
import { useRouter } from 'next/navigation'
import { useAppSelector, useAppDispatch } from '@/redux/hooks'
import { changeType, changeTypeFlag, rootAddToCart, rootToggleAddToCartSuccess, changeRootInvalidProductIds, changeRootCartItemTmp, addCouponToCart, addGroupOrderSelectedNow } from '@/redux/slices/cartSlice'
import GroupOrder from '../layouts/popup/groupOrder'
import DeliveryLocation from "../layouts/popup/deliveryLocation";
import axios from "axios";
import * as config from "@/config/constants";
import Cookies from 'js-cookie';
import DeliveryProductNotShipping from "@/app/[locale]/components/ordering/cart/deliveryProductNotShipping"
import useMediaQuery from '@mui/material/useMediaQuery'

function TypesPopup(props: any) {
    const { workspace, from, isDeliveryOrderOpenManual, setIsDeliveryOrderOpenManual, handleCloseMobilePopup } = props
    const trans = useI18n()
    const router = useRouter()
    const dispatch = useAppDispatch()
    const rootType = useAppSelector((state) => state.cart.type)
    const rootTypeFlag = useAppSelector((state) => state.cart.typeFlag)
    const rootCartItemTmp = useAppSelector((state) => state.cart.rootCartItemTmp)
    const [show, setShow] = useState(false)
    const takeoutOn = useMemo(() => workspace?.setting_open_hours?.find((item: any) => item.type === 0)?.active, [workspace])
    const deliveryOn = useMemo(() => workspace?.setting_open_hours?.find((item: any) => item.type === 1)?.active, [workspace])
    const groupOrderOn = useMemo(() => workspace?.extras?.find((item: any) => item.type === 1)?.active, [workspace])
    const tokenLoggedInCookie = Cookies.get('loggedToken');
    const isMobile = useMediaQuery('(max-width: 1279px)');
    const [isDeliveryOrderOpen, setIsDeliveryOrderOpen] = useState(false);

    useEffect(() => {
        if ((!groupOrderOn && (!takeoutOn || !deliveryOn)) || !rootTypeFlag) {
            setShow(false)
            setIsGroupOrderOpen(false)

            // trigger takeout
            if (!groupOrderOn && takeoutOn && !deliveryOn && rootTypeFlag === true) {
                dispatch(changeType(1))
                dispatch(changeTypeFlag(false))

                if (from !== 'in_cart' && rootCartItemTmp != null) {
                    dispatch(rootAddToCart(rootCartItemTmp))
                    dispatch(rootToggleAddToCartSuccess())
                    dispatch(changeRootCartItemTmp(null))

                    if(handleCloseMobilePopup) {
                        handleCloseMobilePopup()
                    }                    
                }
            }

            // trigger delivery
            if (!groupOrderOn && !takeoutOn && deliveryOn && rootTypeFlag === true) {
                setIsDeliveryOrderOpen(!isDeliveryOrderOpen);
            }
        } else {
            if (rootType === 0 || (rootTypeFlag === true && from === 'in_cart')) {
                setShow(true)
            } else {
                setShow(false)
            }
        }
    }, [takeoutOn, groupOrderOn, deliveryOn, rootTypeFlag, dispatch, from, rootCartItemTmp, handleCloseMobilePopup, isDeliveryOrderOpen, rootType])

    const [isGroupOrderOpen, setIsGroupOrderOpen] = useState(false);
    const initialRef: any = null;
    const [currentLocation, setCurrentLocation] = useState(initialRef);
    const [currentAddress, setCurrentAddress] = useState({
        address: 'Limburgplein 1, 3500 Hasselt',
        lat: '50.92758786546253',
        lng: '5.338539271587612'
    });
    const [showDeliveryProductNotShipping, setShowDeliveryProductNotShipping] = useState(false);

    const handleClose = () => {
        setShow(false)
        setIsGroupOrderOpen(false)
        dispatch(changeTypeFlag(false))
        Cookies.remove('groupOrder')
        Cookies.remove('currentProductId');
    }

    const toggleClick = () => {
        if (!tokenLoggedInCookie) {
            if (isMobile) {
                router.push('/user/login?group-order=true');
            }
        } else {
            setIsGroupOrderOpen(!isGroupOrderOpen);
        }
    }

    useEffect(() => {
        if (Cookies.get('groupOrder') == 'true') {
            toggleClick()
        }
    }, [Cookies.get('groupOrder')])

    useEffect(() => {
        if (!currentAddress) {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                    (position) => {
                        const { latitude, longitude } = position.coords;
                        setCurrentLocation({ lat: latitude, lng: longitude });
                    },
                    (error) => {
                        // console.log(error);
                    }
                );
                if (!currentAddress) {
                    getAddressFromCoordinates(currentLocation?.lat, currentLocation?.lng, 1);
                }
            } else {
                // console.log("Geolocation is not supported by this browser.");
            }
        }
    }, [currentLocation]);

    useEffect(() => {
        if (isDeliveryOrderOpenManual == 1) {
            setIsDeliveryOrderOpenManual(0)
            setIsDeliveryOrderOpen(true);
        }
    })

    // get address from coordinates
    const getAddressFromCoordinates = async (latitude: any, longitude: any, type: any) => {
        axios.get(`https://maps.googleapis.com/maps/api/geocode/json?latlng=${latitude},${longitude}&language=${config.LANGUAGE_CODE}&key=${config.PUBLIC_GOOGLE_MAPS_API_KEY_DISTANCE}`, {})
            .then((res) => {
                const json = res.data;
                if (json.results) {
                    if (type == 1) {
                        setCurrentAddress({
                            address: json?.results[0]?.formatted_address,
                            lat: latitude,
                            lng: longitude
                        })
                    }
                    return json?.results[0]?.formatted_address;

                }
            }).catch(err => {
                // console.log(err)
                return null;
            });
    };

    const handleClickDelivery = () => {
        setIsDeliveryOrderOpen(!isDeliveryOrderOpen);
    }
    const query = new URLSearchParams(window.location.search);

    const onClickChangeType = (type: number) => {
        if (type == 2
            && from !== 'in_cart'
            && rootCartItemTmp != null
            && rootCartItemTmp?.product?.data?.category?.available_delivery == false) {
            setShowDeliveryProductNotShipping(true)
        } else {
            dispatch(changeType(type))
            dispatch(changeTypeFlag(false))
            dispatch(addGroupOrderSelectedNow(null))
            if (from !== 'in_cart' && rootCartItemTmp != null) {
                dispatch(rootAddToCart(rootCartItemTmp))
                dispatch(rootToggleAddToCartSuccess())
                dispatch(changeRootCartItemTmp(null))
                
                if(handleCloseMobilePopup) {
                    handleCloseMobilePopup()
                }
            }
        }

        if (query.get('groupOrder')) {
            router.push('/category/cart');
        }

        dispatch(changeRootInvalidProductIds(null));
    }

    const allowAddProductNotShipping = () => {
        setShowDeliveryProductNotShipping(false)
        dispatch(changeType(2))
        dispatch(changeTypeFlag(false))

        if (from !== 'in_cart' && rootCartItemTmp != null) {
            dispatch(rootAddToCart(rootCartItemTmp))
            dispatch(rootToggleAddToCartSuccess())
            dispatch(changeRootCartItemTmp(null))

            if(handleCloseMobilePopup) {
                handleCloseMobilePopup()
            }
        }
    }

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
                    <div className="row mb-3">
                        <div className="col-sm-12 col-12">
                            <h3 className="types-title">
                                {trans('types.choose_your_order_type')}
                            </h3>
                        </div>
                    </div>
                    {takeoutOn &&
                        <div className="row mb-4" onClick={() => onClickChangeType(1)}>
                            <div className="col-sm-12 col-12 d-flex">
                                <svg xmlns="http://www.w3.org/2000/svg" width="26" height="25" viewBox="0 0 26 25" fill="none">
                                    <path d="M6.5 2.08325L3.25 6.24992V20.8333C3.25 21.3858 3.47827 21.9157 3.8846 22.3064C4.29093 22.6971 4.84203 22.9166 5.41667 22.9166H20.5833C21.158 22.9166 21.7091 22.6971 22.1154 22.3064C22.5217 21.9157 22.75 21.3858 22.75 20.8333V6.24992L19.5 2.08325H6.5Z" stroke="#413E38" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" />
                                    <path d="M3.25 6.25H22.75" stroke="#413E38" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" />
                                    <path d="M17.3335 10.4167C17.3335 11.5218 16.877 12.5816 16.0643 13.363C15.2517 14.1444 14.1495 14.5834 13.0002 14.5834C11.8509 14.5834 10.7487 14.1444 9.93607 13.363C9.12342 12.5816 8.66687 11.5218 8.66687 10.4167" stroke="#413E38" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" />
                                </svg>
                                <div className="types-normal text-uppercase">
                                    {trans('types.pickup')}
                                </div>
                            </div>
                        </div>
                    }
                    {deliveryOn &&
                        <div className="row mb-4" onClick={() => handleClickDelivery()}>
                            <div className="col-sm-12 col-12 d-flex">
                                <svg xmlns="http://www.w3.org/2000/svg" width="27" height="30" viewBox="0 0 27 30" fill="none">
                                    <g clipPath="url(#clip0_297_4419)">
                                        <path d="M19.1249 7.5H2.24988V23.75H19.1249V7.5Z" stroke="#413E38" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" />
                                        <path d="M19.1249 13.75H23.6249L26.9999 17.5V23.75H19.1249V13.75Z" stroke="#413E38" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" />
                                        <path d="M7.31262 30C8.86592 30 10.1251 28.6009 10.1251 26.875C10.1251 25.1491 8.86592 23.75 7.31262 23.75C5.75932 23.75 4.50012 25.1491 4.50012 26.875C4.50012 28.6009 5.75932 30 7.31262 30Z" stroke="#413E38" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" />
                                        <path d="M21.9374 30C23.4907 30 24.7499 28.6009 24.7499 26.875C24.7499 25.1491 23.4907 23.75 21.9374 23.75C20.3841 23.75 19.1249 25.1491 19.1249 26.875C19.1249 28.6009 20.3841 30 21.9374 30Z" stroke="#413E38" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" />
                                    </g>
                                    <defs>
                                        <clipPath id="clip0_297_4419">
                                            <rect width="27" height="30" fill="white" />
                                        </clipPath>
                                    </defs>
                                </svg>
                                <div className="types-normal text-uppercase">
                                    {trans('types.delivery')}
                                </div>
                            </div>
                        </div>
                    }

                    {groupOrderOn &&
                        <div className="row mb-4" onClick={toggleClick}>
                            <div className="col-sm-12 col-12 d-flex">
                                <svg xmlns="http://www.w3.org/2000/svg" width="26" height="26" viewBox="0 0 26 26" fill="none">
                                    <path d="M19.6669 24.2575V22.3194C19.6669 21.2913 19.1753 20.3054 18.3001 19.5784C17.4249 18.8515 16.2379 18.4431 15.0002 18.4431H5.66674C4.42904 18.4431 3.24204 18.8515 2.36686 19.5784C1.49167 20.3054 1 21.2913 1 22.3194V24.2575" stroke="#413E38" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" />
                                    <path d="M10.3338 9.72153C12.543 9.72153 14.3339 7.76915 14.3339 5.36077C14.3339 2.95238 12.543 1 10.3338 1C8.12463 1 6.33374 2.95238 6.33374 5.36077C6.33374 7.76915 8.12463 9.72153 10.3338 9.72153Z" stroke="#413E38" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" />
                                    <path d="M25 24.2575V22.2764C24.9994 21.3986 24.7372 20.5458 24.2545 19.8519C23.7718 19.1581 23.096 18.6626 22.3333 18.4431" stroke="#413E38" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" />
                                    <path d="M17.0004 1C17.7632 1.24792 18.4393 1.81105 18.9222 2.60061C19.405 3.39017 19.6671 4.36126 19.6671 5.36077C19.6671 6.36028 19.405 7.33136 18.9222 8.12092C18.4393 8.91049 17.7632 9.47362 17.0004 9.72153" stroke="#413E38" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" />
                                </svg>
                                <div className="types-normal text-uppercase">
                                    {trans('types.group_order')}
                                </div>
                            </div>
                        </div>
                    }
                </Modal.Body>
            </Modal>
            {isGroupOrderOpen &&
                <GroupOrder toggleClick={() => toggleClick()} dataCart={rootCartItemTmp ? rootCartItemTmp : null} color={''}/>
            }
            {(isDeliveryOrderOpen) &&
                <DeliveryLocation
                    toggleDeliveryOrder={() => handleClickDelivery()}
                    onClickChangeType={() => onClickChangeType(2)}
                    currentAddress={currentAddress}
                />
            }

            {showDeliveryProductNotShipping &&
                <DeliveryProductNotShipping allowAddProductNotShipping={allowAddProductNotShipping} />
            }
        </>
    )
}

export default memo(TypesPopup)