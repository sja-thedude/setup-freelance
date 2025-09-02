import { api } from "@/utils/axios";
import { notFound } from 'next/navigation';
import { AxiosError } from 'axios';

export async function OrderDetail({ id,token }: { id: number ,token:string |undefined}) {
    try {
        const headers = {
            'Content-Language': 'en',
            'Authorization': 'Bearer'+' '+ token,
        };
        // Send a GET request with the custom headers
        const response = await api.get(`orders/${id}`, { headers });
        if (!response) {
            return notFound();
        }
        const data = response.data;
        if (data) {
            return data;
        }
    } catch (error) {
        const err = error as AxiosError;
        const response_err = err.response;
        return response_err;
    }
}
