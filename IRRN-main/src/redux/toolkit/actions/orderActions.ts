import orderSlice from '../slices/orderSlice';

export const { reducer } = orderSlice;

export const OrderActions = {
    ...orderSlice.actions,
};

export type TypesActions = typeof orderSlice.actions;
export type TypesState = ReturnType<typeof reducer>;

export const getOrderListAction = (showLoading: boolean, isRefreshing: boolean, params: object) => ({
    type: OrderActions.getOrderHistoryList.type,
    payload: {
        loading: showLoading,
        refreshing: isRefreshing,
    },
    params,
});

export const getOrderDetailAction = (params: object, callback: Function) => ({
    type: OrderActions.getOrderDetail.type,
    callback,
    params,
});

// export const markNotificationAction = (params: object, callback: Function) => ({
//     type: NotificationActions.markNotification.type,
//     callback,
//     params,
// });