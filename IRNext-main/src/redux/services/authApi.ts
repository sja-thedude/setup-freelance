import { createApi, fetchBaseQuery } from "@reduxjs/toolkit/query/react";
import * as config from "@/config/constants";
import Cookies from "js-cookie";

const language = Cookies.get('Next-Locale');

export const authApi = createApi({
    reducerPath: "userApi",
    refetchOnFocus: true,
    baseQuery: fetchBaseQuery({
        baseUrl: config.API_URL
    }),
    tagTypes: ['Auth'],
    endpoints: (builder) => ({
        login: builder.mutation({
            query: (payload) => ({
                url: 'login',
                method: 'POST',
                body: payload,
                headers: {
                    'Content-type': 'application/json; charset=UTF-8',
                    'Accept-Language': language || 'nl',
                },
            }),
            invalidatesTags: ['Auth']
        }),
    }),
});

export const { useLoginMutation } = authApi;