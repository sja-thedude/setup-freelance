import { createSlice } from '@reduxjs/toolkit';
import { UserDataModel } from '@src/network/dataModels';

interface State {
    userData: UserDataModel | null
}

const initialState: State = {
    userData: null
};

const userDataSlice = createSlice({
    name: 'userDataSlice',
    initialState,
    reducers: {
        setUserData: (state, { payload }) => {
            state.userData = payload;
        },
        removeUserData: (state) => {
            state.userData = null;
        },
    },
});

export default userDataSlice;
