import {createSlice, PayloadAction} from "@reduxjs/toolkit";
import { profileApi } from "@/redux/services/profileApi";

const initialState = {
  data: null,
  isLoading: false,
  error: "",
};

const profileSlice = createSlice({
  name: "profile",
  initialState,
  reducers: {
    updateProfile: (state, action: PayloadAction<any>) => {
      state.data = action.payload
    },
  },
  extraReducers: (builder) => {
    builder
      .addMatcher(profileApi.endpoints.getApiProfile.matchPending, (state) => {
        state.isLoading = true;
        state.error = "";
      })
      .addMatcher(profileApi.endpoints.getApiProfile.matchFulfilled, (state, action) => {
        state.isLoading = false;
        state.data = action.payload;
      })
      .addMatcher(profileApi.endpoints.getApiProfile.matchRejected, (state, action) => {
        state.isLoading = false;
        state.error = action.error?.message || "";
    });
      
  },
});

export const {updateProfile} = profileSlice.actions;
export const selectApiProfileData = (state :any) => state.profile.data;
export const selectApiProfileError = (state:any) => state.profile.error;

export default profileSlice.reducer;
