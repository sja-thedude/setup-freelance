import { createApi, fetchBaseQuery } from "@reduxjs/toolkit/query/react";
import * as config from "@/config/constants";
import queryString from 'query-string'

// Create categoriesApi
export const categoriesApi = createApi({
    reducerPath: "categoriesApi",
    refetchOnFocus: true,
    baseQuery: fetchBaseQuery({
        baseUrl: config.API_URL
    }),
    tagTypes: ['Categories'],
    endpoints: (builder) => ({
        getCategories: builder.query({
            query: ({ workspace_id }) => `categories?workspace_id=${workspace_id}`,
            providesTags: ['Categories'],
        }),
        checkAvailableCategories: builder.query<any, { ids: Array<number> }>({
            query: ({ ids }) => `categories/check_available?${queryString.stringify({'id[]': ids})}`
        }),
    }),
});

// Export actions and hooks
export const { 
    useGetCategoriesQuery,
    useCheckAvailableCategoriesQuery
} = categoriesApi;
