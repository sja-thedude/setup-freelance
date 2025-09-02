import React, {
    useCallback,
    useRef,
} from 'react';

import {
    StyleSheet,
    View,
} from 'react-native';
import { useDispatch } from 'react-redux';
import { useEffectOnce } from 'react-use';

import notifee from '@notifee/react-native';
import FlatListComponent from '@src/components/FlatListComponent';
import BackButton from '@src/components/header/BackButton';
import HeaderComponent from '@src/components/header/HeaderComponent';
import ListItemSeparator from '@src/components/ListItemSeparator';
import RefreshControlComponent from '@src/components/RefreshControlComponent';
import TextComponent from '@src/components/TextComponent';
import { Colors } from '@src/configs';
import { PAGE_SIZE } from '@src/configs/constants';
import { useAppSelector } from '@src/hooks';
import useDimens, { DimensType } from '@src/hooks/useDimens';
import { NotificationListModel, } from '@src/network/dataModels/NotificationListModel';
import {
    getNotificationListAction,
    NotificationActions,
} from '@src/redux/toolkit/actions/notificationActions';
import useThemeColors from '@src/themes/useThemeColors';
import { isTemplateOrGroupApp } from '@src/utils';

import NotificationItem from './component/NotificationItem';
import { useTranslation } from 'react-i18next';

const ListNotificationScreen = () => {
    const Dimens = useDimens();
    const styles = stylesF(Dimens);
    const { t } = useTranslation();

    const { themeColors } = useThemeColors();
    const dispatch = useDispatch();

    const workspaceDetail = useAppSelector((state) => state.storageReducer.templateWorkspaceDetail);

    const notificationsData = useAppSelector((state) => state.notificationReducer.notifications.data);
    const refreshing = useAppSelector((state) => state.notificationReducer.notifications.refreshing);
    const loading = useAppSelector((state) => state.notificationReducer.notifications.loading);
    const canLoadMore = useAppSelector((state) => state.notificationReducer.notifications.canLoadMore);

    const params = useRef({
        page: 1,
        limit: PAGE_SIZE,
        workspace_id: isTemplateOrGroupApp() ? workspaceDetail?.id : undefined
    });

    const getNotificationList = useCallback((loading: boolean, refreshing: boolean) => {
        dispatch(getNotificationListAction(loading, refreshing, params.current));
    }, [dispatch]);

    const handleRefresh = useCallback(() => {
        params.current = { ...params.current, page: 1 };
        getNotificationList(false, true);
    }, [getNotificationList]);

    const onEndReached = useCallback(() => {
        if (!loading && canLoadMore) {
            params.current = { ...params.current, page: params.current.page + 1 };
            getNotificationList(false, false);
        }
    }, [canLoadMore, getNotificationList, loading]);

    useEffectOnce(() => {
        getNotificationList(true, false);
        notifee.cancelAllNotifications();
        return () => {
            dispatch(NotificationActions.clearNotificationList());
        };
    });

    const renderItem = useCallback(({ item } : {item: NotificationListModel}) => (
        <NotificationItem
            item={item}
        />
    ), []);

    return (
        <View style={{ flex: 1, backgroundColor: themeColors.color_app_background }}>
            <HeaderComponent >
                <View style={styles.header}>
                    <BackButton/>
                    <TextComponent style={styles.headerText}>
                        {t('text_title_inbox')}
                    </TextComponent>
                </View>
            </HeaderComponent>

            <View style={{ flex: 1 }}>
                <FlatListComponent
                    contentContainerStyle={styles.flatList}
                    data={notificationsData}
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
                />
            </View>
        </View>
    );
};

export default ListNotificationScreen;

const stylesF = (Dimens: DimensType) => StyleSheet.create({
    flatList: { paddingTop: Dimens.H_22 },
    header: { flexDirection: 'row', alignItems: 'center' },
    headerText: {
        color: Colors.COLOR_WHITE,
        fontSize: Dimens.FONT_26,
        fontWeight: '700',
    },
});