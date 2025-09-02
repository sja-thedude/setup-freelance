import React, {
    useCallback,
    useEffect,
    useMemo,
    useRef,
    useState,
} from 'react';

import { uniqBy } from 'lodash';
import { useTranslation } from 'react-i18next';
import {
    Animated as RNAnimated,
    StyleSheet,
} from 'react-native';
import {
    TabBarProps,
    TabView,
} from 'react-native-tab-view';
import { useDispatch } from 'react-redux';

import AnimatedTextComponent from '@src/components/AnimatedTextComponent';
import TabBarComponent from '@src/components/TabBarComponent';
import TextComponent from '@src/components/TextComponent';
import { Colors } from '@src/configs';
import {
    DEFAULT_DISTANCE,
    LARGE_PAGE_SIZE,
    ORDER_TYPE,
    RESTAURANT_SORT_TYPE,
} from '@src/configs/constants';
import { useAppSelector } from '@src/hooks';
import useDimens, { DimensType } from '@src/hooks/useDimens';
import { getAllRestaurantAction, } from '@src/redux/toolkit/actions/restaurantActions';
import useThemeColors from '@src/themes/useThemeColors';
import { convertHexToRGBA } from '@src/utils';
import { getStatusBarHeight } from '@src/utils/iPhoneXHelper';

import AllRestaurantHeader from './component/AllRestaurantHeader';
import AllRestaurantTab from './component/AllRestaurantTab';
import CategoryRestaurantTab from './component/CategoryRestaurantTab';
import CustomIndicator from './component/CustomIndicator';

const AllRestaurantScreen = () => {
    const Dimens = useDimens();
    const styles = stylesF(Dimens);

    const { themeColors } = useThemeColors();
    const dispatch = useDispatch();
    const { t } = useTranslation();

    const animatedValue = useRef(new RNAnimated.Value(0)).current;

    const restaurantData = useAppSelector((state) => state.restaurantReducer.allRestaurant.data);
    const refreshing = useAppSelector((state) => state.restaurantReducer.allRestaurant.refreshing);
    const loading = useAppSelector((state) => state.restaurantReducer.allRestaurant.loading);
    const canLoadMore = useAppSelector((state) => state.restaurantReducer.allRestaurant.canLoadMore);
    const { lat, lng } = useAppSelector((state) => state.locationReducer);

    const defaultRoute = useMemo(() => ({
        key: 'all',
        title: t('text_all')
    }), [t]);

    const [currentOrderType, setCurrentOrderType] = useState(ORDER_TYPE.TAKE_AWAY);
    const [index, setIndex] = useState(0);
    const [routes, setRoutes] = React.useState<Array<{key: string, title: string}>>([defaultRoute]);

    const [headerHeightValue, setHeaderHeightValue] = useState(0);

    const getCategories = useCallback(() => {
        const categories = restaurantData.map((res) => res.categories).flat();
        const uniqCategories = uniqBy(categories, 'id');

        const routes = uniqCategories.map((category) => ({
            key: `${category.id}`,
            title: category.name
        })).sort((a,b) => {
            if (a.title < b.title) {
                return -1;
            }
            if (a.title > b.title) {
                return 1;
            }
            return 0;
        });
        setRoutes([defaultRoute, ...routes]);
    }, [defaultRoute, restaurantData]);

    useEffect(() => {
        if (restaurantData.length) {
            getCategories();
        }
    }, [getCategories, restaurantData]);

    const params = useRef({
        page: 1,
        limit: LARGE_PAGE_SIZE,
        lat: lat,
        lng: lng,
        radius: DEFAULT_DISTANCE,
        sort_by: 'asc',
        order_by: RESTAURANT_SORT_TYPE.DISTANCE,
        keyword: '',
        open_type: ORDER_TYPE.TAKE_AWAY,
    });

    const getRestaurantData = useCallback((loading: boolean, refreshing: boolean, resetData: boolean) => {
        dispatch(getAllRestaurantAction(loading, refreshing, resetData, params.current));
        if (resetData) {
            setIndex(0);
        }
    }, [dispatch]);

    const handleRefresh = useCallback(() => {
        params.current = { ...params.current, page: 1 };
        getRestaurantData(false, true, false);
    }, [getRestaurantData]);

    const onEndReached = useCallback(() => {
        if (!loading && canLoadMore) {
            params.current = { ...params.current, page: params.current.page + 1 };
            getRestaurantData(false, false, false);
        }
    }, [canLoadMore, getRestaurantData, loading]);

    const handleSearch = useCallback((text: string) => {
        params.current = { ...params.current, page: 1, keyword: text };
        getRestaurantData(true, false, true);
    }, [getRestaurantData]);

    const handleSort = useCallback((sortType: string) => {
        setTimeout(() => {
            params.current = { ...params.current, page: 1, order_by: sortType };
            getRestaurantData(true, false, true);
        }, 500);
    }, [getRestaurantData]);

    const handleFilterType = useCallback((orderType: number, sortType: string) => {
        setTimeout(() => {
            params.current = { ...params.current, page: 1, order_by: sortType, open_type: orderType };
            getRestaurantData(true, false, true);
            setCurrentOrderType(orderType);
        }, 500);
    }, [getRestaurantData]);

    const layoutTranslateY = useMemo(() => animatedValue.interpolate({
        inputRange: [0, 100],
        outputRange: [0, -headerHeightValue + getStatusBarHeight()],
        extrapolate: 'clamp',
    }), [animatedValue, headerHeightValue]);

    const tabColorBackground = useMemo(() => animatedValue.interpolate({
        inputRange: [0, 100],
        outputRange: [convertHexToRGBA(themeColors.color_primary, 0), convertHexToRGBA(themeColors.color_primary, 1)],
        extrapolate: 'clamp',
    }), [animatedValue, themeColors.color_primary]);

    const tabTextFocusedColor = useMemo(() => animatedValue.interpolate({
        inputRange: [0, 100],
        outputRange: [convertHexToRGBA(themeColors.color_primary, 1), convertHexToRGBA(Colors.COLOR_WHITE, 1)],
        extrapolate: 'clamp',
    }), [animatedValue, themeColors.color_primary]);

    const tabTextColor = useMemo(() => animatedValue.interpolate({
        inputRange: [0, 100],
        outputRange: [convertHexToRGBA(themeColors.color_text_2, 1), convertHexToRGBA(Colors.COLOR_WHITE, 0.5)],
        extrapolate: 'clamp',
    }), [animatedValue, themeColors.color_text_2]);

    const tabRadius = useMemo(() => animatedValue.interpolate({
        inputRange: [0, 100],
        outputRange: [0, Dimens.HEADER_BORDER_RADIUS],
        extrapolate: 'clamp',
    }), [Dimens.HEADER_BORDER_RADIUS, animatedValue]);

    useEffect(() => {
        params.current = { ...params.current, page: 1, lat: lat, lng: lng };
        getRestaurantData(true, false, true);
    }, [getRestaurantData, lat, lng]);

    const renderScene = useCallback(({ route }: {route: any}) => {
        switch (route.key) {
            case 'all':
                return (
                    <AllRestaurantTab
                        animatedValue={animatedValue}
                        canLoadMore={canLoadMore}
                        currentOrderType={currentOrderType}
                        handleRefresh={handleRefresh}
                        onEndReached={onEndReached}
                        refreshing={refreshing}
                        restaurantData={restaurantData}
                    />
                );

            default:
            {
                const tabData = restaurantData.filter((res) => res.categories.find((i) => i.id == route.key));
                return (
                    <CategoryRestaurantTab
                        animatedValue={animatedValue}
                        currentOrderType={currentOrderType}
                        handleRefresh={handleRefresh}
                        refreshing={refreshing}
                        restaurantData={tabData}
                    />
                );
            }
        }
    }, [animatedValue, canLoadMore, currentOrderType, handleRefresh, onEndReached, refreshing, restaurantData]);

    const renderTabLabel = useCallback(({ route, focused } : {route: any, focused: boolean}) => (
        <AnimatedTextComponent
            style={{ ...styles.tabText,  color: focused ? tabTextFocusedColor : tabTextColor }}
        >
            {route.title.toUpperCase()}
        </AnimatedTextComponent>
    ), [styles.tabText, tabTextColor, tabTextFocusedColor]);

    const renderTabBar = useCallback((props: TabBarProps<any>) => (
        <TabBarComponent
            {...props}
            scrollEnabled
            tabContainerStyle={{
                ...styles.tabContainer,
                backgroundColor: tabColorBackground,
                borderBottomRightRadius: tabRadius,
                borderBottomLeftRadius: tabRadius
            }}
            style={{ backgroundColor: 'transparent' }}
            tabStyle={styles.tabStyle}
            renderLabel={renderTabLabel}
            renderIndicator={(props) => (
                <CustomIndicator
                    {...props}
                    animatedValue={animatedValue}
                />
            )}
        />
    ), [animatedValue, renderTabLabel, styles.tabContainer, styles.tabStyle, tabColorBackground, tabRadius]);

    const renderEmpty = useCallback(() => (
        <TextComponent style={[styles.emptyText, { color: themeColors.color_common_description_text }]}>
            {t('tab_search_no_item')}
        </TextComponent>
    ), [styles.emptyText, t, themeColors.color_common_description_text]);

    return (
        <RNAnimated.View style={
            [styles.mainContainer,
                {
                    backgroundColor: themeColors.color_app_background,
                    transform: [{ translateY: layoutTranslateY }],
                    marginBottom: -headerHeightValue + getStatusBarHeight()
                }
            ]
        }
        >
            <AllRestaurantHeader
                setHeaderHeightRef={(h: any) => {
                    setHeaderHeightValue(h);
                }}
                animatedValue={animatedValue}
                onSearch={handleSearch}
                onSelectSort={handleSort}
                onSelectType={handleFilterType}
            />

            {restaurantData.length === 0 ? (
                !loading ? renderEmpty() : null
            ) : (
                <TabView
                    keyboardDismissMode='none'
                    key={routes.length}
                    lazy
                    navigationState={{ index, routes }}
                    onIndexChange={setIndex}
                    renderTabBar={renderTabBar}
                    renderScene={renderScene}
                />
            )}

        </RNAnimated.View>
    );
};

export default AllRestaurantScreen;

const stylesF = (Dimens: DimensType) => StyleSheet.create({
    tabStyle: {
        minHeight: 0,
        padding: 0,
        paddingVertical: Dimens.W_6,
        marginRight: Dimens.W_14,
    },
    tabContainer: { paddingVertical: Dimens.H_6, paddingHorizontal: Dimens.W_10 },
    emptyText: {
        fontSize: Dimens.FONT_16,
        textAlign: 'center',
        marginTop: Dimens.H_40,
    },
    tabText: { fontSize: Dimens.FONT_14, fontWeight: '500' },
    mainContainer: { flex: 1 },
});