import React, {
    FC,
    memo,
    useCallback,
    useEffect,
    useMemo,
    useState,
} from 'react';

import {
    StyleSheet,
    TouchableOpacity,
    View,
} from 'react-native';
import Popover from 'react-native-popover-view';
import { Placement } from 'react-native-popover-view/dist/Types';

import {
    DropDownIcon,
    FlagDutchFRIcon,
    FlagDutchNLIcon,
    FlagEnglishIcon,
    FlagGermanyIcon,
} from '@src/assets/svg';
import { LOCALES } from '@src/configs/constants';
import {
    useAppDispatch,
    useAppSelector,
} from '@src/hooks';
import useBoolean from '@src/hooks/useBoolean';
import useCallAPI from '@src/hooks/useCallAPI';
import useDimens, { DimensType } from '@src/hooks/useDimens';
import useIsUserLoggedIn from '@src/hooks/useIsUserLoggedIn';
import I18nApp from '@src/languages';
import { setHeaderContentLanguage } from '@src/network/axios';
import { UserDataModel } from '@src/network/dataModels';
import { getOrderHistoryService } from '@src/network/services/orderServices';
import {
    getUserProfileService,
    updateUserLocaleService,
} from '@src/network/services/profileServices';
import { updateUserData } from '@src/network/util/authUtility';
import { LoadingActions } from '@src/redux/toolkit/actions/loadingActions';
import { StorageActions } from '@src/redux/toolkit/actions/storageActions';
import useThemeColors from '@src/themes/useThemeColors';

interface IProps {
    reloadWorkspaceData: () => void,
}

const LanguageIcon: FC<IProps> = ({ reloadWorkspaceData }) => {
    const { themeColors } = useThemeColors();

    const dispatch = useAppDispatch();

    const Dimens = useDimens();
    const styles = stylesF(Dimens);

    const [isShowPopup, showPopup, hidePopup] = useBoolean(false);

    const isUserLoggedIn = useIsUserLoggedIn();
    const userData = useAppSelector((state) => state.userDataReducer.userData);
    const storageLanguage = useAppSelector((state) => state.storageReducer.language);
    const workspaceId = useAppSelector((state) => state.storageReducer.templateWorkspaceDetail?.id);
    const workspaceLanguages = useAppSelector((state) => state.storageReducer.workspaceLanguages);

    const languages = useMemo(
            () => {
                const languagesConverted = workspaceLanguages?.map((i: string) => {
                    let icon = (
                        <FlagDutchNLIcon
                            width={Dimens.W_20}
                            height={Dimens.W_20}
                        />
                    );

                    switch (i) {
                        case LOCALES.NL:
                            icon = (
                                <FlagDutchNLIcon
                                    width={Dimens.W_20}
                                    height={Dimens.W_20}
                                />
                            );
                            break;
                        case LOCALES.FR:
                            icon = (
                                <FlagDutchFRIcon
                                    width={Dimens.W_20}
                                    height={Dimens.W_20}
                                />
                            );
                            break;
                        case LOCALES.EN:
                            icon = (
                                <FlagEnglishIcon
                                    width={Dimens.W_20}
                                    height={Dimens.W_20}
                                />
                            );
                            break;
                        case LOCALES.DE:
                            icon = (
                                <FlagGermanyIcon
                                    width={Dimens.W_20}
                                    height={Dimens.W_20}
                                />
                            );
                            break;
                        default:
                            icon = (
                                <FlagDutchNLIcon
                                    width={Dimens.W_20}
                                    height={Dimens.W_20}
                                />
                            );
                            break;
                    }

                    return {
                        locale: i,
                        icon: icon
                    };
                });

                return languagesConverted;
            },
            [Dimens.W_20, workspaceLanguages],
    );

    const currentLanguage = useMemo(() => {
        if (isUserLoggedIn) {
            return {
                icon: languages.find((item) => item.locale === userData?.locale)?.icon || languages[0].icon,
                locale: userData?.locale || languages[0].locale,
            };
        } else {
            return {
                icon: languages.find((item) => item.locale === storageLanguage)?.icon || languages[0].icon,
                locale: storageLanguage
            };
        }
    }, [isUserLoggedIn, languages, storageLanguage, userData?.locale]);

    const [selectedLanguage, setSelectedLanguage] = useState<{ icon?: any, locale?: string } | undefined>(currentLanguage);
    const [showLanguage, setShowLanguage] = useState<boolean>(false);

    const saveStorageLanguage = useCallback((language: string) => {
        dispatch(StorageActions.setStorageLanguage(language));
        setHeaderContentLanguage(language);
        I18nApp.changeLanguage(language);
    }, [dispatch]);

    const { callApi: getOrderHistory } = useCallAPI(
            getOrderHistoryService,
            undefined,
            useCallback((data: any) => {
                if (data?.data?.length === 0) {
                    setShowLanguage(true);
                }
            }, [])
    );

    const { callApi: getUserData } = useCallAPI(
            getUserProfileService,
            undefined,
            useCallback((data: UserDataModel) => {
                updateUserData(data);
                reloadWorkspaceData();
            }, [reloadWorkspaceData])
    );

    const { callApi: updateUserLocale } = useCallAPI(
            updateUserLocaleService,
            useCallback(() => {
                dispatch(LoadingActions.showGlobalLoading(true));
            }, [dispatch])
    );

    const saveLanguage = useCallback((language: string) => {
        if (isUserLoggedIn) {
            updateUserLocale({
                locale: language,
            }).then((res) => {
                if (res?.success) {
                    saveStorageLanguage(language);
                    getUserData();
                }
            });
        } else {
            saveStorageLanguage(language);
            reloadWorkspaceData();
        }
    }, [isUserLoggedIn, updateUserLocale, saveStorageLanguage, getUserData, reloadWorkspaceData]);

    useEffect(() => {
        if (languages?.length === 1) {
            setShowLanguage(false);
        } else if (isUserLoggedIn) {
            if (workspaceId) {
                getOrderHistory({
                    page: 1,
                    limit: 10,
                    workspace_id: workspaceId
                });
            }
        } else {
            setShowLanguage(true);
        }
    }, [getOrderHistory, isUserLoggedIn, languages?.length, workspaceId]);

    useEffect(() => {
        setSelectedLanguage(currentLanguage);
    }, [currentLanguage]);

    const renderPopup = useCallback(() => (
        <Popover
            // statusBarTranslucent
            placement={Placement.BOTTOM}
            backgroundStyle={{ opacity: 0 }}
            isVisible={isShowPopup}
            onRequestClose={hidePopup}
            // offset={-Dimens.H_8}
            popoverStyle={[styles.popOverStyle]}
            arrowSize={{ height: 0, width: 0 }}
            from={(
                <View
                    renderToHardwareTextureAndroid
                    collapsable={false}
                >
                    <TouchableOpacity
                        onPress={showPopup}
                        style={[styles.mainContainer]}
                    >
                        {selectedLanguage?.icon}
                        <DropDownIcon
                            width={Dimens.H_14}
                            height={Dimens.H_14}
                            style={{ transform: [{ rotate: isShowPopup ? '180deg' : '0deg' }], marginLeft: Dimens.W_3 }}
                        />
                    </TouchableOpacity>
                </View>
            )}
        >
            <View style={[styles.popupContainer, { backgroundColor: themeColors.color_card_background }]}>
                {languages.map((item, index) => selectedLanguage?.locale !== item.locale ? (
                    <TouchableOpacity
                        key={index}
                        hitSlop={{ top: Dimens.W_4, bottom: Dimens.W_4, left: Dimens.W_4, right: Dimens.W_4 }}
                        onPress={() => {
                            hidePopup();
                            setSelectedLanguage(item);
                            setTimeout(() => {
                                saveLanguage(item.locale);
                            }, 100);
                        }}
                        style={[styles.flagItemContainer]}
                    >
                        {item.icon}
                    </TouchableOpacity>
                ) : null)}
            </View>
        </Popover>
    ), [Dimens.H_14, Dimens.W_3, Dimens.W_4, hidePopup, isShowPopup, languages, saveLanguage, selectedLanguage?.icon, selectedLanguage?.locale, showPopup, styles.flagItemContainer, styles.mainContainer, styles.popOverStyle, styles.popupContainer, themeColors.color_card_background]);

    return (
        <View >
            {showLanguage ? renderPopup() : null}
        </View>
    );
};

export default memo(LanguageIcon);

const stylesF = (Dimens: DimensType) =>
    StyleSheet.create({
        popupContainer: {
            borderRadius: Dimens.RADIUS_12,
            paddingVertical: Dimens.H_4,
            flexDirection: 'row',
            alignItems: 'center',
        },
        curveShapeContainer: {
            marginLeft: Dimens.W_16
        },
        popOverStyle: {
            borderRadius: Dimens.RADIUS_3,
            paddingBottom: Dimens.H_30,
            paddingTop: Dimens.H_4,
            paddingHorizontal: Dimens.W_24,
            alignItems: 'center',
            justifyContent: 'center',
            backgroundColor: 'transparent',
        },
        mainContainer: {
            flexDirection: 'row',
            alignItems: 'center',
            justifyContent: 'space-between',
            marginLeft: Dimens.W_16,
        },
        areaCode: {
            fontSize: Dimens.FONT_16,
            fontWeight: '400',
        },
        flagItemContainer: {
            paddingHorizontal: Dimens.W_4,
        },
    });
