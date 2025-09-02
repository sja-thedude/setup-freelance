import { createSlice } from '@reduxjs/toolkit';
import { Constants } from '@src/configs';
import { LARGE_PAGE_SIZE } from '@src/configs/constants';
import { RestaurantDetailModel } from '@src/network/dataModels/RestaurantDetailModel';
import { RestaurantFavoriteItemModel, } from '@src/network/dataModels/RestaurantFavoriteItemModel';
import { RestaurantNearbyItemModel } from '@src/network/dataModels/RestaurantNearbyItemModel';
import { RestaurantRecentItemModel, } from '@src/network/dataModels/RestaurantRecentItemModel';

interface State {
    recentRestaurant: {
        loading: boolean,
        refreshing: boolean,
        canLoadMore: boolean,
        data: Array<RestaurantRecentItemModel>
    },
    favoriteRestaurant: {
        loading: boolean,
        refreshing: boolean,
        canLoadMore: boolean,
        data: Array<RestaurantFavoriteItemModel>
    },
    allRestaurant: {
        loading: boolean,
        refreshing: boolean,
        canLoadMore: boolean,
        data: Array<RestaurantNearbyItemModel>
    },
    restaurantDetail: {
        loading: boolean,
        refreshing: boolean,
        data: RestaurantNearbyItemModel | RestaurantFavoriteItemModel | RestaurantRecentItemModel | RestaurantDetailModel
    },
    screenToQuit: number | null,
    showTooltip: boolean,
}

const initialState: State = {
    recentRestaurant: {
        loading: false,
        refreshing: false,
        canLoadMore: false,
        data: [],
    },
    favoriteRestaurant: {
        loading: false,
        refreshing: false,
        canLoadMore: false,
        data: []
    },
    allRestaurant: {
        loading: false,
        refreshing: false,
        canLoadMore: false,
        data: []
    },
    restaurantDetail: {
        loading: false,
        refreshing: false,
        data: <RestaurantNearbyItemModel | RestaurantFavoriteItemModel | RestaurantRecentItemModel | RestaurantDetailModel>{}
    },
    screenToQuit: null,
    showTooltip: true,
};

const restaurantSlice = createSlice({
    name: 'restaurantSlice',
    initialState,
    reducers: {
        getRecentRestaurant: (state, { payload }) => {
            const { refreshing } = payload;
            state.recentRestaurant.loading = true;
            state.recentRestaurant.refreshing = refreshing;
        },
        getRecentRestaurantSuccess: (state, { payload }) => {
            const { data } = payload;
            state.recentRestaurant.loading = false;
            state.recentRestaurant.refreshing = false;
            state.recentRestaurant.data = data.current_page === 1 ? data.data : [...state.recentRestaurant.data].concat(data.data);
            state.recentRestaurant.canLoadMore = data.data.length === Constants.PAGE_SIZE;
        },
        getRecentRestaurantFail: (state) => {
            state.recentRestaurant.loading = false;
            state.recentRestaurant.refreshing = false;
            state.recentRestaurant.canLoadMore = false;
        },
        clearRecentRestaurant: (state) => {
            state.recentRestaurant = initialState.recentRestaurant;
        },

        getFavoriteRestaurant: (state, { payload }) => {
            const { refreshing } = payload;
            state.favoriteRestaurant.loading = true;
            state.favoriteRestaurant.refreshing = refreshing;
        },
        getFavoriteRestaurantSuccess: (state, { payload }) => {
            const { data } = payload;
            state.favoriteRestaurant.loading = false;
            state.favoriteRestaurant.refreshing = false;
            state.favoriteRestaurant.data = data.current_page === 1 ? data.data : [...state.favoriteRestaurant.data].concat(data.data);
            state.favoriteRestaurant.canLoadMore = data.data.length === Constants.PAGE_SIZE;
        },
        getFavoriteRestaurantFail: (state) => {
            state.favoriteRestaurant.loading = false;
            state.favoriteRestaurant.refreshing = false;
            state.favoriteRestaurant.canLoadMore = false;
        },
        clearFavoriteRestaurant: (state) => {
            state.favoriteRestaurant = initialState.favoriteRestaurant;
        },

        getAllRestaurant: (state, { payload }) => {
            const { refreshing, resetData } = payload;
            state.allRestaurant.loading = true;
            state.allRestaurant.refreshing = refreshing;
            state.allRestaurant.data = resetData ? [] : state.allRestaurant.data;
        },
        getAllRestaurantSuccess: (state, { payload }) => {
            const { data } = payload;
            state.allRestaurant.loading = false;
            state.allRestaurant.refreshing = false;
            state.allRestaurant.data = data.current_page === 1 ? data.data : [...state.allRestaurant.data].concat(data.data);
            state.allRestaurant.canLoadMore = data.data.length === LARGE_PAGE_SIZE;
        },
        getAllRestaurantFail: (state) => {
            state.allRestaurant.loading = false;
            state.allRestaurant.refreshing = false;
            state.allRestaurant.canLoadMore = false;
        },
        clearAllRestaurant: (state) => {
            state.allRestaurant = initialState.allRestaurant;
        },

        getRestaurantDetail: (state, { payload }) => {
            const { refreshing } = payload;
            state.restaurantDetail.loading = true;
            state.restaurantDetail.refreshing = refreshing;
        },
        getRestaurantDetailSuccess: (state, { payload }) => {
            const { data } = payload;
            state.restaurantDetail.loading = false;
            state.restaurantDetail.refreshing = false;
            state.restaurantDetail.data = data.data;
        },
        getRestaurantDetailFail: (state) => {
            state.restaurantDetail.loading = false;
            state.restaurantDetail.refreshing = false;
        },
        updateRestaurantDetail: (state, { payload }) => {
            state.restaurantDetail.data = payload;
        },
        setExitScreen: (state, { payload }) => {
            state.screenToQuit = payload;
        },
        setShowTooltip: (state, { payload }) => {
            state.showTooltip = payload;
        },
        clearRestaurantDetail: (state) => {
            state.restaurantDetail = initialState.restaurantDetail;
        },
    },
});

export default restaurantSlice;
