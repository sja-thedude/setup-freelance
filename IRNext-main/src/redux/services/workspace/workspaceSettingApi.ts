import { createApi, fetchBaseQuery } from "@reduxjs/toolkit/query/react";
import * as config from "@/config/constants";

export const workspaceSettingApi = createApi({
    reducerPath: "workspaceSettingApi",
    refetchOnFocus: true,
    baseQuery: fetchBaseQuery({
        baseUrl: config.API_URL
    }),
    tagTypes: ['WorkspaceSetting'],
   endpoints: (builder) => ({
        getWorkspaceSettingById: builder.query<any, { id: number }>({
            query: ({ id }) => `workspaces/${id}/settings`,
        }),
    }),
});

export const { useGetWorkspaceSettingByIdQuery } = workspaceSettingApi;