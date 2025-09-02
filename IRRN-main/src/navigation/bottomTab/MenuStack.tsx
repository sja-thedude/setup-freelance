import React, { memo } from 'react';

import { SCREENS } from '@navigation/config/screenName';
import { screenOptionsNative } from '@navigation/shareStack';
import { createNativeStackNavigator } from '@react-navigation/native-stack';
import { RootStackParamList } from '@src/navigation/NavigationRouteProps';
import RestaurantMapScreen
    from '@src/screens/restaurant/mapView/RestaurantMapScreen';
import RestaurantListScreen
    from '@src/screens/restaurant/restauranList/AllRestaurantScreen';
import RestaurantDetailScreen
    from '@src/screens/restaurant/restaurantDetail/RestaurantDetailScreen';
import useThemeColors from '@src/themes/useThemeColors';
import { isTemplateOrGroupApp } from '@src/utils';

const StackNavigator = createNativeStackNavigator<RootStackParamList>();

const ProfileStack = () => {
    const { themeColors } = useThemeColors();

    return (
        <StackNavigator.Navigator
            screenOptions={{ ...screenOptionsNative,
                gestureEnabled: false,
                statusBarStyle: 'light',
                headerShown: false,
                animation: 'slide_from_right',
                contentStyle: { backgroundColor: themeColors.color_app_background } }}
            initialRouteName={isTemplateOrGroupApp() ? SCREENS.RESTAURANT_DETAIL_SCREEN : SCREENS.ALL_RESTAURANT_SCREEN}
        >
            <StackNavigator.Screen
                name={SCREENS.ALL_RESTAURANT_SCREEN}
                component={RestaurantListScreen}
            />
            <StackNavigator.Screen
                name={SCREENS.RESTAURANT_MAP_SCREEN}
                component={RestaurantMapScreen}
            />
            <StackNavigator.Screen
                name={SCREENS.RESTAURANT_DETAIL_SCREEN}
                component={RestaurantDetailScreen}
            />
        </StackNavigator.Navigator>
    );
};

export default memo(ProfileStack);
