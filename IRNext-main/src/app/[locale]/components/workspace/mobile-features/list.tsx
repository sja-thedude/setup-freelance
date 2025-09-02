'use client'

import { InlineShareButtons } from 'sharethis-reactjs';
import variables from '/public/assets/css/home.module.scss';
import 'public/assets/css/sharethis.scss';
import Link from "next/link";
import { useI18n } from '@/locales/client'
import Cookies from 'js-cookie';
import { useRouter } from 'next/navigation';
import $ from "jquery";
import { useEffect, useState, useRef } from 'react';

export default function MobileFeatures({ workspaceDataItem, color, data }: { workspaceDataItem: any, color: any, data: any }) {
    const trans = useI18n()
    const tokenLoggedInCookie = Cookies.get('loggedToken');
    const router = useRouter()

    const handleClick = () => {
        if (workspaceDataItem.key === 'account') {
            router.push("/profile/show");
        } else if (workspaceDataItem.key === 'recent') {
            if (tokenLoggedInCookie) {
                router.push("/function/recent");
            } else {
                router.push("/user/login?recent=true");
            }
        } else if (workspaceDataItem.key === 'menu') {
            router.push("/category/products");
        } else if (workspaceDataItem.key === 'favorites') {
            if (tokenLoggedInCookie) {
                router.push("/category/products?liked=true");
            } else {
                router.push("/user/login?favorites=true");
            }
        } else if (workspaceDataItem.key === 'jobs') {
            router.push("/jobs");
        } else if (workspaceDataItem.key === 'route') {
            let lat = data ? Number(data?.lat) : 20;
            let lng = data ? Number(data?.lng) : 100;
            window.open(`https://www.google.com/maps?q=${lat},${lng}`, '_blank');
        } else if (workspaceDataItem.key === 'loyalty') {
            router.push("/loyalties");
            if (tokenLoggedInCookie) {
                router.push("/loyalties");
            } else {
                router.push("/user/login?loyalties=true");
            }
        } else if (workspaceDataItem.key === 'share') {
            // $('[id^="sharethis-"] .st-btn:first').click();
        } else {
            const url = workspaceDataItem?.url
            window.open(`${url}`, '_blank');
        }
    }

    function translateName(type: any) {
        switch (type) {
            case 'reserve':
                return trans('reserve');
            case 'favorites':
                return trans('favorites');
            case 'share':
                return trans('share');
            case 'loyalty':
                return trans('loyalty-cart');
            case 'menu':
                return trans('menu-cart');
            case 'jobs':
                return "Jobs"
            case 'reviews':
                return "Reviews"
            case 'route':
                return "Route"
            case 'recent':
                return "Recent"
            case 'account':
                return "Account"
            default:
                // Xử lý khi `type` không khớp với bất kỳ giá trị nào
                return type; // hoặc thực hiện xử lý khác nếu cần
        }
    }

    function getIconBasedOnKey(key: any) {
        switch (key) {
            case 'loyalty':
                return (
                    <svg className="loyalty-svg" xmlns="http://www.w3.org/2000/svg" width="34" height="34" viewBox="0 0 34 34" fill="none">
                        <path d="M16.9997 21.25C22.4765 21.25 26.9163 16.8101 26.9163 11.3333C26.9163 5.85647 22.4765 1.41663 16.9997 1.41663C11.5229 1.41663 7.08301 5.85647 7.08301 11.3333C7.08301 16.8101 11.5229 21.25 16.9997 21.25Z" stroke={color} strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" />
                        <path d="M11.6312 19.6775L9.91699 32.5833L17.0003 28.3333L24.0837 32.5833L22.3695 19.6633" stroke={color} strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" />
                    </svg>
                );
            case 'reviews':
                return (
                    <svg className={`${variables.icon} reviews-svg`} xmlns="http://www.w3.org/2000/svg" width="36" height="31" viewBox="0 0 36 31" fill="none">
                        <path d="M3 3.875H12C13.5913 3.875 15.1174 4.41934 16.2426 5.38828C17.3679 6.35722 18 7.67138 18 9.04167V27.125C18 26.0973 17.5259 25.1117 16.682 24.385C15.8381 23.6583 14.6935 23.25 13.5 23.25H3V3.875Z" stroke={color} strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" />
                        <path d="M33 3.875H24C22.4087 3.875 20.8826 4.41934 19.7574 5.38828C18.6321 6.35722 18 7.67138 18 9.04167V27.125C18 26.0973 18.4741 25.1117 19.318 24.385C20.1619 23.6583 21.3065 23.25 22.5 23.25H33V3.875Z" stroke={color} strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" />
                    </svg>
                );
            case 'jobs':
                return (
                    <svg className="jobs-svg" xmlns="http://www.w3.org/2000/svg" width="42" height="40" viewBox="0 0 42 40" fill="none">
                        <path d="M35 11.6666H7C5.067 11.6666 3.5 13.159 3.5 15V31.6666C3.5 33.5076 5.067 35 7 35H35C36.933 35 38.5 33.5076 38.5 31.6666V15C38.5 13.159 36.933 11.6666 35 11.6666Z" stroke={color} strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" />
                        <path d="M28 35V8.33333C28 7.44928 27.6313 6.60143 26.9749 5.97631C26.3185 5.35119 25.4283 5 24.5 5H17.5C16.5717 5 15.6815 5.35119 15.0251 5.97631C14.3687 6.60143 14 7.44928 14 8.33333V35" stroke={color} strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" />
                    </svg>
                );
            case 'account':
                return (
                    <svg className="account-svg" xmlns="http://www.w3.org/2000/svg" width="30" height="32" viewBox="0 0 30 32" fill="none">
                        <path d="M28.3327 31V27.6667C28.3327 25.8986 27.6303 24.2029 26.3801 22.9526C25.1298 21.7024 23.4341 21 21.666 21H8.33268C6.56457 21 4.86888 21.7024 3.61864 22.9526C2.36839 24.2029 1.66602 25.8986 1.66602 27.6667V31" stroke={color} strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" />
                        <path d="M15.0007 14.3333C18.6826 14.3333 21.6673 11.3486 21.6673 7.66667C21.6673 3.98477 18.6826 1 15.0007 1C11.3188 1 8.33398 3.98477 8.33398 7.66667C8.33398 11.3486 11.3188 14.3333 15.0007 14.3333Z" stroke={color} strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" />
                    </svg>
                );
            case 'route':
                return (
                    <svg className="route-svg" xmlns="http://www.w3.org/2000/svg" width="35" height="32" viewBox="0 0 35 32" fill="none">
                        <path d="M1.45801 7.99996V29.3333L11.6663 24L23.333 29.3333L33.5413 24V2.66663L23.333 7.99996L11.6663 2.66663L1.45801 7.99996Z" stroke={color} strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" />
                        <path d="M11.667 2.66663V24" stroke={color} strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" />
                        <path d="M23.333 8V29.3333" stroke={color} strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" />
                    </svg>
                );
            case 'reserve':
                return (
                    <svg className="reserve-svg" xmlns="http://www.w3.org/2000/svg" width="32" height="35" viewBox="0 0 32 35" fill="none">
                        <path d="M5.3335 28.4375C5.3335 27.4705 5.68469 26.5432 6.30981 25.8595C6.93493 25.1757 7.78277 24.7916 8.66683 24.7916H26.6668" stroke={color} strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" />
                        <path d="M8.66683 2.91663H26.6668V32.0833H8.66683C7.78277 32.0833 6.93493 31.6992 6.30981 31.0155C5.68469 30.3317 5.3335 29.4044 5.3335 28.4375V6.56246C5.3335 5.59552 5.68469 4.66819 6.30981 3.98447C6.93493 3.30074 7.78277 2.91663 8.66683 2.91663V2.91663Z" stroke={color} strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" />
                    </svg>
                );
            case 'recent':
                return (
                    <svg className="recent-svg" xmlns="http://www.w3.org/2000/svg" width="33" height="32" viewBox="0 0 33 32" fill="none">
                        <path d="M17.2256 8.27271V17L22.9674 19.9091" stroke={color} strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" />
                        <path fillRule="evenodd" clipRule="evenodd" d="M5.95687 8.27271C8.32986 4.49859 12.4946 2 17.2257 2C24.5889 2 30.5803 8.05201 30.5803 15.5455C30.5803 23.0389 24.5889 29.0909 17.2257 29.0909C10.3455 29.0909 4.66294 23.8067 3.94713 17H1.9375C2.66153 24.8934 9.219 31.0909 17.2257 31.0909C25.7182 31.0909 32.5803 24.1185 32.5803 15.5455C32.5803 6.97243 25.7182 0 17.2257 0C11.3344 0 6.2277 3.35533 3.6521 8.27271H5.95687Z" fill={color} />
                        <path d="M4.04259 12.4666L2.48649 5.8707L9.48809 8.5239L4.04259 12.4666Z" fill={color} />
                    </svg>
                );
            case 'share':
                return (
                    <svg className="share-svg" xmlns="http://www.w3.org/2000/svg" width="35" height="32" viewBox="0 0 35 32" fill="none">
                        <path d="M5.83301 16V26.6667C5.83301 27.3739 6.1403 28.0522 6.68728 28.5523C7.23426 29.0524 7.97613 29.3333 8.74967 29.3333H26.2497C27.0232 29.3333 27.7651 29.0524 28.3121 28.5523C28.859 28.0522 29.1663 27.3739 29.1663 26.6667V16" stroke={color} strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" />
                        <path d="M23.3337 7.99996L17.5003 2.66663L11.667 7.99996" stroke={color} strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" />
                        <path d="M17.5 2.66663V20" stroke={color} strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" />
                    </svg>
                );
            case 'favorites':
                return (
                    <svg className="favorites-svg" xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 32 32" fill="none">
                        <path d="M27.7867 6.14666C27.1057 5.46533 26.2971 4.92485 25.4071 4.5561C24.5172 4.18735 23.5633 3.99756 22.6 3.99756C21.6367 3.99756 20.6828 4.18735 19.7929 4.5561C18.9029 4.92485 18.0943 5.46533 17.4133 6.14666L16 7.55999L14.5867 6.14666C13.2111 4.77107 11.3454 3.99827 9.4 3.99827C7.45462 3.99827 5.58892 4.77107 4.21333 6.14666C2.83774 7.52225 2.06494 9.38795 2.06494 11.3333C2.06494 13.2787 2.83774 15.1444 4.21333 16.52L5.62666 17.9333L16 28.3067L26.3733 17.9333L27.7867 16.52C28.468 15.839 29.0085 15.0304 29.3772 14.1405C29.746 13.2505 29.9358 12.2966 29.9358 11.3333C29.9358 10.37 29.746 9.41613 29.3772 8.52619C29.0085 7.63624 28.468 6.82767 27.7867 6.14666V6.14666Z" stroke={color} strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" />
                    </svg>
                );
            case 'menu':
                return (
                    <svg className="menu-svg" xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 32 32" fill="none">
                        <path d="M24 10.6666H25.3333C26.7478 10.6666 28.1044 11.2285 29.1046 12.2287C30.1048 13.2289 30.6667 14.5855 30.6667 16C30.6667 17.4144 30.1048 18.771 29.1046 19.7712C28.1044 20.7714 26.7478 21.3333 25.3333 21.3333H24" stroke={color} strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" />
                        <path d="M2.66699 10.6666H24.0003V22.6666C24.0003 24.0811 23.4384 25.4377 22.4382 26.4379C21.438 27.4381 20.0815 28 18.667 28H8.00033C6.58584 28 5.22928 27.4381 4.22909 26.4379C3.2289 25.4377 2.66699 24.0811 2.66699 22.6666V10.6666Z" stroke={color} strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" />
                        <path d="M8 1.33337V5.33337" stroke={color} strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" />
                        <path d="M13.333 1.33337V5.33337" stroke={color} strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" />
                        <path d="M18.667 1.33337V5.33337" stroke={color} strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" />
                    </svg>
                );
            default:
                return null;
        }
    }

    const colorMin = color.replace('#', '');
    let svgShare = `<svg width="35" height="32" viewBox="0 0 35 32" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M5.83301 16V26.6667C5.83301 27.3739 6.1403 28.0522 6.68728 28.5523C7.23426 29.0524 7.97613 29.3333 8.74967 29.3333H26.2497C27.0232 29.3333 27.7651 29.0524 28.3121 28.5523C28.859 28.0522 29.1663 27.3739 29.1663 26.6667V16" stroke="%23${colorMin}" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><path d="M23.3337 7.99996L17.5003 2.66663L11.667 7.99996"  stroke="%23${colorMin}" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><path d="M17.5 2.66663V20" stroke="%23${colorMin}" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>`;

    useEffect(() => {
        setTimeout(() => {
            $('.st-inline-share-buttons .st-btn.st-first.st-last.st-remove-label').css({
                'background-image': `url('data:image/svg+xml,${svgShare}')`
            });
        }, 500);
    }, [svgShare]);

    const [maxWidth, setMaxWidth] = useState(100);

    const titleRef: any = useRef(null);

    useEffect(() => {
        if (titleRef?.current) {
            const width = titleRef.current.offsetWidth;
            setMaxWidth(width + 10);
        }
    }, [workspaceDataItem]);

    const handleShareButtonClick = () => {
        const shareButton = document.querySelector('.st-inline-share-buttons .st-btn');
        if (shareButton instanceof HTMLElement) {
            shareButton.click();
        }
    };

    const handleInlineShareButtonClick = (event: any) => {
        event.stopPropagation();
    };

    return (

        <>
            <div className={`${variables.option} work-break-keep-all mb-3 me-3 border-0`} onClick={handleClick} style={{ textAlign: "start" }}>
                {workspaceDataItem.key === 'share'
                    ? (
                        <div className={variables.shareing} onClick={handleShareButtonClick}>
                            <div>
                                <div className={`${variables.title} text-uppercase`}>{workspaceDataItem && workspaceDataItem?.name ? translateName(workspaceDataItem.key) : workspaceDataItem?.title}</div>
                            </div>
                            <div className={variables.descriptionHeight}>
                                <p className={`${variables.description}`} style={{ wordWrap: 'break-word' }}>
                                    {workspaceDataItem?.description && workspaceDataItem?.description.length > 100 ? (
                                        <>{workspaceDataItem?.description.slice(0, 100)}...</>
                                    ) : (
                                        <>{workspaceDataItem?.description}</>
                                    )}
                                </p>
                            </div>
                            <div className={variables.shareButton}>
                                <div onClick={handleInlineShareButtonClick}>
                                    <InlineShareButtons
                                        config={{

                                            alignment: 'center',  // alignment of buttons (left, center, right)
                                            color: 'white',      // set the color of buttons (social, white)
                                            enabled: true,        // show/hide buttons (true, false)
                                            font_size: 16,        // font size for the buttons
                                            labels: null,        // button labels (cta, counts, null)
                                            language: 'en',       // which language to use (see LANGUAGES)

                                            networks: [           // which networks to include (see SHARING NETWORKS)
                                                'sharethis',
                                                // 'whatsapp',
                                                // 'linkedin',
                                                // 'messenger',
                                                // 'facebook',
                                                // 'twitter'
                                            ],
                                            padding: 10,          // padding within buttons (INTEGER)
                                            radius: 4,            // the corner radius on each button (INTEGER)
                                            show_total: false,
                                            size: 30,             // the size of each button (INTEGER)
                                            // OPTIONAL PARAMETERS
                                            url: workspaceDataItem.url ?? null, // (defaults to current url)
                                            // image: 'https://bit.ly/2CMhCMC',  // (defaults to og:image or twitter:image)
                                            // description: 'custom text',       // (defaults to og:description or twitter:description)
                                            // title: 'custom title',            // (defaults to og:title or twitter:title)
                                            // message: 'custom email text',     // (only for email sharing)
                                            // subject: 'custom email subject',  // (only for email sharing)
                                            // username: 'custom twitter handle' // (only for twitter sharing)
                                        }}
                                    />
                                </div>
                            </div>
                        </div>
                    ) : (
                        <div ref={titleRef}>
                            {workspaceDataItem.key === 'jobs' ? (
                                <div className={`${variables.title} text-uppercase`}>
                                    <div className={variables.jobTitle} onClick={() => window.location.href = '/jobs'} style={{ 'textDecoration': 'none' }}>{workspaceDataItem && workspaceDataItem?.name ? workspaceDataItem?.name : workspaceDataItem?.title}</div>
                                </div>
                            ) : (
                                <div className={`${variables.title} text-uppercase`}>{workspaceDataItem && workspaceDataItem?.name ? translateName(workspaceDataItem.key) : workspaceDataItem?.title}</div>
                            )}
                            <div className={variables.titleChild} >
                                <p className={`${variables.description}`} style={{ wordWrap: 'break-word' }}>
                                    {maxWidth <= 100 && workspaceDataItem?.description && workspaceDataItem?.description.length > 85 ? (
                                        <>{workspaceDataItem?.description.slice(0, 85)}...</>
                                    ) : (
                                        <>{workspaceDataItem?.description}</>
                                    )}
                                </p>
                            </div>
                            <div className={`${variables.icon} home-feature-icon`}>
                                {getIconBasedOnKey(workspaceDataItem?.key)}
                            </div>
                        </div>
                    )
                }
            </div>
        </>
    );
};
