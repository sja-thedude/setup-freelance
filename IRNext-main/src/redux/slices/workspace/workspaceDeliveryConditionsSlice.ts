import { createSlice } from '@reduxjs/toolkit';
import { workspaceDeliveryConditionsApi } from '@/redux/services/workspace/workspaceDeliveryConditionsApi';

const initialState = {
    workspace: null,
};

// Config slice
export const workspaceDeliveryConditions = createSlice({
    name: 'workspaceDeliveryConditions',
    initialState,
    reducers: {
        //
    },
    extraReducers: (builder) => {
        builder.addMatcher(workspaceDeliveryConditionsApi.endpoints.getWorkspaceDeliveryConditionsById.matchFulfilled, (state, action) => {
            state.workspace = action.payload;
        })
        builder.addMatcher(workspaceDeliveryConditionsApi.endpoints.getWorkspaceDeliveryConditionsById.matchRejected, (state) => {
            state.workspace = null;
        })
    },
});

export const selectWorkspaceDeliveryConditions = (state: any) => state;

// Export reducer
export default workspaceDeliveryConditions.reducer;
