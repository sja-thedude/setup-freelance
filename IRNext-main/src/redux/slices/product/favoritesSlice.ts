import { createSlice } from "@reduxjs/toolkit";
import { favoritesApi } from "@/redux/services/product/favoritesApi";

const initialState = {
  dataFavorites: null,
  isLoading: false,
  error: "",
};

const favoritesSlice = createSlice({
  name: "favorites",
  initialState,
  reducers: {},
  extraReducers: (builder) => {
    builder
        .addMatcher(favoritesApi.endpoints.getApiFavorites.matchPending, (state) => {
          state.isLoading = true;
          state.error = "";
        })
        .addMatcher(favoritesApi.endpoints.getApiFavorites.matchFulfilled, (state, action) => {
          state.isLoading = false;
          state.dataFavorites = action.payload;
        })
        .addMatcher(favoritesApi.endpoints.getApiFavorites.matchRejected, (state, action) => {
          state.isLoading = false;
          state.error = action.error?.message || "";
        });

  },
});

export const selectFavorites = (state :any) => state.dataFavorites;
export const selectFavoritesError = (state:any) => state.error;

export default favoritesSlice.reducer;
