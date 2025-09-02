// Popup component
'use client'

import variables from '/public/assets/css/home.module.scss';
import React from "react";

export default function Popup({ data, holiday }: any) {
    const lines = data?.setting_preference.holiday_text ? data.setting_preference.holiday_text : '';

    return (
        <>
            <div className='row'>
                <div className={variables.popupContent}>
                    <div className={`${variables.content} mb-2 mx-2`}>
                        <div className={variables.icon_pop}>
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                <path d="M12 22C17.5228 22 22 17.5228 22 12C22 6.47715 17.5228 2 12 2C6.47715 2 2 6.47715 2 12C2 17.5228 6.47715 22 12 22Z" stroke="white" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" />
                                <path d="M12 16V12" stroke="white" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" />
                                <path d="M12 8H12.01" stroke="white" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" />
                            </svg>
                        </div>
                        <div className={variables.text}>
                            {!holiday || !holiday.status && lines && (
                                <div className="space-text" style={{whiteSpace: "pre-line"}}>
                                    {lines}
                                </div>
                            )}
                            {holiday && holiday.status && holiday.data[0].description && (
                                <>
                                    <div className="space-text" style={{whiteSpace: "pre-line"}}>
                                        {holiday.data[0].description}
                                    </div>
                                </>
                            )}
                        </div>
                    </div>
                </div>
            </div>
        </>
    );
}
