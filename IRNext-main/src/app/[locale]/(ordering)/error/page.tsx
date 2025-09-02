"use client"

import React, { useState, useEffect } from 'react'
import Cookies from "js-cookie";
import { api } from "@/utils/axios";
import FailedSelfOrdering from "@/app/[locale]/components/ordering/self-ordering/failed";
import FailedCategoryOrdering from "@/app/[locale]/components/404/not-found";
import FailedTableOrdering from "@/app/[locale]/components/ordering/table-ordering/failed";

export default function Page() {
    const [orderData, setOrderData] = useState<any>([]);
    const query = new URLSearchParams(window.location.search);
    const id = query.get('order_id');
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
        const order = api.get(`/orders/${id}`, {
            headers: {
                Authorization: `Bearer ${tokenLoggedInCookie}`,
                'Content-Language': language
            }
        }).then((res: any) => {
            setOrderData(res.data.data);
        }).catch((err) => {

        });
    }, [tokenLoggedInCookie, id]);

    return (
        <>
            {orderData && orderData?.type == 3 && (
                <FailedSelfOrdering />
            )}

            {orderData && orderData?.type == 2 && (
                <FailedTableOrdering />
            )}

            {orderData && orderData?.type == 1 && (
                <FailedCategoryOrdering />
            )}

            {orderData && orderData?.type == 0 && (
                <FailedCategoryOrdering />
            )}
        </>
    );
}