import { createSlice } from '@reduxjs/toolkit';
import { workspaceJobApi } from '@/redux/services/workspace/workspaceJobApi';

const initialState = {
    workspace: null,
};

export const workspaceJobSlice = createSlice({
    name: 'workspaceJob',
    initialState,
    reducers: {},
    extraReducers: (builder) => {
        builder
            .addMatcher(workspaceJobApi.endpoints.submitJob.matchFulfilled, (state, action) => {
                state.workspace = action.payload;
            })
            .addMatcher(workspaceJobApi.endpoints.submitJob.matchRejected, (state) => {
                state.workspace = null;
            });
    },
});

export const selectWorkspaceJob = (state: any) => state.workspaceJob.workspace;

export default workspaceJobSlice.reducer;
