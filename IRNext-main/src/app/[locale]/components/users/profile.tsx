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
import _, { set } from "lodash";
import { VALIDATION_PHONE_MAX } from "@/config/constants";
import { useAppSelector } from '@/redux/hooks'
import { useGetWorkspaceDataByIdQuery } from '@/redux/services/workspace/workspaceDataApi'
import { useLoadScript } from '@react-google-maps/api';
import { setFlagDesktopChangeType } from '@/redux/slices/flagDesktopChangeTypeSilce'
import {checkHouseNumberExists} from '@/utils/googleMap'
import useQueryEditProfileParam from '@/hooks/useQueryParam';

export default function Profile({ togglePopup }: { togglePopup: any }) {
    const workspaceId = useAppSelector((state) => state.workspaceData.globalWorkspaceId)
    const { data: apiDataToken } = useGetWorkspaceDataByIdQuery({ id: workspaceId })
    const apiData = apiDataToken?.data?.setting_generals;
    const color = useAppSelector((state) => state.workspaceData.globalWorkspaceColor) ?? '#ABA765';
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
        togglePopup();
        setShow(false);

        if (queryEditProfile === true) {
            router.push(window.location.href.replace('&editProfile=true', ''));
            router.push(window.location.href.replace('?editProfile=true', ''));
        }
        if( Cookies.get('opendedAddressDesk') === 'true'){
            dispatch(setFlagDesktopChangeType(true))
            Cookies.remove('opendedAddressDesk')
        }
    };

    const handleShow = () => setShow(true);

    useEffect(() => {
        const hasShownPopup = false;

        if (!hasShownPopup) {
            setShow(true);
            localStorage.setItem('hasShownPopup', 'true');
        }
    }, []);

    const initFormValues = {
        first_name:'',
        last_name: '',
        gsm: '',
        email: '',
        address: ''
    };

    const [defaultValues, setDefaultValues] = useState(initFormValues);

    useEffect(() => {
        setAddress(apiSliceProfile?.data?.address);
        setAvatar(photo);

        if(apiSliceProfile?.data) {
            if (apiSliceProfile?.data?.gsm) {
                if (apiSliceProfile?.data?.gsm.substring(0, 3) != "+32" && apiSliceProfile?.data?.gsm.substring(0, 3) != "+31") {
                    setSelectedCountry('+32');
                } else {
                    setSelectedCountry(apiSliceProfile?.data?.gsm.substring(0, 3));
                }

                setGsm(apiSliceProfile?.data?.gsm.substring(3).replace(/\D/g, ''));
            }

            setDefaultValues({
                first_name: apiSliceProfile?.data?.first_name ?? '',
                last_name: apiSliceProfile?.data?.last_name ?? '',
                gsm: apiSliceProfile?.data?.gsm?.substring(3)?.replace(/\D/g, '') ?? '',
                email: apiSliceProfile?.data?.email ?? '',
                address: apiSliceProfile?.data?.address ?? '',
            })
        }
    }, [apiSliceProfile]);

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
    const router = useRouter();
    const trans = useI18n();
    const hiddenFileInput = useRef<any | null>(null);
    const language = Cookies.get('Next-Locale');
    const validationSchema = Yup.object().shape({
        // first_name: Yup.string().required(trans('required')).matches(/^((?!@).)*$/, trans('lang_phone_valid_message')),
        // last_name: Yup.string().required(trans('required')),
        // gsm: Yup.string().required(trans('required')).min(minGsm, trans('job.message_format_gsm')).max(16, trans('job.message_format_gsm_max') ?? ''),
        // email: Yup.string().required(trans('required')).email(trans('job.message_invalid_email')),
    });

    const formik = useFormik({
        initialValues: defaultValues,
        validationSchema,
        onSubmit: async (values) => {
            var countryCode = selectedCountry.replace(/[/+]/g, '');
            if (values.gsm.startsWith('0')) {
                setMinGsm(9);
                values.gsm = values.gsm.substring(1);
            }
            if (config.REGEX_NUMBER_CHECK.test(formik.values.first_name)) {
                setIsFirstNameValid(false);
                setErrorMessage(trans('first-name-contain-number'));
                return;
            }
            var phoneNumber = '+' + countryCode + values.gsm;
            if (formik.values.first_name !== '' &&
                formik.values.last_name !== '' &&
                formik.values.gsm !== '' &&
                formik.values.email !== '' &&
                checkEmailValid(formik.values.email) &&
                formik.values.gsm.length >= minGsm
            ) {
                try {
                    const response = await api.post('profile',
                    formik.values.address ? {
                            email: values.email,
                            first_name: values.first_name,
                            gsm: phoneNumber,
                            last_name: values.last_name,
                            address: formik.values.address,
                            lat: location?.lat,
                            lng: location?.lng,
                        } : {
                            email: values.email,
                            first_name: values.first_name,
                            gsm: phoneNumber,
                            last_name: values.last_name,
                            address: null,
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
                        setShowSuccessfulMessage(true);
                    }

                } catch (error: any) {
                    setApiErrors(error.response.data.errors);
                    const errors = Object.values(error.response.data.errors);
                    const lastErrorMessage = errors[errors.length - 1];
                    setErrorMessage(lastErrorMessage);
                    setShowSuccessfulMessage(false);
                }
            }
        },
        enableReinitialize: true,
        initialTouched: {
            first_name: true,
            last_name: true,
            gsm: true,
            email: true,
        },
        validateOnMount: true
    });

    const [loadFirstTime, setLoadFirstTime] = useState(true);

    useEffect(() => {
        if(loadFirstTime && apiSliceProfile && !_.isEqual(initFormValues, formik.values)) {
            handleEditClick();
            setLoadFirstTime(false);
        }
    }, [apiSliceProfile, formik.values]);

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
                            border: '1px solid var(--Cart-stroke, #D1D1D1)',
                            height: '50px',
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
                        "& .MuiOutlinedInput-root": {
                            backgroundColor: '#e6e6e6',
                            position: 'absolute',
                            left: '0',
                            bottom: '0px',
                            height: '45px',
                            zIndex: '100',
                            borderTopRightRadius: '0',
                            borderBottomRightRadius: '0'
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
                        }
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
    const [searchValue, setSearchValue] = useState(initialRef);
    const [searchDataAddress, setSearchDataAddress] = useState(initialRef);
    const [timer, setTimer] = useState<any | null>(null);
    //handle search location
    const handleSearch = (e: any) => {
        handleInputChange(e);
        setShowSearch(true);
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
            if (isLoaded) {
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
        if (formik.values.gsm.length < minGsm) {
            setIsGsmValid(false);
            setIsVisible(true);
            setErrorMessage(trans('job.message_format_gsm'));
            setShowSuccessfulMessage(false);
        }

        if (checkEmailValid(formik.values.email)) {
            setIsEmailValid(true);
        } else {
            setIsEmailValid(false);
            if (formik.values.email) {
                setErrorMessage(trans('job.message_invalid_email'));
                setShowSuccessfulMessage(false);
            }
        }

        if (config.REGEX_NUMBER_CHECK.test(formik.values.first_name)) {
            setIsFirstNameValid(false);
            setErrorMessage(trans('first-name-contain-number'));
            return;
        }

        if (formik.values.first_name === '' || formik.values.last_name === '' || formik.values.gsm === '' || formik.values.email === '') {
            if (formik.values.gsm === '') {
                setIsGsmValid(false);
            }
            if (formik.values.email === '') {
                setIsEmailValid(false);
            }

            if (formik.values.last_name === '') {
                setIsLastNameValid(false);
            }

            if (formik.values.first_name === '') {
                setIsFirstNameValid(false);
            }
            
            setIsVisible(true);
            setErrorMessage(trans('missing-fields'));
            setShowSuccessfulMessage(false);
        }
    }

    function checkEmailValid(email: string) {
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
                const dataClone: any = { ...res };
                dataClone.photo = res?.data?.data?.photo;
                dispatch(updateProfile(dataClone?.data))
            }).catch(err => {
                // console.log(err);
                //setErrorMessage(trans('upload-avatar-success'))
            }).finally(() => {
                setIsUploadAvatar(false);
            });
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
            const dataClone: any = { ...res };
            dataClone.photo = null;
            dispatch(updateProfile(dataClone?.data))
        }).catch(err => {
            // console.log(err);
        });
    }

    const [open, setOpen] = useState(false);
    const [inputChanged, setInputChanged] = useState(false);
    const handleChangeInput = () => {
        if(_.isEqual(formik.values, defaultValues)){
            setInputChanged(false);
        } else {
            setInputChanged(true);
        }        
    }

    return (
        <>
            <Button onClick={handleShow} style={{ display: 'none' }}>
                Launch Introduce modal
            </Button>

            <Modal show={show} 
                onHide={handleClose}
                animation={false}
                id='modal-profile'>
                <Modal.Body>
                    <div className="close-popup pt-1" onClick={() => handleClose()}>
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="17" viewBox="0 0 16 17" fill="none" style={{ marginTop: '1px' }}>
                            <path d="M12 4.2168L4 12.2168" stroke="#888888" strokeWidth="3" strokeLinecap="round" strokeLinejoin="round" />
                            <path d="M4 4.2168L12 12.2168" stroke="#888888" strokeWidth="3" strokeLinecap="round" strokeLinejoin="round" />
                        </svg>
                        <div className="ms-1">{trans('close')}</div>
                    </div>
                    {errorMessage && (
                        <div className={`px-3`}>
                            <div className={`${style['error-message']}`}>
                                <svg className="me-2" xmlns="http://www.w3.org/2000/svg" width="21" height="21" viewBox="0 0 21 21" fill="none">
                                    <path d="M9.00368 3.37756L1.59243 15.7501C1.43962 16.0147 1.35877 16.3147 1.35792 16.6203C1.35706 16.9258 1.43623 17.2263 1.58755 17.4918C1.73887 17.7572 1.95706 17.9785 2.22042 18.1334C2.48378 18.2884 2.78313 18.3717 3.08868 18.3751H17.9112C18.2167 18.3717 18.5161 18.2884 18.7794 18.1334C19.0428 17.9785 19.261 17.7572 19.4123 17.4918C19.5636 17.2263 19.6428 16.9258 19.6419 16.6203C19.6411 16.3147 19.5602 16.0147 19.4074 15.7501L11.9962 3.37756C11.8402 3.1204 11.6206 2.90779 11.3585 2.76023C11.0964 2.61267 10.8007 2.53516 10.4999 2.53516C10.1992 2.53516 9.90347 2.61267 9.64138 2.76023C9.3793 2.90779 9.15966 3.1204 9.00368 3.37756V3.37756Z" stroke="#E03009" strokeWidth="1.5" strokeLinecap="round" strokeLinejoin="round" />
                                    <path d="M10.5 7.875V11.375" stroke="#E03009" strokeWidth="1.5" strokeLinecap="round" strokeLinejoin="round" />
                                    <path d="M10.5 14.875H10.5088" stroke="#E03009" strokeWidth="1.5" strokeLinecap="round" strokeLinejoin="round" />
                                </svg>
                                {errorMessage}
                            </div>
                        </div>
                    )}
                    <div className="text-center px-3 pt-3 pb-1 profile-title">{trans('change-profile')}</div>
                    <div className="text-center px-3 profile-description">
                        <span role="button" onClick={() => !isUploadAvatar && handleClick(event)} style={{ color: color ?? '#ABA765', textDecoration: 'underline' }}>{trans('click-here')}</span>
                        <span>{' ' + trans('to-change-photo')}</span>
                    </div>
                    <div className={style['menu-profile']}>
                        <div className={style['avatar']}>
                            {
                                avatar ? (
                                    <Image
                                        alt=''
                                        src={avatar}
                                        width={126}
                                        height={126}
                                        sizes="100vw"
                                        style={{ borderRadius: '50%' }}
                                    />
                                ) : (
                                    <div className={style['avatar-not-found']} style={{ background: color }}>
                                        {apiSliceProfile?.data?.name.split(/\s/).reduce((response: string, word: string) => response += word.slice(0, 1), '')}
                                    </div>
                                )
                            }

                            <div className={style['delete-avatar']} onClick={() => handleDeleteAvatar()}>
                                <svg xmlns="http://www.w3.org/2000/svg" width="38" height="38" viewBox="0 0 38 38" fill="none">
                                    <path d="M4.75 9.5H7.91667H33.25" stroke="white" strokeWidth="3.16667" strokeLinecap="round" strokeLinejoin="round" />
                                    <path d="M30.0833 9.49984V31.6665C30.0833 32.5064 29.7497 33.3118 29.1558 33.9057C28.5619 34.4995 27.7565 34.8332 26.9166 34.8332H11.0833C10.2434 34.8332 9.43799 34.4995 8.84412 33.9057C8.25026 33.3118 7.91663 32.5064 7.91663 31.6665V9.49984M12.6666 9.49984V6.33317C12.6666 5.49332 13.0003 4.68786 13.5941 4.094C14.188 3.50013 14.9934 3.1665 15.8333 3.1665H22.1666C23.0065 3.1665 23.8119 3.50013 24.4058 4.094C24.9997 4.68786 25.3333 5.49332 25.3333 6.33317V9.49984" stroke="white" strokeWidth="3.16667" strokeLinecap="round" strokeLinejoin="round" />
                                    <path d="M15.8334 17.4165V26.9165" stroke="white" strokeWidth="3.16667" strokeLinecap="round" strokeLinejoin="round" />
                                    <path d="M22.1666 17.4165V26.9165" stroke="white" strokeWidth="3.16667" strokeLinecap="round" strokeLinejoin="round" />
                                </svg>
                            </div>
                        </div>
                        <input type="file"
                            ref={hiddenFileInput}
                            onChange={handleChange}
                            accept="image/*"
                            style={{ display: 'none' }} />
                        <div className={style['detail-profile']}>
                            <ThemeProvider theme={theme}>
                                <form onSubmit={formik.handleSubmit} method={'POST'}>
                                    <Grid container spacing={2} style={{ justifyContent: 'center' }}>
                                        <Grid item xs={12} sm={12}>
                                            <div className={`${variables.label} mb-1`}>{trans('first-name')}</div>
                                            <TextField
                                                className={`${variables.texting} ${formik.touched.first_name && (Boolean(formik.errors.first_name) || (apiErrors && apiErrors.first_name)) || !isFirstNameValid ? invalid : ''}`}
                                                fullWidth
                                                id="first_name"
                                                name="first_name"
                                                placeholder={trans('first-name')}
                                                variant="outlined"
                                                value={formik.values.first_name}
                                                onChange={formik.handleChange}
                                                onBlur={formik.handleBlur}
                                                error={formik.touched.first_name && Boolean(formik.errors.first_name || (apiErrors && apiErrors?.first_name)) || !isFirstNameValid}
                                                onKeyUp={() => { setIsFirstNameValid(true); handleChangeInput() }}
                                            />
                                        </Grid>
                                        <Grid item xs={12} sm={12}>
                                            <div className={`${variables.label} mb-1`}>{trans('last-name')}</div>
                                            <TextField
                                                className={`${variables.texting} ${formik.touched.last_name && (Boolean(formik.errors.last_name) || (apiErrors && apiErrors.last_name)) || !isLastNameValid ? invalid : ''}`}
                                                fullWidth
                                                id="last_name"
                                                name="last_name"
                                                placeholder={trans('last-name')}
                                                variant="outlined"
                                                value={formik.values.last_name}
                                                onChange={formik.handleChange}
                                                onBlur={formik.handleBlur}
                                                error={formik.touched.last_name && Boolean(formik.errors.last_name || (apiErrors && apiErrors?.last_name)) || !isLastNameValid}
                                                onKeyUp={() => { setIsLastNameValid(true); handleChangeInput() }}
                                            />
                                        </Grid>

                                        <Grid item xs={12}>
                                            <div className={`${variables.label} mb-1`}>{trans('email')}</div>
                                            <TextField
                                                className={`${variables.texting} ${formik.touched.email && (Boolean(formik.errors.email) || (apiErrors && apiErrors.email)) || !isEmailValid ? invalid : ''}`}
                                                fullWidth
                                                id="email"
                                                name="email"
                                                placeholder={trans('email')}
                                                variant="outlined"
                                                value={formik.values.email}
                                                onChange={() => { handleInputChange(event); apiErrors && apiErrors.email ? apiErrors.email = null : '' }}
                                                onKeyUp={() => { setIsEmailValid(true); handleChangeInput() }}
                                                onBlur={formik.handleBlur}
                                                error={formik.touched.email && Boolean(formik.errors.email || (apiErrors && apiErrors?.email)) || !isEmailValid}
                                            />
                                        </Grid>

                                        <Grid item xs={12}>
                                            <div className={`${variables.label} mb-1`}>{trans('mobile')}</div>
                                            <TextField
                                                type="text"
                                                className={`${variables.texting} ${formik.touched.gsm && (Boolean(formik.errors.gsm) || (apiErrors && apiErrors.gsm)) || !isGsmValid ? invalid : ''}`}
                                                fullWidth
                                                id="gsm"
                                                name="gsm"
                                                placeholder={trans('mobile')}
                                                variant="outlined"
                                                value={formik.values.gsm}
                                                onChange={handleGsmChange}
                                                onBlur={formik.handleBlur}
                                                error={formik.touched.gsm && Boolean(formik.errors.gsm || (apiErrors && apiErrors.gsm)) || !isGsmValid}
                                                onKeyUp={() => { setIsGsmValid(true); handleChangeInput() }}
                                                InputProps={{
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
                                        <Grid item xs={12}>
                                            <div className={`${variables.label} mb-1`}>
                                                {trans('adres')}
                                                <span className={`${variables.subLabel}`}> ({trans('optional')})</span>
                                            </div>
                                            <TextField
                                                className={`${variables.texting} ${formik.touched.address && (Boolean(formik.errors.address) || (apiErrors && apiErrors.address)) ? invalid : ''}`}
                                                fullWidth
                                                id="address"
                                                autoComplete={'off'}
                                                name="address"
                                                inputProps={{
                                                    autoComplete: 'none',
                                                }}
                                                placeholder={trans('enter-address')}
                                                variant="outlined"
                                                value={searchValue ?? address}
                                                onChange={(e) => handleSearch(e)}
                                                onBlur={formik.handleBlur}
                                                onKeyUp={() => {handleChangeInput()}}
                                                error={formik.touched.address && Boolean(formik.errors.address || (apiErrors && apiErrors?.address))}
                                            />
                                            {
                                                showSearch && (
                                                    <div className={`${style['search-result']} col-md-12 col-12`}>
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
                                                                                <input type="text" style={workspaceId ? { color: color, border: `1px solid ${color}` } : { color: color ?? '#ABA765', border: `1px solid #ABA765` }}
                                                                                    onKeyUp={e => {
                                                                                        handleSearchHouseNumber(item.description, event)
                                                                                    }}
                                                                                    className={`${style['form-nomal']} house-number form-control form-control-sm mt-1`}
                                                                                    ref={ref} placeholder={`+ ${trans('house-number')}`}>
                                                                                </input>
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
                                        <Grid item xs={12} className={`d-flex justify-content-center`} style={{ margin: 'auto', marginTop: '35px' }}>
                                            {inputChanged ? (
                                                <Button type="submit" onClick={handleEditClick}>
                                                    <div style={{ background: color }} className={`${style['save-button']}`} >
                                                        {trans('save')}
                                                    </div>
                                                </Button>
                                            ) : (
                                                <Button type="button" className="btn-disabled-none-background">
                                                    <div style={{ background: color }} className={`${style['save-button']}`} >
                                                        {trans('save')}
                                                    </div>
                                                </Button>
                                            )}
                                        </Grid>
                                        {showSuccessfulMessage && (
                                            <Grid item xs={12} className={`d-flex justify-content-center`} style={{ margin: 'auto' }}>
                                                <div className={`${style['successful-message']}`}>
                                                    <svg className="me-2" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                                        <path d="M22 11.0799V11.9999C21.9988 14.1563 21.3005 16.2545 20.0093 17.9817C18.7182 19.7088 16.9033 20.9723 14.8354 21.5838C12.7674 22.1952 10.5573 22.1218 8.53447 21.3744C6.51168 20.6271 4.78465 19.246 3.61096 17.4369C2.43727 15.6279 1.87979 13.4879 2.02168 11.3362C2.16356 9.18443 2.99721 7.13619 4.39828 5.49694C5.79935 3.85768 7.69279 2.71525 9.79619 2.24001C11.8996 1.76477 14.1003 1.9822 16.07 2.85986" stroke={color} strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" />
                                                        <path d="M22 4L12 14.01L9 11.01" stroke={color} strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" />
                                                    </svg>
                                                    {trans('edit-profile-successfully')}
                                                </div>
                                            </Grid>
                                        )}
                                    </Grid>
                                </form>
                            </ThemeProvider>
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
