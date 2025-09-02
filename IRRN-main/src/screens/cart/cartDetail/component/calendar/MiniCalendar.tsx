import React, {
    FC,
    useCallback,
    useEffect,
    useMemo,
    useRef,
    useState,
} from 'react';

import {
    StyleSheet,
    View,
} from 'react-native';

import { FlashList } from '@shopify/flash-list';
import {
    ChevronLeftIcon,
    ChevronRightIcon,
} from '@src/assets/svg';
import ShadowView from '@src/components/ShadowView';
import TextComponent from '@src/components/TextComponent';
import TouchableComponent from '@src/components/TouchableComponent';
import {
    DATE_FORMAT,
    MONTH_FORMAT,
} from '@src/configs/constants';
import useDimens, { DimensType } from '@src/hooks/useDimens';
import useThemeColors from '@src/themes/useThemeColors';
import { capitalizeFirstLetter } from '@src/utils';
import moment from '@src/utils/moment';

import MonthView from './MonthView';

interface IProps {
    minDate: string,
    maxDate: string,
    maxAvailableDate: string,
    disableDates: Array<string>,
    disableDays: Array<number>,
    selectedDate: string,
    onSelectedDate: Function,
}

const MiniCalendar: FC<IProps> = ({ minDate, maxDate, disableDates, disableDays, selectedDate, onSelectedDate, maxAvailableDate }) => {
    const { themeColors } = useThemeColors();
    const Dimens = useDimens();
    const styles = stylesF(Dimens);

    const CALENDAR_DATE_ITEM_HEIGHT = (Dimens.SCREEN_HEIGHT / 2.6 - Dimens.H_38 - Dimens.H_44 - Dimens.H_16) / 6;

    const listRef = useRef <FlashList<any>>(null);

    const allMonth = useMemo(() => {
        const startDate = moment(minDate, DATE_FORMAT);
        const endDate = moment(maxDate, DATE_FORMAT);

        let interim = startDate.clone();
        let months = [];

        while (interim.isBefore(endDate)) {
            months.push(interim.format(MONTH_FORMAT)); // '12-2022'
            interim = interim.add(1, 'month');
        }

        return months;
    }, [maxDate, minDate]);

    const [currentIndex, setCurrentIndex] = useState(0);

    useEffect(() => {
        if (selectedDate) {
            setTimeout(() => {
                const initIndex = allMonth.findIndex((m) => moment(m, MONTH_FORMAT).isSame(moment(selectedDate, DATE_FORMAT).format(MONTH_FORMAT)));
                if (initIndex >= 0) {
                    setCurrentIndex(initIndex);
                    listRef.current?.scrollToIndex({ index: initIndex, animated: false });
                }
            }, 1000);
        }
    }, [allMonth, selectedDate]);

    const handleNextMonth = useCallback(() => {
        listRef.current?.scrollToIndex({ index: currentIndex + 1, animated: true });
        setCurrentIndex(currentIndex + 1);
    }, [currentIndex]);

    const handlePrevMonth = useCallback(() => {
        listRef.current?.scrollToIndex({ index: currentIndex - 1, animated: true });
        setCurrentIndex(currentIndex - 1);
    }, [currentIndex]);

    const onScrollEnd = useCallback((e: any) =>  {
        let contentOffset = e.nativeEvent.contentOffset;
        let viewSize = e.nativeEvent.layoutMeasurement;
        let pageNum = Math.floor(contentOffset.x / viewSize.width);
        setCurrentIndex(pageNum);
    }, []);

    const routes = useMemo(
            () => allMonth.map((month, index) => ({ key: `${index}`, title: '', data:  month })), [allMonth]
    );

    const renderItem = useCallback(({ item }: {item: any}) => (
        <MonthView
            month={item.data}
            minDate={minDate}
            maxDate={maxDate}
            maxAvailableDate={maxAvailableDate}
            disableDates={disableDates}
            disableDays={disableDays}
            selectedDate={selectedDate}
            onSelectedDate={onSelectedDate}
        />
    ), [disableDates, disableDays, maxAvailableDate, maxDate, minDate, onSelectedDate, selectedDate]);

    const renderItemDayHeader = useCallback((text: string) => (
        <TextComponent style={[styles.itemDay, { color: themeColors.color_common_subtext }]}>
            {text}
        </TextComponent>
    ), [styles.itemDay, themeColors.color_common_subtext]);

    const renderHeader = useMemo(() => (
        <>
            <View style={styles.headerContainer}>
                {currentIndex > 0 && (
                    <TouchableComponent
                        onPress={handlePrevMonth}
                        hitSlop={Dimens.DEFAULT_HIT_SLOP}
                    >
                        <ChevronLeftIcon
                            width={Dimens.W_8}
                            height={Dimens.W_14}
                            stroke={themeColors.color_primary}
                        />
                    </TouchableComponent>
                )}

                <TextComponent style={[styles.headerMonthText, { color: themeColors.color_common_subtext }]}>
                    {capitalizeFirstLetter(moment(allMonth[currentIndex], MONTH_FORMAT).format('MMM YYYY'))}
                </TextComponent>
                {currentIndex < allMonth.length - 1 && (
                    <TouchableComponent
                        onPress={handleNextMonth}
                        hitSlop={Dimens.DEFAULT_HIT_SLOP}
                    >
                        <ChevronRightIcon
                            width={Dimens.W_8}
                            height={Dimens.W_14}
                            stroke={themeColors.color_primary}
                        />
                    </TouchableComponent>
                )}
            </View>
            <View style={styles.dayOfWeekContainer}>
                {renderItemDayHeader(moment('12/12/2022', 'DD/MM/YYYY').format('dd').toUpperCase())}
                {renderItemDayHeader(moment('13/12/2022', 'DD/MM/YYYY').format('dd').toUpperCase())}
                {renderItemDayHeader(moment('14/12/2022', 'DD/MM/YYYY').format('dd').toUpperCase())}
                {renderItemDayHeader(moment('15/12/2022', 'DD/MM/YYYY').format('dd').toUpperCase())}
                {renderItemDayHeader(moment('16/12/2022', 'DD/MM/YYYY').format('dd').toUpperCase())}
                {renderItemDayHeader(moment('17/12/2022', 'DD/MM/YYYY').format('dd').toUpperCase())}
                {renderItemDayHeader(moment('18/12/2022', 'DD/MM/YYYY').format('dd').toUpperCase())}
            </View>
        </>
    ), [Dimens.DEFAULT_HIT_SLOP, Dimens.W_14, Dimens.W_8, allMonth, currentIndex, handleNextMonth, handlePrevMonth, renderItemDayHeader, styles.dayOfWeekContainer, styles.headerContainer, styles.headerMonthText, themeColors.color_common_subtext, themeColors.color_primary]);

    const renderDates = useMemo(() => (
        <View
            style={styles.mainContainer}
        >
            <FlashList
                ref={listRef}
                horizontal
                pagingEnabled
                data={routes}
                renderItem={renderItem}
                estimatedItemSize={CALENDAR_DATE_ITEM_HEIGHT * 6}
                showsVerticalScrollIndicator={false}
                onMomentumScrollEnd={onScrollEnd}
            />
        </View>
    ), [CALENDAR_DATE_ITEM_HEIGHT, onScrollEnd, renderItem, routes, styles.mainContainer]);

    return (
        <ShadowView
            style={{ shadowOffset: { width: 0, height: Dimens.H_6 }, shadowColor: '#00000008', shadowRadius: Dimens.H_8 }}
        >
            <View
                style={[styles.container, { backgroundColor: themeColors.color_card_background }]}
            >
                {renderHeader}
                {renderDates}
            </View>
        </ShadowView>
    );
};

export default React.memo(MiniCalendar);

const stylesF = (Dimens: DimensType) => StyleSheet.create({
    mainContainer: { height: '100%', alignItems: 'center' },
    dayOfWeekContainer: {
        flexDirection: 'row',
        width: '100%',
        marginTop: Dimens.H_20,
        marginBottom: Dimens.H_16,
    },
    headerMonthText: {
        fontSize: Dimens.FONT_16,
        marginHorizontal: Dimens.W_16,
    },
    headerContainer: { flexDirection: 'row', alignItems: 'center' },
    container: {
        width: Dimens.SCREEN_WIDTH / 1.14,
        height: Dimens.SCREEN_HEIGHT / 2.6,
        alignItems: 'center',
        borderRadius: Dimens.RADIUS_10,
        paddingHorizontal: Dimens.W_24,
        paddingBottom: Dimens.H_16,
        paddingTop: Dimens.H_22,
    },
    itemDay: {
        fontSize: Dimens.FONT_10,
        fontWeight: '700',
        textAlign: 'center',
        flex: 1,
    },
});
