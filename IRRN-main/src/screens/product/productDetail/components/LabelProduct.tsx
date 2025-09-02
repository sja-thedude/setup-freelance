import React, {
    FC,
    memo,
    useMemo,
} from 'react';

import {
    ScrollView,
    StyleSheet,
    View,
} from 'react-native';

import {
    ChilliIcon,
    VegetableIcon,
} from '@src/assets/svg';
import TextComponent from '@src/components/TextComponent';
import { Colors } from '@src/configs';
import useDimens, { DimensType } from '@src/hooks/useDimens';
import { Product } from '@src/network/dataModels/ProductSectionModel';
import useThemeColors from '@src/themes/useThemeColors';
import { useTranslation } from 'react-i18next';
import { getProductLabel } from '@src/configs/constants';

interface IProps {
    item?: Product;
}

const LabelProduct: FC<IProps> = ({ item }) => {
    const Dimens = useDimens();
    const styles = stylesF(Dimens);
    const { t } = useTranslation();

    const { themeColors } = useThemeColors();

    const PRODUCT_LABELS = useMemo(() => getProductLabel(t), [t]);

    return (
        <View style={styles.viewLabel}>
            <ScrollView
                contentContainerStyle={styles.scrollView}
                horizontal
            >
                {item?.labels
                    ? item.labels
                            .filter((i) => i.type !== 0)
                            .map((label, index) => {
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
                                        productlabel =
                                          PRODUCT_LABELS.VEGGIE.label;
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
                                    <View
                                        key={index}
                                        style={{ flexDirection: 'row' }}
                                    >
                                        <View
                                            style={[
                                                styles.tag,
                                                { backgroundColor: color },
                                            ]}
                                        >
                                            {icon}
                                            <TextComponent style={styles.tagText}>
                                                {productlabel}
                                            </TextComponent>
                                        </View>
                                    </View>
                                );
                            })
                    : null}
            </ScrollView>
        </View>
    );
};

const stylesF = (Dimens: DimensType) => StyleSheet.create({
    viewLabel: {
        width: '100%',
        position: 'absolute',
        bottom: Dimens.H_8
    },
    scrollView: { paddingHorizontal: Dimens.W_15 },
    tag: {
        flexDirection: 'row',
        alignItems: 'center',
        paddingVertical: Dimens.W_5,
        paddingHorizontal: Dimens.W_8,
        borderRadius: 100,
        marginRight: Dimens.W_5,
    },
    tagText: {
        fontSize: Dimens.FONT_10,
        fontWeight: '500',
        color: Colors.COLOR_WHITE,
        marginLeft: Dimens.W_3,
    },
});

export default memo(LabelProduct);
