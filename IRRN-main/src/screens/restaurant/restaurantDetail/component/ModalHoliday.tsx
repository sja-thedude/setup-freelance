import React, {
    FC,
    memo,
    useCallback,
    useEffect,
    useState,
} from 'react';

import {
    InteractionManager,
    ScrollView,
    StatusBar,
    StyleSheet,
    useWindowDimensions,
    View,
} from 'react-native';
import Modal from 'react-native-modal';

import { useIsFocused } from '@react-navigation/native';
import { CloseModalIcon } from '@src/assets/svg';
import ImageLoader from '@src/components/ImageLoader';
import ShadowView from '@src/components/ShadowView';
import TextComponent from '@src/components/TextComponent';
import TouchableComponent from '@src/components/TouchableComponent';
import {
    useAppDispatch,
    useAppSelector,
} from '@src/hooks';
import useBoolean from '@src/hooks/useBoolean';
import useCallAPI from '@src/hooks/useCallAPI';
import useDimens, { DimensType } from '@src/hooks/useDimens';
import { fetchSettingPreference, } from '@src/network/services/restaurantServices';
import { ProductActions } from '@src/redux/toolkit/actions/productActions';
import useThemeColors from '@src/themes/useThemeColors';

interface IProps {
    loading?: boolean;
}

const ModalHoliday: FC<IProps> = () => {
    const Dimens = useDimens();
    const styles = stylesF(Dimens);

    const { height, width } = useWindowDimensions();
    const { themeColors } = useThemeColors();
    const dispatch = useAppDispatch();
    const isFocus = useIsFocused();

    const modalIdsHoliday = useAppSelector((state) => state.productReducer.modalIdsHoliday);
    const restaurantData = useAppSelector((state) => state.restaurantReducer.restaurantDetail.data);

    const [isVisible, showModal, hideModal] = useBoolean();

    const [preferenceData, setPreferenceData] = useState<any>();

    const { callApi: getSettingPreference, loading: loadingPreference } = useCallAPI(
            fetchSettingPreference,
            undefined,
            useCallback((data: any) => {
                setPreferenceData(data);
                if (data?.holiday_text) {
                    setTimeout(() => {
                        showModal();
                    }, 300);
                }
            }, [showModal]),
            undefined,
            true,
            false
    );

    useEffect(() => {
        if (!!restaurantData?.id &&  !modalIdsHoliday?.includes(restaurantData?.id)) {
            InteractionManager.runAfterInteractions(() => {
                getSettingPreference(restaurantData?.id);
            });
        }
    }, [restaurantData?.id, modalIdsHoliday, getSettingPreference]);

    const closeModal = useCallback(() => {
        dispatch(ProductActions.setModalIdsHoliday(restaurantData?.id));
        hideModal();
    }, [dispatch, hideModal, restaurantData?.id]);

    const onModalHide = useCallback(() => {
        setPreferenceData(null);
    }, []);

    return (
        <Modal
            isVisible={isVisible && isFocus && !loadingPreference}
            deviceHeight={height + (StatusBar.currentHeight || 0)}
            deviceWidth={width}
            statusBarTranslucent
            useNativeDriver
            hideModalContentWhileAnimating
            animationIn="fadeIn"
            animationOut="fadeOut"
            style={styles.styleModal}
            onModalHide={onModalHide}
        >
            <View
                style={[
                    styles.container,
                    { backgroundColor: themeColors?.color_card_background },
                ]}
            >
                <View style={styles.viewCenter}>
                    <ShadowView>
                        <ImageLoader
                            source={{ uri: restaurantData?.photo }}
                            style={styles.viewImage}
                        />
                    </ShadowView>

                </View>

                <View style={[styles.viewList, { maxHeight: height / 2 }]}>
                    <ScrollView style={styles.scrollView}>
                        <TextComponent
                            style={StyleSheet.flatten([
                                styles.textContent,
                                { color: themeColors?.color_text },
                            ])}
                        >
                            {preferenceData?.holiday_text}
                        </TextComponent>
                    </ScrollView>
                </View>

                <View style={styles.viewCenter}>
                    <TouchableComponent
                        hitSlop={Dimens.DEFAULT_HIT_SLOP}
                        onPress={closeModal}
                    >
                        <CloseModalIcon
                            width={Dimens.W_36}
                            height={Dimens.W_36}
                            stroke={themeColors.color_text}
                        />
                    </TouchableComponent>
                </View>
            </View>
        </Modal>
    );
};

const stylesF = (Dimens: DimensType) => StyleSheet.create({
    styleModal: { margin: 0 },
    container: {
        borderRadius: Dimens.RADIUS_10,
        paddingTop: Dimens.W_14,
        marginHorizontal: Dimens.W_24,
    },
    viewCenter: { alignItems: 'center' },
    viewImage: {
        width: Dimens.H_70,
        height: Dimens.H_70,
        borderRadius: Dimens.H_70,
    },
    textContent: {
        fontSize: Dimens.FONT_15,
        fontWeight: '400',
        textAlign: 'center',
    },
    scrollView: { paddingHorizontal: Dimens.W_16 },
    viewList: { marginTop: Dimens.H_24, marginBottom: Dimens.H_24 },
});

export default memo(ModalHoliday);
