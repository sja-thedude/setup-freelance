'use client';

import React, { useState, useEffect } from 'react';
import PasswordForm from '@/app/[locale]/components/reset-password/resetPasswordform'
import { api } from "@/utils/axios";
import HomeComponent from "../../../../components/workspace/home";
import { useSearchParams } from 'next/navigation'
import PortalHome from "@/app/[locale]/components/portal/home";
import {useAppSelector} from "@/redux/hooks";
import {useRouter} from "next/navigation";
import Cookies from "js-cookie";

export default function Confirm({
    params
}: {
    params: { token: string, email: string }
}) {
    const workspaceId = useAppSelector((state) => state.workspaceData.globalWorkspaceId)
    let token = params.token[0];
    const searchParams = useSearchParams()
    let emailUser = searchParams.get('email')
    const [data, setData] = useState<any>(null)
    const router = useRouter()
    const language = Cookies.get('Next-Locale') ?? 'nl';

    useEffect(() => {
        if(token && emailUser) {
            const verifyToken = async () => {
                try {
                    const emailEncode = new URLSearchParams({email: emailUser ? emailUser : ''}).toString();
                    const isConfirmed = await api.get(`password/reset/${token}/verify?${emailEncode}`, {
                        headers: {
                            'Content-Language': language
                        }
                    });

                    if(isConfirmed?.data?.success === true) {
                        setData({token, emailUser, email: emailUser})
                    }                  
                } catch (error) {
                    router.push('/not-found');
                }
            }            

            verifyToken()
        }        
    }, [token, emailUser])

    return (
        <>
            {(token && emailUser && data) && (
                <>
                    {
                        window.innerWidth >= 1280 ? (
                            <div className='d-lg-block d-md-none'>
                                {
                                    workspaceId ? (
                                        <HomeComponent isRegisterConfirm={false} isResetPasswordConfirm={true} dataResetPassword={data}/>
                                    ) : (
                                        <PortalHome isRegisterConfirm={false} isResetPasswordConfirm={true} dataResetPassword={data}/>
                                    )
                                }
                            </div>
                        ) : (
                            <>
                                {
                                    workspaceId ? (
                                        <PasswordForm token={token} emailUser={emailUser} />
                                    ) : (
                                        <PortalHome isRegisterConfirm={false} isResetPasswordConfirm={true} dataResetPassword={data}/>
                                    )
                                }
                            </>
                        )
                    }
                </>
            )}
        </>
    )
}
