import React, {
    FC,
    memo,
    useCallback,
    useMemo,
    useState,
} from 'react';

import sum from 'lodash/sum';
import { useTranslation } from 'react-i18next';
import {
    FlatList,
    StyleSheet,
    TouchableOpacity,
    View,
} from 'react-native';

import {
    ButtonPlusIcon,
    MinusIcon,
    ShoppingBagIcon,
} from '@src/assets/svg';
import TextComponent from '@src/components/TextComponent';
import Toast from '@src/components/toast/Toast';
import { Colors } from '@src/configs';
import {
    OPENING_TIME_TYPE,
    ORDER_TYPE,
    RESTAURANT_EXTRA_TYPE,
} from '@src/configs/constants';
import {
    useAppDispatch,
    useAppSelector,
} from '@src/hooks';
import useBoolean from '@src/hooks/useBoolean';
import useCheckEmptyCart from '@src/hooks/useCheckEmptyCart';
import useDimens, { DimensType } from '@src/hooks/useDimens';
import { SCREENS } from '@src/navigation/config/screenName';
import NavigationService from '@src/navigation/NavigationService';
import { ProductDetailModel } from '@src/network/dataModels/ProductDetailModel';
import { ProductOptionModel } from '@src/network/dataModels/ProductOptionModel';
import { RestaurantDetailModel, } from '@src/network/dataModels/RestaurantDetailModel';
import { StorageActions } from '@src/redux/toolkit/actions/storageActions';
import { ProductInCart } from '@src/redux/toolkit/slices/storageSlice';
import useThemeColors from '@src/themes/useThemeColors';

import NotEmptyCartWarningDialog from './NotEmptyCartWarningDialog';

interface IProps {
    item?: ProductDetailModel;
    restaurantData?: RestaurantDetailModel;
    optionsItem: ProductOptionModel[];
    setIsSubmit: React.Dispatch<React.SetStateAction<boolean>>;
    listRef: React.RefObject<FlatList>;
}

const ButtonsAction: FC<IProps> = ({ item, optionsItem, setIsSubmit, restaurantData, listRef }) => {
    const Dimens = useDimens();
    const styles = stylesF(Dimens);

    const [quantity, setQuantity] = useState<number>(1);
    const { themeColors } = useThemeColors();

    const dispatch = useAppDispatch();
    const { t } = useTranslation();

    const [isShowNotEmptyCart, showNotEmptyCart, hideNotEmptyCart] = useBoolean(false);
    const cartRestaurant = useAppSelector((state) => state.storageReducer.cartProducts.restaurant);
    const isEmptyCart = useCheckEmptyCart();

    const handleChangeQuantity = useCallback(
            (type: 'minus' | 'plus') => () => {
                if (type === 'plus') {
                    setQuantity((state) => state + 1);
                } else {
                    setQuantity((state) => (state === 1 ? 1 : state - 1));
                }
            },
            [],
    );

    const price = useMemo(() => {
        let value = Number(item?.price || 0);
        const optionConver = (optionsItem || [])
                ?.map((i) => i?.items || [])
                ?.map((i) => (i?.some((a) => a?.master) ? i?.find((a) => a?.master) : i))
                ?.flat();

        if (optionConver?.length > 0) {
            value += sum(optionConver?.map((i) => Number(i?.price)));
        }

        value = quantity * value;

        if (value >= 0) {
            return `€${value.toFixed(2)}`;
        } else {
            return `- €${Math.abs(value).toFixed(2)}`;
        }
    }, [item?.price, optionsItem, quantity]);

    const isInValid = useMemo(
            () =>
                optionsItem?.filter((i) =>
                i?.min ? i?.items?.length < i?.min : false,
                )?.length > 0,
            [optionsItem],
    );

    const onAddProductToCart = useCallback(() => {
        const mProduct: ProductInCart = {
            ...item,
            quantity: quantity,
            options: optionsItem?.filter((x) => x.items.length > 0).map((y) => ({
                ...y,
                available: true
            })),
            isError: false
        };

        if (!isEmptyCart) {
            if (cartRestaurant?.id === item?.workspace_id) {
                // add the product to cart
                dispatch(StorageActions.setStorageProductsCart(mProduct));
                Toast.showToast(t('product_add_to_cart_success'));
                NavigationService.goBack();
            } else {
                // show warning popup
                showNotEmptyCart();
            }

        } else {
            const isGroupOrderOn = restaurantData?.extras?.find((item) => item.type === RESTAURANT_EXTRA_TYPE.GROUP_ORDER)?.active;

            if (isGroupOrderOn) {
                // show Select Type Popup
                NavigationService.navigate(SCREENS.SELECT_ORDER_TYPE_SCREEN, { product: mProduct });
            } else {
                const isTakeOutOn = restaurantData?.setting_open_hours?.find((item) => item.type === OPENING_TIME_TYPE.TAKE_AWAY)?.active;

                // add the product to cart
                dispatch(StorageActions.setStorageProductsCart(mProduct));
                Toast.showToast(t('product_add_to_cart_success'));

                if (isTakeOutOn) {
                    // order type is AFHAAL, update cart type
                    dispatch(StorageActions.setStorageCartType(ORDER_TYPE.TAKE_AWAY));
                } else {
                    // order type is LEVERING, update cart type
                    dispatch(StorageActions.setStorageCartType(ORDER_TYPE.DELIVERY));
                }

                NavigationService.goBack();
            }
        }

    }, [cartRestaurant?.id, dispatch, isEmptyCart, item, optionsItem, quantity, restaurantData?.extras, restaurantData?.setting_open_hours, showNotEmptyCart, t]);

    const handleSubmit = useCallback(() => {
        !!setIsSubmit && setIsSubmit(true);

        if (!isInValid) {
            onAddProductToCart();
        } else {
            const errorItem = optionsItem.find((i) => i.isWarning);
            if (errorItem) {
                listRef.current?.scrollToIndex({ index: errorItem.index, viewPosition: 0.5 });
            }
        }
    }, [isInValid, listRef, onAddProductToCart, optionsItem, setIsSubmit]);

    const renderQuantityButton = useMemo(() => (
        <View style={[styles.viewButton1]}>
            <View style={styles.viewItemButton1}>
                <TouchableOpacity
                    onPress={handleChangeQuantity('minus')}
                    style={styles.viewButtonMinus}
                >
                    <MinusIcon
                        width={Dimens.H_16}
                        height={Dimens.H_16}
                        stroke={themeColors.color_primary}
                    />
                </TouchableOpacity>

                <View style={[styles.viewButtonMinus, styles.viewBorder]}>
                    <TextComponent
                        style={StyleSheet.flatten([
                            styles.textQuantity,
                            { color: themeColors?.color_text },
                        ])}
                    >
                        {quantity}
                    </TextComponent>
                </View>

                <TouchableOpacity
                    onPress={handleChangeQuantity('plus')}
                    style={styles.viewButtonMinus}
                >
                    <ButtonPlusIcon
                        width={Dimens.H_16}
                        height={Dimens.H_16}
                        stroke={themeColors.color_primary}
                    />
                </TouchableOpacity>
            </View>
        </View>
    ), [Dimens.H_16, handleChangeQuantity, quantity, styles.textQuantity, styles.viewBorder, styles.viewButton1, styles.viewButtonMinus, styles.viewItemButton1, themeColors.color_primary, themeColors?.color_text]);

    const renderAddToCartButton = useMemo(() => (
        <TouchableOpacity
            onPress={handleSubmit}
            style={[styles.buttonPrice, { backgroundColor: themeColors.color_add_to_cart_button_background }]}
        >
            <ShoppingBagIcon />
            <TextComponent
                numberOfLines={1}
                style={styles.textPrice}
            >
                {price}
            </TextComponent>
        </TouchableOpacity>
    ), [handleSubmit, price, styles.buttonPrice, styles.textPrice, themeColors.color_add_to_cart_button_background]);
    return (
        <View style={styles.container}>
            {renderQuantityButton}
            <View style={styles.viewCenter} />
            {renderAddToCartButton}

            <NotEmptyCartWarningDialog
                isShow={isShowNotEmptyCart}
                hideModal={hideNotEmptyCart}
                currentRestaurant={cartRestaurant}
                newRestaurant={restaurantData}
                onAddProductToCart={onAddProductToCart}
                isEmptyCart={isEmptyCart}
            />
        </View>
    );
};

const stylesF = (Dimens: DimensType) => StyleSheet.create({
    container: { flexDirection: 'row', marginBottom: Dimens.H_8 },
    viewCenter: { width: Dimens.W_15 },
    viewButton1: {
        flexDirection: 'row',
        alignItems: 'center',
        flex: 1,
        borderWidth: 1,
        borderRadius: Dimens.RADIUS_6,
        borderColor: '#D1D1D1',
        paddingVertical: Dimens.W_12,
    },
    viewItemButton1: { flex: 1, flexDirection: 'row' },
    viewButtonMinus: {
        flex: 1,
        alignItems: 'center',
        justifyContent: 'center',
    },
    viewBorder: {
        borderLeftWidth: 1,
        borderRightWidth: 1,
        borderLeftColor: '#D1D1D1',
        borderRightColor: '#D1D1D1',
    },
    textQuantity: { fontSize: Dimens.FONT_16, fontWeight: '700' },
    buttonPrice: {
        borderRadius: Dimens.RADIUS_6,
        paddingVertical: Dimens.W_12,
        flex: 1,
        backgroundColor: '#413E38',
        flexDirection: 'row',
        alignItems: 'center',
        justifyContent: 'center',
    },
    textPrice: {
        fontSize: Dimens.FONT_18,
        fontWeight: '600',
        color: Colors.COLOR_WHITE,
        marginLeft: Dimens.W_8,
    },
});

export default memo(ButtonsAction);
