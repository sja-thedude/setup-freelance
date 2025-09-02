'use client'

import { useEffect } from "react";
import Cookies from "js-cookie";
import { useRouter } from 'next/navigation'

export default function Access(props: any) {
    const { valid, origin } = props
    const router = useRouter()
    const query = new URLSearchParams(window.location.search);
    const tableNumber = query.get('tablenumber');

    useEffect(() => {
        Cookies.set('accessOrdering', valid ?? false, { expires: 1 / 24 });
        Cookies.set('tableNumber', tableNumber ?? '', { expires: 1 / 24 });
        router.push(`/${origin}`);
    }, [valid, tableNumber]);

    return (
        <></>
    )
}