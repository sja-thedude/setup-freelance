"use client"

import React, { useState, useEffect } from 'react'
import { useI18n } from '@/locales/client'
import Slider, { Settings } from "react-slick";
import "slick-carousel/slick/slick.css";
import "slick-carousel/slick/slick-theme.css";
import style from 'public/assets/css/order.module.scss'
import Cookies from "js-cookie";
import { api } from "@/utils/axios";
import "react-datepicker/dist/react-datepicker.css";
import Navbar from "../../../components/layouts/profile/navbar";
import moment from "moment";
import variables from '/public/assets/css/function-page.module.scss';
import { rootChangeInCart, changeRootCartTotalPrice, rootCartTotalDiscount, rootCartDatetime, addStepRoot,
    changeRootInvalidProductIds, removeCouponFromCart, rootCartNote, rootCartRedeemId , addCouponToCart, addGroupOrderSelectedNow , addPaymentMethodToCart } from '@/redux/slices/cartSlice'
import { useAppDispatch } from '@/redux/hooks'
import MenuPlus from "@/app/[locale]/components/menu/menu-plus";
import { useAppSelector } from '@/redux/hooks'
import { useGetWorkspaceDataByIdQuery } from '@/redux/services/workspace/workspaceDataApi'
import Footer from "@/app/[locale]/components/menu/footer";
import {useRouter} from "next/navigation";

export default function Successed({ id }: { id: number }) {
    const workspaceId = useAppSelector((state) => state.workspaceData.globalWorkspaceId)
    const { data: apiDataToken } = useGetWorkspaceDataByIdQuery({id: workspaceId})
    const apiData = apiDataToken?.data?.setting_generals;
    const color = useAppSelector((state) => state.workspaceData.globalWorkspaceColor)
    const trans = useI18n();
    const tokenLogin = Cookies.get('loggedToken');
    const [dataOrder, setDataOrder] = useState<any>([]);
    const dispatch = useAppDispatch();
    const [isLargeScreen, setIsLargeScreen] = useState(window.innerWidth >= 1280);
    const router = useRouter();

    dispatch(rootChangeInCart(null));
    dispatch(changeRootCartTotalPrice(null));
    dispatch(rootCartTotalDiscount(null));
    dispatch(changeRootInvalidProductIds(null));
    dispatch(removeCouponFromCart());
    dispatch(addStepRoot(1))
    dispatch(rootCartNote(null));
    dispatch(rootCartRedeemId(null));
    dispatch(rootCartDatetime(null));
    dispatch(addCouponToCart(null));
    dispatch(addGroupOrderSelectedNow(null))
    dispatch(addPaymentMethodToCart(null));
    Cookies.remove('groupOrder')
    const language = Cookies.get('Next-Locale') ?? 'nl';       

    const paymentStatus = [
        trans('unknown'),
        trans('pending'),
        trans('paid'),
        trans('cancelled'),
        trans('failed'),
        trans('expired'),
    ];

    const paymentMethods = [
        trans('online-method'),
        null,
        trans('cash'),
        trans('on-invoice')
    ];

    const settings: Settings = {
        dots: true,
        infinite: true,
        speed: 500,
        slidesToShow: 1,
        slidesToScroll: 1,
        arrows: false,
        className: "cart-custom-carousel"
    };

    const formatDate = (time?: string, timeFormat?: string, outputFormat?: string) => {
        let hourOffset = new Date().getTimezoneOffset() / 60;

        if (hourOffset < 0) {
            return moment(time, timeFormat).add(-hourOffset, 'hours').format(outputFormat);
        } else {
            return moment(time, timeFormat).subtract(hourOffset, 'hours').format(outputFormat);
        }
    }

    useEffect(() => {
        const handleResize = () => {
            setIsLargeScreen(window.innerWidth >= 1280);
        };

        window.addEventListener('resize', handleResize);
        return () => {
            window.removeEventListener('resize', handleResize);
        };
    }, []);

    useEffect(() => {
        const fetchData = async () => {
            const res = await api.get(`/orders/${id}/detail`, {
                headers: {
                    Authorization: `Bearer ${tokenLogin}`,
                    'Content-Language': language
                }
            });

            setDataOrder(res?.data?.data);

            if (res?.data?.data?.payment_method == 0) {
                const order = await api.put(`/orders/${id}/update_payment`, {
                    payment_method: Number(res?.data?.data?.payment_method),
                    payment_status: 2,
                    total_paid: Number(res?.data?.data?.total_price).toFixed(2),
                }, {
                    headers: {
                        Authorization: `Bearer ${tokenLogin}`,
                        'Content-Language': language
                    }
                });

                setDataOrder(order?.data?.data);
            }
        }

        fetchData();
    }, [
        id
    ]);

    const getMobileOperatingSystem = () => {
        const userAgent = navigator.userAgent || navigator.vendor || (window as any).opera;
    
        if (/android/i.test(userAgent)) {
          return 'Android';
        }
    
        if (/iPad|iPhone|iPod/.test(userAgent) && !(window as any).MSStream) {
          return 'iOS';
        }
    
        return 'unknown';
    };
    
    useEffect(() => {
        const device = getMobileOperatingSystem();
        const query = new URLSearchParams(window.location.search);
        const orderId = query.get('order_id') ?? 0;
        const nonDeeplink = query.get('non_deeplink') ?? 0;
        const isApp = query.get('origin') ?? 'app';
        let paramString = window.location.search;

        const fetchDeeplinkConfig = async () => {
            const callDeeplinkConfig = await api.get('/deeplink/configuration' + paramString);            
            const response = callDeeplinkConfig?.data?.data;
            const fallbackUrl = window.location.href + '&non_deeplink=1';

            if (response && response?.deeplink) {
                paramString += '&screen=payment_success';

                if (device === 'Android' && response?.deeplink?.android) {
                    router.push(response?.deeplink?.android + paramString);
        
                    setTimeout(() => {
                        router.push(fallbackUrl);
                    }, 1000);
                } else if (device === 'iOS' && response?.deeplink?.ios) {
                    router.push(response?.deeplink?.ios + paramString);

                    setTimeout(() => {
                        router.push(fallbackUrl);
                    }, 1000);
                }                   
            }
        }

        if(orderId && !nonDeeplink && isApp === 'app') {
            fetchDeeplinkConfig();
        }       
    }, []);

    return (
        <>
            {isLargeScreen ? (
                <>
                    <MenuPlus/>
                    <div className={`${style['order-confirm']}`}>
                        <div className={`${style['row-cart-confirmation']} row`}>
                            <div className="col-md-7">
                                <div className={`${style['group-confirmation']} d-flex`}>
                                    {/* SVG on the left */}
                                    <div>
                                        <svg width="208" height="209" viewBox="0 0 208 209" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <g id="check-circle">
                                                <path id="Vector" d="M190.667 96.5261V104.499C190.656 123.188 184.605 141.373 173.414 156.342C162.224 171.311 146.495 182.261 128.573 187.56C110.651 192.859 91.4964 192.223 73.9656 185.746C56.4347 179.269 41.4672 167.299 31.2952 151.621C21.1231 135.942 16.2917 117.396 17.5214 98.7475C18.751 80.099 25.976 62.3476 38.1186 48.1408C50.2612 33.9339 66.671 24.0328 84.9005 19.9141C103.13 15.7954 122.203 17.6797 139.274 25.2861" stroke={ color ?? '#D87833'} strokeWidth="9" strokeLinecap="round" strokeLinejoin="round"/>
                                                <path id="Vector_2" d="M190.667 35.166L104 121.919L78 95.9194" stroke={ color ?? '#D87833'} strokeWidth="9" strokeLinecap="round" strokeLinejoin="round"/>
                                            </g>
                                        </svg>
                                    </div>

                                    {/* Divs on the right */}
                                    <div className={`${style['group-confirmation-gap']} ml-3`}>
                                        <div className={`${style['order-successfully']}`}>
                                            {trans('order-successfully')}
                                        </div>
                                        <div className={`${style['mail-order-successfully']}`}>
                                            {trans('check-mail-order-successfully')}
                                        </div>
                                        <div
                                            className={`${style['back-btn']}`}
                                            onClick={() => { window.location.href = '/category/products' }}
                                            style={{ background: color ?? '#D87833' }}
                                        >
                                            {trans('back-to-assortment')}
                                        </div>
                                    </div>
                                </div>
                                <div className={`${style['slide-order-information']}`}>
                                    <div className={`${style['slide-card-information']}`}>
                                        <div>
                                            <div className={`${style['card-title-information']}`}>
                                                {
                                                    !dataOrder?.group_id ? trans('details-of-order') + ' #' + dataOrder?.code
                                                        : trans('details-of-order') + ' #G' + dataOrder?.code + (dataOrder?.extra_code ? `-${dataOrder?.extra_code}` : '')
                                                }
                                            </div>
                                            <div className={style['datetime-information']}>
                                                {formatDate(dataOrder?.date_time, 'YYYY-MM-DD hh:mm:ss', 'DD/MM/YYYY [' + trans('at') + '] HH:mm')}
                                            </div>
                                            <div className={`${style['card-body-information']}`}>
                                                <div className={`${style['card-items-information']} row`}>
                                                    <div className={`d-flex justify-content-between`}>
                                                        <div className={`${style['card-body-information-title']} col-sm-6 col-6`}>
                                                            {trans('payment-status')}:
                                                        </div>
                                                        <div className={`${style['card-body-information-description']} col-sm-6 col-6`}>
                                                            {dataOrder?.status ? paymentStatus[dataOrder?.status] : paymentStatus[0]}
                                                        </div>
                                                    </div>
                                                    <div className={`d-flex justify-content-between`}>
                                                        <div className={`${style['card-body-information-title']} col-sm-6 col-6`}>
                                                            {trans('payment-method')}:
                                                        </div>
                                                        <div className={`${style['card-body-information-description']} col-sm-6 col-6`}>
                                                            {dataOrder?.payment_method == 0 ? (paymentMethods[0]) : dataOrder?.payment_method ? paymentMethods[dataOrder?.payment_method] : ''}
                                                        </div>
                                                    </div>
                                                    {dataOrder?.note && (
                                                        <>
                                                            <div className={`${style['card-body-information-title']} col-sm-6 col-6`}>
                                                                {trans('comments')}:
                                                            </div>
                                                            <div className={`${style['card-body-information-description']} col-sm-6 col-6`}>
                                                                {dataOrder?.note}
                                                            </div>
                                                        </>
                                                    )}
                                                    {
                                                        (dataOrder?.type === 0 && dataOrder?.group === null)
                                                            ? (
                                                                <div className={`d-flex justify-content-between`}>
                                                                    <div className={`${style['card-body-information-title']} ${variables.additionInfo}`}>
                                                                        {trans('order-type')}:
                                                                    </div>
                                                                    <div className={`${style['card-body-information-description']} ${variables.additionInfoValue}`}>
                                                                        {trans('take-out')}
                                                                    </div>
                                                                </div>)
                                                            : (dataOrder?.type === 0 && dataOrder?.group !== null)
                                                                ?
                                                                (
                                                                    <>
                                                                        <div className={`d-flex justify-content-between`}>
                                                                            <div className={`${style['card-body-information-title']} ${variables.additionInfo}`}>
                                                                                {trans('group')}:
                                                                            </div>
                                                                            <div className={`${style['card-body-information-description']} ${variables.additionInfoValue}`}>
                                                                                {dataOrder?.group?.name}
                                                                            </div>
                                                                        </div>
                                                                    </>
                                                                )
                                                                : dataOrder?.type === 1 && dataOrder?.group === null
                                                                    ?
                                                                    (
                                                                        <div className={`d-flex justify-content-between`}>
                                                                            <div className={`${style['card-body-information-title']} ${variables.additionInfo}`}>
                                                                                {trans('address')}:
                                                                            </div>
                                                                            <div
                                                                                className={`${style['card-body-information-description']} ${variables.additionInfoValue}`}
                                                                                style={{ width: '300px' }}
                                                                            >
                                                                                {dataOrder?.address}
                                                                            </div>
                                                                        </div>)
                                                                    : (dataOrder?.type === 1 && dataOrder?.group !== null)
                                                                        ?
                                                                        (
                                                                            <>
                                                                                <div className={`d-flex justify-content-between`}>
                                                                                    <div className={`${style['card-body-information-title']} ${variables.additionInfo}`}>
                                                                                        {trans('address')}:
                                                                                    </div>
                                                                                    <div className={`${style['card-body-information-description']} ${variables.additionInfoValue}`}>
                                                                                        {dataOrder?.group?.address_display}
                                                                                    </div>
                                                                                </div>
                                                                                <div className={`d-flex justify-content-between`}>
                                                                                    <div className={`${style['card-body-information-title']} ${variables.additionInfo}`}>
                                                                                        {trans('group')}:
                                                                                    </div>
                                                                                    <div className={`${style['card-body-information-description']} ${variables.additionInfoValue}`}>
                                                                                        {dataOrder?.group?.name}
                                                                                    </div>
                                                                                </div>
                                                                            </>
                                                                        )
                                                                        : dataOrder?.type === 2
                                                                            ? ""
                                                                            : ""
                                                    }
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div className="col-md-5">
                                <div className={`${style['slide-order-billing']}`}>
                                    <div className={`${style['slide-card-billing']}`}>
                                        <div>
                                            <div className={`${style['card-title-billing']}`}>
                                                {trans('ordered-products')}
                                            </div>
                                            { dataOrder?.items?.length > 0 && (
                                                <div className={`${style['card-body-billing']}`}>
                                                    <div className={`${style['card-items-billing']}`}>
                                                        {
                                                            dataOrder?.items?.map((item: any, index: number) => (
                                                                <div className={`detail-item `} key={index}>
                                                                    <div className={`row ${style['main-item-billing']}`}>
                                                                        <div className={`col-sm-1 col-1 ${style['item-quantity-billing']}`}>
                                                                            {item?.quantity}
                                                                        </div>
                                                                        <div className={`col-sm-5 col-5 ${style['item-name-billing']}`}>
                                                                            {item?.product?.name}
                                                                        </div>
                                                                        <div className={`col-sm-6 col-6`}>
                                                                            <div className={`${style['item-price-billing']}`} style={{ color: color ?? '#D87833' }}>
                                                                                €
                                                                                <span>{(item?.subtotal)}</span>
                                                                            </div>
                                                                        </div>
                                                                    </div>

                                                                    { item?.options?.length > 0 && (
                                                                        <div className={`row ${style['sub-item-billing']}`}>
                                                                            {
                                                                                item?.options?.map((option: any, index: number) => (
                                                                                    <>
                                                                                        <div key={index} className={`row ${style['sub-options-billing']}`}>
                                                                                            <div className={`col-sm-8 col-8`}>
                                                                                                <div className={`${style['option-name-billing']}`}>
                                                                                                    {
                                                                                                        option?.option?.is_ingredient_deletion == true ? (" " + trans('with-out')) : null
                                                                                                    }
                                                                                                    {
                                                                                                        option?.option_items?.length > 0 && (
                                                                                                            option?.option_items
                                                                                                                .map((optionItem: any) => optionItem?.option_item.name)
                                                                                                                .join(", ")
                                                                                                        )
                                                                                                    }
                                                                                                </div>
                                                                                            </div>
                                                                                        </div>
                                                                                    </>
                                                                                ))
                                                                            }
                                                                        </div>
                                                                    )}
                                                                    <div className={`${style['line-break-billing']}`}>
                                                                        <div className={`${style['line-order-billing']}`}>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            ))
                                                        }
                                                    </div>
                                                </div>
                                            )}
                                            <div className={`${style['card-footer-billing']}`}>
                                                <div className={`${style['sub-prices-billing']} row`}>
                                                    {
                                                        (dataOrder?.coupon_discount && dataOrder?.coupon_discount > 0)
                                                        || (dataOrder?.redeem_discount && dataOrder?.redeem_discount > 0)
                                                        || (dataOrder?.group_discount && dataOrder?.group_discount > 0)
                                                        || (dataOrder?.ship_price && Number(dataOrder?.ship_price) > 0)
                                                        || (dataOrder?.service_cost && Number(dataOrder?.service_cost) > 0) ? (
                                                            <>
                                                                <div className={`${style['sub-price-label']} col-sm-6 col-6`}>
                                                                    {trans('subtotal')}:
                                                                </div>
                                                                <div className={`${style['sub-price']} col-sm-6 col-6`}>
                                                                    €
                                                                    {dataOrder?.subtotal}
                                                                </div>
                                                            </>
                                                        ) : null}

                                                    {
                                                        (dataOrder?.ship_price && Number(dataOrder?.ship_price) > 0) && (
                                                            <>
                                                                <div className={`${style['sub-price-label']} col-sm-6 col-6`}>
                                                                    {trans('delivery-cost')}:
                                                                </div>
                                                                <div className={`${style['sub-price']} col-sm-6 col-6`}>
                                                                    €
                                                                    {dataOrder?.ship_price}
                                                                </div>
                                                            </>
                                                        )
                                                    }

                                                    {
                                                        (dataOrder?.service_cost && Number(dataOrder?.service_cost) > 0) && (
                                                            <>
                                                                <div className={`${style['sub-price-label']} col-sm-6 col-6`}>
                                                                    {trans('cart.service_cost')}:
                                                                </div>
                                                                <div className={`${style['sub-price']} col-sm-6 col-6`}>
                                                                    €
                                                                    {dataOrder?.service_cost}
                                                                </div>
                                                            </>
                                                        )
                                                    }

                                                    {
                                                        (dataOrder?.coupon_discount && dataOrder?.coupon_discount > 0) && (<>
                                                            <div className={`${style['sub-price-label']} col-sm-6 col-6`}>
                                                                {trans('coupon-discount')}:
                                                            </div>
                                                            <div className={`${style['sub-price']} col-sm-6 col-6`}>
                                                                -
                                                                €
                                                                {dataOrder?.coupon_discount}
                                                            </div>
                                                        </>)
                                                    }

                                                    {
                                                        (dataOrder?.redeem_discount && dataOrder?.redeem_discount > 0) && (<>
                                                            <div className={`${style['sub-price-label']} col-sm-6 col-6`}>
                                                                {trans('redeem-discount')}:
                                                            </div>
                                                            <div className={`${style['sub-price']} col-sm-6 col-6`}>
                                                                -
                                                                €
                                                                {dataOrder?.redeem_discount}
                                                            </div>
                                                        </>)
                                                    }

                                                    {
                                                        (dataOrder?.group_discount && dataOrder?.group_discount > 0) && (<>
                                                            <div className={`${style['sub-price-label']} col-sm-6 col-6`}>
                                                                {trans('group-discount')}:
                                                            </div>
                                                            <div className={`${style['sub-price']} col-sm-6 col-6`}>
                                                                -
                                                                €
                                                                {dataOrder?.group_discount}
                                                            </div>
                                                        </>)
                                                    }
                                                </div>
                                            </div>
                                            <div className={`${style['line-break']}`}>
                                                <div className={`${style['line-order']}`}>
                                                </div>
                                            </div>
                                            <div className={`${style['total-prices-billing']} row`}>
                                                <div className={`${style['main-price-label']} col-sm-6 col-6`}>
                                                    {trans('total')}
                                                </div>
                                                <div className={`${style['main-price']} col-sm-6 col-6`} style={{ color: color ?? '#D87833' }}>
                                                    €
                                                    {dataOrder?.total_price}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div className="text-center" style={{ height: '100px' }}>
                            <Footer trans={trans} />
                        </div>
                    </div>
                </>
            ) : (
                <div className={`${style['order-confirm']}`}>
                    <div className={`row`}>
                        <Navbar content={trans('passed')} background={color} />
                    </div>
                    <div className={`row`}>
                        <div className={`col-sm-12 col-12`}>
                            <div className={`${style['group-confirmation']} text-center`}>
                                <svg xmlns="http://www.w3.org/2000/svg" width="111" height="101" viewBox="0 0 111 101" fill="none">
                                    <path d="M15.6928 38.3904C14.5978 42.3356 14.0445 46.4087 14.0476 50.5C14.0476 75.6297 34.6312 96 60.0239 96C85.4165 96 106 75.6297 106 50.5C106 25.3704 85.4204 5.00004 60.0239 5.00004C47.9292 4.98448 36.3186 9.70078 27.7227 18.121" stroke={color} strokeWidth="10" strokeMiterlimit="22.93" strokeLinejoin="round" />
                                    <path fillRule="evenodd" clipRule="evenodd" d="M0 49.0887H29.434L14.7177 30.5112L0 49.0887Z" fill={color} />
                                    <path d="M38.793 43.8033L60.1159 70.2686L102.826 11.9746" stroke="white" strokeWidth="10" strokeMiterlimit="22.93" strokeLinejoin="round" />
                                    <path d="M38.793 43.8033L60.1159 70.2686L102.826 11.9746" stroke={color} strokeWidth="10" strokeMiterlimit="22.93" strokeLinejoin="round" />
                                </svg>
                                <div className={`${style['order-successfully']}`}>
                                    {trans('order-successfully')}
                                </div>
                                <div className={`${style['mail-order-successfully']}`}>
                                    {trans('check-mail-order-successfully')}
                                </div>
                                <div className={`${style['back-btn']}`} onClick={() => { window.location.href = '/category/products' }}>
                                    {trans('back-to-assortment')}
                                </div>
                            </div>
                        </div>
                        <div className="col-sm-12 col-12" >
                            <div className={`${style['slide-order']}`}>
                                <div className={`${style['slide-card']}`}>
                                    <Slider {...settings}>
                                        <div>
                                            <div className={`${style['card-title']}`}>
                                                {trans('ordered-products')}
                                            </div>
                                            <div className={`${style['name-store']}`} style={{ color: color }}>
                                                {dataOrder?.workspace?.name}
                                            </div>
                                            <div className={`${style['card-body']}`}>
                                                <div className={`${style['card-items']}`}>
                                                    {
                                                        dataOrder?.items?.map((item: any, index: number) => (
                                                            <div className={`detail-item `} key={index}>
                                                                <div className={` row ${style['main-item']}`}>
                                                                    <div className={`col-sm-8 col-8 ${style['item-quantity']}`}>
                                                                        {item?.quantity}
                                                                        <span className={`${style['item-name']}`}>
                                                                            {' x ' + item?.product?.name}
                                                                        </span>
                                                                    </div>
                                                                    <div className={`col-sm-4 col-4`}>
                                                                        <div className={`${style['item-price']}`}>
                                                                            €
                                                                            <span>{(item?.subtotal)}</span>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                {item?.options?.map((option: any, index: number) => (
                                                                    <>
                                                                        <div key={index} className={`row ${style['sub-options']}`}>
                                                                            <div className={`col-sm-8 col-8`}>
                                                                                <div className={`${style['option-name']}`}>
                                                                                    - {
                                                                                    option?.option?.is_ingredient_deletion == true ? (" " + trans('with-out')) : null
                                                                                }
                                                                                    {
                                                                                        option?.option_items?.length > 0 && (
                                                                                            option?.option_items
                                                                                                .map((optionItem: any) => optionItem?.option_item.name)
                                                                                                .join(", ")
                                                                                        )
                                                                                    }
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </>

                                                                ))}
                                                                <div className={`${style['line-break']}`}>
                                                                    <div className={`${style['line-order']}`}>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        ))
                                                    }
                                                </div>
                                            </div>
                                            <div className={`${style['card-footer']}`}>
                                                <div className={`${style['sub-prices']}  row`}>
                                                    {
                                                        (dataOrder?.coupon_discount && dataOrder?.coupon_discount > 0)
                                                        || (dataOrder?.redeem_discount && dataOrder?.redeem_discount > 0)
                                                        || (dataOrder?.group_discount && dataOrder?.group_discount > 0)
                                                        || (dataOrder?.ship_price && Number(dataOrder?.ship_price) > 0)
                                                        || (dataOrder?.service_cost && Number(dataOrder?.service_cost) > 0) ? (
                                                            <>
                                                                <div className={`${style['sub-price-label']} col-sm-6 col-6`}>
                                                                    {trans('subtotal')}:
                                                                </div>
                                                                <div className={`${style['sub-price']} col-sm-6 col-6`}>
                                                                    €
                                                                    {dataOrder?.subtotal}
                                                                </div>
                                                            </>
                                                        ) : null}

                                                    {
                                                        (dataOrder?.ship_price && Number(dataOrder?.ship_price) > 0) && (<>
                                                            <div className={`${style['sub-price-label']} col-sm-6 col-6`}>
                                                                {trans('delivery-cost')}:
                                                            </div>
                                                            <div className={`${style['sub-price']} col-sm-6 col-6`}>
                                                                €
                                                                {dataOrder?.ship_price}
                                                            </div>
                                                        </>)
                                                    }

                                                    {
                                                        (dataOrder?.service_cost && Number(dataOrder?.service_cost) > 0) && (<>
                                                            <div className={`${style['sub-price-label']} col-sm-6 col-6`}>
                                                                {trans('cart.service_cost')}:
                                                            </div>
                                                            <div className={`${style['sub-price']} col-sm-6 col-6`}>
                                                                €
                                                                {dataOrder?.service_cost}
                                                            </div>
                                                        </>)
                                                    }

                                                    {
                                                        (dataOrder?.coupon_discount && dataOrder?.coupon_discount > 0) && (<>
                                                            <div className={`${style['sub-price-label']} col-sm-6 col-6`}>
                                                                {trans('coupon-discount')}:
                                                            </div>
                                                            <div className={`${style['sub-price']} col-sm-6 col-6`}>
                                                                -
                                                                €
                                                                {dataOrder?.coupon_discount}
                                                            </div>
                                                        </>)
                                                    }

                                                    {
                                                        (dataOrder?.redeem_discount && dataOrder?.redeem_discount > 0) && (<>
                                                            <div className={`${style['sub-price-label']} col-sm-6 col-6`}>
                                                                {trans('redeem-discount')}:
                                                            </div>
                                                            <div className={`${style['sub-price']} col-sm-6 col-6`}>
                                                                -
                                                                €
                                                                {dataOrder?.redeem_discount}
                                                            </div>
                                                        </>)
                                                    }

                                                    {
                                                        (dataOrder?.group_discount && dataOrder?.group_discount > 0) && (<>
                                                            <div className={`${style['sub-price-label']} col-sm-6 col-6`}>
                                                                {trans('group-discount')}:
                                                            </div>
                                                            <div className={`${style['sub-price']} col-sm-6 col-6`}>
                                                                -
                                                                €
                                                                {dataOrder?.group_discount}
                                                            </div>
                                                        </>)
                                                    }
                                                    <div className={`${style['main-price-label']} col-sm-6 col-6`}>
                                                        {trans('total')}:
                                                    </div>
                                                    <div className={`${style['main-price']} col-sm-6 col-6`}>
                                                        €
                                                        {dataOrder?.total_price}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div >
                                            <div className={`${style['card-title']}`}>
                                                {
                                                    !dataOrder?.group_id ? trans('details-of-order') + ' #' + dataOrder?.code
                                                        : trans('details-of-order') + ' #G' + dataOrder?.code + (dataOrder?.extra_code ? `-${dataOrder?.extra_code}` : '')
                                                }
                                            </div>
                                            <div className={`${style['name-store']}`} style={{ color: apiData ? apiData?.primary_color : '' }}>
                                                {dataOrder?.workspace?.name.toUpperCase()}
                                            </div>
                                            <div className={style['datetime']}>
                                                {formatDate(dataOrder?.date_time, 'YYYY-MM-DD hh:mm:ss', 'DD/MM/YYYY [' + trans('at') + '] HH:mm')}
                                            </div>
                                            <div className={`${style['card-body']}`}>
                                                <div className={`${style['card-contents']} row`}>
                                                    <div className={`${style['card-label']} col-sm-6 col-6`}>
                                                        {trans('payment-status')}:
                                                    </div>
                                                    <div className={`${style['card-content']} col-sm-6 col-6`}>
                                                        {dataOrder?.status ? paymentStatus[dataOrder?.status] : paymentStatus[0]}
                                                    </div>
                                                    <div className={`${style['card-label']} col-sm-6 col-6`}>
                                                        {trans('payment-method')}:
                                                    </div>
                                                    <div className={`${style['card-content']} col-sm-6 col-6`}>
                                                        {dataOrder?.payment_method == 0 ? (paymentMethods[0]) : dataOrder?.payment_method ? paymentMethods[dataOrder?.payment_method] : ''}
                                                    </div>
                                                    {dataOrder?.note && (
                                                        <>
                                                            <div className={`${style['card-label']} col-sm-6 col-6`}>
                                                                {trans('comments')}:
                                                            </div>
                                                            <div className={`${style['card-content']} col-sm-6 col-6`}>
                                                                {dataOrder?.note}
                                                            </div>
                                                        </>
                                                    )}
                                                    {
                                                        (dataOrder?.type === 0 && dataOrder?.group === null)
                                                            ? (
                                                                <div className={` d-flex justify-content-between`}>
                                                                    <div className={variables.additionInfo}>{trans('order-type')}:</div>
                                                                    <div className={variables.additionInfoValue}>{trans('take-out')}</div>
                                                                </div>)
                                                            : (dataOrder?.type === 0 && dataOrder?.group !== null)
                                                                ?
                                                                (
                                                                    <>
                                                                        <div className={` d-flex justify-content-between`}>
                                                                            <div className={variables.additionInfo}>{trans('group')}:</div>
                                                                            <div className={variables.additionInfoValue}>{dataOrder?.group?.name}</div>
                                                                        </div>
                                                                    </>
                                                                )
                                                                : dataOrder?.type === 1 && dataOrder?.group === null
                                                                    ?
                                                                    (
                                                                        <div className={` d-flex justify-content-between`}>
                                                                            <div className={variables.additionInfo}>{trans('address')}:</div>
                                                                            <div className={variables.additionInfoValue}>{dataOrder?.address}</div>
                                                                        </div>)
                                                                    : (dataOrder?.type === 1 && dataOrder?.group !== null)
                                                                        ?
                                                                        (
                                                                            <>
                                                                                <div className={` d-flex justify-content-between`}>
                                                                                    <div className={variables.additionInfo}>{trans('address')}:</div>
                                                                                    <div className={variables.additionInfoValue}>{dataOrder?.group?.address_display}</div>
                                                                                </div>
                                                                                <div className={` d-flex justify-content-between`}>
                                                                                    <div className={variables.additionInfo}>{trans('group')}:</div>
                                                                                    <div className={variables.additionInfoValue}>{dataOrder?.group?.name}</div>
                                                                                </div>
                                                                            </>
                                                                        )
                                                                        : dataOrder?.type === 2
                                                                            ? ""
                                                                            : ""
                                                    }
                                                </div>
                                            </div>
                                        </div>
                                    </Slider>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            )}
        </>
    );
}