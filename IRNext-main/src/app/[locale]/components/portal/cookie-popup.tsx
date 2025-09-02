"use client"

import {useState} from "react";
import {useI18n} from "@/locales/client";
import 'public/assets/css/popup.scss';

export default function CookiePopup() {
    const trans = useI18n();
    const closedCookiePopup = sessionStorage.getItem('closedCookiePopup');
    const [showPopup, setShowPopup] = useState(!closedCookiePopup);

    const closePopup = () => {
        setShowPopup(false);
        sessionStorage.setItem('closedCookiePopup', 'true');
    }

    return (
        <>
            {showPopup && (
                <div id="cookie-popup" data-type="popup">
                    <div className={`cookie-message`}>
                        <div>
                            {trans('portal.cookie-message')}
                            &nbsp;<a href="https://b2b.itsready.be/cookies" target="_blank">{trans('portal.cookie-message-link')}</a>.
                        </div>
                    </div>
                    <div className={`cookie-actions`}>
                        <button className={`btn-close-cookie`} onClick={() => closePopup()}>
                            <img src="/img/btn-close-cookie.svg"/>
                            <span>{trans('close')}</span>
                        </button>
                    </div>

                    <div className={`clear`}></div>
                </div>
            )}
        </>
    )
}