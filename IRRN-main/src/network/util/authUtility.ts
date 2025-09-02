import { TFunction } from 'i18next';
import Config from 'react-native-config';
import { getUniqueId } from 'react-native-device-info';
import {
    AccessToken,
    LoginManager,
} from 'react-native-fbsdk-next';

import AppleAuth from '@invertase/react-native-apple-authentication';
import { setHeaderContentLanguage, setHeaderToken } from '@network/axios';
import { UserDataModel } from '@network/dataModels';
import notifee from '@notifee/react-native';
import FireBaseMessaging from '@react-native-firebase/messaging';
import { GoogleSignin } from '@react-native-google-signin/google-signin';
import store from '@redux/store/index';
import Toast from '@src/components/toast/Toast';
import {
    IS_ANDROID,
    LOCALES,
} from '@src/configs/constants';
import I18nApp from '@src/languages';
import { SCREENS } from '@src/navigation/config/screenName';
import NavigationService from '@src/navigation/NavigationService';
import { LoadingActions } from '@src/redux/toolkit/actions/loadingActions';
import { StorageActions } from '@src/redux/toolkit/actions/storageActions';
import { UserDataActions } from '@src/redux/toolkit/actions/userDataActions';
import {
    isGroupApp,
    isTemplateApp,
    isTemplateOrGroupApp,
} from '@src/utils';
import { logError } from '@src/utils/logger';

import { logoutService } from '../services/authServices';
import { unsubscribeNotificationTokenService } from '../services/notificationServices';

export const handleLogin = (userData: UserDataModel) => {
    setHeaderToken(userData?.token);
    store.dispatch(StorageActions.setStorageUserData(userData));
    store.dispatch(UserDataActions.setUserData(userData));
};

export const handleLoginSocial = (userData: UserDataModel, fromCart?: boolean, t?: TFunction) => {
    handleLogin(userData);
    if (userData?.first_login && (!userData?.first_name || userData?.first_name?.includes('@') || /\d/.test(userData?.first_name) || !userData?.last_name || !userData?.gsm || !userData?.email)) {
        Toast.showToast(t ? t('message_input_all_field') : '');
        NavigationService.navigate(SCREENS.EDIT_PROFILE_SCREEN, { fromCart });
    }
};

const handleLogoutSocial = async () => {
    try {
        const [isLoginGoogle, isLoginFacebook] = await Promise.all([
            GoogleSignin.isSignedIn(),
            AccessToken.getCurrentAccessToken(),
        ]);

        if (isLoginGoogle) {
            GoogleSignin.signOut();
        }

        if (isLoginFacebook) {
            LoginManager.logOut();
        }

    } catch (error) {
        logError(error);
    }
};

export const handleLogout = async (skipUnsubscribeNotification?: boolean) => {
    if (skipUnsubscribeNotification) {
        notifee.cancelAllNotifications();
        FireBaseMessaging().deleteToken(),

        store.dispatch(StorageActions.removeStorageUserData());
        store.dispatch(StorageActions.clearStorageProcessingOrder());
        store.dispatch(UserDataActions.removeUserData());

        setHeaderToken('');
        handleLogoutSocial();

        // reset all navigation when logout
        NavigationService.reset(SCREENS.BOTTOM_TAB_SCREEN);
    } else {
        store.dispatch(LoadingActions.showGlobalLoading(true));
        const deviceId = await getUniqueId();

        let appToken: string|undefined = '';

        if (isTemplateApp()) {
            appToken = Config.ENV_WORKSPACE_TOKEN;
        }

        if (isGroupApp()) {
            appToken = Config.ENV_GROUP_TOKEN;
        }

        return await unsubscribeNotificationTokenService({
            type: IS_ANDROID ? 3 : 2,
            device_id: isTemplateOrGroupApp() ? `${deviceId}-${appToken}` : deviceId,
        }).then(async (res) => {
            if (res.data?.success) {
                return await logoutService().then((resLogout) => {
                    if (resLogout.data?.success) {

                        notifee.cancelAllNotifications();
                        FireBaseMessaging().deleteToken(),

                        store.dispatch(StorageActions.removeStorageUserData());
                        store.dispatch(StorageActions.clearStorageProcessingOrder());
                        store.dispatch(UserDataActions.removeUserData());

                        setHeaderToken('');
                        handleLogoutSocial();

                        // reset all navigation when logout
                        NavigationService.reset(SCREENS.BOTTOM_TAB_SCREEN);
                    } else {
                        resLogout?.data?.message && Toast.showToast(resLogout?.data?.message);
                    }
                }).catch((error) => {
                    Toast.showToast(error?.response?.data?.message || error?.message);
                });
            } else {
                res?.data?.message && Toast.showToast(res?.data?.message);
            }
        }).catch((error) => {
            Toast.showToast(error?.response?.data?.message || error?.message);
        }).finally(() => {
            store.dispatch(LoadingActions.showGlobalLoading(false));
        });
    }

};

export const updateUserData = (newData: UserDataModel) => {
    const storageData = store.getState().storageReducer.userData;
    store.dispatch(UserDataActions.setUserData({ ...storageData, ...newData }));
    store.dispatch(StorageActions.setStorageUserData({ ...storageData, ...newData }));

    let language = newData?.locale || LOCALES.NL;

    store.dispatch(StorageActions.setStorageLanguage(language));
    setHeaderContentLanguage(language);
    I18nApp.changeLanguage(language);

};

export const updateUserDataToken = (newToken: any) => {
    const storageData = store.getState().storageReducer.userData;
    store.dispatch(UserDataActions.setUserData({ ...storageData, token: newToken }));
    store.dispatch(StorageActions.setStorageUserData({ ...storageData, token: newToken }));
};

export async function loginWithFaceBook() {
    try {
        // Attempt login with permissions
        const result = await LoginManager.logInWithPermissions(['public_profile', 'email']);

        if (result.isCancelled) {
            return Promise.reject({ isCancelled: true });
        }

        // Once signed in, get the users AccesToken
        const data = await AccessToken.getCurrentAccessToken();

        if (!data) {
            throw 'Something went wrong when obtaining access token';
        }

        return data.accessToken;
    } catch (error) {
        logError(error);
        return Promise.reject(error);
    }
}

export async function loginWithGoogle() {
    try {
        await GoogleSignin.signIn();

        return GoogleSignin.getTokens();

    } catch (error) {
        logError(error);
        return Promise.reject(error);
    }
}

export async function loginWithApple() {
    try {
        // Start the sign-in request
        const appleAuthRequestResponse = await AppleAuth.performRequest({
            requestedOperation: AppleAuth.Operation.LOGIN,
            requestedScopes: [AppleAuth.Scope.FULL_NAME, AppleAuth.Scope.EMAIL],
        });

        // Ensure Apple returned a user identityToken
        if (!appleAuthRequestResponse.identityToken) {
            return Promise.reject();
        }

        return appleAuthRequestResponse;

    } catch (error) {
        logError(error);
        return Promise.reject(error);
    }
}