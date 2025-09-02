import React, {
    memo,
    useCallback,
    useEffect,
    useState,
} from 'react';

import {
    StyleSheet,
    View,
} from 'react-native';
import { useDispatch } from 'react-redux';

import TextComponent from '@src/components/TextComponent';
import TouchableComponent from '@src/components/TouchableComponent';
import { useAppSelector } from '@src/hooks';
import useDimens, { DimensType } from '@src/hooks/useDimens';
import { NotificationListModel, } from '@src/network/dataModels/NotificationListModel';
import {
    getNotificationDetailAction,
    markNotificationAction,
    NotificationActions,
} from '@src/redux/toolkit/actions/notificationActions';
import useThemeColors from '@src/themes/useThemeColors';

const NotificationItem = ({ item }: {item: NotificationListModel}) => {
    const Dimens = useDimens();
    const styles = stylesF(Dimens);

    const { themeColors } = useThemeColors();
    const dispatch = useDispatch();

    const [data, setData] = useState(item);
    const notificationNumber = useAppSelector((state) => state.notificationReducer.notificationBadge);

    const getNotificationDetail = useCallback((notificationId: number) => {
        dispatch(getNotificationDetailAction({ notification_id: notificationId }));
    }, [dispatch]);

    const markNotification = useCallback((notificationId: number) => {
        const callback = () => {
            setData({ ...data, status: true });
            dispatch(NotificationActions.updateNotificationBadge(notificationNumber - 1));
        };

        dispatch(markNotificationAction({ id: notificationId }, callback));
    }, [data, dispatch, notificationNumber]);

    const handleItemClick = useCallback((item: NotificationListModel) => {
        getNotificationDetail(item.id);
        if (!item.status) {
            markNotification(item.id);
        }
    }, [getNotificationDetail, markNotification]);

    useEffect(() => {
        setData(item);
    }, [item]);

    return (
        <View>
            <TouchableComponent
                onPress={() => handleItemClick(data)}
                style={styles.itemContainer}
            >
                <View style={{ flex: 1, marginRight: Dimens.W_16 }}>
                    <TextComponent
                        style={styles.titleText}
                        numberOfLines={1}
                    >
                        {data.title}
                    </TextComponent>
                    <TextComponent
                        style={[styles.descText, { color: themeColors.color_common_description_text }]}
                        numberOfLines={1}
                    >
                        {data.description}
                    </TextComponent>
                </View>
                {!data.status && (
                    <View style={[styles.statusIcon, { backgroundColor: themeColors.color_primary }]}/>
                )}
            </TouchableComponent>
        </View>
    );
};

export default memo(NotificationItem);

const stylesF = (Dimens: DimensType) => StyleSheet.create({
    statusIcon: {
        width: Dimens.W_6,
        height: Dimens.W_6,
        borderRadius: Dimens.W_6,
    },
    descText: { fontSize: Dimens.FONT_14, marginTop: Dimens.H_3 },
    titleText: { fontSize: Dimens.FONT_16, fontWeight: '700' },
    itemContainer: {
        flexDirection: 'row',
        alignItems: 'center',
        justifyContent: 'space-between',
        paddingVertical: Dimens.H_12,
        paddingHorizontal: Dimens.W_12,
    },
});