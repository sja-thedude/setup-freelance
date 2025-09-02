import React, {
    memo,
    useCallback,
    useMemo,
} from 'react';

import { useTranslation } from 'react-i18next';
import {
    StyleSheet,
    View,
} from 'react-native';
import { useDispatch } from 'react-redux';

import { Images } from '@src/assets/images';
import {
    BowIcon,
    ClockIcon,
    MoneyMinIcon,
    MotoBikeIcon,
} from '@src/assets/svg';
import ImageComponent from '@src/components/ImageComponent';
import ShadowView from '@src/components/ShadowView';
import TextComponent from '@src/components/TextComponent';
import TouchableComponent from '@src/components/TouchableComponent';
import {
    DEFAULT_CURRENCY,
    DEFAULT_DISTANCE_UNIT,
    ORDER_TYPE,
    RESTAURANT_EXTRA_TYPE,
} from '@src/configs/constants';
import useDimens, { DimensType } from '@src/hooks/useDimens';
import { SCREENS } from '@src/navigation/config/screenName';
import NavigationService from '@src/navigation/NavigationService';
import { RestaurantActions, } from '@src/redux/toolkit/actions/restaurantActions';
import useThemeColors from '@src/themes/useThemeColors';
import { capitalizeFirstLetter } from '@src/utils';
import formatCurrency from '@src/utils/currencyFormatUtil';

import { RestaurantItem } from './AllRestaurantTab';

const RestaurantListItem = ({ item, currentOrderType } : {item: RestaurantItem, currentOrderType: number}) => {
    const Dimens = useDimens();
    const styles = stylesF(Dimens);

    const { themeColors } = useThemeColors();
    const { t } = useTranslation();
    const dispatch = useDispatch();

    const categories = useMemo(() => {
        let categories = '';
        item.categories.map((category, index) => {
            categories = categories + `${index === 0 ? '' : ', '}` + capitalizeFirstLetter(category.name);
        });

        return categories;
    }, [item.categories]);

    const showLoyalty = useMemo(() => item.extras.some((ex) => ex.active && ex.type === RESTAURANT_EXTRA_TYPE.CUSTOMER_CARD), [item.extras]);

    const handleNavToDetail = useCallback(() => {
        dispatch(RestaurantActions.setExitScreen(1));
        dispatch(RestaurantActions.updateRestaurantDetail(item));
        NavigationService.navigate(SCREENS.RESTAURANT_DETAIL_SCREEN);
    }, [dispatch, item]);

    return (
        <TouchableComponent
            onPress={handleNavToDetail}
        >
            <ShadowView
                style={[styles.shadowStyle, { backgroundColor: themeColors.color_card_background }]}
            >

                <View style={[styles.itemContainer, { backgroundColor: themeColors.color_card_background }]} >
                    <ImageComponent
                        resizeMode='cover'
                        defaultImage={Images.image_placeholder}
                        source={{ uri: item.photo }}
                        style={styles.itemImage}
                    />

                    <View style={styles.infoContainer}>
                        <View>
                            <TextComponent
                                numberOfLines={1}
                                style={[styles.resName, { color: themeColors.color_text_2 }]}
                            >
                                {item?.setting_generals?.title}
                            </TextComponent>
                            <TextComponent
                                numberOfLines={1}
                                style={[styles.resCategory, { color: themeColors.color_text_2 }]}
                            >
                                {categories}
                            </TextComponent>
                        </View>

                        {currentOrderType !== ORDER_TYPE.GROUP_ORDER && (
                            <View style={styles.subInfoContainer}>
                                <View style={styles.bottomInfoContainer}>
                                    <ClockIcon
                                        stroke={themeColors.color_primary}
                                        width={Dimens.W_11}
                                        height={Dimens.W_11}
                                    />

                                    <TextComponent style={[styles.subInfoText, { color: themeColors.color_text_2 }]}>
                                        {` ± ${item.setting_preference.takeout_min_time} min`}
                                    </TextComponent>
                                </View>

                                {currentOrderType === ORDER_TYPE.DELIVERY && (
                                    <>
                                        <View style={styles.bottomInfoContainer}>
                                            <MotoBikeIcon
                                                fill={themeColors.color_primary}
                                                width={Dimens.W_12}
                                                height={Dimens.W_11}
                                            />

                                            <TextComponent style={[styles.subInfoText, { color: themeColors.color_text_2 }]}>
                                                {` ${formatCurrency(item?.setting_delivery_conditions[0]?.price, DEFAULT_CURRENCY)[2]}${item?.setting_delivery_conditions[0]?.price}.00`}
                                            </TextComponent>
                                        </View>
                                        <View style={styles.bottomInfoContainer}>
                                            <MoneyMinIcon
                                                fill={themeColors.color_primary}
                                                width={Dimens.W_22}
                                                height={Dimens.W_8}
                                            />

                                            <TextComponent style={[styles.subInfoText, { color: themeColors.color_text_2 }]}>
                                                {` ${formatCurrency(item?.setting_delivery_conditions[0]?.price_min, DEFAULT_CURRENCY)[2]}${item?.setting_delivery_conditions[0]?.price_min}.00`}
                                            </TextComponent>
                                        </View>
                                    </>
                                )}

                            </View>
                        )}

                    </View>

                    <View style={styles.rightInfoContainer}>
                        <View>
                            <TextComponent style={[styles.distanceText, { color: themeColors.color_common_description_text }]}>
                                {`${(Number(item.distance) / 1000).toFixed(1)} ${DEFAULT_DISTANCE_UNIT.toLowerCase()}`}
                            </TextComponent>
                            {(currentOrderType !== ORDER_TYPE.GROUP_ORDER && item.is_open) ? (
                            <TextComponent style={[styles.statusText, { color: themeColors.color_primary }]}>
                                {t('restaurant_state_open')}
                            </TextComponent>
                        ) : null}

                        </View>

                        <View style={styles.iconWrapper}>
                            {item.favoriet_friet && (
                                <ImageComponent
                                    resizeMode='stretch'
                                    source={Images.icon_friet}
                                    style={styles.iconFavorite}
                                />
                            )}

                            {item.kokette_kroket && (
                                <ImageComponent
                                    resizeMode='stretch'
                                    source={Images.icon_kroket}
                                    style={styles.iconFavorite}
                                />
                            )}
                            {showLoyalty && (
                                <BowIcon
                                    stroke={themeColors.color_primary}
                                    width={Dimens.W_12}
                                    height={Dimens.W_16}
                                />
                            )}
                        </View>
                    </View>
                </View>
            </ShadowView>

        </TouchableComponent>
    );
};

export default memo(RestaurantListItem);

const stylesF = (Dimens: DimensType) => StyleSheet.create({
    shadowStyle: { width: '100%', marginBottom: Dimens.H_20, borderRadius: Dimens.H_5 ,
        shadowColor: '#00000003', shadowOffset: { width: 0, height: Dimens.H_2 }, shadowRadius: Dimens.H_10 },
    statusText: {
        fontSize: Dimens.FONT_12,
        fontWeight: '700',
        textAlign: 'right',
    },
    distanceText: {
        fontSize: Dimens.FONT_12,
        fontWeight: '400',
        textAlign: 'right',
    },
    rightInfoContainer: {
        marginLeft: Dimens.W_8,
        justifyContent: 'space-between',
    },
    subInfoText: {
        fontSize: Dimens.FONT_10,
        fontWeight: '400',
        marginLeft: Dimens.W_2,
    },
    bottomInfoContainer: {
        flexDirection: 'row',
        alignItems: 'center',
        marginRight: Dimens.W_4,
    },
    subInfoContainer: { flexDirection: 'row', alignItems: 'center' },
    resCategory: { fontSize: Dimens.FONT_12, fontWeight: '400' },
    resName: { fontSize: Dimens.FONT_16, fontWeight: '700' },
    infoContainer: {
        marginLeft: Dimens.W_8,
        flex: 1,
        justifyContent: 'space-between',
    },
    itemImage: {
        width: Dimens.W_100 / 1.6,
        height: Dimens.W_100 / 1.6,
        borderRadius: Dimens.W_5,
    },
    itemContainer: {
        flexDirection: 'row',
        padding: Dimens.H_10,
        // marginBottom: Dimens.H_18,
        borderRadius: Dimens.H_5,
    },
    iconFavorite: {
        width: Dimens.W_15,
        height: Dimens.W_15 * 1.4,
        borderRadius: 0,
        marginRight: 3,
    },
    iconWrapper: {
        flexDirection: 'row',
        alignItems: 'center',
        justifyContent: 'flex-end',
    },
});