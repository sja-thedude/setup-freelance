import React, { memo } from 'react';

import { createNativeStackNavigator } from '@react-navigation/native-stack';
import DetailLoyaltyRestaurantScreen
    from '@src/screens/award/screens/DetailLoyaltyRestaurantScreen/DetailLoyaltyRestaurantScreen';
import ListRestaurantAwardScreen
    from '@src/screens/award/screens/ListRestaurantAwardScreen/ListRestaurantAwardScreen';
import useThemeColors from '@src/themes/useThemeColors';
import { isTemplateOrGroupApp } from '@src/utils';

import { SCREENS } from '../config/screenName';
import { RootStackParamList } from '../NavigationRouteProps';
import { screenOptionsNative } from '../shareStack';

const StackNavigator = createNativeStackNavigator<RootStackParamList>();

const AwardStack = () => {
    const { themeColors } = useThemeColors();

    return (
        <StackNavigator.Navigator
            screenOptions={{
                ...screenOptionsNative,
                gestureEnabled: false,
                statusBarStyle: 'light',
                headerShown: false,
                animation: 'slide_from_right',
                contentStyle: {
                    backgroundColor: themeColors.color_app_background,
                },
            }}
            initialRouteName={isTemplateOrGroupApp() ? SCREENS.DETAIL_LOYALTY_RESTAURANT_SCREEN : SCREENS.LIST_RESTAURANT_AWARD_SCREEN}
        >
            <StackNavigator.Screen
                name={SCREENS.LIST_RESTAURANT_AWARD_SCREEN}
                component={ListRestaurantAwardScreen}
            />
            <StackNavigator.Screen
                name={SCREENS.DETAIL_LOYALTY_RESTAURANT_SCREEN}
                component={DetailLoyaltyRestaurantScreen}
            />
        </StackNavigator.Navigator>
    );
};

export default memo(AwardStack);
