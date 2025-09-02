// Trong slice của bạn
import { createSlice } from '@reduxjs/toolkit';

const productFavSlice = createSlice({
  name: 'productFav',
  initialState: {
    data: [] as any[], // or whatever the initial state is
  },
  reducers: {
    addToFavorites: (state, action) => {
      const existingItem = state.data.find(item => item.id === action.payload.id);

      if (!existingItem) {
        // if item does not exist in the array, add it
        state.data.push(action.payload);
      } else {
        // if item exists, update it
        Object.assign(existingItem, action.payload);
      }
    },
  },
});

export const { addToFavorites } = productFavSlice.actions;
export default productFavSlice.reducer;
