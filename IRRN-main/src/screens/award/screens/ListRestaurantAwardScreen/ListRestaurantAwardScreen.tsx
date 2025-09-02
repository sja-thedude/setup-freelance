import React, {
    memo,
    useCallback,
} from 'react';

import { useTranslation } from 'react-i18next';
import { StyleSheet } from 'react-native';
import { useUpdateEffect } from 'react-use';

import { useIsFocused } from '@react-navigation/native';
import ActivityPanel from '@src/components/ActivityPanel';
import FlatListComponent from '@src/components/FlatListComponent';
import RefreshControlComponent from '@src/components/RefreshControlComponent';
import useDimens, { DimensType } from '@src/hooks/useDimens';
import useFetchDataList from '@src/hooks/useFetchDataList';
import { fetchListLoyalties } from '@src/network/services/loyalties';

import ItemRestaurantAward from './components/ItemRestaurantAward';

const ListRestaurantAwardScreen = ({}) => {
    const Dimens = useDimens();
    const styles = stylesF(Dimens);

    const { t } = useTranslation();
    const isFocused = useIsFocused();

    const func = useCallback(
            (page: number) => fetchListLoyalties({ page, limit: 15 }),
            [],
    );
    const { data, refreshing, onRefresh, onEndReached, hasNext, fetchDefault } = useFetchDataList(func);

    const renderItem = useCallback(
            ({ item }: any) => <ItemRestaurantAward item={item} />,
            [],
    );

    useUpdateEffect(() => {
        if (isFocused) {
            fetchDefault();
        }
    }, [isFocused]);

    return (
        <ActivityPanel
            hiddenBack
            title={t('Klantenkaarten')}
        >
            <FlatListComponent
                initialNumToRender={15}
                maxToRenderPerBatch={15}
                removeClippedSubviews
                windowSize={210}
                data={data}
                hasNext={hasNext}
                onEndReachedThreshold={0.5}
                onEndReached={onEndReached}
                renderItem={renderItem}
                contentContainerStyle={styles.flatList}
                refreshControl={
                    <RefreshControlComponent
                        refreshing={refreshing}
                        onRefresh={onRefresh}
                    />
                }
            />
        </ActivityPanel>
    );
};

const stylesF = (Dimens: DimensType) => StyleSheet.create({
    flatList: {
        paddingVertical: Dimens.H_24,
    },
});

export default memo(ListRestaurantAwardScreen);
