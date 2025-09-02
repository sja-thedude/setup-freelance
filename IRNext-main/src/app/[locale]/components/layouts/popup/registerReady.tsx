'use client'
import React, { useEffect, useState } from 'react';
import { Button, Modal } from 'react-bootstrap';
import 'public/assets/css/popup.scss';
import { useI18n } from '@/locales/client'
import variables from '/public/assets/css/register.module.scss'
import { useAppDispatch } from '@/redux/hooks'

export default function RegisterReady({ toggleReadyPopup }: { toggleReadyPopup: any }) {
    const [show, setShow] = useState(true);
    const dispatch = useAppDispatch();
    const handleClose = () => {
        toggleReadyPopup()
        setShow(false);
    };
    const btnDark = `btn btn-dark ${variables['btn-dark-register-portal']}`;
    const handleShow = () => setShow(true);
    const trans = useI18n()

    return (
        <>
            <Modal show={show} onHide={handleClose}
                aria-labelledby="contained-modal-title-vcenter"
                centered id='portal-register-ready'
            >
                <Modal.Body>
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" className={`${variables.registerPortalQuit}`} onClick = {handleClose} style={{position: 'absolute' , right: '10px'}}>
                        <path d="M18 6L6 18" stroke="black" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" />
                        <path d="M6 6L18 18" stroke="black" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" />
                    </svg>
                    <div className='row mb-4 mt-4' style={{ marginTop: "15%" }}>
                        <div className='col-sm-12 col-xs-12' style={{ margin: "auto" }}> <h1 className={`${variables.registerPortal} ms-3`}>{trans('register-almost')}</h1></div>
                    </div>

                    <div className='row'>
                        <svg xmlns="http://www.w3.org/2000/svg" width="111" height="101" viewBox="0 0 111 101" fill="none" className='mb-5'>
                            <path d="M15.6931 38.3904C14.5981 42.3356 14.0447 46.4087 14.0479 50.5C14.0479 75.6297 34.6314 96 60.0241 96C85.4168 96 106 75.6297 106 50.5C106 25.3704 85.4206 5.00004 60.0241 5.00004C47.9294 4.98448 36.3188 9.70078 27.7229 18.121" stroke="#B5B268" strokeWidth="10" strokeMiterlimit="22.93" strokeLinejoin="round" />
                            <path fillRule="evenodd" clipRule="evenodd" d="M0 49.0886H29.434L14.7177 30.5111L0 49.0886Z" fill="#B5B268" />
                            <path d="M38.793 43.8031L60.1159 70.2685L102.826 11.9745" stroke="#B5B268" strokeWidth="10" strokeMiterlimit="22.93" strokeLinejoin="round" />
                            <path d="M38.793 43.8031L60.1159 70.2685L102.826 11.9745" stroke="#B5B268" strokeWidth="10" strokeMiterlimit="22.93" strokeLinejoin="round" />
                        </svg>
                        <div className={`${variables.titlePortal} row mb-2 mt-1 d-flex justify-content-center`}>
                            <div className='col-8'><p>{trans('register-check')}</p></div>
                        </div>
                        <div className={`d-flex justify-content-center`}>
                            <button type="button" className={btnDark} onClick={handleClose}>{trans('portal.back-to-its')}</button>
                        </div>
                    </div>
                </Modal.Body>
            </Modal>
        </>
    );
}
