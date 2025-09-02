import React, {
    FC,
    useCallback,
    useEffect,
    useMemo,
    useRef,
    useState,
} from 'react';

import { chunk } from 'lodash';
import {
    FlatList,
    StyleSheet,
    View,
} from 'react-native';

import {
    ChevronLeftIcon,
    ChevronRightIcon,
} from '@src/assets/svg';
import FlatListComponent from '@src/components/FlatListComponent';
import TextComponent from '@src/components/TextComponent';
import TouchableComponent from '@src/components/TouchableComponent';
import { Colors } from '@src/configs';
import { useAppSelector } from '@src/hooks';
import useDimens, { DimensType } from '@src/hooks/useDimens';
import { Timeslot } from '@src/network/dataModels/TimeSlotModel';
import useThemeColors from '@src/themes/useThemeColors';
import { getOrderPrices } from '@src/utils';
import moment from '@src/utils/moment';

import { CartInfo } from '../CartScreen';

interface IProps {
    cartInfo: CartInfo,
    updateCartInfo: Function,
}

const TimeSlotView: FC<IProps> = ({ cartInfo, updateCartInfo }) => {
    const { themeColors } = useThemeColors();
    const Dimens = useDimens();
    const styles = stylesF(Dimens);

    const timeSlotItemRowWidth =  Dimens.SCREEN_WIDTH - Dimens.W_37 * 2 - Dimens.W_7 * 2 - Dimens.W_10 * 2;
    const timeSlotItemWidth =  (timeSlotItemRowWidth - Dimens.W_8 * 4) / 4;
    const timeSlotItemHeight =  timeSlotItemWidth / 1.6;

    const [currentIndex, setCurrentIndex] = useState(0);

    const listRef = useRef<FlatList>(null);

    const cartProducts = useAppSelector((state) => state.storageReducer.cartProducts.data);
    const cartDeliveryFee = useAppSelector((state) => state.storageReducer.cartProducts.deliveryInfo.deliveryFee);
    const discountInfo = useAppSelector((state) => state.storageReducer.cartProducts.discountInfo);
    const groupData = useAppSelector((state) => state.storageReducer.cartProducts.groupFilter.groupData);
    const orderType = useAppSelector((state) => state.storageReducer.cartProducts.type);
    const { isServiceCostOn, isServiceCostAlwaysCharge, serviceCost, serviceCostAmount } = useAppSelector((state) => state.storageReducer.cartProducts.serviceCostInfo || {});

    const totalPrice = useMemo(() => {
        const { total } = getOrderPrices(
                cartProducts,
                cartDeliveryFee,
                discountInfo,
                groupData,
                orderType,
                isServiceCostOn,
                isServiceCostAlwaysCharge,
                serviceCost,
                serviceCostAmount
        );
        return total;
    }, [cartDeliveryFee, cartProducts, discountInfo, groupData, isServiceCostAlwaysCharge, isServiceCostOn, orderType, serviceCost, serviceCostAmount]);

    const onScrollEnd = useCallback((e: any) =>  {
        let contentOffset = e.nativeEvent.contentOffset;
        let viewSize = e.nativeEvent.layoutMeasurement;
        let pageNum = Math.round(contentOffset.x / viewSize.width);
        setCurrentIndex(pageNum);
    }, []);

    const handleNext = useCallback(() => {
        listRef.current?.scrollToIndex({ index: currentIndex + 1, animated: true });
        setCurrentIndex(currentIndex + 1);
    }, [currentIndex]);

    const handlePrev = useCallback(() => {
        listRef.current?.scrollToIndex({ index: currentIndex - 1, animated: true });
        setCurrentIndex(currentIndex - 1);
    }, [currentIndex]);

    useEffect(() => {
        if (cartInfo.timeSlots?.length) {
            listRef.current?.scrollToIndex({ index: 0, animated: true });
            setCurrentIndex(0);
        }
    }, [cartInfo.timeSlots?.length]);

    const renderTimeSlotItemRow = useCallback((item: Array<Timeslot>) => {
        if (!item) {
            return null;
        }

        return (
            <View style={styles.timeSlotRow}>
                {item.map((timeSlot, index) => {
                    let disable = !timeSlot.active;
                    let selected = timeSlot.time === cartInfo.time;

                    if (!disable) {
                        if (timeSlot.current_order >= timeSlot.max_order) {
                            disable = true;
                        }
                        if (Number(timeSlot.current_price) >= Number(timeSlot.max_price) || Number(totalPrice) > Number(timeSlot.max_price) || (Number(totalPrice) + Number(timeSlot.current_price) > Number(timeSlot.max_price))) {
                            disable = true;
                        }
                    }

                    if (selected && disable) {
                        updateCartInfo({ time: null });
                    }

                    return (
                        <TouchableComponent
                            disabled={disable}
                            key={index}
                            onPress={() => updateCartInfo({
                                time: timeSlot.time,
                                setting_timeslot_detail_id: timeSlot.id
                            })}
                            style={[
                                styles.timeSlotContainer, {
                                    borderColor: disable ? themeColors.color_common_description_text : themeColors.color_primary,
                                    backgroundColor: selected ? themeColors.color_primary : 'transparent',
                                    width: timeSlotItemWidth,
                                    height: timeSlotItemHeight,
                                }
                            ]}
                        >
                            <TextComponent style={{
                                fontWeight: '600',
                                color: disable ? themeColors.color_common_description_text : selected ? Colors.COLOR_WHITE : themeColors.color_primary
                            }}
                            >
                                {moment(timeSlot.time, 'HH:mm:ss').format('HH:mm')}
                            </TextComponent>
                        </TouchableComponent>
                    );
                })}

            </View>
        );
    }, [cartInfo.time, styles.timeSlotContainer, styles.timeSlotRow, themeColors.color_common_description_text, themeColors.color_primary, timeSlotItemHeight, timeSlotItemWidth, totalPrice, updateCartInfo]);

    const renderTimeSlotItem = useCallback(({ item } : {item: Array<Timeslot>}) => (
        <View style={{ width: timeSlotItemRowWidth }}>
            {renderTimeSlotItemRow(chunk(item, 4)[0])}
            {renderTimeSlotItemRow(chunk(item, 4)[1])}
            {renderTimeSlotItemRow(chunk(item, 4)[2])}
        </View>
    ), [renderTimeSlotItemRow, timeSlotItemRowWidth]);

    const renderPrevArrow = useMemo(() => (
        <TouchableComponent
            hitSlop={Dimens.DEFAULT_HIT_SLOP}
            disabled={currentIndex === 0}
            onPress={handlePrev}
        >
            <ChevronLeftIcon
                width={Dimens.W_7}
                height={Dimens.W_12}
                stroke={currentIndex > 0 ? themeColors.color_text : 'transparent'}
            />
        </TouchableComponent>
    ), [Dimens.DEFAULT_HIT_SLOP, Dimens.W_12, Dimens.W_7, currentIndex, handlePrev, themeColors.color_text]);

    const renderTimeSlot = useMemo(() => (
        <FlatListComponent
            ref={listRef}
            horizontal
            pagingEnabled
            data={chunk(cartInfo.timeSlots, 12)}
            renderItem={renderTimeSlotItem}
            showsVerticalScrollIndicator={false}
            style={{ marginHorizontal: Dimens.W_10 }}
            onMomentumScrollEnd={onScrollEnd}
            snapToOffsets={chunk(cartInfo.timeSlots, 12).map((x, i) => ((i * timeSlotItemRowWidth)))}
            snapToAlignment={'center'}
            decelerationRate={'fast'}
        />
    ), [Dimens.W_10, cartInfo.timeSlots, onScrollEnd, renderTimeSlotItem, timeSlotItemRowWidth]);

    const renderNextArrow = useMemo(() => (
        <TouchableComponent
            hitSlop={Dimens.DEFAULT_HIT_SLOP}
            disabled={currentIndex === chunk(cartInfo.timeSlots, 12).length - 1}
            onPress={handleNext}
        >
            <ChevronRightIcon
                width={Dimens.W_7}
                height={Dimens.W_12}
                stroke={currentIndex < chunk(cartInfo.timeSlots, 12).length - 1 ? themeColors.color_text : 'transparent'}
            />
        </TouchableComponent>
    ), [Dimens.DEFAULT_HIT_SLOP, Dimens.W_12, Dimens.W_7, cartInfo.timeSlots, currentIndex, handleNext, themeColors.color_text]);

    return (
        <View style={styles.mainContainer}>
            {renderPrevArrow}
            {renderTimeSlot}
            {renderNextArrow}
        </View>
    );
};

export default React.memo(TimeSlotView);

const stylesF = (Dimens: DimensType) => StyleSheet.create({
    mainContainer: {
        flexDirection: 'row',
        alignItems: 'center',
        marginTop: Dimens.H_24,
    },
    timeSlotRow: { flexDirection: 'row', marginTop: Dimens.W_8, width: '100%' },
    timeSlotContainer: {
        alignItems: 'center',
        justifyContent: 'center',
        borderRadius: Dimens.RADIUS_6,
        borderWidth: 1,
        marginHorizontal: Dimens.W_4,
    },
});