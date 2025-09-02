'use client'
import { useEffect, useState } from 'react';
import { Button, Modal } from 'react-bootstrap';
import 'public/assets/css/popup.scss';
import { useI18n } from '@/locales/client'
import FormGroup from './formGroup'
import FormGroupDesktop from "@/app/[locale]/components/layouts/popup/formGroupDesktop";
import IsNotDelevery from './isNotDelevery'
import variables from '/public/assets/css/formGroup.module.scss'
import { api } from "@/utils/axios";
import Cookies from 'js-cookie';
import { useRouter, usePathname } from 'next/navigation'
import { useAppSelector, useAppDispatch } from '@/redux/hooks'
import { changeType, changeTypeFlag, rootAddToCart, rootToggleAddToCartSuccess, changeRootCartItemTmp, addCouponToCart, addGroupOrderSelected, addGroupOrderSelectedNow } from '@/redux/slices/cartSlice'
import { ToastContainer, toast, Slide } from 'react-toastify';
import 'react-toastify/dist/ReactToastify.css';
import { setflagNextData } from '@/redux/slices/flagNextSlice'
import { setGroupOrderDeskData } from '@/redux/slices/groupOrderDeskSlice'
import useMediaQuery from '@mui/material/useMediaQuery'
import { setOptionsStoreData } from '@/redux/slices/optionsStoreSlice';
import { setTmpGroupId } from '@/redux/slices/groupOrderSlice';
import { setFlagDesktopChangeType } from '@/redux/slices/flagDesktopChangeTypeSilce'

export const ORDER_TYPE = {
    TAKE_AWAY: 0,
    DELIVERY: 1,
    GROUP_ORDER: 2
};

export const ERROR_TYPE = {
    NOT_DELIVERY: 1,
    NOT_FOR_SALE: 2,
};


export default function GroupOrder({ toggleClick, dataCart, origin, color }: { toggleClick: any, dataCart: any, origin?: any, color: any }) {
    const workspaceId = useAppSelector((state) => state.workspaceData.globalWorkspaceId)
    const dispatch = useAppDispatch()
    const [show, setShow] = useState(false);
    const [errorType, setErrorType] = useState(0);
    const groupOrderSelected = useAppSelector<any>((state: any) => state.cart.groupOrderSelected);
    const handleClose = () => {
        setShow(false);
        toggleClick();
        Cookies.remove('groupOrder')
    };

    let rootCartItemTmp = useAppSelector((state: any) => state.cart.rootCartItemTmp)
    const handleShow = () => setShow(true);

    useEffect(() => {
        // const hasShownPopup = localStorage.getItem('hasShownPopup');
        const hasShownPopup = false;

        if (!hasShownPopup) {
            setShow(true);
        }
    }, []);
    const [isFormGroupOpen, setIsFormGroupOpen] = useState(false);
    const [isNotDeliveryOpen, setIsNotDeliveryOpen] = useState(false);
    const toggleFormGroup = () => {
        setIsFormGroupOpen(!isFormGroupOpen);
    }
    const toggleIsNotDelivery = () => {
        setIsNotDeliveryOpen(!isNotDeliveryOpen);
    }
    const [isSuccess, setIsSuccess] = useState(false);
    const [listGroup, setListGroup] = useState([]);
    const [inputValue, setInputValue] = useState('');
    const [inputTime, setInputTime] = useState('');
    const [isInputFocused, setInputFocused] = useState(false);
    const [isNoItem, setIsNoItem] = useState(false);
    const [isDisabled, setIsDisabled] = useState(true);
    const [isShow, setIsShow] = useState(true);
    const [storeValue, setStoreValue] = useState('');
    const [storeId, setStoreId] = useState(0);
    const isMobile = useMediaQuery('(max-width: 1279px)');


    const router = useRouter()

    const handleCheck = (check: boolean) => {
        setIsSuccess(check);
    }
    const trans = useI18n()
    const language = Cookies.get('Next-Locale');
    const tokenLoggedInCookie = Cookies.get('loggedToken');

    useEffect(() => {
        toast.dismiss();
        // Hiển thị toast
        if (isSuccess) {
            toast(trans('contact-success'), {
                position: toast.POSITION.BOTTOM_CENTER,
                autoClose: 1500,
                hideProgressBar: true,
                closeOnClick: true,
                closeButton: false,
                transition: Slide,
                className: 'messages',
            });
        }
    }, [isSuccess]);

    const [modalHeight, setModalHeight] = useState('40%');

    useEffect(() => {
        setModalHeight(isInputFocused ? '60%' : '48%');
    }, [isInputFocused]);

    const modalContent = document.querySelector('#group-order .modal-content');
    if (modalContent instanceof HTMLElement) {
        modalContent.style.height = modalHeight;
    }

    const handleSearch = (e: any) => {
        const value = e.target.value;
        if (value.length > 2 && workspaceId) {
            api.get(`groups?keyword=${value}&workspace_id=${workspaceId}&limit=1000`, {
                headers: {
                    'Content-Language': language,
                    'Timestamp': `${Math.floor(Date.now() / 1000)}`,
                    'Timezone': `${Intl.DateTimeFormat().resolvedOptions().timeZone}`,
                }
            }).then(res => {
                if (res?.status == 200 && res?.data?.success == true) {
                    setListGroup(res?.data?.data?.data);
                    if (res?.data?.data?.data?.length == 0) {
                        setIsNoItem(true)
                    } else {
                        setIsNoItem(false)
                    }
                }
            }).catch(err => {
                // console.log(err);
            });
        }
    }
    const handleItemClick = (item: any) => {
        setInputTime(item.close_time)
        setInputValue(item.name);
        setStoreValue(item.name);
        setStoreId(item.id);
        setIsDisabled(false);
        setIsShow(false);
        if (!isMobile) {
            dispatch(setflagNextData(true));
            dispatch(setGroupOrderDeskData(item));
        }
        dispatch(addGroupOrderSelected(item));
        // Set group_id temporary
        dispatch(setTmpGroupId(item.id));
    };

    const handleFocus = () => {
        setInputFocused(true);
        if (!isMobile) {
            dispatch(setflagNextData(false));
            dispatch(setGroupOrderDeskData(null));
        }
    };

    const handleBlur = () => {
        setInputFocused(false);
    };

    const handleInputing = () => {
        Cookies.remove('fromDesk')
        if (storeValue.length > 1) {
            setInputValue('');
            setIsShow(true);
            setStoreValue('');
            setStoreId(0);
            setInputTime('')
            // Clear group_id temporary
            dispatch(setTmpGroupId(null));
        }
        setIsDisabled(true);
    }
    const pathname = usePathname();
    const onClickChangeType = (type: number) => {
        const isCartPage = pathname.includes('cart');
        if (isCartPage) {
            dispatch(changeType(type))
            if (rootCartItemTmp != null) {
                dispatch(rootAddToCart(rootCartItemTmp))
                dispatch(rootToggleAddToCartSuccess())
                dispatch(changeRootCartItemTmp(null))
                dispatch(changeTypeFlag(false))
            }
            if (storeId > 0) {
                setShow(false);
                dispatch(changeTypeFlag(false))
                if (groupOrderSelected) {
                    dispatch(addGroupOrderSelectedNow(groupOrderSelected))
                    Cookies.remove('groupOrder')
                }
            }
        } else {
            api.get(`/groups/${storeId}`, {
                headers: {
                    'Authorization': `Bearer ${tokenLoggedInCookie}`,
                    'Content-Language': language,
                }
            }).then((groupDetail: any) => {
                const groupDetailData = groupDetail?.data?.data;
                const productData = dataCart?.product?.data;

                if (groupDetailData?.is_product_limit !== 0) {
                    var isNotForSale = groupDetailData && (groupDetailData?.type === ORDER_TYPE.TAKE_AWAY || groupDetailData?.type === ORDER_TYPE.DELIVERY) && groupDetailData?.products.findIndex((prod: any) => prod.id === productData?.id) < 0;
                }
                const isNotDelivery = (groupDetailData?.type === ORDER_TYPE.DELIVERY) && !productData?.category.available_delivery;
                if (isNotDelivery) {
                    setErrorType(ERROR_TYPE.NOT_DELIVERY);
                    toggleIsNotDelivery();
                    setShow(false);
                    if (groupOrderSelected) {
                        dispatch(addGroupOrderSelectedNow(groupOrderSelected))
                        Cookies.remove('groupOrder')
                    }
                } else if (isNotForSale) {
                    setErrorType(ERROR_TYPE.NOT_FOR_SALE);
                    toggleIsNotDelivery();
                    setShow(false);
                    if (groupOrderSelected) {
                        dispatch(addGroupOrderSelectedNow(groupOrderSelected))
                        Cookies.remove('groupOrder')
                    }
                } else {
                    dispatch(changeType(type))
                    if (rootCartItemTmp != null) {
                        dispatch(rootAddToCart(rootCartItemTmp))
                        dispatch(rootToggleAddToCartSuccess())
                        dispatch(changeRootCartItemTmp(null))
                    }
                    if (storeId > 0) {
                        setShow(false);
                        dispatch(changeTypeFlag(false))
                        if (groupOrderSelected) {
                            dispatch(addGroupOrderSelectedNow(groupOrderSelected))
                            Cookies.remove('groupOrder')
                        }
                        router.push(`/category/products`);
                    }
                }
            })
        }
    }
    return (
        <>
            {origin == 'desktop' ? (
                <>
                    <h4 className={`ms-2 ${variables.heading}`}>{trans('chose-group')}</h4>

                    <div className='d-flex'>
                        <input type="text" id="controling" className={`${variables.inputing} group-input`} placeholder={trans('enter-infores')}
                            onKeyUp={handleSearch} value={inputValue}
                            onChange={(e: any) => setInputValue(e.target.value)} onFocus={handleFocus}
                            onBlur={handleBlur} onClick={handleInputing}
                            style={{ height: '42px' }}
                        />
                    </div>
                    {(listGroup.length == 0 && isInputFocused) && (
                        <div className={`${variables.listEmpty}`}>
                            {isNoItem && (
                                <p>{trans('no-result')}</p>
                            )}
                        </div>
                    )}
                    {listGroup && listGroup.length > 0 && isShow && (
                        <div className={`${variables.listContain}`}>
                            {listGroup.map((item: any, index: any) => (
                                <div
                                    className={`d-flex flex-row justify-content-between list-group ${index > 0 ? 'no-padding' : ''} mb-2`}
                                    key={item.id}
                                    onClick={() => handleItemClick(item)}
                                >
                                    <div><p className={`${variables.listGroupText}`}>{item.name}</p></div>
                                    <div className={`${variables.closeTime}`}><p>{trans('close-time')} {item.close_time.split(':').slice(0, 2).join(':')}</p></div>
                                </div>
                            ))}

                        </div>
                    )}

                    {!isInputFocused && !(listGroup && listGroup.length > 0 && isShow) && (
                        <div className={`mt-3 ${variables.textong}`}>
                            <p>
                                {trans('register-group-first')} <b>{trans('company')}</b>
                                <b className='text-lowercase'> {trans('group')} of</b> <b>{trans('class')}</b> {trans('register-group-second')}
                                <span className={`${variables['contact-us']}`} style={{ color: color }}
                                    onClick={() => { toggleFormGroup(); dispatch(setFlagDesktopChangeType(false)); Cookies.set('opendedAddressDesk', 'true') }}> {trans('contact-us')}.</span>
                            </p>
                        </div>
                    )}

                </>
            ) : (
                <>

                    <Button variant="primary" onClick={handleShow} style={{ display: 'none' }}></Button>

                    <Modal show={show} onHide={handleClose}
                        aria-labelledby="contained-modal-title-vcenter"
                        centered id='group-order'
                    >
                        <div className={`mx-auto`} style={{ alignItems: 'center' }}>
                            <svg xmlns="http://www.w3.org/2000/svg" width="101" height="3" viewBox="0 0 101 3" fill="none">
                                <path d="M2 1.5H99" stroke="#E1E1E1" strokeWidth="3" strokeLinecap="round" />
                            </svg>
                        </div>
                        <Modal.Header>
                            <h1 className='ms-2'>{trans('chose-group')}</h1>
                        </Modal.Header>
                        <Modal.Body>
                            <div>
                                <input type="text" className='inputing' placeholder={trans('enter-infores')} onKeyUp={handleSearch} value={inputValue} onChange={(e: any) => setInputValue(e.target.value)} onFocus={handleFocus} onBlur={handleBlur} onClick={handleInputing} />
                            </div>
                            {(listGroup.length == 0 && isInputFocused) && (
                                <div className='list-empty'>
                                    {isNoItem && (
                                        <p>{trans('no-result')}</p>
                                    )}
                                </div>
                            )}
                            {listGroup && listGroup.length > 0 && isShow && (
                                <div className='list-contain'>
                                    {listGroup.map((item: any, index: any) => (
                                        <div
                                            className={`d-flex flex-row justify-content-between ${variables.listGroup} ${index > 0 ? 'no-padding' : ''}`}
                                            key={item.id}
                                            onClick={() => handleItemClick(item)}
                                        >
                                            <div><p className='list-group-text'>{item.name}</p></div>
                                            <div className='close-time'><p>{trans('close-time')} {item.close_time.split(':').slice(0, 2).join(':')}</p></div>
                                        </div>
                                    ))}

                                </div>
                            )}
                            {!isInputFocused && (
                                <div className='mt-3'>
                                    <p>
                                        {trans('register-group-first')} <b>{trans('company')}</b> <b className='text-lowercase'>{trans('group')}</b> of <b>{trans('class')}</b> {trans('register-group-second')} <span onClick={toggleFormGroup}>{trans('contact-us')}.</span>
                                    </p>
                                </div>
                            )}
                            {!isInputFocused && (
                                <div className={`mx-auto starting`}>
                                    <button
                                        type="button"
                                        className="btn btn-dark border-0"
                                        disabled={isDisabled}
                                        onClick={() => { dispatch(addCouponToCart(null)); onClickChangeType(3); dispatch(setOptionsStoreData(null)); }}
                                    >
                                        {trans('start-order-popup')}
                                    </button>
                                </div>
                            )}
                        </Modal.Body>
                        <Modal.Footer>
                        </Modal.Footer>
                    </Modal>
                </>
            )}

            {(isFormGroupOpen && !origin) && (
                <FormGroup toggleFormGroup={() => toggleFormGroup()} isSuccess={(check: boolean) => handleCheck(check)} />
            )}

            {(isFormGroupOpen && origin == 'desktop') && (
                <FormGroupDesktop toggleFormGroup={() => toggleFormGroup()} isSuccess={(check: boolean) => handleCheck(check)} />
            )}

            {isNotDeliveryOpen &&
                <IsNotDelevery toggleIsNotDelivery={() => toggleIsNotDelivery()} storeId={storeId ? storeId : ''} rootCartItemTmp={rootCartItemTmp ? rootCartItemTmp : ''} errorType={errorType ? errorType : ''} />
            }
            <ToastContainer />
        </>
    );
}
