'use client'
import React, { useEffect, useMemo, useState } from 'react';
import { Button, Modal } from 'react-bootstrap';
import 'public/assets/css/popup.scss';
import variables from '/public/assets/css/profile.module.scss'
import { useI18n } from '@/locales/client'
import { api } from "@/utils/axios";
import Cookies from "js-cookie";
import { useSelector } from "react-redux";
import { useGetApiProfileQuery } from '@/redux/services/profileApi';
import { selectApiProfileData } from "@/redux/slices/profileSlice";
import { Slide, toast } from "react-toastify";
import {
    Grid, InputAdornment,
    TextField,
} from '@mui/material';
import MenuItem from "@mui/material/MenuItem";
import Select from "@mui/material/Select";
import style from "../../../../../../public/assets/css/profile.module.scss";
import { updateProfile } from '@/redux/slices/profileSlice';
import { useAppDispatch } from '@/redux/hooks'
import { createTheme, ThemeProvider } from "@mui/material/styles";
import * as Yup from "yup";
import { useFormik } from "formik";
import { REGEX_NUMBER_CHECK, VALIDATION_PHONE_MAX } from "@/config/constants";

export default function ProfileUpdate({ color, isShow, togglePopup, newProfileData }: { color: any, isShow: any, togglePopup: any, newProfileData: any }) {
    const [show, setShow] = useState(false);
    const [gsm, setGsm] = useState('');
    const [minGsm, setMinGsm] = useState(9);
    const [isGsmValid, setIsGsmValid] = useState(true);
    const [isEmailValid, setIsEmailValid] = useState(true);
    const [showSuccessfulMessage, setShowSuccessfulMessage] = useState(false);
    const [apiErrors, setApiErrors] = useState<any | null>(null);
    const [isFirstNameValid, setIsFirstNameValid] = useState(true);
    const tokenLoggedInCookie = Cookies.get('loggedToken');
    useGetApiProfileQuery(tokenLoggedInCookie || '');
    useSelector(selectApiProfileData);
    const [errorMessage, setErrorMessage] = useState<any | null>(null);
    const [showedErrorMessage, setShowedErrorMessage] = useState<any | null>(null);
    const [dataProfile, setDataProfile] = useState<any | null>(null);
    const trans = useI18n()
    const invalid = variables['invalid'];
    const dispatch = useAppDispatch();
    const handleClose = () => {
        setShow(false);
    };

    const handleShow = () => setShow(true);
    useEffect(() => {
        if (isShow) {
            setShow(true);
        }
    }, [isShow]);

    useEffect(() => {
        if (errorMessage) {
            toast.dismiss();
            // Hiển thị toast
            toast.onChange(()=> setErrorMessage(null));
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
    }, [errorMessage]);

    const [selectedCountry, setSelectedCountry] = useState('+32'); // Initial value for the country select
    const handleCountryChange = (event: any) => {
        let gsmValue = formik.values.gsm;
        setOpen(false);
        setGsm(gsmValue);
        setSelectedCountry(event.target.value);
    };

    const handleGsmChange = (event: any) => {
        let newValue = event.target.value;
    
        // Clear any existing errors
        formik.setFieldError('gsm', '');
        formik.setFieldTouched('gsm', true);
    
        // Remove characters that are not numbers
        const sanitizedValue = newValue.replace(/\D/g, '');
        newValue = sanitizedValue;
    
        // Check if the value starts with "0" and adjust minGsm accordingly
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
    
        // Update the value in the Formik state
        formik.setFieldValue('gsm', newValue);
    
        // Trigger validation explicitly after updating the field
        formik.validateField('gsm');
    };    

    const checkEmailValid = (email: string) => {
        const emailRegex = /^[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,}$/i;
        return emailRegex.test(email);
    }

    const validation = (values: any, formikOrigin: any) => {
        let valid = true;
        let invalidCounter = 0;
        let message = '';

        if (values.email === '' || !checkEmailValid(values?.email)) {
            message = trans('change_step_email_invalid');
            formikOrigin.setFieldError('email', trans('change_step_email_invalid'));
            setIsEmailValid(false);
            valid = false;
            invalidCounter++;
        }
        if (values.gsm === '' || values.gsm.length < minGsm) {
            message = trans('job.message_format_gsm');
            formikOrigin.setFieldError('gsm', trans('job.message_format_gsm'));
            setIsGsmValid(false);
            valid = false;
            invalidCounter++;
        }
        if (values.first_name === '' || REGEX_NUMBER_CHECK.test(values.first_name)) {
            message = trans('change_step_first_name_invalid');
            formikOrigin.setFieldError('first_name', trans('change_step_first_name_invalid'));
            setIsFirstNameValid(false);
            valid = false;
            invalidCounter++;
        }

        if (valid === false && invalidCounter > 1) {
            message = trans('change_step_common_invalid_format');
            setShowSuccessfulMessage(false);
        }

        if(valid === false) {
            setErrorMessage(message);
            setShowedErrorMessage(message);
        }

        if (values?.gsm?.startsWith('0')) {
            setMinGsm(9);
            values.gsm = values.gsm.substring(1);
            formikOrigin.setFieldValue('gsm', values.gsm);
            formikOrigin.setFieldTouched('gsm', true);
        }

        return {valid, values, message};
    }
    // message error check
    const handleEditClick = () => {
        validation(formik.values, formik);
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
                            paddingLeft: '68px',
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

    useEffect(() => {
        api.get('/profile', {
            headers: {
                'Authorization': `Bearer ${tokenLoggedInCookie}`,
                'Content-Language': language
            }
        }).then((res) => {
            const data = res?.data?.data;
            setDataProfile(data);

            if (data?.gsm !== 'null' && data?.gsm) {
                if (data?.gsm.substring(0, 3) != "+32" && data?.gsm.substring(0, 3) != "+31") {
                    setSelectedCountry('+32');
                } else {
                    setSelectedCountry(data?.gsm.substring(0, 3));
                }
                setGsm(data?.gsm.substring(3).replace(/\D/g, ''));
            }

            if (data?.first_name == "" || data?.first_name.includes("@") || REGEX_NUMBER_CHECK.test(data?.first_name)) {
                setIsFirstNameValid(false);
                formik.setFieldError('gsm', trans('missing-fields'));
            } else {
                formik.setFieldValue('first_name', data?.first_name);
                formik.setFieldTouched('first_name', true);
            }        

            if (data?.gsm == "" || !data?.gsm || data.gsm === 'null') {
                setIsGsmValid(false);
                formik.setFieldError('gsm', trans('job.message_format_gsm'));
            } else {
                formik.setFieldValue('gsm', data?.gsm.substring(3).replace(/\D/g, ''));
                formik.setFieldTouched('gsm', true);
            }

            if (data?.email == "" || !data?.email || data.email === 'null') {
                setIsEmailValid(false);
                formik.setFieldError('email', trans('missing-fields'));
            } else if (!checkEmailValid(data?.email)) {
                setIsEmailValid(false);
                formik.setFieldError('email', trans('job.message_invalid_email'));
            } else {
                formik.setFieldValue('email', data?.email);
                formik.setFieldTouched('email', true);
            }
        });
    }, []);

    const validationSchema = Yup.object().shape({
        first_name: Yup.string().required(trans('required')).matches(/^((?!@).)*$/, trans('lang_phone_valid_message')),
        gsm: Yup.string().required(trans('required')).max(16, trans('job.message_format_gsm_max') ?? ''),
        email: Yup.string().required(trans('required')),
    });

    const language = Cookies.get('Next-Locale');
    const formik = useFormik({
        initialValues: {
            first_name:'',
            gsm: '',
            email: ''
        },
        validationSchema,
        onSubmit: async (values) => {
            const validate: any = validation(values, formik);

            if(validate.valid === true) {
                values = validate.values;
                var countryCode = selectedCountry.replace(/[/+]/g, '');
                var phoneNumber = '+' + countryCode + values.gsm;
    
                try {
                    const response = await api.post('profile',
                        dataProfile?.last_name ?
                            {
                                email: values?.email,
                                first_name: values.first_name,
                                gsm: phoneNumber,
                                last_name: dataProfile?.last_name,
                            } :
                            {
                                email: values?.email,
                                first_name: values.first_name,
                                gsm: phoneNumber,
                                last_name: dataProfile?.last_name,
                                required_only_gsm: 1,
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
                        setShowedErrorMessage(null);
                        setShowSuccessfulMessage(true);
                        newProfileData(true);
                    }
    
                    handleClose();
                } catch (error: any) {
                    setApiErrors(error.response.data.errors);
                    const errors = Object.values(error.response.data.errors);
                    const lastErrorMessage = errors[errors.length - 1];
                    setErrorMessage(lastErrorMessage);
                    setShowedErrorMessage(lastErrorMessage);
                    setShowSuccessfulMessage(false);
                }
            }            
        },
        enableReinitialize: true
    });

    const [open, setOpen] = useState(false);

    const isDisable = useMemo(() => {
        return !formik.values?.first_name 
        || !formik.values?.gsm 
        || !formik.values?.email;
    }, [
        formik.values.first_name, 
        formik.values.gsm,
        formik.values.email
    ]);

    return (
        <>
            {
                window.innerWidth < 1280 ? (
                    <>
                        <Button variant="primary" onClick={() => handleShow} style={{ display: 'none' }}>
                            Launch Introduce modal
                        </Button>
                        <Modal show={show} onHide={handleClose}
                            aria-labelledby="contained-modal-title-vcenter"
                            centered id='product-suggestion' className='profile-update'
                        >
                            <div className={`mx-auto`} style={{ alignItems: 'center' }}>
                                <svg xmlns="http://www.w3.org/2000/svg" width="101" height="3" viewBox="0 0 101 3" fill="none">
                                    <path d="M2 1.5H99" stroke="#E1E1E1" strokeWidth="3" strokeLinecap="round" />
                                </svg>
                            </div>
                            <Modal.Header style={{ justifyContent: "left" }}>
                                <h1>{trans('comlete-account-infor')}</h1>
                            </Modal.Header>
                            <Modal.Body>
                                <ThemeProvider theme={theme}>
                                    <form onSubmit={formik.handleSubmit} method={'POST'}>
                                        <Grid container spacing={2} style={{ justifyContent: 'center' }}>
                                            {!isFirstNameValid && (
                                                <Grid item xs={12} sm={12}>
                                                    <TextField
                                                        className={`${variables.texting}`}
                                                        fullWidth
                                                        id="first_name"
                                                        name="first_name"
                                                        placeholder={trans('first-name')}
                                                        variant="outlined"
                                                        value={formik.values.first_name}
                                                        onChange={(e) => {
                                                            formik.handleChange(e);
                                                            formik.setFieldTouched('first_name', e?.target?.value !== '', false);
                                                        }}
                                                        onBlur={formik.handleBlur}
                                                        error={formik.touched.first_name && Boolean(formik.errors.first_name || (apiErrors && apiErrors?.first_name))}
                                                    />
                                                </Grid>
                                            )}

                                            {!isGsmValid && (
                                                <Grid item xs={12} sm={12} style={{ position: 'relative' }}>
                                                    <TextField
                                                        type="text"
                                                        className={`${variables.texting}`}
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
                                                                <InputAdornment position="end">
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
                                                    <div className={`${variables.vb} ms-2`}>
                                                        {trans('vb')}
                                                    </div>
                                                </Grid>
                                            )}

                                            {!isEmailValid && (
                                                <Grid item xs={12}>
                                                    <TextField
                                                        className={`${variables.texting}`}
                                                        fullWidth
                                                        id="email"
                                                        name="email"
                                                        placeholder={trans('email')}
                                                        variant="outlined"
                                                        value={formik.values.email}
                                                        onChange={(e) => {
                                                            formik.handleChange(e);
                                                            formik.setFieldTouched('email', e?.target?.value !== '', false);
                                                        }}
                                                        onBlur={formik.handleBlur}
                                                        error={formik.touched.email && Boolean(formik.errors.email || (apiErrors && apiErrors?.email))}
                                                    />
                                                </Grid>
                                            )}

                                            <Grid item xs={12} sm={12}>
                                                <div className={`${variables.vb}`}>
                                                    {trans('enter-first-name-phone')}
                                                </div>
                                            </Grid>
                                            <Grid item xs={12} className={`d-flex justify-content-center`} style={{ margin: 'auto' }}>
                                                <div className={'mx-auto mb-4'}>
                                                    <Button
                                                        onClick={() => { handleEditClick() }}
                                                        type="submit"
                                                        className="btn btn-dark border-0"
                                                        style={{ width: "100%", padding: "10px 30px" }}
                                                        disabled={isDisable}
                                                    >
                                                        {trans('save')}
                                                    </Button>
                                                </div>
                                            </Grid>
                                        </Grid>
                                    </form>
                                </ThemeProvider>
                            </Modal.Body>
                            <Modal.Footer>
                            </Modal.Footer>
                        </Modal>
                    </>
                ) : (
                    <>
                        <Button variant="primary" onClick={handleShow} style={{ display: 'none' }}>
                            Launch Introduce modal
                        </Button>
                        <Modal show={show} onHide={handleClose}
                            animation={false}
                            id='modal-profile'
                            className='profile-update model-profile-update'
                        >
                            <Modal.Body>
                                <div className="close-popup pt-1" onClick={() => handleClose()}>
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="17" viewBox="0 0 16 17" fill="none" style={{ marginTop: '1px' }}>
                                        <path d="M12 4.2168L4 12.2168" stroke="#888888" strokeWidth="3" strokeLinecap="round" strokeLinejoin="round" />
                                        <path d="M4 4.2168L12 12.2168" stroke="#888888" strokeWidth="3" strokeLinecap="round" strokeLinejoin="round" />
                                    </svg>
                                    <div className="ms-1">{trans('close')}</div>
                                </div>
                                {showedErrorMessage && (
                                    <div className={`px-3`}>
                                        <div className={`${style['error-message']}`}>
                                            <svg className="me-2" xmlns="http://www.w3.org/2000/svg" width="21" height="21" viewBox="0 0 21 21" fill="none">
                                                <path d="M9.00368 3.37756L1.59243 15.7501C1.43962 16.0147 1.35877 16.3147 1.35792 16.6203C1.35706 16.9258 1.43623 17.2263 1.58755 17.4918C1.73887 17.7572 1.95706 17.9785 2.22042 18.1334C2.48378 18.2884 2.78313 18.3717 3.08868 18.3751H17.9112C18.2167 18.3717 18.5161 18.2884 18.7794 18.1334C19.0428 17.9785 19.261 17.7572 19.4123 17.4918C19.5636 17.2263 19.6428 16.9258 19.6419 16.6203C19.6411 16.3147 19.5602 16.0147 19.4074 15.7501L11.9962 3.37756C11.8402 3.1204 11.6206 2.90779 11.3585 2.76023C11.0964 2.61267 10.8007 2.53516 10.4999 2.53516C10.1992 2.53516 9.90347 2.61267 9.64138 2.76023C9.3793 2.90779 9.15966 3.1204 9.00368 3.37756V3.37756Z" stroke="#E03009" strokeWidth="1.5" strokeLinecap="round" strokeLinejoin="round" />
                                                <path d="M10.5 7.875V11.375" stroke="#E03009" strokeWidth="1.5" strokeLinecap="round" strokeLinejoin="round" />
                                                <path d="M10.5 14.875H10.5088" stroke="#E03009" strokeWidth="1.5" strokeLinecap="round" strokeLinejoin="round" />
                                            </svg>
                                            {showedErrorMessage}
                                        </div>
                                    </div>
                                )}
                                <div className="text-center px-3 pt-3 pb-1 profile-title">{trans('change-profile')}</div>
                                <div className="text-center px-3 profile-description">{trans('enter-first-name-phone')}</div>
                                <div className={style['menu-profile']}>
                                    <div className={style['detail-profile']}>
                                        <ThemeProvider theme={theme}>
                                            <form onSubmit={formik.handleSubmit} method={'POST'}>
                                                <Grid container spacing={2} style={{ justifyContent: 'center' }}>
                                                    {!isFirstNameValid && (
                                                        <Grid item xs={12} sm={12}>
                                                            <div className={`${variables.label} mb-1`}>{trans('first-name')}</div>
                                                            <TextField
                                                                className={`${variables.texting} ${formik.touched.first_name && (Boolean(formik.errors.first_name) || (apiErrors && apiErrors.first_name)) ? invalid : ''}`}
                                                                fullWidth
                                                                id="first_name"
                                                                name="first_name"
                                                                placeholder={trans('first-name')}
                                                                variant="outlined"
                                                                value={formik.values.first_name}
                                                                onChange={(e) => {
                                                                    formik.handleChange(e);
                                                                    formik.setFieldTouched('first_name', e?.target?.value !== '', false);
                                                                }}
                                                                onBlur={formik.handleBlur}
                                                                error={formik.touched.first_name && Boolean(formik.errors.first_name || (apiErrors && apiErrors?.first_name))}
                                                            />
                                                        </Grid>
                                                    )}
                                                    {!isGsmValid && (
                                                        <Grid item xs={12}>
                                                            <div className={`${variables.label} mb-1`}>{trans('mobile')}</div>
                                                            <TextField
                                                                type="text"
                                                                className={`${variables.texting} ${formik.touched.gsm && (Boolean(formik.errors.gsm) || (apiErrors && apiErrors.gsm)) ? invalid : ''}`}
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
                                                    )}
                                                    {!isEmailValid && (
                                                        <Grid item xs={12}>
                                                            <div className={`${variables.label} mb-1`}>{trans('email')}</div>
                                                            <TextField
                                                                className={`${variables.texting} ${formik.touched.email && (Boolean(formik.errors.email) || (apiErrors && apiErrors.email)) ? invalid : ''}`}
                                                                fullWidth
                                                                id="email"
                                                                name="email"
                                                                placeholder={trans('email')}
                                                                variant="outlined"
                                                                value={formik.values.email}
                                                                onChange={(e) => {
                                                                    formik.handleChange(e);
                                                                    formik.setFieldTouched('email', e?.target?.value !== '', false);
                                                                }}
                                                                onBlur={formik.handleBlur}
                                                                error={formik.touched.email && Boolean(formik.errors.email || (apiErrors && apiErrors?.email))}
                                                            />
                                                        </Grid>
                                                    )}
                                                    <Grid item xs={12} className={`d-flex justify-content-center`} style={{ margin: 'auto', marginTop: '35px' }}>
                                                        <Button type="submit" disabled={isDisable} onClick={() => handleEditClick()} style={isDisable ? { background: color, width: 'fit-content', border: '0px', opacity: '0.4' } : { background: color, width: 'fit-content', border: '0px' }}
                                                            className={`${style['save-button']} ${isDisable ? `${style['btn-disable']}` : ``}`}>
                                                            <div >{trans('save')}</div>
                                                        </Button>
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
                    </>
                )
            }
        </>
    );
}
