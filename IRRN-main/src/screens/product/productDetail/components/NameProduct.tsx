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
import { useDispatch } from 'react-redux';

import { HeartIcon } from '@src/assets/svg';
import TextComponent from '@src/components/TextComponent';
import TouchableComponent from '@src/components/TouchableComponent';
import useCallAPI from '@src/hooks/useCallAPI';
import useDimens, { DimensType } from '@src/hooks/useDimens';
import useIsUserLoggedIn from '@src/hooks/useIsUserLoggedIn';
import { Product } from '@src/network/dataModels/ProductSectionModel';
import { toggleProductFavoriteService, } from '@src/network/services/productServices';
import { ProductActions } from '@src/redux/toolkit/actions/productActions';
import useThemeColors from '@src/themes/useThemeColors';

interface IProps {
    item?: Product;
}

const NameProduct: FC<IProps> = ({ item }) => {
    const Dimens = useDimens();
    const styles = stylesF(Dimens);

    const dispatch = useDispatch();

    const { themeColors } = useThemeColors();
    const isUserLoggedIn = useIsUserLoggedIn();

    const [isFavorite, setFavorite] = useState(item?.liked);

    const { callApi: toggleProductFavorite } = useCallAPI(
            toggleProductFavoriteService,
            undefined,
            useCallback((res: any) => {
                if (res.liked) {
                    dispatch(ProductActions.addFavoriteProduct(item?.id));
                } else {
                    dispatch(ProductActions.removeFavoriteProduct(item?.id));
                }
            }, [dispatch, item?.id]),
    );

    const handleFavoriteProduct = useCallback(() => {
        setFavorite((state) => !state);
        toggleProductFavorite({
            product_id: item?.id
        });
    }, [item?.id, toggleProductFavorite]);

    const renderProductName = useMemo(() => (
        <View style={styles.viewName}>
            <TextComponent
                style={StyleSheet.flatten([
                    styles.textName,
                    { color: themeColors?.color_text },
                ])}
            >
                {item?.name}
            </TextComponent>

            {isUserLoggedIn && (
                <TouchableComponent
                    onPress={handleFavoriteProduct}
                >
                    <HeartIcon
                        width={Dimens.H_36}
                        height={Dimens.H_36}
                        stroke={themeColors.color_primary}
                        fill={isFavorite ? themeColors.color_primary : 'transparent'}
                    />
                </TouchableComponent>
            )}
        </View>
    ), [Dimens.H_36, handleFavoriteProduct, isFavorite, isUserLoggedIn, item?.name, styles.textName, styles.viewName, themeColors.color_primary, themeColors?.color_text]);

    return (
        <View style={styles.viewHeader}>
            {renderProductName}

            {!!item?.description && (
                <TextComponent
                    style={StyleSheet.flatten([
                        styles.textDescription,
                        {
                            color: themeColors?.color_product_option,
                        },
                    ])}
                >
                    {item?.description}
                </TextComponent>
            )}
        </View>
    );
};

const stylesF = (Dimens: DimensType) => StyleSheet.create({
    viewHeader: { marginTop: Dimens.H_16  },
    textName: {
        flex: 1,
        fontSize: Dimens.FONT_24,
        fontWeight: '700',
        textTransform: 'uppercase',
    },
    textDescription: {
        marginTop: Dimens.H_5,
        paddingHorizontal: Dimens.W_20,
        fontSize: Dimens.FONT_16,
        fontWeight: '400',
    },
    viewName: {
        flexDirection: 'row',
        alignItems: 'center',
        paddingHorizontal: Dimens.W_20,
    },
});

export default memo(NameProduct);
