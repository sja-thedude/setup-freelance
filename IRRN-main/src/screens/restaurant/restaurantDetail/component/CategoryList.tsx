import React, {
    forwardRef,
    Fragment,
    memo,
    useCallback,
    useEffect,
    useImperativeHandle,
    useMemo,
    useRef,
    useState,
} from 'react';

import {
    StyleSheet,
    TextInput,
    TouchableOpacity,
    View,
} from 'react-native';
import { useToggle } from 'react-use';

import { Images } from '@src/assets/images';
import {
    CloseIcon,
    HeartIcon,
    MagnifierIcon,
} from '@src/assets/svg';
import FlatListComponent from '@src/components/FlatListComponent';
import ImageComponent from '@src/components/ImageComponent';
import TextComponent from '@src/components/TextComponent';
import TouchableComponent from '@src/components/TouchableComponent';
import useDimens, { DimensType } from '@src/hooks/useDimens';
import useIsUserLoggedIn from '@src/hooks/useIsUserLoggedIn';
import useThemeColors from '@src/themes/useThemeColors';

import {
    ItemHeader,
    ItemRow,
    PRODUCT_LIST_ITEM_TYPE,
} from '../RestaurantDetailScreen';

interface IProps {
    handleSelectTab: Function;
    onFilterFavorite: Function;
    listCategoryData: Array<ItemHeader | ItemRow>;
    listOriginData: Array<ItemHeader | ItemRow>;
    isFilterFavorite: boolean;
    setListSearchResultData: React.Dispatch<React.SetStateAction<(ItemHeader | ItemRow)[]>>;
    setIsSearch?: React.Dispatch<React.SetStateAction<boolean>>;
}

const CategoryList = forwardRef<any, IProps>(({
    isFilterFavorite,
    listCategoryData,
    listOriginData,
    handleSelectTab,
    onFilterFavorite,
    setListSearchResultData,
    setIsSearch
}, ref: any) => {
    const Dimens = useDimens();
    const styles = stylesF(Dimens);

    const ICON_FRIET_WIDTH = Dimens.W_14;
    const ICON_FRIET_HEIGHT = ICON_FRIET_WIDTH * 1.4;

    const { themeColors } = useThemeColors();
    const [isSearch, toggleSearch] = useToggle(false);
    const [textSearch, setTextSearch] = useState<string>('');
    const [currentTabIndex, setCurrentTabIndex] = useState(0);
    const [isFavorite, setIsFavorite] = useState(isFilterFavorite);

    const refFlatList = useRef<any>();

    const isUserLoggedIn = useIsUserLoggedIn();

    const routes = useMemo(() => listCategoryData
            ?.filter((i) => i.itemType === PRODUCT_LIST_ITEM_TYPE.HEADER)
            ?.map((category) => ({
                key: `${category.id}`,
                title: category.name,
                category: category,
            })) || [], [listCategoryData]);

    const handleFilterFavorite = useCallback(() => {
        setIsFavorite((state) => !state);
        setTimeout(() => {
            onFilterFavorite(!isFilterFavorite);
        }, 100);
    }, [isFilterFavorite, onFilterFavorite]);

    const handleSearchPress = useCallback(() => {
        if (!isSearch) {
            toggleSearch(true);
            setListSearchResultData([]);
            !!setIsSearch && setIsSearch(true);
        } else {
            setTextSearch('');
            toggleSearch(false);
            setCurrentTabIndex(0);
            setTimeout(() => {
                !!setIsSearch && setIsSearch(false);
            }, 100);
        }
    }, [isSearch, setIsSearch, setListSearchResultData, toggleSearch]);

    const clearSearch = useCallback(() => {
        if (textSearch) {
            setTextSearch('');
            setListSearchResultData([]);
        } else {
            handleSearchPress();
        }
    }, [textSearch, setListSearchResultData, handleSearchPress]);

    const onChangeText = useCallback((value: string) => {
        setTextSearch(value);
    }, []);

    useImperativeHandle(ref, () => ({
        setCurrentTabIndex
    }), []);

    useEffect(() => {
        setIsFavorite(isFilterFavorite);
    }, [isFilterFavorite]);

    useEffect(() => {
        if (isSearch) {
            if (textSearch) {
                setListSearchResultData(
                        listOriginData?.filter((i) => {
                            if (i.itemType === PRODUCT_LIST_ITEM_TYPE.ROW) {
                                return i?.name?.toLowerCase()?.includes(textSearch?.toLowerCase()) || i?.description?.toLowerCase()?.includes(textSearch?.toLowerCase());
                            } else {
                                const products = (i as ItemHeader).products?.filter((p) => p.name?.toLowerCase()?.includes(textSearch?.toLowerCase()) || p.description?.toLowerCase()?.includes(textSearch?.toLowerCase())) || [];
                                return products.length > 0;
                            }
                        },
                        ),
                );
            } else {
                setListSearchResultData([]);
            }
        }
    }, [isSearch, listOriginData, setListSearchResultData, textSearch]);

    useEffect(() => {
        if (refFlatList.current) {
            refFlatList.current?.scrollToIndex({ index: currentTabIndex || 0, animated: true });
        }
    }, [currentTabIndex]);

    const renderItem = useCallback(({ item, index }: any) => {

        const isIconLabel = !!item.category.favoriet_friet || !!item.category.kokette_kroket;

        return (
            <TouchableOpacity
                onPress={() => {
                    setCurrentTabIndex(index);
                    onFilterFavorite(false);
                    handleSelectTab(
                            item.category.tabIndex,
                            item.category.index,
                    );
                    refFlatList.current?.scrollToIndex({ index, animated: true });
                }}
                style={[styles.viewItem, index === currentTabIndex && !isFavorite && { borderBottomColor: (isIconLabel ? themeColors.color_fix_tab_selected : themeColors.color_primary) }]}
            >
                {item.category.favoriet_friet && (
                    <ImageComponent
                        resizeMode="stretch"
                        source={Images.icon_friet}
                        style={[styles.iconFavorite, { width: ICON_FRIET_WIDTH, height: ICON_FRIET_HEIGHT, }]}
                    />
                )}

                {item.category.kokette_kroket && (
                    <ImageComponent
                        resizeMode="stretch"
                        source={Images.icon_kroket}
                        style={[styles.iconFavorite, { width: ICON_FRIET_WIDTH, height: ICON_FRIET_HEIGHT, }]}
                    />
                )}

                <TextComponent
                    numberOfLines={1}
                    style={[
                        styles.tabLabel,
                        {
                            color: currentTabIndex === index && !isFavorite
                                    ? isIconLabel ? themeColors.color_fix_tab_selected : themeColors.color_primary
                                    : themeColors.color_text_2,
                        },
                    ]}
                >
                    {item.title.toUpperCase()}
                </TextComponent>
            </TouchableOpacity>
        );
    },[ICON_FRIET_HEIGHT, ICON_FRIET_WIDTH, currentTabIndex, handleSelectTab, isFavorite, onFilterFavorite, styles.iconFavorite, styles.tabLabel, styles.viewItem, themeColors.color_fix_tab_selected, themeColors.color_primary, themeColors.color_text_2]);

    const renderSearchIcon = useMemo(() => (
        <TouchableComponent
            onPress={handleSearchPress}
            hitSlop={Dimens.DEFAULT_HIT_SLOP}
            style={styles.iconContainer}
        >
            <MagnifierIcon
                width={Dimens.W_20}
                height={Dimens.W_20}
                stroke={themeColors.color_primary}
                strokeWidth={2}
            />
        </TouchableComponent>
    ), [Dimens.DEFAULT_HIT_SLOP, Dimens.W_20, handleSearchPress, styles.iconContainer, themeColors.color_primary]);

    const renderSearchInPut = useMemo(() => (
        <View style={styles.viewSearch}>
            <TextInput
                style={StyleSheet.flatten([
                    styles.textInput,
                    { color: themeColors?.color_text },
                ])}
                autoFocus
                value={textSearch}
                onChangeText={onChangeText}
            />
            <TouchableOpacity
                style={styles.viewClose}
                onPress={clearSearch}
            >
                <CloseIcon />
            </TouchableOpacity>
        </View>
    ), [clearSearch, onChangeText, styles.textInput, styles.viewClose, styles.viewSearch, textSearch, themeColors?.color_text]);

    const renderCategoryList = useMemo(() => (
        <Fragment>
            {isUserLoggedIn && (
                <TouchableComponent
                    onPress={handleFilterFavorite}
                    hitSlop={Dimens.DEFAULT_HIT_SLOP}
                    style={styles.iconContainer}
                >
                    <HeartIcon
                        width={Dimens.W_24}
                        height={Dimens.W_24}
                        strokeWidth={1.5}
                        stroke={themeColors.color_primary}
                        fill={
                            isFavorite
                                ? themeColors.color_primary
                                : 'transparent'
                        }
                    />
                </TouchableComponent>
            )}

            {routes?.length > 0 && (
                <FlatListComponent
                    ref={refFlatList}
                    horizontal
                    data={routes}
                    renderItem={renderItem}
                />
            )}

        </Fragment>
    ), [Dimens.DEFAULT_HIT_SLOP, Dimens.W_24, handleFilterFavorite, isFavorite, isUserLoggedIn, renderItem, routes, styles.iconContainer, themeColors.color_primary]);

    return (
        <View
            style={[
                styles.container,
                { backgroundColor: themeColors.color_app_background },
            ]}
        >
            {renderSearchIcon}

            <Fragment>
                {isSearch ? (renderSearchInPut) : (renderCategoryList)}
            </Fragment>
        </View>
    );
});

export default memo(CategoryList);

const stylesF = (Dimens: DimensType) =>
    StyleSheet.create({
        iconContainer: { marginRight: Dimens.W_5 },
        tabLabel: {
            minWidth: Dimens.W_30,
            textAlign: 'center',
            fontSize: Dimens.FONT_14,
            fontWeight: '500',
        },
        tabLabelContainer: {
            flexDirection: 'row',
            alignItems: 'center',
            backgroundColor: 'transparent',
        },
        tabStyle: {
            minHeight: 0,
            backgroundColor: 'transparent',
            height: Dimens.H_24,
            padding: 0,
            paddingVertical: Dimens.W_6,
        },
        tabBarStyle: { backgroundColor: 'transparent' },
        container: {
            flexDirection: 'row',
            alignItems: 'center',
            overflow: 'visible',
            paddingLeft: Dimens.W_10,
            backgroundColor: 'transparent',
            paddingTop: Dimens.H_8,
            zIndex: 99,
        },
        iconFavorite: {
            borderRadius: 0,
            marginRight: 3,
        },
        iconPlaceholderFavorite: {
            width: 1,
        },
        viewSearch: {
            flexDirection: 'row',
            alignItems: 'center',
            borderBottomWidth: 1,
            borderBottomColor: '#DADADA',
            flex: 1,
            height: Dimens.H_24,
            marginRight: Dimens.W_10,
        },
        textInput: {
            fontSize: 14,
            fontWeight: '400',
            flex: 1,
            margin: 0,
            padding: 0,
        },
        viewClose: {
            height: Dimens.H_24,
            width: Dimens.W_24,
            alignItems: 'center',
            justifyContent: 'center',
        },
        itemSeparatorComponent: { width: 8 },
        viewItem: {
            flexDirection: 'row',
            alignItems: 'center',
            paddingBottom: 1.5,
            borderBottomWidth: 2,
            borderBottomColor: 'transparent',
            marginRight: Dimens.W_15,

        },
    });
