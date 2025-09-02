import * as React from 'react';

import {
    Animated,
    I18nManager,
    StyleProp,
    StyleSheet,
    ViewStyle,
} from 'react-native';
import {
    NavigationState,
    Route,
    SceneRendererProps,
} from 'react-native-tab-view';
import { useEffectOnce } from 'react-use';

import { Colors } from '@src/configs';
import useDimens, { DimensType } from '@src/hooks/useDimens';
import useThemeColors from '@src/themes/useThemeColors';

export type GetTabWidth = (_index: number) => number;

export type Props<T extends Route> = SceneRendererProps & {
    navigationState: NavigationState<T>;
    width: string | number;
    style?: StyleProp<ViewStyle>;
    getTabWidth: GetTabWidth;
    gap?: number;
    animatedValue: Animated.Value;
};

const getTranslateX = (
        position: Animated.AnimatedInterpolation<any>,
        routes: Route[],
        getTabWidth: GetTabWidth,
        gap?: number
) => {
    const inputRange = routes.map((_, i) => i);

    // every index contains widths at all previous indices
    const outputRange = routes.reduce<number[]>((acc, _, i) => {
        if (i === 0) return [0];
        return [...acc, acc[i - 1] + getTabWidth(i - 1) + (gap ?? 0)];
    }, []);

    const translateX = position.interpolate({
        inputRange,
        outputRange,
        extrapolate: 'clamp',
    });

    return Animated.multiply(translateX, I18nManager.isRTL ? -1 : 1);
};

export default function CustomIndicator<T extends Route>({
    getTabWidth,
    layout,
    navigationState,
    position,
    width,
    gap,
    style,
    animatedValue,
}: Props<T>) {
    const { themeColors } = useThemeColors();
    const Dimens = useDimens();
    const styles = stylesF(Dimens);

    const [indicatorBackground, setIndicatorBackground] = React.useState<string>(themeColors.color_primary);

    useEffectOnce(() => {
        const listenerID = animatedValue.addListener((value) => {
            if (value.value >= 100) {
                setIndicatorBackground(Colors.COLOR_WHITE);
            } else {
                setIndicatorBackground(themeColors.color_primary);
            }
        });

        return () => {
            animatedValue.removeListener(listenerID);
        };
    });

    const { routes } = navigationState;

    const transform = [];

    if (layout.width) {
        const translateX = routes.length > 1 ? getTranslateX(position, routes, getTabWidth, gap) : 0;

        transform.push({ translateX });
    }

    const inputRange = routes.map((_, i) => i);
    const outputRange = inputRange.map((j) => getTabWidth(j) - Dimens.W_14);

    transform.push(
            {
                scaleX:
                        routes.length > 1
                            ? position.interpolate({
                                inputRange,
                                outputRange,
                                extrapolate: 'clamp',
                            })
                            : outputRange[0],
            },
            { translateX: 0.5 }
    );

    return (
        <Animated.View
            style={[
                styles.indicator,
                { width: width === 'auto' ? 1 : width },
                { left: 0 },
                { transform },
                style,
                { backgroundColor: indicatorBackground }
            ]}
        />
    );
}

const stylesF = (_Dimens: DimensType) => StyleSheet.create({
    indicator: {
        backgroundColor: '#ffeb3b',
        position: 'absolute',
        left: 0,
        bottom: 0,
        right: 0,
        height: 2,
    },
});
