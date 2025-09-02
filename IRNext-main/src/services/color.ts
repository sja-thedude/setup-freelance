import { api } from "@/utils/axios"; 
import { notFound } from 'next/navigation'
import  { AxiosError } from 'axios';

export async function getColor({ token }: { token: string }) {
    try {
        const response = await api.get(`workspaces/token/${token}`);
    
        if (!response) {
            return notFound()
        }

        return response.data?.data?.setting_generals;
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