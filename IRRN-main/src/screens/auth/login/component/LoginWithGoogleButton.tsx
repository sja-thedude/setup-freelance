import React, {
    FC,
    useCallback,
    useEffect,
    useState,
} from 'react';

import {
    ActivityIndicator,
    InteractionManager,
} from 'react-native';

import { GoogleIcon } from '@src/assets/svg';
import TouchableComponent from '@src/components/TouchableComponent';
import { SOCIAL_PROVIDER } from '@src/configs/constants';
import { useAppDispatch } from '@src/hooks';
import useBoolean from '@src/hooks/useBoolean';
import useCallAPI from '@src/hooks/useCallAPI';
import useDimens from '@src/hooks/useDimens';
import NavigationService from '@src/navigation/NavigationService';
import { UserDataModel } from '@src/network/dataModels';
import { loginWithSocialService } from '@src/network/services/authServices';
import {
    handleLoginSocial,
    loginWithGoogle,
} from '@src/network/util/authUtility';
import { LoadingActions } from '@src/redux/toolkit/actions/loadingActions';
import useThemeColors from '@src/themes/useThemeColors';
import { useTranslation } from 'react-i18next';

interface LoginWithGoogleButtonProps {
    fromCart?: boolean,
    isRegister: boolean,
    callback?: Function,
}

const LoginWithGoogleButton: FC<LoginWithGoogleButtonProps> = ({ isRegister, callback, fromCart }) => {
    const { themeColors } = useThemeColors();
    const Dimens = useDimens();
    const { t } = useTranslation();

    const [isShowLoading, showLoading, hideLoading] = useBoolean(false);
    const [googleToken, setGoogleToken] = useState<string>();

    const dispatch = useAppDispatch();

    const { callApi: loginWithSocial } = useCallAPI(
            loginWithSocialService,
            useCallback(() => {
                dispatch(LoadingActions.showGlobalLoading(true));
            }, [dispatch]),
            useCallback((data: UserDataModel) => {
                NavigationService.pop(isRegister ? 2 : 1);
                handleLoginSocial(data, fromCart ? true : false, t);
                if (!data?.first_login) {
                    InteractionManager.runAfterInteractions(() => {
                        callback && callback(fromCart ? { userData: data, isSocial: true } : undefined);
                    });
                }
            }, [callback, fromCart, isRegister, t]),
    );

    const handleGoogleLogin = useCallback(async () => {
        try {
            showLoading();
            const { accessToken } = await loginWithGoogle();
            setGoogleToken(accessToken);
        } catch (error: any) {
            //
        } finally {
            hideLoading();
        }
    }, [hideLoading, showLoading]);

    useEffect(() => {
        if (googleToken) {
            loginWithSocial(
                    {
                        provider: SOCIAL_PROVIDER.GOOGLE,
                        access_token: googleToken
                    }
            );
        }
    }, [googleToken, loginWithSocial]);

    return (
        <TouchableComponent
            disabled={isShowLoading}
            onPress={handleGoogleLogin}
            style={{ alignItems: 'center', justifyContent: 'center' }}
        >
            <GoogleIcon
                width={Dimens.H_52}
                height={Dimens.H_52}
            />
            {isShowLoading && (
                <ActivityIndicator
                    color={themeColors.color_loading_indicator}
                    size="small"
                    style={{ position: 'absolute', alignSelf: 'center' }}
                />
            )}
        </TouchableComponent>
    );
};

export default LoginWithGoogleButton;