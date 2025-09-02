"use client"

import { memo, useState, useRef, useEffect } from 'react'
import { Modal } from 'react-bootstrap'
import 'public/assets/css/modal.scss'
import ProductDetailContainer from '@/app/[locale]/components/container/productDetailContainer'
import AllergenenListDesk from '@/app/[locale]/components/ordering/allergenens/listDesk';
import FavoriteCard from '@/app/[locale]/components/product/favorite-card'
import style from 'public/assets/css/product.module.scss'
import { currency } from '@/config/currency'
import { manualChangeOrderTypeDesktop } from '@/redux/slices/cartSlice'
import { useAppDispatch } from '@/redux/hooks'
import LabelFavotiteListDesk from '../ordering/labels/favorite/deskList';
import styled from 'styled-components';
import Cookies from "js-cookie";

const CustomScrollbar = styled.div`
    overflow-y: auto;
    overflow-x: hidden;
    padding-top: 0;
    margin-bottom: 89px;
    padding-top: 0;  
    &::-webkit-scrollbar-thumb {
        background: ${props => props.color};
        width: 6px;
        height: 120px;
    }
    &::-webkit-scrollbar {
        width: 6px;
        height: 85px;
    }
    /* Handle on hover */
    &::-webkit-scrollbar-thumb:hover {
    }
`;

function ProductDetailPopup(props?: any) {
    const dispatch = useAppDispatch()
    const {
        showProductDetailPopup,
        setShowProductDetailPopup,
        color,
        productInfo,
        productId,
        setHoliday,
        workspaceInfo,
    } = props

    const handleClose = () => {
        setShowProductDetailPopup(false)
        dispatch(manualChangeOrderTypeDesktop(false))
    }

    const isLoadingProduct = false;
    const isLoadingProductOptions = false;
    const productOptions = {data: productInfo.options};
    const product = {data: productInfo};
    const tokenLoggedInCookie = Cookies.get('loggedToken');
    if (!tokenLoggedInCookie) {
        Cookies.set('currentProductId', productId);
    }

    const [heightImage, setHeightImage] = useState(0)
    const refImage = useRef<any>(null)
    const [heightTitle, setHeightTitle] = useState(0)
    const refTitle = useRef<any>(null)

    useEffect(() => {
        if(refImage?.current?.clientHeight) {
            setHeightImage(refImage.current.clientHeight)
        }
        if(refTitle?.current?.clientHeight) {
            setHeightTitle(refTitle.current.clientHeight)
        }
    })

    return (
        <>
            <Modal className="product-detail-modal"
                size="lg"
                show={showProductDetailPopup}
                onHide={handleClose}
                aria-labelledby="contained-modal-title-vcenter"
                centered>
                <Modal.Body>
                    {isLoadingProduct === false && isLoadingProductOptions === false && (
                        <>
                            {product?.data && product.data.photo != null && (
                                <div ref={refImage} className="row need-calculate-height">
                                    <div className="col-12">
                                        <div className="product-image" style={product.data && product.data.photo != null ? { backgroundImage: `url(${product.data.photo})`, height: '213px', backgroundPosition: 'center' } : {}}>
                                            <svg onClick={() => handleClose()}
                                                className="close-product-popup"
                                                xmlns="http://www.w3.org/2000/svg"
                                                width="36"
                                                height="36"
                                                viewBox="0 0 36 36"
                                                fill="none">
                                                <circle cx="18" cy="18" r="18" fill="white" />
                                                <path d="M24 12L12.5454 23.4545" stroke="#404040" strokeWidth="4" strokeLinecap="round" strokeLinejoin="round" />
                                                <path d="M12.5454 12L24 23.4545" stroke="#404040" strokeWidth="4" strokeLinecap="round" strokeLinejoin="round" />
                                            </svg>
                                        </div>
                                    </div>
                                </div>
                            )}
                            {/* Image, favorite, price  */}
                            <div ref={refTitle} 
                                className="product-detail-header sticky need-calculate-height pdlr-25" 
                                style={{ backgroundColor: productOptions?.data?.length > 0 ? '#F5F5F5' : '#FFFFFF' }}>
                                {product?.data && product.data.photo == null && (
                                    <div style={{ height: '50px' }}>
                                        <div style={{ position: 'absolute', left: '0', background: productOptions?.data?.length > 1 ? '#F5F5F5' : '', width: '100%', minHeight: '50px', zIndex: '998' }} ></div>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" viewBox="0 0 36 36" fill="none" 
                                            className={style.quitting} 
                                            onClick={handleClose} 
                                            style={{ position: 'absolute', top: '10px', right: '25px', zIndex: '999' }}>
                                            <circle cx="18" cy="18" r="18" fill="white" />
                                            <path d="M24 12L12.5454 23.4545" stroke="#404040" strokeWidth="4" strokeLinecap="round" strokeLinejoin="round" />
                                            <path d="M12.5454 12L24 23.4545" stroke="#404040" strokeWidth="4" strokeLinecap="round" strokeLinejoin="round" />
                                        </svg>
                                    </div>
                                )}
                                <div className="row">
                                    <div className="col-12 d-flex justify-content-space">
                                        <div className="col-left">
                                            <h1 className={`product-title d-flex word-break ${style.productTitle}`} style={{ backgroundColor: productOptions?.data?.length == 0 ? '' : '#F5F5F5' }}>
                                                <span>{product?.data?.name}</span>
                                                <FavoriteCard key={product?.data?.id} index={product?.data?.id} item={product?.data} color={color} width={27} height={25} type='detailProduct' />
                                            </h1>
                                        </div>
                                        <div className="col-right">
                                            <div className="product-price">
                                                {currency}
                                                {product?.data?.price}
                                            </div>
                                        </div>
                                    </div>
                                </div>                          
                            </div>    
                            <CustomScrollbar color={color} style={{maxHeight: 'calc(85vh - ('+ (heightImage + heightTitle + 89) +'px))'}}>
                                {/* Desciption, labels, allergenen */}
                                <div className="product-detail-header pdlr-25" style={{ backgroundColor: productOptions?.data?.length > 0 ? '#F5F5F5' : '#FFFFFF' }}>
                                    <div className="row">
                                        <div className="col-12 d-flex justify-content-space">
                                            <div className="col-left">           
                                                <p className="product-description word-break">{product?.data?.description}</p>
                                            </div>
                                        </div>
                                    </div>
                                    { product?.data && (
                                        <div className="row mb-2 pd-tb-10">
                                            <div className="col-6">
                                                <div className={`${style.labels} mt-1`}>
                                                    <LabelFavotiteListDesk labels={product?.data?.labels} color={color} />
                                                </div>
                                            </div>
                                            <div className="col-6">
                                                <div className={style.allergenens}>
                                                    <AllergenenListDesk allergenens={product?.data?.allergenens} photo={true} />
                                                </div>
                                            </div>
                                        </div>
                                    )}                            
                                </div>        
                                {/* Options, add to cart */}
                                <div className="product-options">
                                    <ProductDetailContainer
                                        workspaceInfo={workspaceInfo}
                                        color={color}
                                        product={product}
                                        productOptions={productOptions}
                                        cartType="user_website"
                                        closePopupProductDetail={handleClose}
                                        setHoliday={setHoliday}
                                        origin="desktop_popup"
                                    />
                                </div>            
                            </CustomScrollbar>
                        </>
                    )}
                </Modal.Body>
            </Modal>
        </>
    )
}

export default memo(ProductDetailPopup)
