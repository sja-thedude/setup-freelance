import { useMemo } from 'react';

import { useAppSelector } from '@src/hooks';

const useGetLikedStatus = (productId?: any): boolean => {
    const productsFavorite = useAppSelector((state) => state.productReducer.productsFavorite);
    const isLiked = useMemo(() => productsFavorite.includes(productId), [productId, productsFavorite]);

    return isLiked;
};

export default useGetLikedStatus;
