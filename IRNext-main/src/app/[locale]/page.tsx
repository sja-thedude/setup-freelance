'use client'

import React from 'react';
import dynamic from 'next/dynamic'
import { useAppSelector } from '@/redux/hooks';
import CookiePopup from './components/portal/cookie-popup';

const HomeComponent = dynamic(() => import('./components/workspace/home'))
const PortalHome = dynamic(() => import('./components/portal/home'))
export default function Home() {
    const workspaceId = useAppSelector((state) => state.workspaceData.globalWorkspaceId)

    return (
        <>
            { workspaceId ? (
                <HomeComponent isRegisterConfirm={false} isResetPasswordConfirm={false} dataResetPassword={null}></HomeComponent>
            ) : (
                <>
                    <CookiePopup />

                    <div className="container">
                        <PortalHome></PortalHome>
                    </div>
                </>
            )}
        </>
    );
};
