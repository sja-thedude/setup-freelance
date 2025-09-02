import { createSlice } from '@reduxjs/toolkit';

const flagDesktopChangeTypeSilce = createSlice({
  name: 'flagDesktopChangeType',
  initialState: {
    data: false, // Đối tượng dữ liệu bạn muốn lưu trữ
  },
  reducers: {
    setFlagDesktopChangeType: (state, action) => {
      state.data = action.payload;
    },
  },
});

export const { setFlagDesktopChangeType } = flagDesktopChangeTypeSilce.actions;
export default flagDesktopChangeTypeSilce.reducer;