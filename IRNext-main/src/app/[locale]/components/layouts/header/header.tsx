'use client'
import React, { useState, useEffect } from 'react';
import variables from '/public/assets/css/food.module.scss'
import Maping from '@/app/[locale]/components/layouts/popup/map';
import { useGetWorkspaceDataByIdQuery } from '@/redux/services/workspace/workspaceDataApi'
import {usePathname} from "next/navigation";
import useScrollPosition from '@/hooks/useScrollPosition';

const head = variables['head'];
const backImage = variables['back-image'];
const title = variables['titling'];
const centeredText = variables['centered-text'];
const moreInfo = variables['moreInfo']

export default function Header({ workspaceId, coupons }: { workspaceId: any, coupons: any }) {
    // Set color for  background  if scroll
    const scrolledY = useScrollPosition()
    // Get workspace info
    const { data: apiDataToken } = useGetWorkspaceDataByIdQuery({ id: workspaceId })
    const apiData = apiDataToken?.data?.setting_generals;
    const [isPopupOpen, setIsPopupOpen] = useState(false);
    var photo = apiDataToken?.data?.gallery ? apiDataToken?.data?.gallery[0].full_path : null;
    // Hàm xử lý sự kiện click để mở hoặc đóng popup
    const togglePopup = () => {
        setIsPopupOpen(!isPopupOpen);
    };
    useEffect(() => {
        window.scrollTo(0, 0)
    }, [isPopupOpen]);
    const pathName = usePathname();
    
    return (
        <>
            <div id="header" 
                className={`${head}`}  
                style={{ 
                    boxShadow: coupons > 0 ? `${apiData ? apiData.primary_color : 'white'} 0px 0px 20px 35px` : 'none',
                    background: `linear-gradient(180deg, rgba(0, 0, 0, 0.00) 0%, rgba(0, 0, 0, 0.66) 100%), url('${photo}') lightgray 50% / cover no-repeat`,
                    position: scrolledY > 76 ? 'fixed' : 'sticky'
                }
            }>
                <div className={`${backImage} row`}>
                    <div className="d-flex justify-content-between">
                        <div className={`${title}`}>
                            <h1 className={`${centeredText} ms-2`} 
                                style={{
                                    fontSize: pathName.includes('products') ? '30px' : '18px'
                                }}>
                                {apiDataToken ? apiDataToken?.data?.setting_generals?.title : ''}
                            </h1>
                        </div>
                        <div className={`${moreInfo} col-sm-2 col-xs-2`}>
                            <span className={variables.iconStyle}>
                                <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg" onClick={togglePopup} className={`ms-3 mt-1`}>
                                    <circle cx="10" cy="10" r="9.5" stroke="white" />
                                    <path d="M10.8751 7.244C10.5671 7.244 10.3058 7.13667 10.0911 6.922C9.87644 6.70733 9.76911 6.446 9.76911 6.138C9.76911 5.83 9.87644 5.56867 10.0911 5.354C10.3058 5.13 10.5671 5.018 10.8751 5.018C11.1831 5.018 11.4444 5.13 11.6591 5.354C11.8831 5.56867 11.9951 5.83 11.9951 6.138C11.9951 6.446 11.8831 6.70733 11.6591 6.922C11.4444 7.13667 11.1831 7.244 10.8751 7.244ZM9.92311 15.084C9.47511 15.084 9.11111 14.944 8.83111 14.664C8.56044 14.384 8.42511 13.964 8.42511 13.404C8.42511 13.1707 8.46244 12.8673 8.53711 12.494L9.48911 8H11.5051L10.4971 12.76C10.4598 12.9 10.4411 13.0493 10.4411 13.208C10.4411 13.3947 10.4831 13.53 10.5671 13.614C10.6604 13.6887 10.8098 13.726 11.0151 13.726C11.1831 13.726 11.3324 13.698 11.4631 13.642C11.4258 14.1087 11.2578 14.468 10.9591 14.72C10.6698 14.9627 10.3244 15.084 9.92311 15.084Z" fill="white" />
                                </svg>
                            </span>
                        </div>
                    </div>
                </div>
                <div className={`${backImage} row back-image-shadow`} style={{ 
                    backgroundColor: apiData ? apiData.primary_color : '#FFFFFF',
                    opacity: scrolledY <  76 ? (scrolledY / 76) : '1'
                }} onClick={() => {isPopupOpen ?? setIsPopupOpen(false)}}>
                    <div className="d-flex justify-content-between">
                        <div className={`${title}`}>
                            <h1 className={`${centeredText} ms-2`} 
                                style={{ fontSize: '18px', bottom: '15px' }}>
                                {apiDataToken ? apiDataToken?.data?.setting_generals?.title : ''}
                            </h1>
                        </div>
                        <div className={`${moreInfo} col-sm-2 col-xs-2`}
                            style={{ display: scrolledY < 76 ? 'block' : 'none'}}>
                            <span className={variables.iconStyle}>
                                <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg" onClick={togglePopup} className={`ms-3 mt-1`}>
                                    <circle cx="10" cy="10" r="9.5" stroke="white" />
                                    <path d="M10.8751 7.244C10.5671 7.244 10.3058 7.13667 10.0911 6.922C9.87644 6.70733 9.76911 6.446 9.76911 6.138C9.76911 5.83 9.87644 5.56867 10.0911 5.354C10.3058 5.13 10.5671 5.018 10.8751 5.018C11.1831 5.018 11.4444 5.13 11.6591 5.354C11.8831 5.56867 11.9951 5.83 11.9951 6.138C11.9951 6.446 11.8831 6.70733 11.6591 6.922C11.4444 7.13667 11.1831 7.244 10.8751 7.244ZM9.92311 15.084C9.47511 15.084 9.11111 14.944 8.83111 14.664C8.56044 14.384 8.42511 13.964 8.42511 13.404C8.42511 13.1707 8.46244 12.8673 8.53711 12.494L9.48911 8H11.5051L10.4971 12.76C10.4598 12.9 10.4411 13.0493 10.4411 13.208C10.4411 13.3947 10.4831 13.53 10.5671 13.614C10.6604 13.6887 10.8098 13.726 11.0151 13.726C11.1831 13.726 11.3324 13.698 11.4631 13.642C11.4258 14.1087 11.2578 14.468 10.9591 14.72C10.6698 14.9627 10.3244 15.084 9.92311 15.084Z" fill="white" />
                                </svg>
                            </span>
                        </div>
                    </div>
                    {isPopupOpen && (
                        <Maping data={apiDataToken ? apiDataToken.data : null} 
                            workspaceId={workspaceId ? workspaceId : ''} 
                            color={apiData ? apiData?.primary_color : 'black'} 
                            togglePopup={togglePopup} 
                            origin="home"
                        />
                    )}
                </div>
            </div>
        </>
    )
}