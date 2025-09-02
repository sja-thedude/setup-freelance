import React, {
    FC,
    memo,
    useCallback,
    useMemo,
    useState,
} from 'react';

import {
    StyleSheet,
    View,
} from 'react-native';

import { Images } from '@src/assets/images';
import {
    ChilliIcon,
    HeartIcon,
    VegetableIcon,
} from '@src/assets/svg';
import ImageComponent from '@src/components/ImageComponent';
import ScrollViewComponent from '@src/components/ScrollViewComponent';
import ShadowView from '@src/components/ShadowView';
import TextComponent from '@src/components/TextComponent';
import TouchableComponent from '@src/components/TouchableComponent';
import { Colors } from '@src/configs';
import { DEFAULT_CURRENCY, getProductLabel } from '@src/configs/constants';
import useCallAPI from '@src/hooks/useCallAPI';
import useDimens, { DimensType } from '@src/hooks/useDimens';
import useIsUserLoggedIn from '@src/hooks/useIsUserLoggedIn';
import { SCREENS } from '@src/navigation/config/screenName';
import NavigationService from '@src/navigation/NavigationService';
import { ProductSuggestionModel, } from '@src/network/dataModels/ProductSuggestionModel';
import { toggleProductFavoriteService, } from '@src/network/services/productServices';
import useThemeColors from '@src/themes/useThemeColors';
import formatCurrency from '@src/utils/currencyFormatUtil';
import { useTranslation } from 'react-i18next';

interface IProps {
    item: ProductSuggestionModel,
    hideModal: Function,
}

const SuggestionProductItem: FC<IProps> = ({ item, hideModal }) => {
    const { themeColors } = useThemeColors();
    const Dimens = useDimens();
    const styles = stylesF(Dimens);
    const { t } = useTranslation();

    const PRODUCT_LABELS = useMemo(() => getProductLabel(t), [t]);

    const isUserLoggedIn = useIsUserLoggedIn();

    const [isFavorite, setFavorite] = useState(item.liked);

    const { callApi: toggleProductFavorite } = useCallAPI(
            toggleProductFavoriteService,
            undefined,
            useCallback((res: any) => {
                setFavorite(res.data.liked);
            }, [])
    );

    const handleFavoriteProduct = useCallback(() => {
        setFavorite((state) => !state);
        toggleProductFavorite({
            product_id: item.id
        });
    }, [item.id, toggleProductFavorite]);

    const navigateToProductDetail = useCallback(() => {
        hideModal();
        NavigationService.navigate(SCREENS.PRODUCT_DETAIL_SCREEN, { id: item.id, restaurantId: item.workspace_id });
    }, [hideModal, item.id, item.workspace_id]);

    return (
        <TouchableComponent
            onPress={navigateToProductDetail}
        >
            <ShadowView
                style={[styles.shadow, { backgroundColor: themeColors.color_card_background }]}
            >
                <View style={styles.itemContainer}>
                    <View style={{ flexDirection: 'row' }}>
                        <View style={{ flex: 1 }}>
                            <View style={styles.productNameContainer}>
                                <TextComponent
                                    numberOfLines={1}
                                    style={styles.productName}
                                >
                                    {item.name}
                                </TextComponent>
                                <View style={{ flexDirection: 'row' }}>
                                    {item.category.favoriet_friet && (
                                        <ImageComponent
                                            resizeMode='stretch'
                                            source={Images.icon_friet}
                                            style={styles.iconFavorite}
                                        />
                                    )}

                                    {item.category.kokette_kroket && (
                                        <ImageComponent
                                            resizeMode='stretch'
                                            source={Images.icon_kroket}
                                            style={styles.iconFavorite}
                                        />
                                    )}
                                </View>
                            </View>
                            <TextComponent
                                numberOfLines={5}
                                style={[styles.productDesc, { color: themeColors.color_common_subtext }]}
                            >
                                {item.description}
                            </TextComponent>
                        </View>

                        {item?.photo ? (
                            <ImageComponent
                                source={{ uri: item.photo }}
                                defaultImage={Images.image_placeholder}
                                style={styles.productImage}
                            />
                        ) : null}

                    </View>

                    <View style={styles.bottomContainer}>
                        <View style={styles.tagContainer}>
                            <ScrollViewComponent
                                horizontal
                                style={{ flexGrow: 0 }}
                            >
                                {item?.labels ? (
                                    item.labels.filter((i) => i.type !== 0).map((label, index) => {
                                        let color = themeColors.color_primary;
                                        let productlabel = '';
                                        let icon = null;

                                        switch (label.type) {
                                            case PRODUCT_LABELS.NEW.type:
                                                productlabel = PRODUCT_LABELS.NEW.label;
                                                break;
                                            case PRODUCT_LABELS.PROMO.type:
                                                productlabel = PRODUCT_LABELS.PROMO.label;
                                                break;
                                            case PRODUCT_LABELS.SPICY.type:
                                                color = Colors.COLOR_RED_LABEL;
                                                productlabel = PRODUCT_LABELS.SPICY.label;
                                                icon = (
                                                    <ChilliIcon
                                                        width={Dimens.W_11}
                                                        height={Dimens.W_12}
                                                    />
                                                );
                                                break;
                                            case PRODUCT_LABELS.VEGAN.type:
                                                color = Colors.COLOR_GREEN_VEGAN_LABEL;
                                                productlabel = PRODUCT_LABELS.VEGAN.label;
                                                icon = (
                                                    <VegetableIcon
                                                        width={Dimens.W_11}
                                                        height={Dimens.W_12}
                                                    />
                                                );
                                                break;
                                            case PRODUCT_LABELS.VEGGIE.type:
                                                color = Colors.COLOR_GREEN_VEGGIE_LABEL;
                                                productlabel = PRODUCT_LABELS.VEGGIE.label;
                                                icon = (
                                                    <VegetableIcon
                                                        width={Dimens.W_11}
                                                        height={Dimens.W_12}
                                                    />
                                                );
                                                break;

                                            default:
                                                break;
                                        }

                                        return (
                                            <TouchableComponent
                                                key={index}
                                                activeOpacity={1}
                                                style={{ flexDirection: 'row' }}
                                            >
                                                <View
                                                    style={[styles.tag, { backgroundColor: color }]}
                                                >
                                                    {icon}
                                                    <TextComponent style={styles.tagText}>
                                                        {productlabel}
                                                    </TextComponent>
                                                </View>
                                            </TouchableComponent>
                                        );
                                    })
                                ) : null}
                            </ScrollViewComponent>
                            {isUserLoggedIn && (
                                <TouchableComponent
                                    style={{ marginLeft: Dimens.W_10 }}
                                    hitSlop={Dimens.DEFAULT_HIT_SLOP}
                                    onPress={handleFavoriteProduct}
                                >
                                    <HeartIcon
                                        width={Dimens.W_24}
                                        height={Dimens.W_24}
                                        stroke={themeColors.color_primary}
                                        fill={isFavorite ? themeColors.color_primary : 'transparent'}
                                    />
                                </TouchableComponent>
                            )}
                        </View>

                        <View>
                            <TextComponent style={[styles.price, { color: themeColors.color_primary }]}>
                                {`${formatCurrency(item.price, DEFAULT_CURRENCY)[2]}${item.price}`}
                            </TextComponent>
                        </View>

                    </View>
                </View>

            </ShadowView>
        </TouchableComponent>
    );
};

export default memo(SuggestionProductItem);

const stylesF = (Dimens: DimensType) => StyleSheet.create({
    price: {
        fontSize: Dimens.FONT_16,
        fontWeight: '700',
        marginLeft: Dimens.W_10,
    },
    tagText: {
        fontSize: Dimens.FONT_10,
        fontWeight: '500',
        color: Colors.COLOR_WHITE,
        marginLeft: Dimens.W_3,
    },
    tag: {
        flexDirection: 'row',
        alignItems: 'center',
        paddingVertical: Dimens.W_5,
        paddingHorizontal: Dimens.W_8,
        borderRadius: 100,
        marginRight: Dimens.W_5,
    },
    tagContainer: { flexDirection: 'row', alignItems: 'center', flex: 1 },
    bottomContainer: {
        flexDirection: 'row',
        alignItems: 'center',
        marginTop: Dimens.H_10,
    },
    productImage: {
        width: Dimens.W_128,
        height: Dimens.W_128 / 1.45,
        borderRadius: Dimens.RADIUS_10,
        marginLeft: Dimens.W_10,
    },
    productDesc: { fontSize: Dimens.FONT_14, marginTop: Dimens.H_6 },
    productName: { fontSize: Dimens.FONT_18, fontWeight: '700', flex: 1 },
    productNameContainer: {
        flexDirection: 'row',
        justifyContent: 'space-between',
        alignItems: 'center'
    },
    itemContainer: {
        width: '100%',
        paddingLeft: Dimens.W_16,
        paddingVertical: Dimens.H_10,
        paddingRight: Dimens.W_8,
    },
    shadow: {
        marginBottom: Dimens.H_20,
        borderRadius: Dimens.H_5,
        shadowColor: 'rgba(0, 0, 0, 0.05)',
        shadowRadius: Dimens.H_15,
        shadowOffset: { width: 0, height: Dimens.H_10 },
        shadowOpacity: 1,
    },
    textEmpty: {
        fontSize: Dimens.FONT_14,
        textAlign: 'center',
        paddingVertical: Dimens.H_40,
    },
    iconFavorite: {
        width: Dimens.W_22,
        height: Dimens.W_22 * 1.4,
        borderRadius: 0,
        marginLeft: 3,
    },
});