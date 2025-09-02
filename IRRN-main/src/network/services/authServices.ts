import API from '@network/axios';
import * as Configs from '@network/apiConfig';

export const loginService = (params: any) => API.post(Configs.LOGIN, params);
export const loginWithSocialService = (params: any) => API.post(Configs.LOGIN_WITH_SOCIAL, params);
export const logoutService = () => API.post(Configs.LOGOUT);
export const registerService = (params: any) => API.post(Configs.REGISTER, params);
export const loginWithTokenService = (params: any) => API.get(`${Configs.LOGIN_WITH_TOKEN}/${params.verify_token}`);
export const sendEmailResetPasswordService = (params: any) => API.post(Configs.SEND_EMAIL_RESET_PASSWORD, params);
export const resetPasswordService = (params: any) => API.post(Configs.RESET_PASSWORD, params);
export const refreshTokenService = () => API.get(Configs.REFRESH_TOKEN);
