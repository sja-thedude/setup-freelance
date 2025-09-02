import { createSlice } from '@reduxjs/toolkit';

const flagForcusSlice = createSlice({
  name: 'flagForcus',
  initialState: {
    data: true, // Đối tượng dữ liệu bạn muốn lưu trữ
  },
  reducers: {
    setflagForcusData: (state, action) => {
      state.data = action.payload;
    },
  },
});

export const { setflagForcusData } = flagForcusSlice.actions;
export default flagForcusSlice.reducer;