'use client'
import React from 'react';
import { useRouter } from "next/navigation";
import { FontAwesomeIcon } from "@fortawesome/react-fontawesome";
import { faChevronLeft } from "@fortawesome/free-solid-svg-icons";

export default function Redirect({ from }: any) {
    const router = useRouter()
    const handleClick = () => {
        if (from) {
            router.back()
        } else {
            router.push("/user/login")
        }
    }
    return (
        <>
            <div className='col-sm-2 col-xs-2'> <FontAwesomeIcon
                icon={faChevronLeft}
                onClick={handleClick}
                style={{ color: 'white', cursor: 'pointer', pointerEvents: 'auto' }}
            /></div>
        </>
    );
};
