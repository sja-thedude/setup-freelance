import React, {
    FC,
    memo,
    useCallback,
    useMemo,
    useRef,
    useState,
} from 'react';

import { debounce } from 'lodash';
import { useTranslation } from 'react-i18next';
import {
    Animated as RNAnimated,
    LayoutChangeEvent,
    StyleSheet,
    TouchableOpacity,
    View,
} from 'react-native';
import Popover from 'react-native-popover-view';
import Animated, {
    FadeInRight,
    FadeOutRight,
} from 'react-native-reanimated';
import { useDispatch } from 'react-redux';

import {
    AddressIcon,
    CheckIcon,
    CloseIcon,
    CouplesIcon,
    DropDownIcon,
    MagnifierIcon,
    SettingIcon,
    TabShoppingIcon,
    TruckIcon,
} from '@src/assets/svg';
import InputComponent from '@src/components/InputComponent';
import TextComponent from '@src/components/TextComponent';
import TouchableComponent from '@src/components/TouchableComponent';
import { Colors } from '@src/configs';
import {
    IS_ANDROID,
    ORDER_TYPE,
    RESTAURANT_SORT_TYPE,
} from '@src/configs/constants';
import { useAppSelector } from '@src/hooks';
import useBoolean from '@src/hooks/useBoolean';
import useDimens, { DimensType } from '@src/hooks/useDimens';
import { SCREENS } from '@src/navigation/config/screenName';
import NavigationService from '@src/navigation/NavigationService';
import { LocationActions } from '@src/redux/toolkit/actions/locationActions';
import { AddressType } from '@src/screens/home/component/HomeHeader';
import useThemeColors from '@src/themes/useThemeColors';

interface IProps {
    setHeaderHeightRef: any,
    animatedValue: RNAnimated.Value,
    onSelectType: Function,
    onSelectSort:  Function,
    onSearch:  Function,
}

const AllRestaurantHeader: FC<IProps> = ({ setHeaderHeightRef, animatedValue, onSelectType, onSelectSort, onSearch }) => {
    const Dimens = useDimens();
    const styles = stylesF(Dimens);

    const { themeColors } = useThemeColors();
    const dispatch = useDispatch();
    const { t } = useTranslation();

    const runOnLayout = useRef(0);
    const searchValue = useRef('');

    const address = useAppSelector((state) => state.locationReducer.address);

    const [isShowSearchBar, showSearchBar, hideSearchBar] = useBoolean(false);
    const [isShowOrderTypePopover, showOrderTypePopover, hideOrderTypePopover] = useBoolean(false);
    const [isShowSortPopover, showSortPopover, hideSortPopover] = useBoolean(false);

    const orderTypeFilterData = useMemo(() => [
        {
            type: ORDER_TYPE.TAKE_AWAY,
            title: t('text_pick_up'),
            icon: (
                <TabShoppingIcon
                    stroke={Colors.COLOR_WHITE}
                    width={Dimens.W_15}
                    height={Dimens.W_15}
                />
            ),
        },
        {
            type: ORDER_TYPE.DELIVERY,
            title: t('text_delivery'),
            icon: (
                <TruckIcon
                    stroke={Colors.COLOR_WHITE}
                    width={Dimens.W_15}
                    height={Dimens.W_15}
                />
            ),
        },
        {
            type: ORDER_TYPE.GROUP_ORDER,
            title: t('options_group_orders'),
            icon: (
                <CouplesIcon
                    stroke={Colors.COLOR_WHITE}
                    width={Dimens.W_15}
                    height={Dimens.W_15}
                />
            ),
        },
    ], [Dimens.W_15, t]);

    const [currentOrderType, setCurrentOrderType] = useState(orderTypeFilterData[0]);

    const orderSortDataOrigin = useMemo(() => [
        {
            type: RESTAURANT_SORT_TYPE.DISTANCE,
            title: t('filter_afstand'),
            orderType: [ORDER_TYPE.DELIVERY, ORDER_TYPE.TAKE_AWAY]
        },
        {
            type: RESTAURANT_SORT_TYPE.AMOUNT,
            title: t('filter_amount'),
            orderType: [ORDER_TYPE.DELIVERY]
        },
        {
            type: RESTAURANT_SORT_TYPE.DELIVERY_FEE,
            title: t('filter_leveringskost'),
            orderType: [ORDER_TYPE.DELIVERY]
        },
        {
            type: RESTAURANT_SORT_TYPE.WAITING_TIME,
            title: t('filter_min_wachttijd'),
            orderType: [ORDER_TYPE.DELIVERY, ORDER_TYPE.TAKE_AWAY]
        },
        {
            type: RESTAURANT_SORT_TYPE.NAME,
            title: t('filter_naam'),
            orderType: [ORDER_TYPE.DELIVERY, ORDER_TYPE.TAKE_AWAY, ORDER_TYPE.GROUP_ORDER]
        },
    ], [t]);

    const [currentSortType, setCurrentSortType] = useState(orderSortDataOrigin.filter((item) => item.orderType.includes(currentOrderType.type))[0]);

    const headerRadius = useMemo(() => animatedValue.interpolate({
        inputRange: [0, 100],
        outputRange: [Dimens.HEADER_BORDER_RADIUS, 1],
        extrapolate: 'clamp',
    }), [Dimens.HEADER_BORDER_RADIUS, animatedValue]);

    const headerOpacity = useMemo(() => animatedValue.interpolate({
        inputRange: [0, 100],
        outputRange: [1, 0],
        extrapolate: 'clamp',
    }), [animatedValue]);

    const handleNavMap = useCallback(() => {
        NavigationService.navigate(SCREENS.RESTAURANT_MAP_SCREEN);
    }, []);

    const handleSelectAddress = useCallback((newAddress: AddressType) => {
        dispatch(LocationActions.setLocation({ lat: newAddress.lat, lng: newAddress.lng, address: newAddress.address }));
    }, [dispatch]);

    const selectAddress = useCallback(() => {
        NavigationService.navigate(SCREENS.SELECT_ADDRESS_SCREEN, { onSelectAddress: handleSelectAddress });
    }, [handleSelectAddress]);

    const onSelectOrderType = useCallback((orderType: any) => {
        hideOrderTypePopover();

        if (orderType.type !== currentOrderType.type) {
            setCurrentOrderType(orderType);

            const mSortType = orderSortDataOrigin.find((item) => item.orderType.includes(orderType.type));
            mSortType && setCurrentSortType(mSortType);

            onSelectType(orderType.type, mSortType?.type);
        }
    }, [currentOrderType.type, hideOrderTypePopover, onSelectType, orderSortDataOrigin]);

    const onSelectSortType = useCallback((item: any) => {
        hideSortPopover();

        if (item.type !== currentSortType.type) {
            setCurrentSortType(item);
            onSelectSort(item.type);
        }
    }, [currentSortType.type, hideSortPopover, onSelectSort]);

    // eslint-disable-next-line react-hooks/exhaustive-deps
    const handleSearch = useCallback(debounce(async (text) => {
        searchValue.current = text;
        onSearch(text);
    }, 500), []);

    const renderOrderTypeFilterButton = useMemo(() => (
        <>
            <Popover
                backgroundStyle={{ opacity: 0 }}
                isVisible={isShowOrderTypePopover}
                onRequestClose={hideOrderTypePopover}
                arrowSize={{ width: 0, height: 0 }}
                offset={IS_ANDROID ? -Dimens.H_24 : Dimens.H_5}
                popoverStyle={[styles.popOverStyle, { backgroundColor: themeColors.color_card_background }]}
                from={(
                    <TouchableOpacity
                        onPress={showOrderTypePopover}
                        style={styles.filterButton}
                    >
                        {currentOrderType.icon}
                        <TextComponent
                            numberOfLines={1}
                            style={[styles.filterButtonText]}
                        >
                            {currentOrderType.title.toUpperCase()}
                        </TextComponent>
                        <DropDownIcon
                            stroke={Colors.COLOR_WHITE}
                            width={Dimens.H_10}
                            height={Dimens.H_10}
                        />
                    </TouchableOpacity>
                )}
            >
                <View style={styles.popupContentContainer}>
                    <TextComponent
                        style={[styles.popupTitle, { color: themeColors.color_text_2 }]}
                    >
                        {t('options_select_title')}
                    </TextComponent>
                    {orderTypeFilterData.map((item, index) => (
                        <TouchableComponent
                            key={index}
                            onPress={() => onSelectOrderType(item)}
                            style={styles.filterButtonWrapper}
                        >
                            {currentOrderType.type === item.type ? (
                                    <CheckIcon
                                        stroke={themeColors.color_primary}
                                        width={Dimens.W_12}
                                        height={Dimens.W_12}
                                    />
                                ) : <View style={{ height: Dimens.W_12, width: Dimens.W_12 }}/>}

                            <TextComponent
                                style={[styles.popupItemText, { color: currentOrderType.type === item.type ? themeColors.color_primary : themeColors.color_text_2 }]}
                            >
                                {item.title}
                            </TextComponent>
                        </TouchableComponent>
                    ))}
                </View>
            </Popover>
        </>
    ), [Dimens.H_10, Dimens.H_24, Dimens.H_5, Dimens.W_12, currentOrderType.icon, currentOrderType.title, currentOrderType.type, hideOrderTypePopover, isShowOrderTypePopover, onSelectOrderType, orderTypeFilterData, showOrderTypePopover, styles.filterButton, styles.filterButtonText, styles.filterButtonWrapper, styles.popOverStyle, styles.popupContentContainer, styles.popupItemText, styles.popupTitle, t, themeColors.color_card_background, themeColors.color_primary, themeColors.color_text_2]);

    const renderSortButton = useMemo(() => (
        <>
            <Popover
                backgroundStyle={{ opacity: 0 }}
                isVisible={isShowSortPopover}
                onRequestClose={hideSortPopover}
                offset={IS_ANDROID ? -Dimens.H_24 : Dimens.H_5}
                popoverStyle={[styles.popOverStyle, { backgroundColor: themeColors.color_card_background }]}
                from={(
                    <TouchableOpacity
                        onPress={showSortPopover}
                        hitSlop={Dimens.DEFAULT_HIT_SLOP}
                        style={{ marginLeft: Dimens.W_22 }}
                    >
                        <SettingIcon
                            stroke={Colors.COLOR_WHITE}
                            width={Dimens.W_20}
                            height={Dimens.W_20}
                        />
                    </TouchableOpacity>
                )}
            >
                <View style={styles.popupContentContainer}>
                    <TextComponent
                        style={[styles.popupTitle, { color: themeColors.color_text_2 }]}
                    >
                        {t('filter_label')}
                    </TextComponent>
                    {orderSortDataOrigin.filter((item) => item.orderType.includes(currentOrderType.type)).map((item, index) => (
                        <TouchableComponent
                            key={index}
                            onPress={() => onSelectSortType(item)}
                            style={styles.filterButtonWrapper}
                        >
                            {currentSortType.type === item.type ? (
                                    <CheckIcon
                                        stroke={themeColors.color_primary}
                                        width={Dimens.W_12}
                                        height={Dimens.W_12}
                                    />
                                ) : <View style={{ height: Dimens.W_12, width: Dimens.W_12 }}/>}

                            <TextComponent
                                style={[styles.popupItemText, { color: currentSortType.type === item.type ? themeColors.color_primary : themeColors.color_text_2 }]}
                            >
                                {`${item.title} ${index === 0 ? t('filter_default') : ''}`}
                            </TextComponent>
                        </TouchableComponent>
                    ))}
                </View>
            </Popover>
        </>
    ), [Dimens.DEFAULT_HIT_SLOP, Dimens.H_24, Dimens.H_5, Dimens.W_12, Dimens.W_20, Dimens.W_22, currentOrderType.type, currentSortType.type, hideSortPopover, isShowSortPopover, onSelectSortType, orderSortDataOrigin, showSortPopover, styles.filterButtonWrapper, styles.popOverStyle, styles.popupContentContainer, styles.popupItemText, styles.popupTitle, t, themeColors.color_card_background, themeColors.color_primary, themeColors.color_text_2]);

    const renderSearchBar = useMemo(() => isShowSearchBar ? (
            (
                <Animated.View
                    entering={FadeInRight}
                    exiting={FadeOutRight}
                    style={[styles.searchBarContainer, { backgroundColor: themeColors.color_primary }]}
                >
                    <TouchableComponent
                        hitSlop={Dimens.DEFAULT_HIT_SLOP}
                        onPress={() => {
                            hideSearchBar();
                            if (searchValue.current) {
                                onSearch('');
                            }
                            searchValue.current = '';
                        }}
                    >
                        <CloseIcon
                            stroke={Colors.COLOR_WHITE}
                            width={Dimens.W_12}
                            height={Dimens.W_12}
                        />
                    </TouchableComponent>
                    <View
                        style={styles.searchBarWrapper}
                    >
                        <MagnifierIcon
                            stroke={Colors.COLOR_WHITE}
                            width={Dimens.W_18}
                            height={Dimens.W_18}
                        />
                        <InputComponent
                            backgroundInput={'transparent'}
                            borderInput={'transparent'}
                            placeholderTextColor={Colors.COLOR_WHITE}
                            textColorInput={Colors.COLOR_WHITE}
                            containerStyle={styles.inputContainer}
                            style={styles.searchInput}
                            autoCapitalize={'none'}
                            autoFocus
                            placeholder={t('hint_search_business_name')}
                            onChangeText={handleSearch}
                        />
                    </View>
                </Animated.View>
            )
        ) : null, [Dimens.DEFAULT_HIT_SLOP, Dimens.W_12, Dimens.W_18, handleSearch, hideSearchBar, isShowSearchBar, onSearch, styles.inputContainer, styles.searchBarContainer, styles.searchBarWrapper, styles.searchInput, t, themeColors.color_primary]);

    const renderFilterArea = useMemo(() => (
        <View style={styles.filterAreaContainer}>
            {renderOrderTypeFilterButton}
            <View style={styles.sortAreaContainer}>
                {renderSortButton}
                <TouchableComponent
                    onPress={showSearchBar}
                    hitSlop={Dimens.DEFAULT_HIT_SLOP}
                    style={{ marginLeft: Dimens.W_22 }}
                >
                    <MagnifierIcon
                        stroke={Colors.COLOR_WHITE}
                        strokeWidth={1.5}
                        width={Dimens.W_20}
                        height={Dimens.W_20}
                    />
                </TouchableComponent>

                <TouchableComponent
                    hitSlop={Dimens.DEFAULT_HIT_SLOP}
                    style={{ marginLeft: Dimens.W_22 }}
                    onPress={handleNavMap}
                >
                    <AddressIcon
                        stroke={Colors.COLOR_WHITE}
                        width={Dimens.W_20}
                        height={Dimens.W_20}
                    />
                </TouchableComponent>
            </View>
            {renderSearchBar}
        </View>
    ), [Dimens.DEFAULT_HIT_SLOP, Dimens.W_20, Dimens.W_22, handleNavMap, renderOrderTypeFilterButton, renderSearchBar, renderSortButton, showSearchBar, styles.filterAreaContainer, styles.sortAreaContainer]);

    const renderHeader = useMemo(() => (
        <RNAnimated.View
            style={{
                overflow: 'hidden',
            }}
        >
            <RNAnimated.View
                onLayout={(e: LayoutChangeEvent) => {
                    runOnLayout.current = runOnLayout.current + 1;
                    if (runOnLayout.current <= 2) {
                        setHeaderHeightRef(e?.nativeEvent?.layout?.height || 0);
                    }
                }}
                style={{
                    borderBottomStartRadius: headerRadius,
                    borderBottomEndRadius: headerRadius,
                    paddingHorizontal: Dimens.W_20,
                    paddingBottom: Dimens.H_6,
                    paddingTop: Dimens.COMMON_HEADER_PADDING - Dimens.COMMON_HEADER_PADDING_EXTRA,
                    backgroundColor: themeColors.color_primary
                }}
            >
                <RNAnimated.View
                    style={{ opacity: headerOpacity }}
                >
                    <TouchableComponent
                        onPress={selectAddress}
                        style={styles.addressContainer}
                    >
                        <AddressIcon
                            stroke={Colors.COLOR_WHITE}
                            width={Dimens.W_18}
                            height={Dimens.W_18}
                        />
                        <TextComponent
                            numberOfLines={1}
                            style={styles.addressText}
                        >
                            {address}
                        </TextComponent>
                    </TouchableComponent>
                    {renderFilterArea}
                </RNAnimated.View>
            </RNAnimated.View>
        </RNAnimated.View>
    ), [Dimens.COMMON_HEADER_PADDING, Dimens.COMMON_HEADER_PADDING_EXTRA, Dimens.H_6, Dimens.W_18, Dimens.W_20, address, headerOpacity, headerRadius, renderFilterArea, selectAddress, setHeaderHeightRef, styles.addressContainer, styles.addressText, themeColors.color_primary]);

    return (
        renderHeader
    );
};

export default memo(AllRestaurantHeader);

const stylesF = (Dimens: DimensType) => StyleSheet.create({
    addressContainer: {
        flexDirection: 'row',
        alignItems: 'center',
        marginHorizontal: Dimens.W_10,
        borderBottomWidth: Dimens.H_3,
        borderBottomColor: Colors.COLOR_WHITE,
        paddingVertical: Dimens.H_4,
    },
    sortAreaContainer: {
        flexDirection: 'row',
        alignItems: 'center',
        flex: 1,
        justifyContent: 'flex-end',
    },
    filterAreaContainer: {
        flexDirection: 'row',
        alignItems: 'center',
        justifyContent: 'space-between',
        marginTop: Dimens.H_14,
    },
    inputContainer: { flex: 1, height: '80%' },
    searchInput: { padding: 0, paddingHorizontal: Dimens.W_8 },
    searchBarWrapper: {
        flexDirection: 'row',
        alignItems: 'center',
        marginLeft: Dimens.W_10,
        marginRight: Dimens.W_16,
        borderBottomWidth: Dimens.H_3,
        borderBottomColor: Colors.COLOR_WHITE,
        flex: 1,
    },
    searchBarContainer: {
        position: 'absolute',
        left: -Dimens.W_12,
        right: -Dimens.W_6,
        top: 0,
        bottom: 0,
        flexDirection: 'row',
        alignItems: 'center',
    },
    popupItemText: { fontSize: Dimens.FONT_16, marginLeft: Dimens.W_4 },
    filterButtonWrapper: {
        flexDirection: 'row',
        alignItems: 'center',
        marginTop: Dimens.H_14,
        marginLeft: Dimens.W_8,
    },
    popupTitle: { fontWeight: '700', fontSize: Dimens.FONT_16 },
    popupContentContainer: { padding: Dimens.H_24 },
    filterButtonText: {
        fontWeight: '700',
        fontSize: Dimens.FONT_14,
        color: Colors.COLOR_WHITE,
        marginHorizontal: Dimens.W_10,
    },
    filterButton: {
        flexDirection: 'row',
        alignItems: 'center',
        alignSelf: 'flex-start',
        borderWidth: 1,
        borderColor: Colors.COLOR_WHITE,
        borderRadius: Dimens.H_999,
        paddingVertical: Dimens.H_9,
        paddingHorizontal: Dimens.W_16,
        marginLeft: -Dimens.W_10
    },
    popOverStyle: { borderRadius: Dimens.H_18 },
    addressText: {
        flex: 1,
        fontWeight: '700',
        fontSize: Dimens.FONT_15,
        color: Colors.COLOR_WHITE,
        marginLeft: Dimens.W_5,
    },
});
