"use client"

import { ReactElement, useEffect } from 'react'
import { I18nProviderClient } from '@/locales/client'
import 'bootstrap/dist/css/bootstrap.min.css'
import 'public/assets/css/global.css'
import 'public/assets/css/responsive.scss'
import Cookies from "js-cookie";
import { api } from "@/utils/axios";
import {useRouter} from "next/navigation";
import {handleLoginToken} from "@/utils/axiosRefreshToken";
import { REGEX_NUMBER_CHECK } from '@/config/constants';

export default function ClientLayout({
    children,
    params
}: {
    children: ReactElement
    params: { locale: string, workspaceId: number }
}) {
    const router = useRouter()
    const workspaceId = params.workspaceId;

    useEffect(() => {
        const tokenArr = window.location.href.split("&id_token=");
        const url = Cookies.get('currentLinkLogin') ?? '/';
        if (tokenArr.length === 2) {
            const token = tokenArr[1];
            let data = api.post(`login/social`, {
                'provider': 'apple',
                'access_token': token,
                'workspace_id': workspaceId,
            }).then(res => {
                const userData = res.data.data;

                // Set cookie 'loggedToken' with value 'token'
                handleLoginToken(userData.token);

                if (userData?.first_login && (userData.first_name.includes('@') || REGEX_NUMBER_CHECK.test(userData.first_name) || !userData.gsm)) {
                    if (window.innerWidth < 1280) {
                        router.push("/profile/edit");
                    } else {
                        const query = new URLSearchParams(window.location.search);
                        if (query.size > 0) {
                            router.push(window.location.href + '&editProfile=true')
                        } else {
                            router.push(window.location.href + '?editProfile=true')
                        }
                    }
                } else {
                    router.push(url);
                }
            }).catch(err => {
                // console.log(err);
            });
        }
    }, []);

    useEffect(() => {
        require('bootstrap/dist/js/bootstrap.bundle.min.js')
    }, []);

    return (
        <I18nProviderClient locale={params.locale}>
            {children}
        </I18nProviderClient>
    )
}