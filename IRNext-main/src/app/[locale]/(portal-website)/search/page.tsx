"use client"

import { useI18n } from '@/locales/client'
import React, { useEffect, useState, useRef } from "react";
import { api } from "@/utils/axios";
import Cookies from "js-cookie";
import style from "public/assets/css/portal.module.scss";
import MenuPortal from "@/app/[locale]/components/menu/menu-portal";
import HeaderPortal from "@/app/[locale]/components/menu/header-portal";
import FooterPortal from "@/app/[locale]/components/menu/footer-portal";
import RestaurantCard from "@/app/[locale]/components/portal/restaurant-card";
import 'slick-carousel/slick/slick.css';
import 'slick-carousel/slick/slick-theme.css';
import InfiniteScroll from 'react-infinite-scroll-component';
import { uniqBy } from 'lodash';
import RestaurantMap from "@/app/[locale]/components/portal/restaurant-map";
import { useDebounce } from 'use-debounce';
import variables from '/public/assets/css/portal-search.module.scss'
import * as config from "@/config/constants"
import { addToPortalAddress } from '@/redux/slices/portalAddressSlice'
import { useAppSelector, useAppDispatch } from '@/redux/hooks'
import Image from "next/image";
import ProfileUpdatePortal from '@/app/[locale]/components/layouts/popup/ProfileUpdatePortal';
import PortalLoginDesktopPopup from "@/app/[locale]/components/portal/portalLoginDesktopPopup";
import {useRouter} from "next/navigation";

const ORDER_TYPE = {
    TAKE_AWAY: 0,
    DELIVERY: 1,
};

const ORDER_SORT = {
    DISTANCE: 0,
    MIN_PRICE: 1,
    DELIVERY_COST: 2,
    MIN_WAITING_TIME: 3,
    NAME: 4,
}

const ORDER_MIN_AMOUNT = {
    AMOUNT_1: 10,
    AMOUNT_2: 20,
}

const ORDER_DELIVERY_CHARGE = {
    AMOUNT_0: 0,
    AMOUNT_1: 2.5,
    AMOUNT_2: 4.5,
}

export default function Page() {
    const trans = useI18n();
    const orderTypes = [
        trans('take-away'),
        trans('delivery'),
    ]

    const orderSortValue = {
        [ORDER_SORT.DISTANCE]: "distance",
        [ORDER_SORT.MIN_PRICE]: "amount",
        [ORDER_SORT.DELIVERY_COST]: "delivery_fee",
        [ORDER_SORT.MIN_WAITING_TIME]: "waiting_time",
        [ORDER_SORT.NAME]: "name",
    }

    const orderSortDelivery = {
        [ORDER_SORT.DISTANCE]: trans('portal.distance'),
        [ORDER_SORT.MIN_PRICE]: trans('portal.min-price'),
        [ORDER_SORT.DELIVERY_COST]: trans('portal.delivery-cost'),
        [ORDER_SORT.MIN_WAITING_TIME]: trans('portal.min-wait-time'),
        [ORDER_SORT.NAME]: trans('portal.name'),
    }

    const orderMinAmount = {
        [ORDER_MIN_AMOUNT.AMOUNT_1]: "€" + ORDER_MIN_AMOUNT.AMOUNT_1 + " " + trans('portal.or-less'),
        [ORDER_MIN_AMOUNT.AMOUNT_2]: "€" + ORDER_MIN_AMOUNT.AMOUNT_2 + " " + trans('portal.or-less'),
    }

    const orderDeliveryCharge = {
        [ORDER_DELIVERY_CHARGE.AMOUNT_0]: trans('portal.free'),
        [ORDER_DELIVERY_CHARGE.AMOUNT_1]: "€" + ORDER_DELIVERY_CHARGE.AMOUNT_1.toFixed(2) + " " + trans('portal.or-less'),
        [ORDER_DELIVERY_CHARGE.AMOUNT_2]: "€" + ORDER_DELIVERY_CHARGE.AMOUNT_2.toFixed(2) + " " + trans('portal.or-less'),
    }

    const orderSortTakeAway = {
        [ORDER_SORT.DISTANCE]: trans('portal.distance'),
        [ORDER_SORT.MIN_WAITING_TIME]: trans('portal.min-wait-time'),
        [ORDER_SORT.NAME]: trans('portal.name'),
    }

    const orderSortGroup = {
        [ORDER_SORT.NAME]: trans('portal.name'),
    }
    const [isLoading, setIsLoading] = useDebounce(false, 400);
    const [orderType, setOrderType] = useState(ORDER_TYPE.TAKE_AWAY);
    const [orderSort, setOrderSort] = useState(ORDER_SORT.DISTANCE);
    const [orderSortType, setOrderSortType] = useState<any>(null);
    const [category, setCategory] = useState(null);
    const [minAmountValue, setMinAmountValue] = useState(null);
    const [deliveryCharge, setDeliveryCharge] = useState(null);
    const [location, setLocation] = useState({
        lng: 5.30741,
        lat: 50.89841,
        address: '3500, Hasselt',
    });
    const portalAddressCache = useAppSelector<any>((state: any) => state.portalAddress.data);
    const dispatch = useAppDispatch()
    const [categories, setCategories] = useState<any>(null);
    const [restaurants, setRestaurants] = useState<any>([]);
    const [closeRestaurants, setCloseRestaurants] = useState<any>([]);
    const [positions, setPositions] = useState<any>([]);
    const [dataPositions, setDataPositions] = useState<any>([]);
    const [nextPage, setNextPage] = useState(2);
    const [nextPageClose, setNextPageClose] = useState(2);
    const [keyword, setKeyword] = useDebounce('', 1000);
    const [tmpKeyword, setTmpKeyword] = useState('');
    const [isShowSearch, setIsShowSearch] = useState(false);
    const [isShowMap, setIsShowMap] = useState(false);
    const [loadTrans, setLoadTrans] = useState(0);
    const [left, setLeft] = useState(false);
    const [right, setRight] = useState(true);
    const [isCheck, setIsCheck] = useState(false);
    const [isDiscount, setIsDiscount] = useState(false);
    const [isOpen, setIsOpen] = useState(false);
    const [isGroup, setIsGroup] = useState(false);
    const [scroll, setScroll] = useState(0);

    useEffect(() => {
        if (orderSortTakeAway[0].includes('portal.')) {
            setOrderSortType(orderSortTakeAway)
            setLoadTrans(loadTrans + 1);
        } else {
            setOrderSortType(orderSortTakeAway)
        }
    }, [loadTrans])

    useEffect(() => {
        const query = new URLSearchParams(window.location.search);
        const address = query.get('search');
        const postcode = query.get('postcode');
        if (address && postcode) {
            api.get(`/addresses?limit=1000&page=1&keyword=${postcode}`, {}).then(res => {
                setListAddress(res?.data?.data?.data);
                const item = res?.data?.data?.data?.find((item: any) => item?.id == address);
                handleItemClick(item);
            }).catch(error => {
                // console.log(error)
            });
        }
    }, []);


    useEffect(() => {
        if (orderType == ORDER_TYPE.TAKE_AWAY) {
            setOrderSortType(orderSortTakeAway)
            setOrderSort(ORDER_SORT.DISTANCE)
            setCategory(null)
            setCategories(null)
        } else if (orderType == ORDER_TYPE.DELIVERY) {
            setOrderSortType(orderSortDelivery)
            setOrderSort(ORDER_SORT.DISTANCE)
            setCategory(null)
            setCategories(null)
        }
    }, [orderType]);

    const language = Cookies.get('Next-Locale') ?? 'nl';
    const restaurantData = async (page: any, isOpen: any) => {
        const baseUrl = `/workspaces?limit=10&lat=${location?.lat}&lng=${location?.lng}&is_open=${isOpen}&open_type=${orderType}&order_by=${orderSortValue[orderSort]}${!category ? `` : `&restaurant_category_id=${category}`}${keyword && `&keyword=${keyword}`}${isCheck ? `&isLoyalty=1` : ``}${isDiscount ? `&has_coupon=1` : ``}${isGroup ? `&is_group=1` : ``}${minAmountValue ? `&minimumOrderAmount=${minAmountValue}` : ``}${deliveryCharge ? `&deliveryCharge=${deliveryCharge}` : ``}`;
        const res = await api.get(`${baseUrl}&page=${page}`, {
            headers: {
                'Timezone': Cookies.get('timezone') || 'Asia/Ho_Chi_Minh',
            }
        });

        const data = res?.data?.data?.data;

        if (page == 1 && isOpen == 1) {
            setNextPage(2);
            setRestaurants(data);

            if (data?.length < 10) {
                restaurantData(1, 0);
            }
        }

        if (page == 1 && isOpen == 0) {
            setNextPageClose(2);
            setCloseRestaurants(data);
        }

        if (res?.data && restaurants && restaurants.length > 0
            && page > 1 && page <= res?.data?.data?.last_page && isOpen == 1) {
            const newData = restaurants.concat(data);
            setRestaurants(newData);
            if (page == res?.data?.data?.last_page) {
                restaurantData(1, 0);
            }
        }

        if (res?.data && closeRestaurants && closeRestaurants.length > 0
            && page > 1 && page <= res?.data?.data?.last_page && isOpen == 0) {
            const newData = closeRestaurants.concat(data);
            setCloseRestaurants(newData);
            setIsLoading(false);
        } else {
            setIsLoading(false);
        }
    }

    useEffect(() => {
        if (category == null) {
            let all = [];

            if (restaurants || closeRestaurants) {
                if (restaurants.length > 0 && closeRestaurants.length > 0) {
                    all = restaurants.concat(closeRestaurants);
                } else if (restaurants.length > 0 && closeRestaurants.length == 0) {
                    all = restaurants;
                } else if (restaurants.length == 0 && closeRestaurants.length > 0) {
                    all = closeRestaurants;
                }
            }

            if (all.length > 0) {
                const categories = all.map((res: any) => res.categories).flat();
                const uniqCategories = uniqBy(categories, 'id');

                const routes = uniqCategories.map((category: any) => ({
                    key: `${category.id}`,
                    title: category.name
                })).sort((a, b) => {
                    if (a.title < b.title) {
                        return -1;
                    }
                    if (a.title > b.title) {
                        return 1;
                    }
                    return 0;
                });
                setCategories(routes);

                const positionsData = all.map((location: any) => ({
                    lat: Number(location.lat),
                    lng: Number(location.lng)
                }));

                setPositions(positionsData);

                setDataPositions(all);
            } else {
                setCategories(null);
            }
        }
    }, [restaurants, closeRestaurants]);

    useEffect(() => {
        setIsLoading(true);
        restaurantData(1, 1);
    }, [location, orderType, orderSort, category, keyword, minAmountValue, isCheck, deliveryCharge, isGroup, isDiscount]);

    //paginate
    const fetchMoreData = (isOpen: any) => {
        if (isOpen == 1) {
            setNextPage(nextPage + 1);
            restaurantData(nextPage, isOpen);
        } else {
            setNextPageClose(nextPageClose + 1);
            restaurantData(nextPageClose, isOpen);
        }
    }

    //handle search keyword
    const handleSearch = (e: any) => {
        const text = e.target ? e.target.value : e;
        setTmpKeyword(text);
        setKeyword(text);
        setCategory(null);
    };

    // handle next category
    const ref = useRef<any>(null);
    let scrollValue = 500;

    const scrollBar = (scrollOffset: any) => {
        if (ref.current) {
            ref.current.scrollLeft = ref.current.scrollLeft + scrollOffset;
            const trueScroll = ref.current.scrollLeft + scrollOffset;
            //setRight(true);
            if ((trueScroll > 0)) {
                setLeft(true);
            } else {
                setLeft(false);
            }

            if (trueScroll + ref.current.clientWidth <= ref.current.scrollWidth) {
                setRight(true);
            } else {
                setRight(false);
            }
        }
    };

    useEffect(() => {
        if (ref.current && ref.current.clientWidth == ref.current.scrollWidth) {
            setRight(false);
            setLeft(false);
        } else {
            setRight(true);
            setLeft(false);
        }
    }, [ref.current, categories]);
    const inputRef = useRef<any>(null);
    const inputRefMap = useRef<any>(null);
    const [listAddress, setListAddress] = useState<any>([]);
    const [isShow, setIsShow] = useState(true);
    const [inputValue, setInputValue] = useState('3500, Hasselt');
    const [currentInput, setCurrentInput] = useState('');
    const handleSearched = () => {
        setCurrentInput(inputRef.current?.value);
        if (inputRef.current?.value.trim().length !== 0 && inputRef.current?.value.trim().length !== null) {
            if (portalAddressCache.length > 0) {
                const result = portalAddressCache.find((item: any) => item.postcode == inputRef.current?.value);
                if (result) {
                    setListAddress(result);
                } else {
                    setTimeout(function () {
                        api.get(`/addresses?limit=1000&page=1&keyword=${inputRef.current?.value}`, {
                        }).then(res => {
                            if (inputRef.current?.value.trim().length !== 0 || inputRef.current?.value.trim().length !== null) {
                                setListAddress(res?.data?.data?.data);
                            } else {
                                setListAddress(null);
                                setIsShow(false);
                            }
                        }).catch(error => {
                            // console.log(error)
                        });
                    }, 1000);
                }
            } else {
                setTimeout(function () {
                    api.get(`/addresses?limit=1000&page=1&keyword=${inputRef.current?.value}`, {
                    }).then(res => {
                        if (inputRef.current?.value.trim().length > 0) {
                            setListAddress(res?.data?.data?.data);
                        } else {
                            setListAddress(null);
                            setIsShow(false);
                        }
                    }).catch(error => {
                        // console.log(error)
                    });
                }, 1000);
            }
            setIsShow(true);
        } else {
            setListAddress(null);
            setIsShow(false);
        }

    }
    const handleSearchedMap = () => {
        setCurrentInput(inputRefMap.current?.value);
        if (inputRefMap.current?.value.trim().length !== 0 && inputRefMap.current?.value.trim().length !== null) {
            if (portalAddressCache.length > 0) {
                const result = portalAddressCache.find((item: any) => item.postcode == inputRefMap.current?.value);
                if (result) {
                    setListAddress(result);
                } else {
                    setTimeout(function () {
                        api.get(`/addresses?limit=1000&page=1&keyword=${inputRefMap.current?.value}`, {
                        }).then(res => {
                            if (inputRefMap.current?.value.trim().length !== 0 || inputRefMap.current?.value.trim().length !== null) {
                                setListAddress(res?.data?.data?.data);
                            } else {
                                setListAddress(null);
                                setIsShow(false);
                            }
                        }).catch(error => {
                            // console.log(error)
                        });
                    }, 1000);
                }
            } else {
                setTimeout(function () {
                    api.get(`/addresses?limit=1000&page=1&keyword=${inputRefMap.current?.value}`, {
                    }).then(res => {
                        if (inputRefMap.current?.value.trim().length > 0) {
                            setListAddress(res?.data?.data?.data);
                        } else {
                            setListAddress(null);
                            setIsShow(false);
                        }
                    }).catch(error => {
                        // console.log(error)
                    });
                }, 1000);
            }
            setIsShow(true);
        } else {
            setListAddress(null);
            setIsShow(false);
        }

    }

    useEffect(() => {
        if (typeof currentInput !== 'undefined' && (currentInput.trim().length === 0 || currentInput.trim().length === null)) {
            setListAddress(null);
        }
    }, [currentInput])

    function getCoordinates(address: any, item: any) {
        fetch(`https://maps.googleapis.com/maps/api/geocode/json?address=${address}&key=${config.PUBLIC_GOOGLE_MAPS_API_KEY_DISTANCE}`)
            .then(response => response.json())
            .then(data => {
                if (data.results[0].geometry.location.lat !== null && data.results[0].geometry.location.lng !== null) {
                    const latitude = data.results[0].geometry.location.lat;
                    const longitude = data.results[0].geometry.location.lng;
                    api.put(`/addresses/${item?.id}/location`, {
                        latitude: latitude.toString(),
                        longitude: longitude.toString()
                    }, {
                    }).then(res => {
                        dispatch(addToPortalAddress(res?.data?.data));
                        setLocation(prevLocation => ({
                            ...prevLocation,
                            lat: latitude,
                            lng: longitude,
                            address: address,
                        }));
                    }).catch(err => {
                        // console.log(err);
                    });
                }
            })
    }

    const router = useRouter();
    const handleItemClick = (item: any) => {
        setIsShow(false);
        const addressComponent = item?.address && item?.city?.name && item.address === item.city.name
            ? item.address
            : `${item?.address ? item?.address : ''} ${item?.city?.name ? item?.city?.name : ''}`;

        const fullAddress = `${item?.postcode}, ${addressComponent}`.trim();
        setInputValue(fullAddress);
        if (item?.latitude === null || item?.longitude === null) {
            getCoordinates(`${item?.postcode}, ${item?.address ? item?.address : ''}  ${item?.city?.name}`, item)
        } else {
            setLocation(prevLocation => ({
                ...prevLocation,
                lat: parseFloat(item?.latitude),
                lng: parseFloat(item?.longitude),
                address: fullAddress,
            }));
            dispatch(addToPortalAddress(item));
        }

        router.push(`?search=${item?.id}&postcode=${item?.postcode}`);
    }

    const [isFocused, setIsFocused] = useState(false);
    useEffect(() => {
        setIsShow(true);
    }, [isFocused]);

    const [isPopupOpen, setIsPopupOpen] = useState(false);

    const togglePopup = () => {
        setIsPopupOpen(!isPopupOpen);
    };

    const [isProfileUpdatePopupOpen, setIsProfileUpdatePopupOpen] = useState(false);
    const toggleProfileUpdatePopup = () => {
        setIsProfileUpdatePopupOpen(!isProfileUpdatePopupOpen);
    }
    const [getToggleLoginPopUp, setToggleLoginPopUp] = useState(false);

    const toggleLoginPopUp = () => {
        setToggleLoginPopUp(!getToggleLoginPopUp);
    }

    return (
        <>
            <div className="res-mobile">
                <div style={{ position: 'fixed', bottom: 0, left: 0, width: '100%', zIndex: 100 }}>
                    <MenuPortal />
                </div>
                {isShowMap ? (
                    <div className="row">
                        <RestaurantMap location={location} positions={positions} dataPositions={dataPositions} setIsShowMap={setIsShowMap} />
                    </div>
                ) : (
                    <>
                        <div className={`row ${style['portal-search-header']}`}>
                            <div className={`col-md-12`}>
                                <div className={`${style['group-search']}`}>
                                    <svg className={`me-3 mt-2`} width="12" height="16" viewBox="0 0 12 16" fill="none"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <line x1="1.5" y1="-1.5" x2="11.1151" y2="-1.5"
                                            transform="matrix(0.746071 -0.665866 0.533192 0.845995 1.58838 10.3999)"
                                            stroke="white" strokeWidth="3" strokeLinecap="round" />
                                        <line x1="1.5" y1="-1.5" x2="9.82572" y2="-1.5"
                                            transform="matrix(0.831011 0.556256 -0.427352 0.904085 1 9.7002)"
                                            stroke="white" strokeWidth="3" strokeLinecap="round" />
                                    </svg>

                                    <div className={`${style['group-search-input']} d-flex`}>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="17" height="17"
                                            viewBox="0 0 17 17" fill="none" className={variables.iconMobile}>
                                            <path
                                                d="M14 6.90909C14 10.7273 9 14 9 14C9 14 4 10.7273 4 6.90909C4 5.60712 4.52678 4.35847 5.46447 3.43784C6.40215 2.51721 7.67392 2 9 2C10.3261 2 11.5979 2.51721 12.5355 3.43784C13.4732 4.35847 14 5.60712 14 6.90909Z"
                                                stroke="white" strokeLinecap="round" strokeLinejoin="round" />
                                            <path
                                                d="M9 9C10.1046 9 11 8.10457 11 7C11 5.89543 10.1046 5 9 5C7.89543 5 7 5.89543 7 7C7 8.10457 7.89543 9 9 9Z"
                                                stroke="white" strokeLinecap="round" strokeLinejoin="round" />
                                        </svg>
                                        <input
                                            type="text"
                                            className={`${style['search-input']}`}
                                            autoComplete={'off'}
                                            id={variables.inputing}
                                            ref={inputRef}
                                            onKeyUp={handleSearched}
                                            onFocus={() => setIsFocused(true)}
                                            onBlur={() => setIsFocused(false)}
                                            value={inputValue}
                                            onChange={(e: any) => { setInputValue(e.target.value) }}
                                            placeholder={trans('fill-postal-code')}
                                        />
                                        {inputValue && (
                                            <svg className={variables.iconMobile} style={{ right: 0, top: "10px" }} width="11" height="11" viewBox="0 0 11 11"
                                                fill="none" xmlns="http://www.w3.org/2000/svg"
                                                onClick={() => {
                                                    setIsShowSearch(false), setInputValue('')
                                                }}>
                                                <path d="M2 8.40234L9.17358 1.99994" stroke="white" strokeWidth="3"
                                                    strokeLinecap="round" />
                                                <path d="M2.00025 2L9.1737 8.40255" stroke="white" strokeWidth="3"
                                                    strokeLinecap="round" />
                                            </svg>
                                        )}
                                    </div>
                                    {listAddress && listAddress.length > 0 && isShow && (
                                        <div className={variables.listContainMoblie}>
                                            {listAddress.map((item: any, index: any) => (
                                                <div
                                                    className={`d-flex flex-row justify-content-between ${variables.listGroup} ${index > 0 ? 'no-padding' : ''}`}
                                                    key={item.id}
                                                    onClick={() => handleItemClick(item)}
                                                >
                                                    <div>
                                                        <p className='list-group-text'>
                                                            {item.postcode},&nbsp;
                                                            {item.address && item.city && item.address === item.city.name ? item.address : `${item.address ? item.address : ''} ${item.city ? item.city.name : ''}`}
                                                        </p>
                                                    </div>
                                                </div>
                                            ))}

                                        </div>
                                    )}
                                </div>
                            </div>
                            {isShowSearch ? (
                                <div className={`col-md-12`}>
                                    <div className={`${style['group-search']} ${style['group-search-keyword']}`}>
                                        <svg className={`me-3 mt-2`} width="11" height="11" viewBox="0 0 11 11"
                                            fill="none" xmlns="http://www.w3.org/2000/svg"
                                            onClick={() => {
                                                setIsShowSearch(false), setKeyword(''), setTmpKeyword('')
                                            }}>
                                            <path d="M2 8.40234L9.17358 1.99994" stroke="white" strokeWidth="3"
                                                strokeLinecap="round" />
                                            <path d="M2.00025 2L9.1737 8.40255" stroke="white" strokeWidth="3"
                                                strokeLinecap="round" />
                                        </svg>

                                        <div className={`${style['group-search-input']} d-flex`}>
                                            <svg style={{ position: 'absolute', bottom: "5px" }} xmlns="http://www.w3.org/2000/svg" width="13"
                                                height="13" viewBox="0 0 13 13" fill="none" >
                                                <path
                                                    d="M5.95833 10.2917C8.35157 10.2917 10.2917 8.35157 10.2917 5.95833C10.2917 3.5651 8.35157 1.625 5.95833 1.625C3.5651 1.625 1.625 3.5651 1.625 5.95833C1.625 8.35157 3.5651 10.2917 5.95833 10.2917Z"
                                                    stroke="white" strokeLinecap="round" strokeLinejoin="round" />
                                                <path d="M11.375 11.3748L9.01874 9.01855" stroke="white"
                                                    strokeLinecap="round" strokeLinejoin="round" />
                                            </svg>
                                            <input
                                                type="text"
                                                value={tmpKeyword}
                                                className={`${style['search-input']} ${style['search-input-normal']}`}
                                                id="search-input"
                                                placeholder={trans('portal.search-here')}
                                                autoComplete={'off'}
                                                onChange={(e) => {
                                                    handleSearch(e)
                                                }}
                                            />
                                        </div>

                                    </div>
                                </div>
                            ) : (
                                <>
                                    <div className={`col-md-7 col-7 px-0`}>
                                        <div className={`${style['group-filter']}`}>
                                            <div className={`dropdown portal-dropdown-type ${style['group-filter-item']}`}>
                                                <button type="button" data-bs-toggle="dropdown"
                                                    aria-expanded="false">
                                                    {
                                                        orderType == 0 && (
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="16"
                                                                height="15" viewBox="0 0 16 15" fill="none">
                                                                <path
                                                                    d="M4 1.25L2 3.75V12.5C2 12.8315 2.14048 13.1495 2.39052 13.3839C2.64057 13.6183 2.97971 13.75 3.33333 13.75H12.6667C13.0203 13.75 13.3594 13.6183 13.6095 13.3839C13.8595 13.1495 14 12.8315 14 12.5V3.75L12 1.25H4Z"
                                                                    stroke="white" strokeLinecap="round"
                                                                    strokeLinejoin="round" />
                                                                <path d="M2 3.75H14" stroke="white"
                                                                    strokeLinecap="round" strokeLinejoin="round" />
                                                                <path
                                                                    d="M10.6666 6.25C10.6666 6.91304 10.3856 7.54893 9.88554 8.01777C9.38544 8.48661 8.70716 8.75 7.99992 8.75C7.29267 8.75 6.6144 8.48661 6.1143 8.01777C5.6142 7.54893 5.33325 6.91304 5.33325 6.25"
                                                                    stroke="white" strokeLinecap="round"
                                                                    strokeLinejoin="round" />
                                                            </svg>
                                                        )
                                                    }

                                                    {
                                                        orderType == 1 && (
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="16"
                                                                height="17" viewBox="0 0 16 17" fill="none">
                                                                <path d="M10.6666 2.5H0.666626V11.1667H10.6666V2.5Z"
                                                                    stroke="white" strokeLinecap="round"
                                                                    strokeLinejoin="round" />
                                                                <path
                                                                    d="M10.6666 5.8335H13.3333L15.3333 7.8335V11.1668H10.6666V5.8335Z"
                                                                    stroke="white" strokeLinecap="round"
                                                                    strokeLinejoin="round" />
                                                                <path
                                                                    d="M3.66667 14.4998C4.58714 14.4998 5.33333 13.7536 5.33333 12.8332C5.33333 11.9127 4.58714 11.1665 3.66667 11.1665C2.74619 11.1665 2 11.9127 2 12.8332C2 13.7536 2.74619 14.4998 3.66667 14.4998Z"
                                                                    stroke="white" strokeLinecap="round"
                                                                    strokeLinejoin="round" />
                                                                <path
                                                                    d="M12.3333 14.4998C13.2538 14.4998 14 13.7536 14 12.8332C14 11.9127 13.2538 11.1665 12.3333 11.1665C11.4128 11.1665 10.6666 11.9127 10.6666 12.8332C10.6666 13.7536 11.4128 14.4998 12.3333 14.4998Z"
                                                                    stroke="white" strokeLinecap="round"
                                                                    strokeLinejoin="round" />
                                                            </svg>
                                                        )
                                                    }

                                                    {
                                                        orderType == 2 && (
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="11"
                                                                height="9" viewBox="0 0 11 9" fill="none">
                                                                <path
                                                                    d="M8 8.5V7.83333C8 7.47971 7.81563 7.14057 7.48744 6.89052C7.15925 6.64048 6.71413 6.5 6.25 6.5H2.75C2.28587 6.5 1.84075 6.64048 1.51256 6.89052C1.18437 7.14057 1 7.47971 1 7.83333V8.5"
                                                                    stroke="white" strokeLinecap="round"
                                                                    strokeLinejoin="round" />
                                                                <path
                                                                    d="M4.5 3.5C5.32843 3.5 6 2.82843 6 2C6 1.17157 5.32843 0.5 4.5 0.5C3.67157 0.5 3 1.17157 3 2C3 2.82843 3.67157 3.5 4.5 3.5Z"
                                                                    stroke="white" strokeLinecap="round"
                                                                    strokeLinejoin="round" />
                                                                <path
                                                                    d="M10 8.5V7.81857C9.99978 7.5166 9.90145 7.22326 9.72046 6.9846C9.53946 6.74595 9.28604 6.57549 9 6.5"
                                                                    stroke="white" strokeLinecap="round"
                                                                    strokeLinejoin="round" />
                                                                <path
                                                                    d="M7 0.5C7.28606 0.585278 7.5396 0.778981 7.72066 1.05057C7.90172 1.32216 8 1.65619 8 2C8 2.34381 7.90172 2.67784 7.72066 2.94943C7.5396 3.22102 7.28606 3.41472 7 3.5"
                                                                    stroke="white" strokeLinecap="round"
                                                                    strokeLinejoin="round" />
                                                            </svg>
                                                        )
                                                    }

                                                    <span>{orderTypes[orderType]}</span>
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="7" height="5"
                                                        viewBox="0 0 7 5" fill="none">
                                                        <path d="M6 1.25L3.5 3.75L1 1.25" stroke="white"
                                                            strokeLinecap="round" strokeLinejoin="round" />
                                                    </svg>
                                                </button>
                                                <ul className="dropdown-menu">
                                                    <li><a className="dropdown-item disabled"
                                                        href="#">{trans('portal.choose-ordering-method')}</a></li>
                                                    {
                                                        orderTypes.map((item: any, index: any) => {
                                                            return (
                                                                <li key={index}>
                                                                    <a className={`dropdown-item ${index === orderType ? 'active-item' : ''}`}
                                                                        href="#" onClick={() => {
                                                                            setOrderType(index)
                                                                        }}>
                                                                        {index === orderType ? (
                                                                            <svg className="me-1"
                                                                                xmlns="http://www.w3.org/2000/svg"
                                                                                width="12" height="13"
                                                                                viewBox="0 0 12 13" fill="none">
                                                                                <path d="M10 3.25L4.5 9.20833L2 6.5"
                                                                                    stroke="#B5B268" strokeWidth="2"
                                                                                    strokeLinecap="round"
                                                                                    strokeLinejoin="round" />
                                                                            </svg>
                                                                        ) : (
                                                                            <span style={{ marginLeft: '15px' }}></span>
                                                                        )}
                                                                        {item}
                                                                    </a>
                                                                </li>
                                                            )
                                                        })
                                                    }
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                    <div className={`col-md-5 col-5 pe-0`}>
                                        <div className={`${style['group-filter']} d-flex justify-content-between ${style['group-sort']}`}>
                                            <div className={`dropdown portal-dropdown-type ${style['group-filter-item']}`}>
                                                <button style={{ border: "0px", padding: "9px 14px" }} type="button"
                                                    data-bs-toggle="dropdown"
                                                    aria-expanded="false">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="19" height="21"
                                                        viewBox="0 0 19 21" fill="none">
                                                        <path d="M17.9999 15.7861H11.3888" stroke="white"
                                                            strokeLinecap="round" strokeLinejoin="round" />
                                                        <path d="M7.61111 15.7861H1" stroke="white"
                                                            strokeLinecap="round" strokeLinejoin="round" />
                                                        <path d="M18 5.21436L9.5 5.21436" stroke="white"
                                                            strokeLinecap="round" strokeLinejoin="round" />
                                                        <path d="M5.72222 5.21436L1 5.21436" stroke="white"
                                                            strokeLinecap="round" strokeLinejoin="round" />
                                                        <path d="M11.3888 19.75V11.8215" stroke="white"
                                                            strokeLinecap="round" strokeLinejoin="round" />
                                                        <path d="M5.72229 9.17871V1.25019" stroke="white"
                                                            strokeLinecap="round" strokeLinejoin="round" />
                                                    </svg>
                                                </button>
                                                <ul className={`dropdown-menu ${style['dropdown-menu-filter']}`}>
                                                    <div className={`${style['icon-menu-filter']}`}>
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="19" height="8"
                                                            viewBox="0 0 19 8" fill="none">
                                                            <path d="M9.5 0L18.5933 7.5H0.406734L9.5 0Z" fill="white" />
                                                        </svg>
                                                    </div>
                                                    <li><a className="dropdown-item disabled"
                                                        href="#">{trans('portal.sort-by')}</a></li>
                                                    {orderSortType && Object.entries(orderSortType).map((item: any, index: any) => {
                                                        return (
                                                            <li key={index}>
                                                                <a className={`dropdown-item ${item[0] == orderSort ? 'active-item' : ''}`}
                                                                    href="#" onClick={() => {
                                                                        setOrderSort(item[0]);
                                                                    }}>
                                                                    {item[0] == orderSort ? (
                                                                        <svg className="me-1"
                                                                            xmlns="http://www.w3.org/2000/svg"
                                                                            width="12" height="13" viewBox="0 0 12 13"
                                                                            fill="none">
                                                                            <path d="M10 3.25L4.5 9.20833L2 6.5"
                                                                                stroke="#B5B268" strokeWidth="2"
                                                                                strokeLinecap="round"
                                                                                strokeLinejoin="round" />
                                                                        </svg>
                                                                    ) : (
                                                                        <span style={{ marginLeft: '15px' }}></span>
                                                                    )}
                                                                    {item[1]} {((orderType != 2 && item[0] == 0) || (orderType == 2 && item[0] == 4)) && "(" + trans('portal.default') + ")"}
                                                                </a>
                                                            </li>
                                                        )
                                                    }
                                                    )}
                                                </ul>
                                            </div>
                                            <div className={`portal-dropdown-type ${style['group-filter-item']}`}
                                                onClick={() => {
                                                    setIsShowSearch(true)
                                                }}>
                                                <button style={{ border: "0px", padding: "9px 14px" }} type="button">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="19" height="19"
                                                        viewBox="0 0 19 19" fill="none">
                                                        <path
                                                            d="M8.70833 15.0417C12.2061 15.0417 15.0417 12.2061 15.0417 8.70833C15.0417 5.21053 12.2061 2.375 8.70833 2.375C5.21053 2.375 2.375 5.21053 2.375 8.70833C2.375 12.2061 5.21053 15.0417 8.70833 15.0417Z"
                                                            stroke="white" strokeLinecap="round"
                                                            strokeLinejoin="round" />
                                                        <path d="M16.625 16.6249L13.1812 13.1812" stroke="white"
                                                            strokeLinecap="round" strokeLinejoin="round" />
                                                    </svg>
                                                </button>
                                            </div>
                                            <div className={`portal-dropdown-type ${style['group-filter-item']}`}
                                                onClick={() => {
                                                    setIsShowMap(true)
                                                }}>
                                                <button style={{ border: "0px", padding: "9px 14px" }} type="button">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="19" height="23"
                                                        viewBox="0 0 19 23" fill="none">
                                                        <path
                                                            d="M18 9.65909C18 16.1023 9.5 21.625 9.5 21.625C9.5 21.625 1 16.1023 1 9.65909C1 7.46201 1.89553 5.35492 3.48959 3.80135C5.08365 2.24779 7.24566 1.375 9.5 1.375C11.7543 1.375 13.9163 2.24779 15.5104 3.80135C17.1045 5.35492 18 7.46201 18 9.65909Z"
                                                            stroke="white" strokeLinecap="round"
                                                            strokeLinejoin="round" />
                                                        <path
                                                            d="M9.49946 12.279C10.7798 12.279 11.8176 11.2329 11.8176 9.9425C11.8176 8.65206 10.7798 7.60596 9.49946 7.60596C8.21916 7.60596 7.18127 8.65206 7.18127 9.9425C7.18127 11.2329 8.21916 12.279 9.49946 12.279Z"
                                                            stroke="white" strokeLinecap="round"
                                                            strokeLinejoin="round" />
                                                    </svg>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </>
                            )}
                        </div>

                        <div className={`row`}>
                            {restaurants && restaurants.length == 0 && closeRestaurants && closeRestaurants.length == 0 ? (
                                <div
                                    className={`${style['portal-restaurant-no-result']} col-12`}>{trans('no-result')}</div>
                            ) : (
                                <>
                                    <div id='category-bar' className={`${style['portal-category-bar']} col-md-12 col-12`}>
                                        <div onClick={() => {
                                            setCategory(null)
                                        }}
                                            className={`${style['portal-category-item']} ${!category && "active-category-item"} portal-category-item`}>
                                            {trans('portal.all')}
                                        </div>
                                        {
                                            categories && categories?.map((item: any, index: any) => {
                                                return (
                                                    <div key={index}
                                                        onClick={() => {
                                                            setCategory(item.key)
                                                        }}
                                                        className={`${style['portal-category-item']} ${category == item?.key && "active-category-item"} portal-category-item`}>
                                                        {item.title}
                                                    </div>
                                                )
                                            })
                                        }
                                    </div>
                                    {!isLoading && (
                                        <div className={`${style['portal-restaurant']} col-12`}
                                            style={{ minHeight: 'calc(100vh - 200px)' }}>
                                            <InfiniteScroll
                                                dataLength={restaurants ? restaurants.length : 0}
                                                next={() => fetchMoreData(1)}
                                                hasMore={true}
                                                loader={<> </>}
                                            >
                                                {
                                                    restaurants && restaurants?.map((item: any, index: any) => {
                                                        return (
                                                            <RestaurantCard key={index} index={index} item={item}
                                                                orderType={orderType} isOpen={true} />
                                                        )
                                                    })
                                                }
                                            </InfiniteScroll>

                                            {
                                                closeRestaurants && closeRestaurants.length > 0 && (
                                                    <>
                                                        <div
                                                            className={`${style['portal-restaurant-close']}`}>{trans('currently-closed')}</div>
                                                        <InfiniteScroll
                                                            dataLength={closeRestaurants ? closeRestaurants.length : 0}
                                                            next={() => fetchMoreData(0)}
                                                            hasMore={true}
                                                            loader={<> </>}
                                                        >
                                                            {
                                                                closeRestaurants && closeRestaurants?.map((item: any, index: any) => {
                                                                    return (
                                                                        <RestaurantCard key={index} index={index} isOpen={false}
                                                                            item={item} orderType={orderType} />
                                                                    )
                                                                })
                                                            }
                                                        </InfiniteScroll>
                                                    </>
                                                )
                                            }
                                        </div>
                                    )}
                                </>
                            )}
                        </div>
                    </>
                )}
            </div>
            <div className="res-desktop row">
                <div id="header-desktop" className={`${style['header']}`} style={{ width: '100%', zIndex: 1000 }}>
                    <HeaderPortal toggleProfileUpdatePopup={toggleProfileUpdatePopup} toggleLoginPopUp={toggleLoginPopUp} />
                    <div className={`row ${style['portal-search-header']} portal-search-header`} style={{ position: 'relative' }}>
                        <div className={`col-md-12`}>
                            <div className={`${style['group-search-input']} group-search-input`}>
                                <input
                                    type="text"
                                    className={`${style['search-input']} search-input`}
                                    autoComplete={'off'}
                                    id={variables.inputing}
                                    ref={inputRefMap}
                                    onKeyUp={handleSearchedMap}
                                    onFocus={() => setIsFocused(true)}
                                    onBlur={() => setIsFocused(false)}
                                    value={inputValue}
                                    onChange={(e: any) => setInputValue(e.target.value)}
                                    placeholder={trans('fill-postal-code')}
                                />
                                <div className={`${style['search-icon']} search-icon`}>
                                    <svg width="21" height="21" viewBox="0 0 21 21" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M9.55555 17.1111C13.7284 17.1111 17.1111 13.7284 17.1111 9.55555C17.1111 5.38274 13.7284 2 9.55555 2C5.38274 2 2 5.38274 2 9.55555C2 13.7284 5.38274 17.1111 9.55555 17.1111Z" stroke="white" strokeWidth="2.55555" strokeLinecap="round" strokeLinejoin="round"/>
                                        <path d="M18.9999 18.9999L14.8916 14.8916" stroke="white" strokeWidth="2.55555" strokeLinecap="round" strokeLinejoin="round"/>
                                    </svg>
                                </div>

                            </div>
                            {listAddress && listAddress.length > 0 && isShow && (
                                <div className={variables.listContain}>
                                    {listAddress.map((item: any, index: any) => (
                                        <div
                                            className={`d-flex flex-row justify-content-between ${variables.listGroup} ${index > 0 ? 'no-padding' : ''} ms-3`}
                                            key={item.id}
                                            onClick={() => handleItemClick(item)}
                                            style={{ cursor: 'pointer', paddingTop: index == 0 ? '15px' : '' }}
                                        >
                                            <div>
                                                <p className='list-group-text'>
                                                    {item.postcode},&nbsp;
                                                    {item.address && item.city && item.address === item.city.name ? item.address : `${item.address ? item.address : ''} ${item.city ? item.city.name : ''}`}
                                                </p>
                                            </div>
                                        </div>
                                    ))}

                                </div>
                            )}
                        </div>
                    </div>
                </div>
                <div className={`col-md-12`}>
                    {!isShowMap && (
                        <div className={`${style['category-bar']}`}>
                            <div className={`${style['portal-category-bar']}`} id="categories-group">
                                {left && (
                                    <svg className={`${style['portal-prev-btn']}`} xmlns="http://www.w3.org/2000/svg"
                                        onClick={() => scrollBar(-scrollValue)}
                                        width="10" height="13" viewBox="0 0 10 13" fill="none">
                                        <line x1="1" y1="-1" x2="9.5711" y2="-1" transform="matrix(-0.798167 -0.602437 0.580275 -0.814421 10 11)" stroke="#ABA765" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" />
                                        <line x1="1" y1="-1" x2="8.44273" y2="-1" transform="matrix(-0.833975 0.551803 -0.529751 -0.848153 8.875 0)" stroke="#ABA765" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" />
                                    </svg>
                                )}
                                <div id="categories" ref={ref} className={`${style['portal-category-contents']}`}>
                                    {categories && (
                                        <div onClick={() => { setCategory(null) }} role={'button'}
                                            className={`${style['portal-category-item']} ${!category && "active-category-item"} portal-category-item ms-0`}>
                                            {trans('portal.all')}
                                        </div>
                                    )}

                                    {categories && categories?.map((item: any, index: any) => {
                                        return (
                                            <div key={index} role={'button'}
                                                onClick={() => {
                                                    setCategory(item.key)
                                                }}
                                                className={`${style['portal-category-item']} ${category == item?.key && "active-category-item"} portal-category-item`}>
                                                {item.title}
                                            </div>
                                        )
                                    }
                                    )}
                                </div>
                                {right && (
                                    <svg width="10" height="13" viewBox="0 0 10 13" className={`${style['portal-next-btn']}`}
                                        onClick={() => scrollBar(scrollValue)}
                                        fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <line x1="1" y1="-1" x2="9.5711" y2="-1" transform="matrix(0.798167 0.602437 -0.580275 0.814421 0 2)" stroke="#ABA765" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" />
                                        <line x1="1" y1="-1" x2="8.44273" y2="-1" transform="matrix(0.833975 -0.551803 0.529751 0.848153 1.125 13)" stroke="#ABA765" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" />
                                    </svg>
                                )}
                            </div>
                        </div>
                    )}
                </div>
                {getToggleLoginPopUp && (
                    <PortalLoginDesktopPopup getToggleLoginPopUp={getToggleLoginPopUp} setToggleLoginPopUp={setToggleLoginPopUp} />
                )}
                <div className={`col-md-12`}>
                    <div className="row" style={{ marginBottom: "100px" }}>
                        <div className={`col-md-3`} style={{ padding: "30px 43px", minWidth: "255px"}}>
                            <div className={`d-flex ${style['group-filter']}`}>
                                {
                                    orderTypes.map((item: any, index: any) => {
                                        return (
                                            <div className={`col-md-6 ${style['group-filter-item']} ${index === orderType ? style['group-filter-item-active'] : ''}`}
                                                 onClick={() => {
                                                     setOrderType(index)
                                                 }} key={index} role={"button"}>
                                                { index === 0 && (
                                                    orderType == index ? (
                                                        <svg width="22" height="23" viewBox="0 0 22 23" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                            <path d="M17.9649 11.9627C17.45 9.21682 17.1926 7.84388 16.2153 7.03278C15.238 6.22168 13.8411 6.22168 11.0474 6.22168H10.4129C7.61919 6.22168 6.22234 6.22168 5.24502 7.03278C4.2677 7.84388 4.01027 9.21682 3.49542 11.9627C2.77146 15.8238 2.40947 17.7545 3.46484 19.0261C4.52021 20.2977 6.48445 20.2977 10.4129 20.2977H11.0474C14.9758 20.2977 16.94 20.2977 17.9955 19.0261C18.6077 18.2883 18.7429 17.3287 18.6072 15.899" stroke="white" strokeWidth="1.5" strokeLinecap="round"/>
                                                            <path d="M8.0907 6.22214V5.34239C8.0907 3.88477 9.2723 2.70312 10.73 2.70312C12.1876 2.70312 13.3692 3.88477 13.3692 5.34239V6.22214" stroke="white" strokeWidth="1.5" strokeLinecap="round"/>
                                                        </svg>
                                                    ) : (
                                                        <svg width="22" height="22" viewBox="0 0 22 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                            <path d="M17.7918 11.0193C17.2769 8.27346 17.0195 6.90052 16.0422 6.08942C15.0649 5.27832 13.668 5.27832 10.8743 5.27832H10.2398C7.44609 5.27832 6.04924 5.27832 5.07192 6.08942C4.0946 6.90052 3.83718 8.27346 3.32233 11.0193C2.59836 14.8805 2.23638 16.8111 3.29175 18.0827C4.34712 19.3544 6.31135 19.3544 10.2398 19.3544H10.8743C14.8027 19.3544 16.7669 19.3544 17.8224 18.0827C18.4346 17.3449 18.5698 16.3854 18.4341 14.9556" stroke="#1E1E1E" strokeWidth="1.5" strokeLinecap="round"/>
                                                            <path d="M7.91772 5.27878V4.39903C7.91772 2.94141 9.09932 1.75977 10.557 1.75977C12.0146 1.75977 13.1962 2.94141 13.1962 4.39903V5.27878" stroke="#1E1E1E" strokeWidth="1.5" strokeLinecap="round"/>
                                                        </svg>
                                                    )
                                                )}
                                                {index === 1 && (
                                                    orderType == index ? (
                                                        <svg width="24" height="19" viewBox="0 0 24 19" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                            <g clipPath="url(#clip0_12_929)">
                                                                <path d="M5.61044 0.550781C2.79403 0.550781 0.500244 2.84456 0.500244 5.66098C0.500244 6.75162 0.846852 7.76059 1.42937 8.59172V14.9541C1.42937 15.7253 2.05181 16.3459 2.82306 16.3459H3.79574C4.01714 17.6598 5.16403 18.6688 6.53957 18.6688C7.91511 18.6688 9.06201 17.6598 9.2834 16.3459H13.5806C13.9254 16.3459 14.2394 16.2062 14.4753 15.9866C14.713 16.1971 15.016 16.3423 15.3626 16.3459H15.8744C16.0958 17.6598 17.2427 18.6688 18.6182 18.6688C20.1516 18.6688 21.4056 17.4148 21.4056 15.8814C21.4056 14.348 20.1516 13.094 18.6182 13.094C17.2427 13.094 16.0958 14.103 15.8744 15.4168H15.3699C15.2828 15.4168 15.1521 15.3624 15.0542 15.2644C14.9543 15.1646 14.9017 15.0375 14.9017 14.9523V6.12554C14.9017 5.95133 15.1921 5.66098 15.3663 5.66098H19.0828C19.4512 5.66098 19.9756 6.06385 20.3313 6.4903L20.4039 6.59011H18.1537C17.4133 6.59011 16.76 7.18896 16.76 7.9838V9.84205C16.76 10.2286 16.9396 10.5625 17.1864 10.8093C17.4332 11.0561 17.7671 11.2357 18.1537 11.2357H22.7993V14.9523C22.7993 15.1265 22.5089 15.4168 22.3347 15.4168H21.4056V16.3459H22.3347C23.106 16.3459 23.7284 15.7235 23.7284 14.9523V10.4863C23.7284 9.42286 22.9862 8.48284 22.9862 8.48284L22.9808 8.4774L21.0554 5.9096L21.0481 5.90052C20.6198 5.38333 19.972 4.73185 19.0828 4.73185H15.3663C15.2011 4.73185 15.0469 4.76452 14.9017 4.81714V3.75917C14.9017 3.046 14.3392 2.40904 13.5951 2.40904H9.55016C8.61015 1.27485 7.19286 0.550781 5.61044 0.550781ZM5.61044 1.47991C7.92419 1.47991 9.79152 3.34724 9.79152 5.66098C9.79152 7.97473 7.92419 9.84205 5.61044 9.84205C3.2967 9.84205 1.42937 7.97473 1.42937 5.66098C1.42937 3.34724 3.2967 1.47991 5.61044 1.47991ZM5.60319 1.93721C5.34731 1.94266 5.14225 2.15316 5.14588 2.40904V5.09479C4.9753 5.23453 4.8773 5.4414 4.8773 5.66098C4.8773 5.68457 4.87912 5.70816 4.88093 5.73357L3.88829 6.72621C3.76671 6.84235 3.71771 7.01656 3.76126 7.17807C3.803 7.34139 3.93003 7.46842 4.09335 7.51016C4.25486 7.55371 4.42907 7.50472 4.54521 7.38313L5.53786 6.39049C5.56145 6.39231 5.58685 6.39412 5.61044 6.39412C6.01512 6.39412 6.34358 6.06566 6.34358 5.66098C6.34358 5.4414 6.24559 5.23453 6.07501 5.09479V2.40904C6.07682 2.28382 6.02783 2.16224 5.9389 2.07332C5.84998 1.9844 5.7284 1.9354 5.60319 1.93721ZM10.1563 3.33816H13.5951C13.7947 3.33816 13.9726 3.53052 13.9726 3.75917V6.11647C13.9726 6.1201 13.9726 6.12192 13.9726 6.12554V14.9523C13.9726 14.9559 13.9726 14.9577 13.9726 14.9595V15.0248C13.9726 15.2444 13.8002 15.4168 13.5806 15.4168H9.2834C9.06201 14.103 7.91511 13.094 6.53957 13.094C5.16403 13.094 4.01714 14.103 3.79574 15.4168H2.82306C2.64885 15.4168 2.3585 15.1265 2.3585 14.9541V9.59707C3.24226 10.3302 4.37645 10.7712 5.61044 10.7712C8.42686 10.7712 10.7206 8.4774 10.7206 5.66098C10.7206 4.8244 10.5156 4.03501 10.1563 3.33816ZM18.1537 7.51924H21.1007L22.2295 9.02362C22.2313 9.02725 22.6559 9.75132 22.7449 10.3066H18.1537C18.0756 10.3066 17.945 10.254 17.8433 10.1524C17.7417 10.0507 17.6891 9.92009 17.6891 9.84205V7.9838C17.6891 7.7497 17.9649 7.51924 18.1537 7.51924ZM6.53957 14.0231C7.57214 14.0231 8.39783 14.8488 8.39783 15.8814C8.39783 16.9139 7.57214 17.7396 6.53957 17.7396C5.50701 17.7396 4.68132 16.9139 4.68132 15.8814C4.68132 14.8488 5.50701 14.0231 6.53957 14.0231ZM18.6182 14.0231C19.6508 14.0231 20.4765 14.8488 20.4765 15.8814C20.4765 16.9139 19.6508 17.7396 18.6182 17.7396C17.5857 17.7396 16.76 16.9139 16.76 15.8814C16.76 14.8488 17.5857 14.0231 18.6182 14.0231Z" fill="white"/>
                                                            </g>
                                                            <defs>
                                                                <clipPath id="clip0_12_929">
                                                                    <rect width="23" height="19" fill="white" transform="translate(0.5)"/>
                                                                </clipPath>
                                                            </defs>
                                                        </svg>
                                                    ) : (
                                                        <svg width="23" height="19" viewBox="0 0 23 19" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                            <g clipPath="url(#clip0_12_182)">
                                                                <path d="M5.11044 0.550781C2.29403 0.550781 0.000244141 2.84456 0.000244141 5.66098C0.000244141 6.75162 0.346852 7.76059 0.929371 8.59172V14.9541C0.929371 15.7253 1.55181 16.3459 2.32306 16.3459H3.29574C3.51714 17.6598 4.66403 18.6688 6.03957 18.6688C7.41511 18.6688 8.56201 17.6598 8.7834 16.3459H13.0806C13.4254 16.3459 13.7394 16.2062 13.9753 15.9866C14.213 16.1971 14.516 16.3423 14.8626 16.3459H15.3744C15.5958 17.6598 16.7427 18.6688 18.1182 18.6688C19.6516 18.6688 20.9056 17.4148 20.9056 15.8814C20.9056 14.348 19.6516 13.094 18.1182 13.094C16.7427 13.094 15.5958 14.103 15.3744 15.4168H14.8699C14.7828 15.4168 14.6521 15.3624 14.5542 15.2644C14.4543 15.1646 14.4017 15.0375 14.4017 14.9523V6.12554C14.4017 5.95133 14.6921 5.66098 14.8663 5.66098H18.5828C18.9512 5.66098 19.4756 6.06385 19.8313 6.4903L19.9039 6.59011H17.6537C16.9133 6.59011 16.26 7.18896 16.26 7.9838V9.84205C16.26 10.2286 16.4396 10.5625 16.6864 10.8093C16.9332 11.0561 17.2671 11.2357 17.6537 11.2357H22.2993V14.9523C22.2993 15.1265 22.0089 15.4168 21.8347 15.4168H20.9056V16.3459H21.8347C22.606 16.3459 23.2284 15.7235 23.2284 14.9523V10.4863C23.2284 9.42286 22.4862 8.48284 22.4862 8.48284L22.4808 8.4774L20.5554 5.9096L20.5481 5.90052C20.1198 5.38333 19.472 4.73185 18.5828 4.73185H14.8663C14.7011 4.73185 14.5469 4.76452 14.4017 4.81714V3.75917C14.4017 3.046 13.8392 2.40904 13.0951 2.40904H9.05016C8.11015 1.27485 6.69286 0.550781 5.11044 0.550781ZM5.11044 1.47991C7.42419 1.47991 9.29152 3.34724 9.29152 5.66098C9.29152 7.97473 7.42419 9.84205 5.11044 9.84205C2.7967 9.84205 0.929371 7.97473 0.929371 5.66098C0.929371 3.34724 2.7967 1.47991 5.11044 1.47991ZM5.10319 1.93721C4.84731 1.94266 4.64225 2.15316 4.64588 2.40904V5.09479C4.4753 5.23453 4.3773 5.4414 4.3773 5.66098C4.3773 5.68457 4.37912 5.70816 4.38093 5.73357L3.38829 6.72621C3.26671 6.84235 3.21771 7.01656 3.26126 7.17807C3.303 7.34139 3.43003 7.46842 3.59335 7.51016C3.75486 7.55371 3.92907 7.50472 4.04521 7.38313L5.03786 6.39049C5.06145 6.39231 5.08685 6.39412 5.11044 6.39412C5.51512 6.39412 5.84358 6.06566 5.84358 5.66098C5.84358 5.4414 5.74559 5.23453 5.57501 5.09479V2.40904C5.57682 2.28382 5.52783 2.16224 5.4389 2.07332C5.34998 1.9844 5.2284 1.9354 5.10319 1.93721ZM9.65627 3.33816H13.0951C13.2947 3.33816 13.4726 3.53052 13.4726 3.75917V6.11647C13.4726 6.1201 13.4726 6.12192 13.4726 6.12554V14.9523C13.4726 14.9559 13.4726 14.9577 13.4726 14.9595V15.0248C13.4726 15.2444 13.3002 15.4168 13.0806 15.4168H8.7834C8.56201 14.103 7.41511 13.094 6.03957 13.094C4.66403 13.094 3.51714 14.103 3.29574 15.4168H2.32306C2.14885 15.4168 1.8585 15.1265 1.8585 14.9541V9.59707C2.74226 10.3302 3.87645 10.7712 5.11044 10.7712C7.92686 10.7712 10.2206 8.4774 10.2206 5.66098C10.2206 4.8244 10.0156 4.03501 9.65627 3.33816ZM17.6537 7.51924H20.6007L21.7295 9.02362C21.7313 9.02725 22.1559 9.75132 22.2449 10.3066H17.6537C17.5756 10.3066 17.445 10.254 17.3433 10.1524C17.2417 10.0507 17.1891 9.92009 17.1891 9.84205V7.9838C17.1891 7.7497 17.4649 7.51924 17.6537 7.51924ZM6.03957 14.0231C7.07214 14.0231 7.89783 14.8488 7.89783 15.8814C7.89783 16.9139 7.07214 17.7396 6.03957 17.7396C5.00701 17.7396 4.18132 16.9139 4.18132 15.8814C4.18132 14.8488 5.00701 14.0231 6.03957 14.0231ZM18.1182 14.0231C19.1508 14.0231 19.9765 14.8488 19.9765 15.8814C19.9765 16.9139 19.1508 17.7396 18.1182 17.7396C17.0857 17.7396 16.26 16.9139 16.26 15.8814C16.26 14.8488 17.0857 14.0231 18.1182 14.0231Z" fill="#1E1E1E"/>
                                                            </g>
                                                            <defs>
                                                                <clipPath id="clip0_12_182">
                                                                    <rect width="23" height="19" fill="white"/>
                                                                </clipPath>
                                                            </defs>
                                                        </svg>

                                                    )
                                                )}
                                                {item}
                                            </div>
                                        )
                                    })
                                }
                            </div>
                            <div className={`${style['group-search-input-normal']}`}>
                                <svg width="15" height="15" viewBox="0 0 15 15" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M6.81943 12.4904C9.96924 12.4904 12.5227 9.93701 12.5227 6.78721C12.5227 3.6374 9.96924 1.08398 6.81943 1.08398C3.66963 1.08398 1.11621 3.6374 1.11621 6.78721C1.11621 9.93701 3.66963 12.4904 6.81943 12.4904Z" stroke="#676767" strokeWidth="1.92903" strokeLinecap="round" strokeLinejoin="round"/>
                                    <path d="M13.9483 13.9166L10.8472 10.8154" stroke="#676767" strokeWidth="1.92903" strokeLinecap="round" strokeLinejoin="round"/>
                                </svg>
                                <input
                                    type="text"
                                    value={tmpKeyword}
                                    className={`${style['search-input-normal']}`}
                                    id="search-input"
                                    placeholder={trans('portal.search-here') + '...'}
                                    autoComplete={'off'}
                                    onChange={(e) => {
                                        handleSearch(e)
                                    }}
                                />
                            </div>
                            <div className={`${style['group-check']}`}>
                                <div className={`${style['title-check']}`}>{trans('portal.filters')}</div>
                                <div className={`${style['check-item']}`} onClick={() => { setIsOpen(!isOpen) }} role={"button"}>
                                    {!isOpen ? (
                                        <svg width="30" height="30" viewBox="0 0 30 30" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <rect x="0.5" y="0.5" width="29" height="29" rx="4.5" fill="white" stroke="#CDCDCD"/>
                                        </svg>
                                    ) : (
                                        <svg width="30" height="30" viewBox="0 0 30 30" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <rect x="0.5" y="0.5" width="29" height="29" rx="4.5" fill="#ABA765" stroke="#ABA765"/>
                                            <path d="M23 9L12 20L7 15" stroke="white" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"/>
                                        </svg>

                                    )}
                                    <span className="ms-2">{trans('portal.opening')}</span>
                                </div>
                                <div className={`${style['check-item']}`} onClick={() => { setIsGroup(!isGroup) }} role={"button"}>
                                    {!isGroup ? (
                                        <svg width="30" height="30" viewBox="0 0 30 30" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <rect x="0.5" y="0.5" width="29" height="29" rx="4.5" fill="white" stroke="#CDCDCD"/>
                                        </svg>
                                    ) : (
                                        <svg width="30" height="30" viewBox="0 0 30 30" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <rect x="0.5" y="0.5" width="29" height="29" rx="4.5" fill="#ABA765" stroke="#ABA765"/>
                                            <path d="M23 9L12 20L7 15" stroke="white" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"/>
                                        </svg>

                                    )}
                                    <span className="ms-2">{trans('portal.group-orders')}</span>
                                </div>
                                <div className={`${style['check-item']}`} onClick={() => { setIsCheck(!isCheck) }} role={"button"}>
                                    {!isCheck ? (
                                        <svg width="30" height="30" viewBox="0 0 30 30" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <rect x="0.5" y="0.5" width="29" height="29" rx="4.5" fill="white" stroke="#CDCDCD"/>
                                        </svg>
                                    ) : (
                                        <svg width="30" height="30" viewBox="0 0 30 30" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <rect x="0.5" y="0.5" width="29" height="29" rx="4.5" fill="#ABA765" stroke="#ABA765"/>
                                            <path d="M23 9L12 20L7 15" stroke="white" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"/>
                                        </svg>

                                    )}
                                    <span className="ms-2">{trans('loyalty-cart')}</span>
                                </div>
                                <div className={`${style['check-item']}`} onClick={() => { setIsDiscount(!isDiscount) }} role={"button"}>
                                    {!isDiscount ? (
                                        <svg width="30" height="30" viewBox="0 0 30 30" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <rect x="0.5" y="0.5" width="29" height="29" rx="4.5" fill="white" stroke="#CDCDCD"/>
                                        </svg>
                                    ) : (
                                        <svg width="30" height="30" viewBox="0 0 30 30" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <rect x="0.5" y="0.5" width="29" height="29" rx="4.5" fill="#ABA765" stroke="#ABA765"/>
                                            <path d="M23 9L12 20L7 15" stroke="white" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"/>
                                        </svg>

                                    )}
                                    <span className="ms-2">{trans('portal.promoties')}</span>
                                </div>
                            </div>
                            {orderType == 1 && (
                                <>
                                    <div className={`${style['group-search-radio']}`}>
                                        <div className={`${style['title-radio']}`}>{trans('portal.min-order-amount')}</div>
                                        <div className={`${style['group-radio']}`} onClick={() => { setMinAmountValue(null) }} role={`button`}>
                                            {!minAmountValue ? (
                                                <svg width="30" height="30" viewBox="0 0 30 30" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <rect x="0.5" y="0.5" width="29" height="29" rx="14.5" fill="white" stroke="#ABA765"/>
                                                    <rect x="6.5" y="6.5" width="17" height="17" rx="8.5" fill="#ABA765" stroke="#ABA765"/>
                                                </svg>
                                            ) : (
                                                <svg width="30" height="30" viewBox="0 0 30 30" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <rect x="0.5" y="0.5" width="29" height="29" rx="14.5" fill="white" stroke="#CDCDCD"/>
                                                </svg>
                                            )}
                                            <span className="ms-2">{trans('portal.no-preference')}</span>
                                        </div>
                                        {orderMinAmount && Object.entries(orderMinAmount).map((item: any, index: any) => {
                                                return (
                                                    <div className={`${style['group-radio']}`} onClick={() => { setMinAmountValue(item[0]) }} key={index} role={`button`}>
                                                        {item[0] == minAmountValue ? (
                                                            <svg width="30" height="30" viewBox="0 0 30 30" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                <rect x="0.5" y="0.5" width="29" height="29" rx="14.5" fill="white" stroke="#ABA765"/>
                                                                <rect x="6.5" y="6.5" width="17" height="17" rx="8.5" fill="#ABA765" stroke="#ABA765"/>
                                                            </svg>
                                                        ) : (
                                                            <svg width="30" height="30" viewBox="0 0 30 30" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                <rect x="0.5" y="0.5" width="29" height="29" rx="14.5" fill="white" stroke="#CDCDCD"/>
                                                            </svg>
                                                        )}
                                                        <span className="ms-2">{item[1]}</span>
                                                    </div>
                                                )
                                            }
                                        )}
                                    </div>
                                    <div className={`${style['group-search-radio']}`}>
                                        <div className={`${style['title-radio']}`}>{trans('portal.delivery-charge')}</div>
                                        <div className={`${style['group-radio']}`} onClick={() => { setDeliveryCharge(null) }} role={`button`}>
                                            {!deliveryCharge ? (
                                                <svg width="30" height="30" viewBox="0 0 30 30" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <rect x="0.5" y="0.5" width="29" height="29" rx="14.5" fill="white" stroke="#ABA765"/>
                                                    <rect x="6.5" y="6.5" width="17" height="17" rx="8.5" fill="#ABA765" stroke="#ABA765"/>
                                                </svg>
                                            ) : (
                                                <svg width="30" height="30" viewBox="0 0 30 30" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <rect x="0.5" y="0.5" width="29" height="29" rx="14.5" fill="white" stroke="#CDCDCD"/>
                                                </svg>
                                            )}
                                            <span className="ms-2">{trans('portal.no-preference')}</span>
                                        </div>
                                        {orderDeliveryCharge && Object.entries(orderDeliveryCharge).map((item: any, index: any) => {
                                                return (
                                                    <div className={`${style['group-radio']}`} onClick={() => { setDeliveryCharge(item[0]) }} key={index} role={`button`}>
                                                        {item[0] == deliveryCharge ? (
                                                            <svg width="30" height="30" viewBox="0 0 30 30" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                <rect x="0.5" y="0.5" width="29" height="29" rx="14.5" fill="white" stroke="#ABA765"/>
                                                                <rect x="6.5" y="6.5" width="17" height="17" rx="8.5" fill="#ABA765" stroke="#ABA765"/>
                                                            </svg>
                                                        ) : (
                                                            <svg width="30" height="30" viewBox="0 0 30 30" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                <rect x="0.5" y="0.5" width="29" height="29" rx="14.5" fill="white" stroke="#CDCDCD"/>
                                                            </svg>
                                                        )}
                                                        <span className="ms-2">{item[1]}</span>
                                                    </div>
                                                )
                                            }
                                        )}
                                    </div>
                                </>
                            )}
                        </div>
                        {isShowMap ? (
                            <div className={`col-md-9`} style={{ padding: "30px 43px" }}>
                                <div className={`d-flex ${style['group-sort']} mb-2`}>
                                    <div className={`dropdown portal-dropdown-type ${style['group-filter-item']}`}>
                                    </div>
                                    <div className={`${style['group-map']}`} onClick={() => {setIsShowMap(false)}} role="button">
                                        <svg width="29" height="29" viewBox="0 0 29 29" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M10 7.375H25.4375" stroke="#ABA765" strokeWidth="2.375" strokeLinecap="round" strokeLinejoin="round"/>
                                            <path d="M10 14.5H25.4375" stroke="#ABA765" strokeWidth="2.375" strokeLinecap="round" strokeLinejoin="round"/>
                                            <path d="M10 21.625H25.4375" stroke="#ABA765" strokeWidth="2.375" strokeLinecap="round" strokeLinejoin="round"/>
                                            <path d="M4.0625 7.375H4.07437" stroke="#ABA765" strokeWidth="2.375" strokeLinecap="round" strokeLinejoin="round"/>
                                            <path d="M4.0625 14.5H4.07437" stroke="#ABA765" strokeWidth="2.375" strokeLinecap="round" strokeLinejoin="round"/>
                                            <path d="M4.0625 21.625H4.07437" stroke="#ABA765" strokeWidth="2.375" strokeLinecap="round" strokeLinejoin="round"/>
                                        </svg>
                                        <span>{trans('portal.list-view')}</span>
                                    </div>
                                </div>
                                <RestaurantMap location={location} positions={positions} dataPositions={dataPositions} setIsShowMap={setIsShowMap} />
                            </div>
                        ) : (
                            <div className={`col-md-9`} style={{ padding: "30px 43px" }}>
                                {restaurants && restaurants.length == 0 && closeRestaurants && closeRestaurants.length == 0 ? (
                                    <div style={{textAlign: "center"}}>
                                        <Image
                                            alt='kokette'
                                            src="/img/no-result.png"
                                            width={400}
                                            height={290}
                                        />

                                        <div className={`${style['portal-restaurant-no-result']} col-12`}>{trans('portal.no-result')}</div>
                                        <div className={`${style['portal-restaurant-sub-no-result']} col-12`}>{trans('portal.sub-no-result')}</div>
                                    </div>
                                ) : (
                                    <>
                                        {!isLoading ? (
                                            <>
                                                <div className={`d-flex ${style['group-sort']}`}>
                                                    <div className={`dropdown portal-dropdown-type ${style['group-filter-item']}`}>
                                                        <button type="button"
                                                                data-bs-toggle="dropdown"
                                                                aria-expanded="false">
                                                            {trans('sort-by')} <span className="px-1" style={{textTransform: "lowercase"}}>{orderSortType && orderSortType[orderSort]}</span>
                                                            <svg width="14" height="8" viewBox="0 0 14 8" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                <path d="M1 1L7 7L13 1" stroke="#1E1E1E" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"/>
                                                            </svg>
                                                        </button>
                                                        <ul className={`dropdown-menu ${style['dropdown-menu']}`}>
                                                            <li><a className={`dropdown-item ${style['disabled']}`}
                                                                   href="#">{trans('portal.sort-by')}</a></li>
                                                            {orderSortType && Object.entries(orderSortType).map((item: any, index: any) => {
                                                                    return (
                                                                        <li key={index}>
                                                                            <a className={`dropdown-item ${item[0] == orderSort ? 'active-item' : ''} ${style['dropdown-item']}`}
                                                                               href="#" onClick={() => {
                                                                                setOrderSort(item[0]);
                                                                            }}>
                                                                                {item[0] == orderSort ? (
                                                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="22" viewBox="0 0 24 22" fill="none">
                                                                                        <path d="M20 5.5L9 15.5833L4 11" stroke="#B5B268" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" />
                                                                                    </svg>
                                                                                ) : (
                                                                                    <span style={{ marginLeft: '20px' }}></span>
                                                                                )}
                                                                                {item[1]} {((orderType != 2 && item[0] == 0) || (orderType == 2 && item[0] == 4)) && "(" + trans('portal.default') + ")"}
                                                                            </a>
                                                                        </li>
                                                                    )
                                                                }
                                                            )}
                                                        </ul>
                                                    </div>
                                                    <div className={`${style['group-map']}`} role="button" onClick={() => { setIsShowMap(true) }}>
                                                        <svg width="24" height="25" viewBox="0 0 24 25" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                            <path d="M1 6.5V22.5L8 18.5L16 22.5L23 18.5V2.5L16 6.5L8 2.5L1 6.5Z" stroke="#ABA765" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"/>
                                                            <path d="M8 2.5V18.5" stroke="#ABA765" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"/>
                                                            <path d="M16 6.5V22.5" stroke="#ABA765" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"/>
                                                        </svg>
                                                        <span>{trans('portal.map-view')}</span>
                                                    </div>
                                                </div>
                                                <div className={`${style['portal-restaurant']} col-12`}
                                                     style={{ minHeight: 'calc(100vh - 400px)' }}>
                                                    <InfiniteScroll
                                                        dataLength={restaurants ? restaurants.length : 0}
                                                        next={() => fetchMoreData(1)}
                                                        hasMore={true}
                                                        loader={<> </>}
                                                    >
                                                        {
                                                            restaurants && restaurants?.map((item: any, index: any) => {
                                                                return (
                                                                    <RestaurantCard key={index} index={index} item={item} isOpen={true}
                                                                                    orderType={orderType} />
                                                                )
                                                            })
                                                        }
                                                    </InfiniteScroll>

                                                    {
                                                        closeRestaurants && closeRestaurants.length > 0 && !isOpen && (
                                                            <>
                                                                <div
                                                                    className={`${style['portal-restaurant-close']}`}>{trans('portal.close')}</div>
                                                                <InfiniteScroll
                                                                    dataLength={closeRestaurants ? closeRestaurants.length : 0}
                                                                    next={() => fetchMoreData(0)}
                                                                    hasMore={true}
                                                                    loader={<> </>}
                                                                >
                                                                    {
                                                                        closeRestaurants && closeRestaurants?.map((item: any, index: any) => {
                                                                            return (
                                                                                <RestaurantCard key={index} index={index} isOpen={false}
                                                                                                item={item} orderType={orderType} />
                                                                            )
                                                                        })
                                                                    }
                                                                </InfiniteScroll>
                                                            </>
                                                        )
                                                    }
                                                </div>
                                            </>
                                        ) : (
                                            <div className={`${style['portal-restaurant']} col-12`}
                                                 style={{ minHeight: 'calc(100vh - 400px)' }}>
                                                <Image
                                                    alt='kokette'
                                                    src="/img/loading1.png"
                                                    width={100}
                                                    height={100}
                                                    priority={true}
                                                    sizes="100vw"
                                                    style={{ width: '100%', height: 'auto' }} // optional
                                                />
                                            </div>
                                        )}
                                    </>
                                )}

                            </div>
                        )}
                    </div>
                </div>
                <FooterPortal trans={trans} lang={language} from = {null}/>
            </div>
            {isProfileUpdatePopupOpen && (
                <ProfileUpdatePortal toggleProfileUpdatePopup={toggleProfileUpdatePopup} />
            )}
        </>
    );
};
