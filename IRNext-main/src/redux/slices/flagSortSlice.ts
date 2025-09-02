import { createSlice } from '@reduxjs/toolkit';

const flagSortSlice = createSlice({
  name: 'flagSort',
  initialState: {
    data: true, // Đối tượng dữ liệu bạn muốn lưu trữ
  },
  reducers: {
    setflagSortData: (state, action) => {
      state.data = action.payload;
    },
  },
});

export const { setflagSortData } = flagSortSlice.actions;
export default flagSortSlice.reducer;