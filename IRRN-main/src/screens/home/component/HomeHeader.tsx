import React, {
    useCallback,
    useEffect,
    useMemo,
} from 'react';

import { useTranslation } from 'react-i18next';
import {
    AppState,
    StyleSheet,
    View,
} from 'react-native';
import { useDispatch } from 'react-redux';

import { useFocusEffect } from '@react-navigation/native';
import {
    ClockHistoryIcon,
    InboxMessageIcon,
    MagnifierIcon,
} from '@src/assets/svg';
import HeaderComponent from '@src/components/header/HeaderComponent';
import InputComponent from '@src/components/InputComponent';
import TextComponent from '@src/components/TextComponent';
import TouchableComponent from '@src/components/TouchableComponent';
import { Colors } from '@src/configs';
import { useAppSelector } from '@src/hooks';
import useDimens, { DimensType } from '@src/hooks/useDimens';
import useIsUserLoggedIn from '@src/hooks/useIsUserLoggedIn';
import { SCREENS } from '@src/navigation/config/screenName';
import NavigationService from '@src/navigation/NavigationService';
import { LocationActions } from '@src/redux/toolkit/actions/locationActions';
import {
    getNotificationListAction,
    NotificationActions,
} from '@src/redux/toolkit/actions/notificationActions';
import useThemeColors from '@src/themes/useThemeColors';

export type AddressType = {
    lat: number,
    lng: number,
    address: string,
}

const HomeHeader = () => {
    const Dimens = useDimens();
    const styles = stylesF(Dimens);

    const { t } = useTranslation();
    const { themeColors } = useThemeColors();
    const isUserLoggedIn = useIsUserLoggedIn();

    const dispatch = useDispatch();

    const { address } = useAppSelector((state) => state.locationReducer);
    const notificationNumber = useAppSelector((state) => state.notificationReducer.notificationBadge);

    const handleSelectAddress = useCallback((newAddress: AddressType) => {
        dispatch(LocationActions.setLocation({ lat: newAddress.lat, lng: newAddress.lng, address: newAddress.address }));
        NavigationService.navigate(SCREENS.MENU_TAB_SCREEN);
    }, [dispatch]);

    const selectAddress = useCallback(() => {
        NavigationService.navigate(SCREENS.SELECT_ADDRESS_SCREEN, { onSelectAddress: handleSelectAddress });
    }, [handleSelectAddress]);

    const getNotificationList = useCallback(() => {
        if (isUserLoggedIn) {
            dispatch(getNotificationListAction(false, false, { limit: 1, page: 1 }));
        }
    }, [dispatch, isUserLoggedIn]);

    useFocusEffect(useCallback(() => {
        // get notification badge
        getNotificationList();
    }, [getNotificationList]));

    useEffect(() => {
        const subscription = AppState.addEventListener('change', (nextAppState) => {
            if (nextAppState === 'active' && NavigationService.getCurrentRoute()?.name === SCREENS.HOME_SCREEN) {
                // get notification badge
                getNotificationList();
            }
        });

        return () => {
            subscription.remove();
        };
    }, [getNotificationList]);

    const renderRightIcons = useMemo(() => (
        <View style={styles.container}>
            <TextComponent style={styles.headerText}>
                {t('tab_home_title').toUpperCase()}
            </TextComponent>
            {isUserLoggedIn ? (
                <View style={styles.iconContainer}>
                    <TouchableComponent
                        hitSlop={Dimens.DEFAULT_HIT_SLOP}
                        onPress={() => NavigationService.navigate(SCREENS.ORDER_HISTORY_SCREEN)}
                    >
                        <ClockHistoryIcon
                            width={Dimens.W_24}
                            height={Dimens.W_24}
                        />
                    </TouchableComponent>
                    <TouchableComponent
                        hitSlop={Dimens.DEFAULT_HIT_SLOP}
                        style={{ marginLeft: Dimens.W_16 }}
                        onPress={() => {
                            dispatch(NotificationActions.clearNotificationList());
                            NavigationService.navigate(SCREENS.LIST_NOTIFICATION_SCREEN);
                        }}
                    >
                        <InboxMessageIcon
                            width={Dimens.W_24}
                            height={Dimens.W_24}
                        />
                        {notificationNumber > 0 && (
                            <View style={styles.badgeContainer}>
                                <TextComponent
                                    numberOfLines={1}
                                    style={{ ...styles.badgeText, color: themeColors.color_primary }}
                                >
                                    {notificationNumber}
                                </TextComponent>
                            </View>
                        )}
                    </TouchableComponent>
                </View>
            ) : null}
        </View>
    ), [Dimens.DEFAULT_HIT_SLOP, Dimens.W_16, Dimens.W_24, dispatch, isUserLoggedIn, notificationNumber, styles.badgeContainer, styles.badgeText, styles.container, styles.headerText, styles.iconContainer, t, themeColors.color_primary]);

    const renderInput = useMemo(() => (
        <InputComponent
            inputPress={selectAddress}
            containerStyle={styles.inputContainer}
            style={styles.input}
            textColorInput={themeColors.color_text_2}
            inputBorderRadius={Dimens.W_5}
            placeholder={t('hint_first_name')}
            value={address}
            leftIcon={(
                <MagnifierIcon
                    width={Dimens.H_22}
                    height={Dimens.H_22}
                    stroke={themeColors.color_primary}
                />
            )}
        />
    ), [Dimens.H_22, Dimens.W_5, address, selectAddress, styles.input, styles.inputContainer, t, themeColors.color_primary, themeColors.color_text_2]);

    return (
        <View style={styles.mainContainer}>
            <HeaderComponent
                style={{ paddingTop: Dimens.COMMON_HEADER_PADDING - Dimens.COMMON_HEADER_PADDING_EXTRA }}
            >
                {renderRightIcons}
                {renderInput}
            </HeaderComponent>
        </View>

    );
};

export default HomeHeader;

const stylesF = (Dimens: DimensType) => StyleSheet.create({
    badgeText: {
        fontSize: Dimens.FONT_12,
        fontWeight: '700',
        paddingHorizontal: Dimens.W_3,
        width: '100%',
        textAlign: 'center',
    },
    badgeContainer: {
        position: 'absolute',
        top: -Dimens.W_11,
        right: -Dimens.W_11,
        backgroundColor: Colors.COLOR_WHITE,
        borderRadius: Dimens.W_999,
        justifyContent: 'center',
        alignItems: 'center',
        minWidth: Dimens.W_16,
        minHeight: Dimens.W_16,
    },
    iconContainer: { flexDirection: 'row', alignItems: 'center' },
    container: {
        flexDirection: 'row',
        alignItems: 'center',
        justifyContent: 'space-between',
    },
    mainContainer: { position: 'absolute', right: 0, left: 0 },
    headerText: {
        color: Colors.COLOR_WHITE,
        fontSize: Dimens.FONT_26,
        fontWeight: '700'
    },
    inputContainer: {
        marginTop: Dimens.H_6,
        marginHorizontal: Dimens.W_16,
        marginBottom: -Dimens.H_40 / 2 - Dimens.H_14,
        height: Dimens.H_42,
    },
    input: {
        paddingVertical: 0,
        paddingLeft: Dimens.W_8,
        fontSize: Dimens.FONT_14,
        fontWeight:'700',
    },
});