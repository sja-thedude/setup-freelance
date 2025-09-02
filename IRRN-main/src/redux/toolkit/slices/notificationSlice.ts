import { createSlice } from '@reduxjs/toolkit';
import { Constants } from '@src/configs';
import { NotificationListModel } from '@src/network/dataModels/NotificationListModel';
import { NotificationDetailModel } from '@src/network/dataModels/NotificationDetailModel';

interface State {
    notifications: {
        loading: boolean,
        refreshing: boolean,
        canLoadMore: boolean,
        data: Array<NotificationListModel>
    },
    notificationDetail: NotificationDetailModel,
    notificationBadge: number,
}

const initialState: State = {
    notifications: {
        loading: false,
        refreshing: false,
        canLoadMore: false,
        data: [],
    },
    notificationDetail: <NotificationDetailModel>{},
    notificationBadge: 0,
};

const notificationSlice = createSlice({
    name: 'notificationSlice',
    initialState,
    reducers: {
        getNotificationList: (state, { payload }) => {
            const { refreshing } = payload;
            state.notifications.loading = true;
            state.notifications.refreshing = refreshing;
        },
        getNotificationListSuccess: (state, { payload }) => {
            const { data } = payload;
            state.notifications.loading = false;
            state.notifications.refreshing = false;
            state.notifications.data = data.current_page === 1 ? data.data : [...state.notifications.data].concat(data.data);
            state.notifications.canLoadMore = data.data.length === Constants.PAGE_SIZE;
        },
        getNotificationListFail: (state) => {
            state.notifications.loading = false;
            state.notifications.refreshing = false;
            state.notifications.canLoadMore = false;
        },
        clearNotificationList: (state) => {
            state.notifications = initialState.notifications;
        },

        getNotificationDetail: () => {
        },
        getNotificationDetailSuccess: (state, { payload }) => {
            state.notificationDetail = payload;
        },
        clearNotificationDetail: (state) => {
            state.notificationDetail = initialState.notificationDetail;
        },
        markNotification: () => {
        },
        updateNotificationBadge: (state, { payload }) => {
            state.notificationBadge = payload >= 0 ? payload : 0;
        },

    },
});

export default notificationSlice;
