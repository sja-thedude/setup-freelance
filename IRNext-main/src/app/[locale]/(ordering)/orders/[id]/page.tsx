"use client"

import React, { useState, useEffect } from 'react'
import Cookies from "js-cookie";
import { api } from "@/utils/axios";
import SuccessedSelfOrdering from "@/app/[locale]/components/ordering/self-ordering/successed";
import SuccessedCategoryOrdering from "@/app/[locale]/components/ordering/category/successed";
import ActivatedTableOrdering from "@/app/[locale]/components/ordering/table-ordering/activated";
import DeactivatedTableOrdering from "@/app/[locale]/components/ordering/table-ordering/deactivated";
import { useAppSelector } from '@/redux/hooks'

export default function Page({ params }: { params: { id: number } }) {
    const [orderData, setOrderData] = useState<any>([]);
    const [isActivate, setIsActivate] = useState(false);
    const workspaceId = useAppSelector((state) => state.workspaceData.globalWorkspaceId)
    const [tokenLoggedInCookie, setTokenLoggedInCookie] = useState<string>('');
    const language = Cookies.get('Next-Locale') ?? 'nl';

    useEffect(() => {
        const generateToken = api.get(`auth/token/generate`, {
                headers: {
                    Authorization: `Bearer ${tokenLoggedInCookie}`,
                    'Content-Language': language
                }
            })
            .then((res: any) => {
                setTokenLoggedInCookie(res?.data?.data?.token);
            }).catch((err) => {

            });
    }, []);

    useEffect(() => {
        workspaceId && api.get(`workspaces/` + workspaceId, {
            headers: {
                Authorization: `Bearer ${tokenLoggedInCookie}`,
                'Content-Language': language
            }
        }).then(res => {
            res.data?.data?.extras.map((item: any) => {
                if(item?.type === 11){
                    if(item.active === true){
                        setIsActivate(true)
                    }
                }
            });
        }).catch(error => {
            // console.log(error)
        });

        const order = api.get(`/orders/${params?.id}`, {
            headers: {
                Authorization: `Bearer ${tokenLoggedInCookie}`,
                'Content-Language': language
            }
        }).then((res: any) => {
            setOrderData(res.data.data);
        }).catch((err) => {

        });
    }, [tokenLoggedInCookie, params?.id]);

    return (
        <div className="container">
            {orderData && orderData?.type == 3 && (
                <SuccessedSelfOrdering />
            )}

            {orderData && orderData?.type == 2 && isActivate && (
                <ActivatedTableOrdering />
            )}

            {orderData && orderData?.type == 2 && !isActivate && (
                <DeactivatedTableOrdering />
            )}

            {orderData && orderData?.type == 1 && (
                <SuccessedCategoryOrdering id={orderData?.id} />
            )}

            {orderData && orderData?.type == 0 && (
                <SuccessedCategoryOrdering id={orderData?.id} />
            )}
        </div>
    );
}