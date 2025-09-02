import { isEmptyOrUndefined } from '@src/utils';

import { useAppSelector } from './';
import { useMemo } from 'react';

const useIsUserLoggedIn = (): boolean => {
    const isUserLoggedIn = useAppSelector((state) => state.storageReducer.userData?.token);

    return useMemo(() => !isEmptyOrUndefined(isUserLoggedIn), [isUserLoggedIn]);
};

export default useIsUserLoggedIn;
