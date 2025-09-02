import { Dimensions } from 'react-native';
import DeviceInfo from 'react-native-device-info';

const { height: DEVICE_SCREEN_HEIGHT, width: DEVICE_SCREEN_WIDTH } = Dimensions.get('window');

export const CURRENT_SCREEN_HEIGHT = DEVICE_SCREEN_HEIGHT;
export const CURRENT_SCREEN_WIDTH = DEVICE_SCREEN_WIDTH;

export const X_AND_AFTER_X_DEVICES: string[] = [
    'iPhone17,2', //'iPhone 16 Pro Max'
    'iPhone17,1', //iPhone 16 Pro',
    'iPhone17,4', //iPhone 16 Plus',
    'iPhone17,3', //iPhone 16',

    'iPhone16,2', //'iPhone 15 Pro Max'
    'iPhone16,1', //iPhone 15 Pro',
    'iPhone15,5', //iPhone 15 Plus',
    'iPhone15,4', //iPhone 15',

    'iPhone15,3', //'iPhone 14 Pro Max'
    'iPhone15,2', //iPhone 14 Pro',
    'iPhone14,8', //iPhone 14 Plus',
    'iPhone14,7', //iPhone 14',

    'iPhone14,3', //iPhone 13 Pro Max',
    'iPhone14,2', //iPhone 13 Pro',
    'iPhone14,5', //iPhone 13',
    'iPhone14,4', //iPhone 13 mini',

    'iPhone13,4', //iPhone 12 Pro Max',
    'iPhone13,3', //iPhone 12 Pro',
    'iPhone13,2', //iPhone 12',
    'iPhone13,1', //iPhone 12 mini',

    'iPhone12,5', //iPhone 11 Pro Max',
    'iPhone12,3', //iPhone 11 Pro',
    'iPhone12,1', //iPhone 11',

    'iPhone11,4', //iPhone XS Max',
    'iPhone11,6', //iPhone XS Max',
    'iPhone11,2', //iPhone XS',
    'iPhone11,8', //iPhone XR',
    'iPhone10,3', //iPhone X',
    'iPhone10,6', //iPhone X',
];

export const DEVICE_STANDARD_WIDTHS: any = {
    'iPhone17,2': 440,
    'iPhone17,1': 402,
    'iPhone17,4': 430,
    'iPhone17,3': 393,

    'iPhone16,2': 430,
    'iPhone16,1': 393,
    'iPhone15,5': 430,
    'iPhone15,4': 393,

    'iPhone15,3': 430,
    'iPhone15,2': 393,
    'iPhone14,8': 428,
    'iPhone 14': 390,
    'iPhone14,6': 375,

    'iPhone14,3': 428,
    'iPhone14,2': 390,
    'iPhone14,5': 390,
    'iPhone14,4': 375,

    'iPhone13,4': 428,
    'iPhone13,3': 390,
    'iPhone13,2': 390,
    'iPhone13,1': 375,
    'iPhone12,8': 375,

    'iPhone12,5': 414,
    'iPhone12,3': 375,
    'iPhone12,1': 414,

    'iPhone11,4': 414,
    'iPhone11,6': 414,
    'iPhone11,2': 375,
    'iPhone11,8': 414,
    'iPhone10,3': 375,
    'iPhone10,6': 375,

    'iPhone10,2': 414, //iPhone 8 Plus': 414,
    'iPhone10,5': 414, //iPhone 8 Plus': 414,
    'iPhone10,1': 375, //iPhone 8': 375,
    'iPhone10,4': 375, //iPhone 8': 375,

    'iPhone9,2': 414, //iPhone 7 Plus': 414,
    'iPhone9,4': 414, //iPhone 7 Plus': 414,
    'iPhone9,1': 375, //iPhone 7': 375,
    'iPhone9,3': 375, //iPhone 7': 375,
    'iPhone8,4': 320, //iPhone SE (1st Gen)': 320,

    'iPhone8,2': 414, //iPhone 6s Plus': 414,
    'iPhone8,1': 375, //iPhone 6s': 375,
    'iPhone7,1': 414, //iPhone 6 Plus': 414,
    'iPhone7,2': 375, //iPhone 6': 375,
};

export const DEVICE_STANDARD_HEIGHTS: any = {
    'iPhone17,2': 956,
    'iPhone17,1': 874,
    'iPhone17,4': 932,
    'iPhone17,3': 852,

    'iPhone16,2': 932,
    'iPhone16,1': 852,
    'iPhone15,5': 932,
    'iPhone15,4': 852,

    'iPhone15,3': 932,
    'iPhone15,2': 852,
    'iPhone14,8': 926,
    'iPhone 14': 844,
    'iPhone14,6': 667,

    'iPhone14,3': 926,
    'iPhone14,2': 844,
    'iPhone14,5': 844,
    'iPhone14,4': 812,

    'iPhone13,4': 926,
    'iPhone13,3': 844,
    'iPhone13,2': 844,
    'iPhone13,1': 812,
    'iPhone12,8': 667,

    'iPhone12,5': 896,
    'iPhone12,3': 812,
    'iPhone12,1': 896,

    'iPhone11,4': 896,
    'iPhone11,6': 896,
    'iPhone11,2': 812,
    'iPhone11,8': 896,
    'iPhone10,3': 812,
    'iPhone10,6': 812,

    'iPhone10,2': 736,
    'iPhone10,5': 736,
    'iPhone10,1': 667,
    'iPhone10,4': 667,

    'iPhone9,2': 736,
    'iPhone9,4': 736,
    'iPhone9,1': 667,
    'iPhone9,3': 667,
    'iPhone8,4': 568,

    'iPhone8,2': 736,
    'iPhone8,1': 667,
    'iPhone7,1': 736,
    'iPhone7,2': 667,
};

export const DEVICE_STATUS_BAR_HEIGHTS: any = {
    'iPhone17,2': 62,
    'iPhone17,1': 62,
    'iPhone17,4': 59,
    'iPhone17,3': 59,

    'iPhone16,2': 59,
    'iPhone16,1': 59,
    'iPhone15,5': 59,
    'iPhone15,4': 59,

    'iPhone15,3': 59,
    'iPhone15,2': 59,
    'iPhone14,8': 47,
    'iPhone 14': 47,
    'iPhone14,6': 20,

    'iPhone14,3': 47,
    'iPhone14,2': 47,
    'iPhone14,5': 47,
    'iPhone14,4': 50,

    'iPhone13,4': 47,
    'iPhone13,3': 47,
    'iPhone13,2': 47,
    'iPhone13,1': 50,
    'iPhone12,8': 20,

    'iPhone12,5': 44,
    'iPhone12,3': 44,
    'iPhone12,1': 48,

    'iPhone11,4': 44,
    'iPhone11,6': 44,
    'iPhone11,2': 44,
    'iPhone11,8': 48,
    'iPhone10,3': 44,
    'iPhone10,6': 44,

    'iPhone10,2': 20,
    'iPhone10,5': 20,
    'iPhone10,1': 20,
    'iPhone10,4': 20,

    'iPhone9,2': 20,
    'iPhone9,4': 20,
    'iPhone9,1': 20,
    'iPhone9,3': 20,
    'iPhone8,4': 20,

    'iPhone8,2': 20,
    'iPhone8,1': 20,
    'iPhone7,1': 20,
    'iPhone7,2': 20,
};

const deviceId = DeviceInfo.getDeviceId();
const hasDynamicIsland = DeviceInfo.hasDynamicIsland();

export const getIOSOriginScreenHeight = () => DEVICE_STANDARD_HEIGHTS[deviceId] || CURRENT_SCREEN_HEIGHT;
export const getIOSOriginScreenWidth = () => DEVICE_STANDARD_WIDTHS[deviceId] || CURRENT_SCREEN_WIDTH;
export const getIOSDeviceStatusBarHeight = () => DEVICE_STATUS_BAR_HEIGHTS[deviceId] || (hasDynamicIsland ? 59 : 47);
export const isIPhoneXAndAfterX = () => X_AND_AFTER_X_DEVICES.includes(deviceId);

