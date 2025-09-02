import React, {
    FC,
    memo,
    useEffect,
} from 'react';

import Animated, {
    Easing,
    interpolate,
    useAnimatedStyle,
    useSharedValue,
    withRepeat,
    withTiming,
} from 'react-native-reanimated';

import { LoadingIcon } from '@src/assets/svg';
import useDimens from '@src/hooks/useDimens';
import useThemeColors from '@src/themes/useThemeColors';

interface IProps {
}

const LoadingIndicatorComponent: FC<IProps> = () => {
    const { themeColors } = useThemeColors();
    const Dimens = useDimens();

    const rotate = useSharedValue(0);

    const ringStyle = useAnimatedStyle(() => ({
        transform: [
            {
                rotateZ: `${interpolate(rotate.value, [0, 1], [0, 360])}deg`,
            },
        ],
        width: Dimens.H_38,
        height: Dimens.H_38,
        borderRadius: 999,
    }));

    useEffect(() => {
        rotate.value = withRepeat(
                withTiming(1, {
                    duration: 1000,
                    easing: Easing.linear,
                }),
                -1,
                false
        );
    }, [rotate]);

    return (
        <Animated.View
            style={[ringStyle]}
        >
            <LoadingIcon
                width={Dimens.H_38}
                height={Dimens.H_38}
                fill={themeColors.color_primary}
            />
        </Animated.View>
    );
};

export default memo(LoadingIndicatorComponent);