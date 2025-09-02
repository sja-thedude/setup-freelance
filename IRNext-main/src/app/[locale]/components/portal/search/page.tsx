"use client"

import variables from '/public/assets/css/portal-search.module.scss'
import { useI18n } from '@/locales/client'
import React, { useRef, useState, useEffect } from 'react';
import RegisterReady from '@/app/[locale]/components/layouts/popup/registerReady';
import ProfileUpdatePortal from '@/app/[locale]/components/layouts/popup/ProfileUpdatePortal';
import * as config from "@/config/constants"
import { api } from "@/utils/axios";

export default function PortalSearch() {
    const [currentPostCodeMatch, setCurrentPostCodeMatch] = useState<any>(null);
    const inputRef = useRef<any>(null);
    const [listAddress, setListAddress] = useState<any>([]);
    const [isShow, setIsShow] = useState(true);
    const [inputValue, setInputValue] = useState('');
    const [currentInput, setCurrentInput] = useState('');
    const trans = useI18n()

    const handleSearch = () => {
        setCurrentInput(inputRef.current?.value);
        setTimeout(function () {
            api.get(`/addresses?limit=1000&page=1&keyword=${inputRef.current?.value}`, {
            }).then(res => {
                setListAddress(res?.data?.data?.data);
            }).catch(error => {
                // console.log(error)
            });
        }, 1000);
        setIsShow(true);
    }

    function getCoordinates(address: any, item: any) {
        fetch(`https://maps.googleapis.com/maps/api/geocode/json?address=${address}&key=${config.PUBLIC_GOOGLE_MAPS_API_KEY_DISTANCE}`)
            .then(response => response.json())
            .then(data => {
                if (data.results[0].geometry.location.lat !== null && data.results[0].geometry.location.lng !== null) {
                    const latitude = data.results[0].geometry.location.lat;
                    const longitude = data.results[0].geometry.location.lng;
                    api.put(`/addresses/${item?.id}/location`, {
                        latitude: latitude.toString(),
                        longitude: longitude.toString()
                    }, {
                    }).then(res => {
                        // console.log(res)
                    }).catch(err => {
                        // console.log(err);
                    });
                }
            })
    }

    const handleSearching = () => {
    }
    
    const handleItemClick = (item: any) => {
        setIsShow(false);
        setCurrentPostCodeMatch(item);
        setInputValue(`${item?.postcode}, ${item?.city?.name}`);
        if (item?.latitude === null || item?.longitude === null) {
            getCoordinates(`${item?.postcode}, ${item?.city?.name}`, item)
        }

    }
    const [isFocused, setIsFocused] = useState(false);
    useEffect(() => {
        setIsShow(true);
    }, [isFocused]);

    const handleQuitting = () => {
        setInputValue('');
        setIsShow(false);
        setListAddress([]);
        setCurrentInput('');
        inputRef.current?.focus();
    }
    const [isPopupOpen, setIsPopupOpen] = useState(false);

    const togglePopup = () => {
        setIsPopupOpen(!isPopupOpen);
    };

    const [isReadyPopupOpen, setIsReadyPopupOpen] = useState(false);
    const toggleReadyPopup = () => {
        setIsReadyPopupOpen(!isReadyPopupOpen);
    }

    const [isProfileUpdatePopupOpen, setIsProfileUpdatePopupOpen] = useState(false);
    const toggleProfileUpdatePopup = () => {
        setIsProfileUpdatePopupOpen(!isProfileUpdatePopupOpen);
    }

    return (
        <>
            <div className="row d-flex justify-content-center">
                <div className={`${variables.search} row`}>
                    <div className={`${variables.searchInput} col-8 d-flex`}>
                        <div className={`${variables.searchImg}`}>
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="25" viewBox="0 0 24 25" fill="none">
                                <path d="M10.7884 19.7272C15.1217 19.7272 18.6346 16.1568 18.6346 11.7525C18.6346 7.34821 15.1217 3.77783 10.7884 3.77783C6.4551 3.77783 2.94226 7.34821 2.94226 11.7525C2.94226 16.1568 6.4551 19.7272 10.7884 19.7272Z" stroke="#B5B268" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" />
                                <path d="M20.5962 21.7205L16.3298 17.3843" stroke="#B5B268" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" />
                            </svg>
                        </div>
                        <input id={variables.inputing} ref={inputRef} onKeyUp={handleSearch} type="text" onFocus={() => setIsFocused(true)} onBlur={() => setIsFocused(false)} value={inputValue} onChange={(e: any) => setInputValue(e.target.value)} placeholder={trans('fill-postal-code')} />
                        {(currentInput !== '' && isFocused) || inputValue !== '' ? (
                            <svg xmlns="http://www.w3.org/2000/svg" width="8" height="8" viewBox="0 0 8 8" fill="none" className={variables.quitIcon} onClick={handleQuitting}>
                                <path d="M1 1L7 7" stroke="#BDBDBD" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" />
                                <path d="M7 1L1 7" stroke="#BDBDBD" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" />
                            </svg>
                        ) : null}

                    </div>
                    <div className={`${variables.butStore} col-4`}>
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" className={variables.searchIcon}>
                            <path d="M10.7885 18.94C15.1218 18.94 18.6347 15.3697 18.6347 10.9654C18.6347 6.5611 15.1218 2.99072 10.7885 2.99072C6.45522 2.99072 2.94238 6.5611 2.94238 10.9654C2.94238 15.3697 6.45522 18.94 10.7885 18.94Z" stroke={currentPostCodeMatch && !isFocused ? 'white' : "#AAAAAA"} strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" />
                            <path d="M20.5962 20.9334L16.3298 16.5972" stroke={currentPostCodeMatch && !isFocused ? 'white' : "#AAAAAA"} strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" />
                        </svg>
                        <button
                            type="button"
                            className={`${variables.buttonSearch} ${!currentPostCodeMatch || isFocused ? variables.disabled : ''}`}
                            aria-disabled={!currentPostCodeMatch || isFocused}
                            style={{ background: currentPostCodeMatch && !isFocused ? '#444' : '#E0E0E0' }}
                            onClick={() => {
                                currentPostCodeMatch && !isFocused ? handleSearching() : ''
                            }}
                        >
                            <p style={{ color: currentPostCodeMatch && !isFocused ? 'white' : "#AAAAAA" }}>
                                {trans('search-portal')}
                            </p>
                        </button>

                    </div>
                    {listAddress && listAddress.length > 0 && isShow && (
                        <div className={variables.listContain}>
                            {listAddress.map((item: any, index: any) => (
                                <div
                                    className={`d-flex flex-row justify-content-between ${variables.listGroup} ${index > 0 ? 'no-padding' : ''}`}
                                    key={item.id}
                                    onClick={() => handleItemClick(item)}
                                >
                                    <div><p className='list-group-text'>{item.postcode}, {item?.city?.name}</p></div>
                                </div>
                            ))}
                        </div>
                    )}
                </div>
            </div>

            <button
                type="button"
                onClick={togglePopup}
            >
                <p style={{ color: currentPostCodeMatch && !isFocused ? 'white' : "#AAAAAA" }}>
                    {trans('search-portal')}
                </p>
            </button>

            <button
                type="button"
                onClick={toggleProfileUpdatePopup}
            >
                <p style={{ color: currentPostCodeMatch && !isFocused ? 'red' : "red" }}>
                    {trans('lang_update_profile')}
                </p>
            </button>

            {isReadyPopupOpen && (
                <RegisterReady toggleReadyPopup={toggleReadyPopup} />
            )}

            {isProfileUpdatePopupOpen && (
                <ProfileUpdatePortal toggleProfileUpdatePopup={toggleProfileUpdatePopup} />
            )}

        </>
    )
}
