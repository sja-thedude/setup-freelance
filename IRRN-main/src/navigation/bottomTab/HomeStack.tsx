import React, { memo } from 'react';

import { SCREENS } from '@navigation/config/screenName';
import { screenOptionsNative } from '@navigation/shareStack';
import { createNativeStackNavigator } from '@react-navigation/native-stack';
import { RootStackParamList } from '@src/navigation/NavigationRouteProps';
import HomeScreen from '@src/screens/home/HomeScreen';
import useThemeColors from '@src/themes/useThemeColors';
import RestaurantRecentScreen from '@src/screens/RestaurantRecentScreen';
import RestaurantFavoriteScreen from '@src/screens/RestaurantFavoriteScreen';
import ListNotificationScreen from '@src/screens/notification/ListNotificationScreen';
import OrderHistoryScreen from '@src/screens/order/OrderHistoryScreen';
import RestaurantDetailScreen from '@src/screens/restaurant/restaurantDetail/RestaurantDetailScreen';

const StackNavigator = createNativeStackNavigator<RootStackParamList>();

const ProfileStack = () => {
    const { themeColors } = useThemeColors();

    return (
        <StackNavigator.Navigator
            screenOptions={{
                ...screenOptionsNative,
                gestureEnabled: false,
                statusBarStyle: 'light',
                headerShown: false,
                animation: 'slide_from_right',
                contentStyle: { backgroundColor: themeColors.color_app_background }
            }}
            initialRouteName={SCREENS.HOME_SCREEN}
        >
            <StackNavigator.Screen
                name={SCREENS.HOME_SCREEN}
                component={HomeScreen}
            />
            <StackNavigator.Screen
                name={SCREENS.RESTAURANT_RECENT_SCREEN}
                component={RestaurantRecentScreen}
            />
            <StackNavigator.Screen
                name={SCREENS.RESTAURANT_FAVORITE_SCREEN}
                component={RestaurantFavoriteScreen}
            />
            <StackNavigator.Screen
                name={SCREENS.LIST_NOTIFICATION_SCREEN}
                component={ListNotificationScreen}
            />
            <StackNavigator.Screen
                name={SCREENS.ORDER_HISTORY_SCREEN}
                component={OrderHistoryScreen}
            />
            <StackNavigator.Screen
                name={SCREENS.RESTAURANT_DETAIL_SCREEN}
                component={RestaurantDetailScreen}
                initialParams={{ inHomeStack: true }}
            />
        </StackNavigator.Navigator>
    );
};

export default memo(ProfileStack);
