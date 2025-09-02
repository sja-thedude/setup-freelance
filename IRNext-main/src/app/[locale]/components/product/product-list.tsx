'use client'

import FavoriteDesk from '@/app/[locale]/components/img/favorite/favoriteDesk';
import FavoriteSmall from '@/app/[locale]/components/img/favorite/favoriteSmall';
import KoketteDesk from '@/app/[locale]/components/img/kokette/koketteDesk';
import KoketteSmall from '@/app/[locale]/components/img/kokette/koketteSmall';
import Header from '@/app/[locale]/components/layouts/header/header';
import CouponPopup from '@/app/[locale]/components/layouts/popup/coupon';
import IntroducePopup from '@/app/[locale]/components/layouts/popup/introduce';
import MenuTableOrdering from "@/app/[locale]/components/menu/menu";
import Menu from "@/app/[locale]/components/menu/menu-plus";
import UserWebsiteCart from "@/app/[locale]/components/ordering/cart/userWebsiteCart";
import ProductDesk from '@/app/[locale]/components/product/desktop/product';
import Favorites from "@/app/[locale]/components/product/favorites";
import Product from "@/app/[locale]/components/product/product";
import {currency} from '@/config/currency';
import useScrollPosition, {useDetectScrollToBottom, useDetectScrollToTop} from '@/hooks/useScrollPosition';
import {useI18n} from '@/locales/client';
import {useAppDispatch, useAppSelector} from '@/redux/hooks';
import {useGetCouponsQuery} from '@/redux/services/couponsApi';
import {useGetWorkspaceDataByIdQuery} from '@/redux/services/workspace/workspaceDataApi';
import {addMaxHeightNonePhoto, addMaxHeightPhoto, addStepRoot, changeType} from '@/redux/slices/cartSlice';
import {selectCouponData} from "@/redux/slices/couponSlice";
import {setFlagDesktopChangeType} from '@/redux/slices/flagDesktopChangeTypeSilce';
import {setflagForcusData} from '@/redux/slices/flagForcusSlice';
import {setflagSortData} from '@/redux/slices/flagSortSlice';
import {setGroupOrderData} from '@/redux/slices/groupOrderSlice';
import {reloadProductHeightCalculation} from '@/redux/slices/product/productSlice';
import {api} from "@/utils/axios";
import useMediaQuery from '@mui/material/useMediaQuery';
import Cookies from "js-cookie";
import _, {add} from 'lodash';
import {usePathname} from 'next/navigation';
import cartStyle from 'public/assets/css/cart.module.scss';
import React, {useEffect, useRef, useState} from 'react';
import {batch, useSelector} from "react-redux";
import 'react-responsive-carousel/lib/styles/carousel.min.css';
import Slider from 'react-slick';
import 'slick-carousel/slick/slick-theme.css';
import 'slick-carousel/slick/slick.css';
import ProductFooter from './desktop/productFooter';
import variables from '/public/assets/css/food.module.scss';

const navItem = variables['nav-item'];
const couponsList = variables['coupons-list'];
const doting = variables['doting'];
const dotingList = variables['doting-list'];
const navText = variables['nav-text'];
const emptySearch = variables['empty-search'];
const couping = variables['couping'];
const couName = variables['cou-name'];
const couCode = variables['cou-code'];
const searching = variables['searching'];
const searchInput = variables['searchInput'];
const inputing = variables['inputing'];
const searchingDesk = variables['searching-desk'];
const searchDeskChild = variables['searching-desk-child'];
const searhIcon = variables['searh-icon'];
const favoriteIcon = variables['favorite-icon'];
const navItemDesk = variables['nav-item-desk'];
const navTextForcus = variables['nav-text-forcus'];
const counponDesk = variables['counpon-desk'];
const counponDeskContain = variables['counpon-desk-contain'];
const counponDeskContainAll = variables['counpon-desk-contain-all'];
const counponDeskContainAllContainer = variables['counpon-desk-contain-all-container'];
const couponCodeDesk = variables['coupon-code-desk'];
const mapDesk = variables['map-desk'];
const couponDeskDiscount = variables['coupon-desk-discount'];
const couponDeskInfo = variables['coupon-desk-info'];
import moment from 'moment';

interface Coupon {
    id: number;
    created_at: string;
    updated_at: string;
    code: string;
    promo_name: string;
    workspace_id: number;
    workspace: {
        id: number;
        name: string;
    };
    max_time_all: number;
    max_time_single: number;
    currency: string;
    discount: string;
    expire_time: string;
    discount_type: number;
    percentage: number;
}

const ORDER_TYPE = {
    TAKE_AWAY: 0,
    DELIVERY: 1,
    GROUP_ORDER: 2
};

const MENU_TYPE = {
    TAKE_AWAY: 1,
    DELIVERY: 2,
    GROUP_ORDER: 3
}


function NextArrowDesk(props: any) {
    const {className, style, onClick, color} = props;

    // Kiểm tra xem có onClick hay không để đặt giá trị style.display
    const displayValue = onClick === null ? 'none' : 'block';

    const customStyle = {
        ...style,
        right: '15px',
        width: '10px',
        height: '12px',
        display: displayValue,  // Sử dụng giá trị display tùy thuộc vào có onClick hay không
    };

    return (
        <div style={{position: 'absolute', right: '0px', top: '8px', padding: '15px'}} onClick={onClick}>
            <svg
                xmlns="http://www.w3.org/2000/svg"
                width="10"
                height="13"
                viewBox="0 0 10 13"
                fill="none"
                className={className}
                style={customStyle}
                onClick={onClick}
            >
                <path
                    d="M1.37842 1.78809L8.21959 6.95163"
                    stroke={color ? color : 'black'}
                    strokeWidth="2"
                    strokeLinecap="round"
                    strokeLinejoin="round"
                />
                <line
                    x1="1"
                    y1="-1"
                    x2="8.44273"
                    y2="-1"
                    transform="matrix(0.833975 -0.551803 0.529751 0.848153 1.125 13)"
                    stroke={color ? color : 'black'}
                    strokeWidth="2"
                    strokeLinecap="round"
                    strokeLinejoin="round"
                />
            </svg>
            <div style={{
                width: '30px',
                height: '30px',
                position: 'absolute',
                zIndex: '5000',
                top: '8px',
                right: '-90px'
            }} onClick={onClick}>
            </div>
        </div>
    );
}

function PrevArrowDesk(props: any) {
    const {className, style, onClick, color} = props;
    const displayValue = onClick === null ? 'none' : 'block';
    const customStyle = {
        ...style,
        width: '10px',
        height: '12px',
        position: 'absolute',
        left: '12px',
        display: displayValue,
    };

    return (
        <div style={{position: 'absolute', left: '3px', top: '8px', padding: '15px 13px'}} onClick={onClick}>
            <svg
                xmlns="http://www.w3.org/2000/svg"
                width="10"
                height="13"
                viewBox="0 0 10 13"
                fill="none"
                className={className}
                style={customStyle}
                onClick={onClick}
            >
                <g transform="scale(-1, 1) translate(-10, 0)">
                    <path
                        d="M1.37842 1.78809L8.21959 6.95163"
                        stroke={color ? color : 'black'}
                        strokeWidth="2"
                        strokeLinecap="round"
                        strokeLinejoin="round"
                    />
                    <line
                        x1="1"
                        y1="-1"
                        x2="8.44273"
                        y2="-1"
                        transform="matrix(0.833975 -0.551803 0.529751 0.848153 1.125 13)"
                        stroke={color ? color : 'black'}
                        strokeWidth="2"
                        strokeLinecap="round"
                        strokeLinejoin="round"
                    />
                </g>
            </svg>
        </div>
    );
}
const DATE_FORMAT = 'YYYY-MM-DD';
const TIME_FORMAT = 'HH:mm';

export default function ProductList({baseLink, setIsLoading}: { baseLink: any, setIsLoading: any }) {
    // Get coupons list
    const trans = useI18n()
    const language = Cookies.get('Next-Locale');
    const apiSliceCoupon = useSelector(selectCouponData);
    const {data: couponsData} = useGetCouponsQuery({});
    const step = useAppSelector((state) => state.cart.stepRoot)
    const handleActive = (stepActive: number) => {
        dispatch(addStepRoot(stepActive))
    }
    const bodyHeight = document.body.scrollHeight;
    // Get workspace info
    const workspaceId = useAppSelector((state) => state.workspaceData.globalWorkspaceId)
    const {data: apiDataToken} = useGetWorkspaceDataByIdQuery({id: workspaceId})
    const workspaceInfo = apiDataToken?.data;
    const apiData = workspaceInfo?.setting_generals;
    const color = useAppSelector((state) => state.workspaceData.globalWorkspaceColor)
    const infoCoupons = apiSliceCoupon?.data || couponsData?.data;
    const [isSortOpen, setIsSortOpen] = useState(false);
    const [isAtStart, setIsAtStart] = useState(false);
    const groupOrder = useAppSelector<any>((state: any) => state.groupOrder.data);
    const isMobile = useMediaQuery('(max-width: 1279px)');
    const isMediumDesktop = useMediaQuery('(max-width: 1700px)');
    const isSmallDesktop = useMediaQuery('(max-width: 1490px)');
    const isSuperSmallDesktop = useMediaQuery('(max-width: 1359px)');
    const [isProgrammaticallyScroll, setIsProgrammaticallyScroll] = useState(false);
    let cart = useAppSelector((state) => state.cart.rootData)
    let coupons: Coupon[] | undefined = [];
    if (infoCoupons && apiData) {
        coupons = infoCoupons.data.filter((item: Coupon) => item.workspace_id === apiData.workspace_id);
    }
    const dispatch = useAppDispatch()
    const tokenLoggedInCookie = Cookies.get('loggedToken');
    // Get products category list
    const [openTab, setOpenTab] = React.useState(2);
    const [dataFavorites, setdataFavorites] = React.useState([]);
    const [dataSearch, setDataSearch] = useState<any>([]);
    var [dataGroupOrder, setDataGroupOrder] = useState<any>([]);
    const pathName = usePathname();
    const isTableOrdering = pathName.includes('table-ordering');
    const isSelfOrdering = pathName.includes('self-ordering');
    const groupOrderNowSlice = useAppSelector<any>((state: any) => state.cart.groupOrderSelectedNow);
    const flagForcus = useAppSelector<any>((state: any) => state.flagForcus.data);
    const flagDesktopChangeType = useAppSelector<any>((state: any) => state.flagDesktopChangeType.data);
    let openDeskTopLogin = useAppSelector<any>((state: any) => state.cart.openDeskTopLogin);


    if (tokenLoggedInCookie) {
        openDeskTopLogin = false;
    }

    useEffect(() => {
        dispatch(addMaxHeightNonePhoto(0));
        dispatch(addMaxHeightPhoto(0));
    }, []);

    useEffect(() => {
        // Đặt setFlagDesktopChangeType về false khi component được tạo ra
        dispatch(setFlagDesktopChangeType(false));
    }, []);

    const favoritesTab = async () => {
        if (openTab != 1) {
            const tokenLoggedInCookie = Cookies.get('loggedToken');

            if (groupOrderNowSlice) {
                dataGroupOrder = dataGroupOrder.map((dataGroupOrderItem: any) => {
                    if (dataGroupOrderItem.products && dataGroupOrderItem.products.length > 0) {
                        // create a copy of updatedDataGroupOrderItem
                        const updatedDataGroupOrderItem = {...dataGroupOrderItem};

                        // sort products inside updatedDataGroupOrderItem
                        updatedDataGroupOrderItem.products = updatedDataGroupOrderItem.products.filter((product: any) => {
                            return (
                                (product?.liked === true)
                            );
                        });

                        // assign updatedDataGroupOrderItem to dataGroupOrderItem
                        dataGroupOrderItem = updatedDataGroupOrderItem;
                    }
                    return dataGroupOrderItem;
                });
                // remove menuItems that have no products
                const favDataGroupOrder = (dataGroupOrder.filter((menuItem: any) => menuItem.products.length > 0))
                const productsDataFavGroupOrder = favDataGroupOrder.map((favDataGroupOrderItem: any) => favDataGroupOrderItem.products).flat();
                setdataFavorites(productsDataFavGroupOrder)
            } else {
                const res = await api.get(`products/liked?workspace_id=${workspaceId}`, {
                    headers: {
                        'Authorization': `Bearer ${tokenLoggedInCookie}`,
                        'Content-Language': language
                    }
                });

                setdataFavorites(res?.data?.data?.data)
                if (!isMobile) {
                    getMenuItems()
                }
            }

            setOpenTab(1);
            window.scrollTo(0, 0);
        } else {
            if (groupOrderNowSlice) {
                setOpenTab(4);
            } else {
                setOpenTab(2);
            }
        }
    }

    const query = new URLSearchParams(window.location.search);
    const liked = query.get('liked');

    useEffect(() => {
        if (liked === 'true' && workspaceId) {
            favoritesTab();
        }
    }, [liked, workspaceId, dataGroupOrder]);

    const handleShowing = () => {
        if (isSuperSmallDesktop) {
            return 2
        } else if (isSmallDesktop) {
            return 3
        } else if (isMediumDesktop) {
            return 4
        } else {
            return 5
        }
    }

    // Categories menu slider for mobile
    const settings = {
        className: "categories-menu-mobile",
        infinite: false,
        variableWidth: true,
        swipeToSlide: true,
        slidesToShow: 1,
        slidesToScroll: 1,
        centerMode: false,
        centerPadding: '0',
        focusOnSelect: false,
        touchMove: false,
        draggable: false,
        speed: 300,
        beforeChange: (current: number, next: number) => {
            if (sliderRefMenu.current) {
                sliderRefMenu.current.slickGoTo(next);
            }
        },
        responsive: [
            {
                breakpoint: 524,
                settings: {
                    slidesToShow: 1,
                    slidesToScroll: 1
                }
            },
        ]
    };

    // Categories menu slider for desktop
    const settingDesk = {
        className: "categories-menu-desktop",
        infinite: false,
        variableWidth: true,
        swipeToSlide: true,
        slidesToScroll: 3,
        // slidesToShow: handleShowing(),
        centerMode: false,
        centerPadding: '0',
        nextArrow: <NextArrowDesk color={color}/>,
        prevArrow: <PrevArrowDesk color={color}/>,
        paddingLeft: '30px',
        focusOnSelect: false
    };

    const settingOrder = {
        infinite: false,
        variableWidth: true
    }

    const settingCoupon = {
        className: "product-coupon-mobile-container",
        infinite: true,
        dots: true,
        autoplay: true,
        appendDots: (dots: any) => (
            <div>
                <ul className="coupon-slide-dot-mobile d-flex">
                    {dots.map((dot: any, index: any) => (
                        <div key={index}>
                            {dot}
                        </div>
                    ))}
                </ul>
            </div>
        )
    }

    const settingCouponDesk = {
        infinite: true,
        dots: true,
        autoplay: true,
        fade: true,
        appendDots: (dots: any) => (
            <div>
                <style>
                    {`
                        .coupon-slide-dot-desk button::before {
                            color: ${color ? color : '#FFF'} !important;
                        }
                    `}
                </style>
                <div className="coupon-slide-dot-desk" id={`${dotingList}`}>
                    {dots.map((dot: any, index: any) => (
                        <div id={`${doting}`} key={index} style={{position: 'absolute'}}>
                            {dot}
                        </div>
                    ))}
                </div>
            </div>
        )
    }

    // Tạo một ref cho Slider
    const sliderRef = useRef<Slider | null>(null);
    const sliderRefMenu = useRef<Slider | null>(null);
    const sliderRefDeskGroup = useRef<Slider | null>(null);
    // Hàm để điều khiển Slider
    const goToSlide = (slideIndex: any, force: boolean = false) => {
        if(!isMobile || (isMobile && !isSearchOpen) || force) {
            dispatch(setflagForcusData(true));
            if (groupOrderNowSlice) {
                if (isMobile) {
                    if (sliderRef.current) {
                        const element = document.getElementById(`decoring-${slideIndex.toString()}`);
                        if (element) {
                            if (!loadedFirstTime) {
                                element.scrollIntoView({block: "start", inline: "start", behavior: "smooth"});
                            } else {
                                setLoadedFirstTime(false);
                            }
                        }
                    }
                } else {
                    if (sliderRefDeskGroup.current) {
                        sliderRefDeskGroup.current.slickGoTo(slideIndex, true);
                    }
                }
                if (!isMobile) {
                    if (sliderRefMenu.current) {
                        sliderRefMenu.current.slickGoTo(slideIndex, true);
                    }
                    if (sliderRef.current) {
                        sliderRef.current.slickGoTo(slideIndex, true);
                    }
                }
            } else {
                if (sliderRefMenu.current) {
                    sliderRefMenu.current.slickGoTo(slideIndex, true);
    
                    const element = document.getElementById(`decoring-${slideIndex.toString()}`);
                    if (element) {
                        if (isMobile) {
                            if (!loadedFirstTime) {
                                element.scrollIntoView({block: "start", inline: "start", behavior: "smooth"});
                            } else {
                                setLoadedFirstTime(false);
                            }
                        }
                    }
                }
                if (!isMobile) {
                    if (sliderRef.current) {
                        sliderRef.current.slickGoTo(slideIndex, true);
                    }
                }
            }
        }
    }

    // Set color for  background  if scroll
    const scrolledY = useScrollPosition()

    const [openPopups, setOpenPopups] = useState<{ [key: number]: boolean }>({});
    const toggleCouponPopup = (couponIndex: number) => {
        const updatedOpenPopups: { [key: number]: boolean } = {...openPopups};
        updatedOpenPopups[couponIndex] = !updatedOpenPopups[couponIndex];
        setOpenPopups(updatedOpenPopups);
    };

    var [menuItems, setMenuItems] = useState<any>([]);
    const delivery = query.get('delivery');
    let type = useAppSelector((state) => state.cart.type)
    let products = useAppSelector((state) => state.cart.rootData)
    var [isDeliveryType, setIsDeliveryType] = useState(false);
    const [forcusElement, setForcusElement] = useState(menuItems && menuItems[0] ? menuItems[0].id : '');
    const getMenuItems = async () => {
        let headers = {};

        headers = {
            headers: {
                'Content-Language': language
            }
        }

        if (baseLink == '/category/products') {
            headers = {
                headers: {
                    'Authorization': `Bearer ${tokenLoggedInCookie}`,
                    'Content-Language': language,
                }
            }
        }

        if (workspaceId) {
            let typeOrdering = ''
            if (isTableOrdering) {
                typeOrdering = '&type=2'
            } else if (isSelfOrdering) {
                typeOrdering = '&type=3'
            }

            try {
                const res = await api.get(`categories/products?workspace_id=${workspaceId}${typeOrdering}&per_page=999&from=mobile&date=${moment().format(DATE_FORMAT)}&time=${moment().format(TIME_FORMAT)}`, headers);
                const data = res?.data?.data?.data;
                const resProducts = await api.get(`products/list?workspace_id=${workspaceId}&category_ids=${data.map((x: any) => x.id).join(',')}`, headers);
                const dataProducts = resProducts?.data?.data || [];
                const availability = await api.get(`products/validate_available_timeslot_date_time?date=${moment().format(DATE_FORMAT)}&time=${moment().format(TIME_FORMAT)}&workspace_id=${workspaceId}&product_id=${dataProducts.map((x: any) => x.id).join(',')}`, headers);

                if (delivery === 'true' || isDeliveryType || (type == 2 && products.length != 0)) {
                    const filteredData = data.filter((item: any) => item?.available_delivery === true);
                    setMenuItems(filteredData.map((x: any) => {
                        x.products = dataProducts.filter((y: any) => y.category_id === x.id)
                            .map((x: any) => {
                                x.is_available = (isTableOrdering || isSelfOrdering) ? _.get(availability, `data.data.${x.id}`, true) : true;
                                return x;
                            });
                        return x;
                    }));
                } else {
                    setMenuItems(data.map((x: any) => {
                        x.products = dataProducts.filter((y: any) => y.category_id === x.id)
                            .map((x: any) => {
                                x.is_available = (isTableOrdering || isSelfOrdering) ? _.get(availability, `data.data.${x.id}`, true) : true;
                                return x;
                            });
                        return x;
                    }));
                }
                setIsLoading(false);
            } catch (err) {
                setIsLoading(false);
            }
        } else {
            setIsLoading(false);
        }
    }

    const [loadedFirstTime, setLoadedFirstTime] = useState(false);

    useEffect(() => {
        if (menuItems && menuItems[0]) {
            if (!forcusElement) {
                if (isMobile) {
                    setLoadedFirstTime(true);
                }

                if (groupOrderNowSlice) {
                    setForcusElement(dataGroupOrder[0]?.id);
                } else {
                    setForcusElement(menuItems[0].id);
                }
            }
            if (forcusElement && !isMobile) {
                if (!flagForcus) {
                    setForcusElement(dataGroupOrder[0]?.id);
                } else {
                    setForcusElement(forcusElement);
                }
            }
        }
    }, [menuItems, groupOrderNowSlice, flagForcus, forcusElement]);

    useEffect(() => {
        getMenuItems()
    }, []);

    useEffect(() => {
        if (groupOrderNowSlice) {
            if (dataGroupOrder && dataGroupOrder.length > 3) {
                goToSlide(dataGroupOrder.findIndex((item: any) => item?.id == forcusElement))
            }
        } else {
            if (menuItems && menuItems.length >= 3) {
                goToSlide(menuItems.findIndex((item: any) => item?.id == forcusElement))
            }
        }
    }, [forcusElement, groupOrderNowSlice]);

    const [isSearchOpen, setIsSearchOpen] = useState(false);
    const [isSearchDeskOpen, setIsSearchDeskOpen] = useState(false);
    const inputRef = useRef<HTMLInputElement | null>(null);
    const inputRefDesk = useRef<HTMLInputElement | null>(null);
    const [currentCategory, setCurrentCategory] = useState(null);

    useEffect(() => {
        if (isSearchOpen && inputRef.current) {
            inputRef.current.focus();
        }
    }, [isSearchOpen]);

    useEffect(() => {
        if (isSearchDeskOpen && inputRefDesk.current) {
            inputRefDesk.current.focus();
        }
    }, [isSearchDeskOpen]);

    const toggleSearch = () => {
        setIsSearchOpen(!isSearchOpen);
        if (!isSearchOpen) {
            setOpenTab(3);
        } else {            
            if (groupOrderNowSlice) {
                setOpenTab(4)
            } else {
                setOpenTab(2)
            }

            goToSlide(0, true);
        }
    };

    const toggleSearchDesk = () => {
        setIsSearchDeskOpen(!isSearchDeskOpen);
        if (!isSearchDeskOpen) {
            setOpenTab(3);
        } else {
            if (groupOrderNowSlice) {
                setOpenTab(4)
            } else {
                setOpenTab(2)
            }
        }
    };

    const toggleExit = () => {
        if (inputRef.current) {
            if (inputRef.current.value === '') {
                toggleSearch()
            } else {
                inputRef.current.value = "";
                setDataSearch([])
                window.scrollTo({top: 0, behavior: "smooth"});
                setForcusElement(menuItems && menuItems[0] ? menuItems[0].id : '');
            }
        }
    };

    const toggleExitDesk = () => {
        if (inputRefDesk.current) {
            toggleSearchDesk()
            inputRefDesk.current.value = "";
            setDataSearch([])
            window.scrollTo({top: 0, behavior: "smooth"});
            setForcusElement(menuItems && menuItems[0] ? menuItems[0].id : '');
        }
    };

    const handleSearch = () => {
        setOpenTab(3);

        if (inputRef.current) {
            if (!inputRef.current.value) {
                setDataSearch([])
            } else {
                const keyword = inputRef.current.value.toLowerCase();
                if (groupOrderNowSlice) {
                    if (dataGroupOrder && dataGroupOrder.length > 0 && groupOrderNowSlice) {
                        dataGroupOrder = dataGroupOrder.map((dataGroupOrderItem: any) => {
                            if (dataGroupOrderItem.products && dataGroupOrderItem.products.length > 0) {
                                // Tạo một bản sao của menuItem
                                const updatedDataGroupOrderItem = {...dataGroupOrderItem};

                                // Lọc sản phẩm bên trong bản sao menuItem
                                updatedDataGroupOrderItem.products = updatedDataGroupOrderItem.products.filter((product: any) => {
                                    return (
                                        (product.name && product.name.toLowerCase().includes(keyword)) ||
                                        (product.description && product.description.toLowerCase().includes(keyword))
                                    );
                                });

                                // Gán menuItem ban đầu bằng updatedMenuItem
                                dataGroupOrderItem = updatedDataGroupOrderItem;
                            }
                            return dataGroupOrderItem;
                        });
                        // Loại bỏ các menuItem không còn sản phẩm nào
                        setDataSearch(dataGroupOrder.filter((dataGroupOrderItem: any) => dataGroupOrderItem.products.length > 0))
                    }
                } else {
                    if (menuItems && menuItems.length > 0) {
                        menuItems = menuItems.map((menuItem: any) => {
                            if (menuItem.products && menuItem.products.length > 0) {
                                // Tạo một bản sao của menuItem
                                const updatedMenuItem = {...menuItem};

                                // Lọc sản phẩm bên trong bản sao menuItem
                                updatedMenuItem.products = updatedMenuItem.products.filter((product: any) => {
                                    return (
                                        (product.name && product.name.toLowerCase().includes(keyword)) ||
                                        (product.description && product.description.toLowerCase().includes(keyword))
                                    );
                                });

                                // Gán menuItem ban đầu bằng updatedMenuItem
                                menuItem = updatedMenuItem;
                            }
                            return menuItem;
                        });
                        // Loại bỏ các menuItem không còn sản phẩm nào
                        setDataSearch(menuItems.filter((menuItem: any) => menuItem.products.length > 0))
                    }
                }
            }
        }
    };

    const handleSearchDesk = () => {
        setOpenTab(3);

        if (inputRefDesk.current) {
            if (!inputRefDesk.current.value) {
                setDataSearch([])
            } else {
                const keyword = inputRefDesk.current.value.toLowerCase();
                if (menuItems && menuItems.length > 0) {
                    menuItems = menuItems.map((menuItem: any) => {
                        if (menuItem.products && menuItem.products.length > 0) {
                            const updatedMenuItem = {...menuItem};
                            updatedMenuItem.products = updatedMenuItem.products.filter((product: any) => {
                                return (
                                    (product.name && product.name.toLowerCase().includes(keyword)) ||
                                    (product.description && product.description.toLowerCase().includes(keyword))
                                );
                            });

                            menuItem = updatedMenuItem;
                        }
                        return menuItem;
                    });
                    setDataSearch(menuItems.filter((menuItem: any) => menuItem.products.length > 0))
                }
            }
        }

        dispatch(reloadProductHeightCalculation());
    };

    // handle scroll when click in category
    const handleMenuClick = (itemId: any) => {
        setCurrentCategory(itemId);
        setIsProgrammaticallyScroll(true)

        if (isMobile) {
            const menuElement = document.getElementById("menu-categories");
            const sectionElement = document.getElementById(`section-${itemId}`);
            const currentScrollY = window.scrollY;

            if (sectionElement && menuElement) {
                let topPosition = 0;
                const menuTop = menuElement.getBoundingClientRect().top;
                const sectionTop = sectionElement.getBoundingClientRect().top;
                // Adjust for header height, coupons, and other dynamic offsets
                const headerHeight = scrolledY > 76 ? 76 : 39;
                const couponOffset = coupons && coupons.length > 0 ? 42 : 0;

                if (currentScrollY === 0) {
                    topPosition = sectionTop - headerHeight - couponOffset;
                    setIsAtStart(true);
                } else if (menuTop > 98) {
                    topPosition = sectionTop + currentScrollY - (menuTop + headerHeight + couponOffset);
                } else if (currentScrollY > 0 && currentScrollY < 76) {
                    topPosition = sectionTop + currentScrollY - (menuTop + 39);
                } else {
                    topPosition = sectionTop + currentScrollY - (menuTop + 32);
                }
                requestAnimationFrame(() => {
                    if (currentScrollY === 0 && coupons && coupons.length > 0) topPosition = topPosition - 56;

                    window.scrollTo({top: topPosition, behavior: "smooth"});
                    setTimeout(() => {
                        setIsProgrammaticallyScroll(false);

                    }, 200);
                })

            } else {
                setTimeout(() => {
                    setIsProgrammaticallyScroll(false);

                }, 100);
            }

            setForcusElement(itemId);

        } else {
            dispatch(setflagForcusData(true));
            let topPosition = 0;
            const sectionElementDesk = document.getElementById(`section-desk-${itemId}`);
            const currentScrollY = window.scrollY;
            if (sectionElementDesk) {
                if (coupons && coupons.length > 0) {
                    topPosition = sectionElementDesk.getBoundingClientRect().top + currentScrollY - 212;
                } else {
                    topPosition = sectionElementDesk.getBoundingClientRect().top + currentScrollY - 86;
                }
            }
            window.scrollTo({top: topPosition, behavior: "smooth"});
        }
    };

    if (coupons && coupons.length > 0) {
        Cookies.set('coupons', 'true');
    } else {
        Cookies.set('coupons', 'false');
    }

    const turnoff = () => {
        dispatch(setflagSortData(false));
    }

    useEffect(() => {
        if (groupOrder && menuItems.length > 0) {
            const productGroupOrderIds = groupOrder?.products.length > 0 ? groupOrder?.products.map((productOrder: any) => productOrder.id) : [];
            if (menuItems && menuItems.length > 0) {
                if (groupOrder?.is_product_limit > 0) {
                    menuItems = menuItems.map((menuItem: any) => {
                        if (menuItem.products && menuItem.products.length > 0) {
                            // create copy menuItem
                            const updatedMenuItem = {...menuItem};

                            // filter in copy menuItem
                            updatedMenuItem.products = updatedMenuItem.products.filter((product: any) => {
                                return (
                                    productGroupOrderIds.includes(product.id)
                                );
                            });
                            // make menuItem = updatedMenuItem
                            menuItem = updatedMenuItem;
                        }
                        return menuItem;
                    });
                }
                const checkType = groupOrder?.type === ORDER_TYPE.DELIVERY;
                // delete menuItem when no product
                setDataGroupOrder(
                    menuItems.filter((menuItem: any) =>
                        menuItem.products.length > 0 &&
                        (checkType ? menuItem.available_delivery === checkType : true)
                    )
                );
                if (type == MENU_TYPE.GROUP_ORDER) {
                    setOpenTab(4);
                } else {
                    setOpenTab(2);
                }
            }
        } else {
            if (groupOrderNowSlice) {
                reCallGroupOrder(groupOrderNowSlice?.id)
            }
        }
    }, [groupOrder, menuItems]);

    const reCallGroupOrder = async (groupId: any) => {
        try {
            const groupDetail = await api.get(`/groups/${groupId}`, {
                headers: {
                    'Authorization': `Bearer ${tokenLoggedInCookie}`,
                    'Content-Language': language,
                }
            });
            const groupDetailData = groupDetail?.data?.data;
            if (groupDetailData) {
                dispatch(setGroupOrderData(groupDetailData));
            }
        } catch (error) {
            console.error('Error:', error);
        }
    }

    const [getHoliday, setHoliday] = useState<any | null>(null);

    /**
     * Check if a menu item is highlighted
     *
     * @param menuId Menu ID
     * @returns boolean
     */
    const isHighlightTab = (menuId: number) => {
        return (menuId == forcusElement && openTab != 1)
    }

    const bodyHeightPx = bodyHeight + 'px';

    useEffect(() => {
        if (!groupOrder) {
            setDataGroupOrder([])
            setOpenTab(2);
        }
    }, [groupOrder]);

    useEffect(() => {
        if (type == MENU_TYPE.GROUP_ORDER) {
            setOpenTab(4);
            setIsDeliveryType(false);
        } else {
            if (type == MENU_TYPE.DELIVERY) {
                setIsDeliveryType(true);
            } else {
                setIsDeliveryType(false);
            }
            if(!(inputRefDesk.current && inputRefDesk.current.value) && !(inputRef.current && inputRef.current.value)) {
                setOpenTab(2);
            }
        }
        getMenuItems()
    }, [workspaceId, delivery, type, isDeliveryType]);

    useEffect(() => {
        if (_.isEmpty(cart)) {
            setIsDeliveryType(false);
            setOpenTab(2);
            dispatch(changeType(1));
            dispatch(setGroupOrderData(null));
        }
    }, [cart, dispatch])

    useEffect(() => {
        if (openTab === 2 && !isMobile) {
            window.scrollBy(0, 1);
        }
    }, [openTab, isMobile]);

    const categoryMenuSlideBorderColor = ((item: any, color: any) => {
        let result = 'transparent';

        if (isHighlightTab(item?.id) && (item?.favoriet_friet || item?.kokette_kroket)) {
            result = '#D87833'
        } else {
            result = isHighlightTab(item?.id) && (!item?.favoriet_friet && !item?.kokette_kroket)
                ? `${color ? color : '#000000'}`
                : 'transparent'
        }

        return result + ' !important';
    });

    const categoryMenuSlideTextColor = ((isMobile: any, item: any, color: any, forcusElement: any) => {
        let result = 'inherit';

        if (isMobile) {
            if (item?.id === forcusElement) {
                result = (item?.kokette_kroket || item?.favoriet_friet) ? '#D87833' : color || '#ffffff'
            }
        } else {
            if (isHighlightTab(item?.id)) {
                result = (item?.kokette_kroket || item?.favoriet_friet) ? '#D87833' : color || '#ffffff'
            } else {
                result = '#404040'
            }
        }

        return result;
    });

    const [getHolidayBack, setHolidayBack] = useState<any | null>(null);
    useEffect(() => {
        workspaceId && api.get(`workspaces/` + workspaceId + `/settings/holiday_exceptions`, {
            headers: {
                'Authorization': `Bearer ${tokenLoggedInCookie}`,
            }
        }).then(res => {
            const json = res.data;
            const currentTime = new Date();
            const filteredData = json.data.filter((item: any) => {
                const startTime = new Date(item.start_time + 'T00:00:00');
                const endTime = new Date(item.end_time + 'T23:59:59');
                return currentTime >= startTime && currentTime <= endTime;
            });

            setHolidayBack({
                status: filteredData.length > 0,
                data: filteredData
            });
        }).catch(error => {
            // console.log(error)
        });
    }, [workspaceId]);

    const holidayDiv = useRef<any>(null);

    useEffect(() => {
        if (holidayDiv.current) {
            const rect = holidayDiv.current.getBoundingClientRect();
            const height = rect.height;
            Cookies.set('holidayHeight', height.toString());
        } else {
            Cookies.remove('holidayHeight');
        }
    }, [holidayDiv.current]);

    const isBottom = useDetectScrollToBottom();

    useEffect(() => {
        if (!isBottom) return setCurrentCategory(null);

        if (openTab === 3) {
            if (dataSearch && dataSearch.length > 0) {
                const lastItem = dataSearch[dataSearch.length - 1];
                if (lastItem) {
                    setForcusElement(lastItem.id);
                    setCurrentCategory(lastItem.id);
                } else {
                    setCurrentCategory(null);
                }
            }
            return;
        }

        if (groupOrderNowSlice || openTab === 4) {
            if (dataGroupOrder && dataGroupOrder.length > 0) {
                const lastItem = dataGroupOrder[dataGroupOrder.length - 1];

                if (lastItem) {
                    setForcusElement(lastItem.id);
                    setCurrentCategory(lastItem.id)
                } else {
                    setCurrentCategory(null);
                }
            }
            return;
        }

        if (menuItems.length > 0) {
            const lastItem = menuItems[menuItems.length - 1];
            if (lastItem) {
                setForcusElement(lastItem.id);
                setCurrentCategory(lastItem.id);
            }
        } else {
            setCurrentCategory(null);
        }

    }, [
        isBottom,
        menuItems,
        dataGroupOrder,
        groupOrderNowSlice
    ]);

    const isTop = useDetectScrollToTop();

    useEffect(() => {
        if (isAtStart) {
            setIsAtStart(false);
            return;
        }
        if (!isTop || !menuItems.length || !(menuItems.length > 0)) {
            setCurrentCategory(null);
            return;
        }
        if (openTab === 3) {
            if (dataSearch && dataSearch.length > 0) {
                const firstItem = dataSearch[0];
                if (firstItem) {
                    setForcusElement(firstItem.id);
                    setCurrentCategory(firstItem.id);
                } else {
                    setCurrentCategory(null);
                }
            }
            return;
        }

        if (groupOrderNowSlice || openTab === 4) {
            if (dataGroupOrder && dataGroupOrder.length > 0) {
                const firstItem = dataGroupOrder[0];

                if (firstItem) {
                    setForcusElement(firstItem.id);
                    setCurrentCategory(firstItem.id)
                } else {
                    setCurrentCategory(null);
                }
            }
            return;
        }

        const firstItem = menuItems[0];
        if (firstItem) {
            if (openTab !== 1) setForcusElement(firstItem.id);
            setCurrentCategory(firstItem.id);
        }
    }, [isTop, menuItems, dataGroupOrder, groupOrderNowSlice]);

    // State to track if the element is being dragged
    const [isDragging, setIsDragging] = useState(false);
    // State to track the starting position of the drag
    const [startPos, setStartPos] = useState({x: 0, y: 0});
    const dragThreshold = 5; // Minimum pixels moved to consider it a drag

    // Handle the mouse down event (drag start)
    const handleMouseDown = (e: any) => {
        setStartPos({x: e.clientX, y: e.clientY});
        setIsDragging(false); // Reset dragging state
    };

    // Handle the mouse move event (during drag)
    const handleMouseMove = (e: any) => {
        const distance = Math.sqrt(
            Math.pow(e.clientX - startPos.x, 2) + Math.pow(e.clientY - startPos.y, 2)
        );

        // If the mouse moves beyond the threshold, mark as dragging
        if (distance > dragThreshold) {
            setIsDragging(true);
        }
    };

    // Handle the mouse up event (drag end or click)
    const handleMouseUp = (e: any, payload: any) => {
        // Reset dragging state after drop
        if (!isDragging) {
            handleMenuClick(payload);
            setIsSortOpen(false);
        }

        setIsDragging(false);
    };

    return (
        <>
            <div className={`${variables.all} ${!isMobile ? 'product-list-page' : ''}`} onClick={turnoff}>
                {baseLink === '/category/products' ? (<Menu/>) : (<MenuTableOrdering/>)}

                {isMobile ? (
                    <div className="res-mobile product-list">
                        <Header workspaceId={workspaceId ? workspaceId : ''} coupons={coupons ? coupons.length : ''}/>
                        {coupons && coupons.length > 0 && (
                            coupons.map((coupon: any, index: any) => {
                                if (openPopups[index]) {
                                    return (
                                        <CouponPopup
                                            key={index}
                                            color={apiData ? apiData.primary_color : "black"}
                                            coupon={coupon}
                                            toggleCouponPopup={() => toggleCouponPopup(index)}
                                        />
                                    );
                                } else {
                                    return null;
                                }
                            })
                        )}

                        {coupons && coupons.length > 0 && (
                            <div id="coupons"
                                 className={`${couping}`}
                                 style={scrolledY > 76 ? {position: 'fixed', width: '101%', borderRadius: '0px'} : {}}>
                                <div className={`${couponsList} row d-flex overflow-x-unset`}
                                     style={{backgroundColor: apiData ? apiData?.primary_color : 'white'}}>
                                    <Slider {...settingCoupon} arrows={false}>
                                        {coupons.map((coupon: any, index: any) => (
                                            <div key={index} className={`${variables.couponContainer} d-flex`}
                                                 onClick={() => toggleCouponPopup(index)}>
                                                <div className={`${couName} d-flex`}>
                                                    <svg className='ms-2' width="16" height="16" viewBox="0 0 16 16"
                                                         fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <circle cx="8" cy="8" r="7.5" stroke="white"/>
                                                        <path
                                                            d="M8.87521 5.04405C8.56721 5.04405 8.30587 4.93672 8.09121 4.72205C7.87654 4.50738 7.76921 4.24605 7.76921 3.93805C7.76921 3.63005 7.87654 3.36872 8.09121 3.15405C8.30587 2.93005 8.56721 2.81805 8.87521 2.81805C9.18321 2.81805 9.44454 2.93005 9.65921 3.15405C9.88321 3.36872 9.99521 3.63005 9.99521 3.93805C9.99521 4.24605 9.88321 4.50738 9.65921 4.72205C9.44454 4.93672 9.18321 5.04405 8.87521 5.04405ZM7.92321 12.884C7.47521 12.884 7.11121 12.744 6.83121 12.464C6.56054 12.184 6.42521 11.764 6.42521 11.204C6.42521 10.9707 6.46254 10.6674 6.53721 10.294L7.48921 5.80005H9.50521L8.49721 10.56C8.45987 10.7 8.44121 10.8494 8.44121 11.008C8.44121 11.1947 8.48321 11.33 8.56721 11.414C8.66054 11.4887 8.80987 11.526 9.01521 11.526C9.18321 11.526 9.33254 11.498 9.46321 11.442C9.42587 11.9087 9.25787 12.268 8.95921 12.52C8.66987 12.7627 8.32454 12.884 7.92321 12.884Z"
                                                            fill="white"/>
                                                    </svg>
                                                    <h2 className={`${variables.coupons} ms-2`}>
                                                        {coupon.promo_name}
                                                    </h2>
                                                </div>
                                                <div className={`${couCode} d-flex`}>
                                                    <h2 className={`${variables.code} me-2 text-uppercase`}>
                                                        {coupon.code}
                                                    </h2>
                                                </div>
                                            </div>
                                        ))}
                                    </Slider>
                                </div>
                            </div>
                        )}

                        <div id="menu-categories"
                             className={`${searching} row overflow-x-unset`}
                             style={{
                                 top: (coupons && coupons.length > 0) ? '98px' : '56px',
                                 position: scrolledY > 76 ? 'fixed' : 'sticky',
                                 width: scrolledY > 76 ? '101%' : '',
                             }}>
                            <div className="col-md-1 col-1">
                                <svg xmlns="http://www.w3.org/2000/svg" width="19" height="18" viewBox="0 0 19 18"
                                     fill="none" onClick={toggleSearch}>
                                    <path
                                        d="M8.70833 14.25C12.2061 14.25 15.0417 11.5637 15.0417 8.25C15.0417 4.93629 12.2061 2.25 8.70833 2.25C5.21053 2.25 2.375 4.93629 2.375 8.25C2.375 11.5637 5.21053 14.25 8.70833 14.25Z"
                                        stroke={color} strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"/>
                                    <path d="M16.625 15.75L13.1813 12.4875" stroke={color} strokeWidth="2"
                                          strokeLinecap="round" strokeLinejoin="round"/>
                                </svg>
                            </div>

                            {isSearchOpen && (
                                <div id={searchInput} className={`d-flex`}>
                                    <input id={inputing} ref={inputRef} onKeyUp={handleSearch} type="text"
                                           inputMode="text"/>
                                    <svg className={variables.quitting} xmlns="http://www.w3.org/2000/svg" width="20"
                                         height="20" viewBox="0 0 20 20" fill="none" onClick={toggleExit}>
                                        <path d="M11.25 3.75L3.75 11.25" stroke="#757575" strokeWidth="2"
                                              strokeLinecap="round" strokeLinejoin="round"/>
                                        <path d="M3.75 3.75L11.25 11.25" stroke="#757575" strokeWidth="2"
                                              strokeLinecap="round" strokeLinejoin="round"/>
                                    </svg>
                                </div>
                            )}

                            {(tokenLoggedInCookie && !isTableOrdering && !isSelfOrdering) && (
                                <div className={`favorite-nav-item col-md-1 col-1 ${variables.navMobile}`}>
                                    <svg xmlns="http://www.w3.org/2000/svg" width="19" height="19" viewBox="0 0 19 19"
                                         fill="none" onClick={favoritesTab} className='mt-1'>
                                        <path
                                            d="M14.8434 2.15664C14.4768 1.78995 14.0417 1.49907 13.5627 1.30061C13.0837 1.10215 12.5704 1 12.0519 1C11.5335 1 11.0201 1.10215 10.5411 1.30061C10.0621 1.49907 9.62698 1.78995 9.26046 2.15664L8.49981 2.91729L7.73916 2.15664C6.99882 1.4163 5.9947 1.00038 4.94771 1.00038C3.90071 1.00038 2.89659 1.4163 2.15626 2.15664C1.41592 2.89698 1 3.90109 1 4.94809C1 5.99509 1.41592 6.9992 2.15626 7.73954L2.91691 8.50019L8.49981 14.0831L14.0827 8.50019L14.8434 7.73954C15.21 7.37302 15.5009 6.93785 15.6994 6.45889C15.8979 5.97992 16 5.46654 16 4.94809C16 4.42964 15.8979 3.91626 15.6994 3.43729C15.5009 2.95833 15.21 2.52316 14.8434 2.15664V2.15664Z"
                                            fill={openTab === 1 ? color : '#FFF'} stroke={color} strokeWidth="2"
                                            strokeLinecap="round" strokeLinejoin="round"/>
                                    </svg>
                                </div>
                            )}

                            {(openTab === 4 || (groupOrderNowSlice)) ? (
                                <div
                                    className={`${navItem} col-md-1${tokenLoggedInCookie ? '0' : '1'} col-1${tokenLoggedInCookie ? '0' : '1'} ml-0 slidering`}
                                    onClick={e => {
                                        setOpenTab(4);
                                    }}>
                                    {dataGroupOrder && dataGroupOrder.length > 0 && (
                                        <Slider ref={sliderRef} {...settingOrder} arrows={false}>
                                            {dataGroupOrder && dataGroupOrder.map((item: any, index: number) => (
                                                <div className={`${navText} d-flex`} key={index} id={item?.id}
                                                     onClick={() => handleMenuClick(item?.id)}>
                                                    <div className={`${variables.decoring} category-menu-item`}
                                                         style={{position: 'relative'}} id={`decoring-${index}`}>
                                                        <style jsx>
                                                            {` div::before {
                                                                border-color: ${categoryMenuSlideBorderColor(item, color)};
                                                            } `}
                                                        </style>
                                                        {item?.favoriet_friet && (
                                                            <div style={{alignItems: 'center', marginRight: '3px'}}>
                                                                <FavoriteSmall/>
                                                            </div>
                                                        )}
                                                        {item?.kokette_kroket && (
                                                            <div style={{alignItems: 'center'}}>
                                                                <KoketteSmall/>
                                                            </div>
                                                        )}
                                                        <h2 className="cate-title"
                                                            style={{color: categoryMenuSlideTextColor(isMobile, item, apiData?.primary_color, forcusElement)}}>
                                                            {item.name?.toUpperCase()}
                                                        </h2>
                                                    </div>
                                                </div>
                                            ))}
                                        </Slider>
                                    )}
                                </div>
                            ) : (
                                <div
                                    className={`${navItem} col-md-1${tokenLoggedInCookie ? '0' : '1'} col-1${tokenLoggedInCookie ? '0' : '1'} ml-0 slidering`}
                                    onClick={e => {
                                        setOpenTab(2);
                                    }}>
                                    {menuItems && menuItems.length >= 2 ? (
                                        <Slider ref={sliderRefMenu} {...settings} arrows={false}>
                                            {menuItems && menuItems.map((item: any, index: number) => (
                                                <div className={`${navText} d-flex`} key={index} id={item?.id}
                                                     onClick={() => handleMenuClick(item?.id)}>
                                                    <div className={`${variables.decoring} category-menu-item`}
                                                         style={{position: 'relative'}} id={`decoring-${index}`}>
                                                        <style jsx>
                                                            {` div::before {
                                                                border-color: ${categoryMenuSlideBorderColor(item, color)};
                                                            } `}
                                                        </style>
                                                        {item?.favoriet_friet && (
                                                            <div style={{alignItems: 'center', marginRight: '3px'}}>
                                                                <FavoriteSmall/>
                                                            </div>
                                                        )}
                                                        {item?.kokette_kroket && (
                                                            <div style={{alignItems: 'center'}}>
                                                                <KoketteSmall/>
                                                            </div>
                                                        )}
                                                        <h2 className="cate-title"
                                                            style={{color: categoryMenuSlideTextColor(isMobile, item, apiData?.primary_color, forcusElement)}}>
                                                            {item.name?.toUpperCase()}
                                                        </h2>
                                                    </div>
                                                </div>
                                            ))}
                                        </Slider>
                                    ) : (
                                        menuItems && menuItems.map((item: any, index: number) => (
                                            <div className={`${navText} d-flex`} key={index} id={item?.id}
                                                 onClick={() => handleMenuClick(item?.id)}>
                                                <div className={`${variables.decoring} category-menu-item`}
                                                     style={{position: 'relative'}}>
                                                    <style jsx>
                                                        {` div::before {
                                                            border-color: ${categoryMenuSlideBorderColor(item, color)};
                                                        } `}
                                                    </style>
                                                    {item?.favoriet_friet && (
                                                        <div style={{alignItems: 'center', marginRight: '3px'}}>
                                                            <FavoriteSmall/>
                                                        </div>
                                                    )}
                                                    {item?.kokette_kroket && (
                                                        <div style={{alignItems: 'center'}}>
                                                            <KoketteSmall/>
                                                        </div>
                                                    )}
                                                    <h2 className="cate-title"
                                                        style={{color: categoryMenuSlideTextColor(isMobile, item, apiData?.primary_color, forcusElement)}}>
                                                        {item.name?.toUpperCase()}
                                                    </h2>
                                                </div>
                                            </div>
                                        ))
                                    )}
                                </div>
                            )}
                        </div>

                        {openTab === 1 && (
                            <div id={`favoriteMenu`}
                                 style={(scrolledY > 76 && dataFavorites && dataFavorites.length > 0) ? {marginTop: '214px'} : {}}>
                                <Favorites baseLink={baseLink} products={dataFavorites} key={1} color={color}
                                           coupons={coupons}/>
                            </div>
                        )}

                        {openTab === 2 && (
                            <div id={`productMenu`} className={`${variables.productMenu}`}
                                 style={(scrolledY > 76 && menuItems && menuItems.length > 0) ? {marginTop: '214px'} : {}}>
                                {menuItems && menuItems.map((item: any, index: number) => (
                                    <Product
                                        isProgrammaticallyScroll={isProgrammaticallyScroll}
                                        key={item.id}
                                        baseLink={baseLink}
                                        categoryItems={menuItems.filter((menuItem: {
                                            id: number
                                        }) => menuItem.id === item.id)}
                                        color={color ?? null}
                                        onFocus={(id: any) => {
                                            setForcusElement(id)
                                        }}
                                        isLast={index === menuItems.length - 1}
                                        coupons={coupons}
                                        currentCategory={currentCategory}
                                        setCurrentCategory={setCurrentCategory}
                                        lastItem={menuItems[menuItems.length - 1]?.id}
                                        forcusElement={forcusElement}
                                    />
                                ))}
                            </div>
                        )}

                        {openTab === 3 && (
                            <div id={`productMenu`}
                                 style={(scrolledY > 76 && dataSearch && dataSearch.length > 0) ? {marginTop: '214px'} : {}}>
                                {dataSearch && dataSearch.length > 0 ? (
                                    dataSearch.map((item: any, index: number) => (
                                        <Product
                                            isProgrammaticallyScroll={isProgrammaticallyScroll}
                                            key={item.id}
                                            baseLink={baseLink}
                                            categoryItems={dataSearch.filter(
                                                (menuItem: { id: number }) => menuItem.id === item.id
                                            )}
                                            color={color ?? null}
                                            onFocus={(id: any) => {
                                                setForcusElement(id)
                                            }}
                                            isLast={index === dataSearch.length - 1}
                                            coupons={coupons}
                                            currentCategory={currentCategory}
                                            setCurrentCategory={setCurrentCategory}
                                            lastItem={dataSearch[dataSearch.length - 1]?.id}
                                            forcusElement={forcusElement}
                                        />
                                    ))
                                ) : (
                                    <div className={`${emptySearch}`}>
                                        <p>{trans('nothing-at-all')}</p>
                                    </div>
                                )}
                            </div>
                        )}

                        {openTab === 4 && (
                            <div id={`productMenu`}
                                 style={(scrolledY > 76 && dataGroupOrder && dataGroupOrder.length > 0) ? {marginTop: '214px'} : {}}>
                                {dataGroupOrder && dataGroupOrder.length > 0 ? (
                                    dataGroupOrder.map((item: any, index: number) => (
                                        <Product
                                            isProgrammaticallyScroll={isProgrammaticallyScroll}

                                            key={item.id}
                                            baseLink={baseLink}
                                            categoryItems={dataGroupOrder?.filter(
                                                (menuItem: { id: number }) => menuItem.id === item.id
                                            )}
                                            color={color ?? null}
                                            onFocus={(id: any) => {
                                                setForcusElement(id)
                                            }}
                                            isLast={index === dataGroupOrder.length - 1}
                                            coupons={coupons}
                                            currentCategory={currentCategory}
                                            setCurrentCategory={setCurrentCategory}
                                            lastItem={dataGroupOrder[dataGroupOrder.length - 1]?.id}
                                            forcusElement={forcusElement}
                                        />
                                    ))
                                ) : (
                                    <div className={`${emptySearch}`}>
                                        <p>{trans('nothing-at-all')}</p>
                                    </div>
                                )}
                            </div>
                        )}
                        {getHolidayBack && getHolidayBack.status && (
                            <div className={`${variables.holidayMobile} d-flex`} ref={holidayDiv}>
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                     xmlns="http://www.w3.org/2000/svg" className={variables.calandarSvg}>
                                    <path
                                        d="M19 4H5C3.89543 4 3 4.89543 3 6V20C3 21.1046 3.89543 22 5 22H19C20.1046 22 21 21.1046 21 20V6C21 4.89543 20.1046 4 19 4Z"
                                        stroke="white" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"/>
                                    <path d="M16 2V6" stroke="white" strokeWidth="2" strokeLinecap="round"
                                          strokeLinejoin="round"/>
                                    <path d="M8 2V6" stroke="white" strokeWidth="2" strokeLinecap="round"
                                          strokeLinejoin="round"/>
                                    <path d="M3 10H21" stroke="white" strokeWidth="2" strokeLinecap="round"
                                          strokeLinejoin="round"/>
                                </svg>
                                <div className={`${variables.holidayMobileText} pe-2 ms-2`}>
                                    <div style={{whiteSpace: "pre-line"}}>
                                        {getHolidayBack.data[0].description}
                                    </div>
                                </div>
                            </div>
                        )}
                        <IntroducePopup baseLink={baseLink} workspaceInfo={workspaceInfo} getHolidayStatus={getHoliday}
                                        setHolidayStatus={setHoliday}/>
                    </div>
                ) : (
                    <div className="res-desktop product-list">
                        <div className="desktop-wrapper">
                            {flagDesktopChangeType && (
                                <>
                                    <div style={{
                                        width: openDeskTopLogin ? 'calc(100% - 380px)' : 'calc(100% - 356px)',
                                        position: 'fixed',
                                        left: '0',
                                        top: '0',
                                        height: bodyHeightPx,
                                        background: 'rgba(16, 16, 16, 0.48)',
                                        zIndex: '9999'
                                    }}></div>
                                    <div style={{
                                        width: openDeskTopLogin ? '380px' : '356px',
                                        position: 'fixed',
                                        right: '0',
                                        top: '0',
                                        height: '70px',
                                        background: `rgba(16, 16, 16, ${Math.max(0.48 - (scrolledY / 75) * 0.48, 0)})`,
                                        zIndex: '9999',
                                        transition: 'background 0.3s ease'
                                    }}></div>
                                </>
                            )}
                            <div className="content-wrapper d-flex justify-content-between" style={{
                                marginTop: '50px',
                                border: 'none !important',
                                backfaceVisibility: 'hidden', /* Fixes some rendering artifacts */
                                transform: 'translate3d(0, 0, 0)', /* Forces hardware acceleration */
                            }}>
                                <div className="f-left column-left" style={{
                                    backfaceVisibility: 'hidden'
                                }}>
                                    <div className="des-custom-left-wrapper bg-white">
                                        <div className="white-wrapper">
                                            <div className="container-center">
                                                <div className="menu-coupon">
                                                    <div className={`${searchingDesk}`}>
                                                        <div className="des-search-fixed des-custom-left">
                                                            <div className={`${searchDeskChild} d-flex`}
                                                                 style={{background: !isSearchDeskOpen ? '#F8F8F8' : ''}}>
                                                                <div className={searhIcon} style={{left: '20px'}}>
                                                                    <svg xmlns="http://www.w3.org/2000/svg" width="19"
                                                                         height="18" viewBox="0 0 19 18" fill="none"
                                                                         onClick={toggleSearchDesk} className='mt-2'>
                                                                        <path
                                                                            d="M8.70833 14.25C12.2061 14.25 15.0417 11.5637 15.0417 8.25C15.0417 4.93629 12.2061 2.25 8.70833 2.25C5.21053 2.25 2.375 4.93629 2.375 8.25C2.375 11.5637 5.21053 14.25 8.70833 14.25Z"
                                                                            stroke={color} strokeWidth="2"
                                                                            strokeLinecap="round"
                                                                            strokeLinejoin="round"/>
                                                                        <path d="M16.625 15.75L13.1813 12.4875"
                                                                              stroke={color} strokeWidth="2"
                                                                              strokeLinecap="round"
                                                                              strokeLinejoin="round"/>
                                                                    </svg>
                                                                </div>

                                                                {isSearchDeskOpen && (
                                                                    <div id={searchInput} className={`d-flex`}
                                                                         style={{top: '100px'}}>
                                                                        <input id={inputing} ref={inputRefDesk}
                                                                               onKeyUp={handleSearchDesk} type="text"
                                                                               placeholder={trans('searching-input')}
                                                                               style={{
                                                                                   borderBottom: '1px solid #C4C4C4',
                                                                                   paddingTop: '10px'
                                                                               }}
                                                                        />
                                                                        <svg xmlns="http://www.w3.org/2000/svg"
                                                                             width="15" height="15" viewBox="0 0 15 15"
                                                                             fill="none"
                                                                             className={variables.quittingDesk} style={{
                                                                            position: 'absolute',
                                                                            right: "9px",
                                                                            top: "16px"
                                                                        }} onClick={toggleExitDesk}>
                                                                            <path d="M11.25 3.75L3.75 11.25"
                                                                                  stroke={color ? color : '#757575'}
                                                                                  strokeWidth="2" strokeLinecap="round"
                                                                                  strokeLinejoin="round"/>
                                                                            <path d="M3.75 3.75L11.25 11.25"
                                                                                  stroke={color ? color : '#757575'}
                                                                                  strokeWidth="2" strokeLinecap="round"
                                                                                  strokeLinejoin="round"/>
                                                                        </svg>
                                                                    </div>
                                                                )}

                                                                {(tokenLoggedInCookie && !isTableOrdering && !isSelfOrdering) && (
                                                                    <div className={`${favoriteIcon} me-3`}
                                                                         style={{left: '40px'}}>
                                                                        <svg xmlns="http://www.w3.org/2000/svg"
                                                                             width="17" height="15" viewBox="0 0 17 15"
                                                                             fill="#F5F5F5" onClick={favoritesTab}
                                                                             className='mx-2'
                                                                             style={{marginTop: "15px"}}>
                                                                            <path
                                                                                d="M14.8434 2.15664C14.4768 1.78995 14.0417 1.49907 13.5627 1.30061C13.0837 1.10215 12.5704 1 12.0519 1C11.5335 1 11.0201 1.10215 10.5411 1.30061C10.0621 1.49907 9.62698 1.78995 9.26046 2.15664L8.49981 2.91729L7.73916 2.15664C6.99882 1.4163 5.9947 1.00038 4.94771 1.00038C3.90071 1.00038 2.89659 1.4163 2.15626 2.15664C1.41592 2.89698 1 3.90109 1 4.94809C1 5.99509 1.41592 6.9992 2.15626 7.73954L2.91691 8.50019L8.49981 14.0831L14.0827 8.50019L14.8434 7.73954C15.21 7.37302 15.5009 6.93785 15.6994 6.45889C15.8979 5.97992 16 5.46654 16 4.94809C16 4.42964 15.8979 3.91626 15.6994 3.43729C15.5009 2.95833 15.21 2.52316 14.8434 2.15664V2.15664Z"
                                                                                fill={openTab === 1 ? color : '#FFF'}
                                                                                stroke={color} strokeWidth="2"
                                                                                strokeLinecap="round"
                                                                                strokeLinejoin="round"/>
                                                                        </svg>
                                                                    </div>
                                                                )}

                                                                {!isSearchDeskOpen && (
                                                                    (openTab === 4 || (groupOrderNowSlice)) ? (
                                                                        <div
                                                                            className={`${navItemDesk} ml-0 nav-item-desk`}
                                                                            style={{paddingLeft: tokenLoggedInCookie ? '30px' : ''}}
                                                                            onClick={e => {
                                                                                setOpenTab(4);
                                                                            }}>
                                                                            {dataGroupOrder && dataGroupOrder.length > 0 && (
                                                                                <Slider
                                                                                    ref={sliderRefDeskGroup} {...settingDesk} >
                                                                                    {dataGroupOrder && dataGroupOrder.map((item: any, index: number) => (
                                                                                        <div
                                                                                            onMouseDown={(e: any) => handleMouseDown(e)}
                                                                                            onMouseMove={(e: any) => handleMouseMove(e)}
                                                                                            onMouseUp={(e: any) => handleMouseUp(e, item?.id)}
                                                                                            className={`${navText} category-menu-item d-flex me-5 ${isHighlightTab(item?.id) ? navTextForcus : 'none'}`}
                                                                                            key={index} id={item?.id}
                                                                                            style={{paddingTop: "3px"}}>
                                                                                            <style jsx>
                                                                                                {` div::before {
                                                                                                    border-color: ${categoryMenuSlideBorderColor(item, color)};
                                                                                                } `}
                                                                                            </style>
                                                                                            {item?.favoriet_friet && (
                                                                                                <div className={`mt-1`}
                                                                                                     style={{
                                                                                                         alignItems: 'center',
                                                                                                         width: '23px',
                                                                                                         height: '32px'
                                                                                                     }}>
                                                                                                    <FavoriteDesk/>
                                                                                                </div>
                                                                                            )}
                                                                                            {item?.kokette_kroket && (
                                                                                                <div className={`mt-1`}
                                                                                                     style={{alignItems: 'center'}}>
                                                                                                    <KoketteDesk/>
                                                                                                </div>
                                                                                            )}
                                                                                            <div
                                                                                                className={`${variables.decoringDesk} text-lowercase`}>
                                                                                                <h2 className="cate-title"
                                                                                                    style={{color: categoryMenuSlideTextColor(isMobile, item, apiData?.primary_color, forcusElement)}}>
                                                                                                    {item.name?.toUpperCase()}
                                                                                                </h2>
                                                                                            </div>
                                                                                        </div>
                                                                                    ))}
                                                                                </Slider>
                                                                            )}
                                                                        </div>
                                                                    ) : (
                                                                        <div
                                                                            className={`${navItemDesk} ml-0 nav-item-desk`}
                                                                            style={{paddingLeft: tokenLoggedInCookie ? '30px' : ''}}
                                                                            onClick={e => {
                                                                                setOpenTab(2);
                                                                            }}>
                                                                            {menuItems && menuItems.length > 0 && (
                                                                                <Slider
                                                                                    ref={sliderRef} {...settingDesk} >
                                                                                    {menuItems && menuItems.map((item: any, index: number) => (
                                                                                        <div
                                                                                            onMouseDown={(e: any) => handleMouseDown(e)}
                                                                                            onMouseMove={(e: any) => handleMouseMove(e)}
                                                                                            onMouseUp={(e: any) => handleMouseUp(e, item?.id)}
                                                                                            className={`${navText} category-menu-item d-flex me-5 ${isHighlightTab(item?.id) ? navTextForcus : 'none'}`}
                                                                                            key={index} id={item?.id}
                                                                                            style={{paddingTop: "3px"}}>
                                                                                            <style jsx>
                                                                                                {` div::before {
                                                                                                    border-color: ${categoryMenuSlideBorderColor(item, color)};
                                                                                                } `}
                                                                                            </style>
                                                                                            {item?.favoriet_friet && (
                                                                                                <div className={`mt-1`}
                                                                                                     style={{alignItems: 'center'}}>
                                                                                                    <FavoriteDesk/>
                                                                                                </div>
                                                                                            )}
                                                                                            {item?.kokette_kroket && (
                                                                                                <div className={`mt-1`}
                                                                                                     style={{alignItems: 'center'}}>
                                                                                                    <KoketteDesk/>
                                                                                                </div>
                                                                                            )}
                                                                                            <div
                                                                                                className={`${variables.decoringDesk} text-lowercase`}>
                                                                                                <h2 className="cate-title"
                                                                                                    style={{color: categoryMenuSlideTextColor(isMobile, item, apiData?.primary_color, forcusElement)}}>
                                                                                                    {item.name?.toUpperCase()}
                                                                                                </h2>
                                                                                            </div>
                                                                                        </div>
                                                                                    ))}
                                                                                </Slider>
                                                                            )}
                                                                        </div>
                                                                    )
                                                                )}
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div className={`${counponDeskContainAllContainer}`}>
                                                        <div
                                                            className={`${counponDeskContainAll} d-flex des-custom-left`}>
                                                            {coupons && coupons.length > 0 && (
                                                                <div
                                                                    className={`${counponDeskContain} product-list-coupon`}>
                                                                    <Slider {...settingCouponDesk} arrows={false}>
                                                                        {coupons.map((coupon: any, index: any) => (
                                                                            <div key={index}>
                                                                                <div
                                                                                    className={`${counponDesk} coupon-desk-description`}>
                                                                                    <div className={`${mapDesk}`}>
                                                                                        <svg
                                                                                            xmlns="http://www.w3.org/2000/svg"
                                                                                            width="24" height="24"
                                                                                            viewBox="0 0 24 24"
                                                                                            fill="none">
                                                                                            <path
                                                                                                d="M12 22C17.5228 22 22 17.5228 22 12C22 6.47715 17.5228 2 12 2C6.47715 2 2 6.47715 2 12C2 17.5228 6.47715 22 12 22Z"
                                                                                                stroke={color}
                                                                                                strokeWidth="2"
                                                                                                strokeLinecap="round"
                                                                                                strokeLinejoin="round"/>
                                                                                            <path d="M12 8V12"
                                                                                                  stroke={color}
                                                                                                  strokeWidth="2"
                                                                                                  strokeLinecap="round"
                                                                                                  strokeLinejoin="round"/>
                                                                                            <path d="M12 16H12.01"
                                                                                                  stroke={color}
                                                                                                  strokeWidth="2"
                                                                                                  strokeLinecap="round"
                                                                                                  strokeLinejoin="round"/>
                                                                                        </svg>
                                                                                    </div>

                                                                                    <div
                                                                                        className={`${couponDeskDiscount}`}>{coupon ? coupon.promo_name : ''}</div>

                                                                                    <div
                                                                                        className={`${couponDeskInfo} mt-1`}>
                                                                                        {coupon.discount_type != 2 ? (
                                                                                            <>
                                                                                                {trans('coupon-5')} {currency} {coupon ? coupon.discount : ''} {trans('coupon-6')}
                                                                                            </>
                                                                                        ) : (
                                                                                            <>
                                                                                                {trans('coupon-5')} {Math.abs(coupon.percentage).toFixed(2)}% {trans('coupon-6')}
                                                                                            </>
                                                                                        )}
                                                                                    </div>
                                                                                </div>
                                                                                <div
                                                                                    className={`${couponCodeDesk} d-flex text-uppercase coupon-desk-code`}
                                                                                    style={{
                                                                                        backgroundColor: color ? color : 'black',
                                                                                        borderRadius: '5px',
                                                                                        padding: '33px',
                                                                                        boxShadow: `18px 15px 18px 0px ${color ? `rgba(${parseInt(color.slice(1, 3), 16)}, ${parseInt(color.slice(3, 5), 16)}, ${parseInt(color.slice(5, 7), 16)}, 0.2)` : 'rgba(0, 0, 0, 0.2)'}`,
                                                                                    }}>
                                                                                    <p>{coupon.code}</p>
                                                                                </div>
                                                                            </div>
                                                                        ))}
                                                                    </Slider>
                                                                </div>
                                                            )}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div
                                        className="products-left"
                                        style={{
                                            border: 'none !important',
                                            backfaceVisibility: 'hidden', /* Fixes some rendering artifacts */
                                            transform: 'translate3d(0, 0, 0)', /* Forces hardware acceleration */
                                        }}>
                                        {openTab === 1 && (
                                            <div id={`productMenu`}
                                                 className={`${variables.productMenu} container-center`}>
                                                <Favorites baseLink={baseLink} products={dataFavorites} key={1}
                                                           color={color} coupons={coupons}/>
                                            </div>
                                        )}
                                        {openTab === 2 && (
                                            <div id={`productMenu`}
                                                 className={`${variables.productMenu} container-center`}>
                                                {menuItems && menuItems.map((item: any, index: number) => (
                                                    <ProductDesk
                                                        workspaceInfo={workspaceInfo}
                                                        key={item.id}
                                                        baseLink={baseLink}
                                                        categoryItems={item}
                                                        color={color ?? null}
                                                        forcusElement={forcusElement}
                                                        search={false}
                                                        setHoliday={setHoliday}
                                                        onFocus={(id: any) => {
                                                            setForcusElement(id)
                                                        }}
                                                        coupons={coupons}
                                                        currentCategory={currentCategory}
                                                        setCurrentCategory={setCurrentCategory}
                                                        from={isSearchDeskOpen ? 'search' : ''}
                                                        isLast={index === menuItems.length - 1}
                                                        lastItem={menuItems[menuItems.length - 1]?.id}
                                                    />
                                                ))}
                                            </div>
                                        )}
                                        {openTab === 3 && (
                                            <div id={`productMenu`} className="container-center">
                                                {dataSearch && dataSearch.map((item: any, index: number) => (
                                                    <ProductDesk
                                                        workspaceInfo={workspaceInfo}
                                                        key={item.id}
                                                        baseLink={baseLink}
                                                        categoryItems={item}
                                                        color={color ?? null}
                                                        forcusElement={forcusElement}
                                                        search={false}
                                                        setHoliday={setHoliday}
                                                        onFocus={(id: any) => {
                                                            setForcusElement(id)
                                                        }}
                                                        coupons={coupons}
                                                        currentCategory={currentCategory}
                                                        setCurrentCategory={setCurrentCategory}
                                                        from={isSearchDeskOpen ? 'search' : ''}
                                                        isLast={index === dataSearch.length - 1}
                                                        lastItem={dataSearch[dataSearch.length - 1]?.id}
                                                    />
                                                ))}
                                                {dataSearch.length == 0 && (
                                                    <div className={`${emptySearch}`}>
                                                        <p>{trans('nothing-at-all-desk')}</p>
                                                    </div>
                                                )}
                                            </div>
                                        )}
                                        {openTab === 4 && (
                                            <div id={`productMenu`} className="container-center">
                                                {dataGroupOrder && dataGroupOrder.map((item: any, index: number) => (
                                                    <ProductDesk
                                                        workspaceInfo={workspaceInfo}
                                                        key={item.id}
                                                        baseLink={baseLink}
                                                        categoryItems={item}
                                                        color={color ?? null}
                                                        forcusElement={forcusElement}
                                                        search={false}
                                                        setHoliday={setHoliday}
                                                        onFocus={(id: any) => {
                                                            setForcusElement(id)
                                                        }}
                                                        coupons={coupons}
                                                        currentCategory={currentCategory}
                                                        setCurrentCategory={setCurrentCategory}
                                                        from={isSearchDeskOpen ? 'search' : ''}
                                                        isLast={index === dataGroupOrder.length - 1}
                                                        lastItem={dataGroupOrder[dataGroupOrder.length - 1]?.id}
                                                    />
                                                ))}
                                            </div>
                                        )}
                                    </div>
                                </div>

                                <div className="f-right cart-right">
                                    <div id="cart-container"
                                         className={`cart-container ${cartStyle.cart} ${cartStyle['user-website']}`}
                                         style={{width: (cart && cart.length > 0) ? '100%' : ''}}>
                                        <UserWebsiteCart
                                            origin="desktop"
                                            from="product_list"
                                            navbarHeight={0}
                                            workspace={workspaceInfo}
                                            apiData={apiData}
                                            color={color}
                                            workspaceId={workspaceId}
                                            step={step}
                                            handleActive={handleActive}
                                            setIsDeliveryType={setIsDeliveryType}
                                            isExistRedeem={false}
                                            toggleSorting={isSortOpen}
                                        />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                )}
            </div>

            {/* Footer & copyright */}
            {!isMobile && (
                <ProductFooter bodyHeight={bodyHeight}/>
            )}
        </>
    );
};
