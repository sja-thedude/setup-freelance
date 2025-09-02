'use client'
import { useEffect, useState } from 'react';
import { Button, Modal } from 'react-bootstrap';
import 'public/assets/css/popup.scss';
import { useI18n } from '@/locales/client'
import ProductCard from "@/app/[locale]/components/product/product-card";
import { usePathname, useSearchParams } from 'next/navigation'
import { useAppSelector } from '@/redux/hooks'
import Cookies from "js-cookie";

export default function ProductSuggestion(props: any) {
    const { color, togglePopup, suggestionProduct, baseLink, activeStep2 } = props
    const [show, setShow] = useState(false);
    const searchParams = useSearchParams()
    const groupOrderNowSlice = useAppSelector<any>((state: any) => state.cart.groupOrderSelectedNow);
    const handleClose = () => {
        togglePopup();
        setShow(false);
        if(Cookies.get('addProductSuggest') == 'true') {
           Cookies.remove('addProductSuggest')
        } else {
          activeStep2();
        }
    };

    const handleNothank = () => {
        togglePopup();
        setShow(false);
        activeStep2();
    };

    const handleShow = () => setShow(true);

    useEffect(() => {
        // const hasShownPopup = localStorage.getItem('hasShownPopup');
        const hasShownPopup = false;

        if (!hasShownPopup) {
            setShow(true);
        }
    }, []);

    const trans = useI18n()
    // check mobile
    const [isMobile, setIsMobile] = useState(false);
    const checkWindowSize = () => {
        const width = window.innerWidth;
        setIsMobile(width <= 991);
    };

    useEffect(() => {
        checkWindowSize();
        window.addEventListener('resize', checkWindowSize);
        return () => {
            window.removeEventListener('resize', checkWindowSize);
        };
    }, []);

    useEffect(() => {
        if (Cookies.get('addProductSuggest') == 'true') {
            handleClose()
        }
    }, [Cookies.get('addProductSuggest')])

    return (
        <>
            <Button variant="primary" onClick={handleShow} style={{ display: 'none' }}></Button>

            <Modal show={show} onHide={handleClose}
                aria-labelledby="contained-modal-title-vcenter"
                centered id='product-suggestion'
            >
                <div className={`mx-auto`} style={{ alignItems: 'center' }}>
                    <svg xmlns="http://www.w3.org/2000/svg" width="101" height="3" viewBox="0 0 101 3" fill="none">
                        <path d="M2 1.5H99" stroke="#E1E1E1" strokeWidth="3" strokeLinecap="round" />
                    </svg>
                </div>
                <Modal.Header>
                    <h1>{trans('fits')}</h1>
                </Modal.Header>
                <Modal.Body>
                    {suggestionProduct.length > 0 ? (
                        suggestionProduct.map((item: any, index: number) => (
                            <ProductCard
                                key={item.id}
                                index={index}
                                item={item}
                                color={color}
                                isLastProduct={false}
                                baseLink={baseLink}
                                from={`productSuggestion`}
                                handleCloseSuggest={handleClose}
                                groupOrder={`${groupOrderNowSlice ? groupOrderNowSlice?.id : ''}`}
                            />
                        ))
                    ) : (
                        null
                    )}
                </Modal.Body>

                <Modal.Footer>
                    <div className={'mx-auto mt-2'}>
                        <button
                            type="button"
                            className="btn btn-dark border-0"
                            style={{ width: "100%" }}
                            onClick={handleNothank}
                        >
                            {trans('no-thanks')}
                        </button>
                    </div>
                </Modal.Footer>
            </Modal>
        </>
    );
}
