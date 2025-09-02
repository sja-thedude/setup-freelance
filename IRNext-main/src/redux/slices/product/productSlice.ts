import { createSlice } from '@reduxjs/toolkit';
import { productApi } from '@/redux/services/product/productApi';

const initialState = {
    product: null,
    productOptions: null,
    heightCalculation: false
};

// Config slice
export const productSlice = createSlice({
    name: 'product',
    initialState,
    reducers: {
        reloadProductHeightCalculation: (state) => {
            state.heightCalculation = !state.heightCalculation
        }
    },
    extraReducers: (builder) => {
        builder.addMatcher(productApi.endpoints.getProductById.matchFulfilled, (state, action) => {
            state.product = action.payload;
        })
        builder.addMatcher(productApi.endpoints.getProductById.matchRejected, (state) => {
            state.product = null;
        })
        builder.addMatcher(productApi.endpoints.getProductOptionsById.matchFulfilled, (state, action) => {
            state.productOptions = action.payload;
        })
        builder.addMatcher(productApi.endpoints.getProductOptionsById.matchRejected, (state) => {
            state.productOptions = null;
        })
    },
});

// Export actions
export const {reloadProductHeightCalculation} = productSlice.actions

export const selectProduct = (state: any) => state.product;
export const selectProductOptions = (state: any) => state.productOptions;
export const selectProductHeightCalculation = (state: any) => state.heightCalculation;

// Export reducer
export default productSlice.reducer;
