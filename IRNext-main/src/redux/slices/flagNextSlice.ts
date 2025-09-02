import { createSlice } from '@reduxjs/toolkit';

const flagNextSlice = createSlice({
  name: 'flagNext',
  initialState: {
    data: true, // Đối tượng dữ liệu bạn muốn lưu trữ
  },
  reducers: {
    setflagNextData: (state, action) => {
      state.data = action.payload;
    },
  },
});

export const { setflagNextData } = flagNextSlice.actions;
export default flagNextSlice.reducer;