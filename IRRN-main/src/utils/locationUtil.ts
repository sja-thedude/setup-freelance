import Config from 'react-native-config';
import {
    PERMISSIONS,
    request,
} from 'react-native-permissions';

import {
    DEVICE_LANGUAGE_CODE,
    IS_ANDROID,
} from '@src/configs/constants';

import { log, logError } from './logger';

export const getAddressFromCoordinates  = async (latitude: number, longitude: number) => {
    try {
        const response = await fetch(`https://maps.googleapis.com/maps/api/geocode/json?latlng=${latitude},${longitude}&language=${DEVICE_LANGUAGE_CODE}&key=${Config.ENV_GG_MAP_API_KEY}`);
        const json = await response.json();
        log('getAddressFromCoordinates', json);
        if (json.results) {
            return json?.results[0]?.formatted_address;
        } else {
            return null;
        }

    } catch (error) {
        logError(error);
    }
};

export const searchAddress  = async (keyWord: string) => {
    try {
        if (keyWord.trim()) {
            const response = await fetch(`https://maps.googleapis.com/maps/api/place/autocomplete/json?input=${keyWord}&language=${DEVICE_LANGUAGE_CODE}&types=address&key=${Config.ENV_GG_MAP_API_KEY}`);
            const json = await response.json();
            log('searchAddress', json);
            return json?.predictions || [];
        } else {
            return [];
        }
    } catch (error) {
        logError(error);
    }
};

export const getCoordinatesFromAddress  = async (placeID: any) => {
    try {
        const response = await fetch(`https://maps.googleapis.com/maps/api/place/details/json?place_id=${placeID}&language=${DEVICE_LANGUAGE_CODE}&key=${Config.ENV_GG_MAP_API_KEY}`);
        const json = await response.json();
        log('getCoordinatesFromAddress', json);
        if (json.result) {
            return {
                lat: json.result.geometry.location.lat,
                lng: json.result.geometry.location.lng,
            };
        } else {
            return null;
        }

    } catch (error) {
        logError(error);
    }
};

export const checkLocationPermission = async () => {
    try {
        const permission = IS_ANDROID ? PERMISSIONS.ANDROID.ACCESS_FINE_LOCATION : PERMISSIONS.IOS.LOCATION_WHEN_IN_USE;
        const resultPermission = await request(permission);
        return resultPermission;
    } catch (error) {
        logError('Request Permission Error', error);
    }
};