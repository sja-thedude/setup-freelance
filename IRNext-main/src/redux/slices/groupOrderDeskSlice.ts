import { createSlice } from '@reduxjs/toolkit';

const groupOrderDeskSlice = createSlice({
  name: 'groupOrderDesk',
  initialState: {
    data: null, // Đối tượng dữ liệu bạn muốn lưu trữ
  },
  reducers: {
    setGroupOrderDeskData: (state, action) => {
      state.data = action.payload;
    },
  },
});

export const { setGroupOrderDeskData } = groupOrderDeskSlice.actions;
export default groupOrderDeskSlice.reducer;