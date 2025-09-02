import PushNotification from 'react-native-push-notification';

export const updateAppBadge = (badge: number) => {
    PushNotification.setApplicationIconBadgeNumber(badge);
};