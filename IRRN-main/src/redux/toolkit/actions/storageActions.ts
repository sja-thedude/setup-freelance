import storageSlice from '../slices/storageSlice';

export const { reducer } = storageSlice;

export const StorageActions = {
    ...storageSlice.actions,
};

export type TypesActions = typeof storageSlice.actions;
export type TypesState = ReturnType<typeof reducer>;