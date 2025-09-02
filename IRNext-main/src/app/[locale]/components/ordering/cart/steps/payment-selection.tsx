"use client"

import React, {memo, useEffect, useState, useRef} from 'react'
import { useI18n } from '@/locales/client'
import "slick-carousel/slick/slick.css";
import "slick-carousel/slick/slick-theme.css";
import style from 'public/assets/css/datetime-list.module.scss'
import "react-datepicker/dist/react-datepicker.css";
import Radio from '@mui/material/Radio';
import RadioGroup from '@mui/material/RadioGroup';
import FormControlLabel from '@mui/material/FormControlLabel';
import Typography from "@mui/material/Typography";
import { styled } from '@mui/material/styles';
import { api } from "@/utils/axios";
import moment from 'moment';
import { useAppSelector, useAppDispatch } from '@/redux/hooks'
import Cookies from "js-cookie";
import { addPaymentMethodToCart, changeCartLimitTimeToPayment } from '@/redux/slices/cartSlice'
import { useRouter } from 'next/navigation'
import 'react-toastify/dist/ReactToastify.css';
import {Slide, toast, ToastContainer} from "react-toastify";
import { applyCouponIncludeVatLogic } from '@/services/coupon';
import { toString, toNumber } from 'lodash';
import { ORIGIN_NEXT } from '@/config/constants';

const PaymentSelection = (props: any) => {
    const trans = useI18n();
    const color = props.color
    const activeStep = props.activeStep
    const workspaceId = props.workspaceId
    const type = props.type - 1;
    const workspaceToken = useAppSelector((state) => state.workspaceData.globalWorkspaceToken)
    const cartDeliveryAddress = useAppSelector((state) => state.cart.rootCartDeliveryAddress);
    const cartPaymentMethodFailed:any = useAppSelector((state) => state.cart.paymentMethod);
    const cartLimitTimeToPayment = useAppSelector((state) => state.cart.cartLimitTimeToPayment)
    const isShowRedeemGlobal = useAppSelector((state) => state.cart.isShowRedeemGlobal)
    const totalPriceNeedToPay = useAppSelector((state) => state.cart.totalPriceNeedToPay)
    const language = Cookies.get('Next-Locale');

    //default value cash
    const [value, setValue] = React.useState(null);
    const [orderId, setOrderId] = useState(null);
    const [paymentMethods, setPaymentMethods] = useState<any>([]);
    const [urlOrderConfirmation, setUrlOrderConfirmation] = useState<string>('');
    const tokenLoggedInCookie = Cookies.get('loggedToken');
    const sendActiveStep = props.activeStep
    const orderTypes = [
        trans('take-away'),
        trans('delivery'),
        trans('group-order'),
    ];
    const orderTypesText = [
        'takeout',
        'delivery',
        'in_house',
    ];
    const PAYMENT_METHOD_TYPE = {
        MOLLIE: 0,
        INVOICE: 3,
        CASH: 2
    };
    const typeText = orderTypesText[type];
    let cartCoupon: any = useAppSelector((state) => state.cart.coupon);
    let cartData: any = useAppSelector((state) => state.cart.rootData);
    let cartTotalPrice = useAppSelector((state) => state.cart.rootCartTotalPrice);
    let cartTotalDiscount = useAppSelector((state) => state.cart.rootCartTotalDiscount);
    let cartDatetime: any = useAppSelector((state) => state.cart.rootCartDatetime);
    let cartNote: any = useAppSelector((state) => state.cart.rootCartNote);
    let cartValidCouponProductIds: any = useAppSelector((state) => state.cart.rootCartValidCouponProductIds);
    let cartDeliveryConditions: any = useAppSelector((state) => state.cart.rootCartDeliveryConditions);
    let cartRedeemId: any = useAppSelector((state) => state.cart.rootCartRedeemId);
    const groupOrder = useAppSelector<any>((state) => state.groupOrder.data);
    const groupOrderNowSlice = useAppSelector<any>((state: any) => state.cart.groupOrderSelectedNow);
    const dating = groupOrder && groupOrderNowSlice
    ? groupOrder?.receive_time ?? '00:00:00'
    : cartDatetime?.time ?? '00:00:00';
    let currentAvailableDiscount = cartTotalDiscount;
    const orderDataType = (groupOrder && groupOrderNowSlice) ? groupOrder?.type : Number(type);
    const orderData = {
        note: cartNote ?? '',
        date: moment(cartDatetime?.date).format('YYYY-MM-DD'),
        time: cartDatetime?.time ?? '00:00:00',
        payment_method: Number(value),
        workspace_id: workspaceId,
        date_time: moment(cartDatetime?.date).format('YYYY-MM-DD') + ' ' + dating,
        setting_timeslot_detail_id: cartDatetime?.settingTimeslotId,
        type: orderDataType,
        setting_payment_id: paymentMethods?.filter((item: any) => item.type == value)[0]?.id ?? null,
        // setting_payment_id: null,
        setting_delivery_condition_id: type == 1 ? cartDeliveryConditions?.id : null,
        group_id: (groupOrder && groupOrderNowSlice) ? Number(groupOrder?.id) : null,
        address: type == 1 ? cartDeliveryAddress?.address : null,
        lat: type == 1 ? cartDeliveryAddress?.lat : null,
        lng: type == 1 ? cartDeliveryAddress?.lng : null,
        coupon_code: cartCoupon && cartCoupon?.code ? cartCoupon?.code : null,
        coupon_id: cartCoupon && cartCoupon?.code ? cartCoupon?.id : null,
        reward_id: cartCoupon && !cartCoupon?.code ? cartCoupon?.id : null,
        items: applyCouponIncludeVatLogic(cartData, orderDataType, cartCoupon, cartValidCouponProductIds, groupOrder, groupOrderNowSlice, currentAvailableDiscount, cartRedeemId && isShowRedeemGlobal ? cartRedeemId : null)
    }

    useEffect(() => {
        if (paymentMethods && paymentMethods.length > 0) {
            setValue(paymentMethods[0].type.toString());
        }
    }, [paymentMethods]);

    const router = useRouter();
    const callMollie = async (orderId: any , groupOrderId:any) => {
        const totalPriceIncludeShipAndServiceCost = toNumber(totalPriceNeedToPay ?? 0);

        if (totalPriceIncludeShipAndServiceCost > 0) {
            const res = await api.post(`mollie`, {
                total_price: cartTotalPrice > 0 ? cartTotalPrice : totalPriceIncludeShipAndServiceCost - cartTotalPrice,
                order_id: orderId,
                redirect_url: '/orders/' + orderId + '?is_api=1&order_id=' + orderId,
                cancel_url: '/orders/failed',
                origin: ORIGIN_NEXT
            });

            let redirectLink = res?.data?.data?.url;
            if (groupOrderId) {
                redirectLink = redirectLink + (redirectLink.includes('?') ? '&' : '?') + `groupOrder=${groupOrderId}`;
            }

            window.location.href = redirectLink;
        } else {
            router.push('/orders/' + orderId + '?is_api=1&order_id=' + orderId);
        }
    };

    const BpIcon = styled('span')(({ theme }) => ({
        borderRadius: '50%',
        width: 20,
        height: 20,
        boxShadow:
            theme.palette.mode === 'dark'
                ? '0 0 0 1px rgb(16 22 26 / 40%)'
                : 'inset 0 0 0 2px #4040409e',
        backgroundColor: '#F6F6F6',
        backgroundImage:
            theme.palette.mode === 'dark'
                ? 'linear-gradient(180deg,hsla(0,0%,100%,.05),hsla(0,0%,100%,0))'
                : 'linear-gradient(180deg,hsla(0,0%,100%,.8),hsla(0,0%,100%,0))',
        '.Mui-focusVisible &': {
            outline: '2px auto rgba(19,124,189,.6)',
            outlineOffset: 2,
        },
        'input:hover ~ &': {
            backgroundColor: theme.palette.mode === 'dark' ? '#30404d' : '#ebf1f5',
        },
        'input:disabled ~ &': {
            boxShadow: 'none',
            background:
                theme.palette.mode === 'dark' ? 'rgba(57,75,89,.5)' : 'rgba(206,217,224,.5)',
        },
    }));

    const BpCheckedIcon = styled(BpIcon)({
        backgroundColor: color,
        backgroundImage: 'linear-gradient(180deg,hsla(0,0%,100%,.1),hsla(0,0%,100%,0))',
        boxShadow: 'inset 0 0 0 0px #4040409e',
        '&:before': {
            display: 'block',
            width: 20,
            height: 20,
            backgroundImage: 'url("data:image/svg+xml;utf8,<svg xmlns=\'http://www.w3.org/2000/svg\' width=\'22\' height=\'22\' viewBox=\'0 0 22 22\' fill=\'none\'><path d=\'M17 6.5L8.75 14.5L5 10.8636\' stroke=\'white\' strokeWidth=\'2\' strokeLinecap=\'round\' strokeLinejoin=\'round\'/></svg>")',
            content: '""',
        },
        'input:hover ~ &': {
            backgroundColor: color,
        },
    });

    // Inspired by blueprintjs
    function BpRadio(props: any) {
        return (
            <Radio
                disableRipple
                color="default"
                checkedIcon={<BpCheckedIcon />}
                icon={<BpIcon />}
                {...props}
            />
        );
    }

    const handleChange = (event: any) => {
        setValue(event.target.value);
    };

    useEffect(() => {
        const fetchOrderData = api.get(`workspaces/${workspaceId}/settings/payment_methods`, {
            headers: {
                Authorization: 'Bearer ' + tokenLoggedInCookie,
            }
        }).then((res:any) => {
            if (groupOrder && groupOrderNowSlice) {
                const paymentGroupOrder = []

                if (groupOrder?.payment_mollie === 1) {
                    const mollie = res?.data?.data?.data.filter((item: any) => item?.type_display === "Mollie")
                    if(mollie.length > 0){
                        paymentGroupOrder.push(mollie[0])
                    }
                }

                if (groupOrder?.payment_factuur === 1) {
                    paymentGroupOrder.push({ type: PAYMENT_METHOD_TYPE.INVOICE })
                }

                if (groupOrder?.payment_cash === 1) {
                    paymentGroupOrder.push({ type: PAYMENT_METHOD_TYPE.CASH })
                }
                setPaymentMethods(paymentGroupOrder);
            } else {
                setPaymentMethods(res?.data?.data?.data.filter((item: any) => item[typeText] == true));
            }

        }).catch((err) => {

        });
    }, [workspaceId , groupOrderNowSlice , groupOrder]);

    const [errorMessage, setErrorMessage] = useState('');
    const [loadingSubmitForm, setLoadingSubmitForm] = useState(false);
    const handleNext = async () => {
        if (loadingSubmitForm) return; // prevent re-entry
        setLoadingSubmitForm(true);
        setErrorMessage('');

        try {
            const res: any = await api.post(`orders`, orderData, {
                headers: {
                    'Content-Type': 'text/plain',
                    'Authorization': 'Bearer ' + tokenLoggedInCookie,
                    'Timezone': 'Asia/Ho_Chi_Minh',
                    'App-Token': workspaceToken || '',
                    'Content-Language': language
                }
            });    

            setOrderId(res?.data?.data?.id);

            if (res?.data?.data?.id) {
                let urlConfirm = '/orders/' + res?.data?.data?.id + '?is_api=1&order_id=' + res?.data?.data?.id;
                setUrlOrderConfirmation(urlConfirm);

                if (value == 2) {
                    if (groupOrder && groupOrderNowSlice) {
                        router.push(urlConfirm)
                    } else {
                        window.location.href = urlConfirm;
                    }
                } else if (value == 0) {
                    dispatch(addPaymentMethodToCart(res?.data?.data?.payment_method));
                    if (groupOrder && groupOrderNowSlice) {
                        await callMollie(res?.data?.data?.id, groupOrderNowSlice?.id ?? null);
                    } else {
                        await callMollie(res?.data?.data?.id, null);
                    }
                } else {
                    if (groupOrder && groupOrderNowSlice) {
                        router.push(urlConfirm)
                    } else {
                        window.location.href = urlConfirm;
                    }
                }
            }
        } catch (err: any) {
            setErrorMessage(err?.response?.data?.message);
            setTimeout(() => {
                setLoadingSubmitForm(false);
            }, 1000);  
        }
    }

    let rootType = useAppSelector((state) => state.cart.type);
    const dispatch = useAppDispatch();

    useEffect(() => {
        if (cartPaymentMethodFailed != null) {
            setValue(cartPaymentMethodFailed.toString());
        }
    }, [cartPaymentMethodFailed])

    useEffect(() => {
        if (errorMessage && window.innerWidth < 1280) {
            // turn off all prev toast
            toast.dismiss();
            // display toast
            toast(errorMessage, {
                position: toast.POSITION.BOTTOM_CENTER,
                autoClose: 1500,
                hideProgressBar: true,
                closeOnClick: true,
                closeButton: false,
                transition: Slide,
                className: 'message',
            });
        }
    }, [errorMessage]);

    const handleTexting = () => {
        if(rootType == 1){
            return trans('cash-payment')
        }
        else if (rootType == 2){
            return trans('cash-payment-delivery')
        } else {
            if(groupOrderNowSlice && groupOrderNowSlice?.type == 0){
                return trans('cash-payment')
            } else {
                return trans('cash-payment-delivery')
            }
        }
    }

    const startTimeLimit = localStorage.getItem('cartLimitTimeToPayment');

    // limit time to payment, default 5 minutes
    useEffect(() => {
        let numberOfTimeLimit = 300000;

        if(!startTimeLimit) {
            localStorage.setItem('cartLimitTimeToPayment', toString(moment.now()));
        } else {
            const pastTime = moment(toNumber(startTimeLimit));
            const diffInMiliSeconds = moment().diff(pastTime, 'milliseconds');
            numberOfTimeLimit = diffInMiliSeconds < numberOfTimeLimit ? numberOfTimeLimit - diffInMiliSeconds : 0;
        }
        
        setTimeout(() => {
            if(activeStep) {
                dispatch(changeCartLimitTimeToPayment(true))
                activeStep(2)
            }
        }, numberOfTimeLimit)
    }, [startTimeLimit]);

    const refCartHeader = useRef<HTMLDivElement>(null);
    const refCartFooter = useRef<HTMLDivElement>(null);

    return (
        <>
            <div className={`${style['datetime-list']}`}>
                <div className="res-mobile">
                    {groupOrderNowSlice ? (
                        <h1 className={`${style['group-order']}`} style={{ color: color ? color : 'black' }}>{trans('group-ordering')} {groupOrderNowSlice ? groupOrderNowSlice?.name : ''}</h1>
                    ) : (
                        <div className="row">
                            <div className="col-sm-12 col-12">
                                <div className={`${style['type-label']}`}>
                                    {orderTypes[type]}
                                </div>
                            </div>
                        </div>
                    )}
                </div>
                <div ref={refCartHeader} className="cart-navigation cart-header">
                    <div className={`${style.backing} res-desktop mb-3`} style={{ position: "relative" }}>
                        <svg onClick={() => sendActiveStep(2)} xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" style={{ position: "absolute"}}>
                            <path d="M15 18L9 12L15 6" stroke="#888888" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"/>
                        </svg>
                        {groupOrderNowSlice ? (
                            <h1 className={`${style['group-order']}`} style={{ color: color ? color : 'black' }}>{trans('group-ordering')} {groupOrderNowSlice ? groupOrderNowSlice?.name : ''}</h1>
                        ) : (
                            <div className="row">
                                <div className="col-sm-12 col-12">
                                    <div className={`${style['type-label']}`} style={{ background: color }}>
                                        {orderTypes[type]}
                                    </div>
                                </div>
                            </div>
                        )}
                    </div>
                </div>

                {
                    errorMessage && window.innerWidth >= 1280 && (
                        <div className={`row d-flex ${style['datetime-error']} res-desktop`}>
                            <div className={`col-auto d-flex align-items-center`}>
                            <svg xmlns="http://www.w3.org/2000/svg" width="21" height="21" viewBox="0 0 21 21" fill="none">
                                    <path d="M9.00417 3.37756L1.59292 15.7501C1.44011 16.0147 1.35926 16.3147 1.35841 16.6203C1.35755 16.9258 1.43672 17.2263 1.58804 17.4918C1.73936 17.7572 1.95755 17.9785 2.22091 18.1334C2.48427 18.2884 2.78361 18.3717 3.08917 18.3751H17.9117C18.2172 18.3717 18.5166 18.2884 18.7799 18.1334C19.0433 17.9785 19.2615 17.7572 19.4128 17.4918C19.5641 17.2263 19.6433 16.9258 19.6424 16.6203C19.6416 16.3147 19.5607 16.0147 19.4079 15.7501L11.9967 3.37756C11.8407 3.1204 11.621 2.90779 11.359 2.76023C11.0969 2.61267 10.8012 2.53516 10.5004 2.53516C10.1996 2.53516 9.90396 2.61267 9.64187 2.76023C9.37978 2.90779 9.16015 3.1204 9.00417 3.37756V3.37756Z" stroke="#E03009" strokeWidth="1.5" strokeLinecap="round" strokeLinejoin="round" />
                                    <path d="M10.5 7.875V11.375" stroke="#E03009" strokeWidth="1.5" strokeLinecap="round" strokeLinejoin="round" />
                                    <path d="M10.5 14.875H10.5088" stroke="#E03009" strokeWidth="1.5" strokeLinecap="round" strokeLinejoin="round" />
                                </svg>
                            </div>
                            <div className={`col ps-0 ${style['datetime-error-text']}`}>
                                <p className={`${style.notMargin}`}>{errorMessage ? errorMessage : ""}</p>
                            </div>
                        </div>
                    )
                }
                <div className="row">
                    <div className="col-sm-12 col-12">
                        <div className={`${style['datetime-label']} res-mobile`}>
                            {trans('payment-method')}
                        </div>
                    </div>
                </div>
                <div className="row">
                    <div className={`col-sm-12 col-12 payment-selection p-0`}>
                        <RadioGroup
                            aria-labelledby="demo-radio-buttons-group-label"
                            name="radio-buttons-group"
                            value={value}
                            onChange={handleChange}
                        >
                            {
                                paymentMethods.map((item: any, index: number) => (
                                    item.type == 0 ?
                                        <div key={index} className={`payment-item-group`} style={value == 0 ? {background: '#F5F5F5'} : {}}>
                                            <FormControlLabel style={{width: "100%"}} value={0} control={<BpRadio />}
                                                label={
                                                    <>
                                                        <Typography className={`payment-item-label`} style={value == 0 ? {color: color} : {}}>{trans('online')}</Typography>
                                                        <Typography className={`sub-payment-item-label res-mobile`}>{trans('choose-online-method')}</Typography>
                                                        <Typography className={`sub-payment-item-label res-desktop`}>{trans('choose-online-method-desktop')}</Typography>
                                                    </>
                                                } />
                                        </div>
                                        : item.type == 2 ?
                                            <div key={index} className={`payment-item-group`} style={value == 2 ? {background: '#F5F5F5'} : {}}>
                                                <FormControlLabel style={{width: "100%"}} value={2} control={<BpRadio />}
                                                    label={
                                                        <>
                                                            <Typography className={`payment-item-label res-mobile`} style={value == 2 ? {color: color} : {}}>{trans('cash')}</Typography>
                                                            <Typography className={`payment-item-label res-desktop`} style={value == 2 ? {color: color} : {}}>{trans('pay-cash')}</Typography>
                                                            <Typography className={`sub-payment-item-label res-mobile`}>{handleTexting()}</Typography>
                                                            <Typography className={`sub-payment-item-label res-desktop`}>{handleTexting()}</Typography>
                                                        </>
                                                    } />
                                            </div>
                                            : <div key={index} className={`payment-item-group`} style={value == 3 ? {background: '#F5F5F5'} : {}}>
                                                <FormControlLabel style={{width: "100%"}} value={3} control={<BpRadio />}
                                                    label={
                                                        <>
                                                            <Typography className={`payment-item-label`} style={value == 3 ? {color: color} : {}}>{trans('on-invoice')}</Typography>
                                                            <Typography className={`sub-payment-item-label res-mobile`}>{trans('receive-invoice')}</Typography>
                                                            <Typography className={`sub-payment-item-label res-desktop`}>{trans('receive-invoice-desktop')}</Typography>
                                                        </>
                                                    } />
                                            </div>
                                ))
                            }
                        </RadioGroup>
                    </div>
                </div>
            </div>
            <div ref={refCartFooter} className={`cart-navigation cart-footer`}>
                <div className="row">
                    <div className="col-sm-12 col-12 text-center">
                        {
                            (value && !loadingSubmitForm) ? (
                                <button className={`itr-btn-primary ${style['next-step-btn']}`} 
                                        style={{background: color}} 
                                        onClick={handleNext} 
                                        type="button">
                                    {trans('cart.further')}
                                </button>
                            ) : (
                                <>
                                    <button className={`itr-btn-primary ${style['next-step-btn']} res-desktop mx-auto`}
                                            style={{ background: color, color: "white", opacity: 0.5 }} 
                                            type="button">
                                        {trans('cart.further')}
                                    </button>
                                    <button className={`itr-btn-primary ${style['next-step-btn']} res-mobile mx-auto`}
                                            style={{ background: '#D1D1D1', color: "white" }} 
                                            type="button">
                                        {trans('cart.further')}
                                    </button>
                                </>
                            )
                        }
                    </div>
                </div>
                <div className="row mt-4">
                    <div className="col-sm-12 col-12">
                        <div className={style.steps}>
                            <div className={style['step-item']} onClick={() => {sendActiveStep(1) ; Cookies.remove('oppenedSuggest')} } role={'button'}>
                                <div className={style['step-number']} style={{color: window.innerWidth > 1280  ? '#FFF' : color, borderColor: color, background: window.innerWidth > 1280 ? color : ''}}>1</div>
                                <div className={style['step-name']} style={{ color: color }}>{trans('cart.step_overview')}</div>
                            </div>
                            <div className={style['step-item']} onClick={() => sendActiveStep(2)} role={'button'}>
                                <div className={style['step-number']} style={{color: window.innerWidth > 1280  ? '#FFF' : color, borderColor: color, background: window.innerWidth > 1280 ? color : ''}}>2</div>
                                <div className={style['step-name']} style={{ color: color }}>{trans('date-time')}</div>
                            </div>
                            <div className={style['step-item']}>
                                <div className={style['step-number']} style={{color: window.innerWidth > 1280  ? '#FFF' : color, borderColor: color, background: window.innerWidth > 1280 ? color : ''}}>3</div>
                                <div className={style['step-name']} style={{ color: color }}>{trans('cart.step_payment_method')}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div className="res-mobile">
                <ToastContainer />
            </div>
            <style>{`
                .cart-type-item {
                    border: 1px solid ${color};
                    color: ${color};
                }
                .active {
                    color: #FFFFFF;
                    background: ${color}!important;
                  }`}
            </style>
        </>
    )
}

export default memo(PaymentSelection)