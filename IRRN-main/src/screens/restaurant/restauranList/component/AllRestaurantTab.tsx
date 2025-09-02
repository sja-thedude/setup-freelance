import React, {
    FC,
    memo,
    useCallback,
    useEffect,
    useMemo,
    useState,
} from 'react';

import { useTranslation } from 'react-i18next';
import {
    Animated as RNAnimated,
    StyleSheet,
    View,
} from 'react-native';

import { FlashList } from '@shopify/flash-list';
import ListFooterLoading from '@src/components/ListFooterLoading';
import RefreshControlComponent from '@src/components/RefreshControlComponent';
import TextComponent from '@src/components/TextComponent';
import { ORDER_TYPE } from '@src/configs/constants';
import useDimens, { DimensType } from '@src/hooks/useDimens';
import { RestaurantNearbyItemModel, } from '@src/network/dataModels/RestaurantNearbyItemModel';
import useThemeColors from '@src/themes/useThemeColors';
import { compareArrays } from '@src/utils';

import RestaurantListItem from './RestaurantListItem';

interface IProps {
    restaurantData: RestaurantNearbyItemModel[],
    currentOrderType: number,
    onEndReached: any,
    refreshing: boolean,
    handleRefresh: any,
    canLoadMore: boolean,
    animatedValue: RNAnimated.Value,
}

export interface HeaderItemType {
    type: number, title: string, isShow: boolean
}
export interface RestaurantItem extends RestaurantNearbyItemModel {
    type: number,
}

export const ITEM_TYPE = {
    ROW: 1,
    HEADER: 2
};

const AllRestaurantTab: FC<IProps> = ({ restaurantData, currentOrderType, onEndReached, refreshing, handleRefresh, canLoadMore, animatedValue }) => {
    const Dimens = useDimens();
    const styles = stylesF(Dimens);

    const { themeColors } = useThemeColors();
    const { t } = useTranslation();

    const [listData, setListData] = useState < Array <RestaurantItem | HeaderItemType>> ([]);

    const stickyHeaderIndices = useMemo(() => listData
            .map((item, index) => {
                if (item.type === ITEM_TYPE.HEADER) {
                    return index;
                } else {
                    return null;
                }
            }).filter((item) => item !== null) as number[], [listData]);

    useEffect(() => {
        const openRestaurants = restaurantData.filter((item) => item.is_open);
        const closeRestaurants = restaurantData.filter((item) => !item.is_open);

        const newData = currentOrderType === ORDER_TYPE.GROUP_ORDER ? [
            ...restaurantData.map((item) => ({ ...item, type: ITEM_TYPE.ROW }))
        ] : [
            ...openRestaurants.map((item) => ({ ...item, type: ITEM_TYPE.ROW })),
            {
                type: ITEM_TYPE.HEADER,
                title: t('text_current_close'),
                isShow: closeRestaurants.length > 0
            },
            ...closeRestaurants.map((item) => ({ ...item, type: ITEM_TYPE.ROW })),
        ];

        if (!compareArrays(newData, listData )) {
            setListData(newData);
        }

    }, [currentOrderType, listData, restaurantData, t]);

    const onScroll = useCallback((ev: any) => {
        RNAnimated.event(
                [{ nativeEvent: { contentOffset: { y: animatedValue } } }],
                {
                    useNativeDriver: false,
                },
        )(ev);
    }, [animatedValue]);

    const renderItem = useCallback(({ item } : {item: any}) => {
        if (item.type === ITEM_TYPE.HEADER) {
            if (!item.isShow) {
                return null;
            }

            return (
                <View>
                    <TextComponent style={[styles.sectionText, { color: themeColors.color_common_description_text, }]}>
                        {item.title}
                    </TextComponent>
                </View>
            );
        }

        return (
            <RestaurantListItem
                item={item}
                currentOrderType={currentOrderType}
            />
        );
    }, [currentOrderType, styles.sectionText, themeColors.color_common_description_text]);

    return (
        <View style={styles.mainContainer}>
            <FlashList
                data={listData}
                renderItem={renderItem}
                getItemType={(item) => item.type }
                estimatedItemSize={80}
                stickyHeaderIndices={stickyHeaderIndices}
                onEndReachedThreshold={0}
                onEndReached={onEndReached}
                ListFooterComponent={<ListFooterLoading canLoadMore={canLoadMore}/>}
                contentContainerStyle={styles.listContainer}
                showsVerticalScrollIndicator={false}
                keyboardShouldPersistTaps={'handled'}
                refreshControl={
                    <RefreshControlComponent
                        refreshing={refreshing}
                        onRefresh={handleRefresh}
                    />
                }
                onScroll={onScroll}
            />
        </View>
    );
};

export default memo(AllRestaurantTab);

const stylesF = (Dimens: DimensType) => StyleSheet.create({
    sectionText: {
        fontSize: Dimens.FONT_18,
        fontWeight: '700',
        opacity: 0.5,
        marginVertical: Dimens.H_20,
    },
    listContainer: { paddingTop: Dimens.H_12 },
    mainContainer: { flex: 1, paddingHorizontal: Dimens.W_12 },
});