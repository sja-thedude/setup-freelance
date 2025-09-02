import { api } from "@/utils/axios";
import { notFound } from 'next/navigation';
import { AxiosError } from 'axios';
import Cookies from 'js-cookie';

export async function confirmNewPassword({ token,email,password,password_confirmation  }: { token:string,email: string,password:string,password_confirmation:string }) {
    try {
        const language = Cookies.get('Next-Locale');
        // Set the headers for the request
        const headers = {
            'Content-Language': language || 'en',
            'Content-Type': 'multipart/form-data',
        };

        // Create a FormData object to send the request as "form-data"
        const formData = new FormData();
        formData.append('email', email);

        // Send a POST request with the FormData and custom headers
        const response = await api.post(`password/reset`, {
            'email': email, 'token': token, 'password': password, 'password_confirmation': password_confirmation
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
