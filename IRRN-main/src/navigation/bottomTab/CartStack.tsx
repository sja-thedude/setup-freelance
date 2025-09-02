import React, { memo } from 'react';

import { SCREENS } from '@navigation/config/screenName';
import { screenOptionsNative } from '@navigation/shareStack';
import { createNativeStackNavigator } from '@react-navigation/native-stack';
import { RootStackParamList } from '@src/navigation/NavigationRouteProps';
import CartScreen from '@src/screens/cart/cartDetail/CartScreen';
import useThemeColors from '@src/themes/useThemeColors';

const StackNavigator = createNativeStackNavigator<RootStackParamList>();

const CartStack = () => {
    const { themeColors } = useThemeColors();

    return (
        <StackNavigator.Navigator
            screenOptions={{ ...screenOptionsNative,
                gestureEnabled: false,
                statusBarStyle: 'light',
                headerShown: false,
                animation: 'slide_from_right',
                contentStyle: { backgroundColor: themeColors.color_app_background } }}
            initialRouteName={SCREENS.CART_TAB_SCREEN}
        >
            <StackNavigator.Screen
                name={SCREENS.CART_TAB_SCREEN}
                component={CartScreen}
            />
        </StackNavigator.Navigator>
    );
};

export default memo(CartStack);
