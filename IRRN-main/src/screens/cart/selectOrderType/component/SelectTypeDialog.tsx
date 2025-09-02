import React, {
    FC,
    useCallback,
    useMemo,
} from 'react';

import { useTranslation } from 'react-i18next';
import {
    StyleSheet,
    View,
} from 'react-native';
import { useDispatch } from 'react-redux';

import {
    CouplesIcon,
    TabShoppingIcon,
    TruckIcon,
} from '@src/assets/svg';
import TextComponent from '@src/components/TextComponent';
import Toast from '@src/components/toast/Toast';
import TouchableComponent from '@src/components/TouchableComponent';
import {
    CART_DISCOUNT_TYPE,
    OPENING_TIME_TYPE,
    ORDER_TYPE,
    RESTAURANT_EXTRA_TYPE,
} from '@src/configs/constants';
import { useAppSelector } from '@src/hooks';
import useDimens, { DimensType } from '@src/hooks/useDimens';
import useIsUserLoggedIn from '@src/hooks/useIsUserLoggedIn';
import { SCREENS } from '@src/navigation/config/screenName';
import NavigationService from '@src/navigation/NavigationService';
import { RestaurantDetailModel, } from '@src/network/dataModels/RestaurantDetailModel';
import { StorageActions } from '@src/redux/toolkit/actions/storageActions';
import { ProductInCart } from '@src/redux/toolkit/slices/storageSlice';
import useThemeColors from '@src/themes/useThemeColors';

import { DIALOG_TYPE } from '../SelectOrderTypeScreen';
import BaseDialog from './BaseDialog';

interface IProps {
    setCurrentDialog: Function,
    restaurantData?: RestaurantDetailModel,
    isInCart?: boolean,
    product: ProductInCart,
}

const SelectTypeDialog: FC<IProps> = ({ setCurrentDialog, restaurantData, isInCart, product }) => {
    const { themeColors } = useThemeColors();
    const Dimens = useDimens();
    const styles = stylesF(Dimens);

    const { t } = useTranslation();
    const dispatch = useDispatch();

    const isUserLoggedIn = useIsUserLoggedIn();

    const discountInfo = useAppSelector((state) => state.storageReducer.cartProducts.discountInfo);

    const onSelectOrderType = useCallback((orderType: number) => {
        switch (orderType) {
            case ORDER_TYPE.TAKE_AWAY:
                dispatch(StorageActions.clearStorageGroupFilter());
                dispatch(StorageActions.clearStorageDeliveryInfo());
                if (discountInfo?.discountType === CART_DISCOUNT_TYPE.GROUP_DISCOUNT) {
                    dispatch(StorageActions.clearStorageDiscount());
                }
                dispatch(StorageActions.setStorageCartType(ORDER_TYPE.TAKE_AWAY));

                if (!isInCart) {
                    // add the product to cart
                    dispatch(StorageActions.setStorageProductsCart(product));
                    Toast.showToast(t('product_add_to_cart_success'));
                    NavigationService.pop(2);
                } else {
                    NavigationService.goBack();
                }
                break;

            case ORDER_TYPE.DELIVERY:
                setCurrentDialog(DIALOG_TYPE.SELECT_ADDRESS);
                break;

            case ORDER_TYPE.GROUP_ORDER:
                if (!isUserLoggedIn) {
                    NavigationService.navigate(SCREENS.LOGIN_SCREEN, { callback: () => setCurrentDialog(DIALOG_TYPE.SELECT_GROUP) });
                    return;
                }
                setCurrentDialog(DIALOG_TYPE.SELECT_GROUP);
                break;

            default:
                break;
        }

    }, [discountInfo?.discountType, dispatch, isInCart, isUserLoggedIn, product, setCurrentDialog, t]);

    const deliveryOn = useMemo(() => restaurantData?.setting_open_hours?.find((item) => item.type === OPENING_TIME_TYPE.DELIVERY)?.active, [restaurantData]);
    const takeOutOn = useMemo(() => restaurantData?.setting_open_hours?.find((item) => item.type === OPENING_TIME_TYPE.TAKE_AWAY)?.active, [restaurantData]);
    const groupOrderOn = useMemo(() => restaurantData?.extras.find((item) => item.type === RESTAURANT_EXTRA_TYPE.GROUP_ORDER)?.active, [restaurantData]);

    return (
        <BaseDialog
            onSwipeHide={() => NavigationService.goBack()}
        >
            <View style={[styles.topContainer]}>
                <TextComponent
                    numberOfLines={1}
                    style={styles.title}
                >
                    {t('text_choose_order_type')}
                </TextComponent>

                {takeOutOn && (
                    <TouchableComponent
                        onPress={() => onSelectOrderType(ORDER_TYPE.TAKE_AWAY)}
                        style={styles.itemContainer}
                    >
                        <TabShoppingIcon
                            stroke={themeColors.color_text}
                            width={Dimens.H_26}
                            height={Dimens.H_26}
                        />
                        <TextComponent
                            numberOfLines={1}
                            style={styles.itemText}
                        >
                            {t('text_pick_up').toUpperCase()}
                        </TextComponent>
                    </TouchableComponent>
                )}

                {deliveryOn && (
                    <TouchableComponent
                        onPress={() => onSelectOrderType(ORDER_TYPE.DELIVERY)}
                        style={styles.itemContainer}
                    >
                        <TruckIcon
                            strokeWidth={1.5}
                            stroke={themeColors.color_text}
                            width={Dimens.H_26}
                            height={Dimens.H_26}
                        />
                        <TextComponent
                            numberOfLines={1}
                            style={styles.itemText}
                        >
                            {t('text_delivery').toUpperCase()}
                        </TextComponent>
                    </TouchableComponent>
                )}

                {groupOrderOn && (
                    <TouchableComponent
                        onPress={() => onSelectOrderType(ORDER_TYPE.GROUP_ORDER)}
                        style={styles.itemContainer}
                    >
                        <CouplesIcon
                            stroke={themeColors.color_text}
                            width={Dimens.H_26}
                            height={Dimens.H_26}
                        />
                        <TextComponent
                            numberOfLines={1}
                            style={styles.itemText}
                        >
                            {t('options_group_orders').toUpperCase()}
                        </TextComponent>
                    </TouchableComponent>
                )}

            </View>
        </BaseDialog>

    );
};

export default SelectTypeDialog;

const stylesF = (Dimens: DimensType) => StyleSheet.create({
    itemText: {
        fontSize: Dimens.FONT_18,
        fontWeight: '700',
        marginLeft: Dimens.W_18,
    },
    itemContainer: {
        flexDirection: 'row',
        alignItems: 'center',
        marginTop: Dimens.H_30,
        paddingLeft: Dimens.W_10,
    },
    title: { fontSize: Dimens.FONT_24, fontWeight: '700' },
    topContainer: {  },
    contentContainer: {
        paddingHorizontal: Dimens.W_24,
        paddingTop: Dimens.H_24,
        paddingBottom: Dimens.COMMON_BOTTOM_PADDING * 2,
        borderTopLeftRadius: Dimens.H_32,
        borderTopRightRadius: Dimens.H_32,
    },
    dialogContainer: {
        flex: 1,
        justifyContent: 'flex-end',
    },
    viewHeader: {
        alignItems: 'center',
        justifyContent: 'center',
        marginBottom: Dimens.H_16,
    },
    viewDash: {
        height: Dimens.H_4,
        width: Dimens.H_100,
        borderRadius: Dimens.RADIUS_4,
    },
});