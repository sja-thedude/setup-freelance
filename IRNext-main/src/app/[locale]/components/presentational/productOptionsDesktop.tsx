"use client"

import style from 'public/assets/css/product_desktop.module.scss'
import { memo, useRef, createRef } from 'react'
import _ from 'lodash'
import { currency } from '@/config/currency'

function ProductOptionsDesktop(props: any) {
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
        handleAddToCart
    } = props

    const filteredProductOptions: any[] = productOptions?.data?.length > 0 ? 
    productOptions.data.map((option: any) => ({
        ...option,
        items: option.items.filter((item: any) => item.available !== false)
    })) 
    : [];    
    
    const invalidMessageRefs = useRef([])
    invalidMessageRefs.current = productOptions?.data?.map((option: any, i: any) => {
        if ((option.type == 1 && option.min > 0 && optionRequired == true && (optionItemsStore[option.id] ? optionItemsStore[option.id].length : 0) < option.min)) {
            return invalidMessageRefs.current[i] ?? createRef()
        }
    });

    const executeScroll = () => {
        setTimeout(() => {
            if (invalidMessageRefs?.current?.length > 0) {
                const firstElement: any = _.find(invalidMessageRefs?.current, function (x: any) {
                    return x != undefined
                })

                if (firstElement && firstElement?.current) {
                    firstElement.current.scrollIntoView({ block: 'center' })
                }
            }
        }, 100)
    }

    return (
        <>
            {filteredProductOptions?.length > 0 && (
                <div className="row">
                    <div className="col-12 position-relative">
                        {filteredProductOptions.map((option: any, i: number) => (
                            <div key={'option-' + option.id}>
                                <div className={style['option-title']}>
                                    <h4 className={style.title}>
                                        <strong>{option.name} </strong>
                                        <span>({option.type == 1 ? trans('valid') : trans('optional')})</span>
                                        {
                                            (option.type == 1 && option.min > 0 && optionRequired == true && (optionItemsStore[option.id] ? optionItemsStore[option.id].length : 0) < option.min) && (
                                                <span className={`${style['validate-min-max']}`}
                                                    ref={invalidMessageRefs.current[i]}>
                                                    {trans('choose_at_least_option', { number: option.min })}
                                                </span>
                                            )
                                        }
                                    </h4>
                                    {(optionItemsPrice[option.id] > 0 || optionItemsPrice[option.id] < 0) &&
                                        <div className={style['price-container']}
                                            style={(option.type == 1 && option.min > 0 && optionRequired == true && (optionItemsStore[option.id] ? optionItemsStore[option.id].length : 0) < option.min) ? { color: '#E03009' } : {}}>
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
                                                    <div className={(i >= 9 && !_.find(showMore, { id: optionItem.id }) ? 'visually-hidden ' : '') + `${style['option-items']} ` + (option.is_ingredient_deletion == true && optionItemsStore[option.id] && _.find(optionItemsStore[option.id], optionItem) ? style['disabled-item'] : '')}
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
                                                    <div className={(i >= 9 && !_.find(showMore, { id: optionItem.id }) ? 'visually-hidden ' : '') + `${style['option-items']} ` + (option.is_ingredient_deletion == true && optionItemsStore[option.id] && _.find(optionItemsStore[option.id], optionItem) ? style['disabled-item'] : '')}
                                                        key={'option-item-' + optionItem.id}
                                                        style={generateOptionItemStyle(option.id, optionItem)}>
                                                        {optionItem.name}
                                                    </div>
                                                ))}
                                            </>
                                        )}

                                        {(option.items?.length > 9 && !_.find(showMore, { id: option.items[9].id })) &&
                                            <div className="show-more-toggle text-center"
                                                style={{ color: color }}
                                                onClick={() => toggelShowMore(0, option.items)}>
                                                <span>{trans('show_more')}</span>
                                            </div>
                                        }
                                        {(option.items?.length > 9 && _.find(showMore, { id: option.items[9].id })) &&
                                            <div className="show-more-toggle text-center"
                                                style={{ color: color }}
                                                onClick={() => toggelShowMore(1, option.items)}>
                                                <span>{trans('show_less')}</span>
                                            </div>
                                        }
                                    </div>
                                </div>
                            </div>
                        ))}
                    </div>
                </div>
            )}

            <div className="row">
                <div className={`${style['add-to-cart']} text-center pdlr-25`}>
                    <div className="col-6">
                        <div className={style['minute-content']}>
                            <div className={style.decrease}
                                onClick={() => handleAddToCartNumber(-1)}>
                                <svg xmlns="http://www.w3.org/2000/svg" width="21" height="6" viewBox="0 0 21 6" fill="none">
                                    <path d="M3 3H18" stroke={color ? color : '#F6B545'} strokeWidth="5" strokeLinecap="round" strokeLinejoin="round" />
                                </svg>
                            </div>
                            <div className={style['total-number']}>
                                <input type="number"
                                    ref={inputAddToCartNumberRef}
                                    className={style.number}
                                    defaultValue={addToCartNumber}
                                    onChange={(e) => handleAddToCartNumber(e.target.value ? parseInt(e.target.value) : 0, 'manual')}
                                    onBlur={(e) => handleAddToCartNumber(e.target.value ? parseInt(e.target.value) : 1, 'manual')}
                                />
                            </div>
                            <div className={style.increase}
                                onClick={() => handleAddToCartNumber(1)}>
                                <svg xmlns="http://www.w3.org/2000/svg" width="21" height="21" viewBox="0 0 21 21" fill="none">
                                    <path d="M10.5 3V18" stroke={color ? color : '#F6B545'} strokeWidth="5" strokeLinecap="round" strokeLinejoin="round" />
                                    <path d="M3 10.5H18" stroke={color ? color : '#F6B545'} strokeWidth="5" strokeLinecap="round" strokeLinejoin="round" />
                                </svg>
                            </div>
                        </div>
                    </div>
                    <div className={`col-6 ${style['total-price-flex']}`}>
                        <div className={style['total-price']}>
                            <span>{currency}</span>
                            <span>{_.round(addToCartPrice, 2).toFixed(2)}</span>
                        </div>
                        <div onClick={() => { handleAddToCart(baseCartPrice), executeScroll() }}
                            className={style['cart-money']}
                            style={{ backgroundColor: color }}>
                            <div className={style['cart-money-wrapper']}>
                                <svg xmlns="http://www.w3.org/2000/svg" width="21" height="21" viewBox="0 0 21 21" fill="none">
                                    <path d="M5.42529 1.42358L2.80029 4.92358V17.1736C2.80029 17.6377 2.98467 18.0828 3.31286 18.411C3.64104 18.7392 4.08616 18.9236 4.55029 18.9236H16.8003C17.2644 18.9236 17.7095 18.7392 18.0377 18.411C18.3659 18.0828 18.5503 17.6377 18.5503 17.1736V4.92358L15.9253 1.42358H5.42529Z" stroke="white" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" />
                                    <path d="M2.80029 4.30078H18.5503" stroke="white" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" />
                                    <path d="M14 7.97217C14 8.90043 13.6313 9.79066 12.9749 10.447C12.3185 11.1034 11.4283 11.4722 10.5 11.4722C9.57174 11.4722 8.6815 11.1034 8.02513 10.447C7.36875 9.79066 7 8.90043 7 7.97217" stroke="white" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" />
                                </svg>
                                <span className="text-uppercase">{trans('cart.add')}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>        
        </>
    )
}

export default memo(ProductOptionsDesktop)