import React, {
    forwardRef,
    memo,
    useCallback,
    useEffect,
    useImperativeHandle,
    useMemo,
    useState,
} from 'react';

import { uniq } from 'lodash';
import { useTranslation } from 'react-i18next';
import {
    StyleSheet,
    TouchableOpacity,
    View,
} from 'react-native';
import Popover from 'react-native-popover-view';
import { Placement } from 'react-native-popover-view/dist/Types';
import Animated, {
    FadeIn,
    FadeOut,
    Layout,
} from 'react-native-reanimated';

import { CalendarIcon } from '@src/assets/svg';
import TextComponent from '@src/components/TextComponent';
import {
    DATE_FORMAT,
    IS_ANDROID,
    ORDER_TYPE,
} from '@src/configs/constants';
import {
    useAppDispatch,
    useAppSelector,
} from '@src/hooks';
import useBoolean from '@src/hooks/useBoolean';
import useCallAPI from '@src/hooks/useCallAPI';
import useDimens, { DimensType } from '@src/hooks/useDimens';
import { GroupDetailModel } from '@src/network/dataModels/GroupDetailModel';
import { HolidayExceptionModel, } from '@src/network/dataModels/HolidayExceptionModel';
import { RestaurantSettingPreferenceModel, } from '@src/network/dataModels/RestaurantSettingPreferenceModel';
import { TimeSlotModel } from '@src/network/dataModels/TimeSlotModel';
import { validateTimeSlotService } from '@src/network/services/productServices';
import {
    fetchSettingHolidayException,
    fetchSettingPreference,
    getDetailGroupService,
    getRestaurantTimeSlotConditionService,
    getRestaurantTimeSlotService,
} from '@src/network/services/restaurantServices';
import { LoadingActions } from '@src/redux/toolkit/actions/loadingActions';
import useThemeColors from '@src/themes/useThemeColors';
import { getDaysBetweenDates } from '@src/utils/dateTimeUtil';
import moment from '@src/utils/moment';

import { CartInfo } from '../CartScreen';
import MiniCalendar from './calendar/MiniCalendar';
import ErrorInvalidProductTimeSlotDialog
    from './ErrorInvalidProductTimeSlotDialog';
import TimeSlotView from './TimeSlotView';

interface IProps {
    setDisableNextButton: Function,
    updateErrorMsg: Function,
    cartInfo: CartInfo,
    updateCartInfo: Function,
    handleNextStep: Function,
    handlePrevStep: Function,
}

const dateTimeFormat = `${DATE_FORMAT} HH:mm:ss`;

const CartSelectDateStep = forwardRef<any, IProps>(({ setDisableNextButton, cartInfo, updateCartInfo, handleNextStep, handlePrevStep, updateErrorMsg }, ref: any) => {
    const { themeColors } = useThemeColors();
    const Dimens = useDimens();
    const styles = stylesF(Dimens);

    const { t } = useTranslation();
    const dispatch = useAppDispatch();

    const [groupInactiveTimeSlot, setGroupInactiveTimeSlot] = useState<Array<number>>([]);
    const [disableDates, setDisableDates] = useState<Array<string>>([]);
    const [maxDatePreOrder, setMaxDatePreOrder] = useState<number>();
    const [minTimeOrder, setMinTimeOrder] = useState<number>();
    const [invalidProductMessage, setInvalidProductMessage] = useState<any>('');
    const [popupCloseText, setPopupCloseText] = useState<any>('');
    const [invalidDateTimeSlotProducts, setInvalidDateTimeSlotProducts] = useState<Array<any>>([]);

    const [isShowCalendar, showCalendar, hideCalendar] = useBoolean(false);
    const [isShowInvalidProductPopup, showInvalidProductPopup, hideInvalidProductPopup] = useBoolean(false);

    const cartProducts = useAppSelector((state) => state.storageReducer.cartProducts.data);
    const groupData = useAppSelector((state) => state.storageReducer.cartProducts.groupFilter.groupData);
    const orderType = useAppSelector((state) => state.storageReducer.cartProducts.type);
    const cartRestaurant = useAppSelector((state) => state.storageReducer.cartProducts.restaurant);

    const { callApi: getDetailGroup } = useCallAPI(
            getDetailGroupService,
            undefined,
            useCallback((data: GroupDetailModel) => {
                let inactiveDay = data.timeslots.filter((item) => item.status === 0).map((i) => i.day_number);
                setGroupInactiveTimeSlot(inactiveDay);

                if ( moment().isAfter(moment(data.close_time, 'HH:mm:ss'))) {
                    setDisableDates((state) => uniq([moment().format(DATE_FORMAT), ...state]));
                }
            }, []),
            undefined,
            true,
            false
    );

    const { callApi: fetchSettingHoliday } = useCallAPI(
            fetchSettingHolidayException,
            undefined,
            useCallback((data: Array<HolidayExceptionModel>) => {
                const holidays = data.map((item) => getDaysBetweenDates(item.start_time, item.end_time)).flat();
                setDisableDates((state) => uniq([...holidays, ...state]));
            }, []),
            undefined,
            true,
            false
    );

    const { callApi: fetchRestaurantSettingPreference } = useCallAPI(
            fetchSettingPreference,
            undefined,
            undefined,
            undefined,
            true,
            false,
    );

    const { callApi: getRestaurantTimeSlot } = useCallAPI(
            getRestaurantTimeSlotService,
            undefined,
            undefined,
            undefined,
            true,
            false,
    );

    const { callApi: getRestaurantTimeSlotCondition } = useCallAPI(
            getRestaurantTimeSlotConditionService,
            undefined,
            useCallback((data: any) => {
                const timeSlotDisableDates = Object.entries(data).filter((i) => i[1] === false).map((x) => x[0]).flat();
                setDisableDates((state) => uniq([...timeSlotDisableDates, ...state]));
            }, []),
            undefined,
            true,
            false,
    );

    const { callApi: validateTimeSlot } = useCallAPI(
            validateTimeSlotService,
            useCallback(() => {
                dispatch(LoadingActions.showGlobalLoading(true));
            }, [dispatch]),
            undefined,
            undefined,
            true,
            false
    );

    useEffect(() => {
        if (orderType === ORDER_TYPE.GROUP_ORDER) {
            setDisableNextButton(cartInfo.date === null);
        } else {
            setDisableNextButton(cartInfo.time === null);
        }

        return () => {
            setDisableNextButton(false);
        };
    }, [cartInfo.date, cartInfo.time, orderType, setDisableNextButton]);

    const onClickCalendar = useCallback(async () => {
        dispatch(LoadingActions.showGlobalLoading(true));
        setDisableDates([]);

        if (orderType === ORDER_TYPE.GROUP_ORDER) {
            Promise.all([
                getDetailGroup({
                    group_id: groupData?.id
                }),
                fetchSettingHoliday(cartRestaurant?.id)
            ]).then(() => {
                dispatch(LoadingActions.showGlobalLoading(false));
                showCalendar();
            });
        } else {
            Promise.all([
                fetchSettingHoliday(cartRestaurant?.id),
                fetchRestaurantSettingPreference(cartRestaurant?.id),
                getRestaurantTimeSlotCondition({
                    restaurant_id: cartRestaurant?.id,
                    type: orderType,
                }),
                getRestaurantTimeSlot({
                    restaurant_id: cartRestaurant?.id,
                    date: moment().format(DATE_FORMAT),
                    type: orderType,
                }),
            ]).then((result: any) => {
                dispatch(LoadingActions.showGlobalLoading(false));
                const preferenceData: RestaurantSettingPreferenceModel = result[1].data;
                const timeSlotData: TimeSlotModel = result[3].data;

                if (preferenceData) {
                    const maxOrderDate = orderType === ORDER_TYPE.TAKE_AWAY ? preferenceData.takeout_day_order : preferenceData.delivery_day_order;
                    const minOrderTime = orderType === ORDER_TYPE.TAKE_AWAY ? preferenceData.takeout_min_time : preferenceData.delivery_min_time;

                    setMaxDatePreOrder(maxOrderDate);
                    setMinTimeOrder(minOrderTime);

                    if (timeSlotData.max_mode) {
                        const timeSlotApplicableDays = timeSlotData.max_days; // days of week that can apply time slot setting
                        const limitBeforeDay: number = timeSlotData.max_before; // number of date before limit time
                        const limitBeforeTime = timeSlotData.max_time; // last hour that user can order

                        const days = getDaysBetweenDates(moment().format(DATE_FORMAT), moment().add(maxOrderDate, 'd').format(DATE_FORMAT));

                        const applicableDates = days.filter((d) => {
                            const dayOfWeek = moment(d, DATE_FORMAT).format('d');
                            return timeSlotApplicableDays.includes(Number(dayOfWeek));
                        })
                                .map((i) => moment(`${i} ${limitBeforeTime}`, dateTimeFormat).format(dateTimeFormat))
                                .filter((x) => {
                                    if (limitBeforeDay === 0) {
                                        return moment().isAfter(moment(x, dateTimeFormat));
                                    } else {
                                        return moment(x, dateTimeFormat).diff(moment(), 'h') < limitBeforeDay * 24;
                                    }
                                })
                                .map((y) => moment(y, dateTimeFormat).format(DATE_FORMAT));

                        const todayValidTimeSlot = timeSlotData.timeslots.filter((i) => {
                            const timeSlot = moment(`${moment().format(DATE_FORMAT)} ${i.time}`, dateTimeFormat);
                            return i.type === orderType && i.active && moment().add(minOrderTime, 'm').isBefore(timeSlot);
                        });

                        const todayFormat = todayValidTimeSlot.length === 0 ? [moment().format(DATE_FORMAT)] : [];

                        setDisableDates((state) => uniq([...todayFormat, ...applicableDates, ...state]));

                    }
                }
                showCalendar();
            });
        }
    }, [cartRestaurant?.id, dispatch, fetchRestaurantSettingPreference, fetchSettingHoliday, getDetailGroup, getRestaurantTimeSlot, getRestaurantTimeSlotCondition, groupData?.id, orderType, showCalendar]);

    const onSelectDate = useCallback((date: string) => {
        hideCalendar();
        setTimeout(() => {
            updateCartInfo({ date: date, time: null });
        }, 200);

        if (orderType !== ORDER_TYPE.GROUP_ORDER) {
            dispatch(LoadingActions.showGlobalLoading(true));
            getRestaurantTimeSlot({
                restaurant_id: cartRestaurant?.id,
                date: date,
                type: orderType,
            }).then((result) => {
                dispatch(LoadingActions.showGlobalLoading(false));
                if (result.success) {
                    const timeSlotData: TimeSlotModel = result.data;
                    const validTimeSlot = timeSlotData.timeslots.filter((i) => {
                        const timeSlot = moment(`${i.date} ${i.time}`, dateTimeFormat);
                        return i.type === orderType && moment().add(minTimeOrder, 'm').isBefore(timeSlot);
                    });

                    updateCartInfo({ timeSlots: validTimeSlot });
                }
            });
        }

    }, [cartRestaurant?.id, dispatch, getRestaurantTimeSlot, hideCalendar, minTimeOrder, orderType, updateCartInfo]);

    const handleNext = useCallback(() => {
        validateTimeSlot({
            date: cartInfo.date,
            product_id: cartProducts.map((product) => product.id),
        }).then((result) => {
            if (result.success) {
                const invalidDateProducts = Object.entries(result.data).filter((i) => i[1] === false);

                if (invalidDateProducts.length > 0) {
                    dispatch(LoadingActions.showGlobalLoading(false));

                    // update cart invalid product
                    const invalidProduct = invalidDateProducts.map((p) => p[1] === false ? Number(p[0]) : false).filter(Boolean);
                    setInvalidDateTimeSlotProducts(invalidProduct);

                    // show popup
                    setInvalidProductMessage(t('message_confirm_cart_date_slot'));
                    setPopupCloseText(t('text_change_date'));
                    showInvalidProductPopup();
                } else {
                    validateTimeSlot({
                        date: cartInfo.date,
                        time: orderType === ORDER_TYPE.GROUP_ORDER ? moment(groupData?.receive_time, 'HH:mm:ss').format('HH:mm') : moment(cartInfo.time, 'HH:mm:ss').format('HH:mm'),
                        product_id: cartProducts.map((product) => product.id),
                    }).then((res) => {
                        if (res.success) {
                            const invalidTimeSlotProducts = Object.entries(res.data).filter((i) => i[1] === false);
                            if (invalidTimeSlotProducts.length > 0) {
                                dispatch(LoadingActions.showGlobalLoading(false));

                                // update cart invalid product
                                const invalidProduct = invalidTimeSlotProducts.map((p) => p[1] === false ? Number(p[0]) : false).filter(Boolean);
                                setInvalidDateTimeSlotProducts(invalidProduct);

                                // show popup
                                setInvalidProductMessage(t('message_confirm_cart_time_slot'));
                                setPopupCloseText(t('text_change_time'));
                                showInvalidProductPopup();
                            } else {
                                handleNextStep();
                            }
                        }
                    });
                }
            }
        });
    }, [cartInfo.date, cartInfo.time, cartProducts, dispatch, groupData?.receive_time, handleNextStep, orderType, showInvalidProductPopup, t, validateTimeSlot]);

    useImperativeHandle(ref, () => ({
        handleNext
    }), [handleNext]);

    const renderCalendar = useMemo(() => (
        <MiniCalendar
            minDate={moment().format(DATE_FORMAT)}
            maxDate={moment().add(2, 'y').format(DATE_FORMAT)}
            maxAvailableDate={orderType !== ORDER_TYPE.GROUP_ORDER ? moment().add(maxDatePreOrder, 'd').format(DATE_FORMAT) : moment().add(2, 'y').format(DATE_FORMAT)}
            disableDates={disableDates}
            disableDays={groupInactiveTimeSlot}
            selectedDate={cartInfo.date}
            onSelectedDate={onSelectDate}
        />
    ), [cartInfo.date, disableDates, groupInactiveTimeSlot, maxDatePreOrder, onSelectDate, orderType]);

    const renderPopupCalendar = useMemo(() => (
        <Popover
            placement={Placement.BOTTOM}
            backgroundStyle={{ opacity: 0 }}
            isVisible={isShowCalendar}
            onRequestClose={hideCalendar}
            offset={IS_ANDROID ? -Dimens.H_40 : -Dimens.H_10}
            popoverStyle={[styles.popOverStyle]}
            from={(
                <TouchableOpacity
                    onPress={onClickCalendar}
                    style={[styles.inputContainer, { borderColor: themeColors.color_common_line }]}
                >
                    <TextComponent style={{ fontSize: Dimens.FONT_15, color: cartInfo.date ? themeColors.color_text : themeColors.color_common_subtext }}>
                        {cartInfo.date ? moment(cartInfo.date, DATE_FORMAT).format('dddd DD MMM YYYY') : t('cart_select_date_time_hint')}
                    </TextComponent>
                    <View style={[styles.inputText, { backgroundColor: themeColors.color_primary }]}>
                        <CalendarIcon
                            width={Dimens.W_24}
                            height={Dimens.W_24}
                        />
                    </View>
                </TouchableOpacity>

            )}
        >
            {renderCalendar}
        </Popover>
    ), [Dimens.FONT_15, Dimens.H_10, Dimens.H_40, Dimens.W_24, cartInfo.date, hideCalendar, isShowCalendar, onClickCalendar, renderCalendar, styles.inputContainer, styles.inputText, styles.popOverStyle, t, themeColors.color_common_line, themeColors.color_common_subtext, themeColors.color_primary, themeColors.color_text]);

    const renderTimeSlot = useMemo(() => (
        <TimeSlotView
            cartInfo={cartInfo}
            updateCartInfo={updateCartInfo}
        />
    ), [cartInfo, updateCartInfo]);

    return (
        <Animated.ScrollView
            scrollEnabled={false}
            entering={FadeIn.duration(500)}
            exiting={FadeOut.duration(500)}
            layout={Layout.duration(500)}
            showsVerticalScrollIndicator={false}
        >
            <Animated.View
                layout={Layout.duration(500)}
                style={styles.mainContainer}
            >
                {renderPopupCalendar}
                {renderTimeSlot}
            </Animated.View>

            <ErrorInvalidProductTimeSlotDialog
                hideModal={hideInvalidProductPopup}
                isShow={isShowInvalidProductPopup}
                message={invalidProductMessage}
                popupCloseText={popupCloseText}
                updateErrorMsg={updateErrorMsg}
                handlePrevStep={handlePrevStep}
                updateCartInfo={updateCartInfo}
                invalidDateTimeSlotProducts={invalidDateTimeSlotProducts}
            />
        </Animated.ScrollView>
    );
});

export default memo(CartSelectDateStep);

const stylesF = (Dimens: DimensType) => StyleSheet.create({
    mainContainer: { paddingVertical: Dimens.H_20 },
    inputText: {
        borderRadius: Dimens.RADIUS_6,
        height: Dimens.W_42,
        width: Dimens.W_42,
        justifyContent: 'center',
        alignItems: 'center',
    },
    inputContainer: {
        height: Dimens.W_42,
        flexDirection: 'row',
        alignItems: 'center',
        borderWidth: 1,
        borderRadius: Dimens.RADIUS_6,
        paddingLeft: Dimens.W_12,
        justifyContent: 'space-between',
    },
    popOverStyle: {
        borderRadius: Dimens.H_18,
        paddingBottom: Dimens.H_24,
        paddingTop: Dimens.H_8,
        paddingHorizontal: Dimens.W_16,
        alignItems: 'center',
        justifyContent: 'center',
        backgroundColor: 'transparent',
    },
});