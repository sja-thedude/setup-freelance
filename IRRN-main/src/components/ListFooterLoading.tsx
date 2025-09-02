import React from 'react';

import {
    ActivityIndicator,
    StyleSheet,
    View,
} from 'react-native';

import useDimens, { DimensType } from '@src/hooks/useDimens';
import useThemeColors from '@src/themes/useThemeColors';

const ListFooterLoading = ({ canLoadMore } : {canLoadMore: boolean | undefined}) => {
    const { themeColors } = useThemeColors();
    const Dimens = useDimens();
    const styles = stylesF(Dimens);

    return (
        <View style={[canLoadMore && styles.listFooterComponent]}>{canLoadMore && <ActivityIndicator color={themeColors.color_loading_indicator}/>}</View>
    );
};

export default ListFooterLoading;

const stylesF = (Dimens: DimensType) => StyleSheet.create({
    listFooterComponent: {
        height: Dimens.H_50,
        alignItems: 'center',
        justifyContent: 'center',
    },
});