import React, {
    FC,
    memo,
    useMemo,
} from 'react';

import { useTranslation } from 'react-i18next';
import {
    ScrollView,
    StyleSheet,
    View,
} from 'react-native';

import ListFooterLoading from '@src/components/ListFooterLoading';
import RefreshControlComponent from '@src/components/RefreshControlComponent';
import TextComponent from '@src/components/TextComponent';
import useDimens, { DimensType } from '@src/hooks/useDimens';
import useThemeColors from '@src/themes/useThemeColors';

import {
    ItemHeader,
    ItemRow,
    PRODUCT_LIST_ITEM_TYPE,
} from '../RestaurantDetailScreen';
import SectionHeaderItem from './SectionHeaderItem';
import ProductItem from './ProductItem';

interface IProps {
    isSearch: boolean,
    showFavorite: boolean,
    listRef: any,
    onScroll: (_event: any) => void,
    onScrollBeginDrag: () => void,
    refreshing: boolean,
    handleRefresh: () => void,
    handleSortProduct: Function,
    listData: Array<ItemHeader | ItemRow>,
    listSearchResultData: Array<ItemHeader | ItemRow>,
    listYPositions: any,
    canLoadMore: boolean,
    loading: boolean,
}

const ListProducts: FC<IProps> = ({ canLoadMore, loading, listYPositions, listSearchResultData, listData, isSearch, showFavorite, listRef, onScroll, onScrollBeginDrag, refreshing, handleRefresh, handleSortProduct }) => {
    const { t } = useTranslation();

    const Dimens = useDimens();
    const styles = stylesF(Dimens);
    const { themeColors } = useThemeColors();

    const stickyHeaderIndices = useMemo(() => listData
            .map((item, index) => {
                if (item.itemType === PRODUCT_LIST_ITEM_TYPE.HEADER) {
                    return index;
                } else {
                    return null;
                }
            }).filter((item) => item !== null) as number[], [listData]);

    const stickyHeaderIndicesSearchList = useMemo(() => listSearchResultData
            .map((item, index) => {
                if (item.itemType === PRODUCT_LIST_ITEM_TYPE.HEADER) {
                    return index;
                } else {
                    return null;
                }
            }).filter((item) => item !== null) as number[], [listSearchResultData]);

    const dataRender = useMemo(() => isSearch ? listSearchResultData : listData, [isSearch, listData, listSearchResultData]);
    const stickyHeader = useMemo(() => isSearch ? stickyHeaderIndicesSearchList : stickyHeaderIndices, [isSearch, stickyHeaderIndices, stickyHeaderIndicesSearchList]);

    return (
        <View style={{ height: isSearch ? undefined : showFavorite ? 0 : undefined }}>
            <ScrollView
                ref={listRef}
                contentContainerStyle={styles.listContainer}
                stickyHeaderIndices={stickyHeader}
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
                {dataRender?.length > 0 && dataRender?.map((i, index) =>
                    <View
                        onLayout={(e) => {
                            if (i?.itemType === PRODUCT_LIST_ITEM_TYPE.HEADER) {
                                listYPositions.current = { ...listYPositions.current, [i?.tabIndex]: e?.nativeEvent?.layout?.y };
                            }
                        }}
                        key={index}
                    >
                        {
                            i.itemType === PRODUCT_LIST_ITEM_TYPE.HEADER ? (
                                <SectionHeaderItem
                                    handleSortProduct={handleSortProduct}
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

                <ListFooterLoading canLoadMore={canLoadMore}/>

                {!loading ? (
                    <TextComponent style={StyleSheet.flatten([styles.emptyText, { color: themeColors.color_common_description_text  }])}>
                        { dataRender?.length === 0 ? t('text_no_items_found') : null}
                    </TextComponent>
                ) : null}
            </ScrollView>
        </View>
    );
};

export default memo(ListProducts);

const stylesF = (Dimens: DimensType) => StyleSheet.create({
    emptyText: { marginTop:  Dimens.H_150, textAlign: 'center', fontSize: Dimens.FONT_18, fontWeight:'400' },
    mainContainer: { flex: 1 },
    listContainer: { paddingHorizontal: Dimens.W_12, paddingTop: Dimens.H_2 },
});