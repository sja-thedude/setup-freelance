'use client'

import style from 'public/assets/css/profile.module.scss'
import Menu from "../../../components/menu/menu-plus";
import Location from "../../../components/location/page";
import Image from "next/image";
import React, { useState, useEffect, useRef } from 'react';
import { useSelector } from "react-redux";
import { updateProfile } from '@/redux/slices/profileSlice';
import { useGetApiProfileQuery } from '@/redux/services/profileApi';
import { selectApiProfileData } from "@/redux/slices/profileSlice";
import { useI18n } from '@/locales/client';
import Cookies from 'js-cookie';
import NotFound from '../not-found';
import { useRouter } from "next/navigation";
import { useFormik } from 'formik';
import { useAppDispatch } from '@/redux/hooks';
import * as Yup from 'yup';
import { ToastContainer, toast, Slide } from 'react-toastify';
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
import { FontAwesomeIcon } from "@fortawesome/react-fontawesome";
import { faChevronLeft } from "@fortawesome/free-solid-svg-icons";
import { REGEX_NUMBER_CHECK, VALIDATION_PHONE_MAX } from "@/config/constants";
import { useAppSelector } from '@/redux/hooks'
import { useGetWorkspaceDataByIdQuery } from '@/redux/services/workspace/workspaceDataApi'
import MenuPortal from "@/app/[locale]/components/menu/menu-portal";
import { addOpenEditProfileSuccess } from '@/redux/slices/cartSlice'
import _ from "lodash";

export default function EditProfile() {
    const workspaceId = useAppSelector((state) => state.workspaceData.globalWorkspaceId)
    const { data: apiDataToken } = useGetWorkspaceDataByIdQuery({ id: workspaceId ? workspaceId : 0 })
    const apiData = apiDataToken?.data?.setting_generals;
    const color = useAppSelector((state) => state.workspaceData.globalWorkspaceColor)
    const tokenLoggedInCookie = Cookies.get('loggedToken');
    useGetApiProfileQuery(tokenLoggedInCookie || '');
    var apiSliceProfile = useSelector(selectApiProfileData);
    var photo = apiSliceProfile?.data?.photo ?? '/img/avatar.png';
    const [address, setAddress] = useState<any | null>(null);
    const [gsm, setGsm] = useState<any | null>('');
    const [minGsm, setMinGsm] = useState(9);
    const [avatar, setAvatar] = useState('/img/avatar.png');
    const [selectedCountry, setSelectedCountry] = useState('+32'); // Initial value for the country select
    const [location, setLocation] = useState<any | null>(null);
    const [isVisible, setIsVisible] = useState(false);
    const [errorMessage, setErrorMessage] = useState<any | null>(null);
    const [apiErrors, setApiErrors] = useState<any | null>(null);
    const [open, setOpen] = useState(false);
    const [inputValue, setInputValue] = useState(""); // Store the input value
    const [isFirstNameValid, setIsFirstNameValid] = useState(true); // Store the input value
    const [isEmailValid, setIsEmailValid] = useState(true); // Store the input value
    const [isLastNameValid, setIsLastNameValid] = useState(true); // Store the input value
    const [isGsmValid, setIsGsmValid] = useState(true); // Store the input value
    const [inputChanged, setInputChanged] = useState(false);
    const router = useRouter();
    const trans = useI18n();
    const hiddenFileInput = useRef<any | null>(null);
    const language = Cookies.get('Next-Locale');

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

    useEffect(() => {
        if (errorMessage) {
            toast.dismiss();
            toast.onChange(() => {
                setErrorMessage(null);
            });
            toast(errorMessage, {
                position: toast.POSITION.BOTTOM_CENTER,
                autoClose: 1500,
                hideProgressBar: true,
                closeOnClick: true,
                closeButton: false,
                transition: Slide,
                className: 'message',
            });
        }
        setErrorMessage(null);
    }, [errorMessage]);

    const handleCloseLocation = (isShow: boolean) => {
        var field = document.createElement('input');
        field.setAttribute('type', 'text');
        document.body.appendChild(field);

        setTimeout(function () {
            field.focus();
            setTimeout(function () {
                field.setAttribute('style', 'display:none;');
            }, 50);
        }, 50);
    }

    const validationSchema = Yup.object().shape({
        // first_name: Yup.string().required(trans('required')).matches(/^((?!@).)*$/, trans('lang_phone_valid_message')),
        // last_name: Yup.string().required(trans('required')),
        // gsm: Yup.string().required(trans('required')).min(minGsm, trans('job.message_format_gsm')).max(17, trans('job.message_format_gsm_max') ?? ''),
        // email: Yup.string().required(trans('required')).email(trans('job.message_invalid_email')),
    });

    const initFormValues = {
        first_name:'',
        last_name: '',
        gsm: '',
        email: '',
        address: ''
    };

    const [defaultValues, setDefaultValues] = useState(initFormValues);
    const dispatch = useAppDispatch();
    const formik = useFormik({
        initialValues: defaultValues,
        validationSchema,
        onSubmit: async (values) => {
            var countryCode = selectedCountry.replace(/[/+]/g, '');
            if (values.gsm.startsWith('0')) {
                setMinGsm(9);
                values.gsm = values.gsm.substring(1);
            }
            if (REGEX_NUMBER_CHECK.test(values.first_name)) {
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
                        dispatch(addOpenEditProfileSuccess(true))
                        setErrorMessage('')
                        router.refresh();
                        router.back();
                        dispatch(updateProfile(response?.data))
                    }

                } catch (error: any) {
                    setApiErrors(error.response.data.errors);
                    const errors = Object.values(error.response.data.errors);
                    const lastErrorMessage: any = errors[errors.length - 1];
                    setErrorMessage(lastErrorMessage[0]);
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
        validateOnMount: true,
    });

    const [loadFirstTime, setLoadFirstTime] = useState(true);

    useEffect(() => {
        if(loadFirstTime && apiSliceProfile && !_.isEqual(initFormValues, formik.values)) {
            handleEditClick();
            setLoadFirstTime(false);
        }
    }, [apiSliceProfile, formik.values]);
    
    if (tokenLoggedInCookie?.length == 0) {
        return (<NotFound />);
    }

    const message = variables['message'];
    const invalid = variables['invalid'];
    const handleLocation = (address: string, location: any) => {
        setAddress(address);
        setLocation(location);
    }

    // change default theme of formik
    const inputBorderRadius = 10;
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
                        borderRadius: inputBorderRadius,
                        "& .MuiOutlinedInput-notchedOutline": {
                            border: '0px',
                            //borderRadius: '10px',
                            //background: '#FFF',
                            boxShadow: '0px 4px 16px 0px rgba(0, 0, 0, 0.1)',
                            height: '50px',
                        },
                        "&.Mui-focused": {
                            "& .MuiOutlinedInput-notchedOutline": {
                                border: `0`,
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
                            marginLeft: '65px',
                        },
                        "& .MuiGrid-root": {
                            width: `100%`,
                        },
                        "& .MuiInputBase-input": {
                            padding: '11.5px 14px',
                        }
                    },
                }
            }
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
        }

        if (checkEmailValid(formik.values.email)) {
            setIsEmailValid(true);
        } else {
            setIsEmailValid(false);
            if (formik.values.email) {
                setErrorMessage(trans('job.message_invalid_email'));
            }
        }

        if (REGEX_NUMBER_CHECK.test(formik.values.first_name)) {
            setIsFirstNameValid(false);
            setErrorMessage(trans('first-name-contain-number'));
            return;
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
        }

        if (errorMessage) {
            toast.dismiss();
            toast.onChange(() => {
                setErrorMessage(null);
            });
            toast(errorMessage, {
                position: toast.POSITION.BOTTOM_CENTER,
                autoClose: 1500,
                hideProgressBar: true,
                closeOnClick: true,
                closeButton: false,
                transition: Slide,
                className: 'message',
            });
        }
    }

    function checkEmailValid(email: string) {
        // Sử dụng regex để kiểm tra định dạng email
        const emailRegex = /^[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,}$/i;
        return emailRegex.test(email);
    }

    const handleChange = async (event: any) => {
        if (event.target.files && event.target.files[0]) {
            const photo = event.target.files[0];
            const data = new FormData()
            data.append("photo", photo)
            let dataFavorites = api.post(`profile/change_avatar`, data, {
                headers: {
                    'Authorization': `Bearer ${Cookies.get('loggedToken')}`,
                    'Content-Type': `multipart/form-data`,
                    'Content-Language': language,
                }
            }).then(res => {
                setAvatar(res?.data?.data?.photo ?? '/img/avatar.png');
                setErrorMessage(trans('upload-avatar-success'))
            }).catch(err => {
                // console.log(err);
            });
        }
    };

    const handleClick = (event: any) => {
        if (hiddenFileInput?.current) {
            hiddenFileInput?.current.click();
        }
    };

    const handleChangeInput = () => {
        if(_.isEqual(formik.values, defaultValues)){
            setInputChanged(false);
        } else {
            setInputChanged(true);
        }        
    }

    return (
        <>
            <div style={{ position: 'fixed', bottom: 0, left: 0, width: '100%', zIndex: 100 }}>
                {
                    workspaceId ? (
                        <Menu />
                    ) : (
                        <MenuPortal />
                    )
                }
            </div>
            <div className={style['navbar']}>
                <div className={style['profile-text']} style={{ fontSize: '30px', background: workspaceId ? color : '#B5B268', display: 'flex' }}>
                    <FontAwesomeIcon icon={faChevronLeft} style={{ fontSize: '25px' }}
                        className={`${style['style-icon']} my-auto me-2`} onClick={() => { router.replace(`/${language}/profile/show`); }} />
                    <div>
                        {trans('change-profile')}
                    </div>
                </div>
            </div>
            <div className={style['menu-profile']}>
                <div className={style['avatar']} onClick={handleClick}>
                    <Image
                        alt=''
                        src={avatar}
                        width={100}
                        height={100}
                        sizes="100vw"
                        style={{ borderRadius: '50%' }}
                    />
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
                                        onKeyUp={() => { setIsFirstNameValid(true); handleChangeInput(); }}
                                    />
                                </Grid>
                                <Grid item xs={12} sm={12}>
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
                                        onKeyUp={() => { setIsLastNameValid(true); handleChangeInput(); }}
                                    />
                                </Grid>

                                <Grid item xs={12}>
                                    <TextField
                                        className={`${variables.texting} ${formik.touched.email && (Boolean(formik.errors.email) || (apiErrors && apiErrors.email)) || !isEmailValid ? invalid : ''}`}
                                        fullWidth
                                        id="email"
                                        name="email"
                                        placeholder={trans('email')}
                                        variant="outlined"
                                        value={formik.values.email}
                                        onChange={() => { handleInputChange(event); apiErrors && apiErrors.email ? apiErrors.email = null : '' }}
                                        onBlur={formik.handleBlur}
                                        error={true}
                                        onKeyUp={() => { setIsEmailValid(true); handleChangeInput(); }}
                                    />
                                </Grid>

                                <Grid item xs={12}>
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
                                        onKeyUp={() => { setIsGsmValid(true); handleChangeInput(); }}
                                        InputProps={{
                                            style: { color: !isGsmValid || (apiErrors && apiErrors.isGsmValid) ? '#D94B2C' : '#413E38', paddingRight: '0' },
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
                                                            <div className='d-flex'><svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" shapeRendering="geometricPrecision" textRendering="geometricPrecision" imageRendering="optimizeQuality" fillRule="evenodd" clipRule="evenodd" viewBox="0 0 203.55 141.6"><g fillRule="nonzero"><path fill="#ED2939" d="M203.55 11.19v119.22c0 6.16-5.04 11.19-11.19 11.19H11.19C5.05 141.6.02 136.59 0 130.45V11.15C.02 5.01 5.05 0 11.19 0h181.17c6.15 0 11.19 5.03 11.19 11.19z" /><path fill="#FAE042" d="M135.7 0v141.6H11.19C5.05 141.6.02 136.59 0 130.45V11.15C.02 5.01 5.05 0 11.19 0H135.7z" /><path d="M67.85 0v141.6H11.19C5.05 141.6.02 136.59 0 130.45V11.15C.02 5.01 5.05 0 11.19 0h56.66z" /></g></svg>
                                                                <div className={`${variables.country}`}>+32</div></div>
                                                        </MenuItem>
                                                        <MenuItem className={variables.customMenuItem} value="+31">
                                                            <div className='d-flex'>
                                                                <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" shapeRendering="geometricPrecision" textRendering="geometricPrecision" imageRendering="optimizeQuality" fillRule="evenodd" clipRule="evenodd" viewBox="0 0 43.06 29.96"><g fillRule="nonzero"><path fill="#21468B" d="M43.06 20v7.59c0 1.3-1.06 2.37-2.37 2.37H2.37C1.06 29.96 0 28.89 0 27.59V20h43.06z" /><path fill="#fff" d="M43.06 20H0V2.37C0 1.06 1.06 0 2.37 0h38.32c1.31 0 2.37 1.06 2.37 2.37V20z" /><path fill="#AE1C28" d="M43.06 9.96H0V2.37C0 1.06 1.06 0 2.37 0h38.32c1.31 0 2.37 1.06 2.37 2.37v7.59z" /></g></svg>
                                                                <div className={`${variables.country}`}>+31</div></div>
                                                        </MenuItem>
                                                    </Select>
                                                </InputAdornment>
                                            ),
                                        }}
                                    />
                                </Grid>
                                <Grid item xs={12} style={{ padding: '0', paddingLeft: '16px' }}>
                                    <div className={`${variables.vb} ms-2`}>
                                        {trans('vb')}
                                    </div>
                                </Grid>
                                <Grid item xs={12} style={{ position: "relative" }} data-bs-toggle="modal" data-bs-target="#locationModal">
                                    <TextField
                                        className={`${variables.texting} ${apiErrors && apiErrors.address ? invalid : ''} ps-6`}
                                        fullWidth
                                        name="address"
                                        placeholder={"    " + trans('enter-address')}
                                        //value={formik.values.last_name}
                                        //defaultValue={ apiSliceProfile?.data?.address ? "    " + apiSliceProfile?.data?.address : ''}
                                        value={address ? "    " + address : (apiSliceProfile?.data?.address ? "    " + apiSliceProfile?.data?.address : '')}
                                        variant="outlined"
                                        inputProps={{
                                            autoComplete: 'none',
                                        }}
                                        style={{ pointerEvents: 'none' }}
                                        onBlur={formik.handleBlur}
                                        error={apiErrors && apiErrors.address}
                                        onKeyUp={() => {handleChangeInput()}}
                                    />
                                    <svg style={{ position: 'absolute', left: '23px', top: '28px' }} width="18" height="22" viewBox="0 0 18 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M17 9.18182C17 15.5455 9 21 9 21C9 21 1 15.5455 1 9.18182C1 7.01187 1.84285 4.93079 3.34315 3.3964C4.84344 1.86201 6.87827 1 9 1C11.1217 1 13.1566 1.86201 14.6569 3.3964C16.1571 4.93079 17 7.01187 17 9.18182Z" stroke="#413E38" strokeWidth="1.5" strokeLinecap="round" strokeLinejoin="round" />
                                        <path d="M9 13C10.6569 13 12 11.6569 12 10C12 8.34315 10.6569 7 9 7C7.34315 7 6 8.34315 6 10C6 11.6569 7.34315 13 9 13Z" stroke="#413E38" strokeWidth="1.5" strokeLinecap="round" strokeLinejoin="round" />
                                    </svg>
                                </Grid>
                                <Grid item xs={12} style={{ padding: '0', paddingLeft: '16px' }}>
                                    <div className={`${variables.vb} ms-2`}>
                                        ({trans('optional')})
                                    </div>
                                </Grid>
                                <Grid item xs={12} className={`d-flex justify-content-center`} style={{ margin: 'auto' }}>
                                    {inputChanged ? (
                                        <Button type="submit" onClick={handleEditClick}>
                                            <div className={`${style['language-save-button']}`} >{trans('save')}</div>
                                        </Button>
                                    ) : (
                                        <Button type="button">
                                            <div className={`btn-disabled-none-background ${style['language-save-button']}`} >{trans('save')}</div>
                                        </Button>
                                    )}
                                </Grid>
                            </Grid>
                        </form>

                        <ToastContainer />
                    </ThemeProvider>

                    <div className="d-flex">
                        <div
                            className="modal"
                            id="locationModal"
                        >
                            <div className="modal-dialog">
                                <div className={`modal-content ${style['modal-content-map']}`}>
                                    <div className="modal-body p-0" >
                                        <Location location={handleLocation}
                                            closeLocation={handleCloseLocation}
                                            myAddress={apiSliceProfile?.data?.address ?? ""}
                                            myLocation={{ lat: apiSliceProfile?.data?.lat ?? '', lng: apiSliceProfile?.data?.lng ?? '' }}
                                        />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </>
    );
}
