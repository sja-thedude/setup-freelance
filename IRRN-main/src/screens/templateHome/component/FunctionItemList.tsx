import React, {
    memo,
    useCallback,
    useMemo,
} from 'react';

import {
    StyleSheet,
    View,
} from 'react-native';

import FlatListComponent from '@src/components/FlatListComponent';
import { useAppSelector } from '@src/hooks';
import useDimens, { DimensType } from '@src/hooks/useDimens';
import { SCREENS } from '@src/navigation/config/screenName';
import NavigationService from '@src/navigation/NavigationService';
import { Meta } from '@src/network/dataModels/WorkspaceSettingModel';

import FuncItem from './FuncItem';

const FunctionItemList = () => {
    const Dimens = useDimens();
    const styles = stylesF(Dimens);

    const workspaceSettingMeta = useAppSelector((state) => state.storageReducer.templateWorkspaceSetting?.meta);

    const workspaceSettingData = useMemo(() => workspaceSettingMeta?.filter((i) => i.active) || [], [workspaceSettingMeta]);

    const handleNavToMenu = useCallback(() => {
        NavigationService.navigate(SCREENS.RESTAURANT_DETAIL_SCREEN);
    }, []);

    const renderFuncItem = useCallback(({ item } : {item: Meta}) => (
        <FuncItem
            item={item}
            handleNavToMenu={handleNavToMenu}
        />
    ), [handleNavToMenu]);

    return (
        <View style={{ flex: 1 }} >
            <FlatListComponent
                horizontal
                data={workspaceSettingData}
                renderItem={renderFuncItem}
                showsVerticalScrollIndicator={false}
                contentContainerStyle={styles.funcList}
            />
        </View>
    );
};

export default memo(FunctionItemList);

const stylesF = (Dimens: DimensType) => StyleSheet.create({
    funcList: {
        paddingHorizontal: Dimens.W_8,
        paddingTop: Dimens.H_25,
    },
});