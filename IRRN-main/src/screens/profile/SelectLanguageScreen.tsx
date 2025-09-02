import React, {
    useCallback,
    useEffect,
    useMemo,
    useState,
} from 'react';

import { useTranslation } from 'react-i18next';
import {
    StyleSheet,
    TouchableOpacity,
    View,
} from 'react-native';
import Popover from 'react-native-popover-view';
import { Placement } from 'react-native-popover-view/dist/Types';

import {
    CheckIcon,
    DropDownIcon,
} from '@src/assets/svg';
import ButtonComponent from '@src/components/ButtonComponent';
import BackButton from '@src/components/header/BackButton';
import HeaderComponent from '@src/components/header/HeaderComponent';
import ShadowView from '@src/components/ShadowView';
import TextComponent from '@src/components/TextComponent';
import { Colors } from '@src/configs';
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
import NavigationService from '@src/navigation/NavigationService';
import { setHeaderContentLanguage } from '@src/network/axios';
import { UserDataModel } from '@src/network/dataModels';
import {
    getUserProfileService,
    updateUserLocaleService,
} from '@src/network/services/profileServices';
import { updateUserData } from '@src/network/util/authUtility';
import { LoadingActions } from '@src/redux/toolkit/actions/loadingActions';
import { StorageActions } from '@src/redux/toolkit/actions/storageActions';
import useThemeColors from '@src/themes/useThemeColors';

const SelectLanguageScreen = () => {
    const { themeColors } = useThemeColors();
    const Dimens = useDimens();
    const styles = stylesF(Dimens);
    const { t } = useTranslation();

    const dispatch = useAppDispatch();

    const [isShowPopup, showPopup, hidePopup] = useBoolean(false);

    const isUserLoggedIn = useIsUserLoggedIn();
    const userData = useAppSelector((state) => state.userDataReducer.userData);
    const storageLanguage = useAppSelector((state) => state.storageReducer.language);
    const workspaceLanguages = useAppSelector((state) => state.storageReducer.workspaceLanguages);

    const languages = useMemo(
            () => {
                const languagesConverted = workspaceLanguages?.map((i: string) => {
                    let label = '';

                    switch (i) {
                        case LOCALES.NL:
                            label = t('Nederlands');
                            break;
                        case LOCALES.FR:
                            label = t('Frans');
                            break;
                        case LOCALES.EN:
                            label = t('Engels');
                            break;
                        case LOCALES.DE:
                            label = t('Duitsland');
                            break;
                        default:
                            label = t('Nederlands');
                            break;
                    }

                    return {
                        locale: i,
                        label: label
                    };
                });

                return languagesConverted;
            },
            [t, workspaceLanguages],
    );

    const currentLanguage = useMemo(() => {
        if (isUserLoggedIn) {
            return {
                label: languages.find((item) => item.locale === userData?.locale)?.label || languages[0].label,
                locale: userData?.locale || languages[0].locale,
            };
        } else {
            return {
                label: languages.find((item) => item.locale === storageLanguage)?.label || languages[0].label,
                locale: storageLanguage || languages[0].locale,
            };
        }
    }, [isUserLoggedIn, languages, storageLanguage, userData?.locale]);

    const [selectedLanguage, setSelectedLanguage] = useState<{ label?: string, locale?: string } | undefined>(currentLanguage);

    const languageToShow = useMemo(() => languages.find((item) => item.locale === selectedLanguage?.locale), [languages, selectedLanguage?.locale]);

    const saveStorageLanguage = useCallback(() => {
        dispatch(StorageActions.setStorageLanguage(selectedLanguage));
        setHeaderContentLanguage(selectedLanguage?.locale);
        I18nApp.changeLanguage(selectedLanguage?.locale);
    }, [dispatch, selectedLanguage]);

    const { callApi: getUserData } = useCallAPI(
            getUserProfileService,
            undefined,
            useCallback((data: UserDataModel) => {
                updateUserData(data);
            }, [])
    );

    const { callApi: updateUserLocale } = useCallAPI(
            updateUserLocaleService,
            useCallback(() => {
                dispatch(LoadingActions.showGlobalLoading(true));
            }, [dispatch]),
            useCallback(() => {
                saveStorageLanguage();
                getUserData();
                NavigationService.goBack();
            }, [getUserData, saveStorageLanguage])
    );

    const saveLanguage = useCallback(() => {
        if (isUserLoggedIn) {
            updateUserLocale({
                locale: selectedLanguage?.locale,
            });
        } else {
            saveStorageLanguage();
        }
    }, [isUserLoggedIn, updateUserLocale, selectedLanguage?.locale, saveStorageLanguage]);

    useEffect(() => {
        if (!userData && isUserLoggedIn) {
            getUserData();
        }
    }, [getUserData, isUserLoggedIn, userData]);

    const renderPopup = useCallback(() => (
        <Popover
            placement={Placement.BOTTOM}
            backgroundStyle={{ opacity: 0 }}
            isVisible={isShowPopup}
            onRequestClose={hidePopup}
            offset={-Dimens.H_8}
            popoverStyle={[styles.popOverStyle]}
            arrowSize={{ height: 0, width: 0 }}
            from={(
                <View
                    renderToHardwareTextureAndroid
                    collapsable={false}
                >
                    <ShadowView
                        renderToHardwareTextureAndroid
                        collapsable={false}
                    >
                        <TouchableOpacity
                            onPress={showPopup}
                            style={[styles.mainContainer, { backgroundColor: themeColors.color_card_background }]}
                        >
                            <TextComponent style={[styles.areaCode, { color: themeColors.color_text_2 }]}>
                                {languageToShow?.label}
                            </TextComponent>
                            <DropDownIcon
                                stroke={themeColors.color_text_2}
                                width={Dimens.H_16}
                                height={Dimens.H_16}
                            />
                        </TouchableOpacity>
                    </ShadowView>
                </View>
            )}
        >
            <ShadowView>
                <View style={{ backgroundColor: themeColors.color_card_background, borderRadius: Dimens.RADIUS_10, paddingVertical: Dimens.H_8, width: Dimens.SCREEN_WIDTH - Dimens.W_48 }}>
                    {languages.map((item, index) => (
                        <TouchableOpacity
                            key={index}
                            onPress={() => {
                                setSelectedLanguage(item);
                                hidePopup();
                            }}
                            style={[styles.flagItemContainer]}
                        >
                            {selectedLanguage?.locale === item.locale ? (
                                <CheckIcon
                                    stroke={themeColors.color_primary}
                                    width={Dimens.H_16}
                                    height={Dimens.H_16}
                                />
                            ) : (
                                <View style={{ width: Dimens.H_16, height: Dimens.H_16 }}/>
                            )}
                            <TextComponent style={[styles.areaCode, { color: themeColors.color_text_2, marginLeft: Dimens.W_8, }]}>
                                {item.label}
                            </TextComponent>
                        </TouchableOpacity>
                    ))}
                </View>
            </ShadowView>
        </Popover>
    ), [Dimens.H_16, Dimens.H_8, Dimens.RADIUS_10, Dimens.SCREEN_WIDTH, Dimens.W_48, Dimens.W_8, hidePopup, isShowPopup, languageToShow?.label, languages, selectedLanguage?.locale, showPopup, styles.areaCode, styles.flagItemContainer, styles.mainContainer, styles.popOverStyle, themeColors.color_card_background, themeColors.color_primary, themeColors.color_text_2]);

    return (
        <View style={{ flex: 1 }}>
            <HeaderComponent >
                <View>
                    <BackButton/>
                    <TextComponent style={styles.headerText}>
                        {t('Taal')}
                    </TextComponent>
                </View>
            </HeaderComponent>

            <View style={{
                flex: 1,
                padding: Dimens.H_24
            }}
            >
                {renderPopup()}
            </View>

            <ButtonComponent
                disabled={currentLanguage.locale === selectedLanguage?.locale}
                title={t('text_save')}
                style={{ marginHorizontal: Dimens.W_80, marginBottom: Dimens.COMMON_BOTTOM_PADDING * 2 }}
                onPress={saveLanguage}
            />

        </View>
    );
};

export default SelectLanguageScreen;

const stylesF = (Dimens: DimensType) =>
    StyleSheet.create({
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
            marginTop: Dimens.H_16,
        },

        popOverStyle: {
            borderRadius: Dimens.RADIUS_3,
            paddingBottom: Dimens.H_30,
            paddingTop: Dimens.H_8,
            paddingHorizontal: Dimens.W_24,
            alignItems: 'center',
            justifyContent: 'center',
            backgroundColor: 'transparent',
        },
        mainContainer: {
            flexDirection: 'row',
            alignItems: 'center',
            paddingHorizontal: Dimens.W_16,
            justifyContent: 'space-between',
            paddingVertical: Dimens.H_16,
            borderRadius: Dimens.RADIUS_10
        },
        areaCode: {
            fontSize: Dimens.FONT_16,
            fontWeight: '400',
        },
        flagItemContainer: {
            flex: 1,
            flexDirection: 'row',
            alignItems: 'center',
            paddingVertical: Dimens.H_12,
            paddingHorizontal: Dimens.W_16,
        },
    });