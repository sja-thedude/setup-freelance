'use client'

import React, { useEffect, useState } from 'react';
import Image from "next/image";
import variables from '/public/assets/css/food.module.scss';
import ProductCard from "./product-card";
import { useI18n } from '@/locales/client';
import { useInView } from "react-intersection-observer";
import useMediaQuery from '@mui/material/useMediaQuery'
import SortProduct from '@/app/[locale]/components/layouts/popup/sortProducts';
import { setflagSortData } from '@/redux/slices/flagSortSlice'
import { useAppSelector, useAppDispatch } from '@/redux/hooks'
import Cookies from 'js-cookie';
import useScrollPosition from '@/hooks/useScrollPosition'

const titleTextFirst = variables['title-text-first'];
const titleImage = variables['title-image'];
const underLine = variables['underline-title'];
const empty = variables['empty'];
const emptyDesk = variables['empty-desk'];
const sectionHeading = variables['section-heading'];
const sectionContent = variables['section-content'];

export default function Favorites({ products, color, baseLink, coupons }: { products: any, color: string, baseLink: string, coupons: any }) {
    const trans = useI18n()
    const isMobile = useMediaQuery('(max-width: 1279px)');
    const handleSortFav = () => {
        toggleSortPopup()
    }
    const standard = 0;
    const price = 1;
    const name = 2;
    const priceDecen = 3;
    const sorting = variables['sorting'];
    const resDesktop = variables['res-desktop'];
    const dispatch = useAppDispatch()
    const language = Cookies.get('Next-Locale');
    const [openPopups, setOpenPopups] = useState<any>(false);
    const [selectedOption, setSelectedOption] = useState<number>(0);
    const [selectedOptionDesk, setSelectedOptionDesk] = useState<number>(0);
    const option = variables['option-sorting'];
    const toggleSortPopup = () => {
        setOpenPopups(!openPopups);
    };
    const [productsSorting, setProductsSorting] = useState<any>([]); // Khởi tạo mảng rỗng
    useEffect(() => {
        if (products && products.length > 0) {
            var productsSorting = [...products]; // Tạo bản sao của mảng
            // Sort by ascending price
            if (selectedOption == 1) {
                productsSorting.sort((prev: any, next: any) => prev.price - next.price); // Sắp xếp bản sao
            } else if (selectedOption == 2) {
                productsSorting.sort((prev: any, next: any) => {
                    const namePrev = prev.name.toLowerCase();
                    const nameNext = next.name.toLowerCase();

                    if (namePrev < nameNext) {
                        return -1;
                    }
                    if (namePrev > nameNext) {
                        return 1;
                    }
                    return 0;
                });
            }
            setProductsSorting(productsSorting);
        }
    }, [selectedOption, products]);

    useEffect(() => {
        if (products && products.length > 0) {
            var productsSorting = [...products]; // Tạo bản sao của mảng
            // Sort by ascending price
            if (selectedOptionDesk == 1) {
                productsSorting.sort((prev: any, next: any) => prev.price - next.price); // Sắp xếp bản sao
            } else if (selectedOptionDesk == 2) {
                productsSorting.sort((prev: any, next: any) => {
                    const namePrev = prev.name.toLowerCase();
                    const nameNext = next.name.toLowerCase();

                    if (namePrev < nameNext) {
                        return -1;
                    }
                    if (namePrev > nameNext) {
                        return 1;
                    }
                    return 0;
                }); //sort by name a-z
            } else if (selectedOptionDesk == 3) {
                productsSorting.sort((prev: any, next: any) => next.price - prev.price);
            }
            setProductsSorting(productsSorting); // Cập nhật lại mảng
        }
    }, [selectedOptionDesk, products]);

    const [sortOption, setSortOption] = useState<any>(0);
    const [isSortOpen, setIsSortOpen] = useState(false);
    const handleTagClick = (e: any) => {
        e.stopPropagation();
        setIsSortOpen(!isSortOpen);
        dispatch(setflagSortData(true));
    };
    const [newWidthString, setNewWidthString] = useState(''); // Khởi tạo giá trị rỗng
    const adjustWidthMenu = Cookies.get('adjustWidthMenu');
    useEffect(() => {
        if (adjustWidthMenu) {
            const adjustWidthMenuValue = parseInt(adjustWidthMenu, 10);
            const newWidthValue = adjustWidthMenuValue + 40;
            setNewWidthString(`${newWidthValue}px`);
        } else {
            // Xử lý trường hợp adjustWidthMenu là undefined
        }
    }, [adjustWidthMenu]);

    const scrolledY = useScrollPosition()

    useEffect(() => {
        if (scrolledY > 0) {
            setIsSortOpen(false);
        }
    }, [scrolledY]);

    return (
        <>
            {isMobile ? (
                <section className={variables.section}>
                    <div className={`col-sm-12 col-xs-12`}>
                        <div className={`${sectionHeading} col-sm-12 col-xs-12`}
                            style={{ top: coupons && coupons.length > 0 ? '138px' : '96px' }}>
                            <div className="d-flex justify-content-between">
                                <div key={0} className={`${titleTextFirst}`}>
                                    <h1 style={{ borderBottom: color ? `2px solid ${color}` : '' }}>
                                        {trans('favorites')}
                                    </h1>
                                </div>
                                <div className={titleImage} onClick={handleSortFav}>
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                        <path d="M21 20H14" stroke="#413E38" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" />
                                        <path d="M10 20H3" stroke="#413E38" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" />
                                        <path d="M21 12H12" stroke="#413E38" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" />
                                        <path d="M8 12H3" stroke="#413E38" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" />
                                        <path d="M14 23V17" stroke="#413E38" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" />
                                        <path d="M8 15V9" stroke="#413E38" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" />
                                    </svg>
                                </div>
                            </div>
                        </div>
                        {openPopups ? (
                            <SortProduct
                                key={productsSorting[0]?.id}
                                color={color ? color : "black"}
                                toggleSortPopup={() => toggleSortPopup()}
                                handleSort={(option) => {
                                    setSelectedOption(option);
                                }}
                                selectedOption={selectedOption}
                            />
                        ) : null}
                        <div className="row" id={variables.group}>
                            <div className='col-sm-12 col-xs-12'>
                                {productsSorting && productsSorting.length > 0 ? (
                                    productsSorting.map((item: any, index: number) => (
                                        <ProductCard baseLink={baseLink} key={item.id} index={index} item={item} color={color} isLastProduct={(index === productsSorting.length - 1)} from='fav' handleCloseSuggest='' groupOrder='' />
                                    ))
                                ) : (
                                    <div className={`${empty}`} style={{ minHeight: '50vh' }}>{trans('no-favorite-items')}</div>
                                )}
                            </div>
                        </div>
                    </div>
                </section>
            ) : (
                <section className={variables.section} style={{marginBottom: 0}}>
                    <div className='row'>
                        <div className={`${variables.menuNameDesk}`} style={{
                                color: color,
                                top: coupons && coupons.length > 0 ? '210px' : '86px'
                            }}>
                            <div className="d-flex product-section-title">
                                <div className='text-uppercase'>{trans('favorites')}</div>
                                <div className={`ms-2 d-flex justify-content-between`} onClick={(e) => handleTagClick(e)}>
                                    <svg width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg" className="ms-2"><line x1="3" y1="3" x2="1" y2="3" stroke="#404040" strokeWidth="2" strokeLinecap="round"></line><line x1="11" y1="3" x2="6" y2="3" stroke="#404040" strokeWidth="2" strokeLinecap="round"></line><line x1="3" y1="1" x2="3" y2="5.4" stroke="#404040" strokeWidth="2" strokeLinecap="round"></line><line x1="9" y1="9" x2="11" y2="9" stroke="#404040" strokeWidth="2" strokeLinecap="round"></line><line x1="1" y1="9" x2="6" y2="9" stroke="#404040" strokeWidth="2" strokeLinecap="round"></line><line x1="9" y1="11" x2="9" y2="6.6" stroke="#404040" strokeWidth="2" strokeLinecap="round"></line></svg>
                                </div>
                            </div>
                            
                            {isSortOpen ? (
                                <div className={`${sorting} row ${resDesktop}`} style={{ position: 'absolute', paddingTop: '25px', zIndex: "99", left: '126px', top: '42px', backgroundColor: '#F5F5F5', boxShadow: '0px 10px 20px 2px #0202021A' }} onClick={() => { setIsSortOpen(!isSortOpen); }}>
                                    <div className='row d-flex' style={{ flexDirection: "column" }}>
                                        <div className={`${option} d-flex ms-2`} onClick={() => { setSelectedOptionDesk(standard); setSortOption(standard) }} style={{}}>
                                            <p className={`${language === 'en' ? 'ms-2' : ''} text-uppercase`} style={{ color: selectedOptionDesk === standard ? (color ? color : '#404040') : '#404040' }}>{trans('standard')}</p>
                                        </div>
                                        <div className={`${option} d-flex ms-2`} onClick={() => { setSelectedOptionDesk(price); setSortOption(price) }}>
                                            <p className={`${language === 'en' ? 'ms-2' : ''} text-uppercase`} style={{ color: selectedOptionDesk === price ? (color ? color : '#404040') : '#404040' }}>{trans('sort-by-price-desk-acen')}</p>
                                        </div>
                                        <div className={`${option} d-flex ms-2`} onClick={() => { setSelectedOptionDesk(priceDecen); setSortOption(priceDecen) }}>
                                            <p className={`${language === 'en' ? 'ms-2' : ''} text-uppercase`} style={{ color: selectedOptionDesk === priceDecen ? (color ? color : '#404040') : '#404040' }}>{trans('sort-by-price-desk-decen')}</p>
                                        </div>
                                        <div className={`${option} d-flex ms-2`} onClick={() => { setSelectedOptionDesk(name); setSortOption(name) }}>
                                            <p className={`${language === 'en' ? 'ms-2' : ''} text-uppercase`} style={{ color: selectedOptionDesk === name ? (color ? color : '#404040') : '#404040' }}>{trans('sort-by-name')}</p>
                                        </div>
                                    </div>
                                </div>
                            ) : null}
                        </div>

                        <div id={variables.group}>
                            <div className="row ms-0 me-0">
                                {!productsSorting || productsSorting.length === 0 ? (
                                    <div className={`${empty}`}>{trans('no-items-desk')}</div>
                                ) : (
                                    productsSorting.map((item: any, index: number) => (
                                        <ProductCard key={item.id} index={index} item={item} color={color} baseLink={baseLink} isLastProduct={false} from='fav' handleCloseSuggest='' groupOrder='' />
                                    ))
                                )}
                            </div>
                        </div>
                    </div>
                </section>
            )}
        </>
    );
};
