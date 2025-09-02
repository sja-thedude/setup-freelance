'use client'
import { useEffect, useState } from 'react';
import 'public/assets/css/popup.scss';
import { useI18n } from '@/locales/client'
import variables from '/public/assets/css/food.module.scss'
import Cookies from 'js-cookie';
import useMediaQuery from '@mui/material/useMediaQuery'

const sorting = variables['sorting'];
const option = variables['option-sorting'];
const resMobile = variables['res-mobile'];
export default function SortProducts({ color, toggleSortPopup, handleSort, selectedOption }: { color: any, toggleSortPopup: any, handleSort: (option: number) => void, selectedOption: any }) {
    const [show, setShow] = useState(false);
    const handleClose = () => {
        toggleSortPopup(); // Thêm console.log ở đây
        setShow(false);
    };

    const handleShow = () => setShow(true);

    useEffect(() => {
        // const hasShownPopup = localStorage.getItem('hasShownPopup');
        const hasShownPopup = false;

        if (!hasShownPopup) {
            setShow(true);
        }
    }, []);

    const standard = 0;
    const price = 1;
    const name = 2;
    const priceDecen = 3;
    const language = Cookies.get('Next-Locale');
    const trans = useI18n()
    const isMobile = useMediaQuery('(max-width: 1279px)');

    return (
        <>
            {isMobile ? (
                <div className={`${sorting} row ${resMobile}`} style={{ zIndex: "1200", width: 'max-content', backgroundColor: '#FFFFFF' , position: 'absolute' , right: '0' }} onClick={(e:any) => {e.stopPropagation() , toggleSortPopup}}>
                    <div className='row ms-2'><h1>{trans('sort-by')}</h1></div>
                    <div className='d-flex' style={{ flexDirection: "column" }}>
                        <div className={`${option} d-flex ms-2 mb-2`} onClick={() => { handleSort(standard); handleClose() }} style={{}}>
                            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="13" viewBox="0 0 12 13" fill="none" style={{ visibility: selectedOption === standard ? 'inherit' : "hidden" }}>
                                <path d="M10 3.25L4.5 9.20833L2 6.5" stroke={color ? color : 'black'} strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" />
                            </svg>
                            <p className={`${language === 'en' ? 'ms-2' : ''}`} style={{ color: selectedOption === standard ? (color ? color : 'black') : 'black' }}>{trans('standard')}</p>
                        </div>
                        <div className={`${option} d-flex ms-2 mb-2 `} onClick={() => { handleSort(price); handleClose() }} style={{ position: 'relative', paddingLeft: '12px' }}>
                            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="13" viewBox="0 0 12 13" fill="none" style={{ position: 'absolute', left: '0px', visibility: selectedOption === price ? 'inherit' : "hidden" }}>
                                <path d="M10 3.25L4.5 9.20833L2 6.5" stroke={color ? color : 'black'} strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" />
                            </svg>
                            <p className={`${language === 'en' ? 'ms-2' : ''}`} style={{ textAlign: 'start', color: selectedOption === price ? (color ? color : 'black') : 'black', minWidth: 'max-content' }}>{trans('sort-by-price')}</p>
                        </div>
                        <div className={`${option} d-flex ms-2 mb-2`} onClick={() => { handleSort(name); handleClose() }}>
                            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="13" viewBox="0 0 12 13" fill="none" style={{ visibility: selectedOption === name ? 'inherit' : "hidden" }}>
                                <path d="M10 3.25L4.5 9.20833L2 6.5" stroke={color ? color : 'black'} strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" />
                            </svg>
                            <p className={`${language === 'en' ? 'ms-2' : ''}`} style={{ color: selectedOption === name ? (color ? color : 'black') : 'black' }}>{trans('sort-by-name')}</p>
                        </div>
                    </div>
                </div>
            ) : (
               null
            )}
        </>
    );
}
