"use client"
import variables from '/public/assets/css/step.module.scss'
import { useI18n } from '@/locales/client'
import { Formik, Field, Form } from 'formik';
import Select from '@mui/material/Select';
import MenuItem from '@mui/material/MenuItem';
import { useEffect, useState } from "react";
import FormControl from '@mui/material/FormControl';
import { useSelector } from "react-redux";
import { useAppSelector, useAppDispatch } from '@/redux/hooks'
import { addInfoTable, addStepTable, markStepReversed } from '@/redux/slices/cartSlice'
import Cookies from "js-cookie";
import { api } from "@/utils/axios";
import * as Yup from "yup";
import moment from 'moment';
import _, { set } from 'lodash'

const Step2 = (props: any) => {
    const {color} = props;
    const trans = useI18n();
    const workspaceId = useAppSelector((state) => state.workspaceData.globalWorkspaceId)
    const workspace = useAppSelector((state) => state.workspaceData.workspace)
    let tableOrderingCart: any = useAppSelector((state) => state.cart.data)
    const language = Cookies.get('Next-Locale') ?? 'nl';

    // Check logged token
    const tokenLoggedInCookie = Cookies.get('loggedToken');
    const [workspaceDataFinal, setWorkspaceDataFinal] = useState<any | null>(null);
    useEffect(() => {
        setTimeout(function () {
            workspaceId && api.get(`workspaces/` + workspaceId, {
                headers: {
                    'Authorization': `Bearer ${tokenLoggedInCookie}`,
                    'Content-Language': language
                }
            }).then(res => {
                const json = res.data;
                setWorkspaceDataFinal(json.data);
            }).catch(error => {
                // console.log(error)
            });
        }, 10);
    }, [workspaceId]);

    const [selectedValue, setSelectedValue] = useState('+32');
    const [numberPhone, setNumberPhone] = useState<any | null>();
    const [tableNumber, setTableNumber] = useState<any | null>(null);
    const [tableEmail, setTableEmail] = useState<any | null>(null);
    const [minGsm, setMinGsm] = useState(9);

    //message
    const [messageNumber, setMessageNumber] = useState<any | null>(false);
    const [messagePhone, setMessagePhone] = useState<any | null>(false);
    const [messageEmail, setMessageEmail] = useState<any | null>(false);

    const handlePhoneChange = (event: any) => {
        let newValue = event.target.value;
        if (newValue.startsWith('0')) {
            setMinGsm(10);
        } else {
            setMinGsm(9);
        }

        if (newValue.length >= minGsm) {
            setMessagePhone(false)
        }
        setNumberPhone(newValue);
    };

    const dispatch = useAppDispatch()
    const typeValue = useSelector((state: any) => state.cart.dataInfoTable);
    //check submit enable
    const [checkEnableSubmit, setCheckEnableSubmit] = useState<any | null>(false);

    const [statusEnableSelfService, setStatusEnableSelfService] = useState<any | null>(false);

    const handleEnableButton = () => {
        if(!statusEnableSelfService){
            if(tableNumber){
                setCheckEnableSubmit(true)
            } else {
                setCheckEnableSubmit(false)
            }
        }else{
            if (tableNumber !== null && tableNumber !== ''
                && numberPhone !== null && numberPhone && numberPhone !== '' && numberPhone.length >= minGsm) {
                setCheckEnableSubmit(true)
            } else {
                setCheckEnableSubmit(false)
            }
        }
    }

    useEffect(() => {
        handleEnableButton();
    }, [numberPhone]);

    useEffect(() => {
        handleEnableButton();
    }, [tableNumber]);

    useEffect(() => {
        handleEnableButton();
    }, [tableEmail]);

    const tableNumberFromCookie = Cookies.get('tableNumber') ?? '';

    useEffect(() => {
        if (typeValue) {
            setTableNumber(typeValue?.numberTable ?? tableNumberFromCookie)
            const updatedNumber = typeValue?.phoneNumberTable ? typeValue?.phoneNumberTable.replace(selectedValue, '') : "";
            setNumberPhone(updatedNumber)
            setTableEmail(typeValue?.emailTable)
        } else {
            setTableNumber(tableNumberFromCookie)            
        }
    }, [typeValue, tableNumberFromCookie]);

    useEffect(() => {
        _.get(workspace, 'data.extras', []).map((item: any) => {
            if (item?.type === 11) {
                if (item.active !== true) {
                    setStatusEnableSelfService(false)
                }else{
                    setStatusEnableSelfService(true)
                }
            }
        });
    }, [workspace]);

    useEffect(() => {
        handleEnableButton();
    }, []);

    const invalid = variables['invalid'];
    const validationSchema = Yup.object().shape({
        step_number: Yup.string().required(trans('required')),
        step_email: Yup.string().email(trans('job.message_invalid_email')),
    });

    return (
        <div className={variables.table_ordering_step2}>
            <h2>{trans('step2_title_cart')}</h2>

            <Formik
                initialValues={{
                    step_number: tableNumber ? tableNumber : '',
                    step_phone: numberPhone ? numberPhone : '',
                    step_email: tableEmail ? tableEmail : ''
                }}
                validationSchema={validationSchema}
                validateOnChange={false}
                validateOnBlur={false}
                enableReinitialize={true}
                onSubmit={async (values) => {
                    const DATE_FORMAT = 'YYYY-MM-DD';
                    const TIME_FORMAT = 'HH:mm';
                    // Validate available timeslot to go to step 3
                    const res = await api.get(`products/validate_available_timeslot?from=mobile&date=${moment().format(DATE_FORMAT)}&time=${moment().format(TIME_FORMAT)}&${tableOrderingCart.map((cartItem: any) => `product_id[]=${cartItem.productId}`).join('&')}`);
                    const available = res.data?.data || []
                    if (_.includes(available, false)) {
                        dispatch(markStepReversed(true))
                        dispatch(addStepTable(1))
                    } else {
                        if (checkEnableSubmit) {
                            if (values.step_number === '') {
                                setMessageNumber(true)
                                setCheckEnableSubmit(false)
                            } else {
                                setMessageNumber(false)
                            }
    
                            if (statusEnableSelfService) {
                                if (numberPhone.length < 9 || numberPhone.length === undefined) {
                                    setMessagePhone(true)
                                    setCheckEnableSubmit(false)
                                } else {
                                    setMessagePhone(false)
                                }
    
                                if (tableNumber !== null && numberPhone !== null && tableNumber !== '' && numberPhone !== '' && numberPhone?.length >= 9) {
                                    dispatch(addInfoTable(
                                        {
                                            numberTable: tableNumber,
                                            phoneNumberTable: `${selectedValue}${numberPhone || ''}`,
                                            emailTable: tableEmail,
                                            isLastOne: typeValue ? typeValue?.isLastOne : ''
                                        }
                                    ))
                                    dispatch(addStepTable(3))
                                }
                            } else {
                                setMessagePhone(false)
                                if (tableNumber !== null && numberPhone !== null && tableNumber !== '') {
                                    dispatch(addInfoTable(
                                        {
                                            numberTable: tableNumber,
                                            phoneNumberTable: null,
                                            emailTable: tableEmail,
                                            isLastOne: typeValue ? typeValue?.isLastOne : ''
                                        }
                                    ))
                                    dispatch(addStepTable(3))
                                }
                            }
                        }
                    }
                }}
            >
                {({ handleChange, handleBlur, values, errors, setFieldValue } : {handleChange: any, handleBlur: any, values: any, errors: any, setFieldValue: any}) => (
                    <Form>
                        <div className={variables.error_message}>{errors.step_number ?? ''}</div>
                        <Field id="step_number" 
                            className={`${errors.step_number ? invalid : ''}`}
                            name="step_number" 
                            type="number" 
                            value={tableNumber}
                            placeholder="0" 
                            onBlur={handleBlur}
                            onChange={(e: any) => {
                                errors.step_number ? errors.step_number = false : '';
                                const enteredValue = e.target.value;
                                setFieldValue('step_number', enteredValue);
                                setTableNumber(enteredValue);
                                handleChange(e);
                            }} 
                            style={{ color: workspaceDataFinal?.setting_generals?.primary_color ?? color, marginBottom: "23px" }}
                            inputMode="numeric" />

                        {
                            statusEnableSelfService &&
                            <>
                                {messagePhone && <div className={`${variables.mess_step2} ${variables.mess_step2_phone}`}>{trans('table-ordering-step2-mess-phone')}</div>}
                                <div className={`${variables.input_group} ${messagePhone && variables.messErrorStep2}`}>
                                    <FormControl fullWidth className={`${variables.input_group_item} ${messagePhone && variables.messErrorStep2}`}>
                                        <Select
                                            labelId="dropdown-label"
                                            id="dropdown"
                                            defaultValue="+32"
                                            value={selectedValue}
                                            renderValue={(value: any) => {
                                                if (value === '+32') {
                                                    return (
                                                        <>
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" shapeRendering="geometricPrecision" textRendering="geometricPrecision" imageRendering="optimizeQuality" fillRule="evenodd" clipRule="evenodd" viewBox="0 0 203.55 141.6"> <g fillRule="nonzero"> <path fill="#ED2939" d="M203.55 11.19v119.22c0 6.16-5.04 11.19-11.19 11.19H11.19C5.05 141.6.02 136.59 0 130.45V11.15C.02 5.01 5.05 0 11.19 0h181.17c6.15 0 11.19 5.03 11.19 11.19z" /> <path fill="#FAE042" d="M135.7 0v141.6H11.19C5.05 141.6.02 136.59 0 130.45V11.15C.02 5.01 5.05 0 11.19 0H135.7z" /> <path d="M67.85 0v141.6H11.19C5.05 141.6.02 136.59 0 130.45V11.15C.02 5.01 5.05 0 11.19 0h56.66z" /></g></svg>
                                                            <div className={`${variables.country}`}>+32</div>
                                                        </>
                                                    )
                                                } else {
                                                    return (
                                                        <>
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" shapeRendering="geometricPrecision" textRendering="geometricPrecision" imageRendering="optimizeQuality" fillRule="evenodd" clipRule="evenodd" viewBox="0 0 43.06 29.96"><g fillRule="nonzero"><path fill="#21468B" d="M43.06 20v7.59c0 1.3-1.06 2.37-2.37 2.37H2.37C1.06 29.96 0 28.89 0 27.59V20h43.06z" /><path fill="#fff" d="M43.06 20H0V2.37C0 1.06 1.06 0 2.37 0h38.32c1.31 0 2.37 1.06 2.37 2.37V20z" /><path fill="#AE1C28" d="M43.06 9.96H0V2.37C0 1.06 1.06 0 2.37 0h38.32c1.31 0 2.37 1.06 2.37 2.37v7.59z" /></g></svg>
                                                            <div className={`${variables.country}`}>+31</div>
                                                        </>
                                                    )
                                                }
                                            }}
                                        >
                                            <MenuItem value="+32" selected={selectedValue == '+32'} className={`${variables.menuItem}`}>
                                                <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" shapeRendering="geometricPrecision" textRendering="geometricPrecision" imageRendering="optimizeQuality" fillRule="evenodd" clipRule="evenodd" viewBox="0 0 203.55 141.6" > <g fillRule="nonzero"> <path fill="#ED2939" d="M203.55 11.19v119.22c0 6.16-5.04 11.19-11.19 11.19H11.19C5.05 141.6.02 136.59 0 130.45V11.15C.02 5.01 5.05 0 11.19 0h181.17c6.15 0 11.19 5.03 11.19 11.19z" /> <path fill="#FAE042" d="M135.7 0v141.6H11.19C5.05 141.6.02 136.59 0 130.45V11.15C.02 5.01 5.05 0 11.19 0H135.7z" /> <path d="M67.85 0v141.6H11.19C5.05 141.6.02 136.59 0 130.45V11.15C.02 5.01 5.05 0 11.19 0h56.66z" /></g></svg>
                                                <div className={`${variables.country} font-bold`} style={{ fontSize: '12px', marginLeft: '3px' }}>+32</div>
                                            </MenuItem>
                                            <MenuItem value="+31" selected={selectedValue == '+31'}>
                                                <div className={`${variables.flex} d-flex`} style={{ display: 'flex', alignItems: 'center' }}>
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" shapeRendering="geometricPrecision" textRendering="geometricPrecision" imageRendering="optimizeQuality" fillRule="evenodd" clipRule="evenodd" viewBox="0 0 43.06 29.96"><g fillRule="nonzero"><path fill="#21468B" d="M43.06 20v7.59c0 1.3-1.06 2.37-2.37 2.37H2.37C1.06 29.96 0 28.89 0 27.59V20h43.06z" /><path fill="#fff" d="M43.06 20H0V2.37C0 1.06 1.06 0 2.37 0h38.32c1.31 0 2.37 1.06 2.37 2.37V20z" /><path fill="#AE1C28" d="M43.06 9.96H0V2.37C0 1.06 1.06 0 2.37 0h38.32c1.31 0 2.37 1.06 2.37 2.37v7.59z" /></g></svg>
                                                    <div className={`${variables.country} font-bold`} style={{ fontSize: '12px', marginLeft: '3px' }}>+31</div></div>
                                            </MenuItem>
                                        </Select>
                                    </FormControl>
                                    <Field id="step_phone" maxLength={16} name="step_phone" type="number" value={numberPhone} onChange={(e: any) => {
                                        handlePhoneChange(e);
                                        handleEnableButton();
                                    }} placeholder={trans('contact-gsm')} inputMode="numeric" />
                                </div>
                                <p>{trans('step2_text1')}</p>
                            </>
                        }

                        <div className={variables.error_message}>{errors.step_email ?? ''}</div>
                        <Field
                            id="step_email"
                            className={`${errors.step_email ? invalid : ''} mt-0`}
                            name="step_email"
                            placeholder={trans('step2_email')}
                            value={tableEmail}
                            onBlur={handleBlur}
                            onChange={(e: any) => {
                                errors.step_email ? errors.step_email = false : '';
                                const newValue = e.target.value;
                                setFieldValue('step_email', newValue);
                                setTableEmail(newValue);
                                handleChange(e);
                            }}
                        />

                        <p>{trans('step2_text2')}</p>
                        <button type="submit"
                            disabled={(errors.step_number || errors.step_email || (statusEnableSelfService && messagePhone)) ? true : false}
                            className={
                                (errors.step_number || errors.step_email || !values.step_number || !checkEnableSubmit || (statusEnableSelfService && messagePhone))
                                ? variables.btn_disable
                                : variables['active-btn']
                            }>
                                {trans('step2_button')}
                        </button>
                    </Form>
                )}
            </Formik>
        </div>
    )
}

export default Step2;
