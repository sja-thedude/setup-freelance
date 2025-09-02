"use client";
import React, {useRef, useEffect, useState, memo} from "react";
import variables from "/public/assets/css/food.module.scss";
import {useI18n} from "@/locales/client";
import ProductCard from "./product-card";
import {useInView} from "react-intersection-observer";
import SortProduct from "@/app/[locale]/components/layouts/popup/sortProducts";
import Cookies from "js-cookie";

const titleTextFirst = variables["title-text-first"];
const titleImage = variables["title-image"];
const empty = variables["empty"];
const emptyContainer = variables["empty-container"];
const sectionHeading = variables["section-heading"];
const sectionContent = variables["section-content"];

const Product = memo((props: any) => {
    const {
        categoryItems,
        color,
        onFocus,
        isLast,
        baseLink,
        coupons,
        currentCategory,
        setCurrentCategory,
        isProgrammaticallyScroll
    } = props;
    const trans = useI18n();
    const isSafari = /^((?!chrome|android).)*safari/i.test(navigator.userAgent);
    const [productsSorting, setProductsSorting] = useState<any[]>([]);

    const handleScrollScreen = () => {
        const viewportHeight = window.innerHeight;
        // 138 and 96 is fixed top of element title
        if (coupons && coupons.length > 0) {
            const rootMarginTop = 138;
            const rootMarginBottom = viewportHeight - rootMarginTop;
            if (rootMarginTop > 0 && rootMarginBottom > 0) {
                return `-${rootMarginTop}px 0px -${rootMarginBottom}px 0px`;
            }
        } else {
            const rootMarginTop = 96;
            const rootMarginBottom = viewportHeight - rootMarginTop;
            if (rootMarginTop > 0 && rootMarginBottom > 0) {
                return `-${rootMarginTop}px 0px -${rootMarginBottom}px 0px`;
            }
        }
    };
    const safariRootMargin = () => {
        const viewportHeight = window.innerHeight;

        if (viewportHeight <= 0) {
            console.warn("Invalid viewport height for Safari");
            return "0px 0px 0px 0px"; // Default fallback
        }

        // Set the threshold to 0 for immediate trigger
        const topMarginPercentage = 190; // Trigger as soon as the section starts entering the viewport
        const bottomMarginPercentage = 10.15; // Trigger earlier when an item leaves the bottom

        // Default behavior for categories with products
        const rootMarginTop = -(viewportHeight * (100 - topMarginPercentage)) / 100; // General top margin with 0% threshold
        const rootMarginBottom = -(viewportHeight * bottomMarginPercentage) / 100; // General bottom margin

        return `${rootMarginTop}% 0px ${rootMarginBottom}% 0px`;
    };

    const [ref, inView] = useInView({
        threshold: 0,
        rootMargin: isSafari ? safariRootMargin() : handleScrollScreen(),
    });

    useEffect(() => {
        const checkAutoFocus = () => {
            if (isProgrammaticallyScroll) return
            if (inView) {
                if (currentCategory) {
                    if (currentCategory == categoryItems[0]?.id) {
                        onFocus(categoryItems[0]?.id);
                        setCurrentCategory(null);
                    }
                } else {

                    onFocus(categoryItems[0]?.id);
                }
            }
        }
        checkAutoFocus()


    });

    const [openPopups, setOpenPopups] = useState<{ [key: number]: boolean }>({});
    const [selectedOption, setSelectedOption] = useState<number>(0);

    const toggleSortPopup = (couponIndex: number) => {
        const updatedOpenPopups: { [key: number]: boolean } = {...openPopups};
        updatedOpenPopups[couponIndex] = !updatedOpenPopups[couponIndex];
        setOpenPopups(updatedOpenPopups);
    };

    const handleSort = () => {
        toggleSortPopup(categoryItems[0]?.id);
    };

    useEffect(() => {
        if (categoryItems[0]?.products) {
            var productsSorting = [...categoryItems[0].products];
            // Sort by ascending price
            if (selectedOption == 1) {
                productsSorting.sort((prev: any, next: any) => prev.price - next.price);
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
            }
            setProductsSorting(productsSorting);
        }
    }, [selectedOption, categoryItems[0]?.products]);

    const refing = useRef<any>(null);

    useEffect(() => {
        const handleOutSideClick = (event: any) => {
            if (!refing.current?.contains(event.target)) {
                const updatedOpenPopups: { [key: number]: boolean } = {...openPopups};
                updatedOpenPopups[categoryItems[0]?.id] = false;

                setOpenPopups(updatedOpenPopups);
            }
        };
        window.addEventListener("mousedown", handleOutSideClick);

        return () => {
            window.removeEventListener("mousedown", handleOutSideClick);
        };
    }, [refing]);

    return (
        <>
            <section
                id={`section-${categoryItems[0]?.id}`}
                className={variables.section}
                ref={ref}
            >
                <div
                    id={`group-${categoryItems[0]?.id}`}
                    className={`${sectionHeading} col-sm-12 col-xs-12`}
                    style={{top: Cookies.get("coupons") == "true" ? "138px" : "96px"}}
                >
                    <div className="d-flex justify-content-between">
                        {categoryItems &&
                            categoryItems.map((catItem: any, catIndex: number) => (
                                <div
                                    key={catIndex}
                                    className={`${titleTextFirst} category-title-mobile`}
                                >
                                    <h1
                                        style={{borderBottom: color ? `2px solid ${color}` : ""}}
                                    >
                                        {catItem.name}
                                    </h1>
                                </div>
                            ))}

                        {categoryItems && productsSorting?.length >= 2 && (
                            <div>
                                <div
                                    className={`${titleImage}`}
                                    onClick={handleSort}
                                    style={{position: "relative"}}
                                >
                                    <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        width="24"
                                        height="24"
                                        viewBox="0 0 24 24"
                                        fill="none"
                                    >
                                        <path
                                            d="M21 20H14"
                                            stroke="#413E38"
                                            strokeWidth="2"
                                            strokeLinecap="round"
                                            strokeLinejoin="round"
                                        />
                                        <path
                                            d="M10 20H3"
                                            stroke="#413E38"
                                            strokeWidth="2"
                                            strokeLinecap="round"
                                            strokeLinejoin="round"
                                        />
                                        <path
                                            d="M21 12H12"
                                            stroke="#413E38"
                                            strokeWidth="2"
                                            strokeLinecap="round"
                                            strokeLinejoin="round"
                                        />
                                        <path
                                            d="M8 12H3"
                                            stroke="#413E38"
                                            strokeWidth="2"
                                            strokeLinecap="round"
                                            strokeLinejoin="round"
                                        />
                                        <path
                                            d="M14 23V17"
                                            stroke="#413E38"
                                            strokeWidth="2"
                                            strokeLinecap="round"
                                            strokeLinejoin="round"
                                        />
                                        <path
                                            d="M8 15V9"
                                            stroke="#413E38"
                                            strokeWidth="2"
                                            strokeLinecap="round"
                                            strokeLinejoin="round"
                                        />
                                    </svg>
                                    {openPopups[categoryItems[0]?.id] ? (
                                        <div
                                            style={{
                                                position: "absolute",
                                                right: 0,
                                                zIndex: 1000,
                                                top: "45px",
                                                width: "100vw",
                                                height: "100vh",
                                            }}
                                            ref={refing}
                                        >
                                            <SortProduct
                                                key={categoryItems[0]?.id}
                                                color={color ? color : "black"}
                                                toggleSortPopup={() =>
                                                    toggleSortPopup(categoryItems[0]?.id)
                                                }
                                                handleSort={(option) => {
                                                    setSelectedOption(option);
                                                }}
                                                selectedOption={selectedOption}
                                            />
                                        </div>
                                    ) : null}
                                </div>
                            </div>
                        )}
                    </div>
                </div>

                <div
                    className={`${sectionContent} row`}
                    id={variables.group}
                    style={{
                        backgroundColor:
                            !categoryItems || !productsSorting || productsSorting.length === 0
                                ? "#F8F8F8"
                                : "",
                    }}
                >
                    <div
                        className="col-sm-12 col-xs-12 d-flex  justify-content-center"
                        style={{
                            flexDirection: "column",
                            height:
                                !categoryItems ||
                                !productsSorting ||
                                productsSorting.length === 0
                                    ? "auto"
                                    : "",
                        }}
                    >
                        {!categoryItems ||
                        !productsSorting ||
                        productsSorting.length === 0 ? (
                            <div className={`${empty}`}>
                                <div className={`${emptyContainer}`}>
                                    {trans("no-items")}
                                </div>
                            </div>
                        ) : (
                            productsSorting.map((item: any, index: number) => (
                                <ProductCard
                                    key={item.id}
                                    index={index}
                                    item={item}
                                    color={color}
                                    baseLink={baseLink}
                                    isLastProduct={isLast && index === productsSorting.length - 1}
                                    from=""
                                    handleCloseSuggest=""
                                    groupOrder=""
                                />
                            ))
                        )}
                    </div>
                </div>
            </section>
        </>
    );
});

Product.displayName = "Product";

export default Product;
