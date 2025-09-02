import React, {
    useCallback,
    useState,
} from 'react';

import {
    InteractionManager,
    View,
} from 'react-native';

import { InfoIcon } from '@src/assets/svg';
import TouchableComponent from '@src/components/TouchableComponent';
import {
    useAppDispatch,
    useAppSelector,
} from '@src/hooks';
import useBoolean from '@src/hooks/useBoolean';
import useCallAPI from '@src/hooks/useCallAPI';
import useDimens from '@src/hooks/useDimens';
import {
    SettingDeliveryMinCondition,
    SettingOpenHourShort,
} from '@src/network/dataModels/RestaurantNearbyItemModel';
import {
    getRestaurantMinDeliveryConditionService,
    getRestaurantOpeningHourService,
} from '@src/network/services/restaurantServices';
import { LoadingActions } from '@src/redux/toolkit/actions/loadingActions';
import { isTemplateOrGroupApp } from '@src/utils';

import RestaurantInfoDialog from './RestaurantInfoDialog';

const RestaurantInfoIcon = () => {
    const Dimens = useDimens();

    const dispatch = useAppDispatch();

    const [isShowInfo, showInfo, hideInfo] = useBoolean(false);

    const workspaceDetail = useAppSelector((state) => state.storageReducer.templateWorkspaceDetail);
    const restaurantDetail = useAppSelector((state) => state.restaurantReducer.restaurantDetail.data);
    const { lat, lng } = useAppSelector((state) => state.locationReducer);

    const [restaurantDeliveryCondition, setRestaurantDeliveryCondition] = useState<SettingDeliveryMinCondition>();
    const [restaurantOpeningHour, setRestaurantOpeningHour] = useState<Array<SettingOpenHourShort>>([]);

    const { callApi: getRestaurantMinDeliveryCondition } = useCallAPI(
            getRestaurantMinDeliveryConditionService,
            undefined,
            useCallback((data: SettingDeliveryMinCondition) => {
                setRestaurantDeliveryCondition(data);
            }, [])
    );

    const { callApi: getRestaurantOpeningHour } = useCallAPI(
            getRestaurantOpeningHourService,
            useCallback(() => {
                dispatch(LoadingActions.showGlobalLoading(true));
            }, [dispatch]),
            useCallback((data: Array<SettingOpenHourShort>) => {
                setRestaurantOpeningHour(data);
            }, [])
    );

    const handleClickInfoIcon = useCallback(() => {
        Promise.all([
            getRestaurantMinDeliveryCondition({
                restaurant_id: isTemplateOrGroupApp() ? workspaceDetail?.id : restaurantDetail.id,
                lat: lat,
                lng: lng,
            }),
            getRestaurantOpeningHour({
                restaurant_id: isTemplateOrGroupApp() ? workspaceDetail?.id : restaurantDetail.id,
            })
        ]).then(() => {
            InteractionManager.runAfterInteractions(() => {
                showInfo();
            });
        });
    }, [getRestaurantMinDeliveryCondition, getRestaurantOpeningHour, lat, lng, restaurantDetail.id, showInfo, workspaceDetail?.id]);

    return (
        <View>
            <TouchableComponent
                hitSlop={Dimens.DEFAULT_HIT_SLOP}
                onPress={handleClickInfoIcon}
            >
                <InfoIcon
                    width={Dimens.W_20}
                    height={Dimens.W_20}
                />
            </TouchableComponent>

            <RestaurantInfoDialog
                hideModal={hideInfo}
                isShow={isShowInfo}
                restaurantInfo={isTemplateOrGroupApp() ? workspaceDetail : restaurantDetail}
                deliveryCondition={restaurantDeliveryCondition}
                openingHour={restaurantOpeningHour}
            />
        </View>
    );
};

export default RestaurantInfoIcon;