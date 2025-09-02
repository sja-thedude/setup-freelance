"use client"

import React, { memo, useEffect, useState, useRef } from 'react'
import _, { set } from 'lodash'
import Link from 'next/link'
import { useI18n } from '@/locales/client'
import { useAppSelector, useAppDispatch } from '@/redux/hooks'
import style from 'public/assets/css/cart.module.scss'
import { currency } from '@/config/currency'
import { Formik, Form, Field } from 'formik'
import { addCouponToCartTable, addCouponToCartSelf, removeCouponFromCart, addStepTable, rootCartValidCouponProductIdsTable,rootCartValidCouponProductIdsSelf, rootCartTotalDiscount, rootCartTotalDiscountTable, rootCartTotalDiscountSelf, markStepReversed } from '@/redux/slices/cartSlice'
import Cookies from "js-cookie";
import { useRouter } from 'next/navigation'
import { api } from "@/utils/axios";
import ListType from '@/app/[locale]/components/layouts/popup/listType'
import { useCheckAvailableProductsQuery } from '@/redux/services/product/productApi'
import { useCheckAvailableCategoriesQuery } from '@/redux/services/categoriesApi'
import styleModal from 'public/assets/css/profile.module.scss'
import { ToastContainer, toast, Slide } from 'react-toastify';
import 'react-toastify/dist/ReactToastify.css';
import useMediaQuery from '@mui/material/useMediaQuery'
import TextareaAutosize from 'react-textarea-autosize'
import moment from 'moment';

export const VALUE_DISCOUNT_TYPE = {
    NO_DISCOUNT: 0,
    FIXED_AMOUNT: 1,
    PERCENTAGE: 2,
};

const OrderOverview = (props: any) => {
    let { cart, color, workspaceId, changeInCart, cartNote, activeStep, origin } = props
    const trans = useI18n()
    const dispatch = useAppDispatch()
    const isMobile = useMediaQuery('(max-width: 1279px)');
    let stepReversed = useAppSelector((state: any) => state.cart.stepReversed)
    let cartCouponTable = useAppSelector((state: any) => state.cart.couponTable)
    let cartCouponSelf = useAppSelector((state: any) => state.cart.couponSelf)
    const [removeIndex, setRemoveIndex] = useState<any>(null);
    const router = useRouter()
    const [suggestionProduct, setSuggestionProduct] = useState<any[]>([]);
    const [isListTypeOpen, setIsListTypeOpen] = useState(false);
    const tokenLoggedInCookie = Cookies.get('loggedToken');
    const language = Cookies.get('Next-Locale') ?? 'nl';

    if (cart && cart.length > 0) {
        var firstCategoryId = cart[0].product?.data?.category_id;
    } else {
        Cookies.remove('productSuggestion');
    }

    const defaultNoteTable = useAppSelector((state) => state.cart.cartNote);
    const defaultNoteSeft = useAppSelector((state) => state.cart.selfOrderingCartNote);
    const defaultNote = origin === 'table_ordering' ? defaultNoteTable : defaultNoteSeft;
    const [note, setNote] = useState(defaultNote);
    const allSameCategory = cart?.every((item: any) => item.product?.data?.category_id === firstCategoryId);
    let categoryId = allSameCategory ? firstCategoryId : 0;
    const productIds = _.map(cart, 'productId')
    const categoryIds = _.map(cart, 'product.data.category_id')
    const { data: productAvailable, isLoading: isLoadingProduct } = useCheckAvailableProductsQuery({ ids: _.uniq(productIds) })
    const { data: categoryAvailable, isLoading: isLoadingCategory } = useCheckAvailableCategoriesQuery({ ids: _.uniq(categoryIds) })
    const [productOptionNotExist, setProductOptionNotExist] = useState<Array<number>>([])
    const [productCategoryNotAvailable, setProductCategoryNotAvailable] = useState(0)
    const [productOptionAvailables, setProductOptionAvailables] = useState<any>({})
    const [productInvalidTimeslot, setProductInvalidTimeslot] = useState<any>({})
    const [products, setProducts] = useState<any>({})
    const [counponPriceTable, setCounponPriceTable] = useState('')
    const [counponPriceSelf, setCounponPriceSelf] = useState('')
    const [cartTotalPriceCoupon, setCartTotalPriceCoupon] = useState('')
    const [currentInput, setCurrentInput] = useState('');
    const [couponErrorTable, setCouponErrorTable] = useState('');
    const [couponErrorSelf, setCouponErrorSelf] = useState('');
    const [couponErrorButtonTable, setCouponErrorButtonTable] = useState('');
    const [couponErrorButtonSelf, setCouponErrorButtonSelf] = useState('');
    const [isVisibleTable, setIsVisibleTable] = useState(false);
    const [isVisibleSelf, setIsVisibleSelf] = useState(false);
    const [loadedProducts, setLoadedProducts] = useState(0)
    const [loadedProductOptions, setLoadedProductOptions] = useState(0)
    const [isDisabled, setIsDisabled] = useState(true);
    const inputProductNumberRefs = useRef<any>([])

    useEffect(() => {
        setLoadedProducts(0)
        setLoadedProductOptions(0)

        const getProducts = async () => {
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
            if (stepReversed) {
                dispatch(markStepReversed(false))
                const res = await api.get(`products/validate_available_timeslot?from=mobile&date=${moment().format(DATE_FORMAT)}&time=${moment().format(TIME_FORMAT)}&${cart.map((cartItem: any) => `product_id[]=${cartItem.productId}`).join('&')}`);
                const available = res.data?.data || []
                if (_.includes(available, false)) {
                    setProductInvalidTimeslot(available);
                } else {
                    setProductInvalidTimeslot({});
                }
            }
        }

        if (!_.isEmpty(cart)) {
            getProducts()
        }
    }, [cart, stepReversed])

    useEffect(() => {
        const fetchData = async () => {
            if (categoryId) {
                try {
                    if (tokenLoggedInCookie) {
                        const headers = {
                            Authorization: `Bearer ${tokenLoggedInCookie}`,
                            'Content-Language': language
                        };
                        const res = await api.get(`categories/${categoryId}/suggestion_products?order_by=name&sort_by=asc`, { headers });
                        const resProducts = await api.get(`products/list?workspace_id=${workspaceId}&category_ids=${categoryId}`, { headers });
                        setSuggestionProduct((res?.data?.data?.data || []).map((x: any) => {
                            x.options = resProducts?.data?.data?.filter((item: any) => item.id == x.id)[0]?.options || []
                            return x;
                        }));
                    } else {
                        // console.log('No Token.');
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
                const response = await api.get(`/products/validate_coupon${queryParams}`, {
                    headers: {
                        'Authorization': `Bearer ${tokenLoggedInCookie}`,
                        'Content-Language': language,
                    }
                });
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
            // console.log('err:', err);
            // setCouponError(err.response.data.message);
            if (origin === 'table_ordering') {
                toast.dismiss();
                toast(err.response.data.message, {
                    position: toast.POSITION.BOTTOM_CENTER,
                    autoClose: 1500,
                    hideProgressBar: true,
                    closeOnClick: true,
                    closeButton: false,
                    transition: Slide,
                    className: 'message',
                });
            } else {
                toast.dismiss();
                toast(err.response.data.message, {
                    position: toast.POSITION.BOTTOM_CENTER,
                    autoClose: 1500,
                    hideProgressBar: true,
                    closeOnClick: true,
                    closeButton: false,
                    transition: Slide,
                    className: 'message',
                });
            }
            return false;
        }
    }

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

                if (categoryAvailable?.data[cartItem.product.data.category_id] == false || productInvalidTimeslot?.[cartItem.product.data.id] == false || productAvailable?.data[cartItem.product.data.id] == false) {
                    setProductCategoryNotAvailable(1)
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
                }

                return cartItem
            })

            if (!_.isEqual(_.uniq(productOptionNotExistClone), _.uniq(productOptionNotExist))) {
                setProductOptionNotExist(_.uniq(productOptionNotExistClone))
            }

            if (!_.isEqual(cart, cartClone)) {
                dispatch(changeInCart(cartClone))
            }

            // Load coupon to cart
            if (origin === 'table_ordering') {
                if (cartCouponTable) {
                    const couponData: any = cartCouponTable;
                    if (couponData?.discount_type == VALUE_DISCOUNT_TYPE.PERCENTAGE) {
                        let discount = couponData?.percentage;
                        setCounponPriceTable(_.round((cartClone?.reduce((total: any, item: any) => total + item.basePrice * item.productTotal, 0) * discount / 100), 2).toFixed(2))
                        setCartTotalPriceCoupon(_.round((cartClone?.reduce((total: any, item: any) => total + item.basePrice * item.productTotal, 0) * (100 - discount) / 100), 2).toFixed(2));
                    } else if (couponData?.discount_type == VALUE_DISCOUNT_TYPE.FIXED_AMOUNT) {
                        let discount = couponData?.discount;
                        setCounponPriceTable(_.round(discount, 2).toFixed(2))
                        setCartTotalPriceCoupon(_.round((cartClone?.reduce((total: any, item: any) => total + item.basePrice * item.productTotal, 0) - discount), 2).toFixed(2));
                    } else {

                    }

                    // Check available coupon with product and delete if not available
                    validateCouponProduct(cartCouponTable)
                        .then(function (success) {
                            if (!success) {
                                dispatch(addCouponToCartTable(null));
                                setCounponPriceTable('');
                                setCartTotalPriceCoupon('');
                            }
                        }, function (error) {
                            // console.log('error:', error);
                        });

                }
            } else {
                if (cartCouponSelf) {
                    const couponData: any = cartCouponSelf;
                    if (couponData?.discount_type == VALUE_DISCOUNT_TYPE.PERCENTAGE) {
                        let discount = couponData?.percentage;
                        setCounponPriceSelf(_.round((cartClone?.reduce((total: any, item: any) => total + item.basePrice * item.productTotal, 0) * discount / 100), 2).toFixed(2))
                        setCartTotalPriceCoupon(_.round((cartClone?.reduce((total: any, item: any) => total + item.basePrice * item.productTotal, 0) * (100 - discount) / 100), 2).toFixed(2));
                    } else if (couponData?.discount_type == VALUE_DISCOUNT_TYPE.FIXED_AMOUNT) {
                        let discount = couponData?.discount;
                        setCounponPriceSelf(_.round(discount, 2).toFixed(2))
                        setCartTotalPriceCoupon(_.round((cartClone?.reduce((total: any, item: any) => total + item.basePrice * item.productTotal, 0) - discount), 2).toFixed(2));
                    } else {

                    }
                    // Check available coupon with product and delete if not available
                    validateCouponProduct(cartCouponSelf)
                        .then(function (success) {
                            if (!success) {
                                dispatch(addCouponToCartSelf(null));
                                setCounponPriceSelf('');
                                setCartTotalPriceCoupon('');
                            }
                        }, function (error) {
                            // console.log('error:', error);
                        });
                }
            }
        } else {
            if (origin === 'table_ordering') {
                if (_.isEmpty(cart)) {
                    dispatch(addCouponToCartTable(null));
                }
            } else {
                if (_.isEmpty(cart)) {
                    dispatch(addCouponToCartSelf(null));
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
        productInvalidTimeslot,
        dispatch
    ])

    const changeNumberProduct = (productIndex: number, value: number, isManual: boolean = false) => {
        let cartClone = [...cart]
        let newNumber = _.toNumber(cartClone[productIndex].productTotal) + _.toNumber(value)

        if (isManual === true) {
            newNumber = _.toNumber(value)
        }

        if (newNumber > 0) {
            const product = { ...cartClone[productIndex] }
            product.productTotal = _.toNumber(cartClone[productIndex].productTotal) + value
            cartClone[productIndex] = { ...product }
            dispatch(changeInCart(cartClone))
            cart = [...cartClone]

            if (inputProductNumberRefs.current[productIndex]) {
                inputProductNumberRefs.current[productIndex].value = newNumber.toString()
            }
        }

        if (currentInput) {
            handleSubmitCoupon({ coupon: currentInput }, { resetForm: () => { } });
        }
    }

    const removeProduct = (productIndex: number) => {
        const cartClone = [...cart]
        _.remove(cartClone, cartClone[productIndex])
        dispatch(changeInCart(cartClone))
        cart = [...cartClone]
    }
    const DATE_FORMAT = 'YYYY-MM-DD';
    const TIME_FORMAT = 'HH:mm';

    const togglePopup = async () => {
        // Validate available timeslot to go to step 2
        const res = await api.get(`products/validate_available_timeslot?from=mobile&date=${moment().format(DATE_FORMAT)}&time=${moment().format(TIME_FORMAT)}&${cart.map((cartItem: any) => `product_id[]=${cartItem.productId}`).join('&')}`);
        const available = res.data?.data || []
        if (_.includes(available, false)) {
            setProductInvalidTimeslot(available);
            dispatch(markStepReversed(true))
        } else {
            if (origin === 'table_ordering') {
                if (cartCouponTable && cartCouponTable?.code) {
                    const validCoupon = await validateCouponProduct(cartCouponTable);
                    if (!validCoupon) {
                        handleRemoveCoupon();
                    } else {
                        dispatch(cartNote(note))
                        dispatch(addStepTable(2))
                        dispatch(rootCartTotalDiscountTable(counponPriceTable));
                    }
                } else {
                    dispatch(cartNote(note))
                    dispatch(addStepTable(2))
                    dispatch(rootCartTotalDiscountTable(counponPriceTable));
                }
            } else {
                if (cartCouponSelf && cartCouponSelf?.code) {
                    const validCoupon = await validateCouponProduct(cartCouponSelf);
    
                    if (!validCoupon) {
                        handleRemoveCouponSelf();
                    } else {
                        dispatch(cartNote(note))
                        if (typeof activeStep === 'function') {
                            activeStep(2);
                        }
                        dispatch(rootCartTotalDiscountSelf(counponPriceSelf));
                    }
                } else {
    
                    if (typeof activeStep === 'function') {
                        dispatch(cartNote(note))
    
                        activeStep(2);
                    }
                }
            }
        }
    };
    const toggleOrderType = () => {
        if (tokenLoggedInCookie) {
            setIsListTypeOpen(!isListTypeOpen);
        } else {
            router.push('/user/login')
        }
    }

    const handleSubmitCoupon = (values: { coupon: string }, { resetForm }: { resetForm: () => void }) => {
        const couponValue = values.coupon;
        // validate coupon
        let halo = api.get(`coupons/validate_code?code=${couponValue}&workspace_id=${workspaceId}`, {
            headers: {
                'Content-Language': language,
            }
        }).then(cou => {
            if (cou?.status == 200 && cou?.data?.success == true) {
                const productIds = cart.map((item: any) => item.productId);
                if (productIds.length > 0) {
                    // validate product with coupon
                    const productIdParams = productIds.map((id: any) => `product_id[]=${id}`).join('&');
                    const queryParams = `?${productIdParams}&code=${couponValue}`;
                    let halo = api.get(`/products/validate_coupon${queryParams}`, {
                        headers: {
                            'Authorization': `Bearer ${tokenLoggedInCookie}`,
                            'Content-Language': language,
                        }
                    }).then(product => {
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
                                dispatch(rootCartValidCouponProductIdsTable(availabelProducts));
                            }

                            if (!validCouponProduct) {
                                setCouponErrorTable(trans('cart.message_invalid_coupon_product'));
                                // setCouponErrorButton(trans('cart.message_invalid_coupon_product'));
                                setIsVisibleTable(true)

                                return;
                            }

                            resetForm();
                            const couponData = cou?.data?.data;
                            if (couponData?.discount_type == VALUE_DISCOUNT_TYPE.PERCENTAGE) {
                                let discount = couponData?.percentage;
                                setCounponPriceTable(_.round((totalPriceCouponProducts * discount / 100), 2).toFixed(2))
                                setCartTotalPriceCoupon(_.round((cart?.reduce((total: any, item: any) => total + item.basePrice * item.productTotal, 0) - (totalPriceCouponProducts * discount / 100)), 2).toFixed(2));
                            } else if (couponData?.discount_type == VALUE_DISCOUNT_TYPE.FIXED_AMOUNT) {
                                let discount = couponData?.discount;

                                const totalDiscount = parseFloat(_.round(discount, 2).toFixed(2));
                                if (totalPriceCouponProducts > totalDiscount) {
                                    // if coupon price < total price of products set coupon price = coupon price

                                    setCounponPriceTable(_.round(discount, 2).toFixed(2))
                                    setCartTotalPriceCoupon(_.round((cart?.reduce((total: any, item: any) => total + item.basePrice * item.productTotal, 0) - discount), 2).toFixed(2));
                                } else {
                                    // if coupon price > total price of products set coupon price = total price of products

                                    setCounponPriceTable(_.round((totalPriceCouponProducts), 2).toFixed(2))
                                    setCartTotalPriceCoupon(_.round((cart?.reduce((total: any, item: any) => total + item.basePrice * item.productTotal, 0) - totalPriceCouponProducts), 2).toFixed(2));
                                }
                            } else {

                            }

                            // Add coupon into cart
                            dispatch(addCouponToCartTable(couponData));
                            setCouponErrorTable('');
                            setCouponErrorButtonTable('');
                            setIsVisibleTable(false);

                            toast.dismiss();
                            if (isMobile) {
                                toast(trans('cart.message_apply_coupon_successfully'), {
                                    position: toast.POSITION.BOTTOM_CENTER,
                                    autoClose: 1000,
                                    hideProgressBar: true,
                                    closeOnClick: true,
                                    closeButton: false,
                                    transition: Slide,
                                    className: 'message'
                                });
                            }
                        }
                    }).catch(err => {
                        // console.log(err)
                        // const responseError = err.response.data;
                        if (typeof err.response.data !== 'undefined') {
                            setCouponErrorTable(err.response.data.message);
                            // setCouponErrorButton(err.response.data.message);
                            setIsVisibleTable(true)
                        }
                    });
                }
            }
        }).catch(err => {
            toast.dismiss();
            toast(err.response.data.message, {
                position: toast.POSITION.BOTTOM_CENTER,
                autoClose: 1500,
                hideProgressBar: true,
                closeOnClick: true,
                closeButton: false,
                transition: Slide,
                className: 'message',
            });
        });
    }

    const handleSubmitCouponSelf = (values: { coupon: string }, { resetForm }: { resetForm: () => void }) => {
        const couponValue = values.coupon;
        // validate coupon
        let halo = api.get(`coupons/validate_code?code=${couponValue}&workspace_id=${workspaceId}`, {
            headers: {
                'Content-Language': language,
            }
        }).then(cou => {
            if (cou?.status == 200 && cou?.data?.success == true) {
                const productIds = cart.map((item: any) => item.productId);
                if (productIds.length > 0) {
                    // validate product with coupon
                    const productIdParams = productIds.map((id: any) => `product_id[]=${id}`).join('&');
                    const queryParams = `?${productIdParams}&code=${couponValue}`;
                    let halo = api.get(`/products/validate_coupon${queryParams}`, {
                        headers: {
                            'Authorization': `Bearer ${tokenLoggedInCookie}`,
                            'Content-Language': language,
                        }
                    }).then(product => {
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
                                dispatch(rootCartValidCouponProductIdsSelf(availabelProducts));
                            }

                            if (!validCouponProduct) {
                                setCouponErrorSelf(trans('cart.message_invalid_coupon_product'));
                                // setCouponErrorButton(trans('cart.message_invalid_coupon_product'));
                                setIsVisibleSelf(true)

                                return;
                            }

                            resetForm();
                            const couponData = cou?.data?.data;
                            if (couponData?.discount_type == VALUE_DISCOUNT_TYPE.PERCENTAGE) {
                                let discount = couponData?.percentage;

                                setCounponPriceSelf(_.round((totalPriceCouponProducts * discount / 100), 2).toFixed(2))
                                setCartTotalPriceCoupon(_.round((cart?.reduce((total: any, item: any) => total + item.basePrice * item.productTotal, 0) - (totalPriceCouponProducts * discount / 100)), 2).toFixed(2));
                            } else if (couponData?.discount_type == VALUE_DISCOUNT_TYPE.FIXED_AMOUNT) {
                                let discount = couponData?.discount;

                                const totalDiscount = parseFloat(_.round(discount, 2).toFixed(2));
                                if (totalPriceCouponProducts > totalDiscount) {
                                    // if coupon price < total price of products set coupon price = coupon price
                                    setCounponPriceSelf(_.round(discount, 2).toFixed(2))
                                    setCartTotalPriceCoupon(_.round((cart?.reduce((total: any, item: any) => total + item.basePrice * item.productTotal, 0) - discount), 2).toFixed(2));
                                } else {
                                    // if coupon price > total price of products set coupon price = total price of products
                                    setCounponPriceSelf(_.round((totalPriceCouponProducts), 2).toFixed(2))
                                    setCartTotalPriceCoupon(_.round((cart?.reduce((total: any, item: any) => total + item.basePrice * item.productTotal, 0) - totalPriceCouponProducts), 2).toFixed(2));
                                }
                            } else {

                            }

                            // Add coupon into cart
                            dispatch(addCouponToCartSelf(couponData));
                            setCouponErrorSelf('');
                            setCouponErrorButtonSelf('');
                            setIsVisibleSelf(false);
                            toast.dismiss();
                            if (isMobile) {
                                toast(trans('cart.message_apply_coupon_successfully'), {
                                    position: toast.POSITION.BOTTOM_CENTER,
                                    autoClose: 1000,
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
                            setCouponErrorSelf(err.response.data.message);
                            // setCouponErrorButton(err.response.data.message);
                            setIsVisibleSelf(true)
                        }
                    });
                }
            }
        }).catch(err => {
            // setCouponError(err.response.data.message);
            toast.dismiss();
            toast(err.response.data.message, {
                position: toast.POSITION.BOTTOM_CENTER,
                autoClose: 1500,
                hideProgressBar: true,
                closeOnClick: true,
                closeButton: false,
                transition: Slide,
                className: 'message',
            });
        });
    }

    /**
     * Handle remove coupon
     */
    const handleRemoveCoupon = () => {
        dispatch(removeCouponFromCart());
        dispatch(addCouponToCartTable(null));
        setCounponPriceTable('');
        setCartTotalPriceCoupon('');
        setCurrentInput('');
    }

    /**
 * Handle remove coupon
 */
    const handleRemoveCouponSelf = () => {
        dispatch(removeCouponFromCart());
        dispatch(addCouponToCartSelf(null));
        setCounponPriceSelf('');
        setCartTotalPriceCoupon('');
        setCurrentInput('');
    }

    const handleDisable = (e: any) => {
        const value = e.target.value;
        if (value.length > 0) {
            setIsDisabled(false);
        } else {
            setIsDisabled(true);
        }
    }

    const styleStepNumber: any = { color: color, borderColor: color };
    const styleStepName: any = { color: color };
    const [step, setStep] = useState<any | null>(1);

    const updateStep = (step: any) => {
        setStep(step);
    }
    const calculateTop = () => {
        if (step && step !== 1) {
            return '80px';
        } else {
            if (props.coupons?.data.length > 0) {
                return '100px';
            } else {
                return '45px'
            }
        }
    }

    return (
        <>
            {_.isEmpty(cart) ? (
                <div className={style['empty-box-wrapper']}>
                    <div className={`${style['cart-box-shadow']} ${style['empty-box']}`}>
                        <p className={`${style['empty-message']} text-center`}>
                            {trans('cart.empty_cart')}
                        </p>
                        <Link href={origin === 'table_ordering' ? "/table-ordering/products" : "/self-ordering/products"}
                            className={`d-block width-100 ${style['gray-button']} text-uppercase mt-3`}>
                            {trans('cart.view_range')}
                        </Link>
                    </div>
                </div>
            ) : (
                <>
                    <div className={style['cart-wrapper']} style={{ marginTop: calculateTop() }}>
                        {
                            <>
                                <div className="row table-cart">
                                    <div className={`col-sm-12 col-12 ${style['product-list']}`}>
                                        {cart?.map((item: any, index: any) => (
                                            <div className={`row table-cart ${style['product-item-area']}`} key={`product-item-${index}-${item.productId}`}>
                                                <div className={`col-sm-12 col-12 ${style['product-item']}`}>
                                                    <div className="row table-cart">
                                                        <div className="col-sm-9 col-9 d-flex">
                                                            <div className={style['number-of-product']}>
                                                                <svg className={style.increase} onClick={() => changeNumberProduct(index, 1)} width="17" height="17" viewBox="0 0 17 17" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                    <path d="M12.5358 10.2041L8.53577 6.12244L4.53577 10.2041" stroke="#C4C4C4" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" />
                                                                </svg>
                                                                <div className={`${_.includes(productOptionNotExist, index) ? 'error-color' : ''} ${style.number}`}>
                                                                    <input type="number"
                                                                        min={1}
                                                                        ref={(element) => inputProductNumberRefs.current[index] = element}
                                                                        className={style.number}
                                                                        defaultValue={item.productTotal}
                                                                        onChange={(e) => changeNumberProduct(index, e.target.value ? parseInt(e.target.value) : 0, true)}
                                                                        onBlur={(e) => changeNumberProduct(index, e.target.value ? parseInt(e.target.value) : item.productTotal, true)}
                                                                    />                                                        </div>
                                                                <svg className={style.decrease} onClick={() => changeNumberProduct(index, -1)} xmlns="http://www.w3.org/2000/svg" width="17" height="17" viewBox="0 0 17 17" fill="none">
                                                                    <path d="M12.5358 6.79593L8.53577 10.8776L4.53577 6.79593" stroke="#C4C4C4" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" />
                                                                </svg>
                                                            </div>
                                                            <div className={style['product-name-options']}>
                                                                <div className={`${style['product-name']} ${_.includes(productOptionNotExist, index) ? 'error-color' : ''}`}>
                                                                    {item.product?.data?.name}
                                                                </div>
                                                                <div className={`${style['product-options']} ${_.includes(productOptionNotExist, index) ? 'error-color' : ''}`}>
                                                                    {_.map(item?.optionItemsStore, (optionItemsStoreValue: any, optionItemsStoreKey: number) => (
                                                                        <div className={style.option} key={`product-option-${index}-${optionItemsStoreKey}`}>
                                                                            {(_.find(optionItemsStoreValue?.optionItems, { master: true })) ? (
                                                                                <div className={style.option}>
                                                                                    <span className={style['option-item']}>
                                                                                        {_.find(optionItemsStoreValue?.optionItems, { master: true }).name}
                                                                                    </span>
                                                                                </div>
                                                                            ) : (
                                                                                <>
                                                                                    {optionItemsStoreValue?.optionItems?.map((optionItem: any, optionItemIndex: number) => (
                                                                                        <span className={style['option-item']} key={`product-option-item-${optionItemsStoreValue.optionId}-${optionItemIndex}-${optionItem.id}`}>
                                                                                            {_.find(item?.productOptions?.data, { id: _.toNumber(optionItemsStoreValue.optionId) }) && _.find(item?.productOptions?.data, { id: _.toNumber(optionItemsStoreValue.optionId) })?.is_ingredient_deletion == true ? 'Z ' + optionItem.name : optionItem.name}
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
                                                                    <div className={`${_.includes(productOptionNotExist, index) ? 'error-color' : ''} ${style.price}`} style={{ color: color }}>
                                                                        {currency}
                                                                        {_.round(item.basePrice * item.productTotal, 2).toFixed(2)}
                                                                    </div>
                                                                    <div className={style.remove} data-bs-toggle="modal" data-bs-target="#delete-modal" onClick={() => { setRemoveIndex(index) }}>
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
                                {cartTotalPriceCoupon && origin === 'table_ordering' && counponPriceTable && (
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
                                        <div className="row">
                                            <div className={`col-sm-12 col-12 mt-2 mb-2 ${style['total-text']}`}>
                                                <div className="float-start">
                                                    {trans('cart.coupon_discount')}
                                                </div>
                                                <div className="float-end">
                                                    -{currency}
                                                    {counponPriceTable}
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
                                        <div className="line"></div>
                                    </div>
                                )}
                                {cartTotalPriceCoupon && origin === 'self_ordering' && counponPriceSelf && (
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
                                        <div className="row">
                                            <div className={`col-sm-12 col-12 mt-2 mb-2 ${style['total-text']}`}>
                                                <div className="float-start">
                                                    {trans('cart.coupon_discount')}
                                                </div>
                                                <div className="float-end">
                                                    -{currency}
                                                    {counponPriceSelf}
                                                    <svg className={style['remove-coupon']} width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg" onClick={handleRemoveCouponSelf}>
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
                                        <div className="line"></div>
                                    </div>
                                )}
                                {origin === 'table_ordering' ? (
                                    <div className={`${cartTotalPriceCoupon && counponPriceTable ? style.totaling : ''} row`}>
                                        <div className="col-sm-12 col-12">
                                            <div className={`float-end pb-2 pdr-19 ${style['total-price']}`}>
                                                <span className="me-3 text-uppercase">{trans('cart.total')}</span>
                                                <span>
                                                    {currency}
                                                    {cartTotalPriceCoupon ? cartTotalPriceCoupon : _.round(cart?.reduce((total: any, item: any) => total + item.basePrice * item.productTotal, 0), 2).toFixed(2)}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                ) : (
                                    <div className={`${cartTotalPriceCoupon && counponPriceSelf ? style.totaling : ''} row`}>
                                        <div className="col-sm-12 col-12">
                                            <div className={`float-end pb-2 pdr-19 ${style['total-price']}`}>
                                                <span className="me-3 text-uppercase">{trans('cart.total')}</span>
                                                <span>
                                                    {currency}
                                                    {cartTotalPriceCoupon ? cartTotalPriceCoupon : _.round(cart?.reduce((total: any, item: any) => total + item.basePrice * item.productTotal, 0), 2).toFixed(2)}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                )}
                                {origin == 'table_ordering' ? (
                                    <div className="row mt-2">
                                        <div className="col-sm-12 col-12">
                                            {!cartTotalPriceCoupon && (
                                                <Formik initialValues={{ coupon: '' }} onSubmit={handleSubmitCoupon}>
                                                    <Form>
                                                        <div className="row mb-2 mt-2">
                                                            <div className={`col-sm-12 col-12 ${style['coupon-form']}`} >
                                                                <Field className={`input-text me-3 ${style['flex-2']} ${couponErrorTable ? style.invalid : ''} `} type="text" name="coupon" placeholder={trans('cart.coupon_code')}
                                                                    onInput={(e: any) => {
                                                                        const uppercaseValue = ("" + e.target.value).toUpperCase();
                                                                        e.target.value = uppercaseValue;
                                                                        setCurrentInput(uppercaseValue);
                                                                        setCouponErrorTable('');
                                                                    }}
                                                                    onKeyUp={handleDisable}
                                                                />
                                                                <button className={`text-uppercase itr-btn-primary ${style['flex-1']} ${isDisabled ? style.disabled : ''}`} type="submit">{trans('cart.apply_coupon')}</button>
                                                            </div>
                                                        </div>
                                                    </Form>
                                                </Formik>
                                            )}

                                            {couponErrorTable && (
                                                <p className={`${couponErrorTable ? style.invalidText : ''}`}>{couponErrorTable}</p>
                                            )}

                                        </div>
                                    </div>
                                ) : (
                                    <div className="row mt-2">
                                        <div className="col-sm-12 col-12">
                                            {!cartTotalPriceCoupon && (
                                                <Formik initialValues={{ coupon: '' }} onSubmit={handleSubmitCouponSelf}>
                                                    <Form>
                                                        <div className="row mb-2 mt-2">
                                                            <div className={`col-sm-12 col-12 ${style['coupon-form']}`} >
                                                                <Field className={`input-text me-3 ${style['flex-2']} ${couponErrorSelf ? style.invalid : ''} `} type="text" name="coupon" placeholder={trans('cart.coupon_code')}
                                                                    onInput={(e: any) => {
                                                                        const uppercaseValue = ("" + e.target.value).toUpperCase();
                                                                        e.target.value = uppercaseValue;
                                                                        setCurrentInput(uppercaseValue);
                                                                        setCouponErrorSelf('');
                                                                    }}
                                                                    onKeyUp={handleDisable}
                                                                />
                                                                <button className={`text-uppercase itr-btn-primary ${style['flex-1']} ${isDisabled ? style.disabled : ''}`} type="submit">{trans('cart.apply_coupon')}</button>
                                                            </div>
                                                        </div>
                                                    </Form>
                                                </Formik>
                                            )}

                                            {couponErrorSelf && (
                                                <p className={`${couponErrorSelf ? style.invalidText : ''}`}>{couponErrorSelf}</p>
                                            )}
                                        </div>
                                    </div>
                                )}

                                <div className="row mb-3">
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
                                <div className="row">
                                    <div className="col-sm-12 col-12 text-center">
                                        {(productOptionNotExist.length == 0 && !_.isEmpty(cart)) ? (
                                            <button className="itr-btn-primary-bold text-uppercase" type="button" onClick={togglePopup}>{trans('cart.further')}</button>
                                        ) : (
                                            <button className="itr-btn-primary-bold text-uppercase btn-disabled" type="button" disabled>{trans('cart.further')}</button>
                                        )}
                                    </div>
                                </div>
                                {(productOptionNotExist.length > 0 && !_.isEmpty(cart)) && (
                                    <>
                                        {productCategoryNotAvailable == 1 ? (
                                            <p className="text-center error mt-3">{trans('cart.product_not_available')}</p>
                                        ) : (
                                            <p className="text-center error mt-3">{trans('cart.product_option_not_available')}</p>
                                        )}
                                    </>
                                )}
                            </>
                        }
                        {/* {origin == 'table_ordering' ? (
                            <div className={`${style.messaging} row mt-4`}>
                                {(isVisibleTable && couponErrorButtonTable) && (
                                    <div className={`${style.message} d-flex flex-column align-items-center justify-content-center`}>
                                        <p className={style.messageText}>
                                            {couponErrorButtonTable ? couponErrorButtonTable : ''}
                                        </p>
                                    </div>
                                )}
                            </div>
                        ) : (
                            <div className={`${style.messaging} row mt-4`}>
                                {(isVisibleSelf && couponErrorButtonSelf) && (
                                    <div className={`${style.message} d-flex flex-column align-items-center justify-content-center`}>
                                        <p className={style.messageText}>
                                            {couponErrorButtonSelf ? couponErrorButtonSelf : ''}
                                        </p>
                                    </div>
                                )}
                            </div>
                        )} */}
                    </div>
                    {isListTypeOpen &&
                        <ListType toggleOrderType={() => toggleOrderType()} />
                    }

                    {
                        origin == 'self_ordering' && (
                            <div className={`${style.messaging} row mt-4`}>
                                <div className="col-sm-12 col-12">
                                    <div className={style.steps}>
                                        <div className={style['step-item']} onClick={() => activeStep(1)}>
                                            <div className={style['step-number']} style={window.innerWidth < 1280 ? { color: color, borderColor: color } : { background: color }}>1</div>
                                            <div className={style['step-name']} style={{ color: color }}>{trans('cart.step_overview')}</div>
                                        </div>
                                        <div className={style['step-item']}>
                                            <div className={style['step-number']}>2</div>
                                            <div className={style['step-name']} >{trans('cart.facts')}</div>
                                        </div>
                                        <div className={style['step-item']}>
                                            <div className={style['step-number']}>3</div>
                                            <div className={style['step-name']}>{trans('cart.step_payment_method')}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        )
                    }
                </>
            )}
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
                                    onClick={() => { removeProduct(removeIndex ?? 0) }}>
                                    {trans('yes-remove')}
                                </div>
                                <div
                                    data-type="button"
                                    className={styleModal['btn-no-logout']}
                                    data-bs-dismiss="modal"
                                    style={{ color: color }}
                                > {trans('no-cancel')} </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <ToastContainer />
        </>
    )
}

export default memo(OrderOverview)
