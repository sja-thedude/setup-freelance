import React, {
    FC,
    useCallback,
    useState,
} from 'react';

import { useTranslation } from 'react-i18next';
import {
    InteractionManager,
    StyleSheet,
    View,
} from 'react-native';
import { useDispatch } from 'react-redux';

import { AddressType } from '@screens/home/component/HomeHeader';
import {
    MagnifierIcon,
    RadioButtonCheckedIcon,
    RadioButtonUnCheckedIcon,
} from '@src/assets/svg';
import ButtonComponent from '@src/components/ButtonComponent';
import TextComponent from '@src/components/TextComponent';
import Toast from '@src/components/toast/Toast';
import TouchableComponent from '@src/components/TouchableComponent';
import {
    ADDRESS_TYPE,
    CART_DISCOUNT_TYPE,
    ORDER_TYPE,
} from '@src/configs/constants';
import { useAppSelector } from '@src/hooks';
import useBoolean from '@src/hooks/useBoolean';
import useCallAPI from '@src/hooks/useCallAPI';
import useDimens, { DimensType } from '@src/hooks/useDimens';
import useIsUserLoggedIn from '@src/hooks/useIsUserLoggedIn';
import { SCREENS } from '@src/navigation/config/screenName';
import NavigationService from '@src/navigation/NavigationService';
import { DeliveryConditionModel, } from '@src/network/dataModels/DeliveryConditionModel';
import { RestaurantDetailModel, } from '@src/network/dataModels/RestaurantDetailModel';
import { getRestaurantDeliveryConditionService, } from '@src/network/services/restaurantServices';
import { StorageActions } from '@src/redux/toolkit/actions/storageActions';
import { ProductInCart } from '@src/redux/toolkit/slices/storageSlice';
import useThemeColors from '@src/themes/useThemeColors';

import BaseDialog from './BaseDialog';
import ErrorOutOfDeliveryDistanceDialog
    from './ErrorOutOfDeliveryDistanceDialog';

interface IProps {
    setCurrentDialog: Function,
    product: ProductInCart,
    isInCart?: boolean,
    restaurantData?: RestaurantDetailModel,
    showErrorNotDelivery: Function,
    callback?: Function,
}

const SelectAddressDialog: FC<IProps> = ({ product, isInCart, restaurantData, showErrorNotDelivery, callback }) => {
    const { themeColors } = useThemeColors();
    const Dimens = useDimens();
    const styles = stylesF(Dimens);

    const { t } = useTranslation();
    const dispatch = useDispatch();

    const [isShowErrorDeliveryDistance, showErrorDeliveryDistance, hideErrorDeliveryDistance] = useBoolean(false);

    const isUserLoggedIn = useIsUserLoggedIn();
    const userData = useAppSelector((state) => state.storageReducer.userData);
    const currentAddress = useAppSelector((state) => state.locationReducer);
    const discountInfo = useAppSelector((state) => state.storageReducer.cartProducts.discountInfo);

    const [currentSearchAddress, setCurrentSearchAddress] = useState(currentAddress);
    const [selectedAddressType, setSelectedAddressType] = useState(ADDRESS_TYPE.SELECTED_ADDRESS);

    const { callApi: getRestaurantDeliveryCondition, loading } = useCallAPI(
            getRestaurantDeliveryConditionService,
            undefined,
            useCallback((data: Array<DeliveryConditionModel>) => {
                if (data.length === 0) {
                    // show out of delivery distance popup
                    showErrorDeliveryDistance();
                } else {
                    // update cart type
                    dispatch(StorageActions.clearStorageGroupFilter());
                    if (discountInfo?.discountType === CART_DISCOUNT_TYPE.GROUP_DISCOUNT) {
                        dispatch(StorageActions.clearStorageDiscount());
                    }
                    dispatch(StorageActions.setStorageCartType(ORDER_TYPE.DELIVERY));

                    // update cart address
                    dispatch(StorageActions.setStorageDeliveryInfo({
                        address: selectedAddressType === ADDRESS_TYPE.PROFILE_ADDRESS ? userData?.address : currentSearchAddress.address,
                        lat: selectedAddressType === ADDRESS_TYPE.PROFILE_ADDRESS ? userData?.lat : currentSearchAddress.lat,
                        lng: selectedAddressType === ADDRESS_TYPE.PROFILE_ADDRESS ? userData?.lng : currentSearchAddress.lng,
                        addressType: selectedAddressType,
                        settingDeliveryConditionId: data[0].id,
                        deliveryFee: data[0],
                    }));

                    if (!isInCart) {
                        // add the product to cart
                        dispatch(StorageActions.setStorageProductsCart(product));

                        if (!product.category.available_delivery) {
                            // show error dialog
                            showErrorNotDelivery();
                        } else {
                            Toast.showToast(t('product_add_to_cart_success'));
                            NavigationService.pop(2);
                        }
                    } else {
                        NavigationService.goBack();
                        InteractionManager.runAfterInteractions(() => {
                            callback && callback();
                        });
                    }
                }
            }, [callback, currentSearchAddress.address, currentSearchAddress.lat, currentSearchAddress.lng, discountInfo?.discountType, dispatch, isInCart, product, selectedAddressType, showErrorDeliveryDistance, showErrorNotDelivery, t, userData?.address, userData?.lat, userData?.lng])
    );

    const handleSelectAddress = useCallback((newAddress: AddressType) => {
        setCurrentSearchAddress(newAddress);
    }, []);

    const handleConfirmAddress = useCallback(() => {
        getRestaurantDeliveryCondition({
            restaurant_id: restaurantData?.id,
            lat: selectedAddressType === ADDRESS_TYPE.PROFILE_ADDRESS ? userData?.lat : currentSearchAddress.lat,
            lng: selectedAddressType === ADDRESS_TYPE.PROFILE_ADDRESS ? userData?.lng : currentSearchAddress.lng,
        });
    }, [currentSearchAddress.lat, currentSearchAddress.lng, getRestaurantDeliveryCondition, restaurantData?.id, selectedAddressType, userData?.lat, userData?.lng]);

    return (
        <BaseDialog
            onSwipeHide={() => NavigationService.goBack()}
        >
            <View>
                <TextComponent
                    style={styles.title}
                >
                    {t('text_title_confirm_delivery')}
                </TextComponent>

                {isUserLoggedIn ? (
                    <TouchableComponent
                        onPress={() => {
                            if (userData?.address) {
                                setSelectedAddressType(0);
                            } else {
                                NavigationService.navigate(SCREENS.EDIT_PROFILE_SCREEN_2);
                            }
                        }}
                        style={styles.addressContainer}
                    >
                        {selectedAddressType === ADDRESS_TYPE.PROFILE_ADDRESS ? (
                            <RadioButtonCheckedIcon
                                width={Dimens.H_20}
                                height={Dimens.H_20}
                                stroke={themeColors.color_primary}
                                fill={themeColors.color_primary}
                            />
                        ) : (
                            <RadioButtonUnCheckedIcon
                                width={Dimens.H_20}
                                height={Dimens.H_20}
                            />
                        )}
                        <View style={styles.mAddContainer}>
                            <TextComponent style={styles.mAddressTitle}>
                                {t('text_my_address')}
                            </TextComponent>
                            {userData?.address ? (
                                <TextComponent
                                    style={styles.mAddressText}
                                >
                                    {userData?.address}
                                </TextComponent>
                            ) : (
                                <TextComponent
                                    style={styles.mAddressText}
                                >
                                    {t('goto_profile_setting_address')}
                                    <TextComponent
                                        style={[styles.mAddressText, { textDecorationLine: 'underline' }]}
                                    >
                                        {t('Mijn profiel')}
                                    </TextComponent>
                                    {t('en sla uw adres op')}
                                </TextComponent>
                            )}

                        </View>
                    </TouchableComponent>
                ) : null}

                <TouchableComponent
                    onPress={() => {
                        setSelectedAddressType(1);
                        NavigationService.navigate(SCREENS.SELECT_ADDRESS_SCREEN, { onSelectAddress: handleSelectAddress });
                    }}
                    style={styles.addressContainer}
                >
                    <TouchableComponent
                        hitSlop={Dimens.DEFAULT_HIT_SLOP}
                        onPress={() => setSelectedAddressType(1)}
                    >
                        {selectedAddressType === ADDRESS_TYPE.SELECTED_ADDRESS ? (
                            <RadioButtonCheckedIcon
                                width={Dimens.H_20}
                                height={Dimens.H_20}
                                stroke={themeColors.color_primary}
                                fill={themeColors.color_primary}
                            />
                        ) : (
                            <RadioButtonUnCheckedIcon
                                width={Dimens.H_20}
                                height={Dimens.H_20}
                            />
                        )}
                    </TouchableComponent>
                    <View style={styles.mAddContainer}>
                        <View style={[styles.selectAddressContainer, { borderColor: themeColors.color_common_line }]}>
                            <MagnifierIcon
                                width={Dimens.H_20}
                                height={Dimens.H_20}
                                stroke={themeColors.color_primary}
                            />
                            <TextComponent
                                numberOfLines={1}
                                style={styles.selectAddressText}
                            >
                                {currentSearchAddress.address}
                            </TextComponent>
                        </View>
                    </View>

                </TouchableComponent>

                <ButtonComponent
                    loading={loading}
                    title={t('text_confirm')}
                    style={styles.button}
                    onPress={handleConfirmAddress}
                />
            </View>

            <ErrorOutOfDeliveryDistanceDialog
                hideModal={hideErrorDeliveryDistance}
                isShow={isShowErrorDeliveryDistance}
                restaurantData={restaurantData}
            />
        </BaseDialog>
    );
};

export default SelectAddressDialog;

const stylesF = (Dimens: DimensType) => StyleSheet.create({
    button: { width: '50%', alignSelf: 'center', marginTop: Dimens.H_10 },
    selectAddressText: {
        fontSize: Dimens.FONT_12,
        fontWeight: '700',
        flex: 1,
        marginLeft: Dimens.W_8,
    },
    selectAddressContainer: {
        flexDirection: 'row',
        alignItems: 'center',
        borderRadius: Dimens.RADIUS_6,
        borderWidth: 1,
        paddingVertical: Dimens.H_10,
        paddingHorizontal: Dimens.W_8,
    },
    mAddContainer: { marginLeft: Dimens.W_16, flex: 1 },
    mAddressText: { fontSize: Dimens.FONT_12, fontWeight: '400' },
    mAddressTitle: { fontSize: Dimens.FONT_16, fontWeight: '700' },
    addressContainer: {
        flexDirection: 'row',
        alignItems: 'center',
        paddingRight: Dimens.W_24,
        marginBottom: Dimens.H_16,
    },
    title: {
        fontSize: Dimens.FONT_24,
        fontWeight: '700',
        marginBottom: Dimens.H_24,
        textAlign: 'center',
    },
});