import { createSlice } from "@reduxjs/toolkit";
import { couponsApi } from "@/redux/services/couponsApi";

const initialState = {
  counponData: null,
  counponIsLoading: false,
  counponError: "",
};

const dataCouponSlice = createSlice({
  name: "dataCoupon",
  initialState,
  reducers: {},
  extraReducers: (builder) => {
    builder
      .addMatcher(couponsApi.endpoints.getCoupons.matchPending, (state) => {
        state.counponIsLoading = true;
        state.counponError = "";
      })
      .addMatcher(couponsApi.endpoints.getCoupons.matchFulfilled, (state, action) => {
        state.counponIsLoading = false;
        state.counponData = action.payload;
      })
      .addMatcher(couponsApi.endpoints.getCoupons.matchRejected, (state, action) => {
        state.counponIsLoading = false;
        state.counponError = action.error?.message || "";
    });
      
  },
});

export const selectCouponData = (state :any) => state.dataCoupon.data;
export const selectApiError = (state:any) => state.color.error;
export default dataCouponSlice.reducer;
