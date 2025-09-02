import locationSlice from '../slices/locationSlice';

export const { reducer } = locationSlice;

export const LocationActions = {
    ...locationSlice.actions,
};

export type TypesActions = typeof locationSlice.actions;
export type TypesState = ReturnType<typeof reducer>;