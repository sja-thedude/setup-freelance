import React, {
    memo,
    useCallback,
    useMemo,
    useRef,
} from 'react';

import debounce from 'lodash/debounce';
import {
    Animated,
    StyleSheet,
    View,
} from 'react-native';
import FastImage, {
    FastImageProps,
    OnLoadEvent,
} from 'react-native-fast-image';

import { IS_ANDROID } from '@src/configs/constants';
import useDimens, { DimensType } from '@src/hooks/useDimens';
import { isEmptyOrUndefined } from '@utils/index';

export interface ImageComponentProps extends FastImageProps {
    source: any,
    transition?: any,
    defaultImage?: any,
    hiddenLoading?: boolean,
}

const ImageComponent = (props: ImageComponentProps) => {
    // const { themeColors } = useThemeColors();
    const Dimens = useDimens();
    const styles = stylesF(Dimens);

    const { source, defaultImage, style, onLoadEnd, hiddenLoading, ...attributes } = props;

    const hasImage = useMemo(() => !isEmptyOrUndefined(source?.uri), [source]);
    const placeholderOpacity = useRef(new Animated.Value(1)).current;

    const getImage = useCallback(() => {
        if (!isEmptyOrUndefined(source?.uri)) {
            return  source;
        }

        if (typeof source === 'number') {
            return  source;
        }

        return defaultImage;

    }, [defaultImage, source]);

    const handleOnLoad = useCallback(
            (e: OnLoadEvent) => {
                const { transition, onLoad } = props;

                if (!transition) {
                    placeholderOpacity.setValue(0);
                    return;
                }

                const minimumWait = 100;
                const staggerNonce = 200 * Math.random();

                debounce(
                        () => {
                            Animated.timing(placeholderOpacity, {
                                toValue: 0,
                                duration: 350,
                                useNativeDriver: IS_ANDROID ? false : true,
                            }).start();
                        },
                IS_ANDROID ? 0 : Math.floor(minimumWait + staggerNonce),
                )();

                onLoad && onLoad(e);
            },
            [placeholderOpacity, props],
    );

    const handleOnLoadEnd = useCallback(() => {
        Animated.timing(placeholderOpacity, {
            toValue: 0,
            duration: 350,
            useNativeDriver: IS_ANDROID ? false : true,
        }).start();

        !!onLoadEnd && onLoadEnd();
    }, [onLoadEnd, placeholderOpacity]);

    return (
        <View style={[styles.container, style]}>
            <FastImage
                {...attributes}
                onLoad={handleOnLoad}
                onLoadEnd={handleOnLoadEnd}
                style={[StyleSheet.absoluteFillObject]}
                source={getImage()}
            />

            {hasImage && !hiddenLoading && (
                <Animated.View
                    pointerEvents={hasImage ? 'none' : 'auto'}
                    accessibilityElementsHidden={hasImage}
                    importantForAccessibility={hasImage ? 'no-hide-descendants' : 'yes'}
                    style={[
                        styles.placeholderContainer,
                        styles.placeholder,
                        { opacity: hasImage ? placeholderOpacity : 1 },
                    ]}
                >
                    {/* <ActivityIndicator color={themeColors.color_loading_indicator}/> */}
                </Animated.View>
            )}
        </View>
    );
};

const stylesF = (Dimens: DimensType) => StyleSheet.create({
    container: {
        backgroundColor: 'transparent',
        position: 'relative',
        overflow: 'hidden',
        borderRadius: Dimens.RADIUS_3,
    },
    placeholderContainer: { ...StyleSheet.absoluteFillObject },
    placeholder: {
        backgroundColor: 'transparent',
        alignItems: 'center',
        justifyContent: 'center',
        zIndex: 100,
    },
});

ImageComponent.defaultProps = {
    transition: true,
};

export default memo(ImageComponent);
