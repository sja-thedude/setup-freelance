import { createApi, fetchBaseQuery } from "@reduxjs/toolkit/query/react";
import * as config from "@/config/constants";
import Cookies from "js-cookie";
import queryString from 'query-string'

const tokenLoggedInCookie = Cookies.get('loggedToken');
const language = Cookies.get('Next-Locale');
const headers = {
    'Authorization': `Bearer ${tokenLoggedInCookie}`,
    'Content-Language': language
};

export const productApi = createApi({
    reducerPath: "productApi",
    refetchOnFocus: true,
    baseQuery: fetchBaseQuery({
        baseUrl: config.API_URL
    }),
    tagTypes: ['Product'],
    endpoints: (builder) => ({
        getProductById: builder.query<any, { id: number }>({
            query: ({ id }) => ({
                url: `products/${id}`,
                method: 'GET',
                headers: headers
            })
        }),
        getProductOptionsById: builder.query<any, { id: number }>({
            query: ({ id }) => ({
                url: `products/${id}/options?limit=100&page=1`,
                method: 'GET',
                headers: headers
            })
        }),
        checkAvailableProducts: builder.query<any, { ids: Array<number> }>({
            query: ({ ids }) => `products/check_available?${queryString.stringify({'id[]': ids})}`
        }),
    }),
});

export const { 
    useGetProductByIdQuery,
    useGetProductOptionsByIdQuery,
    useCheckAvailableProductsQuery
} = productApi;