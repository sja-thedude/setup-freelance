import React, {
    FC,
    memo,
    useCallback,
    useMemo,
} from 'react';

import { useTranslation } from 'react-i18next';
import {
    StyleSheet,
    View,
} from 'react-native';

import ListItemSeparator from '@src/components/ListItemSeparator';
import ScrollViewComponent from '@src/components/ScrollViewComponent';
import TextComponent from '@src/components/TextComponent';
import TouchableComponent from '@src/components/TouchableComponent';
import {
    DEFAULT_CURRENCY,
    ORDER_TYPE,
} from '@src/configs/constants';
import useDimens, { DimensType } from '@src/hooks/useDimens';
import {
    Item,
    OrderDetailModel,
} from '@src/network/dataModels/OrderDetailModel';
import useThemeColors from '@src/themes/useThemeColors';
import formatCurrency from '@src/utils/currencyFormatUtil';

interface IProps {
    orderDetailData?: OrderDetailModel
}

const FirstInfoPage: FC<IProps> = ({ orderDetailData }) => {
    const Dimens = useDimens();
    const styles = stylesF(Dimens);

    const { t } = useTranslation();
    const { themeColors } = useThemeColors();

    const discountInfo = useMemo(() => {
        if (orderDetailData?.coupon_discount !== null) {
            return {
                name: t('text_coupon_discount'),
                discount: `- ${formatCurrency(orderDetailData?.coupon_discount, DEFAULT_CURRENCY)[2]}${orderDetailData?.coupon_discount || 0}`
            };
        }

        if (orderDetailData?.group_discount !== null && Number(orderDetailData?.group_discount) > 0) {
            return {
                name: t('text_group_discount'),
                discount: `- ${formatCurrency(orderDetailData?.group_discount, DEFAULT_CURRENCY)[2]}${orderDetailData?.group_discount || 0}`
            };
        }

        if (orderDetailData?.redeem_discount !== null) {
            return {
                name: t('text_reward_discount'),
                discount: `- ${formatCurrency(orderDetailData?.redeem_discount, DEFAULT_CURRENCY)[2]}${orderDetailData?.redeem_discount || 0}`
            };
        }

        return null;

    }, [orderDetailData?.coupon_discount, orderDetailData?.group_discount, orderDetailData?.redeem_discount, t]);

    const renderProductItem = useCallback((item: Item, index: number) => (
        <View style={styles.productContainer}>
            <View style={styles.productNameContainer}>
                <TextComponent
                    numberOfLines={1}
                    style={styles.productQuantity}
                >
                    {`${item.quantity} x`}
                </TextComponent>
                <TextComponent
                    numberOfLines={1}
                    style={styles.productName}
                >
                    {item.product.name}
                </TextComponent>
                <TextComponent style={styles.productPrice}>
                    {`${formatCurrency(item.subtotal, DEFAULT_CURRENCY)[2]}${item.subtotal}`}
                </TextComponent>
            </View>

            <View style={styles.optionContainer}>
                {item.options.map((opt, idx) => {
                    let label = opt.option.is_ingredient_deletion ? `-  ${t('cart_item_zonder')}` : '-';
                    opt.option_items.map((subOpt, index) => {
                        label = label + `${index === 0 ? '' : ','} ${subOpt.option_item.name}`;
                    });
                    return (
                        <TextComponent
                            key={idx}
                            style={[styles.optionsName, { color: themeColors.color_common_subtext }]}
                        >
                            {label}
                        </TextComponent>
                    );
                })}
            </View>
            {(orderDetailData?.items?.length && index !== orderDetailData?.items?.length - 1) && (
                <ListItemSeparator
                    style={[styles.divider, { backgroundColor: themeColors.color_card_divider }]}
                />
            )}
        </View>
    ), [styles.productContainer, styles.productNameContainer, styles.productQuantity, styles.productName, styles.productPrice, styles.optionContainer, styles.divider, styles.optionsName, orderDetailData?.items?.length, themeColors.color_card_divider, themeColors.color_common_subtext, t]);

    return (
        <TouchableComponent
            activeOpacity={1}
            style={styles.pageContainer}
        >
            <View style={styles.headerContainer}>
                <TextComponent style={styles.textTitle}>
                    {t('text_ordered_items')}
                </TextComponent>

                <TextComponent style={[styles.shopName, { color: themeColors.color_primary }]}>
                    {orderDetailData?.workspace?.title?.toUpperCase() || orderDetailData?.workspace?.name?.toUpperCase()}
                </TextComponent>
            </View>

            <ScrollViewComponent
                style={styles.scrollView}
            >
                {orderDetailData?.items?.map((item, index) => (
                    <TouchableComponent
                        activeOpacity={1}
                        key={index}
                    >
                        {renderProductItem(item, index)}
                    </TouchableComponent>
                ))}
            </ScrollViewComponent>

            <ListItemSeparator
                style={[styles.divider, { backgroundColor: themeColors.color_card_divider }]}
            />

            <View style={styles.bottomContainer}>
                {orderDetailData?.total_price !== orderDetailData?.subtotal && (
                    <View style={styles.bottomItemContainer}>
                        <TextComponent style={[styles.leftText, { color: themeColors.color_common_description_text }]}>
                            {t('text_subtotal')}
                        </TextComponent>
                        <TextComponent style={[styles.rightText, { color: themeColors.color_common_description_text }]}>
                            {`${formatCurrency(orderDetailData?.subtotal, DEFAULT_CURRENCY)[2]}${orderDetailData?.subtotal || 0}`}
                        </TextComponent>
                    </View>
                )}

                {discountInfo ? (
                                <View style={styles.bottomItemContainer}>
                                    <TextComponent style={[styles.leftText, { color: themeColors.color_common_description_text }]}>
                                        {discountInfo?.name}
                                    </TextComponent>
                                    <TextComponent style={[styles.rightText, { color: themeColors.color_common_description_text }]}>
                                        {discountInfo?.discount}
                                    </TextComponent>
                                </View>
                            ) : null}

                {(orderDetailData?.type !== ORDER_TYPE.GROUP_ORDER && !!orderDetailData?.service_cost) ? (
                                <View style={styles.bottomItemContainer}>
                                    <TextComponent style={[styles.leftText, { color: themeColors.color_common_description_text }]}>
                                        {t('Servicekost')}
                                    </TextComponent>
                                    <TextComponent style={[styles.rightText, { color: themeColors.color_common_description_text }]}>
                                        {`${formatCurrency((orderDetailData?.service_cost || 0), DEFAULT_CURRENCY)[2]}${(orderDetailData?.service_cost || 0)}`}
                                    </TextComponent>
                                </View>
                            ) : null}

                {orderDetailData?.type === ORDER_TYPE.DELIVERY && orderDetailData?.setting_delivery_condition ? (
                                <View style={styles.bottomItemContainer}>
                                    <TextComponent style={[styles.leftText, { color: themeColors.color_common_description_text }]}>
                                        {t('cart_deliver_fee')}
                                    </TextComponent>
                                    <TextComponent style={[styles.rightText, { color: themeColors.color_common_description_text }]}>
                                        {`${formatCurrency((orderDetailData?.ship_price || 0), DEFAULT_CURRENCY)[2]}${(orderDetailData?.ship_price || 0)}`}
                                    </TextComponent>
                                </View>
                            ) : null}
            </View>

            <View style={styles.totalContainer}>
                <TextComponent style={styles.totalPriceText}>
                    {t('text_total')}
                </TextComponent>
                <TextComponent style={styles.totalPrice}>
                    {`${formatCurrency(orderDetailData?.total_price, DEFAULT_CURRENCY)[2]}${orderDetailData?.total_price || 0}`}
                </TextComponent>
            </View>

        </TouchableComponent>
    );
};

export default memo(FirstInfoPage);

const stylesF = (Dimens: DimensType) => StyleSheet.create({
    totalPrice: { fontSize: Dimens.FONT_16, fontWeight: '700' },
    totalPriceText: { fontSize: Dimens.FONT_16, fontWeight: '700', flex: 1 },
    rightText: { fontSize: Dimens.FONT_12 },
    leftText: { fontSize: Dimens.FONT_12, flex: 1 },
    totalContainer: {
        marginLeft: Dimens.W_35,
        marginRight: Dimens.W_16,
        marginTop: Dimens.H_10,
        flexDirection: 'row',
        alignItems: 'center',
    },
    bottomItemContainer: {
        marginLeft: Dimens.W_35,
        marginRight: Dimens.W_16,
        marginTop: Dimens.H_3,
        flexDirection: 'row',
        alignItems: 'center',
    },
    bottomContainer: { marginTop: Dimens.H_10 },
    scrollView: { flexGrow: 0 },
    shopName: {
        fontSize: Dimens.FONT_16,
        fontWeight: '700',
        marginBottom: Dimens.H_8,
    },
    headerContainer: { marginTop: Dimens.H_24, marginHorizontal: Dimens.W_35 },
    pageContainer: { width: Dimens.SCREEN_WIDTH - Dimens.W_48 },
    divider: {
        width: undefined,
        marginLeft: 35,
        marginRight: Dimens.W_8,
        marginTop: Dimens.H_10,
    },
    optionsName: { fontSize: Dimens.FONT_14, flex: 1, marginTop: Dimens.H_3 },
    optionContainer: {
        marginLeft: Dimens.W_35 + Dimens.W_16,
        marginTop: Dimens.H_3,
        paddingRight: Dimens.W_16,
    },
    productPrice: { fontSize: Dimens.FONT_16, fontWeight: '400' },
    productName: {
        flex: 1,
        marginRight: Dimens.W_16,
        fontSize: Dimens.FONT_16,
        fontWeight: '400',
    },
    productQuantity: {
        fontSize: Dimens.FONT_16,
        fontWeight: '600',
        textAlign: 'right',
        minWidth: Dimens.W_35,
        marginRight: Dimens.W_3,
    },
    productNameContainer: {
        flexDirection: 'row',
        alignItems: 'center',
        paddingRight: Dimens.W_16,
    },
    productContainer: { marginTop: Dimens.H_5 },
    timeText: { fontSize: Dimens.FONT_14 },
    descText: { fontSize: Dimens.FONT_14, marginTop: Dimens.H_16 },
    textAButton: {
        width: '75%',
        alignSelf: 'center',
        marginTop: Dimens.H_20,
    },
    textTitle: {
        fontSize: Dimens.FONT_16,
        fontWeight: '700',
        marginBottom: Dimens.H_8,
    },
});