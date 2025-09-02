import variables from '/public/assets/css/portal.module.scss';
import Image from "next/image";
import * as configLocales from "@/config/locales";
import React, { useState } from "react";
import Cookies from "js-cookie";
import {useAppDispatch} from '@/redux/hooks';
import { changeGlobalLocale } from '@/redux/slices/authSlice';
import useMediaQuery from '@mui/material/useMediaQuery'
import { useAppSelector } from '@/redux/hooks'

export default function SwitchLangMobile(props: any) {
    const { origin, customCss, customArrow } = props;
    const dispatch = useAppDispatch();
    const isMobile = useMediaQuery('(max-width: 1279px)');
    const supportLanguages: { [key: string]: string } = configLocales.LOCALES_WITH_NAMES;
    const [currentLanguage, setCurrentLanguage] = useState(Cookies.get('Next-Locale') ?? configLocales.LOCALE_FALLBACK);
    const activeLanguages = useAppSelector((state) => state.auth.activeLanguages)
    const globalWorkspaceId = useAppSelector((state) => state.workspaceData.globalWorkspaceId)
    
    /**
     * Change language
     */
    const onChangeLanguage = (languageKey: string) => {
        const domain = location.protocol + '//' + location.host;
        const oldLocaleUrl = domain + '/' + currentLanguage;
        const newLocaleUrl = domain + '/' + languageKey;
        const newUrl = location.href.replace(oldLocaleUrl, newLocaleUrl);

        setCurrentLanguage(languageKey);
        dispatch(changeGlobalLocale(languageKey));
        window.location.href = newUrl;
    }

    return (
        <>
            {isMobile && (!globalWorkspaceId || activeLanguages?.length > 1) && (
                <>
                    {origin === 'home-page' ? (
                        <div className={`dropdown portal-dropdown-type table-self-ordering-lang`} 
                        style={customCss ?? {position: 'fixed', top: '14px', left: '14px', zIndex: 9}}>
                            <button style={{border: "0px", padding: "0px", display: 'flex', flexDirection: 'row'}} 
                                    type="button"
                                    className={`${variables.header_info_item}`}
                                    data-bs-toggle="dropdown"
                                    aria-expanded="false">
                                    <Image
                                        alt={'portal-logo'}
                                        src={`/img/${currentLanguage}.svg`}
                                        width={20}
                                        height={20}
                                        sizes="100vw"
                                        style={{ objectFit: "cover" }}
                                        className={`${variables.logo}`}
                                    />
                                    <span className="menu-down" style={{ fontWeight: '556', fontFamily: 'SF Compact Display', fontSize: '14px', color: '#1E1E1E', textTransform: 'none' }}>
                                        <img src={`/img/chevron-down${customArrow ?? ''}.svg`} />
                                    </span>
                                    <span className="menu-up" style={{ fontWeight: '556', fontFamily: 'SF Compact Display', fontSize: '14px', color: '#1E1E1E', textTransform: 'none' }}>
                                        <img src={`/img/chevron-up${customArrow ?? ''}.svg`} />
                                    </span>
                            </button>
                            <ul className={`dropdown-menu reformat ${variables['dropdown-menu-language']}`}>
                                {Object.keys(supportLanguages).map((languageKey) => {
                                    const language = supportLanguages[languageKey];
                                    const isSelected = currentLanguage === languageKey;
                                    const flagUrl = `/img/${languageKey}.svg`;

                                    return !isSelected && (!globalWorkspaceId || activeLanguages.includes(languageKey)) && (
                                        <li key={languageKey}>
                                            <button type="button" className="dropdown-item" onClick={() => onChangeLanguage(languageKey)}>
                                                <img src={flagUrl} style={{width: '20px', height: '20px'}}/>
                                            </button>
                                        </li>
                                    );
                                })}
                            </ul>
                        </div>
                    ) : (
                        <div className={`dropdown portal-dropdown-type`}>
                            <button style={{border: "0px", padding: "0px", display: 'flex', flexDirection: 'row'}} type="button"
                                    className={`${variables.header_info_item}`}
                                    data-bs-toggle="dropdown"
                                    aria-expanded="false">
                                <Image
                                    alt={'portal-logo'}
                                    src={`/img/${currentLanguage}.png`}
                                    width={26}
                                    height={26}
                                    sizes="100vw"
                                    style={{ objectFit: "cover" }}
                                    className={`${variables.logo}`}
                                />
                                <span style={{ fontWeight: '556', fontFamily: 'SF Compact Display', fontSize: '14px', color: '#1E1E1E', textTransform: 'none' }}>
                                    {supportLanguages[currentLanguage]}
                                                <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M4 6L8 10L12 6" stroke="black" strokeWidth="1.33333" strokeLinecap="round" strokeLinejoin="round"/>
                                    </svg>
                                </span>
                            </button>
                            <ul className={`dropdown-menu reformat ${variables['dropdown-menu-language']}`} style={{ marginLeft : '-75px', marginTop: '15px', paddingTop: '15px'}}>
                                {Object.keys(supportLanguages).map((languageKey) => {
                                    const language = supportLanguages[languageKey];
                                    const isSelected = currentLanguage === languageKey;
                                    const flagUrl = `/img/${languageKey}.png`;

                                    return (
                                        (!globalWorkspaceId || activeLanguages.includes(languageKey)) &&
                                        <li key={languageKey}>
                                            <button type="button" className="dropdown-item" onClick={() => onChangeLanguage(languageKey)}>
                                                <div style={{
                                                    display : 'flex',
                                                    alignItems: 'center',
                                                    justifyContent: 'start',
                                                    textAlign: 'left',
                                                    textTransform: 'none',
                                                    marginBottom: '10px',
                                                    fontFamily: 'SF Compact Display Medium',
                                                    fontSize: '18px',
                                                    lineHeight: '21.48px',
                                                    color: '#1E1E1E',
                                                }}>
                                                    <img src={flagUrl} alt="" style={{ width: '26px', height: '26px', marginRight: '5px' }} />
                                                    <span style={ isSelected ? {fontWeight: '656', fontFamily: 'SF Compact Display Medium', fontSize: '18px', color: '#1E1E1E'} : {}}>{language}</span>
                                                </div>
                                            </button>
                                        </li>
                                    );
                                })}
                            </ul>
                        </div>
                    )}
                </>
            )}
        </>        
    );
}