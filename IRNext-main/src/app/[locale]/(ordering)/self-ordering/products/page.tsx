'use client'

import React from 'react';
import ProductList from "@/app/[locale]/components/product/product-list";
import Loading from "@/app/[locale]/components/loading";
import {useDebounce} from "use-debounce";
import useValidateSecurity from "@/hooks/useTableSelfOrderingSecurity";
import {useRouter} from "next/navigation";
import { useAppSelector } from '@/redux/hooks'
import {useValidateToTriggerClosedScreen} from "@/hooks/useTableSelfOrderingSecurity";
import 'public/assets/css/only.products.responsive.scss'
import {OPENING_HOUR_SELF_ORDERING_TYPE, EXTRA_SETTING_SELF_ORDERING_TYPE} from "@/config/constants"
import InvalidSecurity from "@/app/[locale]/components/404/invalid-security";

export default function Page() {    
    const router = useRouter();
    const [isLoading, setIsLoading] = useDebounce(true, 400);
    const workspaceId = useAppSelector((state) => state.workspaceData.globalWorkspaceId)
    useValidateToTriggerClosedScreen(router, workspaceId, OPENING_HOUR_SELF_ORDERING_TYPE, EXTRA_SETTING_SELF_ORDERING_TYPE);
    const validateSecurity = useValidateSecurity();
    if (!validateSecurity) {
        return (<InvalidSecurity />);
    }
    
    return (
        <>
            {isLoading && <Loading/>}
            <div className='row' style={{ backgroundColor: '#f8f8f8' }}>
                <div className='col-12'>
                    <ProductList setIsLoading={setIsLoading} baseLink={'/self-ordering/products'} />
                </div>
            </div>
        </>
    );
};