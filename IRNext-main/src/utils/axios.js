import axios from "axios"
import * as config from "@/config/constants"
import pickBy from 'lodash/pickBy'
import https from 'https'
import Cookies from "js-cookie";
import {handleLogoutToken, handleLoginToken} from "./axiosRefreshToken";

const language = Cookies.get('Next-Locale') ?? 'nl';

if (process.env.NODE_ENV === 'local' || process.env.NODE_ENV === 'development') {
    const httpsAgent = new https.Agent({
        rejectUnauthorized: false
    })

    axios.defaults.httpsAgent = httpsAgent
}

const axiosInstance = axios.create({
    baseURL: config.API_URL,
    paramsSerializer: params => JSON.stringify(params, { arrayFormat: 'repeat' }),
    timeout: 30000,
    headers: {
        'Content-Type': 'application/json',
        'Content-Language': language
    }
})

axiosInstance.interceptors.response.use(function (response) {
    // Do something with response data
    return response;
}, function (error) {
    if (error.response?.status !== 401) {
        return Promise.reject(error);
    } else {
        const token = Cookies.get('loggedToken');
        if (token) {
            axios.get('/auth/token/refresh', {
                baseURL: config.API_URL,
                paramsSerializer: params => JSON.stringify(params, {arrayFormat: 'repeat'}),
                timeout: 30000,
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Content-Type': 'application/json',
                }
            }).then((res) => {
                const userData = res.data.data;
                Cookies.remove('loggedToken');
                // Set cookie 'loggedToken' with value 'token'
                handleLoginToken(userData.token);
                window.location.reload();
            }).catch((err) => {
                const checkToken = Cookies.get('loggedToken');
                if (token == checkToken) {
                    handleLogoutToken();
                    if (window.innerWidth < 1280) {
                        window.location.href = '/user/login';
                    } else {
                        const query = new URLSearchParams(window.location.search);
                        if (query.size > 0) {
                            window.location.href = window.location.href + "&login=true";
                        } else {
                            window.location.href = window.location.href + "?login=true";
                        }
                    }
                }
            })
        }
        return Promise.reject(error);
    }
});

export const api = axiosInstance;
export function setHeaders(params) {
    const newHeaders = {
        ...axiosInstance.defaults.headers.common,
        ...params,
    };
    axiosInstance.defaults.headers.common = pickBy(newHeaders, val => !!val);
}

