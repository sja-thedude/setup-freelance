import * as Configs from '@network/apiConfig';
import API from '@network/axios';

export const getUserProfileService = (params: any) => API.get(Configs.PROFILE, { params });
export const updateUserProfileService = (params: any) => API.post(Configs.PROFILE, params );
export const deleteUserService = () => API.delete(Configs.DELETE_USER);
export const changeUserAvatarService = (body: any) => API.post(Configs.CHANGE_AVATAR, body, {
    headers: {
        'Content-Type': 'multipart/form-data',
    },
    transformRequest: () => body,
});
export const removeUserAvatarService = () => API.post(Configs.REMOVE_AVATAR);
export const updateUserLocaleService = (body: any) => API.post(Configs.CHANGE_LANGUAGE, body);