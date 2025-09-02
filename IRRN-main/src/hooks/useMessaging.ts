import {
    useCallback,
    useEffect,
} from 'react';

import omit from 'lodash/omit';
import { getUniqueId } from 'react-native-device-info';
import { useDispatch } from 'react-redux';
import {
    useDeepCompareEffect,
    useEffectOnce,
} from 'react-use';

import notifee, { EventType } from '@notifee/react-native';
import FireBaseMessaging from '@react-native-firebase/messaging';
import { IS_ANDROID } from '@src/configs/constants';
import { registerNotificationToken, } from '@src/network/services/notificationServices';
import {
    getNotificationDetailAction,
    markNotificationAction,
    NotificationActions,
} from '@src/redux/toolkit/actions/notificationActions';
import { updateAppBadge } from '@src/utils/appBadgeUtil';
import { log } from '@src/utils/logger';

import { useAppSelector } from './';
import useIsUserLoggedIn from './useIsUserLoggedIn';
import {
    isGroupApp, isTemplateApp, isTemplateOrGroupApp
} from '@src/utils';
import Config from 'react-native-config';

const useMessaging = () => {
    const isUserLoggedIn = useIsUserLoggedIn();
    // const isEmptyCart = useCheckEmptyCart();

    const notificationNumber = useAppSelector((state) => state.notificationReducer.notificationBadge);
    // const workspaceDetail = useAppSelector((state) => state.storageReducer.templateWorkspaceDetail);
    const dispatch = useDispatch();

    // const { callApi: getRestaurantDetailById } = useCallAPI(
    //         getRestaurantDetailService
    // );

    // const { callApi: getWorkspaceSettingById } = useCallAPI(
    //         getWorkspaceSettingByIdService
    // );

    // const getWorkspaceData = useCallback((workspaceId: number) => {
    //     const promise = [
    //         getRestaurantDetailById({ restaurant_id: workspaceId }),
    //         getWorkspaceSettingById({ restaurant_id: workspaceId }),
    //     ];

    //     Promise.all(promise).then((result) => {
    //         const detailRes = result[0];
    //         const settingRes = result[1];
    //         if (detailRes.success) {
    //             dispatch(StorageActions.setStorageWorkspaceDetail(detailRes.data));
    //             dispatch(RestaurantActions.updateRestaurantDetail(detailRes.data));
    //         }

    //         if (settingRes.success) {
    //             dispatch(StorageActions.setStorageWorkspaceSetting(settingRes.data));
    //         }
    //     });
    // }, [dispatch, getRestaurantDetailById, getWorkspaceSettingById]);

    const markNotification = useCallback((notificationId: number) => {
        const callback = () => {
            dispatch(NotificationActions.updateNotificationBadge(notificationNumber - 1));
        };

        dispatch(markNotificationAction({ id: notificationId }, callback));
    }, [dispatch, notificationNumber]);

    const getNotificationDetail = useCallback((notificationId: number) => {
        dispatch(getNotificationDetailAction({ notification_id: notificationId }, () => markNotification(notificationId)));
    }, [dispatch, markNotification]);

    const handleNotification = useCallback((notification: any) => {
        log('handleNotification', notification);

        if (isUserLoggedIn) {
            const notification_id = JSON.parse(notification.data.notification).id;

            if (notification_id) {
                getNotificationDetail(notification_id);

                // if (isGroupApp() && workspace_id !== workspaceDetail?.id) {
                //     // clear cart product
                //     if (!isEmptyCart) {
                //         dispatch(StorageActions.clearStorageProductsCart());
                //     }
                //     getWorkspaceData(workspace_id);
                // }
            }
        }
    }, [getNotificationDetail, isUserLoggedIn]);

    // handle app icon badge
    useEffect(() => {
        if (isUserLoggedIn) {
            // update badge
            updateAppBadge(notificationNumber);
        } else {
            // remove badge
            updateAppBadge(0);
            notifee.cancelAllNotifications();
        }
    }, [isUserLoggedIn, notificationNumber]);

    // register firebase token
    useEffect(() => {
        if (isUserLoggedIn) {
            const regToken = async () => {
                const authStatus = await FireBaseMessaging().hasPermission();
                if (authStatus === FireBaseMessaging.AuthorizationStatus.AUTHORIZED || authStatus === FireBaseMessaging.AuthorizationStatus.PROVISIONAL) {
                    await FireBaseMessaging().registerDeviceForRemoteMessages();
                    const [deviceId, fcmToken] = await Promise.all([
                        getUniqueId(),
                        FireBaseMessaging().getToken(),
                    ]);

                    log('fcmToken', fcmToken);

                    let appToken: string|undefined = '';

                    if (isTemplateApp()) {
                        appToken = Config.ENV_WORKSPACE_TOKEN;
                    }

                    if (isGroupApp()) {
                        appToken = Config.ENV_GROUP_TOKEN;
                    }

                    registerNotificationToken({
                        type: IS_ANDROID ? 3 : 2,
                        device_id: isTemplateOrGroupApp() ? `${deviceId}-${appToken}` : deviceId,
                        token: fcmToken
                    });
                }
            };
            setTimeout(() => {
                regToken();
            }, 1000);
        }
    }, [isUserLoggedIn]);

    // handle click notification when app in foreground
    useDeepCompareEffect(() => {
        const unsubscribe = notifee.onForegroundEvent(({ detail, type }) => {
            log('__________onForegroundEvent', detail);

            if (type === EventType.PRESS) {
                handleNotification(detail.notification);
            }
        });

        return unsubscribe;
    }, [handleNotification]);

    useEffectOnce(() => {
        notifee.createChannels([{ id: 'notifee_default', name: 'Default channel notification' }]);
    });

    // handle incoming notification when app in foreground
    useDeepCompareEffect(() => {
        const unsubscribe = FireBaseMessaging().onMessage(async (remoteMessage) => {
            const { notification, data, messageId } = remoteMessage;
            log('__________onMessage', remoteMessage);

            dispatch(NotificationActions.updateNotificationBadge(notificationNumber + 1));

            await notifee.displayNotification({
                id: messageId,
                title: notification?.title,
                body: notification?.body,
                android: { channelId: 'notifee_default', smallIcon: 'ic_notification' },
                data: omit(data || {}, ['fcm_options']),
            });
        });

        return unsubscribe;
    }, [dispatch, notificationNumber]);

    useEffectOnce(() => {
        // handle notification open app from closed state
        FireBaseMessaging()
                .getInitialNotification()
                .then((remoteMessage) => {
                    log('__________getInitialNotification', remoteMessage);

                    if (remoteMessage) {
                        handleNotification(remoteMessage);
                    }
                });

        // handle notification open app from background state
        FireBaseMessaging().onNotificationOpenedApp((remoteMessage) => {
            log('__________onNotificationOpenedApp', remoteMessage);

            if (remoteMessage) {
                handleNotification(remoteMessage);
            }
        });
    });
};

export default useMessaging;
