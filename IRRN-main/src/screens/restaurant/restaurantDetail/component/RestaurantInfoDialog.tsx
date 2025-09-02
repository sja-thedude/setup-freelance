import React, {
    useCallback,
    useMemo,
    useRef,
    useState,
} from 'react';

import { uniqBy } from 'lodash';
import { useTranslation } from 'react-i18next';
import {
    StyleSheet,
    TouchableOpacity,
    View,
} from 'react-native';
import LinearGradient from 'react-native-linear-gradient';
import MapView, {
    Marker,
    PROVIDER_GOOGLE,
} from 'react-native-maps';

import { useLayout } from '@react-native-community/hooks';
import { DropDownIcon } from '@src/assets/svg';
import DialogComponent from '@src/components/DialogComponent';
import ScrollViewComponent from '@src/components/ScrollViewComponent';
import ShadowView from '@src/components/ShadowView';
import TextComponent from '@src/components/TextComponent';
import TouchableComponent from '@src/components/TouchableComponent';
import {
    DEFAULT_CURRENCY,
    OPENING_TIME_TYPE,
} from '@src/configs/constants';
import useDimens, { DimensType } from '@src/hooks/useDimens';
import { RestaurantDetailModel } from '@src/network/dataModels/RestaurantDetailModel';
import { RestaurantFavoriteItemModel } from '@src/network/dataModels/RestaurantFavoriteItemModel';
import {
    RestaurantNearbyItemModel,
    SettingDeliveryMinCondition,
    SettingOpenHourShort,
} from '@src/network/dataModels/RestaurantNearbyItemModel';
import { RestaurantRecentItemModel } from '@src/network/dataModels/RestaurantRecentItemModel';
import useThemeColors from '@src/themes/useThemeColors';
import { openMap } from '@src/utils';
import formatCurrency from '@src/utils/currencyFormatUtil';
import { getStatusBarHeight } from '@src/utils/iPhoneXHelper';
import moment from '@utils/moment';

interface ModalProps {
    isShow: boolean,
    hideModal: () => void,
    restaurantInfo?: RestaurantNearbyItemModel | RestaurantFavoriteItemModel | RestaurantRecentItemModel | RestaurantDetailModel | null,
    deliveryCondition?: SettingDeliveryMinCondition,
    openingHour?: Array<SettingOpenHourShort>,
}

const RestaurantInfoDialog = ({ isShow, hideModal, restaurantInfo, deliveryCondition, openingHour }: ModalProps) => {
    const Dimens = useDimens();
    const styles = stylesF(Dimens);

    const { themeColors } = useThemeColors();
    const { t } = useTranslation();

    const [selectType, setSelectType] = useState(0);

    const renderFee = useCallback(() => {
        const activeDelivery = openingHour?.find((i) => i.type === OPENING_TIME_TYPE.DELIVERY)?.active;

        if (!activeDelivery) {
            return null;
        }

        let price = `${formatCurrency(deliveryCondition?.price || 0, DEFAULT_CURRENCY)[2]}${deliveryCondition?.price}`;

        if (Number(deliveryCondition?.free.replace('.', '')) === 0) {
            price = t('text_free');
        }

        return (
            <View style={[{ marginTop: Dimens.H_20 }, styles.topContainer]}>
                <View style={styles.costContainer}>
                    <TextComponent
                        numberOfLines={1}
                        style={styles.costTitle}
                    >
                        {t('text_delivery_cost')}
                    </TextComponent>
                    <TextComponent
                        numberOfLines={1}
                        style={[styles.costValue, { color: themeColors.color_primary, }]}
                    >
                        {price}
                    </TextComponent>
                </View>
                <View style={styles.costContainer}>
                    <TextComponent
                        numberOfLines={1}
                        style={styles.costTitle}
                    >
                        {t('text_delivery_minimum')}
                    </TextComponent>
                    <TextComponent
                        numberOfLines={1}
                        style={[styles.costValue, { color: themeColors.color_primary, }]}
                    >
                        {`${formatCurrency(deliveryCondition?.price_min || 0, DEFAULT_CURRENCY)[2]}${deliveryCondition?.price_min}`}

                    </TextComponent>
                </View>
                <View style={styles.costContainer}>
                    <TextComponent
                        numberOfLines={1}
                        style={styles.costTitle}
                    >
                        {t('text_delivery_waiting_time_minimum')}
                    </TextComponent>
                    <TextComponent
                        numberOfLines={1}
                        style={[styles.costValue, { color: themeColors.color_primary, }]}
                    >
                        {`${deliveryCondition?.delivery_min_time} min.`}
                    </TextComponent>
                </View>
            </View>
        );
    }, [Dimens.H_20, deliveryCondition?.delivery_min_time, deliveryCondition?.free, deliveryCondition?.price, deliveryCondition?.price_min, openingHour, styles.costContainer, styles.costTitle, styles.costValue, styles.topContainer, t, themeColors.color_primary]);

    const [heightScrollView, setHeightScrollView] = useState<number>(0);
    const { onLayout, height: heightView } = useLayout();

    const onContentSizeChange = useCallback((_w: number, h: number) => {
        setHeightScrollView(h);
    }, []);

    const isShowArrow = useMemo(() => heightScrollView > (Dimens.SCREEN_HEIGHT - getStatusBarHeight() - 50) - heightView, [Dimens.SCREEN_HEIGHT, heightScrollView, heightView]);

    const renderOpeningTime = useCallback(() => {
        const timeData = openingHour?.filter((i) => i.active)[selectType]?.timeslots;
        const activeOpeningHour = openingHour?.filter((i) => i.active) || [];

        const timeDataConvert = uniqBy(timeData, 'day_number').map((item) => {
            const duplicates = timeData?.filter((i) => i.day_number === item.day_number);

            return {
                ...item,
                open_time: duplicates?.map((d) => ({ start_time: d.start_time, end_time: d.end_time })),
            };
        });

        return (
            <View style={{ marginBottom: Dimens.H_8 }}>
                <View style={[
                    styles.timeHeaderRowContainer,
                    styles.topContainer,
                    { justifyContent: activeOpeningHour.length > 2 ? 'space-between' : undefined }
                ]}
                >
                    {activeOpeningHour.map((item, index) => (
                        <TouchableComponent
                            key={index}
                            onPress={() => setSelectType(index)}
                            style={{
                                flex: activeOpeningHour.length > 2 ? undefined : 1,
                            }}
                        >
                            <TextComponent
                                style={[
                                    styles.timeText,
                                    {
                                        fontWeight: selectType === index ? '700' : '400',
                                        color: selectType === index ? themeColors.color_primary : themeColors.color_common_subtext,
                                    }
                                ]}
                            >
                                {item.type_display}
                            </TextComponent>
                        </TouchableComponent>
                    ))}
                </View>
                <View style={[styles.timeValueContainer, styles.topContainer]}>
                    {timeDataConvert?.map((item, index) => (
                        <TouchableComponent
                            key={index}
                            activeOpacity={1}
                            style={styles.timeValueWrapper}
                        >
                            <TextComponent
                                key={index}
                                style={[styles.timeValueText, { color: themeColors.color_text, }]}
                            >
                                {item.day_number_display}
                            </TextComponent>

                            <View>
                                {item.open_time?.map((i, idx) => (
                                    <TextComponent
                                        key={idx}
                                        style={[styles.rightText, { color: themeColors.color_common_description_text, fontVariant: ['tabular-nums'] }]}
                                    >
                                        {i.start_time && i.end_time ? `${moment(i.start_time, 'HH:mm:ss').format('HH:mm')} - ${moment(i.end_time, 'HH:mm:ss').format('HH:mm')}` : t('restaurant_state_close')}
                                    </TextComponent>
                                ))}
                            </View>
                        </TouchableComponent>

                    ))}
                </View>
            </View>
        );

    }, [Dimens.H_8, openingHour, selectType, styles.rightText, styles.timeHeaderRowContainer, styles.timeText, styles.timeValueContainer, styles.timeValueText, styles.timeValueWrapper, styles.topContainer, t, themeColors.color_common_description_text, themeColors.color_common_subtext, themeColors.color_primary, themeColors.color_text]);

    const renderMap = useCallback(() => (
        restaurantInfo?.lat && restaurantInfo?.lng ? (
                    <ShadowView
                        style={styles.shadowStyle}
                    >
                        <View style={styles.mapContainer}>
                            <MapView
                                loadingEnabled
                                style={styles.map}
                                provider={PROVIDER_GOOGLE}
                                region={{
                                    latitude: restaurantInfo?.lat ? Number(restaurantInfo.lat) : 37.78825,
                                    longitude: restaurantInfo?.lng ? Number(restaurantInfo.lng) : -122.4324,
                                    latitudeDelta: 0.0050,
                                    longitudeDelta: 0.0050,
                                }}
                            >
                                <Marker
                                    coordinate={{ latitude :  Number(restaurantInfo?.lat) , longitude : Number(restaurantInfo?.lng) }}
                                />
                            </MapView>
                            <TouchableComponent
                                onPress={() => openMap( Number(restaurantInfo?.lat), Number(restaurantInfo?.lng), restaurantInfo?.setting_generals?.title || '')}
                                style={styles.gradientContainer}
                            >
                                <LinearGradient
                                    colors={['#00000000', '#00000050', '#00000090']}
                                    style={styles.gradient}
                                />
                            </TouchableComponent>
                        </View>
                    </ShadowView>
        ) : <View style={{ height: Dimens.COMMON_BOTTOM_PADDING }}/>

    ), [Dimens.COMMON_BOTTOM_PADDING, restaurantInfo?.lat, restaurantInfo?.lng, restaurantInfo?.setting_generals?.title, styles.gradient, styles.gradientContainer, styles.map, styles.mapContainer, styles.shadowStyle]);

    const refScrollViewComponent = useRef<any>();

    return (
        <DialogComponent
            hideModal={hideModal}
            isVisible={isShow}
            containerStyle={{ paddingHorizontal: 0, maxHeight: Dimens.SCREEN_HEIGHT - getStatusBarHeight() - 50, paddingBottom: 0 }}
        >
            <ScrollViewComponent
                scrollEventThrottle={16}
                ref={refScrollViewComponent}
                onContentSizeChange={onContentSizeChange}
            >
                <TextComponent
                    numberOfLines={1}
                    style={[styles.restaurantName, styles.topContainer]}
                >
                    {restaurantInfo?.setting_generals?.title}
                </TextComponent>

                <TextComponent
                    numberOfLines={2}
                    style={[styles.restaurantAdd, { color: themeColors.color_common_subtext }, styles.topContainer]}
                >
                    {restaurantInfo?.address}
                </TextComponent>
                <TextComponent
                    numberOfLines={1}
                    style={[styles.restaurantAdd, { color: themeColors.color_common_subtext }, styles.topContainer]}
                >
                    {`BTW: ${restaurantInfo?.btw_nr}`}
                </TextComponent>

                {renderFee()}
                {renderOpeningTime()}

            </ScrollViewComponent>

            {isShowArrow && (
                <TouchableOpacity
                    onPress={() => refScrollViewComponent.current?.scrollToEnd({ animated: true })}
                    style={[styles.viewArrow]}
                >
                    <TextComponent style={[styles.textViewMore, { color: themeColors.color_primary }]}>{t('options_view_more')}</TextComponent>
                    <DropDownIcon
                        width={10}
                        height={8}
                        stroke={themeColors.color_primary}
                    />
                </TouchableOpacity>
            )}

            <View onLayout={onLayout} >
                {renderMap()}
            </View>
        </DialogComponent>
    );
};

export default RestaurantInfoDialog;

const stylesF = (Dimens: DimensType) => StyleSheet.create({
    shadowStyle: { shadowRadius: Dimens.H_20, shadowColor: '#00000010', shadowOffset: { width: 0, height: -Dimens.H_3 } },
    restaurantAdd: { fontSize: Dimens.FONT_16, marginTop: Dimens.H_4 },
    restaurantName: { fontSize: Dimens.FONT_22, fontWeight: '700' },
    topContainer: { paddingHorizontal: Dimens.W_20 },
    gradient: { width: '100%', height: '100%' },
    gradientContainer: {
        position: 'absolute',
        top: 0,
        left: 0,
        right: 0,
        bottom: 0,
    },
    mapContainer: {
        height: Dimens.SCREEN_HEIGHT / 4,
        // marginHorizontal: 0,
        borderTopStartRadius: Dimens.RADIUS_30,
        borderTopEndRadius: Dimens.RADIUS_30,
        // overflow: 'hidden',
        width: Dimens.SCREEN_WIDTH,
    },
    rightText: {
        fontSize: Dimens.FONT_16,
        marginTop: Dimens.H_4,
    },
    timeValueText: {
        fontSize: Dimens.FONT_16,
        marginTop: Dimens.H_4,
    },
    timeValueWrapper: {
        flexDirection: 'row',
        justifyContent: 'space-between',
        paddingBottom: Dimens.H_4,
    },
    timeValueContainer: {
        // flexDirection: 'row',
        // maxHeight: Dimens.SCREEN_HEIGHT / 4,
        marginTop: Dimens.H_8,
    },
    timeText: {
        fontSize: Dimens.FONT_16,
    },
    timeHeaderRowContainer: {
        flexDirection: 'row',
        alignItems: 'center',
        justifyContent: 'space-between',
        marginTop: Dimens.H_24,
    },
    costValue: {
        fontSize: Dimens.FONT_16,
        fontWeight: '700',
        textAlign: 'right',
        flex: 1,
    },
    costTitle: { fontSize: Dimens.FONT_16, fontWeight: '700', flex: 4 },
    costContainer: {
        flexDirection: 'row',
        alignItems: 'center',
        marginBottom: Dimens.H_8,
    },
    dialogContainer: {
        // borderRadius: Dimens.RADIUS_6,
        // paddingTop: Dimens.H_8,
        // marginBottom: -Dimens.COMMON_BOTTOM_PADDING * 2,
        // maxHeight: Dimens.SCREEN_HEIGHT - getStatusBarHeight(),
        // width: '100%',
    },
    map: {
        width: '100%',
        height: '100%',
    },
    viewArrow: { alignItems: 'center', justifyContent: 'center', flexDirection: 'row', overflow: 'hidden', paddingVertical: 7 },
    textViewMore: { fontWeight: '700', fontSize: Dimens.FONT_14, marginRight: 5, },
});