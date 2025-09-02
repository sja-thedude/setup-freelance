'use client'

import React, { useState, useEffect } from "react";
import {
    GoogleMap,
    useLoadScript,
    Marker,
    OverlayView,
} from "@react-google-maps/api";
import { useI18n } from '@/locales/client';
import style from "public/assets/css/portal.module.scss";
import useMediaQuery from '@mui/material/useMediaQuery';
import * as config from "@/config/constants";
import Cookies from "js-cookie";

export default function RestaurantMap({ location, positions, dataPositions, setIsShowMap }: any) {
    const trans = useI18n();
    const language = Cookies.get('Next-Locale') ?? 'nl';
    const [currentLocation, setCurrentLocation] = useState<any>(null);
    const [currentData, setCurrentData] = useState<any>(null);
    const isMobile = useMediaQuery('(max-width: 1279px)');
    const { isLoaded , loadError } = useLoadScript({
        googleMapsApiKey: `${config.PUBLIC_GOOGLE_MAPS_API_KEY}`,
        libraries: ["places"],
    });

    useEffect(() => {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(
                (position) => {
                    const { latitude, longitude } = position.coords;
                    setCurrentLocation({ lat: latitude, lng: longitude });
                },
                (error) => {
                    // console.log(error);
                }
            );
        } else {
            // console.log("Geolocation is not supported by this browser.");
        }
    }, []);

    if (!isLoaded) {
        return <div>Loading...</div>;
    }

    const getPixelPositionOffset = (width: any, height: any) => ({
        x: -(width / 2),
        y: -(height / 2)
    });

    const queryString = window.location.search;

    if(isLoaded){
        return (
            <>
                <div className={`${style['map-navbar']} res-mobile`}>
                    <svg onClick={() => {setIsShowMap(false)}} width="12" height="16" viewBox="0 0 12 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <line x1="1.5" y1="-1.5" x2="11.1151" y2="-1.5" transform="matrix(0.746071 -0.665866 0.533192 0.845995 1.5882 10.3999)" stroke="white" strokeWidth="3" strokeLinecap="round"/>
                        <line x1="1.5" y1="-1.5" x2="9.82572" y2="-1.5" transform="matrix(0.831011 0.556256 -0.427352 0.904085 1 9.7002)" stroke="white" strokeWidth="3" strokeLinecap="round"/>
                    </svg>

                    <div className={style['map-text']}>{trans('portal.back-to-list')}</div>
                </div>
                <div className="res-desktop" style={{position: "relative"}}>
                    <div role={"button"} onClick={() => {setIsShowMap(false)}} className={style['map-text']}>
                        <svg className="me-1" width="23" height="23" viewBox="0 0 23 23" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M7.66699 5.75H20.1253" stroke="white" strokeWidth="1.91667" strokeLinecap="round" strokeLinejoin="round"/>
                            <path d="M7.66699 11.5H20.1253" stroke="white" strokeWidth="1.91667" strokeLinecap="round" strokeLinejoin="round"/>
                            <path d="M7.66699 17.25H20.1253" stroke="white" strokeWidth="1.91667" strokeLinecap="round" strokeLinejoin="round"/>
                            <path d="M2.875 5.75H2.88458" stroke="white" strokeWidth="1.91667" strokeLinecap="round" strokeLinejoin="round"/>
                            <path d="M2.875 11.5H2.88458" stroke="white" strokeWidth="1.91667" strokeLinecap="round" strokeLinejoin="round"/>
                            <path d="M2.875 17.25H2.88458" stroke="white" strokeWidth="1.91667" strokeLinecap="round" strokeLinejoin="round"/>
                        </svg>
                        {trans('portal.back-to-list')}
                    </div>
                </div>
                <GoogleMap
                    options={{ fullscreenControl: false, mapTypeControl:false , streetViewControl:false }}
                    zoom={12}
                    center={{ lat: location?.lat, lng: location?.lng }}
                    mapContainerClassName="map"
                    mapContainerStyle={{ width: "100%", height: '760px', borderRadius: "10px" }}
                >
                    <Marker
                        icon="/img/current-icon2.png"
                        position={currentLocation ? { lat: currentLocation?.lat, lng: currentLocation?.lng } : { lat: location?.lat, lng: location?.lng }}
                    >

                    </Marker>
                    {
                        positions && positions.map((position: any, index: any) =>
                            <>
                                <Marker
                                    key={index}
                                    onClick={() => {
                                        if (currentData === index) {
                                            setCurrentData(null);
                                        } else {
                                            setCurrentData(index);
                                        }
                                    }}
                                    icon={dataPositions[index]?.is_open ? "/img/open-mark.png" : "/img/close-mark.png"}
                                    position={new window.google.maps.LatLng(position)}
                                />
                            </>
                        )
                    }
                    {
                        currentData != null && !isMobile && (
                            <OverlayView
                                position={{ lat: positions[currentData]?.lat, lng: positions[currentData]?.lng }}
                                mapPaneName={OverlayView.OVERLAY_MOUSE_TARGET}
                                getPixelPositionOffset={getPixelPositionOffset}
                            >
                                <div className={`${style['map-popup']}`}
                                     onClick={() => {window.open(`https://${dataPositions[currentData]?.slug}.${config.WEBSITE_DOMAIN}/${language}/category/products${queryString}&portal=1&origin=search`)}}>
                                    <div className={`${style['title']}`}>
                                        {dataPositions[currentData]?.name}
                                        <svg width="11" height="16" viewBox="0 0 11 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <line x1="1.41177" y1="1.41695" x2="8.91695" y2="8.08823" stroke="#B5B268" strokeWidth="2" strokeLinecap="round"/>
                                            <line x1="1.18866" y1="14.0984" x2="8.66863" y2="8.39328" stroke="#B5B268" strokeWidth="2" strokeLinecap="round"/>
                                        </svg>
                                    </div>
                                    <div className={`${style['address']}`}>
                                        {dataPositions[currentData]?.address}
                                    </div>
                                    <div className={`${style['status']}`}>
                                        {dataPositions[currentData]?.is_open ? (
                                            <>
                                                <svg className="me-1" width="12" height="13" viewBox="0 0 12 13" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <circle cx="5.70536" cy="6.50028" r="5.70536" fill="#6CCE4A"/>
                                                </svg>
                                                {trans('portal.open')}
                                            </>
                                        ) : (
                                            <>
                                                <svg className="me-1" width="12" height="13" viewBox="0 0 12 13" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <circle cx="5.70536" cy="6.50028" r="5.70536" fill="#E03009"/>
                                                </svg>
                                                {trans('portal.close')}
                                            </>
                                        )}
                                    </div>
                                </div>
                            </OverlayView>
                        )
                    }
                </GoogleMap>
            </>
        );
    }
};
