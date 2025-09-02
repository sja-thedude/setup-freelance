import React, {
    FC,
    memo,
    useCallback,
    useMemo,
    useRef,
    useState,
} from 'react';

import update from 'immer';
import {
    Animated,
    StyleSheet,
    TouchableOpacity,
    View,
} from 'react-native';
import { FlatList } from 'react-native-gesture-handler';
import { useAsync } from 'react-use';

import { useRoute } from '@react-navigation/native';
import { CloseModalIcon } from '@src/assets/svg';
import FlatListComponent from '@src/components/FlatListComponent';
import useDimens, { DimensType } from '@src/hooks/useDimens';
import { SCREENS } from '@src/navigation/config/screenName';
import { ProductDetailScreenProps } from '@src/navigation/NavigationRouteProps';
import NavigationService from '@src/navigation/NavigationService';
import {
    Item as ItemOptionItem,
    ProductOptionModel,
} from '@src/network/dataModels/ProductOptionModel';
import {
    fetchDetailProduct,
    fetchOptionProduct,
} from '@src/network/services/productServices';
import { getRestaurantDetailService } from '@src/network/services/restaurantServices';
import useThemeColors from '@src/themes/useThemeColors';

import ButtonsAction from './components/ButtonsAction';
import ItemOption from './components/Item/ItemOption';
import LabelProduct from './components/LabelProduct';
import ListAllergenes from './components/ListAllergenes';
import NameProduct from './components/NameProduct';

interface IProps {
    route: any;
}

const ProductDetailScreen: FC<IProps> = () => {
    const Dimens = useDimens();
    const styles = stylesF(Dimens);

    const IMAGE_WIDTH = Dimens.SCREEN_WIDTH;
    const IMAGE_HEIGHT = Dimens.SCREEN_HEIGHT / 4.5;
    const DEFAULT_IMAGE_PLACE_HOLDER_VIEW_HEIGHT = Dimens.H_100;

    const { params } = useRoute<ProductDetailScreenProps>();
    const { id, restaurantId } = params;
    const { themeColors } = useThemeColors();
    const [optionsItem, setOptionsItem] = useState<ProductOptionModel[]>([]);
    const [isSubmit, setIsSubmit] = useState<boolean>(false);

    const listRef = useRef<FlatList>(null);

    const handleUpdate = useCallback((index: number, i: ProductOptionModel, value: ItemOptionItem[], isWarning: boolean) => {
        !!setOptionsItem &&
                setOptionsItem((state) =>
                    update(state, (draf) => {
                        draf[index] = { ...i, items: value, index: index, isWarning: isWarning };
                    }),
                );
    },
    [setOptionsItem],
    );

    const { value: data } = useAsync(
            () => fetchDetailProduct(id).then((res) => res?.data?.data),
            [id],
    );
    const { value: options } = useAsync(
            () => fetchOptionProduct(id).then((res) => res?.data?.data),
            [id],
    );
    const { value: restaurantData } = useAsync(
            () =>
                getRestaurantDetailService({ restaurant_id: restaurantId }).then(
                        (res) => res?.data?.data,
                ),
            [id],
    );

    const convertedOptions = useMemo(() => options?.map((op: ProductOptionModel, index: number) => ({ ...op, optionOrder: index, items: op.items.map((oi, index) => ({ ...oi, order: index }) )  })), [options]);

    const animatedValue =  useRef(new Animated.Value(0)).current;

    const photoContainerTopSpace = useMemo(() => animatedValue.interpolate({
        inputRange: [0, data?.photo ? IMAGE_HEIGHT : DEFAULT_IMAGE_PLACE_HOLDER_VIEW_HEIGHT],
        outputRange: [0, data?.photo ? -IMAGE_HEIGHT : -DEFAULT_IMAGE_PLACE_HOLDER_VIEW_HEIGHT],
        extrapolate: 'clamp',
    }), [DEFAULT_IMAGE_PLACE_HOLDER_VIEW_HEIGHT, IMAGE_HEIGHT, animatedValue, data?.photo]);

    const photoHeight = useMemo(() => animatedValue.interpolate({
        inputRange: [data?.photo ? -IMAGE_HEIGHT : -DEFAULT_IMAGE_PLACE_HOLDER_VIEW_HEIGHT, 0],
        outputRange: [data?.photo ? IMAGE_HEIGHT * 2 : DEFAULT_IMAGE_PLACE_HOLDER_VIEW_HEIGHT * 2, data?.photo ? IMAGE_HEIGHT : DEFAULT_IMAGE_PLACE_HOLDER_VIEW_HEIGHT],
        extrapolateRight: 'clamp',
    }), [DEFAULT_IMAGE_PLACE_HOLDER_VIEW_HEIGHT, IMAGE_HEIGHT, animatedValue, data?.photo]);

    const handleOnScroll = useCallback((event: any) => {
        Animated.event(
                [
                    {
                        nativeEvent: {
                            contentOffset: { y: animatedValue },
                        }
                    },

                ],
                {
                    listener: (_event: any) => {
                    },
                    useNativeDriver: false
                }
        )(event);
    }, [animatedValue]);

    const renderTopImage = useMemo(() => (
        <Animated.View style={{ position: 'absolute', top: photoContainerTopSpace }}>
            <Animated.View style={
                {
                    width: IMAGE_WIDTH,
                    height: photoHeight,
                }
            }
            >
                {
                    data?.photo ? (
                        <Animated.Image
                            source={{ uri: data?.photo }}
                            style={styles.styleImage}
                        />
                    ) : (
                        <Animated.View style={[styles.viewEmpty, { backgroundColor: themeColors.color_primary }]} />
                    )
                }
            </Animated.View>
            <Animated.View>
                <LabelProduct item={data} />
            </Animated.View>
        </Animated.View>
    ), [photoContainerTopSpace, IMAGE_WIDTH, data, photoHeight, styles.styleImage, styles.viewEmpty, themeColors.color_primary]);

    const renderHeader = useMemo(() => data ? (
            <>
                <NameProduct
                    item={data}
                />
                <ListAllergenes item={data} />
            </>
        ) : null, [data]);

    const renderItem = useCallback(({ item, index } : {item: ProductOptionModel, index: number}) => (
        <ItemOption
            isSubmit={isSubmit}
            handleUpdate={handleUpdate}
            index={index}
            item={item}
        />
    ), [handleUpdate, isSubmit]);

    const renderListOptions = useMemo(() => (
        <Animated.View style={{ flex: 1 }}>
            <FlatListComponent
                ref={listRef}
                data={convertedOptions || []}
                ListHeaderComponent={renderHeader}
                renderItem={renderItem}
                contentContainerStyle={{ paddingBottom: Dimens.H_20, paddingTop: data?.photo ? IMAGE_HEIGHT : DEFAULT_IMAGE_PLACE_HOLDER_VIEW_HEIGHT }}
                scrollEventThrottle={1}
                onScroll={handleOnScroll}
            />
        </Animated.View>
    ), [DEFAULT_IMAGE_PLACE_HOLDER_VIEW_HEIGHT, Dimens.H_20, IMAGE_HEIGHT, convertedOptions, data?.photo, handleOnScroll, renderHeader, renderItem]);

    const renderBottomButton = useMemo(() => (
        <View style={[styles.viewFooter, { backgroundColor: themeColors?.color_app_background, }]}>
            <ButtonsAction
                setIsSubmit={setIsSubmit}
                optionsItem={optionsItem}
                item={data}
                restaurantData={restaurantData}
                listRef={listRef}
            />
            <View style={styles.buttonClose}>
                <TouchableOpacity onPress={() => NavigationService.goBack(SCREENS.PRODUCT_DETAIL_SCREEN)}>
                    <CloseModalIcon
                        width={Dimens.H_56}
                        height={Dimens.H_56}
                        stroke={themeColors.color_text}
                    />
                </TouchableOpacity>
            </View>
        </View>
    ), [Dimens.H_56, data, optionsItem, restaurantData, styles.buttonClose, styles.viewFooter, themeColors?.color_app_background, themeColors.color_text]);

    return (
        <View
            style={[
                styles.container,
                { backgroundColor: themeColors?.color_app_background },
            ]}
        >
            {renderTopImage}
            {renderListOptions}
            {renderBottomButton}
        </View>
    );
};

const stylesF = (Dimens: DimensType) => StyleSheet.create({
    container: { flex: 1 },
    styleImage: { width: '100%', height: '100%' },
    buttonClose: { alignItems: 'center' },
    viewFooter: {
        paddingHorizontal: Dimens.W_12,
        paddingBottom: Dimens.H_8,
    },
    viewEmpty: { width: '100%', height: '100%' },
});

export default memo(ProductDetailScreen);
