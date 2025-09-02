'use client'

import { api } from "@/utils/axios";
import Cookies from "js-cookie";
import React, { useState, useEffect } from 'react';
import useMediaQuery from '@mui/material/useMediaQuery'
import { useAppSelector, useAppDispatch } from '@/redux/hooks'
import { addToFavorites } from '@/redux/slices/productFavSlice'
import style from 'public/assets/css/product.module.scss';

export default function FavoriteCard({ index, item, color, width, height, type }: { index: number, item: any, color: string, width: number, height: number, type: string }) {
    const [product, setProduct] = useState(item);
    const tokenLoggedInCookie = Cookies.get('loggedToken');
    const productFav = useAppSelector<any>((state: any) => state.productFav.data);
    const [isLiked, setIsLiked] = useState(null);
    const isMobile = useMediaQuery('(max-width: 1279px)');
    const dispatch = useAppDispatch()
    const language = Cookies.get('Next-Locale') ?? 'nl';

    useEffect(() => {
        const fetchData = async () => {
            try {
                if (type === 'detailProduct' && item?.liked === undefined) {
                    const res = await api.get(`products/${item.id}`, {
                        headers: {
                            'Authorization': `Bearer ${Cookies.get('loggedToken')}`,
                            'Content-Language': language
                        }
                    });
                    setProduct(res?.data?.data);
                }
            } catch (error) {
                // Xử lý lỗi ở đây nếu cần thiết
                console.error('Error fetching product data:', error);
            }
        };
    
        fetchData();
        // eslint-disable-next-line react-hooks/exhaustive-deps
    }, []);
    

    const favorites = async (id: number) => {
        try {
            const res = await api.get(`products/${id}/toggle_like`, {
                headers: {
                    'Authorization': `Bearer ${Cookies.get('loggedToken')}`
                }
            });
            setProduct(res?.data?.data);
            const collection = document.getElementsByClassName('like-' + item.id);
            dispatch(addToFavorites(res?.data?.data));
            if (item.liked) {
                for (let i = 0; i < collection.length; i++) {
                    collection[i].setAttribute("fill", "#FFF");
                    if (!isMobile) {
                        collection[i].setAttribute("stroke", "#404040");
                    }
                }
            } else {
                for (let i = 0; i < collection.length; i++) {
                    if (isMobile) {
                        collection[i].setAttribute("fill", color);
                    } else {
                        collection[i].setAttribute("fill", '#E45A5A');
                    }
                }
            }
        } catch (error) {
            // Xử lý lỗi ở đây nếu cần thiết
            console.error('Error toggling like:', error);
        }
    };

    useEffect(() => {
        if (productFav) {
            setIsLiked(productFav.find((productFavItem: any) => productFavItem.id === product?.id)?.liked)
        }
    }, [productFav])

    return (
        <>
            {
                isMobile && tokenLoggedInCookie ? (
                    (product.liked) ? (
                        <svg xmlns="http://www.w3.org/2000/svg" width={width} height={height} viewBox="0 0 17 15"
                            fill="none" className={`ms-1 ${product.liked}`} onClick={() => favorites(item.id)}>
                            <path
                                d="M14.8434 2.15664C14.4768 1.78995 14.0417 1.49907 13.5627 1.30061C13.0837 1.10215 12.5704 1 12.0519 1C11.5335 1 11.0201 1.10215 10.5411 1.30061C10.0621 1.49907 9.62698 1.78995 9.26046 2.15664L8.49981 2.91729L7.73916 2.15664C6.99882 1.4163 5.9947 1.00038 4.94771 1.00038C3.90071 1.00038 2.89659 1.4163 2.15626 2.15664C1.41592 2.89698 1 3.90109 1 4.94809C1 5.99509 1.41592 6.9992 2.15626 7.73954L2.91691 8.50019L8.49981 14.0831L14.0827 8.50019L14.8434 7.73954C15.21 7.37302 15.5009 6.93785 15.6994 6.45889C15.8979 5.97992 16 5.46654 16 4.94809C16 4.42964 15.8979 3.91626 15.6994 3.43729C15.5009 2.95833 15.21 2.52316 14.8434 2.15664V2.15664Z"
                                fill={color} className={`like-${item.id}`}
                                stroke={color} strokeWidth="2" strokeLinecap="round"
                                strokeLinejoin="round" />
                        </svg>
                    ) : (
                        <svg xmlns="http://www.w3.org/2000/svg" width={width} height={height} viewBox="0 0 17 15"
                            fill="none" className={`ms-1 ${product.liked}`} onClick={() => favorites(item.id)} >
                            <path
                                d="M14.8434 2.15664C14.4768 1.78995 14.0417 1.49907 13.5627 1.30061C13.0837 1.10215 12.5704 1 12.0519 1C11.5335 1 11.0201 1.10215 10.5411 1.30061C10.0621 1.49907 9.62698 1.78995 9.26046 2.15664L8.49981 2.91729L7.73916 2.15664C6.99882 1.4163 5.9947 1.00038 4.94771 1.00038C3.90071 1.00038 2.89659 1.4163 2.15626 2.15664C1.41592 2.89698 1 3.90109 1 4.94809C1 5.99509 1.41592 6.9992 2.15626 7.73954L2.91691 8.50019L8.49981 14.0831L14.0827 8.50019L14.8434 7.73954C15.21 7.37302 15.5009 6.93785 15.6994 6.45889C15.8979 5.97992 16 5.46654 16 4.94809C16 4.42964 15.8979 3.91626 15.6994 3.43729C15.5009 2.95833 15.21 2.52316 14.8434 2.15664V2.15664Z"
                                stroke={color} strokeWidth="2" strokeLinecap="round"
                                strokeLinejoin="round" className={`like-${item.id}`} />
                        </svg>

                    )
                ) : (<></>)
            }
            {
                !isMobile && tokenLoggedInCookie && product ? (
                    (product.liked) ? (
                        isLiked === true ? (
                            <svg xmlns="http://www.w3.org/2000/svg" width="31" height="31" viewBox="0 0 31 31" fill="none" className={`ms-1 ${product.liked} ${style.liking}`} onClick={(e: any) => { e.stopPropagation(); favorites(item.id) }}>
                                <circle cx="15.5" cy="15.5" r="15.5" fill="white" />
                                <path d="M22.6891 9.41035C22.2738 8.96323 21.7806 8.60854 21.2377 8.36655C20.6949 8.12455 20.1131 8 19.5255 8C18.9379 8 18.3561 8.12455 17.8133 8.36655C17.2704 8.60854 16.7772 8.96323 16.3619 9.41035L15.4998 10.3378L14.6377 9.41035C13.7987 8.50762 12.6607 8.00047 11.4741 8.00047C10.2875 8.00047 9.14947 8.50762 8.31042 9.41035C7.47137 10.3131 7 11.5374 7 12.8141C7 14.0908 7.47137 15.3151 8.31042 16.2178L9.1725 17.1453L15.4998 23.9528L21.8271 17.1453L22.6891 16.2178C23.1047 15.7709 23.4344 15.2403 23.6593 14.6563C23.8842 14.0723 24 13.4463 24 12.8141C24 12.1819 23.8842 11.5559 23.6593 10.9719C23.4344 10.3879 23.1047 9.85726 22.6891 9.41035Z"
                                    fill={'#E45A5A'} className={`like-${item.id}`}
                                    stroke={'#E45A5A'}
                                    strokeWidth="2"
                                    strokeLinecap="round"
                                    strokeLinejoin="round"
                                />
                            </svg>
                        ) : isLiked === false ? (
                            <svg xmlns="http://www.w3.org/2000/svg" width="31" height="31" viewBox="0 0 31 31" fill="none" className={`ms-1 ${product.liked} ${style.liking}`} onClick={(e: any) => { e.stopPropagation(); favorites(item.id) }}>
                            <circle cx="15.5" cy="15.5" r="15.5" fill="white" />
                            <path d="M22.6891 9.41035C22.2738 8.96323 21.7806 8.60854 21.2377 8.36655C20.6949 8.12455 20.1131 8 19.5255 8C18.9379 8 18.3561 8.12455 17.8133 8.36655C17.2704 8.60854 16.7772 8.96323 16.3619 9.41035L15.4998 10.3378L14.6377 9.41035C13.7987 8.50762 12.6607 8.00047 11.4741 8.00047C10.2875 8.00047 9.14947 8.50762 8.31042 9.41035C7.47137 10.3131 7 11.5374 7 12.8141C7 14.0908 7.47137 15.3151 8.31042 16.2178L9.1725 17.1453L15.4998 23.9528L21.8271 17.1453L22.6891 16.2178C23.1047 15.7709 23.4344 15.2403 23.6593 14.6563C23.8842 14.0723 24 13.4463 24 12.8141C24 12.1819 23.8842 11.5559 23.6593 10.9719C23.4344 10.3879 23.1047 9.85726 22.6891 9.41035Z"
                                className={`like-${item.id}`}
                                stroke={'#404040'}
                                strokeWidth="2"
                                strokeLinecap="round"
                                strokeLinejoin="round"
                            />
                        </svg>
                        ) : (
                            <svg xmlns="http://www.w3.org/2000/svg" width="31" height="31" viewBox="0 0 31 31" fill="none" className={`ms-1 ${product.liked} ${style.liking}`} onClick={(e: any) => { e.stopPropagation(); favorites(item.id) }}>
                            <circle cx="15.5" cy="15.5" r="15.5" fill="white" />
                            <path d="M22.6891 9.41035C22.2738 8.96323 21.7806 8.60854 21.2377 8.36655C20.6949 8.12455 20.1131 8 19.5255 8C18.9379 8 18.3561 8.12455 17.8133 8.36655C17.2704 8.60854 16.7772 8.96323 16.3619 9.41035L15.4998 10.3378L14.6377 9.41035C13.7987 8.50762 12.6607 8.00047 11.4741 8.00047C10.2875 8.00047 9.14947 8.50762 8.31042 9.41035C7.47137 10.3131 7 11.5374 7 12.8141C7 14.0908 7.47137 15.3151 8.31042 16.2178L9.1725 17.1453L15.4998 23.9528L21.8271 17.1453L22.6891 16.2178C23.1047 15.7709 23.4344 15.2403 23.6593 14.6563C23.8842 14.0723 24 13.4463 24 12.8141C24 12.1819 23.8842 11.5559 23.6593 10.9719C23.4344 10.3879 23.1047 9.85726 22.6891 9.41035Z"
                                fill={'#E45A5A'} className={`like-${item.id}`}
                                stroke={'#E45A5A'}
                                strokeWidth="2"
                                strokeLinecap="round"
                                strokeLinejoin="round"
                            />
                        </svg>
                        )
                    ) : (
                        (isLiked) ? (
                            <svg xmlns="http://www.w3.org/2000/svg" width="31" height="31" viewBox="0 0 31 31" fill="none" className={`ms-1 ${product.liked} ${style.liking}`} onClick={(e: any) => { e.stopPropagation(); favorites(item.id) }}>
                                <circle cx="15.5" cy="15.5" r="15.5" fill="white" />
                                <path d="M22.6891 9.41035C22.2738 8.96323 21.7806 8.60854 21.2377 8.36655C20.6949 8.12455 20.1131 8 19.5255 8C18.9379 8 18.3561 8.12455 17.8133 8.36655C17.2704 8.60854 16.7772 8.96323 16.3619 9.41035L15.4998 10.3378L14.6377 9.41035C13.7987 8.50762 12.6607 8.00047 11.4741 8.00047C10.2875 8.00047 9.14947 8.50762 8.31042 9.41035C7.47137 10.3131 7 11.5374 7 12.8141C7 14.0908 7.47137 15.3151 8.31042 16.2178L9.1725 17.1453L15.4998 23.9528L21.8271 17.1453L22.6891 16.2178C23.1047 15.7709 23.4344 15.2403 23.6593 14.6563C23.8842 14.0723 24 13.4463 24 12.8141C24 12.1819 23.8842 11.5559 23.6593 10.9719C23.4344 10.3879 23.1047 9.85726 22.6891 9.41035Z"
                                    fill={'#E45A5A'} className={`like-${item.id}`}
                                    stroke={'#E45A5A'}
                                    strokeWidth="2"
                                    strokeLinecap="round"
                                    strokeLinejoin="round"
                                />
                            </svg>
                        ) : (
                            <svg xmlns="http://www.w3.org/2000/svg" width="31" height="31" viewBox="0 0 31 31" fill="none" className={`ms-1 ${product.liked} ${style.liking}`} onClick={(e: any) => { e.stopPropagation(); favorites(item.id) }}>
                                <circle cx="15.5" cy="15.5" r="15.5" fill="white" />
                                <path d="M22.6891 9.41035C22.2738 8.96323 21.7806 8.60854 21.2377 8.36655C20.6949 8.12455 20.1131 8 19.5255 8C18.9379 8 18.3561 8.12455 17.8133 8.36655C17.2704 8.60854 16.7772 8.96323 16.3619 9.41035L15.4998 10.3378L14.6377 9.41035C13.7987 8.50762 12.6607 8.00047 11.4741 8.00047C10.2875 8.00047 9.14947 8.50762 8.31042 9.41035C7.47137 10.3131 7 11.5374 7 12.8141C7 14.0908 7.47137 15.3151 8.31042 16.2178L9.1725 17.1453L15.4998 23.9528L21.8271 17.1453L22.6891 16.2178C23.1047 15.7709 23.4344 15.2403 23.6593 14.6563C23.8842 14.0723 24 13.4463 24 12.8141C24 12.1819 23.8842 11.5559 23.6593 10.9719C23.4344 10.3879 23.1047 9.85726 22.6891 9.41035Z"
                                    className={`like-${item.id}`}
                                    stroke={'#404040'}
                                    strokeWidth="2"
                                    strokeLinecap="round"
                                    strokeLinejoin="round"
                                />
                            </svg>
                        )
                    )
                ) : (<></>)
            }

        </>
    );
};
