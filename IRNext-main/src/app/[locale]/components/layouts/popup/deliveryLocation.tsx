'use client'

import React, { useEffect, useState } from 'react';
import { Modal } from 'react-bootstrap';
import 'public/assets/css/popup.scss';
import { useI18n } from '@/locales/client'
import Radio from '@mui/material/Radio';
import RadioGroup from '@mui/material/RadioGroup';
import FormControlLabel from '@mui/material/FormControlLabel';
import {useSelector} from "react-redux";
import Typography from '@mui/material/Typography';
import { useGetApiProfileQuery } from '@/redux/services/profileApi';
import { selectApiProfileData } from "@/redux/slices/profileSlice";
import Location from "../../location/page";
import style from 'public/assets/css/profile.module.scss'
import Cookies from "js-cookie";
import { useAppSelector, useAppDispatch } from '@/redux/hooks'
import { rootCartDeliveryAddress, rootCartDeliveryConditions, addGroupOrderSelectedNow } from '@/redux/slices/cartSlice'
import { api } from "@/utils/axios";
import DeliveryNotShipping from "../../ordering/cart/deliveryNotShipping";
import UnavailableDelivery from "../../ordering/cart/unavailableDelivery";
import { useGetWorkspaceDataByIdQuery } from '@/redux/services/workspace/workspaceDataApi'

export default function DeliveryLocation({ toggleDeliveryOrder, onClickChangeType, currentAddress }: { toggleDeliveryOrder: any, onClickChangeType: any, currentAddress: any}) {
    const [show, setShow] = useState(false);
    const [showEr, setShowEr] = useState(false);
    const [showPopupUnavailableDelivery, setShowPopupUnavailableDelivery] = useState(false);
    const dispatch = useAppDispatch()
    const [showLocationPopup, setShowLocationPopup] = useState('none');
    const [address, setAddress] = useState(currentAddress?.address ?? '');
    const [location, setLocation] = useState({ lat: currentAddress?.lat ?? 0, lng: currentAddress?.lng ?? 0});
    const workspaceId = useAppSelector((state) => state.workspaceData.globalWorkspaceId)
    const { data: apiDataToken } = useGetWorkspaceDataByIdQuery({id: workspaceId})
    const apiData = apiDataToken?.data?.setting_generals;
    const color = useAppSelector((state) => state.workspaceData.globalWorkspaceColor)
    var workspaceName = apiData?.title;
    const [value, setValue] = React.useState(1);
    const tokenLoggedInCookie = Cookies.get('loggedToken');
    useGetApiProfileQuery(tokenLoggedInCookie || '');
    var apiSliceProfile = useSelector(selectApiProfileData);
    const trans = useI18n();
    let rootCartItemTmp = useAppSelector((state) => state.cart.rootCartItemTmp)

    useEffect(() => {
        const hasShownPopup = false;

        if (!hasShownPopup) {
            setShow(true);
        }
    }, []);

    const handleClose = () => {
        toggleDeliveryOrder();
        setShow(false);
    };

    const handleLocation = (address:string, location: any) => {
        setAddress(address);
        setLocation(location);
    }

    const handleCloseLocation = (isShow: boolean) => {
        if (isShow) {
            setShowLocationPopup('block');
        } else {
            setShowLocationPopup('none');
        }
    }

    const handleChange = (event: any) => {
        if (event.target.value == 0
            && (!apiSliceProfile?.data?.address || !apiSliceProfile?.data?.lng || !apiSliceProfile?.data?.lat)) {
            window.location.href = '/profile/edit';
        }
        setValue(event.target.value);
    };

    const [workspaceDeliveryConditions, setWorkspaceDeliveryConditions] = useState(null);
    const deliveryAddress = useAppSelector((state) => state.cart.rootCartDeliveryAddress);
    const handleSave = () => {
        let deliveryAddress = {
            address: apiSliceProfile?.data?.address,
            lat: apiSliceProfile?.data?.lat,
            lng: apiSliceProfile?.data?.lng,
        }
        
        if (value != 0) {
            deliveryAddress = {
                address: address,
                lat: location?.lat,
                lng: location?.lng,
            }
        }
        fetchDeliveryConditions(deliveryAddress);
        dispatch(addGroupOrderSelectedNow(null))
    }

    const fetchDeliveryConditions = (deliveryAddress: any) => {
        const res = api.get(`workspaces/${workspaceId}/settings/delivery_conditions?lat=${deliveryAddress?.lat}&lng=${deliveryAddress?.lng}`, {
            headers: {
                Authorization: `Bearer ${tokenLoggedInCookie}`,
            }
        }).then((res) => {
            const validateDatas = res?.data;
            if (validateDatas?.data && validateDatas?.data.length > 0) {
                setShowEr(false)
                setWorkspaceDeliveryConditions(validateDatas?.data[0]);
                dispatch(rootCartDeliveryAddress(deliveryAddress));
                dispatch(rootCartDeliveryConditions(validateDatas?.data[0]));
                onClickChangeType(2);
                handleClose();
                // const res = api.get(`products/validate_available_delivery?product_id[]=${rootCartItemTmp?.['productId']}`, {})
                //     .then((res) => {
                //         const validateDelivery = res?.data?.data;
                //         if (validateDelivery) {
                //             if (rootCartItemTmp?.['productId'] && !validateDelivery[rootCartItemTmp?.['productId']]) {
                //                 setShowPopupUnavailableDelivery(true)
                //             } else {
                //                 let element: HTMLElement = document.getElementsByClassName('modal-backdrop')[0] as HTMLElement;
                //                 element.click();
                //                 onClickChangeType(2);
                //             }
                //         } else {
                //             let element: HTMLElement = document.getElementsByClassName('modal-backdrop')[0] as HTMLElement;
                //             element.click();
                //             onClickChangeType(2);
                //         }
                //     })
            } else {
                setShowEr(true)
                setWorkspaceDeliveryConditions(null);
            }
        }).catch((err) => {
            if (workspaceDeliveryConditions) {
                setShowEr(false)
            } else {
                setShowEr(true)
            }
        });
    }

    const handlink = () => {
        handleClose();
        window.location.href = '/profile/edit';
    }

    const handleToggle = () => {
        setShowPopupUnavailableDelivery(!showPopupUnavailableDelivery);
        handleClose();
        onClickChangeType(2);
    }

    return (
        <>
            <Modal show={show} onHide={handleClose}
                   aria-labelledby="contained-modal-title-vcenter"
                   centered id='delivery-order' className={`delivery-order`}
            >
                <Modal.Body style={{ height: 'auto' }}>
                    <div className={`mx-auto`} id={'delivery-popup'} style={{ textAlign: 'center' }}>
                        <svg xmlns="http://www.w3.org/2000/svg" width="101" height="3" viewBox="0 0 101 3" fill="none">
                            <path d="M2 1.5H99" stroke="#E1E1E1" strokeWidth="3" strokeLinecap="round" />
                        </svg>
                    </div>
                    <div className={`${style['detail-title']} pt-2 text-center`}>
                        {trans('choose-address-question')}
                    </div>
                    <div className={`group-content`} >
                        <RadioGroup
                            aria-labelledby="demo-radio-buttons-group-label"
                            value={value}
                            name="radio-buttons-group"
                            onChange={handleChange}
                        >
                            {
                                apiSliceProfile?.data?.email && (
                                    <FormControlLabel value={0}
                                                      control={<Radio sx={{
                                                          color: '#717171',
                                                          '&.Mui-checked': {
                                                              color: color,
                                                          },
                                                      }
                                                      }/>}
                                                      label={
                                                          <>
                                                              <Typography className={'main-label'}>{ trans('my-address') }</Typography>
                                                              <Typography className={'sub-label'}>
                                                                  { (apiSliceProfile?.data?.address && apiSliceProfile?.data?.lng ? apiSliceProfile?.data?.address : (
                                                                          <>
                                                                              <span>{trans('go-to')}</span>
                                                                              <span><span onClick={() => handlink()} style={{ color: '#000', textDecoration: 'underline' }}>{' ' + trans('my-profile') + ' '}</span></span>
                                                                              <span>{trans('save-address')}</span>
                                                                          </>
                                                                      )
                                                                  ) }
                                                              </Typography>
                                                          </>
                                                      } />
                                )
                            }
                            <FormControlLabel value={1}
                                              control={<Radio sx={{
                                                color: '#717171',
                                                    '&.Mui-checked': {
                                                        color: color,
                                                    },
                                                }
                                              }

                                              />} label={''} />
                            <div>
                                <div style={{ marginLeft: '30px', marginTop: '-55px' }}>
                                    <div style={{ position: 'relative' }} onClick={() => setShowLocationPopup('block')}>
                                        <svg width="25"
                                             height="24"
                                             viewBox="0 0 25 24"
                                             fill="none"
                                             style={{ position: 'absolute', top: '8px', left: '5px' }}
                                             xmlns="http://www.w3.org/2000/svg">
                                            <g id="search">
                                                <path id="Vector" d="M11.6733 19C16.0066 19 19.5195 15.4183 19.5195 11C19.5195 6.58172 16.0066 3 11.6733 3C7.33999 3 3.82715 6.58172 3.82715 11C3.82715 15.4183 7.33999 19 11.6733 19Z" stroke={ color } strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"/>
                                                <path id="Vector_2" d="M21.4807 20.9999L17.2144 16.6499" stroke={ color } strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"/>
                                            </g>
                                        </svg>
                                        <input
                                            type="text"
                                            value={address ? address : (currentAddress ? currentAddress?.address : '')}
                                            className={`form-control form-control-map`}
                                            id="search-input"
                                            placeholder=" "
                                            style={{ pointerEvents: 'none'}}
                                            autoComplete={'off'}
                                        />
                                    </div>
                                </div>
                            </div>
                        </RadioGroup>
                    </div>
                    <div onClick={() => handleSave()}
                        className={`${style['btn-yes-logout']} btn-confirm`}
                        style={{ width: '165px' }}>
                        { trans('confirm') }
                    </div>
                    <div className="d-flex 123">
                        <div style={{ display: `${showLocationPopup}` }}>
                            <div className="modal-dialog">
                                <div className={`modal-content ${style['modal-content-map']}`}>
                                    <div className="modal-body p-0" >
                                        <Location location = {handleLocation}
                                                  closeLocation = {handleCloseLocation}
                                                  myAddress={apiSliceProfile?.data?.address ?? ""}
                                                  myLocation={{lat:apiSliceProfile?.data?.lat ?? '', lng:apiSliceProfile?.data?.lng ?? ''}}
                                        />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </Modal.Body>
            </Modal>
            { showEr && workspaceDeliveryConditions == null && (
                <DeliveryNotShipping togglePopup={() => {
                    setShowEr(!showEr);
                }} isShow={ showEr } workspaceName={workspaceName} />
            )}
            { showPopupUnavailableDelivery && (
                <UnavailableDelivery togglePopup={() => handleToggle()}
                                     isShow={showPopupUnavailableDelivery}/>
            )}
        </>
    );
}
