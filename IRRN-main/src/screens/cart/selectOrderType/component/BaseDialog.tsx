import React, {
    FC,
    ReactNode,
    useState,
} from 'react';

import {
    StyleSheet,
    View,
} from 'react-native';
import {
    Gesture,
    GestureDetector,
} from 'react-native-gesture-handler';
import Animated, {
    runOnJS,
    SlideInDown,
    SlideOutDown,
    useAnimatedStyle,
    useSharedValue,
    withTiming,
} from 'react-native-reanimated';

import { useLayout } from '@react-native-community/hooks';
import useDimens, { DimensType } from '@src/hooks/useDimens';
import useKeyboardShow from '@src/hooks/useKeyboardShow';
import useThemeColors from '@src/themes/useThemeColors';

interface IProps {
    children: ReactNode,
    onSwipeHide?: any,
}

const BaseDialog: FC<IProps> = ({ children, onSwipeHide }) => {
    const { themeColors } = useThemeColors();
    const Dimens = useDimens();
    const styles = stylesF(Dimens);

    const { animatedValue } = useKeyboardShow();

    const [show, setShow] = useState(true);

    const { onLayout, height } = useLayout();

    const position = useSharedValue(0);

    const panGesture = Gesture.Pan()
            .onUpdate((e) => {
                if (e.translationY < 0) {
                    position.value = 0;
                } else {
                    position.value = e.translationY;
                }
            }).onEnd((e) => {
                if (e.translationY <= height / 2) {
                    position.value = withTiming(0, {
                        duration: 200,
                    });
                } else {
                    runOnJS(setShow)(false);
                    runOnJS(onSwipeHide)();
                }
            });

    const animatedStyle = useAnimatedStyle(() => ({
        transform: [{ translateY: position.value }],
        bottom: animatedValue.value
    }));

    return show ? (
        <GestureDetector
            gesture={panGesture}
        >
            <Animated.View
                entering={SlideInDown}
                exiting={SlideOutDown}
                onLayout={onLayout}
                style={[
                    styles.contentContainer,
                    animatedStyle,
                    {
                        backgroundColor: themeColors.color_dialog_background,
                    },
                ]}
            >
                <View style={[styles.viewHeader, { backgroundColor: themeColors.color_dialog_background }]}>
                    <View style={[styles.viewDash, { backgroundColor: themeColors.color_dash }]} />
                </View>
                {children}
            </Animated.View>
        </GestureDetector>
    ) : null;
};

export default BaseDialog;

const stylesF = (Dimens: DimensType) => StyleSheet.create({
    contentContainer: {
        paddingHorizontal: Dimens.W_24,
        paddingTop: Dimens.H_24,
        paddingBottom: Dimens.COMMON_BOTTOM_PADDING * 2,
        borderTopLeftRadius: Dimens.H_32,
        borderTopRightRadius: Dimens.H_32,
    },
    viewHeader: {
        alignItems: 'center',
        justifyContent: 'center',
        marginBottom: Dimens.H_16,
    },
    viewDash: {
        height: Dimens.H_4,
        width: Dimens.H_100,
        borderRadius: Dimens.RADIUS_4,
    },
});