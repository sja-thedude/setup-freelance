import { createSlice } from '@reduxjs/toolkit';
import { workspaceSettingApi } from '@/redux/services/workspace/workspaceSettingApi';

const initialState = {
    workspace: null,
};

// Config slice
export const workspaceSetting = createSlice({
    name: 'workspaceSetting',
    initialState,
    reducers: {
        //
    },
    extraReducers: (builder) => {
        builder.addMatcher(workspaceSettingApi.endpoints.getWorkspaceSettingById.matchFulfilled, (state, action) => {
            state.workspace = action.payload;
        })
        builder.addMatcher(workspaceSettingApi.endpoints.getWorkspaceSettingById.matchRejected, (state) => {
            state.workspace = null;
        })
    },
});

export const selectWorkspaceSetting = (state: any) => state;

// Export reducer
export default workspaceSetting.reducer;
