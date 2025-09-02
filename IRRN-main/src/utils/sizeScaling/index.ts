import { PixelRatio } from 'react-native';

import { IS_ANDROID } from '@src/configs/constants';

import { getStatusBarHeight, isIPhoneXx } from '../iPhoneXHelper';

const pixelDensity = PixelRatio.get();

const metricsNumber = (width: number, height: number) => {
    const density = pixelDensity * 160;
    const x = Math.pow((width * pixelDensity) / density, 2);
    const y = Math.pow((height * pixelDensity) / density, 2);
    const screenInches = Math.sqrt(x + y) + 1.6;

    return screenInches;
};

export const fontScaleIOS = (fontSize: number, width: number, height: number) => {
    const ratio = (metricsNumber(width, height) + pixelDensity) / 10;
    const value = fontSize * Number(ratio.toFixed(1));
    return Number(value.toFixed(1));
};

export const fontScaleAndroid = (fontSize: number, width: number, height: number, standardScreenHeight = guidelineBaseHeight) => {
    const standardLength = width > height ? width : height;
    const offset = width > height ? 0 : getStatusBarHeight();

    const deviceHeight = standardLength - offset;

    const heightPercent = (fontSize * deviceHeight) / standardScreenHeight;
    return Math.round(heightPercent + 1);
};

// export const fontScale = (fontSize: number, screenWidth: number, screenHeight: number) => IS_ANDROID ?  fontScaleAndroid(fontSize, screenWidth, screenHeight) : fontScaleIOS(fontSize, screenWidth, screenHeight);
export const fontScale = (fontSize: number, screenWidth: number, screenHeight: number) => IS_ANDROID ?  fontScaleAndroid(fontSize, screenWidth, screenHeight) : fontScaleIOS(isIPhoneXx() ? fontSize :  fontSize + 0.5, screenWidth, screenHeight);

export const radiusScale = (radius: number, screenWidth: number, screenHeight: number) => {
    const ratio = (metricsNumber(screenWidth, screenHeight) + pixelDensity) / 10;
    const value = radius * Number(ratio.toFixed(1));
    return Number(value.toFixed(1));
};

//Guideline sizes are based on standard ~5" screen mobile device
const guidelineBaseWidth = 375;
const guidelineBaseHeight = 812;

// export const horizontalScale = (size: number, screenWidth: number) => screenWidth / guidelineBaseWidth * size;
// export const verticalScale = (size: number, screenHeight: number) => screenHeight / guidelineBaseHeight * size;

export const horizontalScale = (size: number, screenWidth: number) => screenWidth / guidelineBaseWidth * (isIPhoneXx() ? size : size + 0.5);
export const verticalScale = (size: number, screenHeight: number) => screenHeight / guidelineBaseHeight * (isIPhoneXx() ? size : size + 0.5);
export const moderateScale = (size: number, screenWidth: number, factor = 0.5) => size + ( horizontalScale(size, screenWidth) - size ) * factor;

