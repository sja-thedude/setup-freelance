'use client'
import style from 'public/assets/css/profile.module.scss'
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
import 'react-toastify/dist/ReactToastify.css';
import {VALIDATION_PHONE_MAX} from "@/config/constants";
import { useGetWorkspaceDataByIdQuery } from '@/redux/services/workspace/workspaceDataApi'
import { setFlagDesktopChangeType } from '@/redux/slices/flagDesktopChangeTypeSilce'
import { useAppSelector, useAppDispatch } from '@/redux/hooks'

export default function FormGroup({ toggleFormGroup, isSuccess }: { toggleFormGroup: any, isSuccess: any }) {
    const workspaceToken = useAppSelector((state) => state.workspaceData.globalWorkspaceToken)
    const workspaceId = useAppSelector((state) => state.workspaceData.globalWorkspaceId)
    const { data: apiDataToken } = useGetWorkspaceDataByIdQuery({id: workspaceId})
    const apiData = apiDataToken?.data?.setting_generals;
    const color = useAppSelector((state) => state.workspaceData.globalWorkspaceColor)
    const [show, setShow] = useState(false);
    const [selectedCountry, setSelectedCountry] = useState("+32"); // Initial value for the country select

    const handleCountryChange = (event: any) => {
        let gsmValue = formik.values.gsm

        formik.setFieldValue('gsm', gsmValue);
        setSelectedCountry(event.target.value);
    };
    const dispatch = useAppDispatch()
    const handleClose = () => {
        toggleFormGroup(); // Thêm console.log ở đây
        setShow(false);
        setFormSuccess(false);
        if (Cookies.get('opendedAddressDesk') == 'true') {
            dispatch(setFlagDesktopChangeType(true))
            Cookies.remove('opendedAddressDesk')
        }
    };

    const handleShow = () => setShow(true);

    useEffect(() => {
        // const hasShownPopup = localStorage.getItem('hasShownPopup');
        const hasShownPopup = false;

        if (!hasShownPopup) {
            setShow(true);
        }
    }, []);

    const trans = useI18n()
    const language = Cookies.get('Next-Locale');
    const message = variables['message'];
    const invalid = variables['invalid'];
    const [isVisible, setIsVisible] = useState(false);
    const [errorMessage, setErrorMessage] = useState<any | null>(null);
    const [apiErrors, setApiErrors] = useState<any | null>(null);
    const [isFirstNameValid, setIsFirstNameValid] = useState(true); // Store the input value
    const [isEmailValid, setIsEmailValid] = useState(true); // Store the input value
    const [isGsmValid, setIsGsmValid] = useState(true); // Store the input value
    const [isLastNameValid, setIsLastNameValid] = useState(true); // Store the input value
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
        if(enableButton){
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
        }
    }

    const [enableButton, setEnableButton] = useState<any | null>(false);
    const [email, setEmail] = useState<any | null>(null);
    const [firstname, setFirstname] = useState<any | null>(null);
    const [lastname, setLastName] = useState<any | null>(null);
    const [gms, setGms] = useState<any | null>();

    const checkEnableButton = () => {
        if (email && firstname && lastname && gms) {
            setEnableButton(true);
        }else{
            setEnableButton(false);
        }
    }

    const handleGsmChange = (event: any) => {
        let newValue = event.target.value;
        formik.handleChange(event);
        // Remove characters that are not numbers
        const sanitizedValue = newValue.replace(/\D/g, '');
        newValue = sanitizedValue;

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
        setGms(newValue);
        checkEnableButton();
    };

    const handleFirstNameChange = (event: any) => {
        formik.handleChange(event);
        setFirstname(event.target.value);
        checkEnableButton();
    }

    const handleLastNameChange = (event: any) => {
        formik.handleChange(event);
        setLastName(event.target.value);
        checkEnableButton();
    }

    const handleEmailChange = (event: any) => {
        formik.handleChange(event);
        setEmail(event.target.value);
        checkEnableButton();
    }

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
            if(enableButton){
                if (values.gsm.startsWith('0')) {
                    setMinGsm(9);
                    values.gsm = values.gsm.substring(1);
                    formik.setFieldValue('gsm', values.gsm);
                    setGms(values.gsm);
                }
                // Only subit when the term and condition is clicked
                if (workspaceId && isEmailValid && isFirstNameValid && isLastNameValid && isGsmValid && isGsmValid) {
                    //toast.dismiss();
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
                            // isSuccess(true)
                            setFormSuccess(true)
                            //toggleFormGroup()
                        }
                    } catch (error: any) {
                        setApiErrors(error.response.data.data);
                        const errors = Object.values(error.response.data.data);
                        const lastErrorMessage = errors[errors.length - 1];
                        setErrorMessage(lastErrorMessage);
                    }
                }
            }
        },
    });

    const [formSuccess, setFormSuccess] = useState<any | null>(false);
    const [open, setOpen] = useState(false);

    return (
        <>
            <Button onClick={handleShow} style={{ display: 'none' }}></Button>

            <Modal show={show} onHide={handleClose}
                   animation={false}
                   id='modal-profile'
            >
                <Modal.Body>
                    {formSuccess ? (
                        <div className={`${variables.fromGroupDesktopSuccess}`}>
                            <div className="close-popup pt-1" onClick={() => handleClose()}>
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="25" viewBox="0 0 24 25" fill="none" style={{ marginTop: '-3px' }}>
                                    <path d="M14 17L10 12.5L14 8" stroke="#888888" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"/>
                                </svg>
                                <div className="ms-1">{ trans('back') }</div>
                            </div>

                            <div className={`${variables.message}`}>
                                <div>
                                    <svg xmlns="http://www.w3.org/2000/svg" width="134" height="134" viewBox="0 0 134 134" fill="none">
                                        <path d="M122.833 11.167L61.4166 72.5837" stroke={color ? color : "#D87833"} strokeWidth="4" strokeLinecap="round" strokeLinejoin="round"/>
                                        <path d="M122.833 11.167L83.75 122.834L61.4166 72.5837L11.1666 50.2503L122.833 11.167Z" stroke={color ? color : "#D87833"} strokeWidth="4" strokeLinecap="round" strokeLinejoin="round"/>
                                    </svg>
                                    <h3>{trans('title-success-contact')}</h3>
                                    <p>{trans('description-success-contact')}</p>
                                    <a href="#" onClick={() => handleClose()} style={{color: color ? color : "#D87833"}}>{trans('back-contact')}</a>
                                </div>
                            </div>
                        </div>
                    ) : (
                        <div className={`${variables.fromGroupDesktop}`}>
                            <div className="close-popup pt-1" onClick={() => handleClose()}>
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="25" viewBox="0 0 24 25" fill="none" style={{ marginTop: '-3px' }}>
                                    <path d="M14 17L10 12.5L14 8" stroke="#888888" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"/>
                                </svg>
                                <div className="ms-1">{ trans('back') }</div>
                            </div>
                            <div className={`px-3`}>
                                <div className={`${style['error-message']} ${variables.errorMessage} ${!errorMessage && variables.errorMessageHide}`}>
                                    <svg className="me-2" xmlns="http://www.w3.org/2000/svg" width="21" height="21" viewBox="0 0 21 21" fill="none">
                                        <path d="M9.00368 3.37756L1.59243 15.7501C1.43962 16.0147 1.35877 16.3147 1.35792 16.6203C1.35706 16.9258 1.43623 17.2263 1.58755 17.4918C1.73887 17.7572 1.95706 17.9785 2.22042 18.1334C2.48378 18.2884 2.78313 18.3717 3.08868 18.3751H17.9112C18.2167 18.3717 18.5161 18.2884 18.7794 18.1334C19.0428 17.9785 19.261 17.7572 19.4123 17.4918C19.5636 17.2263 19.6428 16.9258 19.6419 16.6203C19.6411 16.3147 19.5602 16.0147 19.4074 15.7501L11.9962 3.37756C11.8402 3.1204 11.6206 2.90779 11.3585 2.76023C11.0964 2.61267 10.8007 2.53516 10.4999 2.53516C10.1992 2.53516 9.90347 2.61267 9.64138 2.76023C9.3793 2.90779 9.15966 3.1204 9.00368 3.37756V3.37756Z" stroke="#E03009" strokeWidth="1.5" strokeLinecap="round" strokeLinejoin="round"/>
                                        <path d="M10.5 7.875V11.375" stroke="#E03009" strokeWidth="1.5" strokeLinecap="round" strokeLinejoin="round"/>
                                        <path d="M10.5 14.875H10.5088" stroke="#E03009" strokeWidth="1.5" strokeLinecap="round" strokeLinejoin="round"/>
                                    </svg>
                                    {errorMessage && errorMessage}
                                </div>
                            </div>
                            <div className={`${variables.title} text-center px-3 pt-3 pb-1 profile-title`}>{ trans('contact') }</div>
                            <div className="text-center px-3 profile-description" role="button">{ trans('contact-description') }</div>
                            <div className={style['menu-profile']}>
                                <div style={{marginTop: '30px'}}>
                                    <ThemeProvider theme={theme}>
                                        <form onSubmit={formik.handleSubmit}>
                                            <Grid container spacing={2} style={{ justifyContent: 'center' }}>
                                                <Grid item xs={11}>
                                                    <TextField
                                                        // formik.touched.first_name && (Boolean(formik.errors.first_name) ||
                                                        className={`${variables.texting} ${!isFirstNameValid || (apiErrors && apiErrors.first_name) ? invalid : ''}`}
                                                        fullWidth
                                                        id="first_name"
                                                        name="first_name"
                                                        placeholder={trans('first-name')}
                                                        variant="outlined"
                                                        value={formik.values.first_name}
                                                        onChange={handleFirstNameChange}
                                                        onBlur={formik.handleBlur}
                                                        error={formik.touched.first_name && Boolean(formik.errors.first_name || (apiErrors && apiErrors?.first_name))}
                                                        helperText={""}
                                                        onKeyUp={() => { setIsFirstNameValid(true) }}
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
                                                        placeholder={trans('last-name')}
                                                        variant="outlined"
                                                        value={formik.values.last_name}
                                                        onChange={handleLastNameChange}
                                                        onBlur={formik.handleBlur}
                                                        error={formik.touched.last_name && Boolean(formik.errors.last_name || (apiErrors && apiErrors.last_name))}
                                                        helperText={""}
                                                        onKeyUp={() => { setIsLastNameValid(true) }}
                                                        InputProps={{
                                                            style: { color: !isLastNameValid || (apiErrors && apiErrors.isLastNameValid) ? '#D94B2C' : '#413E38' },
                                                        }}
                                                    />
                                                </Grid>

                                                <Grid item xs={11}>
                                                    <TextField
                                                        className={`${variables.texting}`}
                                                        fullWidth
                                                        id="company"
                                                        name="company"
                                                        style={{ backgroundColor: '#FFFFFF' }}
                                                        placeholder={trans('company-label')}
                                                        variant="outlined"
                                                        value={formik.values.company}
                                                        onChange={formik.handleChange}
                                                        onBlur={formik.handleBlur}
                                                        error={formik.touched.company && Boolean(formik.errors.company || (apiErrors && apiErrors.company))}
                                                        helperText={""}
                                                    />
                                                </Grid>

                                                <Grid item xs={11}>
                                                    <TextField
                                                        className={`${variables.texting} ${!isEmailValid || (apiErrors && apiErrors.email) ? invalid : ''}`}
                                                        fullWidth
                                                        id="email"
                                                        name="email"
                                                        placeholder={trans('email-field')}
                                                        variant="outlined"
                                                        value={formik.values.email}
                                                        onChange={handleEmailChange}
                                                        onBlur={formik.handleBlur}
                                                        error={formik.touched.email && Boolean(formik.errors.email || (apiErrors && apiErrors.email))}
                                                        helperText={""}
                                                        onKeyUp={() => { setIsEmailValid(true) }}
                                                        inputProps={{ style: { color: !isEmailValid || (apiErrors && apiErrors.email) ? '#D94B2C' : '#413E38' } }}
                                                    />
                                                </Grid>

                                                <Grid item xs={11}>
                                                    <TextField
                                                        type="text"
                                                        className={`${variables.texting} ${!isGsmValid || (apiErrors && apiErrors.gsm) ? invalid : ''}`}
                                                        fullWidth
                                                        id="gsm"
                                                        name="gsm"
                                                        placeholder={trans('contact-gsm')}
                                                        variant="outlined"
                                                        value={formik.values.gsm}
                                                        onChange={handleGsmChange}
                                                        onBlur={formik.handleBlur}
                                                        error={formik.touched.gsm && Boolean(formik.errors.gsm || (apiErrors && apiErrors.gsm))}
                                                        helperText={""}
                                                        onKeyUp={() => { setIsGsmValid(true) }}
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
                                                                            <div className='d-flex' style={{ display: 'flex', alignItems: 'center' }}><svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" shapeRendering="geometricPrecision" textRendering="geometricPrecision" imageRendering="optimizeQuality" fillRule="evenodd" clipRule="evenodd" viewBox="0 0 203.55 141.6" > <g fillRule="nonzero"> <path fill="#ED2939" d="M203.55 11.19v119.22c0 6.16-5.04 11.19-11.19 11.19H11.19C5.05 141.6.02 136.59 0 130.45V11.15C.02 5.01 5.05 0 11.19 0h181.17c6.15 0 11.19 5.03 11.19 11.19z" /> <path fill="#FAE042" d="M135.7 0v141.6H11.19C5.05 141.6.02 136.59 0 130.45V11.15C.02 5.01 5.05 0 11.19 0H135.7z" /> <path d="M67.85 0v141.6H11.19C5.05 141.6.02 136.59 0 130.45V11.15C.02 5.01 5.05 0 11.19 0h56.66z" /></g></svg>
                                                                                <div className={`${variables.country}`}>+32</div></div>
                                                                        </MenuItem>
                                                                        <MenuItem className={variables.customMenuItem} value="+31">
                                                                            <div className='d-flex' style={{ display: 'flex', alignItems: 'center' }}>
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
                                                        placeholder={trans('city-label')}
                                                        variant="outlined"
                                                        value={formik.values.city}
                                                        onChange={formik.handleChange}
                                                        onBlur={formik.handleBlur}
                                                        error={formik.touched.city && Boolean(formik.errors.city || (apiErrors && apiErrors.city))}
                                                        helperText={""}
                                                    />
                                                </Grid>
                                                <Grid item xs={11}>
                                                    <TextareaAutosize
                                                        className={`${variables.messaging}`}
                                                        id="message"
                                                        name="message"
                                                        style={{
                                                            backgroundColor: '#FFFFFF',
                                                            fontFamily: 'SF Compact Display',
                                                            fontSize: '16px',
                                                            fontStyle: 'normal',
                                                            fontWeight: '400',
                                                            lineHeight: '20px',
                                                            padding: '12px 10px',
                                                            color: '#949494',
                                                        }}
                                                        aria-label="minimum height"
                                                        minRows={3}
                                                        placeholder={trans('message')}
                                                        value={formik.values.message}
                                                        onChange={formik.handleChange}
                                                        onBlur={formik.handleBlur}
                                                    />
                                                </Grid>

                                                <Grid item xs={12} className={`d-flex justify-content-center mt-2`} style={{ margin: 'auto' }}>
                                                    <Button variant="contained" type="submit" onClick={handleSubmitClick} style={{width: 'calc(100% - 20px)'}}>
                                                        <div className={`${variables.regisButtonText} ${style['save-button']}`}  style={enableButton ? { background: color } : {background: color, opacity: '0.5'}}>{trans('submit-contact')}</div>
                                                    </Button>
                                                </Grid>

                                                <Grid item xs={12} className={`d-flex justify-content-center mt-2`} style={{ margin: 'auto' }}>
                                                    <a href="#" className={`${variables.back}`} style={{color: color}} onClick={() => handleClose()}>{trans('back-contact')}</a>
                                                </Grid>
                                            </Grid>
                                        </form>
                                    </ThemeProvider>
                                </div>
                            </div>
                        </div>
                    )}
                </Modal.Body>
            </Modal>
            <style>{`
                .MuiButtonBase-root {
                    width: 100%!important;
                    padding: 0px!important;
                }
                `}
            </style>
        </>
    );
}
