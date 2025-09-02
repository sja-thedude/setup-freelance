"use client";
import React, {memo, useEffect, useState} from "react";
import variables from "/public/assets/css/food.module.scss";
import {useI18n} from "@/locales/client";
import ProductCard from "../product-card";
import {useInView} from "react-intersection-observer";
import Cookies from "js-cookie";
import {useAppSelector, useAppDispatch} from "@/redux/hooks";
import {setflagSortData} from "@/redux/slices/flagSortSlice";
import useScrollPosition from "@/hooks/useScrollPosition";

const empty = variables["empty-desk"];

const ProductDesk = memo((props: any) => {
    const {
        categoryItems,
        color,
        baseLink,
        setHoliday,
        workspaceInfo,
        onFocus,
        coupons,
        currentCategory,
        setCurrentCategory,
        from,
        lastItem,
        forcusElement,
    } = props;
    const standard = 0;
    const price = 1;
    const name = 2;
    const priceDecen = 3;
    const sorting = variables["sorting"];
    const option = variables["option-sorting"];
    const standarDesk = variables["standar-desk"];
    const resDesktop = variables["res-desktop"];
    const language = Cookies.get("Next-Locale");
    const trans = useI18n();
    const nextSortSlice = useAppSelector<any>(
        (state: any) => state.flagSort.data
    );
    const [selectedOption, setSelectedOption] = useState<number>(0);
    const maximumHeightPhotoLoaded = useAppSelector(
        (state) => state.cart.maxHeightNonePhotoLoaded
    );
    const maximumHeightNonePhotoLoaded = useAppSelector(
        (state) => state.cart.maxHeightNonePhotoLoaded
    );
    const handleScrollScreen = () => {
        const viewportHeight = window.innerHeight;
        // 212 and 86 is fixed top of element title
        if (coupons && coupons.length > 0) {
            const rootMarginTop = 213;
            const rootMarginBottom = viewportHeight - rootMarginTop;
            if (rootMarginTop > 0 && rootMarginBottom > 0) {
                return `-${rootMarginTop}px 0px -${rootMarginBottom}px 0px`;
            }
        } else {
            const rootMarginTop = 87;
            const rootMarginBottom = viewportHeight - rootMarginTop;
            if (rootMarginTop > 0 && rootMarginBottom > 0) {
                return `-${rootMarginTop}px 0px -${rootMarginBottom}px 0px`;
            }
        }
    };

    const [ref, inView] = useInView({
        threshold: 0,
        rootMargin: handleScrollScreen(),
    });

    const dispatch = useAppDispatch();
    const [isSortOpen, setIsSortOpen] = useState(false);

    useEffect(() => {
        const checkAutoFocus = () => {
            if (inView) {
                if (currentCategory) {
                    if (currentCategory == categoryItems?.id) {
                        onFocus(categoryItems?.id);
                        setCurrentCategory(null);
                    }
                } else {
                    onFocus(categoryItems?.id);
                }
            }
        }

        checkAutoFocus()

    });

    const [productsSorting, setProductsSorting] = useState<any[]>([]);

    useEffect(() => {
        if (categoryItems?.products) {
            var productsSorting = [...categoryItems.products];
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
            } else if (selectedOption == 3) {
                productsSorting.sort((prev: any, next: any) => next.price - prev.price);
            }
            setProductsSorting(productsSorting);
        }
    }, [selectedOption, categoryItems?.products]);

    useEffect(() => {
        if (!nextSortSlice) {
            setIsSortOpen(false);
        }
    }, [nextSortSlice]);

    const handleTagClick = (e: any) => {
        e.stopPropagation();
        setIsSortOpen(!isSortOpen);
        dispatch(setflagSortData(true));
    };

    const [windowWidth, setWindowWidth] = useState(window.innerWidth);
    useEffect(() => {
        const handleResize = () => {
            setWindowWidth(window.innerWidth);
        };
        window.addEventListener("resize", handleResize);
        return () => window.removeEventListener("resize", handleResize);
    }, []);

    const [sortOption, setSortOption] = useState<any>(0);
    const scrolledY = useScrollPosition();

    useEffect(() => {
        if (scrolledY > 0) {
            setIsSortOpen(false);
        }
    }, [scrolledY]);

    const [allProductCardsRendered, setAllProductCardsRendered] = useState(false);

    useEffect(() => {
        if (productsSorting.length > 0) {
            if (maximumHeightPhotoLoaded && maximumHeightNonePhotoLoaded) {
                const timer = setTimeout(() => {
                    setAllProductCardsRendered(true);
                }, 100);
                return () => clearTimeout(timer);
            }
        } else {
            const timer = setTimeout(() => {
                setAllProductCardsRendered(true);
            }, 500);

            return () => clearTimeout(timer);
        }
    }, [maximumHeightNonePhotoLoaded, maximumHeightPhotoLoaded, productsSorting]);

    return (
        <>
            <section
                id={`section-desk-${categoryItems?.id}`}
                className={`${variables.sectionDesk}`}
                ref={ref}
            >
                {allProductCardsRendered ? (
                    <div
                        className={`${variables.menuNameDesk}`}
                        id={`group-${categoryItems[0]?.id}`}
                        style={{
                            color: color,
                            top: coupons && coupons.length > 0 ? "210px" : "86px",
                        }}
                    >
                        <div className="d-flex product-section-title">
                            <div>{categoryItems?.name}</div>
                            {productsSorting.length > 0 ? (
                                <div
                                    className={`${standarDesk}`}
                                    onClick={(e) => handleTagClick(e)}
                                    style={{right: "0"}}
                                >
                                    <svg
                                        width="12"
                                        height="12"
                                        viewBox="0 0 12 12"
                                        fill="none"
                                        xmlns="http://www.w3.org/2000/svg"
                                        className="ms-3"
                                    >
                                        <line
                                            x1="3"
                                            y1="3"
                                            x2="1"
                                            y2="3"
                                            stroke="#404040"
                                            strokeWidth="2"
                                            strokeLinecap="round"
                                        />
                                        <line
                                            x1="11"
                                            y1="3"
                                            x2="6"
                                            y2="3"
                                            stroke="#404040"
                                            strokeWidth="2"
                                            strokeLinecap="round"
                                        />
                                        <line
                                            x1="3"
                                            y1="1"
                                            x2="3"
                                            y2="5.4"
                                            stroke="#404040"
                                            strokeWidth="2"
                                            strokeLinecap="round"
                                        />
                                        <line
                                            x1="9"
                                            y1="9"
                                            x2="11"
                                            y2="9"
                                            stroke="#404040"
                                            strokeWidth="2"
                                            strokeLinecap="round"
                                        />
                                        <line
                                            x1="1"
                                            y1="9"
                                            x2="6"
                                            y2="9"
                                            stroke="#404040"
                                            strokeWidth="2"
                                            strokeLinecap="round"
                                        />
                                        <line
                                            x1="9"
                                            y1="11"
                                            x2="9"
                                            y2="6.6"
                                            stroke="#404040"
                                            strokeWidth="2"
                                            strokeLinecap="round"
                                        />
                                    </svg>
                                </div>
                            ) : null}
                        </div>
                        {isSortOpen ? (
                            <div
                                className={`${sorting} row ${resDesktop} pt-4`}
                                style={{
                                    backgroundColor: "#F5F5F5",
                                    boxShadow: "0px 10px 20px 2px #0202021A",
                                }}
                                onClick={() => {
                                    setIsSortOpen(!isSortOpen);
                                }}
                            >
                                <div className="row d-flex" style={{flexDirection: "column"}}>
                                    <div
                                        className={`${option} d-flex ms-2`}
                                        onClick={() => {
                                            setSelectedOption(standard);
                                            setSortOption(standard);
                                        }}
                                        style={{}}
                                    >
                                        <p
                                            className={`${
                                                language === "en" ? "ms-2" : ""
                                            } text-uppercase`}
                                            style={{
                                                color:
                                                    selectedOption === standard
                                                        ? color
                                                            ? color
                                                            : "#404040"
                                                        : "#404040",
                                            }}
                                        >
                                            {trans("standard")}
                                        </p>
                                    </div>
                                    <div
                                        className={`${option} d-flex ms-2`}
                                        onClick={() => {
                                            setSelectedOption(price);
                                            setSortOption(price);
                                        }}
                                    >
                                        <p
                                            className={`${
                                                language === "en" ? "ms-2" : ""
                                            } text-uppercase`}
                                            style={{
                                                color:
                                                    selectedOption === price
                                                        ? color
                                                            ? color
                                                            : "#404040"
                                                        : "#404040",
                                            }}
                                        >
                                            {trans("sort-by-price-desk-acen")}
                                        </p>
                                    </div>
                                    <div
                                        className={`${option} d-flex ms-2`}
                                        onClick={() => {
                                            setSelectedOption(priceDecen);
                                            setSortOption(priceDecen);
                                        }}
                                    >
                                        <p
                                            className={`${
                                                language === "en" ? "ms-2" : ""
                                            } text-uppercase`}
                                            style={{
                                                color:
                                                    selectedOption === priceDecen
                                                        ? color
                                                            ? color
                                                            : "#404040"
                                                        : "#404040",
                                            }}
                                        >
                                            {trans("sort-by-price-desk-decen")}
                                        </p>
                                    </div>
                                    <div
                                        className={`${option} d-flex ms-2`}
                                        onClick={() => {
                                            setSelectedOption(name);
                                            setSortOption(name);
                                        }}
                                    >
                                        <p
                                            className={`${
                                                language === "en" ? "ms-2" : ""
                                            } text-uppercase`}
                                            style={{
                                                color:
                                                    selectedOption === name
                                                        ? color
                                                            ? color
                                                            : "#404040"
                                                        : "#404040",
                                            }}
                                        >
                                            {trans("sort-by-name")}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        ) : null}
                    </div>
                ) : null}

                <div id={variables.group}>
                    <div className="row ms-0 me-0">
                        {!categoryItems ||
                        !productsSorting ||
                        productsSorting.length === 0 ? (
                            <div className={`${empty} empty-desktop`}>
                                {trans("no-items-desk")}
                            </div>
                        ) : (
                            productsSorting.map((item: any, index: number) => (
                                <ProductCard
                                    key={item.id}
                                    index={index}
                                    item={item}
                                    color={color}
                                    baseLink={baseLink}
                                    isLastProduct={false}
                                    from={from}
                                    handleCloseSuggest=""
                                    groupOrder=""
                                    setHoliday={setHoliday}
                                    workspaceInfo={workspaceInfo}
                                />
                            ))
                        )}
                    </div>
                </div>
            </section>
        </>
    );
});

ProductDesk.displayName = "ProductDesk";

export default ProductDesk;
