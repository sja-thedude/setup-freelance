import React, {
    forwardRef,
    Fragment,
    memo,
    useCallback,
    useEffect,
    useImperativeHandle,
    useState,
} from 'react';

import {
    StyleSheet,
    View,
} from 'react-native';
import { SwiperFlatList } from 'react-native-swiper-flatlist';

import { useIsFocused } from '@react-navigation/native';
import { Colors } from '@src/configs';
import { useAppSelector } from '@src/hooks';
import useBoolean from '@src/hooks/useBoolean';
import useCallAPI from '@src/hooks/useCallAPI';
import useDimens, { DimensType } from '@src/hooks/useDimens';
import { fetchCoupons } from '@src/network/services/productServices';
import useThemeColors from '@src/themes/useThemeColors';

import ItemCoupon from './Item/ItemCoupon';
import ModalCoupon from './ModalCoupon';

const CouponRestaurant = forwardRef(({}, ref) => {
    const Dimens = useDimens();
    const styles = stylesF(Dimens);
    const isFocused = useIsFocused();

    const { themeColors } = useThemeColors();
    const [isVisible, showModal, hideModal] = useBoolean();

    const [currentItem, setCurrentItem] = useState();
    const [couponData, setCouponData] = useState<any>();

    const restaurantData = useAppSelector(
            (state) => state.restaurantReducer.restaurantDetail.data,
    );

    const { callApi: getCoupons } = useCallAPI(
            fetchCoupons,
            undefined,
            useCallback((data: any) => {
                setCouponData(data.data);
            }, []),
            undefined,
            true,
            false
    );

    useImperativeHandle(ref, () => ({
        onRefresh: () => {
            if (restaurantData?.id) {
                getCoupons(restaurantData?.id);
            }
        }
    }), [getCoupons, restaurantData?.id]);

    useEffect(() => {
        if (restaurantData?.id && isFocused) {
            getCoupons(restaurantData?.id);
        }
    }, [getCoupons, restaurantData?.id, isFocused]);

    const clickItem = useCallback((item: any) => {
        setCurrentItem(item);
        showModal();
    }, [showModal]);

    const renderItem = useCallback(
            ({ item }: any) => (
                <ItemCoupon
                    onClickItem={() => clickItem(item)}
                    item={item}
                />
            ),
            [clickItem],
    );

    return (
        <Fragment>
            {!!couponData && couponData?.length > 0 && (
                <View style={[styles.container, { backgroundColor: themeColors.color_primary }]}>
                    <SwiperFlatList
                        autoplayLoopKeepAnimation
                        autoplayDelay={4}
                        showPagination={couponData?.length > 1}
                        paginationActiveColor={Colors.COLOR_WHITE}
                        paginationDefaultColor={'rgba(255, 255, 255, 0.5)'}
                        paginationStyleItem={styles.paginationStyleItem}
                        paginationStyle={styles.paginationStyle}
                        data={couponData}
                        autoplay
                        autoplayLoop
                        renderItem={renderItem}
                    />
                    <ModalCoupon
                        isVisible={isVisible}
                        onClose={hideModal}
                        item={currentItem}
                        restaurantId={restaurantData?.id}
                    />
                </View>
            )}
        </Fragment>
    );
});

const stylesF = (Dimens: DimensType) => StyleSheet.create({
    container: {
        height: Dimens.H_60,
        borderBottomLeftRadius: Dimens.HEADER_BORDER_RADIUS,
        borderBottomRightRadius: Dimens.HEADER_BORDER_RADIUS,
        marginTop: -Dimens.HEADER_BORDER_RADIUS,
        paddingTop: -Dimens.HEADER_BORDER_RADIUS + Dimens.H_18,
        justifyContent: 'center',
        alignItems: 'center',
        zIndex: 1,
    },
    paginationStyleItem: {
        width: Dimens.W_6,
        height: Dimens.W_6,
        borderRadius: Dimens.W_6,
        marginHorizontal: Dimens.W_4,
    },
    paginationStyle: { bottom: -Dimens.H_16 },
});

export default memo(CouponRestaurant);
