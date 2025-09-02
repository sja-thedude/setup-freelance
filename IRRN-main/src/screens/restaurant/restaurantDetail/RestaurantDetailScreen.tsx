import React, {
    memo,
    useCallback,
    useEffect,
    useMemo,
    useRef,
    useState,
} from 'react';

import { sortBy } from 'lodash';
import debounce from 'lodash/debounce';
import { useTranslation } from 'react-i18next';
import {
    Animated as RNAnimated,
    InteractionManager,
    ScrollView,
    StyleSheet,
    View,
} from 'react-native';
import { useDispatch } from 'react-redux';

import {
    useFocusEffect,
    useIsFocused,
    useNavigation,
    useRoute,
} from '@react-navigation/native';
import CategoryList from '@screens/restaurant/restaurantDetail/component/CategoryList';
import DetailRestaurantHeader
    from '@screens/restaurant/restaurantDetail/component/DetailRestaurantHeader';
import HolidayException from '@screens/restaurant/restaurantDetail/component/HolidayException';
import ModalHoliday from '@screens/restaurant/restaurantDetail/component/ModalHoliday';
import { Constants } from '@src/configs';
import {
    LARGE_PAGE_SIZE,
    ORDER_TYPE,
} from '@src/configs/constants';
import { useAppSelector } from '@src/hooks';
import useCallAPI from '@src/hooks/useCallAPI';
import useDimens, { DimensType } from '@src/hooks/useDimens';
import { SCREENS } from '@src/navigation/config/screenName';
import { RestaurantDetailScreenProps } from '@src/navigation/NavigationRouteProps';
import {
    Product,
    ProductSectionModel,
} from '@src/network/dataModels/ProductSectionModel';
import { getDetailGroupService } from '@src/network/services/restaurantServices';
import {
    getProductsAction,
    ProductActions,
} from '@src/redux/toolkit/actions/productActions';
import { StorageActions } from '@src/redux/toolkit/actions/storageActions';
import { ProductSectionModelInterFace } from '@src/redux/toolkit/slices/productSlice';
import useThemeColors from '@src/themes/useThemeColors';
import {
    getOrderSortData,
    isTemplateOrGroupApp,
} from '@src/utils';

import CouponRestaurant from './component/CouponRestaurant';
import ListFavoriteProducts from './component/ListFavoriteProducts';
import ListProducts from './component/ListProducts';

export const PRODUCT_LIST_ITEM_TYPE = {
    HEADER: 1,
    ROW: 2
};

export interface ItemHeader extends ProductSectionModel {
    index: number,
    tabIndex: number,
    sortType: number,
    itemType: number,
    title: string,
}

export interface ItemRow extends Product {
    itemType: number,
    isEmpty?: boolean,
    index: number,
    tabIndex: number,
}

const RestaurantDetailScreen = () => {
    const Dimens = useDimens();
    const styles = stylesF(Dimens);

    const { themeColors } = useThemeColors();
    const dispatch = useDispatch();
    const { t } = useTranslation();

    const isFocused = useIsFocused();

    const { params: screenParams } = useRoute<RestaurantDetailScreenProps>();
    const { setParams } = useNavigation<any>();
    const navigation = useNavigation<any>();

    const listYPositions = useRef<any>({});
    const refCouponRestaurant = useRef<any>({});

    const animatedValue = useRef(new RNAnimated.Value(0)).current;
    const listRef = useRef <ScrollView>(null);
    const listFavoriteRef = useRef <ScrollView>(null);
    const scrollByUser = useRef(false);
    const tabRef = useRef<any>();

    const restaurantData = useAppSelector((state) => state.restaurantReducer.restaurantDetail.data);
    const screenToQuit = useAppSelector((state) => state.restaurantReducer.screenToQuit);
    const orderType = useAppSelector((state) => state.storageReducer.cartProducts.type);
    const groupData = useAppSelector((state) => state.storageReducer.cartProducts.groupFilter.groupData);

    const productOriginData = useRef<Array<ProductSectionModelInterFace>>([]);
    const [refreshing, setRefreshing] = useState(false);
    const [loading, setLoading] = useState(false);
    const [canLoadMore, setCanLoadMore] = useState(false);

    const [showFavorite, setShowFavorite] = useState(false);
    const [isSearch, setIsSearch] = useState<boolean>(false);

    const [listData, setListData] = useState<Array<ItemHeader | ItemRow>>([]);
    const [listSearchResultData, setListSearchResultData] = useState<Array<ItemHeader | ItemRow>>([]);

    const ORDER_SORT_DATA = useMemo(() => getOrderSortData(t), [t]);

    const params = useRef({
        page: 1,
        limit: LARGE_PAGE_SIZE,
        keyword: '',
        workspace_id: restaurantData.id,
    });

    const convertData = useCallback((originData: Array<ProductSectionModelInterFace>) => {
        const convertedData: any = originData.map((item, index) => (
            [
                {
                    ...item,
                    index: index,
                    tabIndex: index,
                    sortType: item.sortType || ORDER_SORT_DATA[0].type,
                    itemType: PRODUCT_LIST_ITEM_TYPE.HEADER,
                    title: item.name,
                },
                    item?.products?.length ? [...item.products.map((product) => ({ ...product, index: index, tabIndex: index, itemType: PRODUCT_LIST_ITEM_TYPE.ROW }))] : [{ isEmpty: true, index: index, tabIndex: index, itemType: PRODUCT_LIST_ITEM_TYPE.ROW }],
            ]
        )).flat(2).map((i, idx ) => ({
            ...i,
            index: idx
        }));

        return convertedData;
    }, [ORDER_SORT_DATA]);

    const updateFavoriteProductsData = useCallback((originData: Array<ProductSectionModelInterFace>) => {
        const convertedData: any = convertData(originData);
        const ids: any = [];
        convertedData.map((d: ItemHeader | ItemRow) => {
            if (d.itemType === PRODUCT_LIST_ITEM_TYPE.ROW && (d as ItemRow).liked) {
                ids.push(d.id);
            }
        });

        dispatch(ProductActions.addMultiFavoriteProducts(ids));
    }, [convertData, dispatch]);

    const updateListData = useCallback((originData: Array<ProductSectionModelInterFace>) => {
        const convertedData: any = convertData(originData);
        setListData(convertedData);
    }, [convertData]);

    const updateProductsOriginData = useCallback((data: Array<ProductSectionModelInterFace>) => {
        productOriginData.current = data;
    }, []);

    const scrollToTab = useCallback((tabIndex: number) => {
        tabRef.current?.setCurrentTabIndex(tabIndex);
    }, []);

    const handleFilterFavorite = useCallback((isFavorite: boolean) => {
        setShowFavorite(isFavorite);
        if (isFavorite) {
            scrollByUser.current = false;
            listFavoriteRef.current?.scrollTo({ x: 0, y: 0, animated: false });
        }
    }, []);

    const { callApi: getDetailGroup } = useCallAPI(
            getDetailGroupService
    );

    const getProductsData = useCallback((loading: boolean, refreshing: boolean) => {
        const callback = (success: boolean, data: any) => {
            setLoading(false);
            setRefreshing(false);

            if (success) {
                setCanLoadMore(data.data.length === Constants.LARGE_PAGE_SIZE);

                const newData = data.current_page === 1 ? data.data : [...productOriginData.current].concat(data.data);

                updateProductsOriginData(newData);
                updateListData(newData);
                updateFavoriteProductsData(newData);
            } else {
                setCanLoadMore(false);
            }
        };

        setLoading(loading);
        setRefreshing(refreshing);

        dispatch(getProductsAction(loading, refreshing, params.current, callback));
    }, [dispatch, updateFavoriteProductsData, updateListData, updateProductsOriginData]);

    const handleRefresh = useCallback(() => {
        params.current = { ...params.current, page: 1 };
        if (orderType === ORDER_TYPE.GROUP_ORDER && groupData?.workspace_id === restaurantData.id) {
            getDetailGroup({
                group_id: groupData?.id
            }).then((result) => {
                if (result.success) {
                    // update group filter
                    dispatch(StorageActions.setStorageGroupFilter(
                            {
                                data: result.data,
                                filterByDeliverable: result.data.type === ORDER_TYPE.DELIVERY ? true : false,
                            }
                    ));
                }
                getProductsData(false, true);
            });
        } else {
            getProductsData(false, true);
        }
        refCouponRestaurant.current?.onRefresh();
    }, [dispatch, getDetailGroup, getProductsData, groupData?.id, groupData?.workspace_id, orderType, restaurantData.id]);

    // eslint-disable-next-line react-hooks/exhaustive-deps
    const onEndReached = useCallback(debounce(() => {
        if (!loading && canLoadMore) {
            params.current = { ...params.current, page: params.current.page + 1 };
            getProductsData(false, false);
        }
    }, 200), [canLoadMore, getProductsData, loading]);

    const handleSortProduct = useCallback((section: ItemHeader, sortType: number) => {
        if (sortType === section.sortType || section.products.length === 0) {
            return;
        }

        const startIndex = section.index + 1;
        const splitLength = section.products.length;
        const endIndex = startIndex + splitLength;

        const splitData = listData.slice(startIndex, endIndex);

        let sortedSplitData: any[] = [];

        switch (sortType) {
            case ORDER_SORT_DATA[0].type:
            {
                sortedSplitData = sortBy(splitData, ['order']);
                break;
            }

            case ORDER_SORT_DATA[1].type:
            {
                sortedSplitData = sortBy(splitData, (o: ItemRow) => Number(o.price));
                break;
            }

            case ORDER_SORT_DATA[2].type:
            {
                sortedSplitData = sortBy(splitData, ['name']);
                break;
            }

            default:
                break;
        }

        const newData = [...listData];

        newData.splice(startIndex - 1, splitLength + 1, ...[{ ...section, sortType: sortType },...sortedSplitData]);

        setListData(newData);
    }, [ORDER_SORT_DATA, listData]);

    const handleSelectTab = useCallback((selectedTabIndex: number, _sectionIndex: number) => {
        scrollByUser.current = false;
        scrollToTab(selectedTabIndex);
        if (listData.length) {
            listRef.current?.scrollTo({ x: 0, y: listYPositions.current[selectedTabIndex], animated: true });
        }
    }, [listData.length, scrollToTab]);

    const onScrollBeginDrag = useCallback(() => {
        scrollByUser.current = true;
    }, []);

    const onScroll = useCallback((event: any) => {
        const currentOffset = event.nativeEvent.contentOffset.y;
        const { layoutMeasurement, contentOffset, contentSize } = event.nativeEvent;

        if (!showFavorite) {
            if (layoutMeasurement.height + contentOffset.y >= contentSize.height - 20) {
                onEndReached();
            }
        }

        if (scrollByUser.current) {
            if (!showFavorite) {
                productOriginData.current?.forEach((_, index) => {
                    if (currentOffset > listYPositions.current[index]) {
                        scrollToTab(index);
                    }
                });
            }

            RNAnimated.event(
                    [{ nativeEvent: { contentOffset: { y: animatedValue } } }],
                    {
                        useNativeDriver: false,
                    },
            )(event);
        }

    }, [animatedValue, onEndReached, scrollToTab, showFavorite]);

    useFocusEffect(useCallback(() => {
        const prevScreen = screenParams?.prevScreen;

        if (prevScreen !== SCREENS.PRODUCT_DETAIL_SCREEN) {
            params.current = { ...params.current, page: 1 };
            if (orderType === ORDER_TYPE.GROUP_ORDER && groupData?.workspace_id === restaurantData.id) {
                getDetailGroup({
                    group_id: groupData?.id
                }).then((result) => {
                    if (result.success) {
                        // update group filter
                        dispatch(StorageActions.setStorageGroupFilter(
                                {
                                    data: result.data,
                                    filterByDeliverable: result.data.type === ORDER_TYPE.DELIVERY ? true : false,
                                }
                        ));
                    }
                    getProductsData(false, false);
                });
            } else {
                getProductsData(false, false);
            }
        }

    }, [dispatch, getDetailGroup, getProductsData, groupData?.id, groupData?.workspace_id, orderType, restaurantData.id, screenParams]));

    useEffect(() => {
        if (!isFocused) {
            setParams({ prevScreen: null });
        }
    }, [isFocused, setParams]);

    // refresh data when restaurant changed
    useEffect(() => {
        InteractionManager.runAfterInteractions(() => {
            if (restaurantData.id !== params.current.workspace_id) {
                updateProductsOriginData([]);
                setListData([]);
                setListSearchResultData([]);
                dispatch(ProductActions.clearFavoriteProducts());
                setShowFavorite(false);
                scrollToTab(0);
                params.current = { ...params.current, page: 1, workspace_id: restaurantData.id, };
                getProductsData(!isTemplateOrGroupApp(), false);
            }
        });
    }, [restaurantData.id, getProductsData, scrollToTab, dispatch, updateProductsOriginData]);

    // filter favorite when open from workspace home's favorite func
    useEffect(() => {
        if (screenParams && isTemplateOrGroupApp()) {
            const { showFavorite } = screenParams;
            InteractionManager.runAfterInteractions(() => {
                if (showFavorite && !loading) {
                    handleFilterFavorite(true);
                    setParams({ showFavorite: null });
                }
            });
        }
    }, [handleFilterFavorite, loading, screenParams, setParams]);

    useEffect(() => {
        if (screenParams && screenParams?.inHomeStack) {
            if (screenToQuit === 1) {
                navigation.goBack();
            }
        } else {
            if (screenToQuit === 2) {
                navigation.goBack();
            }
        }
    }, [navigation, screenParams, screenToQuit]);

    const renderScreenHeader = useMemo(() => (
        <DetailRestaurantHeader
            animatedValue={animatedValue}
        />
    ), [animatedValue]);

    // eslint-disable-next-line react-hooks/exhaustive-deps
    const listDataForSearch = useMemo(() => convertData(productOriginData.current), [convertData, productOriginData.current]);

    const renderCategoryList = useMemo(() => (
        <CategoryList
            ref={tabRef}
            setIsSearch={setIsSearch}
            setListSearchResultData={setListSearchResultData}
            listCategoryData={listData}
            listOriginData={listDataForSearch}
            handleSelectTab={handleSelectTab}
            onFilterFavorite={handleFilterFavorite}
            isFilterFavorite={showFavorite}
        />
    ), [handleFilterFavorite, handleSelectTab, listData, listDataForSearch, showFavorite]);

    const renderListProduct = useMemo(() => (
        <ListProducts
            canLoadMore={canLoadMore}
            listYPositions={listYPositions}
            listRef={listRef}
            listSearchResultData={listSearchResultData}
            loading={loading}
            handleRefresh={handleRefresh}
            handleSortProduct={handleSortProduct}
            isSearch={isSearch}
            listData={listData}
            onScroll={onScroll}
            onScrollBeginDrag={onScrollBeginDrag}
            refreshing={refreshing}
            showFavorite={showFavorite}
        />
    ), [canLoadMore, handleRefresh, handleSortProduct, isSearch, listData, listSearchResultData, loading, onScroll, onScrollBeginDrag, refreshing, showFavorite]);

    const renderListFavorite = useMemo(() => (
        <ListFavoriteProducts
            handleRefresh={handleRefresh}
            listData={listData}
            listFavoriteRef={listFavoriteRef}
            onScroll={onScroll}
            onScrollBeginDrag={onScrollBeginDrag}
            refreshing={refreshing}
            showFavorite={showFavorite}
            isSearch={isSearch}
        />
    ), [handleRefresh, isSearch, listData, onScroll, onScrollBeginDrag, refreshing, showFavorite]);

    const renderList = useMemo(() => (
        <View style={{ flex: 1 }}>
            {renderListProduct}
            {renderListFavorite}
        </View>
    ), [renderListFavorite, renderListProduct]);

    const renderHolidayException = useMemo(() =>  (
        <HolidayException isInHome={false}/>
    ), []);

    const renderCoupon = useMemo(() => (
        <CouponRestaurant ref={refCouponRestaurant}  />
    ), []);

    return (
        <View style={[styles.mainContainer, { backgroundColor: themeColors.color_app_background }]}>
            {renderScreenHeader}
            {renderCoupon}
            {renderCategoryList}
            {renderList}
            <ModalHoliday />
            {renderHolidayException}
        </View>
    );
};

export default memo(RestaurantDetailScreen);

const stylesF = (Dimens: DimensType) => StyleSheet.create({
    emptyText: { marginTop:  Dimens.H_150, textAlign: 'center', fontSize: Dimens.FONT_18, fontWeight:'400' },
    mainContainer: { flex: 1 },
    listContainer: { paddingHorizontal: Dimens.W_12, paddingTop: Dimens.H_2 },
});