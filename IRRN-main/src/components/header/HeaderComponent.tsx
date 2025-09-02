import React, {
    FC,
    ReactNode,
} from 'react';

import {
    Animated,
    LayoutChangeEvent,
    StyleSheet,
    ViewStyle,
} from 'react-native';

import useDimens, { DimensType } from '@src/hooks/useDimens';
import useThemeColors from '@src/themes/useThemeColors';

import ShadowView from '../ShadowView';

interface IProps {
    onLayout?: ((_event: LayoutChangeEvent) => void) | undefined,
    style?: ViewStyle,
    disabledShadow?: boolean,
    children?: ReactNode,
}

const HeaderComponent: FC<IProps> = ({ onLayout, style, disabledShadow, children, ...res }) => {
    const { themeColors } = useThemeColors();

    const Dimens = useDimens();
    const styles = stylesF(Dimens);

    return (
        <ShadowView
            disabledShadow={disabledShadow}
            style={{ shadowColor: '#00000010', shadowRadius: Dimens.H_10  }}
        >
            <Animated.View
                onLayout={onLayout}
                style={[styles.headerContainer, { backgroundColor: themeColors.color_primary }, style]}
                {...res}
            >
                {children}
            </Animated.View>
        </ShadowView>
    );
};

export default HeaderComponent;

const stylesF = (Dimens: DimensType) => StyleSheet.create({
    headerContainer: {
        paddingTop: Dimens.COMMON_HEADER_PADDING,
        paddingBottom: Dimens.H_14,
        paddingHorizontal: Dimens.W_20,
        borderBottomStartRadius: Dimens.HEADER_BORDER_RADIUS,
        borderBottomEndRadius: Dimens.HEADER_BORDER_RADIUS,
    },
});