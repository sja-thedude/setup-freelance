import {
    useCallback,
    useEffect,
    useMemo,
} from 'react';

import { Keyboard } from 'react-native';
import {
    Easing,
    useSharedValue,
    withTiming,
} from 'react-native-reanimated';

const useKeyboardShow = (defaultValue?: number) => {
    const animatedValue = useSharedValue(defaultValue || 0);

    const showAnimated = useCallback(
            (event: any) => {
                animatedValue.value = withTiming((defaultValue || 0) + (event?.endCoordinates?.height || 0), {
                    duration: 500,
                    easing: Easing.bezier(0, 0.1, 0.25, 1)
                });
            },
            [animatedValue, defaultValue],
    );

    const hideAnimated = useCallback(() => {
        animatedValue.value = withTiming(defaultValue || 0, {
            duration: 200,
            easing: Easing.bezier(0.25, 0.1, 0.25, 1)
        });
    }, [animatedValue, defaultValue]);

    useEffect(() => {
        const subscriptions = [
            Keyboard.addListener('keyboardWillShow', showAnimated),
            Keyboard.addListener('keyboardDidShow', showAnimated),
            Keyboard.addListener('keyboardWillHide', hideAnimated),
            Keyboard.addListener('keyboardDidHide', hideAnimated),
        ];

        return () => {
            subscriptions.forEach((subscription) => subscription.remove());
        };
    }, [hideAnimated, showAnimated]);

    return useMemo(() => ({ animatedValue }), [animatedValue]);
};

export default useKeyboardShow;
