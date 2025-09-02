import { createApi, fetchBaseQuery } from "@reduxjs/toolkit/query/react";
import * as config from "@/config/constants";
import Cookies from "js-cookie";

const tokenLoggedInCookie = Cookies.get('loggedToken');
const language = Cookies.get('Next-Locale');

// Create couponsApi
export const couponsApi = createApi({
    reducerPath: "couponsApi",
    refetchOnFocus: true,
    baseQuery: fetchBaseQuery({
        baseUrl: config.API_URL,
        prepareHeaders: (headers) => {            
            if (tokenLoggedInCookie) {
              headers.set('Authorization', `Bearer ${tokenLoggedInCookie}`);
            }
            
            headers.set('Content-Type', 'application/json');
            headers.set('Accept-Language', language || 'nl');
            
            return headers;
        }
    }),
    tagTypes: ['Coupons'],
    endpoints: (builder) => ({
        // Endpoint for getting coupons without any parameters
        getCoupons: builder.query({
            query: () => "coupons", // API endpoint URL without parameters
            providesTags: ['Coupons'],
        }),
    }),
});

// Export actions and hooks
export const { useGetCouponsQuery } = couponsApi;
