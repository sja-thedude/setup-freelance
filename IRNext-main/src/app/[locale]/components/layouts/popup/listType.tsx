'use client'
import React, { useEffect, useState } from 'react';
import { Button, Modal } from 'react-bootstrap';
import 'public/assets/css/popup.scss';
import { useI18n } from '@/locales/client'
import axios from "axios";
import * as config from "@/config/constants";

export default function List({ toggleOrderType }: { toggleOrderType: any}) {
    const [show, setShow] = useState(false);
    const handleClose = () => {
        toggleOrderType(); // Thêm console.log ở đây
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
    const [isGroupOrderOpen, setIsGroupOrderOpen] = useState(false);

    const handleClick = () => {
        setIsGroupOrderOpen(!isGroupOrderOpen);
    }

    const [isDeliveryOrderOpen, setIsDeliveryOrderOpen] = useState(false);
    const initialRef: any = null;
    const [currentLocation, setCurrentLocation] = useState(initialRef);
    const [currentAddress, setCurrentAddress] = useState(initialRef);
    useEffect(() => {
        if (!currentAddress) {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                    (position) => {
                        const {latitude, longitude} = position.coords;
                        //setSelectedPlace(null);
                        //setSearchLngLat(null);
                        setCurrentLocation({lat: latitude, lng: longitude});
                    },
                    (error) => {
                        // console.log(error);
                    }
                );
                if (!currentAddress) {
                    getAddressFromCoordinates(currentLocation?.lat, currentLocation?.lng, 1);
                }
            } else {
                // console.log("Geolocation is not supported by this browser.");
            }
        }
    }, [currentLocation]);

    // get address from coordinates
    const getAddressFromCoordinates  = async (latitude: number, longitude: number, type : number) => {
        axios.get(`https://maps.googleapis.com/maps/api/geocode/json?latlng=${latitude},${longitude}&language=${config.LANGUAGE_CODE}&key=${config.PUBLIC_GOOGLE_MAPS_API_KEY_DISTANCE}`, {})
            .then((res) => {
                const json = res.data;
                if (json.results) {
                    if (type == 1) {
                        setCurrentAddress(json?.results[0]?.formatted_address)
                    }
                    return json?.results[0]?.formatted_address;

                } else {
                    setCurrentAddress(null)
                    return null;
                }
            }).catch(err => {
            // console.log(err)
            return null;
        });
    };

    const handleClickDelivery = () => {
        setIsDeliveryOrderOpen(!isDeliveryOrderOpen);
    }

    const trans = useI18n()
    return (
        <>
            <Button variant="primary" onClick={handleShow} style={{ display: 'none' }}></Button>

            <Modal show={show} onHide={handleClose}
                aria-labelledby="contained-modal-title-vcenter"
                centered id='list-type'
            >
                <Modal.Header>
                    <h1>{trans('fits')}</h1>
                </Modal.Header>
                <Modal.Body>
                <div>{trans('lang_afaal')}</div>
                <div onClick={handleClickDelivery}>{trans('lang_levering')}</div>
                <div onClick={handleClick}>{trans('lang_groepsbestelling')}</div>
                </Modal.Body>
                <Modal.Footer>
                </Modal.Footer>
            </Modal>
        </>
    );
}
