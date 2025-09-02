import axios, {
    InternalAxiosRequestConfig,
    ParamsSerializerOptions,
} from 'axios';
import {
    parse,
    stringify,
} from 'qs';
import Config from 'react-native-config';

import {
    API_TIMEOUT,
    API_URL,
    CHECK_TOKEN_EXPIRED_WHITE_LIST,
    REFRESH_TOKEN,
} from '@network/apiConfig';
import {
    handleLogout,
    updateUserDataToken,
} from '@network/util/authUtility';
import { SCREENS } from '@src/navigation/config/screenName';
import NavigationService from '@src/navigation/NavigationService';
import {
    isGroupApp,
    isTemplateApp,
} from '@src/utils';
import { log } from '@utils/logger';

import { refreshTokenService } from './services/authServices';

export function setHeaders(params: object): void {
    const newHeaders = {
        ...axiosInstance.defaults.headers.common,
        ...params,
    };
    axiosInstance.defaults.headers.common = newHeaders;
}

export const setHeaderToken = (accessToken: any) => {
    setHeaders({ Authorization: `Bearer ${accessToken}` });
};

export const setHeaderContentLanguage = (language?: string) => {
    setHeaders({ 'Content-Language': language });
};

let isTokenRefreshing = false;
let requestQueue = <any>[];

function subscribeTokenRefresh(cb: any) {
    requestQueue.push(cb);
}

function onTokenRefreshed(token: string) {
    requestQueue.map((cb: any) => cb(token));
}

const axiosInstance = axios.create({
    baseURL: API_URL,
    paramsSerializer: {
        encode: (params: string) => parse(params),
        serialize: (params: Record<string, any>, _options?: ParamsSerializerOptions) => stringify(params),
    },
    timeout: API_TIMEOUT,
    headers: {
        'Content-Type': 'application/json',
        // 'Content-Language': DEFAULT_LANGUAGE,
        'cache-control': 'no-cache',
        'Timestamp': `${Math.floor(Date.now() / 1000)}`,
        'Timezone': `${Intl.DateTimeFormat().resolvedOptions().timeZone}`,
        'Group-Token': isGroupApp() ? Config.ENV_GROUP_TOKEN :  undefined, // group app
        'App-Token': isTemplateApp() ? Config.ENV_WORKSPACE_TOKEN : undefined, // template app
    },
});

axiosInstance.interceptors.request.use(
        function(config: InternalAxiosRequestConfig) {
            return config;
        },
        function(error) {
            return Promise.reject(error);
        },
);

axiosInstance.interceptors.response.use(
        function(response) {
            log('api: ', `[${response.config?.method}] ${response.config?.baseURL}${response.config?.url}`);
            log('data: ', response.config?.data);
            log('params: ', response.config?.params);
            log('response: ', response);
            log('---------------------------');

            return response;
        },
        async function(error) {
            log('api: ', `[${error.config?.method}] ${error.config?.baseURL}${error.config?.url}`);
            log('data: ', error.config?.data);
            log('params: ', error.config?.params);
            log('error: ', error);
            log('---------------------------');

            if (error?.response?.status === 401) {
                const apiURL = error.config?.url;

                if (!CHECK_TOKEN_EXPIRED_WHITE_LIST.includes(apiURL)) {
                    if (apiURL === REFRESH_TOKEN) {
                        // logout user when token refreshing expired
                        handleLogout(true).then(() => {
                            setTimeout(() => {
                                NavigationService.navigate(SCREENS.LOGIN_SCREEN);
                            }, 1000);
                        });
                    } else {
                        if (!isTokenRefreshing) {
                            isTokenRefreshing = true;
                            // refresh token when token expired
                            refreshTokenService().then((res) => {
                                if (res.status === 200) {
                                    const newToken = res?.data?.data?.token;

                                    setHeaderToken(newToken);
                                    updateUserDataToken(newToken);

                                    // resolve pending request
                                    // setTimeout(() => {
                                    onTokenRefreshed(newToken);
                                    requestQueue = [];
                                    isTokenRefreshing = false;
                                    // }, 500);

                                }
                            });
                        }

                        // add pending request to queue
                        return new Promise((resolve) => {
                            subscribeTokenRefresh((token: string) => {
                                const originRequest = error.config;
                                originRequest.headers.Authorization = `Bearer ${token}`;
                                resolve(axiosInstance(originRequest));
                            });
                        });
                    }

                    return Promise.reject(null);
                }
            }

            return Promise.reject(error);
        },
);

export default axiosInstance;