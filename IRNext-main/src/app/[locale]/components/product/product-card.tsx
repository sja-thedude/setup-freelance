import React, { memo, useEffect, useState, useRef, useMemo, useLayoutEffect } from "react";
import Image from "next/image";
import variables from "/public/assets/css/food.module.scss";
import LabelFavoriteList from "../ordering/labels/favorite/list";
import LabelFavotiteListDesk from "../ordering/labels/favorite/deskList";
import style from "public/assets/css/product.module.scss";
import Cookies from "js-cookie";
import {useI18n} from "@/locales/client";
import FavoriteCard from "./favorite-card";
import useMediaQuery from "@mui/material/useMediaQuery";
import ProductDetailPopup from "@/app/[locale]/components/desktop/productDetailPopup";
import ProductDetailMobilePopup from "@/app/[locale]/components/product/productDetailMobilePopup";
import { handleShowCartItem, manualChangeOrderTypeDesktop } from "@/redux/slices/cartSlice";
import { useAppSelector, useAppDispatch } from "@/redux/hooks";
import { usePathname } from "next/navigation";
import FavoriteListProductMobile from "@/app/[locale]/components/img/favorite/favoriteListProductMobile";
import KoketteListProductMobile from "@/app/[locale]/components/img/kokette/koketteListProductMobile";
import FavoriteListProductDesk from "@/app/[locale]/components/img/favorite/favoriteListProductDesk";
import KoketteListProductDesk from "@/app/[locale]/components/img/kokette/koketteListProductDesk";
import {
  addMaxHeightNonePhoto,
  addMaxHeightPhoto,
  addMaxHeightPhotoLoaded,
  addMaxHeightNonePhotoLoaded,
} from "@/redux/slices/cartSlice";
import { batch } from "react-redux";

const titleText = variables["title-text"];
const titleTextCartDesk = variables["title-text-cart-desk"];
const titleFav = variables["title-fav"];
const titleKoke = variables["title-koke"];
const productDetail = variables["product-detail"];
const productDetailDesk = variables["product-detail-desk"];
const productPhoto = variables["product-photo"];
const productNonePhoto = variables["product-none-photo"];
const productDescription = variables["description-food"];
const productWI = variables["description-food-without"];
const productDesWI = variables["description-food-without-desk"];
const productImage = variables["product-imagin"];
const productImageDesk = variables["product-imagin-desk"];
const pricing = variables["pricing"];
const pricingSuggestion = variables["pricing-suggestion"];
const currency = "€";

const ProductCard = memo(
  ({
    index,
    item,
    color,
    isLastProduct,
    from,
    baseLink,
    handleCloseSuggest,
    groupOrder,
    setHoliday,
    workspaceInfo,
  }: {
    index: number;
    item: any;
    color: string;
    isLastProduct: boolean;
    from: string;
    baseLink: string;
    handleCloseSuggest: any;
    groupOrder: any;
    setHoliday?: any;
    workspaceInfo?: any;
  }) => {
    const trans = useI18n()
    const tokenLoggedInCookie = Cookies.get("loggedToken");
    const isMobile = useMediaQuery("(max-width: 1279px)");
    const heightCalculation = useAppSelector((state) => state.product?.heightCalculation)
    const maxHeightNonePhotoSetting = useAppSelector((state) => state.cart.maxHeightNonePhoto);
    const maxHeightPhotoSetting = useAppSelector((state) => state.cart.maxHeightPhoto);
    const isShowCartItem = useAppSelector((state) => state.cart.showCartItem);
    const handleTotalAttrMealWidth = (width: number) => {
      let finalWidth = width;
      const filteredLabels = item.labels.filter(
        (label: any) => label.type !== 0
      );
      const count = filteredLabels.length;
      if (count >= 2) {
        finalWidth += 20;
      }
    };

    const [showProductDetailMobilePopup, setShowProductDetailMobilePopup] = useState(false);
    const navigateToProductDetail = () => {
      setShowProductDetailMobilePopup(true);
      if (from == "productSuggestion") {
        Cookies.set("productSuggestion", "true");
      }
    };

    const [isHovered, setIsHovered] = useState(false);

    const handleMouseEnter = () => {
      setIsHovered(true);
    };

    const handleMouseLeave = () => {
      setIsHovered(false);
    };

    const [showProductDetailPopup, setShowProductDetailPopup] = useState(false);
    const dispatch = useAppDispatch();
    const handleProductDetailPopup = () => {
      dispatch(manualChangeOrderTypeDesktop(false));
      setShowProductDetailPopup(true);
    };

    const pathName = usePathname();
    const isTableOrdering = pathName.includes("table-ordering");
    const isSelfOrdering = pathName.includes("self-ordering");

    const productPhotoRef = useRef<NodeListOf<HTMLElement> | null>(null);
    const productNonePhotoRef = useRef<NodeListOf<HTMLElement> | null>(null);

    useLayoutEffect(() => {
      requestAnimationFrame(() => {
        setTimeout(() => { 
          productPhotoRef.current = document.querySelectorAll(`.${productPhoto}`);
          productNonePhotoRef.current = document.querySelectorAll(`.${productNonePhoto}`);
    
          const updateMaxHeight = async (elements: HTMLElement[], dispatchAction: any, loadedAction: any, minHeight: number) => {
            if (!elements.length) return;
    
            let maxHeight = Math.ceil(
              Math.max(...elements.map((el) => el.offsetHeight))
            );
    
            maxHeight = Math.max(maxHeight, minHeight);
    
            if (maxHeight > 0) {
              elements.forEach((el) => (el.style.height = `${maxHeight}px`));
    
              batch(() => {
                dispatch(dispatchAction(maxHeight));
                dispatch(loadedAction(true));
                dispatch(handleShowCartItem(true));
              });
            }
          };
    
          updateMaxHeight(Array.from(productPhotoRef.current || []), addMaxHeightPhoto, addMaxHeightPhotoLoaded, 225);
          updateMaxHeight(Array.from(productNonePhotoRef.current || []), addMaxHeightNonePhoto, addMaxHeightNonePhotoLoaded, 225);
        }, 300);
      });
    }, [productPhotoRef, productNonePhotoRef, from, heightCalculation, dispatch]);
    
    useEffect(() => {
      if (
        Cookies.get("currentProductId") == item.id &&
        Cookies.get("groupOrder")
      ) {
        setShowProductDetailMobilePopup(true);
        Cookies.remove("currentProductId");
      }
    }, [item.id]);

    useEffect(() => {
      const pricingElement = document.getElementById(`pricing-${item.id}`);
      const labelingElement = document.getElementById(`labeling-${item.id}`);

      if (
        pricingElement &&
        labelingElement &&
        !labelingElement.getAttribute("style")
      ) {
        const pricingWidth = pricingElement.offsetWidth;

        if (labelingElement) {
          labelingElement.style.paddingRight = `${pricingWidth}px`;
        }
      }
    });

    const calculateMarginBottom = () => {
      if (isMobile && isLastProduct) {
        const holidayHeight = Cookies.get("holidayHeight");
        if (holidayHeight) {
          return `${parseInt(holidayHeight) + 90}px`;
        } else {
          return "50%";
        }
      }
    };

    const isVisible = useMemo(() => {
      return  (maxHeightPhotoSetting !== 0 || maxHeightNonePhotoSetting !== 0) && isShowCartItem;
    }, [maxHeightPhotoSetting, maxHeightNonePhotoSetting, isShowCartItem]);

    return (
      <>
        {isMobile ? (
          <div
            className={`${productDetail}`}
            style={{ marginBottom: calculateMarginBottom() }}
            id={`product-${item.id}`}
          >
            <div onClick={() => {
              if (item.is_available) {
                navigateToProductDetail();
              }
            }}>
              <div>
                <div className={variables.title}>
                  <div className={`${titleText} ms-2`}>
                    <h1 className="ms-1 word-break">{item.name}</h1>
                  </div>
                  <div
                    className={`${titleFav} mt-1`}
                    style={{
                      marginLeft: !item?.category?.kokette_kroket ? "30px" : "",
                    }}
                  >
                    {item?.category?.favoriet_friet == true ? (
                      <FavoriteListProductMobile />
                    ) : null}
                  </div>
                  <div className={`${titleKoke} mt-1`}>
                    {item?.category?.kokette_kroket == true ? (
                      <KoketteListProductMobile />
                    ) : null}
                  </div>
                </div>
                <div
                  className={`${variables.containAll} ps-1`}
                  style={{
                    minHeight: item.photo ? "79px" : "",
                    paddingRight: item.photo ? "130px" : "",
                  }}
                >
                  {item.photo ? (
                    <>
                      <div
                        className={`d-flex align-items-start ps-2`}
                        style={{ padding: 0 }}
                      >
                        <div className={`${productDescription} word-break`}>
                          <p>
                            {item?.description &&
                            item?.description.length > 137 ? (
                              <>{item?.description.slice(0, 137)}...</>
                            ) : (
                              <>{item?.description}</>
                            )}
                          </p>
                        </div>
                      </div>
                      <div
                        className={`${variables.photoContain} pe-2`}
                        style={{ textAlign: "center" }}
                      >
                        <Image
                          className={`${productImage}`}
                          alt="food"
                          src={item.photo ? item.photo : 'https://fakeimg.pl/120x79'}
                          width={120}
                          height={79}
                          sizes="200vw"
                          style={{
                            right: from == "productSuggestion" ? "5px" : "4px",
                          }}
                        />
                      </div>
                    </>
                  ) : (
                    <div
                      className={`d-flex align-items-start word-break ${productWI} col-12`}
                    >
                      <div
                        className={productWI}
                        style={{ paddingRight: "8px" }}
                      >
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
            {window.location.href.includes("category/cart") ? (
              <div className={`row ps-1 pe-1 mt-3 mb-1 d-flex`}>
                <div
                  key={item.id}
                  className={`${style["labels"]} d-flex ${variables.labels} labeling`}
                  id={`labeling-${item.id}`}
                >
                  <LabelFavoriteList
                    labels={item?.labels}
                    color={color}
                    onTotalAttrMealWidth={handleTotalAttrMealWidth}
                  />
                  {!isTableOrdering && !isSelfOrdering && (
                    <div className={variables.favContain}>
                      <FavoriteCard
                        key={item.id}
                        index={item.id}
                        item={item}
                        color={color}
                        width={17}
                        height={15}
                        type={"null"}
                      />
                    </div>
                  )}
                </div>
                <div
                  className={`${pricingSuggestion} d-flex`}
                  style={{ color: color ? color : "black" }}
                  id={`pricing-${item.id}`}
                >
                  {currency}
                  {item.price}
                </div>
              </div>
            ) : (
              <div
                className={`${variables.notScrolled} row ps-1 pe-1 mt-3 mb-1`}
              >
                <div
                  key={item.id}
                  className={`${style["labels"]} d-flex ${variables.labels} labeling`}
                  id={`labeling-${item.id}`}
                >
                  <LabelFavoriteList
                    labels={item?.labels}
                    color={color}
                    onTotalAttrMealWidth={handleTotalAttrMealWidth}
                  />
                  {!isTableOrdering && !isSelfOrdering && (
                    <div className={variables.favContain}>
                      <FavoriteCard
                        key={item.id}
                        index={item.id}
                        item={item}
                        color={color}
                        width={17}
                        height={15}
                        type={"null"}
                      />
                    </div>
                  )}
                </div>
                <div
                  className={`${pricing} d-flex`}
                  style={{ color: color ? color : "black" }}
                  id={`pricing-${item.id}`}
                >
                  {currency}
                  {item.price}
                </div>
              </div>
            )}
            <ProductDetailMobilePopup
              showPopup={showProductDetailMobilePopup}
              setShowProductDetailMobilePopup={
                setShowProductDetailMobilePopup
              }
              productInfo={item}
              productId={item.id}
              baseLink={baseLink}
            />
            {!item.is_available && <div className={`${variables['not-available']}`}>
              <span>{trans("not-available")}</span>
            </div>}
          </div>
        ) : (
          <>
            {/* {idShow && ( */}
              <div
                className={`${productDetailDesk} product-card-detail-item ${item.photo ? productPhoto : productNonePhoto} res-desktop product-item ${isVisible ? variables.visible : variables.hidden}`}
                style={{
                  marginBottom: isMobile && isLastProduct ? "50%" : "",
                  ...((maxHeightPhotoSetting !== 0 || maxHeightNonePhotoSetting !== 0) && { height: item?.photo ? `${maxHeightPhotoSetting}px` : `${maxHeightNonePhotoSetting}px` }),
                }}
                id={`product-${item.id}`}
                onClick={handleProductDetailPopup}
              >
                <div className={`${variables.deskContain}`}>
                  <div
                    className={`${variables.heartDesk}`}
                    style={{ display: "inline-block" }}
                  >
                    <FavoriteCard
                      key={item.id}
                      index={item.id}
                      item={item}
                      color={color}
                      width={17}
                      height={15}
                      type={"null"}
                    />
                  </div>
                  {item?.allergenens && item?.allergenens.length > 0 && (
                    <div
                      className={`${variables.infoDesk}`}
                      style={{ display: "inline-block" }}
                      onMouseLeave={(e: any) => {
                        e.stopPropagation();
                        handleMouseLeave();
                      }}
                      onMouseEnter={(e: any) => {
                        e.stopPropagation();
                        handleMouseEnter();
                      }}
                    >
                      <svg
                        width="31"
                        height="31"
                        viewBox="0 0 31 31"
                        fill="none"
                        xmlns="http://www.w3.org/2000/svg"
                      >
                        <circle cx="15.5" cy="15.5" r="15.5" fill="white" />
                        <circle
                          cx="15.5"
                          cy="15.5"
                          r="9.5"
                          stroke={"#404040"}
                          strokeWidth="2"
                        />
                        <path
                          d="M16.3752 12.2938C16.0672 12.2938 15.8059 12.1865 15.5912 11.9718C15.3765 11.7571 15.2692 11.4958 15.2692 11.1878C15.2692 10.8798 15.3765 10.6185 15.5912 10.4038C15.8059 10.1798 16.0672 10.0678 16.3752 10.0678C16.6832 10.0678 16.9445 10.1798 17.1592 10.4038C17.3832 10.6185 17.4952 10.8798 17.4952 11.1878C17.4952 11.4958 17.3832 11.7571 17.1592 11.9718C16.9445 12.1865 16.6832 12.2938 16.3752 12.2938ZM15.4232 20.1338C14.9752 20.1338 14.6112 19.9938 14.3312 19.7138C14.0605 19.4338 13.9252 19.0138 13.9252 18.4538C13.9252 18.2205 13.9625 17.9171 14.0372 17.5438L14.9892 13.0498H17.0052L15.9972 17.8098C15.9599 17.9498 15.9412 18.0991 15.9412 18.2578C15.9412 18.4445 15.9832 18.5798 16.0672 18.6638C16.1605 18.7385 16.3099 18.7758 16.5152 18.7758C16.6832 18.7758 16.8325 18.7478 16.9632 18.6918C16.9259 19.1585 16.7579 19.5178 16.4592 19.7698C16.1699 20.0125 15.8245 20.1338 15.4232 20.1338Z"
                          fill={"#404040"}
                        />
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
                          width={49}
                          height={49}
                          quality={100}
                          priority
                          unoptimized={true}
                          key={"allergenen-" + allergenen.id}
                        />
                      ))}
                    </div>
                  )}
                  <div>
                    {item.photo ? (
                      <>
                        <div className={`${variables.photoContainDesk}`}>
                          <Image
                            className={`${productImageDesk}`}
                            alt="food"
                            src={item.photo}
                            width={600}
                            height={400}
                            loading="lazy"
                            style={{ minHeight: "191px" }}
                          />
                        </div>
                      </>
                    ) : tokenLoggedInCookie ? (
                      <div className={`${variables.nothing}`}></div>
                    ) : item?.allergenens && item?.allergenens.length > 0 ? (
                      <div className={`${variables.nothing}`}></div>
                    ) : null}
                    <div className={variables.title}>
                      <div className={`${titleTextCartDesk} word-break mt-2`}>
                        <h1>{item.name}</h1>
                      </div>
                      <div
                        className="d-flex kokette-icon"
                        style={{ marginRight: "9px" }}
                      >
                        <div
                          className={`${titleFav} mt-1`}
                          style={{
                            marginLeft: !item?.category?.kokette_kroket
                              ? "30px"
                              : "",
                          }}
                        >
                          {item?.category?.favoriet_friet == true ? (
                            <FavoriteListProductDesk />
                          ) : null}
                        </div>
                        <div className={`${titleKoke} mt-1`}>
                          {item?.category?.kokette_kroket == true ? (
                            <KoketteListProductDesk />
                          ) : null}
                        </div>
                      </div>
                    </div>
                    <div className={`${style.desContainer} word-break`}>
                      {item.photo ? (
                        <>
                          <div
                            className={`d-flex align-items-start col-sm-12 col-12`}
                          >
                            <div className={`${productDescription}`}>
                              {item?.description &&
                              item?.description.length > 140 ? (
                                <>{item?.description.slice(0, 140)}...</>
                              ) : (
                                <>{item?.description}</>
                              )}
                            </div>
                          </div>
                        </>
                      ) : (
                        <div
                          className={`d-flex align-items-start ${productDesWI} col-12`}
                        >
                          <div
                            className={productDesWI}
                            style={{ paddingRight: "8px" }}
                          >
                            {item?.description &&
                            item?.description.length > 157 ? (
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
                <div
                  key={item.id}
                  className={`${style["labels"]} pe-2 mb-2 ${style.marginLeft12}`}
                  style={{
                    width: '95%'
                  }}
                >
                  <LabelFavotiteListDesk
                    labels={item?.labels}
                    color={color}
                    price={item.price}
                    from="product-list"
                  />
                </div>
              </div>
            {/* )} */}
            {showProductDetailPopup == true && (
              <ProductDetailPopup
                workspaceInfo={workspaceInfo}
                showProductDetailPopup={showProductDetailPopup}
                setShowProductDetailPopup={setShowProductDetailPopup}
                color={color}
                productInfo={item}
                productId={item.id}
                setHoliday={setHoliday}
              />
            )}
          </>
        )}
      </>
    );
  }
);

ProductCard.displayName = "ProductCard";

export default ProductCard;
