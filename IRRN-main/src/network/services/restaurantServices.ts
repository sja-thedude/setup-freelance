import API from '@network/axios';
import * as Configs from '@network/apiConfig';

export const getRestaurantNearbyService = (params: any) => API.get(Configs.RESTAURANT_NEARBY, { params });
export const getRestaurantRecentService = (params: any) => API.get(Configs.RESTAURANT_RECENT, { params });
export const getRestaurantFavoriteService = (params: any) => API.get(Configs.RESTAURANT_FAVORITE, { params });
export const getRestaurantDetailService = (params: any) => API.get(`${Configs.RESTAURANT_DETAIL}/${params.restaurant_id}`);
export const fetchSettingPreference = (id: number) => API.get(`${Configs.RESTAURANT_DETAIL}/${id}/settings/preferences`);
export const fetchSettingHolidayException = (id: number) => API.get(`${Configs.RESTAURANT_DETAIL}/${id}/settings/holiday_exceptions`);
export const getRestaurantMinDeliveryConditionService = (params: any) => API.get(`${Configs.RESTAURANT_DETAIL}/${params.restaurant_id}/settings/delivery_conditions/min`, { params: { lat: params.lat, lng: params.lng } });
export const getRestaurantOpeningHourService = (params: any) => API.get(`${Configs.RESTAURANT_DETAIL}/${params.restaurant_id}/settings/opening_hours`);
export const getListGroupService = (params: any) => API.get(Configs.LIST_GROUP, { params });
export const getDetailGroupService = (params: any) => API.get(`${Configs.GROUP_DETAIL}/${params.group_id}`);
export const getPaymentMethodService = (params: any) => API.get(`${Configs.PAYMENT_METHOD}/${params.restaurant_id}/settings/payment_methods`);
export const getRestaurantTimeSlotService = (params: any) => API.get(`${Configs.RESTAURANT_DETAIL}/${params.restaurant_id}/settings/timeslots`, { params });
export const getRestaurantTimeSlotConditionService = (params: any) => API.get(`${Configs.RESTAURANT_DETAIL}/${params.restaurant_id}/settings/timeslot_order_days`, { params });
export const getRestaurantDeliveryConditionService = (params: any) => API.get(`${Configs.RESTAURANT_DETAIL}/${params.restaurant_id}/settings/delivery_conditions`, { params });
export const getMyRedeemService = (params: any) => API.get(`${Configs.RESTAURANT_DETAIL}/${params.restaurant_id}/loyalties/my_redeem`);
export const validateRewardProductService = (params: any) => API.get(`${Configs.RESTAURANT_DETAIL}/${params.restaurant_id}/rewards/${params.reward_id}/validate_products`, { params });
export const getWorkspaceDetailByTokenService = (params: any) => API.get(`${Configs.RESTAURANT_DETAIL}/token/${params.restaurant_token}`);
export const getWorkspaceSettingService = (params: any) => API.get(`${Configs.RESTAURANT_DETAIL}/token/${params.restaurant_token}/settings`);
export const registerJobService = (params: any) => API.post(`${Configs.RESTAURANT_DETAIL}/${params.workspace_id}/jobs`, params.params);
export const getGroupAppDetailByTokenService = (params: any) => API.get(`${Configs.GROUP_APP_DETAIL}/token/${params.group_app_token}`);
export const getGroupListRestaurantService = (params: any) => API.get(`${Configs.GROUP_APP_DETAIL}/${params.group_app_id}/getRestaurantList`);
export const getWorkspaceSettingByIdService = (params: any) => API.get(`${Configs.RESTAURANT_DETAIL}/${params.restaurant_id}/settings`);
export const getWorkspaceLanguageService = (params: any) => API.get(`${Configs.RESTAURANT_DETAIL}/${params.restaurant_id}/languages`);

