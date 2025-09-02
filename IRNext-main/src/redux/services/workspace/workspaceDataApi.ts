import { createApi, fetchBaseQuery } from "@reduxjs/toolkit/query/react";
import * as config from "@/config/constants";

export const workspaceDataApi = createApi({
    reducerPath: "workspaceDataApi",
    refetchOnFocus: true,
    baseQuery: fetchBaseQuery({
        baseUrl: config.API_URL
    }),
    tagTypes: ['WorkspaceData'],
    endpoints: (builder) => ({
        getWorkspaceDataById: builder.query<any, { id: number }>({
            query: ({ id }) => `workspaces/${id}`,
        }),
    }),
});

export const { useGetWorkspaceDataByIdQuery } = workspaceDataApi;