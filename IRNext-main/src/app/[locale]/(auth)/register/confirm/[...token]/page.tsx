'use client';

import React, { useEffect } from 'react';
import variables from '/public/assets/css/register.module.scss'
import { useI18n } from '@/locales/client'
import Link from 'next/link';
import { useGetWorkspaceDataByIdQuery } from '@/redux/services/workspace/workspaceDataApi';
import HomeComponent from "../../../../components/workspace/home";
import { useAppSelector } from '@/redux/hooks'
import PortalHome from "@/app/[locale]/components/portal/home";
import Cookies from 'js-cookie';
import { api } from "@/utils/axios";
import { useRouter } from "next/navigation";

export default function Register({
    params
}: {
    params: { token: string }
}) {
    const router = useRouter();
    const {token} = params
    const workspaceId = useAppSelector((state) => state.workspaceData.globalWorkspaceId)
    const { data: apiDataToken } = useGetWorkspaceDataByIdQuery({id: workspaceId ? workspaceId : 0})
    const trans = useI18n();
    var color = apiDataToken?.data?.setting_generals?.primary_color
    const btnDark = `btn btn-dark ${variables['btn-dark']}`;
    const ready = variables['ready'];
    const language = Cookies.get('Next-Locale') ?? 'nl';

    const  getMobileOperatingSystem = (): string => {
        const userAgent = navigator.userAgent || navigator.vendor || (window as any).opera;

        if (/windows phone/i.test(userAgent)) {
            return 'Windows Phone';
        }
      
        if (/android/i.test(userAgent)) {
            return 'Android';
        }
      
        if (/iPad|iPhone|iPod/.test(userAgent) && !(window as any).MSStream) {
            return 'iOS';
        }
      
        return 'unknown';
    }

    const deepLinkRedirect = (androidDeeplink: string, iosDeeplink: string, data: any) => {
        const device = getMobileOperatingSystem();
        const queryString = new URLSearchParams(data).toString();

        if (device === 'Android') {
            window.location.href = `${androidDeeplink}?${queryString}`;
        } else if (device === 'iOS') {
            window.location.href = `${iosDeeplink}?${queryString}`;
        }
    };

    const callVerify = async (token: string, workspaceId: number) => {        
        try {
            const verifyResponse = await api.get(`register/verify/${token}?verify=1&workspace_id=${workspaceId}`, {
                headers: {
                    'Content-Language': language
                }
            });

            if(verifyResponse?.data?.success == true) {
                deepLinkRedirect(
                    verifyResponse?.data?.data?.config?.android.deeplink,
                    verifyResponse?.data?.data?.config?.ios.deeplink,
                    {
                        screen: 'registered_confirmation',
                        token: verifyResponse?.data?.data?.token,
                        verify_token: verifyResponse?.data?.data?.verify_token,
                        redirect_url: verifyResponse?.data?.data?.redirect_url
                    }
                );
            }            
        } catch (error: any) {
            // router.push('/not-found');
        }
    };

    useEffect(() => {
        if (token?.length > 0 && workspaceId) {
            callVerify(token[0], workspaceId ? workspaceId : 0);
        }
    }, [
        token,
        workspaceId
    ]);

    return (
        <>
            {
                window.innerWidth >= 992 ? (
                    <div className='d-lg-block d-md-none'>
                        { 
                            workspaceId 
                            ? <HomeComponent isRegisterConfirm={true} isResetPasswordConfirm={false} dataResetPassword={null}/>
                            : <PortalHome isRegisterConfirm={true} isResetPasswordConfirm={false} dataResetPassword={null}/>
                        }
                    </div>
                ) : (
                    <>
                        {
                            workspaceId ? (
                                <div className='row res-mobile' style={{ backgroundColor: color ? color : '#B5B268', minHeight: '100vh' }}>
                                    <div className={`${variables.toping} row`}>
                                        <div className='col-sm-12 col-xs-12' style={{ margin: "auto" }}> <h1 className={`${variables.register} ms-3`}>{trans('title')} {apiDataToken?.data?.title}</h1></div>
                                    </div>
                                    <div className={`${variables.title} row mb-2 mt-1`}><p>{trans('register-success')}</p></div>
                                    <div className='row' style={{ marginBottom: '450px' }}>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="111" height="101" viewBox="0 0 111 101" fill="none">
                                            <path d="M15.6931 38.3904C14.5981 42.3356 14.0447 46.4087 14.0479 50.5C14.0479 75.6297 34.6314 96 60.0241 96C85.4168 96 106 75.6297 106 50.5C106 25.3704 85.4206 5.00004 60.0241 5.00004C47.9294 4.98448 36.3188 9.70078 27.7229 18.121" stroke="white" strokeWidth="10" strokeMiterlimit="22.93" strokeLinejoin="round" />
                                            <path fillRule="evenodd" clipRule="evenodd" d="M0 49.0886H29.434L14.7177 30.5111L0 49.0886Z" fill="white" />
                                            <path d="M38.793 43.8031L60.1159 70.2685L102.826 11.9745" stroke="white" strokeWidth="10" strokeMiterlimit="22.93" strokeLinejoin="round" />
                                            <path d="M38.793 43.8031L60.1159 70.2685L102.826 11.9745" stroke="white" strokeWidth="10" strokeMiterlimit="22.93" strokeLinejoin="round" />
                                        </svg>
                                        <div className={`${ready} mt-4 ms-2`}>
                                            <Link href="/user/login" legacyBehavior >
                                                <button type="button" className={`${btnDark} border-0`} >
                                                    {trans('ready')}
                                                </button>
                                            </Link>
                                        </div>
                                    </div>

                                </div>
                            ) : <PortalHome isRegisterConfirm={true} isResetPasswordConfirm={false} dataResetPassword={null}/>
                        }
                    </>
                )
            }
        </>
    );
};
