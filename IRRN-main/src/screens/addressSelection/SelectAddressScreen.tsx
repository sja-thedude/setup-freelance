import React, {
    useCallback,
    useEffect,
    useRef,
    useState,
} from 'react';

import { debounce } from 'lodash';
import { useTranslation } from 'react-i18next';
import {
    Alert,
    Keyboard,
    SectionListData,
    StyleSheet,
    View,
} from 'react-native';
import Geolocation from 'react-native-geolocation-service';
import { KeyboardAwareSectionList, } from 'react-native-keyboard-aware-scroll-view';
import {
    openSettings,
    RESULTS,
} from 'react-native-permissions';
import { useDispatch } from 'react-redux';
import { useEffectOnce } from 'react-use';

import { useRoute } from '@react-navigation/native';
import {
    AddressIcon,
    CheckIcon,
    ClockIcon,
    CloseIcon,
    HomeIcon,
    MagnifierIcon,
    NavigationIcon,
    PlusIcon,
} from '@src/assets/svg';
import ButtonComponent from '@src/components/ButtonComponent';
import BackButton from '@src/components/header/BackButton';
import HeaderComponent from '@src/components/header/HeaderComponent';
import InputComponent from '@src/components/InputComponent';
import TextComponent from '@src/components/TextComponent';
import TouchableComponent from '@src/components/TouchableComponent';
import { Colors } from '@src/configs';
import { useAppSelector } from '@src/hooks';
import useDimens, { DimensType } from '@src/hooks/useDimens';
import useIsUserLoggedIn from '@src/hooks/useIsUserLoggedIn';
import { SelectAddressScreenProps } from '@src/navigation/NavigationRouteProps';
import NavigationService from '@src/navigation/NavigationService';
import { GooglePlaceAddressResultModel, } from '@src/network/dataModels/GooglePlaceAddressResultModel';
import { LoadingActions } from '@src/redux/toolkit/actions/loadingActions';
import { StorageActions } from '@src/redux/toolkit/actions/storageActions';
import useThemeColors from '@src/themes/useThemeColors';
import {
    compareArrays,
    isEmptyOrUndefined,
} from '@src/utils';
import {
    checkLocationPermission,
    getAddressFromCoordinates,
    getCoordinatesFromAddress,
    searchAddress,
} from '@src/utils/locationUtil';
import {
    logError,
    logWarning,
} from '@src/utils/logger';

const SelectAddressScreen = () => {
    const { themeColors } = useThemeColors();
    const Dimens = useDimens();
    const styles = stylesF(Dimens);

    const { t } = useTranslation();
    const dispatch = useDispatch();

    const { params } = useRoute<SelectAddressScreenProps>();
    const { onSelectAddress } = params;

    const { address } = useAppSelector((state) => state.locationReducer);
    const recentAddressData = useAppSelector((state) => state.storageReducer.recentAddress || []);
    const userData = useAppSelector((state) => state.userDataReducer.userData);
    const isUserLoggedIn = useIsUserLoggedIn();

    const [selectedAddress,  setSelectedAddress] = useState<GooglePlaceAddressResultModel | null>(null);
    const [searchText,  setSearchText] = useState('');

    const searchHouseNumberText = useRef('');

    const [listData, setListData] = useState<Array<any>>([
        {
            id: 1,
            title: t('text_use_location'),
            data: [],
            icon: (
                <NavigationIcon
                    stroke={themeColors.color_primary}
                    width={Dimens.W_24}
                    height={Dimens.W_24}
                />
            )
        },
        {
            id: 2,
            title: t('text_use_current_location'),
            data: [],
            icon: (
                <AddressIcon
                    width={Dimens.W_24}
                    height={Dimens.W_24}
                    stroke={themeColors.color_primary}
                />
            )
        },
        {
            id: 3,
            title: t('text_recent_location'),
            data: recentAddressData || [],
            icon: (
                <ClockIcon
                    width={Dimens.W_24}
                    height={Dimens.W_24}
                    stroke={themeColors.color_primary}
                />
            )
        },
        {
            id: 4,
            title: t('text_user_my_location'),
            data: [],
            icon: (
                <HomeIcon
                    width={Dimens.W_24}
                    height={Dimens.W_24}
                    stroke={themeColors.color_primary}
                />
            )
        },
    ]);

    useEffect(() => {
        if (!compareArrays(listData[2].data, recentAddressData )) {
            const newData = listData.map((item) => {

                if (item.id === 3) {
                    return {
                        ...item,
                        data: recentAddressData,
                    };
                }

                return item;
            });

            setListData(newData);
        }
    }, [listData, recentAddressData]);

    // eslint-disable-next-line react-hooks/exhaustive-deps
    const handleSearch = useCallback(debounce(async (text) => {
        const searchResult = await searchAddress(text);

        const newData = listData.map((item) => {
            if (item.id === 1) {
                return {
                    ...item,
                    data: searchResult,
                };
            }

            return item;
        });

        setListData(newData);
    }, 500), [listData]);

    useEffectOnce(() => {
        handleSearch(address);
    });

    const handleSelectAddress = useCallback((address: GooglePlaceAddressResultModel) => {
        Keyboard.dismiss();
        setSelectedAddress(address);
        setSearchText(address.description);

        const newData = listData.map((item) => {

            if (item.id === 1) {
                return {
                    ...item,
                    data: [address],
                };
            }

            return item;
        });

        setListData(newData);
    }, [listData]);

    const checkStreetNumberResultAddress = useCallback((searchResult: Array<GooglePlaceAddressResultModel>, callback?: Function) => {
        const isStreetNumber = searchResult.some((add: GooglePlaceAddressResultModel) =>
            add.types.includes('premise')
            || add.types.includes('subpremise')
            || add.types.includes('street_number')
            || add.types.includes('street_address'));

        if (isStreetNumber) {
            const matchAddress = searchResult.find((add: GooglePlaceAddressResultModel) =>
                add.types.includes('premise')
                || add.types.includes('subpremise')
                || add.types.includes('street_number')
                || add.types.includes('street_address'));

            if (matchAddress) {
                callback && callback(matchAddress);
                handleSelectAddress(matchAddress);
            }
        } else {
            const newData = listData.map((item) => {

                if (item.id === 1) {
                    return {
                        ...item,
                        data: searchResult,
                    };
                }

                return item;
            });

            setListData(newData);
        }
    }, [handleSelectAddress, listData]);

    const handleGetCurrentLocation = useCallback(async () => {
        const resultPermission = await checkLocationPermission();
        if (resultPermission === RESULTS.BLOCKED) {
            Alert.alert(
                    t('text_turn_on_location_services'),
                    '',
                    [
                        {
                            text: 'Cancel',
                            style: 'cancel'
                        },
                        { text: 'OK', onPress: () => openSettings().catch(() => {
                            logWarning('Cannot open settings');
                        }) }
                    ]
            );
        } else {
            dispatch(LoadingActions.showGlobalLoading(true));
            Geolocation.getCurrentPosition(
                    async (position) => {
                        const lat = position?.coords?.latitude;
                        const lng = position?.coords?.longitude;
                        const address = await getAddressFromCoordinates(lat, lng);
                        dispatch(LoadingActions.showGlobalLoading(false));
                        const newData = listData.map((item) => {

                            if (item.id === 2) {
                                return {
                                    ...item,
                                    data: [
                                        {
                                            description: address || '',
                                        }
                                    ],
                                };
                            }

                            return item;
                        });

                        setListData(newData);
                    },
                    (error) => {
                        dispatch(LoadingActions.showGlobalLoading(false));
                        logError(error.code, error.message);
                    },
                    { enableHighAccuracy: true, timeout: 5000, maximumAge: 10000 }
            );
        }
    }, [dispatch, listData, t]);

    const handleSelectCurrentLocation = useCallback(async (address: string) => {
        setSelectedAddress(null);
        setSearchText(address);
        const searchResult = await searchAddress(address);
        checkStreetNumberResultAddress(searchResult);
    }, [checkStreetNumberResultAddress]);

    const handleHouseNumberSearch = useCallback(async (text: string) => {
        const searchResult = await searchAddress(text);
        checkStreetNumberResultAddress(searchResult);
    }, [checkStreetNumberResultAddress]);

    const handleSelectRecentAddress = useCallback((address: GooglePlaceAddressResultModel) => {
        setSelectedAddress(address);
        setSearchText(address.description);

        const newData = listData.map((item) => {

            if (item.id === 1) {
                return {
                    ...item,
                    data: [address],
                };
            }

            return item;
        });

        setListData(newData);
    }, [listData]);

    const deleteRecentAddress = useCallback((address: GooglePlaceAddressResultModel | null) => {
        if (address) {
            const saved = recentAddressData.some((add) => add.place_id === address.place_id);
            if (saved) {
                const newData = recentAddressData.filter((add) => add.place_id !== address.place_id);
                dispatch(StorageActions.setStorageAddress(newData));
            }
        }
    }, [dispatch, recentAddressData]);

    const saveAddressToLocal = useCallback((address: GooglePlaceAddressResultModel | null) => {
        if (address) {
            const saved = recentAddressData.some((add) => add.place_id === address.place_id);
            let newData;

            if (saved) {
                newData = recentAddressData.filter((add) => add.place_id !== address.place_id);
                newData = [address, ...newData];
            } else {
                if (recentAddressData.length === 5) {
                    recentAddressData.pop();
                }
                newData = [address, ...recentAddressData];
            }

            dispatch(StorageActions.setStorageAddress(newData));
        }
    }, [dispatch, recentAddressData]);

    const handleSelectHomeAddress = useCallback(async () => {
        if (userData?.address && userData?.lat && userData?.lng) {
            const realAddress = {
                lat: userData?.lat,
                lng: userData?.lng,
                address: userData?.address
            };

            const callback = (add: GooglePlaceAddressResultModel) => {
                saveAddressToLocal(add);
                NavigationService.pop();
                onSelectAddress(realAddress);
            };

            const searchResult = await searchAddress(userData?.address);
            checkStreetNumberResultAddress(searchResult, callback);
        }
    }, [checkStreetNumberResultAddress, onSelectAddress, saveAddressToLocal, userData?.address, userData?.lat, userData?.lng]);

    const handleConfirmAddress = useCallback(async () => {
        dispatch(LoadingActions.showGlobalLoading(true));
        const result = await getCoordinatesFromAddress(selectedAddress?.place_id);
        dispatch(LoadingActions.showGlobalLoading(false));
        if (result) {
            const realAddress = {
                lat: result.lat,
                lng: result.lng,
                address: selectedAddress?.description
            };
            saveAddressToLocal(selectedAddress);
            NavigationService.pop();
            onSelectAddress(realAddress);
        }
    }, [dispatch, onSelectAddress, saveAddressToLocal, selectedAddress]);

    const renderHighLightText = useCallback((text: string, pattern: string) => {
        if (!text.toLowerCase().startsWith(pattern.toLowerCase())) {
            return (
                <TextComponent
                    numberOfLines={1}
                    style={[styles.addressText, { color: themeColors.color_text_2 }]}
                >
                    {text}
                </TextComponent>
            );
        }

        const matchText = text.slice(0, pattern.length);
        const restText = text.slice(pattern.length, text.length);

        return (
            <TextComponent
                numberOfLines={1}
                style={[styles.addressText, { color: themeColors.color_text_2 }]}
            >
                <TextComponent style={styles.addressHighLightText}>{matchText}</TextComponent>
                {restText}
            </TextComponent>
        );
    }, [styles.addressHighLightText, styles.addressText, themeColors.color_text_2]);

    const renderSectionHeader = useCallback(({ section }: SectionListData<any, any>) => {
        if (section.id === 4 && !isUserLoggedIn) {
            return null;
        }

        let onPress;

        if (section.id === 2) {
            onPress = handleGetCurrentLocation;
        }

        if (section.id === 4) {
            onPress = handleSelectHomeAddress;
        }

        return (
            <TouchableComponent
                onPress={onPress}
                disabled={!onPress}
                style={[styles.sectionHeaderContainer, { backgroundColor: themeColors.color_app_background }]}
            >
                {section.icon}
                <TextComponent
                    numberOfLines={1}
                    style={[styles.sectionHeaderText, { color: themeColors.color_text_2 }]}
                >
                    {section.title}
                    {section.id === 4 && isUserLoggedIn && (
                        <TextComponent style={[styles.homeAddText, { color: themeColors.color_text_2 }]}>
                            {`  ${userData?.address || ''}`}
                        </TextComponent>
                    )}
                </TextComponent>
            </TouchableComponent>
        );
    }, [handleGetCurrentLocation, handleSelectHomeAddress, isUserLoggedIn, styles.homeAddText, styles.sectionHeaderContainer, styles.sectionHeaderText, themeColors.color_app_background, themeColors.color_text_2, userData?.address]);

    const renderItem = useCallback(({ item, section }: {item: GooglePlaceAddressResultModel | any, section: any}) => {
        if (section.id === 1) {
            const isStreetNumber = item.types?.includes('premise') || item.types?.includes('subpremise') || item.types?.includes('street_number') || item.types?.includes('street_address');
            const isSelected = item?.place_id === selectedAddress?.place_id;

            return (
                <TouchableComponent
                    disabled={!isStreetNumber}
                    onPress={() => handleSelectAddress(item)}
                    style={styles.addTextContainer}
                >
                    <View style={styles.addTextWrapper}>
                        <View style={{ flex: 1, marginRight: isSelected ? Dimens.W_26 : Dimens.W_38 }}>
                            {renderHighLightText(item.description, searchText)}
                        </View>
                        {isSelected && (
                            <CheckIcon
                                width={Dimens.W_14}
                                height={Dimens.W_14}
                                stroke={themeColors.color_primary}
                            />
                        )}
                    </View>
                    {!isStreetNumber && (
                        <InputComponent
                            containerStyle={styles.inputAddContainer}
                            style={styles.inputAdd}
                            textColorInput={themeColors.color_text_2}
                            inputBorderRadius={Dimens.W_8}
                            placeholder={t('hint_search_house_number')}
                            leftIcon={(
                                <PlusIcon
                                    width={Dimens.W_16}
                                    height={Dimens.W_16}
                                    fill={themeColors.color_input_place_holder}
                                />
                            )}
                            onChangeText={(text) => {
                                searchHouseNumberText.current = text;
                            }}
                            onBlur={() => {
                                searchHouseNumberText.current.trim() && handleHouseNumberSearch(`${searchHouseNumberText.current} ${item.description}`);
                                searchHouseNumberText.current = '';
                            }}
                        />
                    )}
                </TouchableComponent>
            );
        }

        if (section.id === 2) {
            return (
                <TouchableComponent
                    onPress={() => handleSelectCurrentLocation(item.description)}
                    style={styles.currentLocationAddContainer}
                >
                    {renderHighLightText(item.description, searchText)}
                </TouchableComponent>
            );
        }

        if (section.id === 3) {
            return (
                <View
                    style={styles.recentAddContainer}
                >
                    <TouchableComponent
                        style={styles.recentAddWrapper}
                        onPress={() => handleSelectRecentAddress(item)}
                    >
                        {renderHighLightText(item.description, searchText)}
                    </TouchableComponent>

                    <TouchableComponent
                        onPress={() => deleteRecentAddress(item)}
                        hitSlop={Dimens.DEFAULT_HIT_SLOP}
                    >
                        <CloseIcon
                            width={Dimens.W_10}
                            height={Dimens.W_10}
                        />
                    </TouchableComponent>
                </View>
            );
        }

        return <View/>;

    }, [Dimens.DEFAULT_HIT_SLOP, Dimens.W_10, Dimens.W_14, Dimens.W_16, Dimens.W_26, Dimens.W_38, Dimens.W_8, deleteRecentAddress, handleHouseNumberSearch, handleSelectAddress, handleSelectCurrentLocation, handleSelectRecentAddress, renderHighLightText, searchText, selectedAddress?.place_id, styles.addTextContainer, styles.addTextWrapper, styles.currentLocationAddContainer, styles.inputAdd, styles.inputAddContainer, styles.recentAddContainer, styles.recentAddWrapper, t, themeColors.color_input_place_holder, themeColors.color_primary, themeColors.color_text_2]);

    return (
        <View style={[styles.mainContainer, { backgroundColor: themeColors.color_app_background }]}>
            <HeaderComponent
                style={{ paddingTop: Dimens.COMMON_HEADER_PADDING - Dimens.COMMON_HEADER_PADDING_EXTRA }}
            >
                <View style={styles.container}>
                    <BackButton/>
                    <TextComponent style={styles.headerText}>
                        {t('tab_home_title').toUpperCase()}
                    </TextComponent>
                </View>
                <InputComponent
                    containerStyle={styles.inputContainer}
                    style={styles.input}
                    textColorInput={themeColors.color_text_2}
                    inputBorderRadius={Dimens.W_5}
                    placeholder={''}
                    value={searchText}
                    autoCapitalize={'none'}
                    autoFocus
                    leftIcon={(
                        <MagnifierIcon
                            width={Dimens.W_24}
                            height={Dimens.W_24}
                            stroke={themeColors.color_primary}
                        />
                    )}
                    rightIcon={searchText ? (
                        <CloseIcon
                            width={Dimens.W_12}
                            height={Dimens.W_12}
                        />
                    ) : null}
                    rightIconPress={() => {
                        setSearchText('');
                        handleSearch('');
                        setSelectedAddress(null);
                    }}
                    onChangeText={(text) => {
                        setSearchText(text);
                        handleSearch(text);
                        setSelectedAddress(null);
                    }}
                />
            </HeaderComponent>
            <View style={styles.listContainer}>
                <KeyboardAwareSectionList
                    keyboardShouldPersistTaps={'handled'}
                    showsVerticalScrollIndicator={false}
                    stickySectionHeadersEnabled
                    sections={listData.filter(Boolean)}
                    renderItem={renderItem}
                    renderSectionHeader={renderSectionHeader}
                />
                {!isEmptyOrUndefined(selectedAddress) && (
                    <ButtonComponent
                        title={t('text_search_address')}
                        onPress={handleConfirmAddress}
                    />
                )}

            </View>

        </View>
    );
};

export default SelectAddressScreen;

const stylesF = (Dimens: DimensType) => StyleSheet.create({
    listContainer: {
        flex: 1,
        paddingHorizontal: Dimens.W_12,
        marginTop: Dimens.H_24,
        overflow: 'hidden',
        zIndex: -999
    },
    mainContainer: { flex: 1, paddingBottom: Dimens.COMMON_BOTTOM_PADDING },
    recentAddWrapper: { flex: 1, marginRight: Dimens.W_26 },
    recentAddContainer: {
        flexDirection: 'row',
        alignItems: 'center',
        paddingVertical: Dimens.H_10,
        paddingLeft: Dimens.W_38,
    },
    currentLocationAddContainer: {
        paddingVertical: Dimens.H_10,
        paddingLeft: Dimens.W_38,
        paddingRight: Dimens.W_38,
    },
    inputAdd: { fontSize: Dimens.FONT_12, padding: 0 },
    inputAddContainer: {
        height: Dimens.H_34,
        marginTop: Dimens.H_10,
        marginRight: Dimens.W_38,
    },
    addTextWrapper: { flexDirection: 'row', alignItems: 'center' },
    addTextContainer: {
        paddingVertical: Dimens.H_10,
        paddingLeft: Dimens.W_38,
    },
    homeAddText: { fontSize: Dimens.FONT_12, fontWeight: '400' },
    sectionHeaderText: {
        fontSize: Dimens.FONT_12,
        fontWeight: '700',
        marginLeft: Dimens.W_15,
        flex: 1,
    },
    sectionHeaderContainer: {
        flexDirection: 'row',
        alignItems: 'center',
        paddingTop: Dimens.H_24,
    },
    addressHighLightText: { fontSize: Dimens.FONT_12, fontWeight: '700' },
    addressText: { fontSize: Dimens.FONT_12, flex: 1 },
    container: {
        flexDirection: 'row',
        alignItems: 'center',
    },
    headerText: {
        color: Colors.COLOR_WHITE,
        fontSize: Dimens.FONT_26,
        fontWeight: '700',
    },
    inputContainer: {
        marginTop: Dimens.H_6,
        marginHorizontal: Dimens.W_16,
        marginBottom: -Dimens.H_40 / 2 - Dimens.H_14,
        height: Dimens.H_42,
    },
    input: {
        paddingVertical: 0,
        fontSize: Dimens.FONT_14,
    },
});