'use client'

import {useFormik} from "formik";
import * as Yup from "yup";
import {useSubmitJobMutation} from '@/redux/services/workspace/workspaceJobApi';
import {useI18n} from '@/locales/client'
import React, {useEffect, useState} from "react";
import {ToastContainer, toast, Slide} from 'react-toastify';
import 'react-toastify/dist/ReactToastify.css';
import Select from "@mui/material/Select";
import MenuItem from "@mui/material/MenuItem";
import variables from '/public/assets/css/profile.module.scss'
import {VALIDATION_PHONE_MAX} from "@/config/constants";
import { useAppSelector } from '@/redux/hooks'

export default function JobForm({apiColor}: any) {
    const trans = useI18n();
    const workspaceId = useAppSelector((state) => state.workspaceData.globalWorkspaceId)
    const [isSubmitClicked, setIsSubmitClicked] = useState(false);
    const [selectedCountry, setSelectedCountry] = useState('+32'); // Initial value for the country select
    const [minGsm, setMinGsm] = useState(9);
    const separateLength = 6;
    const phoneMin = 13; // 11 and "+", "/"
    const phoneMax = 20; // 18 and "+", "/"

    const validationSchema = Yup.object().shape({
        name: Yup.string().required(trans('fill-all')),
        email: Yup.string().required(trans('fill-all'))
            .email(trans('job.message_invalid_email')),
        phone: Yup.string().required(trans('fill-all'))
            .min(minGsm, trans('job.message_format_gsm')),
        content: Yup.string().required(trans('fill-all')),
    });

    const [submitJob, {
        isLoading: logLoading,
        isError: logError,
        error
    }] = useSubmitJobMutation();

    const handleSubmitClick = () => {
        if (formik.values.phone.startsWith('0')) {
            setMinGsm(9);
            formik.values.phone = formik.values.phone.substring(1);
            formik.setFieldValue('phone', formik.values.phone);
        }
        setIsSubmitClicked(true);
    }

    const formik = useFormik({
        initialValues: {
            name: "",
            email: "",
            phone: "",
            content: "",
            workspace_id: "",
        },
        validationSchema,
        validateOnChange: false,
        validateOnBlur: false,
        // validateOnMount: true,
        onSubmit: async (values, {resetForm}) => {
            values.workspace_id = workspaceId;
            var countryCode = selectedCountry.replace(/[/+]/g, '');
            var phoneNumber = '+'+ countryCode + values.phone;
            values.phone = phoneNumber;
            setIsSubmitClicked(true);
            const apiData = await submitJob(values);

            if ('data' in apiData) {
                const response = apiData.data;

                if (response.success) {
                    toast.dismiss();
                    toast(response.message, {
                        position: toast.POSITION.BOTTOM_CENTER,
                        autoClose: 1500,
                        hideProgressBar: true,
                        closeOnClick: true,
                        closeButton: false,
                        transition: Slide,
                        className: 'message',
                    });
                } else {
                    toast(response.message || trans('failed'), {
                        position: toast.POSITION.BOTTOM_CENTER,
                        autoClose: 1500,
                        hideProgressBar: true,
                        closeOnClick: true,
                        closeButton: false,
                        transition: Slide,
                        className: 'message',
                    });
                }
            } else {
                toast(trans('failed'), {
                    position: toast.POSITION.BOTTOM_CENTER,
                    autoClose: 1500,
                    hideProgressBar: true,
                    closeOnClick: true,
                    closeButton: false,
                    transition: Slide,
                    className: 'message',
                });
            }
            resetForm();
        },
    });

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
        formik.setFieldValue('phone', newValue);
    };

    useEffect(() => {

        if (formik.errors && Object.keys(formik.errors).length > 0) {
            let firstErrKey = Object.keys(formik.errors)[0];
            let firstErrValue = formik.errors[firstErrKey as keyof typeof formik.errors] || trans('failed');

            toast(firstErrValue, {
                position: toast.POSITION.BOTTOM_CENTER,
                autoClose: 1500,
                hideProgressBar: true,
                closeOnClick: true,
                closeButton: false,
                transition: Slide,
                className: 'message',
            });
        }

    }, [formik.errors]);

    const handleCountryChange = (event: any) => {
        let gsmValue = formik.values.phone

        formik.setFieldValue('phone', gsmValue);
        setSelectedCountry(event.target.value);
    };
    const [open, setOpen] = useState(false);

    return (
        <>
            <form onSubmit={formik.handleSubmit} noValidate>
                <div className="container-fluid">
                    <div className="row mb-3">
                        <div className="col-12">
                            <input type="text" name="name"
                               onChange={formik.handleChange}
                               onBlur={formik.handleBlur}
                               value={formik.values.name}
                               className={`form-control ${formik.errors.name ? "form-control-custom-invalid" : "form-control-custom"}`}
                               placeholder={trans('job.full_name')}/>
                        </div>
                    </div>

                    <div className="row mb-3">
                        <div className="col-12">
                            <input type="email" name="email"
                               onChange={formik.handleChange}
                               onBlur={formik.handleBlur}
                               value={formik.values.email}
                               className={`form-control ${formik.errors.email ? "form-control-custom-invalid" : "form-control-custom"}`}
                               placeholder={trans('job.email')}/>
                        </div>
                    </div>

                    <div className="row mb-3">
                        <div className="col-12" style={{position:'relative'}}>
                            <input type="text" name="phone"
                               style={{paddingLeft: '100px'}}
                               onChange={handleGsmChange}
                               onBlur={formik.handleBlur}
                               value={formik.values.phone}
                               className={`form-control ${formik.errors.phone ? "form-control-custom-invalid" : "form-control-custom"}`}
                               placeholder={trans('job.phone')}/>
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
                                style={{position:'absolute', top:0, background:'#e6e6e6'}}
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
                        </div>
                    </div>

                    <div className="row mb-3">
                        <div className="col-12">
                            <textarea name="content"
                                onChange={formik.handleChange}
                                onBlur={formik.handleBlur}
                                value={formik.values.content} rows={4}
                                className={`form-control ${formik.errors.content ? "form-control-custom-invalid" : "form-control-custom"}`}
                                placeholder={trans('job.content')}/>
                        </div>
                    </div>

                    <div className="row mb-3">
                        <div className="col-12 text-center">
                            <button type="submit" onClick={handleSubmitClick}
                                 className="btn btn-primary btn-submit">{trans('job.btn_apply')}</button>
                        </div>
                    </div>
                </div>
            </form>

            <ToastContainer/>
            <style>{`
                .MuiSelect-select {
                    padding: 13px 32px 12px 5px;
                }`}
            </style>
        </>
    )
}


