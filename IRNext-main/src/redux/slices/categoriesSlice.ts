import { createSlice } from "@reduxjs/toolkit";
import { categoriesApi } from "@/redux/services/categoriesApi";

const initialState = {
  categoriesData: null,
  categoriesIsLoading: false,
  categoriesError: "",
};

const dataCategoriesSlice = createSlice({
  name: "dataCategories",
  initialState,
  reducers: {},
  extraReducers: (builder) => {
    builder
      .addMatcher(categoriesApi.endpoints.getCategories.matchPending, (state) => {
        state.categoriesIsLoading = true;
        state.categoriesError = "";
      })
      .addMatcher(categoriesApi.endpoints.getCategories.matchFulfilled, (state, action) => {
        state.categoriesIsLoading = false;
        state.categoriesData = action.payload;
      })
      .addMatcher(categoriesApi.endpoints.getCategories.matchRejected, (state, action) => {
        state.categoriesIsLoading = false;
        state.categoriesError = action.error?.message || "";
    });
      
  },
});

export const selectCategoriesData = (state :any) => state.categoriesData;
export const selectCategoriesError = (state:any) => state.color.error;
export default dataCategoriesSlice.reducer;
