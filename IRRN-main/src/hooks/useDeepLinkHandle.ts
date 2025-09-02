import { useEffect } from 'react';

import { Linking } from 'react-native';

import { log, logError } from '@src/utils/logger';
import NavigationService from '@src/navigation/NavigationService';
import { SCREENS } from '@src/navigation/config/screenName';

// itsready://itsready.be/?screen=registered_confirmation_failed&redirect_url=https%3A%2F%2Fdefault.itsready.vitex.asia%2Fnl%2Flogin
// itsready://itsready.be/?screen=registered_confirmation&token=eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9
// itsready://itsready.be/?screen=reset_password&token=0647c0a911d9770cf2cdcd774fc4761ca3076829fc49d4c8a0f31c96ca6705f6&email=tam7%40yopmail.com
// itsready://itsready.be/?screen=payment_success&order_id=3582

const handleScreen = (params: any) => {
    try {
        const { screen, token, email } = params;

        if (screen === 'registered_confirmation_failed' || screen === 'reset_password_failed') {
            NavigationService.navigate(SCREENS.DEEP_LINK_ERROR_SCREEN);
            return;
        }

        if (screen === 'registered_confirmation') {
            NavigationService.navigate(SCREENS.CONFIRM_REGISTER_SCREEN, { token: token });
            return;
        }

        if (screen === 'reset_password') {
            NavigationService.navigate(SCREENS.RESET_PASSWORD_SCREEN, { token: token, email: decodeURIComponent(email) });
            return;
        }
    } catch (error) {
        logError('handleScreenError', error);
    }
};

const parserDeepLink = (url: any) => {
    if (url) {
        try {
            const startPos = url.indexOf('?');
            if (startPos > 0) {
                const queryString = url.substring(startPos + 1);
                const queryArray = queryString.split('&');

                const paramsArray = queryArray.map((item: any) => {
                    const split = item.split('=');
                    return {
                        [split[0]]: split[1],
                    };
                });

                const paramsObject = paramsArray.reduce((obj: any, item: any) => ({ ...obj, ...item }) ,{});

                handleScreen(paramsObject);
            }
        } catch (error) {
            logError('parserDeepLinkError', error);
        }
    }
};

const getInitUrl = async () => {
    const url = await Linking.getInitialURL();
    parserDeepLink(url);
    log('getInitUrl', url);
};

const useDeepLinkHandle = () => {
    useEffect(() => {

        // linking open app from close state
        getInitUrl();

        // linking open app from background state
        const linkingListener =  Linking.addEventListener('url', (url) => {
            parserDeepLink(url?.url);
            log('DeepLink', url?.url);
        });

        return () => {
            linkingListener.remove();
        };

    }, []);
};

export default useDeepLinkHandle;