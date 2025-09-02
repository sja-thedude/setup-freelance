'use client'
import React, { useState, useEffect } from 'react';
import { useFormik } from 'formik';
import * as Yup from 'yup';
import {
  Button,
  Grid,
  TextField,
} from '@mui/material';
import { FontAwesomeIcon } from "@fortawesome/react-fontawesome";
import { faChevronLeft } from "@fortawesome/free-solid-svg-icons";
import { useRouter } from "next/navigation";
import { useI18n } from '@/locales/client';
import variables from '/public/assets/css/register.module.scss'
import { createTheme, ThemeProvider } from "@mui/material/styles";
import { InputAdornment } from '@mui/material';
import Select from '@mui/material/Select';
import MenuItem from '@mui/material/MenuItem';
import IconButton from "@mui/material/IconButton";
import { api } from "@/utils/axios";
import Cookies from 'js-cookie';
import GoogleLogin from "react-google-login";
import FacebookLogin from "react-facebook-login/dist/facebook-login-render-props";
import { gapi } from "gapi-script";
import { ToastContainer, toast, Slide } from 'react-toastify';
import AppleLogin from 'react-apple-login';
import 'react-toastify/dist/ReactToastify.css';
import { REGEX_NUMBER_CHECK, VALIDATION_PHONE_MAX } from "@/config/constants";
import Image from "next/image";
import { useAppSelector } from '@/redux/hooks'
import { useGetWorkspaceDataByIdQuery } from '@/redux/services/workspace/workspaceDataApi';
import { handleLoginToken } from "@/utils/axiosRefreshToken";
import { TERMS_CONDITIONS_LINK, PRIVACY_POLICY_LINK } from '@/config/constants';
import { ORIGIN_NEXT } from '@/config/constants';

const Register = () => {
  const message = variables['message'];
  const invalid = variables['invalid'];

  // Trong functional component
  const workspaceId = useAppSelector((state) => state.workspaceData.globalWorkspaceId)
  const { data: apiDataToken } = useGetWorkspaceDataByIdQuery({ id: workspaceId })
  const apiData = apiDataToken?.data?.setting_generals;
  // Tạo theme của bạn
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
              border: `0`
            },
            "&.Mui-focused": {
              "& .MuiOutlinedInput-notchedOutline": {
                border: `0`,
                color: 'red'
              }
            },
            "& .MuiOutlinedInput-root": {
              backgroundColor: '#e6e6e6',
              position: 'absolute',
              left: '0',
              bottom: '-2px',
              height: '50px',
              zIndex: '100',
              borderTopRightRadius: '0', // Đặt bán kính cho góc trên bên phải
              borderBottomRightRadius: '0', // Đặt bán kính cho góc dưới bên phải
              padding: '12.5px 0px',
            },
            "& .MuiOutlinedInput-input": {
              padding: '12.5px 10px 12px',
            },
            "& #gsm": {
              marginLeft: '70px',
            }
          },
        }
      }
    }
  });
  const line = variables['line'];
  const dashLine = variables['dash-line'];
  const loginWith = variables['login-with'];

  const router = useRouter();
  const trans = useI18n();
  const language = Cookies.get('Next-Locale');
  const color = useAppSelector((state) => state.workspaceData.globalWorkspaceColor)
  const [minGsm, setMinGsm] = useState(9);

  const validationSchema = Yup.object().shape({
    first_name: Yup.string().required(trans('required')),
    last_name: Yup.string().required(trans('required')),
    gsm: Yup.string().required(trans('required')).min(minGsm, trans('job.message_format_gsm')).max(16, trans('job.message_format_gsm_max') ?? ''),
    email: Yup.string().required(trans('required')).email('Ongeldig e-mailadres'),
    password: Yup.string().required(trans('required')),
    password_confirmation: Yup.string()
      .required(trans('required'))
      .oneOf([Yup.ref('password'), ''], 'passworden moeten overeenkomen'),

  });

  const [isVisible, setIsVisible] = useState(false);
  const [errorMessage, setErrorMessage] = useState<any | null>(null);
  const [apiErrors, setApiErrors] = useState<any | null>(null);

  const formik = useFormik({
    initialValues: {
      first_name: '',
      last_name: '',
      gsm: '',
      email: '',
      password: '',
      password_confirmation: '',
    },
    validationSchema,

    onSubmit: async (values) => {
      // Only subit when the term and condition is clicked
      if (!isFirstIconVisible && values.password.length >= 6 && values.password_confirmation.length >= 6) {
        try {
          if (values.gsm.startsWith('0')) {
            setMinGsm(9);
            values.gsm = values.gsm.substring(1);
          }
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
            setErrorMessage(null);
            router.push("/user/register/ready");
          }
        } catch (error: any) {
          setApiErrors(error.response.data.data);
          const errors = Object.values(error.response.data.data);
          const lastErrorMessage = errors[errors.length - 1];
          setErrorMessage(lastErrorMessage);
        }
      }
    },
  });

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

  const [selectedCountry, setSelectedCountry] = useState("+32"); // Initial value for the country select
  const [inputValue, setInputValue] = useState(""); // Store the input value
  const [isFirstNameValid, setIsFirstNameValid] = useState(true); // Store the input value
  const [isEmailValid, setIsEmailValid] = useState(true); // Store the input value
  const [isLastNameValid, setIsLastNameValid] = useState(true); // Store the input value
  const [isPasswordValid, setIsPasswordValid] = useState(true); // Store the input value
  const [isPasswordConfirmationValid, setIsPasswordConfirmationValid] = useState(true); // Store the input value
  const [isGsmValid, setIsGsmValid] = useState(true); // Store the input value


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
    // Update the value in the formik state
    formik.setFieldValue('gsm', newValue);
  };

  const [isButtonClicked, setIsButtonClicked] = useState(false);
  // message error check
  const handleRegisterClick = () => {

    if (isFirstIconVisible) {
      setIsButtonClicked(true);
      setIsVisible(true);
      setErrorMessage(trans('accpet-terms'));
    }

    if (checkEmailValid(formik.values.email)) {
    } else {
      setIsEmailValid(false);
      if (formik.values.email) {
        setErrorMessage(trans('format-email'));
      }
    }

    if (formik.values.password !== formik.values.password_confirmation) {
      setErrorMessage(trans('password-not-match'));
      setIsPasswordValid(false);
      setIsPasswordConfirmationValid(false);
    }

    if (formik.values.password.length < 6) {
      setErrorMessage(trans('password-min-length'));
      setIsPasswordValid(false);
    }

    if (formik.values.password_confirmation.length < 6) {
      setErrorMessage(trans('password-min-length'));
      setIsPasswordConfirmationValid(false);
    }

    if (formik.values.gsm.length < minGsm) {
      setErrorMessage(trans('job.message_format_gsm'));
      setIsGsmValid(false);
    } else {
      setIsGsmValid(true);
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

      setIsVisible(true);
      setErrorMessage(trans('missing-fields'));
    }

    if (formik.values.gsm.startsWith('0')) {
      setMinGsm(9);
      formik.values.gsm = formik.values.gsm.substring(1);
      formik.setFieldValue('gsm', formik.values.gsm);
    }
  }

  // toggle icon check
  const [isFirstIconVisible, setIsFirstIconVisible] = useState(true);

  const toggleIcon = () => {
    setIsFirstIconVisible(!isFirstIconVisible);
  };


  function checkEmailValid(email: string) {
    // Sử dụng regex để kiểm tra định dạng email
    const emailRegex = /^[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,}$/i;
    return emailRegex.test(email);
  }

  useEffect(() => {
    function start() {
      gapi.client.init({
        clientId: process.env.NEXT_PUBLIC_GOOGLE_CLIENT_ID,
        scope: 'email',
      });
    }
    gapi.load('client:auth2', start);

    const expires = new Date();
    expires.setMonth(expires.getMonth() + 1); // survive for 1 month
    Cookies.set('currentLinkLogin', window.location.href, { expires });
  }, []);

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
  const [open, setOpen] = useState(false);

  useEffect(() => {
    if (errorMessage) {
      toast.dismiss();
      // Hiển thị toast
      if (Array.isArray(errorMessage)) {
        toast(errorMessage[0], {
          position: toast.POSITION.BOTTOM_CENTER,
          autoClose: 1500,
          hideProgressBar: true,
          closeOnClick: true,
          closeButton: false,
          transition: Slide,
          className: 'message',
        });
        setErrorMessage(null);
      } else {
        const cleanedErrorMessage = errorMessage.replace("/", "");
        toast(cleanedErrorMessage, {
          position: toast.POSITION.BOTTOM_CENTER,
          autoClose: 1500,
          hideProgressBar: true,
          closeOnClick: true,
          closeButton: false,
          transition: Slide,
          className: 'message',
        });
        setErrorMessage(null);
      }
    }
  }, [errorMessage]);

  return (
    <ThemeProvider theme={theme}>
      <div className='row' style={{ backgroundColor: color ? color : '#B5B268', minHeight: '100vh' }}>
        <div className="row mt-5">
          <div className="col-sm-12 col-xs-12 d-flex align-items-center ">
            <div className='ms-2'> <FontAwesomeIcon
              icon={faChevronLeft}
              onClick={() => router.back()}
              style={{ color: 'white', cursor: 'pointer', pointerEvents: 'auto', width: '20px', height: '20px' }}
            /></div>
            <div className='col-sm-10 col-xs-10' style={{ margin: "auto" }}> <h1 className={`${variables.register} ms-3`}>{trans('register-account')}</h1></div>
          </div>
        </div>
        <div className={`${variables.title} col-10`}><p>{trans('create-account')}</p></div>
        <form onSubmit={formik.handleSubmit} className='mb-2'>
          <Grid container spacing={2} style={{ justifyContent: 'center' }}>
            <Grid item xs={workspaceId ? 5 : 10} sm={workspaceId ? 5 : 10}>
              <TextField
                // formik.touched.first_name && (Boolean(formik.errors.first_name) ||
                className={`${variables.texting} ${!isFirstNameValid || (apiErrors && apiErrors.first_name) ? invalid : ''}`}
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
                InputLabelProps={{
                  shrink: false, // Đặt shrink thành false chỉ khi giá trị không rỗng
                  style: {
                    fontFamily: 'SF Compact Display',
                    fontSize: "16px",
                    fontStyle: 'normal',
                    fontWeight: ' 400',
                    lineHeight: '20px',
                    letterSpacing: '-0.24px',
                    color: !isFirstNameValid || (apiErrors && apiErrors.first_name) ? '#D94B2C' : '#949494'
                  }
                }}
                InputProps={{
                  style: { color: !isFirstNameValid || (apiErrors && apiErrors.isFirstNameValid) ? '#D94B2C' : '#413E38' },
                }}
                onKeyUp={() => { setIsFirstNameValid(true) }}
              />
            </Grid>
            <Grid item xs={workspaceId ? 5 : 10} sm={workspaceId ? 5 : 10}>
              <TextField
                className={`${variables.texting} ${!isLastNameValid || (apiErrors && apiErrors.last_name) ? invalid : ''}`}
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
                InputLabelProps={{
                  shrink: false, // Đặt shrink thành false chỉ khi giá trị không rỗng
                  style: {
                    fontFamily: 'SF Compact Display',
                    fontSize: "16px",
                    fontStyle: 'normal',
                    fontWeight: ' 400',
                    lineHeight: '20px',
                    letterSpacing: '-0.24px',
                    color: !isLastNameValid || (apiErrors && apiErrors.last_name) ? '#D94B2C' : '#949494'
                  }
                }}
                InputProps={{
                  style: { color: !isLastNameValid || (apiErrors && apiErrors.isLastNameValid) ? '#D94B2C' : '#413E38' },
                }}
                onKeyUp={() => { setIsLastNameValid(true) }}
              />
            </Grid>
            <Grid item xs={10}>
              <TextField
                type="text"
                className={`${variables.texting} ${!isGsmValid || (apiErrors && apiErrors.gsm) ? invalid : ''}`}
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
                  shrink: false,
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
              <div className={`${variables.vb} ms-2`}>
                {trans('vb')}
              </div>
            </Grid>
            <Grid item xs={10}>
              <TextField
                className={`${variables.texting} ${!isEmailValid || (apiErrors && apiErrors.email) ? invalid : ''}`}
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
                className={`${variables.texting} ${!isPasswordValid || (apiErrors && apiErrors.password) ? invalid : ''}`}
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
                onKeyUp={() => { setIsPasswordValid(true) }}
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
                    <InputAdornment position="end">
                      <IconButton
                        aria-label="toggle password visibility"
                        onClick={handleClickShowPassword}
                        edge="end"
                      >
                        {showPassword
                          ?
                          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="25" viewBox="0 0 24 25" fill="none">
                            <path d="M1 12.6473C1 12.6473 5 4.68262 12 4.68262C19 4.68262 23 12.6473 23 12.6473C23 12.6473 19 20.612 12 20.612C5 20.612 1 12.6473 1 12.6473Z" stroke={!isPasswordValid || (apiErrors && apiErrors.password) ? '#D94B2C' : 'black'} strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" />
                            <path d="M12 15.6342C13.6569 15.6342 15 14.297 15 12.6474C15 10.9979 13.6569 9.66064 12 9.66064C10.3431 9.66064 9 10.9979 9 12.6474C9 14.297 10.3431 15.6342 12 15.6342Z" stroke={!isPasswordValid || (apiErrors && apiErrors.password) ? '#D94B2C' : 'black'} strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" />
                            <line x1="5.378" y1="1.318" x2="19.318" y2="23.622" stroke={!isPasswordValid || (apiErrors && apiErrors.password) ? '#D94B2C' : 'black'} strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" />
                          </svg>
                          :
                          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="25" viewBox="0 0 24 25" fill="none">
                            <path d="M1 12.6473C1 12.6473 5 4.68262 12 4.68262C19 4.68262 23 12.6473 23 12.6473C23 12.6473 19 20.612 12 20.612C5 20.612 1 12.6473 1 12.6473Z" stroke={!isPasswordValid || (apiErrors && apiErrors.password) ? '#D94B2C' : 'black'} strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" />
                            <path d="M12 15.6342C13.6569 15.6342 15 14.297 15 12.6474C15 10.9979 13.6569 9.66064 12 9.66064C10.3431 9.66064 9 10.9979 9 12.6474C9 14.297 10.3431 15.6342 12 15.6342Z" stroke={!isPasswordValid || (apiErrors && apiErrors.password) ? '#D94B2C' : 'black'} strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" />
                          </svg>
                        }
                      </IconButton>
                    </InputAdornment>
                  )
                }}
              />
            </Grid>
            <Grid item xs={10}>
              <TextField
                className={`${variables.texting} ${!isPasswordConfirmationValid || (apiErrors && apiErrors.password_confirmation) ? invalid : ''}`}
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
                onKeyUp={() => { setIsPasswordConfirmationValid(true) }}
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
                    <InputAdornment position="end">
                      <IconButton
                        aria-label="toggle password visibility"
                        onClick={handleClickShowRepeatPassword}
                        edge="end"
                      >
                        {showRepeatPassword
                          ?
                          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="25" viewBox="0 0 24 25" fill="none">
                            <path d="M1 12.6473C1 12.6473 5 4.68262 12 4.68262C19 4.68262 23 12.6473 23 12.6473C23 12.6473 19 20.612 12 20.612C5 20.612 1 12.6473 1 12.6473Z" stroke={!isPasswordValid || (apiErrors && apiErrors.password) ? '#D94B2C' : 'black'} strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" />
                            <path d="M12 15.6342C13.6569 15.6342 15 14.297 15 12.6474C15 10.9979 13.6569 9.66064 12 9.66064C10.3431 9.66064 9 10.9979 9 12.6474C9 14.297 10.3431 15.6342 12 15.6342Z" stroke={!isPasswordValid || (apiErrors && apiErrors.password) ? '#D94B2C' : 'black'} strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" />
                            <line x1="5.378" y1="1.318" x2="19.318" y2="23.622" stroke={!isPasswordValid || (apiErrors && apiErrors.password) ? '#D94B2C' : 'black'} strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" />
                          </svg>
                          :
                          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="25" viewBox="0 0 24 25" fill="none">
                            <path d="M1 12.6473C1 12.6473 5 4.68262 12 4.68262C19 4.68262 23 12.6473 23 12.6473C23 12.6473 19 20.612 12 20.612C5 20.612 1 12.6473 1 12.6473Z" stroke={!isPasswordValid || (apiErrors && apiErrors.password) ? '#D94B2C' : 'black'} strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" />
                            <path d="M12 15.6342C13.6569 15.6342 15 14.297 15 12.6474C15 10.9979 13.6569 9.66064 12 9.66064C10.3431 9.66064 9 10.9979 9 12.6474C9 14.297 10.3431 15.6342 12 15.6342Z" stroke={!isPasswordValid || (apiErrors && apiErrors.password) ? '#D94B2C' : 'black'} strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" />
                          </svg>
                        }
                      </IconButton>
                    </InputAdornment>
                  )
                }}
              />
            </Grid>
            <div className="row mt-3 ms-3 mb-2">
              <div className='col-sm-12 col-xs-12 d-flex align-items-center '>
                <div className={`${workspaceId ? '' : 'mb-4'} ms-4 me-2 `} onClick={toggleIcon}>
                  {isFirstIconVisible ? (
                    <svg xmlns="http://www.w3.org/2000/svg" width="17" height="17" viewBox="0 0 17 17" fill='none'
                    >
                      <path d="M16 8.5V14.3333C16 14.7754 15.8244 15.1993 15.5118 15.5118C15.1993 15.8244 14.7754 16 14.3333 16H2.66667C2.22464 16 1.80072 15.8244 1.48816 15.5118C1.17559 15.1993 1 14.7754 1 14.3333V2.66667C1 2.22464 1.17559 1.80072 1.48816 1.48816C1.80072 1.17559 2.22464 1 2.66667 1H11.8333" stroke={isButtonClicked ? "red" : "white"} strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" />
                    </svg>
                  ) : (
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="white" viewBox="0 0 24 24">
                      <path d="M20 12.194v9.806h-20v-20h18.272l-1.951 2h-14.321v16h16v-5.768l2-2.038zm.904-10.027l-9.404 9.639-4.405-4.176-3.095 3.097 7.5 7.273 12.5-12.737-3.096-3.096z" />
                    </svg>
                  )}
                </div>
                {workspaceId ? (
                  <div className='me-2' style={{ width: '80%' }}>
                    <p className={`${variables.termMobile}`} onClick={() => { window.open(TERMS_CONDITIONS_LINK, "_blank") }}>
                      {trans('agree')} <span className={variables.underline}>{trans('term-condition')}</span>.
                    </p>
                  </div>
                ) : (
                  <div className='me-2'> <p className={`${variables.termo}`}>
                    {trans('agree')} <span className={variables.underline} onClick={() => { window.open(TERMS_CONDITIONS_LINK, "_blank") }}>{trans('term-condition')}</span> {trans('en')} <span className={variables.underline} onClick={() => { window.open(PRIVACY_POLICY_LINK, "_blank") }}>{trans('privacy-portal')}</span>.
                  </p></div>
                )}

              </div>
            </div>

            <Grid item xs={12} className={`${variables.regis} d-flex justify-content-center`} style={{ margin: 'auto' }}>
              <Button variant="contained" type="submit" className={`${variables.regisButton}`} onClick={handleRegisterClick}>
                <div className={`${variables.regisButtonText}`}>{trans('register-btn')}</div>
              </Button>
            </Grid>
          </Grid>
        </form>
        <div className={`${line} text-center mt-2`}>
          <span className={`${dashLine}`}></span>&nbsp;&nbsp;
          {trans('of')}
          &nbsp;&nbsp;<span className={`${dashLine}`}></span>
        </div>
        <div className={`${loginWith} text-center`}>{trans('register-with')}</div>
        <div className="row d-flex" style={{ justifyContent: 'space-evenly', margin: 'auto', marginBottom: "30%" }}>
          {(apiDataToken?.data?.google_enabled > 0 || !workspaceId) && (
            <GoogleLogin
              clientId={process.env.NEXT_PUBLIC_GOOGLE_CLIENT_ID ?? ''}
              render={renderProps => (
                <div onClick={renderProps.onClick} className='col-sm-3 col-3 d-grid justify-content-center' style={{ backgroundColor: 'white', borderRadius: '50%', width: '50px', height: '50px' }}>
                  <svg xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" width="50" height="50" viewBox="0 0 48 48" style={{ margin: 'auto' }}>
                    <path fill="#FFC107" d="M43.611,20.083H42V20H24v8h11.303c-1.649,4.657-6.08,8-11.303,8c-6.627,0-12-5.373-12-12c0-6.627,5.373-12,12-12c3.059,0,5.842,1.154,7.961,3.039l5.657-5.657C34.046,6.053,29.268,4,24,4C12.955,4,4,12.955,4,24c0,11.045,8.955,20,20,20c11.045,0,20-8.955,20-20C44,22.659,43.862,21.35,43.611,20.083z"></path><path fill="#FF3D00" d="M6.306,14.691l6.571,4.819C14.655,15.108,18.961,12,24,12c3.059,0,5.842,1.154,7.961,3.039l5.657-5.657C34.046,6.053,29.268,4,24,4C16.318,4,9.656,8.337,6.306,14.691z"></path><path fill="#4CAF50" d="M24,44c5.166,0,9.86-1.977,13.409-5.192l-6.19-5.238C29.211,35.091,26.715,36,24,36c-5.202,0-9.619-3.317-11.283-7.946l-6.522,5.025C9.505,39.556,16.227,44,24,44z"></path><path fill="#1976D2" d="M43.611,20.083H42V20H24v8h11.303c-0.792,2.237-2.231,4.166-4.087,5.571c0.001-0.001,0.002-0.001,0.003-0.002l6.19,5.238C36.971,39.205,44,34,44,24C44,22.659,43.862,21.35,43.611,20.083z"></path>
                  </svg>
                </div>
              )}
              buttonText="Login"
              onSuccess={onSuccess}
              onFailure={onFailure}
              cookiePolicy={'single_host_origin'}
            />)}

          {(apiDataToken?.data?.facebook_enabled > 0 || !workspaceId) && (
            <FacebookLogin
              appId={process.env.NEXT_PUBLIC_FACEBOOK_APP_ID}
              callback={responseFacebook}
              isMobile={false}
              render={(renderProps: any) => (
                <div onClick={renderProps.onClick} className='col-sm-3 col-3 d-grid justify-content-center' style={{ backgroundColor: 'white', borderRadius: '50%', width: '50px', height: '50px' }}>
                  <svg xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" width="50" height="50" viewBox="0 0 48 48" style={{ margin: 'auto' }}>
                    <linearGradient id="Ld6sqrtcxMyckEl6xeDdMa_uLWV5A9vXIPu_gr1" x1="9.993" x2="40.615" y1="9.993" y2="40.615" gradientUnits="userSpaceOnUse"><stop offset="0" stopColor="#2aa4f4"></stop><stop offset="1" stopColor="#007ad9"></stop></linearGradient><path fill="url(#Ld6sqrtcxMyckEl6xeDdMa_uLWV5A9vXIPu_gr1)" d="M24,4C12.954,4,4,12.954,4,24s8.954,20,20,20s20-8.954,20-20S35.046,4,24,4z"></path><path fill="#fff" d="M26.707,29.301h5.176l0.813-5.258h-5.989v-2.874c0-2.184,0.714-4.121,2.757-4.121h3.283V12.46 c-0.577-0.078-1.797-0.248-4.102-0.248c-4.814,0-7.636,2.542-7.636,8.334v3.498H16.06v5.258h4.948v14.452 C21.988,43.9,22.981,44,24,44c0.921,0,1.82-0.084,2.707-0.204V29.301z"></path>
                  </svg>
                </div>
              )}
            />)}

          {(apiDataToken?.data?.apple_enabled > 0 || !workspaceId) && (
            <AppleLogin
              clientId={process.env.NEXT_PUBLIC_APPLE_CLIENT_ID ?? ''}
              redirectURI={window.location.origin}
              responseType="id_token code"
              responseMode="fragment"
              usePopup={true}
              callback={responseApple}
              // scope="name email"
              render={(renderProps: any) => (
                <div onClick={renderProps.onClick} className='col-sm-3 col-3 d-grid justify-content-center' style={{ backgroundColor: 'white', borderRadius: '50%', width: '50px', height: '50px' }}>
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
            />)}
        </div>
      </div>
      <ToastContainer />
    </ThemeProvider>
  );
};

export default Register;
