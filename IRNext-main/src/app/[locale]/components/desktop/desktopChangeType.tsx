'use client'

import React, { useState, memo, useMemo, useEffect } from 'react'
import 'public/assets/css/modal.scss'
import { useI18n } from '@/locales/client'
import { useRouter } from 'next/navigation'
import { useAppSelector, useAppDispatch } from '@/redux/hooks'
import { changeType, changeTypeFlag, rootAddToCart, rootToggleAddToCartSuccess, changeRootInvalidProductIds, changeRootCartItemTmp, addOpenLoginDesktop } from '@/redux/slices/cartSlice'
import { setflagNextData } from '@/redux/slices/flagNextSlice'
import { setIsOnGroupDeskData } from '@/redux/slices/isOnGroupDeskSlice'
import GroupOrder from '../layouts/popup/groupOrder'
import axios from "axios";
import * as config from "@/config/constants";
import Cookies from 'js-cookie';
import { manualChangeOrderTypeDesktop, addFormGroupOpen , addIsReadyTakeOut } from '@/redux/slices/cartSlice'
import useMediaQuery from '@mui/material/useMediaQuery'

function DesktopChangeType(props: any) {
    const { workspace, from, isDeliveryOrderOpenManual, setIsDeliveryOrderOpenManual, setIsDeliveryType } = props
    const trans = useI18n()
    const router = useRouter()
    const dispatch = useAppDispatch()
    let rootType = useAppSelector((state) => state.cart.type)
    let rootTypeFlag = useAppSelector((state) => state.cart.typeFlag)
    let rootCartItemTmp = useAppSelector((state) => state.cart.rootCartItemTmp)
    const [show, setShow] = useState(false)
    const takeoutOn = useMemo(() => workspace?.setting_open_hours?.find((item: any) => item.type === 0)?.active, [workspace])
    const deliveryOn = useMemo(() => workspace?.setting_open_hours?.find((item: any) => item.type === 1)?.active, [workspace])
    const groupOrderOn = useMemo(() => workspace?.extras?.find((item: any) => item.type === 1)?.active, [workspace])
    const tokenLoggedInCookie = Cookies.get('loggedToken');
    const color = workspace?.setting_generals?.primary_color;
    const isMobile = useMediaQuery('(max-width: 1279px)');
    const [isLoginOpen, setIsLoginOpen] = useState<any | null>(false);
    const formGroupOpen = useAppSelector((state) => state.cart.formGroupOpen)

    const togglePopupLogin = () => {
        setIsLoginOpen(!isLoginOpen);
    }

    if (!rootType) {
        dispatch(changeType(1))
    }
    useEffect(() => {
        if ((!groupOrderOn && (!takeoutOn || !deliveryOn)) || !rootTypeFlag) {
            setShow(false)

            // trigger takeout
            if (!groupOrderOn && takeoutOn && !deliveryOn && rootTypeFlag === true) {
                dispatch(changeType(1))
                dispatch(changeTypeFlag(false))

                if (from !== 'in_cart' && rootCartItemTmp != null) {
                    dispatch(rootAddToCart(rootCartItemTmp))
                    dispatch(rootToggleAddToCartSuccess())
                    dispatch(changeRootCartItemTmp(null))
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
    }, [takeoutOn, groupOrderOn, deliveryOn, rootTypeFlag, dispatch])

    const [isGroupOrderOpen, setIsGroupOrderOpen] = useState(false);
    const [isDeliveryOrderOpen, setIsDeliveryOrderOpen] = useState(false);
    const isReadyTakeOut = useAppSelector((state) => state.cart.isReadyTakeOut);
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
    }

    const [isSuccess, setIsSuccess] = useState(false);
    const [isFormGroupOpen, setIsFormGroupOpen] = useState(false);
    const toggleFormGroup = () => {
        setIsFormGroupOpen(!isFormGroupOpen);
    }
    const handleCheck = (check: boolean) => {
        setIsSuccess(check);
    }

    const toggleClick = (type?: number) => {
        if (!tokenLoggedInCookie) {
            if (isMobile) {
                router.push('/user/login?group-order=true');
            } else {
                togglePopupLogin();
            }
            // router.push('/user/login?group-order=true');

            // Show popup login
            let btnPopupLogin = document.getElementById('btnPopupLogin');

            if (btnPopupLogin) {
                btnPopupLogin.click();

                return type;
            }
        } else {
            setIsGroupOrderOpen(!isGroupOrderOpen);
        }

        setIsFormGroupOpen(!isFormGroupOpen);
        setShow(false);

        if (type) {
            if (type == 3 && !isMobile) {
                dispatch(setflagNextData(false));
                dispatch(setIsOnGroupDeskData(true))
            }
            // dispatch(changeType(type));
            // dispatch(changeTypeFlag(false));
        }

        return type;
    }

    useEffect(() => {
        if (Cookies.get('groupOrder') == 'true') {
            toggleClick()
            Cookies.remove('groupOrder')
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
        dispatch(addIsReadyTakeOut(false));
        setIsDeliveryOrderOpenManual(!isDeliveryOrderOpenManual);
    }
    const query = new URLSearchParams(window.location.search);

    const onClickChangeType = (type: number) => {
        if (type == 2
            && from !== 'in_cart'
            && rootCartItemTmp != null
            && rootCartItemTmp?.product?.data?.category?.available_delivery == false) {
            setShowDeliveryProductNotShipping(true)
        } else {
            dispatch(changeTypeFlag(false))
            if(type == 1){
               dispatch(addIsReadyTakeOut(true));
            }
            if (type != 2 && window.location.href.includes('category/products')) {
                setIsDeliveryType(false)
            } else {
                setIsDeliveryType(true)
            }

            if (from !== 'in_cart' && rootCartItemTmp != null) {
                dispatch(rootAddToCart(rootCartItemTmp))
                dispatch(rootToggleAddToCartSuccess())
                dispatch(changeRootCartItemTmp(null))
                dispatch(manualChangeOrderTypeDesktop(false))
            }
        }

        if (query.get('groupOrder') && isMobile) {
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
            router.push('/category/products?delivery=true')
        }
    }

    useEffect(() => {
        if (!isMobile) {
            if (Cookies.get('fromDesk') == 'groupOrderDesk') {
                toggleClick(3)
            }
        }

    }, [Cookies.get('fromDesk')])

    const handleClick3 = () => {
        toggleClick(3);
        dispatch(addIsReadyTakeOut(false));
        dispatch(addFormGroupOpen(true));
        if (!tokenLoggedInCookie) {
            const currentProductId: any = Cookies.get('currentProductId');
            dispatch(addOpenLoginDesktop(true))
            Cookies.set('groupOrderDesktop', 'true');
            Cookies.set('currentProductId', currentProductId);
        } else {
            dispatch(addOpenLoginDesktop(false))
        }
    }

    useEffect(() => {
        if (Cookies.get('currentProductId') && Cookies.get('groupOrderDesktop')) {
            setIsFormGroupOpen(true);
            Cookies.remove('currentProductId');
            // Cookies.remove('groupOrderDesktop');
        }
    }, [Cookies.get('currentProductId'), Cookies.get('groupOrderDesktop')]);

    useEffect(() => {
        if (!formGroupOpen) {
            setIsFormGroupOpen(false);
        }
    }, [formGroupOpen])
        
    return (
        <>
            <div className="types-body desktop">
                {(!isFormGroupOpen) &&
                    <div className="row mb-3">
                        <div className="col-sm-12 col-12">
                            <h3 className="types-title">
                                {trans('types.choose_your_order_type')}
                            </h3>
                        </div>
                    </div>
                }

                {(!isFormGroupOpen && takeoutOn) &&
                    <div className="row mb-3" onClick={() => onClickChangeType(1)}>
                        <div className="col-sm-12 col-12 cursor-pointer">
                            <div className={`type-option ${rootType === 1 || isReadyTakeOut ? 'active' : ''}`}>
                                <div className="types-normal text-uppercase" style={rootType === 1 || isReadyTakeOut ? { color: color } : {}}>
                                    {trans('types.pickup')}
                                </div>
                                <p className="type-description">
                                    {trans('cart.type_takeout_description')} {workspace?.address}.
                                </p>
                            </div>
                        </div>
                    </div>
                }

                {(!isFormGroupOpen && deliveryOn) &&
                    <div className="row mb-3" onClick={() => handleClickDelivery()}>
                        <div className="col-sm-12 col-12 cursor-pointer">
                            <div className={`type-option ${rootType === 2 && !isReadyTakeOut ? 'active' : ''}`}>
                                <div className="types-normal text-uppercase" style={rootType === 2 && !isReadyTakeOut ? { color: color } : {}}>
                                    {trans('types.delivery')}
                                </div>
                                <p className="type-description">
                                    {trans('cart.type_delivery_description')}
                                </p>
                            </div>
                        </div>
                    </div>
                }

                {(!isFormGroupOpen && groupOrderOn) &&
                    <div className="row mb-3" onClick={handleClick3}>
                        <div className="col-sm-12 col-12 cursor-pointer">
                            <div className={`type-option ${rootType === 3 && !isReadyTakeOut ? 'active' : ''}`}>
                                <div className="types-normal text-uppercase" style={rootType === 3 && !isReadyTakeOut ? { color: color } : {}}>
                                    {trans('types.group_order')}
                                </div>
                                <p className="type-description">
                                    {trans('cart.type_group_description')}
                                </p>
                            </div>
                        </div>
                    </div>
                }

                {(isFormGroupOpen) &&
                    <GroupOrder toggleClick={() => toggleClick()} dataCart={rootCartItemTmp ? rootCartItemTmp : null} origin={'desktop'} color={color} />
                }
                {/* {isLoginOpen && <Login togglePopup={() => togglePopupLogin()} from={'groupOrderDesk'} />} */}
            </div>
        </>
    )
}

export default memo(DesktopChangeType)
