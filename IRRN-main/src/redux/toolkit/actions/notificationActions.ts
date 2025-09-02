import notificationSlice from '../slices/notificationSlice';

export const { reducer } = notificationSlice;

export const NotificationActions = {
    ...notificationSlice.actions,
};

export type TypesActions = typeof notificationSlice.actions;
export type TypesState = ReturnType<typeof reducer>;

export const getNotificationListAction = (showLoading: boolean, isRefreshing: boolean, params: object) => ({
    type: NotificationActions.getNotificationList.type,
    payload: {
        loading: showLoading,
        refreshing: isRefreshing,
    },
    params,
});

export const getNotificationDetailAction = (params: object, callback?: Function) => ({
    type: NotificationActions.getNotificationDetail.type,
    callback,
    params,
});

export const markNotificationAction = (params: object, callback: Function) => ({
    type: NotificationActions.markNotification.type,
    callback,
    params,
});