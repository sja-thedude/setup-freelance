import React, {
    FC,
    memo,
    useCallback,
    useEffect,
    useState,
} from 'react';

import get from 'lodash/get';
import {
    StyleSheet,
    View,
} from 'react-native';

import { useFocusEffect } from '@react-navigation/native';
import ActivityPanel from '@src/components/ActivityPanel';
import ImageComponent from '@src/components/ImageComponent';
import {
    useAppDispatch,
    useAppSelector,
} from '@src/hooks';
import useCallAPI from '@src/hooks/useCallAPI';
import useDimens, { DimensType } from '@src/hooks/useDimens';
import { DetailLoyaltyRestaurantScreenProps, } from '@src/navigation/NavigationRouteProps';
import {
    Loyalty,
    Reward,
} from '@src/network/dataModels/LoyaltyModal';
import {
    getDetailLoyaltyService,
    getTemplateDetailLoyaltyService,
} from '@src/network/services/loyalties';
import { LoadingActions } from '@src/redux/toolkit/actions/loadingActions';
import {
    isGroupApp,
    isTemplateOrGroupApp,
} from '@src/utils';

import ViewDetailReward from './components/ViewDetailReward';
import ViewReward from './components/ViewReward';

interface IProps {
    route: DetailLoyaltyRestaurantScreenProps;
}

const DetailLoyaltyRestaurantScreen: FC<IProps> = ({ route: { params } }) => {
    const Dimens = useDimens();
    const styles = stylesF(Dimens);

    const id = params?.id;
    const [rewardWidth, setRewardWidth] = useState<number>();
    const [reward, setReward] = useState<Reward>();
    const [loyaltyData, setLoyaltyData] = useState<Loyalty>();
    const dispatch = useAppDispatch();

    const workspaceDetail = useAppSelector((state) => state.storageReducer.templateWorkspaceDetail);

    const { callApi: getDetailLoyalty } = useCallAPI(
            getDetailLoyaltyService,
            useCallback(() => {
                dispatch(LoadingActions.showGlobalLoading(true));
            }, [dispatch]),
            useCallback((data: Loyalty) => {
                if (data.reward) {
                    setReward(data.reward);
                } else {
                    if (data.rewards?.length) {
                        setReward(data.rewards[0]);
                    } else {
                        setReward(undefined);
                    }
                }
                setLoyaltyData(data);
            }, [])
    );

    const { callApi: getTemplateDetailLoyalty } = useCallAPI(
            getTemplateDetailLoyaltyService,
            useCallback(() => {
                dispatch(LoadingActions.showGlobalLoading(true));
            }, [dispatch]),
            useCallback((data: Loyalty) => {
                if (data.reward) {
                    setReward(data.reward);
                } else {
                    if (data.rewards?.length) {
                        setReward(data.rewards[0]);
                    } else {
                        setReward(undefined);
                    }
                }
                setLoyaltyData(data);
            }, [])
    );

    const getData = useCallback(() => {
        if (isTemplateOrGroupApp()) {
            getTemplateDetailLoyalty({
                workspace_id: workspaceDetail?.id
            });
        } else {
            getDetailLoyalty({
                loyalty_id: id
            });
        }
    }, [getDetailLoyalty, getTemplateDetailLoyalty, id, workspaceDetail?.id]);

    useEffect(() => {
        if (isGroupApp()) {
            setLoyaltyData(undefined);
            setReward(undefined);
        }
    }, [workspaceDetail?.id]);

    useFocusEffect(useCallback(() => {
        getData();
    }, [getData]));

    return (
        <ActivityPanel
            hiddenBack={isTemplateOrGroupApp()}
            title={loyaltyData?.workspace.title || loyaltyData?.workspace.name}
        >
            <ImageComponent
                style={styles.viewImage}
                source={{
                    uri:
                        get(reward, 'photo') ||
                        get(loyaltyData, 'workspace.gallery[0].full_path'),
                }}
            />
            {loyaltyData ? (
                <View style={styles.content}>
                    <ViewReward
                        reward={reward}
                        setReward={setReward}
                        setRewardWidth={setRewardWidth}
                        item={loyaltyData}
                    />
                    <ViewDetailReward
                        onRefresh={getData}
                        reward={reward}
                        setReward={setReward}
                        loyalty={loyaltyData}
                        rewardWidth={rewardWidth}
                    />
                </View>
            ) : null}
        </ActivityPanel>
    );
};

const stylesF = (Dimens: DimensType) => StyleSheet.create({
    viewImage: { height: Dimens.SCREEN_WIDTH / 2.1, width: Dimens.SCREEN_WIDTH },
    content: { flex: 1, flexDirection: 'row' },
});

export default memo(DetailLoyaltyRestaurantScreen);
