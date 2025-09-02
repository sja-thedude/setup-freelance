import { createSlice } from '@reduxjs/toolkit';
import { workspaceOpenHoursApi } from '@/redux/services/workspace/workspaceOpenHoursApi';

const initialState = {
    workspace: null,
};

// Config slice
export const workspaceOpenHours = createSlice({
    name: 'workspaceOpenHours',
    initialState,
    reducers: {
        //
    },
    extraReducers: (builder) => {
        builder.addMatcher(workspaceOpenHoursApi.endpoints.getWorkspaceOpenHoursById.matchFulfilled, (state, action) => {
            state.workspace = action.payload;
        })
        builder.addMatcher(workspaceOpenHoursApi.endpoints.getWorkspaceOpenHoursById.matchRejected, (state) => {
            state.workspace = null;
        })
    },
});

export const selectWorkspaceOpenHours = (state: any) => state;

// Export reducer
export default workspaceOpenHours.reducer;
