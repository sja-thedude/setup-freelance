'use client'

import React from 'react';
import Image from "next/image";
import variables from '/public/assets/css/food.module.scss';
import ProductCard from "./product-card";
import { useI18n } from '@/locales/client';

const titleTextFirst = variables['title-text-first'];
const titleImage = variables['title-image'];
const underLine = variables['underline-title'];
const empty = variables['empty'];

export default function Searched({products, color, baseLink}: {products: any, color: string, baseLink:string}) {
    const trans = useI18n();

    return (
        <>
            <div className="col-sm-12 col-xs-12">
                <div className="d-flex justify-content-between">
                        <div key={1} className={`${titleTextFirst} ms-3`}>
                            <h1 className={underLine}>{ trans('favorites') }</h1>
                        </div>
                    <div className={titleImage}>
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                            <path d="M21 20H14" stroke="#413E38" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" />
                            <path d="M10 20H3" stroke="#413E38" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" />
                            <path d="M21 12H12" stroke="#413E38" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" />
                            <path d="M8 12H3" stroke="#413E38" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" />
                            <path d="M14 23V17" stroke="#413E38" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" />
                            <path d="M8 15V9" stroke="#413E38" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" />
                        </svg>
                    </div>
                </div>
            </div>
            <div className='row ps-2 pe-2' id={variables.group}>
                <div className='col-sm-12 col-xs-12'>
                    {products && products.length > 0 ? (
                        products.map((item: any, index: number) => (
                            <ProductCard baseLink={baseLink} key={item.id} index={index} item={item} color={color} isLastProduct= { (index === products.length - 1) } from='search' handleCloseSuggest='' groupOrder=''/>
                        ))
                    ) : (
                        <div className={`${empty}`}>{trans('no-favorite-items')}</div>
                    )}
                </div>
            </div>
        </>
    );
};
