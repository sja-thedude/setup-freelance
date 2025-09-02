import React, {
    FC,
    Fragment,
    memo,
    useCallback,
    useMemo,
    useRef,
    useState,
} from 'react';

import isString from 'lodash/isString';
import { useTranslation } from 'react-i18next';
import {
    ActivityIndicator,
    StyleSheet,
    TouchableOpacity,
    View,
} from 'react-native';
import { useDeepCompareEffect, useEffectOnce } from 'react-use';

import { SuccessSmallIcon } from '@src/assets/svg';
import FlatListComponent from '@src/components/FlatListComponent';
import ScrollViewComponent from '@src/components/ScrollViewComponent';
import TextComponent from '@src/components/TextComponent';
import Toast from '@src/components/toast/Toast';
import { Colors } from '@src/configs';
import {
    CART_DISCOUNT_TYPE,
    REWARD_TYPE,
    VALUE_DISCOUNT_TYPE,
} from '@src/configs/constants';
import {
    useAppDispatch,
    useAppSelector,
} from '@src/hooks';
import useBoolean from '@src/hooks/useBoolean';
import useCallAPI from '@src/hooks/useCallAPI';
import useCheckEmptyCart from '@src/hooks/useCheckEmptyCart';
import useDimens, { DimensType } from '@src/hooks/useDimens';
import {
    Loyalty,
    Reward,
} from '@src/network/dataModels/LoyaltyModal';
import { MyRedeemModel } from '@src/network/dataModels/MyRedeemModel';
import { redeemLoyalty } from '@src/network/services/loyalties';
import {
    getMyRedeemService,
    validateRewardProductService,
} from '@src/network/services/restaurantServices';
import { LoadingActions } from '@src/redux/toolkit/actions/loadingActions';
import { StorageActions } from '@src/redux/toolkit/actions/storageActions';
import useThemeColors from '@src/themes/useThemeColors';

import ModalSuccessReward from './ModalSuccessReward';
import { FlatList } from 'react-native';

interface IProps {
    reward?: Reward;
    setReward?: React.Dispatch<React.SetStateAction<Reward | undefined>>;
    loyalty?: Loyalty;
    onRefresh?: () => void;
    rewardWidth?: number;
}

export interface DataSuccessProps {
    time?: string,
    email?: string,
    reward_level_id?: number
}

const ViewDetailReward: FC<IProps> = ({ reward, loyalty, onRefresh, setReward, rewardWidth }) => {
    const { themeColors } = useThemeColors();
    const Dimens = useDimens();
    const styles = stylesF(Dimens);
    const listRef = useRef<FlatList>(null);
    const isUserScroll = useRef(false);

    const CONTAINER_WIDTH = useMemo(() => Dimens.SCREEN_WIDTH - (rewardWidth || 0), [Dimens.SCREEN_WIDTH, rewardWidth]);
    const CARD_WIDTH = useMemo(() => CONTAINER_WIDTH - Dimens.W_16 * 2, [CONTAINER_WIDTH, Dimens.W_16]);

    const dispatch = useAppDispatch();
    const { t } = useTranslation();
    const isEmptyCart = useCheckEmptyCart();

    const [isVisible, showModal, hideModal] = useBoolean();

    const [loading, setLoading] = useState<boolean>(false);
    const [dataSuccess, setDataSuccess] = useState<DataSuccessProps | undefined>();

    const cartProducts = useAppSelector((state) => state.storageReducer.cartProducts.data);

    const rewardList = useMemo(() => loyalty?.rewards?.length ? loyalty?.rewards : [undefined], [loyalty?.rewards]);

    useEffectOnce(() => {
        setDataSuccess({
            time: reward?.last_redeem_history?.created_at,
            email: loyalty?.user.email,
            reward_level_id: reward?.last_redeem_history?.reward_level_id
        });
    });

    useDeepCompareEffect(() => {
        const index = rewardList?.findIndex((r) => r?.id === reward?.id);
        if (index >= 0) {
            isUserScroll.current = false;
            listRef.current?.scrollToIndex({ index: index, animated: true });
        }
    }, [reward?.id, rewardList]);

    const handleOnScroll = useCallback((event: any) => {
        if (isUserScroll.current) {
            const currentIndex = parseInt(`${(event.nativeEvent.contentOffset.x / CARD_WIDTH).toFixed(1)}`);
            if (rewardList[currentIndex]) {
                setReward && setReward(rewardList[currentIndex]);
            }
        }
    }, [CARD_WIDTH, rewardList, setReward]);

    const handleUserBeginScroll = useCallback(() => {
        isUserScroll.current = true;
    }, []);

    const { callApi: validateRewardProduct } = useCallAPI(
            validateRewardProductService,
            useCallback(() => {
                dispatch(LoadingActions.showGlobalLoading(true));
            }, [dispatch])
    );

    const { callApi: getMyRedeem } = useCallAPI(
            getMyRedeemService,
            useCallback(() => {
                dispatch(LoadingActions.showGlobalLoading(true));
            }, [dispatch]),
            useCallback((data: MyRedeemModel) => {
                if (!isEmptyCart) {
                    validateRewardProduct({
                        restaurant_id: loyalty?.workspace_id,
                        reward_id: data.reward_data?.id,
                        product_id: cartProducts.map((i) => i.id)
                    }).then((result) => {
                        if (result.success) {
                            const resultProducts = Object.entries(result.data).filter((i) => i[1] === true);
                            if (resultProducts.length > 0) {
                                Toast.showToast(t('message_auto_apply_loyalty'));
                                // apply discount to the cart
                                dispatch(StorageActions.setStorageDiscount({
                                    discountType: CART_DISCOUNT_TYPE.LOYALTY_DISCOUNT,
                                    discount: { ...data.reward_data, redeem_id: data.id },
                                }));
                            } else {
                                Toast.showToast(t('message_use_loyalty'));
                            }
                        }
                    });
                } else {
                    Toast.showToast(t('message_use_loyalty'));
                }
            }, [cartProducts, dispatch, isEmptyCart, loyalty?.workspace_id, t, validateRewardProduct])
    );

    const handleRedeem = useCallback(async (rewardItem: Reward) => {
        try {
            setLoading(true);
            const response = await redeemLoyalty(
                    loyalty?.id as number,
                    rewardItem?.id as number,
            );
            !!onRefresh && onRefresh();
            if (rewardItem?.type === REWARD_TYPE.DISCOUNT) {
                getMyRedeem({
                    restaurant_id: loyalty?.workspace_id
                });
            } else {
                setDataSuccess({
                    time: response.reward?.last_redeem_history?.created_at,
                    email: response.user.email,
                    reward_level_id: response.reward_level_id
                });
                showModal();
            }
        } catch (error: any) {
            if (isString(error?.response?.data?.message)) {
                Toast.showToast(error?.response?.data?.message);
            }
        } finally {
            setLoading(false);
        }
    }, [getMyRedeem, loyalty?.id, loyalty?.workspace_id, onRefresh, showModal]);

    const renderRewardDetail = useCallback(({ item, index }:{item: Reward, index: number}) => {
        const point = loyalty?.point || 0;
        const score = item?.score || 0;

        const showCheckIcon = () => {
            if (item?.type === REWARD_TYPE.PHYSICAL_GIFT) {
                if (item?.repeat === 1) {
                    return !!dataSuccess && dataSuccess?.reward_level_id === item?.id;
                } else {
                    if (item?.last_redeem_history) {
                        return true;
                    } else {
                        return false;
                    }
                }
            } else {
                return false;
            }
        };

        const buttonStatus = () => {
            let allowClick = true;
            let enableColor = false;
            let title = '';

            if (score > point) {
                allowClick = false;
                enableColor = false;
                title = `${score - point} ${t('credits nodig')}`;

                if (item?.type === REWARD_TYPE.DISCOUNT) {
                    if (item?.is_redeem) {
                        title = t('text_already_redeemed');
                        allowClick = true;
                    } else if (item?.is_used) {
                        title = item?.repeat ? title : t('text_already_redeemed');
                    }
                } else {
                    if (item?.is_redeem) {
                        title = t('text_already_redeemed');
                        allowClick = true;
                    }
                }
            } else {
                if (item?.is_redeem) {
                    if (item?.type === REWARD_TYPE.PHYSICAL_GIFT) {
                        title = item?.repeat ? t('text_redeem') : t('text_already_redeemed');
                        enableColor = !!item?.repeat;
                    } else {
                        title = t('text_already_redeemed');
                        enableColor = false;
                    }
                } else {
                    if (item?.type === REWARD_TYPE.PHYSICAL_GIFT) {
                        title = t('text_redeem');
                        enableColor = true;
                    } else {
                        if (item?.repeat) {
                            title = t('text_redeem');
                            enableColor = true;
                        } else {
                            title = item?.is_used ? t('text_already_redeemed') : t('text_redeem');
                            enableColor = !item?.is_used;
                            allowClick = !item?.is_used;
                        }
                    }
                }
            }

            return { title, allowClick, enableColor };

        };

        return (
            <View
                key={index}
                style={[
                    styles.content,
                    { width: CARD_WIDTH }
                ]}
            >
                <ScrollViewComponent>
                    <View style={styles.viewContent}>
                        {!!item && (
                            <Fragment>
                                <View style={styles.viewFlexRow}>
                                    <TextComponent
                                        style={StyleSheet.flatten([
                                            styles.textTitle,
                                            { color: themeColors?.color_text },
                                        ])}
                                    >
                                        {item?.title}
                                    </TextComponent>

                                    {showCheckIcon() && (
                                        <TouchableOpacity
                                            style={{
                                                marginLeft: Dimens.W_2
                                            }}
                                            onPress={showModal}
                                        >
                                            <SuccessSmallIcon
                                                stroke={themeColors.color_primary}
                                            />
                                        </TouchableOpacity>
                                    )}
                                </View>

                                <TextComponent style={[styles.textScore, { color: themeColors.color_primary }]}>
                                    {score} {t(score > 1 ? 'CREDITS' : 'CREDIT')}
                                </TextComponent>

                                <TextComponent
                                    style={StyleSheet.flatten([
                                        item?.type === REWARD_TYPE.DISCOUNT ? styles.textDesc : styles.textValue ,
                                        { color: item?.type === REWARD_TYPE.DISCOUNT ? themeColors?.color_text : themeColors?.color_input_place_holder },
                                    ])}
                                >
                                    {item?.description}
                                </TextComponent>

                                {item?.type === REWARD_TYPE.DISCOUNT && (
                                    <TextComponent
                                        style={StyleSheet.flatten([
                                            styles.textValue,
                                            {
                                                color: themeColors?.color_input_place_holder,
                                            },
                                        ])}
                                    >
                                        {t('desDiscount', {
                                            value: item?.discount_type === VALUE_DISCOUNT_TYPE.PERCENTAGE
                                            ? `${item?.percentage}%`
                                            : `€${Number(item?.reward || 0)}.00`,
                                        })}
                                    </TextComponent>
                                )}
                            </Fragment>
                        )}
                    </View>
                </ScrollViewComponent>

                <TouchableOpacity
                    disabled={!buttonStatus().allowClick || loading || !item}
                    onPress={() => handleRedeem(item)}
                    style={[
                        styles.viewButton,
                        {
                            backgroundColor: (buttonStatus().enableColor || !item)
                                        ? themeColors.color_primary
                                        : Colors.COLOR_INPUT_PLACE_HOLDER,
                        },
                    ]}
                >
                    {!!item && (
                        <Fragment>
                            {loading ? (
                                    <ActivityIndicator color={Colors.COLOR_WHITE} />
                                ) : (
                                    <TextComponent style={styles.textButton}>
                                        {buttonStatus().title}
                                    </TextComponent>
                                )}
                        </Fragment>
                    )}
                </TouchableOpacity>
            </View>
        );
    }, [CARD_WIDTH, Dimens.W_2, dataSuccess, handleRedeem, loading, loyalty?.point, showModal, styles.content, styles.textButton, styles.textDesc, styles.textScore, styles.textTitle, styles.textValue, styles.viewButton, styles.viewContent, styles.viewFlexRow, t, themeColors?.color_input_place_holder, themeColors.color_primary, themeColors?.color_text]);

    return (
        <View style={{ width: CONTAINER_WIDTH }}>
            <ModalSuccessReward
                dataSuccess={dataSuccess}
                isVisible={isVisible}
                onClose={hideModal}
            />

            <View style={{
                flex: 1,
                marginHorizontal: Dimens.W_16,
                marginBottom: Dimens.H_4,
                marginTop: Dimens.H_18,
                borderRadius: Dimens.RADIUS_6,

                shadowColor: '#000',
                shadowOffset: {
                    width: 0,
                    height: 1,
                },
                shadowOpacity: 0.22,
                shadowRadius: 2.22,
                elevation: 3,

                backgroundColor: themeColors?.color_card_background,
            }}
            >
                <FlatListComponent
                    ref={listRef}
                    style={{
                        flex: 1,
                    }}
                    horizontal
                    pagingEnabled
                    data={rewardList}
                    renderItem={renderRewardDetail}
                    onScroll={handleOnScroll}
                    onScrollBeginDrag={handleUserBeginScroll}
                    scrollEventThrottle={1000}
                >
                </FlatListComponent>
            </View>

        </View>
    );
};

const stylesF = (Dimens: DimensType) => StyleSheet.create({
    container: {},
    content: {
        justifyContent: 'space-between',
    },
    textTitle: {
        fontSize: Dimens.FONT_24,
        fontWeight: '700',
        textAlign: 'center',
        width: '100%'
        // flex: 1,
    },
    textScore: {
        marginTop: Dimens.H_10,
        textAlign: 'center',
        fontSize: Dimens.FONT_16,
        fontWeight: '700',
    },
    textDesc: {
        marginTop: Dimens.H_20,
        fontSize: Dimens.FONT_16,
        fontWeight: '700',
        textAlign: 'center',
    },
    textValue: {
        marginTop: Dimens.H_10,
        textAlign: 'center',
        fontSize: Dimens.FONT_14,
        fontWeight: '400',
    },
    viewButton: {
        height: Dimens.W_95,
        alignItems: 'center',
        justifyContent: 'center',
        paddingHorizontal: Dimens.W_15,
        borderRadius: Dimens.RADIUS_6,
    },
    viewContent: {
        paddingHorizontal: Dimens.H_10,
        paddingVertical: Dimens.W_16,
        // flex: 1,
        alignItems: 'center',
    },
    textButton: {
        fontSize: Dimens.FONT_20,
        fontWeight: '700',
        color: Colors.COLOR_WHITE,
    },
    viewFlexRow: {
        flexDirection: 'row',
        alignItems: 'center',
        paddingHorizontal: Dimens.W_20,
    },
});

export default memo(ViewDetailReward);
