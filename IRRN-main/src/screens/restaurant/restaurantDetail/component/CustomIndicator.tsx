import * as React from 'react';

import {
    Animated,
    Easing,
    I18nManager,
    Platform,
    StyleProp,
    StyleSheet,
    ViewStyle,
} from 'react-native';
import {
    NavigationState,
    Route,
    SceneRendererProps,
} from 'react-native-tab-view';

import useDimens, { DimensType } from '@src/hooks/useDimens';
import useThemeColors from '@src/themes/useThemeColors';

export type GetTabWidth = (_index: number) => number;

export type Props<T extends Route> = SceneRendererProps & {
    navigationState: NavigationState<T>;
    width: string | number;
    style?: StyleProp<ViewStyle>;
    getTabWidth: GetTabWidth;
    gap?: number;
    isIconLabel: boolean,
    isFullIconLabel: boolean
};

const useAnimatedValue = (initialValue: number) => {
    const lazyRef = React.useRef<Animated.Value>();

    if (lazyRef.current === undefined) {
        lazyRef.current = new Animated.Value(initialValue);
    }

    return lazyRef.current as Animated.Value;
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
    isIconLabel,
    isFullIconLabel
}: Props<T>) {
    const Dimens = useDimens();
    const styles = stylesF(Dimens);

    const TAB_MARGIN = Dimens.W_14;
    const ICON_FRIET_WIDTH = Dimens.W_14;

    const { themeColors } = useThemeColors();

    const isIndicatorShown = React.useRef(false);
    const isWidthDynamic = width === 'auto';

    const opacity = useAnimatedValue(isWidthDynamic ? 0 : 1);

    const indicatorVisible = isWidthDynamic
    ? layout.width &&
      navigationState.routes
              .slice(0, navigationState.index)
              .every((_, r) => getTabWidth(r))
    : true;

    React.useEffect(() => {
        const fadeInIndicator = () => {
            if (
                !isIndicatorShown.current &&
        isWidthDynamic &&
        // We should fade-in the indicator when we have widths for all the tab items
        indicatorVisible
            ) {
                isIndicatorShown.current = true;

                Animated.timing(opacity, {
                    toValue: 1,
                    duration: 150,
                    easing: Easing.in(Easing.linear),
                    useNativeDriver: true,
                }).start();
            }
        };

        fadeInIndicator();

        return () => opacity.stopAnimation();
    }, [indicatorVisible, isWidthDynamic, opacity]);

    const { routes } = navigationState;

    const transform = [];

    if (layout.width) {
        const translateX = routes.length > 1 ? getTranslateX(position, routes, getTabWidth, gap) : 0;

        transform.push({ translateX });
    }

    const leftSpace = React.useMemo(() => isFullIconLabel ? ICON_FRIET_WIDTH * 2 + Dimens.W_5 : isIconLabel ?  ICON_FRIET_WIDTH + Dimens.W_3 : 0, [Dimens.W_3, Dimens.W_5, ICON_FRIET_WIDTH, isFullIconLabel, isIconLabel]);
    if (width === 'auto') {
        const inputRange = routes.map((_, i) => i);
        const outputRange = inputRange.map((j) => getTabWidth(j) - TAB_MARGIN - leftSpace);

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
    }

    return (
        <Animated.View
            style={[
                styles.indicator,
                { width: width === 'auto' ? 1 : width },
                layout.width && Platform.OS !== 'macos'
                ? { left: leftSpace }
                : { left: `${(100 / routes.length) * navigationState.index}%` },
                { transform },
                width === 'auto' ? { opacity: opacity } : null,
                style,
                { backgroundColor: isIconLabel ? themeColors.color_fix_tab_selected : themeColors.color_primary }
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
