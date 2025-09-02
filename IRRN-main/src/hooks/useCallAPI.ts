import {
    useCallback,
    useState,
} from 'react';

import { useDispatch } from 'react-redux';

import Toast from '@src/components/toast/Toast';
import { processResponseData } from '@src/network/util/responseDataUtility';
import { LoadingActions } from '@src/redux/toolkit/actions/loadingActions';

const useCallAPI = (
        service: Function,
        onPreRun?: Function,
        onSuccess?: Function,
        onError?: Function,
        showErrorToast: boolean = true,
        hideGlobalLoading: boolean = true
) => {
    const dispatch = useDispatch();
    const [loading, setLoading] = useState(false);
    const [data, setData] = useState<any>();

    const callApi = useCallback( async (params?: any) => {
        try {
            setLoading(true);
            onPreRun && onPreRun();

            const { data: resData, message, status } = processResponseData(await service(params));

            hideGlobalLoading && dispatch(LoadingActions.showGlobalLoading(false));
            setLoading(false);

            if (resData.success) {
                onSuccess && onSuccess(resData.data, message, status);
                setData(resData);

                return {
                    status,
                    success: true,
                    data: resData.data,
                    message
                };
            } else {
                showErrorToast && message && Toast.showToast(message);
                onError && onError(status, message);
                return {
                    status,
                    success: false,
                    data: null,
                    message
                };
            }

        } catch (error: any) {
            setLoading(false);
            dispatch(LoadingActions.showGlobalLoading(false));
            onError && onError(error?.response?.status, error?.response?.data?.message || error?.message);
            showErrorToast && (error?.response?.data?.message || error?.message) && Toast.showToast(error?.response?.data?.message || error?.message);

            return {
                status: error?.response?.status,
                success: false,
                data: null,
                message: error?.response?.data?.message || error?.message
            };
        }
    }, [dispatch, hideGlobalLoading, onError, onPreRun, onSuccess, service, showErrorToast]);

    return { callApi, loading, data };
};

export default useCallAPI;
