import { createApi, fetchBaseQuery } from "@reduxjs/toolkit/query/react";
import * as config from "@/config/constants";
// Create categoriesApi
export const productsCategoryListApi = createApi({
    reducerPath: "productsCategoryListApi",
    refetchOnFocus: true,
    baseQuery: fetchBaseQuery({
        baseUrl: config.API_URL
    }),
    tagTypes: ['ProductsCategoryList'],
    endpoints: (builder) => ({
        getProductsCategoryList: builder.query({
          query: ({ workspace_id , loggedToken }) => 
          ({
            url: `categories/products?workspace_id=${workspace_id}&per_page=999`,
            method: 'GET',
            headers: {
                'Authorization': `Bearer ${loggedToken}`,
            },
        }),
          providesTags: ['ProductsCategoryList'],
        }),
      }),
});

// Export actions and hooks
export const { useGetProductsCategoryListQuery } = productsCategoryListApi;
