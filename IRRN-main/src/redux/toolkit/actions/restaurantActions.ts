import { RestaurantFavoriteItemModel } from '@src/network/dataModels/RestaurantFavoriteItemModel';
import { RestaurantNearbyItemModel } from '@src/network/dataModels/RestaurantNearbyItemModel';
import { RestaurantRecentItemModel } from '@src/network/dataModels/RestaurantRecentItemModel';
import restaurantSlice from '../slices/restaurantSlice';

export const { reducer } = restaurantSlice;

export const RestaurantActions = {
    ...restaurantSlice.actions,
};

export type TypesActions = typeof restaurantSlice.actions;
export type TypesState = ReturnType<typeof reducer>;

export const getRecentRestaurant = (showLoading: boolean, isRefreshing: boolean, params: object) => ({
    type: RestaurantActions.getRecentRestaurant.type,
    payload: {
        loading: showLoading,
        refreshing: isRefreshing,
    },
    params,
});

export const getFavoriteRestaurant = (showLoading: boolean, isRefreshing: boolean, params: object) => ({
    type: RestaurantActions.getFavoriteRestaurant.type,
    payload: {
        loading: showLoading,
        refreshing: isRefreshing,
    },
    params,
});

export const getAllRestaurantAction = (showLoading: boolean, isRefreshing: boolean, resetData: boolean, params: object) => ({
    type: RestaurantActions.getAllRestaurant.type,
    payload: {
        loading: showLoading,
        refreshing: isRefreshing,
        resetData: resetData,
    },
    params,
});

export const getRestaurantDetailAction = (showLoading: boolean, isRefreshing: boolean, resetData: boolean, params: object) => ({
    type: RestaurantActions.getRestaurantDetail.type,
    payload: {
        loading: showLoading,
        refreshing: isRefreshing,
    },
    params,
});

export const updateRestaurantDetailAction = (data: RestaurantNearbyItemModel | RestaurantFavoriteItemModel | RestaurantRecentItemModel) => ({
    type: RestaurantActions.updateRestaurantDetail.type,
    payload: data,
});