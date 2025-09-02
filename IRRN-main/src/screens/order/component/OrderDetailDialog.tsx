import React, {
    useCallback,
    useState,
} from 'react';

import { uniqBy } from 'lodash';
import { useTranslation } from 'react-i18next';
import {
    InteractionManager,
    NativeScrollEvent,
    NativeSyntheticEvent,
    StyleSheet,
    View,
} from 'react-native';
import { useDispatch } from 'react-redux';

import {
    getProductDetailService,
    getProductOptionsService,
} from '@network/services/productServices';
import ButtonComponent from '@src/components/ButtonComponent';
import DialogComponent from '@src/components/DialogComponent';
import ScrollViewComponent from '@src/components/ScrollViewComponent';
import ShadowView from '@src/components/ShadowView';
import TextComponent from '@src/components/TextComponent';
import { Colors } from '@src/configs';
import { ORDER_TYPE } from '@src/configs/constants';
import { useAppSelector } from '@src/hooks';
import useCallAPI from '@src/hooks/useCallAPI';
import useDimens, { DimensType } from '@src/hooks/useDimens';
import { SCREENS } from '@src/navigation/config/screenName';
import NavigationService from '@src/navigation/NavigationService';
import { OrderDetailModel } from '@src/network/dataModels/OrderDetailModel';
import { ProductOptionModel } from '@src/network/dataModels/ProductOptionModel';
import { SettingOpenHourShort } from '@src/network/dataModels/RestaurantNearbyItemModel';
import { getOrderDetailService } from '@src/network/services/orderServices';
import {
    getDetailGroupService,
    getRestaurantOpeningHourService,
} from '@src/network/services/restaurantServices';
import { OrderActions } from '@src/redux/toolkit/actions/orderActions';
import { StorageActions } from '@src/redux/toolkit/actions/storageActions';
import { DIALOG_TYPE } from '@src/screens/cart/selectOrderType/SelectOrderTypeScreen';
import useThemeColors from '@src/themes/useThemeColors';

import FirstInfoPage from './FirstInfoPage';
import SecondInfoPage from './SecondInfoPage';

interface ModalProps {
    isShow: boolean,
    hideModal: () => void
}

const OrderDetailDialog = ({ isShow, hideModal }: ModalProps) => {
    const Dimens = useDimens();
    const styles = stylesF(Dimens);

    const { themeColors } = useThemeColors();
    const dispatch = useDispatch();
    const { t } = useTranslation();

    const [index, setIndex] = useState(0);
    const [loading, setLoading] = useState(false);
    const [errorText, setErrorText] = useState<any>('');

    const orderDetailData = useAppSelector((state) => state.orderReducer.orderDetail);

    const { callApi: getProductOptions } = useCallAPI(
            getProductOptionsService,
    );

    const { callApi: getProductDetail } = useCallAPI(
            getProductDetailService,
    );

    const { callApi: getDetailGroup } = useCallAPI(
            getDetailGroupService,
    );

    const { callApi: getRestaurantOpeningHour } = useCallAPI(
            getRestaurantOpeningHourService,
            undefined,
            undefined,
            undefined,
            true,
            false
    );

    const handleModalHide = useCallback(() => {
        dispatch(OrderActions.clearOrderDetail());
        setIndex(0);
        setErrorText('');
    }, [dispatch]);

    const handleGotoCart = useCallback(() => {
        setLoading(false);
        InteractionManager.runAfterInteractions(() => {
            hideModal();
        });
        InteractionManager.runAfterInteractions(() => {
            NavigationService.navigate(SCREENS.CART_TAB_SCREEN);
        });
    }, [hideModal]);

    const { callApi: getOrderDetail } = useCallAPI(
            getOrderDetailService,
            undefined,
            useCallback((data: OrderDetailModel) => {

                // check group available
                if ((data.group_id && !data.group) || (data.group && data.group?.active !== 1)) {
                    setLoading(false);
                    setErrorText(t('Deze groep is niet langer actief. Gelieve contact op te nemen met de uitbater'));
                    return;
                }

                // get products detail
                Promise.all(
                        orderDetailData.items.map((i) => getProductDetail({ product_id: i.product_id }))
                ).then((result: any) => {
                    let detailArr: Array<any> = result.map((r: any, idx: number) => {
                        const orderOptions =  orderDetailData.items[idx].options;
                        const convertOptions = orderOptions.length === 0 ? [] : orderOptions.map((op) => ({
                            ...op.option,
                            items: op.option_items.map((oit) => ({
                                ...oit.option_item
                            })),
                        }));

                        const quantity = orderDetailData.items[idx].quantity;

                        if (r.success) {
                            return {
                                ...r.data,
                                quantity: quantity,
                                options: convertOptions
                            };
                        } else {
                            const tempProduct = orderDetailData.items[idx].product;
                            return {
                                ...tempProduct,
                                category: { ...tempProduct.category, available_delivery: false },
                                quantity: quantity,
                                options: convertOptions
                            };
                        }
                    });

                    // get products options
                    const hasOptionProducts = uniqBy(orderDetailData.items.filter((i) => i.options.length > 0), 'product_id');
                    Promise.all(
                            hasOptionProducts.map((i) => getProductOptions({ product_id: i.product_id }))
                    ).then((result) => {
                        const newOptionsArr: Array<{options: ProductOptionModel[], product_id: number}> = result.map((data: any, idx: number) => ({
                            product_id: hasOptionProducts[idx].product_id,
                            options: (data.success ? data.data : []).map((option: ProductOptionModel, index: number) => ({ ...option, optionOrder: index, items: option.items.map((i, index) => ({ ...i, order: index })) })),
                        }));

                        const productsInCart = detailArr.map((p) => {
                            let pOptions: Array<ProductOptionModel> = p.options;
                            if (pOptions.length === 0) {
                                return p;
                            } else {
                                const newProductOptions: Array<ProductOptionModel> = newOptionsArr.find((op) => op.product_id === p.id)?.options || [];
                                pOptions = pOptions.map((po, index) => {
                                    const availableOption = newProductOptions.find((opo) => opo.id === po.id);
                                    const updatedItems = po.items.map((poi) => {
                                        const updatedOptionItem = availableOption?.items.find((it) => it.available && it.id === poi.id);
                                        return updatedOptionItem ? {
                                            ...updatedOptionItem,
                                            available: true,
                                        } : {
                                            ...poi,
                                            available: false,
                                        };
                                    });

                                    return {
                                        ...po,
                                        available: !!availableOption,
                                        items: updatedItems,
                                        optionOrder: availableOption ? availableOption.optionOrder : index,
                                    };
                                });

                                return {
                                    ...p,
                                    options: pOptions
                                };
                            }
                        });

                        // update other cart info
                        // if cart type === GROUP => update group filter
                        // if cart type === DELIVERY => update delivery info

                        if (orderDetailData.group_id) {
                            getDetailGroup({
                                group_id: orderDetailData.group_id
                            }).then((result) => {
                                if (result.success) {
                                    // clear cart
                                    dispatch(StorageActions.clearStorageProductsCart());

                                    // update group filter
                                    dispatch(StorageActions.setStorageGroupFilter(
                                            {
                                                data: result.data,
                                                filterByDeliverable: result.data.type === ORDER_TYPE.DELIVERY,
                                            }
                                    ));
                                    // update cart type
                                    dispatch(StorageActions.setStorageCartType(ORDER_TYPE.GROUP_ORDER));
                                    // add products to cart
                                    dispatch(StorageActions.setStorageMultiProductsCart(productsInCart));
                                    handleGotoCart();
                                } else {
                                    const callback = () => {
                                        // update cart type
                                        dispatch(StorageActions.setStorageCartType(ORDER_TYPE.GROUP_ORDER));
                                        // add products to cart
                                        dispatch(StorageActions.setStorageMultiProductsCart(productsInCart));
                                        handleGotoCart();
                                    };

                                    // clear cart
                                    dispatch(StorageActions.clearStorageProductsCart());
                                    hideModal();
                                    setLoading(false);
                                    NavigationService.navigate(SCREENS.SELECT_ORDER_TYPE_SCREEN, { isInCart: true, product: productsInCart[0], defaultPopup: DIALOG_TYPE.SELECT_GROUP, callback });

                                }
                            });
                        } else {
                            switch (orderDetailData.type) {
                                case ORDER_TYPE.DELIVERY:
                                    {
                                        const callback = () => {
                                            // add products to cart
                                            dispatch(StorageActions.setStorageMultiProductsCart(productsInCart));
                                            handleGotoCart();
                                        };

                                        // clear cart
                                        dispatch(StorageActions.clearStorageProductsCart());
                                        hideModal();
                                        setLoading(false);
                                        NavigationService.navigate(SCREENS.SELECT_ORDER_TYPE_SCREEN, { isInCart: true, product: productsInCart[0], defaultPopup: DIALOG_TYPE.SELECT_ADDRESS, callback });
                                    }
                                    break;

                                default:
                                    // clear cart
                                    dispatch(StorageActions.clearStorageProductsCart());
                                    // update cart type
                                    dispatch(StorageActions.setStorageCartType(ORDER_TYPE.TAKE_AWAY));
                                    // add products to cart
                                    dispatch(StorageActions.setStorageMultiProductsCart(productsInCart));
                                    handleGotoCart();
                                    break;
                            }
                        }

                    });

                });
            }, [dispatch, getDetailGroup, getProductDetail, getProductOptions, handleGotoCart, hideModal, orderDetailData.group_id, orderDetailData.items, orderDetailData.type, t]),
    );

    const handleReOrder = useCallback(() => {
        setLoading(true);
        setErrorText('');

        getRestaurantOpeningHour({ restaurant_id: orderDetailData?.workspace_id }).then((result) => {
            if (result.success) {
                // check afhaal type available
                const disableAfhaalType = result.data.find((i: SettingOpenHourShort) => i.type === ORDER_TYPE.TAKE_AWAY && i.active === false);
                const disableLeveringType = result.data.find((i: SettingOpenHourShort) => i.type === ORDER_TYPE.DELIVERY && i.active === false);
                if ((orderDetailData.type === ORDER_TYPE.TAKE_AWAY && disableAfhaalType) || orderDetailData.type === ORDER_TYPE.DELIVERY && disableLeveringType) {
                    setLoading(false);
                    setErrorText(t('cart_can_not_add_product_to_cart_cuz_restaurant_is_closed'));
                } else {
                    // get order detail to check group status if order is group order
                    getOrderDetail({
                        order_id: orderDetailData?.id
                    });
                }
            } else {
                // get order detail to check group status if order is group order
                getOrderDetail({
                    order_id: orderDetailData?.id
                });
            }
        });

    }, [getOrderDetail, getRestaurantOpeningHour, orderDetailData?.id, orderDetailData.type, orderDetailData?.workspace_id, t]);

    const handleOnScroll = useCallback((event: NativeSyntheticEvent<NativeScrollEvent>) => {
        const currentIndex = Number((event.nativeEvent.contentOffset.x / (Dimens.SCREEN_WIDTH - Dimens.W_48) * 2).toFixed(0));
        setIndex(currentIndex > 0 ? 1 : 0);
    }, [Dimens.SCREEN_WIDTH, Dimens.W_48]);

    return (
        <DialogComponent
            hideModal={hideModal}
            isVisible={isShow}
            onModalHide={handleModalHide}
        >
            <View style={{ }}>
                <ShadowView
                    style={styles.shadowContainer}
                >
                    <View style={[styles.dialogContainer, { backgroundColor: themeColors.color_card_background }]}>
                        <ScrollViewComponent
                            horizontal
                            pagingEnabled
                            snapToAlignment={'center'}
                            decelerationRate={'fast'}
                            snapToInterval={Dimens.SCREEN_WIDTH - Dimens.W_48} //item width
                            scrollEventThrottle={300}
                            onScroll={handleOnScroll}
                        >
                            <FirstInfoPage orderDetailData={orderDetailData} />
                            <SecondInfoPage orderDetailData={orderDetailData} />
                        </ScrollViewComponent>

                        <View style={styles.indicatorContainer}>
                            <View
                                style={[styles.indicator, { backgroundColor: index === 0 ? themeColors.color_dot : themeColors.color_dot_inactive }]}
                            />
                            <View
                                style={[styles.indicator, { backgroundColor: index === 1 ? themeColors.color_dot : themeColors.color_dot_inactive }]}
                            />

                        </View>
                    </View>
                </ShadowView>

                {
                errorText ? (
                        <TextComponent style={styles.errorText}>
                            {errorText}
                        </TextComponent>
                    ) : null
                }

                <ButtonComponent
                    loading={loading}
                    title={t('text_order_again')}
                    style={styles.textAButton}
                    onPress={handleReOrder}
                />
            </View>

        </DialogComponent>
    );
};

export default OrderDetailDialog;

const stylesF = (Dimens: DimensType) => StyleSheet.create({
    errorText: {
        fontSize: Dimens.FONT_12,
        color: Colors.COLOR_RED_ERROR,
        textAlign: 'center',
        marginHorizontal: Dimens.W_16,
        marginTop: Dimens.H_8
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
        maxHeight: Dimens.SCREEN_HEIGHT / 2,
    },
    shadowContainer: { margin: Dimens.W_8 },
    textAButton: {
        width: '75%',
        alignSelf: 'center',
        marginTop: Dimens.H_20,
    },
    indicator: {
        width: Dimens.H_6,
        height: Dimens.H_6,
        borderRadius: Dimens.H_6,
        backgroundColor: Colors.COLOR_RED_ERROR,
        marginHorizontal: Dimens.W_4,
    },
});