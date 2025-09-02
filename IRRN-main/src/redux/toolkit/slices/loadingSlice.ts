import { createSlice } from '@reduxjs/toolkit';

interface State {
    showGlobalLoading: boolean,
}

const initialState: State = {
    showGlobalLoading: false,
};

const loadingSlice = createSlice({
    name: 'loadingSlice',
    initialState,
    reducers: {
        showGlobalLoading: (state, { payload }) => {
            state.showGlobalLoading = payload;
        },
    },
});

export default loadingSlice;
