import React, {
    FC,
    memo,
    useCallback,
    useEffect,
    useState,
} from 'react';

import { debounce } from 'lodash';
import { useDispatch } from 'react-redux';

import { HeartIcon } from '@src/assets/svg';
import TouchableComponent from '@src/components/TouchableComponent';
import useCallAPI from '@src/hooks/useCallAPI';
import useDimens from '@src/hooks/useDimens';
import useIsUserLoggedIn from '@src/hooks/useIsUserLoggedIn';
import { toggleProductFavoriteService, } from '@src/network/services/productServices';
import { ProductActions } from '@src/redux/toolkit/actions/productActions';
import useThemeColors from '@src/themes/useThemeColors';

import useGetLikedStatus from '../useGetLikedStatus';

interface IProps {
    productId?: number
}

const FavoriteHeart: FC<IProps> = ({ productId }) => {
    const Dimens = useDimens();
    const dispatch = useDispatch();

    const { themeColors } = useThemeColors();
    const isUserLoggedIn = useIsUserLoggedIn();

    const isLiked = useGetLikedStatus(productId);

    const [liked, setLiked] = useState(isLiked);

    const { callApi: toggleProductFavorite } = useCallAPI(
            toggleProductFavoriteService,
            undefined,
            useCallback((res: any) => {
                if (res.liked) {
                    dispatch(ProductActions.addFavoriteProduct(productId));
                } else {
                    dispatch(ProductActions.removeFavoriteProduct(productId));
                }
            }, [dispatch, productId]),
    );

    // eslint-disable-next-line react-hooks/exhaustive-deps
    const handleFavoriteProduct = useCallback(debounce(() => {
        setLiked((state) => !state);
        toggleProductFavorite({
            product_id: productId
        });
    }, 500, { leading: true, trailing: false, maxWait: 3000 }), [productId, toggleProductFavorite]);

    useEffect(() => {
        setLiked(isLiked);
    }, [isLiked]);

    return isUserLoggedIn ? (
        <TouchableComponent
            style={{ marginLeft: Dimens.W_10 }}
            hitSlop={Dimens.DEFAULT_HIT_SLOP}
            onPress={handleFavoriteProduct}
        >
            <HeartIcon
                width={Dimens.W_24}
                height={Dimens.W_24}
                stroke={themeColors.color_primary}
                fill={liked ? themeColors.color_primary : 'transparent'}
            />
        </TouchableComponent>
    ) : null;
};

export default memo(FavoriteHeart);