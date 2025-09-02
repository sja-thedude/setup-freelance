import React, {
    useCallback,
    useState,
} from 'react';

import {
    NativeScrollEvent,
    NativeSyntheticEvent,
    StyleSheet,
    View,
} from 'react-native';
import Animated, { FadeInRight } from 'react-native-reanimated';
import { useEffectOnce } from 'react-use';

import { useRoute } from '@react-navigation/native';
import { RegisterSuccessIcon } from '@src/assets/svg';
import ButtonComponent from '@src/components/ButtonComponent';
import HeaderComponent from '@src/components/header/HeaderComponent';
import ScrollViewComponent from '@src/components/ScrollViewComponent';
import ShadowView from '@src/components/ShadowView';
import TextComponent from '@src/components/TextComponent';
import { Colors } from '@src/configs';
import {
    PAYMENT_METHOD,
    PAYMENT_STATUS,
} from '@src/configs/constants';
import { useAppDispatch } from '@src/hooks';
import useCallAPI from '@src/hooks/useCallAPI';
import useDimens, { DimensType } from '@src/hooks/useDimens';
import { SCREENS } from '@src/navigation/config/screenName';
import { OrderSuccessScreenProps } from '@src/navigation/NavigationRouteProps';
import NavigationService from '@src/navigation/NavigationService';
import { OrderDetailModel } from '@src/network/dataModels/OrderDetailModel';
import { RestaurantDetailModel, } from '@src/network/dataModels/RestaurantDetailModel';
import {
    getOrderDetailService,
    updatePaymentService,
} from '@src/network/services/orderServices';
import { getRestaurantDetailService, } from '@src/network/services/restaurantServices';
import { RestaurantActions, } from '@src/redux/toolkit/actions/restaurantActions';
import { StorageActions } from '@src/redux/toolkit/actions/storageActions';
import FirstInfoPage from '@src/screens/order/component/FirstInfoPage';
import SecondInfoPage from '@src/screens/order/component/SecondInfoPage';
import useThemeColors from '@src/themes/useThemeColors';
import { isTemplateOrGroupApp } from '@src/utils';
import { useTranslation } from 'react-i18next';

const OrderSuccessScreen = () => {
    const { themeColors } = useThemeColors();
    const Dimens = useDimens();
    const styles = stylesF(Dimens);
    const { t } = useTranslation();

    const dispatch = useAppDispatch();

    const { params } = useRoute<OrderSuccessScreenProps>();
    const { orderId } = params;

    const [index, setIndex] = useState(0);
    const [orderData, setOrderData] = useState<OrderDetailModel>();

    const { callApi: updatePayment } = useCallAPI(
            updatePaymentService,
            undefined,
            useCallback((data: OrderDetailModel) => {
                setOrderData(data);
            }, []),
    );

    const { callApi: getOrderDetail } = useCallAPI(
            getOrderDetailService,
            undefined,
            useCallback((data: OrderDetailModel) => {
                setOrderData(data);
                if (data.payment_method === PAYMENT_METHOD.MOLLIE) {
                    updatePayment({
                        order_id: orderId,
                        payment_method: data.payment_method,
                        payment_status: PAYMENT_STATUS.PAID,
                        total_paid: Number(data.total_paid).toFixed(2)
                    });
                }
            }, [orderId, updatePayment]),
    );

    const { callApi: getRestaurantDetail, loading } = useCallAPI(
            getRestaurantDetailService,
            undefined,
            useCallback((data: RestaurantDetailModel) => {
                dispatch(RestaurantActions.setExitScreen(1));
                dispatch(RestaurantActions.updateRestaurantDetail(data));
                NavigationService.navigate(SCREENS.MENU_TAB_SCREEN, { screen: SCREENS.RESTAURANT_DETAIL_SCREEN } );
            }, [dispatch]),
    );

    useEffectOnce(() => {
        dispatch(StorageActions.clearStorageProductsCart());
        dispatch(StorageActions.clearStorageProcessingOrder());
        getOrderDetail({
            order_id: orderId
        });
    });

    const handleGoback = useCallback(() => {
        if (!isTemplateOrGroupApp()) {
            getRestaurantDetail({ restaurant_id: orderData?.workspace_id });
        } else {
            NavigationService.navigate(SCREENS.MENU_TAB_SCREEN);
        }

    }, [getRestaurantDetail, orderData?.workspace_id]);

    const handleOnScroll = useCallback((event: NativeSyntheticEvent<NativeScrollEvent>) => {
        const currentIndex = Number((event.nativeEvent.contentOffset.x / (Dimens.SCREEN_WIDTH - Dimens.W_48) * 2).toFixed(0));
        setIndex(currentIndex > 0 ? 1 : 0);
    }, [Dimens.SCREEN_WIDTH, Dimens.W_48]);

    return (
        <View
            style={[styles.container, { backgroundColor: themeColors.color_app_background }]}
        >
            <HeaderComponent >
                <TextComponent style={styles.headerText}>
                    {t('text_title_confirm_order')}
                </TextComponent>
            </HeaderComponent>
            <Animated.View
                style={styles.animationContainer}
                entering={FadeInRight}
            >
                <RegisterSuccessIcon
                    width={Dimens.H_110}
                    height={Dimens.H_110}
                    stroke={themeColors.color_primary}
                />

                <TextComponent style={{ ...styles.headerSuccessText, color: themeColors.color_text }}>
                    {t('text_title_order_success')}
                </TextComponent>
                <TextComponent style={{ ...styles.conditionText, color: themeColors.color_text }}>
                    {t('text_confirm_order_description')}
                </TextComponent>

                <ButtonComponent
                    loading={loading}
                    title={t('text_back_to_range')}
                    style={styles.button}
                    onPress={handleGoback}
                />

                <ShadowView style={[styles.dialogContainer, { backgroundColor: themeColors.color_card_background }]}>
                    <ScrollViewComponent
                        horizontal
                        pagingEnabled
                        snapToAlignment={'center'}
                        decelerationRate={'fast'}
                        snapToInterval={Dimens.SCREEN_WIDTH - Dimens.W_48} //item width
                        scrollEventThrottle={300}
                        onScroll={handleOnScroll}
                        style={{ flexGrow: 0 }}
                    >
                        <FirstInfoPage orderDetailData={orderData} />
                        <SecondInfoPage orderDetailData={orderData} />
                    </ScrollViewComponent>

                    <View style={styles.indicatorContainer}>
                        <View
                            style={[styles.indicator, { backgroundColor: index === 0 ? themeColors.color_dot : themeColors.color_dot_inactive }]}
                        />
                        <View
                            style={[styles.indicator, { backgroundColor: index === 1 ? themeColors.color_dot : themeColors.color_dot_inactive }]}
                        />

                    </View>
                </ShadowView>
            </Animated.View>

        </View>
    );
};

export default OrderSuccessScreen;

const stylesF = (Dimens: DimensType) => StyleSheet.create({
    button: {
        // backgroundColor: Colors.COLOR_BUTTON_DEFAULT,
        marginVertical: Dimens.H_20,
        width: '80%',
        height: Dimens.W_46
    },
    animationContainer: {
        flex: 1,
        marginTop: Dimens.H_38,
        paddingHorizontal: Dimens.W_22,
        alignItems: 'center',
    },
    container: { flex: 1 },
    conditionText: {
        color: Colors.COLOR_WHITE,
        fontSize: Dimens.FONT_16,
        textAlign: 'center',
    },
    headerSuccessText: {
        color: Colors.COLOR_WHITE,
        fontSize: Dimens.FONT_20,
        fontWeight: '700',
        textAlign: 'center',
        marginBottom: Dimens.H_20,
        marginTop: Dimens.H_16,
    },
    headerText: {
        color: Colors.COLOR_WHITE,
        fontSize: Dimens.FONT_26,
        fontWeight: '700',
    },
    indicatorContainer: {
        flexDirection: 'row',
        justifyContent: 'center',
        marginTop: Dimens.H_16,
        marginBottom: -Dimens.H_4,
    },
    dialogContainer: {
        borderRadius: Dimens.RADIUS_6,
        paddingBottom: Dimens.H_16,
        maxHeight: Dimens.SCREEN_HEIGHT / 2.5,
    },
    indicator: {
        width: Dimens.H_6,
        height: Dimens.H_6,
        borderRadius: Dimens.H_6,
        marginHorizontal: Dimens.W_4,
    },
});