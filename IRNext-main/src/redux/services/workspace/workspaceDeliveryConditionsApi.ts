import { createApi, fetchBaseQuery } from "@reduxjs/toolkit/query/react";
import * as config from "@/config/constants";

export const workspaceDeliveryConditionsApi = createApi({
    reducerPath: "workspaceDeliveryConditionsApi",
    refetchOnFocus: true,
    baseQuery: fetchBaseQuery({
        baseUrl: config.API_URL
    }),
    tagTypes: ['WorkspaceDeliveryConditions'],
   endpoints: (builder) => ({
        getWorkspaceDeliveryConditionsById: builder.query<any, { id: number }>({
            query: ({ id }) => `workspaces/${id}/settings/delivery_conditions/min`,
        }),
        getWorkspaceDeliveryConditionsByIdLatLong: builder.query<any, { id: number, lat: string, lng: string }>({
            query: ({ id, lat, lng }) => `workspaces/${id}/settings/delivery_conditions?lat=${lat}&lng=${lng}`,
        }),
    }),
});

export const { 
    useGetWorkspaceDeliveryConditionsByIdQuery,
    useGetWorkspaceDeliveryConditionsByIdLatLongQuery
} = workspaceDeliveryConditionsApi;