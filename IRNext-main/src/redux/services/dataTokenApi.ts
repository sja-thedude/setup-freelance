import { createApi, fetchBaseQuery } from "@reduxjs/toolkit/query/react";
import * as config from "@/config/constants";

// Create dataTokenApi
export const dataTokenApi = createApi({
    reducerPath: "dataTokenApi",
    refetchOnFocus: true,
    baseQuery: fetchBaseQuery({
        baseUrl: config.API_URL
    }),
    tagTypes: ['Color'],
    endpoints: (builder) => ({
        // end poin for color
        getApiData: builder.query({
            query: (token: string) => `workspaces/token/${token}`,
            providesTags: ['Color'],
          }),
    }),
});

// Export actions and hooks
export const { useGetApiDataQuery } = dataTokenApi;
