import { createSlice } from '@reduxjs/toolkit';

const optionsStoreSlice = createSlice({
  name: 'optionsStore',
  initialState: {
    data: null, // Đối tượng dữ liệu bạn muốn lưu trữ
  },
  reducers: {
    setOptionsStoreData: (state, action) => {
      state.data = action.payload;
    },
  },
});

export const { setOptionsStoreData } = optionsStoreSlice.actions;
export default optionsStoreSlice.reducer;