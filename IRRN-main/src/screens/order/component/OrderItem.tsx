import React, {
    memo,
    useCallback,
    useEffect,
    useState,
} from 'react';

import { useTranslation } from 'react-i18next';
import {
    Pressable,
    StyleSheet,
    View,
} from 'react-native';
import { useDispatch } from 'react-redux';

import { EyeIcon } from '@src/assets/svg';
import TextComponent from '@src/components/TextComponent';
import TouchableComponent from '@src/components/TouchableComponent';
import useDimens, { DimensType } from '@src/hooks/useDimens';
import { OrderHistoryListItem, } from '@src/network/dataModels/OrderHistoryListItem';
import { getOrderDetailAction } from '@src/redux/toolkit/actions/orderActions';
import useThemeColors from '@src/themes/useThemeColors';
import { getOrderType } from '@src/utils';
import { getTimeByTimeZone } from '@src/utils/dateTimeUtil';

const OrderItem = ({ item, showDetail }: {item: OrderHistoryListItem, showDetail: Function}) => {
    const Dimens = useDimens();
    const styles = stylesF(Dimens);

    const { themeColors } = useThemeColors();
    const dispatch = useDispatch();

    const { t } = useTranslation();

    const [data, setData] = useState(item);

    const getOrderDetail = useCallback((orderId: number) => {
        dispatch(getOrderDetailAction({ order_id: orderId }, showDetail));
    }, [dispatch, showDetail]);

    const handleItemClick = useCallback((item: OrderHistoryListItem) => {
        getOrderDetail(item.id);
    }, [getOrderDetail]);

    useEffect(() => {
        setData(item);
    }, [item]);

    return (
        <Pressable
            onPress={() => handleItemClick(data)}
            style={styles.itemContainer}
        >
            <View style={{ flex: 1 }}>
                <TextComponent style={styles.itemTitle}>
                    {`${getTimeByTimeZone(data.date_time, 'YYYY-MM-DD hh:mm:ss', `DD/MM/YYYY [${t('text_date_order_history')}] HH:mm`)}`}
                </TextComponent>
                <TextComponent style={styles.itemDesc}>
                    {data.workspace.title || data.workspace.name} -
                    <TextComponent style={{ color: themeColors.color_common_description_text }}>
                        {` #${data.group_id ? 'G' : ''}${data.parent_code}${data.group_id ? ` - ${data.extra_code}` : ''} - ${getOrderType(data.type, t)}`}
                    </TextComponent>
                </TextComponent>
            </View>
            <TouchableComponent
                hitSlop={Dimens.DEFAULT_HIT_SLOP}
                onPress={() => handleItemClick(data)}
            >
                <EyeIcon
                    stroke={themeColors.color_primary}
                />
            </TouchableComponent>
        </Pressable>
    );
};

export default memo(OrderItem);

const stylesF = (Dimens: DimensType) => StyleSheet.create({
    itemDesc: { fontSize: Dimens.FONT_14, fontWeight: '400' },
    itemTitle: {
        fontSize: Dimens.FONT_16,
        fontWeight: '700',
        marginBottom: Dimens.H_6,
    },
    itemContainer: {
        flexDirection: 'row',
        alignItems: 'center',
        paddingHorizontal: Dimens.W_12,
        paddingVertical: Dimens.W_12,
    },
});