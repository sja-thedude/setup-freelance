// Trong slice của bạn
import { createSlice } from '@reduxjs/toolkit';

const portalAddressSlice = createSlice({
  name: 'portalAddress',
  initialState: {
    data: [] as any[], // or whatever the initial state is
  },
  reducers: {
    addToPortalAddress: (state, action) => {
      const existingItem = state.data.find(item => item.id === action.payload.id);

      if (!existingItem) {
        // if item does not exist in the array, add it
        state.data.push(action.payload);
      }
    },
  },
});

export const { addToPortalAddress } = portalAddressSlice.actions;
export default portalAddressSlice.reducer;
