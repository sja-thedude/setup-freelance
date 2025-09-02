import {
    Platform,
    StatusBar,
} from 'react-native';

import { IS_IOS } from '@src/configs/constants';

import {
    getIOSDeviceStatusBarHeight,
    isIPhoneXAndAfterX,
} from './iphoneScreenSizeHelper';

export const isIPhoneXx = () => IS_IOS && isIPhoneXAndAfterX(); // iphone X and after X

export function getStatusBarHeight() {
    return Platform.select({
        ios: getIOSDeviceStatusBarHeight(),
        android: StatusBar.currentHeight,
        default: 0,
    });
}

export function getBottomSpace() {
    return isIPhoneXx() ? 34 : 0;
}
