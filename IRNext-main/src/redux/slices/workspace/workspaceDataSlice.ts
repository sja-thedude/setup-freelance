import { createSlice, PayloadAction } from '@reduxjs/toolkit'
import { workspaceDataApi } from '@/redux/services/workspace/workspaceDataApi';

const globalWorkspaceId: any = null
const globalWorkspaceToken: any = null
const globalWorkspaceColor: any = null
const initialState = {
    workspace: null,
    globalWorkspaceId: globalWorkspaceId,
    globalWorkspaceToken: globalWorkspaceToken,
    globalWorkspaceColor: globalWorkspaceColor
};

// Config slice
export const workspaceData = createSlice({
    name: 'workspaceData',
    initialState,
    reducers: {
        changeGlobalWorkspaceId: (state, action: PayloadAction<any>) => {
            state.globalWorkspaceId = action.payload
        },
        changeGlobalWorkspaceToken: (state, action: PayloadAction<any>) => {
            state.globalWorkspaceToken = action.payload
        },
        changeGlobalWorkspaceColor: (state, action: PayloadAction<any>) => {
            state.globalWorkspaceColor = action.payload
        }
    },
    extraReducers: (builder) => {
        builder.addMatcher(workspaceDataApi.endpoints.getWorkspaceDataById.matchFulfilled, (state, action) => {
            state.workspace = action.payload;
        })
        builder.addMatcher(workspaceDataApi.endpoints.getWorkspaceDataById.matchRejected, (state) => {
            state.workspace = null;
        })
    },
});

export const selectWorkspaceData = (state: any) => state;
export const { 
    changeGlobalWorkspaceId, 
    changeGlobalWorkspaceToken,
    changeGlobalWorkspaceColor
} = workspaceData.actions

// Export reducer
export default workspaceData.reducer;
