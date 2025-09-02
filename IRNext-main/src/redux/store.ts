import { configureStore, combineReducers, MiddlewareArray } from "@reduxjs/toolkit";
import { setupListeners } from "@reduxjs/toolkit/dist/query";
import { persistStore, persistReducer } from 'redux-persist'
import createWebStorage from "redux-persist/lib/storage/createWebStorage";
import { authApi } from "./services/authApi";
import authReducer from "./slices/authSlice";
import cartReducer from "./slices/cartSlice";
import { productApi } from "./services/product/productApi";
import productReducer from "./slices/product/productSlice";
import { dataTokenApi } from './services/dataTokenApi';
import dataTokenReducer from "./slices/dataTokenSlice";
import { couponsApi } from './services/couponsApi';
import couponReducer from "./slices/couponSlice";
import { categoriesApi } from './services/categoriesApi';
import categoriesReducer from "./slices/categoriesSlice";
import { favoritesApi } from './services/product/favoritesApi';
import favoritesReducer from "./slices/product/favoritesSlice";
import { productsCategoryApi } from './services/product/productsCategoryApi';
import productsCategoryReducer from "./slices/product/productCategorySlice";
import workdspaceSettingReducer from "./slices/workspace/workdspaceSettingSlice";
import { workspaceSettingApi } from "./services/workspace/workspaceSettingApi";
import { workspaceJobApi } from "./services/workspace/workspaceJobApi";
import { profileApi } from './services/profileApi';
import profileReducer from "./slices/profileSlice";
import workdspaceDataReducer from "./slices/workspace/workspaceDataSlice";
import { workspaceDataApi } from "./services/workspace/workspaceDataApi";
import workdspaceOpenHoursReducer from "./slices/workspace/workspaceOpenHoursSlice";
import { workspaceOpenHoursApi } from "./services/workspace/workspaceOpenHoursApi";
import workdspaceDeliveryConditionsReducer from "./slices/workspace/workspaceDeliveryConditionsSlice";
import { workspaceDeliveryConditionsApi } from "./services/workspace/workspaceDeliveryConditionsApi";
import { productsCategoryListApi } from './services/product/productsCategoryListApi';
import productsCategoryListReducer from "./slices/product/productsCategoryListSlice";
import  groupOrderReducer from "./slices/groupOrderSlice";
import optionStoreReducer from "./slices/optionsStoreSlice";
import productFavReducer from "./slices/productFavSlice";
import portalAddressReducer from "./slices/portalAddressSlice";
import flagNextReducer from "./slices/flagNextSlice";
import flagSortReducer from "./slices/flagSortSlice";
import flagForcusReducer from "./slices/flagForcusSlice";
import flagDesktopChangeTypeReducer from "./slices/flagDesktopChangeTypeSilce";
import groupOrderDeskReducer from "./slices/groupOrderDeskSlice";
import isOnGroupDeskReducer  from "./slices/isOnGroupDeskSlice";

const reducers = combineReducers({
    auth: authReducer,
    cart: cartReducer,
    dataToken: dataTokenReducer,
    dataCoupon: couponReducer,
    dataCategories: categoriesReducer,
    product: productReducer,
    productsCategory: productsCategoryReducer,
    profile: profileReducer,
    favorite: favoritesReducer,
    workspaceSetting: workdspaceSettingReducer,
    workspaceData: workdspaceDataReducer,
    workspaceOpenHours: workdspaceOpenHoursReducer,
    workspaceDeliveryConditions: workdspaceDeliveryConditionsReducer,
    productsCategoryList: productsCategoryListReducer,
    groupOrder: groupOrderReducer,
    optionStore: optionStoreReducer,
    productFav: productFavReducer,
    portalAddress: portalAddressReducer,
    flagNext: flagNextReducer,
    flagDesktopChangeType: flagDesktopChangeTypeReducer,
    flagSort: flagSortReducer,
    flagForcus: flagForcusReducer,
    groupOrderDesk: groupOrderDeskReducer,
    isOnGroupDesk: isOnGroupDeskReducer,
    [authApi.reducerPath]: authApi.reducer,
    [productApi.reducerPath]: productApi.reducer,
    [dataTokenApi.reducerPath]: dataTokenApi.reducer,
    [couponsApi.reducerPath]: couponsApi.reducer,
    [categoriesApi.reducerPath]: categoriesApi.reducer,
    [favoritesApi.reducerPath]: favoritesApi.reducer,
    [productsCategoryApi.reducerPath]: productsCategoryApi.reducer,
    [profileApi.reducerPath]: profileApi.reducer,
    [workspaceSettingApi.reducerPath]: workspaceSettingApi.reducer,
    [workspaceJobApi.reducerPath]: workspaceJobApi.reducer,
    [workspaceDataApi.reducerPath]: workspaceDataApi.reducer,
    [workspaceOpenHoursApi.reducerPath]: workspaceOpenHoursApi.reducer,
    [workspaceDeliveryConditionsApi.reducerPath]: workspaceDeliveryConditionsApi.reducer,
    [productsCategoryListApi.reducerPath]: productsCategoryListApi.reducer,
});

const createNoopStorage = () => {
    return {
        getItem(_key: any) {
            return Promise.resolve(null);
        },
        setItem(_key: any, value: any) {
            return Promise.resolve(value);
        },
        removeItem(_key: any) {
            return Promise.resolve();
        },
    };
};

const storage = typeof window !== 'undefined' ? createWebStorage('local') : createNoopStorage();
const persistConfig = {
    key: 'root',
    storage: storage,
    whitelist: ['storage', 'auth', 'cart']
};

const persistedReducer = persistReducer(persistConfig, reducers);

export const store = configureStore({
    reducer: persistedReducer,
    devTools: process.env.NODE_ENV !== 'production',
    middleware: (getDefaultMiddleware) =>
        (getDefaultMiddleware({
            serializableCheck: false,
            immutableCheck: false,
        }) as MiddlewareArray<any>).concat([
            authApi.middleware,
            productApi.middleware,
            dataTokenApi.middleware,
            couponsApi.middleware,
            categoriesApi.middleware,
            favoritesApi.middleware,
            productsCategoryApi.middleware,
            workspaceSettingApi.middleware,
            workspaceJobApi.middleware,
            profileApi.middleware,
            workspaceDataApi.middleware,
            workspaceOpenHoursApi.middleware,
            workspaceDeliveryConditionsApi.middleware,
            productsCategoryListApi.middleware,
        ]), // use MiddlewareArray
});


setupListeners(store.dispatch);

export type RootState = ReturnType<typeof store.getState>;
export type AppDispatch = typeof store.dispatch;
export const persistorStore = persistStore(store);
