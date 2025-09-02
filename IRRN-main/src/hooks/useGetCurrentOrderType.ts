import { ORDER_TYPE } from '@src/configs/constants';

import { useAppSelector } from './';
import { useMemo } from 'react';

const useGetCurrentOrderType = (): number => {
    const currentOrderType = useAppSelector((state) => state.storageReducer.cartProducts?.type) || ORDER_TYPE.TAKE_AWAY;

    return useMemo(() => currentOrderType, [currentOrderType]);
};

export default useGetCurrentOrderType;
