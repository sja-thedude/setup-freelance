import React, { FC } from 'react';

import { View, ViewProps } from 'react-native';

import useThemeColors from '@src/themes/useThemeColors';

interface IProps extends ViewProps {
}

const ListItemSeparator: FC<IProps> = ({ style, ...rest }) => {
    const { themeColors } = useThemeColors();

    return (
        <View
            style={[{ width: '100%', height: 1, backgroundColor: themeColors.color_divider }, style]}
            {...rest}
        />
    );
};

export default ListItemSeparator;