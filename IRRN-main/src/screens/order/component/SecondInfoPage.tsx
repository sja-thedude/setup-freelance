import React, { FC, memo } from 'react';

import { useTranslation } from 'react-i18next';
import {
    StyleSheet,
    View,
} from 'react-native';

import ScrollViewComponent from '@src/components/ScrollViewComponent';
import TextComponent from '@src/components/TextComponent';
import TouchableComponent from '@src/components/TouchableComponent';
import { ORDER_TYPE, } from '@src/configs/constants';
import useDimens, { DimensType } from '@src/hooks/useDimens';
import { OrderDetailModel } from '@src/network/dataModels/OrderDetailModel';
import useThemeColors from '@src/themes/useThemeColors';
import {
    getOrderType,
    getPaymentMethod,
    getPaymentStatus,
} from '@src/utils';
import { getTimeByTimeZone } from '@src/utils/dateTimeUtil';

interface IProps {
    orderDetailData?: OrderDetailModel
}

const SecondInfoPage: FC<IProps> = ({ orderDetailData }) => {
    const Dimens = useDimens();
    const styles = stylesF(Dimens);

    const { t } = useTranslation();
    const { themeColors } = useThemeColors();

    return (
        <TouchableComponent
            activeOpacity={1}
            style={styles.pageContainer}
        >
            <View style={styles.header}>
                <TextComponent style={styles.textTitle}>
                    {`${t('text_details_of_order')} #${orderDetailData?.group && orderDetailData?.group_id ? 'G' : ''}${orderDetailData?.parent_code}${orderDetailData?.group_id ? ` - ${orderDetailData?.extra_code}` : ''}`}
                </TextComponent>

                <TextComponent style={[styles.titleText, { color: themeColors.color_primary }]}>
                    {orderDetailData?.workspace?.title?.toUpperCase() || orderDetailData?.workspace?.name?.toUpperCase()}
                </TextComponent>

                <TextComponent style={styles.timeText}>
                    {`${getTimeByTimeZone(orderDetailData?.date_time, 'YYYY-MM-DD hh:mm:ss', `DD/MM/YYYY [${t('text_date_order_history')}] HH:mm`)}`}

                </TextComponent>
            </View>
            <ScrollViewComponent
                style={styles.scrollView}
            >
                <TouchableComponent
                    activeOpacity={1}
                    style={styles.infoContainer}
                >
                    <View style={styles.infoRow}>
                        <TextComponent style={styles.infoTitleText}>
                            {t('text_payment_status')}
                        </TextComponent>
                        <TextComponent style={{ fontSize: Dimens.FONT_13, fontWeight: '700' }}>
                            {getPaymentStatus(orderDetailData?.payment_status, orderDetailData?.payment_method, orderDetailData?.is_test_account, t)}
                        </TextComponent>
                    </View>

                    <View style={styles.infoRow}>
                        <TextComponent style={styles.infoTitleText}>
                            {t('text_payment_method')}
                        </TextComponent>
                        <TextComponent style={{ fontSize: Dimens.FONT_13, fontWeight: '700' }}>
                            {getPaymentMethod(orderDetailData?.payment_method, t)}
                        </TextComponent>
                    </View>

                    {orderDetailData?.note ? (
                    <View style={styles.infoRow}>
                        <TextComponent style={styles.infoTitleText}>
                            {t('text_remarks')}
                        </TextComponent>
                        <TextComponent style={styles.infoValueText}>
                            {orderDetailData?.note}
                        </TextComponent>
                    </View>
                ) : null
                    }

                    {orderDetailData?.type === ORDER_TYPE.DELIVERY ? (
                     <View style={styles.infoRow}>
                         <TextComponent style={styles.infoTitleText}>
                             {t('text_delivery_address')}
                         </TextComponent>
                         <TextComponent style={styles.infoValueText}>
                             {orderDetailData?.group?.address_display || orderDetailData?.address}
                         </TextComponent>
                     </View>
                ) : null
                    }

                    {orderDetailData?.type === ORDER_TYPE.TAKE_AWAY && !orderDetailData?.group_id ? (
                     <View style={styles.infoRow}>
                         <TextComponent style={styles.infoTitleText}>
                             {t('text_type_of_order')}
                         </TextComponent>
                         <TextComponent style={styles.infoValueText}>
                             {getOrderType(orderDetailData?.type, t)}
                         </TextComponent>
                     </View>
                ) : null
                    }

                    {orderDetailData?.group_id ? (
                     <View style={styles.infoRow}>
                         <TextComponent style={styles.infoTitleText}>
                             {`${t('text_group')}:`}
                         </TextComponent>
                         <TextComponent style={styles.infoValueText}>
                             {orderDetailData?.group?.name || ''}
                         </TextComponent>
                     </View>
                ) : null
                    }
                </TouchableComponent>
            </ScrollViewComponent>

        </TouchableComponent>
    );
};

export default memo(SecondInfoPage);

const stylesF = (Dimens: DimensType) => StyleSheet.create({
    infoValueText: {
        fontSize: Dimens.FONT_13,
        fontWeight: '700',
        textAlign: 'right',
        flex: 1.5
    },
    infoTitleText: { fontSize: Dimens.FONT_13, fontWeight: '400', flex: 1 },
    infoRow: {
        marginLeft: Dimens.W_35,
        marginRight: Dimens.W_16,
        marginTop: Dimens.H_8,
        flexDirection: 'row',
        alignItems: 'stretch',
    },
    infoContainer: { marginTop: Dimens.H_10 },
    timeText: {
        fontSize: Dimens.FONT_13,
        fontWeight: '700',
        marginBottom: Dimens.H_8,
    },
    titleText: {
        fontSize: Dimens.FONT_16,
        fontWeight: '700',
        marginBottom: Dimens.H_8,
    },
    header: { marginTop: Dimens.H_24, marginHorizontal: Dimens.W_35 },
    pageContainer: { width: Dimens.SCREEN_WIDTH - Dimens.W_48 },
    textTitle: {
        fontSize: Dimens.FONT_16,
        fontWeight: '700',
        marginBottom: Dimens.H_8,
    },
    scrollView: { flexGrow: 0 },
});