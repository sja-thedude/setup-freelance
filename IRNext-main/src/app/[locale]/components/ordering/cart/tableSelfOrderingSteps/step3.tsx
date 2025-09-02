"use client"
import variables from '/public/assets/css/step.module.scss'
import { useI18n } from '@/locales/client'
import { TextField } from '@mui/material';
import { use, useState, useEffect } from "react";
import RadioGroup from '@mui/material/RadioGroup';
import Radio from '@mui/material/Radio';
import { styled } from '@mui/material/styles';
import FormControlLabel from '@mui/material/FormControlLabel';
import Typography from "@mui/material/Typography";
import { addInfoTable, addStepTable, markStepReversed } from '@/redux/slices/cartSlice'
import { useSelector } from "react-redux";
import { useAppSelector, useAppDispatch } from '@/redux/hooks'
import { api } from "@/utils/axios";
import _, { set } from 'lodash'
import moment from 'moment';

const Step3 = ({ color }: any) => {
    let tableOrderingCart: any = useAppSelector((state) => state.cart.data)
    const typeValue = useSelector((state: any) => state.cart.dataInfoTable);
    const trans = useI18n();
    const [value, setValue] = useState('');
    useEffect(() => {
        setValue(typeValue?.isLastOne)
    }, [typeValue])
    const dispatch = useAppDispatch()
    const handleChange = (event: any) => {
        setValue(event.target.value);
        // Your logic for handling the change goes here
    };
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

    const handleNext = async () => {
        const DATE_FORMAT = 'YYYY-MM-DD';
        const TIME_FORMAT = 'HH:mm';
        // Validate available timeslot to go to step 3
        const res = await api.get(`products/validate_available_timeslot?from=mobile&date=${moment().format(DATE_FORMAT)}&time=${moment().format(TIME_FORMAT)}&${tableOrderingCart.map((cartItem: any) => `product_id[]=${cartItem.productId}`).join('&')}`);
        const available = res.data?.data || []
        if (_.includes(available, false)) {
            dispatch(markStepReversed(true))
            dispatch(addStepTable(1))
        } else {
            dispatch(addInfoTable(
                {
                    numberTable: typeValue?.numberTable,
                    phoneNumberTable: typeValue?.phoneNumberTable,
                    emailTable: typeValue?.emailTable,
                    isLastOne: value
                }
            ))
            dispatch(addStepTable(4))
        }
    }

    return (
        <>
            <div className="row" style={{ marginTop: '100px', paddingTop: '2px' }}>
                <div className="col-sm-12 col-12">
                    <div className={`${variables.step3Title}`}>
                        {trans('step_confirmation')}
                    </div>
                </div>
            </div>
            <div className={`${variables.step3Contain} row`}>
                <div className={`col-sm-12 col-12 payment-selection p-0`}>
                    <RadioGroup
                        aria-labelledby="demo-radio-buttons-group-label"
                        name="radio-buttons-group"
                        value={value}
                        onChange={handleChange}
                    >
                        {
                            <>
                                <div className={`payment-item-group-table`} style={{ background: '#F5F5F5' }}>
                                    <FormControlLabel value={1} control={<BpRadio />}
                                        label={
                                            <>
                                                <Typography className='step3-title-field'>{trans('step3-1-1')} <b>{trans('lastest')}</b> {trans('step3-2')} <b>{trans('complete')}</b></Typography>
                                            </>
                                        }
                                    />
                                </div>
                                <div className={`payment-item-group-table`} style={{ background: '#F5F5F5' }}>
                                    <FormControlLabel value={0} control={<BpRadio />}
                                        label={
                                            <>
                                                <Typography className='step3-title-field'>{trans('step3-1-2')} <b>{trans('not')}</b> {trans('step3-3')} <b>{trans('not-complete')}</b></Typography>
                                            </>
                                        }
                                    />
                                </div>
                            </>
                        }

                    </RadioGroup>
                </div>
            </div >
            <div className="row" style={{ marginTop: '31px', marginBottom: '38px' }}>
                <div className="col-sm-12 col-12 text-center">
                    {
                        value !== '' ? (
                            <button className={`itr-btn-primary ${variables['next-step-btn-step3']}`} style={{ background: '#413E38' }} onClick={() => handleNext()} type="button" >{trans('cart.further')}</button>
                        ) : (
                            <button className={`itr-btn-primary ${variables['next-step-btn-step3']}`}
                                style={{ background: 'rgba(65, 62, 56, 0.50)', color: "white" }} type="button" onClick={handleNext}>{trans('cart.further')}</button>
                        )
                    }

                </div>
            </div>
        </>
    )
}

export default Step3;
