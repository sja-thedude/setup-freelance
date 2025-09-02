import { createApi, fetchBaseQuery } from "@reduxjs/toolkit/query/react";
import * as config from "@/config/constants";

// Create dataTokenApi
export const profileApi = createApi({
    reducerPath: "profileApi",
    refetchOnFocus: true,
    baseQuery: fetchBaseQuery({
        baseUrl: config.API_URL
    }),
    tagTypes: ['Profile'],
    endpoints: (builder) => ({
        getApiProfile: builder.query({
            query: (token: string) => ({
                url: 'profile',
                method: 'GET',
                headers: {
                    'Authorization': `Bearer ${token}`,
                },
            }),
        })
    }),
});

// Export actions and hooks
export const { useGetApiProfileQuery } = profileApi;
