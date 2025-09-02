import { api } from "@/utils/axios"; 
import { notFound } from 'next/navigation'

export async function getPhoto({ token }: { token: string }) {
    try {
        // use Axios to send GET request
        const response = await api.get(`workspaces/token/${token}`);
    
        if (!response) {
            return notFound()
        }

        const data = response.data;

        const photo = data.data.photo;
        if (photo) {
            return photo;
        }
    } catch (error ) {
    }
}