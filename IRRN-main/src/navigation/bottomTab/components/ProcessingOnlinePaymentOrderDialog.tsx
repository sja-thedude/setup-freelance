import React, { useCallback } from 'react';

import { useTranslation } from 'react-i18next';
import {
    Alert,
    StyleSheet,
    View,
} from 'react-native';
import { useUpdateEffect } from 'react-use';

import { useAppState } from '@react-native-community/hooks';
import ButtonComponent from '@src/components/ButtonComponent';
import DialogComponent from '@src/components/DialogComponent';
import LoadingIndicatorComponent
    from '@src/components/LoadingIndicatorComponent';
import TextComponent from '@src/components/TextComponent';
import { ORDER_STATUS } from '@src/configs/constants';
import {
    useAppDispatch,
    useAppSelector,
} from '@src/hooks';
import useBoolean from '@src/hooks/useBoolean';
import useCallAPI from '@src/hooks/useCallAPI';
import useDimens, { DimensType } from '@src/hooks/useDimens';
import useIsUserLoggedIn from '@src/hooks/useIsUserLoggedIn';
import { SCREENS } from '@src/navigation/config/screenName';
import NavigationService from '@src/navigation/NavigationService';
import { OrderDetailModel } from '@src/network/dataModels/OrderDetailModel';
import {
    cancelOrderService,
    getOrderDetailService,
} from '@src/network/services/orderServices';
import { StorageActions } from '@src/redux/toolkit/actions/storageActions';

interface ModalProps {
}

const ProcessingOnlinePaymentOrderDialog = ({}: ModalProps) => {
    const Dimens = useDimens();
    const styles = stylesF(Dimens);

    const dispatch = useAppDispatch();
    const [isShowPopup, showPopup, hidePopup] = useBoolean(false);
    const isUserLoggedIn = useIsUserLoggedIn();
    const { t } = useTranslation();
    const state = useAppState();
    const userData = useAppSelector((state) => state.storageReducer.userData);

    const orderId = useAppSelector((state) => state.storageReducer.onlinePaymentProcessingOrder);

    const { callApi: getOrderDetail } = useCallAPI(
            getOrderDetailService,
            undefined,
            useCallback((data: OrderDetailModel) => {
                switch (data.status) {
                    case ORDER_STATUS.PAID:
                        NavigationService.navigate(SCREENS.ORDER_SUCCESS_SCREEN, { orderId: orderId });
                        hidePopup();
                        break;
                    case ORDER_STATUS.FAILED:
                        NavigationService.navigate(SCREENS.ORDER_FAIL_SCREEN);
                        hidePopup();
                        break;

                    default:
                        showPopup();
                        break;
                }
            }, [hidePopup, orderId, showPopup]),
            (status: number) => {
                if (status === 404) {
                    NavigationService.navigate(SCREENS.ORDER_FAIL_SCREEN);
                    hidePopup();
                }
            }
    );

    const { callApi: cancelOrder, loading } = useCallAPI(
            cancelOrderService,
    );

    const handleCancelPress = useCallback(() => {
        Alert.alert(
                t('text_order_confirm_cancel'),
                '',
                [
                    {
                        text: t('delete_account_popup_cancel'),
                        style: 'cancel',
                    },
                    {
                        text: t('delete_account_popup_confirm'),
                        onPress: () => {
                            hidePopup();
                            dispatch(StorageActions.clearStorageProcessingOrder());
                            cancelOrder({ order_id: orderId });
                        }
                    }
                ]
        );
    }, [cancelOrder, dispatch, hidePopup, orderId, t]);

    useUpdateEffect(() => {
        if (state === 'active') {
            if (orderId && isUserLoggedIn) {
                getOrderDetail({
                    order_id: orderId
                });
            }
        }
    }, [isUserLoggedIn, orderId, state, userData]);

    return (
        <DialogComponent
            hideModal={hidePopup}
            isVisible={isShowPopup}
            swipeDirection={undefined}
            onBackdropPress={undefined}
            onBackButtonPress={undefined}
        >
            <View style={{ alignItems: 'center' }}>
                <TextComponent style={styles.textTitle}>
                    {t('attention')}
                </TextComponent>

                <TextComponent style={styles.textMsg}>
                    {t('Uw betaling werd nog niet verwerkt. Gelieve te wachten of druk op annuleren!')}
                </TextComponent>

                <LoadingIndicatorComponent/>

                <ButtonComponent
                    loading={loading}
                    title={t('text_cancel')}
                    style={styles.textAButton}
                    onPress={handleCancelPress}
                />
            </View>
        </DialogComponent>
    );
};

export default ProcessingOnlinePaymentOrderDialog;

const stylesF = (Dimens: DimensType) => StyleSheet.create({
    textAButton: {
        width: '60%',
        alignSelf: 'center',
        marginTop: Dimens.H_34,
    },
    textTitle: {
        fontSize: Dimens.FONT_24,
        fontWeight: '700',
        textAlign: 'center',
        marginTop: Dimens.H_16,
        marginHorizontal: Dimens.W_24,
    },
    textMsg: {
        fontSize: Dimens.FONT_18,
        textAlign: 'center',
        marginVertical: Dimens.H_24,
    },
});