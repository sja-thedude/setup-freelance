import React, {
    useCallback,
    useEffect,
    useRef,
} from 'react';

import {
    StyleSheet,
    View,
} from 'react-native';
import { useDispatch } from 'react-redux';

import { Images } from '@src/assets/images';
import { BowIcon } from '@src/assets/svg';
import FlatListComponent from '@src/components/FlatListComponent';
import BackButton from '@src/components/header/BackButton';
import HeaderComponent from '@src/components/header/HeaderComponent';
import ImageComponent from '@src/components/ImageComponent';
import RefreshControlComponent from '@src/components/RefreshControlComponent';
import TextComponent from '@src/components/TextComponent';
import TouchableComponent from '@src/components/TouchableComponent';
import { Colors } from '@src/configs';
import {
    DEFAULT_DISTANCE,
    DEFAULT_DISTANCE_UNIT,
    PAGE_SIZE,
    RESTAURANT_EXTRA_TYPE,
    RESTAURANT_SORT_TYPE,
} from '@src/configs/constants';
import { useAppSelector } from '@src/hooks';
import useDimens, { DimensType } from '@src/hooks/useDimens';
import { SCREENS } from '@src/navigation/config/screenName';
import NavigationService from '@src/navigation/NavigationService';
import { RestaurantRecentItemModel, } from '@src/network/dataModels/RestaurantRecentItemModel';
import {
    getRecentRestaurant,
    RestaurantActions,
} from '@src/redux/toolkit/actions/restaurantActions';
import useThemeColors from '@src/themes/useThemeColors';
import { getTimeByTimeZone } from '@src/utils/dateTimeUtil';
import { useTranslation } from 'react-i18next';

const RestaurantRecentScreen = () => {
    const Dimens = useDimens();
    const styles = stylesF(Dimens);
    const { t } = useTranslation();
    const { themeColors } = useThemeColors();
    const dispatch = useDispatch();

    const { lat, lng } = useAppSelector((state) => state.locationReducer);

    const recentRestaurantData = useAppSelector((state) => state.restaurantReducer.recentRestaurant.data);
    const refreshing = useAppSelector((state) => state.restaurantReducer.recentRestaurant.refreshing);
    const loading = useAppSelector((state) => state.restaurantReducer.recentRestaurant.loading);
    const canLoadMore = useAppSelector((state) => state.restaurantReducer.recentRestaurant.canLoadMore);

    const params = useRef({
        page: 1,
        limit: PAGE_SIZE,
        lat: lat,
        lng: lng,
        radius: DEFAULT_DISTANCE,
        order_by: RESTAURANT_SORT_TYPE.DISTANCE,
    });

    const getRestaurantRecent = useCallback((loading: boolean, refreshing: boolean) => {
        dispatch(getRecentRestaurant(loading, refreshing, params.current));
    }, [dispatch]);

    const handleRefresh = useCallback(() => {
        params.current = { ...params.current, page: 1 };
        getRestaurantRecent(false, true);
    }, [getRestaurantRecent]);

    const onEndReached = useCallback(() => {
        if (!loading && canLoadMore) {
            params.current = { ...params.current, page: params.current.page + 1 };
            getRestaurantRecent(false, false);
        }
    }, [canLoadMore, getRestaurantRecent, loading]);

    useEffect(() => {
        getRestaurantRecent(true, false);

        return () => {
            dispatch(RestaurantActions.clearRecentRestaurant());
        };
    }, [dispatch, getRestaurantRecent]);

    const handleNavToDetail = useCallback((item: RestaurantRecentItemModel ) => {
        dispatch(RestaurantActions.setExitScreen(2));
        dispatch(RestaurantActions.updateRestaurantDetail(item));
        NavigationService.navigate(SCREENS.RESTAURANT_DETAIL_SCREEN);
    }, [dispatch]);

    const renderItem = useCallback(({ item } : {item: RestaurantRecentItemModel}) => {
        const showLoyalty = item.extras.some((ex) => ex.active && ex.type === RESTAURANT_EXTRA_TYPE.CUSTOMER_CARD);

        return (
            <TouchableComponent
                onPress={() => handleNavToDetail(item)}
                style={[styles.itemContainer, { backgroundColor:  themeColors.color_card_background }]}
            >
                <View style={styles.imageContainer}>
                    <ImageComponent
                        resizeMode='cover'
                        defaultImage={Images.image_placeholder}
                        source={{ uri: item.photo }}
                        style={styles.image}
                    />
                    <View >
                        <TextComponent style={[styles.restaurantName, { color: themeColors.color_text_2 }]}>
                            {item.setting_generals?.title}
                        </TextComponent>
                        <TextComponent style={{ ...styles.resFavorite, color: themeColors.color_text_2 }}>
                            {`${getTimeByTimeZone(item.latest_order.created_at, 'YYYY-MM-DD hh:mm:ss', `DD/MM/YYYY [${t('text_date_order_history')}] hh:mm A`)}`}
                        </TextComponent>
                    </View>
                </View>

                <View style={styles.iconContainer}>
                    <TextComponent style={{ ...styles.distanceText, color: themeColors.color_common_description_text }}>
                        {`${(Number(item.distance) / 1000).toFixed(1)} ${DEFAULT_DISTANCE_UNIT.toLowerCase()}`}
                    </TextComponent>
                    <View style={styles.iconWrapper}>
                        {item.favoriet_friet && (
                            <ImageComponent
                                resizeMode='stretch'
                                source={Images.icon_friet}
                                style={styles.iconFavorite}
                            />
                        )}

                        {item.kokette_kroket && (
                            <ImageComponent
                                resizeMode='stretch'
                                source={Images.icon_kroket}
                                style={styles.iconFavorite}
                            />
                        )}
                        {showLoyalty && (
                            <BowIcon
                                stroke={themeColors.color_primary}
                                width={Dimens.W_12}
                                height={Dimens.W_16}
                            />
                        )}
                    </View>
                </View>
            </TouchableComponent>
        );
    }, [Dimens.W_12, Dimens.W_16, handleNavToDetail, styles.distanceText, styles.iconContainer, styles.iconFavorite, styles.iconWrapper, styles.image, styles.imageContainer, styles.itemContainer, styles.resFavorite, styles.restaurantName, t, themeColors.color_card_background, themeColors.color_common_description_text, themeColors.color_primary, themeColors.color_text_2]);

    return (
        <View style={{ flex: 1, backgroundColor: themeColors.color_app_background }}>
            <HeaderComponent >
                <View style={styles.header}>
                    <BackButton/>
                    <TextComponent style={styles.headerText}>
                        {t('text_recent_ordered')}
                    </TextComponent>
                </View>
            </HeaderComponent>

            <View style={{ flex: 1 }}>
                <FlatListComponent
                    contentContainerStyle={styles.flatList}
                    data={recentRestaurantData}
                    hasNext={canLoadMore}
                    renderItem={renderItem}
                    onEndReachedThreshold={0}
                    onEndReached={onEndReached}
                    refreshControl={
                        <RefreshControlComponent
                            refreshing={refreshing}
                            onRefresh={handleRefresh}
                        />
                    }
                />
            </View>

        </View>
    );
};

export default RestaurantRecentScreen;

const stylesF = (Dimens: DimensType) => StyleSheet.create({
    flatList: { paddingTop: Dimens.H_22, paddingHorizontal: Dimens.W_12 },
    header: { flexDirection: 'row', alignItems: 'center' },
    iconFavorite: {
        width: Dimens.W_17,
        height: Dimens.W_17 * 1.4,
        borderRadius: 0,
        marginRight: 3,
    },
    iconWrapper: { flexDirection: 'row', alignItems: 'center', justifyContent: 'flex-end' },
    distanceText: { fontSize: Dimens.FONT_13 },
    iconContainer: { justifyContent: 'space-between', alignItems: 'flex-end' },
    resFavorite: { fontSize: Dimens.FONT_13 },
    restaurantName: { fontSize: Dimens.FONT_16, fontWeight: '700' },
    image: {
        width: Dimens.W_100 / 1.6,
        height: Dimens.W_100 / 1.6,
        borderRadius: Dimens.W_5,
        marginRight: Dimens.W_8,
    },
    imageContainer: { flexDirection: 'row', flex: 1 },
    itemContainer: {
        flexDirection: 'row',
        padding: Dimens.W_8,
        marginBottom: Dimens.H_18,
        borderRadius: Dimens.W_5,
    },
    headerText: {
        color: Colors.COLOR_WHITE,
        fontSize: Dimens.FONT_26,
        fontWeight: '700',
    },
});