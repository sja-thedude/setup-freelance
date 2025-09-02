import { api } from "@/utils/axios"; 
import { notFound } from 'next/navigation'
import  { AxiosError } from 'axios';
import { toNumber } from "lodash";

export const serviceCostSetting = async (workspaceId: number) => {
    try {    
        return await api.get(`workspaces/${workspaceId}/settings/service-cost`);
    } catch (error ) {
        const err = error as AxiosError

         if (err instanceof Error) {
            if (err.response && err.response.status === 404) {
                return notFound()
            } else {
                return null;
            }
        }
    }
}

export const calculateServiceCost = (setting: any, price: number) => {
    let cost = 0;

    if(setting && (setting?.service_cost_always_charge || price < setting?.service_cost_amount)) {
        cost = toNumber(setting?.service_cost);
    }

    return cost;
}