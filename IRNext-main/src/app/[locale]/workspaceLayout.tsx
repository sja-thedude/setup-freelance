"use client"

import { toNumber } from 'lodash'
import { ReactElement, useEffect } from 'react'
import { useAppDispatch } from '@/redux/hooks'
import { changeGlobalWorkspaceId, changeGlobalWorkspaceToken, changeGlobalWorkspaceColor } from '@/redux/slices/workspace/workspaceDataSlice'
import { LOCALE_FALLBACK } from '@/config/locales';
import { changeGlobalLocale, changeActiveLanguages } from '@/redux/slices/authSlice';
import { api } from "@/utils/axios";

export default function WorkspaceLayout({
    workspaceId,
    workspaceToken,
    workspaceColor,
    language,
    children    
}: {
    workspaceId: any,
    workspaceToken: any,
    workspaceColor: any,
    language: string,
    children: ReactElement
}) {
    const dispatch = useAppDispatch();
    const getRestaurantLanguageSetting = async () => {
        const settings = await api.get(`workspaces/${workspaceId}/languages`);
        const activeLanguages = settings?.data?.data?.active_languages ?? [LOCALE_FALLBACK];
        dispatch(changeActiveLanguages(activeLanguages));

        // redirect to first language if current language is not active
        // if(language && !activeLanguages.includes(language)) {
        //     let defaultLanguage = activeLanguages[0];

        //     if (activeLanguages.includes(LOCALE_FALLBACK)) {
        //         defaultLanguage = LOCALE_FALLBACK;
        //     }

        //     dispatch(changeGlobalLocale(defaultLanguage));

        //     const domain = location.protocol + '//' + location.host;
        //     const oldLocaleUrl = domain + '/' + language;
        //     const newLocaleUrl = domain + '/' + defaultLanguage;
        //     const newUrl = location.href.replace(oldLocaleUrl, newLocaleUrl);
        //     window.location.href = newUrl;
        // }
    };

    useEffect(() => {
        const workspaceIdToRedux = workspaceId != '' && workspaceId != null ? toNumber(workspaceId) : null
        const workspaceTokenToRedux = workspaceToken != '' && workspaceToken != null ? workspaceToken : null
        const workspaceColorToRedux = workspaceColor != '' && workspaceColor != null ? workspaceColor : null
        dispatch(changeGlobalWorkspaceId(workspaceIdToRedux))
        dispatch(changeGlobalWorkspaceToken(workspaceTokenToRedux))
        dispatch(changeGlobalWorkspaceColor(workspaceColorToRedux))

        if(workspaceIdToRedux) {
            getRestaurantLanguageSetting();
        }        
    }, [
        workspaceId,
        workspaceToken,
        workspaceColor
    ])    

    return (
        <>
            {children}
        </>
    )
}