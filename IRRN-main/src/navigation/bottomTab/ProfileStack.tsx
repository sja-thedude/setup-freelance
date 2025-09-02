import React, { memo } from 'react';

import { SCREENS } from '@navigation/config/screenName';
import { RootStackParamList } from '@src/navigation/NavigationRouteProps';
import { screenOptionsNative } from '@navigation/shareStack';
import { createNativeStackNavigator } from '@react-navigation/native-stack';
import EditProfileScreen from '@src/screens/profile/EditProfileScreen';
import ProfileScreen from '@src/screens/profile/ProfileScreen';
import useThemeColors from '@src/themes/useThemeColors';

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
            initialRouteName={SCREENS.PROFILE_SCREEN}
        >
            <StackNavigator.Screen
                name={SCREENS.PROFILE_SCREEN}
                component={ProfileScreen}
            />
            <StackNavigator.Screen
                name={SCREENS.EDIT_PROFILE_SCREEN}
                component={EditProfileScreen}
            />
        </StackNavigator.Navigator>
    );
};

export default memo(ProfileStack);
