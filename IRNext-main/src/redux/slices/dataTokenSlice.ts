import { createSlice } from "@reduxjs/toolkit";
import { dataTokenApi } from "@/redux/services/dataTokenApi";

const initialState = {
  data: null,
  isLoading: false,
  error: "",
};

const dataTokenSlice = createSlice({
  name: "dataToken",
  initialState,
  reducers: {},
  extraReducers: (builder) => {
    builder
      .addMatcher(dataTokenApi.endpoints.getApiData.matchPending, (state) => {
        state.isLoading = true;
        state.error = "";
      })
      .addMatcher(dataTokenApi.endpoints.getApiData.matchFulfilled, (state, action) => {
        state.isLoading = false;
        state.data = action.payload;
      })
      .addMatcher(dataTokenApi.endpoints.getApiData.matchRejected, (state, action) => {
        state.isLoading = false;
        state.error = action.error?.message || "";
    });
      
  },
});

export const selectApiData = (state :any) => state.dataToken.data;
export const selectApiError = (state:any) => state.color.error;
export default dataTokenSlice.reducer;
