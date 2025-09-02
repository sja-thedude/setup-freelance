"use client"

import variables from '/public/assets/css/step.module.scss'
import { useI18n } from '@/locales/client'
import { Formik, Field, Form } from 'formik';
import Select from '@mui/material/Select';
import MenuItem from '@mui/material/MenuItem';
import {useEffect, useState} from "react";
import FormControl from '@mui/material/FormControl';
import { useSelector } from "react-redux";
import { useAppSelector, useAppDispatch } from '@/redux/hooks'
import { addInfoTable } from '@/redux/slices/cartSlice'
import Cookies from "js-cookie";
import {api} from "@/utils/axios";
import useValidateSecurity from "@/hooks/useTableSelfOrderingSecurity";
import InvalidSecurity from "@/app/[locale]/components/404/invalid-security";

const Step2 = ({updateStep} : any) => {
    const trans = useI18n();
    const workspaceId = useAppSelector((state) => state.workspaceData.globalWorkspaceId)
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
        }, 1000);
    }, [workspaceId]);

    const [selectedValue, setSelectedValue] = useState('+32');
    const [numberPhone, setNumberPhone] = useState<any | null>(null);
    const [tableNumber, setTableNumber] = useState<any | null>(null);
    const [tableEmail, setTableEmail] = useState<any | null>(null);

    //message
    const [messageNumber, setMessageNumber] = useState<any | null>(false);
    const [messagePhone, setMessagePhone] = useState<any | null>(false);
    const [messageEmail, setMessageEmail] = useState<any | null>(false);

    const handleChange = (event: any) => {
        // setSelectedValue(event.target.value);
        if (event.target.value === '+31') {
            setSelectedValue('+31');
            setNumberPhone(6);
        } else {
            setSelectedValue('+32');
            setNumberPhone(4);
        }
    };

    const handlePhoneChange = (event: any) => {
        let newValue = event.target.value;
        setNumberPhone(newValue);
    };

    const dispatch = useAppDispatch()
    const typeValue = useSelector((state: any) => state.cart.dataInfoTable);
    //check submit enable
    const [checkEnableSubmit, setCheckEnableSubmit] = useState<any | null>(false);

    const handleEnableButton = () => {
        if(tableNumber !== null && tableEmail !== null && numberPhone !== null && tableNumber !== '' && tableEmail !== '' && numberPhone !== '') {
            setCheckEnableSubmit(true)
        }else{
            setCheckEnableSubmit(false)
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

    const [statusEnableSelfService, setStatusEnableSelfService] = useState<any | null>(true);
    useEffect(() => {
     if(typeValue && typeValue.length > 0){
            setTableNumber(typeValue[0].numberTable)
            const updatedNumber = typeValue[0].phoneNumberTable.replace(selectedValue, '');
            setNumberPhone(updatedNumber)
            setTableEmail(typeValue[0].emailTable)
     }
    }, [typeValue]);

    useEffect(() => {
        workspaceDataFinal?.extras.map((item: any) => {
            if(item?.type === 11){
                if(item.active !== true){
                    setStatusEnableSelfService(false)
                }
            }
        });
    }, [workspaceDataFinal]);

    const isEmailValid = (email: any) => {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return emailRegex.test(email);
    };

    const validateSecurity = useValidateSecurity();
    if (!validateSecurity) {
        return (<InvalidSecurity />);
    }

    return(
        <>
            <div className={variables.table_ordering_step2}>
                <h2>{trans('step2_title_cart')}</h2>

                <Formik
                    initialValues={{
                        step_number: '',
                        step_phone: '',
                        step_email: '',
                    }}
                    onSubmit={(values) => {
                        if(checkEnableSubmit){
                            if(values.step_number === '') {
                                setMessageNumber(true)
                            }else{
                                setMessageNumber(false)
                            }

                            if(!statusEnableSelfService){
                                if(numberPhone.length < 9 || numberPhone.length === undefined) {
                                    setMessagePhone(true)
                                }else{
                                    setMessagePhone(false)
                                }
                            }else{
                                setMessagePhone(false)
                            }

                            if(values.step_email === '') {
                                setMessageEmail(true)
                            }else if(!isEmailValid(values.step_email)) {
                                setMessageEmail(true)
                            }else{
                                setMessageEmail(false)
                            }

                            if(tableNumber !== null && tableEmail !== null && numberPhone !== null && tableNumber !== '' && tableEmail !== '' && numberPhone !== ''&& numberPhone?.length >= 9 && isEmailValid(tableEmail)) {
                                dispatch(addInfoTable(
                                    {
                                        numberTable: tableNumber,
                                        phoneNumberTable: `${selectedValue}${numberPhone || ''}`,
                                        emailTable: tableEmail,
                                        isLastOne: typeValue && typeValue.length > 0 ? typeValue?.isLastOne : ''
                                    }
                                ))
                                updateStep(3)
                            }
                        }
                    }}
                >
                    {({ values, setFieldValue }) => (
                    <Form>
                        <Field id="step_number" name="step_number" value={tableNumber} placeholder="0" onChange={(e: any) => {
                            setFieldValue('step_number', e.target.value);
                            setTableNumber(e.target.value);
                        }}/>

                        {
                            !statusEnableSelfService &&
                            <>
                                { messagePhone && <div className={`${variables.mess_step2} ${variables.mess_step2_phone}`}>{trans('table-ordering-step2-mess-phone')}</div>}
                                <div className={`${variables.input_group} ${messagePhone && variables.messErrorStep2}`}>
                                    <FormControl fullWidth className={`${variables.input_group_item} ${messagePhone && variables.messErrorStep2}`}>
                                        <Select
                                            labelId="dropdown-label"
                                            id="dropdown"
                                            defaultValue="+32"
                                            value={selectedValue}
                                            onChange={handleChange}
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
                                                <div className={`${variables.country} font-bold`} style={{fontSize: '12px', marginLeft:'3px'}}>+32</div>
                                            </MenuItem>
                                            <MenuItem value="+31" selected={selectedValue == '+31'}>
                                                <div className={`${variables.flex} d-flex`} style={{ display: 'flex', alignItems: 'center' }}>
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" shapeRendering="geometricPrecision" textRendering="geometricPrecision" imageRendering="optimizeQuality" fillRule="evenodd" clipRule="evenodd" viewBox="0 0 43.06 29.96"><g fillRule="nonzero"><path fill="#21468B" d="M43.06 20v7.59c0 1.3-1.06 2.37-2.37 2.37H2.37C1.06 29.96 0 28.89 0 27.59V20h43.06z" /><path fill="#fff" d="M43.06 20H0V2.37C0 1.06 1.06 0 2.37 0h38.32c1.31 0 2.37 1.06 2.37 2.37V20z" /><path fill="#AE1C28" d="M43.06 9.96H0V2.37C0 1.06 1.06 0 2.37 0h38.32c1.31 0 2.37 1.06 2.37 2.37v7.59z" /></g></svg>
                                                    <div className={`${variables.country} font-bold`} style={{fontSize: '12px', marginLeft:'3px'}}>+31</div></div>
                                            </MenuItem>
                                        </Select>
                                    </FormControl>
                                    <Field id="step_phone" maxLength={16} name="step_phone" value={numberPhone} onChange={(e: any) => {
                                        handlePhoneChange(e);
                                        handleEnableButton();
                                    }} placeholder={trans('contact-gsm')}/>
                                </div>
                            </>
                        }

                        <p>{trans('step2_text1')}</p>

                        { messageEmail && <div className={`${variables.mess_step2} ${variables.mess_step2_email}`}>{trans('table-ordering-step2-mess-email')}</div> }
                        <Field
                            id="step_email"
                            name="step_email"
                            placeholder={trans('step2_email')}
                            type=""
                            class={`${messageEmail && variables.messErrorStep2}`}
                            value={tableEmail}
                            onChange={(e: any) => {
                                const newValue = e.target.value;
                                setFieldValue('step_email', newValue);
                                setTableEmail(newValue);
                            }}
                        />

                        <p>{trans('step2_text2')}</p>

                        <button type="submit" className={`${!checkEnableSubmit && variables.btn_disable}`}>
                            {trans('step2_button')}
                        </button>
                    </Form>
                    )}
                </Formik>
            </div>
        </>
    )
}

export default Step2;
