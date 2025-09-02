'use client';

import React, { useState } from "react";
import style from "../../../../../public/assets/css/profile.module.scss";
import 'public/assets/css/common.scss';
import { FontAwesomeIcon } from "@fortawesome/react-fontawesome";
import { faChevronLeft } from "@fortawesome/free-solid-svg-icons";
import '@fortawesome/fontawesome-svg-core/styles.css';
import { useI18n } from '@/locales/client';
import { useRouter } from "next/navigation";
import MenuPortal from "@/app/[locale]/components/menu/menu-portal";
import { useAppSelector } from '@/redux/hooks'
import * as locales from "@/config/locales";
import Cookies from 'js-cookie';
import Menu from "../../components/menu/menu-plus";
import {useAppDispatch} from '@/redux/hooks';
import { changeGlobalLocale } from '@/redux/slices/authSlice';
import { api } from "@/utils/axios";

export default function Condition() {
    const dispatch = useAppDispatch();
    const workspaceId = useAppSelector((state) => state.workspaceData.globalWorkspaceId)
    const wpColor = useAppSelector((state) => state.workspaceData.globalWorkspaceColor)
    const color = !workspaceId ? '#B5B268' : wpColor;
    const router = useRouter();
    const trans = useI18n();
    const language = Cookies.get('Next-Locale');
    const [currentLanguage, setCurrentLanguage] = useState(language ?? locales.LOCALE_FALLBACK);
    const supportLanguages: { [key: string]: string } = locales.LOCALES_WITH_NAMES;
    const tokenLoggedInCookie = Cookies.get('loggedToken');
    const activeLanguages = useAppSelector((state) => state.auth.activeLanguages)

    /**
     * Change language
     */
    const onChangeLanguage = (languageKey: string) => {
        setCurrentLanguage(languageKey);
    }

    /**
     * Save the selected language
     */
    async function saveLanguage() {
        dispatch(changeGlobalLocale(currentLanguage));
        Cookies.set('Next-Locale', currentLanguage);

        if(tokenLoggedInCookie) {
            await api.post('profile/change_language', {
                locale: currentLanguage
            } , {
                headers: {
                    'Authorization': 'Bearer ' + tokenLoggedInCookie
                }
            });
        }

        let profileUrl = `/${currentLanguage}/profile/portal/show`;

        if(workspaceId) {
            profileUrl = `/${currentLanguage}/profile/show`;
        }

        return router.push(profileUrl);
    }

    /**
     * Back to previous page
     */
    function back() {
        // document.referrer = previous URL
        if (document.referrer.endsWith("/language")) {
            let profileUrl = `/profile/portal/show`;

            if(workspaceId) {
                profileUrl = `/profile/show`;
            }

            return router.push(profileUrl);
        }

        return router.back();
    }

    return (
        <>
            <div style={{ position: 'fixed', bottom: 0, left: 0, width: '100%' ,zIndex: 100 }}>
                {
                    workspaceId ? (
                        <Menu />
                    ) : (
                        <MenuPortal />
                    )
                }
            </div>
            <div className={style['navbar']}>
                <div className={style['profile-text']} style={{ fontSize: '30px', background: color, display:'flex' }}>
                    <FontAwesomeIcon icon={faChevronLeft} style={{fontSize: '25px'}} className={`${style['style-icon']} my-auto me-2`} onClick={() => back()} />
                    <div>
                        { trans('language') }
                    </div>
                </div>
            </div>
            <div className={`language-setting ${style['language-setting']}`}>
                <div className="dropdown itr-dropdown">
                    <button className="btn btn-light dropdown-toggle full-width" 
                        type="button" 
                        data-bs-toggle="dropdown" 
                        aria-expanded="false">
                        {supportLanguages[currentLanguage]}
                    </button>
                    <ul className="dropdown-menu">
                        {Object.keys(supportLanguages).map((languageKey, index) => {
                            const language = supportLanguages[languageKey];
                            const isSelected = currentLanguage === languageKey;
                            return (
                                (activeLanguages.includes(languageKey) || !workspaceId) &&
                                <li key={index}>
                                    <a className="dropdown-item" onClick={() => onChangeLanguage(languageKey)}>
                                        <span className="item-image-container">
                                            {isSelected && <img src="/img/checked-language.svg" alt="" />}
                                        </span>
                                        <span>{language}</span>
                                    </a>
                                </li>
                            )
                        })}
                    </ul>                    
                </div>
            </div>
            <div className={style['language-save']}>
                <div className={`btn-div ${style['language-save-button']} ${language == currentLanguage ? 'disabled' : ''}`}
                    onClick={() => saveLanguage()}>
                    {trans('save')}
                </div>
            </div>
        </>
    )
}