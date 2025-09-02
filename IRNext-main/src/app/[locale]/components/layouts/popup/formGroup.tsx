'use client'

import { useEffect, useState } from 'react';
import { Button, Modal } from 'react-bootstrap';
import 'public/assets/css/popup.scss';
import { useI18n } from '@/locales/client'
import { createTheme, ThemeProvider } from "@mui/material/styles";
import { useFormik } from 'formik';
import * as Yup from 'yup';
import Cookies from 'js-cookie';
import { api } from "@/utils/axios";
import {
    Grid,
    TextField,
    TextareaAutosize,
} from '@mui/material';
import * as config from "@/config/constants";
import variables from '/public/assets/css/formGroup.module.scss'
import { InputAdornment } from '@mui/material';
import Select from '@mui/material/Select';
import MenuItem from '@mui/material/MenuItem';
import { ToastContainer, toast, Slide } from 'react-toastify';
import 'react-toastify/dist/ReactToastify.css';
import {VALIDATION_PHONE_MAX} from "@/config/constants";
import { useAppSelector } from '@/redux/hooks'

export default function FormGroup({ toggleFormGroup, isSuccess }: { toggleFormGroup: any, isSuccess: any }) {
    const [show, setShow] = useState(false);
    const [selectedCountry, setSelectedCountry] = useState("+32"); // Initial value for the country select
    const handleCountryChange = (event: any) => {
        let gsmValue = formik.values.gsm

        formik.setFieldValue('gsm', gsmValue);
        setSelectedCountry(event.target.value);
    };

    const handleClose = () => {
        toggleFormGroup(); // Thêm console.log ở đây
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
    const trans = useI18n()
    const language = Cookies.get('Next-Locale');
    const message = variables['message'];
    const invalid = variables['invalid'];
    const [isVisible, setIsVisible] = useState(false);
    const [errorMessage, setErrorMessage] = useState<any | null>(null);
    const [apiErrors, setApiErrors] = useState<any | null>(null);
    const [isFirstNameValid, setIsFirstNameValid] = useState(true); // Store the input value
    const [isEmailValid, setIsEmailValid] = useState(true); // Store the input value
    const [isLastNameValid, setIsLastNameValid] = useState(true); // Store the input value
    const [isGsmValid, setIsGsmValid] = useState(true); // Store the input value
    const [minGsm, setMinGsm] = useState(9);
    const validationSchema = Yup.object().shape({
        first_name: Yup.string().required(trans('required')),
        last_name: Yup.string().required(trans('required')),
        gsm: Yup.string().required(trans('required')),
        email: Yup.string().required(trans('required')).email(trans('lang_email_valid_message')),
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
                            bottom: '2.5px',
                            height: '42px',
                            zIndex: '100',
                            borderTopRightRadius: '0', // Đặt bán kính cho góc trên bên phải
                            borderBottomRightRadius: '0', // Đặt bán kính cho góc dưới bên phải
                            padding: '12.5px 0px',
                        },
                        "& .MuiOutlinedInput-input": {
                            padding: '8.5px 10px 12px',
                        },
                        "& #gsm": {
                            marginLeft: '70px',
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
        if (formik.values.gsm.length < minGsm){
            setIsGsmValid(false);
            setErrorMessage(trans('format-gsm'));
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
            setErrorMessage(trans('missing-fields-red'));
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
            if (formik.values.gsm.length >= minGsm) {
                setIsGsmValid(true);
            }
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

        // Update the value in the formik state
        formik.setFieldValue('gsm', newValue);
    };
    const formik = useFormik({
        initialValues: {
            first_name: '',
            last_name: '',
            gsm: '',
            email: '',
            company: '',
            city: '',
            message: '',
        },
        validationSchema,
        onSubmit: async (values) => {
            if (values.gsm.startsWith('0')) {
                setMinGsm(9);
                values.gsm = values.gsm.substring(1);
                formik.setFieldValue('gsm', values.gsm);
            }
            // Only subit when the term and condition is clicked
            if (workspaceId && isEmailValid && isFirstNameValid && isLastNameValid && isGsmValid) {
                toast.dismiss();
                try {
                    const headers = {
                        'Content-Language': language,
                        'App-Token': workspaceToken
                    };
                    const response = await api.post(`workspaces/${workspaceId}/contacts`, {
                        email: values.email,
                        first_name: values.first_name,
                        last_name: values.last_name,
                        phone: selectedCountry + values.gsm,
                        company_name: values.company,
                        address: values.city,
                        content: values.message,
                        // add more data if needed
                    }, { headers });
                    if ('data' in response) {
                        isSuccess(true)
                        toggleFormGroup()
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
    const [open, setOpen] = useState(false);

    return (
        <>
            <Button variant="primary" onClick={handleShow} style={{ display: 'none' }}></Button>

            <Modal show={show} onHide={handleClose}
                aria-labelledby="contained-modal-title-vcenter"
                centered id='form-group'
            >
                <div className={`mx-auto`} style={{ alignItems: 'center' }}>
                    <svg xmlns="http://www.w3.org/2000/svg" width="101" height="3" viewBox="0 0 101 3" fill="none">
                        <path d="M2 1.5H99" stroke="#E1E1E1" strokeWidth="3" strokeLinecap="round" />
                    </svg>
                </div>
                <Modal.Header>
                    <h1>{trans('intersted-ordering')}</h1>
                    <div className='promise'>{trans('contact-promise')}</div>
                </Modal.Header>
                <Modal.Body>
                    <ThemeProvider theme={theme}>
                        <div className='row'>
                            <form onSubmit={formik.handleSubmit}>
                                <Grid container spacing={2} style={{ justifyContent: 'center' }}>
                                    <Grid item xs={11}>
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
                                                    marginTop: '-5px',
                                                    marginLeft: '-5px'
                                                }
                                            }}
                                            InputProps={{
                                                style: { color: !isFirstNameValid || (apiErrors && apiErrors.isFirstNameValid) ? '#D94B2C' : '#413E38' },
                                            }}
                                        />
                                    </Grid>
                                    <Grid item xs={11}>
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
                                                    marginTop: '-5px',
                                                    marginLeft: '-5px'
                                                }
                                            }}
                                            InputProps={{
                                                style: { color: !isLastNameValid || (apiErrors && apiErrors.isLastNameValid) ? '#D94B2C' : '#413E38' },
                                            }}
                                        />
                                    </Grid>
                                    <Grid item xs={11}>
                                        <TextField
                                            className={`${variables.texting} ${!isEmailValid || (apiErrors && apiErrors.email) ? invalid : ''}`}
                                            fullWidth
                                            id="email"
                                            name="email"
                                            label={formik.values.email ? '' : trans('email-field')}
                                            variant="outlined"
                                            value={formik.values.email}
                                            onChange={formik.handleChange}
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
                                                    color: !isEmailValid || (apiErrors && apiErrors.email) ? '#D94B2C' : '#949494',
                                                    marginTop: '-5px',
                                                    marginLeft: '-5px'
                                                }
                                            }}
                                            inputProps={{ style: { color: !isEmailValid || (apiErrors && apiErrors.email) ? '#D94B2C' : '#413E38' } }}
                                        />
                                    </Grid>
                                    <Grid item xs={11}>
                                        <TextField
                                            className={`${variables.texting}`}
                                            fullWidth
                                            id="company"
                                            name="company"
                                            style={{ backgroundColor: '#FFFFFF' }}
                                            label={formik.values.company ? '' : trans('company-label')}
                                            variant="outlined"
                                            value={formik.values.company}
                                            onChange={formik.handleChange}
                                            onBlur={formik.handleBlur}
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
                                                    marginTop: '-5px',
                                                    marginLeft: '-5px'
                                                }
                                            }}
                                        />
                                    </Grid>
                                    <Grid item xs={11}>
                                        <TextField
                                            type="text"
                                            className={`${variables.texting} ${!isGsmValid || (apiErrors && apiErrors.gsm) ? invalid : ''}`}
                                            fullWidth
                                            id="gsm"
                                            name="gsm"
                                            label={formik.values.gsm ? '' : trans('mobile-plus')}
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
                                                    transform: formik.values.gsm !== "" ? '' : 'translate(100px, 10px) scale(1)', // Adjust label position
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
                                    <Grid item xs={11}>
                                        <TextField
                                            className={`${variables.texting}`}
                                            fullWidth
                                            id="city"
                                            name="city"
                                            style={{ backgroundColor: '#FFFFFF' }}
                                            label={formik.values.city ? '' : trans('city-label')}
                                            variant="outlined"
                                            value={formik.values.city}
                                            onChange={formik.handleChange}
                                            onBlur={formik.handleBlur}
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
                                                    color: '#949494',
                                                    marginTop: '-5px',
                                                    marginLeft: '-5px'
                                                }
                                            }}
                                        />
                                    </Grid>
                                    <Grid item xs={11}>
                                        <TextareaAutosize
                                            className={`${variables.messaging}`}
                                            id="message"
                                            name="message"
                                            style={{
                                                backgroundColor: '#FFFFFF',
                                                border: 'none',
                                                outline: 'none',
                                            }}
                                            aria-label="minimum height"
                                            minRows={3}
                                            placeholder={trans('message')}
                                            value={formik.values.message}
                                            onChange={formik.handleChange}
                                            onBlur={formik.handleBlur}
                                        />
                                    </Grid>

                                    <Grid item xs={12} className={`${variables.regis} d-flex justify-content-center mt-2`} style={{ margin: 'auto' }}>
                                        <Button variant="contained" type="submit" className={`${variables.regisButton}`} onClick={handleSubmitClick}>
                                            <div className={`${variables.regisButtonText}`}>{trans('send-message')}</div>
                                        </Button>
                                    </Grid>
                                </Grid>
                            </form>
                        </div>
                    </ThemeProvider>
                </Modal.Body>
                <Modal.Footer>
                </Modal.Footer>
            </Modal>
            <ToastContainer />
        </>
    );
}
