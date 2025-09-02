import productSlice from '../slices/productSlice';

export const { reducer } = productSlice;

export const ProductActions = {
    ...productSlice.actions,
};

export type TypesActions = typeof productSlice.actions;
export type TypesState = ReturnType<typeof reducer>;

export const getProductsAction = (showLoading: boolean, isRefreshing: boolean, params: object, callback: Function) => ({
    type: ProductActions.getProducts.type,
    payload: {
        loading: showLoading,
        refreshing: isRefreshing,
    },
    params,
    callback,
});

export const updateProductsDataAction = (data: any) => ({
    type: ProductActions.updateProducts.type,
    payload: data,
});

export const updateProductsOriginDataAction = (data: any) => ({
    type: ProductActions.updateProductsOrigin.type,
    payload: data,
});

// export const getFavoriteProductsAction = (showLoading: boolean, isRefreshing: boolean, params: object) => ({
//     type: ProductActions.getFavoriteProducts.type,
//     payload: {
//         loading: showLoading,
//         refreshing: isRefreshing,
//     },
//     params,
// });