import React, { useEffect, useState } from 'react';
import { Button, Modal } from 'react-bootstrap';
import 'public/assets/css/popup.scss';
import * as config from "@/config/constants";
import Cookies from "js-cookie";
import { api } from "@/utils/axios";
import { useGetWorkspaceDataByIdQuery } from "@/redux/services/workspace/workspaceDataApi";
import { useI18n } from '@/locales/client'

const IntroducePopup = ({ baseLink, workspaceInfo, getHolidayStatus, setHolidayStatus }: { baseLink?: any, workspaceInfo?: any, getHolidayStatus?: any, setHolidayStatus?: any }) => {
    const [show, setShow] = useState(false);
    const [isLargeScreen, setIsLargeScreen] = useState(window.innerWidth >= 1280);
    const [getHoliday, setHoliday] = useState<any | null>(null);
    const [getSettingData, setSettingData] = useState<any | null>(null);
    const { data: apiDataToken } = useGetWorkspaceDataByIdQuery({ id: workspaceInfo?.id })
    const apiData = apiDataToken?.data?.setting_generals;
    const color = apiData?.primary_color;
    const trans = useI18n();

    const handleClose = () => {
        setShow(false);
        setHolidayStatus(false);
        const prevItemId = localStorage.getItem('prevItem');
        if (prevItemId) {
            const element = document.getElementById(`product-${prevItemId.toString()}`);
            if (element) {
                element.scrollIntoView({ behavior: "smooth" });
                const currentScrollY = window.scrollY;
                let topPosition;
                topPosition = element.getBoundingClientRect().top + currentScrollY - 170;
                // Sử dụng topPosition để cuộn
                window.scrollTo({ top: topPosition, behavior: "smooth" });
                localStorage.removeItem('prevItem');
            }
        }
    };

    const language = Cookies.get('Next-Locale') ?? 'nl';
    const handleShow = () => setShow(true);

    const fetchHolidayData = () => {
        const tokenLoggedInCookie = Cookies.get('loggedToken');
        workspaceInfo?.id && api.get(`workspaces/${workspaceInfo.id}/settings/holiday_exceptions`, {
            headers: {
                'Authorization': `Bearer ${tokenLoggedInCookie}`,
                'Content-Language': language
            }
        }).then(res => {
            const json = res.data;
            const currentTime = new Date();
            const isInTimeRange = json.data.some((item: any) => {
                const startTime = new Date(item.start_time);
                const endTime = new Date(item.end_time);
                return currentTime >= startTime && currentTime <= endTime;
            });
            if (isInTimeRange) {
                setHoliday({
                    status: true,
                    data: json.data,
                });
            }
        }).catch(error => {
            console.error("Error fetching holiday data", error);
        });
    };

    const fetchDataSettings = () => {
        const tokenLoggedInCookie = Cookies.get('loggedToken');
        workspaceInfo?.id && api.get(`workspaces/${workspaceInfo.id}/settings/preferences`, {
            headers: {
                'Authorization': `Bearer ${tokenLoggedInCookie}`,
                'Content-Language': language
            }
        }).then(res => {
            const json = res.data;
            if (json.data) {
                setSettingData({
                    status: true,
                    data: json.data,
                });
            }
        }).catch(error => {
            console.error("Error fetching data", error);
        });
    };

    workspaceInfo = workspaceInfo ?? {};
    const wpPreferences = workspaceInfo?.setting_preference ?? {};
    const multilineText = wpPreferences.holiday_text ?? '';
    const tableOrderingText = wpPreferences.table_ordering_pop_up_text ?? '';
    const selfOrderingText = wpPreferences.self_ordering_pop_up_text ?? '';
    const lines = multilineText;
    const linesTableOrdering = tableOrderingText;
    const linesSelfOrdering = selfOrderingText;
    // Display default image when has error
    const placeholderImage = config.BASE_URL + 'images/default-user.png';

    const onImageError = (e: React.SyntheticEvent<HTMLImageElement, Event>) => {
        e.currentTarget.src = placeholderImage
    };

    useEffect(() => {
        const hasShownPopup = sessionStorage.getItem('hasShownPopup');
        fetchHolidayData();

        // Add this condition to fetch settings data for specific baseLinks
        if (baseLink == '/table-ordering/products' || baseLink == '/self-ordering/products') {
            fetchDataSettings();
        }

        const showPopup = baseLink == '/table-ordering/products' ? wpPreferences.table_ordering_pop_up_text
            : baseLink == '/self-ordering/products' ? wpPreferences.self_ordering_pop_up_text
                : wpPreferences.holiday_text || getHolidayStatus;

        if (!hasShownPopup && showPopup) {
            setShow(true);
            sessionStorage.setItem('hasShownPopup', 'true');
        }

        const handleResize = () => {
            setIsLargeScreen(window.innerWidth >= 1280);
        };

        window.addEventListener('resize', handleResize);
        return () => {
            window.removeEventListener('resize', handleResize);
        };
    }, [workspaceInfo]);

    return (
        <>
            <Button variant="primary" onClick={handleShow} style={{display: 'none'}}></Button>

            {
                isLargeScreen ? (
                    <Modal show={getHolidayStatus && getHoliday?.status} onHide={handleClose}
                        aria-labelledby="contained-modal-title-vcenter"
                        centered id="introduce-popup" className="custom-introduce-modal">
                        <Modal.Footer>
                            <div onClick={handleClose} className={'mx-auto'}>
                                <svg width="16" height="17" viewBox="0 0 16 17" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <g id="x">
                                        <path id="Vector" d="M12 4.5L4 12.5" stroke="#888888" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" />
                                        <path id="Vector_2" d="M4 4.5L12 12.5" stroke="#888888" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" />
                                    </g>
                                </svg>
                                <span className={'introduce-close'}> {trans('lang_sluiten')} </span>
                            </div>
                        </Modal.Footer>
                        <Modal.Header>
                            <div className={'photo-container'}>
                                <img src={workspaceInfo.photo} alt="" className={'logo-photo'} onError={onImageError} />
                            </div>
                        </Modal.Header>
                        <div className="modal-content-wrapper">
                            <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <circle cx="10" cy="10" r="9.5" stroke={color ?? '#D87833'} />
                                <path d="M10.8751 7.244C10.5671 7.244 10.3058 7.13667 10.0911 6.922C9.87644 6.70733 9.76911 6.446 9.76911 6.138C9.76911 5.83 9.87644 5.56867 10.0911 5.354C10.3058 5.13 10.5671 5.018 10.8751 5.018C11.1831 5.018 11.4444 5.13 11.6591 5.354C11.8831 5.56867 11.9951 5.83 11.9951 6.138C11.9951 6.446 11.8831 6.70733 11.6591 6.922C11.4444 7.13667 11.1831 7.244 10.8751 7.244ZM9.92311 15.084C9.47511 15.084 9.11111 14.944 8.83111 14.664C8.56044 14.384 8.42511 13.964 8.42511 13.404C8.42511 13.1707 8.46244 12.8673 8.53711 12.494L9.48911 8H11.5051L10.4971 12.76C10.4598 12.9 10.4411 13.0493 10.4411 13.208C10.4411 13.3947 10.4831 13.53 10.5671 13.614C10.6604 13.6887 10.8098 13.726 11.0151 13.726C11.1831 13.726 11.3324 13.698 11.4631 13.642C11.4258 14.1087 11.2578 14.468 10.9591 14.72C10.6698 14.9627 10.3244 15.084 9.92311 15.084Z" fill={color ?? '#D87833'} />
                            </svg>
                            <Modal.Body>
                                <div className={'workspace-content'}>
                                    {!getHoliday || !getHoliday.status ?
                                        (<div style={{whiteSpace: "pre-line"}}>
                                            {lines}
                                        </div>)
                                        :
                                        <p>{getHoliday.data[0].description}</p>
                                    }
                                </div>
                            </Modal.Body>
                        </div>
                    </Modal>
                ) : (
                    <Modal show={show}
                        aria-labelledby="contained-modal-title-vcenter"
                        centered id="introduce-popup">
                        <Modal.Header>
                            <div className={'photo-container'}>
                                <img src={workspaceInfo.photo} alt="" className={'logo-photo'} onError={onImageError} />
                            </div>
                        </Modal.Header>
                        <Modal.Body className={'pb-0'}>
                            <div className={'workspace-content no-spacing'}>
                                {
                                    baseLink == '/table-ordering/products' ?
                                        (getSettingData && getSettingData.data.table_ordering_pop_up_text) ?
                                            ((<div style={{whiteSpace: "pre-line"}}>
                                                {linesTableOrdering}
                                            </div>))
                                            : <p>{getSettingData?.data?.table_ordering_pop_up_text}</p>
                                        : baseLink == '/self-ordering/products' ?
                                            (getSettingData && getSettingData.data.self_ordering_pop_up_text) ?
                                                ((<div style={{whiteSpace: "pre-line"}}>
                                                    {linesSelfOrdering}
                                                </div>))
                                                : <p>{getSettingData?.data?.self_ordering_pop_up_text}</p>
                                            : baseLink == '/category/products' ?
                                                ((<div style={{whiteSpace: "pre-line"}}>
                                                    {lines}
                                                </div>))
                                                : <p>{trans('lang_no_data')}</p>
                                }
                            </div>
                        </Modal.Body>
                        <Modal.Footer style={{paddingBottom: '0px'}}>
                            <div onClick={handleClose} className={'mx-auto my-0'}>
                                <svg width="57" height="56" viewBox="0 0 57 56" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <g filter="url(#filter0_d_297_3117)">
                                        <path d="M45.7598 22.5168C45.7598 31.601 38.0797 39.0335 28.5198 39.0335C18.9599 39.0335 11.2798 31.601 11.2798 22.5168C11.2798 13.4325 18.9599 6 28.5198 6C38.0797 6 45.7598 13.4325 45.7598 22.5168Z" stroke="#413E38" strokeWidth="2" />
                                    </g>
                                    <path d="M34.5995 16.6777L22.4395 28.3555" stroke="#413E38" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" />
                                    <path d="M22.4395 16.6777L34.5995 28.3555" stroke="#413E38" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" />
                                    <defs>
                                        <filter id="filter0_d_297_3117" x="0.279785" y="0" width="56.48" height="55.0334" filterUnits="userSpaceOnUse" colorInterpolationFilters="sRGB">
                                            <feFlood floodOpacity="0" result="BackgroundImageFix" />
                                            <feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0" result="hardAlpha" />
                                            <feOffset dy="5" />
                                            <feGaussianBlur stdDeviation="5" />
                                            <feColorMatrix type="matrix" values="0 0 0 0 1 0 0 0 0 1 0 0 0 0 1 0 0 0 0.15 0" />
                                            <feBlend mode="normal" in2="BackgroundImageFix" result="effect1_dropShadow_297_3117" />
                                            <feBlend mode="normal" in="SourceGraphic" in2="effect1_dropShadow_297_3117" result="shape" />
                                        </filter>
                                    </defs>
                                </svg>
                            </div>
                        </Modal.Footer>
                    </Modal>
                )
            }
            <style>{`
                .modal-backdrop.show {
                    opacity: 0.55!important;
                }`}
            </style>
        </>
    );
};

export default IntroducePopup;
