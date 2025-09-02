import {
    call,
    put,
    takeLatest,
} from 'redux-saga/effects';

import * as ProductServices from '@network/services/productServices';
import store from '@redux/store';
import Toast from '@src/components/toast/Toast';
import { ORDER_TYPE } from '@src/configs/constants';
import { processResponseData } from '@src/network/util/responseDataUtility';
import { LoadingActions } from '@src/redux/toolkit/actions/loadingActions';
import { ProductActions } from '@src/redux/toolkit/actions/productActions';

function* getProductListSaga(params) {
    try {
        yield put(LoadingActions.showGlobalLoading(params.payload.loading));
        const { data, message } = processResponseData(yield call(ProductServices.getProductListService, params.params));
        yield put(LoadingActions.showGlobalLoading(false));

        if (data !== null) {
            if (data.success) {
                let filteredData = data;
                const currentRestaurantId = store.getState().storageReducer?.cartProducts?.restaurant?.id;

                if (params.params.workspace_id === currentRestaurantId) {
                    const currentOrderType = store.getState().storageReducer?.cartProducts?.type || ORDER_TYPE.TAKE_AWAY;

                    if (currentOrderType === ORDER_TYPE.DELIVERY) {
                        filteredData = {
                            ...data,
                            data: {
                                ...data.data,
                                data: data.data.data.filter((item) => item.available_delivery)
                            }
                        };
                    }

                    if (currentOrderType === ORDER_TYPE.GROUP_ORDER) {
                        const isProductLimit = store.getState().storageReducer?.cartProducts?.groupFilter?.groupData.is_product_limit;
                        const groupAvailableProduct = store.getState().storageReducer?.cartProducts?.groupFilter?.groupData.products;
                        const filterByDeliverable = store.getState().storageReducer?.cartProducts?.groupFilter?.filterByDeliverable;

                        let categoryData = data.data.data;

                        if (isProductLimit) {
                            categoryData = data.data.data.map((category) => {
                                // get all products available for the group
                                const availableProduct = category.products.map((p) => {
                                    if (groupAvailableProduct.findIndex((gP) => gP.id === p.id) >= 0) {
                                        return p;
                                    }
                                    return false;
                                });

                                return {
                                    ...category,
                                    products: availableProduct.filter(Boolean)
                                };
                            });
                        }

                        filteredData = {
                            ...data,
                            data: {
                                ...data.data,
                                data:  categoryData.filter((c) => c.products.length).filter((d) => filterByDeliverable ? d.available_delivery : true) // filter by delivery available category
                            }
                        };
                    }
                }

                // yield put(ProductActions.getProductsSuccess(filteredData));
                params?.callback && params.callback(true, filteredData.data);
            } else {
                // yield put(ProductActions.getProductsFail());
                params?.callback && params.callback(false);
                Toast.showToastError(message);
            }
        }
    } catch (error) {
        yield put(ProductActions.getProductsFail());
        yield put(LoadingActions.showGlobalLoading(false));
        (error?.response?.data?.message || error?.message) && Toast.showToast(error?.response?.data?.message || error?.message);
    }
}

// function* getFavoriteProductListSaga(params) {
//     try {
//         yield put(LoadingActions.showGlobalLoading(params.payload.loading));
//         const { data, message } = processResponseData(yield call(ProductServices.getFavoriteProductService, params.params));
//         yield put(LoadingActions.showGlobalLoading(false));

//         if (data !== null) {
//             if (data.success) {
//                 yield put(ProductActions.getFavoriteProductsSuccess(data));
//             } else {
//                 yield put(ProductActions.getFavoriteProductsFail());
//                 Toast.showToastError(message);
//             }
//         }
//     } catch (error) {
//         yield put(ProductActions.getFavoriteProductsFail());
//         yield put(LoadingActions.showGlobalLoading(false));
//         (error?.response?.data?.message || error?.message) && Toast.showToast(error?.response?.data?.message || error?.message);
//     }
// }

function* productSagas() {
    yield takeLatest(ProductActions.getProducts.type, getProductListSaga);
    // yield takeLatest(ProductActions.getFavoriteProducts.type, getFavoriteProductListSaga);

}

export default productSagas;
