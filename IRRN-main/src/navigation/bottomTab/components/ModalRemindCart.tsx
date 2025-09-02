import React, {
    memo,
    useCallback,
    useEffect,
    useRef,
} from 'react';

import dayjs from 'dayjs';
import { useTranslation } from 'react-i18next';
import {
    StyleSheet,
    TouchableOpacity,
    View,
} from 'react-native';

import AsyncStorage from '@react-native-async-storage/async-storage';
import ButtonComponent from '@src/components/ButtonComponent';
import DialogComponent from '@src/components/DialogComponent';
import TextComponent from '@src/components/TextComponent';
import {
    useAppDispatch,
    useAppSelector,
} from '@src/hooks';
import useBoolean from '@src/hooks/useBoolean';
import useDimens, { DimensType } from '@src/hooks/useDimens';
import { StorageActions } from '@src/redux/toolkit/actions/storageActions';
import useThemeColors from '@src/themes/useThemeColors';

const ModalRemindCart = () => {
    const Dimens = useDimens();
    const styles = stylesF(Dimens);

    const dispatch = useAppDispatch();
    const { t } = useTranslation();
    const refInterval = useRef<any>();
    const { themeColors } = useThemeColors();
    const [isVisible, showModal, hideModal] = useBoolean();
    const cartProducts = useAppSelector(
            (state) => state?.storageReducer?.cartProducts,
    );

    const handeCheckTimeRemind = useCallback(async () => {
        try {
            if (cartProducts?.data?.length > 0) {
                const cart = {
                    length: cartProducts?.data?.length,
                    time: dayjs().toISOString(),
                };
                const remindCart = await AsyncStorage.getItem('REMIND_CART');
                if (remindCart) {
                    const convertRemindCart = JSON.parse(remindCart);
                    if (convertRemindCart?.length !== cart?.length) {
                        AsyncStorage.setItem(
                                'REMIND_CART',
                                JSON.stringify(cart),
                        );
                    }
                } else {
                    AsyncStorage.setItem('REMIND_CART', JSON.stringify(cart));
                }
            } else {
                AsyncStorage.removeItem('REMIND_CART');
            }
        } catch (error) {
            //
        }
    }, [cartProducts?.data?.length]);

    useEffect(() => {
        handeCheckTimeRemind();
    }, [handeCheckTimeRemind]);

    const handleClearInterval = useCallback(() => {
        if (refInterval.current) {
            clearInterval(refInterval.current);
        }
    }, []);

    const handleCheckCart = useCallback(async () => {
        try {
            const remindCart = await AsyncStorage.getItem('REMIND_CART');
            if (remindCart) {
                const convertRemindCart = JSON.parse(remindCart);
                const timeRemind = dayjs(convertRemindCart?.time).add(
                        60,
                        'minute',
                );
                if (dayjs().diff(timeRemind, 'second') > 0) {
                    showModal();
                    handleClearInterval();
                    AsyncStorage.removeItem('REMIND_CART');
                }
            }
        } catch (error) {
            //
        }
    }, [handleClearInterval, showModal]);

    useEffect(() => {
        handleClearInterval();

        if (cartProducts?.data?.length > 0) {
            refInterval.current = setInterval(handleCheckCart, 1000);
        }

        return () => handleClearInterval();
    }, [cartProducts?.data?.length, handleCheckCart, handleClearInterval]);

    const handleRemoveCart = useCallback(() => {
        dispatch(StorageActions.clearStorageProductsCart());
        hideModal();
    }, [dispatch, hideModal]);

    return (
        <DialogComponent
            isVisible={isVisible}
            hideModal={hideModal}
        >
            <View
                style={styles.container}
            >
                <TextComponent
                    style={StyleSheet.flatten([
                        styles.title,
                        { color: themeColors?.color_text },
                    ])}
                >
                    {t('text_title_inbox_notify')}
                </TextComponent>

                <TextComponent
                    style={StyleSheet.flatten([
                        styles.textDesc,
                        { color: themeColors?.color_text },
                    ])}
                >
                    {t('text_content_inbox_notify')}
                    <TextComponent
                        style={StyleSheet.flatten([
                            styles.textDesc,
                            { color: themeColors?.color_text },
                            styles.textBold
                        ])}
                    >
                        {cartProducts?.restaurant?.title || cartProducts?.restaurant?.name}.
                    </TextComponent>
                </TextComponent>

                <ButtonComponent
                    onPress={hideModal}
                    title={t('text_go_on')}
                    style={styles.viewButton}
                />

                <TouchableOpacity
                    onPress={handleRemoveCart}
                    style={styles.resetCart}
                >
                    <TextComponent
                        style={[
                            styles.textResetCart,
                            { color: themeColors.color_primary },
                        ]}
                    >
                        {t('text_empty_shop_cart')}
                    </TextComponent>
                </TouchableOpacity>
            </View>
        </DialogComponent>
    );
};

const stylesF = (Dimens: DimensType) =>
    StyleSheet.create({
        container: {
            paddingTop: Dimens.H_16,
            paddingHorizontal: Dimens.W_24,
        },
        title: {
            fontSize: Dimens.FONT_24,
            fontWeight: '700',
            textAlign: 'center',
        },
        textBold: { fontWeight: '700', fontSize: Dimens.FONT_18 },
        textDesc: {
            fontSize: Dimens.FONT_18,
            fontWeight: '400',
            textAlign: 'center',
            marginTop: Dimens.H_15,
        },
        viewButton: { marginHorizontal: Dimens.W_20, marginTop: Dimens.H_15 },
        resetCart: { marginTop: Dimens.H_15 },
        textResetCart: {
            fontSize: Dimens.FONT_15,
            fontWeight: '400',
            textAlign: 'center',
        },
    });

export default memo(ModalRemindCart);
