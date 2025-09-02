import { api } from "@/utils/axios"; 
import { notFound } from 'next/navigation'
import  { AxiosError } from 'axios';

export async function getWorkspaceByToken({ token }: { token: string }) {
    try {
        const response = await api.get(`workspaces/token/${token}`);
    
        if (!response) {
            return notFound()
        }

        return response?.data?.data
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

export async function getWorkspaceById({ id }: { id: any }) {
    try {
        const response = await api.get(`workspaces/${id}`);
    
        if (!response) {
            return notFound()
        }

        return response?.data?.data
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

export async function checkOrderTypeActive(workspaceId: any, orderDetail: any, tokenLoggedInCookie: any) {
    try {
        if (workspaceId) {
            const orderType = orderDetail?.type;
            const isGroupOrder = orderDetail?.group_id;
            const res = await api.get(`workspaces/` + workspaceId, {
                headers: {
                    'Authorization': `Bearer ${tokenLoggedInCookie}`,
                }
            });
    
            const json = res.data;
            let flagCheckTime = true;        
    
            if(isGroupOrder) {
                // validate in manager extra setting
                json.data?.extras.map((item: any) => {
                    if (item?.type == 1) {
                        if (item.active != true) {
                            flagCheckTime = false;
                        }
                    }
                });
            } else {
                // validate in manager opening hours setting
                json.data?.setting_open_hours.map((item: any) => {
                    if (item?.type == orderType) {
                        if (item.active != true) {
                            flagCheckTime = false;
                        }
                    }
                });
            }            
    
            return flagCheckTime;
        } else {
            return false;
        }    
    } catch (error ) {
        return false;
    }
}