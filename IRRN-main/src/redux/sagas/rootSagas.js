import { all } from 'redux-saga/effects';

import restaurantSagas from '@src/redux/sagas/restaurantSagas';
import notificationSagas from '@src/redux/sagas/notificationSagas';
import orderSagas from '@src/redux/sagas/orderSagas';
import productSagas from '@src/redux/sagas/productSagas';

export default function* rootSagas() {
    yield all([restaurantSagas(), notificationSagas(), orderSagas(), productSagas()]);
}
