import { createSlice } from '@reduxjs/toolkit';
import { Constants } from '@src/configs';
import { OrderDetailModel } from '@src/network/dataModels/OrderDetailModel';
import { OrderHistoryListItem, } from '@src/network/dataModels/OrderHistoryListItem';

interface State {
    orderHistoryList: {
        loading: boolean,
        refreshing: boolean,
        canLoadMore: boolean,
        data: Array<OrderHistoryListItem>
    },
    orderDetail: OrderDetailModel,
}

const initialState: State = {
    orderHistoryList: {
        loading: false,
        refreshing: false,
        canLoadMore: false,
        data: [],
    },
    orderDetail: <OrderDetailModel>{},
};

const orderSlice = createSlice({
    name: 'orderSlice',
    initialState,
    reducers: {
        getOrderHistoryList: (state, { payload }) => {
            const { refreshing } = payload;
            state.orderHistoryList.loading = true;
            state.orderHistoryList.refreshing = refreshing;
        },
        getOrderHistoryListSuccess: (state, { payload }) => {
            const { data } = payload;
            state.orderHistoryList.loading = false;
            state.orderHistoryList.refreshing = false;
            state.orderHistoryList.data = data.current_page === 1 ? data.data : [...state.orderHistoryList.data].concat(data.data);
            state.orderHistoryList.canLoadMore = data.data.length === Constants.PAGE_SIZE;
        },
        getOrderHistoryListFail: (state) => {
            state.orderHistoryList.loading = false;
            state.orderHistoryList.refreshing = false;
            state.orderHistoryList.canLoadMore = false;
        },
        clearOrderHistoryList: (state) => {
            state.orderHistoryList = initialState.orderHistoryList;
        },

        getOrderDetail: () => {
        },
        getOrderDetailSuccess: (state, { payload }) => {
            state.orderDetail = payload;
        },
        clearOrderDetail: (state) => {
            state.orderDetail = initialState.orderDetail;
        },

    },
});

export default orderSlice;
