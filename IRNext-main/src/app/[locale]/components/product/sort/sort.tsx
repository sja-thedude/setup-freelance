'use client'
import React, { useRef, useEffect, useState } from 'react';
import variables from '/public/assets/css/food.module.scss'
import { useI18n } from '@/locales/client'

const sorting = variables['sorting'];
const option = variables['option-sorting'];

export default function SortProduct({ color, toggleSortPopup, handleSort, selectedOption }: { color: string, toggleSortPopup: any, handleSort: (option: number) => void, selectedOption: any }) {

    const trans = useI18n();
    const standard = 0;
    const price = 1;
    const name = 2;
    return (
        <>
            <div className={`${sorting} row`} style={{ zIndex: "200" }} onClick={toggleSortPopup}>
                <div className='row ms-2'><h1>{trans('sort-by')}</h1></div>
                <div className='row d-flex' style={{ flexDirection: "column" }}>
                    <div className={`${option} d-flex ms-2`} onClick={() => { handleSort(standard) }} style={{}}>
                        {selectedOption === standard && (
                            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="13" viewBox="0 0 12 13" fill="none">
                                <path d="M10 3.25L4.5 9.20833L2 6.5" stroke={color ? color : 'black'} strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" />
                            </svg>
                        )}
                        <p className={`ms-2`} style={{color: selectedOption === standard ? (color ? color : 'black') : 'black'}}>{trans('standard')}</p>
                    </div>
                    <div className={`${option} d-flex ms-2`} onClick={() => { handleSort(price) }}>
                        {selectedOption === price && (
                            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="13" viewBox="0 0 12 13" fill="none">
                                <path d="M10 3.25L4.5 9.20833L2 6.5" stroke={color ? color : 'black'} strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" />
                            </svg>
                        )}
                        <p className={`ms-2`} style={{color: selectedOption === price ? (color ? color : 'black') : 'black'}}>{trans('sort-by-price')}</p>
                    </div>
                    <div className={`${option} d-flex ms-2`} onClick={() => { handleSort(name) }}>
                        {selectedOption === name && (
                            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="13" viewBox="0 0 12 13" fill="none">
                                <path d="M10 3.25L4.5 9.20833L2 6.5" stroke={color ? color : 'black'} strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" />
                            </svg>
                        )}
                        <p className={`ms-2`} style={{color: selectedOption === name ? (color ? color : 'black') : 'black'}}>{trans('sort-by-name')}</p>
                    </div>
                </div>
            </div>
        </>
    );
};
