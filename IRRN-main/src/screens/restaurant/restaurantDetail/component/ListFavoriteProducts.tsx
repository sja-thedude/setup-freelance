import React, {
    FC,
    memo,
    useCallback,
    useEffect,
    useMemo,
    useState,
} from 'react';

import { sortBy } from 'lodash';
import { useTranslation } from 'react-i18next';
import {
    ScrollView,
    StyleSheet,
    View,
} from 'react-native';

import RefreshControlComponent from '@src/components/RefreshControlComponent';
import TextComponent from '@src/components/TextComponent';
import { useAppSelector } from '@src/hooks';
import useDimens, { DimensType } from '@src/hooks/useDimens';
import useThemeColors from '@src/themes/useThemeColors';

import {
    ItemHeader,
    ItemRow,
    PRODUCT_LIST_ITEM_TYPE,
} from '../RestaurantDetailScreen';
import SectionHeaderItem from './SectionHeaderItem';
import ProductItem from './ProductItem';
import { getOrderSortData } from '@src/utils';

interface IProps {
    showFavorite: boolean,
    isSearch: boolean,
    listFavoriteRef: any,
    onScroll: (_event: any) => void,
    onScrollBeginDrag: () => void,
    refreshing: boolean,
    handleRefresh: () => void,
    listData: Array<ItemHeader | ItemRow>,
}

const ListFavoriteProducts: FC<IProps> = ({ listData, showFavorite, listFavoriteRef, onScroll, onScrollBeginDrag, refreshing, handleRefresh, isSearch }) => {
    const { t } = useTranslation();

    const Dimens = useDimens();
    const styles = stylesF(Dimens);
    const { themeColors } = useThemeColors();

    const ORDER_SORT_DATA = useMemo(() => getOrderSortData(t), [t]);

    const [searchSortType, setSearchSortType] = useState<number>(ORDER_SORT_DATA[0].type);

    const productsFavorite = useAppSelector((state) => state.productReducer.productsFavorite);

    const stickyHeaderIndicesFavoriteList = useMemo(() => [0], []);

    const listFavoriteData = useMemo(() => {
        const favoriteData = listData.filter((item) => item.itemType === PRODUCT_LIST_ITEM_TYPE.ROW && productsFavorite.includes(item.id));
        let sortedFavorite = favoriteData;

        const favoriteItemHeader: any = {
            index: 0,
            tabIndex: 0,
            sortType: searchSortType,
            itemType: PRODUCT_LIST_ITEM_TYPE.HEADER,
            title: t('text_favorites'),
            products: new Array(favoriteData.length),
        };

        switch (searchSortType) {
            case ORDER_SORT_DATA[1].type:
            {
                sortedFavorite = sortBy(favoriteData, (o: any) => Number(o.price));
                break;
            }

            case ORDER_SORT_DATA[2].type:
            {
                sortedFavorite = sortBy(favoriteData, ['name']);
                break;
            }

            default:
                break;
        }

        sortedFavorite.unshift(favoriteItemHeader);
        return sortedFavorite;
    }, [ORDER_SORT_DATA, listData, productsFavorite, searchSortType, t]);

    const handleSortFavoriteProduct = useCallback((_section: ItemHeader, sortType: number) => {
        setSearchSortType(sortType);
    }, []);

    useEffect(() => {
        if (!showFavorite) {
            setSearchSortType(ORDER_SORT_DATA[0].type);
        }
    }, [ORDER_SORT_DATA, showFavorite]);

    if (!showFavorite) {
        return null;
    }

    return (
        <View style={{ height: isSearch ? 0 : showFavorite ? undefined : 0 }}>
            <ScrollView
                ref={listFavoriteRef}
                contentContainerStyle={styles.listContainer}
                stickyHeaderIndices={stickyHeaderIndicesFavoriteList}
                scrollEventThrottle={99}
                showsVerticalScrollIndicator={false}
                onScroll={onScroll}
                onScrollBeginDrag={onScrollBeginDrag}
                refreshControl={
                    <RefreshControlComponent
                        refreshing={refreshing}
                        onRefresh={handleRefresh}
                    />
                }
            >
                {listFavoriteData?.length > 0 && listFavoriteData?.map((i, index) =>
                    <View
                        key={index}
                    >
                        {
                            i.itemType === PRODUCT_LIST_ITEM_TYPE.HEADER ? (
                                <SectionHeaderItem
                                    handleSortProduct={handleSortFavoriteProduct}
                                    section={i as ItemHeader}
                                />
                            ) : (
                                <ProductItem
                                    item={i as ItemRow}
                                />
                            )
                        }
                    </View>
                )}

                {listFavoriteData?.length === 1 ? (
                   <TextComponent style={StyleSheet.flatten([styles.emptyText, { color: themeColors.color_common_description_text  }])}>
                       { t('text_no_favorite_items')}
                   </TextComponent>
                ) : null}
            </ScrollView>
        </View>
    );
};

export default memo(ListFavoriteProducts);

const stylesF = (Dimens: DimensType) => StyleSheet.create({
    emptyText: { marginTop:  Dimens.H_150, textAlign: 'center', fontSize: Dimens.FONT_18, fontWeight:'400' },
    mainContainer: { flex: 1 },
    listContainer: { paddingHorizontal: Dimens.W_12, paddingTop: Dimens.H_2 },
});