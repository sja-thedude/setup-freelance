import { StyleSheet } from 'react-native';

export const convertFontWeightToFontFamily = (style: any) => {
    let fontWeight;
    let fontFamily;

    if (Array.isArray(style)) {
        fontWeight = StyleSheet.flatten(style)?.fontWeight;
        fontFamily = StyleSheet.flatten(style)?.fontFamily;
    } else {
        fontWeight = style?.fontWeight;
        fontFamily = style?.fontFamily;
    }

    if (fontFamily) {
        return fontFamily;
    } else {
        switch (fontWeight) {
            case '100':
            case '200':
                return 'Roboto-Light';
            case '300':
                return 'Roboto-Thin';
            case '400':
                return 'Roboto-Regular';
            case '500':
            case '600':
                return 'Roboto-Medium';
            case '700':
            case '800':
            case '900':
                return 'Roboto-Bold';
            default:
                return 'Roboto-Regular';
        }
    }
};