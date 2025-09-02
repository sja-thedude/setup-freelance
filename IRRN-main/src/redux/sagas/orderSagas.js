import {
    call,
    put,
    takeLatest,
    delay
} from 'redux-saga/effects';

import * as OrderServices from '@network/services/orderServices';
import Toast from '@src/components/toast/Toast';
import { processResponseData } from '@src/network/util/responseDataUtility';
import { LoadingActions } from '@src/redux/toolkit/actions/loadingActions';
import { OrderActions } from '@src/redux/toolkit/actions/orderActions';

function* getOrderListSaga(params) {
    try {
        yield put(LoadingActions.showGlobalLoading(params.payload.loading));
        const { data, message } = processResponseData(yield call(OrderServices.getOrderHistoryService, params.params));
        yield put(LoadingActions.showGlobalLoading(false));

        if (data !== null) {
            if (data.success) {
                yield put(OrderActions.getOrderHistoryListSuccess(data));
            } else {
                yield put(OrderActions.getOrderHistoryListFail());
                Toast.showToastError(message);
            }
        }
    } catch (error) {
        yield put(OrderActions.getOrderHistoryListFail());
        yield put(LoadingActions.showGlobalLoading(false));
        (error?.response?.data?.message || error?.message) && Toast.showToast(error?.response?.data?.message || error?.message);
    }
}

function* getOrderDetailSaga(params) {
    try {
        yield put(LoadingActions.showGlobalLoading(true));
        const { data, message } = processResponseData(yield call(OrderServices.getOrderDetailService, params.params));
        yield put(LoadingActions.showGlobalLoading(false));

        if (data !== null) {
            if (data.success) {
                yield put(OrderActions.getOrderDetailSuccess(data.data));
                yield delay(500);
                params.callback && params.callback();
            } else {
                Toast.showToastError(message);
            }
        }
    } catch (error) {
        yield put(LoadingActions.showGlobalLoading(false));
        (error?.response?.data?.message || error?.message) && Toast.showToast(error?.response?.data?.message || error?.message);
    }
}

function* restaurantSagas() {
    yield takeLatest(OrderActions.getOrderHistoryList.type, getOrderListSaga);
    yield takeLatest(OrderActions.getOrderDetail.type, getOrderDetailSaga);

}

export default restaurantSagas;
