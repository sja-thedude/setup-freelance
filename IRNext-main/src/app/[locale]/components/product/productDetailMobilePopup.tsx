'use client'

import {memo, useEffect, useState} from 'react'
import { Modal } from 'react-bootstrap'
import { useGetWorkspaceDataByIdQuery } from '@/redux/services/workspace/workspaceDataApi';
import { useAppSelector } from '@/redux/hooks'
import UserWebsiteDetail from './detail/userWebsiteDetail'
import TabeOrderingDetail from './detail/tableOrderingDetail'
import SelfOrderingDetail from './detail/selfOrderingDetail'
import Cookies from 'js-cookie';

function ProductDetailMobilePopup(props?: any) {
    let {
        showPopup,
        productInfo,
        productId,
        baseLink,
        setShowProductDetailMobilePopup
    } = props


    const handleClose = () => {

        if (setShowProductDetailMobilePopup) {
            setShowProductDetailMobilePopup(false)
        }
    }
    const workspaceId = useAppSelector((state) => state.workspaceData.globalWorkspaceId)
    const { data: apiDataToken } = useGetWorkspaceDataByIdQuery({ id: workspaceId })
    const workspace = apiDataToken?.data;
    const color = workspace?.setting_generals?.primary_color
    const isLoadingProduct = false;
    const isLoadingProductOptions = false;
    const product = {data: productInfo};
    const productOptions = {data: productInfo.options};
    const tokenLoggedInCookie = Cookies.get('loggedToken');
    if (!tokenLoggedInCookie) {
        Cookies.set('currentProductId', productId);
    }
    
    return (
        <>
            <Modal className="product-detail-mobile-modal"
                size="lg"
                show={showPopup}
                onHide={handleClose}
                animation={false}
                aria-labelledby="contained-modal-title-vcenter"
                centered>
                <Modal.Body className="">
                    {!isLoadingProduct && !isLoadingProductOptions && (
                        <div className="container">
                            {baseLink === '/category/products' && (
                                <UserWebsiteDetail
                                    workspace={workspace}
                                    workspaceId={workspaceId}
                                    isLoadingProduct={isLoadingProduct}
                                    isLoadingProductOptions={isLoadingProductOptions}
                                    product={product}
                                    color={color}
                                    productId={productId}
                                    productOptions={productOptions}
                                    handleClose={handleClose} />
                            )}
                            {baseLink === '/self-ordering/products' && (
                                <SelfOrderingDetail
                                    isLoadingProduct={isLoadingProduct}
                                    isLoadingProductOptions={isLoadingProductOptions}
                                    product={product}
                                    color={color}
                                    productId={productId}
                                    productOptions={productOptions}
                                    handleClose={handleClose} />
                            )}
                            {baseLink === '/table-ordering/products' && (
                                <TabeOrderingDetail
                                    isLoadingProduct={isLoadingProduct}
                                    isLoadingProductOptions={isLoadingProductOptions}
                                    product={product}
                                    color={color}
                                    productId={productId}
                                    productOptions={productOptions}
                                    handleClose={handleClose} />
                            )}
                        </div>
                    )}
                </Modal.Body>
            </Modal>
        </>
    )
}

export default memo(ProductDetailMobilePopup)