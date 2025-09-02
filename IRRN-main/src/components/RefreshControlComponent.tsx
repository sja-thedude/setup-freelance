import React, {
    FC,
    memo,
} from 'react';

import {
    RefreshControl,
    RefreshControlProps,
    RefreshControlPropsAndroid,
    RefreshControlPropsIOS,
} from 'react-native';

import useThemeColors from '@src/themes/useThemeColors';

interface IProps extends RefreshControlProps, RefreshControlPropsAndroid, RefreshControlPropsIOS {
}

const RefreshControlComponent: FC<IProps> = ({ ...rest }) => {
    const { themeColors } = useThemeColors();

    return (
        <RefreshControl
            colors={[themeColors.color_primary]}
            tintColor={themeColors.color_primary}
            {...rest}
        />
    );
};

export default memo(RefreshControlComponent);
