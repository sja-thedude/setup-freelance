'use client'

import { memo } from 'react'
import ProductDetailContainer from '@/app/[locale]/components/container/productDetailContainer'
import style from 'public/assets/css/product.module.scss'
import LabelList from '@/app/[locale]/components/ordering/labels/list';
import AllergenenList from '@/app/[locale]/components/ordering/allergenens/list';
import BackButton from '@/app/[locale]/components/layouts/ordering/backButton'

function SelfOrderingDetail(props?: any) {
    let {
        isLoadingProduct,
        isLoadingProductOptions,
        product,
        color,
        productId,
        productOptions,
        handleClose
    } = props

    return (
        <>
            {isLoadingProduct === false && isLoadingProductOptions === false && (
                <div>
                    <div className="row overflow-hidden">
                        <div className="col-sm-12 col-xs-12">
                            <div className={style['product-image']} style={product?.data && product?.data?.photo != null ? { backgroundImage: `url(${product.data.photo})`, height: '175px' , backgroundPosition: 'center' } : { backgroundColor: color, minHeight: '56px' }}>
                                <div className={`ps-3 pe-3 ${style.labels}`}>
                                    <LabelList labels={product?.data?.labels} color={color} />
                                </div>
                            </div>
                        </div>
                    </div>
                    <div className={`${style['product-detail']} overflow-hidden`}>
                        <div className="row ps-2 pe-2">
                            <div className="col-sm-12 col-xs-12">
                                <div className="float-start">
                                    <h1 className={`${style['product-title']} mt-3`}>
                                        {product?.data?.name}
                                    </h1>
                                </div>
                            </div>
                        </div>
                        <div className="row ps-2 pe-2">
                            <div className="col-sm-12 col-xs-12">
                                <p className={style['product-description']}>{product?.data?.description}</p>
                            </div>
                        </div>
                        <div className="row ps-2 pe-2">
                            <div className="col-sm-12 col-xs-12">
                                <div className={style.allergenens}>
                                    <AllergenenList allergenens={product?.data?.allergenens} />
                                </div>
                            </div>
                        </div>
                        <ProductDetailContainer color={color}
                            product={product}
                            productOptions={productOptions}
                            cartType="self_ordering"
                            handleCloseMobilePopup={handleClose} />
                        <div className={`${style['close-page']} row text-center ps-2 pe-2`}>
                            <div className="col-sm-12">
                                <BackButton id={productId} handleCloseMobilePopup={handleClose} />
                            </div>
                        </div>
                    </div>
                </div>
            )}
        </>
    )
}

export default memo(SelfOrderingDetail)