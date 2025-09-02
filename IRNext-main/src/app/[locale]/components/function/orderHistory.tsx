'use client'

import React, { useState, memo, useEffect } from "react";
import * as config from "@/config/constants"
import Cookies from 'js-cookie';
import { OrderDetail } from '@/services/orderDetail';
import { Modal } from 'react-bootstrap';
import variables from '/public/assets/css/function-page.module.scss';
import { useI18n } from '@/locales/client';
import * as configLocales from "@/config/locales";
import '@/app/[locale]/components/function/custom.scss';
import "react-responsive-carousel/lib/styles/carousel.min.css";
import { api } from "@/utils/axios";
import moment from 'moment';
import InfiniteScroll from 'react-infinite-scroll-component';
import Slider, { Settings } from "react-slick";
import "slick-carousel/slick/slick.css";
import "slick-carousel/slick/slick-theme.css";
import style from 'public/assets/css/slider.module.scss';
import { changeTypeFlag, changeType, changeRootInvalidProductIds, rootChangeInCart, changeRootCartTmp, addGroupOrderSelectedNow } from '@/redux/slices/cartSlice'
import { useAppDispatch } from '@/redux/hooks'
import _ from "lodash";
import DeliveryLocation from "../layouts/popup/deliveryLocation";
import { useAppSelector } from '@/redux/hooks'
import { useGetWorkspaceDataByIdQuery } from '@/redux/services/workspace/workspaceDataApi'
import { checkOrderTypeActive } from "@/services/workspace";

const reorder = variables['reorder'];

export function OrderHistory() {
    const workspaceToken = useAppSelector((state) => state.workspaceData.globalWorkspaceToken)
    const workspaceId = useAppSelector((state) => state.workspaceData.globalWorkspaceId)
    const workspaceUrl = workspaceId ? `&workspace_id=${workspaceId}` : '';
    const { data: apiDataToken } = useGetWorkspaceDataByIdQuery({ id: workspaceId })
    const apiData = apiDataToken?.data?.setting_generals;
    const color = !workspaceId ? '#B5B268' : apiData?.primary_color;
    const token = Cookies.get('loggedToken');
    const [orderLists, setOrderLists] = useState<any[]>([]);
    const [selectedOrderId, setSelectedOrderId] = useState(null);
    const [orderDetails, setOrderDetails] = useState<OrderDetails | null>(null);
    const [nextPage, setNextPage] = useState(2);
    const [isDeliveryOrderOpen, setIsDeliveryOrderOpen] = useState(false);
    const [groupInActiveMessage, setGroupInActiveMessage] = useState('');
    const [currentAddress, setCurrentAddress] = useState({
        address: 'Limburgplein 1, 3500 Hasselt',
        lat: '50.92758786546253',
        lng: '5.338539271587612'
    });
    const dispatch = useAppDispatch()
    const [currentLanguage, setCurrentLanguage] = useState(Cookies.get('Next-Locale') ?? configLocales.LOCALE_FALLBACK);
    const baseUrl = config.BASE_URL + '';

    const handleClickDelivery = () => {
        setIsDeliveryOrderOpen(!isDeliveryOrderOpen);
    }
    
    const trans = useI18n();
    type OrderDetails = {
        data?: {
            id: number;
            workspace: {
                name: string;
            };
            items: any[];
            group: {
                name: string;
                address_display: string;
                active: any
            };
            type: number;
            group_id: number;
            code: string;
            ship_price: string;
            service_cost: string;
            subtotal: number;
            coupon_discount: number | null;
            redeem_discount: number | null;
            group_discount: number | null;
            total_price: number;
            date_time: string;
            payment_method: number;
            payment_status: number;
            status: number;
            note: string;
            extra_code?: number;
            data: any[];
            address: string;
            setting_delivery_condition: {
                price: number;
            }
        };

        // ... add more properties as needed
    }

    const fetchMoreData = () => {
        api.get(`orders/history?limit=15&page=${nextPage}` + workspaceUrl, {
            headers: {
                'Authorization': `Bearer ${token}`,
                'App-Token': workspaceToken,
                'Content-Language': currentLanguage
            }
        }).then(res => {
            const json = res.data;
            setNextPage(nextPage + 1);
            if (json.data) {
                const newData = orderLists.concat(json.data.data);
                // Lọc bản ghi với điều kiện status === 2 và có tồn tại mollie
                const filteredData = newData.filter(item => (item.payment_method === 0 || item.payment_method_display === "Mollie") && item.status === 2);
                // Thêm vào các bản ghi không có mollie
                newData.forEach(item => {
                    if (item.payment_method !== 0 && item.payment_method_display !== "Mollie") {
                        filteredData.push(item);
                    }
                });
                filteredData.sort((prev: any, next: any) => next.id - prev.id);
                setOrderLists(filteredData);
                return orderLists;
            } else {
                return orderLists;
            }
        }).catch(error => {
            // console.log(error)
        });
    }

    const formatDate = (time?: string, timeFormat?: string, outputFormat?: string) => {
        let hourOffset = new Date().getTimezoneOffset() / 60;

        if (hourOffset < 0) {
            return moment(time, timeFormat).add(-hourOffset, 'hours').format(outputFormat);
        } else {
            return moment(time, timeFormat).subtract(hourOffset, 'hours').format(outputFormat);
        }
    }

    // default data
    useEffect(() => {
        api.get(`orders/history?limit=15&page=1` + workspaceUrl, {
            headers: {
                'Authorization': `Bearer ${token}`,
                'App-Token': workspaceToken,
                'Content-Language': currentLanguage
            }
        }).then(res => {
            const json = res.data;
            if (json.data) {
                const filteredData = json?.data?.data.filter((item: any) => (item.payment_method === 0 || item.payment_method_display === "Mollie") && item.status === 2);
                // Thêm vào các bản ghi không có mollie
                json?.data?.data.forEach((item: any) => {
                    if (item.payment_method !== 0 && item.payment_method_display !== "Mollie") {
                        filteredData.push(item);
                    }
                });
                filteredData.sort((prev: any, next: any) => next.id - prev.id);
                setOrderLists(filteredData);
                return json.data;
            } else {
                return [];
            }
        }).catch(error => {
            // console.log(error)
        });
    }, []);

    const handleOrderClick = async (orderId: any) => {
        try {
            const response = await OrderDetail({ id: orderId, token });
            if ((response as any)?.data) {
                setOrderDetails(response as any);
            }
            setSelectedOrderId(orderId);
        } catch (error) {
            console.error(`Error fetching order details for order ID ${orderId}:`, error);
        }
    };
    // State to control the visibility of the modal
    const [showModal, setShowModal] = useState(false);

    // Function to open the modal
    const handleOpenModal = () => setShowModal(true);

    // Function to close the modal
    const handleCloseModal = () => setShowModal(false);
    const settings: Settings = {
        dots: true,
        infinite: false,
        speed: 500,
        slidesToShow: 1,
        slidesToScroll: 1,
        arrows: false,
        className: "order-custom-carousel",
        appendDots: (dots: any) => (
            <div>
                <ul style={{ padding: '0', paddingBottom: '5px', marginBottom: '0' }}>
                    {dots.map((dot: any, index: any) => (
                        <li key={index} style={{ display: "inline-block", margin: "0 -2px" }}>
                            {dot}
                        </li>
                    ))}
                </ul>
            </div>
        ),
    };

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

    useEffect(() => {
        setGroupInActiveMessage('');
    }, [orderDetails]);

    const tokenLoggedInCookie = Cookies.get('loggedToken');
    const handleReorder = async () => {
        const orderDetail = orderDetails?.data;

        if(orderDetail?.group?.active == 1 || !orderDetail?.group) {
            setGroupInActiveMessage('');
            const validateTypeStatus = await checkOrderTypeActive(workspaceId, orderDetail, tokenLoggedInCookie);
            if(validateTypeStatus === false) {
                setGroupInActiveMessage(trans('cart.type_not_active'));
            } else {
                const data: any = orderDetails?.data?.items.map(async (item: any) => {
                    const optionItemsStore = item?.options.map((option: any) => {
                        return {
                            'optionId': option?.option?.id,
                            'optionItems': option?.option_items.map((optionItem: any) => {
                                return optionItem?.option_item
                            })
                        }
                    });
                    // Check if the product has options
                    let productOptions: any = [];
                    try {
                        const res: any = await api.get(`products/${item?.product?.id}/options?limit=100&page=1`, {
                            headers: {
                                'Content-Language': currentLanguage
                            }
                        });
                        if (res && res?.code != 'ERR_NETWORK') {
                            productOptions = res;
                        }
                    } catch (error) {
                        console.log(error);
                    }
        
                    let optionItemsStoreSort: any = [];
                    const productOptionsData = productOptions?.data?.data.length > 0 && productOptions?.data?.data.map((option: any) => {
                        optionItemsStore.find((optionItemStore: any) => {
                            if (optionItemStore?.optionId == option?.id) {
                                optionItemsStoreSort.push(optionItemStore);
                            }
                        })
                    })
        
                    return {
                        'basePrice': Number(item?.price),
                        'productId': item?.product?.id,
                        'product': {
                            'data': item?.product
                        },
                        'productTotal': item?.quantity,
                        'productOptions': productOptions.length > 0 ? productOptions?.data : [],
                        'optionItemsStore': optionItemsStore,
                    };
                })
        
                if (!workspaceId) {
                    // Reorder from order history
                    let reorderUrl = baseUrl + currentLanguage + '/category/cart' + '?action=reorder';
                    reorderUrl += '&order_id=' + (orderDetail ? orderDetail.id : 0);
                    window.open(reorderUrl, '_blank');
                    return;
                }
        
                Promise.all(data).then((values: any) => {
                    if (orderDetails?.data?.group_id) {
                        onClickChangeType(3);
                        dispatch(rootChangeInCart(values))
                    } else if (orderDetails?.data?.type && orderDetails?.data?.type === 1) {
                        handleClickDelivery();
                        dispatch(changeRootCartTmp(values));
                    } else if (orderDetails?.data?.type === 0) {
                        onClickChangeType(1);
                        dispatch(rootChangeInCart(values))
                    }
                });
                
                handleCloseModal();
            }
        } else {
            setGroupInActiveMessage(trans('cart.group_inactive'));
        }        
    }

    let rootCartTmp = useAppSelector((state) => state.cart.rootCartTmp);
    const onClickChangeType = (type: number) => {
        dispatch(changeType(type))
        dispatch(changeTypeFlag(false))
        dispatch(changeRootInvalidProductIds(null));
        if (type === 2) {
            dispatch(rootChangeInCart(rootCartTmp))
        }
        if (type === 3) {
            dispatch(addGroupOrderSelectedNow(orderDetails?.data?.group))
        } else {
            dispatch(addGroupOrderSelectedNow(null))
        }
        window.location.href = '/category/cart';
    }

    return (
        <>
            <InfiniteScroll
                style={{ minHeight: '100vh' }}
                dataLength={orderLists.length}
                next={() => fetchMoreData()}
                hasMore={true}
                loader={<> </>}
            >{
                    orderLists.length !== 0 ? (
                        <div className="" style={{ minHeight: '80vh', display: 'flex', flexDirection: 'column' }}>
                            {orderLists != null && orderLists.map((item, index) => (
                                <div className="mt-2 py-1 d-flex justify-content-between border-bottom container" key={item.id} onClick={() => { handleOrderClick(item.id); handleOpenModal() }}>

                                    <div>
                                        <div className={variables.mainDateTime}>
                                            {formatDate(item.date_time, 'YYYY-MM-DD hh:mm:ss', 'DD/MM/YYYY [' + trans('at') + '] HH:mm')}
                                        </div>
                                        <div className={variables.status}>
                                            <span style={{ color: "#413E38" }}>{item.workspace.name}</span>  - #{item.group !== null ? 'G' : ''}{item.code}{(!!item.group && item?.extra_code) ? `-${item?.extra_code}` : ''} - {item.type === 0
                                                ? trans('take-out')
                                                : item.type === 1
                                                    ? trans('delivery')
                                                    : item.type === 2
                                                        ? ""
                                                        : ""}
                                        </div>
                                    </div>
                                    <button
                                        className="btn border-0"
                                        type="button"
                                        onClick={() => { handleOrderClick(item.id); handleOpenModal() }}
                                        style={{ borderLeft: 'none', minWidth: '40px' }}
                                    >
                                        <svg width="24" height="25" viewBox="0 0 24 25" fill="none" xmlns="http://www.w3.org/2000/svg" style={{ cursor: 'pointer', pointerEvents: 'auto' }}>
                                            <path d="M1 12.5C1 12.5 5 4.16663 12 4.16663C19 4.16663 23 12.5 23 12.5C23 12.5 19 20.8333 12 20.8333C5 20.8333 1 12.5 1 12.5Z" stroke={color} strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" />
                                            <path d="M12 15.625C13.6569 15.625 15 14.2259 15 12.5C15 10.7741 13.6569 9.375 12 9.375C10.3431 9.375 9 10.7741 9 12.5C9 14.2259 10.3431 15.625 12 15.625Z" stroke={color} strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" />
                                        </svg>
                                    </button>
                                </div>
                            ))}
                        </div>
                    ) : null
                }
            </InfiniteScroll>

            {selectedOrderId && ((orderDetails as any)?.data as any) && (
                <Modal show={showModal} onHide={handleCloseModal} centered >
                    <Modal.Body>
                        <hr className="mx-auto" style={{
                            textAlign: 'center',
                            width: '100px',
                            marginTop: '1rem',
                            marginBottom: '1rem',
                            border: '0',
                            borderTop: '2px solid #E1E1E1'
                        }} />
                        <div className={`${style['slide-order']}`}>
                            <div className={`${style['slide-card']}`}>
                                <Slider {...settings}>
                                    <div >
                                        <div className={`${style['card-title']}`}>
                                            {trans('ordered-products')}
                                        </div>
                                        <div className={`${style['name-store']}`} style={{ color: color }}>
                                            {orderDetails?.data?.workspace?.name.toUpperCase()}
                                        </div>
                                        <div className={`${style['card-body']}`}>
                                            <div className={`${style['card-items']}`}>
                                                {
                                                    orderDetails?.data?.items?.map((item: any, index: number) => (
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
                                                                                -{option?.option?.is_ingredient_deletion == true ? (" " + trans('with-out')) : null}
                                                                                {option?.option_items?.map((optionItem: any, index: number) => (
                                                                                    option?.option_items?.length > 1
                                                                                        ? optionItem?.option_item.name + (index !== option.option_items.length - 1 ? ", " : "")
                                                                                        : " " + optionItem?.option_item.name
                                                                                ))}
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </>

                                                            ))}
                                                            {orderDetails?.data?.items?.length && index !== orderDetails?.data?.items?.length - 1 && (
                                                                <div className={`${style['line-break']}`}>
                                                                    <div className={`${style['line-order']}`}>
                                                                    </div>
                                                                </div>
                                                            )}
                                                        </div>
                                                    ))
                                                }
                                            </div>
                                        </div>
                                        <div className={`${style['card-footer']}`}>
                                            <div className={`${style['line-break']}`}>
                                                <div className={`${style['line-order']}`}>
                                                </div>
                                            </div>
                                            <div className={`${style['sub-prices']}  row`}>
                                                {
                                                    (orderDetails?.data?.coupon_discount && orderDetails?.data?.coupon_discount > 0)
                                                        || (orderDetails?.data?.redeem_discount && orderDetails?.data?.redeem_discount > 0)
                                                        || (orderDetails?.data?.group_discount && orderDetails?.data?.group_discount > 0)
                                                        || (orderDetails?.data?.ship_price && Number(orderDetails?.data?.ship_price) > 0)
                                                        || (orderDetails?.data?.service_cost && Number(orderDetails?.data?.service_cost) > 0) ? (
                                                        <>
                                                            <div className={`${style['sub-price-label']} col-sm-6 col-6`}>
                                                                {trans('subtotal')}:
                                                            </div>
                                                            <div className={`${style['sub-price']} col-sm-6 col-6`}>
                                                                €
                                                                {orderDetails?.data?.subtotal}
                                                            </div>
                                                        </>
                                                    ) : null}


                                                {
                                                    (orderDetails?.data?.ship_price && Number(orderDetails?.data?.ship_price) > 0) && (<>
                                                        <div className={`${style['sub-price-label']} col-sm-6 col-6`}>
                                                            {trans('delivery-cost')}:
                                                        </div>
                                                        <div className={`${style['sub-price']} col-sm-6 col-6`}>
                                                            €
                                                            {orderDetails?.data?.ship_price}
                                                        </div>
                                                    </>)
                                                }

{
                                                    (orderDetails?.data?.service_cost && Number(orderDetails?.data?.service_cost) > 0) && (<>
                                                        <div className={`${style['sub-price-label']} col-sm-6 col-6`}>
                                                            {trans('cart.service_cost')}:
                                                        </div>
                                                        <div className={`${style['sub-price']} col-sm-6 col-6`}>
                                                            €
                                                            {orderDetails?.data?.service_cost}
                                                        </div>
                                                    </>)
                                                }

                                                {
                                                    (orderDetails?.data?.coupon_discount && orderDetails?.data?.coupon_discount > 0) && (<>
                                                        <div className={`${style['sub-price-label']} col-sm-6 col-6`}>
                                                            {trans('coupon-discount')}:
                                                        </div>
                                                        <div className={`${style['sub-price']} col-sm-6 col-6`}>
                                                            -
                                                            €
                                                            {orderDetails?.data?.coupon_discount}
                                                        </div>
                                                    </>)
                                                }

                                                {
                                                    (orderDetails?.data?.redeem_discount && orderDetails?.data?.redeem_discount > 0) && (<>
                                                        <div className={`${style['sub-price-label']} col-sm-6 col-6`}>
                                                            {trans('redeem-discount')}:
                                                        </div>
                                                        <div className={`${style['sub-price']} col-sm-6 col-6`}>
                                                            -
                                                            €
                                                            {orderDetails?.data?.redeem_discount}
                                                        </div>
                                                    </>)
                                                }

                                                {
                                                    (orderDetails?.data?.group_discount && orderDetails?.data?.group_discount > 0) && (<>
                                                        <div className={`${style['sub-price-label']} col-sm-6 col-6`}>
                                                            {trans('group-discount')}:
                                                        </div>
                                                        <div className={`${style['sub-price']} col-sm-6 col-6`}>
                                                            -
                                                            €
                                                            {orderDetails?.data?.group_discount}
                                                        </div>
                                                    </>)
                                                }
                                                <div className={`${style['main-price-label']} col-sm-6 col-6`}>
                                                    {trans('total')}:
                                                </div>
                                                <div className={`${style['main-price']} col-sm-6 col-6`}>
                                                    €
                                                    {orderDetails?.data?.total_price}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div >
                                        <div className={`${style['card-title']}`}>
                                            {trans('details-of-order') + ' #' + (orderDetails?.data?.group !== null ? 'G' : '') + orderDetails?.data?.code + ((!!orderDetails?.data?.group && orderDetails?.data?.extra_code) ? `-${orderDetails?.data?.extra_code}` : '')}
                                        </div>
                                        <div className={`${style['name-store']}`} style={{ color: color }}>
                                            {orderDetails?.data?.workspace?.name.toUpperCase()}
                                        </div>
                                        <div className={style['datetime']}>
                                            {formatDate(orderDetails?.data?.date_time, 'YYYY-MM-DD hh:mm:ss', 'DD/MM/YYYY [' + trans('at') + '] HH:mm')}
                                        </div>
                                        <div className={`${style['card-body']}`}>
                                            <div className={`${style['card-contents']} row`}>
                                                <div className={`${style['card-label']} col-sm-6 col-6`}>
                                                    {trans('payment-status')}:
                                                </div>
                                                <div className={`${style['card-content']} col-sm-6 col-6`}>
                                                    {orderDetails?.data?.status ? paymentStatus[orderDetails?.data?.status] : paymentStatus[0]}
                                                </div>
                                                <div className={`${style['card-label']} col-sm-6 col-6`}>
                                                    {trans('payment-method')}:
                                                </div>
                                                <div className={`${style['card-content']} col-sm-6 col-6`}>
                                                    {orderDetails?.data?.payment_method == 0 ? (paymentMethods[0]) : orderDetails?.data?.payment_method ? paymentMethods[orderDetails?.data?.payment_method] : ''}
                                                </div>
                                                {orderDetails?.data?.note && (
                                                    <>
                                                        <div className={`${style['card-label']} col-sm-6 col-6`}>
                                                            {trans('comments')}:
                                                        </div>
                                                        <div className={`${style['card-content']} col-sm-6 col-6`}>
                                                            {orderDetails?.data?.note}
                                                        </div>
                                                    </>
                                                )}
                                                {
                                                    (orderDetails?.data?.type === 0 && orderDetails?.data?.group === null)
                                                        ? (
                                                            <div className={` d-flex justify-content-between`}>
                                                                <div className={variables.additionInfo}>{trans('order-type')}:</div>
                                                                <div className={variables.additionInfoValue}>{trans('take-out')}</div>
                                                            </div>)
                                                        : (orderDetails?.data?.type === 0 && orderDetails?.data?.group !== null)
                                                            ?
                                                            (
                                                                <>

                                                                    <div className={` d-flex justify-content-between`}>
                                                                        <div className={variables.additionInfo}>{trans('group')}:</div>
                                                                        <div className={variables.additionInfoValue} style={{paddingLeft: '20px'}}>{orderDetails?.data?.group?.name}</div>
                                                                    </div>
                                                                </>
                                                            )
                                                            : orderDetails?.data?.type === 1 && orderDetails?.data?.group === null
                                                                ?
                                                                (
                                                                    <div className={` d-flex justify-content-between`}>
                                                                        <div className={variables.additionInfo}>{trans('address')}:</div>
                                                                        <div className={variables.additionInfoValue} style={{paddingLeft: '20px'}}>{orderDetails?.data?.address}</div>
                                                                    </div>)
                                                                : (orderDetails?.data?.type === 1 && orderDetails?.data?.group !== null)
                                                                    ?
                                                                    (
                                                                        <>
                                                                            <div className={` d-flex justify-content-between`}>
                                                                                <div className={variables.additionInfo}>{trans('address')}:</div>
                                                                                <div className={variables.additionInfoValue} style={{paddingLeft: '20px'}}>{orderDetails?.data?.group?.address_display}</div>
                                                                            </div>
                                                                            <div className={` d-flex justify-content-between`}>
                                                                                <div className={variables.additionInfo}>{trans('group')}:</div>
                                                                                <div className={variables.additionInfoValue} style={{paddingLeft: '20px'}}>{orderDetails?.data?.group?.name}</div>
                                                                            </div>
                                                                        </>
                                                                    )
                                                                    : orderDetails?.data?.type === 2
                                                                        ? ""
                                                                        : ""
                                                }
                                            </div>
                                        </div>
                                    </div>
                                </Slider>
                            </div>
                        </div>

                        {groupInActiveMessage !== '' && (
                            <p className="text-center error error-reorder mt-3">{groupInActiveMessage}</p>
                        )}
                        
                        <div className={`${reorder} mx-auto mt-2 mb-3`} onClick={() => handleReorder()}>{trans('reorder')}</div>
                    </Modal.Body>
                </Modal>
            )}

            {(isDeliveryOrderOpen) &&
                <DeliveryLocation
                    toggleDeliveryOrder={() => handleClickDelivery()}
                    onClickChangeType={() => onClickChangeType(2)}
                    currentAddress={currentAddress}
                />
            }
        </>
    );
}

export default memo(OrderHistory);
