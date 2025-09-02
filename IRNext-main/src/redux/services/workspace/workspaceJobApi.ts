import { createApi, fetchBaseQuery } from "@reduxjs/toolkit/query/react";
import * as config from "@/config/constants";

export const workspaceJobApi = createApi({
    reducerPath: "workspaceJobApi",
    refetchOnFocus: true,
    baseQuery: fetchBaseQuery({
        baseUrl: config.API_URL
    }),
    tagTypes: ['Jobs'],
    endpoints: (builder) => ({
        submitJob: builder.mutation({
            query: (payload) => ({
                url: `workspaces/${payload.workspace_id}/jobs`,
                method: 'POST',
                body: payload,
                headers: {
                    'Content-type': 'application/json; charset=UTF-8',
                },
            }),
            invalidatesTags: ['Jobs']
        }),
    }),
});

export const { useSubmitJobMutation } = workspaceJobApi;
