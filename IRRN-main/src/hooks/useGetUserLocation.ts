import { useEffect } from 'react';

import Geolocation from 'react-native-geolocation-service';

import { LocationActions } from '@src/redux/toolkit/actions/locationActions';
import { getAddressFromCoordinates } from '@src/utils/locationUtil';
import { log, logError } from '@src/utils/logger';

import { useAppDispatch } from './';

const useGetUserLocation = (active: boolean) => {
    const dispatch = useAppDispatch();

    useEffect(() => {
        if (active) {
            Geolocation.getCurrentPosition(
                    async (position) => {
                        log('getCurrentPosition', position);
                        const lat = position?.coords?.latitude;
                        const lng = position?.coords?.longitude;
                        const address = await getAddressFromCoordinates(lat, lng);
                        dispatch(LocationActions.setLocation({ lat: lat, lng: lng, address: address || '' }));
                    },
                    (error) => {
                        logError(error.code, error.message);
                    },
                    { enableHighAccuracy: true, timeout: 5000, maximumAge: 10000 }
            );
        }

    }, [active, dispatch]);
};

export default useGetUserLocation;