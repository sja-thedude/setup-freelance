import { createSlice } from '@reduxjs/toolkit';
import { productsCategoryListApi } from '@/redux/services/product/productsCategoryListApi';

const initialState = {
    productsCategoryList: null,
};

// Config slice
export const productsCategoryListSlice = createSlice({
    name: 'productsCategoryList',
    initialState,
    reducers: {
        //
    },
    extraReducers: (builder) => {
        builder.addMatcher(productsCategoryListApi.endpoints.getProductsCategoryList.matchFulfilled, (state, action) => {
            state.productsCategoryList = action.payload;
        })
        builder.addMatcher(productsCategoryListApi.endpoints.getProductsCategoryList.matchRejected, (state) => {
            state.productsCategoryList = null;
        })
    },
});

export const selectProductsCategoryList = (state: any) => state.productsCategoryList;

// Export reducer
export default productsCategoryListSlice.reducer;
