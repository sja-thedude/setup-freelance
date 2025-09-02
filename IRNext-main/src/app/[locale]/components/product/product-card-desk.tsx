import React, { useState, useEffect } from 'react';
import Image from "next/image";
import variables from '/public/assets/css/food.module.scss'
import style from 'public/assets/css/product.module.scss';
import Cookies from "js-cookie";
import FavoriteCard from './favorite-card'
import useMediaQuery from '@mui/material/useMediaQuery'
import LabelFavotiteListDesk from '../ordering/labels/favorite/deskList';
import { useAppSelector, useAppDispatch } from '@/redux/hooks'
import {
    manualChangeOrderTypeDesktop,
    addMaxHeightPhoto,
    addMaxHeightPhotoLoaded,
    addMaxHeightNonePhoto,
    addMaxHeightNonePhotoLoaded,
} from '@/redux/slices/cartSlice'
import ProductDetailPopup from '@/app/[locale]/components/desktop/productDetailPopup'
import { useGetWorkspaceDataByIdQuery } from '@/redux/services/workspace/workspaceDataApi';
import FavoriteListProductDesk from '@/app/[locale]/components/img/favorite/favoriteListProductDesk'
import KoketteListProductMobile from '@/app/[locale]/components/img/kokette/koketteListProductMobile'

const titleTextCartDesk = variables['title-text-cart-desk'];
const titleFav = variables['title-fav'];
const titleKoke = variables['title-koke'];
const productDetail = variables['product-detail'];
const productPhoto = variables["product-photo"];
const productNonePhoto = variables["product-none-photo"];
const productDetailDesk = variables['product-detail-desk'];
const productDetailDeskSec = variables['product-detail-desk-sec'];
const productDetailRelated = variables['popup-related'];
const productDescription = variables['description-food'];
const productDesWI = variables['description-food-without-desk'];
const productImageDesk = variables['product-imagin-desk'];
const pricing = variables['pricing'];
const currency = '€';

export default function ProductCardDesk({ index, item, color, isLastProduct, from, baseLink, handleCloseSuggest, groupOrder }: { index: number, item: any, color: string, isLastProduct: boolean, from: string, baseLink: string, handleCloseSuggest: any, groupOrder: any }) {
    const [totalAttrMealWidth, setTotalAttrMealWidth] = useState(0);
    const tokenLoggedInCookie = Cookies.get('loggedToken');
    const isMobile = useMediaQuery('(max-width: 1279px)');
    const isTablet = useMediaQuery('(max-width: 1400px)');
    const handleTotalAttrMealWidth = (width: number) => {
        var finalWidth = width;
        const filteredLabels = item.labels.filter((label: any) => label.type !== 0);
        var count = filteredLabels.length;
        if (count >= 2) {
            finalWidth += 20;
        }
        setTotalAttrMealWidth(finalWidth);
    };
    const dispatch = useAppDispatch()
    const [showProductDetailPopup, setShowProductDetailPopup] = useState(false)
    const navigateToProductDetail = () => {
        dispatch(manualChangeOrderTypeDesktop(false));
        setShowProductDetailPopup(true)
        // router.push(`${baseLink}/${item.id}?from=${from}${groupOrder ? `&groupOrder=${groupOrder}` : ''}`);
    };
    const workspaceId = useAppSelector((state) => state.workspaceData.globalWorkspaceId)
    const { data: apiDataToken } = useGetWorkspaceDataByIdQuery({ id: workspaceId })
    const workspaceInfo = apiDataToken?.data;
    const [isHovered, setIsHovered] = useState(false);
    const [maxHeightPhoto, setMaxHeightPhoto] = useState(0);
    const [maxHeightNonePhoto, setMaxHeightNonePhoto] = useState(0);
    const [idShow, setIsShow] = useState(false);
    const productPhotoElement = document.querySelectorAll(`.popup-related.${productPhoto}`);
    const productNonePhotoElement = document.querySelectorAll(
      `.popup-related.${productNonePhoto}`
    );
    
    useEffect(() => {
        const originMaxHeightPhoto = maxHeightPhoto;
        if (productPhotoElement.length > 0) {
            let maxHeightPhotoTmp = 0;
            let photoHeightNotSame = false;
            productPhotoElement.forEach((element) => {
                const height = element.clientHeight;

                if (height > maxHeightPhotoTmp) {
                    maxHeightPhotoTmp = height;
                    photoHeightNotSame = true;
                }
            });
            if (
                maxHeightPhotoTmp > 0 &&
                (maxHeightPhotoTmp != originMaxHeightPhoto || photoHeightNotSame)
            ) {
                setMaxHeightPhoto(maxHeightPhotoTmp);
                dispatch(addMaxHeightPhoto(maxHeightPhotoTmp));
                const productPhotoElement = document.querySelectorAll<HTMLElement>(`.${productPhoto}`);
                productPhotoElement.forEach((element, index) => {
                    element.style.height = `${maxHeightPhotoTmp}px`;
                    if (index === productPhotoElement.length - 1) {
                        dispatch(addMaxHeightPhotoLoaded(true));
                    }
                });
            }
        }

        const originMaxHeightNonePhoto = maxHeightNonePhoto;
        if (productNonePhotoElement.length > 0) {
            let maxHeightNonePhotoTmp = 0;
            let nonePhotoHeightNotSame = false;
            productNonePhotoElement.forEach((element) => {
                const height = element.clientHeight;

                if (height > maxHeightNonePhotoTmp) {
                    maxHeightNonePhotoTmp = height;
                    nonePhotoHeightNotSame = true;
                }
            });

            if (
                maxHeightNonePhotoTmp > 0 &&
                (maxHeightNonePhotoTmp != originMaxHeightNonePhoto ||
                nonePhotoHeightNotSame)
            ) {
                setMaxHeightNonePhoto(maxHeightNonePhotoTmp);
                dispatch(addMaxHeightNonePhoto(maxHeightNonePhotoTmp));
                const productNonePhotoElement =
                document.querySelectorAll<HTMLElement>(`.${productNonePhoto}`);
                productNonePhotoElement.forEach((element, index) => {
                    element.style.height = `${maxHeightNonePhotoTmp}px`;
                    if (index === productNonePhotoElement.length - 1) {
                        dispatch(addMaxHeightNonePhotoLoaded(true));
                    }
                });
            }
        }
        const productLabel = document.querySelectorAll<HTMLElement>(".food-pricing-label");
        if (productLabel.length > 0) {
            productLabel.forEach((element) => {
                const parent = element.parentElement;
                if (parent) {
                    parent.style.height = `${element.clientHeight + 10}px`;
                }
                element.style.position = "absolute";
                element.style.bottom = "10px";
                element.style.width = "calc(100% - 1rem - 5px)";
            });
        }
        setIsShow(true);
    }, [productPhotoElement, productNonePhotoElement]);

    const handleMouseEnter = () => {
        setIsHovered(true);
    };

    const handleMouseLeave = () => {
        setIsHovered(false);
    };
    const calculateMarginBottom = () => {
        if (isMobile && isLastProduct) {
            const holidayHeight = Cookies.get('holidayHeight');
            if (holidayHeight) {
                return `${parseInt(holidayHeight) + 90}px`;
            } else {
                return '50%';
            }
        }
    };

    return (
        <>
            <div key={index} className={`${(from != 'productSuggestion') ? productDetailDesk : productDetailDeskSec} ${productDetailRelated} ${
                  item.photo
                  ? productPhoto
                  : productNonePhoto
                } col-md-12 mb-5 res-desktop popup-related d-flex flex-column justify-content-between`}
                style={{ height: 'fit-content', marginBottom: calculateMarginBottom() }}
                id={`product-${item.id}`}
                onClick={navigateToProductDetail}
            >
                <div className={`${variables.deskContain}`} >
                    <div className={`${variables.heartDesk}`} style={{ display: 'inline-block' }}>
                        <FavoriteCard key={item.id} index={item.id} item={item} color={color} width={17} height={15} type={'null'} />
                    </div>
                    {item?.allergenens && item?.allergenens.length > 0 && (
                        <div className={`${variables.infoDesk}`} style={{ display: 'inline-block' }} onMouseLeave={(e: any) => { e.stopPropagation(); handleMouseLeave() }} onMouseEnter={(e: any) => { e.stopPropagation(); handleMouseEnter() }}>
                            <svg width="31" height="31" viewBox="0 0 31 31" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <circle cx="15.5" cy="15.5" r="15.5" fill="white" />
                                <circle cx="15.5" cy="15.5" r="9.5" stroke='#404040' strokeWidth="2" />
                                <path d="M16.3752 12.2938C16.0672 12.2938 15.8059 12.1865 15.5912 11.9718C15.3765 11.7571 15.2692 11.4958 15.2692 11.1878C15.2692 10.8798 15.3765 10.6185 15.5912 10.4038C15.8059 10.1798 16.0672 10.0678 16.3752 10.0678C16.6832 10.0678 16.9445 10.1798 17.1592 10.4038C17.3832 10.6185 17.4952 10.8798 17.4952 11.1878C17.4952 11.4958 17.3832 11.7571 17.1592 11.9718C16.9445 12.1865 16.6832 12.2938 16.3752 12.2938ZM15.4232 20.1338C14.9752 20.1338 14.6112 19.9938 14.3312 19.7138C14.0605 19.4338 13.9252 19.0138 13.9252 18.4538C13.9252 18.2205 13.9625 17.9171 14.0372 17.5438L14.9892 13.0498H17.0052L15.9972 17.8098C15.9599 17.9498 15.9412 18.0991 15.9412 18.2578C15.9412 18.4445 15.9832 18.5798 16.0672 18.6638C16.1605 18.7385 16.3099 18.7758 16.5152 18.7758C16.6832 18.7758 16.8325 18.7478 16.9632 18.6918C16.9259 19.1585 16.7579 19.5178 16.4592 19.7698C16.1699 20.0125 15.8245 20.1338 15.4232 20.1338Z" fill={'#404040'} />
                            </svg>
                        </div>
                    )}
                    {isHovered && (
                        <div className={`${variables.allergenensListDesk}`}>
                            {item?.allergenens?.map((allergenen: any, i: number) => (
                                <Image
                                    alt={allergenen.type_display}
                                    className={style.image}
                                    src={allergenen.icon}
                                    width={44}
                                    height={43}
                                    quality={100}
                                    priority
                                    unoptimized={true}
                                    key={'allergenen-' + allergenen.id}
                                />
                            ))}
                        </div>)}
                    <div>
                        {item.photo ? (
                            <>
                                <div className={`${variables.photoContainDesk}`}>
                                    <Image
                                        className={`${productImageDesk}`}
                                        alt='food'
                                        src={item.photo}
                                        width={600}
                                        height={400}
                                        layout="responsive"
                                        style={{ minHeight: '191px' }}
                                    />
                                </div>
                            </>
                        ) : (
                            <div className={`${variables.nothing}`}>
                            </div>
                        )}

                        <div className={variables.title}>
                            <div className={`${titleTextCartDesk} mt-2`}>
                                <h1 className="ms-1 word-break">{item.name}</h1>
                            </div>
                            <div className='d-flex me-3'>
                                <div className={`${titleFav} mt-1`} style={{ marginLeft: (!item?.category?.kokette_kroket) ? '30px' : '' }}>
                                    {
                                        item?.category?.favoriet_friet == true ? (
                                        <FavoriteListProductDesk/>
                                    ) : (null)
                                    }
                                </div>
                                <div className={`${titleKoke} mt-1`}>
                                    {
                                        item?.category?.kokette_kroket == true ? (<Image
                                            alt='kokette'
                                            src="/img/kokette.png"
                                            width={100}
                                            height={100}
                                            sizes="100vw"
                                            style={{ width: '18px', height: '27px' }} // optional
                                        />) : (null)
                                    }
                                </div>
                            </div>
                        </div>
                        <div className="row ps-3 pe-3">
                            {item.photo ? (
                                <>
                                    <div className={`d-flex align-items-start col-sm-12 col-12`} style={{ paddingLeft: '12px', paddingRight: '12px' }}>
                                        <div className={`${productDescription} word-break`}>
                                            {item?.description && item?.description.length > 140 ? (
                                                <>{item?.description.slice(0, 140)}...</>
                                            ) : (
                                                <>{item?.description}</>
                                            )}
                                        </div>
                                    </div>
                                </>
                            ) : (
                                <div className={`d-flex align-items-start ${productDesWI} col-12`}>
                                    <div className={`${productDesWI} word-break`} style={{ paddingRight: '8px' }}>
                                        {item?.description && item?.description.length > 157 ? (
                                            <>{item?.description.slice(0, 157)}...</>
                                        ) : (
                                            <>{item?.description}</>
                                        )}
                                    </div>
                                </div>
                            )}
                        </div>
                    </div>
                </div>
                <div className='row ps-1 pe-2'>
                    <div className={`${style['product-detail']} mt-2 mb-1 col-12 pb-0`}>
                        <div className="d-flex justify-content-between align-items-end">
                            <div className={`${style['product-image']} d-flex ms-1`} style={{ width: '100%' }}>
                                <div key={item.id} className={`${style['labels']}`} style={{ position: 'relative', zIndex: 1, maxWidth: (item.price > 100 && !tokenLoggedInCookie) ? '75%' : isTablet ? '70%' : '72%' }}>
                                    <LabelFavotiteListDesk labels={item?.labels} color={color} />
                                </div>
                            </div>
                            <div className={`${pricing}`} style={{position: 'relative' , padding: '0', margin: '0', marginBottom: '.3rem', color: color ? color : 'black' }}>{currency}{item.price}</div>
                        </div>
                    </div>
                </div>
            </div>
            {showProductDetailPopup == true && (
                <ProductDetailPopup
                    workspaceInfo={workspaceInfo}
                    showProductDetailPopup={showProductDetailPopup}
                    setShowProductDetailPopup={setShowProductDetailPopup}
                    color={color}
                    productInfo={item}
                    productId={item.id}
                />
            )}
        </>
    );
};
