'use client'

import React, {useState, memo, useEffect, useRef} from "react";
import style from 'public/assets/css/profile.module.scss'
import '@fortawesome/fontawesome-svg-core/styles.css';
import * as config from "@/config/constants"
import { useI18n } from '@/locales/client';
import {useSelector} from "react-redux";
import { useGetApiProfileQuery } from '@/redux/services/profileApi';
import { selectApiProfileData } from "@/redux/slices/profileSlice";
import Cookies from "js-cookie";
import { useAppSelector, useAppDispatch } from '@/redux/hooks'
import { useLoadScript } from '@react-google-maps/api';
import {
   addReadyDelivery
} from '@/redux/slices/cartSlice'
import { setFlagDesktopChangeType } from '@/redux/slices/flagDesktopChangeTypeSilce'
import {checkHouseNumberExists} from '@/utils/googleMap'

function DeliveryLocation(props: any) {
    const sendLocation = props.location;
    const errorDeliveryMessage = props.errorDeliveryMessage;
    const color = useAppSelector((state) => state.workspaceData.globalWorkspaceColor)
    const trans = useI18n();
    const initialRef: any = null;
    const [searchDataAddress, setSearchDataAddress] = useState(initialRef);
    const [searchValue, setSearchValue] = useState('');
    const [showResult, setShowResult] = useState(false);
    const dispatch = useAppDispatch()
    const [activeType, setActiveType] = useState(2);
    const ref = useRef(null);
    const tokenLoggedInCookie = Cookies.get('loggedToken');
    useGetApiProfileQuery(tokenLoggedInCookie || '');
    var apiSliceProfile = useSelector(selectApiProfileData);
    const loading = (e: any) => {}
    const [timer, setTimer] = useState<any | null>(null);
    //handle search location
    const handleSearch = (e: any) => {
        const text = e.target ? e.target.value : e;
        setSearchValue(text);
        clearTimeout(timer);
        const newTimer = setTimeout(() => {
            const searchResult = searchAddress(text, false);
            setShowResult(true);
        }, 1000)

        setTimer(newTimer);
    };

    // search address from house number
    const handleSearchHouseNumber = (address: string, e: any) => {
        const houseNumber = e.target ? e.target.value : '';
        const text = houseNumber + ' ' +  address;
        clearTimeout(timer);
        const newTimer = setTimeout(() => {
            return searchAddress(text, true);
        }, 1000)

        setTimer(newTimer);
    };

    const { isLoaded } = useLoadScript({
        googleMapsApiKey: `${config.PUBLIC_GOOGLE_MAPS_API_KEY_AUTOCOMPLETE}`,
        libraries: ['places']
    })

    // get address from text search
    const searchAddress = (keyWord: string, isHouse: boolean) => {
        if (keyWord.trim()) {        
            if(isLoaded) {
                const displaySuggestions = (predictions: any, status: any) => {
                    if (status != google.maps.places.PlacesServiceStatus.OK || !predictions) {
                        return []
                    } else {
                        setSearchDataAddress(predictions ?? []);

                        return predictions || [];
                    }
                }
        
                const autocomplete = new window.google.maps.places.AutocompleteService();
                autocomplete.getPlacePredictions({
                    language: config.NEXT_DEVICE_LANGUAGE_CODE,
                    input: keyWord
                }, displaySuggestions);
            }
        }
    }

    const handleSelectLocation = (placeID: any, address:string, lat:any, lng:any) => {
        if  (lat) {
            let data = {
                'description': address,
                'lat': lat,
                'lng': lng,
            };
            sendLocation(data);
            setShowResult(false);
        } else {
            const callback = (place: any, status: any) => {
                if (status != google.maps.places.PlacesServiceStatus.OK || !place) {
                    return []
                } else {
                    if (place) {
                        const lat = place.geometry.location.lat();
                        const lng = place.geometry.location.lng();
                        let data = {
                            'description': address,
                            'lat': lat,
                            'lng': lng,
                        };
                        setSearchDataAddress([data]);
                        sendLocation(data);
                        setSearchValue(address);
                        setShowResult(false);
                    } else {
                        return null;
                    }
                }
            }

            const service = new window.google.maps.places.PlacesService(
                new window.google.maps.Map(document.createElement('div'))
            );
            service.getDetails({
                language: config.NEXT_DEVICE_LANGUAGE_CODE,
                placeId: placeID,
            }, callback);
        }
    }

    const getHighlightedText = (text: string, highlight: string) => {
        const first = 0;
        if (highlight) {
            const parts = text.split(new RegExp(`(${highlight})`, 'gi'));
            return <span> {parts.map((part, i) =>
                        <span key={i} className={`${part.toLowerCase() === highlight.toLowerCase() && i < 3 ? 'font-bold' : ''}`}>
                            {part}
                            </span>)
                    } </span>
        } else {
            return <span>{text}</span>
        }
    }

    const handleActiveType = (type: any) => {
        setActiveType(type);
        // This is for ready delivery but need to click next to confirm is delivery type
        dispatch(addReadyDelivery(true));
    }

    const handleSelectMyLocation = () => {
        handleActiveType(1);
        if (apiSliceProfile?.data?.address && apiSliceProfile?.data?.lng) {
            handleSelectLocation(null, apiSliceProfile?.data?.address, apiSliceProfile?.data?.lat, apiSliceProfile?.data?.lng);
        }
    }

    const handleSelectSearchLocation = () => {
        handleActiveType(2);
        if (searchDataAddress && searchDataAddress.length == 1 && searchDataAddress[0]?.lat && searchDataAddress[0]?.lng && !searchDataAddress[0]?.placeID) {
            handleSelectLocation(null, searchDataAddress[0]?.description, searchDataAddress[0]?.lat, searchDataAddress[0]?.lng);
        } else {
            sendLocation(null);
        }
    }

    // Set color for  background  if scroll
    const [scrolled, setScrolled] = useState(false);
    useEffect(() => {
        window.addEventListener('scroll', handleScroll);
        return () => {
            window.removeEventListener('scroll', handleScroll);
        };
    }, []);

    const handleScroll = () => {
        if (window.scrollY > 0) {
            setScrolled(true);
        } else {
            setScrolled(false);
        }
    };

    return (
        <>
            {errorDeliveryMessage && (
                <div className="row types-error">
                    <svg className="ps-0 col-1" xmlns="http://www.w3.org/2000/svg" width="21" height="21" viewBox="0 0 21 21" fill="none">
                        <path d="M9.0038 3.37756L1.59255 15.7501C1.43975 16.0147 1.35889 16.3147 1.35804 16.6203C1.35718 16.9258 1.43635 17.2263 1.58767 17.4918C1.73899 17.7572 1.95718 17.9785 2.22054 18.1334C2.4839 18.2884 2.78325 18.3717 3.0888 18.3751H17.9113C18.2169 18.3717 18.5162 18.2884 18.7796 18.1334C19.0429 17.9785 19.2611 17.7572 19.4124 17.4918C19.5637 17.2263 19.6429 16.9258 19.6421 16.6203C19.6412 16.3147 19.5604 16.0147 19.4076 15.7501L11.9963 3.37756C11.8403 3.1204 11.6207 2.90779 11.3586 2.76023C11.0965 2.61267 10.8008 2.53516 10.5 2.53516C10.1993 2.53516 9.90359 2.61267 9.6415 2.76023C9.37942 2.90779 9.15979 3.1204 9.0038 3.37756V3.37756Z" stroke="#E03009" strokeWidth="1.5" strokeLinecap="round" strokeLinejoin="round"/>
                        <path d="M10.5 7.875V11.375" stroke="#E03009" strokeWidth="1.5" strokeLinecap="round" strokeLinejoin="round"/>
                        <path d="M10.5 14.875H10.5088" stroke="#E03009" strokeWidth="1.5" strokeLinecap="round" strokeLinejoin="round"/>
                    </svg>
                    <div className="col-10 p-0">
                        {errorDeliveryMessage}
                    </div>
                </div>
            )}
            <div className="types-body desktop">
                <div className="row mb-3">
                    <div className="col-sm-12 col-12">
                        <h3  className="types-title">
                            {trans('choose-address-question')}
                        </h3>
                    </div>
                </div>
                {
                    tokenLoggedInCookie && (
                        <div className="row mb-3" >
                            <div className="col-sm-12 col-12">
                                <div className={`type-option type-delivery ${activeType === 1 && 'active'}`}
                                     onClick={() => handleSelectMyLocation()}>
                                    <div className="types-normal text-uppercase" style={activeType == 1 ? { color: color } : {}}>
                                        {trans('my-address')}
                                    </div>
                                    { (apiSliceProfile?.data?.address && apiSliceProfile?.data?.lng ? (
                                            <div className="type-description"> {apiSliceProfile?.data?.address}</div>
                                        ) : (
                                            <>
                                                <div className="type-description">
                                                    <span>{trans('go-to')}</span>
                                                    <span role="button"
                                                          style={{color: color}}
                                                          onClick={() => {document.getElementById('infor-group')?.click(); Cookies.set('opendedAddressDesk', 'true'); dispatch(setFlagDesktopChangeType(false))}}
                                                          className={`link-type-description`}>{' ' + trans('my-profile') + ' '}</span>
                                                    <span>{trans('save-address')}</span>
                                                </div>
                                            </>
                                        )
                                    ) }
                                </div>
                            </div>
                        </div>
                    )
                }
                <div className="row mb-3">
                    <div className="col-sm-12 col-12">
                        <div className={`type-option type-delivery ${activeType === 2 && 'active'}`} 
                            onClick={() => handleSelectSearchLocation()}
                            style={!tokenLoggedInCookie ? {paddingTop: '30px'} : {}} >
                            { tokenLoggedInCookie && (
                                <div className="types-normal text-uppercase" style={activeType == 2 ? { color: color } : {}}>
                                    {trans('another address')}
                                </div> 
                            )}
                            <div className="type-description" style={{position: 'relative'}}>
                                <input
                                    type="text"
                                    value={searchValue}
                                    className={`form-control ${style['form-control-map']}`}
                                    id="search-input"
                                    placeholder={trans('enter-delivery')}
                                    autoComplete={'off'}
                                    onChange={(e) => handleSearch(e)}
                                />
                                {
                                    showResult && (
                                        <div className={ `${style['search-result']} col-md-12 col-12`} style={{position: 'absolute'}}>
                                            {searchDataAddress && searchDataAddress.map((item: any, key:number) => (
                                                <div className="row py-3" key={key} style={{borderBottom: `${searchDataAddress.length != key + 1 ? 0.5 : 0}px solid #C4C4C4`}}>
                                                    <div className={`col-md-12 col-12 ${ item.lat ? ('d-flex justify-content-between pe-0') : ('')} `}  >
                                                        <div className={`${style['result-item']} px-0`}
                                                             role={"button"}
                                                             onClick={ item.types && checkHouseNumberExists(item?.types, true) ? (() => handleSelectLocation(item.place_id ?? null, item.description ?? null, item.lat ?? null, item.lng ?? null)) : (loading) }
                                                        >{ getHighlightedText(item.description, searchValue)  }</div>
                                                        {
                                                            !((item.types && checkHouseNumberExists(item?.types, true)) || item.lat) && (
                                                                <input type="text" style={{color:color,  border: `1px solid ${color}`}}
                                                                       onKeyUp={e => {
                                                                           handleSearchHouseNumber(item.description, event)
                                                                       }}
                                                                       className= {`${style['form-nomal']} house-number form-control form-control-sm mt-1`}
                                                                       ref={ref} placeholder={`+ ${trans('house-number')}`}></input>
                                                            )
                                                        }
                                                    </div>
                                                </div>
                                            ))}
                                        </div>
                                    )
                                }
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <style>{`
                .house-number::-webkit-input-placeholder {
                    color: ${color};
                }
                .house-number:-moz-placeholder {
                    color: ${color};
                }
                .house-number::-moz-placeholder {
                    color: ${color};
                }
                `}
            </style>
        </>
    );
}

export default memo(DeliveryLocation)
