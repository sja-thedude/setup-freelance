import { Platform } from 'react-native';

import { Colors } from '@configs/index';
import { NativeStackNavigationOptions } from '@react-navigation/native-stack';

export const screenOptionsNative: Partial<NativeStackNavigationOptions> = {
    contentStyle: { backgroundColor: Colors.COLOR_APP_BACKGROUND, },
    orientation: 'portrait',
    headerTitleStyle: {
        color: Colors.COLOR_DEFAULT_TEXT_BLACK,
        fontSize: 18,
        fontWeight: '700',
    },
    statusBarColor: 'transparent',
    statusBarAnimation: 'fade',
    statusBarTranslucent: true,
    statusBarStyle: 'light',
    animation: Platform.OS === 'android' ? 'fade' : 'default',
    headerShadowVisible: false,
    headerBackTitleVisible: false,
    headerTintColor: Colors.COLOR_DEFAULT_TEXT_BLACK,
    headerTitleAlign: 'center',
};
