"use client"

import React, {memo, useEffect, useState} from 'react'
import { useI18n } from '@/locales/client'
import "slick-carousel/slick/slick.css";
import "slick-carousel/slick/slick-theme.css";
import style from 'public/assets/css/datetime-list.module.scss'
import variables from '/public/assets/css/step.module.scss'
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
import * as config from "@/config/constants";
import { addPaymentMethodToCart } from '@/redux/slices/cartSlice'
import { useRouter } from 'next/navigation'
import {OPENING_HOUR_SELF_ORDERING_TYPE, EXTRA_SETTING_SELF_ORDERING_TYPE} from "@/config/constants"
import { ORIGIN_NEXT } from '@/config/constants';
import _, { set } from 'lodash'

const PaymentSelection = (props: any) => {
    const trans = useI18n();
    const color = props.color
    const workspaceId = props.workspaceId
    const workspace = props.workspace
    const type = 3;
    const workspaceToken = useAppSelector((state) => state.workspaceData.globalWorkspaceToken)
    const cartPaymentMethodFailed:any = useAppSelector((state) => state.cart.paymentMethod);
    //default value cash
    const [value, setValue] = React.useState(null);
    const [orderId, setOrderId] = useState(null);
    const [isDisabled, setIsDisabled] = useState(false);
    const [paymentMethods, setPaymentMethods] = useState<any>([]);
    const [urlOrderConfirmation, setUrlOrderConfirmation] = useState<string>('');
    const [tokenLoggedInCookie, setTokenLoggedInCookie] = useState<string>('');
    const sendActiveStep = props.activeStep
    const markStepReversed = props.stepReversed

    const PAYMENT_METHOD_TYPE = {
        MOLLIE: 0,
        INVOICE: 3,
        CASH: 2
    };
    let cartCoupon: any = useAppSelector((state) => state.cart.couponSelf);
    let cartData: any = useAppSelector((state) => state.cart.selfOrderingData);
    let cartTotalDiscount = useAppSelector((state) => state.cart.rootCartTotalDiscountSelf);
    let cartNote: any = useAppSelector((state) => state.cart.selfOrderingCartNote);
    let cartInfoSelfOrder: any = useAppSelector((state) => state.cart.dataInfoSelfOrder);
    let cartValidCouponProductIds: any = useAppSelector((state) => state.cart.rootCartValidCouponProductIdsSelf);
    let currentAvailableDiscount = cartTotalDiscount;

    const timezone: any = config.TIMEZONE;

    const getCurrentDateInTimeZone = (timezone: string): string => {
        const currentDate = new Date();
        const options: Intl.DateTimeFormatOptions = {
            timeZone: timezone,
            year: 'numeric',
            month: 'numeric',
            day: 'numeric',
            hour: 'numeric',
            minute: 'numeric',
            second: 'numeric',
        };

        return currentDate.toLocaleString('en-US', options);
    };
    const now: any = new Date(getCurrentDateInTimeZone(timezone));
    const orderData = {
        note: cartNote ?? '',
        date: moment(now).format('YYYY-MM-DD'),
        time: moment(now).format('hh:mm:ss') ?? '00:00:00',
        payment_method: Number(value),
        workspace_id: workspaceId,
        date_time: moment(now).format('YYYY-MM-DD hh:mm:ss'),
        type: type,
        setting_payment_id: paymentMethods?.filter((item: any) => item.type == value)[0]?.id ?? null,
        coupon_code: cartCoupon && cartCoupon?.code ? cartCoupon?.code : null,
        coupon_id: cartCoupon && cartCoupon?.code ? cartCoupon?.id : null,
        reward_id: cartCoupon && !cartCoupon?.code ? cartCoupon?.id : null,
        //table_number: 2,
        //table_last_person: true,
        email: cartInfoSelfOrder?.email ?? null,
        name: cartInfoSelfOrder?.full_name ?? null,
        phone: null,
        items: cartData?.map((item: any) => {
            let discount = 0;
            let available_discount = false;

            if (cartCoupon && cartCoupon.code) {
              if (cartValidCouponProductIds && cartValidCouponProductIds.includes(String(item.productId))) {
                available_discount = true;
              }
            } else if (cartValidCouponProductIds && cartValidCouponProductIds.includes(item.productId)) {
              available_discount = true;
            }

            const totolPriceItem = item.basePrice * item.productTotal;
            if (available_discount && currentAvailableDiscount > 0) {
                if (totolPriceItem >= cartTotalDiscount) {
                    discount = cartTotalDiscount;
                    currentAvailableDiscount -= discount;
                } else {
                    discount = totolPriceItem;
                    currentAvailableDiscount -= discount;
                }
            }
         
            return {
                product_id: item.productId,
                quantity: item.productTotal,
                available_discount: available_discount,
                discount: available_discount && discount,
                redeem_history_id: null,
                coupon_id: cartCoupon && cartCoupon?.code && cartValidCouponProductIds && cartValidCouponProductIds.includes(String(item.productId)) ? cartCoupon?.id : null,
                options: item.optionItemsStore?.map((op: any) => {
                    const iSelectedMaster = op.optionItems.find((io: any) => io.master);

                    if (iSelectedMaster) {
                        return {
                            option_id: op.optionId,
                            option_items: [{ option_item_id: iSelectedMaster.id }]
                        };
                    }

                    return {
                        option_id: op.optionId,
                        option_items: op.optionItems.map((it: any) => ({
                            option_item_id: it.id
                        }))
                    };
                })
            };
        }),
    }

    const router = useRouter();
    const callMollie = async (orderId: any, order: any) => {
        if (order?.total_price > 0) {
            const res = await api.post(`mollie`, {
                total_price: order?.total_price,
                order_id: orderId,
                redirect_url: '/self-ordering/successed',
                cancel_url: '/self-ordering/failed',
                origin: ORIGIN_NEXT
            });
            const redirectLink = res?.data?.data?.url;
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
        }).then((res: any) => {
            setPaymentMethods(res?.data?.data?.data.filter((item: any) => item.type != PAYMENT_METHOD_TYPE.INVOICE && item.self_ordering == true));
        }).catch((err) => {

        });

        const generateToken = api.get(`auth/token/generate`, {})
            .then((res: any) => {
                setTokenLoggedInCookie(res?.data?.data?.token);
            }).catch((err) => {

            });
    }, [workspaceId]);

    const [workspaceDataFinal, setWorkspaceDataFinal] = useState<any | null>(null);

    const getDayInTimeZone = (timezone: any) => {
        const currentDate = new Date();
        const options = {
            timeZone: timezone,
        };
        const dateInTimeZone = new Date(currentDate.toLocaleString('en-US', options));

        return dateInTimeZone.getDay();
    };

    const DATE_FORMAT = 'YYYY-MM-DD';
    const TIME_FORMAT = 'HH:mm';
    const handleSubmit = async () => {
        // Validate available timeslot to go to last step: payment
        const res = await api.get(`products/validate_available_timeslot?from=mobile&date=${moment().format(DATE_FORMAT)}&time=${moment().format(TIME_FORMAT)}&${cartData.map((cartItem: any) => `product_id[]=${cartItem.productId}`).join('&')}`);
        const available = res.data?.data || []
        if (_.includes(available, false)) {
            sendActiveStep(1)
            markStepReversed(true)
        } else {
            setIsDisabled(true);
            handleNext();
        }
    };
    const triggerCloseRestaurant = () => {
        Cookies.set('fromSelfCart', 'true')
        router.push('/self-ordering/closed');
    };

    const language = Cookies.get('Next-Locale') ?? 'nl';
    const checkTime = (clicking: any, dataPass: any) => {
        const now: any = new Date(getCurrentDateInTimeZone(timezone));
        const dayName: any = getDayInTimeZone(timezone);
        const dataFinal = dataPass ? dataPass : workspaceDataFinal;
        dataFinal?.setting_open_hours.map((item: any, index: any) => {
            if (item.type === OPENING_HOUR_SELF_ORDERING_TYPE) {
                const hasDayName = (obj: any) => obj.day_number === dayName;
                // check if has day name or not
                if (!item.open_time_slots.some(hasDayName)) {
                    triggerCloseRestaurant();
                } else {
                    item?.open_time_slots.map((range: any, time_index: any) => {
                        if (range.day_number === dayName) {
                            const startTime = new Date(now);
                            const endTime = new Date(now);
                            const startHoursMinutesSeconds = range.start_time.split(':').map((val: string) => parseInt(val, 10));
                            const endHoursMinutesSeconds = range.end_time.split(':').map((val: string) => parseInt(val, 10));

                            startTime.setHours(startHoursMinutesSeconds[0], startHoursMinutesSeconds[1], startHoursMinutesSeconds[2]);
                            endTime.setHours(endHoursMinutesSeconds[0], endHoursMinutesSeconds[1], endHoursMinutesSeconds[2]);

                            if (now >= startTime && now <= endTime) {
                                setIsDisabled(true);
                                if (cartData.length > 0) {
                                    const fetchOrderData = api.post(`orders`, orderData, {
                                        headers: {
                                            'Content-Type': 'text/plain',
                                            'Authorization': 'Bearer ' + tokenLoggedInCookie,
                                            'Timezone': 'Asia/Ho_Chi_Minh',
                                            'App-Token': workspaceToken || '',
                                            'Content-Language': language
                                        },
                                    }).then((res) => {
                                        setOrderId(res?.data?.data?.id);
                                        let urlConfirm = '/orders/' + res?.data?.data?.id + '?is_api=1&order_id=' + res?.data?.data?.id;
                                        setUrlOrderConfirmation(urlConfirm);
                
                                        if (value == 2) {
                                            router.push(urlConfirm);
                                        } else if (value == 0) {
                                            dispatch(addPaymentMethodToCart(res?.data?.data?.payment_method));
                                            callMollie(res?.data?.data?.id, res?.data?.data);
                                        } else {
                                            router.push(urlConfirm);
                                        }
                                    }).catch((err) => {
                                        // console.log(err);
                                    });
                                }
                            } else {
                                triggerCloseRestaurant();
                            }
                        }
                    })
                }
            }
        })
    };

    const handleNext = async () => {
        try {
            if (workspaceId) {
                const res = await api.get(`workspaces/` + workspaceId, {
                    headers: {
                        'Authorization': `Bearer ${tokenLoggedInCookie}`,
                        'Content-Language': language
                    }
                });

                const json = res.data;
                setWorkspaceDataFinal(json.data);
                let flagCheckTime = true;
                
                // validate in admin extra setting
                json.data?.extras.map((item: any) => {
                    if (item?.type === EXTRA_SETTING_SELF_ORDERING_TYPE) {
                        if (item.active != true) {
                            flagCheckTime = false;
                            triggerCloseRestaurant();
                        }
                    }
                });

                // validate in manager opening hours setting
                json.data?.setting_open_hours.map((item: any) => {
                    if (item?.type === OPENING_HOUR_SELF_ORDERING_TYPE) {
                        if (item.active != true) {
                            flagCheckTime = false;
                            triggerCloseRestaurant();
                        }
                    }
                });

                if(flagCheckTime === true) {
                    checkTime(true, json.data);
                }
            }
        } catch (error) {
            console.log(error);
        }
    }

    const dispatch = useAppDispatch();

    useEffect(() => {
        if (cartPaymentMethodFailed != null) {
            setValue(cartPaymentMethodFailed.toString());
        }
    }, [cartPaymentMethodFailed])

    useEffect(() => {
        if (paymentMethods && paymentMethods.length > 0) {
            setValue(paymentMethods[0].type.toString());
        }
    }, [paymentMethods]);

    return (
        <>
            <div className={`${style['datetime-list']}`} style={{marginTop: '145px'}}>
                <div className="row">
                    <div className="col-sm-12 col-12">
                        <div className={`${style['datetime-label']} mt-0 res-mobile`}>
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
                                                            <Typography className={`sub-payment-item-label res-mobile`}>{trans('cash-payment')}</Typography>
                                                            <Typography className={`sub-payment-item-label res-desktop`}>{trans('cash-payment-desktop')}</Typography>
                                                        </>
                                                    } />
                                            </div>
                                            : <div key={index} className={`payment-item-group`} style={value == 3 ? {background: '#F5F5F5'} : {}}>
                                                <FormControlLabel value={3} control={<BpRadio />}
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
                <div className="row">
                    <div className="col-sm-12 col-12 text-center">
                        {
                            !isDisabled ? (
                                <button className={`itr-btn-primary ${style['next-step-btn']} ${variables['active-btn']}`} 
                                    onClick={() => {
                                        setIsDisabled(true);
                                        handleSubmit();
                                    }}
                                    type="button">
                                {trans('cart.further')}
                                </button>
                            ) : (
                                <button className={`itr-btn-primary ${style['next-step-btn']} res-mobile mx-auto`}
                                        style={{ background: 'rgba(65, 62, 56, 0.50)', color: "white" }} 
                                        type="button">
                                    {trans('cart.further')}
                                </button>
                            )
                        }
                    </div>
                </div>
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
                            <div className={style['step-number']} style={window.innerWidth < 1280 ? { color: color, borderColor: color } : {background: color}}>3</div>
                            <div className={style['step-name']} style={{ color: color }}>{trans('cart.step_payment_method')}</div>
                        </div>
                    </div>
                </div>
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