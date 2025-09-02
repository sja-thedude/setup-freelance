import React, {
    useCallback,
    useEffect,
    useMemo,
} from 'react';

import {
    StyleSheet,
    View,
} from 'react-native';

import { Images } from '@src/assets/images';
import HeaderComponent from '@src/components/header/HeaderComponent';
import ImageComponent from '@src/components/ImageComponent';
import TextComponent from '@src/components/TextComponent';
import TouchableComponent from '@src/components/TouchableComponent';
import { Colors } from '@src/configs';
import {
    ITS_READY_MORE_INFO_LINK,
    ITS_READY_PRIVACY_LINK,
    ITS_READY_TERM_AND_CONDITION_LINK,
} from '@src/configs/constants';
import { useAppSelector } from '@src/hooks';
import useBoolean from '@src/hooks/useBoolean';
import useCallAPI from '@src/hooks/useCallAPI';
import useDimens, { DimensType } from '@src/hooks/useDimens';
import useIsUserLoggedIn from '@src/hooks/useIsUserLoggedIn';
import { SCREENS } from '@src/navigation/config/screenName';
import NavigationService from '@src/navigation/NavigationService';
import { UserDataModel } from '@src/network/dataModels';
import { getUserProfileService } from '@src/network/services/profileServices';
import { updateUserData } from '@src/network/util/authUtility';
import useThemeColors from '@src/themes/useThemeColors';
import {
    handleOpenLink,
    openInAppBrowser,
} from '@src/utils';

import ConfirmDelAccountDialog from './component/ConfirmDelAccountDialog';
import ConfirmLogoutDialog from './component/ConfirmLogoutDialog';
import ScrollViewComponent from '@src/components/ScrollViewComponent';
import { useTranslation } from 'react-i18next';

const ProfileScreen = () => {
    const { themeColors } = useThemeColors();
    const Dimens = useDimens();
    const styles = stylesF(Dimens);
    const { t } = useTranslation();

    const [isShowConfirmLogout, showConfirmLogout, hideConfirmLogout] = useBoolean(false);
    const [isShowConfirmDel, showConfirmDel, hideConfirmDel] = useBoolean(false);

    const isUserLoggedIn = useIsUserLoggedIn();
    const userStorageData = useAppSelector((state) => state.storageReducer.userData);
    const userData = useAppSelector((state) => state.userDataReducer.userData);
    const workspaceLanguages = useAppSelector((state) => state.storageReducer.workspaceLanguages);

    const { callApi: getUserData } = useCallAPI(
            getUserProfileService,
            undefined,
            useCallback((data: UserDataModel) => {
                updateUserData(data);
            }, [])
    );

    useEffect(() => {
        if (!userData && isUserLoggedIn) {
            getUserData();
        }
    }, [getUserData, isUserLoggedIn, userData]);

    const languageItem = useMemo(() => ({
        name: t('Taal'),
        onPress: () => NavigationService.navigate(SCREENS.SELECT_LANGUAGE_SCREEN),
        color: themeColors.color_text_profile_menu
    }), [t, themeColors.color_text_profile_menu]);

    const defaultItems = useMemo(() => ([
        {
            name: t('text_more_information'),
            onPress: () => handleOpenLink(ITS_READY_MORE_INFO_LINK),
            color: themeColors.color_text_profile_menu
        },
        {
            name: t('text_title_term_and_conditions'),
            onPress: () => openInAppBrowser(ITS_READY_TERM_AND_CONDITION_LINK),
            color: themeColors.color_text_profile_menu
        },
        {
            name: t('text_privacybeleid'),
            onPress: () => openInAppBrowser(ITS_READY_PRIVACY_LINK),
            color: themeColors.color_text_profile_menu
        }
    ]), [t, themeColors.color_text_profile_menu]);

    const loggedInItems = useMemo(() => ([
        {
            name: t('text_change_profile'),
            onPress: () => NavigationService.navigate(SCREENS.EDIT_PROFILE_SCREEN),
            color: themeColors.color_text_profile_menu
        },
        ...defaultItems,
        workspaceLanguages?.length > 1 && languageItem,
        {
            name: t('text_de_active'),
            onPress: showConfirmDel,
            color: themeColors.color_text_profile_menu
        },
        {
            name: t('text_logout'),
            onPress: showConfirmLogout,
            color: themeColors.color_error
        }
    ].filter(Boolean)), [defaultItems, languageItem, showConfirmDel, showConfirmLogout, t, themeColors.color_error, themeColors.color_text_profile_menu, workspaceLanguages?.length]);

    const notLoggedInItems = useMemo(() => ([
        {
            name: t('profile_login_or_register'),
            onPress: () => NavigationService.navigate(SCREENS.LOGIN_SCREEN),
            color: themeColors.color_text_profile_menu
        },
        ...defaultItems,
        workspaceLanguages?.length > 1 && languageItem
    ].filter(Boolean)), [defaultItems, languageItem, t, themeColors.color_text_profile_menu, workspaceLanguages?.length]);

    const menuItems = useMemo(() => isUserLoggedIn ? loggedInItems : notLoggedInItems, [loggedInItems, notLoggedInItems, isUserLoggedIn]);

    return (
        <View style={{ flex: 1 }}>
            <HeaderComponent >
                <TextComponent style={styles.headerText}>
                    {t('text_profile')}
                </TextComponent>
            </HeaderComponent>

            <ScrollViewComponent bounces={false}>
                {isUserLoggedIn && (
                    <ImageComponent
                        defaultImage={Images.defaultUserAvatar}
                        source={{ uri: userData?.photo ? userData.photo : userStorageData?.photo }}
                        style={styles.avatar}
                    />
                )}

                <View>
                    {menuItems.map((item, index) => (
                        <View
                            key={index}
                        >
                            <View style={{ backgroundColor: themeColors.color_divider, height: 0.5, opacity: 0.5 }}/>
                            <TouchableComponent

                                style={[styles.menuItemContainer]}
                                onPress={item.onPress}
                            >
                                <TextComponent style={{ ...styles.menuText, color: item?.color }}>
                                    {item.name}
                                </TextComponent>
                            </TouchableComponent>
                        </View>

                    ))}
                </View>
            </ScrollViewComponent>

            <ConfirmLogoutDialog
                hideModal={hideConfirmLogout}
                isShow={isShowConfirmLogout}
            />

            <ConfirmDelAccountDialog
                hideModal={hideConfirmDel}
                isShow={isShowConfirmDel}
            />
        </View>
    );
};

export default ProfileScreen;

const stylesF = (Dimens: DimensType) => StyleSheet.create({
    menuText: { fontSize: Dimens.FONT_16 },
    menuItemContainer: {
        paddingHorizontal: Dimens.W_20,
        paddingVertical: Dimens.H_24,
    },
    avatar: {
        width: Dimens.H_100,
        height: Dimens.H_100,
        borderRadius: Dimens.H_100,
        alignSelf: 'center',
        marginVertical: Dimens.H_20,
    },
    headerText: {
        color: Colors.COLOR_WHITE,
        fontSize: Dimens.FONT_26,
        fontWeight: '700',
    },
});