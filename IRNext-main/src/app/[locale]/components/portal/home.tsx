'use client'

import React, { useEffect, useState, useRef } from 'react';
import Image from "next/image";
import searchStyle from '/public/assets/css/portal-search.module.scss'
import Link from 'next/link';
import "react-responsive-carousel/lib/styles/carousel.min.css";
import { useI18n } from '@/locales/client'
import Cookies from 'js-cookie';
import Slider, { Settings } from "react-slick";
import 'slick-carousel/slick/slick.css';
import 'slick-carousel/slick/slick-theme.css';
import { api } from "@/utils/axios";
import { useRouter } from 'next/navigation'
import { useAppSelector } from '@/redux/hooks';
import 'splide-nextjs/splide/dist/css/themes/splide-default.min.css';
import style from "public/assets/css/portal.module.scss";
import HeaderPortal from "@/app/[locale]/components/menu/header-portal";
import FooterPortal from "@/app/[locale]/components/menu/footer-portal";
import useMediaQuery from "@mui/material/useMediaQuery";
import PortalLoginDesktopPopup from "@/app/[locale]/components/portal/portalLoginDesktopPopup";
import RegisterConfirm from "@/app/[locale]/components/users/registerConfirm";
import ResetPassword from "@/app/[locale]/components/users/resetPassword";

export default function PortalHome({ isRegisterConfirm, isResetPasswordConfirm, dataResetPassword }: { isRegisterConfirm?: boolean, isResetPasswordConfirm?: boolean, dataResetPassword?: any }) {
    const trans = useI18n();
    const language = Cookies.get('Next-Locale') ?? 'nl';
    const [isProfileUpdatePopupOpen, setIsProfileUpdatePopupOpen] = useState(false);
    const [inputValue, setInputValue] = useState('');
    const [isRegisterConfirmOpen, setIsRegisterConfirmOpen] = useState<any | null>(isRegisterConfirm ?? false);
    const [isResetPasswordConfirmOpen, setIsResetPasswordConfirmOpen] = useState<any | null>(isResetPasswordConfirm ?? false);
    const [getToggleLoginPopUp, setToggleLoginPopUp] = useState(false);

    const togglePopupResetPasswordConfirm = () => {
        setIsResetPasswordConfirmOpen(!isResetPasswordConfirmOpen);
    }

    const togglePopupRegisterConfirm = () => {
        setIsRegisterConfirmOpen(!isRegisterConfirmOpen);
    }

    const toggleProfileUpdatePopup = () => {
        setIsProfileUpdatePopupOpen(!isProfileUpdatePopupOpen);
    }

    const toggleLoginPopUp = () => {
        setToggleLoginPopUp(!getToggleLoginPopUp);
    }
    const router = useRouter();
    const isMobile = useMediaQuery('(max-width: 1279px)');
    const settings: Settings = {
        dots: isMobile ? true : false,
        infinite: false,
        speed: 500,
        slidesToShow: isMobile ? 1 : 3,
        slidesToScroll: isMobile ? 1 : 3,
        arrows: false,
        className: "portal-home-carousel"
    };

    const handleSearch = (item : any) => {
        if (inputValue) {
            router.push(`/search?search=${item?.id}&postcode=${item?.postcode}`);
        }
    }

    const inputRef = useRef<any>(null);
    const [listAddress, setListAddress] = useState<any>([]);
    const [isFocused, setIsFocused] = useState(false);
    const [currentInput, setCurrentInput] = useState('');
    const [isShow, setIsShow] = useState(true);
    const portalAddressCache = useAppSelector<any>((state: any) => state.portalAddress.data);
    const handleSearched = () => {
        setCurrentInput(inputRef.current?.value);
        if (inputRef.current?.value.trim().length !== 0 && inputRef.current?.value.trim().length !== null) {
            if (portalAddressCache.length > 0) {
                const result = portalAddressCache.find((item: any) => item.postcode == inputRef.current?.value);
                if (result) {
                    setListAddress(result);
                } else {
                    setTimeout(function () {
                        api.get(`/addresses?limit=1000&page=1&keyword=${inputRef.current?.value}`, {
                        }).then(res => {
                            if (inputRef.current?.value.trim().length !== 0 || inputRef.current?.value.trim().length !== null) {
                                setListAddress(res?.data?.data?.data);
                            } else {
                                setListAddress(null);
                                setIsShow(false);
                            }
                        }).catch(error => {
                            // console.log(error)
                        });
                    }, 1000);
                }
            } else {
                setTimeout(function () {
                    api.get(`/addresses?limit=1000&page=1&keyword=${inputRef.current?.value}`, {
                    }).then(res => {
                        if (inputRef.current?.value.trim().length > 0) {
                            setListAddress(res?.data?.data?.data);
                        } else {
                            setListAddress(null);
                            setIsShow(false);
                        }
                    }).catch(error => {
                        // console.log(error)
                    });
                }, 1000);
            }
            setIsShow(true);
        } else {
            setListAddress(null);
            setIsShow(false);
        }
    }

    return (
        <>
            <div className="row">
                <div id="header-desktop" className={`${style['header']}`} style={{ width: '100%', zIndex: 1000 }}>
                    <HeaderPortal toggleProfileUpdatePopup={toggleProfileUpdatePopup} toggleLoginPopUp={toggleLoginPopUp} />
                </div>
                <div className="col-md-12">
                    <div className={`row ${style['group-section-1']}`}>
                        <div className={`col-md-5`}>
                            <div className={`${style['section-title']}`}>
                                {trans('portal.craving-something')}
                                <Link href="/"> {trans('portal.tasty')}?</Link>
                            </div>
                            <div className={`${style['section-description']}`}>
                                {trans('lang_portal_lorem')}
                            </div>
                            <div className={`${style['section-search']}`}>
                                <input
                                    type="text"
                                    className={`${style['search-input']}`}
                                    autoComplete={'off'}
                                    ref={inputRef}
                                    onKeyUp={handleSearched}
                                    onFocus={() => setIsFocused(true)}
                                    onBlur={() => setIsFocused(false)}
                                    value={inputValue}
                                    onChange={(e: any) => { setInputValue(e.target.value) }}
                                    placeholder={trans('portal.home-placeholder')}
                                />
                                <div className={`${style['search-icon']}`}>
                                    <svg width="21" height="21" viewBox="0 0 21 21" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M9.55555 17.1111C13.7284 17.1111 17.1111 13.7284 17.1111 9.55555C17.1111 5.38274 13.7284 2 9.55555 2C5.38274 2 2 5.38274 2 9.55555C2 13.7284 5.38274 17.1111 9.55555 17.1111Z" stroke="white" strokeWidth="2.55555" strokeLinecap="round" strokeLinejoin="round"/>
                                        <path d="M18.9999 18.9999L14.8916 14.8916" stroke="white" strokeWidth="2.55555" strokeLinecap="round" strokeLinejoin="round"/>
                                    </svg>
                                </div>
                                {listAddress && listAddress.length > 0 && isShow && (
                                    <div className={searchStyle.listContain} style={isMobile ? {top: "50px", left: 0, width: "100%"} : {top: "65px", left: 0, width: "100%"}}>
                                        {listAddress.map((item: any, index: any) => (
                                            <div role={"button"}
                                                className={`d-flex flex-row justify-content-between ${searchStyle.listGroup} ${index > 0 ? 'no-padding' : ''}`}
                                                key={item.id}
                                                onClick={() => handleSearch(item)}
                                            >
                                                <div>
                                                    <p className='list-group-text ms-3 mt-3'>
                                                        {item.postcode},&nbsp;
                                                        {item.address && item.city && item.address === item.city.name ? item.address : `${item.address ? item.address : ''} ${item.city ? item.city.name : ''}`}
                                                    </p>
                                                </div>
                                            </div>
                                        ))}

                                    </div>
                                )}
                            </div>

                        </div>
                        <div className={`col-md-7 res-desktop`} style={{position: "relative"}}>
                            <Image
                                alt='kokette'
                                src="/img/home-section1.png"
                                width={100}
                                height={100}
                                priority={true}
                                sizes="100vw"
                                style={{ width: '100%', height: 'auto' }} // optional
                            />
                            <div className={`${style['section-popup']}`}>
                                <div className={`${style['popup-title']}`}>
                                    <Image
                                        alt='kokette'
                                        src="/img/icon-home.png"
                                        width={100}
                                        height={100}
                                        priority={true}
                                        sizes="100vw"
                                        style={{ width: '44px', height: '44px' }} // optional
                                    />
                                    {trans('lang_burger_stad')}
                                </div>
                                <div className={`${style['popup-description']}`}>
                                    {trans('lang_deluxe')}
                                    <div className={`${style['popup-icon']}`}>
                                        <svg width="29" height="29" viewBox="0 0 29 29" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M9.0625 21.75C10.0635 21.75 10.875 22.5615 10.875 23.5625C10.875 24.5635 10.0635 25.375 9.0625 25.375C8.06148 25.375 7.25 24.5635 7.25 23.5625C7.25 22.5615 8.06148 21.75 9.0625 21.75Z" stroke="white" strokeWidth="1.8125"/>
                                            <path d="M19.9375 21.7501C20.9385 21.7501 21.75 22.5615 21.75 23.5626C21.75 24.5636 20.9385 25.3751 19.9375 25.3751C18.9365 25.3751 18.125 24.5636 18.125 23.5626C18.125 22.5615 18.9365 21.7501 19.9375 21.7501Z" stroke="white" strokeWidth="1.8125"/>
                                            <path d="M15.7084 15.7083V13.2917M15.7084 13.2917V10.875M15.7084 13.2917H18.1251M15.7084 13.2917H13.2917" stroke="white" strokeWidth="1.8125" strokeLinecap="round"/>
                                            <path d="M2.41675 3.625L2.73238 3.73597C4.30513 4.28892 5.09151 4.5654 5.5413 5.2236C5.99109 5.88182 5.99109 6.75611 5.99109 8.50468V11.7933C5.99109 15.3478 6.06751 16.5207 7.11441 17.625C8.16131 18.7292 9.84627 18.7292 13.2162 18.7292H14.5001M19.6239 18.7292C21.5101 18.7292 22.4532 18.7292 23.1198 18.1859C23.7865 17.6426 23.9769 16.719 24.3577 14.8716L24.9616 11.9417C25.381 9.84029 25.5906 8.78962 25.0543 8.0923C24.5179 7.395 22.6851 7.395 20.649 7.395H13.3201M5.99109 7.395H8.45841" stroke="white" strokeWidth="1.8125" strokeLinecap="round"/>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div className={`col-md-7 res-mobile mt-2`} style={{position: "relative"}}>
                            <Image
                                alt='kokette'
                                src="/img/home-mobile-section1.png"
                                width={100}
                                height={100}
                                priority={true}
                                sizes="100vw"
                                style={{ width: '100%', height: 'auto' }} // optional
                            />
                            <div className={`${style['section-popup']}`}>
                                <div className={`${style['popup-title']}`}>
                                    <Image
                                        alt='kokette'
                                        src="/img/icon-home.png"
                                        width={100}
                                        height={100}
                                        priority={true}
                                        sizes="100vw"
                                        style={{ width: '23px', height: '23px' }} // optional
                                    />
                                    {trans('lang_burger_stad')}
                                </div>
                                <div className={`${style['popup-description']}`}>
                                    {trans('lang_deluxe')}
                                    <div className={`${style['popup-icon']}`}>
                                        <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M4.91042 11.5374C5.42106 11.5374 5.83501 11.9513 5.83501 12.4619C5.83501 12.9726 5.42106 13.3865 4.91042 13.3865C4.39979 13.3865 3.98584 12.9726 3.98584 12.4619C3.98584 11.9513 4.39979 11.5374 4.91042 11.5374Z" stroke="white" strokeWidth="0.924585"/>
                                            <path d="M10.4579 11.5375C10.9685 11.5375 11.3825 11.9514 11.3825 12.4621C11.3825 12.9727 10.9685 13.3866 10.4579 13.3866C9.94729 13.3866 9.53333 12.9727 9.53333 12.4621C9.53333 11.9514 9.94729 11.5375 10.4579 11.5375Z" stroke="white" strokeWidth="0.924585"/>
                                            <path d="M8.30053 8.45543V7.22265M8.30053 7.22265V5.98987M8.30053 7.22265H9.53331M8.30053 7.22265H7.06775" stroke="white" strokeWidth="0.924585" strokeLinecap="round"/>
                                            <path d="M1.52026 2.2915L1.68127 2.34811C2.48356 2.63018 2.8847 2.77122 3.11415 3.10698C3.34359 3.44274 3.34359 3.88873 3.34359 4.78071V6.4583C3.34359 8.27147 3.38257 8.8698 3.91661 9.43312C4.45065 9.99637 5.31018 9.99638 7.02925 9.99638H7.68416M10.2979 9.99638C11.2601 9.99638 11.7412 9.99637 12.0812 9.71925C12.4213 9.44212 12.5184 8.97095 12.7127 8.02855L13.0207 6.53396C13.2347 5.46202 13.3416 4.92606 13.068 4.57035C12.7944 4.21464 11.8595 4.21464 10.8208 4.21464H7.08226M3.34359 4.21464H4.60221" stroke="white" strokeWidth="0.924585" strokeLinecap="round"/>
                                        </svg>
                                    </div>

                                </div>

                            </div>
                        </div>
                    </div>
                    <div className={`row ${style['group-section-2']}`}>
                        <div className={`${style['section-title']}`}>
                            {trans('portal.3-steps')}
                        </div>
                        <div className={`col-md-12 px-0`}>
                            <Slider {...settings}>
                                <div className={`${style['card-slide']}`}>
                                    <div className={`${style['card-slide-content']}`}>
                                        <div className={`${style['card-icon']}`}>
                                            <svg width="59" height="73" viewBox="0 0 59 73" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <g clip-path="url(#clip0_6_137)">
                                                    <path d="M58.3788 28.976C58.3097 35.0864 56.5714 40.4881 53.8787 45.6176C49.8782 53.243 44.4034 59.7311 38.1773 65.5998C35.6206 68.0123 32.871 70.2238 30.1884 72.5042C29.4066 73.1663 28.9924 73.1581 28.2247 72.555C19.7323 65.8923 12.2106 58.3218 6.46364 49.1186C3.51911 44.4033 1.24675 39.3955 0.389789 33.8436C-0.968757 25.0303 1.21832 17.1491 6.93477 10.3239C11.5749 4.78816 17.5371 1.38063 24.6751 0.355118C35.0154 -1.12933 43.849 2.03045 50.9098 9.74716C55.9216 15.2301 58.2813 21.8197 58.3788 28.976ZM7.28202 29.1059C7.26374 41.1765 17.0294 51.0376 29.0289 51.0721C31.9163 51.0863 34.7782 50.5302 37.4502 49.4356C40.1221 48.341 42.5517 46.7296 44.5994 44.6938C46.6471 42.658 48.2726 40.2379 49.3827 37.5723C50.4928 34.9068 51.0656 32.0482 51.0682 29.1608C51.0885 17.1796 41.2842 7.30827 29.3173 7.27578C17.1594 7.24329 7.31045 17.009 7.28202 29.1059Z" fill="#ABA765"/>
                                                    <path d="M34.0408 35.2551C34.0408 33.659 34.0225 32.0608 34.0529 30.4647C34.071 30.2567 34.0303 30.0479 33.9356 29.8619C33.8408 29.676 33.6957 29.5203 33.5168 29.4128C29.758 26.7038 29.2848 20.8533 32.5319 17.5412C34.7759 15.2506 38.0758 15.214 40.3299 17.456C43.6684 20.7701 43.2196 26.7058 39.4059 29.4371C39.2375 29.5384 39.1006 29.6845 39.0106 29.8592C38.9205 30.0339 38.8809 30.2302 38.8962 30.4261C38.9151 33.6441 38.9151 36.8628 38.8962 40.0821C38.9119 40.6068 38.7546 41.122 38.4485 41.5484C38.1425 41.9749 37.7047 42.2888 37.2026 42.4418C36.718 42.6121 36.1903 42.6145 35.7042 42.4488C35.218 42.2831 34.8017 41.9588 34.522 41.528C34.2397 41.0549 34.0798 40.5189 34.057 39.9684C34.0022 38.3987 34.0408 36.8269 34.0408 35.2551Z" fill="#ABA765"/>
                                                    <path d="M19.4461 30.4383C17.5941 30.2149 16.3411 29.3234 15.8863 27.4958C15.8312 27.2746 15.8033 27.0475 15.803 26.8196C15.803 23.6029 15.7725 20.3863 15.8274 17.1696C15.8509 16.93 15.9241 16.6979 16.0423 16.4882C16.1605 16.2784 16.321 16.0955 16.5137 15.9512C17.2996 15.4374 18.2114 16.1035 18.2236 17.1473C18.2419 18.8714 18.2236 20.5995 18.2236 22.3155V23.0384H20.6605V17.8824C20.6605 17.6042 20.6483 17.324 20.6605 17.0458C20.7031 16.3147 21.1742 15.8273 21.8342 15.7989C22.0013 15.7907 22.1684 15.8177 22.3244 15.8781C22.4804 15.9386 22.622 16.0312 22.74 16.1499C22.8579 16.2686 22.9496 16.4108 23.0091 16.5672C23.0685 16.7237 23.0944 16.8909 23.0851 17.0579C23.1075 18.8551 23.0851 20.6543 23.0851 22.4535V23.0424H25.522V21.8728C25.522 20.303 25.522 18.7312 25.522 17.1615C25.522 16.3289 26.0256 15.803 26.7404 15.8091C27.4552 15.8151 27.9365 16.3249 27.9406 17.1046C27.9527 20.3721 27.969 23.6395 27.9406 26.9069C27.9179 27.6843 27.6411 28.4328 27.1526 29.0379C26.6641 29.6431 25.9908 30.0715 25.2356 30.2576C24.9473 30.3327 24.6528 30.3814 24.3117 30.4606V35.6186C24.3117 37.1132 24.3279 38.6099 24.3117 40.1024C24.3211 40.4269 24.2653 40.7499 24.1476 41.0524C24.0299 41.3549 23.8527 41.6308 23.6266 41.8636C23.4004 42.0964 23.1298 42.2815 22.8308 42.4078C22.5319 42.5342 22.2106 42.5993 21.886 42.5993C21.5614 42.5993 21.2401 42.5342 20.9411 42.4078C20.6422 42.2815 20.3716 42.0964 20.1454 41.8636C19.9192 41.6308 19.7421 41.3549 19.6244 41.0524C19.5067 40.7499 19.4509 40.4269 19.4603 40.1024C19.442 37.164 19.4603 34.2235 19.4603 31.2851L19.4461 30.4383Z" fill="#ABA765"/>
                                                </g>
                                                <defs>
                                                    <clipPath id="clip0_6_137">
                                                        <rect width="58.3789" height="73" fill="white"/>
                                                    </clipPath>
                                                </defs>
                                            </svg>
                                        </div>
                                        <div className={`${style['card-title']}`}>
                                            {trans('portal.choose-trader')}
                                        </div>
                                        <div className={`${style['card-description']}`}>
                                            {trans('portal.sub-choose-trader')}.
                                        </div>
                                    </div>
                                    <div className={`${style['group-section-backgroup']}`}></div>
                                </div>

                                <div className={`${style['card-slide']}`}>
                                    <div className={`${style['card-slide-content']}`}>
                                        <div className={`${style['card-icon']}`}>
                                            <svg width="73" height="73" viewBox="0 0 73 73" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <g clip-path="url(#clip0_6_147)">
                                                    <path d="M21.2916 57.7916C19.6126 57.7916 18.25 59.1542 18.25 60.8332C18.25 62.5122 19.6126 63.875 21.2916 63.875C22.9706 63.875 24.3332 62.5124 24.3332 60.8334C24.3332 59.1544 22.9706 57.7916 21.2916 57.7916Z" fill="#ABA765"/>
                                                    <path d="M69.9584 12.1666H42.5834V6.07725C42.5834 2.72139 39.8619 0 36.5061 0H6.07725C2.72139 0 0 2.72139 0 6.07725V66.9228C0 70.2786 2.72139 73 6.07725 73H36.5061C39.862 73 42.5834 70.2786 42.5834 66.9228V54.75H69.9584C71.6382 54.75 73 53.3882 73 51.7084V15.2084C73 13.5285 71.6382 12.1666 69.9584 12.1666ZM66.9166 24.3334H27.375V18.25H39.5416H66.9166V24.3334ZM36.5 66.9166H6.08338V6.08338H36.5V12.1668H24.3334C22.6535 12.1668 21.2918 13.5285 21.2918 15.2084V51.7084C21.2918 53.3882 22.6535 54.75 24.3334 54.75H36.5V66.9166ZM39.5416 48.6666H27.375V30.4166H66.9166V48.6666H39.5416Z" fill="#ABA765"/>
                                                    <path d="M60.9535 36.5H51.5883C49.9761 36.5 48.6653 37.8079 48.6653 39.42V42.705C48.6653 44.3171 49.9763 45.625 51.5883 45.625H60.9535C62.5657 45.625 63.8735 44.3171 63.8735 42.705V39.42C63.8735 37.8079 62.5657 36.5 60.9535 36.5Z" fill="#ABA765"/>
                                                </g>
                                                <defs>
                                                    <clipPath id="clip0_6_147">
                                                        <rect width="73" height="73" fill="white"/>
                                                    </clipPath>
                                                </defs>
                                            </svg>
                                        </div>
                                        <div className={`${style['card-title']}`}>
                                            {trans('portal.choose-payment')}
                                        </div>
                                        <div className={`${style['card-description']}`}>
                                            {trans('portal.sub-choose-payment')}
                                        </div>
                                    </div>
                                    <div className={`${style['group-section-backgroup']}`}></div>
                                </div>
                                <div className={`${style['card-slide']}`}>
                                    <div className={`${style['card-slide-content']}`}>
                                        <div className={`${style['card-icon']}`}>
                                            <svg width="73" height="73" viewBox="0 0 73 73" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <g clip-path="url(#clip0_6_185)">
                                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M56.0845 58.1947C52.7904 61.3953 49.2842 64.7191 49.2842 64.7191L43.2389 54.3987C49.5693 52.8566 55.042 49.202 58.8586 44.2289L65.4696 55.512C65.4696 55.512 60.7565 56.867 56.0845 58.1947ZM36.5 50.2057C23.9121 50.2057 13.7104 39.9789 13.7104 27.3682C13.7104 14.7574 23.9121 4.53055 36.5 4.53055C49.088 4.53055 59.2897 14.7574 59.2897 27.3682C59.2897 39.9789 49.088 50.2057 36.5 50.2057ZM23.7159 64.7191C23.7159 64.7191 20.2096 61.3953 16.9155 58.1947C12.2435 56.867 7.53042 55.512 7.53042 55.512L14.1415 44.2289C17.958 49.202 23.4307 52.8566 29.7612 54.3987L23.7159 64.7191ZM61.6417 39.7759C63.4735 36.1008 64.532 31.9854 64.532 27.6168C64.532 12.3643 51.9806 0 36.5 0C21.0195 0 8.46804 12.3643 8.46804 27.6168C8.46804 31.9854 9.52654 36.1008 11.3584 39.7759L-0.0205078 59.1893C-0.0205078 59.1893 7.19511 60.6379 14.5156 62.1435C19.3975 67.5775 24.2565 73 24.2565 73L34.7184 55.147C35.3115 55.1835 35.8978 55.2359 36.5 55.2359C37.1023 55.2359 37.6886 55.1835 38.2817 55.147L48.7435 73C48.7435 73 53.6025 67.5775 58.4844 62.1435C65.805 60.6379 73.0206 59.1893 73.0206 59.1893L61.6417 39.7759ZM36.5 38.7812C30.1992 38.7812 25.0938 33.6758 25.0938 27.375C25.0938 21.0742 30.1992 15.9688 36.5 15.9688C42.8008 15.9688 47.9063 21.0742 47.9063 27.375C47.9063 33.6758 42.8008 38.7812 36.5 38.7812ZM36.5 11.4062C27.6807 11.4062 20.5313 18.5557 20.5313 27.375C20.5313 36.1943 27.6807 43.3438 36.5 43.3438C45.3193 43.3438 52.4688 36.1943 52.4688 27.375C52.4688 18.5557 45.3193 11.4062 36.5 11.4062Z" fill="#ABA765"/>
                                                </g>
                                                <defs>
                                                    <clipPath id="clip0_6_185">
                                                        <rect width="73" height="73" fill="white"/>
                                                    </clipPath>
                                                </defs>
                                            </svg>
                                        </div>
                                        <div className={`${style['card-title']}`}>
                                            {trans('portal.save-points')}
                                        </div>
                                        <div className={`${style['card-description']}`}>
                                            {trans('portal.sub-save-points')}.
                                        </div>
                                    </div>
                                    <div className={`${style['group-section-backgroup']}`}></div>
                                </div>
                            </Slider>
                        </div>
                    </div>
                    <div className={`row ${style['group-section-3']}`}>
                        <div className={`${style['group-section-backgroup']}`}></div>
                        <div className={`col-md-12 ${style['group-section-content']}`}>
                            <svg className={`${style['section-underline']} res-desktop`} width="415" height="16" viewBox="0 0 415 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M0.852157 9.96344C1.30478 9.77638 1.89553 9.67873 2.51862 9.61873C5.32546 9.30226 6.15134 9.14344 11.4652 8.70108C10.5835 8.66579 9.78112 8.64932 8.94642 8.33873C8.53302 8.17417 8.25367 7.96339 8.14508 7.7341C8.0365 7.50481 8.10376 7.26773 8.33803 7.05402C8.58197 6.73638 9.31675 6.55756 9.89869 6.34226C13.1229 5.2552 20.9908 4.86697 26.869 4.38932C35.1925 3.71873 43.5219 3.03403 51.96 2.64108C58.7964 1.2752 75.5522 0.956379 82.4531 0.829321C92.5989 0.571673 102.756 0.434025 112.917 0.386967C148.492 -0.361269 184.169 0.326967 219.776 0.290497C299.711 2.14461 252.618 1.1305 323.891 3.56697C337.493 3.82344 351.089 4.13638 364.659 4.60814C368.301 4.73991 373.997 4.8905 374.852 5.0505C375.828 5.18697 376.474 5.56108 376.621 5.96461C412.913 6.88108 412.222 6.80932 412.904 6.95285C414.253 7.14226 414.991 7.79285 414.606 8.34108C414.43 8.58812 414.058 8.80569 413.548 8.96133C413.037 9.11697 412.414 9.20231 411.773 9.20461C397.991 9.20344 401.9 9.53167 381.482 8.79755L384.698 9.01755C385.283 9.10697 385.897 9.21403 386.223 9.43873C387.455 9.99403 386.852 11.0834 384.565 11.294C383.572 11.6034 382.679 11.5293 380.413 11.5423C379.072 11.6246 377.729 11.6999 376.38 11.7552C386.561 12.3834 390.044 12.6999 392.836 13.2034C393.55 13.334 394.197 13.514 394.876 13.6705C399.123 14.4952 395.857 16.6093 392.472 15.8305C391.675 15.6623 390.943 15.4481 390.138 15.287C386.899 14.7964 383.428 14.6187 380.039 14.3634C369.309 13.6305 360.873 13.2493 349.099 12.8058C337.352 12.4011 325.604 11.9623 313.827 11.7352C280.648 10.9528 247.436 10.474 214.216 10.0952C173.239 9.59167 192.969 9.77285 144.048 9.64461C99.3382 9.64226 113.123 9.54579 82.5325 10.014C75.2318 10.1199 72.7571 10.2387 63.1962 10.5587C58.141 10.7728 50.4729 11.3881 43.5131 11.6493C42.2757 11.6681 40.962 11.8128 39.7981 11.6058C39.321 11.5332 38.9039 11.4087 38.5939 11.2464C38.2839 11.0841 38.0933 10.8903 38.0434 10.687L38.0346 10.6564C17.8842 11.6058 12.4733 11.6799 2.9301 11.9717C2.53724 11.9784 2.14691 11.9435 1.79854 11.8705C1.32104 11.7981 0.903645 11.6737 0.593565 11.5113C0.283484 11.349 0.0930877 11.1551 0.0439062 10.9517C-0.1295 10.5905 0.232008 10.2281 0.852157 9.96344Z" fill="#ABA765"/>
                            </svg>
                            <svg className={`${style['section-underline']} res-mobile`} width="274" height="12" viewBox="0 0 274 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M-17.4008 6.98471C-17.0823 6.85309 -16.6666 6.78439 -16.2282 6.74217C-14.2533 6.5195 -13.6722 6.40775 -9.93325 6.0965C-10.5536 6.07167 -11.1182 6.06008 -11.7055 5.84154C-11.9964 5.72576 -12.1929 5.57745 -12.2693 5.41612C-12.3457 5.25478 -12.2984 5.08797 -12.1336 4.93761C-11.9619 4.71411 -11.4449 4.58828 -11.0355 4.4368C-8.7669 3.67193 -3.2309 3.39876 0.905083 3.06268C6.76163 2.59085 12.6223 2.10908 18.5595 1.8326C23.3696 0.871542 35.1592 0.647215 40.0149 0.557815C47.1536 0.37653 54.3005 0.279678 61.4496 0.246568C86.4805 -0.279902 111.584 0.204351 136.638 0.17869C192.881 1.48327 159.745 0.769726 209.894 2.48406C219.465 2.66452 229.031 2.88471 238.579 3.21665C241.141 3.30936 245.149 3.41532 245.751 3.52789C246.437 3.62392 246.892 3.88715 246.996 4.17108C272.531 4.81592 272.045 4.76543 272.525 4.86642C273.474 4.99969 273.993 5.45745 273.722 5.8432C273.598 6.01701 273.337 6.17011 272.978 6.27961C272.619 6.38912 272.18 6.44917 271.729 6.45079C262.032 6.44996 264.783 6.68091 250.416 6.16438L252.679 6.31917C253.09 6.38209 253.522 6.45741 253.752 6.61552C254.618 7.00623 254.194 7.77276 252.585 7.92093C251.887 8.13864 251.258 8.08649 249.663 8.09559C248.72 8.15354 247.775 8.20652 246.826 8.24542C253.99 8.68746 256.44 8.91013 258.405 9.26442C258.907 9.35631 259.362 9.48296 259.84 9.59305C262.828 10.1733 260.531 11.6608 258.148 11.1129C257.588 10.9945 257.073 10.8438 256.506 10.7304C254.227 10.3852 251.785 10.2602 249.401 10.0806C241.851 9.56491 235.915 9.29671 227.631 8.98463C219.365 8.69987 211.1 8.39111 202.813 8.23135C179.468 7.68087 156.099 7.34397 132.725 7.07742C103.893 6.72313 117.775 6.85061 83.3537 6.76038C51.8955 6.75873 61.5943 6.69085 40.0707 7.02031C34.9338 7.09481 33.1926 7.17841 26.4654 7.40357C22.9085 7.55422 17.5131 7.98715 12.6161 8.17092C11.7455 8.18417 10.8211 8.28598 10.0022 8.14029C9.66645 8.08921 9.37302 8.00163 9.1549 7.88741C8.93679 7.77319 8.80265 7.63687 8.76758 7.4938L8.76137 7.47227C-5.41676 8.14029 -9.22393 8.19244 -15.9387 8.39773C-16.2151 8.40247 -16.4897 8.37791 -16.7349 8.32654C-17.0708 8.27562 -17.3645 8.18809 -17.5827 8.07384C-17.8009 7.95959 -17.9348 7.82318 -17.9695 7.68005C-18.0915 7.42592 -17.8371 7.17096 -17.4008 6.98471Z" fill="#ABA765"/>
                            </svg>
                            <div className={`${style['section-title']}`}>
                                {trans('portal.download-app') + " "}
                                <div className={`${style['section-app']}`}>
                                    {trans('lang_itsready_app')}
                                </div>
                            </div>
                            <div className={`${style['section-description']}`}>
                                {trans('portal.sub-download-app')}.
                            </div>
                            <div className={`${style['section-check-list']}`}>
                                <span>✓</span> {trans('portal.dishes-at-hand')}.
                            </div>
                            <div className={`${style['section-check-list']}`}>
                                <span>✓</span> {trans('portal.promotions-and-offers')}.
                            </div>
                            <div className={`${style['section-check-list']}`}>
                                <span>✓</span> {trans('portal.loyalty-merchants-together')}.
                            </div>
                            <div className={`${style['section-download']}`}>
                                <Image
                                    alt='kokette'
                                    src="/img/appstore.png"
                                    width={100}
                                    height={100}
                                    priority={true}
                                    sizes="100vw"
                                    style={{ width: '177px', height: '61px', marginRight: "30px" }} // optional
                                />
                                <Image
                                    alt='kokette'
                                    src="/img/ggplay.png"
                                    width={100}
                                    height={100}
                                    priority={true}
                                    sizes="100vw"
                                    style={{ width: '206px', height: '61px' }} // optional
                                />
                            </div>
                        </div>
                        <div className={`col-md-12 ${style['section-pr']} res-desktop`}>
                            <Image
                                alt='kokette'
                                src="/img/pr.png"
                                width={100}
                                height={100}
                                priority={true}
                                sizes="100vw"
                                style={{ width: '156px', height: '150px' }} // optional
                            />
                        </div>
                        <div className={`col-md-12 ${style['section-image']} res-desktop`}>
                            <Image
                                alt='kokette'
                                src="/img/home-phone.png"
                                width={100}
                                height={100}
                                priority={true}
                                sizes="100vw"
                                style={{ width: '475px', height: '780px' }} // optional
                            />
                        </div>
                        <div className={`col-md-12 ${style['section-image']} res-mobile`}>
                            <Image
                                alt='kokette'
                                src="/img/home-phone.png"
                                width={100}
                                height={100}
                                priority={true}
                                sizes="100vw"
                                style={{ width: '320px', height: '636px' }} // optional
                            />
                        </div>
                    </div>
                    {getToggleLoginPopUp && (
                        <PortalLoginDesktopPopup getToggleLoginPopUp={getToggleLoginPopUp} setToggleLoginPopUp={setToggleLoginPopUp} />
                    )}
                    {isRegisterConfirmOpen && (
                        <RegisterConfirm togglePopup={() => togglePopupRegisterConfirm()} />
                    )}

                    {isResetPasswordConfirmOpen && (
                        <ResetPassword togglePopup={() => togglePopupResetPasswordConfirm()} token={dataResetPassword?.token} email={dataResetPassword?.email} />
                    )}
                </div>
                <FooterPortal trans={trans} lang={language} from = {null}/>
            </div>

        </>
    );
};
