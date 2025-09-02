import { api } from "@/utils/axios"; 
import { notFound } from 'next/navigation'
import  { AxiosError } from 'axios';

export async function checkAvailableCategories({ ids }: { ids: Array<number> }) {
    try {
        const response = await api.get('categories/check_available', {
            params: {
                id: ids
            }
        });
    
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