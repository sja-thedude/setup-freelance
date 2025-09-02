import { createSlice } from '@reduxjs/toolkit';

const isOnGroupDeskSlice = createSlice({
  name: 'isOnGroupDesk',
  initialState: {
    data: null, // Đối tượng dữ liệu bạn muốn lưu trữ
  },
  reducers: {
    setIsOnGroupDeskData: (state, action) => {
      state.data = action.payload;
    },
  },
});

export const { setIsOnGroupDeskData } = isOnGroupDeskSlice.actions;
export default isOnGroupDeskSlice.reducer;