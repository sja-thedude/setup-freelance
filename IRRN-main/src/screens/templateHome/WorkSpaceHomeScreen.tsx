import React, {
    Fragment,
    memo,
    useCallback,
    useEffect,
    useMemo,
    useState,
} from 'react';

import { useTranslation } from 'react-i18next';
import {
    AppState,
    BackHandler,
    StyleSheet,
    View,
} from 'react-native';
import Config from 'react-native-config';
import { useEffectOnce } from 'react-use';

import ButtonComponent from '@src/components/ButtonComponent';
import ImageComponent from '@src/components/ImageComponent';
import ShadowView from '@src/components/ShadowView';
import TextComponent from '@src/components/TextComponent';
import {
    useAppDispatch,
    useAppSelector,
} from '@src/hooks';
import useCallAPI from '@src/hooks/useCallAPI';
import useCheckEmptyCart from '@src/hooks/useCheckEmptyCart';
import useDimens, { DimensType } from '@src/hooks/useDimens';
import { SCREENS } from '@src/navigation/config/screenName';
import NavigationService from '@src/navigation/NavigationService';
import {
    getRestaurantDetailService,
    getWorkspaceDetailByTokenService,
    getWorkspaceSettingByIdService,
    getWorkspaceSettingService,
} from '@src/network/services/restaurantServices';
import { LoadingActions } from '@src/redux/toolkit/actions/loadingActions';
import { RestaurantActions, } from '@src/redux/toolkit/actions/restaurantActions';
import { StorageActions } from '@src/redux/toolkit/actions/storageActions';
import useThemeColors from '@src/themes/useThemeColors';
import { isTemplateApp } from '@src/utils';

import CurveShape from './component/CurveShape';
import FunctionItemList from './component/FunctionItemList';
import ImageSlideList from './component/ImageSlideList';
import TemplateHomeHeader from './component/TemplateHomeHeader';
import TooltipComponent from './component/TooltipComponent';

const WorkSpaceHomeScreen = () => {
    const Dimens = useDimens();
    const styles = stylesF(Dimens);

    const { t } = useTranslation();
    const { themeColors } = useThemeColors();
    const dispatch = useAppDispatch();

    const isEmptyCart = useCheckEmptyCart();

    const workspaceId = useAppSelector((state) => state.storageReducer.templateWorkspaceDetail?.id);
    const settingGenerals = useAppSelector((state) => state.storageReducer.templateWorkspaceDetail?.setting_generals);
    const workspacePhoto = useAppSelector((state) => state.storageReducer.templateWorkspaceDetail?.photo);

    const [currentImageIndex, setCurrentImageIndex] = useState(0);

    const { callApi: getRestaurantDetailByToken } = useCallAPI(
            getWorkspaceDetailByTokenService
    );

    const { callApi: getWorkspaceSetting } = useCallAPI(
            getWorkspaceSettingService
    );

    const { callApi: getRestaurantDetailById } = useCallAPI(
            getRestaurantDetailService,
            undefined,
            undefined,
            undefined,
            true,
            false
    );

    const { callApi: getWorkspaceSettingById } = useCallAPI(
            getWorkspaceSettingByIdService,
            undefined,
            undefined,
            undefined,
            true,
            false
    );

    const getWorkspaceData = useCallback(() => {
        let promise;

        if (isTemplateApp()) {
            promise = [
                getRestaurantDetailByToken({ restaurant_token: Config.ENV_WORKSPACE_TOKEN }),
                getWorkspaceSetting({ restaurant_token: Config.ENV_WORKSPACE_TOKEN }),
            ];
        } else {
            promise = [
                getRestaurantDetailById({ restaurant_id:workspaceId }),
                getWorkspaceSettingById({ restaurant_id: workspaceId }),
            ];
        }

        Promise.all(promise).then((result) => {
            const detailRes = result[0];
            const settingRes = result[1];
            if (detailRes.success) {
                dispatch(StorageActions.setStorageWorkspaceDetail(detailRes.data));
                dispatch(RestaurantActions.updateRestaurantDetail(detailRes.data));
            }

            if (settingRes.success) {
                dispatch(StorageActions.setStorageWorkspaceSetting(settingRes.data));
            }

            dispatch(LoadingActions.showGlobalLoading(false));
        });

        setCurrentImageIndex(0);
    }, [dispatch, getRestaurantDetailById, getRestaurantDetailByToken, getWorkspaceSetting, getWorkspaceSettingById, workspaceId]);

    useEffect(() => {
        getWorkspaceData();
    }, [getWorkspaceData]);

    useEffect(() => {
        const subscription = AppState.addEventListener('change', (nextAppState) => {
            if (nextAppState === 'active' && NavigationService.getCurrentRoute()?.name === SCREENS.WORKSPACE_HOME_SCREEN) {
                getWorkspaceData();
            }
        });

        return () => {
            subscription.remove();
        };
    }, [getWorkspaceData]);

    useEffectOnce(() => {
        const subscription = BackHandler.addEventListener('hardwareBackPress', () => true);

        return () => {
            subscription.remove();
        };
    });

    const handleNavToMenu = useCallback(() => {
        NavigationService.navigate(SCREENS.RESTAURANT_DETAIL_SCREEN);
    }, []);

    const renderFunctionList = useMemo(() => (
        <FunctionItemList/>
    ), []);

    const renderWorkspacePhoto = useMemo(() => (
        <Fragment>
            <CurveShape/>

            <View style={styles.resNameContainer}>
                <View style={{ flex: 1 }}>
                    <TextComponent style={styles.resName}>
                        {settingGenerals?.title}
                    </TextComponent>
                    <TextComponent style={styles.resAdd}>
                        {settingGenerals?.subtitle}
                    </TextComponent>
                </View>
                <ShadowView
                    style={{ shadowRadius: Dimens.H_15, shadowColor: '#00000015' }}
                >
                    <ImageComponent
                        hiddenLoading
                        resizeMode='cover'
                        source={{ uri: workspacePhoto }}
                        style={styles.resLogo}
                    />
                </ShadowView>
            </View>

        </Fragment>
    ), [Dimens.H_15, settingGenerals?.subtitle, settingGenerals?.title, styles.resAdd, styles.resLogo, styles.resName, styles.resNameContainer, workspacePhoto]);

    const renderImageSlide = useMemo(() => (
        <ImageSlideList
            setCurrentImageIndex={setCurrentImageIndex}
        />
    ), []);

    const renderHeader = useMemo(() => (
        <TemplateHomeHeader
            currentImageIndex={currentImageIndex}
            reloadWorkspaceData={getWorkspaceData}
        />
    ), [currentImageIndex, getWorkspaceData]);

    const renderStartButton = useMemo(() => (
        <ButtonComponent
            title={t(isEmptyCart ? 'template_home_start_order' : 'template_home_continue_order')}
            style={[styles.button, { backgroundColor: themeColors.workspace_home_button_background }]}
            styleTitle={[styles.buttonTitle, { color: themeColors.workspace_home_button_text }]}
            onPress={handleNavToMenu}
        />
    ), [handleNavToMenu, isEmptyCart, styles.button, styles.buttonTitle, t, themeColors.workspace_home_button_background, themeColors.workspace_home_button_text]);

    return (
        <View style={{ flex: 1 }}>
            <View>
                {renderImageSlide}
                {renderHeader}
                {renderWorkspacePhoto}
            </View>

            {renderStartButton}
            {renderFunctionList}

            <TooltipComponent/>

        </View>
    );
};

export default memo(WorkSpaceHomeScreen);

const stylesF = (Dimens: DimensType) => StyleSheet.create({
    funcList: {
        paddingHorizontal: Dimens.W_8,
        paddingTop: Dimens.H_25,
        // paddingBottom: Dimens.H_24,
    },
    resLogo: {
        width: Dimens.W_110,
        height: Dimens.W_110,
        borderRadius: Dimens.W_110,
    },
    resAdd: { fontSize: Dimens.FONT_16, fontWeight: '400', color: 'white' },
    resName: { fontSize: Dimens.FONT_24, fontWeight: '700', color: 'white' },
    resNameContainer: {
        position: 'absolute',
        bottom: Dimens.H_16,
        right: 0,
        left: 0,
        flexDirection: 'row',
        alignItems: 'center',
        paddingLeft: Dimens.H_12,
        paddingRight: Dimens.H_8,
    },
    image: { width: Dimens.SCREEN_WIDTH, height: Dimens.SCREEN_HEIGHT / 1.7 },
    button: {
        marginHorizontal: Dimens.W_24,
        marginTop: Dimens.H_26,
    },
    buttonTitle: {
        fontSize: Dimens.FONT_16,
        fontWeight: '700',
    },
});