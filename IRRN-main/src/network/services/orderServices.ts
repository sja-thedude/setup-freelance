import API from '@network/axios';
import * as Configs from '@network/apiConfig';

export const getOrderHistoryService = (params: any) => API.get(Configs.ORDER_HISTORY, { params });
export const getOrderDetailService = (params: any) => API.get(`${Configs.ORDER_DETAIL}/${params.order_id}`);
export const createOrderService = (params: any) => API.post(Configs.CREATE_ORDER, params);
export const getMolliePaymentLinkService = (params: any) => API.get(Configs.MOLLIE, { params });
export const cancelOrderService = (params: any) => API.put(`${Configs.CANCEL_ORDER}/${params.order_id}/cancel`);
export const updatePaymentService = (params: any) => API.put(`${Configs.UPDATE_ORDER_PAYMENT}/${params.order_id}/update_payment`, params);
