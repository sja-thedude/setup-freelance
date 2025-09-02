import { createSlice, PayloadAction } from '@reduxjs/toolkit'
import { authApi } from '@/redux/services/authApi';
import * as configLocales from "@/config/locales";
import Cookies from "js-cookie";
import { LOCALE_FALLBACK } from '@/config/locales';

const initialState = {
    isLogged: false,
    currentUser: null,
    globalLocale: Cookies.get('Next-Locale') ?? configLocales.LOCALE_FALLBACK,
    activeLanguages: [LOCALE_FALLBACK],
};

// Config slice
export const authSlice = createSlice({
    name: 'auth',
    initialState,
    reducers: {
        logout: (state) => {
            state.isLogged = false;
            state.currentUser = null;
        },
        changeGlobalLocale: (state, action: PayloadAction<any>) => {
            state.globalLocale = action.payload
        },
        changeActiveLanguages: (state, action: PayloadAction<any>) => {
            state.activeLanguages = action.payload
        },
    },
    extraReducers: (builder) => {
        builder.addMatcher(authApi.endpoints.login.matchPending, (state) => {
            // todo
        })
        builder.addMatcher(authApi.endpoints.login.matchFulfilled, (state, action) => {
            state.isLogged = true;
            state.currentUser = action.payload;
        })
        builder.addMatcher(authApi.endpoints.login.matchRejected, (state) => {
            state.isLogged = true;
            state.currentUser = null;
        })
    },
});

// Export actions
export const { 
    logout,
    changeGlobalLocale,
    changeActiveLanguages,
} = authSlice.actions;

// Select state currentUser from slice
export const selectUser = (state: any) => state.user.currentUser;
export const selectGlobalLocale = (state: any) => state.user.globalLocale;
export const selectActiveLanguages = (state: any) => state.user.changeActiveLanguages;

// Export reducer
export default authSlice.reducer;
