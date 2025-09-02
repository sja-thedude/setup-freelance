import React, {
    FC,
    memo,
    useCallback,
    useMemo,
    useState,
} from 'react';

import { useTranslation } from 'react-i18next';
import {
    Keyboard,
    StyleSheet,
    View,
} from 'react-native';

import ButtonComponent from '@src/components/ButtonComponent';
import InputComponent from '@src/components/InputComponent';
import TextComponent from '@src/components/TextComponent';
import Toast from '@src/components/toast/Toast';
import { CART_DISCOUNT_TYPE } from '@src/configs/constants';
import {
    useAppDispatch,
    useAppSelector,
} from '@src/hooks';
import useCallAPI from '@src/hooks/useCallAPI';
import useDimens, { DimensType } from '@src/hooks/useDimens';
import { CouponDetailModal } from '@src/network/dataModels/CouponDetailModal';
import {
    validateCouponCodeService,
    validateProductCouponService,
} from '@src/network/services/couponServices';
import { LoadingActions } from '@src/redux/toolkit/actions/loadingActions';
import { StorageActions } from '@src/redux/toolkit/actions/storageActions';
import useThemeColors from '@src/themes/useThemeColors';

interface IProps {

}

const CouponInput: FC<IProps> = ({}) => {
    const { themeColors } = useThemeColors();
    const Dimens = useDimens();
    const styles = stylesF(Dimens);

    const { t } = useTranslation();
    const dispatch = useAppDispatch();

    const [code, setCode] = useState('');
    const [error, setError] = useState<any>();

    const cartRestaurant = useAppSelector((state) => state.storageReducer.cartProducts.restaurant);
    const cartProducts = useAppSelector((state) => state.storageReducer.cartProducts.data);
    const discountInfo = useAppSelector((state) => state.storageReducer.cartProducts.discountInfo);

    const showInput = useMemo(() => discountInfo.discountType !== CART_DISCOUNT_TYPE.COUPON_DISCOUNT, [discountInfo.discountType]);

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

    const handleValidateCode = useCallback(() => {
        Keyboard.dismiss();
        validateCouponCode({
            code: code,
            workspace_id: cartRestaurant?.id
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
                            setError('');
                            setCode('');
                            Toast.showToast(t('cart_get_coupon_success'));
                        } else {
                            setError('  ');
                            Toast.showToast(t('coupon_discount_does_not_apply_in_cart'));
                        }
                    } else {
                        setError('  ');
                    }
                });
            } else {
                setError(resData.message);
            }
        });
    }, [cartProducts, cartRestaurant?.id, code, dispatch, t, validateCouponCode, validateProductCoupon]);

    return showInput ? (
        <View style={styles.container}>
            <View style={styles.couponContainer}>
                <InputComponent
                    containerStyle={styles.inputContainer}
                    style={styles.input}
                    autoCapitalize={'characters'}
                    placeholder={t('cart_hint_coupon_code')}
                    borderInput={themeColors.color_common_line}
                    value={code}
                    onChangeText={(text) => {
                        setCode(text.toUpperCase());
                        setError('');
                    }}
                    error={error}
                />

                <ButtonComponent
                    disabled={!code}
                    title={t('text_coupon')}
                    style={styles.couponBtn}
                    styleTitle={{ fontWeight: '600' }}
                    onPress={handleValidateCode}
                />
            </View>
            {error?.trim() ? (
                <TextComponent style={[styles.textError, { color: themeColors.color_error }]}>
                    {error}
                </TextComponent>
            ) : null}
        </View>
    ) : null;
};

export default memo(CouponInput);

const stylesF = (Dimens: DimensType) => StyleSheet.create({
    textError: {
        fontSize: Dimens.FONT_13,
        marginTop: Dimens.H_2,
        marginLeft: Dimens.W_10,
    },
    container: { marginBottom: Dimens.H_12 },
    couponBtn: {
        marginLeft: Dimens.W_10,
        height: Dimens.W_46,
        paddingHorizontal: Dimens.W_20,
    },
    couponContainer: {
        flexDirection: 'row',
        alignItems: 'center',
    },
    input: { fontSize: Dimens.FONT_15, padding: 0 },
    inputContainer: { height: Dimens.W_46, flex: 1 },
});