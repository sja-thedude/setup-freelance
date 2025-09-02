'use client'

import Loading from "@/app/[locale]/components/loading";
import UserWebsiteCart from "@/app/[locale]/components/ordering/cart/userWebsiteCart";
import { useI18n } from '@/locales/client';
import { useAppDispatch, useAppSelector } from '@/redux/hooks';
import { useGetWorkspaceDataByIdQuery } from '@/redux/services/workspace/workspaceDataApi';
import { addCouponToCart, addStepRoot, getCart } from '@/redux/slices/cartSlice';
import { api } from "@/utils/axios";
import { hexToRgb } from "@/utils/rgb";
import 'bootstrap/dist/css/bootstrap.css';
import Cookies from 'js-cookie';
import _ from "lodash";
import moment from "moment";
import Image from "next/image";
import style from 'public/assets/css/loyalty.module.scss';
import { useEffect, useMemo, useState } from 'react';
import ProgressBar from 'react-bootstrap/ProgressBar';
import { useSelector } from "react-redux";
import { Slide, ToastContainer, toast } from 'react-toastify';
import 'react-toastify/dist/ReactToastify.css';
import { useDebounce } from "use-debounce";
import cartStyle from "../../../../../public/assets/css/cart.module.scss";
import NotFound from "../../components/404/not-found";
import Navbar from "../../components/layouts/loyalty/navbar";
import Footer from "../../components/menu/footer";
import Menu from "../../components/menu/menu-plus";

export default function Loyalty() {
    const workspaceId = useAppSelector((state:any) => state.workspaceData.globalWorkspaceId)
    const { data: apiDataToken } = useGetWorkspaceDataByIdQuery({id: workspaceId})
    const workspaceInfo = apiDataToken?.data;
    const apiData = workspaceInfo?.setting_generals;
    const color = useAppSelector((state) => state.workspaceData.globalWorkspaceColor)
    let rgbColor = hexToRgb('#FFFFFF')

    if (color) {
        rgbColor = hexToRgb(color);
    }
    const trans = useI18n();
    const tokenLoggedInCookie = Cookies.get('loggedToken');
    var cart = useSelector(getCart);
    const dispatch = useAppDispatch()
    const [loyaltyData, setLoyaltyData] = useState<any>([]);
    const [activeCard, setActiveCard] = useState(0);
    const [fetchData, setFetchData] = useState(0);
    const [isErrorMessage, setIsErrorMessage] = useState(true);
    const [activeRedeem, setActiveRedeem] = useState<any>([]);
    const [dataSuccess, setDataSuccess] = useState<any>(null);
    const [errorMessage, setErrorMessage] = useState<any | null>(null);
    const [isLoading, setIsLoading] = useDebounce(true, 400);
    const heightCard = window.innerHeight - 460 < 320 ? 320 : window.innerHeight - 460;
    const heightBar = heightCard + 80;
    const isAdd = window.innerHeight - 460 < 320 ? false : true;
    const point = loyaltyData?.point;
    const [allowClick, setAllowClick] = useState(true);
    const [enableColor, setEnableColor] = useState(false);
    const [title, setTitle] = useState('');
    var [isDeliveryType, setIsDeliveryType] = useState(false);
    const buttonStatus = (reward:any) => {
        let allowClick = true;
        let enableColor = false;
        let title = '';
        const score = reward?.score;
        if (score > point) {
            allowClick = false;
            enableColor = false;
            title = `${score - point} ${trans('credits-required')}`;

            //REWARD_TYPE.DISCOUNT
            if (reward?.type === 1) {
                if (reward?.is_redeem) {
                    title = trans('redeemed');
                    allowClick = true;
                } else if (reward?.is_used) {
                    title = reward?.repeat ? title : trans('redeemed');
                }
            } else {
                if (reward?.is_redeem) {
                    title = trans('redeemed');
                    allowClick = true;
                }
            }
        } else {
            if (reward?.is_redeem) {
                //REWARD_TYPE.PHYSICAL_GIFT
                if (reward?.type === 2) {
                    title = reward?.repeat ? trans('redeem') : trans('redeemed');
                    enableColor = !!reward?.repeat;
                } else {
                    title = trans('redeemed');
                    enableColor = false;
                }
            } else {
                if (reward?.type === 2) {
                    title = trans('redeem');
                    enableColor = true;
                } else {
                    if (reward?.repeat) {
                        title = trans('redeem');
                        enableColor = true;
                    } else {
                        title = reward?.is_used ? trans('redeemed') : trans('redeem');
                        enableColor = !reward?.is_used;
                        allowClick = !reward?.is_used;
                    }
                }
            }
        }

        return { title, allowClick, enableColor };
    }
    useEffect(() => {
        const fetchDataLoyalty = async () => {
            if (workspaceId) {
                const res = await api.get(`workspaces/${workspaceId}/loyalties/of_workspace`, {
                    headers: {
                        Authorization: `Bearer ${tokenLoggedInCookie}`,
                    }
                });
                const loyaltyDatas = res?.data?.data;
                if (loyaltyDatas) {
                    setLoyaltyData(loyaltyDatas);
                    if (activeCard == 0) {
                        if (loyaltyDatas?.reward) {
                            setActiveRedeem(loyaltyDatas?.reward);
                            loyaltyDatas?.rewards?.filter((reward: any, key: number) => {
                                if (reward?.id == loyaltyDatas?.reward?.id) {
                                    setActiveCard(key);
                                }
                            });
                        } else if (loyaltyDatas?.rewards?.[0]) {
                            setActiveCard(0);
                            setActiveRedeem(loyaltyDatas?.rewards?.[0]);
                        }
                    } else {
                        setActiveRedeem(loyaltyDatas?.rewards?.[activeCard]);
                    }
                    setIsLoading(false);
                }
            }
        }

        fetchDataLoyalty();
    }, [workspaceId, fetchData])
    useEffect(() => {
        if (activeRedeem) {
            const currentdata = buttonStatus(activeRedeem);
            setAllowClick(currentdata.allowClick);
            setEnableColor(currentdata.enableColor);
            setTitle(currentdata.title);
        }
    }, [point, activeRedeem?.is_redeem, activeRedeem?.is_used, activeRedeem?.repeat, activeRedeem?.type, trans]);
    const showCheckIcon = useMemo(() => {
        if (activeRedeem?.type == 2) {
            if (activeRedeem?.repeat == 1) {
                if (dataSuccess) {
                    return false;
                } else {
                    return false;
                }
            } else {
                if (activeRedeem?.last_redeem_history) {
                    return true;
                } else {
                    return false;
                }
            }
        } else {
            return false;
        }
    }, [activeRedeem, dataSuccess]);

    const activeImgs = useMemo(() => {
        return loyaltyData?.workspace?.gallery?.filter((item: any) => item.active === 1) ?? [];
    }, [
        loyaltyData?.workspace?.gallery
    ]);

    const handleActiveCard = (index:number) => {
        setActiveCard(index);
        setActiveRedeem(loyaltyData?.rewards?.[index]);
    }

    const language = Cookies.get('Next-Locale');
    const formatDate = (time?: string, timeFormat?: string, outputFormat?: string) => {
        let hourOffset = new Date().getTimezoneOffset() / 60;

        if (hourOffset < 0) {
            return moment(time, timeFormat).add(-hourOffset, 'hours').format(outputFormat);
        } else {
            return moment(time, timeFormat).subtract(hourOffset, 'hours').format(outputFormat);
        }
    }

    let cartCoupon: any = useAppSelector((state) => state.cart.coupon);
    const [isExistRedeem, setIsExistRedeem] = useState(false);
    const [currentRedeemId, setCurrentRedeemId] = useState(0);
    const handleRedeem = (id:number, type:number) => {
        if (workspaceId) {
            setCurrentRedeemId(id);
            setIsErrorMessage(true)
            setErrorMessage(null);
            const res = api.post(`loyalties/${loyaltyData?.id}/redeem/${id}`, {},{
                headers: {
                    Authorization: `Bearer ${tokenLoggedInCookie}`,
                    'Content-Language': language,
                }
            }).then((res) => {
                if (type == 1) {
                    const cartProductIds = _.map(cart?.rootData, 'productId').map(i => Number(i))
                    if (cartProductIds?.length > 0) {
                        const res = api.get(`workspaces/${workspaceId}/rewards/${id}/validate_products?product_id[]=${cartProductIds.toString()}`, {
                            headers: {
                                Authorization: `Bearer ${tokenLoggedInCookie}`,
                            }
                        }).then((res) => {
                            const validateDatas = res?.data;
                            if (validateDatas?.data?.[cartProductIds.toString()]) {
                                setErrorMessage(trans('auto-deducted-shopping-cart'));
                                if (!cartCoupon || !cartCoupon.code) {
                                    loyaltyData?.rewards?.filter((reward: any, key: number) => {
                                        if (reward?.id == id) {
                                            setIsExistRedeem(true);
                                            dispatch(addCouponToCart(reward));
                                        }
                                    });
                                }
                            } else {
                                setErrorMessage(trans('applied-to-next-order'));
                            }
                        }).catch((err) => {
                            setErrorMessage(trans('applied-to-next-order'));
                        });
                    } else {
                        setErrorMessage(trans('applied-to-next-order'));
                    }
                } else {
                    if (window.innerWidth < 1280) {
                        const data = {
                            'email': res?.data?.data?.user?.email,
                            'created_at': res?.data?.data?.reward?.last_redeem_history?.created_at,
                        }
                        setDataSuccess(data);
                        document.getElementById('open-modal')?.click();
                    } else {
                        setErrorMessage(trans('check-request-gift-email'));
                    }
                }
                setFetchData(fetchData + 1);
            }).catch((err: any) => {
                const data = err?.response?.data;
                setErrorMessage(data?.message);
            });
        }
    }

    useEffect(() => {
        toast.dismiss();
        toast(errorMessage, {
            position: toast.POSITION.BOTTOM_CENTER,
            autoClose: 1500,
            hideProgressBar: true,
            closeOnClick: true,
            closeButton: false,
            transition: Slide,
            className: 'message',
        });
    }, [errorMessage]);

    const [isSortOpen, setIsSortOpen] = useState(false);
    const step = useAppSelector((state) => state.cart.stepRoot)
    const handleActive = (stepActive: number) => {
        dispatch(addStepRoot(stepActive))
    }

    if (!tokenLoggedInCookie) {
        return (<NotFound />);
    }

    const handleBack = () => {
        window.location.href = "/category/products"
    }

    return (
        <>
            {isLoading && <Loading/>}
            <div className="res-mobile">
                <div style={isAdd ? {} : { marginBottom: '50px' }}>
                    <div className={`row`}>
                        <div style={{ position: 'fixed', bottom: 0, left: 0, width: '100%' ,zIndex: 100 }}>
                            <Menu />
                        </div>
                        <Navbar content={apiDataToken ? apiDataToken?.data?.setting_generals?.title : ''} background={ color }/>
                        {(activeRedeem?.photo || activeImgs[0]?.full_path) && (
                            <Image src={activeRedeem?.photo ?? activeImgs[0]?.full_path}
                                   className="px-0"
                                   alt="gallery"
                                   width={0}
                                   height={180}
                                   sizes="100vw"
                                   style={{ width: '100%', height: '180px', borderRadius: '0px 0px 10px 10px' }} />
                        )}

                    </div>
                    <div className={`${style['loyalty-redeem']}`}>
                        {
                            loyaltyData?.rewards?.length > 0 && (
                                <div className="row">
                                    <div className="col-sm-5 col-5">
                                        <div className={style['progress-static']}>
                                            {loyaltyData?.point + '/' + loyaltyData?.highest_point}
                                        </div>
                                        <div className="d-flex">
                                            <div className="loyalty-redeem-bar">
                                                <ProgressBar variant="custom-total-bar" style={isAdd ? { width: heightBar, marginTop: heightBar } : {}}
                                                             now={Number(Math.abs(loyaltyData?.point * 100 / loyaltyData?.highest_point).toFixed(2))}
                                                />
                                            </div>
                                            <div className="loyalty-redeem-bar-detail">
                                                {
                                                    loyaltyData?.rewards?.length > 0 && loyaltyData?.rewards?.map((item:any, index:number) => (
                                                        <div className="progress" key={index}
                                                             style={{ marginTop: ((heightBar*(100-item?.score*100/loyaltyData?.highest_point))/100) + 17, width: heightBar}}>
                                                            <div className="bar" onClick={() => handleActiveCard(index)}>
                                                                <div className="bar-number">
                                                                    {index + 1}
                                                                </div>
                                                                <svg xmlns="http://www.w3.org/2000/svg"
                                                                     viewBox="0 0 35 42" width="35" height="42">
                                                                    <g xmlns="http://www.w3.org/2000/svg" transform="matrix(0 1 -1 0 35 -0)">
                                                                        <circle cx="24.5" cy="17.5" r="17.5"
                                                                                fill={ activeCard == index ? color
                                                                                    : (loyaltyData?.point) < (item?.score) ? "#CCCCCC"
                                                                                        : "#3C3C3C"}/>
                                                                        <path d="M-4.22474e-07 17.9712L10 27.3302L10 8.0001L-4.22474e-07 17.9712Z"
                                                                              fill={ activeCard == index ? color
                                                                                  : (loyaltyData?.point) < (item?.score) ? "#CCCCCC"
                                                                                      : "#3C3C3C"}/>
                                                                    </g>
                                                                </svg>
                                                            </div>
                                                        </div>
                                                    ))
                                                }
                                            </div>
                                        </div>
                                    </div>
                                    <div className="col-sm-7 col-7 ps-0">
                                        <div className={style['loyalty-card']}>
                                            <div className={style['loyalty-card-body']} style={{height: heightCard}}>
                                                <div className={style['loyalty-card-title']}>
                                                    {
                                                        showCheckIcon ? (
                                                            <div className="d-flex">
                                                                <div className="col-sm-10 col-10">
                                                                    {activeRedeem?.title}
                                                                </div>
                                                                <div className="col-sm-2 col-2" style={{ textAlign: "left" }}>
                                                                    <svg width="22" height="22" viewBox="0 0 22 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                        <path d="M21 10.0857V11.0057C20.9988 13.1621 20.3005 15.2604 19.0093 16.9875C17.7182 18.7147 15.9033 19.9782 13.8354 20.5896C11.7674 21.201 9.55726 21.1276 7.53447 20.3803C5.51168 19.633 3.78465 18.2518 2.61096 16.4428C1.43727 14.6338 0.879791 12.4938 1.02168 10.342C1.16356 8.19029 1.99721 6.14205 3.39828 4.5028C4.79935 2.86354 6.69279 1.72111 8.79619 1.24587C10.8996 0.770634 13.1003 0.988061 15.07 1.86572" stroke={color} strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"/>
                                                                        <path d="M21 3.00586L11 13.0159L8 10.0159" stroke={color} strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"/>
                                                                    </svg>

                                                                </div>
                                                            </div>
                                                        ) :(<div>
                                                            {activeRedeem?.title}
                                                        </div>)
                                                    }
                                                </div>
                                                <div className={style['loyalty-card-credit']} style={{ color: color }}>
                                                    {activeRedeem?.score} credits
                                                </div>
                                                <div className={style['loyalty-card-subtitle']}>
                                                    {activeRedeem?.type != 2 && activeRedeem?.description}
                                                </div>
                                                <div className={style['loyalty-card-content']}>
                                                    {activeRedeem?.type == 2
                                                        ? activeRedeem?.description
                                                        : activeRedeem?.discount_type == 1
                                                            ? trans('receive-up-to') + " €" + Math.abs(activeRedeem?.reward).toFixed(2) + " " + trans('discount-next-order')
                                                            : trans('receive-up-to') + " " + Math.abs(activeRedeem?.percentage).toFixed(2) + "% " + trans('discount-next-order')
                                                    }
                                                </div>
                                            </div>
                                            <div className={style['loyalty-card-footer']}
                                                 onClick={
                                                     allowClick ? (()=>handleRedeem(activeRedeem?.id, activeRedeem?.type)) : (() => {})
                                                 }
                                                 style={!enableColor ? { background: "#979797" } : { background: color }}>
                                                {title}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            )
                        }
                    </div>
                    <div className={`${style['profile-info-item']} res-mobile`} id="open-modal" data-bs-toggle="modal" data-bs-target="#confirmModal">
                    </div>
                    <div className="d-flex res-mobile">
                        <div className="modal" id="confirmModal">
                            <div className="modal-dialog">
                                <div className={`modal-content ${style['modal-content-confirm']}`}>
                                    <div className="modal-body pt-0">
                                        <div className="text-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="101" height="3" viewBox="0 0 101 3" fill="none">
                                                <path d="M2 1.5H99" stroke="#E1E1E1" strokeWidth="3" strokeLinecap="round" />
                                            </svg>
                                        </div>
                                        <div className="text-center pt-3">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="106" height="106" viewBox="0 0 106 106" fill="none">
                                                <path d="M103 48.4286V53.0286C102.994 63.8107 99.5025 74.302 93.0466 82.9377C86.5908 91.5735 77.5164 97.891 67.1768 100.948C56.8371 104.005 45.7863 103.638 35.6723 99.9015C25.5584 96.1649 16.9233 89.2591 11.0548 80.2139C5.18633 71.1688 2.39896 60.4689 3.10838 49.7102C3.81781 38.9514 7.98603 28.7102 14.9914 20.514C21.9968 12.3177 31.4639 6.60553 41.9809 4.22935C52.498 1.85317 63.5013 2.9403 73.35 7.32862" stroke="#91A900" strokeWidth="6" strokeLinecap="round" strokeLinejoin="round"/>
                                                <path d="M103 13.0293L53 63.0793L38 48.0793" stroke="#91A900" strokeWidth="6" strokeLinecap="round" strokeLinejoin="round"/>
                                            </svg>
                                        </div>
                                        <div className="p-4">
                                            <div className={style['status-text']}>
                                                { trans('successful-redeem') }
                                            </div>
                                            <div className={style['confirm-text']}>
                                                { trans('request-gift') }
                                            </div>
                                            <div className={style['confirm-text']} style={{ color:color }}>
                                                { formatDate(dataSuccess?.created_at, 'YYYY-MM-DD hh:mm:ss', 'DD/MM/YYYY') + " - " + dataSuccess?.email}
                                            </div>
                                            <div className={style['confirm-text']}>
                                                { trans('check-mail-gift') }
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div className="loyalties-messages res-mobile">
                        <ToastContainer />
                    </div>
                </div>
            </div>
            <div className="res-desktop">
                <Menu />
                <div className="desktop-wrapper">
                    <div className="row mgt-70">
                        <div className="col-12">
                            <div className={style['loyalty-bar-group']}>
                                <div className={style["back-btn"]} onClick= {handleBack} role="button">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="25" viewBox="0 0 24 25" fill="none">
                                        <path d="M14 17L10 12.5L14 8" stroke="#404040" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"/>
                                    </svg>
                                    {trans('back-to-assortment')}
                                </div>
                                <ProgressBar variant="custom-total-bar" bsPrefix="progress-bar loyalty-progress"
                                             label={`${Number(Math.abs((loyaltyData?.point * 100 / loyaltyData?.highest_point)).toFixed(2)) > 100 ? 100 
                                                 : Math.abs((loyaltyData?.point * 100 / loyaltyData?.highest_point)).toFixed(2)
                                                     ? Math.abs((loyaltyData?.point * 100 / loyaltyData?.highest_point)).toFixed(2) : 0}%`}
                                             now={Number(Math.abs((loyaltyData?.point * 100 / loyaltyData?.highest_point)).toFixed(2)) > 100 ? 100 : Number(Math.abs((loyaltyData?.point * 100 / loyaltyData?.highest_point)).toFixed(2))}
                                />
                                <div className={style['progress-static']}>
                                    <span className={style['user-point']} style={{ color: color }}>
                                        {loyaltyData?.point ?? 0}
                                    </span>
                                    <span className={style['highest-point']}>
                                        /{loyaltyData?.highest_point ?? 0} {trans('lang_credits')}
                                    </span>
                                </div>
                            </div>

                        </div>                        
                    </div>
                    <div className="row">
                        <div className="col-12">
                            <div className="loyalty-left row" style={{minHeight: '66vh'}}>
                                {
                                    loyaltyData?.rewards?.length > 0 && loyaltyData?.rewards?.map((item:any, index:number) => {
                                        const currentStatus = buttonStatus(item);
                                        return (
                                            <div className="col-4" key={index} style={{ width: '320px' }}>
                                                <div className={style['loyalty-card']}>
                                                    <div className={style['loyalty-card-body']}>
                                                        <div className={style['loyalty-card-title']}>
                                                            {item?.title}
                                                        </div>
                                                        {
                                                            item?.photo ? (
                                                                <Image src={item?.photo ?? ''}
                                                                    className="px-0"
                                                                    alt="gallery" width={0}
                                                                    height={0}
                                                                    sizes="100vw"
                                                                    style={{ width: '100%', height: '150px', borderRadius: '5px 5px 0px 0px' }} />
                                                            ) : (
                                                                <div className="mb-3"></div>
                                                            )
                                                        }

                                                        <div className={style['loyalty-card-credit']} style={{ color: color }}>
                                                            {item?.score} credits
                                                        </div>
                                                        <div className={style['loyalty-card-subtitle']}>
                                                            {item?.description}
                                                        </div>
                                                        <div className={style['loyalty-card-content']}>
                                                            {item?.type == 2
                                                                ? ""
                                                                : item?.discount_type == 1
                                                                    ? trans('receive-up-to') + " €" + Math.abs(item?.reward).toFixed(2) + " " + trans('discount-next-order')
                                                                    : trans('receive-up-to') + " " + Math.abs(item?.percentage).toFixed(2) + "% " + trans('discount-next-order')
                                                            }
                                                        </div>
                                                    </div>
                                                    {
                                                        errorMessage && currentRedeemId == item?.id && (
                                                            <div className={`row`}>
                                                                <div className={`col-12`}>
                                                                    <div className={`${style['infor-message']} d-flex`}>
                                                                        <div className="col-1">
                                                                            <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                                <circle cx="10" cy="10" r="9" stroke={color} strokeWidth="2"/>
                                                                                <path d="M10.8751 7.244C10.5671 7.244 10.3058 7.13667 10.0911 6.922C9.87644 6.70733 9.76911 6.446 9.76911 6.138C9.76911 5.83 9.87644 5.56867 10.0911 5.354C10.3058 5.13 10.5671 5.018 10.8751 5.018C11.1831 5.018 11.4444 5.13 11.6591 5.354C11.8831 5.56867 11.9951 5.83 11.9951 6.138C11.9951 6.446 11.8831 6.70733 11.6591 6.922C11.4444 7.13667 11.1831 7.244 10.8751 7.244ZM9.92311 15.084C9.47511 15.084 9.11111 14.944 8.83111 14.664C8.56044 14.384 8.42511 13.964 8.42511 13.404C8.42511 13.1707 8.46244 12.8673 8.53711 12.494L9.48911 8H11.5051L10.4971 12.76C10.4598 12.9 10.4411 13.0493 10.4411 13.208C10.4411 13.3947 10.4831 13.53 10.5671 13.614C10.6604 13.6887 10.8098 13.726 11.0151 13.726C11.1831 13.726 11.3324 13.698 11.4631 13.642C11.4258 14.1087 11.2578 14.468 10.9591 14.72C10.6698 14.9627 10.3244 15.084 9.92311 15.084Z" fill={color}/>
                                                                            </svg>
                                                                        </div>
                                                                        <div className="ps-2">
                                                                            {errorMessage}
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>)
                                                    }

                                                    <div className={style['loyalty-card-footer']} role={"button"}
                                                        onClick={
                                                            currentStatus?.allowClick
                                                                ? (()=> {
                                                                    handleActiveCard(index)
                                                                    handleRedeem(item?.id, item?.type)
                                                                })
                                                                : (() => {})
                                                        }
                                                        style={!currentStatus?.enableColor ? { opacity: "0.5", background: color } : { background: color }}>
                                                        {currentStatus?.title}
                                                    </div>
                                                </div>
                                            </div>
                                        )
                                    })
                                }

                                {
                                    loyaltyData?.rewards?.length > 0 && loyaltyData?.rewards?.map((item:any, index:number) => (
                                        <div className="col-4" key={index} style={{ maxWidth: '320px' }}>
                                        </div>
                                    ))
                                }
                            </div>
                            <div className="f-right">
                                <div className="row">
                                    <div id="cart-container" className={`cart-container mt-3 ${cartStyle.cart} ${cartStyle['user-website']}`}>
                                        <UserWebsiteCart
                                            origin="desktop"
                                            navbarHeight={0}
                                            workspace={workspaceInfo}
                                            apiData={apiData}
                                            color={color}
                                            workspaceId={workspaceId}
                                            step={step}
                                            handleActive={handleActive}
                                            isExistRedeem={isExistRedeem}
                                            setIsDeliveryType={setIsDeliveryType}
                                            toggleSorting={isSortOpen} />
                                    </div>
                                </div>
                            </div>                  
                        </div>
                    </div>
                </div>
                <Footer trans={trans}/>
            </div>

            <style>{`
                .bg-custom-total-bar {
                    background: linear-gradient(90deg, rgba(${rgbColor ? rgbColor.r / 1.2 : hexToRgb('#FFFFFF')}, ${rgbColor ? rgbColor.g / 1.2 : hexToRgb('#FFFFFF')}, ${rgbColor ? rgbColor.b / 1.2 : hexToRgb('#FFFFFF')}, 1), rgba(${rgbColor ? rgbColor.r : hexToRgb('#FFFFFF')}, ${rgbColor ? rgbColor.g : hexToRgb('#FFFFFF')}, ${rgbColor ? rgbColor.b : hexToRgb('#FFFFFF')}, 0.4))!important;
                }
                @media (min-width: 1280px) { 
                    .bg-custom-total-bar {
                        background: ${color}!important;
                    }
                    .loyalty-progress {
                        color: #FFF;
                        text-align: center;
                        text-shadow: 0px 0px 20px #FFF;
                        font-family: SF Compact;
                        font-size: 40px;
                        font-style: normal;
                        font-weight: 1000;
                        line-height: normal;
                        border: 1px solid #D2D2D2;
                        border-radius: 5px;
                        background: var(--W1, #F5F5F5);
                    }
                    
                    .loyalty-progress-bar {
                        border-radius: 5px;
                        height: 62px;
                    }
                `}
            </style>
        </>
    );
}