import { createApi, fetchBaseQuery } from "@reduxjs/toolkit/query/react";
import * as config from "@/config/constants";

// Create categoriesApi
export const favoritesApi = createApi({
    reducerPath: "favoritesApi",
    refetchOnFocus: true,
    baseQuery: fetchBaseQuery({
        baseUrl: config.API_URL
    }),
    tagTypes: ['Favorites'],
    endpoints: (builder) => ({
        getApiFavorites: builder.query({
            query: ({workspace_id}) => ({
                url: `products/liked?workspace_id=${workspace_id}`,
                method: 'GET',
                headers: {
                    'Authorization': `Bearer `,
                },
            }),
        }),
      }),
});

// Export actions and hooks
export const { useGetApiFavoritesQuery } = favoritesApi;
