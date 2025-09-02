import {
    HeaderStyleInterpolators,
    StackNavigationOptions,
    TransitionSpecs,
} from '@react-navigation/stack';

export const SlideFromLeft: StackNavigationOptions = {
    gestureDirection: 'horizontal',
    transitionSpec: {
        open: TransitionSpecs.TransitionIOSSpec,
        close: TransitionSpecs.TransitionIOSSpec,
    },
    headerStyleInterpolator: HeaderStyleInterpolators.forFade,
    cardStyleInterpolator: ({ current, next, layouts } : {current: any, next?: any, layouts: any}) => ({
        cardStyle: {
            transform: [
                {
                    translateX: current.progress.interpolate({
                        inputRange: [0, 1],
                        outputRange: [-layouts.screen.width, -1],
                    }),
                },
                {
                    translateX: next
                        ? next.progress.interpolate({
                            inputRange: [0, 1],
                            outputRange: [1, layouts.screen.width / 3],
                        })
                        : 1,
                },
            ],
        },
    }),
};