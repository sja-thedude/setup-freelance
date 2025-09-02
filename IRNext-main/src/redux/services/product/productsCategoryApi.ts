import { createApi, fetchBaseQuery } from "@reduxjs/toolkit/query/react";
import * as config from "@/config/constants";

export const productsCategoryApi = createApi({
    reducerPath: "productsCategoryApi",
    refetchOnFocus: true,
    baseQuery: fetchBaseQuery({
        baseUrl: config.API_URL
    }),
    tagTypes: ['ProductsCategory'],
    endpoints: (builder) => ({
        getProductsCategoryById: builder.query<any, { id: number }>({
            query: ({ id }) => `categories/${id}/suggestion_products`,
        }),
        getProductsCategoryOptionsById: builder.query<any, { id: number }>({
            query: ({ id }) => `products/${id}/suggestion_products?limit=100&page=1&order_by=name&sort_by=asc`,
        }),
    }),
});

export const { useGetProductsCategoryByIdQuery, useGetProductsCategoryOptionsByIdQuery } = productsCategoryApi;