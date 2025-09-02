import API from '@network/axios';
import * as Configs from '@network/apiConfig';

export const getProductListService = (params: any) => API.get(Configs.PRODUCT_LIST, { params });
export const toggleProductFavoriteService = (params: any) => API.get(`${Configs.PRODUCT_TOGGLE_FAVORITE}/${params.product_id}/toggle_like`);
export const getFavoriteProductService = (params: any) => API.get(Configs.PRODUCT_FAVORITE_PRODUCTS, { params });
export const fetchDetailProduct = (id: number | string) => API.get(`${Configs.PRODUCT_TOGGLE_FAVORITE}/${id}`);
export const fetchOptionProduct = (id: number | string) => API.get(`${Configs.PRODUCT_TOGGLE_FAVORITE}/${id}/options`, { params: { limit: 200, page: 1 } });
export const getProductDetailService = (params: any) => API.get(`${Configs.PRODUCT_DETAIL}/${params.product_id}`);
export const getProductOptionsService = (params: any) => API.get(`${Configs.PRODUCT_OPTIONS}/${params.product_id}/options`);
export const fetchCoupons = (id: number | string) => API.get(Configs.COUPON, { params : { limit: 100, page: 1, workspace_id: id } });
export const createContactWorkspace = (id: number | string, body: any) => API.post(`${Configs.RESTAURANT_DETAIL}/${id}/contacts`, body);
export const validateTimeSlotService = (params: any) => API.get(Configs.VALIDATE_TIME_SLOT, { params });
export const checkAvailableProductService = (params: any) => API.get(Configs.CHECK_PRODUCT_AVAILABLE, { params });
export const validateProductAvailableDeliveryService = (params: any) => API.get(Configs.VALIDATE_PRODUCT_AVAILABLE_DELIVERY, { params });
export const getSuggestionProductService = (params: any) => API.get(`${Configs.SUGGESTION_PRODUCTS}/${params.category_id}/suggestion_products`, { params });
