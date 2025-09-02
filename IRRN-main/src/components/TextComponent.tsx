import React, {
    FC,
    memo,
} from 'react';

import {
    StyleProp,
    StyleSheet,
    Text,
    TextProps,
    TextStyle,
} from 'react-native';

import { IS_ANDROID } from '@src/configs/constants';
import useDimens, { DimensType } from '@src/hooks/useDimens';
import useThemeColors from '@src/themes/useThemeColors';
import { convertFontWeightToFontFamily } from '@src/utils/fontUtil';

interface TextComponentProps extends TextProps {
    style?: StyleProp<TextStyle>;
}

const TextComponent: FC<TextComponentProps> = ({ style, ...rest }) => {
    const { themeColors } = useThemeColors();
    const Dimens = useDimens();
    const styles = stylesF(Dimens);

    return (
        <Text
            style={StyleSheet.flatten([styles.textStyle, { color: themeColors.color_text }, style, { fontFamily: convertFontWeightToFontFamily(style) }])}
            allowFontScaling={false}
            {...rest}
            { ...(IS_ANDROID && { fontWeight: undefined }) }
        />
    );
};

const stylesF = (Dimens: DimensType) => StyleSheet.create({
    textStyle: { includeFontPadding: false, fontSize: Dimens.FONT_14 },
});

export default memo(TextComponent);
