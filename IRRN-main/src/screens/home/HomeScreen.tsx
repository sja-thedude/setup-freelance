import React, {
    useCallback,
    useEffect,
    useMemo,
    useState,
} from 'react';

import { useTranslation } from 'react-i18next';
import {
    StyleSheet,
    View,
} from 'react-native';
import { useDispatch } from 'react-redux';

import { Images } from '@src/assets/images';
import { ArrowRightIcon } from '@src/assets/svg';
import FlatListComponent from '@src/components/FlatListComponent';
import ImageComponent from '@src/components/ImageComponent';
import RefreshControlComponent from '@src/components/RefreshControlComponent';
import ScrollViewComponent from '@src/components/ScrollViewComponent';
import TextComponent from '@src/components/TextComponent';
import TouchableComponent from '@src/components/TouchableComponent';
import {
    DEFAULT_DISTANCE,
    DEFAULT_DISTANCE_UNIT,
    RESTAURANT_SORT_TYPE,
} from '@src/configs/constants';
import { useAppSelector } from '@src/hooks';
import useBoolean from '@src/hooks/useBoolean';
import useCallAPI from '@src/hooks/useCallAPI';
import useDimens, { DimensType } from '@src/hooks/useDimens';
import useIsUserLoggedIn from '@src/hooks/useIsUserLoggedIn';
import { SCREENS } from '@src/navigation/config/screenName';
import NavigationService from '@src/navigation/NavigationService';
import { RestaurantFavoriteItemModel, } from '@src/network/dataModels/RestaurantFavoriteItemModel';
import { RestaurantNearbyItemModel, } from '@src/network/dataModels/RestaurantNearbyItemModel';
import { RestaurantRecentItemModel, } from '@src/network/dataModels/RestaurantRecentItemModel';
import {
    getRestaurantFavoriteService,
    getRestaurantNearbyService,
    getRestaurantRecentService,
} from '@src/network/services/restaurantServices';
import { RestaurantActions, } from '@src/redux/toolkit/actions/restaurantActions';
import useThemeColors from '@src/themes/useThemeColors';
import { isEmptyOrUndefined } from '@src/utils';

import HomeHeader from './component/HomeHeader';
import LoadingPlaceHolder from './component/LoadingPlaceHolder';

const HomeScreen = () => {
    const Dimens = useDimens();
    const styles = stylesF(Dimens);

    const { themeColors } = useThemeColors();
    const { t } = useTranslation();
    const dispatch = useDispatch();

    const { lat, lng } = useAppSelector((state) => state.locationReducer);

    const isUserLoggedIn = useIsUserLoggedIn();

    const [nearbyRestaurantData,  setNearbyRestaurantData] = useState<Array<RestaurantNearbyItemModel>>([]);
    const [recentRestaurantData,  setRecentRestaurantData] = useState<Array<RestaurantRecentItemModel>>([]);
    const [favoriteRestaurantData,  setFavoriteRestaurantData] = useState<Array<RestaurantFavoriteItemModel>>([]);

    const [refreshing,  setRefreshing, hideRefreshing] = useBoolean(false);
    const [loadingPlaceHolder,  setLoadingPlaceHolder, hideLoadingPlaceHolder] = useBoolean(true);

    const { callApi: getRestaurantNearby } = useCallAPI(
            getRestaurantNearbyService,
            undefined,
            useCallback((data: any) => {
                !isUserLoggedIn && hideRefreshing();
                !isUserLoggedIn && hideLoadingPlaceHolder();
                setNearbyRestaurantData(data.data);
            }, [hideLoadingPlaceHolder, hideRefreshing, isUserLoggedIn])
    );

    const { callApi: getRestaurantRecent } = useCallAPI(
            getRestaurantRecentService,
            undefined,
            useCallback((data: any) => {
                hideRefreshing();
                hideLoadingPlaceHolder();
                setRecentRestaurantData(data.data);
            }, [hideLoadingPlaceHolder, hideRefreshing])
    );

    const { callApi: getRestaurantFavorite } = useCallAPI(
            getRestaurantFavoriteService,
            undefined,
            useCallback((data: any) => {
                hideRefreshing();
                hideLoadingPlaceHolder();
                setFavoriteRestaurantData(data.data);
            }, [hideLoadingPlaceHolder, hideRefreshing])
    );

    const loadHomeData = useCallback(() => {
        getRestaurantNearby({
            page: 1,
            limit: 10,
            lat: lat,
            lng: lng,
            radius: DEFAULT_DISTANCE,
            order_by: RESTAURANT_SORT_TYPE.DISTANCE,
        });

        if (isUserLoggedIn) {
            getRestaurantRecent({
                page: 1,
                limit: 5,
                lat: lat,
                lng: lng,
                radius: DEFAULT_DISTANCE,
                order_by: RESTAURANT_SORT_TYPE.DISTANCE,
            });
            getRestaurantFavorite({
                page: 1,
                limit: 5,
                lat: lat,
                lng: lng,
                radius: DEFAULT_DISTANCE,
                order_by: RESTAURANT_SORT_TYPE.DISTANCE,
            });
        }
    }, [getRestaurantFavorite, getRestaurantNearby, getRestaurantRecent, isUserLoggedIn, lat, lng]);

    const handleRefresh = useCallback(() => {
        setRefreshing();
        loadHomeData();
    }, [loadHomeData, setRefreshing]);

    const handleNavToDetail = useCallback((item: RestaurantNearbyItemModel | RestaurantRecentItemModel | RestaurantFavoriteItemModel) => {
        dispatch(RestaurantActions.setExitScreen(1));
        dispatch(RestaurantActions.updateRestaurantDetail(item));
        NavigationService.navigate(SCREENS.MENU_TAB_SCREEN, { screen: SCREENS.RESTAURANT_DETAIL_SCREEN } );
    }, [dispatch]);

    useEffect(() => {
        setLoadingPlaceHolder();
        loadHomeData();
    }, [isUserLoggedIn, lng, lat, setLoadingPlaceHolder, loadHomeData]);

    const renderEmpty = useMemo(() => (
        <TextComponent style={{ ...styles.emptyText, color: themeColors.color_common_description_text }}>
            {t('text_no_result')}
        </TextComponent>
    ), [styles.emptyText, t, themeColors.color_common_description_text]);

    const renderTitle = useCallback(( text : string, type: number) => {
        let onPress = undefined;

        switch (type) {
            case 1:
                onPress = () => NavigationService.navigate(SCREENS.MENU_TAB_SCREEN);
                break;
            case 2:
                onPress = () => NavigationService.navigate(SCREENS.RESTAURANT_RECENT_SCREEN);
                break;
            case 3:
                onPress = () => NavigationService.navigate(SCREENS.RESTAURANT_FAVORITE_SCREEN);
                break;
        }

        return (
            <TouchableComponent
                onPress={onPress}
                style={styles.titleContainer}
            >
                <TextComponent style={[styles.titleText, { color: themeColors.color_text_2 }]}>
                    {`${text} `}
                </TextComponent>
                <ArrowRightIcon
                    width={Dimens.W_12}
                    height={Dimens.W_12}
                    stroke={themeColors.color_text_2}
                />
            </TouchableComponent>
        );
    }, [Dimens.W_12, styles.titleContainer, styles.titleText, themeColors.color_text_2]);

    const renderItem = useCallback(({ item } : {item: RestaurantNearbyItemModel | RestaurantRecentItemModel | RestaurantFavoriteItemModel}) => (
        <TouchableComponent
            onPress={() => handleNavToDetail(item)}
            style={styles.itemContainer}
        >
            <View>
                <ImageComponent
                    resizeMode='cover'
                    defaultImage={Images.image_placeholder}
                    source={{ uri: item.photo }}
                    style={styles.itemImage}
                />
                <View style={styles.iconContainer}>
                    {item.favoriet_friet && (
                        <ImageComponent
                            resizeMode='stretch'
                            source={Images.icon_friet}
                            style={styles.iconStyle}
                        />
                    )}

                    {item.kokette_kroket && (
                        <ImageComponent
                            resizeMode='stretch'
                            source={Images.icon_kroket}
                            style={[styles.iconStyle, { marginLeft: Dimens.W_4 }]}
                        />
                    )}

                </View>
            </View>
            <TextComponent
                numberOfLines={1}
                style={[styles.resName, { color: themeColors.color_text_2 }]}
            >
                {item?.setting_generals?.title}
            </TextComponent>
            <TextComponent style={{ ...styles.distance, color: themeColors.color_common_description_text }}>
                {`${(Number(item.distance) / 1000).toFixed(1)} ${DEFAULT_DISTANCE_UNIT.toLowerCase()}`}
            </TextComponent>
        </TouchableComponent>
    ), [Dimens.W_4, handleNavToDetail, styles.distance, styles.iconContainer, styles.iconStyle, styles.itemContainer, styles.itemImage, styles.resName, themeColors.color_common_description_text, themeColors.color_text_2]);

    const renderContent = useMemo(() => (
        <View style={styles.listMainContainer}>
            <FlatListComponent
                horizontal
                data={nearbyRestaurantData}
                renderItem={renderItem}
                ListEmptyComponent={renderEmpty}
                contentContainerStyle={styles.flatListContent}
                style={styles.flatList}
            />

            {isUserLoggedIn && (
                <>
                    {!isEmptyOrUndefined(recentRestaurantData) && (
                        <>
                            {renderTitle(t('text_recent_ordered'), 2)}
                            <FlatListComponent
                                horizontal
                                data={recentRestaurantData}
                                renderItem={renderItem}
                                contentContainerStyle={styles.flatListContent}
                                style={styles.flatList}
                            />
                        </>
                    )}

                    {!isEmptyOrUndefined(favoriteRestaurantData) && (
                        <>
                            {renderTitle(t('text_favorites'), 3)}
                            <FlatListComponent
                                horizontal
                                data={favoriteRestaurantData}
                                renderItem={renderItem}
                                contentContainerStyle={styles.flatListContent}
                                style={styles.flatList}
                            />
                        </>
                    )}
                </>
            )}
        </View>
    ), [favoriteRestaurantData, isUserLoggedIn, nearbyRestaurantData, recentRestaurantData, renderEmpty, renderItem, renderTitle, styles.flatList, styles.flatListContent, styles.listMainContainer, t]);

    const renderHeader = useMemo(() => (
        <HomeHeader/>
    ), []);
    return (
        <View style={{ flex: 1, backgroundColor: themeColors.color_app_background }}>
            <ScrollViewComponent
                refreshControl={
                    <RefreshControlComponent
                        progressViewOffset={Dimens.H_76 + Dimens.COMMON_HEADER_PADDING}
                        refreshing={refreshing}
                        onRefresh={handleRefresh}
                    />
                }
            >
                <View style={styles.container}>
                    {renderTitle(t('text_recent_you'), 1)}
                    {
                        loadingPlaceHolder ? (<LoadingPlaceHolder/>) : (renderContent)
                    }
                </View>
            </ScrollViewComponent>
            {renderHeader}
        </View>
    );
};

export default HomeScreen;

const stylesF = (Dimens: DimensType) => StyleSheet.create({
    flatList: { flexGrow: 0, overflow: 'visible' },
    flatListContent: { paddingHorizontal: Dimens.W_18, flexGrow: 1 },
    listMainContainer: { flex: 1, paddingBottom: Dimens.H_24 },
    distance: { fontSize: Dimens.FONT_14, fontWeight: '400' },
    resName: {
        fontSize: Dimens.FONT_16,
        fontWeight: '700',
        marginTop: Dimens.H_4,
        marginBottom: Dimens.H_6,
    },
    iconStyle: {
        width: Dimens.W_22,
        height: Dimens.W_22 * 1.4,
        borderRadius: 0,
    },
    iconContainer: {
        position: 'absolute',
        top: -Dimens.W_35 / 4,
        flexDirection: 'row',
        left: Dimens.W_4,
    },
    itemImage: {
        width: Dimens.W_160 / 1.4,
        height: Dimens.W_160 / 1.4,
        borderRadius: Dimens.W_5,
    },
    itemContainer: { marginRight: Dimens.W_36, paddingTop: Dimens.W_35 / 4 },
    titleText: { fontSize: Dimens.FONT_20, fontWeight: '700' },
    titleContainer: {
        flexDirection: 'row',
        alignSelf: 'flex-start',
        alignItems: 'center',
        marginTop: Dimens.H_24,
        marginBottom: Dimens.H_14,
        paddingHorizontal: Dimens.W_18,
    },
    emptyText: {
        fontSize: Dimens.FONT_14,
        width: '100%',
        textAlign: 'center',
        marginVertical: Dimens.H_50,
    },
    container: { flex: 1, paddingTop: Dimens.H_70 + Dimens.COMMON_HEADER_PADDING },
});