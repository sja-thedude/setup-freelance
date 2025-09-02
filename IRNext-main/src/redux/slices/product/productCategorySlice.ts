import { createSlice } from '@reduxjs/toolkit';
import { productsCategoryApi } from '@/redux/services/product/productsCategoryApi';

const initialState = {
    productsCategory: null,
    productsCategoryOptions: null
};

// Config slice
export const productsCategorySlice = createSlice({
    name: 'productsCategory',
    initialState,
    reducers: {
        //
    },
    extraReducers: (builder) => {
        builder.addMatcher(productsCategoryApi.endpoints.getProductsCategoryById.matchFulfilled, (state, action) => {
            state.productsCategory = action.payload;
        })
        builder.addMatcher(productsCategoryApi.endpoints.getProductsCategoryById.matchRejected, (state) => {
            state.productsCategory = null;
        })
        builder.addMatcher(productsCategoryApi.endpoints.getProductsCategoryOptionsById.matchFulfilled, (state, action) => {
            state.productsCategory = action.payload;
        })
        builder.addMatcher(productsCategoryApi.endpoints.getProductsCategoryOptionsById.matchRejected, (state) => {
            state.productsCategory = null;
        })
    },
});

export const selectProductsCategory = (state: any) => state.productsCategory;
export const selectProductsCategoryOptions = (state: any) => state.productsCategoryOptions;

// Export reducer
export default productsCategorySlice.reducer;
