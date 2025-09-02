import React, {
    forwardRef,
    useCallback,
    useEffect,
    useImperativeHandle,
    useMemo,
    useState,
} from 'react';

import last from 'lodash/last';
import sum from 'lodash/sum';
import { useTranslation } from 'react-i18next';
import {
    StyleSheet,
    TouchableOpacity,
    View,
} from 'react-native';
import { useToggle } from 'react-use';

import { DropDownIcon } from '@src/assets/svg';
import TextComponent from '@src/components/TextComponent';
import { Colors } from '@src/configs';
import useDimens, { DimensType } from '@src/hooks/useDimens';
import { ItemOption as ItemOptionItem, } from '@src/network/dataModels/OrderDetailModel';
import { ProductOptionModel } from '@src/network/dataModels/ProductOptionModel';
import useThemeColors from '@src/themes/useThemeColors';

interface IProps {
    index: number;
    item: ProductOptionModel;
    handleUpdate?: (_index: number, _i: ProductOptionModel, _value: ItemOptionItem[], _isWarning: boolean) => void;
    isSubmit?: boolean;
}

const ItemOption = forwardRef<any, IProps>(({ index, item, handleUpdate, isSubmit }, ref) => {
    const Dimens = useDimens();
    const styles = stylesF(Dimens);

    const [isShowMore, toggleShowMore] = useToggle(false);
    const [isMore, setIsMore] = useState<boolean>(false);

    const { themeColors } = useThemeColors();
    const { t } = useTranslation();
    const [itemsSelected, setItemsSelected] = useState<ItemOptionItem[]>([]);

    useImperativeHandle(ref, () => ({}));

    const selectedOther = useMemo(
            () => itemsSelected?.filter((i) => !i?.master),
            [itemsSelected],
    );

    const itemOptions = useMemo(
            () => item?.items?.filter((i) =>  i.available) || [],
            [item?.items],
    );

    const isRequired = useMemo(
            () =>
                !!item?.min && selectedOther?.length < item?.min,
            [item?.min, selectedOther?.length],
    );

    const isWarning = useMemo(
            () =>
                !!isSubmit && isRequired,
            [isRequired, isSubmit],
    );

    useEffect(() => {
        if (handleUpdate) {
            handleUpdate(index, item, itemsSelected, isRequired);
        }
    }, [handleUpdate, index, isRequired, item, itemsSelected]);

    const handleSelecteItem = useCallback(
            (value: ItemOptionItem) => () => {
                if (value?.master) {
                    if (itemsSelected?.some((i) => i?.id === value?.id)) {
                        setItemsSelected([]);
                    } else {
                        setItemsSelected(itemOptions);
                    }
                } else {
                    const max = Number(item?.max || 0);

                    if (itemsSelected?.some((i) => i?.id === value?.id)) {
                        let newValue = itemsSelected?.filter(
                                (i) => i?.id !== value?.id,
                        );
                        if (itemsSelected?.some((i) => i?.master)) {
                            newValue = newValue?.filter((i) => !i?.master);
                        }
                        setItemsSelected(newValue?.slice(0, max));
                    } else {
                        if (selectedOther?.length >= max) {
                            setItemsSelected((state) => [
                                ...state.filter(
                                        (i) => i?.id !== last(selectedOther)?.id,
                                ),
                                value,
                            ]);
                        } else {
                            let newValue = [...itemsSelected, value];

                            const itemsOther = itemOptions.filter((i) => !i?.master);

                            if (newValue?.filter((i) => !i?.master)?.length === itemsOther?.length ) {
                                const newI = itemOptions.find((i) => !!i?.master);
                                if (newI) {
                                    newValue = [
                                        ...newValue,
                                        newI,
                                    ];
                                }

                                newValue = [
                                    ...newValue
                                ].filter(Boolean);

                            }
                            setItemsSelected(newValue);
                        }
                    }
                }
            },
            [item?.max, itemOptions, itemsSelected, selectedOther],
    );

    const valueRender = useMemo(() => {
        if (itemsSelected?.some((i) => i?.master)) {
            const price = Number(
                    itemsSelected?.find((i) => i?.master)?.price,
            );

            if (price > 0) {
                return `€${price.toFixed(2)}`;
            } else if (price < 0) {
                return `- €${Math.abs(price).toFixed(2)}`;
            }
        } else if (itemsSelected?.length > 0) {
            const total = sum(itemsSelected?.map((i) => Number(i?.price)));

            if (total > 0) {
                return `€${total.toFixed(2)}`;
            } else if (total < 0) {
                return `- €${Math.abs(total).toFixed(2)}`;
            }
        }

        return '';
    }, [itemsSelected]);

    const renderOptionHeader = useMemo(() => (
        <View style={[styles.viewHeader, index === 0 && styles.mt20]}>
            <TextComponent
                numberOfLines={1}
                style={StyleSheet.flatten([
                    styles.textTitle,
                    { color: themeColors?.color_product_option },
                ])}
            >
                {item?.name}{' '}
                {isWarning ? (
                            <TextComponent style={styles.textWarning}>
                                (
                                {t('gelieve minimaal {{value}} te kiezen', {
                                    value: item?.min,
                                })}
                                )
                            </TextComponent>
                        ) : (
                            <TextComponent style={[styles.textOption, { color: themeColors?.color_product_option }]}>
                                    ({item?.min === 0 ? t('optioneel') : t('verplicht')})
                            </TextComponent>
                        )}
            </TextComponent>

            {!!valueRender && (
                <TextComponent
                    style={StyleSheet.flatten([
                        styles.textValue,
                        { color: themeColors?.color_text },
                    ])}
                >
                    {valueRender}
                </TextComponent>
            )}
        </View>
    ), [index, isWarning, item?.min, item?.name, styles.mt20, styles.textOption, styles.textTitle, styles.textValue, styles.textWarning, styles.viewHeader, t, themeColors?.color_product_option, themeColors?.color_text, valueRender]);

    const renderOptionItem = useMemo(() => (
        <View style={{ overflow: 'hidden', maxHeight: isMore && isShowMore ? undefined : 145 }} >
            <View
                onLayout={(e) => {
                    setIsMore(e?.nativeEvent?.layout?.height > 145);
                }}
                style={styles.viewItems}
            >
                {itemOptions.map((i, index) => (
                    <TouchableOpacity
                        onPress={handleSelecteItem(i)}
                        key={index}
                        style={[
                            styles.viewItem,
                            { borderColor: themeColors.color_primary },
                            itemsSelected?.some((a) => a?.id === i?.id) &&
                                        (item?.is_ingredient_deletion
                                            ? styles.viewSelectedType1
                                            : { backgroundColor: themeColors.color_primary }),
                        ]}
                    >
                        <TextComponent
                            style={StyleSheet.flatten([
                                styles.textNameItem,
                                { color: themeColors.color_primary },
                                itemsSelected?.some((a) => a?.id === i?.id) &&
                                            (item?.is_ingredient_deletion
                                                ? styles.textSelectedType1
                                                : styles.textSelectedType0),
                            ])}
                        >
                            {i?.name}
                        </TextComponent>
                    </TouchableOpacity>
                ))}
            </View>
        </View>
    ), [handleSelecteItem, isMore, isShowMore, item?.is_ingredient_deletion, itemOptions, itemsSelected, styles.textNameItem, styles.textSelectedType0, styles.textSelectedType1, styles.viewItem, styles.viewItems, styles.viewSelectedType1, themeColors.color_primary]);

    return (
        <View>
            {renderOptionHeader}
            {renderOptionItem}

            {isMore && (
                <TouchableOpacity
                    onPress={toggleShowMore}
                    style={styles.viewMore}
                >
                    <TextComponent style={[styles.textViewMore, { color: themeColors.color_primary }]}>{isShowMore ? t('options_view_less') : t('options_view_more')}</TextComponent>
                    <View style={{ transform: [{ rotate: isShowMore ? '180deg' : '0deg' }] }} >
                        <DropDownIcon
                            width={10}
                            height={8}
                            stroke={themeColors.color_primary}
                        />
                    </View>
                </TouchableOpacity>
            )}
        </View>
    );
},
);

const stylesF = (Dimens: DimensType) => StyleSheet.create({
    viewHeader: {
        flexDirection: 'row',
        alignItems: 'center',
        marginTop: Dimens.H_20,
        paddingHorizontal: Dimens.W_20,
        marginBottom: Dimens.H_5,
    },
    mt20: { marginTop: Dimens.H_40 },
    textTitle: { fontSize: Dimens.FONT_14, fontWeight: '700', flex: 1 },
    textOption: { fontWeight: '400' },
    textWarning: { fontWeight: '400', color: Colors.COLOR_RED_ERROR },
    textValue: { fontSize: Dimens.FONT_16, fontWeight: '700' },
    viewItems: {
        flexDirection: 'row',
        flexWrap: 'wrap',
        paddingHorizontal: Dimens.W_10,
    },
    textNameItem: {
        fontSize: Dimens.FONT_14,
        fontWeight: '700',
    },
    viewItem: {
        paddingHorizontal: Dimens.W_15,
        height: 38,
        alignItems: 'center',
        justifyContent: 'center',
        borderWidth: 2,
        marginLeft: 10,
        borderRadius: Dimens.RADIUS_22,
        marginTop: 10,
    },
    viewSelectedType1: { borderColor: '#CFCFCF' },
    textSelectedType1: { color: '#CFCFCF', textDecorationLine: 'line-through' },
    viewSelectedType0: {},
    textSelectedType0: { color: Colors.COLOR_WHITE },
    textViewMore: { fontWeight: '700', fontSize: Dimens.FONT_14, marginRight: 5, },
    viewMore: {
        flexDirection: 'row',
        alignItems: 'center',
        justifyContent: 'center',
        paddingTop: 10,
    }
});

export default ItemOption;
