import {
    useCallback,
    useEffect,
} from 'react';

import { LOCALES } from '@src/configs/constants';
import I18nApp from '@src/languages';
import { setHeaderContentLanguage } from '@src/network/axios';
import { getUserProfileService, updateUserLocaleService } from '@src/network/services/profileServices';
import { getWorkspaceLanguageService } from '@src/network/services/restaurantServices';
import { StorageActions } from '@src/redux/toolkit/actions/storageActions';

import { isTemplateOrGroupApp } from '../utils/index';
import {
    useAppDispatch,
    useAppSelector,
} from './';
import useCallAPI from './useCallAPI';
import useIsUserLoggedIn from './useIsUserLoggedIn';
import { UserDataModel } from '@src/network/dataModels';
import { updateUserData } from '@src/network/util/authUtility';

const useGetWorkspaceLanguage = () => {
    const dispatch = useAppDispatch();

    const storageLanguage = useAppSelector((state) => state.storageReducer.language);
    const workspaceDetail = useAppSelector((state) => state.storageReducer.templateWorkspaceDetail);

    const isUserLoggedIn = useIsUserLoggedIn();

    const saveStorageLanguage = useCallback((language: string) => {
        dispatch(StorageActions.setStorageLanguage(language));
        setHeaderContentLanguage(language);
        I18nApp.changeLanguage(language);
    }, [dispatch]);

    const { callApi: updateUserLocale } = useCallAPI(
            updateUserLocaleService
    );

    const { callApi: getUserData } = useCallAPI(
            getUserProfileService,
            undefined,
            useCallback((data: UserDataModel) => {
                updateUserData(data);
            }, [])
    );

    const { callApi: getWorkspaceLanguage } = useCallAPI(
            getWorkspaceLanguageService,
            undefined,
            useCallback((data: any) => {
                const activeLanguages = data?.active_languages as string[] || [];
                if (activeLanguages.length > 0) {
                    dispatch(StorageActions.setStorageWorkspaceLanguages(activeLanguages));

                    if (storageLanguage) {
                        const isSelectedLanguageActive = activeLanguages.some((i: string) => i === storageLanguage);
                        if (!isSelectedLanguageActive) {
                            dispatch(StorageActions.setStorageLanguage(LOCALES.NL));
                            setHeaderContentLanguage(LOCALES.NL);
                            I18nApp.changeLanguage(LOCALES.NL);

                            if (isUserLoggedIn) {
                                updateUserLocale({
                                    locale: LOCALES.NL,
                                }).then((res) => {
                                    if (res?.success) {
                                        saveStorageLanguage(LOCALES.NL);
                                        getUserData();
                                    }
                                });
                            } else {
                                saveStorageLanguage(LOCALES.NL);
                            }
                        }
                    }
                }
            }, [dispatch, getUserData, isUserLoggedIn, saveStorageLanguage, storageLanguage, updateUserLocale])
    );

    useEffect(() => {
        if (isTemplateOrGroupApp() && workspaceDetail?.id) {
            getWorkspaceLanguage({
                restaurant_id: workspaceDetail?.id
            });
        }
    }, [getWorkspaceLanguage, workspaceDetail?.id]);
};

export default useGetWorkspaceLanguage;
