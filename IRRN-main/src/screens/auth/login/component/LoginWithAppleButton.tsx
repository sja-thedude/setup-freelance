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

import { AppleIcon } from '@src/assets/svg';
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
    loginWithApple,
} from '@src/network/util/authUtility';
import { LoadingActions } from '@src/redux/toolkit/actions/loadingActions';
import useThemeColors from '@src/themes/useThemeColors';
import { useTranslation } from 'react-i18next';

interface LoginWithAppleButtonProps {
    fromCart?: boolean,
    isRegister: boolean,
    callback?: Function,
}

const LoginWithAppleButton: FC<LoginWithAppleButtonProps> = ({ isRegister, callback, fromCart }) => {
    const { themeColors } = useThemeColors();
    const Dimens = useDimens();
    const { t } = useTranslation();

    const [isShowLoading, showLoading, hideLoading] = useBoolean(false);
    const [appleToken, setAppleToken] = useState<string | null>();

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

    const handleAppleLogin = useCallback(async () => {
        try {
            showLoading();
            const { identityToken } = await loginWithApple();
            setAppleToken(identityToken);
        } catch (error: any) {
            //
        } finally {
            hideLoading();
        }
    }, [hideLoading, showLoading]);

    useEffect(() => {
        if (appleToken) {
            loginWithSocial(
                    {
                        provider: SOCIAL_PROVIDER.APPLE,
                        access_token: appleToken
                    }
            );
        }
    }, [appleToken, loginWithSocial]);

    return (
        <TouchableComponent
            disabled={isShowLoading}
            onPress={handleAppleLogin}
            style={{ alignItems: 'center', justifyContent: 'center' }}
        >
            <AppleIcon
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

export default LoginWithAppleButton;