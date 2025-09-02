import React, {
    FC,
    memo,
    ReactNode,
} from 'react';

import { StyleSheet } from 'react-native';
import Animated, {
    FadeIn,
    FadeOut,
} from 'react-native-reanimated';

import { useAppSelector } from '@src/hooks';
import useDimens, { DimensType } from '@src/hooks/useDimens';

import LoadingIndicatorComponent from './LoadingIndicatorComponent';
import TouchableComponent from './TouchableComponent';

interface IProps {
    cancelable?: boolean;
    overlayColor?: string;
    visible?: boolean;
    customIndicator?: ReactNode;
}

const LoadingOverlay: FC<IProps> = () => {
    const showGlobalLoading = useAppSelector((state) => state.loadingReducer.showGlobalLoading);
    const Dimens = useDimens();
    const styles = stylesF(Dimens);

    return showGlobalLoading ? (
        <Animated.View
            entering={FadeIn}
            exiting={FadeOut}
            style={styles.mainContainer}
        >
            <TouchableComponent
                activeOpacity={1}
                style={styles.subContainer}
            >
                <Animated.View
                    entering={FadeIn}
                    exiting={FadeOut}
                    style={[styles.background, { backgroundColor: 'transparent' }]}
                >
                    <LoadingIndicatorComponent/>
                </Animated.View>
            </TouchableComponent>
        </Animated.View>
    ) : null;
};

const stylesF = (Dimens: DimensType) => StyleSheet.create({
    subContainer: { flex: 1, alignItems: 'center', justifyContent: 'center' },
    mainContainer: {
        position: 'absolute',
        top: 0,
        bottom: 0,
        left: 0,
        right: 0,
    },
    background: {
        justifyContent: 'center',
        alignItems: 'center',
        padding: Dimens.H_10,
        borderRadius: 999,
    },
    styleModal: { margin: 0 },
});

export default memo(LoadingOverlay);
