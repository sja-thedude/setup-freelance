import React, {
    memo,
    useCallback,
    useMemo,
} from 'react';

import {
    StyleSheet,
    View,
} from 'react-native';

import {
    TabAccountIcon,
    TabCustomerIcon,
    TabHomeIcon,
    TabMenuIcon,
    TabShoppingIcon,
} from '@assets/svg/index';
import TextComponent from '@components/TextComponent';
import { SCREENS } from '@navigation/config/screenName';
import { BottomTabBarProps } from '@react-navigation/bottom-tabs';
import TouchableComponent from '@src/components/TouchableComponent';
import { Colors } from '@src/configs';
import { useAppSelector } from '@src/hooks';
import useDimens, { DimensType } from '@src/hooks/useDimens';
import useIsUserLoggedIn from '@src/hooks/useIsUserLoggedIn';
import useThemeColors from '@src/themes/useThemeColors';
import { isTemplateOrGroupApp } from '@src/utils';

const CustomTabBar = ({ state, descriptors, navigation }: BottomTabBarProps) => {
    const { themeColors } = useThemeColors();
    const Dimens = useDimens();
    const styles = stylesF(Dimens);

    const iconSize = Dimens.H_26;

    const isUserLoggedIn = useIsUserLoggedIn();

    const cartProducts = useAppSelector((state) => state.storageReducer.cartProducts.data);

    const cartBadge = useMemo(() => {
        let badge = 0;
        cartProducts.map((item) => {
            badge = badge + item.quantity;
        });
        return badge;
    }, [cartProducts]);

    const getTabIcon = useCallback((index: number, isFocused: boolean) => {
        switch (index) {
            case 0:
                return (
                    <TabHomeIcon
                        width={iconSize}
                        height={iconSize}
                        stroke={isFocused ? themeColors.color_primary : themeColors.color_tab_inactive}
                    />
                );

            case 1:
                return (
                    <TabMenuIcon
                        width={iconSize}
                        height={iconSize}
                        stroke={isFocused ? themeColors.color_primary : themeColors.color_tab_inactive}
                    />
                );

            case 2:
                return (
                    <TabShoppingIcon
                        width={iconSize}
                        height={iconSize}
                        stroke={isFocused ? themeColors.color_primary : themeColors.color_tab_inactive}
                    />
                );

            case 3:
                return (
                    <TabCustomerIcon
                        width={iconSize}
                        height={iconSize}
                        stroke={isFocused ? themeColors.color_primary : themeColors.color_tab_inactive}
                    />
                );
            case 4:
                return (
                    <TabAccountIcon
                        width={iconSize}
                        height={iconSize}
                        stroke={isFocused ? themeColors.color_primary : themeColors.color_tab_inactive}
                    />
                );

            default:
                return <View/>;
        }
    }, [iconSize, themeColors.color_primary, themeColors.color_tab_inactive]);

    const onTabPress = useCallback((route: any, isFocused: boolean, index: number) => () => {
        const event = navigation.emit({
            type: 'tabPress',
            target: route.key,
            canPreventDefault: true,
        });

        if (!isFocused && !event.defaultPrevented) {
            if (index === 3 && !isUserLoggedIn) {
                navigation.navigate({ name: SCREENS.LOGIN_SCREEN, params: { callback: () => navigation.navigate(SCREENS.AWARD_TAB_SCREEN) } });
            } else {
                if (isTemplateOrGroupApp() && index === 0) {
                    navigation.navigate({ name: SCREENS.WORKSPACE_HOME_SCREEN, params: {} });
                } else {
                    navigation.navigate({ name: route.name, params: {} });
                }
            }
        }
    }, [isUserLoggedIn, navigation]);

    return (
        <View style={[styles.tabBarContainer, { backgroundColor: themeColors.color_app_background }]}>
            {state?.routes?.map((route, index) => {
                const { options } = descriptors[route.key];
                const label: any = options.tabBarLabel !== undefined
                  ? options.tabBarLabel
                  : options.title !== undefined
                  ? options.title
                  : route.name;

                const isFocused = state.index === index;

                return (
                    <TouchableComponent
                        key={index}
                        accessibilityRole="button"
                        accessibilityState={isFocused ? { selected: true } : {}}
                        accessibilityLabel={options.tabBarAccessibilityLabel}
                        testID={options.tabBarTestID}
                        onPress={onTabPress(route, isFocused, index)}
                        style={styles.tabBarButton}
                    >
                        {getTabIcon(index, isFocused)}
                        <TextComponent
                            numberOfLines={1}
                            style={{ ...styles.taBarButtonText, color: isFocused ? themeColors.color_primary : themeColors.color_tab_inactive }}
                        >
                            {label}
                        </TextComponent>
                        {(index === 2 && cartBadge > 0) && (
                            <View style={[styles.badgeContainer, { backgroundColor: themeColors.color_primary }]}>
                                <TextComponent style={styles.badgeText}>
                                    {cartBadge}
                                </TextComponent>
                            </View>
                        )}
                    </TouchableComponent>
                );
            })}
        </View>
    );
};

const stylesF = (Dimens: DimensType) => StyleSheet.create({
    badgeText: {
        fontSize: Dimens.FONT_10,
        fontWeight: '500',
        color: Colors.COLOR_WHITE,
        textAlign: 'center',
    },
    badgeContainer: {
        position: 'absolute',
        minWidth: Dimens.H_18,
        height: Dimens.H_18,
        borderRadius: Dimens.W_999,
        right: Dimens.W_12,
        top: -Dimens.H_6,
        alignItems: 'center',
        justifyContent: 'center',
    },
    taBarButtonText: { fontSize: Dimens.FONT_8, marginTop: Dimens.H_4 },
    tabBarContainer: {
        flexDirection: 'row',
        justifyContent: 'space-around',
        paddingBottom: Dimens.TAB_BAR_BOTTOM_PADDING,
        paddingTop: Dimens.H_8,
    },
    tabBarButton: {
        alignItems: 'center',
        width: '100%',
        maxWidth: (Dimens.SCREEN_WIDTH - Dimens.W_24) / 5,
    },
});

export default memo(CustomTabBar);