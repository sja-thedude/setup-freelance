"use client"

import React, { memo, useEffect, useState, useMemo, useRef } from 'react'
import _ from 'lodash'
import { useI18n } from '@/locales/client'
import { useAppSelector, useAppDispatch } from '@/redux/hooks'
import style from 'public/assets/css/cart.module.scss'
import { currency } from '@/config/currency'
import { Formik, Form, Field } from 'formik'
import {
    rootChangeInCart, rootCartTotalDiscount, changeTypeFlag, changeType, changeRootCartTotalPrice, changeRootCartTmp, changeRootCartHistory
    , rootCartNote, rootCartDeliveryOpen, addCouponToCart, changeRootInvalidProductIds, removeCouponFromCart
    , rootCartValidCouponProductIds, rootCartRedeemId, handleTypeBeforeChange, rootCartDeliveryAddress, rootCartDeliveryConditions
    , changeTypeNotActiveErrorMessage, rootToggleAddToCartSuccess, resetRootCart, addGroupOrderSelectedNow, addGroupOrderSelected, addReadyDelivery, addIsReadyTakeOut, rootCartDatetime, changeIsShowRedeemGlobal, addCartTotalPriceNeedToPay
} from '@/redux/slices/cartSlice'
import ProductSuggestion from '@/app/[locale]/components/layouts/popup/productSuggestion'
import ProductSuggestionDesk from '@/app/[locale]/components/layouts/popup/productSuggestionDesk'
import ProfileUpdate from '@/app/[locale]/components/layouts/popup/ProfileUpdate'
import Cookies from "js-cookie";
import { useRouter, usePathname, useSearchParams } from 'next/navigation'
import { api } from "@/utils/axios";
import { useCheckAvailableProductsQuery } from '@/redux/services/product/productApi'
import { useCheckAvailableCategoriesQuery } from '@/redux/services/categoriesApi'
import { useGetWorkspaceDeliveryConditionsByIdLatLongQuery } from '@/redux/services/workspace/workspaceDeliveryConditionsApi'
import styleModal from 'public/assets/css/profile.module.scss'
import { useSelector } from "react-redux";
import { selectApiProfileData } from "@/redux/slices/profileSlice";
import { useGetApiProfileQuery } from '@/redux/services/profileApi';
import TypesPopup from '@/app/[locale]/components/types/typesPopup';
import { ToastContainer, toast, Slide } from 'react-toastify';
import 'react-toastify/dist/ReactToastify.css';
import InvalidCart from '../invalidCart'
import DeliveryNotShipping from '../deliveryNotShipping'
import { setGroupOrderData } from '@/redux/slices/groupOrderSlice'
import DesktopChangeType from '@/app/[locale]/components/desktop/desktopChangeType'
import DeliveryLocation from '@/app/[locale]/components/desktop/deliveryLocation'
import Login from '../../../users/login'
import { setflagNextData } from '@/redux/slices/flagNextSlice'
import { manualChangeOrderTypeDesktop } from '@/redux/slices/cartSlice'
import { setIsOnGroupDeskData } from '@/redux/slices/isOnGroupDeskSlice'
import Loyalty from '@/app/[locale]/components/ordering/loyalty/loyalty'
import { setflagForcusData } from '@/redux/slices/flagForcusSlice'
import { setFlagDesktopChangeType } from '@/redux/slices/flagDesktopChangeTypeSilce'
import styled from 'styled-components';
import useMediaQuery from '@mui/material/useMediaQuery'
import TextareaAutosize from 'react-textarea-autosize'
import useScrollPosition from '@/hooks/useScrollPosition';
import TypeNotActiveErrorMessage from '../../../workspace/typeNotActiveErrorMessage'
import { REGEX_NUMBER_CHECK } from '@/config/constants'
import { serviceCostSetting, calculateServiceCost } from '@/services/setting'

export const VALUE_DISCOUNT_TYPE = {
    NO_DISCOUNT: 0,
    FIXED_AMOUNT: 1,
    PERCENTAGE: 2,
};

export const ORDER_TYPE = {
    TAKE_AWAY: 0,
    DELIVERY: 1,
    GROUP_ORDER: 2
};

export const ERROR_TYPE = {
    NOT_DELIVERY: 1,
    NOT_FOR_SALE: 2,
    GROUP_ORDER: 3
};

const CustomScrollbar = styled.div`
    overflow-y: auto;
    padding-top: 0;

    &::-webkit-scrollbar-thumb {
        background: #888;
    }
    &::-webkit-scrollbar {
        width: 5px;
        height: 80px;
    }
`;

const OrderOverview = (props: any) => {
    let { cart, color, workspace, invalidProductIds, activeStep, setIsDeliveryType, origin, changeOrderTypeDesktopManual, isExistRedeem, from } = props
    let cartCoupon: any = useAppSelector((state) => state.cart.coupon);
    let cartDeliveryOpen = useAppSelector((state) => state.cart.rootCartDeliveryOpen);
    const typeBeforeChange: any = useAppSelector((state) => state.cart.typeBeforeChange);
    ///get delivery address here
    const deliveryAddress = useAppSelector((state: any) => state.cart.rootCartDeliveryAddress);
    const trans = useI18n()
    const dispatch = useAppDispatch()
    const workspaceId = workspace?.id
    const [isPopupOpen, setIsPopupOpen] = useState(false);
    const [removeIndex, setRemoveIndex] = useState<any>(null);
    const [removeProductId, setRemoveProductId] = useState<any>(null);
    const router = useRouter()
    const [suggestionProduct, setSuggestionProduct] = useState<any[]>([]);
    const [isOpenEditProfile, setIsOpenEditProfile] = useState(false);
    const tokenLoggedInCookie = Cookies.get('loggedToken');
    const language = Cookies.get('Next-Locale');
    const groupOrderSlice = useAppSelector<any>((state: any) => state.groupOrder.data);
    const reedemSlice = useAppSelector<any>((state: any) => state.cart.rootCartRedeemId);
    const nextFlagSlice = useAppSelector<any>((state: any) => state.flagNext.data);
    const groupOrderSelected = useAppSelector<any>((state: any) => state.cart.groupOrderSelected);
    const isOnGroupDesk = useAppSelector<any>((state: any) => state.isOnGroupDesk.data);
    const groupOrderNowSlice = useAppSelector<any>((state: any) => state.cart.groupOrderSelectedNow);
    const typeNotActiveErrorMessage = useAppSelector<any>((state: any) => state.cart.typeNotActiveErrorMessage);

    if (cart && cart.length > 0) {
        var firstCategoryId = cart[0].product?.data?.category_id;
    } else {
        Cookies.remove('productSuggestion');
    }

    const [isHideChangeTypeScreenComplete, setIsHideChangeTypeScreenComplete] = useState(false);
    const allSameCategory = cart?.every((item: any) => item.product?.data?.category_id === firstCategoryId);
    let categoryId = allSameCategory ? firstCategoryId : 0;
    const productIds = _.map(cart, 'productId')
    const categoryIds = _.map(cart, 'product.data.category_id')
    const { data: productAvailable, isLoading: isLoadingProduct } = useCheckAvailableProductsQuery({ ids: _.uniq(productIds) })
    const { data: categoryAvailable, isLoading: isLoadingCategory } = useCheckAvailableCategoriesQuery({ ids: _.uniq(categoryIds) })
    const [productOptionNotExist, setProductOptionNotExist] = useState<Array<number>>([])
    const [productOptionNotCount, setProductOptionNotCount] = useState<Array<number>>([])
    const [productCategoryNotAvailable, setProductCategoryNotAvailable] = useState(0)
    const [productDeliveryInvalid, setProductDeliveryInvalid] = useState(0)
    const [productOptionAvailables, setProductOptionAvailables] = useState<any>({})
    const [products, setProducts] = useState<any>({})
    const [counponPrice, setCounponPrice] = useState('')
    const [loyalPrice, setLoyalPrice] = useState('')
    const [groupOrderPrice, setGroupOrderPrice] = useState('')
    const [isRedeem, setIsRedeem] = useState(false)
    const [isShowRedeem, setIsShowRedeem] = useState(true)
    const [isExsitRedeem, setIsExsitRedeem] = useState(false)
    const [isChangeTotalPrice, setIsChangeTotalPrice] = useState(false)
    const [cartTotalPriceCoupon, setCartTotalPriceCoupon] = useState('')
    const [cartTotalPriceGroupOrder, setCartTotalPriceGroupOrder] = useState('')
    const [cartTotalPriceLoyal, setCartTotalPriceLoyal] = useState('')
    const [currentInput, setCurrentInput] = useState('');
    const [couponError, setCouponError] = useState('');
    const [couponErrorButton, setCouponErrorButton] = useState('');
    const [isVisible, setIsVisible] = useState(false);
    const [firstName, setFirstName] = useState('');
    const defaultNote = useAppSelector((state) => state.cart.rootCartNote);
    const [note, setNote] = useState(defaultNote);
    const [gsm, setGsm] = useState('');
    const [isFormValid, setIsFormValid] = useState(false);
    const [loadedProducts, setLoadedProducts] = useState(0)
    const [isShow, setIsShow] = useState(0)
    const [loadedProductOptions, setLoadedProductOptions] = useState(0)
    const [errorMessage, setErrorMessage] = useState(0)
    useGetApiProfileQuery(tokenLoggedInCookie || '');
    var apiSliceProfile = useSelector(selectApiProfileData);
    let rootType = useAppSelector((state) => state.cart.type)
    const takeoutOn = useMemo(() => workspace?.setting_open_hours?.find((item: any) => item.type === 0)?.active, [workspace])
    const deliveryOn = useMemo(() => workspace?.setting_open_hours?.find((item: any) => item.type === 1)?.active, [workspace])
    const groupOrderOn = useMemo(() => workspace?.extras?.find((item: any) => item.type === 1)?.active, [workspace])
    const [triggerShowInvalidCart, setTriggerShowInvalidCart] = useState(false)
    const [isDeliveryOrderOpenManual, setIsDeliveryOrderOpenManual] = useState(0)
    const [isDisabled, setIsDisabled] = useState(true);
    const [groupName, setGroupName] = useState('');
    const [isGroupOrderOn, setIsGroupOrderOn] = useState(false);
    let workspaceDeliveryConditions: any = null
    let deliveryConditionLoading = true
    const { data: getWorkspaceDeliveryConditions, isLoading: isDeliveryCondLoading } = useGetWorkspaceDeliveryConditionsByIdLatLongQuery({ id: workspaceId, lat: deliveryAddress?.lat, lng: deliveryAddress?.lng })
    const inputProductNumberRefs = useRef<any>([])
    const buttonNextRef = useRef<any>(null)
    const [changeOrderType, setChangeOrderType] = useState(false)
    const [isLoginOpen, setIsLoginOpen] = useState<any | null>(false);
    const [checkedCategory, setCheckedCategory] = useState(false)
    let rootCartTmp = useAppSelector((state) => state.cart.rootCartTmp);
    let rootCartHistory = useAppSelector((state) => state.cart.rootCartHistory);
    const isReadyDelivery = useAppSelector((state) => state.cart.readyDelivery);
    const isReadyTakeOut = useAppSelector((state) => state.cart.isReadyTakeOut);
    const togglePopupLogin = () => {
        setIsLoginOpen(!isLoginOpen);
    }
    
    if (!isDeliveryCondLoading) {
        deliveryConditionLoading = false
        workspaceDeliveryConditions = (getWorkspaceDeliveryConditions?.data)?.length ? getWorkspaceDeliveryConditions?.data[0] : null
    }

    const triggerSetIsShowRedeem = (value: boolean) => {
        setIsShowRedeem(value);
        dispatch(changeIsShowRedeemGlobal(value));
    }

    const pathname = usePathname()
    const searchParams = useSearchParams()
    var baseLink = ''

    if (pathname.includes('category')) {
        baseLink = 'category'
    } else {
        baseLink = 'table-ordering'
    }

    useEffect(() => {
        if (isExistRedeem) {
            redeemData(true);
        }
    }, [isExistRedeem])

    const isMobile = useMediaQuery('(max-width: 1279px)');
    const headers = {
        headers: {
            'Authorization': `Bearer ${tokenLoggedInCookie}`,
            'Content-Language': language
        }
    };

    useEffect(() => {
        setLoadedProducts(0)
        setLoadedProductOptions(0)
        dispatch(changeTypeNotActiveErrorMessage(false));

        const getProducts =  async () => {
            const resProducts = await api.get(`products/list?workspace_id=${workspaceId}&ids=${productIds.join(',')}`, {
                headers: {
                    'Content-Language': language
                }
            });
            setLoadedProducts(1)
            setLoadedProductOptions(1)
            setProducts((resProducts?.data?.data || []).reduce((acc: any, product: any) => {
                acc[product.id] = {data: product};
                return acc;
            }, {}));
            setProductOptionAvailables((resProducts?.data?.data || []).reduce((acc: any, product: any) => {
                acc[product.id] = {data: product.options};
                return acc;
            }, {}));
        }

        if (!_.isEmpty(cart)) {
            getProducts()
        } else {
            dispatch(rootCartNote(null))
        }
    }, [cart, workspaceId])

    useEffect(() => {
        const fetchData = async () => {
            if (categoryId) {
                try {
                    if (tokenLoggedInCookie) {
                        const res = await api.get(`categories/${categoryId}/suggestion_products?order_by=name&sort_by=asc&limit=500`, headers);
                        const resProducts = await api.get(`products/list?workspace_id=${workspaceId}&category_ids=${categoryId}`, headers);
                        setSuggestionProduct((res?.data?.data?.data || []).map((x: any) => {
                            x.options = resProducts?.data?.data?.filter((item: any) => item.id == x.id)[0]?.options || []
                            return x;
                        }));
                    }
                } catch (error) {
                    console.error(error);
                }
            } else {
                setSuggestionProduct([]);
            }
        };

        fetchData();
    }, [categoryId]);

    /**
     * Validate coupon with products and product categories
     *
     * @param coupon
     */
    const validateCouponProduct = async (coupon: any) => {
        try {
            const couponValue = coupon.code;
            const productIds = cart.map((item: any) => item.productId);
            if (productIds.length > 0) {
                // validate product with coupon
                const productIdParams = productIds.map((id: any) => `product_id[]=${id}`).join('&');
                const queryParams = `?${productIdParams}&code=${couponValue}`;
                const response = await api.get(`/products/validate_coupon${queryParams}`, headers);
                const couponData: any = cartCoupon;
                var totalPriceCouponProducts = 0;
                if (typeof response?.data?.data !== 'undefined') {
                    let validCouponProduct = false;
                    // get product that available with coupon
                    var availabelProducts = Object.keys(response?.data?.data).filter(key => response?.data?.data[key] === true);
                    if (availabelProducts.length > 0) {
                        totalPriceCouponProducts = calculateTotalPriceCouponProuduct(availabelProducts)
                    }
                    for (let pId in response?.data?.data) {
                        if (response?.data?.data[pId] == true) {
                            validCouponProduct = true;
                            break;
                        }
                    }
                    dispatch(rootCartValidCouponProductIds(availabelProducts));
                }
                if (couponData?.discount_type == VALUE_DISCOUNT_TYPE.PERCENTAGE) {
                    let discount = couponData?.percentage;
                    setCounponPrice(_.round((totalPriceCouponProducts * discount / 100), 2).toFixed(2))
                    setCartTotalPriceCoupon(_.round((cart?.reduce((total: any, item: any) => total + item.basePrice * item.productTotal, 0) - (totalPriceCouponProducts * discount / 100)), 2).toFixed(2));
                } else if (couponData?.discount_type == VALUE_DISCOUNT_TYPE.FIXED_AMOUNT) {
                    let discount = couponData?.discount;
                    const totalDiscount = parseFloat(_.round(discount, 2).toFixed(2));
                    if (totalPriceCouponProducts > totalDiscount) {
                        // if coupon price < total price of products set coupon price = coupon price
                        setCounponPrice(_.round(discount, 2).toFixed(2))
                        setCartTotalPriceCoupon(_.round((cart?.reduce((total: any, item: any) => total + item.basePrice * item.productTotal, 0) - discount), 2).toFixed(2));
                    } else {
                        // if coupon price > total price of products set coupon price = total price of products
                        setCounponPrice(_.round((totalPriceCouponProducts), 2).toFixed(2))
                        setCartTotalPriceCoupon(_.round((cart?.reduce((total: any, item: any) => total + item.basePrice * item.productTotal, 0) - totalPriceCouponProducts), 2).toFixed(2));
                    }
                } else {
                    setCounponPrice('')
                    setCartTotalPriceCoupon('')
                }
                const isValid = Object.values(response?.data?.data).some(value => value);
                if (!isValid) {
                    toast.dismiss();
                    toast(response?.data?.message || trans('failed'), {
                        position: toast.POSITION.BOTTOM_CENTER,
                        autoClose: 1500,
                        hideProgressBar: true,
                        closeOnClick: true,
                        closeButton: false,
                        transition: Slide,
                        className: 'message',
                    });
                }

                return isValid;
            }
        } catch (err: any) {
            // setCouponError(err.response.data.message);
            setCouponError(err.response.data.message);
            setCouponErrorButton(err.response.data.message);
            setIsVisible(true);

            return false;
        }
    }

    const redeemData = async (param: any) => {
        if (tokenLoggedInCookie && workspaceId && (isShowRedeem || param == true)) {
            if (!cartCoupon || !cartCoupon.code) {
                try {
                    const res = await api.get(`workspaces/${workspaceId}/loyalties/my_redeem`, headers);

                    if (res?.data?.data) {
                        const redeemRes = res?.data?.data?.reward_data;
                        const cartProductIds = _.map(cart, 'productId').map(i => Number(i));
                        const validateDatas = await api.get(`workspaces/${workspaceId}/rewards/${res?.data?.data?.reward_level_id}/validate_products?${cartProductIds.map(i => `product_id[]=${i}`).join('&')}`, headers);

                        const validProductIds = Object.entries(validateDatas?.data?.data).filter((item: any) => item[1] === true).map((item: any) => Number(item[0]));
                        dispatch(rootCartValidCouponProductIds(validProductIds));

                        const totalPriceRedeemProducts = cart.filter((item: any) => validProductIds.includes(item.productId)).reduce((total: any, item: any) => total + item.basePrice * item.productTotal, 0);
                        if (validProductIds && validProductIds.length > 0) {
                            setCartTotalPriceGroupOrder('');
                            if (redeemRes?.discount_type == VALUE_DISCOUNT_TYPE.PERCENTAGE) {
                                let discount = redeemRes?.percentage;
                                const loyalPrice = _.round((totalPriceRedeemProducts * discount / 100), 2).toFixed(2);
                                setLoyalPrice(loyalPrice);
                                setCartTotalPriceLoyal(_.round((cart?.reduce((total: any, item: any) => total + item.basePrice * item.productTotal, 0) - (totalPriceRedeemProducts * discount / 100)), 2).toFixed(2));
                            } else if (redeemRes?.discount_type == VALUE_DISCOUNT_TYPE.FIXED_AMOUNT) {
                                let discount = redeemRes?.reward;
                                const totalDiscount = parseFloat(_.round(discount, 2).toFixed(2));

                                if (totalPriceRedeemProducts > totalDiscount) {
                                    // if coupon price < total price of products set coupon price = coupon price
                                    setLoyalPrice(_.round(discount, 2).toFixed(2))
                                    setCartTotalPriceLoyal(_.round((cart?.reduce((total: any, item: any) => total + item.basePrice * item.productTotal, 0) - discount), 2).toFixed(2));
                                } else {
                                    // if coupon price > total price of products set coupon price = total price of products
                                    setLoyalPrice(_.round((totalPriceRedeemProducts), 2).toFixed(2))
                                    setCartTotalPriceLoyal(_.round((cart?.reduce((total: any, item: any) => total + item.basePrice * item.productTotal, 0) - totalPriceRedeemProducts), 2).toFixed(2));

                                }
                            } else {
                                setLoyalPrice('')
                                setCartTotalPriceLoyal('')
                            }
                            setIsRedeem(true);
                            setIsExsitRedeem(true);

                            dispatch(addCouponToCart(redeemRes));
                            dispatch(rootCartRedeemId(res?.data?.data?.id));
                        } else {
                            setIsRedeem(false);
                            setIsExsitRedeem(false);
                            dispatch(rootCartRedeemId(null));
                        }
                    }
                } catch (err: any) {
                    setIsRedeem(false);
                    setIsExsitRedeem(false);
                    dispatch(rootCartRedeemId(null));
                }
            }
        }
    };

    useEffect(() => {
        setIsChangeTotalPrice(false);
    }, [workspaceId, tokenLoggedInCookie, isShowRedeem, isChangeTotalPrice]);

    const calculateTotalPriceCouponProuduct = (productIds: any) => {
        var totalBasePrice = 0;
        const filteredProducts = cart.filter((product: any) => {
            return productIds.includes(product.productId.toString());
        });
        if (filteredProducts.length > 0) {
            totalBasePrice = filteredProducts.reduce((total: any, product: any) => {
                return total + product.basePrice * product.productTotal;
            }, 0);
        }
        return totalBasePrice
    }

    useEffect(() => {
        if (!_.isEmpty(cart)
            && !isLoadingProduct
            && !isLoadingCategory
            && loadedProducts == 1
            && loadedProductOptions == 1) {
            let cartClone = [...cart]
            const productOptionNotExistClone: any = []
            setProductCategoryNotAvailable(0)

            cartClone = _.map(cartClone, (item, key) => {
                let cartItem = { ...item }
                const optionItemsPrice: any = {}
                if (categoryAvailable?.data[cartItem.product.data.category_id] == false || productAvailable?.data[cartItem.product.data.id] == false) {
                    setProductCategoryNotAvailable(1)
                    setIsPopupOpen(false)
                    productOptionNotExistClone.push(key)
                }

                const productId = cartItem.product.data.id

                if (products[productId]) {
                    cartItem.product = products[productId]
                    cartItem.productOptions = productOptionAvailables[productId]
                    cartItem.optionItemsStore = _.map(item?.optionItemsStore, (optionItemsStoreValue: any) => {
                        const optionItemsStoreValueClone = { ...optionItemsStoreValue }
                        const checkExistOption = _.find(productOptionAvailables[productId]?.data, { id: optionItemsStoreValueClone.optionId })

                        if (!checkExistOption) {
                            productOptionNotExistClone.push(key)
                        }

                        // compare with live options and refresh them
                        const optionMasterStore = _.find(optionItemsStoreValueClone.optionItems, { master: true })
                        optionItemsStoreValueClone.optionItems = _.map(optionItemsStoreValueClone.optionItems, (optionItem: any) => {
                            let optionItemId = optionItem?.id

                            if (optionMasterStore) {
                                optionItemId = optionMasterStore.id
                            }

                            const checkExistOptionItem = _.find(checkExistOption?.items, { id: optionItemId, available: true })
                            const newItem = _.find(checkExistOption?.items, { id: optionItem?.id })

                            if (!checkExistOptionItem) {
                                productOptionNotExistClone.push(key)
                            } else {
                                optionItem = newItem
                            }

                            return optionItem
                        })

                        // BEGIN: calculate price
                        const master = _.find(optionItemsStoreValueClone.optionItems, { master: true })

                        if (master) {
                            if (optionItemsPrice[key]) {
                                optionItemsPrice[key].push(_.toNumber(master.price))
                            } else {
                                optionItemsPrice[key] = [_.toNumber(master.price)]
                            }
                        } else {
                            const convertStrToNumber = _.map(optionItemsStoreValueClone.optionItems, 'price').map(i => Number(i))

                            if (optionItemsPrice[key]) {
                                optionItemsPrice[key].push(_.sum(convertStrToNumber))
                            } else {
                                optionItemsPrice[key] = [_.sum(convertStrToNumber)]
                            }
                        }
                        // END: calculate price

                        return optionItemsStoreValueClone
                    })

                    cartItem.basePrice = _.sum(optionItemsPrice[key]) + _.toNumber(cartItem.product.data.price)

                    if (rootType == 2 && cartItem?.product?.data?.category?.available_delivery == false) {
                        setProductDeliveryInvalid(1)
                        productOptionNotExistClone.push(key)
                    }
                }

                return cartItem
            })
            if (!_.isEqual(_.uniq(productOptionNotExistClone), _.uniq(productOptionNotExist)) && !checkedCategory) {
                setProductOptionNotExist(_.uniq(productOptionNotExistClone))
            }

            if (!_.isEqual(cart, cartClone)) {
                dispatch(rootChangeInCart(cartClone))
            }

            // Load coupon to cart
            if (cartCoupon && !isRedeem && cartCoupon?.code) {
                // Check available coupon with product and delete if not available
                validateCouponProduct(cartCoupon)
                    .then(function (success) {
                        if (!success) {
                            handleRemoveCoupon();
                        }
                    }, function (error) {
                        // console.log('error:', error);
                    });

            } else {
                if (!groupOrderNowSlice) {
                    redeemData(true);
                }
            }
        }
    }, [
        cart,
        isLoadingCategory,
        isLoadingProduct,
        productAvailable,
        categoryAvailable,
        productOptionAvailables,
        products,
        productOptionNotExist,
        productCategoryNotAvailable,
        rootType,
        loadedProducts,
        loadedProductOptions,
        dispatch,
        workspaceId,
        checkedCategory,
    ])

    const checkedCartBeforeNextStep = async () => {
        setLoadedProducts(0)
        setLoadedProductOptions(0)
        setCheckedCategory(false)
        const getProducts = async () => {
            const resProducts = await api.get(`products/list?workspace_id=${workspaceId}&ids=${_.uniq(productIds).join(',')}`, {
                headers: {
                    'Content-Language': language
                }
            });
            setLoadedProducts(1)
            setLoadedProductOptions(1)
            const allProducts = (resProducts?.data?.data || []).reduce((acc: any, product: any) => {
                acc[product.id] = {data: product};
                return acc;
            }, {});
            setProducts(allProducts);
            const productOptions = (resProducts?.data?.data || []).reduce((acc: any, product: any) => {
                acc[product.id] = {data: product.options};
                return acc;
            }, {});
            setProductOptionAvailables(productOptions);
            let checking = true;
            
            const productOptionNotExistClone: any = [];
            const differentIndexes: any = [];
            // case delete category products
            if (Object.keys(allProducts).length == 0) {
                checking = false;
            } else {
                // case delete category products
                if (Object.keys(allProducts).length !== _.uniq(productIds).length) {
                    let realIndex = 0;
                    _.uniq(productIds).forEach((item) => {
                        if (!Object.keys(allProducts).includes(String(item))) {
                            differentIndexes.push(realIndex);
                        }
                        realIndex++;
                    });
                    setProductOptionNotExist(_.uniq(differentIndexes));
                    setCheckedCategory(true);
                    checking = false;
                }
                else {
                    // case turn off category products
                    for (const key in allProducts) {
                        if (Object.hasOwnProperty.call(allProducts, key)) {
                            const item = allProducts[key];
                            if (item?.data?.hasOwnProperty('active') && item?.data?.active === false) {
                                const dataKeys = Object.keys(allProducts);
                                const pos = dataKeys.indexOf(key);
                                productOptionNotExistClone.push(pos)
                                checking = false;
                            }
                        }
                    }
                    if (!checking) {
                        setProductOptionNotExist(_.uniq(productOptionNotExistClone))
                        setCheckedCategory(true)
                    } else {
                        setCheckedCategory(false)
                    }
                }

            }
            const filterUnavailableOptions = (data: any) => {
                const result: any = {};
                _.forEach(data, (value, key) => {
                    result[key] = {
                        data: _.map(value.data, (item) => ({
                            ...item,
                            items: _.filter(item.items, { available: false })
                        }))
                    };
                });
                return result;
            };
            const filteredData = filterUnavailableOptions(productOptions);
            // Get the IDs of the false option along with its child options
            const unavailableOptionDetails: Record<number, number[]> = {};
            _.forEach(filteredData, (item: any) => {
                _.forEach(item.data, (option: any) => {
                    const unavailableOptionIds: number[] = [option.id];
                    if (!option.available) {
                        unavailableOptionIds.push(..._.chain(option.items)
                            .filter((item: any) => !item.available)
                            .map((item: any) => item.id)
                            .value());
                    }
                    if (unavailableOptionIds.length > 1) {
                        unavailableOptionDetails[option.id] = unavailableOptionIds.slice(1);
                    }
                });
            });
            // All IDS
            const allIds = Object.values(productOptions).flatMap((item: any) => {
                if (item.data && Array.isArray(item.data)) {
                    return item.data.flatMap((subItem: any) => {
                        if (subItem.items && Array.isArray(subItem.items)) {
                            return subItem.items.map((subSubItem: any) => subSubItem.id);
                        }
                        return [];
                    });
                }
                return [];
            });

            cart.forEach((item: any) => {
                item.optionItemsStore.forEach((optionItem: any) => {
                    const unavailableOptionIdsForOption = unavailableOptionDetails[optionItem.optionId];
                    //case delete option
                    const isOptionItemValid = optionItem.optionItems.every((item: any) => allIds.includes(item.id));
                    if (!isOptionItemValid) {
                        checking = false;
                        return;
                    }
                    //case turn off option when in cart is available
                    if (unavailableOptionIdsForOption) {
                        const containsUnavailableOption = optionItem.optionItems.some((item: any) =>
                            item.available && unavailableOptionIdsForOption.includes(item.id)
                        );
                        if (containsUnavailableOption) {
                            checking = false;
                            return;
                        }
                    }
                });
            });
            return checking;
        }

        if (!_.isEmpty(cart)) {
            const checking = await getProducts();
            return checking;
        } else {
            dispatch(rootCartNote(null))
        }
    }

    useEffect(() => {
        if (_.isEmpty(cart)) {
            dispatch(addGroupOrderSelectedNow(null))
            dispatch(addGroupOrderSelected(null));
            Cookies.set('oppenedSuggest', 'false')
        }
    }, [cart])

    useEffect(() => {
        if (rootType !== 1) {
            Cookies.set('oppenedSuggest', 'false')
            setIsPopupOpen(false)
        }
    }, [products])

    const changeNumberProduct = (productIndex: number, value: number, isManual: boolean = false) => {
        let cartClone = [...cart]
        let newNumber = _.toNumber(cartClone[productIndex].productTotal) + _.toNumber(value)

        if (isManual === true) {
            newNumber = _.toNumber(value)
        }

        if (newNumber > 0) {
            const product = { ...cartClone[productIndex] }
            product.productTotal = newNumber
            cartClone[productIndex] = { ...product }
            dispatch(rootChangeInCart(cartClone))
            cart = [...cartClone]

            if (inputProductNumberRefs.current[productIndex]) {
                inputProductNumberRefs.current[productIndex].value = newNumber.toString()
            }
        }

        if (currentInput && !isRedeem) {
            handleSubmitCoupon({ coupon: currentInput }, { resetForm: () => { } });
        }

        setIsChangeTotalPrice(true);
    }

    const removeProduct = (productIndex: number) => {
        const cartClone = [...cart]
        _.remove(cartClone, cartClone[productIndex])
        dispatch(rootChangeInCart(cartClone))
        cart = [...cartClone]

        if (_.isEmpty(cart)) {
            // Remove coupon from cart
            handleRemoveCoupon();
            setIsDeliveryType(false);
            dispatch(rootCartNote(null))
            dispatch(changeType(1))
            dispatch(addGroupOrderSelectedNow(null))
            dispatch(setGroupOrderData(null));
            dispatch(rootCartDatetime(null));
            if (!isMobile && searchParams.get('delivery') == 'true') {
                window.location.href = `/${baseLink}/products`
            }
            window.location.reload();
        }

        if (isRedeem && isExsitRedeem) {
            redeemData(true);
        }
        const updatedProductOptionNotExist = productOptionNotExist.filter(item => item !== productIndex);
        setProductOptionNotExist(updatedProductOptionNotExist);
        setIsChangeTotalPrice(true);
    }

    const removeInvalidProductId = (value: any) => {
        const index = invalidProductIds?.indexOf(value);

        if (index > -1) { // only splice array when item is found
            const newArr = [...invalidProductIds];
            newArr.splice(index, 1); // 2nd parameter means remove one item only
            dispatch(changeRootInvalidProductIds(newArr));
        }
        setIsChangeTotalPrice(true);
    }

    const togglePopup = async () => {
        if (REGEX_NUMBER_CHECK.test(apiSliceProfile?.data?.first_name)) {
            setIsOpenEditProfile(true);
            setIsShow(isShow + 1);
            return;
        }
        if (!isFormValid) {
            setIsOpenEditProfile(true);
            setIsShow(isShow + 1);
        }
        const result = await checkedCartBeforeNextStep();
        if (tokenLoggedInCookie && suggestionProduct.length > 0) {
            if (result) {
                setIsPopupOpen(true);
            }
            if (Cookies.get('productSuggestion') == 'true') {
                setIsPopupOpen(false);
            }
        } else if (!tokenLoggedInCookie) {
            if (origin !== 'desktop') {
                router.push('/user/login?categoryCart=true');
            } else {
                if (groupOrderNowSlice) {
                    Cookies.set('step2GroupDesk', 'true')
                }
            }
        }
    }

    useEffect(() => {
        if (!isMobile) {
            if (Cookies.get('fromDesk') == 'suggestionDesk') {
                if (buttonNextRef.current) {
                    buttonNextRef.current.click()
                }
            } else if (Cookies.get('fromDesk') == 'groupOrderDesk') {
                toggleOrderType();
            }
        }
    }, [Cookies.get('fromDesk')])

    const handleActiveStep2 = async () => {
        if(apiSliceProfile?.data?.gsm.length > 0 && !apiSliceProfile?.data?.first_name?.includes('@')) {
            if (workspace) {
                const takeoutOn = workspace?.setting_open_hours?.find((item: any) => item.type === 0)?.active
                const deliveryOn = workspace?.setting_open_hours?.find((item: any) => item.type === 1)?.active
                const groupOrderOn = workspace?.extras?.find((item: any) => item.type === 1)?.active
    
                if (groupOrderOn !== true && (rootType == 1 && takeoutOn == false && takeoutOn != 'undefined')
                    || (rootType == 2 && deliveryOn == false && deliveryOn != 'undefined')
                    || (rootType == 3 && groupOrderOn == false && groupOrderOn != 'undefined')) {
                    setTriggerShowInvalidCart(true)
                } else {
                    setTriggerShowInvalidCart(false)
                    const result = await checkedCartBeforeNextStep();
                    if (!productCategoryNotAvailable && tokenLoggedInCookie && productOptionNotExist.length == 0 && result) {
                        triggerActiveStep2()
                        Cookies.remove('productSuggestion')
                        Cookies.remove('step2GroupDesk')
                    }
                }
            } else {
                if (tokenLoggedInCookie) {
                    if (!Cookies.get('atStep2')) {
                        triggerActiveStep2()
                    }
                }
            }
        }
    }

    const triggerActiveStep2 = () => {
        activeStep(2)
    }

    if (!productCategoryNotAvailable && tokenLoggedInCookie && Cookies.get('step2GroupDesk') == 'true' && groupOrderNowSlice) {
        triggerActiveStep2()
        Cookies.remove('productSuggestion')
    }
    // clear cart when type off in admin
    useEffect(() => {
        if (origin === 'desktop' && (
            (rootType == 1 && takeoutOn == false && takeoutOn != 'undefined')
            || (rootType == 2 && deliveryOn == false && deliveryOn != 'undefined')
            || (rootType == 3 && groupOrderOn == false && groupOrderOn != 'undefined')
        )) {
            if (isHideChangeTypeScreenComplete) {
                dispatch(resetRootCart());
            }
        }
    }, [
        takeoutOn,
        deliveryOn,
        groupOrderOn,
        triggerShowInvalidCart,
        rootType,
        origin,
        isHideChangeTypeScreenComplete
    ])

    const toggleOrderType = () => {
        dispatch(changeTypeFlag(true))
        setChangeOrderType(true)
        dispatch(rootCartDeliveryOpen(false))
        dispatch(handleTypeBeforeChange(rootType))

        if (isExsitRedeem && !isShowRedeem && (!groupOrderNowSlice || !groupOrderNowSlice?.id)) {
            handleAgain()
        }
    }

    const onClickChangeType = (type: number) => {
        if (type != rootType) {
            dispatch(changeTypeFlag(false))
            isPopupOpen && setIsPopupOpen(false)
            if (type === 2) {
                //dispatch(changeType(type))
                dispatch(addIsReadyTakeOut(false));
                setDeliveryAddressTmp(null)
                setIsDeliveryOrderOpenManual(1)
            } else {
                dispatch(changeType(type))
            }

            if (type != 2 && window.location.href.includes('category/products')) {
                setIsDeliveryType(false)
            }
        }

    }

    const handleSubmitCoupon = (values: { coupon: string }, { resetForm }: { resetForm: () => void }) => {
        const couponValue = values.coupon;
        // validate coupon
        api.get(`coupons/validate_code?code=${couponValue}&workspace_id=${workspaceId}`, headers)
        .then(cou => {
            if (cou?.status == 200 && cou?.data?.success == true) {
                const productIds = cart.map((item: any) => item.productId);
                if (productIds.length > 0) {
                    // validate product with coupon
                    const productIdParams = productIds.map((id: any) => `product_id[]=${id}`).join('&');
                    const queryParams = `?${productIdParams}&code=${couponValue}`;
                    api.get(`/products/validate_coupon${queryParams}`, headers)
                    .then(product => {
                        if (product?.status == 200 && product?.data?.success == true) {
                            let validCouponProduct = false;
                            var totalPriceCouponProducts = 0;
                            if (typeof product?.data?.data !== 'undefined') {
                                // get product that available with coupon
                                var availabelProducts = Object.keys(product?.data?.data).filter(key => product?.data?.data[key] === true);
                                if (availabelProducts.length > 0) {
                                    totalPriceCouponProducts = calculateTotalPriceCouponProuduct(availabelProducts)
                                }
                                for (let pId in product?.data?.data) {
                                    if (product?.data?.data[pId] == true) {
                                        validCouponProduct = true;
                                        break;
                                    }
                                }
                                dispatch(rootCartValidCouponProductIds(availabelProducts));
                            }

                            if (!validCouponProduct) {
                                setCouponError(trans('cart.message_invalid_coupon_product'));
                                // setCouponErrorButton(trans('cart.message_invalid_coupon_product'));
                                setIsVisible(true)

                                return;
                            }

                            resetForm();
                            // set group order price to empty when add coupon
                            setGroupOrderPrice('');
                            setCartTotalPriceGroupOrder('');
                            const couponData = cou?.data?.data;
                            if (couponData?.discount_type == VALUE_DISCOUNT_TYPE.PERCENTAGE) {
                                let discount = couponData?.percentage;
                                setCounponPrice(_.round((totalPriceCouponProducts * discount / 100), 2).toFixed(2))
                                setCartTotalPriceCoupon(_.round((cart?.reduce((total: any, item: any) => total + item.basePrice * item.productTotal, 0) - (totalPriceCouponProducts * discount / 100)), 2).toFixed(2));
                            } else if (couponData?.discount_type == VALUE_DISCOUNT_TYPE.FIXED_AMOUNT) {
                                let discount = couponData?.discount;
                                const totalDiscount = parseFloat(_.round(discount, 2).toFixed(2));
                                if (totalPriceCouponProducts > totalDiscount) {
                                    // if coupon price < total price of products set coupon price = coupon price
                                    setCounponPrice(_.round(discount, 2).toFixed(2))
                                    setCartTotalPriceCoupon(_.round((cart?.reduce((total: any, item: any) => total + item.basePrice * item.productTotal, 0) - discount), 2).toFixed(2));
                                } else {
                                    // if coupon price > total price of products set coupon price = total price of products
                                    setCounponPrice(_.round((totalPriceCouponProducts), 2).toFixed(2))
                                    setCartTotalPriceCoupon(_.round((cart?.reduce((total: any, item: any) => total + item.basePrice * item.productTotal, 0) - totalPriceCouponProducts), 2).toFixed(2));
                                }
                            } else {
                                setCounponPrice('')
                                setCartTotalPriceCoupon('')
                            }

                            // Add coupon into cart
                            dispatch(addCouponToCart(couponData));
                            triggerSetIsShowRedeem(false);
                            setIsRedeem(false);
                            setCouponError('');
                            setCouponErrorButton('');
                            setIsVisible(false);

                            toast.dismiss();
                            if (isMobile) {
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
                        }
                    }).catch(err => {
                        // const responseError = err.response.data;
                        if (typeof err.response.data !== 'undefined') {
                            setCouponError(err.response.data.message);
                            // setCouponErrorButton(err.response.data.message);
                            setIsVisible(true)
                        }
                    });
                }
            }
        }).catch(err => {
            setCouponErrorButton(err.response.data.message);
            setCouponError(err.response.data.message);
            setIsVisible(true)
        });
    }

    const handleRemoveCoupon = () => {
        dispatch(removeCouponFromCart());
        if (isRedeem) {
            triggerSetIsShowRedeem(false);
        } else {
            triggerSetIsShowRedeem(true);
        }
        setCounponPrice('');
        setCartTotalPriceCoupon('');
        dispatch(addCouponToCart(null));
        cartCoupon = null;
        setCurrentInput('');
        calculateGroupOrderPrice();
    }

    const handleRemoveLoyal = () => {
        dispatch(removeCouponFromCart());
        if (isRedeem) {
            triggerSetIsShowRedeem(false);
        } else {
            triggerSetIsShowRedeem(true);
        }
        if (origin === 'desktop') {
            triggerSetIsShowRedeem(false);
        }
        setLoyalPrice('');
        setCartTotalPriceLoyal('');
        setCurrentInput('');
        setCounponPrice('');
        setCartTotalPriceCoupon('');
        setIsRedeem(false);
        if (groupOrderNowSlice && groupOrderNowSlice?.discount_type !== VALUE_DISCOUNT_TYPE.NO_DISCOUNT) {
            calculateGroupOrderPrice();
        }
    }

    const handleLoyalGroupOrder = () => {
        setCurrentInput('');
        setCounponPrice('');
        setCartTotalPriceCoupon('');
        if (groupOrderNowSlice && groupOrderNowSlice?.id) {
            dispatch(removeCouponFromCart());
            dispatch(addCouponToCart(null));
            cartCoupon = null;
        }
        redeemData(true);
    }

    const handleRemoveGroupOrder = () => {
        setGroupOrderPrice('');
        setCartTotalPriceGroupOrder('');
    }

    const query = new URLSearchParams(window.location.search);

    const calculateGroupOrderPrice = () => {
        if (groupOrderNowSlice && groupOrderNowSlice?.id && workspaceId && !_.isEmpty(cart)
            && ((!cartCoupon) || (cartCoupon && !cartCoupon?.code))) {
            const groupOrder = groupOrderNowSlice?.id;
            api.get(`/groups/${groupOrder}`, headers)
            .then((groupDetail: any) => {
                if (groupDetail?.status == 200 && groupDetail?.data?.success == true) {
                    const groupDetailData = groupDetail?.data?.data;
                    dispatch(setGroupOrderData(groupDetailData));
                    setGroupName(groupDetailData?.name);
                    const productIds = cart.map((item: any) => item.productId);
                    if (productIds.length > 0) {
                        // validate product with groupOrder
                        let isNotForSale: any = false;
                        const productOptionNotCountCopy: any = [];
                        var count = 0;
                        cart.forEach((product: any, index: any) => {
                            if (product?.product?.data) {
                                const productData = product?.product?.data;

                                if (groupDetailData && groupDetailData?.is_product_limit !== 0) {
                                    isNotForSale = groupDetailData && (groupDetailData?.type === ORDER_TYPE.TAKE_AWAY || groupDetailData?.type === ORDER_TYPE.DELIVERY) && groupDetailData?.products.findIndex((prod: any) => prod.id === productData?.id) < 0;

                                    if (isNotForSale) {
                                        productOptionNotCountCopy.push(index)
                                        setErrorMessage(ERROR_TYPE.NOT_FOR_SALE)
                                        count++;
                                    }
                                }
                                const isNotDelivery = groupDetailData && (groupDetailData?.type === ORDER_TYPE.DELIVERY) && !productData?.category.available_delivery;

                                if (isNotDelivery) {
                                    productOptionNotCountCopy.push(index)
                                    setErrorMessage(ERROR_TYPE.NOT_DELIVERY)
                                    count++;
                                }
                            }
                        });
                        if (count == 0) {
                            setErrorMessage(0)
                        }
                        if (!_.isEqual(_.uniq(productOptionNotCountCopy), _.uniq(productOptionNotCount))) {
                            setProductOptionNotCount(_.uniq(productOptionNotCountCopy))
                        }
                        if (groupDetail?.status == 200 && groupDetail?.data?.success == true) {
                            const discountData = groupDetail?.data?.data;
                            // disable loyalty and Coupon when group order
                            setCounponPrice('');
                            setCartTotalPriceCoupon('');
                            if (reedemSlice) {
                                setIsExsitRedeem(true);
                            } else {
                                redeemData(true)
                            }
                            setLoyalPrice('');
                            setCartTotalPriceLoyal('');
                            setIsRedeem(false);
                            triggerSetIsShowRedeem(false);
                            dispatch(addCouponToCart(null));
                            if (discountData?.discount_type == VALUE_DISCOUNT_TYPE.PERCENTAGE) {
                                let discount = discountData?.percentage;
                                setGroupOrderPrice(_.round((cart?.reduce((total: any, item: any) => total + item.basePrice * item.productTotal, 0) * discount / 100), 2).toFixed(2))
                                setCartTotalPriceGroupOrder(_.round((cart?.reduce((total: any, item: any) => total + item.basePrice * item.productTotal, 0) * (100 - discount) / 100), 2).toFixed(2));
                            } else if (discountData?.discount_type == VALUE_DISCOUNT_TYPE.FIXED_AMOUNT) {
                                let discount = discountData?.discount;
                                const totalPrice = parseFloat((cart?.reduce((total: any, item: any) => total + item.basePrice * item.productTotal, 0)).toFixed(2));
                                const totalDiscount = parseFloat(_.round(discount, 2).toFixed(2));
                                if (totalPrice > totalDiscount) {
                                    setGroupOrderPrice(_.round(discount, 2).toFixed(2))
                                    setCartTotalPriceGroupOrder(_.round((cart?.reduce((total: any, item: any) => total + item.basePrice * item.productTotal, 0) - discount), 2).toFixed(2));
                                } else {
                                    setGroupOrderPrice((cart?.reduce((total: any, item: any) => total + item.basePrice * item.productTotal, 0)).toFixed(2))
                                    setCartTotalPriceGroupOrder('0.00');
                                }
                            } else {
                                setGroupOrderPrice('')
                                setCartTotalPriceGroupOrder('')
                                handleAgain()
                            }
                        }

                    }
                }
            }).catch(err => {
                // console.log(err);
            });
        } else {
            setGroupOrderPrice('');
            setCartTotalPriceGroupOrder('');
            dispatch(addGroupOrderSelectedNow(null))
        }
    }

    useEffect(() => {
        if (groupOrderNowSlice && groupOrderNowSlice?.id && workspaceId) {
            calculateGroupOrderPrice();
        }

        if (!groupOrderNowSlice || !groupOrderNowSlice?.id) {
            if (rootType == 1) {
                setErrorMessage(0)
                setProductOptionNotCount([])
            }
            // handleAgain()
            triggerSetIsShowRedeem(true);
            handleLoyalGroupOrder()
        }
    }, [groupOrderNowSlice, workspaceId, cart]);

    useEffect(() => {
        if (couponErrorButton) {
            //If there is a condition to display the div, display it and set a timeout to hide the div after 3 seconds
            setIsVisible(true);
            if (origin !== 'desktop') {
                const timeout = setTimeout(() => {
                    setIsVisible(false);
                    setCouponErrorButton('');
                }, 3000);
                return () => clearTimeout(timeout); // delete timeout
            }
        }
    }, [couponErrorButton])

    const checkEmailValid = (email: string) => {
        const emailRegex = /^[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,}$/i;
        return emailRegex.test(email);
    }

    useEffect(() => {
        setFirstName(apiSliceProfile?.data?.first_name ?? '');
        setGsm(apiSliceProfile?.data?.gsm ?? '');
        if (apiSliceProfile?.data?.first_name == ""
            || apiSliceProfile?.data?.first_name?.includes("@")
            || REGEX_NUMBER_CHECK.test(apiSliceProfile?.data?.first_name)
            || apiSliceProfile?.data?.gsm == ""
            || !apiSliceProfile?.data?.gsm) {
            setIsFormValid(false);
        } else {
            setIsFormValid(true);
        }

        if(!apiSliceProfile?.data?.email || !checkEmailValid(apiSliceProfile?.data?.email)) {
            setIsFormValid(false);
        }

        if (apiSliceProfile == null) {
            setIsFormValid(true);
        }
    }, [
        apiSliceProfile?.data?.first_name, 
        apiSliceProfile?.data?.gsm,
        apiSliceProfile?.data?.email,
    ]);

    const profileData = (data: boolean) => {
        if (data) {
            setIsFormValid(true);
            setIsOpenEditProfile(false);

            if (!isPopupOpen || suggestionProduct.length == 0) {
                handleActiveStep2()
            }
        }
    }

    const handleDisable = (e: any) => {
        const value = e.target.value;

        if (value.length > 0) {
            setIsDisabled(false);
        } else {
            setIsDisabled(true);
        }
    }

    const submitStep1 = async (totalPrice: number) => {
        let validForm = true;
        if (tokenLoggedInCookie && REGEX_NUMBER_CHECK.test(apiSliceProfile?.data?.first_name)) {
            setIsOpenEditProfile(true);
            setIsFormValid(false);
            setIsShow(isShow + 1);
            return;
        }
        if (cartCoupon && cartCoupon?.code) {
            const validCoupon = await validateCouponProduct(cartCoupon);

            if (!validCoupon) {
                handleRemoveCoupon();
                validForm = false;
            }
        }
        
        let discount: any = 0;

        if (cartCoupon) {
            discount = isRedeem ? loyalPrice : counponPrice;                
        } else {
            discount = (groupOrderNowSlice && groupOrderNowSlice?.id && groupOrderPrice) ? groupOrderPrice : discount;
        }

        dispatch(rootCartTotalDiscount(discount));

        if (workspace) {
            const takeoutOn = workspace?.setting_open_hours?.find((item: any) => item.type === 0)?.active
            const deliveryOn = workspace?.setting_open_hours?.find((item: any) => item.type === 1)?.active
            const groupOrderOn = workspace?.extras?.find((item: any) => item.type === 1)?.active

            if ((groupOrderOn !== true && rootType == 1 && takeoutOn == false && takeoutOn != 'undefined')
                || (rootType == 2 && deliveryOn == false && deliveryOn != 'undefined')
                || (rootType == 3 && groupOrderOn == false && groupOrderOn != 'undefined')) {
                setTriggerShowInvalidCart(true)
                validForm = false;
            }
        }
        if (validForm) {
            // Only show popup if the form is valid
            const result = await checkedCartBeforeNextStep();
            if (result) {
                fetchDataSuggest();
            }
            togglePopup();
        }

        dispatch(changeRootCartTotalPrice(totalPrice))

        if (tokenLoggedInCookie) {
            setTimeout(function () {
                nextStepValidate(validForm);
            }, 1000)
        }

        if (tokenLoggedInCookie && window.location.href.includes('activeStep=1')) {
            const url = window.location.href.replace('activeStep=1', '');
            history.pushState({}, "cart", url);
            router.push(url)
        }
        if (!tokenLoggedInCookie) {
            togglePopupLogin();
        }
    }

    const openSuggest = query.get('openSuggest');
    const fetchDataSuggest = async () => {
        if (categoryId) {
            try {
                if (tokenLoggedInCookie) {
                    try {
                        const res = await api.get(`categories/${categoryId}/suggestion_products?order_by=name&sort_by=asc`, headers);
                        if (res?.data?.data?.data.length > 0) {
                            setIsPopupOpen(true);
                        } else {
                            handleActiveStep2()
                        }

                    } catch (error) {
                        console.log(error);
                    }
                }
            } catch (error) {
                console.error(error);
            }
        } else {
            setSuggestionProduct([]);
            if (tokenLoggedInCookie && !Cookies.get('atStep2')) {
                triggerActiveStep2()
                Cookies.remove('productSuggestion')
            }
        }
    };

    useEffect(() => {
        // for mobile
        const fetchData = async () => {
            if (openSuggest === 'true') {
                if (categoryId) {
                    const result = await checkedCartBeforeNextStep();
                    if (result) {
                        fetchDataSuggest();
                        const url = window.location.href.replace('openSuggest=true', '');
                        router.push(url)
                    }
                } else {
                    handleActiveStep2()
                    const url = window.location.href.replace('openSuggest=true', '');
                    router.push(url)
                }
            }
        };
        fetchData();
    }, [openSuggest, categoryId]);

    useEffect(() => {
        // for desktop 
        if (Cookies.get('productSuggestion') == 'false') {
            fetchDataSuggest();
        }
    }, [Cookies.get('productSuggestion'), categoryId]);

    useEffect(() => {
        // for desktop grouporder
        if (Cookies.get('step2GroupDesk') == 'true') {
            handleActiveStep2()
        }
    }, [Cookies.get('step2GroupDesk')]);

    useEffect(() => {
        if (Cookies.get('activeStep2') == 'true') {
            handleActiveStep2()
            Cookies.remove('activeStep2')
        }
    }, [Cookies.get('activeStep2')]);

    useEffect(() => {
        if (groupOrderNowSlice && groupOrderNowSlice?.id) {
            setIsGroupOrderOn(true)
        } else {
            setIsGroupOrderOn(false)
            handleRemoveGroupOrder()
        }
    }, [pathname, searchParams])

    const cartTotalAfterDiscount = (workspaceDeliveryConditions: any) => {
        let total = 0
        const subTotal = _.toNumber(cart?.reduce((total: any, item: any) => total + item.basePrice * item.productTotal, 0))

        if (cartTotalPriceCoupon) {
            // if have coupon
            total = _.toNumber(cartTotalPriceCoupon);
        } else if (cartTotalPriceGroupOrder) {
            // if have group order
            total = _.toNumber(cartTotalPriceGroupOrder);
        } else if (cartTotalPriceLoyal) {
            total = _.toNumber(cartTotalPriceLoyal);
        } else {
            // if not have coupon and group order
            total = subTotal
        }

        if (rootType == 2 && _.toNumber(workspaceDeliveryConditions?.price) > 0) {
            if (subTotal < _.toNumber(workspaceDeliveryConditions?.free)) {
                total += _.toNumber(workspaceDeliveryConditions?.price)
            }
        }

        return _.toNumber(total).toFixed(2)
    }

    const cartTotalAfterServiceCost = (subTotal: any, total: any) => {
        let subTotalConvert = _.toNumber(subTotal);
        let totalConvert = _.toNumber(total);

        if (rootType != 3 && serviceCostSettingState?.service_cost_set) {
            totalConvert += calculateServiceCost(serviceCostSettingState, subTotalConvert);
        }

        return _.toNumber(totalConvert).toFixed(2)
    }

    const handleAgain = () => {
        dispatch(removeCouponFromCart())
        triggerSetIsShowRedeem(true);
        handleLoyalGroupOrder()
    }

    const hideChangeTypeScreen = async (action: string) => {
        if (!isMobile && action === 'next' && isReadyTakeOut) {
            dispatch(changeType(1))
        }
        Cookies.remove('fromDesk')
        if (action === 'back' && !isDeliveryOrderOpenManual) {
            dispatch(changeRootCartTmp(null))
        }

        if (action === 'back' && rootCartHistory != null) {
            dispatch(rootChangeInCart(rootCartHistory))
            dispatch(changeRootCartHistory(null))
            dispatch(rootCartDeliveryOpen(false))
        }

        if (isDeliveryOrderOpenManual) {
            setIsDeliveryOrderOpenManual(0)
        } else {
            setChangeOrderType(false)
        }

        if (action === 'back' && typeBeforeChange !== null) {
            dispatch(changeType(typeBeforeChange))
        }

        if (!typeBeforeChange && action === 'back') {
            if (!isDeliveryOrderOpenManual) {
                dispatch(manualChangeOrderTypeDesktop(false))
                dispatch(setFlagDesktopChangeType(false));
            }
        }

        dispatch(handleTypeBeforeChange(null))

        if (isOnGroupDesk === true && action === 'back') {
            dispatch(setIsOnGroupDeskData(false))
        }

        if (rootType == 3 && action === 'back') {
            dispatch(setIsOnGroupDeskData(true))
        }

        if (action === 'next' && rootCartTmp != null) {
            dispatch(rootChangeInCart(rootCartTmp))
            dispatch(rootToggleAddToCartSuccess())
            dispatch(changeRootCartTmp(null))
            dispatch(changeRootCartHistory(null))
            dispatch(manualChangeOrderTypeDesktop(false))
            dispatch(rootCartDeliveryOpen(false))
            setIsDeliveryOrderOpenManual(0);
            setChangeOrderType(false)
        }
        if (!isMobile && action === 'next' && groupOrderSelected) {
            if (window.location.href.includes('/category/products')) {
                if (groupOrderSelected && workspaceId) {
                    const groupOrder = groupOrderSelected?.id;
                    try {
                        const groupDetail = await api.get(`/groups/${groupOrder}`, headers);
                        if (groupDetail?.status === 200 && groupDetail?.data?.success === true) {
                            const groupDetailData = groupDetail?.data?.data;
                            dispatch(setGroupOrderData(groupDetailData));
                        }
                    } catch (error) {
                        console.log(error);
                    }
                }
                dispatch(changeType(3))
                dispatch(addGroupOrderSelectedNow(groupOrderSelected))
                dispatch(addGroupOrderSelected(null))
                dispatch(setflagForcusData(false));
                dispatch(addReadyDelivery(false));
                router.push(`/category/products`);
            } else if (window.location.href.includes('/loyalties')) {
                dispatch(addGroupOrderSelectedNow(groupOrderSelected))
            }
            setIsHideChangeTypeScreenComplete(true);
        }
    }

    useEffect(() => {
        if (typeBeforeChange !== null && !rootType) {
            dispatch(changeType(typeBeforeChange))
            dispatch(handleTypeBeforeChange(null))
        }
    }, []);

    const [deliveryAddressTmp, setDeliveryAddressTmp] = useState<any>(null);

    const handleLocation = (data: any) => {
        setDeliveryAddressTmp({
            address: data?.description,
            lat: data?.lat,
            lng: data?.lng,
        });
        //dispatch(rootCartDeliveryAddress(data));
    }

    const [errorDeliveryMessage, setErrorDeliveryMessage] = useState<any>(null);
    const handleSaveDeliveryAddress = () => {
        fetchDeliveryConditions(deliveryAddressTmp);
    }

    const fetchDeliveryConditions = (deliveryAddress: any) => {
        setErrorDeliveryMessage(null);
        api.get(`workspaces/${workspaceId}/settings/delivery_conditions?lat=${deliveryAddress?.lat}&lng=${deliveryAddress?.lng}`, headers)
        .then((res) => {
            const validateDatas = res?.data;
            if (validateDatas?.data && validateDatas?.data.length > 0) {
                dispatch(rootCartDeliveryAddress(deliveryAddress));
                dispatch(rootCartDeliveryConditions(validateDatas?.data[0]));
                dispatch(changeType(2))
                setChangeOrderType(false)
                setDeliveryAddressTmp(null);
                hideChangeTypeScreen('next')
                setIsDeliveryType(true);
            } else {
                setErrorDeliveryMessage(workspace?.name + ' ' + trans('choose-another-location'))
            }
        }).catch((err) => {
            // setErrorDeliveryMessage('Error');
        });
    }

    const nextStepValidate = (valid: any) => {
        if (isFormValid && !productCategoryNotAvailable && ((isPopupOpen && suggestionProduct.length > 0) || suggestionProduct.length == 0)) {
            if (valid) {
                handleActiveStep2()
            }
        }
    }

    useEffect(() => {
        if (groupOrderNowSlice) {
            setIsGroupOrderOn(true)
        } else {
            setIsGroupOrderOn(false)
            setGroupOrderPrice('');
        }
    }, [groupOrderNowSlice])

    useEffect(() => {
        const shouldPerformAction = changeOrderType || cartDeliveryOpen || changeOrderTypeDesktopManual || isDeliveryOrderOpenManual > 0;

        if (shouldPerformAction) {
            dispatch(setFlagDesktopChangeType(true));
        } else {
            dispatch(setFlagDesktopChangeType(false));
        }
    }, [changeOrderType, cartDeliveryOpen, changeOrderTypeDesktopManual, isDeliveryOrderOpenManual]);

    useEffect(() => {
        if (!isMobile && groupOrderNowSlice && groupOrderNowSlice?.id) {
            if (rootType == 1) {
                dispatch(addGroupOrderSelectedNow(null))
                dispatch(setGroupOrderData(null));
            } else if (rootType == 2) {
                dispatch(addGroupOrderSelectedNow(null))
                dispatch(setGroupOrderData(null));
            } else {
                if (isReadyDelivery) {
                    dispatch(addGroupOrderSelectedNow(null))
                    dispatch(setGroupOrderData(null));
                }
            }
        }
        if (isExsitRedeem && !isShowRedeem && origin !== 'desktop') {
            handleAgain()
        }
        if (rootType != 3) {
            if (rootType == 1 || errorMessage == ERROR_TYPE.NOT_FOR_SALE) {
                setErrorMessage(0);
                setProductOptionNotCount([]);
            }
        }
    }, [rootType])

    useEffect(() => {
        dispatch(rootCartNote(note))
    }, [note])

    useEffect(() => {
        if (Cookies.get('oppenedSuggest') == 'true') {
            handleActiveStep2()
        }
    }, [Cookies.get('oppenedSuggest')])

    const refCartHeader = useRef<HTMLDivElement>(null);
    const refCartFooter = useRef<HTMLDivElement>(null);
    const scrolledY = useScrollPosition()

    useEffect(() => {
        if (refCartHeader.current && refCartFooter.current && origin === 'desktop') {
            const footerHeight = refCartFooter.current.clientHeight;
            const cartContent = document.querySelector('.cart-content');

            if (cartContent) {
                let dynamicHeight = 120 + footerHeight;

                if(scrolledY <= 70) {
                    dynamicHeight = dynamicHeight + 70 - scrolledY;
                }

                cartContent.setAttribute('style', `max-height: calc(100vh - ${dynamicHeight}px)`);
            }
        }
    }, [
        refCartHeader.current,
        refCartFooter.current,
        scrolledY,
        origin
    ])

    const [serviceCostSettingState, setServiceCostSettingState] = useState<any>(null);

    useEffect(() => {
        const getServiceCostSetting = async (workspaceId: number) => {
            const settingServiceCost: any = await serviceCostSetting(workspaceId);
            const settingData = settingServiceCost?.data?.data ?? null;
            setServiceCostSettingState(settingData);
        }

        getServiceCostSetting(workspaceId);
    }, [
        workspaceId
    ]);

    const renderCartContent = () => { 
        return (
            <>
                {origin == 'desktop' && (
                    <>
                        { typeNotActiveErrorMessage == true ? (
                            <TypeNotActiveErrorMessage />
                        ) : (
                            (productOptionNotExist.length > 0 && !_.isEmpty(cart)) ? (
                                <>
                                    {(productDeliveryInvalid == 1 || errorMessage == ERROR_TYPE.NOT_DELIVERY) ? (
                                        <div className={`row d-flex ${style.messageDesk} mt-2`}>
                                            <div className={`col-auto ${style.warningDesk}`}>
                                                <svg xmlns="http://www.w3.org/2000/svg" width="21" height="21" viewBox="0 0 21 21" fill="none">
                                                    <path d="M9.00368 3.37756L1.59243 15.7501C1.43962 16.0147 1.35877 16.3147 1.35792 16.6203C1.35706 16.9258 1.43623 17.2263 1.58755 17.4918C1.73887 17.7572 1.95706 17.9785 2.22042 18.1334C2.48378 18.2884 2.78313 18.3717 3.08868 18.3751H17.9112C18.2167 18.3717 18.5161 18.2884 18.7794 18.1334C19.0428 17.9785 19.261 17.7572 19.4123 17.4918C19.5636 17.2263 19.6428 16.9258 19.6419 16.6203C19.6411 16.3147 19.5602 16.0147 19.4074 15.7501L11.9962 3.37756C11.8402 3.1204 11.6206 2.90779 11.3585 2.76023C11.0964 2.61267 10.8007 2.53516 10.4999 2.53516C10.1992 2.53516 9.90347 2.61267 9.64138 2.76023C9.3793 2.90779 9.15966 3.1204 9.00368 3.37756V3.37756Z" stroke="#E03009" strokeWidth="1.5" strokeLinecap="round" strokeLinejoin="round" />
                                                    <path d="M10.5 7.875V11.375" stroke="#E03009" strokeWidth="1.5" strokeLinecap="round" strokeLinejoin="round" />
                                                    <path d="M10.5 14.875H10.5088" stroke="#E03009" strokeWidth="1.5" strokeLinecap="round" strokeLinejoin="round" />
                                                </svg>
                                            </div>
                                            <div className={`col ${style.errorDesk}`}>
                                                <p className={style.errorDeskText}>{trans('cart.delivery_product_invalid')}</p>
                                            </div>
                                        </div>
                                    ) : (
                                        <>
                                            {productCategoryNotAvailable == 1 ? (
                                                <div className={`row d-flex ${style.messageDesk} mt-2`}>
                                                    <div className={`col-auto ${style.warningDesk}`}>
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="21" height="21" viewBox="0 0 21 21" fill="none">
                                                            <path d="M9.00368 3.37756L1.59243 15.7501C1.43962 16.0147 1.35877 16.3147 1.35792 16.6203C1.35706 16.9258 1.43623 17.2263 1.58755 17.4918C1.73887 17.7572 1.95706 17.9785 2.22042 18.1334C2.48378 18.2884 2.78313 18.3717 3.08868 18.3751H17.9112C18.2167 18.3717 18.5161 18.2884 18.7794 18.1334C19.0428 17.9785 19.261 17.7572 19.4123 17.4918C19.5636 17.2263 19.6428 16.9258 19.6419 16.6203C19.6411 16.3147 19.5602 16.0147 19.4074 15.7501L11.9962 3.37756C11.8402 3.1204 11.6206 2.90779 11.3585 2.76023C11.0964 2.61267 10.8007 2.53516 10.4999 2.53516C10.1992 2.53516 9.90347 2.61267 9.64138 2.76023C9.3793 2.90779 9.15966 3.1204 9.00368 3.37756V3.37756Z" stroke="#E03009" strokeWidth="1.5" strokeLinecap="round" strokeLinejoin="round" />
                                                            <path d="M10.5 7.875V11.375" stroke="#E03009" strokeWidth="1.5" strokeLinecap="round" strokeLinejoin="round" />
                                                            <path d="M10.5 14.875H10.5088" stroke="#E03009" strokeWidth="1.5" strokeLinecap="round" strokeLinejoin="round" />
                                                        </svg>
                                                    </div>
                                                    <div className={`col ${style.errorDesk}`}>
                                                        <p className={style.errorDeskText}>{trans('cart.product_not_available')}</p>
                                                    </div>
                                                </div>
                                            ) : (
                                                <div className={`row d-flex ${style.messageDesk} mt-2`}>
                                                    <div className={`col-auto ${style.warningDesk}`}>
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="21" height="21" viewBox="0 0 21 21" fill="none">
                                                            <path d="M9.00368 3.37756L1.59243 15.7501C1.43962 16.0147 1.35877 16.3147 1.35792 16.6203C1.35706 16.9258 1.43623 17.2263 1.58755 17.4918C1.73887 17.7572 1.95706 17.9785 2.22042 18.1334C2.48378 18.2884 2.78313 18.3717 3.08868 18.3751H17.9112C18.2167 18.3717 18.5161 18.2884 18.7794 18.1334C19.0428 17.9785 19.261 17.7572 19.4123 17.4918C19.5636 17.2263 19.6428 16.9258 19.6419 16.6203C19.6411 16.3147 19.5602 16.0147 19.4074 15.7501L11.9962 3.37756C11.8402 3.1204 11.6206 2.90779 11.3585 2.76023C11.0964 2.61267 10.8007 2.53516 10.4999 2.53516C10.1992 2.53516 9.90347 2.61267 9.64138 2.76023C9.3793 2.90779 9.15966 3.1204 9.00368 3.37756V3.37756Z" stroke="#E03009" strokeWidth="1.5" strokeLinecap="round" strokeLinejoin="round" />
                                                            <path d="M10.5 7.875V11.375" stroke="#E03009" strokeWidth="1.5" strokeLinecap="round" strokeLinejoin="round" />
                                                            <path d="M10.5 14.875H10.5088" stroke="#E03009" strokeWidth="1.5" strokeLinecap="round" strokeLinejoin="round" />
                                                        </svg>
                                                    </div>
                                                    <div className={`col ${style.errorDesk}`}>
                                                        <p className="text-center error mt-3">{trans('cart.product_option_not_available')}</p>
                                                    </div>
                                                </div>
                                            )}
                                        </>
                                    )}
                                </>
                            ) : (
                                errorMessage == ERROR_TYPE.NOT_DELIVERY && origin == 'desktop' && (
                                    <div className={`row d-flex ${style.messageDesk} mt-2`}>
                                        <div className={`col-auto ${style.warningDesk}`}>
                                            <svg xmlns="http://www.w3.org/2000/svg" width="21" height="21" viewBox="0 0 21 21" fill="none">
                                                <path d="M9.00368 3.37756L1.59243 15.7501C1.43962 16.0147 1.35877 16.3147 1.35792 16.6203C1.35706 16.9258 1.43623 17.2263 1.58755 17.4918C1.73887 17.7572 1.95706 17.9785 2.22042 18.1334C2.48378 18.2884 2.78313 18.3717 3.08868 18.3751H17.9112C18.2167 18.3717 18.5161 18.2884 18.7794 18.1334C19.0428 17.9785 19.261 17.7572 19.4123 17.4918C19.5636 17.2263 19.6428 16.9258 19.6419 16.6203C19.6411 16.3147 19.5602 16.0147 19.4074 15.7501L11.9962 3.37756C11.8402 3.1204 11.6206 2.90779 11.3585 2.76023C11.0964 2.61267 10.8007 2.53516 10.4999 2.53516C10.1992 2.53516 9.90347 2.61267 9.64138 2.76023C9.3793 2.90779 9.15966 3.1204 9.00368 3.37756V3.37756Z" stroke="#E03009" strokeWidth="1.5" strokeLinecap="round" strokeLinejoin="round" />
                                                <path d="M10.5 7.875V11.375" stroke="#E03009" strokeWidth="1.5" strokeLinecap="round" strokeLinejoin="round" />
                                                <path d="M10.5 14.875H10.5088" stroke="#E03009" strokeWidth="1.5" strokeLinecap="round" strokeLinejoin="round" />
                                            </svg>
                                        </div>
                                        <div className={`col ${style.errorDesk}`}>
                                            <p className={style.errorDeskText}>{trans('cart.delivery_product_invalid')}
                                            </p>
                                        </div>
                                    </div>
                                )
                            )
                        )}
                    </>                    
                )}

                {invalidProductIds && invalidProductIds.length > 0 && origin == 'desktop' && (
                    <div className={`row d-flex ${style.messageDesk} mt-2`}>
                        <div className={`col-auto ${style.warningDesk}`}>
                            <svg xmlns="http://www.w3.org/2000/svg" width="21" height="21" viewBox="0 0 21 21" fill="none">
                                <path d="M9.00368 3.37756L1.59243 15.7501C1.43962 16.0147 1.35877 16.3147 1.35792 16.6203C1.35706 16.9258 1.43623 17.2263 1.58755 17.4918C1.73887 17.7572 1.95706 17.9785 2.22042 18.1334C2.48378 18.2884 2.78313 18.3717 3.08868 18.3751H17.9112C18.2167 18.3717 18.5161 18.2884 18.7794 18.1334C19.0428 17.9785 19.261 17.7572 19.4123 17.4918C19.5636 17.2263 19.6428 16.9258 19.6419 16.6203C19.6411 16.3147 19.5602 16.0147 19.4074 15.7501L11.9962 3.37756C11.8402 3.1204 11.6206 2.90779 11.3585 2.76023C11.0964 2.61267 10.8007 2.53516 10.4999 2.53516C10.1992 2.53516 9.90347 2.61267 9.64138 2.76023C9.3793 2.90779 9.15966 3.1204 9.00368 3.37756V3.37756Z" stroke="#E03009" strokeWidth="1.5" strokeLinecap="round" strokeLinejoin="round" />
                                <path d="M10.5 7.875V11.375" stroke="#E03009" strokeWidth="1.5" strokeLinecap="round" strokeLinejoin="round" />
                                <path d="M10.5 14.875H10.5088" stroke="#E03009" strokeWidth="1.5" strokeLinecap="round" strokeLinejoin="round" />
                            </svg>
                        </div>
                        <div className={`col ${style.errorDesk}`}>
                            <p className={style.errorDeskText}> {trans('unavailable-products')}</p>
                        </div>
                    </div>
                )}

                {errorMessage == ERROR_TYPE.NOT_FOR_SALE && origin === 'desktop' && (
                    <div className={`row d-flex ${style.messageDesk} mt-2`}>
                        <div className={`col-auto ${style.warningDesk}`}>
                            <svg xmlns="http://www.w3.org/2000/svg" width="21" height="21" viewBox="0 0 21 21" fill="none">
                                <path d="M9.00368 3.37756L1.59243 15.7501C1.43962 16.0147 1.35877 16.3147 1.35792 16.6203C1.35706 16.9258 1.43623 17.2263 1.58755 17.4918C1.73887 17.7572 1.95706 17.9785 2.22042 18.1334C2.48378 18.2884 2.78313 18.3717 3.08868 18.3751H17.9112C18.2167 18.3717 18.5161 18.2884 18.7794 18.1334C19.0428 17.9785 19.261 17.7572 19.4123 17.4918C19.5636 17.2263 19.6428 16.9258 19.6419 16.6203C19.6411 16.3147 19.5602 16.0147 19.4074 15.7501L11.9962 3.37756C11.8402 3.1204 11.6206 2.90779 11.3585 2.76023C11.0964 2.61267 10.8007 2.53516 10.4999 2.53516C10.1992 2.53516 9.90347 2.61267 9.64138 2.76023C9.3793 2.90779 9.15966 3.1204 9.00368 3.37756V3.37756Z" stroke="#E03009" strokeWidth="1.5" strokeLinecap="round" strokeLinejoin="round" />
                                <path d="M10.5 7.875V11.375" stroke="#E03009" strokeWidth="1.5" strokeLinecap="round" strokeLinejoin="round" />
                                <path d="M10.5 14.875H10.5088" stroke="#E03009" strokeWidth="1.5" strokeLinecap="round" strokeLinejoin="round" />
                            </svg>
                        </div>
                        <div className={`col ${style.errorDesk}`}>
                            <p className={style.errorDeskText}>{trans('cart.delivery_product_not_sale')}</p>
                        </div>
                    </div>
                )}

                {isExsitRedeem && !isShowRedeem && origin === 'desktop' && (
                    <div className={`${style.messageLoyalDesk} mt-4 row`}>
                        <div className={`${style.warningDeskLoyal}`}>
                            <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <circle cx="10" cy="10" r="9" stroke={color} strokeWidth="2" />
                                <path d="M10.8751 7.244C10.5671 7.244 10.3058 7.13667 10.0911 6.922C9.87644 6.70733 9.76911 6.446 9.76911 6.138C9.76911 5.83 9.87644 5.56867 10.0911 5.354C10.3058 5.13 10.5671 5.018 10.8751 5.018C11.1831 5.018 11.4444 5.13 11.6591 5.354C11.8831 5.56867 11.9951 5.83 11.9951 6.138C11.9951 6.446 11.8831 6.70733 11.6591 6.922C11.4444 7.13667 11.1831 7.244 10.8751 7.244ZM9.92311 15.084C9.47511 15.084 9.11111 14.944 8.83111 14.664C8.56044 14.384 8.42511 13.964 8.42511 13.404C8.42511 13.1707 8.46244 12.8673 8.53711 12.494L9.48911 8H11.5051L10.4971 12.76C10.4598 12.9 10.4411 13.0493 10.4411 13.208C10.4411 13.3947 10.4831 13.53 10.5671 13.614C10.6604 13.6887 10.8098 13.726 11.0151 13.726C11.1831 13.726 11.3324 13.698 11.4631 13.642C11.4258 14.1087 11.2578 14.468 10.9591 14.72C10.6698 14.9627 10.3244 15.084 9.92311 15.084Z" fill={color} />
                            </svg>
                        </div>
                        <div className={`${style.errorDeskLoyal}`} onClick={handleAgain}>
                            <p className={style.errorDeskLoyalText}>{trans('cart.apply_redeem_discount_desk')}</p>
                        </div>
                        <div className={`${style.boxDesk} d-flex`} onClick={handleAgain}>
                            <Loyalty color={color} />
                        </div>
                    </div>
                )}

                {(isVisible && couponError && origin === 'desktop') && (
                    <div className={`row d-flex ${style.messageDesk} mt-2`}>
                        <div className={`col-auto ${style.warningDesk}`}>
                            <svg xmlns="http://www.w3.org/2000/svg" width="21" height="21" viewBox="0 0 21 21" fill="none">
                                <path d="M9.00368 3.37756L1.59243 15.7501C1.43962 16.0147 1.35877 16.3147 1.35792 16.6203C1.35706 16.9258 1.43623 17.2263 1.58755 17.4918C1.73887 17.7572 1.95706 17.9785 2.22042 18.1334C2.48378 18.2884 2.78313 18.3717 3.08868 18.3751H17.9112C18.2167 18.3717 18.5161 18.2884 18.7794 18.1334C19.0428 17.9785 19.261 17.7572 19.4123 17.4918C19.5636 17.2263 19.6428 16.9258 19.6419 16.6203C19.6411 16.3147 19.5602 16.0147 19.4074 15.7501L11.9962 3.37756C11.8402 3.1204 11.6206 2.90779 11.3585 2.76023C11.0964 2.61267 10.8007 2.53516 10.4999 2.53516C10.1992 2.53516 9.90347 2.61267 9.64138 2.76023C9.3793 2.90779 9.15966 3.1204 9.00368 3.37756V3.37756Z" stroke="#E03009" strokeWidth="1.5" strokeLinecap="round" strokeLinejoin="round" />
                                <path d="M10.5 7.875V11.375" stroke="#E03009" strokeWidth="1.5" strokeLinecap="round" strokeLinejoin="round" />
                                <path d="M10.5 14.875H10.5088" stroke="#E03009" strokeWidth="1.5" strokeLinecap="round" strokeLinejoin="round" />
                            </svg>
                        </div>
                        <div className={`col ${style.errorDesk}`}>
                            <p className={style.errorDeskText}>{couponError}</p>
                        </div>
                    </div>
                )}

                <div className="row">
                    <div className={`col-sm-12 col-12 ${style['product-list']}`}>
                        {cart?.map((item: any, index: any) => (
                            <div className={`row ${style['product-item-area']}`} key={`product-item-${index}-${item.productId}`}>
                                <div className={`col-sm-12 col-12 ${style['product-item']}`}>
                                    <div className="row">
                                        <div className="col-sm-9 col-9 d-flex">
                                            <div className={`${style['number-of-product']} ${item?.optionItemsStore?.length > 0 ? style['number-of-product-options'] : ''}`}>
                                                <svg className={style.increase} onClick={() => changeNumberProduct(index, 1)} width="17" height="17" viewBox="0 0 17 17" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M12.5358 10.2041L8.53577 6.12244L4.53577 10.2041" stroke="#C4C4C4" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" />
                                                </svg>
                                                <div className={`${_.includes(productOptionNotExist, index) || (invalidProductIds && invalidProductIds.length > 0 && invalidProductIds.includes(item?.productId.toString())) ? 'error-color' : ''} ${_.includes(productOptionNotCount, index) ? 'error-color' : ''} ${style.number}`}>
                                                    <input type="number"
                                                        min={1}
                                                        ref={(element) => inputProductNumberRefs.current[index] = element}
                                                        className={`${style.number} ${_.includes(productOptionNotExist, index) || (invalidProductIds && invalidProductIds.length > 0 && invalidProductIds.includes(item?.productId.toString())) ? 'error-color' : ''} ${_.includes(productOptionNotCount, index) ? 'error-color' : ''}`}
                                                        defaultValue={item.productTotal}
                                                        key={item.productTotal}
                                                        // onChange={(e) => changeNumberProduct(index, e.target.value ? parseInt(e.target.value) : 0, true)}
                                                        onBlur={(e) => changeNumberProduct(index, e.target.value ? parseInt(e.target.value) : item.productTotal, true)}
                                                    />
                                                </div>
                                                <svg className={style.decrease} onClick={() => changeNumberProduct(index, -1)} xmlns="http://www.w3.org/2000/svg" width="17" height="17" viewBox="0 0 17 17" fill="none">
                                                    <path d="M12.5358 6.79593L8.53577 10.8776L4.53577 6.79593" stroke="#C4C4C4" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" />
                                                </svg>
                                            </div>
                                            <div className={style['product-name-options']}>
                                                <div className={`${style['product-name']} ${_.includes(productOptionNotExist, index) || (invalidProductIds && invalidProductIds.length > 0 && invalidProductIds.includes(item?.productId.toString())) ? 'error-color' : ''} ${_.includes(productOptionNotCount, index) ? 'error-color' : ''}`}>
                                                    {item.product?.data?.name}
                                                </div>
                                                <div className={`${style['product-options']} ${_.includes(productOptionNotExist, index) || (invalidProductIds && invalidProductIds.length > 0 && invalidProductIds.includes(item?.productId.toString())) ? 'error-color' : ''} ${_.includes(productOptionNotCount, index) ? 'error-color' : ''}`}>
                                                    {_.map(item?.optionItemsStore, (optionItemsStoreValue: any, optionItemsStoreKey: number) => (
                                                        <div className={style.option} key={`product-option-${index}-${optionItemsStoreKey}`}>
                                                            {(_.find(optionItemsStoreValue?.optionItems, { master: true })) ? (
                                                                <div className={style.option}>
                                                                    <span className={style['option-item']}>
                                                                        {trans('with-out') + _.find(optionItemsStoreValue?.optionItems, { master: true }).name}
                                                                    </span>
                                                                </div>
                                                            ) : (
                                                                <>
                                                                    {optionItemsStoreValue?.optionItems?.map((optionItem: any, optionItemIndex: number) => (
                                                                        <span className={style['option-item']} key={`product-option-item-${optionItemsStoreValue.optionId}-${optionItemIndex}-${optionItem.id}`}>
                                                                            {_.find(item?.productOptions?.data, { id: _.toNumber(optionItemsStoreValue.optionId) }) && _.find(item?.productOptions?.data, { id: _.toNumber(optionItemsStoreValue.optionId) })?.is_ingredient_deletion == true ? trans('with-out') + optionItem.name : optionItem.name}
                                                                            {optionItemIndex < optionItemsStoreValue.optionItems.length - 1 ? ', ' : ''}
                                                                        </span>
                                                                    ))}
                                                                </>
                                                            )}
                                                        </div>
                                                    ))}
                                                </div>
                                            </div>
                                        </div>
                                        <div className="col-sm-3 col-3">
                                            <div className="float-end">
                                                <div className={style['product-price']}>
                                                    <div className={`${_.includes(productOptionNotExist, index) || (invalidProductIds && invalidProductIds.length > 0 && invalidProductIds.includes(item?.productId.toString())) ? 'error-color' : ''} ${_.includes(productOptionNotCount, index) ? 'error-color' : ''} ${style.price}`} style={{ color: color }}>
                                                        {currency}
                                                        {_.round(item.basePrice * item.productTotal, 2).toFixed(2)}
                                                    </div>
                                                    <div className={`${style.remove} res-mobile`} data-bs-toggle="modal" data-bs-target="#delete-modal" onClick={() => { setRemoveIndex(index); setRemoveProductId(item?.productId.toString()) }}>
                                                        <svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                            <path d="M7.3691 12.6667C10.5908 12.6667 13.2024 10.055 13.2024 6.83333C13.2024 3.61167 10.5908 1 7.3691 1C4.14744 1 1.53577 3.61167 1.53577 6.83333C1.53577 10.055 4.14744 12.6667 7.3691 12.6667Z" stroke="#404040" strokeWidth="1.5" strokeLinecap="round" strokeLinejoin="round" />
                                                            <path d="M9.11926 5.08313L5.61926 8.58313" stroke="#313131" strokeWidth="1.5" strokeLinecap="round" strokeLinejoin="round" />
                                                            <path d="M5.61926 5.08313L9.11926 8.58313" stroke="#404040" strokeWidth="1.5" strokeLinecap="round" strokeLinejoin="round" />
                                                        </svg>
                                                    </div>
                                                    <div className={`${style.remove} res-desktop`} onClick={() => { calculateGroupOrderPrice(); removeProduct(index); removeInvalidProductId(item?.productId.toString()); }}>
                                                        <svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                            <path d="M7.3691 12.6667C10.5908 12.6667 13.2024 10.055 13.2024 6.83333C13.2024 3.61167 10.5908 1 7.3691 1C4.14744 1 1.53577 3.61167 1.53577 6.83333C1.53577 10.055 4.14744 12.6667 7.3691 12.6667Z" stroke="#404040" strokeWidth="1.5" strokeLinecap="round" strokeLinejoin="round" />
                                                            <path d="M9.11926 5.08313L5.61926 8.58313" stroke="#313131" strokeWidth="1.5" strokeLinecap="round" strokeLinejoin="round" />
                                                            <path d="M5.61926 5.08313L9.11926 8.58313" stroke="#404040" strokeWidth="1.5" strokeLinecap="round" strokeLinejoin="round" />
                                                        </svg>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div className="col-sm-12 col-12">
                                    <div className="line-gray"></div>
                                </div>
                            </div>
                        ))}
                    </div>
                </div>

                {((counponPrice || groupOrderPrice || loyalPrice) 
                    || (rootType == 2 && _.toNumber(workspaceDeliveryConditions?.price) > 0 && cart?.reduce((total: any, item: any) => total + item.basePrice * item.productTotal, 0) < _.toNumber(workspaceDeliveryConditions?.free))
                    || (rootType != 3 && serviceCostSettingState?.service_cost_set)
                ) && (
                    <div className={style.cartContain}>
                        <div className="row">
                            <div className={`col-sm-12 col-12 mb-2 ${style['total-text']}`}>
                                <div className="float-start">
                                    {trans('cart.subtotal')}
                                </div>
                                <div className="float-end pdr-19">
                                    {currency}
                                    {cart?.reduce((total: any, item: any) => total + item.basePrice * item.productTotal, 0).toFixed(2)}
                                </div>
                            </div>
                        </div>

                        {(rootType == 2 && _.toNumber(workspaceDeliveryConditions?.price) > 0 && cart?.reduce((total: any, item: any) => total + item.basePrice * item.productTotal, 0) < _.toNumber(workspaceDeliveryConditions?.free)) && (
                            <div className="row">
                                <div className={`col-sm-12 col-12 mt-2 mb-2 ${style['total-text']}`}>
                                    <div className="float-start">
                                        {trans('cart.delivery_costs')}
                                    </div>
                                    <div className="float-end pdr-19">
                                        {currency}
                                        {workspaceDeliveryConditions.price}
                                    </div>
                                </div>
                            </div>
                        )}

                        {(rootType != 3 && serviceCostSettingState?.service_cost_set) && (
                            <div className="row">
                                <div className={`col-sm-12 col-12 mt-2 mb-2 ${style['total-text']}`}>
                                    <div className="float-start">
                                        {trans('cart.service_cost')}
                                    </div>
                                    <div className="float-end pdr-19">
                                        {currency}
                                        {_.toNumber(calculateServiceCost(serviceCostSettingState, cart?.reduce((total: any, item: any) => total + item.basePrice * item.productTotal, 0))).toFixed(2)}
                                    </div>
                                </div>
                            </div>
                        )}

                        {(!isRedeem && counponPrice) && (
                            <div className="row">
                                <div className={`col-sm-12 col-12 mt-2 mb-2 ${style['total-text']}`}>
                                    <div className="float-start">
                                        {isRedeem ? trans('cart.redeem_discount') : trans('cart.coupon_discount')}
                                    </div>
                                    <div className="float-end">
                                        -{currency}
                                        {counponPrice}
                                        <svg className={style['remove-coupon']} width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg" onClick={handleRemoveCoupon}>
                                            <g clipPath="url(#clip0_4290_4189)">
                                                <path d="M7.23333 13.0667C10.455 13.0667 13.0667 10.455 13.0667 7.23336C13.0667 4.0117 10.455 1.40002 7.23333 1.40002C4.01167 1.40002 1.39999 4.0117 1.39999 7.23336C1.39999 10.455 4.01167 13.0667 7.23333 13.0667Z" stroke="#888888" strokeLinecap="round" strokeLinejoin="round" />
                                                <path d="M8.98334 5.48309L5.48334 8.98309" stroke="#888888" strokeLinecap="round" strokeLinejoin="round" />
                                                <path d="M5.48334 5.48309L8.98334 8.98309" stroke="#888888" strokeLinecap="round" strokeLinejoin="round" />
                                            </g>
                                            <defs>
                                                <clipPath id="clip0_4290_4189">
                                                    <rect width="14" height="14" fill="white" />
                                                </clipPath>
                                            </defs>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                        )}

                        {isRedeem && loyalPrice && origin !== 'desktop' && (
                            <div className="row">
                                <div className={`col-sm-12 col-12 mt-2 mb-2 ${style['total-text']}`}>
                                    <div className="float-start">
                                        {trans('cart.redeem_discount')}
                                    </div>
                                    <div className="float-end">
                                        -{currency}
                                        {loyalPrice}
                                        <svg className={style['remove-coupon']} width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg" onClick={handleRemoveLoyal}>
                                            <g clipPath="url(#clip0_4290_4189)">
                                                <path d="M7.23333 13.0667C10.455 13.0667 13.0667 10.455 13.0667 7.23336C13.0667 4.0117 10.455 1.40002 7.23333 1.40002C4.01167 1.40002 1.39999 4.0117 1.39999 7.23336C1.39999 10.455 4.01167 13.0667 7.23333 13.0667Z" stroke="#888888" strokeLinecap="round" strokeLinejoin="round" />
                                                <path d="M8.98334 5.48309L5.48334 8.98309" stroke="#888888" strokeLinecap="round" strokeLinejoin="round" />
                                                <path d="M5.48334 5.48309L8.98334 8.98309" stroke="#888888" strokeLinecap="round" strokeLinejoin="round" />
                                            </g>
                                            <defs>
                                                <clipPath id="clip0_4290_4189">
                                                    <rect width="14" height="14" fill="white" />
                                                </clipPath>
                                            </defs>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                        )}

                        {isExsitRedeem && isShowRedeem && origin === 'desktop' && (
                            <div className="row">
                                <div className={`col-sm-12 col-12 mt-2 mb-2 ${style['total-text']}`}>
                                    <div className="float-start">
                                        {trans('cart.redeem_discount')}
                                    </div>
                                    <div className="float-end">
                                        -{currency}
                                        {loyalPrice}
                                        <svg className={style['remove-coupon']} width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg" onClick={handleRemoveLoyal}>
                                            <g clipPath="url(#clip0_4290_4189)">
                                                <path d="M7.23333 13.0667C10.455 13.0667 13.0667 10.455 13.0667 7.23336C13.0667 4.0117 10.455 1.40002 7.23333 1.40002C4.01167 1.40002 1.39999 4.0117 1.39999 7.23336C1.39999 10.455 4.01167 13.0667 7.23333 13.0667Z" stroke="#888888" strokeLinecap="round" strokeLinejoin="round" />
                                                <path d="M8.98334 5.48309L5.48334 8.98309" stroke="#888888" strokeLinecap="round" strokeLinejoin="round" />
                                                <path d="M5.48334 5.48309L8.98334 8.98309" stroke="#888888" strokeLinecap="round" strokeLinejoin="round" />
                                            </g>
                                            <defs>
                                                <clipPath id="clip0_4290_4189">
                                                    <rect width="14" height="14" fill="white" />
                                                </clipPath>
                                            </defs>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                        )}

                        {(!isRedeem && groupOrderPrice) && origin !== 'desktop' && (
                            <div className="row">
                                <div className={`col-sm-12 col-12 mt-2 mb-2 ${style['total-text']}`}>
                                    <div className="float-start">
                                        {trans('cart.group_discount')}
                                    </div>
                                    <div className="float-end pdr-19">
                                        -{currency}
                                        {groupOrderPrice}
                                    </div>
                                </div>
                            </div>
                        )}

                        {((isExsitRedeem && !isShowRedeem) || (!isExsitRedeem)) && groupOrderPrice && origin === 'desktop' && (
                            <div className="row">
                                <div className={`col-sm-12 col-12 mt-2 mb-2 ${style['total-text']}`}>
                                    <div className="float-start">
                                        {trans('cart.group_discount')}
                                    </div>
                                    <div className="float-end pdr-19">
                                        -{currency}
                                        {groupOrderPrice}
                                    </div>
                                </div>
                            </div>
                        )}

                        <div className="line mb-2"></div>
                    </div>
                )}

                <div className={`${(cartTotalPriceCoupon && counponPrice || (cartTotalPriceGroupOrder && groupOrderPrice) || ((cartTotalPriceLoyal && loyalPrice))) ? style.totaling : ''} row`}>
                    <div className="col-sm-12 col-12">
                        <div className={`float-end pb-2 pdr-19 ${style['total-price']}`}>
                            <span className="me-3 text-uppercase">{trans('cart.total')}</span>
                            <span style={origin == 'desktop' ? { color: color } : {}}>
                                {currency}
                                {cartTotalAfterServiceCost(cart?.reduce((total: any, item: any) => total + item.basePrice * item.productTotal, 0), cartTotalAfterDiscount(workspaceDeliveryConditions))}
                            </span>
                        </div>
                    </div>
                </div>

                {rootType == 2 && workspaceDeliveryConditions != null && origin === 'desktop' && (
                    <>
                        <div className={`${style.messageDeleveryDesk} mt-2 row`}>
                            <div className={`${style.warningDeskLoyal}`}>
                                <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <circle cx="10" cy="10" r="9" stroke={color} strokeWidth="2" />
                                    <path d="M10.8751 7.244C10.5671 7.244 10.3058 7.13667 10.0911 6.922C9.87644 6.70733 9.76911 6.446 9.76911 6.138C9.76911 5.83 9.87644 5.56867 10.0911 5.354C10.3058 5.13 10.5671 5.018 10.8751 5.018C11.1831 5.018 11.4444 5.13 11.6591 5.354C11.8831 5.56867 11.9951 5.83 11.9951 6.138C11.9951 6.446 11.8831 6.70733 11.6591 6.922C11.4444 7.13667 11.1831 7.244 10.8751 7.244ZM9.92311 15.084C9.47511 15.084 9.11111 14.944 8.83111 14.664C8.56044 14.384 8.42511 13.964 8.42511 13.404C8.42511 13.1707 8.46244 12.8673 8.53711 12.494L9.48911 8H11.5051L10.4971 12.76C10.4598 12.9 10.4411 13.0493 10.4411 13.208C10.4411 13.3947 10.4831 13.53 10.5671 13.614C10.6604 13.6887 10.8098 13.726 11.0151 13.726C11.1831 13.726 11.3324 13.698 11.4631 13.642C11.4258 14.1087 11.2578 14.468 10.9591 14.72C10.6698 14.9627 10.3244 15.084 9.92311 15.084Z" fill={color} />
                                </svg>
                            </div>
                            <div className={`${style.errorDeskDelevery}`} onClick={handleAgain}>
                                <p className={style.errorDeskDeleveryText}>
                                    {trans('cart.delivery_free_from')} <b>{currency}
                                        {_.toNumber(workspaceDeliveryConditions?.free).toFixed(2)}</b>
                                </p>
                            </div>
                        </div>
                        <div className={`${style.messageDeleveryDesk} mt-2 row`}>
                            <div className={`${style.warningDeskLoyal}`}>
                                <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <circle cx="10" cy="10" r="9" stroke={color} strokeWidth="2" />
                                    <path d="M10.8751 7.244C10.5671 7.244 10.3058 7.13667 10.0911 6.922C9.87644 6.70733 9.76911 6.446 9.76911 6.138C9.76911 5.83 9.87644 5.56867 10.0911 5.354C10.3058 5.13 10.5671 5.018 10.8751 5.018C11.1831 5.018 11.4444 5.13 11.6591 5.354C11.8831 5.56867 11.9951 5.83 11.9951 6.138C11.9951 6.446 11.8831 6.70733 11.6591 6.922C11.4444 7.13667 11.1831 7.244 10.8751 7.244ZM9.92311 15.084C9.47511 15.084 9.11111 14.944 8.83111 14.664C8.56044 14.384 8.42511 13.964 8.42511 13.404C8.42511 13.1707 8.46244 12.8673 8.53711 12.494L9.48911 8H11.5051L10.4971 12.76C10.4598 12.9 10.4411 13.0493 10.4411 13.208C10.4411 13.3947 10.4831 13.53 10.5671 13.614C10.6604 13.6887 10.8098 13.726 11.0151 13.726C11.1831 13.726 11.3324 13.698 11.4631 13.642C11.4258 14.1087 11.2578 14.468 10.9591 14.72C10.6698 14.9627 10.3244 15.084 9.92311 15.084Z" fill={color} />
                                </svg>
                            </div>
                            <div className={`${style.errorDeskDelevery}`} onClick={handleAgain}>
                                <p className={`${style.errorDeskDeleveryText}`}>
                                    {trans('cart.delivery_minimum-desk', {
                                        workspaceName: workspace?.name,
                                        price:
                                            <strong className="font-bold">
                                                {currency + _.toNumber(workspaceDeliveryConditions.price_min).toFixed(2)}
                                            </strong>
                                    })}
                                </p>
                            </div>
                        </div>
                    </>
                )}

                {rootType != 3 && serviceCostSettingState?.service_cost_set  && origin === 'desktop' && (
                    <div className={`${style.messageDeleveryDesk} mt-2 row`}>
                        <div className={`${style.warningDeskLoyal}`}>
                            <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <circle cx="10" cy="10" r="9" stroke={color} strokeWidth="2" />
                                <path d="M10.8751 7.244C10.5671 7.244 10.3058 7.13667 10.0911 6.922C9.87644 6.70733 9.76911 6.446 9.76911 6.138C9.76911 5.83 9.87644 5.56867 10.0911 5.354C10.3058 5.13 10.5671 5.018 10.8751 5.018C11.1831 5.018 11.4444 5.13 11.6591 5.354C11.8831 5.56867 11.9951 5.83 11.9951 6.138C11.9951 6.446 11.8831 6.70733 11.6591 6.922C11.4444 7.13667 11.1831 7.244 10.8751 7.244ZM9.92311 15.084C9.47511 15.084 9.11111 14.944 8.83111 14.664C8.56044 14.384 8.42511 13.964 8.42511 13.404C8.42511 13.1707 8.46244 12.8673 8.53711 12.494L9.48911 8H11.5051L10.4971 12.76C10.4598 12.9 10.4411 13.0493 10.4411 13.208C10.4411 13.3947 10.4831 13.53 10.5671 13.614C10.6604 13.6887 10.8098 13.726 11.0151 13.726C11.1831 13.726 11.3324 13.698 11.4631 13.642C11.4258 14.1087 11.2578 14.468 10.9591 14.72C10.6698 14.9627 10.3244 15.084 9.92311 15.084Z" fill={color} />
                            </svg>
                        </div>
                        <div className={`${style.errorDeskDelevery}`}>
                            <p className={style.errorDeskDeleveryText}>
                                { serviceCostSettingState?.service_cost_always_charge ? 
                                    trans('cart.service_cost_always_charge_on', {
                                        workspaceName: workspace?.name,
                                        cost: <strong className="font-bold">{currency}{_.toNumber(serviceCostSettingState?.service_cost).toFixed(2)}</strong>
                                    }) 
                                    : 
                                    trans('cart.service_cost_always_charge_off', {
                                        workspaceName: workspace?.name,
                                        cost: <strong className="font-bold">{currency}{_.toNumber(serviceCostSettingState?.service_cost).toFixed(2)}</strong>,
                                        price: <strong className="font-bold">{currency}{_.toNumber(serviceCostSettingState?.service_cost_amount).toFixed(2)}</strong>
                                    })
                                }
                            </p>
                        </div>
                    </div>
                )}

                <div className="row mt-2">
                    <div className="col-sm-12 col-12">
                        {!cartTotalPriceCoupon && (
                            <Formik initialValues={{ coupon: '' }} onSubmit={handleSubmitCoupon}>
                                <Form>
                                    <div className="row mb-2 mt-2">
                                        <div className={`col-sm-12 col-12 ${style['coupon-form']}`} >
                                            <Field className={`input-text me-3 ${style['flex-2']} ${couponError ? style.invalid : ''} `} type="text" name="coupon" placeholder={trans('cart.coupon_code')}
                                                onInput={(e: any) => {
                                                    const uppercaseValue = ("" + e.target.value).toUpperCase();
                                                    e.target.value = uppercaseValue;
                                                    setCurrentInput(uppercaseValue);
                                                    setCouponError('');
                                                }}
                                                onKeyUp={handleDisable}
                                            />
                                            <button className={`text-uppercase itr-btn-primary ${style['flex-1']} ${isDisabled ? style.disabled + ' disabled' : ''}`}
                                                type="submit"
                                                disabled={isDisabled}
                                                style={origin == 'desktop' ? { backgroundColor: color } : {}}>
                                                {trans('cart.apply_coupon')}
                                            </button>
                                        </div>
                                    </div>
                                </Form>
                            </Formik>
                        )}

                        {couponError && origin !== 'desktop' && (
                            <p className={`${couponError ? style.invalidText : ''}`}>{couponError}</p>
                        )}
                    </div>
                </div>
                <div className={`${style.textareaField} row note-textarea`}>
                    <div className="col-sm-12 col-12">
                        <TextareaAutosize
                            className="textarea autosize"
                            rows={1}
                            name="note"
                            defaultValue={defaultNote}
                            value={note}
                            onChange={(event) => setNote(event.target.value.substring(0, 100))}
                            placeholder={trans('cart.comment') + '...'}
                        />
                    </div>
                </div>
            </>
        )
    }
    
    useEffect(() => {
        const totalPriceNeedToPay = cartTotalAfterServiceCost(cart?.reduce((total: any, item: any) => total + item.basePrice * item.productTotal, 0), cartTotalAfterDiscount(workspaceDeliveryConditions));
        dispatch(addCartTotalPriceNeedToPay(totalPriceNeedToPay));
    }, [
        cart,
        workspaceDeliveryConditions,
        serviceCostSettingState,
        workspaceId,
        rootType,
        cartTotalPriceCoupon,
        counponPrice,
        groupOrderPrice,
        cartTotalPriceLoyal,
        loyalPrice
    ]);    

    return (
        <>
            {/* case mobile or desktop default screen, show default or when back from change type screen */}
            {(changeOrderType === false || origin !== 'desktop') && !isDeliveryOrderOpenManual && !changeOrderTypeDesktopManual && !cartDeliveryOpen && (
                <>
                    {rootType == 2 && workspaceDeliveryConditions != null && origin !== 'desktop' && (
                        <div className={`${style['delivery-fee']} delivery-fee text-center`}>
                            {trans('cart.delivery_free_from')} {currency}
                            {_.toNumber(workspaceDeliveryConditions?.free).toFixed(2)}
                        </div>
                    )}

                    <div className={`${style['cart-wrapper']} cart-wrapper`}>
                        {!_.isEmpty(cart) && (
                            <>
                                <div ref={refCartHeader} className="cart-navigation cart-header">
                                    {(!isGroupOrderOn) ? (
                                        <>
                                            {(takeoutOn || deliveryOn) && rootType != 3 && (
                                                <div className={`${style['cart-types']} cart-types d-flex`}>
                                                    {takeoutOn && (
                                                        <div onClick={() => onClickChangeType(1)}
                                                            className={`${style['cart-type-item']} ${rootType == 1 ? style['active'] : ''}`}
                                                            style={origin == 'desktop' ? (rootType == 1 ? { backgroundColor: color, borderColor: color } : { color: color, borderColor: color }) : {}}>
                                                            {trans('types.pickup')}
                                                        </div>
                                                    )}
                                                    {deliveryOn && (
                                                        <div onClick={() => {
                                                            onClickChangeType(2)
                                                        }}
                                                            className={`${style['cart-type-item']} ${rootType == 2 ? style['active'] : ''}`}
                                                            style={origin == 'desktop' ? (rootType == 2 ? { backgroundColor: color, borderColor: color } : { color: color, borderColor: color }) : {}}>
                                                            {trans('types.delivery')}
                                                        </div>
                                                    )}
                                                </div>
                                            )}

                                            {groupOrderOn && (
                                                <>
                                                    {origin !== 'desktop' ? (
                                                        <div className="row">
                                                            <div className="col-sm-12 col-12">
                                                                <div className={style.changing}
                                                                    style={{ marginTop: '-4px' }}
                                                                    onClick={toggleOrderType}>
                                                                    <span className={`${style['change-ordertype']}`}>
                                                                        {trans('change-ordertype')}
                                                                    </span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    ) : (
                                                        <div className="row">
                                                            <div className="col-sm-12 col-12">
                                                                <div className={style.changing}
                                                                    style={{ marginTop: '-4px' }}
                                                                    onClick={toggleOrderType}>
                                                                    <span className={`${style['change-ordertype-desk']} text-uppercase`}>
                                                                        {trans('change-ordertype-desk')}
                                                                    </span>
                                                                    <svg xmlns="http://www.w3.org/2000/svg" width="15" height="19" viewBox="0 0 15 19" fill="none" className='ms-2'>
                                                                        <path d="M10.917 1.70801L13.7503 4.54134L10.917 7.37467" stroke={color} strokeWidth="1.5" strokeLinecap="round" strokeLinejoin="round" />
                                                                        <path d="M1 8.79102V7.37435C1 6.6229 1.29851 5.90223 1.82986 5.37088C2.36122 4.83953 3.08189 4.54102 3.83333 4.54102H13.75" stroke={color} strokeWidth="1.5" strokeLinecap="round" strokeLinejoin="round" />
                                                                        <path d="M3.83333 17.2917L1 14.4583L3.83333 11.625" stroke={color} strokeWidth="1.5" strokeLinecap="round" strokeLinejoin="round" />
                                                                        <path d="M13.75 10.208V11.6247C13.75 12.3761 13.4515 13.0968 12.9201 13.6281C12.3888 14.1595 11.6681 14.458 10.9167 14.458H1" stroke={color} strokeWidth="1.5" strokeLinecap="round" strokeLinejoin="round" />
                                                                    </svg>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    )}
                                                </>
                                            )}
                                        </>
                                    ) : (
                                        groupOrderNowSlice && groupOrderNowSlice.id && groupOrderOn && (
                                            <>
                                                <h1 className={`${style['group-order']}`} style={{ color: color ? color : 'black' }}>
                                                    {trans('group-ordering')} {groupName || groupOrderSlice?.name || groupOrderNowSlice?.group?.name}
                                                </h1>
                                                <div className="row">
                                                    <div className="col-sm-12 col-12">
                                                        <div className={style.changing} style={{ marginTop: '-4px' }} onClick={toggleOrderType}>
                                                            {origin !== 'desktop' ? (
                                                                <span className={`${style['change-ordertype']}`}>
                                                                    {trans('change-ordertype')}
                                                                </span>
                                                            ) : (
                                                                <>
                                                                    <span className={`${style['change-ordertype-desk']} text-uppercase`}>
                                                                        {trans('change-ordertype-desk')}
                                                                    </span>
                                                                    <svg xmlns="http://www.w3.org/2000/svg" width="15" height="19" viewBox="0 0 15 19" fill="none" className='ms-2'>
                                                                        <path d="M10.917 1.70801L13.7503 4.54134L10.917 7.37467" stroke={color} strokeWidth="1.5" strokeLinecap="round" strokeLinejoin="round" />
                                                                        <path d="M1 8.79102V7.37435C1 6.6229 1.29851 5.90223 1.82986 5.37088C2.36122 4.83953 3.08189 4.54102 3.83333 4.54102H13.75" stroke={color} strokeWidth="1.5" strokeLinecap="round" strokeLinejoin="round" />
                                                                        <path d="M3.83333 17.2917L1 14.4583L3.83333 11.625" stroke={color} strokeWidth="1.5" strokeLinecap="round" strokeLinejoin="round" />
                                                                        <path d="M13.75 10.208V11.6247C13.75 12.3761 13.4515 13.0968 12.9201 13.6281C12.3888 14.1595 11.6681 14.458 10.9167 14.458H1" stroke={color} strokeWidth="1.5" strokeLinecap="round" strokeLinejoin="round" />
                                                                    </svg>
                                                                </>
                                                            )}
                                                        </div>
                                                    </div>
                                                </div>
                                            </>
                                        )
                                    )}
                                </div>

                                { origin === 'desktop' && from === 'product_list' ? (
                                    <CustomScrollbar className="cart-content">
                                        {renderCartContent()}
                                    </CustomScrollbar>       
                                ) : renderCartContent()}
                                                         
                            </>
                        )}

                        <div ref={refCartFooter} className={`${style.nextButtonFixed} cart-navigation cart-footer`}>
                            <div className={`${style.goNext} text-center`}>
                                {(productOptionNotCount.length == 0 && (rootType != 2 || (rootType == 2 && workspaceDeliveryConditions != null && cart?.reduce((total: any, item: any) => total + item.basePrice * item.productTotal, 0).toFixed(2) >= _.toNumber((workspaceDeliveryConditions?.price_min ?? 0)))))
                                    && (productOptionNotExist.length == 0 && (!invalidProductIds || invalidProductIds.length == 0) && !_.isEmpty(cart)) ? (
                                    <button className="itr-btn-primary-bold text-uppercase"
                                        type="button"
                                        ref={buttonNextRef}
                                        style={origin == 'desktop' ? { backgroundColor: color, height: '50px' } : {}}
                                        onClick={() => { submitStep1(_.toNumber(cartTotalPriceCoupon ? cartTotalPriceCoupon : _.toNumber(cart?.reduce((total: any, item: any) => total + item.basePrice * item.productTotal, 0)).toFixed(2))); }}>
                                        {trans('cart.further')}
                                    </button>
                                ) : (
                                    <button className={`itr-btn-primary-bold text-uppercase ${origin !== 'desktop' ? 'btn-disabled' : ''}`}
                                        type="button"
                                        disabled
                                        style={origin == 'desktop' ? { backgroundColor: color, opacity: '0.35' } : { opacity: '0.35' }}>
                                        {trans('cart.further')}
                                    </button>
                                )}
                            </div>

                            {
                                origin != 'desktop' &&
                                    (productOptionNotExist.length > 0 && !_.isEmpty(cart)) ? (
                                    <>
                                        {productDeliveryInvalid == 1 || errorMessage == ERROR_TYPE.NOT_DELIVERY ? (
                                            <p className="text-center error mt-3">{trans('cart.delivery_product_invalid')}</p>
                                        ) : (
                                            <>
                                                {productCategoryNotAvailable == 1 ? (
                                                    <p className="text-center error mt-3">{trans('cart.product_not_available')}</p>
                                                ) : (
                                                    <p className="text-center error mt-3">{trans('cart.product_option_not_available')}</p>
                                                )}
                                            </>
                                        )}
                                    </>
                                ) : (
                                    errorMessage == ERROR_TYPE.NOT_DELIVERY && isMobile && origin != 'desktop' && (
                                        <div className="pt-2 text-center itr-btn-dangerous">
                                            {trans('cart.delivery_product_invalid')}
                                        </div>
                                    )
                                )
                            }

                            {
                                invalidProductIds && invalidProductIds.length > 0 && origin != 'desktop' && (
                                    <div className="col-sm-12 col-12 pt-2 text-center itr-btn-dangerous">
                                        {trans('unavailable-products')}
                                    </div>
                                )
                            }

                            {
                                errorMessage == ERROR_TYPE.NOT_FOR_SALE && isMobile && origin != 'desktop' && (
                                    <div className="pt-2 text-center itr-btn-dangerous">
                                        {trans('cart.delivery_product_not_sale')}
                                    </div>
                                )
                            }

                            {(rootType == 2 && workspaceDeliveryConditions != null) && origin !== 'desktop' && (
                                <p className={`${style['delivery-minimum']} text-center mb-0 mt-3`}>
                                    {trans('cart.delivery_minimum', { workspaceName: workspace?.name, price: <strong className="font-bold">{currency + _.toNumber(workspaceDeliveryConditions.price_min).toFixed(2)}</strong> })}
                                </p>
                            )}

                            {(rootType != 3 && serviceCostSettingState?.service_cost_set) && origin !== 'desktop' && (
                                <p className={`${style['delivery-minimum']} text-center mb-0 mt-3`}>
                                    { serviceCostSettingState?.service_cost_always_charge ? 
                                        trans('cart.service_cost_always_charge_on', {
                                            workspaceName: workspace?.name,
                                            cost: <strong className="font-bold">{currency}{_.toNumber(serviceCostSettingState?.service_cost).toFixed(2)}</strong>
                                        }) 
                                        : 
                                        trans('cart.service_cost_always_charge_off', {
                                            workspaceName: workspace?.name,
                                            cost: <strong className="font-bold">{currency}{_.toNumber(serviceCostSettingState?.service_cost).toFixed(2)}</strong>,
                                            price: <strong className="font-bold">{currency}{_.toNumber(serviceCostSettingState?.service_cost_amount).toFixed(2)}</strong>
                                        })
                                    }
                                </p>
                            )}

                            <div className={`${style.messaging}`}>
                                <div className="col-sm-12 col-12">
                                    <div className={style.steps}>
                                        <div className={style['step-item']} onClick={() => activeStep(1)}>
                                            <div className={style['step-number']} style={{ color: origin == 'desktop' ? '#FFF' : color, borderColor: color, background: origin == 'desktop' ? color : '' }}>1</div>
                                            <div className={`${style['step-name']} ${origin === 'desktop' ? 'text-uppercase' : ''}`} style={{ color: color }}>{trans('cart.step_overview')}</div>
                                        </div>
                                        <div className={style['step-item']}>
                                            <div className={style['step-number']}>2</div>
                                            <div className={`${style['step-name']} ${origin === 'desktop' ? 'text-uppercase' : ''}`}>{trans('date-time')}</div>
                                        </div>
                                        <div className={style['step-item']}>
                                            <div className={style['step-number']}>3</div>
                                            <div className={`${style['step-name']} ${origin === 'desktop' ? 'text-uppercase' : ''}`}>{trans('cart.step_payment_method')}</div>
                                        </div>
                                    </div>
                                </div>

                                {isExsitRedeem && !isShowRedeem && origin !== 'desktop' && (
                                    <div className={style['show-redeem-again']} onClick={handleAgain}>{trans('cart.apply_redeem_discount')}</div>
                                )}
                            </div>
                        </div>

                        {/* Popup delete confirmation */}
                        <div className="d-flex">
                            <div className="modal fade" id="delete-modal">
                                <div className="modal-dialog modal-dialog-centered">
                                    <div className={`modal-content ${styleModal['modal-content-login']}`}>
                                        <div className="modal-body" >
                                            <div className={`mx-auto`} id={'delivery-popup'} style={{ textAlign: 'center' }}>
                                                <svg xmlns="http://www.w3.org/2000/svg" width="101" height="3" viewBox="0 0 101 3" fill="none">
                                                    <path d="M2 1.5H99" stroke="#E1E1E1" strokeWidth="3" strokeLinecap="round" />
                                                </svg>
                                            </div>
                                            <div className={`${styleModal['btn-confirm-logout']} px-3`}>
                                                {trans('delete-confirmation')}
                                            </div>
                                            <div className={styleModal['btn-yes-logout']}
                                                data-bs-dismiss="modal"
                                                onClick={() => { calculateGroupOrderPrice(), removeProduct(removeIndex ?? 0), removeInvalidProductId(removeProductId) }}>
                                                {trans('yes-remove')}
                                            </div>
                                            <div
                                                data-type="button"
                                                data-bs-dismiss="modal"
                                                className={styleModal['btn-no-logout']}
                                                style={{ color: color }}
                                            > {trans('no-cancel')} </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    {isOpenEditProfile && !isFormValid ? (
                        <ProfileUpdate color={color} isShow={isShow} togglePopup={() => togglePopup()} newProfileData={profileData} />
                    ) : (
                        isPopupOpen && suggestionProduct.length > 0 && isMobile ? (
                            <ProductSuggestion color={color} togglePopup={() => togglePopup()} suggestionProduct={suggestionProduct} baseLink={`/${baseLink}/products`} activeStep2={() => handleActiveStep2()} />
                        ) : (isPopupOpen && suggestionProduct.length > 0 && !isMobile && !productCategoryNotAvailable) && (
                            <ProductSuggestionDesk color={color} togglePopup={() => togglePopup()} suggestionProduct={suggestionProduct} baseLink={`/${baseLink}/products`} activeStep2={() => handleActiveStep2()} />
                        )
                    )}
                </>
            )}

            {/* case mobile */}
            {origin !== 'desktop' ? (
                <>
                    <TypesPopup workspace={workspace}
                        from="in_cart"
                        isDeliveryOrderOpenManual={isDeliveryOrderOpenManual}
                        setIsDeliveryOrderOpenManual={setIsDeliveryOrderOpenManual} />

                    {
                        (groupOrderOn !== true && (rootType == 1 && takeoutOn == false && takeoutOn != 'undefined')
                            || (rootType == 2 && deliveryOn == false && deliveryOn != 'undefined')
                            || (rootType == 3 && groupOrderOn == false && groupOrderOn != 'undefined') || triggerShowInvalidCart)

                        && (
                            <InvalidCart />
                        )}

                    {rootType == 2 && workspaceDeliveryConditions == null && !deliveryConditionLoading && deliveryAddress != null && deliveryAddress?.lat != 'undefined' && deliveryAddress?.lng != 'undefined' && (
                            <DeliveryNotShipping togglePopup={() => { }} isShow={isShow} workspaceName={workspace?.name} />
                        )}

                </>
            ) : (
                <>
                    {/* case desktop and when select change type */}
                    {(changeOrderType === true || cartDeliveryOpen || changeOrderTypeDesktopManual || isDeliveryOrderOpenManual > 0) && (
                        <>
                            {isDeliveryOrderOpenManual || cartDeliveryOpen ? (
                                <DeliveryLocation errorDeliveryMessage={errorDeliveryMessage} location={handleLocation} />
                            ) : (
                                <DesktopChangeType workspace={workspace}
                                    from="in_cart"
                                    setIsDeliveryType={setIsDeliveryType}
                                    isDeliveryOrderOpenManual={isDeliveryOrderOpenManual}
                                    setIsDeliveryOrderOpenManual={setIsDeliveryOrderOpenManual} />
                            )
                            }
                            <div className="row type-actions">
                                <div className="col-6">
                                    <button type="button"
                                        className="desktop-btn-back"
                                        onClick={() => { hideChangeTypeScreen('back'); setErrorDeliveryMessage(null); dispatch(setflagNextData(true)); }}>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="25" viewBox="0 0 24 25" fill="none">
                                            <path d="M14 17L10 12.5L14 8" stroke="#888888" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" />
                                        </svg>
                                        {trans('back')}
                                    </button>
                                </div>
                                <div className="col-6">
                                    {rootType != 0
                                        ? deliveryAddressTmp && deliveryAddressTmp?.lat
                                            ? (
                                                <button type="button"
                                                    className="desktop-btn-next"
                                                    style={{ background: color }}
                                                    onClick={() => handleSaveDeliveryAddress()}>
                                                    {trans('cart.further')}
                                                </button>
                                            ) : isDeliveryOrderOpenManual
                                                ? (
                                                    <button type="button"
                                                        className="desktop-btn-next"
                                                        style={{ background: color, opacity: 0.5 }}>
                                                        {trans('cart.further')}
                                                    </button>
                                                ) : (
                                                    <button type="button"
                                                        className={`desktop-btn-next ${nextFlagSlice === false ? 'btn-disabled-none-background' : ''}`}
                                                        style={{ background: color }}
                                                        onClick={() => hideChangeTypeScreen('next')}>
                                                        {trans('cart.further')}
                                                    </button>
                                                )
                                        : deliveryAddressTmp
                                            ? (
                                                <button type="button"
                                                    className="desktop-btn-next"
                                                    style={{ background: color }}
                                                    onClick={() => handleSaveDeliveryAddress()}>
                                                    {trans('cart.further')}
                                                </button>
                                            ) : (
                                                <button type="button"
                                                    className="desktop-btn-next"
                                                    style={{ background: color, opacity: 0.5 }}>
                                                    {trans('cart.further')}
                                                </button>
                                            )
                                    }
                                </div>
                            </div>
                        </>
                    )}
                    {isLoginOpen ? (<Login togglePopup={() => togglePopupLogin()} from={'suggestionDesk'} />) : null}
                </>
            )}

            <div className="res-mobile">
                <ToastContainer />
            </div>
        </>
    )
}

export default memo(OrderOverview)
