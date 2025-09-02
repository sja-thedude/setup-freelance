'use client'

import React from 'react';
import ProductList from "@/app/[locale]/components/product/product-list";
import Loading from "@/app/[locale]/components/loading";
import useMediaQuery from '@mui/material/useMediaQuery';
import {useDebounce} from "use-debounce";
import 'public/assets/css/only.products.responsive.scss';

export default function Page() {
    const [isLoading, setIsLoading] = useDebounce(true, 400);
    const isMobile = useMediaQuery('(max-width: 1279px)');

    return (
        <>
            {isLoading && <Loading/>}
            <div className='row' style={{ backgroundColor: isMobile ? '#f8f8f8' : '#FFFFFF', minHeight: isMobile ? '100%' : '100dvh'}}>
                <div className='col-12' style={{position: 'relative'}}>
                    <ProductList setIsLoading={setIsLoading} baseLink={'/category/products'} />
                </div>
            </div>
        </>
    );
};