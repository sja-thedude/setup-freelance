'use client'

import { useEffect, useState, useRef } from 'react';
import {
    Button,
    Grid,
    TextField,
  } from '@mui/material';
import 'public/assets/css/popup.scss';
import { useI18n } from '@/locales/client'
import { createTheme, ThemeProvider } from "@mui/material/styles";
import { useFormik } from 'formik';
import * as Yup from 'yup';
import Cookies from 'js-cookie';
import { api } from "@/utils/axios";
import variables from '/public/assets/css/portalRegister.module.scss'
import { InputAdornment } from '@mui/material';
import Select from '@mui/material/Select';
import MenuItem from '@mui/material/MenuItem';
import { ToastContainer, toast, Slide } from 'react-toastify';
import 'react-toastify/dist/ReactToastify.css';
import { REGEX_NUMBER_CHECK, VALIDATION_PHONE_MAX } from "@/config/constants";
import { useAppSelector } from '@/redux/hooks'
import "react-datepicker/dist/react-datepicker.css";
import { useGetWorkspaceDataByIdQuery } from '@/redux/services/workspace/workspaceDataApi';
import GoogleLogin from "react-google-login";
import FacebookLogin from "react-facebook-login/dist/facebook-login-render-props";
import AppleLogin from 'react-apple-login';
import { useRouter } from "next/navigation";
import Image from "next/image";
import { FontAwesomeIcon } from "@fortawesome/react-fontawesome";
import { faChevronLeft } from "@fortawesome/free-solid-svg-icons";
import {handleLoginToken} from "@/utils/axiosRefreshToken";
import { TERMS_CONDITIONS_LINK, PRIVACY_POLICY_LINK } from '@/config/constants';
import { ORIGIN_NEXT } from '@/config/constants';

export default function PortalRegister({ togglePopup }: any) {
    const [show, setShow] = useState(false);
    const [selectedCountry, setSelectedCountry] = useState("+32"); // Initial value for the country select
    const line = variables['line-mobile'];
    const dashLine = variables['dash-line-mobile'];
    const loginWith = variables['login-with-mobile'];
    const handleCountryChange = (event: any) => {
        let gsmValue = formik.values.gsm
        if (event.target.value == '+31') {
            if (!gsmValue || gsmValue.length === 1) {
                gsmValue = '6';
            }
        } else if (event.target.value == '+32') {
            if (!gsmValue || gsmValue.length === 1) {
                gsmValue = '4';
            }
        }

        formik.setFieldValue('gsm', gsmValue);
        setSelectedCountry(event.target.value);
    };
    
    const handleClose = () => {
        togglePopup(); // Thêm console.log ở đây
        setShow(false);
    };

    const handleShow = () => setShow(true);

    useEffect(() => {
        // const hasShownPopup = localStorage.getItem('hasShownPopup');
        const hasShownPopup = false;

        if (!hasShownPopup) {
            setShow(true);
        }
    }, []);

    // count unread notification
    const workspaceToken = useAppSelector((state) => state.workspaceData.globalWorkspaceToken)
    const workspaceId = useAppSelector((state) => state.workspaceData.globalWorkspaceId)
    const { data: apiDataToken } = useGetWorkspaceDataByIdQuery({ id: workspaceId })
    const workspaceInfo = apiDataToken?.data;
    const apiData = workspaceInfo?.setting_generals;
    const color = '#B5B35C';
    const trans = useI18n()
    const language = Cookies.get('Next-Locale');
    const message = variables['message'];
    const invalid = variables['invalid'];
    const [isVisible, setIsVisible] = useState(false);
    const [errorMessage, setErrorMessage] = useState<any | null>(null);
    const [errorMessageAPI, setErrorMessageAPI] = useState<any | null>(null);
    const [apiErrors, setApiErrors] = useState<any | null>(null);
    const [isFirstNameValid, setIsFirstNameValid] = useState(true); // Store the input value
    const [isEmailValid, setIsEmailValid] = useState(true); // Store the input value
    const [isLastNameValid, setIsLastNameValid] = useState(true); // Store the input value
    const [isGsmValid, setIsGsmValid] = useState(true); // Store the input value
    const [isPasswordValid, setIsPasswordValid] = useState(true); // Store the input value
    const [isPasswordConfirmationValid, setIsPasswordConfirmationValid] = useState(true); // Store the input value
    const [currentPassword, setCurrentPassword] = useState(''); // Store the input value
    const [currentPasswordConfirmation, setCurrentPasswordConfirmation] = useState(''); // Store the input value
    const validationSchema = Yup.object().shape({
        first_name: Yup.string().required(trans('required')),
        last_name: Yup.string().required(trans('required')),
        gsm: Yup.string().required(trans('required')),
        email: Yup.string().required(trans('required')).email('Ongeldig e-mailadres'),
    });
    const theme = createTheme({
        components: {
            // Inputs
            MuiOutlinedInput: {
                styleOverrides: {
                    root: {
                        '&:focus': {
                            border: 'none',
                            outline: 'none',
                        },
                        "& MuiTextField": {
                            border: '1px solid var(--Cart-stroke, #D1D1D1)'
                        },
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
                            border: 0
                        },
                        "&.Mui-focused": {
                            "& .MuiOutlinedInput-notchedOutline": {
                                color: 'red'
                            }
                        },
                        "& .MuiOutlinedInput-root": {
                            backgroundColor: '#e6e6e6',
                            position: 'absolute',
                            left: '0',
                            top: '-1px',
                            height: '50px',
                            zIndex: '100',
                            borderTopRightRadius: '0', // Đặt bán kính cho góc trên bên phải
                            borderBottomRightRadius: '0', // Đặt bán kính cho góc dưới bên phải
                            padding: '12.5px 0px',
                            paddingBottom: '10px'
                        },
                        "& .MuiOutlinedInput-input": {
                            padding: '13.5px 10px 12px',
                        },
                        "& #gsm": {
                            marginLeft: '70px',
                        },
                        "& #first_name": {
                            marginLeft: '3px',
                        },
                        "& #last_name": {
                            marginLeft: '3px',
                        },
                        "& #postcode": {
                            marginLeft: '18px',
                            marginTop: '3px',
                        },
                    },
                }
            }
        }
    });
    function checkEmailValid(email: string) {
        // Sử dụng regex để kiểm tra định dạng email
        const emailRegex = /^[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,}$/i;
        return emailRegex.test(email);
    }
    const handleSubmitClick = () => {
        if (formik.values.gsm.length < 10) {
            setIsGsmValid(false);
            setErrorMessage(trans('format-gsm'));
        }

        if (formik.values.password.length < 6 && formik.values.password.length > 0) {
            setErrorMessage(trans('password-min-length'));
            setIsPasswordValid(false);
        }

        if (formik.values.password_confirmation.length < 6 && formik.values.password_confirmation.length > 0) {
            setErrorMessage(trans('password-min-length'));
            setIsPasswordConfirmationValid(false);
        }

        if (formik.values.password !== formik.values.password_confirmation) {
            setErrorMessage(trans('password-not-match'));
            setIsPasswordValid(false);
            setIsPasswordConfirmationValid(false);
        }

        if (formik.values.first_name === '' || formik.values.last_name === '' || formik.values.gsm === '' || formik.values.email === '' || formik.values.password === '' || formik.values.password_confirmation === '') {
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
            if (formik.values.password === '') {
                setIsPasswordValid(false);
            }
            if (formik.values.password_confirmation === '') {
                setIsPasswordConfirmationValid(false);
            }
            setErrorMessage(trans('missing-fields'));
        } else {
            if (checkEmailValid(formik.values.email)) {
                setIsEmailValid(true);
            } else {
                setIsEmailValid(false);
                if (formik.values.email) {
                    setErrorMessage(trans('email-fail'));
                }
            }
            setIsFirstNameValid(true);
            setIsLastNameValid(true);
            if (formik.values.gsm.length >= 9) {
                setIsGsmValid(true);
            }
        }

        if (isFirstIconVisible) {
            setIsButtonClicked(true);
            setErrorMessage(trans('accpet-terms'));
        }

        if (errorMessage) {
            toast.dismiss();
            // Hiển thị toast
            toast(errorMessage, {
                position: toast.POSITION.BOTTOM_CENTER,
                autoClose: 1500,
                hideProgressBar: true,
                closeOnClick: true,
                closeButton: false,
                transition: Slide,
                className: 'messages',
            });
        }
    }
    const handleGsmChange = (event: any) => {
        let newValue = event.target.value;
        formik.handleChange(event);
        // Remove characters that are not numbers
        const sanitizedValue = newValue.replace(/\D/g, '');
        newValue = sanitizedValue;

        // Check if the value starts with "0" and remove it
        if (newValue.startsWith('0')) {
            newValue = newValue.substring(1);
        }

        if (parseInt(selectedCountry + newValue) >= VALIDATION_PHONE_MAX) {
            // Limit the phone number length
            newValue = newValue.substring(0, (VALIDATION_PHONE_MAX - selectedCountry.length));
        }

        // Update the value in the formik state
        formik.setFieldValue('gsm', newValue);
    };
    const formik = useFormik({
        initialValues: {
            first_name: '',
            last_name: '',
            gsm: '4',
            email: '',
            company: '',
            city: '',
            message: '',
            postcode: '',
            password: '',
            password_confirmation: '',
        },
        validationSchema,
        onSubmit: async (values) => {
            // Only subit when the term and condition is clicked
            if (isEmailValid && isFirstNameValid && isLastNameValid && isGsmValid && !isFirstIconVisible && values.password.length >= 6 && values.password_confirmation.length >= 6 && values.password === values.password_confirmation) {
                toast.dismiss();
                try {
                    const headers = {
                        'Content-Language': language,
                    };
                    const response = await api.post('register', {
                        email: values.email,
                        first_name: values.first_name,
                        gsm: selectedCountry + values.gsm,
                        last_name: values.last_name,
                        password: values.password,
                        password_confirmation: values.password_confirmation,
                        origin: ORIGIN_NEXT
                        // add more data if needed
                    }, { headers });
                    if ('data' in response) {
                        router.push("/user/register/portal/ready");
                    }
                } catch (error: any) {
                    setApiErrors(error.response.data.data);
                    const errors = Object.values(error.response.data.data);
                    const lastErrorMessage: any = errors[errors.length - 1];
                    setErrorMessage(lastErrorMessage[0]);
                }
            }
        },
    });
    const [open, setOpen] = useState(false);
    const [selectedDate, setSelectedDate] = useState(new Date('2011-01-10'));
    const [openDatePicker, setOpenDatePicker] = useState(false);
    const handleSelectedDate = (date: any) => {
        const today = new Date();
        setSelectedDate(date);
    }
    const fakeData = [
        { postalCode: '3600', city: 'Genk' },
        { postalCode: '3600', city: 'VuxCao' },
        { postalCode: '3600', city: 'Okela' },
        { postalCode: '3600', city: 'Keke' },
        { postalCode: '3500', city: 'Hasselt' },
        { postalCode: '3960', city: 'Bree' },
        { postalCode: '3660', city: 'Oudsbergen', suburb: 'Opglabbeek' },
        { postalCode: '3670', city: 'Oudsbergen', suburb: 'Meeuwen-gruitrode' }
    ];
    const [currentPostCodeMatch, setCurrentPostCodeMatch] = useState<any>(null);
    const inputRef = useRef<any>(null);
    const [listAddress, setListAddress] = useState<any>([]);
    const [isShow, setIsShow] = useState(true);
    const [inputValue, setInputValue] = useState('');
    const [currentInput, setCurrentInput] = useState('');
    const [rootType, setRootType] = useState(1);
    const [step, setStep] = useState(1);
    const router = useRouter();
    const handleSearch = () => {
        setCurrentInput(formik.values.postcode);
        const postalCodeItems = fakeData.filter(item => item.postalCode == formik.values.postcode);
        setIsShow(true);
        setListAddress(postalCodeItems);
    }

    const handleItemClick = (item: any) => {
        setIsShow(false);
        setCurrentPostCodeMatch(item);
        formik.setFieldValue('postcode', `${item.postalCode} , ${item.city}${item.suburb ? ',' + item.suburb : ''}`);
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

    const onClickChangeType = (type: number) => {
        setRootType(type)
    }

    const onSuccess = (response: any) => {
        responseLoginToken(response, 'google');
    };

    const responseFacebook = (response: any) => {
        responseLoginToken(response, 'facebook');
    };

    const responseApple = (response: any) => {
        responseLoginToken({
            accessToken: response?.authorization?.id_token
        }, 'apple');
    };

    const responseLoginToken = (response: any, provider: string) => {
        api.post(`login/social`, {
            'provider': provider,
            'access_token': response?.accessToken,
            'workspace_id': workspaceId,
        }, {
            headers: {
                'Content-Language': language
            }
        }).then(res => {
            const userData = res.data.data;

            // Set cookie 'loggedToken' with value 'token'
            handleLoginToken(userData.token);

            if (userData?.first_login && (userData.first_name.includes('@') || REGEX_NUMBER_CHECK.test(userData.first_name) || !userData.gsm)) {
                history.pushState({}, "show profile", "/profile/show");
                window.location.href = '/profile/edit';
            } else {
                const query = new URLSearchParams(window.location.search);
                if (query.get('account') === 'true') {
                    router.push("/profile/show");
                } else if (query.get('recent')) {
                    router.push("/function/recent");
                } else if (query.get('favorites')) {
                    router.push("/category/products?liked=true");
                } else if (query.get('product_suggestion')) {
                    router.push("/table-ordering/cart?open=true");
                } else if (query.get('group-order')) {
                    Cookies.set('groupOrder', 'true');
                    router.back();
                } else if (query.get('loyalties') === 'true') {
                    router.push("/loyalties");
                } else if (query.get('categoryCart') === 'true') {
                    router.push("/category/cart?openSuggest=true");
                }
                else {
                    router.push("/");
                }
            }
        }).catch(err => {
            // console.log(err);
        });
    }
    const onFailure = (response: any) => {
        // console.log('FAILED', response);
    };
    const handleInputChange = (event: any) => {
        let value = event.target.value;
        setInputValue(value);
        formik.handleChange(event);
    };
    // eye on password
    const [showPassword, setShowPassword] = useState(false);

    const handleClickShowPassword = () => {
        setShowPassword(!showPassword);
    };

    // eye on password
    const [showRepeatPassword, setShowRepeatPassword] = useState(false);
    const handleClickShowRepeatPassword = () => {
        setShowRepeatPassword(!showRepeatPassword);
    };
    const [isFirstIconVisible, setIsFirstIconVisible] = useState(true);
    const [isButtonClicked, setIsButtonClicked] = useState(false);
    const toggleIcon = () => {
        setIsFirstIconVisible(!isFirstIconVisible);
    };
    const handlePass = () => {
        setCurrentPassword(formik.values.password)
        setIsPasswordValid(true)
    }
    const handlePassConfirm = () => {
        setIsPasswordConfirmationValid(true)
        setCurrentPasswordConfirmation(formik.values.password_confirmation)
    }
    useEffect(() => {
        if (step == 0) {
            handleClose()
            // then open login popup
        }
    }, [step])

    useEffect(() => {
        toast.dismiss();
        // Hiển thị toast
        toast(errorMessage, {
            position: toast.POSITION.BOTTOM_CENTER,
            autoClose: 1500,
            hideProgressBar: true,
            closeOnClick: true,
            closeButton: false,
            transition: Slide,
            className: 'messages',
        });
    }, [errorMessage]);

    return (
        <>
            <div className='container' style={{ backgroundColor: "#B5B268", minHeight: '100vh', paddingTop: '24px' }}>
                <div className='header-contain d-flex justify-content-center col-12 mt-3'>
                    <div className='ms-2'> <FontAwesomeIcon
                        icon={faChevronLeft}
                        onClick={() => router.back()}
                        style={{ color: 'white', cursor: 'pointer', pointerEvents: 'auto', width: '20px', height: '20px' }}
                    /></div>
                    <div className='col-sm-10 col-xs-10' style={{ margin: "auto" }}> <h1 className={`${variables.register}`}>{trans('register-account')}</h1></div>
                </div>
                <div className={`${variables.title} col-10 mt-2`}><p>{trans('create-account')}</p></div>
                <ThemeProvider theme={theme}>
                    <form onSubmit={formik.handleSubmit} onClick={() => { setIsShow(false) }}>
                        <Grid container spacing={2} style={{ justifyContent: 'center' }}>

                            <Grid item xs={10}>
                                <TextField
                                    // formik.touched.first_name && (Boolean(formik.errors.first_name) ||
                                    className={`${variables.textingPortal} ${!isFirstNameValid || (apiErrors && apiErrors.first_name) ? invalid : ''}`}
                                    fullWidth
                                    id="first_name"
                                    name="first_name"
                                    label={formik.values.first_name ? '' : trans('first-name')}
                                    variant="outlined"
                                    value={formik.values.first_name}
                                    onChange={formik.handleChange}
                                    onBlur={formik.handleBlur}
                                    error={formik.touched.first_name && Boolean(formik.errors.first_name || (apiErrors && apiErrors?.first_name))}
                                    helperText={""}
                                    onKeyUp={() => { setIsFirstNameValid(true) }}

                                    InputLabelProps={{
                                        shrink: false, // Đặt shrink thành false chỉ khi giá trị không rỗng
                                        style: {
                                            fontFamily: 'SF Compact Display',
                                            fontSize: "16px",
                                            fontStyle: 'normal',
                                            fontWeight: ' 400',
                                            lineHeight: '20px',
                                            letterSpacing: '-0.24px',
                                            color: !isFirstNameValid || (apiErrors && apiErrors.first_name) ? '#D94B2C' : '#949494',

                                        }
                                    }}
                                    InputProps={{
                                        style: { color: !isFirstNameValid || (apiErrors && apiErrors.isFirstNameValid) ? '#D94B2C' : '#413E38' },
                                    }}
                                />
                            </Grid>
                            <Grid item xs={10}>
                                <TextField
                                    className={`${variables.textingPortal} ${!isLastNameValid || (apiErrors && apiErrors.last_name) ? invalid : ''}`}
                                    fullWidth
                                    id="last_name"
                                    name="last_name"
                                    style={{ backgroundColor: '#FFFFFF' }}
                                    label={formik.values.last_name ? '' : trans('last-name')}
                                    variant="outlined"
                                    value={formik.values.last_name}
                                    onChange={formik.handleChange}
                                    onBlur={formik.handleBlur}
                                    error={formik.touched.last_name && Boolean(formik.errors.last_name || (apiErrors && apiErrors.last_name))}
                                    helperText={""}
                                    onKeyUp={() => { setIsLastNameValid(true) }}
                                    InputLabelProps={{
                                        shrink: false, // Đặt shrink thành false chỉ khi giá trị không rỗng
                                        style: {
                                            fontFamily: 'SF Compact Display',
                                            fontSize: "16px",
                                            fontStyle: 'normal',
                                            fontWeight: ' 400',
                                            lineHeight: '20px',
                                            letterSpacing: '-0.24px',
                                            color: !isLastNameValid || (apiErrors && apiErrors.last_name) ? '#D94B2C' : '#949494',

                                        }
                                    }}
                                    InputProps={{
                                        style: { color: !isLastNameValid || (apiErrors && apiErrors.isLastNameValid) ? '#D94B2C' : '#413E38' },
                                    }}
                                />
                            </Grid>
                            <Grid item xs={10}>
                                <TextField
                                    type="text"
                                    className={`${variables.textingPortal} ${!isGsmValid || (apiErrors && apiErrors.gsm) ? invalid : ''}`}
                                    fullWidth
                                    id="gsm"
                                    name="gsm"
                                    label={formik.values.gsm ? '' : trans('mobile')}
                                    variant="outlined"
                                    value={formik.values.gsm}
                                    onChange={handleGsmChange}
                                    onBlur={formik.handleBlur}
                                    error={formik.touched.gsm && Boolean(formik.errors.gsm || (apiErrors && apiErrors.gsm))}
                                    helperText={""}
                                    onKeyUp={() => { setIsGsmValid(true) }}
                                    InputLabelProps={{
                                        shrink: false, // Đặt shrink thành false chỉ khi giá trị không rỗng
                                        style: {
                                            transform: formik.values.gsm !== "" ? '' : 'translate(64%, 70%) scale(1)', // Adjust label position
                                            display: formik.values.gsm !== "" ? 'none' : 'block', // Hide label when value is empty
                                            fontFamily: 'SF Compact Display',
                                            fontSize: "16px",
                                            fontStyle: 'normal',
                                            fontWeight: ' 400',
                                            lineHeight: '20px',
                                            letterSpacing: '-0.24px',
                                            color: !isGsmValid || (apiErrors && apiErrors.gsm) ? '#D94B2C' : '#949494'
                                        }
                                    }}
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
                                                    value={selectedCountry}
                                                    onChange={handleCountryChange}
                                                >
                                                    <MenuItem className={variables.customMenuItem} value="+32">
                                                        <div className='d-flex'>
                                                            <Image src="/img/belgium-flag-icon.svg" alt="" width={25} height={25} />
                                                            <div className={`${variables.country}`}>+32</div></div>
                                                    </MenuItem>
                                                    <MenuItem className={variables.customMenuItem} value="+31">
                                                        <div className='d-flex'>
                                                            <Image src="/img/netherlands-flag-icon.svg" alt="" width={25} height={25} />
                                                            <div className={`${variables.country}`}>+31</div></div>
                                                    </MenuItem>
                                                </Select>

                                            </InputAdornment>
                                        ),
                                    }}
                                />
                            </Grid>
                            <Grid item xs={10} style={{ padding: '0', paddingLeft: '16px' }}>
                                <div className={`${variables.vbm} ms-2`}>
                                    {trans('vb')}
                                </div>
                            </Grid>
                            <Grid item xs={10}>
                                <TextField
                                    className={`${variables.textingPortal} ${!isEmailValid || (apiErrors && apiErrors.email) ? invalid : ''}`}
                                    fullWidth
                                    id="email"
                                    name="email"
                                    label={formik.values.email ? '' : ' E-mailadress'}
                                    variant="outlined"
                                    value={formik.values.email}
                                    onChange={handleInputChange}
                                    onBlur={formik.handleBlur}
                                    error={formik.touched.email && Boolean(formik.errors.email || (apiErrors && apiErrors.email))}
                                    helperText={""}
                                    onKeyUp={() => { setIsEmailValid(true) }}
                                    InputLabelProps={{
                                        shrink: false, // Đặt shrink thành false chỉ khi giá trị không rỗng
                                        style: {
                                            fontFamily: 'SF Compact Display',
                                            fontSize: "16px",
                                            fontStyle: 'normal',
                                            fontWeight: ' 400',
                                            lineHeight: '20px',
                                            letterSpacing: '-0.24px',
                                            color: !isEmailValid || (apiErrors && apiErrors.email) ? '#D94B2C' : '#949494'
                                        }
                                    }}
                                    inputProps={{ style: { color: !isEmailValid || (apiErrors && apiErrors.email) ? '#D94B2C' : '#413E38' } }}
                                />
                            </Grid>
                            <Grid item xs={10}>
                                <TextField
                                    className={`${variables.textingPortal} ${!isPasswordValid || (apiErrors && apiErrors.password) ? invalid : ''}`}
                                    fullWidth
                                    id="password"
                                    name="password"
                                    style={{ backgroundColor: '#FFFFFF' }}
                                    type={showPassword ? "text" : "password"}
                                    label={formik.values.password ? '' : trans('password')}
                                    variant="outlined"
                                    value={formik.values.password}
                                    onChange={formik.handleChange}
                                    onBlur={formik.handleBlur}
                                    error={formik.touched.password && Boolean(formik.errors.password || (apiErrors && apiErrors.password))}
                                    onKeyUp={handlePass}
                                    helperText={""}
                                    InputLabelProps={{
                                        shrink: false, // Đặt shrink thành false chỉ khi giá trị không rỗng
                                        style: {
                                            fontFamily: 'SF Compact Display',
                                            fontSize: "16px",
                                            fontStyle: 'normal',
                                            fontWeight: ' 400',
                                            lineHeight: '20px',
                                            letterSpacing: '-0.24px',
                                            color: !isPasswordValid || (apiErrors && apiErrors.password) ? '#D94B2C' : '#949494'
                                        }
                                    }}
                                    InputProps={{
                                        style: { color: !isPasswordValid || (apiErrors && apiErrors.isPasswordValid) ? '#D94B2C' : '#413E38' },
                                        endAdornment: (
                                            <InputAdornment position="end" onClick={handleClickShowPassword}>
                                                {currentPassword && (
                                                    <>
                                                        {showPassword
                                                            ? (
                                                                <svg width="24" height="25" viewBox="0 0 24 25" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                    <path d="M1 12.4999C1 12.4999 5 4.83325 12 4.83325C19 4.83325 23 12.4999 23 12.4999C23 12.4999 19 20.1666 12 20.1666C5 20.1666 1 12.4999 1 12.4999Z" stroke="#BDBDBD" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" />
                                                                    <path d="M12 15.375C13.6569 15.375 15 14.0878 15 12.5C15 10.9122 13.6569 9.625 12 9.625C10.3431 9.625 9 10.9122 9 12.5C9 14.0878 10.3431 15.375 12 15.375Z" stroke="#BDBDBD" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" />
                                                                    <line x1="5.378" y1="1.318" x2="19.318" y2="23.622" stroke="#BDBDBD" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" />
                                                                </svg>
                                                            )
                                                            : (
                                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                                                    <path d="M1 12C1 12 5 4 12 4C19 4 23 12 23 12C23 12 19 20 12 20C5 20 1 12 1 12Z" stroke="black" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" />
                                                                    <path d="M12 15C13.6569 15 15 13.6569 15 12C15 10.3431 13.6569 9 12 9C10.3431 9 9 10.3431 9 12C9 13.6569 10.3431 15 12 15Z" stroke="black" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" />
                                                                </svg>
                                                            )
                                                        }
                                                    </>
                                                )}

                                            </InputAdornment>
                                        )
                                    }}
                                />
                            </Grid>
                            <Grid item xs={10}>
                                <TextField
                                    className={`${variables.textingPortal} ${!isPasswordConfirmationValid || (apiErrors && apiErrors.password_confirmation) ? invalid : ''}`}
                                    fullWidth
                                    id="password_confirmation"
                                    name="password_confirmation"
                                    style={{ backgroundColor: '#FFFFFF' }}
                                    type={showRepeatPassword ? "text" : "password"}
                                    label={formik.values.password_confirmation ? '' : trans('password-confirm')}
                                    variant="outlined"
                                    value={formik.values.password_confirmation}
                                    onChange={formik.handleChange}
                                    onBlur={formik.handleBlur}
                                    error={formik.touched.password_confirmation && Boolean(formik.errors.password_confirmation || (apiErrors && apiErrors.password_confirmation))}
                                    helperText={""}
                                    onKeyUp={handlePassConfirm}
                                    InputLabelProps={{
                                        shrink: false, // Đặt shrink thành false chỉ khi giá trị không rỗng
                                        style: {
                                            fontFamily: 'SF Compact Display',
                                            fontSize: "16px",
                                            fontStyle: 'normal',
                                            fontWeight: ' 400',
                                            lineHeight: '20px',
                                            letterSpacing: '-0.24px',
                                            color: !isPasswordConfirmationValid || (apiErrors && apiErrors.password_confirmation) ? '#D94B2C' : '#949494'
                                        }
                                    }}
                                    InputProps={{
                                        style: { color: !isPasswordConfirmationValid || (apiErrors && apiErrors.isPasswordConfirmationValid) ? '#D94B2C' : '#413E38' },
                                        endAdornment: (
                                            <InputAdornment position="end" onClick={handleClickShowRepeatPassword}>
                                                {currentPasswordConfirmation && (
                                                    <>
                                                        {showRepeatPassword
                                                            ? (
                                                                <svg width="24" height="25" viewBox="0 0 24 25" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                    <path d="M1 12.4999C1 12.4999 5 4.83325 12 4.83325C19 4.83325 23 12.4999 23 12.4999C23 12.4999 19 20.1666 12 20.1666C5 20.1666 1 12.4999 1 12.4999Z" stroke="#BDBDBD" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" />
                                                                    <path d="M12 15.375C13.6569 15.375 15 14.0878 15 12.5C15 10.9122 13.6569 9.625 12 9.625C10.3431 9.625 9 10.9122 9 12.5C9 14.0878 10.3431 15.375 12 15.375Z" stroke="#BDBDBD" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" />
                                                                    <line x1="5.378" y1="1.318" x2="19.318" y2="23.622" stroke="#BDBDBD" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" />
                                                                </svg>
                                                            )
                                                            : (
                                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                                                    <path d="M1 12C1 12 5 4 12 4C19 4 23 12 23 12C23 12 19 20 12 20C5 20 1 12 1 12Z" stroke="black" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" />
                                                                    <path d="M12 15C13.6569 15 15 13.6569 15 12C15 10.3431 13.6569 9 12 9C10.3431 9 9 10.3431 9 12C9 13.6569 10.3431 15 12 15Z" stroke="black" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" />
                                                                </svg>
                                                            )
                                                        }
                                                    </>
                                                )}
                                            </InputAdornment>
                                        )
                                    }}
                                />
                            </Grid>
                            <Grid item xs={10} className='d-flex'>
                                <div className="me-2" onClick={toggleIcon}>
                                    {isFirstIconVisible ? (
                                        <svg xmlns="http://www.w3.org/2000/svg" width="17" height="17" viewBox="0 0 17 17" fill='none' className={variables.teamIcon}
                                        >
                                            <path d="M16 8.5V14.3333C16 14.7754 15.8244 15.1993 15.5118 15.5118C15.1993 15.8244 14.7754 16 14.3333 16H2.66667C2.22464 16 1.80072 15.8244 1.48816 15.5118C1.17559 15.1993 1 14.7754 1 14.3333V2.66667C1 2.22464 1.17559 1.80072 1.48816 1.48816C1.80072 1.17559 2.22464 1 2.66667 1H11.8333" stroke={isButtonClicked ? "red" : "#FFF"} strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" />
                                        </svg>
                                    ) : (
                                        <svg width="17" height="17" viewBox="0 0 17 17" fill="none" xmlns="http://www.w3.org/2000/svg"> className={variables.teamIcon}
                                            <path d="M16 8.5V14.3333C16 14.7754 15.8244 15.1993 15.5118 15.5118C15.1993 15.8244 14.7754 16 14.3333 16H2.66667C2.22464 16 1.80072 15.8244 1.48816 15.5118C1.17559 15.1993 1 14.7754 1 14.3333V2.66667C1 2.22464 1.17559 1.80072 1.48816 1.48816C1.80072 1.17559 2.22464 1 2.66667 1H11.8333" stroke="#FFF" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" />
                                            <path d="M5 7.83333L7.5 10.3333L15.8333 2" stroke="#FFF" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" />
                                        </svg>
                                    )}
                                </div>
                                <div className='me-2'> <p className={`${variables.termo}`} style={{margin: '0'}}>
                                    {trans('agree')} <span className={variables.underlinem} onClick={() => { window.open(TERMS_CONDITIONS_LINK, "_blank") }}>{trans('term-condition')}</span> {trans('en')} <span className={variables.underlinem} onClick={() => { window.open(PRIVACY_POLICY_LINK, "_blank") }}>{trans('privacy-portal')}</span>.
                                </p></div>

                            </Grid>

                            <Grid item xs={12} className={`${variables.regis} d-flex justify-content-center mt-2`} style={{ margin: 'auto', paddingTop: '22px' }}>
                                <Button variant="contained" type='submit' className={`${variables.regisButton}`} onClick={handleSubmitClick}>
                                    <div className={`${variables.regisButtonText}`}>{trans('register-btn')}</div>
                                </Button>
                            </Grid>

                            <Grid item xs={12} className={`${variables.regis}`} style={{ margin: 'auto', paddingTop: '10px', marginBottom: '-10px' }}>
                                <div className={`${line} text-center mt-2`}>
                                    <span className={`${dashLine}`}></span>&nbsp;&nbsp;
                                    {trans('of')}
                                    &nbsp;&nbsp;<span className={`${dashLine}`}></span>
                                </div>
                                <div className={`${loginWith} text-center`}>{trans('register-with')}</div>
                            </Grid>
                            <Grid item xs={9} className={`${variables.regis} d-flex justify-content-between mb-5`} style={{ margin: 'auto', paddingTop: '0px' }}>
                                <GoogleLogin
                                    clientId={process.env.NEXT_PUBLIC_GOOGLE_CLIENT_ID ?? ''}
                                    render={renderProps => (
                                        <div onClick={renderProps.onClick} className={`${variables.socialing} col-sm-3 col-3 d-grid justify-content-center`} style={{ backgroundColor: 'white', borderRadius: '50%', width: '50px', height: '50px' }}>
                                            <svg xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" width="50" height="50" viewBox="0 0 48 48" style={{ margin: 'auto' }}>
                                                <path fill="#FFC107" d="M43.611,20.083H42V20H24v8h11.303c-1.649,4.657-6.08,8-11.303,8c-6.627,0-12-5.373-12-12c0-6.627,5.373-12,12-12c3.059,0,5.842,1.154,7.961,3.039l5.657-5.657C34.046,6.053,29.268,4,24,4C12.955,4,4,12.955,4,24c0,11.045,8.955,20,20,20c11.045,0,20-8.955,20-20C44,22.659,43.862,21.35,43.611,20.083z"></path><path fill="#FF3D00" d="M6.306,14.691l6.571,4.819C14.655,15.108,18.961,12,24,12c3.059,0,5.842,1.154,7.961,3.039l5.657-5.657C34.046,6.053,29.268,4,24,4C16.318,4,9.656,8.337,6.306,14.691z"></path><path fill="#4CAF50" d="M24,44c5.166,0,9.86-1.977,13.409-5.192l-6.19-5.238C29.211,35.091,26.715,36,24,36c-5.202,0-9.619-3.317-11.283-7.946l-6.522,5.025C9.505,39.556,16.227,44,24,44z"></path><path fill="#1976D2" d="M43.611,20.083H42V20H24v8h11.303c-0.792,2.237-2.231,4.166-4.087,5.571c0.001-0.001,0.002-0.001,0.003-0.002l6.19,5.238C36.971,39.205,44,34,44,24C44,22.659,43.862,21.35,43.611,20.083z"></path>
                                            </svg>
                                        </div>
                                    )}
                                    buttonText="Login"
                                    onSuccess={onSuccess}
                                    onFailure={onFailure}
                                    cookiePolicy={'single_host_origin'}
                                />
                                <FacebookLogin
                                    appId={process.env.NEXT_PUBLIC_FACEBOOK_APP_ID}
                                    callback={responseFacebook}
                                    isMobile={false}
                                    render={(renderProps: any) => (
                                        <div onClick={renderProps.onClick} className={`${variables.socialing} col-sm-3 col-3 d-grid justify-content-center`} style={{ backgroundColor: 'white', borderRadius: '50%', width: '50px', height: '50px' }}>
                                            <svg xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" width="50" height="50" viewBox="0 0 48 48" style={{ margin: 'auto' }}>
                                                <linearGradient id="Ld6sqrtcxMyckEl6xeDdMa_uLWV5A9vXIPu_gr1" x1="9.993" x2="40.615" y1="9.993" y2="40.615" gradientUnits="userSpaceOnUse"><stop offset="0" stopColor="#2aa4f4"></stop><stop offset="1" stopColor="#007ad9"></stop></linearGradient><path fill="url(#Ld6sqrtcxMyckEl6xeDdMa_uLWV5A9vXIPu_gr1)" d="M24,4C12.954,4,4,12.954,4,24s8.954,20,20,20s20-8.954,20-20S35.046,4,24,4z"></path><path fill="#fff" d="M26.707,29.301h5.176l0.813-5.258h-5.989v-2.874c0-2.184,0.714-4.121,2.757-4.121h3.283V12.46 c-0.577-0.078-1.797-0.248-4.102-0.248c-4.814,0-7.636,2.542-7.636,8.334v3.498H16.06v5.258h4.948v14.452 C21.988,43.9,22.981,44,24,44c0.921,0,1.82-0.084,2.707-0.204V29.301z"></path>
                                            </svg>
                                        </div>
                                    )}
                                />
                                <AppleLogin
                                    clientId={process.env.NEXT_PUBLIC_APPLE_CLIENT_ID ?? ''}
                                    redirectURI={window.location.origin}
                                    responseType="id_token code"
                                    responseMode="fragment"
                                    usePopup={true}
                                    callback={responseApple}
                                    // scope="name email"
                                    render={(renderProps: any) => (
                                        <div onClick={renderProps.onClick} className={`${variables.socialing} col-sm-3 col-3 d-grid justify-content-center`} style={{ backgroundColor: 'white', borderRadius: '50%', width: '50px', height: '50px' }}>
                                            <svg style={{ margin: "auto" }} xmlns="http://www.w3.org/2000/svg" width="50" height="41" viewBox="0 0 33 41" fill="none">
                                                <g clipPath="url(#clip0_5618_3513)">
                                                    <path d="M31.524 13.8801C31.292 14.0601 27.196 16.3681 27.196 21.5001C27.196 27.4361 32.408 29.5361 32.564 29.5881C32.54 29.7161 31.736 32.4641 29.816 35.2641C28.104 37.7281 26.316 40.1881 23.596 40.1881C20.876 40.1881 20.176 38.6081 17.036 38.6081C13.976 38.6081 12.888 40.2401 10.4 40.2401C7.912 40.2401 6.176 37.9601 4.18 35.1601C1.868 31.8721 0 26.7641 0 21.9161C0 14.1401 5.056 10.0161 10.032 10.0161C12.676 10.0161 14.88 11.7521 16.54 11.7521C18.12 11.7521 20.584 9.91214 23.592 9.91214C24.732 9.91214 28.828 10.0161 31.524 13.8801ZM22.164 6.62014C23.408 5.14414 24.288 3.09614 24.288 1.04814C24.288 0.764141 24.264 0.476141 24.212 0.244141C22.188 0.320141 19.78 1.59214 18.328 3.27614C17.188 4.57214 16.124 6.62014 16.124 8.69614C16.124 9.00814 16.176 9.32014 16.2 9.42014C16.328 9.44414 16.536 9.47214 16.744 9.47214C18.56 9.47214 20.844 8.25614 22.164 6.62014Z" fill="black" />
                                                </g>
                                                <defs>
                                                    <clipPath id="clip0_5618_3513">
                                                        <rect width="32.56" height="40" fill="white" transform="translate(0 0.244141)" />
                                                    </clipPath>
                                                </defs>
                                            </svg>
                                        </div>
                                    )}
                                />
                            </Grid>
                        </Grid>
                    </form>
                    <ToastContainer />
                </ThemeProvider>

                <style>{`
                .cart-type-item {
                    border: 1px solid ${color};
                    color: ${color};
                }
                .active {
                    color: #FFFFFF;
                    background: ${color}!important;
                  }
                .react-datepicker__day--selected {
                    background: ${color}!important;
                }
                .react-datepicker__day:hover {
                    background: ${color}!important;
                    border-radius: 50%;
                    color: #FFFFFF !important;
                }
                .too:hover {
                    cursor: pointer;
                }
                .react-datepicker-popper{
                    z-index: 3000 !important;
                }
                `}
                </style>
            </div>
        </>
    );
}
