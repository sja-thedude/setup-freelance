import { api } from "@/utils/axios";
import { notFound } from 'next/navigation';
import { AxiosError } from 'axios';

export async function resetPassword({ email,  }: { email: string }) {
    try {
        // Set the headers for the request
        const headers = {
            'Content-Language': 'en',
            'Content-Type': 'multipart/form-data',
        };

        // Create a FormData object to send the request as "form-data"
        const formData = new FormData();
        formData.append('email', email);

        // Send a POST request with the FormData and custom headers
        const response = await api.post(`password/email`, {
            'email': email,
            'is_next': 1
        }, { headers });
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
