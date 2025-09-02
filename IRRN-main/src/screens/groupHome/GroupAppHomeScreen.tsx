import React, {
    useCallback,
    useMemo,
    useState,
} from 'react';

import { useTranslation } from 'react-i18next';
import {
    BackHandler,
    FlatList,
    InteractionManager,
    StyleSheet,
    View,
} from 'react-native';
import Config from 'react-native-config';
import Geolocation from 'react-native-geolocation-service';
import {
    useEffectOnce,
    useUpdateEffect,
} from 'react-use';

import { Images } from '@src/assets/images';
import { MapIconGroupHome } from '@src/assets/svg';
import ButtonComponent from '@src/components/ButtonComponent';
import BackButton from '@src/components/header/BackButton';
import ImageComponent from '@src/components/ImageComponent';
import TextComponent from '@src/components/TextComponent';
import TouchableComponent from '@src/components/TouchableComponent';
import {
    DEFAULT_DISTANCE_UNIT,
    DEFAULT_HOME_ADDRESS,
} from '@src/configs/constants';
import {
    useAppDispatch,
    useAppSelector,
} from '@src/hooks';
import useCallAPI from '@src/hooks/useCallAPI';
import useDimens, { DimensType } from '@src/hooks/useDimens';
import NavigationService from '@src/navigation/NavigationService';
import { setHeaders } from '@src/network/axios';
import { RestaurantDetailModel } from '@src/network/dataModels/RestaurantDetailModel';
import {
    getGroupAppDetailByTokenService,
    getGroupListRestaurantService,
    getWorkspaceSettingByIdService,
} from '@src/network/services/restaurantServices';
import { LoadingActions } from '@src/redux/toolkit/actions/loadingActions';
import { LocationActions } from '@src/redux/toolkit/actions/locationActions';
import { StorageActions } from '@src/redux/toolkit/actions/storageActions';
import useThemeColors from '@src/themes/useThemeColors';
import { getStatusBarHeight } from '@src/utils/iPhoneXHelper';
import { getAddressFromCoordinates } from '@src/utils/locationUtil';
import {
    log,
    logError,
} from '@src/utils/logger';

const GroupAppHomeScreen = () => {
    const { themeColors } = useThemeColors();
    const Dimens = useDimens();
    const styles = stylesF(Dimens);

    const { t } = useTranslation();
    const dispatch = useAppDispatch();

    const workspaceDetail = useAppSelector((state) => state.storageReducer.templateWorkspaceDetail);
    const groupAppDetail = useAppSelector((state) => state.storageReducer.groupAppDetail);
    const { lat, lng, address } = useAppSelector((state) => state.locationReducer);

    const [listRestaurant, setListRestaurant] = useState<Array<RestaurantDetailModel>>([]);
    const [selectedRestaurant, setSelectedRestaurant] = useState<RestaurantDetailModel | null>(workspaceDetail);

    const { callApi: getGroupAppDetailByToken } = useCallAPI(
            getGroupAppDetailByTokenService,
            undefined,
            useCallback((data: any) => {
                dispatch(StorageActions.setStorageGroupAppDetail(data));
            }, [dispatch]),
            undefined,
            false
    );

    const { callApi: getGroupListRestaurant } = useCallAPI(
            getGroupListRestaurantService,
            undefined,
            useCallback((data: any) => {
                setListRestaurant(data.restaurants);
            }, []),
    );

    const { callApi: getWorkspaceSettingById } = useCallAPI(
            getWorkspaceSettingByIdService,
            useCallback(() => {
                dispatch(LoadingActions.showGlobalLoading(true));
            }, [dispatch]),
            (data: any) => {
                dispatch(StorageActions.setStorageWorkspaceDetail(selectedRestaurant));
                dispatch(StorageActions.setStorageWorkspaceSetting(data));
                InteractionManager.runAfterInteractions(() => {
                    NavigationService.goBack();
                });
            },
            undefined,
            false,
            false
    );

    const handleSelectRestaurant = useCallback(() => {
        if (selectedRestaurant) {
            if (selectedRestaurant.id !== workspaceDetail?.id) {
                getWorkspaceSettingById({ restaurant_id: selectedRestaurant?.id });
            } else {
                NavigationService.goBack();
            }
        }
    }, [getWorkspaceSettingById, selectedRestaurant, workspaceDetail?.id]);

    useUpdateEffect(() => {
        if (!selectedRestaurant) {
            setSelectedRestaurant(listRestaurant[0]);
        }
    }, [listRestaurant]);

    useEffectOnce(() => {
        dispatch(LoadingActions.showGlobalLoading(true));

        if (lat === DEFAULT_HOME_ADDRESS.lat && lng === DEFAULT_HOME_ADDRESS.lng && address === DEFAULT_HOME_ADDRESS.address) {
            Geolocation.getCurrentPosition(
                    async (position) => {
                        log('getCurrentPosition', position);
                        const lat = position?.coords?.latitude;
                        const lng = position?.coords?.longitude;
                        const address = await getAddressFromCoordinates(lat, lng);
                        dispatch(LocationActions.setLocation({ lat: lat, lng: lng, address: address || '' }));

                        setHeaders({ lat, lng });
                        getGroupListRestaurant({ group_app_id: groupAppDetail?.id });
                    },
                    (error) => {
                        setHeaders({ lat: undefined, lng: undefined });
                        getGroupListRestaurant({ group_app_id: groupAppDetail?.id });

                        logError(error.code, error.message);
                    },
                    { enableHighAccuracy: true, timeout: 5000, maximumAge: 10000 }
            );
        } else {
            setHeaders({ lat, lng });
            getGroupListRestaurant({ group_app_id: groupAppDetail?.id });
        }
    });

    useEffectOnce(() => {
        getGroupAppDetailByToken({ group_app_token: Config.ENV_GROUP_TOKEN });
    });

    useEffectOnce(() => {
        const subscription = BackHandler.addEventListener('hardwareBackPress', () => true);

        return () => {
            subscription.remove();
        };
    });

    const renderItem = useCallback(({ item } : {item: RestaurantDetailModel, index: number}) => {
        const selected = item.id === selectedRestaurant?.id;

        return (
            <TouchableComponent
                onPress={() => {
                    setSelectedRestaurant(item);
                }}
                style={[styles.itemContainer, {
                    backgroundColor: selected ? '#1B1A19' : '#322F28',
                    borderColor: selected ? themeColors.group_color : 'transparent',
                }]}
            >
                <ImageComponent
                    defaultImage={Images.image_placeholder}
                    source={{ uri: item.photo }}
                    style={styles.itemImage}
                />
                <View style={styles.itemInfoContainer}>
                    <View style={styles.itemNameContainer}>
                        <TextComponent
                            numberOfLines={2}
                            style={styles.itemNameText}
                        >
                            {item?.setting_generals?.title || ' '}
                        </TextComponent>
                    </View>

                    <TextComponent style={styles.address1Text}>
                        {item.address_line_1}
                    </TextComponent>
                    <View style={{
                        flexDirection: 'row',
                        alignItems: 'flex-end'
                    }}
                    >
                        <TextComponent style={styles.address2Text}>
                            {item.address_line_2 || ' '}
                        </TextComponent>
                        <TextComponent style={styles.distanceText}>
                            {`${(Number(item.distance) / 1000).toFixed(1)} ${DEFAULT_DISTANCE_UNIT.toLowerCase()}`}
                        </TextComponent>
                    </View>
                </View>
            </TouchableComponent>
        );
    }, [selectedRestaurant?.id, styles.address1Text, styles.address2Text, styles.distanceText, styles.itemContainer, styles.itemImage, styles.itemInfoContainer, styles.itemNameContainer, styles.itemNameText, themeColors.group_color]);

    const renderHeader = useMemo(() => (
        <View style={[styles.tabContainer, { backgroundColor: themeColors.group_color }]}>
            {workspaceDetail ? (
                    <BackButton
                        style={styles.backButton}
                        onPress={NavigationService.goBack}
                    />
                ) : null}
            <MapIconGroupHome
                width={Dimens.W_124}
                height={Dimens.W_124}
            />
            <TextComponent style={styles.groupName}>
                {groupAppDetail?.name}
            </TextComponent>
            <TextComponent style={styles.groupDesc}>
                {groupAppDetail?.description}
            </TextComponent>
        </View>
    ), [Dimens.W_124, groupAppDetail?.description, groupAppDetail?.name, styles.backButton, styles.groupDesc, styles.groupName, styles.tabContainer, themeColors.group_color, workspaceDetail]);

    const renderListRestaurant = useMemo(() => (
        <View style={styles.listContainer}>
            <FlatList
                keyboardShouldPersistTaps={'handled'}
                contentContainerStyle={styles.listItem}
                showsVerticalScrollIndicator={false}
                data={listRestaurant}
                renderItem={renderItem}
            />
        </View>
    ), [listRestaurant, renderItem, styles.listContainer, styles.listItem]);

    const renderButton = useMemo(() => (
        <View style={styles.buttonContainer}>
            <ButtonComponent
                title={t('group_selecting_continue')}
                style={{ backgroundColor: themeColors.group_color }}
                styleTitle={{ fontWeight: '700' }}
                onPress={handleSelectRestaurant}
            />
        </View>
    ), [handleSelectRestaurant, styles.buttonContainer, t, themeColors.group_color]);

    return (
        <View style={styles.container}>
            {renderHeader}
            {renderListRestaurant}
            {renderButton}
        </View>
    );
};

export default GroupAppHomeScreen;

const stylesF = (Dimens: DimensType) => StyleSheet.create({
    listItem: { paddingVertical: Dimens.H_12 },
    listContainer: { flex: 1, paddingHorizontal: Dimens.W_24 },
    groupDesc: {
        fontSize: Dimens.FONT_16,
        fontWeight: '400',
        marginTop: Dimens.H_8,
        textAlign: 'center',
        color: 'white',
    },
    groupName: {
        fontSize: Dimens.FONT_26,
        fontWeight: '700',
        marginTop: Dimens.H_8,
        textAlign: 'center',
        color: 'white',
    },
    backButton: {
        left: Dimens.W_16,
        position: 'absolute',
        top: getStatusBarHeight() + Dimens.H_16,
    },
    address2Text: {
        fontSize: Dimens.FONT_16,
        fontWeight: '700',
        color: 'white',
        flex: 1
    },
    address1Text: {
        fontSize: Dimens.FONT_16,
        fontWeight: '400',
        color: '#898A8D',
    },
    distanceText: {
        fontSize: Dimens.FONT_16,
        fontWeight: '400',
        color: '#898A8D',
    },
    itemNameText: {
        fontSize: Dimens.FONT_20,
        fontWeight: '700',
        flex: 1,
        marginRight: Dimens.W_4,
        color: 'white',
    },
    itemNameContainer: {
        flexDirection: 'row',
        alignItems: 'center',
        justifyContent: 'space-between',
    },
    itemInfoContainer: { flex: 1, marginLeft: Dimens.W_16 },
    itemImage: {
        width: Dimens.W_64,
        height: Dimens.W_64,
        borderRadius: Dimens.W_64,
    },
    itemContainer: {
        flexDirection: 'row',
        alignItems: 'center',
        marginBottom: Dimens.H_8,
        borderRadius: Dimens.RADIUS_10,
        borderWidth: 2,
        paddingHorizontal: Dimens.W_10,
        paddingVertical: Dimens.H_16,
    },
    buttonContainer: {
        paddingBottom: Dimens.COMMON_BOTTOM_PADDING,
        marginHorizontal: Dimens.W_24,
    },
    tabContainer: {
        width: '100%',
        paddingHorizontal: Dimens.W_38,
        paddingBottom: Dimens.H_24,
        paddingTop: getStatusBarHeight() + Dimens.H_8,
        borderBottomLeftRadius: Dimens.RADIUS_30,
        borderBottomRightRadius: Dimens.RADIUS_30,
        alignItems: 'center',
    },
    container: { flex: 1, backgroundColor: '#404040' },
});
