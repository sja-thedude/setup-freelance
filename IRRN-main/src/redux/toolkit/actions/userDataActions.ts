import userDataSlice from '../slices/userDataSlice';

export const { reducer } = userDataSlice;

export const UserDataActions = {
    ...userDataSlice.actions,
};

export type TypesActions = typeof userDataSlice.actions;
export type TypesState = ReturnType<typeof reducer>;