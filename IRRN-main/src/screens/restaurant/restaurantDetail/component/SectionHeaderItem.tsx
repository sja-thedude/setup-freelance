import React, {
    FC,
    memo,
    useCallback,
    useMemo,
} from 'react';

import { useTranslation } from 'react-i18next';
import {
    StyleSheet,
    TouchableOpacity,
    View,
} from 'react-native';
import Popover from 'react-native-popover-view';

import {
    CheckIcon,
    SettingIcon,
} from '@src/assets/svg';
import TextComponent from '@src/components/TextComponent';
import TouchableComponent from '@src/components/TouchableComponent';
import { IS_ANDROID, } from '@src/configs/constants';
import useBoolean from '@src/hooks/useBoolean';
import useDimens, { DimensType } from '@src/hooks/useDimens';
import useThemeColors from '@src/themes/useThemeColors';

import { ItemHeader, } from '../RestaurantDetailScreen';
import { getOrderSortData } from '@src/utils';

interface IProps {
    section: ItemHeader,
    handleSortProduct: Function,
}

const SectionHeaderItem: FC<IProps> = ({ section, handleSortProduct }) => {
    const Dimens = useDimens();
    const styles = stylesF(Dimens);

    const { themeColors } = useThemeColors();
    const { t } = useTranslation();

    const ORDER_SORT_DATA = useMemo(() => getOrderSortData(t), [t]);

    const [isShowSortPopover, showSortPopover, hideSortPopover] = useBoolean(false);

    const sectionData = useMemo(() => ({
        products: section?.products || [],
        sortType: section?.sortType,
        title: section?.title || ''
    }), [section?.products, section?.sortType, section?.title]);

    const sortProduct = useCallback((sortType: number) => () => {
        hideSortPopover();
        setTimeout(() => {
            handleSortProduct(section, sortType);
        }, 300);
    }, [handleSortProduct, hideSortPopover, section]);

    const renderSortPopup = useMemo(() => sectionData.products.length > 1 && (
        <Popover
            backgroundStyle={{ opacity: 0 }}
            isVisible={isShowSortPopover}
            onRequestClose={hideSortPopover}
            offset={IS_ANDROID ? -Dimens.H_24 : Dimens.H_5}
            popoverStyle={[styles.popOverStyle, { backgroundColor: themeColors.color_card_background }]}
            from={(
                <TouchableOpacity
                    onPress={showSortPopover}
                    hitSlop={Dimens.DEFAULT_HIT_SLOP}
                    style={{ marginLeft: Dimens.W_20 }}
                >
                    <SettingIcon
                        stroke={themeColors.color_text_2}
                        strokeWidth={2}
                        width={Dimens.W_18}
                        height={Dimens.W_18}
                    />
                </TouchableOpacity>

            )}
        >
            <View style={styles.popupContentContainer}>
                <TextComponent
                    style={styles.popupTitle}
                >
                    {t('filter_label')}
                </TextComponent>
                {ORDER_SORT_DATA.map((item, index) => (
                    <TouchableComponent
                        key={index}
                        onPress={sortProduct(item.type)}
                        style={styles.filterButtonWrapper}
                    >
                        {sectionData.sortType === item.type ? (
                                <CheckIcon
                                    stroke={themeColors.color_primary}
                                    width={Dimens.W_12}
                                    height={Dimens.W_12}
                                />
                            ) : <View style={{ height: Dimens.W_12, width: Dimens.W_12 }}/>}

                        <TextComponent
                            style={[styles.popupItemText, { color: sectionData.sortType === item.type ? themeColors.color_primary : themeColors.color_text }]}
                        >
                            {item.title}
                        </TextComponent>
                    </TouchableComponent>
                ))}
            </View>
        </Popover>
    ), [Dimens.DEFAULT_HIT_SLOP, Dimens.H_24, Dimens.H_5, Dimens.W_12, Dimens.W_18, Dimens.W_20, ORDER_SORT_DATA, hideSortPopover, isShowSortPopover, sectionData.products.length, sectionData.sortType, showSortPopover, sortProduct, styles.filterButtonWrapper, styles.popOverStyle, styles.popupContentContainer, styles.popupItemText, styles.popupTitle, t, themeColors.color_card_background, themeColors.color_primary, themeColors.color_text, themeColors.color_text_2]);

    return (
        <View
            style={[styles.sectionContainer, { backgroundColor: themeColors.color_app_background }]}
        >
            <View style={[styles.sectionWrapper, { backgroundColor: themeColors.color_app_background, borderBottomColor: themeColors.color_divider }]}>
                <TextComponent
                    numberOfLines={1}
                    style={[styles.sectionText, { color: themeColors.color_text_2 }]}
                >
                    {sectionData.title.toUpperCase()}
                </TextComponent>
                {renderSortPopup}
            </View>
        </View>
    );
};

export default memo(SectionHeaderItem);

const stylesF = (Dimens: DimensType) => StyleSheet.create({
    sectionText: { fontSize: Dimens.FONT_18, fontWeight: '700', flex: 1 },
    sectionWrapper: {
        flexDirection: 'row',
        alignItems: 'center',
        justifyContent: 'space-between',
        marginLeft: Dimens.W_10,
        marginRight: Dimens.W_5,
        paddingBottom: Dimens.H_3,
        borderBottomWidth: 1,
    },
    sectionContainer: { paddingTop: Dimens.H_16, marginBottom: Dimens.H_8 },
    popupItemText: { fontSize: Dimens.FONT_16, marginLeft: Dimens.W_4 },
    filterButtonWrapper: {
        flexDirection: 'row',
        alignItems: 'center',
        marginTop: Dimens.H_14,
        marginLeft: Dimens.W_8,
    },
    popupTitle: { fontWeight: '700', fontSize: Dimens.FONT_16 },
    popupContentContainer: { padding: Dimens.H_24 },
    popOverStyle: { borderRadius: Dimens.H_18 },
});