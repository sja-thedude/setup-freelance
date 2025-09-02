import React, {
    FC,
    memo,
    useCallback,
} from 'react';

import { useTranslation } from 'react-i18next';
import {
    InteractionManager,
    StyleSheet,
    View,
} from 'react-native';

import ButtonComponent from '@src/components/ButtonComponent';
import DialogComponent from '@src/components/DialogComponent';
import TextComponent from '@src/components/TextComponent';
import Toast from '@src/components/toast/Toast';
import {
    CART_DISCOUNT_TYPE,
    VALUE_DISCOUNT_TYPE,
} from '@src/configs/constants';
import {
    useAppDispatch,
    useAppSelector,
} from '@src/hooks';
import useCallAPI from '@src/hooks/useCallAPI';
import useCheckEmptyCart from '@src/hooks/useCheckEmptyCart';
import useDimens, { DimensType } from '@src/hooks/useDimens';
import { CouponDetailModal } from '@src/network/dataModels/CouponDetailModal';
import { CouponRestaurant } from '@src/network/dataModels/CouponModal';
import {
    validateCouponCodeService,
    validateProductCouponService,
} from '@src/network/services/couponServices';
import { LoadingActions } from '@src/redux/toolkit/actions/loadingActions';
import { StorageActions } from '@src/redux/toolkit/actions/storageActions';
import useThemeColors from '@src/themes/useThemeColors';

interface IProps {
    isVisible?: boolean;
    onClose?: () => void;
    item?: CouponRestaurant;
    restaurantId?: number | undefined;
}

const ModalCoupon: FC<IProps> = ({ isVisible, onClose, item, restaurantId }) => {
    const Dimens = useDimens();
    const styles = stylesF(Dimens);

    const { themeColors } = useThemeColors();
    const { t } = useTranslation();
    const isEmptyCart = useCheckEmptyCart();
    const dispatch = useAppDispatch();

    const cartProducts = useAppSelector((state) => state.storageReducer.cartProducts.data);

    const { callApi: validateProductCoupon } = useCallAPI(
            validateProductCouponService,
            useCallback(() => {
                dispatch(LoadingActions.showGlobalLoading(true));
            }, [dispatch])
    );

    const { callApi: validateCouponCode } = useCallAPI(
            validateCouponCodeService,
            useCallback(() => {
                dispatch(LoadingActions.showGlobalLoading(true));
            }, [dispatch])
    );

    const handlePress = useCallback(() => {
        !!onClose && onClose();

        if (isEmptyCart) {
            InteractionManager.runAfterInteractions(() => {
                Toast.showToast(
                        t('product_error_no_item_in_cart'),
                );
            });
            return;
        } else {
            validateCouponCode({
                code: item?.code,
                workspace_id: restaurantId
            }).then((resData) => {
                if (resData.success) {
                    const data: CouponDetailModal = resData.data;
                    validateProductCoupon({
                        product_id: cartProducts.map((p) => p.id),
                        code: data.code
                    }).then((result) => {
                        if (result.success) {
                            const resultProducts = Object.entries(result.data).filter((i) => i[1] === true);
                            if (resultProducts.length > 0) {
                                // apply coupon discount to the cart
                                dispatch(StorageActions.setStorageDiscount({
                                    discountType: CART_DISCOUNT_TYPE.COUPON_DISCOUNT,
                                    discount: data,
                                }));
                                Toast.showToast(t('cart_get_coupon_success'));
                            } else {
                                Toast.showToast(t('coupon_discount_does_not_apply_in_cart'));
                            }
                        }
                    });
                }
            });
        }

    }, [cartProducts, dispatch, isEmptyCart, item?.code, onClose, restaurantId, t, validateCouponCode, validateProductCoupon]);

    return (
        <DialogComponent
            isVisible={isVisible}
            hideModal={onClose}
        >
            <View>
                <TextComponent
                    adjustsFontSizeToFit
                    numberOfLines={1}
                    style={StyleSheet.flatten([
                        styles.textHeader,
                        { color: themeColors?.color_text },
                    ])}
                >
                    {t('cart_hint_coupon_code')}:{' '}
                    <TextComponent style={[styles.textCode, { color: themeColors.color_primary }]}>{item?.code}</TextComponent>
                </TextComponent>

                <TextComponent
                    style={StyleSheet.flatten([
                        styles.textName,
                        { color: themeColors?.color_text },
                    ])}
                >
                    {item?.promo_name}
                </TextComponent>

                <TextComponent
                    style={StyleSheet.flatten([
                        styles.textDiscount,
                        { color: themeColors?.color_common_subtext },
                    ])}
                >
                    {t('couponDesc', {
                        value: item?.discount_type === VALUE_DISCOUNT_TYPE.PERCENTAGE
                        ? `${item?.percentage}%`
                        : `€${Number(item?.discount)?.toFixed(2)}`,
                    })}
                </TextComponent>

                <View style={styles.viewButton}>
                    <ButtonComponent
                        onPress={handlePress}
                        title={t('text_goto_shop_cart')}
                        style={styles.button}
                    />
                </View>
            </View>
        </DialogComponent>
    );
};

const stylesF = (Dimens: DimensType) => StyleSheet.create({
    textHeader: {
        marginTop: Dimens.H_16,
        fontSize: Dimens.FONT_24,
        fontWeight: '700',
        textAlign: 'center',
    },
    textCode: { textTransform: 'uppercase', fontWeight: '700', fontSize: Dimens.FONT_24 },
    textName: {
        fontSize: Dimens.FONT_14,
        fontWeight: '400',
        textAlign: 'center',
        marginTop: Dimens.H_4,
    },
    textDiscount: {
        textAlign: 'center',
        marginTop: Dimens.H_34,
        fontSize: Dimens.FONT_15,
        fontWeight: '400',
    },
    viewButton: { alignItems: 'center', marginTop: Dimens.H_24, width: '100%' },
    button: { paddingHorizontal: Dimens.W_38 },
});

export default memo(ModalCoupon);
