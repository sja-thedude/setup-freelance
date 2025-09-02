import { createApi, fetchBaseQuery } from "@reduxjs/toolkit/query/react";
import * as config from "@/config/constants";

export const workspaceOpenHoursApi = createApi({
    reducerPath: "workspaceOpenHoursApi",
    refetchOnFocus: true,
    baseQuery: fetchBaseQuery({
        baseUrl: config.API_URL
    }),
    tagTypes: ['WorkspaceOpenHours'],
   endpoints: (builder) => ({
        getWorkspaceOpenHoursById: builder.query<any, { id: number ,lang: any }>({
            query: ({ id , lang }) => ({
                url: `workspaces/${id}/settings/opening_hours`,
                method: 'GET',
                headers: {
                    'Content-Language': lang,
                },
            }),
        }),
    }),
});

export const { useGetWorkspaceOpenHoursByIdQuery } = workspaceOpenHoursApi;