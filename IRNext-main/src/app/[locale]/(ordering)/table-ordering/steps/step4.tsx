"use client"

import variables from '/public/assets/css/step.module.scss'
import { useI18n } from '@/locales/client'
import RadioGroup from '@mui/material/RadioGroup';
import Radio from '@mui/material/Radio';
import { styled } from '@mui/material/styles';
import FormControlLabel from '@mui/material/FormControlLabel';
import Typography from "@mui/material/Typography";
import React, { useEffect, useState } from 'react'
import { api } from "@/utils/axios";
import Cookies from "js-cookie";
import { useSelector } from "react-redux";
import useValidateSecurity from "@/hooks/useTableSelfOrderingSecurity";
import InvalidSecurity from "@/app/[locale]/components/404/invalid-security";

const Step4 = ({ color, updateStep, workspaceId }: any) => {
    const language = Cookies.get('Next-Locale') ?? 'nl';
    const trans = useI18n();
    const [value, setValue] = useState(null);
    const typeValue = useSelector((state: any) => state.cart.dataInfoTable);
    const handleChange = (event: any) => {
        setValue(event.target.value);
        // Your logic for handling the change goes here
    };

    const PAYMENT_METHOD_TYPE = {
        MOLLIE: 0,
        INVOICE: 1,
        CASH: 2
    };

    const tokenLoggedInCookie = Cookies.get('loggedToken');
    const BpIcon = styled('span')(({ theme }) => ({
        borderRadius: '50%',
        width: 20,
        height: 20,
        boxShadow:
            theme.palette.mode === 'dark'
                ? '0 0 0 1px rgb(16 22 26 / 40%)'
                : 'inset 0 0 0 2px #4040409e',
        backgroundColor: '#F6F6F6',
        backgroundImage:
            theme.palette.mode === 'dark'
                ? 'linear-gradient(180deg,hsla(0,0%,100%,.05),hsla(0,0%,100%,0))'
                : 'linear-gradient(180deg,hsla(0,0%,100%,.8),hsla(0,0%,100%,0))',
        '.Mui-focusVisible &': {
            outline: '2px auto rgba(19,124,189,.6)',
            outlineOffset: 2,
        },
        'input:hover ~ &': {
            backgroundColor: theme.palette.mode === 'dark' ? '#30404d' : '#ebf1f5',
        },
        'input:disabled ~ &': {
            boxShadow: 'none',
            background:
                theme.palette.mode === 'dark' ? 'rgba(57,75,89,.5)' : 'rgba(206,217,224,.5)',
        },
    }));

    const BpCheckedIcon = styled(BpIcon)({
        backgroundColor: color,
        backgroundImage: 'linear-gradient(180deg,hsla(0,0%,100%,.1),hsla(0,0%,100%,0))',
        boxShadow: 'inset 0 0 0 0px #4040409e',
        '&:before': {
            display: 'block',
            width: 20,
            height: 20,
            backgroundImage: 'url("data:image/svg+xml;utf8,<svg xmlns=\'http://www.w3.org/2000/svg\' width=\'22\' height=\'22\' viewBox=\'0 0 22 22\' fill=\'none\'><path d=\'M17 6.5L8.75 14.5L5 10.8636\' stroke=\'white\' strokeWidth=\'2\' strokeLinecap=\'round\' strokeLinejoin=\'round\'/></svg>")',
            content: '""',
        },
        'input:hover ~ &': {
            backgroundColor: color,
        },
    });

    // Inspired by blueprintjs
    function BpRadio(props: any) {
        return (
            <Radio
                disableRipple
                color="default"
                checkedIcon={<BpCheckedIcon />}
                icon={<BpIcon />}
                {...props}
            />
        );
    }

    const [paymentMethods, setPaymentMethods] = useState<any>([]);

    useEffect(() => {
        const fetchOrderData = api.get(`workspaces/${workspaceId}/settings/payment_methods`, {
            headers: {
                Authorization: 'Bearer ' + tokenLoggedInCookie,
                'Content-Language': language
            }
        }).then((res: any) => {
            setPaymentMethods(res?.data?.data?.data.filter((item: any) => item.type != PAYMENT_METHOD_TYPE.INVOICE && item.in_house == true));
        }).catch((err) => {

        });
    }, [workspaceId]);

    const handleNext = () => {
        //
    }

    const validateSecurity = useValidateSecurity();
    if (!validateSecurity) {
        return (<InvalidSecurity />);
    }

    return (
        <>
            <div className="row d-block d-md-none mt-5">
                <div className="col-sm-12 col-12">
                    <div className={`${variables.step3Title}`}>
                        {trans('payment-method')}
                    </div>
                </div>
            </div>
            <div className="row">
                <div className={`col-sm-12 col-12 payment-selection p-0`}>
                    <RadioGroup
                        aria-labelledby="demo-radio-buttons-group-label"
                        name="radio-buttons-group"
                        value={value}
                        onChange={handleChange}
                    >
                        {
                            paymentMethods.map((item: any, index: number) => (
                                item.type == 0 ?
                                    <div key={index} className={`payment-item-group-table`} style={value == 0 ? { background: '#F5F5F5' } : {}}>
                                        <FormControlLabel value={0} control={<BpRadio />}
                                            label={
                                                <>
                                                    <Typography className={`payment-item-label`} style={value == 0 ? { color: color } : {}}>{trans('online')}</Typography>
                                                    <Typography className={`sub-payment-item-label res-mobile`}>{trans('choose-online-method')}</Typography>
                                                    <Typography className={`sub-payment-item-label res-desktop`}>{trans('choose-online-method-desktop')}</Typography>
                                                </>
                                            } />
                                    </div>
                                    : item.type == 2 ?
                                        <div key={index} className={`payment-item-group-table`} style={value == 2 ? { background: '#F5F5F5' } : {}}>
                                            <FormControlLabel value={2} control={<BpRadio />}
                                                label={
                                                    <>
                                                        <Typography className={`payment-item-label res-mobile`} style={value == 2 ? { color: color } : {}}>{trans('cash')}</Typography>
                                                        <Typography className={`payment-item-label res-desktop`} style={value == 2 ? { color: color } : {}}>{trans('pay-cash')}</Typography>
                                                        <Typography className={`sub-payment-item-label res-mobile`}>{trans('cash-payment')}</Typography>
                                                        <Typography className={`sub-payment-item-label res-desktop`}>{trans('cash-payment-desktop')}</Typography>
                                                    </>
                                                } />
                                        </div>
                                        : <div key={index} className={`payment-item-group`} style={value == 3 ? { background: '#F5F5F5' } : {}}>
                                            <FormControlLabel value={3} control={<BpRadio />}
                                                label={
                                                    <>
                                                        <Typography className={`payment-item-label`} style={value == 3 ? { color: color } : {}}>{trans('on-invoice')}</Typography>
                                                        <Typography className={`sub-payment-item-label res-mobile`}>{trans('receive-invoice')}</Typography>
                                                        <Typography className={`sub-payment-item-label res-desktop`}>{trans('receive-invoice-desktop')}</Typography>
                                                    </>
                                                } />
                                        </div>
                            ))
                        }
                    </RadioGroup>
                </div>
            </div>

            <div className="row" style={{marginTop: '70px' , marginBottom: '50px'}}>
                <div className="col-sm-12 col-12 text-center">
                    {
                        value ? (
                            <button className={`itr-btn-primary ${variables['next-step-btn-step3']}`} style={{ background: '#413E38' }} onClick={() => handleNext()} type="button" >{trans('cart.further')}</button>
                        ) : (
                            <button className={`itr-btn-primary ${variables['next-step-btn-step3']}`} style={{ background: 'rgba(65, 62, 56, 0.50)', color: "white" }} type="button" onClick={handleNext}>{trans('cart.further')}</button>
                        )
                    }
                </div>
            </div>
        </>
    )
}

export default Step4;
