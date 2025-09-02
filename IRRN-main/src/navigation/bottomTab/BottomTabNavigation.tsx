import React, {
    memo,
    useCallback,
} from 'react';

import { useTranslation } from 'react-i18next';

import {
    BottomTabBarProps,
    createBottomTabNavigator,
} from '@react-navigation/bottom-tabs';
import { useAppSelector } from '@src/hooks';
import useGetUserLocation from '@src/hooks/useGetUserLocation';
import CustomTabBar from '@src/navigation/bottomTab/components/CustomTabBar';
import { SCREENS } from '@src/navigation/config/screenName';
import {
    isGroupApp,
    isTemplateOrGroupApp,
} from '@src/utils';

import AwardStack from './AwardStack';
import CartStack from './CartStack';
import EmptyStack from './EmptyStack';
import HomeStack from './HomeStack';
import MenuStack from './MenuStack';
import ProfileStack from './ProfileStack';

const BottomTabNavigator = createBottomTabNavigator();

const BottomTab = () => {
    const { t } = useTranslation();
    const workspaceDetail = useAppSelector((state) => state.storageReducer.templateWorkspaceDetail);

    useGetUserLocation(!isGroupApp() || (isGroupApp() && workspaceDetail !== null));

    const renderTabBar = useCallback((props: BottomTabBarProps) => (
        <CustomTabBar {...props} />
    ), []);

    return (
        <BottomTabNavigator.Navigator
            tabBar={renderTabBar}
            screenOptions={{
                headerShown: false,
            }}
            initialRouteName={SCREENS.HOME_TAB_SCREEN}
        >
            <BottomTabNavigator.Screen
                options={{
                    title: t('menu_home'),
                }}
                name={SCREENS.HOME_TAB_SCREEN}
                component={isTemplateOrGroupApp() ? EmptyStack : HomeStack}
            />
            <BottomTabNavigator.Screen
                options={{
                    title: t('menu'),
                    lazy: false
                }}
                name={SCREENS.MENU_TAB_SCREEN}
                component={MenuStack}
            />
            <BottomTabNavigator.Screen
                options={{
                    title: t('menu_shopping_cart'),
                    lazy: false
                }}
                name={SCREENS.SHOPPING_CARD_TAB_SCREEN}
                component={CartStack}
            />
            <BottomTabNavigator.Screen
                options={{
                    title: t('menu_award'),
                }}
                name={SCREENS.AWARD_TAB_SCREEN}
                component={AwardStack}
            />
            <BottomTabNavigator.Screen
                options={{
                    title: t('menu_account'),
                    lazy: false
                }}
                name={SCREENS.ACCOUNT_TAB_SCREEN}
                component={ProfileStack}
            />
        </BottomTabNavigator.Navigator>
    );
};

export default memo(BottomTab);
