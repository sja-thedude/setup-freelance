'use client'

import style from 'public/assets/css/profile.module.scss'
import React, { useState, useEffect } from 'react';
import { useI18n } from '@/locales/client';
import { useRouter } from "next/navigation";
import 'react-toastify/dist/ReactToastify.css';
import { Button } from '@mui/material';
import { Modal } from "react-bootstrap";
import { useAppSelector } from '@/redux/hooks'
import { useGetWorkspaceDataByIdQuery } from '@/redux/services/workspace/workspaceDataApi'

export default function RegisterConfirm({ togglePopup }: { togglePopup: any }) {
    const workspaceId = useAppSelector((state) => state.workspaceData.globalWorkspaceId)
    const { data: apiDataToken } = useGetWorkspaceDataByIdQuery({id: workspaceId})
    const apiData = apiDataToken?.data?.setting_generals;
    const color = useAppSelector((state) => state.workspaceData.globalWorkspaceColor)
    const router = useRouter();
    const trans = useI18n();
    const [show, setShow] = useState(false);
    const handleClose = () => {
        togglePopup();
        setShow(false);
        const query = new URLSearchParams(window.location.search);
        if (query.get('registerConfirm') === 'true') {
            router.push(window.location.href.replace('&registerConfirm=true', ''));
            router.push(window.location.href.replace('?registerConfirm=true', ''));
        }
    };

    const handleShow = () => setShow(true);

    useEffect(() => {
        // const hasShownPopup = localStorage.getItem('hasShownPopup');
        const hasShownPopup = false;

        if (!hasShownPopup) {
            setShow(true);
            localStorage.setItem('hasShownPopup', 'true');
        }
    }, []);

    return (
        <>
            <Button onClick={handleShow} style={{ display: 'none' }}></Button>
            <Modal show={show} onHide={handleClose}
                   animation={false}
                    id='modal-profile'
            >
                <Modal.Body>
                    <div className="close-popup text-828282" onClick={() => {
                        router.push( "/?login=true");
                        handleClose()
                    }}>
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="25" viewBox="0 0 24 25" fill="none">
                            <path d="M14 17L10 12.5L14 8" stroke="#808080" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"/>
                        </svg>
                        <div className="mt-1">{ trans('back') }</div>
                    </div>
                    <div className="text-center" style={{marginTop: '180px'}}>
                        <svg xmlns="http://www.w3.org/2000/svg" width="134" height="134" viewBox="0 0 134 134" fill="none">
                            <path d="M122.833 61.8634V67.0001C122.827 79.0401 118.928 90.7553 111.719 100.399C104.51 110.042 94.3768 117.096 82.8308 120.51C71.2849 123.924 58.9448 123.514 47.6509 119.341C36.357 115.169 26.7144 107.457 20.1613 97.357C13.6082 87.2566 10.4956 75.3084 11.2878 63.2945C12.08 51.2805 16.7345 39.8445 24.5571 30.692C32.3798 21.5395 42.9515 15.1609 54.6955 12.5075C66.4395 9.85414 78.7266 11.0681 89.7243 15.9684" stroke={ workspaceId ? (color ?? '#D87833') : '#ABA765'} strokeWidth="4" strokeLinecap="round" strokeLinejoin="round"/>
                            <path d="M122.833 22.3333L67 78.2224L50.25 61.4724" stroke={ workspaceId ? (color ?? '#D87833') : '#ABA765'} strokeWidth="4" strokeLinecap="round" strokeLinejoin="round"/>
                        </svg>

                        {
                            workspaceId ?
                                (
                                    <>
                                        <div className="text-center px-3 pt-4 pb-1 profile-title">{ trans('account-created') }</div>
                                        <div className="text-center px-3 profile-description text-828282">
                                            {trans('active-account')}
                                        </div>
                                        <div role="button" onClick={() => {
                                            router.push( "/?login=true");
                                            handleClose();
                                        }} style={{color: color}}
                                             className={`${style['footer-login-text-desk']} text-center pt-5 mt-0 px-3 text-uppercase`}>
                                            {trans('back-to-login')}
                                        </div>
                                    </>
                                )
                                :
                                (
                                    <>
                                        <div className="res-mobile">
                                            <div className="text-center font-bold px-3 pt-4 pb-1 profile-title"
                                                 style={{
                                                     fontSize: '24px',
                                                     fontWeight: '790',
                                                     lineHeight: '29px',
                                                     textAlign: 'center',
                                                     color: '#404040'
                                                 }}>{ trans('account-created') }</div>
                                            <div className="text-center px-3 profile-description text-828282"
                                                 style={{
                                                     fontFamily: 'SF Compact Display Medium',
                                                     fontSize: '18px',
                                                     fontWeight: '556',
                                                     lineHeight: '25px',
                                                     textAlign: 'center',
                                                     color: '#828282'
                                                 }}>
                                                {trans('active-account')}
                                            </div>
                                            <div role="button" onClick={() => {
                                                router.push( "/?login=true");
                                                handleClose();}}
                                                 className={`${style['footer-login-text']} font-bold text-center pt-5 mt-0 px-3`}
                                                 style ={{
                                                     fontSize: '16px',
                                                     fontWeight: '790',
                                                     lineHeight: '19.09px',
                                                     color: '#ABA765',
                                                     textTransform: 'uppercase',
                                                     letterSpacing: '2px'
                                                 }}
                                            >
                                                {trans('back-to-login')}
                                            </div>
                                        </div>
                                        <div className="res-desktop">
                                            <div className="text-center px-3 pt-4 pb-1 profile-title"
                                                 style={{
                                                     fontFamily: 'SF Compact Display',
                                                     fontSize: '24px',
                                                     fontWeight: '790',
                                                     lineHeight: '29px',
                                                     textAlign: 'center',
                                                     color: '#404040'
                                                 }}>{ trans('account-created') }</div>
                                            <div className="text-center px-3 profile-description text-828282"
                                                 style={{
                                                     fontFamily: 'SF Compact Display',
                                                     fontSize: '18px',
                                                     fontWeight: '556',
                                                     lineHeight: '25px',
                                                     textAlign: 'center',
                                                     color: '#828282'
                                                 }}>
                                                {trans('active-account')}
                                            </div>
                                            <div role="button" onClick={() => {
                                                router.push( "/?login=true");
                                                handleClose();}}
                                                 className={`${style['footer-login-text']} text-center pt-5 mt-0 px-3`}
                                                 style ={{
                                                     fontFamily: 'SF Compact Display',
                                                     fontSize: '16px',
                                                     fontWeight: '790',
                                                     lineHeight: '19.09px',
                                                     color: '#ABA765',
                                                     textTransform: 'uppercase',
                                                     letterSpacing: '2px'
                                                 }}
                                            >
                                                {trans('back-to-login')}
                                            </div>
                                        </div>
                                    </>
                                )
                        }
                    </div>
                </Modal.Body>
            </Modal>
        </>
    );
}