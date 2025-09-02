"use client"

import style from 'public/assets/css/product.module.scss'
import _ from 'lodash'
import { currency } from '@/config/currency'
import { useAppDispatch } from '@/redux/hooks'
import { setOptionsStoreData } from '@/redux/slices/optionsStoreSlice'
import React, { memo, useRef, createRef , useEffect } from 'react';
import Cookies from 'js-cookie';
import { addStepTable , addStepCategory , addStepRoot } from '@/redux/slices/cartSlice'
import Image from "next/image";

function ProductOptionsMobile(props: any) {
    let {
        product,
        productOptions,
        optionItemsStore,
        optionItemsPrice,
        optionRequired,
        addToCartNumber,
        addToCartPrice,
        baseCartPrice,
        showMore,
        toggelShowMore,
        color,
        trans,
        generateOptionItemStyle,
        calculatePriceOption,
        handleAddToCartNumber,
        inputAddToCartNumberRef,
        handleAddToCart,
        origin
    } = props
    const dispatch = useAppDispatch()
    const buttonRef:any = useRef();
    const invalidMessageRefs = useRef([])

    invalidMessageRefs.current = productOptions?.data?.map((option: any, i: any) => {
        if((option.type == 1 && option.min > 0 && optionRequired == true && ( optionItemsStore && optionItemsStore[option.id] ? optionItemsStore[option.id].length : 0) < option.min)) {
            return invalidMessageRefs.current[i] ?? createRef()
        }
    });

    const executeScroll = () => {
        setTimeout(() => {
            if(invalidMessageRefs?.current?.length > 0) {
                const firstElement: any = _.find(invalidMessageRefs?.current, function (x: any) {
                    return x != undefined
                })
    
                if(firstElement && firstElement?.current) {
                    firstElement.current.scrollIntoView({ behavior: 'smooth' })
                }
            }
        }, 100)
    }

    useEffect(() => {
        if(Cookies.get('groupOrder') == 'true' && buttonRef.current && optionItemsStore && Object.keys(optionItemsStore).length > 0) {
            buttonRef.current.click();
            Cookies.remove('groupOrder')
        }
    }, [Cookies.get('groupOrder') , optionItemsStore])
    
    return (
        <>  
            <div className="row ps-2 pe-2">
                <div className="col-sm-12 col-xs-12">
                    <div className={style.options}>
                        {productOptions.data.map((option: any, i: number) => (
                            <div key={'option-' + option.id}>
                                <div className={style['option-title']}>
                                    <h4 className={style.title}>
                                        <strong>{option.name} </strong>
                                        {
                                            (option.type == 1 && option.min > 0 && optionRequired == true && (optionItemsStore && optionItemsStore[option.id] ? optionItemsStore[option.id].length : 0) < option.min) ? (
                                                <span className={`${style['validate-min-max']}`}
                                                    ref={invalidMessageRefs.current[i]}>
                                                    ({trans('choose_at_least_option', {number: option.min})})
                                                </span>
                                            ) : (
                                                <span>({ option.type == 1 ? trans('valid') : trans('optional') })</span>
                                            )
                                        }
                                    </h4>
                                    { (optionItemsPrice[option.id] > 0 || optionItemsPrice[option.id] < 0) && 
                                        <div className={style['price-container']}>
                                            <span className="currency">{currency}</span>
                                            <span className="price">{_.round(optionItemsPrice[option.id], 2).toFixed(2)}</span>
                                        </div>
                                    }                                    
                                </div>
                                <div className="row">
                                    <div className="col-sm-12 col-xs-12">
                                        {option.max > 0 ? (
                                            <>
                                                {option.items.map((optionItem: any, i: number) => (
                                                    optionItem.available == true &&
                                                    <div className={(i >= 9 && !_.find(showMore, {id:optionItem.id}) ? 'visually-hidden ' : '') + `${style['option-items']} ` + (option.is_ingredient_deletion == true && optionItemsStore[option.id] && _.find(optionItemsStore[option.id], optionItem) ? style['disabled-item'] : '')}
                                                        key={'option-item-' + optionItem.id}
                                                        style={generateOptionItemStyle(option.id, optionItem)}
                                                        onClick={() => calculatePriceOption(option, optionItem, product.data.price)}>
                                                        {optionItem.name}
                                                    </div>
                                                ))}
                                            </>
                                        ) : (
                                            <>
                                                {option.items.map((optionItem: any, i: number) => (
                                                    optionItem.available == true &&
                                                    <div className={(i >= 9 && !_.find(showMore, {id:optionItem.id}) ? 'visually-hidden ' : '') + `${style['option-items']} ` + (option.is_ingredient_deletion == true && optionItemsStore[option.id] && _.find(optionItemsStore[option.id], optionItem) ? style['disabled-item'] : '')}
                                                        key={'option-item-' + optionItem.id}
                                                        style={generateOptionItemStyle(option.id, optionItem)}>
                                                        {optionItem.name}
                                                    </div>
                                                ))}
                                            </>
                                        )}
                                        
                                        { (option.items?.length > 9 && !_.find(showMore, {id:option.items[9].id})) &&
                                            <div className="show-more-toggle text-center"
                                                style={{color: color}}
                                                onClick={() => toggelShowMore(0, option.items)}>
                                                <span>{trans('show_more')}</span>
                                                <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 15 15" fill="none">
                                                    <path d="M3.75 5.625L7.5 9.375L11.25 5.625" stroke={color} strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"/>
                                                </svg>
                                            </div>
                                        }   
                                        { (option.items?.length > 9 && _.find(showMore, {id:option.items[9].id})) &&
                                            <div className="show-more-toggle text-center"
                                                style={{color: color}}
                                                onClick={() => toggelShowMore(1, option.items)}>
                                                <span>{trans('show_less')}</span>
                                                <svg xmlns="http://www.w3.org/2000/svg" width="11" height="7" viewBox="0 0 11 7" fill="none">
                                                    <path d="M9.25 5.375L5.5 1.625L1.75 5.375" stroke={color} strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"/>
                                                </svg>
                                            </div>
                                        }                                        
                                    </div>
                                </div>
                            </div>
                        ))}
                    </div>
                </div>
            </div>
            <div className={`${style['add-to-cart']} row text-center mt-5 ps-2 pe-2`}>
                <div className="col-sm-6 col-6">
                    <div className={style['minute-content']}>                        
                        <div className={style.decrease}
                            onClick={() => handleAddToCartNumber(-1)}>
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="32" viewBox="0 0 24 32" fill="none">
                                <path d="M5.48096 15.803H18.6733" stroke={color ? color : '#F6B545'} strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" />
                            </svg>
                        </div>
                        <div className={style['total-number']}>
                            <div className={style['saparate-line']}></div>
                            <input type="number" 
                                ref={inputAddToCartNumberRef}
                                className={style.number} 
                                defaultValue={addToCartNumber}
                                onChange={(e) => handleAddToCartNumber(e.target.value ? parseInt(e.target.value) : 0, 'manual')}
                                onBlur={(e) => handleAddToCartNumber(e.target.value ? parseInt(e.target.value) : 1, 'manual')}
                            />
                            <div className={style['saparate-line']}></div>
                        </div>
                        <div className={style.increase}
                            onClick={() => handleAddToCartNumber(1)}>
                            <svg xmlns="http://www.w3.org/2000/svg" width="25" height="26" viewBox="0 0 25 26" fill="none">
                                <path d="M12.1152 5.5144V20.3914" stroke={color ? color : '#F6B545'} strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" />
                                <path d="M5.04834 12.9529H19.183" stroke={color ? color : '#F6B545'} strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" />
                            </svg>
                        </div>
                    </div>
                </div>
                <div className="col-sm-6 col-6">
                    <div className={style['cart-money']}>
                        <div ref={buttonRef} className={style['cart-money-wrapper']}
                             onClick={() => {
                                 handleAddToCart(baseCartPrice);
                                 dispatch(setOptionsStoreData(optionItemsStore));
                                 dispatch(addStepRoot(1));
                                 dispatch(addStepTable(1));
                                 dispatch(addStepCategory(1));
                                 executeScroll()
                             }}>
                            <Image src={'/img/add-to-cart.svg'} alt={'add to cart'} height={21} width={21}/>
                            <span>{currency}</span>
                            <span>{_.round(addToCartPrice, 2).toFixed(2)}</span>
                        </div>
                    </div>
                </div>
            </div>
        </>
    )
}

export default memo(ProductOptionsMobile)