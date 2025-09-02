import {
    persistReducer,
    persistStore,
} from 'redux-persist';
import autoMergeLevel2 from 'redux-persist/lib/stateReconciler/autoMergeLevel2';
import createSagaMiddleware from 'redux-saga';

import AsyncStorage from '@react-native-async-storage/async-storage';
import {
    combineReducers,
    configureStore,
} from '@reduxjs/toolkit';
import rootSagas from '@src/redux/sagas/rootSagas';
import { reducer as loadingReducer, } from '@src/redux/toolkit/actions/loadingActions';
import { reducer as locationReducer, } from '@src/redux/toolkit/actions/locationActions';
import { reducer as notificationReducer, } from '@src/redux/toolkit/actions/notificationActions';
import { reducer as orderReducer, } from '@src/redux/toolkit/actions/orderActions';
import { reducer as productReducer, } from '@src/redux/toolkit/actions/productActions';
import { reducer as restaurantReducer, } from '@src/redux/toolkit/actions/restaurantActions';
import { reducer as storageReducer, } from '@src/redux/toolkit/actions/storageActions';
import { reducer as userDataReducer, } from '@src/redux/toolkit/actions/userDataActions';

const sagaMiddleware = createSagaMiddleware();

const rootReducer = combineReducers({
    storageReducer: storageReducer,
    userDataReducer: userDataReducer,
    loadingReducer: loadingReducer,
    locationReducer: locationReducer,
    restaurantReducer: restaurantReducer,
    notificationReducer: notificationReducer,
    orderReducer: orderReducer,
    productReducer: productReducer,
});

export type RootState = ReturnType<typeof rootReducer>;

const persistConfig = {
    key: 'root',
    version: 1,
    storage: AsyncStorage,
    whitelist: ['storageReducer'],
    stateReconciler: autoMergeLevel2
};

const persistedReducer = persistReducer<RootState>(persistConfig, rootReducer);

const store = configureStore({
    reducer: persistedReducer,
    middleware: (getDefaultMiddleware) => [
        ...getDefaultMiddleware({ serializableCheck: false, immutableCheck: false }),
        sagaMiddleware,
    ],
});

sagaMiddleware.run(rootSagas);

export type AppDispatch = typeof store.dispatch;

export const persistor = persistStore(store);
export default store;
