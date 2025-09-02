import React, {
    useCallback,
    useEffect,
} from 'react';

import { StyleSheet } from 'react-native';
import { useDispatch } from 'react-redux';

import ButtonComponent from '@src/components/ButtonComponent';
import DialogComponent from '@src/components/DialogComponent';
import TextComponent from '@src/components/TextComponent';
import useCallAPI from '@src/hooks/useCallAPI';
import useDimens, { DimensType } from '@src/hooks/useDimens';
import NavigationService from '@src/navigation/NavigationService';
import { RestaurantDetailModel, } from '@src/network/dataModels/RestaurantDetailModel';
import { getRestaurantDetailService, } from '@src/network/services/restaurantServices';
import { LoadingActions } from '@src/redux/toolkit/actions/loadingActions';
import { RestaurantActions, } from '@src/redux/toolkit/actions/restaurantActions';
import { StorageActions } from '@src/redux/toolkit/actions/storageActions';
import useThemeColors from '@src/themes/useThemeColors';
import { useTranslation } from 'react-i18next';

interface ModalProps {
    isShow: boolean,
    hideModal: () => void,
    currentRestaurant?: {name: string | null, id: number | null, title: string | null},
    newRestaurant?: RestaurantDetailModel,
    onAddProductToCart: Function,
    isEmptyCart: boolean,
}

const NotEmptyCartWarningDialog = ({ isShow, hideModal, currentRestaurant, newRestaurant, onAddProductToCart, isEmptyCart }: ModalProps) => {
    const Dimens = useDimens();
    const styles = stylesF(Dimens);
    const { t } = useTranslation();

    const { themeColors } = useThemeColors();

    const dispatch = useDispatch();

    const { callApi: getRestaurantDetail } = useCallAPI(
            getRestaurantDetailService,
            useCallback(() => {
                dispatch(LoadingActions.showGlobalLoading(true));
            }, [dispatch]),
            useCallback((data: RestaurantDetailModel) => {
                dispatch(RestaurantActions.updateRestaurantDetail(data));
                NavigationService.goBack();
            }, [dispatch]),
    );

    const gotoCurrentRestaurant = useCallback(() => {
        hideModal();
        getRestaurantDetail({
            restaurant_id: currentRestaurant?.id
        });
    }, [currentRestaurant?.id, getRestaurantDetail, hideModal]);

    const clearProductCart = useCallback(() => {
        // clear cart
        dispatch(StorageActions.clearStorageProductsCart());
    }, [dispatch]);

    useEffect(() => {
        if (isEmptyCart && isShow) {
            onAddProductToCart();
            hideModal();
        }
    }, [hideModal, isEmptyCart, isShow, onAddProductToCart]);

    return (
        <DialogComponent
            hideModal={hideModal}
            isVisible={isShow}
        >
            <TextComponent style={styles.textTitle}>
                {t('text_title_msg_add_product_to_cart')}
            </TextComponent>
            <TextComponent style={styles.textDesc}>
                {t('text_content_msg_add_product_cart_sheet')}
                <TextComponent style={{ fontWeight: '700' }}>
                    {` ${currentRestaurant?.title || currentRestaurant?.name}.`}
                </TextComponent>
            </TextComponent>

            <ButtonComponent
                title={`${t('text_return_restaurant')} ${currentRestaurant?.title || currentRestaurant?.name}`}
                style={styles.textAButton}
                onPress={gotoCurrentRestaurant}
            />
            <ButtonComponent
                title={`${t('text_clear_cart_and_open_product')} ${newRestaurant?.setting_generals?.title}`}
                styleTitle={[styles.btnText, { color: themeColors.color_primary }]}
                style={styles.textCButton}
                onPress={clearProductCart}
            />
        </DialogComponent>
    );
};

export default NotEmptyCartWarningDialog;

const stylesF = (Dimens: DimensType) => StyleSheet.create({
    btnText: { textAlign: 'center' },
    textCButton: {
        width: '75%',
        alignSelf: 'center',
        backgroundColor: 'transparent',
        paddingHorizontal: 0,
    },
    textAButton: {
        width: '75%',
        alignSelf: 'center',
        marginTop: Dimens.H_34,
        marginBottom: Dimens.H_16,
        height: Dimens.W_46
    },
    textTitle: {
        fontSize: Dimens.FONT_24,
        fontWeight: '700',
        textAlign: 'center',
        marginTop: Dimens.H_16,
        marginHorizontal: Dimens.W_24,
    },
    textDesc: {
        fontSize: Dimens.FONT_14,
        fontWeight: '400',
        textAlign: 'center',
        marginTop: Dimens.H_24,
        marginHorizontal: Dimens.W_24,
    },
});