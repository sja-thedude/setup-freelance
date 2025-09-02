import {
    call,
    put,
    takeLatest,
    delay
} from 'redux-saga/effects';

import * as NotificationServices from '@network/services/notificationServices';
import Toast from '@src/components/toast/Toast';
import { processResponseData } from '@src/network/util/responseDataUtility';
import { LoadingActions } from '@src/redux/toolkit/actions/loadingActions';
import { NotificationActions } from '@src/redux/toolkit/actions/notificationActions';

function* getNotificationListSaga(params) {
    try {
        yield put(LoadingActions.showGlobalLoading(params.payload.loading));
        const { data, message } = processResponseData(yield call(NotificationServices.getListNotificationService, params.params));
        yield put(LoadingActions.showGlobalLoading(false));

        if (data !== null) {
            if (data.success) {
                yield put(NotificationActions.getNotificationListSuccess(data));
                yield put(NotificationActions.updateNotificationBadge(data.data.total_unread));
            } else {
                yield put(NotificationActions.getNotificationListFail());
                Toast.showToastError(message);
            }
        }
    } catch (error) {
        yield put(NotificationActions.getNotificationListFail());
        yield put(LoadingActions.showGlobalLoading(false));
        (error?.response?.data?.message || error?.message) && Toast.showToast(error?.response?.data?.message || error?.message);
    }
}

function* getNotificationDetailSaga(params) {
    try {
        yield put(LoadingActions.showGlobalLoading(true));
        const { data, message } = processResponseData(yield call(NotificationServices.detailNotificationService, params.params));
        yield put(LoadingActions.showGlobalLoading(false));

        if (data !== null) {
            if (data.success) {
                yield put(NotificationActions.getNotificationDetailSuccess(data.data));
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

function* markNotificationSaga(params) {
    try {
        yield put(LoadingActions.showGlobalLoading(true));
        const { data } = processResponseData(yield call(NotificationServices.markNotificationService, params.params));
        yield put(LoadingActions.showGlobalLoading(false));

        if (data !== null) {
            if (data.success) {
                params.callback && params.callback();
            }
        }
    } catch (error) {
        yield put(LoadingActions.showGlobalLoading(false));
    }
}

function* restaurantSagas() {
    yield takeLatest(NotificationActions.getNotificationList.type, getNotificationListSaga);
    yield takeLatest(NotificationActions.getNotificationDetail.type, getNotificationDetailSaga);
    yield takeLatest(NotificationActions.markNotification.type, markNotificationSaga);

}

export default restaurantSagas;
