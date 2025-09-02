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

import FlatListComponent from '@src/components/FlatListComponent';
import BackButton from '@src/components/header/BackButton';
import HeaderComponent from '@src/components/header/HeaderComponent';
import ListItemSeparator from '@src/components/ListItemSeparator';
import RefreshControlComponent from '@src/components/RefreshControlComponent';
import TextComponent from '@src/components/TextComponent';
import { Colors } from '@src/configs';
import { PAGE_SIZE } from '@src/configs/constants';
import { useAppSelector } from '@src/hooks';
import useBoolean from '@src/hooks/useBoolean';
import useDimens, { DimensType } from '@src/hooks/useDimens';
import { OrderHistoryListItem } from '@src/network/dataModels/OrderHistoryListItem';
import {
    getOrderListAction,
    OrderActions,
} from '@src/redux/toolkit/actions/orderActions';
import useThemeColors from '@src/themes/useThemeColors';
import { isTemplateOrGroupApp } from '@src/utils';

import OrderDetailDialog from './component/OrderDetailDialog';
import OrderItem from './component/OrderItem';
import { useTranslation } from 'react-i18next';

const OrderHistoryScreen = () => {
    const Dimens = useDimens();
    const styles = stylesF(Dimens);
    const { t } = useTranslation();

    const { themeColors } = useThemeColors();
    const dispatch = useDispatch();

    const [isShowDetail, showDetail, hideDetail] = useBoolean(false);

    const workspaceDetail = useAppSelector((state) => state.storageReducer.templateWorkspaceDetail);

    const orderData = useAppSelector((state) => state.orderReducer.orderHistoryList.data);
    const refreshing = useAppSelector((state) => state.orderReducer.orderHistoryList.refreshing);
    const loading = useAppSelector((state) => state.orderReducer.orderHistoryList.loading);
    const canLoadMore = useAppSelector((state) => state.orderReducer.orderHistoryList.canLoadMore);

    const params = useRef({
        page: 1,
        limit: PAGE_SIZE,
        workspace_id: isTemplateOrGroupApp() ? workspaceDetail?.id : undefined
    });

    const getOrderList = useCallback((loading: boolean, refreshing: boolean) => {
        dispatch(getOrderListAction(loading, refreshing, params.current));
    }, [dispatch]);

    const handleRefresh = useCallback(() => {
        params.current = { ...params.current, page: 1 };
        getOrderList(false, true);
    }, [getOrderList]);

    const onEndReached = useCallback(() => {
        if (!loading && canLoadMore) {
            params.current = { ...params.current, page: params.current.page + 1 };
            getOrderList(false, false);
        }
    }, [canLoadMore, getOrderList, loading]);

    useEffect(() => {
        getOrderList(true, false);

        return () => {
            dispatch(OrderActions.clearOrderHistoryList());
        };
    }, [dispatch, getOrderList]);

    const renderItem = useCallback(({ item } : {item: OrderHistoryListItem}) => (
        <OrderItem
            showDetail={showDetail}
            item={item}
        />
    ), [showDetail]);

    const renderEmpty = useCallback(() => {
        if (loading) {
            return null;
        }

        if (!orderData?.length) {
            return (
                <TextComponent style={[styles.emptyText, { color: themeColors.color_common_description_text }]}>
                    {t('Geen gegevens.')}
                </TextComponent>
            );
        }

        return null;
    }, [loading, orderData?.length, styles.emptyText, t, themeColors.color_common_description_text]);

    return (
        <View style={{ flex: 1, backgroundColor: themeColors.color_app_background }}>
            <HeaderComponent >
                <View style={styles.header}>
                    <BackButton/>
                    <TextComponent style={styles.headerText}>
                        {t('text_title_home_history')}
                    </TextComponent>
                </View>
            </HeaderComponent>

            <View style={{ flex: 1 }}>
                <FlatListComponent
                    contentContainerStyle={styles.flatList}
                    data={orderData}
                    hasNext={canLoadMore}
                    renderItem={renderItem}
                    onEndReachedThreshold={0}
                    onEndReached={onEndReached}
                    ItemSeparatorComponent={ListItemSeparator}
                    refreshControl={
                        <RefreshControlComponent
                            refreshing={refreshing}
                            onRefresh={handleRefresh}
                        />
                    }
                    ListEmptyComponent={renderEmpty()}
                />
            </View>
            <OrderDetailDialog
                hideModal={hideDetail}
                isShow={isShowDetail}
            />
        </View>
    );
};

export default OrderHistoryScreen;

const stylesF = (Dimens: DimensType) => StyleSheet.create({
    flatList: { paddingTop: Dimens.H_12 },
    header: { flexDirection: 'row', alignItems: 'center' },
    headerText: {
        color: Colors.COLOR_WHITE,
        fontSize: Dimens.FONT_26,
        fontWeight: '700',
    },
    emptyText: {
        fontSize: Dimens.FONT_15,
        textAlign: 'center',
        marginTop: Dimens.SCREEN_HEIGHT / 3,
    },
});