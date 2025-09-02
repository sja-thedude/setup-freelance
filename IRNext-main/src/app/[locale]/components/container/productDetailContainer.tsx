"use client"

import {useState, memo, useEffect, useRef, useCallback} from 'react'
import { useI18n } from '@/locales/client'
import _ from 'lodash'
import { useAppSelector, useAppDispatch } from '@/redux/hooks'
import {
    addToCart, addStepRoot, changeInCart, toggleAddToCartSuccess, changeTypeFlag, changeType
    , rootAddToCart, rootChangeInCart, rootToggleAddToCartSuccess, changeRootCartTmp, changeRootCartItemTmp
    , manualChangeOrderTypeDesktop, selfOrderingAddToCart, selfOrderingChangeInCart, addStepSelfOrdering
} from '@/redux/slices/cartSlice'
import Cookies from 'js-cookie';
import ProductOptionsMobile from '@/app/[locale]/components/presentational/productOptionsMobile'
import ProductOptionsDesktop from '@/app/[locale]/components/presentational/productOptionsDesktop'
import { api } from "@/utils/axios";

function ProductDetailContainer(props?: any) {
    let {
        color,
        product,
        productOptions,
        cartType,
        closePopupProductDetail,
        origin,
        setHoliday,
        workspaceInfo,
        workspaceId,
        handleCloseMobilePopup
    } = props

    const language = Cookies.get('Next-Locale') ?? 'nl';

    type itemStore = {
        [key: number]: Array<any>
    }

    type itemPrice = {
        [key: number]: number
    }

    const trans = useI18n()
    const dispatch = useAppDispatch()
    let cart: any = []
    let cartUserWebsite = useAppSelector((state) => state.cart.rootData)
    let cartTableOrdering = useAppSelector((state) => state.cart.data)
    let cartSelfOrdering = useAppSelector((state) => state.cart.selfOrderingData)
    let optionSlice: any = useAppSelector((state) => state.optionStore.data);
    let addToCartRedux: any = addToCart
    let changeInCartRedux: any = changeInCart
    let toggleAddToCartSuccessRedux: any = toggleAddToCartSuccess

    if (!workspaceId && workspaceInfo) {
        workspaceId = workspaceInfo.id
    }

    if (cartType == 'user_website') {
        cart = cartUserWebsite
        addToCartRedux = rootAddToCart
        changeInCartRedux = rootChangeInCart
        toggleAddToCartSuccessRedux = rootToggleAddToCartSuccess
    } else if (cartType == 'self_ordering') {
        cart = cartSelfOrdering
        addToCartRedux = selfOrderingAddToCart
        changeInCartRedux = selfOrderingChangeInCart
    } else {
        cart = cartTableOrdering
    }

    let rootType = useAppSelector((state) => state.cart.type)
    const [addToCartPrice, setAddToCartPrice] = useState<number>(0)
    const [baseCartPrice, setBaseCartPrice] = useState<number>(0)
    const [addToCartNumber, setAddToCartNumber] = useState<number>(1)
    const [optionItemsStore, setOptionItemsStore] = useState<itemStore>({})
    const [originOptionItemsStore, setOriginOptionItemsStore] = useState<itemStore>({})
    const [optionItemsPrice, setOptionItemsPrice] = useState<itemPrice>({})
    const [showMore, setShowMore] = useState([])
    const [optionRequired, setOptionRequired] = useState<boolean>(false)
    const inputAddToCartNumberRef = useRef<HTMLInputElement>(null)
    const query = new URLSearchParams(window.location.search);
    const from = query.get('from');

    useEffect(() => {
        setAddToCartPrice(product?.data.price)
        setBaseCartPrice(product?.data.price)
        dispatch(changeTypeFlag(false))
    }, [
        product?.data.price,
        dispatch
    ])

    const fetchHolidayData = () => {
        return new Promise((resolve, reject) => {
            const tokenLoggedInCookie = Cookies.get('loggedToken');
            if (workspaceId) {
                api.get(`workspaces/${workspaceId}/settings/holiday_exceptions`, {
                    headers: {
                        'Authorization': `Bearer ${tokenLoggedInCookie}`,
                        'Content-Language': language
                    }
                }).then(res => {
                    const json = res.data;
                    const currentTime = new Date();
                    const isInTimeRange = json.data.some(({ end_time, start_time }: { end_time: string; start_time: string }) => {
                        const startTime = new Date(start_time);
                        const endTime = new Date(end_time);
                        return currentTime >= startTime && currentTime <= endTime;
                    });
                    if (isInTimeRange) {
                        resolve({
                            status: true,
                            data: json.data,
                        });
                    } else {
                        resolve(null);
                    }
                }).catch(error => {
                    console.error("Error fetching holiday data", error);
                    reject(error);
                });
            } else {
                reject(new Error("Workspace ID is not available"));
            }
        });
    };

    const handleAddToCartNumber = (value: number, type?: string) => {
        type = type ? type : 'auto'
        let number = addToCartNumber + value

        if (type == 'manual') {
            number = value
        }

        if (number < 1) {
            number = 1
        }

        setAddToCartNumber(number)

        if (inputAddToCartNumberRef.current != null) {
            inputAddToCartNumberRef.current.value = number.toString()
        }

        setAddToCartPrice(number * baseCartPrice)
    }

    const calculatePriceOption = (option: any, optionItem: any, productPrice: number) => {
        const optionId = option.id
        let originOptionItemsStoreClone: itemStore = _.cloneDeep(originOptionItemsStore);
        let optionItemsStoreClone: itemStore = _.cloneDeep(optionItemsStore);
        let optionItemsPriceClone: itemPrice = _.cloneDeep(optionItemsPrice);
        let optionItemsClone = [...option.items]
        _.remove(optionItemsClone, { master: true })
        _.remove(optionItemsClone, { available: false })

        // BEGIN: calculate priority to show        
        if (_.find(originOptionItemsStoreClone[optionId], optionItem)) {
            _.remove(originOptionItemsStoreClone[optionId], optionItem)
        } else {
            const hasMaster = _.find(originOptionItemsStoreClone[optionId], { master: true })

            if ((originOptionItemsStoreClone[optionId] ? originOptionItemsStoreClone[optionId].length : 0) >= option.max && option.max > 0 && optionItem.master != true) {
                originOptionItemsStoreClone[optionId].pop()
            }

            if (optionItem.master == true) {
                originOptionItemsStoreClone[optionId] ? (originOptionItemsStoreClone[optionId]).push(optionItem) : originOptionItemsStoreClone[optionId] = [optionItem]
            } else {
                if (!hasMaster && (originOptionItemsStoreClone[optionId] ? originOptionItemsStoreClone[optionId].length : 0) < option.max) {
                    originOptionItemsStoreClone[optionId] ? (originOptionItemsStoreClone[optionId]).push(optionItem) : originOptionItemsStoreClone[optionId] = [optionItem]
                }
            }
        }

        originOptionItemsStoreClone[optionId] = originOptionItemsStoreClone[optionId] ?? []
        let originOptionItemWithoutMaster = [...originOptionItemsStoreClone[optionId]]
        _.remove(originOptionItemWithoutMaster, { master: true })

        if (optionItem.master == true) {
            if (optionItemsStoreClone[optionId] && optionItemsStoreClone[optionId].length == option.items.length) {
                optionItemsStoreClone[optionId] = []
                originOptionItemsStoreClone[optionId] = []
            } else {
                optionItemsStoreClone[optionId] = option.items
            }
        } else {
            if (originOptionItemWithoutMaster.length == optionItemsClone.length) {
                optionItemsStoreClone[optionId] = option.items
            } else if (_.find(originOptionItemsStoreClone[optionId], { master: true })) {
                _.remove(originOptionItemsStoreClone[optionId], { master: true })

                if (optionItemsStoreClone[optionId] && optionItemsStoreClone[optionId].length == option.items.length) {
                    _.remove(originOptionItemsStoreClone[optionId], optionItem)
                }

                optionItemsStoreClone[optionId] = originOptionItemsStoreClone[optionId]
            } else {
                // Case normaly
                optionItemsStoreClone[optionId] = originOptionItemsStoreClone[optionId]
                _.remove(originOptionItemsStoreClone[optionId], { master: true })
            }
        }

        setOriginOptionItemsStore({ ...originOptionItemsStoreClone })
        setOptionItemsStore({ ...optionItemsStoreClone })

        // END: calculate priority to show

        // BEGIN: calculate price
        const master = _.find(optionItemsStoreClone[optionId], { master: true })

        if (master) {
            optionItemsPriceClone[optionId] = _.toNumber(master.price)
        } else {
            const convertStrToNumber = _.map(optionItemsStoreClone[optionId], 'price').map(i => Number(i))
            optionItemsPriceClone[optionId] = _.sum(convertStrToNumber)
        }

        setOptionItemsPrice({ ...optionItemsPriceClone })
        const newBaseCartPrice = _.toNumber(productPrice) + _.sum(_.values(optionItemsPriceClone))
        setAddToCartPrice(addToCartNumber * newBaseCartPrice)
        setBaseCartPrice(newBaseCartPrice)
        // END: calculate price
    }

    const handleAddToCart = useCallback((price: number) => {
        let flag = true
        let optionItemsStoreSort: any = []

        setOptionRequired(true)

        productOptions.data.map((option: any) => {
            const numberOfOptionItems = (optionItemsStore[option.id] ? optionItemsStore[option.id].length : 0)
            const master = _.find(optionItemsStore[option.id], { master: true })

            if (option.type == 1 && option.min > 0 && (numberOfOptionItems < option.min || (numberOfOptionItems > option.max && !master))) {
                flag = false
            }
        })

        if (flag) {
            if (origin === 'desktop_popup') {
                closePopupProductDetail()
            }

            productOptions.data.map((option: any) => {
                if (optionItemsStore[option.id]) {
                    optionItemsStoreSort.push({
                        optionId: option.id,
                        optionItems: _.orderBy(optionItemsStore[option.id], ['order'], ['asc'])
                    })
                }
            })

            let existFlag = false
            let cartClone = [...(cart || [])];

            if (cartClone.length) {
                _.map(cartClone, (itemOriginal: any, key: number) => {
                    let item = { ...itemOriginal }
                    let subFlag = false

                    if (item.productId == product.data.id) {
                        if (_.isEmpty(optionItemsStore) && _.isEmpty(item.optionItemsStore)) {
                            existFlag = true
                            subFlag = true
                        } else {
                            let cloneOptionItemsStoreSort = _.cloneDeep(optionItemsStoreSort)
                            let cloneItemOptionItemsStore = _.cloneDeep(item.optionItemsStore)

                            cloneOptionItemsStoreSort = _.map(cloneOptionItemsStoreSort, (itemOptions: any) => {
                                itemOptions.optionItems = _.map(itemOptions.optionItems, (optionItem: any) => {
                                    optionItem = _.omit(optionItem, ['name', 'order', 'price'])

                                    return optionItem
                                })

                                itemOptions.optionItems = _.orderBy(itemOptions.optionItems, ['id'], ['asc'])

                                return itemOptions
                            })

                            cloneItemOptionItemsStore = _.map(cloneItemOptionItemsStore, (itemOptions: any) => {
                                itemOptions.optionItems = _.map(itemOptions.optionItems, (optionItem: any) => {
                                    optionItem = _.omit(optionItem, ['name', 'order', 'price'])

                                    return optionItem
                                })

                                itemOptions.optionItems = _.orderBy(itemOptions.optionItems, ['id'], ['asc'])

                                return itemOptions
                            })

                            if (_.isEqual(_.sortBy(cloneOptionItemsStoreSort, ['optionId']), _.sortBy(cloneItemOptionItemsStore, ['optionId']))) {
                                existFlag = true
                                subFlag = true
                                item.optionItemsStore = _.cloneDeep(optionItemsStoreSort)
                            }
                        }

                        if (subFlag) {
                            item.productTotal = _.toNumber(item.productTotal) + _.toNumber(addToCartNumber)
                            cartClone[key] = item
                        }
                    }
                })
            }

            const cartData = {
                productId: product.data.id,
                product: product,
                productTotal: addToCartNumber,
                productOptions: productOptions,
                basePrice: price,
                optionItemsStore: optionItemsStoreSort
            }

            if (cartType == 'user_website' && (rootType === 0 || _.isEmpty(cart))) {
                dispatch(changeType(0))
                dispatch(changeTypeFlag(true))
                dispatch(changeRootCartItemTmp(cartData))
                dispatch(changeRootCartTmp([cartData]))

                if (origin === 'desktop_popup') {
                    if (!Cookies.get('loggedToken') && !_.isEmpty(cart)) {
                        dispatch(manualChangeOrderTypeDesktop(false))
                    } else {
                        dispatch(manualChangeOrderTypeDesktop(true))
                    }

                }
            } else {
                if (!existFlag) {
                    if (cartData) {
                        dispatch(addToCartRedux(cartData))
                    }
                } else {
                    dispatch(changeInCartRedux(cartClone))
                }

                dispatch(toggleAddToCartSuccessRedux())

                if (origin == 'desktop_popup') {
                    if(Cookies.get('fromSuggestDesk') == 'true'){
                        Cookies.set('addProductSuggest', 'true')
                    }
                    if (window.location.href.includes('groupOrder=')) {
                        // history.pushState({groupOrder: query.get("groupOrder"), activeStep: 1}, "show profile",
                        //     `?groupOrder=${query.get("groupOrder")}&activeStep=1`);
                        // router.push(`?groupOrder=${query.get("groupOrder")}&activeStep=1`)
                        dispatch(addStepRoot(1))
                    } else {
                        dispatch(addStepRoot(1))
                    }
                }

                if (from == 'productSuggestion') {
                    Cookies.set(from, 'true');
                }

                if (origin !== 'desktop_popup') {
                    if(Cookies.get('productSuggestion') == 'true'){
                        Cookies.set('addProductSuggest', 'true')
                    }
                    if (handleCloseMobilePopup) {
                        handleCloseMobilePopup()
                    }
                }
                Cookies.remove('oppenedSuggest')
            }
        }

        const showPopUpHoliday = sessionStorage.getItem('showPopUpHoliday');

        if (showPopUpHoliday !== 'true') {
            fetchHolidayData()
                .then(() => {
                    if (setHoliday) {
                        setHoliday(false);
                    }

                    sessionStorage.setItem('showPopUpHoliday', 'true');
                    flag = false;
                })
                .catch(error => {
                    console.error("Error fetching holiday data", error);
                });
        }

        if (cartType == 'self_ordering') {
            dispatch(addStepSelfOrdering(1))
        }
    },[addToCartNumber, addToCartRedux, cart, cartType, changeInCartRedux, closePopupProductDetail, dispatch, fetchHolidayData, from, handleCloseMobilePopup, optionItemsStore, origin, product, productOptions, rootType, setHoliday, toggleAddToCartSuccessRedux])

    useEffect(() => {
        if (Cookies.get('groupOrder') == 'true') {
            setOptionItemsStore(optionSlice)
            handleAddToCart(baseCartPrice)
        }
    }, [baseCartPrice, handleAddToCart, optionSlice])

    const toggelShowMore = (type: number, optionItems: any) => {
        let showMoreClone = [...showMore]

        if (type == 0) {
            // Show more
            showMoreClone = _.merge(showMoreClone, optionItems)
        } else {
            // Show less
            showMoreClone = _.differenceBy(showMoreClone, optionItems, 'id')
        }

        setShowMore([...showMoreClone])
    }

    const generateOptionItemStyle = (optionId: number, optionItem: any) => {
        let result = {}
        if (optionItemsStore) {
            if (origin === 'desktop_popup') {
                result = optionItemsStore[optionId] && _.find(optionItemsStore[optionId], optionItem) ? { backgroundColor: color, color: '#ffffff' } : {}
            } else {
                result = optionItemsStore[optionId] && _.find(optionItemsStore[optionId], optionItem) ? { backgroundColor: color, borderColor: color, color: '#ffffff' } : { borderColor: color, color: color }
            }
        }
        return result
    }

    return (
        <>
            {origin === 'desktop_popup' ? (
                <ProductOptionsDesktop
                    product={product}
                    productOptions={productOptions}
                    optionItemsStore={optionItemsStore}
                    optionItemsPrice={optionItemsPrice}
                    optionRequired={optionRequired}
                    addToCartNumber={addToCartNumber}
                    addToCartPrice={addToCartPrice}
                    baseCartPrice={baseCartPrice}
                    showMore={showMore}
                    toggelShowMore={toggelShowMore}
                    color={color}
                    trans={trans}
                    generateOptionItemStyle={generateOptionItemStyle}
                    calculatePriceOption={calculatePriceOption}
                    handleAddToCartNumber={handleAddToCartNumber}
                    inputAddToCartNumberRef={inputAddToCartNumberRef}
                    handleAddToCart={handleAddToCart}
                />
            ) : (
                <ProductOptionsMobile
                    product={product}
                    productOptions={productOptions}
                    optionItemsStore={optionItemsStore}
                    optionItemsPrice={optionItemsPrice}
                    optionRequired={optionRequired}
                    addToCartNumber={addToCartNumber}
                    addToCartPrice={addToCartPrice}
                    baseCartPrice={baseCartPrice}
                    showMore={showMore}
                    toggelShowMore={toggelShowMore}
                    color={color}
                    trans={trans}
                    generateOptionItemStyle={generateOptionItemStyle}
                    calculatePriceOption={calculatePriceOption}
                    handleAddToCartNumber={handleAddToCartNumber}
                    inputAddToCartNumberRef={inputAddToCartNumberRef}
                    handleAddToCart={handleAddToCart}
                />
            )}
        </>
    )
}

export default memo(ProductDetailContainer)