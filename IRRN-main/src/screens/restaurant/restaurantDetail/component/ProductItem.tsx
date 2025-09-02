import React, {
    FC,
    memo,
    useCallback,
    useEffect,
    useMemo,
    useState,
} from 'react';

import { isEqual } from 'lodash';
import { useTranslation } from 'react-i18next';
import {
    StyleSheet,
    View,
} from 'react-native';

import { Images } from '@src/assets/images';
import {
    ChilliIcon,
    VegetableIcon,
} from '@src/assets/svg';
import ImageComponent from '@src/components/ImageComponent';
import ScrollViewComponent from '@src/components/ScrollViewComponent';
import ShadowView from '@src/components/ShadowView';
import TextComponent from '@src/components/TextComponent';
import TouchableComponent from '@src/components/TouchableComponent';
import { Colors } from '@src/configs';
import {
    DEFAULT_CURRENCY,
    getProductLabel,
} from '@src/configs/constants';
import useDimens, { DimensType } from '@src/hooks/useDimens';
import { SCREENS } from '@src/navigation/config/screenName';
import NavigationService from '@src/navigation/NavigationService';
import useThemeColors from '@src/themes/useThemeColors';
import formatCurrency from '@src/utils/currencyFormatUtil';

import { ItemRow } from '../RestaurantDetailScreen';
import FavoriteHeart from './Item/FavoriteHeart';

interface IProps {
    item: ItemRow,
}

const ProductItem: FC<IProps> = ({ item }) => {
    const Dimens = useDimens();
    const styles = stylesF(Dimens);

    const { themeColors } = useThemeColors();
    const { t } = useTranslation();

    const PRODUCT_LABELS = useMemo(() => getProductLabel(t), [t]);

    const [productItem, setProductItem] = useState(item);

    const navigateToProductDetail = useCallback(() => {
        NavigationService.navigate(SCREENS.PRODUCT_DETAIL_SCREEN, { id: productItem?.id, restaurantId: productItem?.workspace_id });
    }, [productItem?.id, productItem?.workspace_id]);

    useEffect(() => {
        if (!isEqual(item,productItem)) {
            setProductItem(item);
        }
    }, [item, productItem]);

    const renderEmpty = useMemo(() => (
        <TextComponent style={[styles.textEmpty, { color: themeColors.color_common_description_text, }]}>
            {t('text_no_items')}
        </TextComponent>
    ), [styles.textEmpty, t, themeColors.color_common_description_text]);

    const renderProductInfo = useMemo(() => (
        <View style={{ flexDirection: 'row' }}>
            <View style={{ flex: 1 }}>
                <View style={styles.productNameContainer}>
                    <TextComponent
                        numberOfLines={1}
                        style={styles.productName}
                    >
                        {productItem?.name}
                    </TextComponent>
                    <View style={{ flexDirection: 'row' }}>
                        {productItem?.category?.favoriet_friet && (
                            <ImageComponent
                                resizeMode='stretch'
                                source={Images.icon_friet}
                                style={styles.iconFavorite}
                            />
                        )}

                        {productItem?.category?.kokette_kroket && (
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
                    {productItem?.description}
                </TextComponent>
            </View>

            {productItem?.photo ? (
                            <ImageComponent
                                source={{ uri: productItem?.photo }}
                                defaultImage={Images.image_placeholder}
                                style={styles.productImage}
                            />
                        ) : null}
        </View>
    ), [productItem?.category?.favoriet_friet, productItem?.category?.kokette_kroket, productItem?.description, productItem?.name, productItem?.photo, styles.iconFavorite, styles.productDesc, styles.productImage, styles.productName, styles.productNameContainer, themeColors.color_common_subtext]);

    const renderHeartIcon = useMemo(() => (
        <FavoriteHeart productId={productItem?.id}/>
    ), [productItem?.id]);

    const renderProductLabels = useMemo(() => (
        <View style={styles.bottomContainer}>
            <View style={styles.tagContainer}>
                <ScrollViewComponent
                    horizontal
                    style={{ flexGrow: 0 }}
                >
                    {productItem?.labels ? (
                        productItem?.labels.filter((i) => i.type !== 0).map((label, index) => {
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
                {renderHeartIcon}
            </View>

            <View>
                <TextComponent style={[styles.price, { color: themeColors.color_primary }]}>
                    {`${formatCurrency(productItem?.price, DEFAULT_CURRENCY)[2]}${productItem?.price}`}
                </TextComponent>
            </View>

        </View>
    ), [Dimens.W_11, Dimens.W_12, PRODUCT_LABELS.NEW.label, PRODUCT_LABELS.NEW.type, PRODUCT_LABELS.PROMO.label, PRODUCT_LABELS.PROMO.type, PRODUCT_LABELS.SPICY.label, PRODUCT_LABELS.SPICY.type, PRODUCT_LABELS.VEGAN.label, PRODUCT_LABELS.VEGAN.type, PRODUCT_LABELS.VEGGIE.label, PRODUCT_LABELS.VEGGIE.type, productItem?.labels, productItem?.price, renderHeartIcon, styles.bottomContainer, styles.price, styles.tag, styles.tagContainer, styles.tagText, themeColors.color_primary]);

    return !productItem?.isEmpty ? (
        <TouchableComponent
            onPress={navigateToProductDetail}
        >
            <ShadowView
                style={[
                    styles.shadow,
                    {
                        backgroundColor: themeColors.color_card_background
                    }]}
            >
                <View style={styles.itemContainer}>
                    {renderProductInfo}
                    {renderProductLabels}
                </View>
            </ShadowView>
        </TouchableComponent>
    ) : renderEmpty;
};

export default memo(ProductItem);

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
        shadowColor: 'rgba(0, 0, 0, 0.02)',
        shadowRadius: Dimens.H_10,
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