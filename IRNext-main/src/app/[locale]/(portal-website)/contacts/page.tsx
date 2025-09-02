"use client"

import { useI18n } from '@/locales/client'
import React, { useState } from "react";
import { api } from "@/utils/axios";
import Cookies from "js-cookie";
import style from "public/assets/css/portal.module.scss";
import HeaderPortal from "@/app/[locale]/components/menu/header-portal";
import FooterPortal from "@/app/[locale]/components/menu/footer-portal";
import 'slick-carousel/slick/slick.css';
import 'slick-carousel/slick/slick-theme.css';
import variables from '/public/assets/css/portal-search.module.scss'
import Image from "next/image";
import Link from "next/link";
import * as Yup from "yup";
import {useFormik} from "formik";
import {Button} from "react-bootstrap";

export default function Page() {
    const trans = useI18n();
    const language = Cookies.get('Next-Locale') ?? 'nl';
    const [isProfileUpdatePopupOpen, setIsProfileUpdatePopupOpen] = useState(false);

    const toggleProfileUpdatePopup = () => {
        setIsProfileUpdatePopupOpen(!isProfileUpdatePopupOpen);
    }
    const [getToggleLoginPopUp, setToggleLoginPopUp] = useState(false);
    const [isSuccess, setIsSuccess] = useState(false);
    const toggleLoginPopUp = () => {
        setToggleLoginPopUp(!getToggleLoginPopUp);
    }

    const validationSchema = Yup.object().shape({
        first_name: Yup.string().required(trans('fill-all')),
        email: Yup.string().required(trans('fill-all'))
            .email(trans('job.message_invalid_email')),
        last_name: Yup.string().required(trans('fill-all')),
    });

    const formik = useFormik({
        initialValues: {
            first_name: "",
            email: "",
            phone: "",
            content: "",
            last_name: "",
        },
        enableReinitialize: true,
        validateOnMount: true,
        validationSchema,
        onSubmit: async (values, {resetForm}) => {
            try {
                const headers = {
                    'Content-Language': language,
                };
                const response = await api.post(`/contacts/to_admin`, {
                    email: values.email,
                    first_name: values.first_name,
                    last_name: values.last_name,
                    phone: values.phone,
                    content: values.content,
                }, { headers });
                if ('data' in response) {
                    setIsSuccess(true)
                    resetForm();
                }
            } catch (error: any) {
                //console.log(error.response.data.message);
            }
        },
    });

    return (
        <>
            <div className="row">
                <div id="header-desktop" className={`${style['header']}`} style={{ width: '100%', zIndex: 1000 }}>
                    <HeaderPortal toggleProfileUpdatePopup={toggleProfileUpdatePopup} toggleLoginPopUp={toggleLoginPopUp} />
                </div>
                <div className="col-md-12" style={{background: "#FFF"}}>
                    <div className={`row ${style['group-contact-intro']}`}>
                        <svg className="res-desktop" style={{position: "absolute", left: "49%", transform: "translate(-50%, -25%)"}} width="246" height="77" viewBox="0 0 246 77" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M12.2884 53.6408C2.27162 47.6752 -0.297929 36.1954 4.42704 27.5863C9.76892 17.8651 22.7033 12.1988 35.6265 10.7673C43.9042 9.85631 52.3856 10.416 60.6486 11.6803C69.0267 12.961 77.3223 14.8135 85.4382 16.9715C93.2221 19.0393 101.042 21.3441 108.429 24.2547C114.067 26.4737 119.744 29.3402 123.018 33.7711C126.283 38.1763 125.918 43.3906 122.441 47.4666C119.022 51.4629 113.16 54.1081 106.952 54.1427C93.6485 54.2083 84.0303 42.696 85.1742 32.9909C86.517 21.5002 98.7025 13.3877 112.036 10.6289C126.485 7.63836 142.779 9.54795 156.878 13.673C171.008 17.8085 183.929 24.7139 194.021 33.6897C203.594 42.2191 210.613 52.5034 214.384 63.4311C215.316 66.1246 216.035 68.8533 216.551 71.6014C216.852 73.1961 213.764 73.6582 213.464 72.0551C209.418 50.6955 192.691 31.4035 168.732 20.7212C156.44 15.2411 142.467 12.0457 128.543 11.7666C115.041 11.4919 100.746 14.5434 92.8801 23.6276C89.4755 27.5663 87.4801 32.516 88.4812 37.3991C89.4021 41.9165 92.5975 46.2581 97.3437 48.984C102.1 51.7107 107.992 52.4242 113.203 50.47C118.034 48.6567 121.998 44.8742 122.18 40.4673C122.377 35.8272 118.12 32.0139 113.562 29.4685C107.996 26.3673 101.464 24.3787 95.2054 22.4259C87.6113 20.0617 79.8671 18.0301 72.0248 16.3432C64.4321 14.7156 56.7145 13.3638 48.8889 12.9504C35.7028 12.2458 21.8576 14.6175 12.6399 22.3181C4.96072 28.7285 2.7389 38.8771 8.73111 46.9133C10.0839 48.7322 11.8969 50.3237 14.028 51.5944C15.6717 52.5799 13.9425 54.627 12.2884 53.6408Z" fill="#CDCDCD"/>
                            <path d="M212.681 73.7245C203.69 68.3663 193.081 64.9792 182.039 63.9977C180.011 63.814 180.186 61.3151 182.214 61.4988C193.791 62.5262 204.982 66.0543 214.421 71.6781C216.075 72.656 214.346 74.7114 212.681 73.7245Z" fill="#CDCDCD"/>
                            <path d="M213.525 72.9892C220.332 65.1451 229.521 58.7565 240.217 54.4153C242.012 53.6871 243.438 55.9632 241.654 56.6838C231.426 60.8401 222.658 66.9482 216.161 74.4457C215.023 75.7472 212.387 74.2991 213.525 72.9892Z" fill="#CDCDCD"/>
                        </svg>

                        <div className={`col-md-6 px-0`}>
                            <div className={`${style['contact-title']}`}>
                                {trans('portal.contact-with-us')}
                            </div>
                            <div className={`${style['contact-description']}`}>
                                {trans('portal.sub-contact-with-email')}
                                <Link href={`#`} onClick={(e) => {
                                    window.location.href = "mailto:info@itsready.be";
                                    e.preventDefault();
                                }}>info@itsready.be.</Link>
                            </div>
                            <div className={`${style['contact-img']} res-desktop`}>
                                <Image src="/img/contact-img.png"
                                       alt="contact-us"
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
                                            src="/img/icon-contact.png"
                                            width={100}
                                            height={100}
                                            priority={true}
                                            sizes="100vw"
                                            style={{ width: '44px', height: '44px' }} // optional
                                        />
                                        Het Baksalon
                                    </div>
                                    <div className={`${style['popup-description']}`}>
                                        Ontbijtbox met éclairs en croissaints
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
                        </div>
                        <div className={`col-md-6 ${style['group-contact-form']}`} style={{position: "relative"}}>
                            <form onSubmit={formik.handleSubmit} noValidate>
                                <div className={`container-fluid ${style['contact-form']}`}>
                                    <div className={`row ${style['group-contact-field']}`}>
                                        <div className="col-12">
                                            <div className={`${style['form-label']}`}>{trans('first-name')}*</div>
                                            <input type="text" name="first_name"
                                                   onChange={formik.handleChange}
                                                   onBlur={formik.handleBlur}
                                                   value={formik.values.first_name}
                                                   className={`form-control ${formik.touched.first_name && formik.errors.first_name ? style["form-custom-contact-invalid"] : "form-custom-contact"}`}
                                                   placeholder={trans('portal.typy-here-first-name')}/>
                                        </div>
                                    </div>
                                    <div className={`row ${style['group-contact-field']}`}>
                                        <div className="col-12">
                                            <div className={`${style['form-label']}`}>{trans('last-name')}*</div>
                                            <input type="text" name="last_name"
                                                   onChange={formik.handleChange}
                                                   onBlur={formik.handleBlur}
                                                   value={formik.values.last_name}
                                                   className={`form-control ${formik.touched.last_name && formik.errors.last_name ? style["form-custom-contact-invalid"] : "form-custom-contact"}`}
                                                   placeholder={trans('portal.typy-here-last-name')}/>
                                        </div>
                                    </div>
                                    <div className={`row ${style['group-contact-field']}`}>
                                        <div className="col-12">
                                            <div className={`${style['form-label']}`}>{trans('email')}*</div>
                                            <input type="email" name="email"
                                                   onChange={formik.handleChange}
                                                   onBlur={formik.handleBlur}
                                                   value={formik.values.email}
                                                   className={`form-control ${formik.touched.email && formik.errors.email ? style["form-custom-contact-invalid"] : "form-custom-contact"}`}
                                                   placeholder={trans('portal.typy-here-email')}/>
                                        </div>
                                    </div>
                                    <div className={`row ${style['group-contact-field']}`}>
                                        <div className="col-12" style={{position:'relative'}}>
                                            <div className={`${style['form-label']}`}>{trans('phone')}</div>
                                            <input type="text" name="phone"
                                                   onChange={formik.handleChange}
                                                   onBlur={formik.handleBlur}
                                                   value={formik.values.phone}
                                                   className={`form-control ${formik.errors.phone ? style["form-custom-contact-invalid"] : "form-custom-contact"}`}
                                                   placeholder={trans('portal.typy-here-phone')}/>
                                        </div>
                                    </div>
                                    <div className={`row ${style['group-contact-field']}`}>
                                        <div className="col-12">
                                            <div className={`${style['form-label']}`}>{trans('message')}</div>
                                            <textarea name="content"
                                                      onChange={formik.handleChange}
                                                      onBlur={formik.handleBlur}
                                                      value={formik.values.content}
                                                      className={`form-control ${formik.errors.content ? style["form-custom-contact-invalid"] : "form-custom-contact"}`}
                                                      placeholder={trans('portal.typy-here-message')}/>
                                        </div>
                                    </div>
                                    <div className="row">
                                        <div className="col-12 text-center">
                                            {isSuccess ? (
                                                <>
                                                    <div className={`${style['success-message']}`}>
                                                        <svg className="me-1" width="21" height="16" viewBox="0 0 21 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                            <path d="M1.7688 8.77045L6.99835 14L19.2311 2" stroke="#B5B268" strokeWidth="2.61478" strokeLinecap="round" strokeLinejoin="round"/>
                                                        </svg>
                                                        {trans('title-success-contact')}
                                                    </div>
                                                    <div className={`${style['sub-success-message']}`}>{trans('portal.sub-contact-success')}</div>
                                                </>
                                            ) : (
                                                <Button variant="contained" type="submit" className={`${variables.regisButton}`}>
                                                    <div className={`${variables.regisButtonText}`}>{trans('send-message')}</div>
                                                </Button>
                                            )}

                                        </div>
                                    </div>

                                </div>
                                <div className={`${style['group-section-backgroup']}`}></div>
                            </form>
                        </div>
                        <div className={`${style['contact-img']} res-mobile mx-auto px-0`}>
                            <Image
                                src="/img/contact-img.png"
                                alt="contact-us"
                                width={100}
                                height={100}
                                priority={true}
                                sizes="100vw"
                                style={{ width: '375px', height: 'auto' }} // optional
                            />
                            <div className={`${style['section-popup']}`}>
                                <div className={`${style['popup-title']}`}>
                                    <Image
                                        alt='kokette'
                                        src="/img/icon-contact.png"
                                        width={100}
                                        height={100}
                                        priority={true}
                                        sizes="100vw"
                                        style={{ width: '25px', height: '25px' }} // optional
                                    />
                                    Het Baksalon
                                </div>
                                <div className={`${style['popup-description']}`}>
                                    Ontbijtbox met éclairs en croissaints
                                    <div className={`${style['popup-icon']}`}>
                                        <svg width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M5.81906 13.4783C6.40258 13.4783 6.87561 13.9513 6.87561 14.5348C6.87561 15.1183 6.40258 15.5914 5.81906 15.5914C5.23554 15.5914 4.76251 15.1183 4.76251 14.5348C4.76251 13.9513 5.23554 13.4783 5.81906 13.4783Z" stroke="white" strokeWidth="1.15705"/>
                                            <path d="M12.1584 13.4783C12.7419 13.4783 13.2149 13.9513 13.2149 14.5348C13.2149 15.1183 12.7419 15.5914 12.1584 15.5914C11.5749 15.5914 11.1018 15.1183 11.1018 14.5348C11.1018 13.9513 11.5749 13.4783 12.1584 13.4783Z" stroke="white" strokeWidth="1.15705"/>
                                            <path d="M9.6931 9.95639V8.54765M9.6931 8.54765V7.13892M9.6931 8.54765H11.1018M9.6931 8.54765H8.28436" stroke="white" strokeWidth="1.15705" strokeLinecap="round"/>
                                            <path d="M1.94507 2.91272L2.12906 2.97741C3.04585 3.29973 3.50426 3.4609 3.76645 3.84458C4.02864 4.22827 4.02864 4.73792 4.02864 5.75721V7.67425C4.02864 9.74621 4.07319 10.4299 4.68345 11.0737C5.29372 11.7173 6.27592 11.7173 8.24035 11.7173H8.98874M11.9755 11.7173C13.0751 11.7173 13.6248 11.7173 14.0134 11.4006C14.402 11.0839 14.513 10.5455 14.735 9.46862L15.087 7.76071C15.3315 6.53577 15.4537 5.92331 15.141 5.51682C14.8284 5.11035 13.76 5.11035 12.5731 5.11035H8.30093M4.02864 5.11035H5.46691" stroke="white" strokeWidth="1.15705" strokeLinecap="round"/>
                                        </svg>
                                    </div>

                                </div>

                            </div>
                        </div>
                    </div>
                </div>
                <FooterPortal trans={trans} lang={language} from = {null}/>
            </div>
        </>
    );
};
