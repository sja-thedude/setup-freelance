'use client'

import style from 'public/assets/css/profile.module.scss'
import Image from "next/image";
import React, { useState, useEffect, useRef } from 'react';
import * as config from "@/config/constants"
import { useSelector, useDispatch } from "react-redux";
import { useGetApiProfileQuery } from '@/redux/services/profileApi';
import { selectApiProfileData } from "@/redux/slices/profileSlice";
import { useI18n } from '@/locales/client';
import Cookies from 'js-cookie';
import { useRouter } from "next/navigation";
import { useFormik } from 'formik';
import * as Yup from 'yup';
import { updateProfile } from '@/redux/slices/profileSlice';
import 'react-toastify/dist/ReactToastify.css';
import {
    Button,
    Grid,
    TextField,
} from '@mui/material';
import variables from '/public/assets/css/profile.module.scss'
import { createTheme, ThemeProvider } from "@mui/material/styles";
import { InputAdornment } from '@mui/material';
import Select from '@mui/material/Select';
import MenuItem from '@mui/material/MenuItem';
import { api } from "@/utils/axios";
import { Modal } from "react-bootstrap";
import _ from "lodash";
import { VALIDATION_PHONE_MAX } from "@/config/constants";
import { useAppSelector } from '@/redux/hooks'
import { useGetWorkspaceDataByIdQuery } from '@/redux/services/workspace/workspaceDataApi'
import 'public/assets/css/popup.scss';
import { useLoadScript } from '@react-google-maps/api';
import {checkHouseNumberExists} from '@/utils/googleMap'
import useQueryEditProfileParam from '@/hooks/useQueryParam';

export default function ProfileUpdatePortal({ toggleProfileUpdatePopup }: { toggleProfileUpdatePopup: any }) {
    const workspaceId = useAppSelector((state) => state.workspaceData.globalWorkspaceId)
    const { data: apiDataToken } = useGetWorkspaceDataByIdQuery({ id: workspaceId })
    const apiData = apiDataToken?.data?.setting_generals;
    const color = useAppSelector((state) => state.workspaceData.globalWorkspaceColor)
    const tokenLoggedInCookie = Cookies.get('loggedToken');
    useGetApiProfileQuery(tokenLoggedInCookie || '');
    var apiSliceProfile = useSelector(selectApiProfileData);
    var photo = apiSliceProfile?.data?.photo ?? null;
    const dispatch = useDispatch();
    const [address, setAddress] = useState<any | null>(null);
    const [gsm, setGsm] = useState("");
    const [minGsm, setMinGsm] = useState(9);
    const [avatar, setAvatar] = useState(null);
    const [selectedCountry, setSelectedCountry] = useState('+32'); // Initial value for the country select
    const [show, setShow] = useState(false);
    const [showSearch, setShowSearch] = useState(false);
    const ref = useRef(null);
    const queryEditProfile = useQueryEditProfileParam();

    const handleClose = () => {
        if (isUploadAvatar) return;

        toggleProfileUpdatePopup();
        setShow(false);
        const query = new URLSearchParams(window.location.search);
        if (queryEditProfile === true) {
            router.push(window.location.href.replace('?editProfile=true', ''));
            router.push(window.location.href.replace('&editProfile=true', ''));
        }
    };
    const handleShow = () => setShow(true);

    useEffect(() => {
        // const hasShownPopup = localStorage.getItem('hasShownPopup');
        const hasShownPopup = false;

        if (!hasShownPopup) {
            setShow(true);
            localStorage.setItem('hasShownPopup', 'true');
        }
    }, []);
    useEffect(() => {
        setAddress(apiSliceProfile?.data?.address);
        setSearchValue(apiSliceProfile?.data?.address);
        setAvatar(photo);
        if (apiSliceProfile?.data?.gsm) {
            if (apiSliceProfile?.data?.gsm.substring(0, 3) != "+32" && apiSliceProfile?.data?.gsm.substring(0, 3) != "+31") {
                setSelectedCountry('+32');
            } else {
                setSelectedCountry(apiSliceProfile?.data?.gsm.substring(0, 3));
            }
            setGsm(apiSliceProfile?.data?.gsm.substring(3).replace(/\D/g, ''));
        }
    }, [apiSliceProfile?.data?.address, apiSliceProfile?.data?.gsm, apiSliceProfile?.data?.photo]);
    const [location, setLocation] = useState<any | null>(null);
    const [isVisible, setIsVisible] = useState(false);
    const [errorMessage, setErrorMessage] = useState<any | null>(null);
    const [showSuccessfulMessage, setShowSuccessfulMessage] = useState(false);
    const [apiErrors, setApiErrors] = useState<any | null>(null);
    const [inputValue, setInputValue] = useState(""); // Store the input value
    const [isFirstNameValid, setIsFirstNameValid] = useState(true); // Store the input value
    const [isEmailValid, setIsEmailValid] = useState(true); // Store the input value
    const [isLastNameValid, setIsLastNameValid] = useState(true); // Store the input value
    const [isGsmValid, setIsGsmValid] = useState(true); // Store the input value
    const [isUploadAvatar, setIsUploadAvatar] = useState(false); // Store the input value
    const [showLocationPopup, setShowLocationPopup] = useState('none');
    const router = useRouter();
    const trans = useI18n();
    const hiddenFileInput = useRef<any | null>(null);
    const language = Cookies.get('Next-Locale');

    const handleCloseLocation = (isShow: boolean) => {
        if (isShow) {
            setShowLocationPopup('block');
        } else {
            setShowLocationPopup('none');
        }
    }

    const validationSchema = Yup.object().shape({
        first_name: Yup.string().required(trans('required')).matches(/^((?!@).)*$/, trans('lang_phone_valid_message')),
        last_name: Yup.string().required(trans('required')),
        gsm: Yup.string().required(trans('required')).min(minGsm, trans('job.message_format_gsm')).max(16, trans('job.message_format_gsm_max') ?? ''),
        email: Yup.string().required(trans('required')).email(trans('job.message_invalid_email')),
    });

    const formik = useFormik({
        initialValues: {
            first_name: apiSliceProfile?.data?.first_name ?? '',
            last_name: apiSliceProfile?.data?.last_name ?? '',
            gsm: gsm ?? '',
            email: apiSliceProfile?.data?.email ?? '',
            address: apiSliceProfile?.data?.address ?? address ?? '',
        },
        validationSchema,
        onSubmit: async (values) => {
            var countryCode = selectedCountry.replace(/[/+]/g, '');
            if (values.gsm.startsWith('0')) {
                setMinGsm(9);
                values.gsm = values.gsm.substring(1);
            }
            var phoneNumber = '+' + countryCode + values.gsm;
            try {
                const response = await api.post('profile',
                    address ? {
                        email: values.email,
                        first_name: values.first_name,
                        gsm: phoneNumber,
                        last_name: values.last_name,
                        address: address,
                        lat: location?.lat,
                        lng: location?.lng,
                    } : {
                        email: values.email,
                        first_name: values.first_name,
                        gsm: phoneNumber,
                        last_name: values.last_name,
                    }
                    , {
                        headers: {
                            'Authorization': 'Bearer ' + tokenLoggedInCookie,
                            'Content-Language': language,
                        }
                    });
                if ('data' in response) {
                    dispatch(updateProfile(response?.data))
                    setErrorMessage(null);
                    handleClose();
                }

            } catch (error: any) {
                setApiErrors(error.response.data.errors);
                const errors = Object.values(error.response.data.errors);
                const lastErrorMessage = errors[errors.length - 1];
                setErrorMessage(lastErrorMessage);
                setShowSuccessfulMessage(false);
            }
        },
        enableReinitialize: true,
        initialTouched: {
            first_name: true,
            last_name: true,
            gsm: true,
            email: true,
        },
        validateOnMount: true,
    });


    if (tokenLoggedInCookie?.length == 0) {
        // return (<NotFound />);
    }
    const invalid = variables['invalid'];

    const handleLocation = (address: string, location: any) => {
        setAddress(address);
        setLocation(location);
    }

    // change default theme of formik
    const theme = createTheme({
        components: {
            // Inputs
            MuiOutlinedInput: {
                styleOverrides: {
                    root: {
                        // dont show increment or decrement buttons in number input
                        '& input::-webkit-outer-spin-button, & input::-webkit-inner-spin-button':
                        {
                            display: 'none',
                        },
                        '& input[type=number]': {
                            MozAppearance: 'textfield',
                        },
                        // remove outline
                        "& .MuiOutlinedInput-notchedOutline": {
                            borderRadius: '6px',
                            // border: '1px solid var(--Cart-stroke, #D1D1D1)',
                            // height: '50px',
                            width: '300px',
                        },
                        "& .MuiSelect-icon": {
                            right: '0',
                        },
                        "& .MuiSelect-select": {
                            paddingRight: '22px!important',
                            paddingLeft: '5px!important',
                            "& .MuiOutlinedInput-notchedOutline": {
                                borderTopRightRadius: '0',
                                borderBottomRightRadius: '0',
                            }
                        },
                        "&.Mui-focused": {
                            "& .MuiOutlinedInput-notchedOutline": {
                                borderWidth: '1px',
                                border: '1px solid var(--Cart-stroke, #D1D1D1)',
                            }
                        },
                        "&.Mui-error": {
                            "& .MuiOutlinedInput-notchedOutline": {
                                border: '1px solid #d32f2f',
                            }
                        },
                        "& .MuiOutlinedInput-root": {
                            backgroundColor: '#e6e6e6',
                            position: 'absolute',
                            left: '0',
                            bottom: '0px',
                            height: '45px',
                            zIndex: '100',
                            borderTopRightRadius: '0',
                            borderBottomRightRadius: '0',
                        },
                        "& #gsm": {
                            marginLeft: '50px',
                        },
                        "& .MuiGrid-root": {
                            width: `100%`,
                        },
                        "& .MuiInputBase-input": {
                            padding: '11px 14px',
                        },
                        "& .MuiButtonBase-root": {
                            width: '100%!important',
                            padding: '0px',
                        },
                        "& #address": {
                            paddingLeft: '35px',
                        },
                    },
                }
            },
        }
    });

    const handleCountryChange = (event: any) => {
        let gsmValue = formik.values.gsm

        formik.setFieldValue('gsm', gsmValue);
        setSelectedCountry(event.target.value);
    };

    const handleInputChange = (event: any) => {
        let value = event.target.value;
        setInputValue(value);
        formik.handleChange(event);
    };

    const initialRef: any = null;
    const [searchValue, setSearchValue] = useState('');
    const [searchDataAddress, setSearchDataAddress] = useState(initialRef);
    const [selectedLocation, setSelectedLocation] = useState(initialRef);
    const [selectedLngLat, setSelectedLngLat] = useState(initialRef);
    const [timer, setTimer] = useState<any | null>(null);
    //handle search location
    const handleSearch = (e: any) => {
        handleInputChange(e);
        if (e.target.value.length == 0) {
            setShowSearch(false);
        } else {
            setShowSearch(true);
        }

        const text = e.target ? e.target.value : e;
        setSearchValue(text);

        clearTimeout(timer);
        const newTimer = setTimeout(() => {
            const searchResult = searchAddress(text, false);
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
                        setShowSearch(false);
                        return []
                    } else {
                        if (isHouse) {
                            const firstPrediction = predictions[0]

                            if(firstPrediction) {
                                setSearchDataAddress(firstPrediction ? [firstPrediction] : []);
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

    const handleSelectLocation = (placeID: any, address: string, lat: any, lng: any) => {
        if (lat) {
            let data = {
                'description': address,
                'lat': lat,
                'lng': lng,
            };
            setSearchDataAddress([data]);
            setSearchValue(address);
        } else {
            const callback = (place: any, status: any) => {
                if (status != google.maps.places.PlacesServiceStatus.OK || !place) {
                    return []
                } else {
                    if (place) {
                        const lat = place.geometry.location.lat();
                        const lng = place.geometry.location.lng();
                        const address = place.formatted_address;
                        let data = {
                            'description': address,
                            'lat': lat,
                            'lng': lng,
                        };
                        setSearchDataAddress([data]);
                        setSearchValue(address);
                        handleLocation(address, data)
                        setShowSearch(false);
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

    // search address from house number
    const handleSearchHouseNumber = (address: string, e: any) => {
        e.preventDefault();
        const houseNumber = e.target ? e.target.value : '';
        const text = houseNumber + ' ' + address;

        clearTimeout(timer);
        const newTimer = setTimeout(() => {
            return searchAddress(text, true);
        }, 1000)

        setTimer(newTimer);
    };

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

    const loading = (e: any) => { };

    const handleGsmChange = (event: any) => {
        let newValue = event.target.value;
        formik.handleChange(event);
        // Remove characters that are not numbers
        const sanitizedValue = newValue.replace(/\D/g, '');
        newValue = sanitizedValue;

        // Check if the value starts with "0" and remove it
        if (newValue.startsWith('0')) {
            setMinGsm(10);
        } else {
            setMinGsm(9);
        }

        if (parseInt(selectedCountry + newValue) >= VALIDATION_PHONE_MAX) {
            // Limit the phone number length
            newValue = newValue.substring(0, (VALIDATION_PHONE_MAX - selectedCountry.length));
        }

        if (apiErrors && apiErrors.gsm) {
            apiErrors.gsm = false;
        }

        // Update the value in the formik state
        formik.setFieldValue('gsm', newValue);
    };

    // message error check
    const handleEditClick = () => {
        if (checkEmailValid(formik.values.email)) {
        } else {
            setIsEmailValid(false);
            if (formik.values.email) {
                setErrorMessage(trans('format-email'));
                setShowSuccessfulMessage(false);
            }
        }

        if (formik.values.first_name === '' || formik.values.last_name === '' || formik.values.gsm === '' || formik.values.email === '') {
            if (formik.values.first_name === '') {
                setIsFirstNameValid(false);
            }
            if (formik.values.last_name === '') {
                setIsLastNameValid(false);
            }
            if (formik.values.gsm === '') {
                setIsGsmValid(false);
            }
            if (formik.values.email === '') {
                setIsEmailValid(false);
            }

            setIsVisible(true);
            setErrorMessage(trans('missing-fields'));
            setShowSuccessfulMessage(false);
        }
    }

    function checkEmailValid(email: string) {
        // Sử dụng regex để kiểm tra định dạng email
        const emailRegex = /^[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,}$/i;
        return emailRegex.test(email);
    }

    const handleChange = async (event: any) => {
        if (event.target.files && event.target.files[0]) {
            setIsUploadAvatar(true);
            const photo = event.target.files[0];
            const data = new FormData()
            data.append("photo", photo)
            let dataFavorites = api.post(`profile/change_avatar`, data, {
                headers: {
                    'Authorization': `Bearer ${Cookies.get('loggedToken')}`,
                    'Content-Type': `multipart/form-data`,
                }
            }).then(res => {
                setAvatar(res?.data?.data?.photo);
            }).catch(err => {
                // console.log(err);
                //setErrorMessage(trans('upload-avatar-success'))
            }).finally(() => {
                setIsUploadAvatar(false);
            });;
        }
    };

    const handleClick = (event: any) => {
        if (hiddenFileInput?.current) {
            hiddenFileInput?.current.click();
        }
    };

    const handleDeleteAvatar = () => {
        let deleteAvatar = api.post(`profile/remove_avatar`, {}, {
            headers: {
                'Authorization': `Bearer ${Cookies.get('loggedToken')}`,
            }
        }).then(res => {
            setAvatar(null);
        }).catch(err => {
            // console.log(err);
        });
    }
    const [open, setOpen] = useState(false);
    const handleTop = () => {
        if (!showSearch) {
            return '25px'
        } else {
            return '30px'
        }
    }

    return (
        <>
            <Button onClick={handleShow} style={{ display: 'none' }}></Button>

            <Modal show={show} onHide={handleClose}
                animation={false}
                id='modal-profile-portal'
            >
                <Modal.Body>
                    <div className='row'>
                        <div className='col-3'>
                            <div className="text-center px-3 pt-3 pb-1 profile-title">{trans('change-profile')}</div>
                            <div className="text-center px-3 profile-descriptions">

                                <div className={`${style['avatar']} mb-3`}>
                                    {
                                        avatar ? (
                                            <Image
                                                alt=''
                                                src={avatar}
                                                width={200}
                                                height={200}
                                                sizes="100vw"
                                                style={{ borderRadius: '50%' }}
                                            />
                                        ) : (
                                            <div className={`${style['avatar-not-found']} mb-4`} style={{ marginTop: '34px' }}>
                                                <Image
                                                    alt='sliders'
                                                    src="/img/avatarn.svg"
                                                    width={200}
                                                    height={200}
                                                />
                                            </div>
                                        )
                                    }

                                    <div className={style['delete-avatars']} onClick={() => handleDeleteAvatar()}>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="38" height="38" viewBox="0 0 38 38" fill="none">
                                            <path d="M4.75 9.5H7.91667H33.25" stroke="white" strokeWidth="3.16667" strokeLinecap="round" strokeLinejoin="round" />
                                            <path d="M30.0833 9.49984V31.6665C30.0833 32.5064 29.7497 33.3118 29.1558 33.9057C28.5619 34.4995 27.7565 34.8332 26.9166 34.8332H11.0833C10.2434 34.8332 9.43799 34.4995 8.84412 33.9057C8.25026 33.3118 7.91663 32.5064 7.91663 31.6665V9.49984M12.6666 9.49984V6.33317C12.6666 5.49332 13.0003 4.68786 13.5941 4.094C14.188 3.50013 14.9934 3.1665 15.8333 3.1665H22.1666C23.0065 3.1665 23.8119 3.50013 24.4058 4.094C24.9997 4.68786 25.3333 5.49332 25.3333 6.33317V9.49984" stroke="white" strokeWidth="3.16667" strokeLinecap="round" strokeLinejoin="round" />
                                            <path d="M15.8334 17.4165V26.9165" stroke="white" strokeWidth="3.16667" strokeLinecap="round" strokeLinejoin="round" />
                                            <path d="M22.1666 17.4165V26.9165" stroke="white" strokeWidth="3.16667" strokeLinecap="round" strokeLinejoin="round" />
                                        </svg>
                                    </div>
                                </div>

                                <span role="button" onClick={() =>  !isUploadAvatar && handleClick(event)} style={{ color: '#B5B268', textDecoration: 'underline' }}>{trans('click-here')}</span>
                                <span>{' ' + trans('to-change-photo')}</span>
                                <input type="file"
                                    ref={hiddenFileInput}
                                    onChange={handleChange}
                                    accept="image/*"
                                    style={{ display: 'none' }} />
                            </div>
                        </div>
                        <div className='col-9'>
                            <div className="close-popup pt-1 me-3" onClick={() => handleClose()}>
                                <svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M13 1L1 13" stroke="black" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" />
                                    <path d="M1 1L13 13" stroke="black" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" />
                                </svg>

                            </div>
                            <div className={`${style['detail-profile']} mt-5`}>
                                <ThemeProvider theme={theme}>
                                    <form onSubmit={formik.handleSubmit} method={'POST'} style={{ position: 'relative' }}>
                                        <Grid container spacing={2} style={{ justifyContent: 'start' }}>
                                            <Grid item xs={6} sm={6} className={`${variables.portalUpdateP} d-flex`}>
                                                <div className={`${variables.labelPotal} mb-1 col-2`}>{trans('first-name')}</div>
                                                <TextField
                                                    className={`${variables.textingPotaling} ${formik.touched.first_name && (Boolean(formik.errors.first_name) || (apiErrors && apiErrors.first_name)) ? invalid : ''}`}
                                                    fullWidth
                                                    id="first_name"
                                                    name="first_name"
                                                    placeholder={trans('first-name')}
                                                    variant="outlined"
                                                    value={formik.values.first_name}
                                                    onChange={formik.handleChange}
                                                    onBlur={formik.handleBlur}
                                                    error={formik.touched.first_name && Boolean(formik.errors.first_name || (apiErrors && apiErrors?.first_name))}
                                                />
                                            </Grid>

                                            <Grid item xs={6} className={`${variables.portalUpdateP} d-flex`}>
                                                <div className={`${variables.labelPotal} mb-1`}>
                                                    {trans('lang_email_form_label')}
                                                </div>
                                                <TextField
                                                    className={`${variables.textingPotaling} ${formik.touched.email && (Boolean(formik.errors.email) || (apiErrors && apiErrors.email)) ? invalid : ''}`}
                                                    fullWidth
                                                    id="email"
                                                    name="email"
                                                    placeholder={trans('email')}
                                                    variant="outlined"
                                                    value={formik.values.email}
                                                    onChange={() => { handleInputChange(event); apiErrors && apiErrors.email ? apiErrors.email = null : '' }}
                                                    onBlur={formik.handleBlur}
                                                    error={formik.touched.email && Boolean(formik.errors.email || (apiErrors && apiErrors?.email))}
                                                />

                                                {errorMessage && (
                                                    <div className={`${variables.vbae} ms-2 mt-1`}>
                                                        {errorMessage}
                                                    </div>
                                                )}
                                            </Grid>

                                            <Grid item xs={6} sm={6} className={`${variables.portalUpdateP} d-flex`}>
                                                <div className={`${variables.labelPotal} mb-1`}>{trans('last-name')}</div>
                                                <TextField
                                                    className={`${variables.textingPotaling} ${formik.touched.last_name && (Boolean(formik.errors.last_name) || (apiErrors && apiErrors.last_name)) ? invalid : ''}`}
                                                    fullWidth
                                                    id="last_name"
                                                    name="last_name"
                                                    placeholder={trans('last-name')}
                                                    variant="outlined"
                                                    value={formik.values.last_name}
                                                    onChange={formik.handleChange}
                                                    onBlur={formik.handleBlur}
                                                    error={formik.touched.last_name && Boolean(formik.errors.last_name || (apiErrors && apiErrors?.last_name))}
                                                />
                                            </Grid>

                                            <Grid item xs={6} className={`${variables.portalUpdateP} d-flex`} style={{ paddingTop: errorMessage ? '30px' : '16px' }}>
                                                <div className={`${variables.labelPotal} mb-1`} style={{ top: errorMessage ? '45px' : '30px' }}>{trans('adres')}</div>
                                                <svg width="18" height="22" viewBox="0 0 18 22" fill="none" xmlns="http://www.w3.org/2000/svg" className={variables.addressIcon} style={{ top: errorMessage ? '42px' : '26px' }}>
                                                    <path d="M17 9.18182C17 15.5455 9 21 9 21C9 21 1 15.5455 1 9.18182C1 7.01187 1.84285 4.93079 3.34315 3.3964C4.84344 1.86201 6.87827 1 9 1C11.1217 1 13.1566 1.86201 14.6569 3.3964C16.1571 4.93079 17 7.01187 17 9.18182Z" stroke="#413E38" strokeWidth="1.5" strokeLinecap="round" strokeLinejoin="round" />
                                                    <path d="M9 13C10.6569 13 12 11.6569 12 10C12 8.34315 10.6569 7 9 7C7.34315 7 6 8.34315 6 10C6 11.6569 7.34315 13 9 13Z" stroke="#413E38" strokeWidth="1.5" strokeLinecap="round" strokeLinejoin="round" />
                                                </svg>

                                                <TextField
                                                    className={`${variables.textingPotaling} ${formik.touched.address && (Boolean(formik.errors.address) || (apiErrors && apiErrors.address)) ? invalid : ''}`}
                                                    fullWidth
                                                    id="address"
                                                    autoComplete={'off'}
                                                    name="address"
                                                    inputProps={{
                                                        autoComplete: 'none',
                                                    }}
                                                    label={formik.values.address ? '' : trans('enter-address')}
                                                    placeholder={trans('enter-address')}
                                                    variant="outlined"
                                                    value={searchValue}
                                                    onChange={(e) => handleSearch(e)}
                                                    onBlur={formik.handleBlur}
                                                    error={formik.touched.address && Boolean(formik.errors.address || (apiErrors && apiErrors?.address))}
                                                    InputLabelProps={{
                                                        shrink: false, // Đặt shrink thành false chỉ khi giá trị không rỗng
                                                        style: {
                                                            fontFamily: 'SF Compact Display',
                                                            fontSize: "16px",
                                                            fontStyle: 'normal',
                                                            fontWeight: ' 400',
                                                            lineHeight: '20px',
                                                            letterSpacing: '-0.24px',
                                                            color: '#949494',
                                                            marginLeft: '18px',
                                                            marginTop: '-3px',
                                                        }
                                                    }}

                                                />
                                                {
                                                    showSearch && (
                                                        <div className={`${style['search-result-portal']} col-md-12 col-12`} style={{ width: '300px', position: "absolute", top: "65px", left: '80px' }}>
                                                            {searchDataAddress && searchDataAddress.map((item: any, key: number) => {
                                                                const selectable = item.types && checkHouseNumberExists(item?.types, true) ? true : false;
                                                                return (
                                                                    <div className="row py-3" key={key} style={{ borderBottom: `${searchDataAddress.length != key + 1 ? 0.5 : 0}px solid #C4C4C4` }}>
                                                                        <div className={`col-md-12 col-12 ${item.lat ? ('d-flex justify-content-between pe-0') : ('')} `}  >
                                                                            <div className={`${style['result-item']} px-0`} role={selectable ? "button" : ""}
                                                                                onClick={selectable ? (() => handleSelectLocation(item.place_id ?? null, item.description ?? null, item.lat ?? null, item.lng ?? null)) : (loading)}
                                                                            >{getHighlightedText(item.description, searchValue)}</div>
                                                                            {
                                                                                selectable || item.lat ? (
                                                                                    item.lat ? (
                                                                                        <div></div>
                                                                                    ) : (
                                                                                        <div></div>
                                                                                    )
                                                                                ) : (
                                                                                    <input type="text" style={{ color: '#B5B268', border: `1px solid #B5B268` }}
                                                                                        onKeyPress={e => {
                                                                                            if (e.key === "Enter") {
                                                                                                handleSearchHouseNumber(item.description, event)
                                                                                            }
                                                                                        }}
                                                                                        className={`${style['form-nomal']} house-number form-control form-control-sm mt-1`}
                                                                                        ref={ref} placeholder={`+ ${trans('house-number')}`}></input>
                                                                                )
                                                                            }
                                                                        </div>
                                                                    </div>
                                                                )
                                                            })}
                                                        </div>
                                                    )
                                                }
                                            </Grid>

                                            <Grid item xs={6} className={`${variables.portalUpdateP} d-flex`}>
                                                <div className={`${variables.labelPotal} mb-1`}>{trans('lang_gsm_form_label')}</div>
                                                <TextField
                                                    type="text"
                                                    className={`${variables.textingPotaling} ${formik.touched.gsm && (Boolean(formik.errors.gsm) || (apiErrors && apiErrors.gsm)) ? invalid : ''}`}
                                                    fullWidth
                                                    id="gsm"
                                                    name="gsm"
                                                    placeholder={trans('mobile')}
                                                    variant="outlined"
                                                    value={formik.values.gsm}
                                                    onChange={handleGsmChange}
                                                    onBlur={formik.handleBlur}
                                                    error={formik.touched.gsm && Boolean(formik.errors.gsm || (apiErrors && apiErrors.gsm))}
                                                    InputProps={{
                                                        // style: { color: !isGsmValid || (apiErrors && apiErrors.isGsmValid) ? '#D94B2C' : '#413E38' ,paddingRight: '0'},
                                                        startAdornment: (
                                                            <InputAdornment position="start">
                                                                <Select
                                                                    open={open}
                                                                    onOpen={(e) => {
                                                                        e.preventDefault();
                                                                        setTimeout(() => {
                                                                            (document.activeElement as HTMLElement).blur();
                                                                            setOpen(true);
                                                                        }, 0);
                                                                    }}
                                                                    onClose={() => setOpen(false)}
                                                                    value={selectedCountry == "+31" ? "+31" : "+32"}
                                                                    onChange={handleCountryChange}
                                                                >
                                                                    <MenuItem className={variables.customMenuItem} value="+32">
                                                                        <div className='d-flex ps-2'><svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" shapeRendering="geometricPrecision" textRendering="geometricPrecision" imageRendering="optimizeQuality" fillRule="evenodd" clipRule="evenodd" viewBox="0 0 203.55 141.6"><g fillRule="nonzero"><path fill="#ED2939" d="M203.55 11.19v119.22c0 6.16-5.04 11.19-11.19 11.19H11.19C5.05 141.6.02 136.59 0 130.45V11.15C.02 5.01 5.05 0 11.19 0h181.17c6.15 0 11.19 5.03 11.19 11.19z" /><path fill="#FAE042" d="M135.7 0v141.6H11.19C5.05 141.6.02 136.59 0 130.45V11.15C.02 5.01 5.05 0 11.19 0H135.7z" /><path d="M67.85 0v141.6H11.19C5.05 141.6.02 136.59 0 130.45V11.15C.02 5.01 5.05 0 11.19 0h56.66z" /></g></svg>
                                                                            <div className={`${variables.country}`}>+32</div></div>
                                                                    </MenuItem>
                                                                    <MenuItem className={variables.customMenuItem} value="+31">
                                                                        <div className='d-flex ps-2'>
                                                                            <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" shapeRendering="geometricPrecision" textRendering="geometricPrecision" imageRendering="optimizeQuality" fillRule="evenodd" clipRule="evenodd" viewBox="0 0 43.06 29.96"><g fillRule="nonzero"><path fill="#21468B" d="M43.06 20v7.59c0 1.3-1.06 2.37-2.37 2.37H2.37C1.06 29.96 0 28.89 0 27.59V20h43.06z" /><path fill="#fff" d="M43.06 20H0V2.37C0 1.06 1.06 0 2.37 0h38.32c1.31 0 2.37 1.06 2.37 2.37V20z" /><path fill="#AE1C28" d="M43.06 9.96H0V2.37C0 1.06 1.06 0 2.37 0h38.32c1.31 0 2.37 1.06 2.37 2.37v7.59z" /></g></svg>
                                                                            <div className={`${variables.country}`}>+31</div></div>
                                                                    </MenuItem>
                                                                </Select>
                                                            </InputAdornment>
                                                        ),
                                                    }}
                                                />
                                            </Grid>
                                            <Grid item xs={6} className="d-flex p-0">
                                                <div className={`${variables.clicked} col-2 mb-3`} style={{ paddingBottom: showSearch ? '30px' : '', marginRight: 'auto' }}>
                                                    <Button type="submit" onClick={() => handleEditClick()}>
                                                        <div style={Object.keys(formik.errors).length != 0 ? { background: '#B5B268', opacity: '0.4' } : { background: '#B5B268' }}
                                                            className={`${style['save-buttons']} ${Object.keys(formik.errors).length != 0 ? `${style['btn-disable']}` : ``}`}>{trans('save')}</div>
                                                    </Button>
                                                </div>
                                            </Grid>
                                            <Grid item xs={12} className="d-flex p-0">
                                                <div className={`${variables.labelPotal} mb-1`}></div>
                                                <div className={`${variables.vba} ms-2 mt-1`}>
                                                    {trans('vb')}
                                                </div>
                                            </Grid>
                                        </Grid>
                                    </form>
                                </ThemeProvider>
                            </div>
                        </div>
                    </div>
                </Modal.Body>
            </Modal>
            <style>{`
                .MuiButtonBase-root {
                    width: 100%!important;
                    padding: 0px!important;
                }
                .house-number::-webkit-input-placeholder {
                    color: #B5B268;
                }
                .house-number:-moz-placeholder {
                    color: #B5B268;
                
                }
                .house-number::-moz-placeholder {
                    color: #B5B268;
                
                }
                `}
            </style>
        </>
    );
}