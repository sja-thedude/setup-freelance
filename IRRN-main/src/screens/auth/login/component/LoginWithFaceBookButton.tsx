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

import { FaceBookIcon } from '@src/assets/svg';
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
    loginWithFaceBook,
} from '@src/network/util/authUtility';
import { LoadingActions } from '@src/redux/toolkit/actions/loadingActions';
import useThemeColors from '@src/themes/useThemeColors';
import { useTranslation } from 'react-i18next';

interface LoginWithFaceBookButtonProps {
    fromCart?: boolean,
    isRegister: boolean,
    callback?: Function,
}

const LoginWithFaceBookButton: FC<LoginWithFaceBookButtonProps> = ({ isRegister, callback, fromCart }) => {
    const { themeColors } = useThemeColors();
    const Dimens = useDimens();
    const { t } = useTranslation();

    const [isShowLoading, showLoading, hideLoading] = useBoolean(false);
    const [facebookToken, setFacebookToken] = useState<string>();

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

    const handleFaceBookLogin = useCallback(async () => {
        try {
            showLoading();
            const token = await loginWithFaceBook();
            setFacebookToken(token);
        } catch (error: any) {
            //
        } finally {
            hideLoading();
        }
    }, [hideLoading, showLoading]);

    useEffect(() => {
        if (facebookToken) {
            loginWithSocial(
                    {
                        provider: SOCIAL_PROVIDER.FACEBOOK,
                        access_token: facebookToken
                    }
            );
        }
    }, [facebookToken, loginWithSocial]);

    return (
        <TouchableComponent
            disabled={isShowLoading}
            onPress={handleFaceBookLogin}
            style={{ alignItems: 'center', justifyContent: 'center' }}
        >
            <FaceBookIcon
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

export default LoginWithFaceBookButton;