import React, {
    FC,
    memo,
    useCallback,
    useMemo,
} from 'react';

import {
    Share,
    StyleSheet,
    View,
} from 'react-native';
import Config from 'react-native-config';

import { useLayout } from '@react-native-community/hooks';
import {
    AccountIcon,
    BagIcon,
    BookCloseIcon,
    BookOpenIcon,
    BowIcon,
    ClockHistoryIcon2,
    CupIcon,
    HeartIcon2,
    MapIcon,
    ShareIcon,
} from '@src/assets/svg';
import ShadowView from '@src/components/ShadowView';
import TextComponent from '@src/components/TextComponent';
import TouchableComponent from '@src/components/TouchableComponent';
import {
    DEFAULT_FUNC_KEY,
    IS_ANDROID,
} from '@src/configs/constants';
import { useAppSelector } from '@src/hooks';
import useDimens, { DimensType } from '@src/hooks/useDimens';
import useIsUserLoggedIn from '@src/hooks/useIsUserLoggedIn';
import { SCREENS } from '@src/navigation/config/screenName';
import NavigationService from '@src/navigation/NavigationService';
import { Meta } from '@src/network/dataModels/WorkspaceSettingModel';
import useThemeColors from '@src/themes/useThemeColors';
import { openInAppBrowser, openMap } from '@src/utils';
import { getBottomSpace } from '@src/utils/iPhoneXHelper';

interface IProps {
    item: Meta,
    handleNavToMenu: Function,
}

const FuncItem: FC<IProps> = ({ item, handleNavToMenu }) => {
    const Dimens = useDimens();
    const styles = stylesF(Dimens);

    const { themeColors } = useThemeColors();
    const isUserLoggedIn = useIsUserLoggedIn();
    const { onLayout: onContainerLayout, height: containerHeight } = useLayout();
    const { onLayout, width: titleWidth, height: titleHeight } = useLayout();
    const { onLayout: onIconLayout, height: iconHeight } = useLayout();

    const workspaceDetail = useAppSelector((state) => state.storageReducer.templateWorkspaceDetail);

    const descTextHeight = useMemo(() => containerHeight - titleHeight - iconHeight - Dimens.W_12 - Dimens.H_6 - Dimens.H_8, [Dimens.H_6, Dimens.H_8, Dimens.W_12, containerHeight, iconHeight, titleHeight]);
    const descTextLine = useMemo(() => Number((descTextHeight / Dimens.H_16).toFixed(0)), [Dimens.H_16, descTextHeight]);

    const handleFuncClick = useCallback((item: Meta) => () => {

        switch (item.key) {
            case DEFAULT_FUNC_KEY.JOBS:
                NavigationService.navigate(SCREENS.TEMPLATE_JOB_REGISTER_SCREEN, { data: item });
                break;

            case DEFAULT_FUNC_KEY.ROUTE:
                openMap( Number(workspaceDetail?.lat), Number(workspaceDetail?.lng), workspaceDetail?.setting_generals?.title || '');
                break;

            case DEFAULT_FUNC_KEY.RECENT:
                {
                    if (!isUserLoggedIn) {
                        NavigationService.navigate(SCREENS.LOGIN_SCREEN, { callback: () => NavigationService.navigate(SCREENS.TEMPLATE_ORDER_HISTORY_SCREEN) });
                    } else {
                        NavigationService.navigate(SCREENS.TEMPLATE_ORDER_HISTORY_SCREEN);
                    }
                }
                break;

            case DEFAULT_FUNC_KEY.FAVORITES:
                {
                    if (!isUserLoggedIn) {
                        NavigationService.navigate(SCREENS.LOGIN_SCREEN, { callback: () => NavigationService.navigate(SCREENS.RESTAURANT_DETAIL_SCREEN, { showFavorite: true }) });
                    } else {
                        NavigationService.navigate(SCREENS.RESTAURANT_DETAIL_SCREEN, { showFavorite: true });
                    }
                }
                break;

            case DEFAULT_FUNC_KEY.ACCOUNT:
                NavigationService.navigate(SCREENS.ACCOUNT_TAB_SCREEN);
                break;

            case DEFAULT_FUNC_KEY.SHARE:
                {
                    Share.share({
                        message:
                          IS_ANDROID ? Config.ENV_ANDROID_STORE_URL || '' : Config.ENV_IOS_STORE_URL || ''
                    });
                }
                break;

            case DEFAULT_FUNC_KEY.LOYALTY:
                {
                    if (!isUserLoggedIn) {
                        NavigationService.navigate(SCREENS.LOGIN_SCREEN, { callback: () => NavigationService.navigate(SCREENS.AWARD_TAB_SCREEN) });
                    } else {
                        NavigationService.navigate(SCREENS.AWARD_TAB_SCREEN);
                    }
                }
                break;

            case DEFAULT_FUNC_KEY.MENU:
                handleNavToMenu();
                break;

            default:
                openInAppBrowser(item.url);
                break;
        }
    }, [handleNavToMenu, isUserLoggedIn, workspaceDetail?.lat, workspaceDetail?.lng, workspaceDetail?.setting_generals?.title]);

    const getItemIcon = useCallback(() => {
        const funcIconSize = Dimens.W_30;
        let icon;

        switch (item.key) {
            case DEFAULT_FUNC_KEY.JOBS:
                icon = (
                    <BagIcon
                        stroke={themeColors.color_primary}
                        width={funcIconSize}
                        height={funcIconSize}
                    />
                );
                break;

            case DEFAULT_FUNC_KEY.REVIEWS:
                icon = (
                    <BookOpenIcon
                        stroke={themeColors.color_primary}
                        width={funcIconSize}
                        height={funcIconSize}
                    />
                );
                break;

            case DEFAULT_FUNC_KEY.RESERVE:
                icon = (
                    <BookCloseIcon
                        stroke={themeColors.color_primary}
                        width={funcIconSize}
                        height={funcIconSize}
                    />
                );
                break;

            case DEFAULT_FUNC_KEY.ROUTE:
                icon = (
                    <MapIcon
                        stroke={themeColors.color_primary}
                        width={funcIconSize}
                        height={funcIconSize}
                    />
                );
                break;

            case DEFAULT_FUNC_KEY.RECENT:
                icon = (
                    <ClockHistoryIcon2
                        stroke={themeColors.color_primary}
                        width={funcIconSize}
                        height={funcIconSize}
                    />
                );
                break;

            case DEFAULT_FUNC_KEY.FAVORITES:
                icon = (
                    <HeartIcon2
                        stroke={themeColors.color_primary}
                        // strokeWidth={0.8}
                        fill={'transparent'}
                        width={funcIconSize}
                        height={funcIconSize}
                    />
                );
                break;

            case DEFAULT_FUNC_KEY.ACCOUNT:
                icon = (
                    <AccountIcon
                        stroke={themeColors.color_primary}
                        width={funcIconSize}
                        height={funcIconSize}
                    />
                );
                break;

            case DEFAULT_FUNC_KEY.SHARE:
                icon = (
                    <ShareIcon
                        stroke={themeColors.color_primary}
                        width={funcIconSize}
                        height={funcIconSize}
                    />
                );
                break;

            case DEFAULT_FUNC_KEY.LOYALTY:
                icon = (
                    <BowIcon
                        stroke={themeColors.color_primary}
                        width={funcIconSize}
                        height={funcIconSize}
                    />
                );
                break;

            case DEFAULT_FUNC_KEY.MENU:
                icon = (
                    <CupIcon
                        stroke={themeColors.color_primary}
                        width={funcIconSize}
                        height={funcIconSize}
                    />
                );
                break;

            default:
                break;
        }

        return icon;
    }, [Dimens.W_30, item.key, themeColors.color_primary]);

    return (
        <TouchableComponent
            onPress={handleFuncClick(item)}
            style={styles.funcItemContainer}
        >
            <ShadowView
                style={{ shadowRadius: Dimens.H_15, shadowColor: '#00000008' }}
            >
                <View
                    onLayout={onContainerLayout}
                    style={[styles.itemContainer, { backgroundColor: themeColors.workspace_home_func_background, }]}
                >
                    <TextComponent
                        onLayout={onLayout}
                        style={[styles.funcItemTitle, { color: themeColors.workspace_home_func_title }]}
                    >
                        {item.default ? item.name?.toUpperCase() : item.title?.toUpperCase()}
                    </TextComponent>
                    <TextComponent
                        numberOfLines={descTextLine > 0 ? descTextLine : 1}
                        style={[styles.funcItemDesc, {
                            maxWidth: titleWidth,
                            height: descTextHeight,
                            color: themeColors.workspace_home_func_desc,
                            lineHeight: Dimens.H_16
                        }]}
                    >
                        {item.description}
                    </TextComponent>
                    <View
                        onLayout={onIconLayout}
                        style={styles.funcIcon}
                    >
                        {getItemIcon()}
                    </View>
                </View>
            </ShadowView>
        </TouchableComponent>
    );
};

export default memo(FuncItem);

const stylesF = (Dimens: DimensType) => StyleSheet.create({
    funcIcon: { position: 'absolute', bottom: 2, right: 2 },
    funcItemDesc: {
        fontSize: Dimens.FONT_14,
        fontWeight: '400',
        flex: 1,
        marginTop: Dimens.H_6,
    },
    funcItemTitle: {
        fontSize: Dimens.FONT_16,
        fontWeight: '700',
        minWidth: Dimens.H_88,
    },
    itemContainer: {
        borderRadius: Dimens.RADIUS_6,
        paddingHorizontal: Dimens.W_8,
        paddingTop: Dimens.W_8,
        paddingBottom: Dimens.W_4,
        alignSelf: 'flex-start',
        marginBottom: getBottomSpace() || Dimens.H_15,
    },
    funcItemContainer: { marginHorizontal: Dimens.W_8 },
});