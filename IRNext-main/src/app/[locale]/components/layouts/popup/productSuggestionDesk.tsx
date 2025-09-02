'use client'
import { useEffect, useState } from 'react';
import { Button, Modal } from 'react-bootstrap';
import 'public/assets/css/popup.scss';
import { useI18n } from '@/locales/client'
import ProductCardDesktop from "@/app/[locale]/components/product/product-card-desk";
import { useSearchParams } from 'next/navigation'
import styled from 'styled-components';
import Cookies from "js-cookie";
import { useAppSelector } from '@/redux/hooks'

export default function ProductSuggestionDesk(props: any) {
    const { color, togglePopup, suggestionProduct, baseLink, activeStep2 } = props
    const groupOrderNowSlice = useAppSelector<any>((state: any) => state.cart.groupOrderSelectedNow);
    const [show, setShow] = useState(false);

    const handleClose = () => {
        togglePopup();
        setShow(false);
        Cookies.remove('fromDesk')
        Cookies.remove('fromSuggestDesk')
        if(Cookies.get('addProductSuggest') != 'true') {
            Cookies.set('oppenedSuggest', 'true')
        } else {
            Cookies.remove('addProductSuggest')
        }
    };

    const handleNothank = () => {
        togglePopup();
        setShow(false);
        activeStep2();
        Cookies.set('oppenedSuggest', 'true')
    };

    const handleShow = () => setShow(true);

    useEffect(() => {
        // const hasShownPopup = localStorage.getItem('hasShownPopup');
        const hasShownPopup = false;

        if (!hasShownPopup) {
            setShow(true);
        }
        Cookies.set('fromSuggestDesk', 'true')
    }, []);

    useEffect(() => {
        if (Cookies.get('addProductSuggest') == 'true') {
            handleClose()
        }
    }, [Cookies.get('addProductSuggest')])

    function chunkArray(array: any[], size: number) {
        return Array.from({ length: Math.ceil(array.length / size) }, (v, index) =>
            array.slice(index * size, index * size + size)
        );
    }

    const trans = useI18n()

    const CustomScrollbar = styled.div`
    overflow-y: auto;
    padding-top: 0;
    max-height: 76vh !important;
    padding: 40px;
    .tail-contain {
        margin-top: 2%;
      }

      .description {
        color: #413E38;
        text-align: center;
        font-family: SF Compact Display;
        font-size: 14px;
        font-style: normal;
        font-weight: 400;
        line-height: normal;
        letter-spacing: -0.24px;
      }

      .detail {
        color: #757575;
        text-align: center;
        font-family: Roboto;
        font-size: 14px;
        font-style: normal;
        font-weight: 400;
        line-height: normal;
        width: 92%;
      }

      .workspace-content {
        text-align: justify;
      }

      p {
        text-align: center;
      }

      .form-control-account {
        color: var(--Input-field, #717171);
        font-family: SF Compact Display;
        font-size: 14px;
        font-weight: 400;
      }
    &::-webkit-scrollbar-thumb {
        background: ${color};
        width: 5px;
        height: 80%;
        border-right: 20px white solid;
        background-clip: padding-box;
    }
    &::-webkit-scrollbar {
        width: 25px;
        height: 492px;
    }
`;
    return (
        <>
            <Button variant="primary" onClick={handleShow} style={{ display: 'none' }}></Button>

            <Modal show={show} onHide={handleClose}
                aria-labelledby="contained-modal-title-vcenter"
                centered id='product-suggestion-desk' style={{ scrollbarColor: color ? color : '#D87833' }}
            >
                <div className='suited' onClick={handleClose}>
                    <svg xmlns="http://www.w3.org/2000/svg" width="17" height="17" viewBox="0 0 17 17" fill="none">
                        <path d="M12.1724 4.5L4.17236 12.5" stroke="#888888" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" />
                        <path d="M4.17236 4.5L12.1724 12.5" stroke="#888888" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" />
                    </svg>
                    <div className='text-uppercase'>{trans('close')}</div>
                </div>
                <Modal.Header>
                    <h1>{trans('fits')}</h1>
                </Modal.Header>
                <CustomScrollbar>
                    <div className='col-12'>
                        {suggestionProduct.length > 0 ? (
                            chunkArray(suggestionProduct, 2).map((row, rowIndex) => (
                                <div className="row" key={rowIndex}>
                                    {row.map((item: any, index: number) => (
                                        <div className="col-md-6" key={index}>
                                            <ProductCardDesktop
                                                index={index}
                                                item={item}
                                                color={color}
                                                isLastProduct={false}
                                                baseLink={baseLink}
                                                from={`productSuggestion`}
                                                handleCloseSuggest={handleClose}
                                                groupOrder={`${groupOrderNowSlice ? groupOrderNowSlice?.id : ''}`}
                                            />
                                        </div>
                                    ))}
                                </div>
                            ))
                        ) : (
                            null
                        )}
                    </div>
                </CustomScrollbar>
                <Modal.Footer>
                    <div className={'mx-auto mt-2'}>
                        <button
                            type="button"
                            className="btn btn-dark border-0 btn-desk-no"
                            onClick={handleNothank}
                            style={{ backgroundColor: color ? color : '#D87833' }}
                        >
                            {trans('continue-desk')}
                        </button>
                    </div>
                </Modal.Footer>
            </Modal>
        </>
    );
}
