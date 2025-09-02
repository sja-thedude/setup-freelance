import React, {
    FC,
    memo,
} from 'react';

import {
    Animated,
    StyleSheet,
    TextProps,
    TextStyle,
} from 'react-native';

import { IS_ANDROID } from '@src/configs/constants';
import useDimens, { DimensType } from '@src/hooks/useDimens';
import useThemeColors from '@src/themes/useThemeColors';
import { convertFontWeightToFontFamily } from '@src/utils/fontUtil';

interface TextComponentProps extends Animated.AnimatedProps<TextProps> {
    style?: Animated.AnimatedProps<TextStyle>
}

const AnimatedTextComponent: FC<TextComponentProps> = ({ style, ...rest }) => {
    const { themeColors } = useThemeColors();
    const Dimens = useDimens();
    const styles = stylesF(Dimens);

    return (
        <Animated.Text
            style={[styles.textStyle, { color: themeColors.color_text }, style, { fontFamily: convertFontWeightToFontFamily(style) }]}
            allowFontScaling={false}
            {...rest}
            { ...(IS_ANDROID && { fontWeight: undefined }) }
        />
    );
};

const stylesF = (Dimens: DimensType) => StyleSheet.create({
    textStyle: { includeFontPadding: false, fontSize: Dimens.FONT_14 },
});

export default memo(AnimatedTextComponent);
