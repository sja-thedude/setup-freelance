import variables from '/public/assets/css/portal.module.scss';
import Image from "next/image";
import * as configLocales from "@/config/locales";
import React, { useState } from "react";
import Cookies from "js-cookie";
import {useAppDispatch} from '@/redux/hooks';
import { changeGlobalLocale } from '@/redux/slices/authSlice';
import useMediaQuery from '@mui/material/useMediaQuery'
import { api } from "@/utils/axios";
import { useAppSelector } from '@/redux/hooks'

export default function SwitchLangDesktop() {    
    const dispatch = useAppDispatch();
    const isMobile = useMediaQuery('(max-width: 1279px)');
    const activeLanguages = useAppSelector((state) => state.auth.activeLanguages)
    const supportLanguages: { [key: string]: string } = configLocales.LOCALES_WITH_NAMES;
    const [currentLanguage, setCurrentLanguage] = useState(Cookies.get('Next-Locale') ?? configLocales.LOCALE_FALLBACK);
    const tokenLoggedInCookie = Cookies.get('loggedToken');
    const globalWorkspaceId = useAppSelector((state) => state.workspaceData.globalWorkspaceId)

    /**
     * Change language
     */
    const onChangeLanguage = async (languageKey: string) => {
        const domain = location.protocol + '//' + location.host;
        const oldLocaleUrl = domain + '/' + currentLanguage;
        const newLocaleUrl = domain + '/' + languageKey;
        const newUrl = location.href.replace(oldLocaleUrl, newLocaleUrl);

        setCurrentLanguage(languageKey);
        dispatch(changeGlobalLocale(languageKey));

        if(tokenLoggedInCookie) {
            await api.post('profile/change_language', {
                locale: languageKey
            } , {
                headers: {
                    'Authorization': 'Bearer ' + tokenLoggedInCookie
                }
            });
        }

        window.location.href = newUrl;
    }

    return (
        <>
            {!isMobile && (!globalWorkspaceId || activeLanguages?.length > 1) && (
                <div className={`d-flex justify-content-between`}>
                    <div className={`dropdown portal-dropdown-type`}>
                        <button style={{border: "0px", padding: "0px"}} 
                                type="button"
                                className={`${variables.header_info_item}`}
                                data-bs-toggle="dropdown"
                                aria-expanded="false">
                            <Image
                                alt={'portal-logo'}
                                src={`/img/${currentLanguage}.png`}
                                width={35}
                                height={35}
                                sizes="100vw"
                                style={{ objectFit: "cover" }}
                                className={`${variables.logo}`}
                            />
                        </button>
                        <ul className={`dropdown-menu ${(!globalWorkspaceId || activeLanguages?.length > 1) ? '' : 'invisible'} reformat ${variables['dropdown-menu-language']}`} 
                            style={{ marginLeft : '-75px', marginTop: '15px', paddingTop: '15px'}}>
                            { Object.keys(supportLanguages).map((languageKey) => {
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
                                                fontFamily: 'SF Compact Display',
                                                fontSize: '18px',
                                                lineHeight: '21.48px',
                                            }}>
                                                <img src={flagUrl} alt="" style={{ width: '35px', height: '35px', marginRight: '5px' }} />
                                                <span style={ isSelected ? {fontWeight: '656', fontFamily: 'SF Compact Display', fontSize: '18px', color: '#1E1E1E'} : { fontFamily: 'SF Compact Display', fontSize: '18px', color: '#1E1E1E' }}>{language}</span>
                                            </div>
                                        </button>
                                    </li>
                                );
                            })}
                        </ul>
                    </div>
                </div>
            )}
        </>        
    );
}