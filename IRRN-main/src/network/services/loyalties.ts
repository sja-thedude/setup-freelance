import * as Configs from '@network/apiConfig';
/* eslint-disable arrow-parens */
import API from '@network/axios';
import { Loyalty } from '@network/dataModels/LoyaltyModal';

import {
    ResponseCommon,
    ResponseList,
} from '@network/dataModels';

export const fetchListLoyalties = (params: {
    limit?: string | number;
    page?: number | string;
}) =>
    API.get<ResponseCommon<ResponseList<Loyalty>>>(Configs.LIST_LOYALTY, {
        params,
    }).then(res => {
        if (res?.data.success) {
            return res?.data?.data;
        }

        return Promise.reject(res?.data);
    });

export const redeemLoyalty = (id: number | string, rewardId: number | string) =>
    API.post<ResponseCommon<Loyalty>>(
            `${Configs.LIST_LOYALTY}/${id}/redeem/${rewardId}`,
    ).then(res => {
        if (res?.data.success) {
            return res?.data?.data;
        }

        return Promise.reject(res?.data);
    });

export const getDetailLoyaltyService = (params: any) => API.get(`${Configs.LIST_LOYALTY}/${params.loyalty_id}`);

export const getTemplateDetailLoyaltyService = (params: any) => API.get(`${Configs.RESTAURANT_DETAIL}/${params.workspace_id}/loyalties/of_workspace`);