import API from '@network/axios';
import * as Configs from '@network/apiConfig';

export const getListNotificationService = (params: any) => API.get(Configs.LIST_NOTIFICATION, { params });
export const detailNotificationService = (params: any) => API.get(`${Configs.DETAIL_NOTIFICATION}/${params.notification_id}`);
export const markNotificationService = (params: any) => API.get(Configs.MARK_NOTIFICATION_AS_READ, { params });
export const registerNotificationToken = (params: any) => API.post(Configs.NOTIFICATION_REGISTER_TOKEN, params);
export const unsubscribeNotificationTokenService = (params: any) => API.post(Configs.NOTIFICATION_UNSUBSCRIBE_TOKEN, params);
