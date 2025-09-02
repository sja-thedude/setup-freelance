import React, {
    FC,
    Fragment,
    useCallback,
    useEffect,
    useState,
} from 'react';

import { debounce } from 'lodash';
import { useTranslation } from 'react-i18next';
import {
    ActivityIndicator,
    InteractionManager,
    Keyboard,
    StyleSheet,
    View,
} from 'react-native';
import { ScrollView } from 'react-native-gesture-handler';
import { useDispatch } from 'react-redux';

import ButtonComponent from '@src/components/ButtonComponent';
import InputComponent from '@src/components/InputComponent';
import ShadowView from '@src/components/ShadowView';
import TextComponent from '@src/components/TextComponent';
import Toast from '@src/components/toast/Toast';
import TouchableComponent from '@src/components/TouchableComponent';
import {
    ORDER_TYPE,
    PAGE_SIZE,
} from '@src/configs/constants';
import useBoolean from '@src/hooks/useBoolean';
import useCallAPI from '@src/hooks/useCallAPI';
import useDimens, { DimensType } from '@src/hooks/useDimens';
import NavigationService from '@src/navigation/NavigationService';
import { GroupDetailModel } from '@src/network/dataModels/GroupDetailModel';
import { GroupModel } from '@src/network/dataModels/GroupModel';
import { RestaurantDetailModel, } from '@src/network/dataModels/RestaurantDetailModel';
import {
    getDetailGroupService,
    getListGroupService,
} from '@src/network/services/restaurantServices';
import { StorageActions } from '@src/redux/toolkit/actions/storageActions';
import { ProductInCart } from '@src/redux/toolkit/slices/storageSlice';
import useThemeColors from '@src/themes/useThemeColors';
import {
    checkAvailableProductForGroup,
    isEmptyOrUndefined,
} from '@src/utils';
import moment from '@utils/moment';

import { DIALOG_TYPE } from '../SelectOrderTypeScreen';
import BaseDialog from './BaseDialog';
import ModalCreateGroup from './ModalCreateGroup';

interface IProps {
    setCurrentDialog: Function,
    showErrorNotForSale: Function,
    showErrorNotDelivery: Function,
    product: ProductInCart,
    isInCart?: boolean,
    restaurantData?: RestaurantDetailModel,
    callback?: Function,
}

const SelectGroupDialog: FC<IProps> = ({ product, setCurrentDialog, isInCart, showErrorNotForSale, showErrorNotDelivery, restaurantData, callback }) => {
    const { themeColors } = useThemeColors();
    const Dimens = useDimens();
    const styles = stylesF(Dimens);

    const { t } = useTranslation();
    const dispatch = useDispatch();

    const [groupData, setGroupData] = useState<Array<GroupModel>>([]);
    const [selectedGroup, setSelectedGroup] = useState<GroupModel>();

    const [showList, setShowList] = useState(false);
    const [showButton, setShowBUtton] = useState(true);
    const [canShowEmpty, setShowEmpty] = useState(false);

    const [isShowModalCreate, showModalCreate, hideModalCreate] = useBoolean();

    const { callApi: getListGroup, loading } = useCallAPI(
            getListGroupService,
            undefined,
            useCallback((data: any) => {
                setGroupData(data.data);
                setShowEmpty(true);
            }, [])
    );

    const { callApi: getDetailGroup, loading: loadingDetail } = useCallAPI(
            getDetailGroupService,
            undefined,
            useCallback((data: GroupDetailModel) => {

                // clear current filter
                dispatch(StorageActions.clearStorageGroupFilter());
                dispatch(StorageActions.clearStorageDeliveryInfo());
                // clear current discount
                dispatch(StorageActions.clearStorageDiscount());

                const { isNotDelivery, isNotForSale } = checkAvailableProductForGroup(data, product);

                if (data.is_product_limit !== 0) {
                    if (!isInCart) {
                        // add product to cart
                        dispatch(StorageActions.setStorageProductsCart(product));

                        // show alert if error
                        setCurrentDialog(DIALOG_TYPE.NONE);
                        if (isNotForSale) {
                            showErrorNotForSale();
                        } else if (isNotDelivery) {
                            showErrorNotDelivery();
                        }

                        // update group filter
                        dispatch(StorageActions.setStorageGroupFilter(
                                {
                                    data: data,
                                    filterByDeliverable: data.type === ORDER_TYPE.DELIVERY ,
                                }
                        ));

                        // show success toast
                        if (!isNotForSale && !isNotDelivery) {
                            Toast.showToast(t('product_add_to_cart_success'));
                            NavigationService.pop(2);
                        }

                    } else {
                        // update group filter
                        dispatch(StorageActions.setStorageGroupFilter(
                                {
                                    data: data,
                                    filterByDeliverable: data.type === ORDER_TYPE.DELIVERY,
                                }
                        ));
                        InteractionManager.runAfterInteractions(() => {
                            NavigationService.goBack();
                        });
                        InteractionManager.runAfterInteractions(() => {
                            callback && callback();
                        });
                    }

                } else {
                    if (!isInCart) {
                        // add product to cart
                        dispatch(StorageActions.setStorageProductsCart(product));

                        // show alert if error
                        setCurrentDialog(DIALOG_TYPE.NONE);
                        if (isNotDelivery) {
                            showErrorNotDelivery();
                        }

                        // update group filter
                        dispatch(StorageActions.setStorageGroupFilter(
                                {
                                    data: data,
                                    filterByDeliverable: data.type === ORDER_TYPE.DELIVERY,
                                }
                        ));

                        // show success toast
                        if (!isNotDelivery) {
                            InteractionManager.runAfterInteractions(() => {
                                Toast.showToast(t('product_add_to_cart_success'));
                                NavigationService.pop(2);
                            });
                        }
                    } else {
                        // update group filter
                        dispatch(StorageActions.setStorageGroupFilter(
                                {
                                    data: data,
                                    filterByDeliverable: data.type === ORDER_TYPE.DELIVERY,
                                }
                        ));
                        InteractionManager.runAfterInteractions(() => {
                            NavigationService.goBack();
                        });
                        InteractionManager.runAfterInteractions(() => {
                            callback && callback();
                        });
                    }
                }

                // update cart type
                dispatch(StorageActions.setStorageCartType(ORDER_TYPE.GROUP_ORDER));
            }, [callback, dispatch, isInCart, product, setCurrentDialog, showErrorNotDelivery, showErrorNotForSale, t])
    );

    const handleGetGroupDetail = useCallback(() => {
        getDetailGroup({
            group_id: selectedGroup?.id
        });
    }, [getDetailGroup, selectedGroup?.id]);

    const handleStartBestelling = useCallback(() => {
        handleGetGroupDetail();
    }, [handleGetGroupDetail]);

    // eslint-disable-next-line react-hooks/exhaustive-deps
    const handleSearch = useCallback(debounce(async (text) => {
        getListGroup({
            page: 1,
            limit: PAGE_SIZE,
            keyword: text,
            workspace_id: restaurantData?.id
        });
    }, 500), []);

    const listenKeyboardShow = useCallback(() => {
        setShowBUtton(false);
        setShowList(true);
        setSelectedGroup(undefined);
    }, []);

    const listenKeyboardHide = useCallback(() => {
        if (groupData.length) {
            setShowList(selectedGroup === undefined);
            setShowBUtton(selectedGroup !== undefined);
        } else {
            setShowList(false);
            setShowBUtton(true);
        }
    }, [groupData.length, selectedGroup]);

    useEffect(() => {
        const subscriptions = [
            Keyboard.addListener('keyboardWillShow', listenKeyboardShow),
            Keyboard.addListener('keyboardDidShow', listenKeyboardShow),
            Keyboard.addListener('keyboardWillHide', listenKeyboardHide),
            Keyboard.addListener('keyboardDidHide', listenKeyboardHide),
        ];

        return () => {
            subscriptions.forEach((subscription) => subscription.remove());
        };
    }, [listenKeyboardHide, listenKeyboardShow]);

    return (
        <Fragment>
            {isShowModalCreate && <ModalCreateGroup onClose={hideModalCreate} />}
            <BaseDialog
                onSwipeHide={() => NavigationService.goBack()}
            >
                <View>
                    <TextComponent
                        numberOfLines={1}
                        style={styles.title}
                    >
                        {t('text_choose_group')}
                    </TextComponent>

                    <InputComponent
                        containerStyle={styles.inputContainer}
                        style={styles.input}
                        autoCapitalize={'none'}
                        placeholder={t('hint_input_company_or_group_class_name')}
                        borderInput={themeColors.color_common_description_text}
                        backgroundInput={'transparent'}
                        value={!isEmptyOrUndefined(selectedGroup) ? selectedGroup?.name : undefined}
                        rightIcon={loading ? (
                                <ActivityIndicator
                                    size={'small'}
                                    color={themeColors.color_loading_indicator}
                                />
                            ) : null}
                        onChangeText={(text) => {
                            setSelectedGroup(undefined);
                            if (text.length > 2) {
                                handleSearch(text);
                            }
                        }}
                    />

                    {showList ? (
                                <View style={styles.listGroupContainer}>
                                    <ShadowView
                                        style={styles.shadow}
                                    >
                                        <ScrollView
                                            showsHorizontalScrollIndicator={false}
                                            showsVerticalScrollIndicator={false}
                                            keyboardShouldPersistTaps={'handled'}
                                            style={styles.scrollView}
                                        >
                                            <View style={[styles.listWrapper, { backgroundColor: themeColors.color_card_background }]}>
                                                {groupData.length > 0 ? [...groupData].map((item, index) => (
                                                    <TouchableComponent
                                                        key={index}
                                                        onPress={() => {
                                                            setSelectedGroup(item);
                                                            setShowList(false);
                                                            setShowBUtton(true);

                                                            setTimeout(() => {
                                                                Keyboard.dismiss();
                                                            }, 500);
                                                        }}
                                                        style={styles.listItem}
                                                    >
                                                        <TextComponent style={[styles.itemName, { color: themeColors.color_common_subtext }]}>
                                                            {item.name}
                                                        </TextComponent>
                                                        <TextComponent style={[styles.itemTime, { color: themeColors.color_common_description_text }]}>
                                                            {t('text_purchase')} {moment(item.close_time, 'HH:mm:ss').format('HH:mm')}
                                                        </TextComponent>
                                                    </TouchableComponent>

                                                )) : canShowEmpty ? (
                                                    <TextComponent style={[styles.emptyText, { color: themeColors.color_common_description_text }]}>
                                                        {t('tab_search_no_item')}
                                                    </TextComponent>
                                                ) : null}
                                            </View>
                                        </ScrollView>
                                    </ShadowView>
                                </View>
                        ) : null}

                    {showButton ? (
                            <View>
                                <TextComponent
                                    style={styles.descContainer}
                                >
                                    {`${t('text_description_start_order')} `}
                                    <TextComponent
                                        style={{ fontWeight: '700' }}
                                    >
                                        {t('hint_company_name').toLowerCase()}, {t('text_group').toLowerCase()}
                                    </TextComponent>
                                    {` ${t('text_register_of').toLowerCase()} `}
                                    <TextComponent
                                        style={{ fontWeight: '700' }}
                                    >
                                        {t('text_klas').toLowerCase()}
                                    </TextComponent>
                                    {` ${t('text_interest_in_ordering')} `}
                                    <TextComponent
                                        onPress={showModalCreate}
                                        style={{ textDecorationLine: 'underline' }}
                                    >
                                        {t('text_contact_us')}
                                    </TextComponent>
                                </TextComponent>

                                <ButtonComponent
                                    loading={loadingDetail}
                                    disabled={isEmptyOrUndefined(selectedGroup)}
                                    title={t('text_start_order')}
                                    style={{ width: '50%', alignSelf: 'center' }}
                                    onPress={handleStartBestelling}
                                />
                            </View>
                        ) : null}
                </View>
            </BaseDialog>
        </Fragment>

    );
};

export default SelectGroupDialog;

const stylesF = (Dimens: DimensType) => StyleSheet.create({
    descContainer: {
        fontSize: Dimens.FONT_14,
        marginBottom: Dimens.H_30,
        marginTop: Dimens.H_16,
    },
    emptyText: {
        textAlign: 'center',
        alignSelf: 'center',
        marginTop: Dimens.H_8,
    },
    itemTime: { textAlign: 'right' },
    itemName: { flex: 1 },
    listItem: {
        flexDirection: 'row',
        alignItems: 'center',
        paddingHorizontal: Dimens.W_10,
        paddingVertical: Dimens.H_8,
    },
    listWrapper: {
        width: '100%',
        borderRadius: Dimens.H_6,
        minHeight: Dimens.H_50,
        paddingTop: Dimens.H_10,
    },
    scrollView: {
        paddingHorizontal: Dimens.W_8,
        marginHorizontal: -Dimens.W_8,
        borderRadius: Dimens.H_6,
    },
    shadow: { width: '100%' },
    listGroupContainer: {
        minHeight: Dimens.H_100,
        maxHeight: Dimens.H_150,
        width: '100%',
    },
    input: { fontSize: Dimens.FONT_15, padding: 0 },
    inputContainer: { height: Dimens.W_46 },
    title: {
        fontSize: Dimens.FONT_24,
        fontWeight: '700',
        marginBottom: Dimens.H_20,
    },
});