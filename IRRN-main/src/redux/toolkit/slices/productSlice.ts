import uniq from 'lodash/uniq';

import { createSlice } from '@reduxjs/toolkit';
import { Constants } from '@src/configs';
import { ProductSectionModel, } from '@src/network/dataModels/ProductSectionModel';

export interface ProductSectionModelInterFace extends ProductSectionModel {
    sortType: number,
}

interface State {
    products: {
        loading: boolean,
        refreshing: boolean,
        canLoadMore: boolean,
        data: Array<ProductSectionModelInterFace>
        dataOrigin: Array<ProductSectionModelInterFace>
    },

    productsFavorite: Array<number>,
    modalIdsHoliday: number[]
}

const initialState: State = {
    products: {
        loading: false,
        refreshing: false,
        canLoadMore: false,
        data: [],
        dataOrigin: [],
    },

    productsFavorite: [],
    modalIdsHoliday: []
};

const productSlice = createSlice({
    name: 'productSlice',
    initialState,
    reducers: {
        getProducts: (state, { payload }) => {
            const { refreshing } = payload;
            state.products.loading = true;
            state.products.refreshing = refreshing;
        },
        getProductsSuccess: (state, { payload }) => {
            const { data } = payload;
            state.products.loading = false;
            state.products.refreshing = false;
            state.products.data = data.current_page === 1 ? data.data : [...state.products.data].concat(data.data);
            state.products.dataOrigin = data.current_page === 1 ? data.data : [...state.products.dataOrigin].concat(data.data);
            state.products.canLoadMore = data.data.length === Constants.LARGE_PAGE_SIZE;
        },
        getProductsFail: (state) => {
            state.products.loading = false;
            state.products.refreshing = false;
            state.products.canLoadMore = false;
        },
        updateProducts: (state, { payload }) => {
            state.products.data = payload;
        },
        updateProductsOrigin: (state, { payload }) => {
            state.products.dataOrigin = payload;
        },
        clearProducts: (state) => {
            state.products = initialState.products;
        },

        // getFavoriteProducts: (state, { payload }) => {
        //     const { refreshing } = payload;
        //     state.productsFavorite.loading = true;
        //     state.productsFavorite.refreshing = refreshing;
        // },
        // getFavoriteProductsSuccess: (state, { payload }) => {
        //     const { data } = payload;
        //     state.productsFavorite.loading = false;
        //     state.productsFavorite.refreshing = false;
        //     state.productsFavorite.data = data.current_page === 1 ? data.data : [...state.productsFavorite.data].concat(data.data);
        //     state.productsFavorite.canLoadMore = data.data.length === Constants.LARGE_PAGE_SIZE;
        // },
        // getFavoriteProductsFail: (state) => {
        //     state.productsFavorite.loading = false;
        //     state.productsFavorite.refreshing = false;
        //     state.productsFavorite.canLoadMore = false;
        // },
        addMultiFavoriteProducts: (state, { payload }) => {
            const newData = [...state.productsFavorite, ...payload];
            state.productsFavorite = uniq(newData);
        },
        addFavoriteProduct: (state, { payload }) => {
            const newData = [...state.productsFavorite, payload];
            state.productsFavorite = uniq(newData);
        },
        removeFavoriteProduct: (state, { payload }) => {
            const newData = state.productsFavorite.filter((p) => p !== payload);
            state.productsFavorite = uniq(newData);
        },
        clearFavoriteProducts: (state) => {
            state.productsFavorite = initialState.productsFavorite;
        },
        setModalIdsHoliday: (state, { payload }) => {
            state.modalIdsHoliday = uniq([...(state.modalIdsHoliday || []), payload]);
        }
    },
});

export default productSlice;
