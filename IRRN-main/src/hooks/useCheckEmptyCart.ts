import { isEmptyOrUndefined } from '@src/utils';

import { useAppSelector } from '.';
import { useMemo } from 'react';

const useCheckEmptyCart = (): boolean => {
    const storageProducts = useAppSelector((state) => state.storageReducer.cartProducts?.data);

    return useMemo(() => isEmptyOrUndefined(storageProducts), [storageProducts]);
};

export default useCheckEmptyCart;
