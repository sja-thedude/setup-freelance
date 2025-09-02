import React, {
    FC,
    memo,
    useCallback,
    useMemo,
} from 'react';

import {
    AppState,
    StyleSheet,
    View,
} from 'react-native';
import { useDispatch } from 'react-redux';
import { useEffectOnce } from 'react-use';

import { useLayout } from '@react-native-community/hooks';
import { useFocusEffect } from '@react-navigation/native';
import {
    ClockHistoryIcon2,
    InboxMessageIcon,
    MapIconGroupHome,
} from '@src/assets/svg';
import HeaderComponent from '@src/components/header/HeaderComponent';
import TextComponent from '@src/components/TextComponent';
import TouchableComponent from '@src/components/TouchableComponent';
import { Colors } from '@src/configs';
import { useAppSelector } from '@src/hooks';
import useBoolean from '@src/hooks/useBoolean';
import useCheckEmptyCart from '@src/hooks/useCheckEmptyCart';
import useDimens, { DimensType } from '@src/hooks/useDimens';
import useIsUserLoggedIn from '@src/hooks/useIsUserLoggedIn';
import { SCREENS } from '@src/navigation/config/screenName';
import NavigationService from '@src/navigation/NavigationService';
import {
    getNotificationListAction,
    NotificationActions,
} from '@src/redux/toolkit/actions/notificationActions';
import HolidayException
    from '@src/screens/restaurant/restaurantDetail/component/HolidayException';
import RestaurantInfoIcon
    from '@src/screens/restaurant/restaurantDetail/component/RestaurantInfoIcon';
import useThemeColors from '@src/themes/useThemeColors';
import { isGroupApp } from '@src/utils';

import ChangeRestaurantAlertDialog from './ChangeRestaurantAlertDialog';
import LanguageIcon from './LanguageIcon';

interface IProps {
    currentImageIndex?: number,
    reloadWorkspaceData: () => void,
}

const TemplateHomeHeader: FC<IProps> = ({ currentImageIndex, reloadWorkspaceData }) => {
    const Dimens = useDimens();
    const styles = stylesF(Dimens);

    const { themeColors } = useThemeColors();
    const isUserLoggedIn = useIsUserLoggedIn();

    const dispatch = useDispatch();

    const { onLayout, height } = useLayout();

    const [isShowModal, showModal, hideModal] = useBoolean(false);

    const isEmptyCart = useCheckEmptyCart();

    const notificationNumber = useAppSelector((state) => state.notificationReducer.notificationBadge);
    const apiGallery = useAppSelector((state) => state.storageReducer.templateWorkspaceDetail?.api_gallery);

    const imageList = useMemo(() => {
        const images = apiGallery?.filter((i) => i.active);
        return images?.length ? images : [{}];
    }, [apiGallery]);

    const getNotificationList = useCallback(() => {
        if (isUserLoggedIn) {
            dispatch(getNotificationListAction(false, false, { limit: 1, page: 1 }));
        }
    }, [dispatch, isUserLoggedIn]);

    const handleNavToOrderHistory = useCallback(() => {
        if (!isUserLoggedIn) {
            NavigationService.navigate(SCREENS.LOGIN_SCREEN, { callback: () => NavigationService.navigate(SCREENS.TEMPLATE_ORDER_HISTORY_SCREEN) });
        } else {
            NavigationService.navigate(SCREENS.TEMPLATE_ORDER_HISTORY_SCREEN);
        }
    }, [isUserLoggedIn]);

    const handleNavToGroupHome = useCallback(() => {
        if (isEmptyCart) {
            NavigationService.navigate(SCREENS.GROUP_APP_HOME_SCREEN);
        } else {
            showModal();
        }
    }, [isEmptyCart, showModal]);

    const handleNavToNotification = useCallback(() => {
        dispatch(NotificationActions.clearNotificationList());
        NavigationService.navigate(SCREENS.TEMPLATE_LIST_NOTIFICATION_SCREEN);
    }, [dispatch]);

    useFocusEffect(useCallback(() => {
        // get notification badge
        getNotificationList();
    }, [getNotificationList]));

    useEffectOnce(() => {
        const subscription = AppState.addEventListener('change', (nextAppState) => {
            if (nextAppState === 'active' && NavigationService.getCurrentRoute()?.name === SCREENS.WORKSPACE_HOME_SCREEN) {
                // get notification badge
                getNotificationList();
            }
        });

        return () => {
            subscription.remove();
        };
    });

    const renderLeftIcons = useMemo(() => (
        <View style={styles.iconContainer}>
            <RestaurantInfoIcon/>

            {isGroupApp() ? (
                <TouchableComponent
                    onPress={handleNavToGroupHome}
                    style={styles.mapIconContainer}
                >
                    <MapIconGroupHome
                        strokeWidth={5}
                        width={Dimens.W_22}
                        height={Dimens.W_22}
                    />
                </TouchableComponent>
            ) : null}

            <LanguageIcon reloadWorkspaceData={reloadWorkspaceData}/>
        </View>
    ), [Dimens.W_22, handleNavToGroupHome, reloadWorkspaceData, styles.iconContainer, styles.mapIconContainer]);

    const renderIndicator = useMemo(() => imageList && imageList.length > 1 ? (
        <View style={styles.indicatorContainer}>
            {imageList.map((image, index) => (
                <View
                    key={index}
                    style={[styles.indicator, { backgroundColor: currentImageIndex === index ? Colors.COLOR_WHITE : Colors.COLOR_WHITE_50 }]}
                />
            ))}
        </View>
    ) : null, [currentImageIndex, imageList, styles.indicator, styles.indicatorContainer]);

    const renderRightIcons = useMemo(() => isUserLoggedIn ? (
        <View style={styles.rightIconContainer}>
            <TouchableComponent
                hitSlop={Dimens.DEFAULT_HIT_SLOP}
                style={{ marginLeft: Dimens.W_12 }}
                onPress={handleNavToOrderHistory}
            >
                <ClockHistoryIcon2
                    width={Dimens.H_24}
                    height={Dimens.H_24}
                    stroke='white'
                />
            </TouchableComponent>

            <TouchableComponent
                hitSlop={Dimens.DEFAULT_HIT_SLOP}
                style={{ marginLeft: Dimens.W_12 }}
                onPress={handleNavToNotification}
            >
                <InboxMessageIcon
                    strokeWidth={1.5}
                    width={Dimens.H_26}
                    height={Dimens.H_26}
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
    ) : null , [Dimens.DEFAULT_HIT_SLOP, Dimens.H_24, Dimens.H_26, Dimens.W_12, handleNavToNotification, handleNavToOrderHistory, isUserLoggedIn, notificationNumber, styles.badgeContainer, styles.badgeText, styles.rightIconContainer, themeColors.color_primary]);

    return (
        <View
            onLayout={onLayout}
            style={styles.mainContainer}
        >
            <HeaderComponent
                disabledShadow
                style={styles.headerStyle}
            >
                <View style={styles.container}>
                    {renderLeftIcons}

                    <View style={styles.iconContainer}>
                        {renderIndicator}
                        {renderRightIcons}
                    </View>
                </View>
            </HeaderComponent>

            <HolidayException
                isInHome
                topSpace={height}
            />

            <ChangeRestaurantAlertDialog
                hideModal={hideModal}
                isShow={isShowModal}
            />

        </View>
    );
};

export default memo(TemplateHomeHeader);

const stylesF = (Dimens: DimensType) => StyleSheet.create({
    mapIconContainer: { marginLeft: Dimens.W_16 },
    rightIconContainer: { flexDirection: 'row', alignItems: 'center' },
    container: {
        flexDirection: 'row',
        alignItems: 'center',
        justifyContent: 'space-between',
    },
    headerStyle: { backgroundColor: 'transparent', paddingTop: Dimens.COMMON_HEADER_PADDING - Dimens.H_8 },
    mainContainer: { position: 'absolute', top: 0, right: 0, left: 0 },
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
    indicatorContainer: {
        flexDirection: 'row',
        justifyContent: 'center',
    },
    indicator: {
        width: Dimens.W_7,
        height: Dimens.W_7,
        borderRadius: Dimens.W_7,
        backgroundColor: Colors.COLOR_WHITE,
        marginHorizontal: Dimens.W_4,
    },
});