import { createSlice } from '@reduxjs/toolkit';
import { DEFAULT_HOME_ADDRESS } from '@src/configs/constants';

interface State {
    lat: number,
    lng: number,
    address: string,
}

const initialState: State = {
    lat: DEFAULT_HOME_ADDRESS.lat,
    lng: DEFAULT_HOME_ADDRESS.lng,
    address: DEFAULT_HOME_ADDRESS.address,
};

const storageSlice = createSlice({
    name: 'storage',
    initialState,
    reducers: {
        setLocation: (state, { payload }) => {
            state.lat = payload.lat;
            state.lng = payload.lng;
            state.address = payload.address;
        },
        resetLocation: () => {
            initialState;
        },
    },
});

export default storageSlice;
