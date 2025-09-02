import React, {
    FC,
    useCallback,
    useMemo,
} from 'react';

import {
    StyleSheet,
    TouchableOpacity,
    View,
} from 'react-native';

import FlatListComponent from '@src/components/FlatListComponent';
import TextComponent from '@src/components/TextComponent';
import { Colors } from '@src/configs';
import {
    DATE_FORMAT,
    MONTH_FORMAT,
} from '@src/configs/constants';
import useDimens, { DimensType } from '@src/hooks/useDimens';
import useThemeColors from '@src/themes/useThemeColors';
import moment from '@src/utils/moment';

interface IProps {
    month: string,
    minDate: string,
    maxDate: string,
    maxAvailableDate: string,
    disableDates: Array<string>,
    disableDays: Array<number>,
    selectedDate: string,
    onSelectedDate: Function,
}

const MonthView: FC<IProps> = ({ month, minDate, disableDates, disableDays, selectedDate, onSelectedDate, maxAvailableDate }) => {
    const { themeColors } = useThemeColors();
    const Dimens = useDimens();
    const styles = stylesF(Dimens);

    const CALENDAR_DATE_ITEM_WIDTH = (Dimens.SCREEN_WIDTH / 1.14 - Dimens.W_48) / 7;
    const CALENDAR_DATE_ITEM_HEIGHT = (Dimens.SCREEN_HEIGHT / 2.6 - Dimens.H_38 - Dimens.H_44 - Dimens.H_16) / 6;

    const genMonthData = useMemo(() => {
        const allDayInMonth = new Array(moment(month, MONTH_FORMAT).daysInMonth())
                .fill(null)
                .map((x, i) => ({ date:  moment(month, MONTH_FORMAT).startOf('month').add(i, 'days').format(DATE_FORMAT) })); // '03/08/2022'

        if (allDayInMonth.length) {
            const firstDayOfMonth = moment(allDayInMonth[0].date, DATE_FORMAT);
            const lastDayOfMonth = moment(allDayInMonth[allDayInMonth.length - 1 ].date, DATE_FORMAT);

            const startOfFirstWeek = firstDayOfMonth.clone().startOf('week');
            const endOfLastWeek = lastDayOfMonth.clone().endOf('week');

            const diffStart = firstDayOfMonth.diff(startOfFirstWeek, 'days');
            const diffEnd = endOfLastWeek.diff(lastDayOfMonth, 'days');

            const startEmptyDate = Array.from({ length: diffStart }, () => (
                {
                    isEmpty: true
                }
            ));

            const endEmptyDate = Array.from({ length: diffEnd }, () => (
                {
                    isEmpty: true
                }
            ));

            return [...startEmptyDate, ...allDayInMonth, ...endEmptyDate];

        }

        return [];
    }, [month]);

    const renderItemDate = useCallback(({ item } : {item: any}) => {
        const isEmptyDay = item.isEmpty;
        const isSelected = item?.date === selectedDate;

        let disabled = false;

        if (!isEmptyDay) {
            const beforeMinDate = moment(item?.date, DATE_FORMAT).isBefore(moment(minDate, DATE_FORMAT));
            const afterMaxAvailableDate = moment(item?.date, DATE_FORMAT).isAfter(moment(maxAvailableDate, DATE_FORMAT));
            const inDisableDate = disableDates.includes(item?.date);
            const inDisableDays = disableDays.includes(Number(moment(item?.date, DATE_FORMAT).format('d')));

            disabled = beforeMinDate || inDisableDate || inDisableDays || afterMaxAvailableDate;
        }

        return isEmptyDay ? (
            <View
                style={[styles.itemDayContainer, { width: CALENDAR_DATE_ITEM_WIDTH, height: CALENDAR_DATE_ITEM_HEIGHT }]}
            />
        ) : (
            <TouchableOpacity
                disabled={disabled}
                style={[styles.itemDayContainer, { width: CALENDAR_DATE_ITEM_WIDTH, height: CALENDAR_DATE_ITEM_HEIGHT }]}
                onPress={() => onSelectedDate(item.date)}
            >
                <View style={[styles.todayHighLight, { backgroundColor: isSelected ? themeColors.color_primary : themeColors.color_card_background }]}>
                    <TextComponent style={[styles.textDate, { color: isSelected ? Colors.COLOR_WHITE : disabled ? themeColors.color_common_description_text : themeColors.color_text_2 }]}>
                        {moment(item.date, DATE_FORMAT).format('D')}
                    </TextComponent>
                </View>
            </TouchableOpacity>
        );
    }, [CALENDAR_DATE_ITEM_HEIGHT, CALENDAR_DATE_ITEM_WIDTH, disableDates, disableDays, maxAvailableDate, minDate, onSelectedDate, selectedDate, styles.itemDayContainer, styles.textDate, styles.todayHighLight, themeColors.color_card_background, themeColors.color_common_description_text, themeColors.color_primary, themeColors.color_text_2]);

    return (
        <FlatListComponent
            showsVerticalScrollIndicator={false}
            scrollEnabled={false}
            numColumns={7}
            data={genMonthData}
            renderItem={renderItemDate}
        />
    );
};

export default React.memo(MonthView);

const stylesF = (Dimens: DimensType) => StyleSheet.create({
    itemDayContainer: {
        paddingVertical: Dimens.H_12,
        justifyContent: 'center',
        alignItems: 'center',
    },
    textDate: {
        fontSize: Dimens.FONT_14,
        fontWeight: '400',
        textAlign: 'center',
        width: '100%'
    },
    todayHighLight: {
        borderRadius: Dimens.RADIUS_24,
        width: Dimens.W_26,
        height: Dimens.W_26,
        justifyContent: 'center',
        alignItems: 'center',
    },
});