import AsyncStorage from '@react-native-async-storage/async-storage';
import { logError } from '@src/utils/logger';

export const storeData = async (key: string, value: string) => {
    try {
        await AsyncStorage.setItem(key, value);
    } catch (e) {
        logError('Storage Save Error', e);
    }
};

export const getStorageData = async (key: string) => {
    try {
        const value = await AsyncStorage.getItem(key);
        return value;
    } catch (e) {
        logError('Storage Reading Value Error', e);
    }
};