import React, {
    memo,
    useCallback,
} from 'react';

import { View } from 'react-native';

import { SCREENS } from '@navigation/config/screenName';
import { screenOptionsNative } from '@navigation/shareStack';
import { createNativeStackNavigator } from '@react-navigation/native-stack';
import { RootStackParamList } from '@src/navigation/NavigationRouteProps';
import useThemeColors from '@src/themes/useThemeColors';
import { useFocusEffect } from '@react-navigation/native';
import NavigationService from '../NavigationService';
import { useAppSelector } from '@src/hooks';

const StackNavigator = createNativeStackNavigator<RootStackParamList>();

const Empty = () => <View/>;

const EmptyStack = () => {
    const { themeColors } = useThemeColors();
    const workspaceDetail = useAppSelector((state) => state.storageReducer.templateWorkspaceDetail);

    useFocusEffect(useCallback(() => {
        if (workspaceDetail) {
            NavigationService.navigate(SCREENS.WORKSPACE_HOME_SCREEN);
        } else {
            NavigationService.navigate(SCREENS.GROUP_APP_HOME_SCREEN);
        }
    }, [workspaceDetail]));

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
                component={Empty}
            />
        </StackNavigator.Navigator>
    );
};

export default memo(EmptyStack);
