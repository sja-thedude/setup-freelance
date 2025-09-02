import {
    call,
    put,
    takeLatest,
} from 'redux-saga/effects';

import * as RestaurantService from '@network/services/restaurantServices';
import Toast from '@src/components/toast/Toast';
import { processResponseData } from '@src/network/util/responseDataUtility';
import { LoadingActions } from '@src/redux/toolkit/actions/loadingActions';
import { RestaurantActions, } from '@src/redux/toolkit/actions/restaurantActions';

function* getRecentRestaurantSaga(params) {
    try {
        yield put(LoadingActions.showGlobalLoading(params.payload.loading));
        const { data, message } = processResponseData(yield call(RestaurantService.getRestaurantRecentService, params.params));
        yield put(LoadingActions.showGlobalLoading(false));

        if (data !== null) {
            if (data.success) {
                yield put(RestaurantActions.getRecentRestaurantSuccess(data));
            } else {
                yield put(RestaurantActions.getRecentRestaurantFail());
                Toast.showToastError(message);
            }
        }
    } catch (error) {
        yield put(RestaurantActions.getRecentRestaurantFail());
        yield put(LoadingActions.showGlobalLoading(false));
        (error?.response?.data?.message || error?.message) && Toast.showToast(error?.response?.data?.message || error?.message);
    }
}

function* getFavoriteRestaurantSaga(params) {
    try {
        yield put(LoadingActions.showGlobalLoading(params.payload.loading));
        const { data, message } = processResponseData(yield call(RestaurantService.getRestaurantFavoriteService, params.params));
        yield put(LoadingActions.showGlobalLoading(false));

        if (data !== null) {
            if (data.success) {
                yield put(RestaurantActions.getFavoriteRestaurantSuccess(data));
            } else {
                yield put(RestaurantActions.getFavoriteRestaurantFail());
                Toast.showToastError(message);
            }
        }
    } catch (error) {
        yield put(RestaurantActions.getFavoriteRestaurantFail());
        yield put(LoadingActions.showGlobalLoading(false));
        (error?.response?.data?.message || error?.message) && Toast.showToast(error?.response?.data?.message || error?.message);
    }
}

function* getAllRestaurantSaga(params) {
    try {
        yield put(LoadingActions.showGlobalLoading(params.payload.loading));
        const { data, message } = processResponseData(yield call(RestaurantService.getRestaurantNearbyService, params.params));
        yield put(LoadingActions.showGlobalLoading(false));

        if (data !== null) {
            if (data.success) {
                yield put(RestaurantActions.getAllRestaurantSuccess(data));
            } else {
                yield put(RestaurantActions.getAllRestaurantFail());
                Toast.showToastError(message);
            }
        }
    } catch (error) {
        yield put(RestaurantActions.getAllRestaurantFail());
        yield put(LoadingActions.showGlobalLoading(false));
        (error?.response?.data?.message || error?.message) && Toast.showToast(error?.response?.data?.message || error?.message);
    }
}

function* getRestaurantDetailSaga(params) {
    try {
        yield put(LoadingActions.showGlobalLoading(params.payload.loading));
        const { data, message } = processResponseData(yield call(RestaurantService.getRestaurantDetailService, params.params));
        yield put(LoadingActions.showGlobalLoading(false));

        if (data !== null) {
            if (data.success) {
                yield put(RestaurantActions.getRestaurantDetailSuccess(data));
            } else {
                yield put(RestaurantActions.getRestaurantDetailFail());
                Toast.showToastError(message);
            }
        }
    } catch (error) {
        yield put(RestaurantActions.getRestaurantDetailFail());
        yield put(LoadingActions.showGlobalLoading(false));
        (error?.response?.data?.message || error?.message) && Toast.showToast(error?.response?.data?.message || error?.message);
    }
}

function* restaurantSagas() {
    yield takeLatest(RestaurantActions.getRecentRestaurant.type, getRecentRestaurantSaga);
    yield takeLatest(RestaurantActions.getFavoriteRestaurant.type, getFavoriteRestaurantSaga);
    yield takeLatest(RestaurantActions.getAllRestaurant.type, getAllRestaurantSaga);
    yield takeLatest(RestaurantActions.getRestaurantDetail.type, getRestaurantDetailSaga);

}

export default restaurantSagas;
