import { createSlice } from '@reduxjs/toolkit';

const groupOrderSlice = createSlice({
  name: 'groupOrder',
  initialState: {
    data: null,
    tmpGroupId: null,
  },
  reducers: {
    setGroupOrderData: (state, action) => {
      state.data = action.payload;
    },

    /**
     * Set group_id temporary
     */
    setTmpGroupId: (state, action) => {
      state.tmpGroupId = action.payload;
    },
  },
});

export const { setGroupOrderData, setTmpGroupId } = groupOrderSlice.actions;
export default groupOrderSlice.reducer;