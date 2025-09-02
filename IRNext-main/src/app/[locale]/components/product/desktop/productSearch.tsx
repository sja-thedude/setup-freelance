'use client'
import React, { useRef, useEffect, useState } from 'react';
import variables from '/public/assets/css/food.module.scss'
import { useI18n } from '@/locales/client'
import ProductCard from '../product-card';
import SortProduct from '@/app/[locale]/components/layouts/popup/sortProducts';
import Cookies from "js-cookie";

const empty = variables['empty-desk'];
const sectionContent = variables['section-content'];


export default function ProductDeskSearch({ categoryItems, color, baseLink, forcusElement, isSortOpen, option, toggleSortingDesk, search, setHoliday, workspaceInfo }: { categoryItems: any, color: string, baseLink: string, forcusElement: any, isSortOpen: any, option: any, toggleSortingDesk: any, search: any, setHoliday? : any, workspaceInfo?: any }) {
    const currency = '€';
    const trans = useI18n()
    const [openPopups, setOpenPopups] = useState<{ [key: number]: boolean }>({});
    const [selectedOption, setSelectedOption] = useState<number>(0);
    const toggleSortPopup = (couponIndex: number) => {
        const updatedOpenPopups: { [key: number]: boolean } = { ...openPopups };
        updatedOpenPopups[couponIndex] = !updatedOpenPopups[couponIndex];
        setOpenPopups(updatedOpenPopups);
        toggleSortingDesk()
    };

    const [productsSorting, setProductsSorting] = useState<any[]>([]); // Khởi tạo mảng rỗng

    useEffect(() => {
        if (categoryItems[0]?.products) {
            var productsSorting = [...categoryItems[0].products]; // Tạo bản sao của mảng
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
                }); //sort by name a-z
            } else if (selectedOption == 3) {
                productsSorting.sort((prev: any, next: any) => next.price - prev.price);
            }
            setProductsSorting(productsSorting); // Cập nhật lại mảng
        }
        option(selectedOption)
    }, [selectedOption, categoryItems[0]?.products]);
    // Gộp tất cả các đối tượng thành một đối tượng duy nhất
    const searching: any = categoryItems.reduce((result: any, item: any) => {
        Object.keys(item).forEach(key => {
            if (key === "products" && Array.isArray(result[key])) {
                result[key] = result[key].concat(item[key]);
            } else {
                result[key] = item[key];
            }
        });

        return result;
    }, {});
    if (search) {
        categoryItems = [{}]
        categoryItems[0] = searching
    }
    return (
        <>
            <section id={`section-${categoryItems[0]?.id}`} className={`${variables.sectionDesk}`} style={{ display: (categoryItems[0]?.id == forcusElement || search) ? 'block' : 'none' }}>
                {isSortOpen ? (
                    <SortProduct
                        key={forcusElement}
                        color={color ? color : "black"}
                        toggleSortPopup={() => toggleSortPopup(forcusElement)}
                        handleSort={(option) => {
                            setSelectedOption(option);
                        }}
                        selectedOption={selectedOption}
                    />
                ) : null}
                <div className={`${sectionContent} row`} id={variables.group}>
                    <div className='row' style={{ height: (!categoryItems || !productsSorting || productsSorting.length === 0) ? '75px' : '', padding: '0', paddingLeft: '22px' }}>
                        {!categoryItems || !productsSorting || productsSorting.length === 0 ? (
                            <div className={`${empty}`}>{trans('no-items-desk')}</div>
                        ) : (
                            productsSorting.map((item: any, index: number) => (
                                <ProductCard key={item.id} index={index} item={item} color={color} baseLink={baseLink} isLastProduct={false} from='search' handleCloseSuggest='' groupOrder='' setHoliday={setHoliday} workspaceInfo={workspaceInfo} />
                            ))
                        )}
                    </div>
                </div>
            </section>
        </>
    );
};
