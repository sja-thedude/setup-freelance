import loadingSlice from '../slices/loadingSlice';

export const { reducer } = loadingSlice;

export const LoadingActions = {
    ...loadingSlice.actions,
};

export type TypesActions = typeof loadingSlice.actions;
export type TypesState = ReturnType<typeof reducer>;