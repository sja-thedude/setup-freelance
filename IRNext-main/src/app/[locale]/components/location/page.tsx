'use client'

import React, {useState, useEffect} from "react";
import style from 'public/assets/css/profile.module.scss'
import {FontAwesomeIcon} from "@fortawesome/react-fontawesome";
import {faChevronLeft, faCheck} from "@fortawesome/free-solid-svg-icons";
import '@fortawesome/fontawesome-svg-core/styles.css';
import * as config from "@/config/constants";
import axios from 'axios';
import { useI18n } from '@/locales/client';
import _ from 'lodash'
import Cookies from "js-cookie";
import { useAppSelector } from '@/redux/hooks'
import { useGetWorkspaceDataByIdQuery } from '@/redux/services/workspace/workspaceDataApi'
import { useLoadScript } from '@react-google-maps/api';
import {useDebounce} from "use-debounce";
import {checkHouseNumberExists} from '@/utils/googleMap'
import { isDesktop } from 'react-device-detect';

const Location = (props: any) => {
    const sendLocation = props.location;
    const sendCloseLocation = props.closeLocation;
    const workspaceId = useAppSelector((state) => state.workspaceData.globalWorkspaceId)
    const { data: apiDataToken } = useGetWorkspaceDataByIdQuery({id: workspaceId ? workspaceId : 0})
    const apiData = apiDataToken?.data?.setting_generals;
    const color = useAppSelector((state) => state.workspaceData.globalWorkspaceColor)
    const trans = useI18n();
    const initialRef: any = null;
    const currentType = 1;
    const [currentLocation, setCurrentLocation] = useState(initialRef);
    const [currentAddress, setCurrentAddress] = useState(initialRef);
    const [searchDataAddress, setSearchDataAddress] = useState(initialRef);
    const [searchValue, setSearchValue] = useState('');
    const [value] = useDebounce(searchValue, 1000);
    const [searchLngLat, setSearchLngLat] = useState(initialRef);
    const [selectedPlace, setSelectedPlace] = useState(initialRef);
    const [selectedLocation, setSelectedLocation] = useState(initialRef);
    const [selectedLngLat, setSelectedLngLat] = useState(initialRef);
    const [recentLocation, setRecentLocation] = useState<any[]>([]);
    const [recentLngLat, setRecentLngLat] = useState<any[]>([]);
    const tokenLoggedInCookie = Cookies.get('loggedToken');

    useEffect(() => {
        const dataRecentLocation = Cookies.get('recentLocation');
        const dataRecentLngLat = Cookies.get('recentLngLat');
        if (dataRecentLocation) {
            setRecentLocation(JSON.parse(dataRecentLocation));
        }

        if (dataRecentLngLat) {
            setRecentLngLat(JSON.parse(dataRecentLngLat));
        }
    }, []);

    // get address from coordinates
    const getAddressFromCoordinates  = async (latitude: number, longitude: number, type : number) => {
        axios.get(`https://maps.googleapis.com/maps/api/geocode/json?latlng=${latitude},${longitude}&language=${config.NEXT_DEVICE_LANGUAGE_CODE}&key=${config.PUBLIC_GOOGLE_MAPS_API_KEY_DISTANCE}`, {})
        .then((res) => {
            const json = res.data;
            if (json.results) {
                if (type == 1) {
                    setCurrentAddress(json?.results[0]?.formatted_address)
                }
                return json?.results[0]?.formatted_address;

            } else {
                return null;
            }
        }).catch(err => {
            // console.log(err)
            return null;
        });
    };

    const loading = (e: any) => {}
    const [timer, setTimer] = useState<any | null>(null);
    //handle search location
    const handleSearch = (e: any) => {
        const text = e.target ? e.target.value : e;
        setSearchValue(text);

        clearTimeout(timer);
        const newTimer = setTimeout(() => {
            const searchResult = searchAddress(text, false);
        }, 1000)

        setTimer(newTimer);
    };

    //reset search input
    const resetSearch = () => {
        setSearchValue("");
        setSelectedLocation('');
        setSelectedLngLat([]);
        setSearchDataAddress([]);
    }

    const deleteRecentLocation = (key:number) => {
        recentLocation[key] = null;
        recentLngLat[key] = null;
        var filteredRecentLocation = recentLocation.filter(function (el) {
            return el != null;
        });
        var filteredRecentLngLat = recentLngLat.filter(function (el) {
            return el != null;
        });
        setRecentLocation(filteredRecentLocation);
        setRecentLngLat(filteredRecentLngLat);
        //set cookie
        const expires = new Date();
        expires.setMonth(expires.getMonth() + 1); // survive for 1 month
        var jsonRecentLocation = JSON.stringify(filteredRecentLocation);
        var jsonRecentLngLat = JSON.stringify(filteredRecentLocation);
        Cookies.set('recentLocation', jsonRecentLocation);
        Cookies.set('recentLngLat', jsonRecentLngLat);
    }

    //set selected location
    const handleSave = () => {
        //send location to parent component
        sendLocation(selectedLocation, selectedLngLat);

        //close keyboard
        handleCloseKeyboard()

        //save location as recent locations
        if  (selectedLocation && !recentLocation?.includes(selectedLocation)) {
            if (recentLocation.length >= 5) {
                recentLocation.shift();
                recentLngLat.shift();

                recentLocation.push(selectedLocation);
                recentLngLat.push({lat: selectedLngLat.lat, lng: selectedLngLat.lng});
            } else {
                recentLocation.push(selectedLocation);
                recentLngLat.push({lat: selectedLngLat.lat, lng: selectedLngLat.lng});
            }
        }

        resetSearch();

        //set cookie
        const expires = new Date();
        expires.setMonth(expires.getMonth() + 1); // survive for 1 month
        var jsonRecentLocation = JSON.stringify(recentLocation);
        var jsonRecentLngLat = JSON.stringify(recentLngLat);
        Cookies.set('recentLocation', jsonRecentLocation);
        Cookies.set('recentLngLat', jsonRecentLngLat);
        sendCloseLocation(false);
    }

    const handleCloseKeyboard = () => {
        //close keyboard on ios
        var field = document.createElement('input');
        field.setAttribute('type', 'text');
        document.body.appendChild(field);

        setTimeout(function() {
            field.focus();
            setTimeout(function() {
                field.setAttribute('style', 'display:none;');
            }, 50);
        }, 50);
    }

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
                        if (isHouse) {
                            const firstPrediction = predictions[0]

                            if(firstPrediction) {
                                setSearchDataAddress(firstPrediction ? [firstPrediction] : []);
                                handleSelectLocation(firstPrediction.place_id, firstPrediction.description, null, null)
                            } else {
                                setSearchDataAddress(firstPrediction ? [firstPrediction] : []);
                            }
                        } else {
                            setSearchDataAddress(predictions ?? []);
                        }

                        return predictions || [];
                    }
                }
        
                const autocomplete = new window.google.maps.places.AutocompleteService();
                autocomplete.getPlacePredictions({
                    language: config.NEXT_DEVICE_LANGUAGE_CODE,
                    types: ['address'],
                    input: keyWord 
                }, displaySuggestions);
            }
        }
    }

    const handleSelectLocation = (placeID: any, address:string, lat:any, lng:any) => {
        if  (lat) {
            handleSelectRecentLocation(address, lng, lat);
            let data = {
                'description': address,
                'lat': lat,
                'lng': lng,
            };
            setSearchDataAddress([data]);
        } else {
            if(isLoaded) {
                const callback = (place: any, status: any) => {
                    if (status != google.maps.places.PlacesServiceStatus.OK || !place) {
                        return []
                    } else {
                        if (place) {
                            const lat = place.geometry.location.lat();
                            const lng = place.geometry.location.lng();
                            handleSelectRecentLocation(address, lng, lat);
                            let data = {
                                'description': address,
                                'lat': lat,
                                'lng': lng,
                            };
                            setSearchDataAddress([data]);
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
    }

    const handleSelectRecentLocation = (addr : any, lng:any, lat:any) => {
        setSearchValue(addr);
        setSelectedLocation(addr);
        setSelectedLngLat({lat: lat, lng: lng});
    }

    // get current location
    const handleGetLocationClick = () => {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(
                (position) => {
                    const { latitude, longitude } = position.coords;
                    setSelectedPlace(null);
                    setSearchLngLat(null);
                    setCurrentLocation({ lat: latitude, lng: longitude });
                    getAddressFromCoordinates(latitude, longitude, currentType);
                },
                (error) => {
                    alert(error.message)
                }
            )

            getAddressFromCoordinates(currentLocation?.lat, currentLocation?.lng, currentType);
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

    window.onpopstate = e => {
        // document.getElementsByClassName('modal-backdrop')?.[0]?.remove();
        sendCloseLocation(false);
    };

    return (
        <>
            <div className={style['navbar']}>
                <div className={`${style['profile-text']} ${style['profile-text-map']}`}
                     style={{ fontSize: '29px', background: workspaceId ? color : '#B5B268'}}>
                    <FontAwesomeIcon data-bs-dismiss="modal"
                                     onClick={() =>  sendCloseLocation(false)}
                                     aria-label="Close" icon={faChevronLeft} className={style['style-icon']} />
                    { trans('its-ready') }
                </div>
            </div>
            <div>
                <div style={{ position: 'absolute', top: '50px', left: '50%', transform: 'translate(-50%, 0%)' }}>
                    <div style={{ position: 'relative' }}>
                        <svg width="25"
                             height="24"
                             viewBox="0 0 25 24"
                             fill="none"
                             style={{ position: 'absolute', top: '8px', left: '5px' }}
                             xmlns="http://www.w3.org/2000/svg">
                            <g id="search">
                                <path id="Vector" d="M11.6733 19C16.0066 19 19.5195 15.4183 19.5195 11C19.5195 6.58172 16.0066 3 11.6733 3C7.33999 3 3.82715 6.58172 3.82715 11C3.82715 15.4183 7.33999 19 11.6733 19Z" stroke={ workspaceId ? color : '#B5B268' } strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"/>
                                <path id="Vector_2" d="M21.4807 20.9999L17.2144 16.6499" stroke={ workspaceId ? color : '#B5B268' } strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"/>
                            </g>
                        </svg>
                        <input
                            type="text"
                            value={searchValue}
                            className={`form-control ${style['form-control-map']}`}
                            id="search-input"
                            placeholder=" "
                            autoComplete={'off'}
                            onChange={(e) => handleSearch(e)}
                        />
                        {
                            searchValue && searchValue.length > 0 ?
                                (<svg xmlns="http://www.w3.org/2000/svg" width="12" height="12"
                                      viewBox="0 0 12 12" fill="none"
                                      onClick={() => resetSearch()}
                                      style={{ position: 'absolute', top: '15px', right: '5px' }}>
                                <path d="M9 3L3 9" stroke="#BDBDBD" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"/>
                                <path d="M3 3L9 9" stroke="#BDBDBD" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"/>
                            </svg>) : (<></>)
                        }

                    </div>
                </div>
            </div>
            <div className='container' style={{ marginTop: '40px' }}>
                {/* Find location */}
                <div className={ `${style['group-search']} row `}>
                    <div className={`col-md-1 col-1 ${style['search-icon']}`}>
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                            <path d="M3 11L22 2L13 21L11 13L3 11Z" stroke={ workspaceId ? color : '#B5B268' } strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"/>
                        </svg>
                    </div>
                    <div className={`${style['search-label']} col-md-11 col-11`}>
                        { trans('location') }
                    </div>
                    <div className={ `${style['search-result']} col-md-12 col-12`}>
                        {searchDataAddress && searchDataAddress.map((item: any, key:number) => (
                            <div className="row py-2" key={key} style={{position: 'relative' , paddingRight: '30px'}}>
                                <div className="col-md-1 col-1"></div>
                                <div className={`col-md-11 col-11 ${ item.lat ? ('d-flex justify-content-between pe-0') : ('')} `}  >
                                    <div className={`${style['result-item']} px-0`}
                                         onClick={ item.types && checkHouseNumberExists(item?.types, true) ? (() => handleSelectLocation(item.place_id ?? null, item.description ?? null, item.lat ?? null, item.lng ?? null)) : (loading) }
                                    >{ getHighlightedText(item.description, searchValue)  }</div>
                                    {
                                        (item.types && checkHouseNumberExists(item?.types, true)) || item.lat ? (
                                            item.lat ? (
                                                <FontAwesomeIcon icon={faCheck} style={{ color: workspaceId ? color : '#B5B268' , position: 'absolute' , right: '10px' }}/>
                                            ) : (
                                                <div></div>
                                            )
                                        ) : (
                                            <input
                                            type="text"
                                            onKeyPress={(e) => {
                                              // For desktop users: trigger on Enter key press
                                              if (e.key === "Enter") {
                                                handleSearchHouseNumber(item.description, event);
                                              }
                                            }}
                                            onBlur={(e) => {
                                              // For mobile users: trigger on input blur
                                              if (!isDesktop) {
                                                handleSearchHouseNumber(item.description, event);
                                              }
                                            }}
                                            className={`${style['form-nomal']} form-control form-control-sm mt-1`}
                                            placeholder={`+ ${trans('house-number')}`}
                                          />
                                        )
                                    }
                            
                                </div>
                            </div>
                        ))} 
                    </div>
                </div>

                {/* Current location */}
                <div className={ `${style['group-search']} row `}>
                    <div className={`col-md-1 col-1 ${style['search-icon']}`}>
                        <svg width="18" height="22" viewBox="0 0 18 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M17 9.18182C17 15.5455 9 21 9 21C9 21 1 15.5455 1 9.18182C1 7.01187 1.84285 4.93079 3.34315 3.3964C4.84344 1.86201 6.87827 1 9 1C11.1217 1 13.1566 1.86201 14.6569 3.3964C16.1571 4.93079 17 7.01187 17 9.18182Z" stroke={ workspaceId ? color : '#B5B268' } strokeWidth="1.5" strokeLinecap="round" strokeLinejoin="round"/>
                            <path d="M9 13C10.6569 13 12 11.6569 12 10C12 8.34315 10.6569 7 9 7C7.34315 7 6 8.34315 6 10C6 11.6569 7.34315 13 9 13Z" stroke={ workspaceId ? color : '#B5B268' } strokeWidth="1.5" strokeLinecap="round" strokeLinejoin="round"/>
                        </svg>
                    </div>
                    <div className={`${style['search-label']} col-md-10 col-10`} onClick={handleGetLocationClick}>
                        { trans('current-location') }
                    </div>
                    <div className={ `${style['search-result']} row col-md-12 col-12`}>
                        <div className={`col-md-1 col-1`}></div>
                        <div className={`${style['result-item']} col-md-10 col-10`}
                             onClick={ () => handleSelectLocation(null, currentAddress, currentLocation?.lat, currentLocation?.lng) }> {currentAddress ? getHighlightedText(currentAddress, searchValue) : ''} </div>
                    </div>
                </div>

                {/* Recent location */}
                { recentLocation.length > 0 && recentLngLat.length > 0 &&
                    <div className={ `${style['group-search']} row `}>
                        <div className={`col-md-1 col-1 ${style['search-icon']}`}>
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                <path d="M12 22C17.5228 22 22 17.5228 22 12C22 6.47715 17.5228 2 12 2C6.47715 2 2 6.47715 2 12C2 17.5228 6.47715 22 12 22Z" stroke={ workspaceId ? color : '#B5B268' } strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"/>
                                <path d="M12 6V12L16 14" stroke={ workspaceId ? color : '#B5B268' } strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"/>
                            </svg>
                        </div>
                        <div className={`${style['search-label']} col-md-10 col-10`}>
                            { trans('recently-location') }
                        </div>
                        <div className={ style['search-result'] }>
                            {
                                recentLocation.length > 0 && recentLngLat.length > 0 && recentLocation.reverse().map((item: any, key: number) => (
                                    <div className="row" key={key} style={{position: 'relative'}}>
                                        <div className="col-md-1 col-1"></div>
                                        <div className={`col-md-10 col-10`}  >
                                            <div className={`${style['result-item']} ${key} px-0`}
                                                onClick={ () => handleSelectLocation(null, item, recentLngLat.reverse()[key]['lat'], recentLngLat.reverse()[key]['lng']) }> { item ? getHighlightedText(item, searchValue) : ''} </div>
                                        </div>
                                        <div className="col-md-1 col-1">
                                            <svg xmlns="http://www.w3.org/2000/svg"
                                                width="12" height="12"
                                                onClick={() => deleteRecentLocation(key)}
                                                viewBox="0 0 12 12" fill="none"
                                                style={{ position: 'absolute'  ,right: '10px' }}>
                                                <path d="M9 3L3 9" stroke="#BDBDBD" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"/>
                                                <path d="M3 3L9 9" stroke="#BDBDBD" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"/>
                                            </svg>
                                        </div>
                                    </div>
                                ))
                            }
                        </div>
                    </div>
                }
                
                {/* My location */}
                { tokenLoggedInCookie && props?.myAddress && (
                        <div className={ `${style['group-search']} row `}>
                            <div className={`col-md-1 col-1 ${style['search-icon']}`}>
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                    <path d="M3 9L12 2L21 9V20C21 20.5304 20.7893 21.0391 20.4142 21.4142C20.0391 21.7893 19.5304 22 19 22H5C4.46957 22 3.96086 21.7893 3.58579 21.4142C3.21071 21.0391 3 20.5304 3 20V9Z" stroke={ workspaceId ? color : '#B5B268' } strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"/>
                                    <path d="M9 22V12H15V22" stroke={ workspaceId ? color : '#B5B268' } strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"/>
                                </svg>
                            </div>
                            <div className={`${style['search-label']} col-md-10 col-10 d-block`}>
                                { trans('my-location') }
                                <span className={`${style['result-item']} px-0 d-inline`}
                                      onClick={ () => { handleCloseKeyboard(); handleSelectLocation(null, props?.myAddress, props?.myLocation?.lat, props?.myLocation?.lng) }} data-bs-dismiss="modal" aria-label="Close"> { props?.myAddress } </span>
                            </div>
                        </div>
                    )
                }
            </div>
            <div>
                {selectedLocation && (
                    <div id={`save-map`} className={`${style['language-save']}`}
                         onClick={handleSave} data-bs-dismiss="modal"
                         aria-label="Close">
                        <div className={style['language-save-button']}>
                            { trans('confirm-location') }
                        </div>
                    </div>
                )}
            </div>
        </>
    );
};

export default Location;