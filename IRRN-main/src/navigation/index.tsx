import React, {
    memo,
    useCallback,
    useRef,
    useState,
} from 'react';

import Config from 'react-native-config';

import LoadingOverlay from '@components/LoadingOverlay';
import CustomToastProvider from '@components/toast/CustomToastProvider';
import useMessaging from '@hooks/useMessaging';
import ModalRemindCart from '@navigation/bottomTab/components/ModalRemindCart';
import ProcessingOnlinePaymentOrderDialog
    from '@navigation/bottomTab/components/ProcessingOnlinePaymentOrderDialog';
import { SCREENS } from '@navigation/config/screenName';
import NavigationService from '@navigation/NavigationService';
import analytics from '@react-native-firebase/analytics';
import {
    NavigationContainer,
    NavigationContainerRef,
} from '@react-navigation/native';
import {
    createStackNavigator,
    TransitionPresets,
} from '@react-navigation/stack';
import {
    useAppDispatch,
    useAppSelector,
} from '@src/hooks';
import useCallAPI from '@src/hooks/useCallAPI';
import useDeepLinkHandle from '@src/hooks/useDeepLinkHandle';
import useGetWorkspaceLanguage from '@src/hooks/useGetWorkspaceLanguage';
import useIsUserLoggedIn from '@src/hooks/useIsUserLoggedIn';
import I18nApp from '@src/languages';
import BottomTab from '@src/navigation/bottomTab/BottomTabNavigation';
import { RootStackParamList } from '@src/navigation/NavigationRouteProps';
import {
    setHeaderContentLanguage,
    setHeaders,
    setHeaderToken,
} from '@src/network/axios';
import {
    getGroupAppDetailByTokenService,
    getWorkspaceDetailByTokenService,
    getWorkspaceSettingService,
} from '@src/network/services/restaurantServices';
import { StorageActions } from '@src/redux/toolkit/actions/storageActions';
import SelectAddressScreen from '@src/screens/addressSelection/SelectAddressScreen';
import ForgotPasswordScreen from '@src/screens/auth/forgotPassword/ForgotPasswordScreen';
import ResetPasswordScreen from '@src/screens/auth/forgotPassword/ResetPasswordScreen';
import LoginScreen from '@src/screens/auth/login/LoginScreen';
import ConfirmRegisterScreen from '@src/screens/auth/register/ConfirmRegisterScreen';
import CreateAccountScreen from '@src/screens/auth/register/CreateAccountScreen';
import WebViewScreen from '@src/screens/browser/WebViewScreen';
import OrderFailScreen from '@src/screens/cart/orderResult/OrderFailScreen';
import OrderSuccessScreen from '@src/screens/cart/orderResult/OrderSuccessScreen';
import SelectOrderTypeScreen from '@src/screens/cart/selectOrderType/SelectOrderTypeScreen';
import DeepLinkErrorScreen from '@src/screens/error/DeepLinkErrorScreen';
import GroupAppHomeScreen from '@src/screens/groupHome/GroupAppHomeScreen';
import DetailNotificationDialog from '@src/screens/notification/component/DetailNotificationDialog';
import ListNotificationScreen from '@src/screens/notification/ListNotificationScreen';
import OrderHistoryScreen from '@src/screens/order/OrderHistoryScreen';
import ProductDetailScreen from '@src/screens/product/productDetail/ProductDetailScreen';
import EditProfileScreen from '@src/screens/profile/EditProfileScreen';
import SelectLanguageScreen from '@src/screens/profile/SelectLanguageScreen';
import SplashScreen from '@src/screens/splash/SplashScreen';
import WorkSpaceHomeScreen from '@src/screens/templateHome/WorkSpaceHomeScreen';
import TemplateJobRegisterScreen from '@src/screens/templateJob/TemplateJobRegisterScreen';
import WalkThroughScreen from '@src/screens/walkThrough/WalkThroughScreen';
import useThemeColors from '@src/themes/useThemeColors';
import {
    isGroupApp,
    isTemplateApp,
    isTemplateOrGroupApp,
} from '@src/utils';
import { log } from '@src/utils/logger';

import { SlideFromLeft } from './config/screenTransition';

const StackNavigator = createStackNavigator<RootStackParamList>();

const AppNavigation = () => {
    const { themeColors } = useThemeColors();
    useMessaging();
    useDeepLinkHandle();
    useGetWorkspaceLanguage();

    const dispatch = useAppDispatch();

    const navigationRef = useRef<NavigationContainerRef<{}>>();
    const routeNameRef = useRef<string>();

    const [loading, setLoading] = useState(true);

    const showWalkThrough = useAppSelector((state) => state.storageReducer.showWalkThrough);
    const userData = useAppSelector((state) => state.storageReducer.userData);
    const workspaceDetail = useAppSelector((state) => state.storageReducer.templateWorkspaceDetail);
    const groupAppDetail = useAppSelector((state) => state.storageReducer.groupAppDetail);
    const storageLanguage = useAppSelector((state) => state.storageReducer.language);

    const isUserLoggedIn = useIsUserLoggedIn();

    // const { callApi: getUserData } = useCallAPI(
    //         getUserProfileService,
    //         undefined,
    //         useCallback((data: any) => {
    //             updateUserData(data);
    //         }, []),
    // );

    const { callApi: getGroupAppDetailByToken } = useCallAPI(
            getGroupAppDetailByTokenService,
            undefined,
            undefined,
            undefined,
            false
    );

    const { callApi: getRestaurantDetailByToken } = useCallAPI(
            getWorkspaceDetailByTokenService,
            undefined,
            undefined,
            undefined,
            false
    );

    const { callApi: getRestaurantFuncData } = useCallAPI(
            getWorkspaceSettingService,
            undefined,
            undefined,
            undefined,
            false
    );

    const getInitData = useCallback(async () => {

        // get group app data
        if (isGroupApp()) {
            setHeaders({ lat: undefined, lng: undefined });
            const detailGroupApp = await getGroupAppDetailByToken({ group_app_token: Config.ENV_GROUP_TOKEN });

            if (detailGroupApp.status === 404) {
                return;
            }

            if (detailGroupApp.success) {
                dispatch(StorageActions.setStorageGroupAppDetail(detailGroupApp.data));
            }
        }

        // get template workspace data
        if (isTemplateApp()) {
            const [detailRes, settingRes] = await Promise.all([
                getRestaurantDetailByToken({ restaurant_token: Config.ENV_WORKSPACE_TOKEN }),
                getRestaurantFuncData({ restaurant_token: Config.ENV_WORKSPACE_TOKEN }),
            ]);

            if (detailRes.status === 404) {
                return;
            }

            if (detailRes.success) {
                dispatch(StorageActions.setStorageWorkspaceDetail(detailRes.data));
            }

            if (settingRes.success) {
                dispatch(StorageActions.setStorageWorkspaceSetting(settingRes.data));
            }

        }

        // if (isUserLoggedIn) {
        // get user data in case open app from close state
        // await getUserData();
        // }

        let timeout = 1000;

        if (isTemplateOrGroupApp()) {
            if (workspaceDetail || groupAppDetail) {
                timeout = 0;
            } else {
                timeout = 2000;
            }
        }

        setTimeout(() => {
            setLoading(false);
        }, timeout);
    }, [dispatch, getGroupAppDetailByToken, getRestaurantDetailByToken, getRestaurantFuncData, groupAppDetail, workspaceDetail]);

    const ref = useCallback((refNavigation: NavigationContainerRef<{}>) => {
        navigationRef.current = refNavigation;
        NavigationService.setTopLevelNavigator(refNavigation);
    }, []);

    const onStateChange = useCallback(async () => {
        const previousRouteName = routeNameRef.current;
        const currentRouteName = navigationRef.current?.getCurrentRoute()?.name;

        log('Current Screen', currentRouteName);

        if (previousRouteName !== currentRouteName) {
            await analytics().logScreenView({
                screen_name: currentRouteName,
                screen_class: currentRouteName,
            });
        }

        routeNameRef.current = currentRouteName;
    }, []);

    const onReady = useCallback(() => {
        routeNameRef.current = navigationRef.current?.getCurrentRoute()?.name;
        if (isUserLoggedIn) {
            // set axios header token when open app from close state
            setHeaderToken(userData?.token);
            setHeaderContentLanguage(userData?.locale);
            dispatch(StorageActions.setStorageLanguage(userData?.locale));
            I18nApp.changeLanguage(userData?.locale);
        } else {
            if (typeof storageLanguage === 'string') {
                setHeaderContentLanguage(storageLanguage);
                I18nApp.changeLanguage(storageLanguage);
            } else if (typeof storageLanguage === 'object') {
                setHeaderContentLanguage(storageLanguage?.locale || 'nl');
                I18nApp.changeLanguage(storageLanguage?.locale || 'nl');
            }

        }

        getInitData();
    }, [dispatch, getInitData, isUserLoggedIn, storageLanguage, userData?.locale, userData?.token]);

    const checkAppScreen = useCallback(() => {
        if (loading) {
            return (
                <StackNavigator.Screen
                    options={{
                        ...TransitionPresets.ModalFadeTransition,
                        gestureEnabled: false,
                    }}
                    name={SCREENS.SPLASH_SCREEN}
                    component={SplashScreen}
                />
            );
        }

        if (showWalkThrough) {
            return (
                <StackNavigator.Screen
                    options={{
                        ...TransitionPresets.ModalFadeTransition,
                        gestureEnabled: false,
                    }}
                    name={SCREENS.WALK_THROUGH_SCREEN}
                    component={WalkThroughScreen}
                />
            );
        } else {
            return (
                <>
                    <StackNavigator.Screen
                        name={SCREENS.BOTTOM_TAB_SCREEN}
                        component={BottomTab}
                        options={{
                            ...TransitionPresets.ModalFadeTransition,
                        }}
                    />
                    <StackNavigator.Screen
                        name={SCREENS.GROUP_APP_HOME_SCREEN}
                        component={GroupAppHomeScreen}
                        options={{
                            gestureEnabled: false,
                        }}
                    />
                    <StackNavigator.Screen
                        name={SCREENS.WORKSPACE_HOME_SCREEN}
                        component={WorkSpaceHomeScreen}
                        options={{
                            gestureEnabled: false,
                            ...SlideFromLeft
                        }}
                    />
                    <StackNavigator.Screen
                        name={SCREENS.LOGIN_SCREEN}
                        component={LoginScreen}
                    />
                    <StackNavigator.Screen
                        name={SCREENS.FORGOT_PASSWORD_SCREEN}
                        component={ForgotPasswordScreen}
                    />
                    <StackNavigator.Screen
                        name={SCREENS.CREATE_ACCOUNT_SCREEN}
                        component={CreateAccountScreen}
                    />
                    <StackNavigator.Screen
                        name={SCREENS.SELECT_ADDRESS_SCREEN}
                        component={SelectAddressScreen}
                    />
                    <StackNavigator.Screen
                        name={SCREENS.PRODUCT_DETAIL_SCREEN}
                        component={ProductDetailScreen}
                        options={{
                            ...TransitionPresets.FadeFromBottomAndroid,
                            gestureEnabled: false,
                        }}
                    />
                    <StackNavigator.Screen
                        name={SCREENS.SELECT_ORDER_TYPE_SCREEN}
                        component={SelectOrderTypeScreen}
                        options={{
                            ...TransitionPresets.ModalTransition,
                            presentation: 'transparentModal',
                            cardOverlayEnabled: true,
                            gestureDirection: 'vertical',
                            cardStyle: { backgroundColor: 'transparent' },
                        }}
                    />
                    <StackNavigator.Screen
                        options={{
                            ...TransitionPresets.ModalSlideFromBottomIOS,
                            gestureEnabled: false
                        }}
                        name={SCREENS.ORDER_SUCCESS_SCREEN}
                        component={OrderSuccessScreen}
                    />
                    <StackNavigator.Screen
                        options={{
                            ...TransitionPresets.ModalSlideFromBottomIOS,
                            gestureEnabled: false
                        }}
                        name={SCREENS.ORDER_FAIL_SCREEN}
                        component={OrderFailScreen}
                    />
                    <StackNavigator.Screen
                        name={SCREENS.WEB_VIEW_SCREEN}
                        component={WebViewScreen}
                    />
                    <StackNavigator.Screen
                        name={SCREENS.TEMPLATE_LIST_NOTIFICATION_SCREEN}
                        component={ListNotificationScreen}
                    />
                    <StackNavigator.Screen
                        name={SCREENS.TEMPLATE_ORDER_HISTORY_SCREEN}
                        component={OrderHistoryScreen}
                    />
                    <StackNavigator.Screen
                        name={SCREENS.TEMPLATE_JOB_REGISTER_SCREEN}
                        component={TemplateJobRegisterScreen}
                    />
                    <StackNavigator.Screen
                        name={SCREENS.EDIT_PROFILE_SCREEN_2}
                        component={EditProfileScreen}
                        options={{
                            gestureEnabled: false
                        }}
                    />
                    <StackNavigator.Screen
                        name={SCREENS.SELECT_LANGUAGE_SCREEN}
                        component={SelectLanguageScreen}
                    />
                </>
            );
        }
    }, [loading, showWalkThrough]);

    return (
        <NavigationContainer
            ref={ref}
            onReady={onReady}
            onStateChange={onStateChange}
        >
            <StackNavigator.Navigator
                detachInactiveScreens={false}
                initialRouteName={SCREENS.BOTTOM_TAB_SCREEN}
                screenOptions={{
                    ...TransitionPresets.SlideFromRightIOS,
                    headerShown: false,
                    gestureEnabled: true,
                    cardStyle: {
                        backgroundColor: themeColors.color_app_background,
                    },
                }}
            >
                {checkAppScreen()}
                <StackNavigator.Screen
                    options={{ gestureEnabled: false }}
                    name={SCREENS.CONFIRM_REGISTER_SCREEN}
                    component={ConfirmRegisterScreen}
                />
                <StackNavigator.Screen
                    options={{ gestureEnabled: false }}
                    name={SCREENS.DEEP_LINK_ERROR_SCREEN}
                    component={DeepLinkErrorScreen}
                />
                <StackNavigator.Screen
                    options={{ gestureEnabled: false }}
                    name={SCREENS.RESET_PASSWORD_SCREEN}
                    component={ResetPasswordScreen}
                />
            </StackNavigator.Navigator>
        </NavigationContainer>
    );
};

const NavigationWrapper = () => (
    <>
        <AppNavigation />
        <CustomToastProvider />
        <LoadingOverlay />
        <ModalRemindCart />
        <ProcessingOnlinePaymentOrderDialog />
        <DetailNotificationDialog/>
    </>
);

export default memo(NavigationWrapper);
