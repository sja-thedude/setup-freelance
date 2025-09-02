"use client"

import style from "public/assets/css/portal.module.scss";
import {useI18n} from "@/locales/client";
import Cookies from "js-cookie";
import {api} from "@/utils/axios";
import React, { useEffect, useState } from 'react';
import HeaderPortal from "@/app/[locale]/components/menu/header-portal";
import FooterPortal from "@/app/[locale]/components/menu/footer-portal";
import PortalLoginDesktopPopup from "@/app/[locale]/components/portal/portalLoginDesktopPopup";

export default function Page() {
    const trans = useI18n()
    const [getSettingData, setSettingData] = useState<any | null>(null);
    const language = Cookies.get('Next-Locale');

    const [isProfileUpdatePopupOpen, setIsProfileUpdatePopupOpen] = useState(false);
    const toggleProfileUpdatePopup = () => {
        setIsProfileUpdatePopupOpen(!isProfileUpdatePopupOpen);
    }

    const goBack = () => {
        history.back();
    }

    const fetchDataSettings = () => {
        const tokenLoggedInCookie = Cookies.get('loggedToken');
        api.get(`pages/cookie-policy`, {
            headers: {
                'Authorization': `Bearer ${tokenLoggedInCookie}`,
                'Content-Language': language,
            }
        }).then(res => {
            const json = res.data;
            if (json.data) {
                setSettingData({
                    status: true,
                    data: json.data,
                });
            }
        }).catch(error => {
            console.error("Error fetching data", error);
        });
    };

    const [getToggleLoginPopUp, setToggleLoginPopUp] = useState(false);

    const toggleLoginPopUp = () => {
        setToggleLoginPopUp(!getToggleLoginPopUp);
    }

    useEffect(() => {
        fetchDataSettings();
    }, []);

    return (
        <>
            <div style={{ background: "#FFF", width: "calc(100% + 1.5rem)", marginLeft: "-0.75rem"}}>
                <div className="res-mobile">
                    <div style={{ padding: '3px 7px 3px 10px' }}><HeaderPortal toggleProfileUpdatePopup={toggleProfileUpdatePopup} toggleLoginPopUp={toggleLoginPopUp}/></div>
                    <div style={{ borderTop: "solid 1px #CDCDCD"}}>
                        <div className={`${style['terms-title-mobile']}`}>
                            <div>{trans('cookie-policy')}</div>
                        </div>
                        <div className={`${style['terms-description-mobile']}`}>

                            { getSettingData && getSettingData.data.content ? (
                                // Render the CKEditor content as HTML
                                <div dangerouslySetInnerHTML={{__html: getSettingData?.data?.content}}/>
                            ) : null}
                        </div>
                    </div>
                    {getToggleLoginPopUp && (
                        <PortalLoginDesktopPopup getToggleLoginPopUp={getToggleLoginPopUp} setToggleLoginPopUp={setToggleLoginPopUp} />
                    )}
                    <div className={'row'} style={{ margin: 'auto'}}><FooterPortal trans={trans} lang={language} from = {null}/></div>
                </div>
                <div className="res-desktop">
                    <div style={{ padding: '15px'}}><HeaderPortal toggleProfileUpdatePopup={toggleProfileUpdatePopup} toggleLoginPopUp={toggleLoginPopUp}/></div>
                    <div style={{ borderTop: "solid 1px #CDCDCD"}}>
                        <div className={`${style['terms-title-desktop']}`}>
                            <div>{trans('cookie-policy')}</div>
                        </div>
                        <div className={`${style['terms-description']}`}>

                            { getSettingData && getSettingData.data.content ? (
                                // Render the CKEditor content as HTML
                                <div dangerouslySetInnerHTML={{__html: getSettingData?.data?.content}}/>
                            ) : null}
                        </div>
                    </div>
                    {getToggleLoginPopUp && (
                        <PortalLoginDesktopPopup getToggleLoginPopUp={getToggleLoginPopUp} setToggleLoginPopUp={setToggleLoginPopUp} />
                    )}
                    <div className={'row'}><FooterPortal trans={trans} lang={language} from = {null}/></div>
                </div>
            </div>
        </>
    );
};
