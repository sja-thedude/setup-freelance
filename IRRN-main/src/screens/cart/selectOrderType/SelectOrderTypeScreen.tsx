import React, {
    useCallback,
    useState,
} from 'react';

import {
    StyleSheet,
    View,
} from 'react-native';
import { useEffectOnce } from 'react-use';

import { useRoute } from '@react-navigation/native';
import { useAppDispatch } from '@src/hooks';
import useBoolean from '@src/hooks/useBoolean';
import useCallAPI from '@src/hooks/useCallAPI';
import useDimens, { DimensType } from '@src/hooks/useDimens';
import { SelectOrderTypeScreenProps, } from '@src/navigation/NavigationRouteProps';
import NavigationService from '@src/navigation/NavigationService';
import { RestaurantDetailModel, } from '@src/network/dataModels/RestaurantDetailModel';
import { getRestaurantDetailService, } from '@src/network/services/restaurantServices';
import { LoadingActions } from '@src/redux/toolkit/actions/loadingActions';

import ErrorNotDeliveryDialog from './component/ErrorNotDeliveryDialog';
import ErrorNotForSaleDialog from './component/ErrorNotForSaleDialog';
import SelectAddressDialog from './component/SelectAddressDialog';
import SelectGroupDialog from './component/SelectGroupDialog';
import SelectTypeDialog from './component/SelectTypeDialog';

export const DIALOG_TYPE = {
    NONE: 0,
    MAIN: 1,
    SELECT_ADDRESS: 2,
    SELECT_GROUP: 3,
};

const SelectOrderTypeScreen = () => {
    const Dimens = useDimens();
    const styles = stylesF(Dimens);

    const { params } = useRoute<SelectOrderTypeScreenProps>();
    const { product, defaultPopup, isInCart, callback } = params;

    const dispatch = useAppDispatch();

    const [currentDialog, setCurrentDialog] = useState(defaultPopup || DIALOG_TYPE.MAIN);
    const [currentRestaurant, setCurrentRestaurant] = useState<RestaurantDetailModel>();

    const [isShowErrorNotForSale, showErrorNotForSale, hideErrorNotForSale] = useBoolean(false);
    const [isShowErrorNotDelivery, showErrorNotDelivery, hideErrorNotDelivery] = useBoolean(false);

    const { callApi: getRestaurantDetail } = useCallAPI(
            getRestaurantDetailService,
            useCallback(() => {
                dispatch(LoadingActions.showGlobalLoading(true));
            }, [dispatch]),
            useCallback((data: RestaurantDetailModel) => {
                setCurrentRestaurant(data);
            }, []),
            useCallback(() => {
                NavigationService.goBack();
            }, [])
    );

    useEffectOnce(() => {
        if ( product?.workspace_id ) {
            getRestaurantDetail({
                restaurant_id: product.workspace_id
            });
        }
    });

    const renderSelectTypeDialog = useCallback(() => (
        <SelectTypeDialog
            isInCart={isInCart}
            product={product}
            setCurrentDialog={setCurrentDialog}
            restaurantData={currentRestaurant}
        />
    ), [currentRestaurant, isInCart, product]);

    const renderSelectGroupDialog = useCallback(() => (
        <SelectGroupDialog
            isInCart={isInCart}
            product={product}
            restaurantData={currentRestaurant}
            setCurrentDialog={setCurrentDialog}
            showErrorNotForSale={showErrorNotForSale}
            showErrorNotDelivery={showErrorNotDelivery}
            callback={callback}
        />
    ), [callback, currentRestaurant, isInCart, product, showErrorNotDelivery, showErrorNotForSale]);

    const renderSelectAddressDialog = useCallback(() => (
        <SelectAddressDialog
            isInCart={isInCart}
            product={product}
            restaurantData={currentRestaurant}
            setCurrentDialog={setCurrentDialog}
            showErrorNotDelivery={showErrorNotDelivery}
            callback={callback}
        />
    ), [callback, currentRestaurant, isInCart, product, showErrorNotDelivery]);

    return (
        <View style={[styles.dialogContainer]}>
            {currentDialog === DIALOG_TYPE.MAIN && currentRestaurant && renderSelectTypeDialog()}
            {currentDialog === DIALOG_TYPE.SELECT_GROUP && currentRestaurant && renderSelectGroupDialog()}
            {currentDialog === DIALOG_TYPE.SELECT_ADDRESS && currentRestaurant && renderSelectAddressDialog()}

            <ErrorNotForSaleDialog
                isShow={isShowErrorNotForSale}
                hideModal={hideErrorNotForSale}
            />

            <ErrorNotDeliveryDialog
                isShow={isShowErrorNotDelivery}
                hideModal={hideErrorNotDelivery}
            />
        </View>
    );
};

export default SelectOrderTypeScreen;

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