import React, { memo } from 'react';

import ContentLoader, { Rect } from 'react-content-loader/native';
import { View } from 'react-native';

import useDimens from '@src/hooks/useDimens';
import useThemeColors from '@src/themes/useThemeColors';

const LoadingPlaceHolder = () => {
    const { themeColors } = useThemeColors();
    const Dimens = useDimens('screen');

    return (
        <View>
            <ContentLoader
                speed={1}
                height={Dimens.SCREEN_HEIGHT}
                width={Dimens.SCREEN_WIDTH * 2}
                backgroundColor={themeColors.color_loading_placeholder_background}
                foregroundColor={themeColors.color_loading_placeholder_foreground}
                fillOpacity={0.05}
            >
                <Rect
                    x={`${Dimens.W_18}`}
                    y="0"
                    rx="5"
                    ry="5"
                    width={`${Dimens.W_160}`}
                    height={`${Dimens.H_95}`}
                />
                <Rect
                    x={`${Dimens.W_18}`}
                    y={`${Dimens.H_95 + Dimens.H_8}`}
                    rx="2"
                    ry="2"
                    width={`${Dimens.W_71}`}
                    height={`${Dimens.H_8}`}
                />
                <Rect
                    x={`${Dimens.W_18 + Dimens.W_71 + Dimens.W_60 }`}
                    y={`${Dimens.H_95 + Dimens.H_8}`}
                    rx="2"
                    ry="2"
                    width={`${Dimens.W_28}`}
                    height={`${Dimens.H_8}`}
                />

                <Rect
                    x={`${Dimens.W_18}`}
                    y={`${Dimens.H_95 + Dimens.H_8 + Dimens.H_8 + Dimens.H_8}`}
                    rx="2"
                    ry="2"
                    width={`${Dimens.W_160}`}
                    height={`${Dimens.H_8}`}
                />

                {/* --- */}

                <Rect
                    x={`${Dimens.W_38 + Dimens.W_160}`}
                    y="0"
                    rx="5"
                    ry="5"
                    width={`${Dimens.W_160}`}
                    height={`${Dimens.H_95}`}
                />
                <Rect
                    x={`${Dimens.W_38 + Dimens.W_160}`}
                    y={`${Dimens.H_95 + Dimens.H_8}`}
                    rx="2"
                    ry="2"
                    width={`${Dimens.W_71}`}
                    height={`${Dimens.H_8}`}
                />
                <Rect
                    x={`${Dimens.W_38 + Dimens.W_160 + Dimens.W_71 + Dimens.W_57 }`}
                    y={`${Dimens.H_95 + Dimens.H_8}`}
                    rx="2"
                    ry="2"
                    width={`${Dimens.W_28}`}
                    height={`${Dimens.H_8}`}
                />

                <Rect
                    x={`${Dimens.W_38 + Dimens.W_160}`}
                    y={`${Dimens.H_95 + Dimens.H_8 + Dimens.H_8 + Dimens.H_8}`}
                    rx="2"
                    ry="2"
                    width={`${Dimens.W_160}`}
                    height={`${Dimens.H_8}`}
                />

                {/* --- */}

                <Rect
                    x={`${Dimens.W_18}`}
                    y={`${Dimens.H_95 + Dimens.H_40 + Dimens.H_16}`}
                    rx="5"
                    ry="5"
                    width={`${Dimens.W_160}`}
                    height={`${Dimens.H_95}`}
                />
                <Rect
                    x={`${Dimens.W_18}`}
                    y={`${Dimens.H_95 + Dimens.H_40 + Dimens.H_16 + Dimens.H_95 + Dimens.H_8}`}
                    rx="2"
                    ry="2"
                    width={`${Dimens.W_71}`}
                    height={`${Dimens.H_8}`}
                />
                <Rect
                    x={`${Dimens.W_18 + Dimens.W_71 + Dimens.W_60 }`}
                    y={`${Dimens.H_95 + Dimens.H_40 + Dimens.H_16 + Dimens.H_95 + Dimens.H_8}`}
                    rx="2"
                    ry="2"
                    width={`${Dimens.W_28}`}
                    height={`${Dimens.H_8}`}
                />
                <Rect
                    x={`${Dimens.W_18}`}
                    y={`${Dimens.H_95 + Dimens.H_40 + Dimens.H_16 + Dimens.H_95 + Dimens.H_8 + Dimens.H_8 + Dimens.H_8}`}
                    rx="2"
                    ry="2"
                    width={`${Dimens.W_160}`}
                    height={`${Dimens.H_8}`}
                />

                {/* --- */}

                <Rect
                    x={`${Dimens.W_38 + Dimens.W_160}`}
                    y={`${Dimens.H_95 + Dimens.H_40 + Dimens.H_16}`}
                    rx="5"
                    ry="5"
                    width={`${Dimens.W_160}`}
                    height={`${Dimens.H_95}`}
                />
                <Rect
                    x={`${Dimens.W_38 + Dimens.W_160}`}
                    y={`${Dimens.H_95 + Dimens.H_40 + Dimens.H_16 + Dimens.H_95 + Dimens.H_8}`}
                    rx="2"
                    ry="2"
                    width={`${Dimens.W_71}`}
                    height={`${Dimens.H_8}`}
                />
                <Rect
                    x={`${Dimens.W_38 + Dimens.W_160 + Dimens.W_71 + Dimens.W_57}`}
                    y={`${Dimens.H_95 + Dimens.H_40 + Dimens.H_16 + Dimens.H_95 + Dimens.H_8}`}
                    rx="2"
                    ry="2"
                    width={`${Dimens.W_28}`}
                    height={`${Dimens.H_8}`}
                />
                <Rect
                    x={`${Dimens.W_38 + Dimens.W_160}`}
                    y={`${Dimens.H_95 + Dimens.H_40 + Dimens.H_16 + Dimens.H_95 + Dimens.H_8 + Dimens.H_8 + Dimens.H_8}`}
                    rx="2"
                    ry="2"
                    width={`${Dimens.W_160}`}
                    height={`${Dimens.H_8}`}
                />

                {/* --- */}

                <Rect
                    x={`${Dimens.W_18}`}
                    y={`${Dimens.H_95 + Dimens.H_40 + Dimens.H_16 + Dimens.H_95 + Dimens.H_42 + Dimens.H_16}`}
                    rx="5"
                    ry="5"
                    width={`${Dimens.W_160}`}
                    height={`${Dimens.H_95}`}
                />
                <Rect
                    x={`${Dimens.W_18}`}
                    y={`${Dimens.H_95 + Dimens.H_40 + Dimens.H_16 + Dimens.H_95 + Dimens.H_42 + Dimens.H_16 + Dimens.H_8 + Dimens.H_95}`}
                    rx="2"
                    ry="2"
                    width={`${Dimens.W_71}`}
                    height={`${Dimens.H_8}`}
                />
                <Rect
                    x={`${Dimens.W_18 + Dimens.W_71 + Dimens.W_57}`}
                    y={`${Dimens.H_95 + Dimens.H_40 + Dimens.H_16 + Dimens.H_95 + Dimens.H_42 + Dimens.H_16 + Dimens.H_8 + Dimens.H_95}`}
                    rx="2"
                    ry="2"
                    width={`${Dimens.W_28}`}
                    height={`${Dimens.H_8}`}
                />
                <Rect
                    x={`${Dimens.W_18}`}
                    y={`${Dimens.H_95 + Dimens.H_40 + Dimens.H_16 + Dimens.H_95 + Dimens.H_42 + Dimens.H_16 + Dimens.H_8 + Dimens.H_95 + Dimens.H_8 + Dimens.H_8}`}
                    rx="2"
                    ry="2"
                    width={`${Dimens.W_160}`}
                    height={`${Dimens.H_8}`}
                />

                {/* --- */}

                <Rect
                    x={`${Dimens.W_38 + Dimens.W_160}`}
                    y={`${Dimens.H_95 + Dimens.H_40 + Dimens.H_16 + Dimens.H_95 + Dimens.H_42 + Dimens.H_16}`}
                    rx="5"
                    ry="5"
                    width={`${Dimens.W_160}`}
                    height={`${Dimens.H_95}`}
                />
                <Rect
                    x={`${Dimens.W_38 + Dimens.W_160}`}
                    y={`${Dimens.H_95 + Dimens.H_40 + Dimens.H_16 + Dimens.H_95 + Dimens.H_42 + Dimens.H_16 + Dimens.H_8 + Dimens.H_95}`}
                    rx="2"
                    ry="2"
                    width={`${Dimens.W_71}`}
                    height={`${Dimens.H_8}`}
                />
                <Rect
                    x={`${Dimens.W_38 + Dimens.W_160 + Dimens.W_71 + Dimens.W_57}`}
                    y={`${Dimens.H_95 + Dimens.H_40 + Dimens.H_16 + Dimens.H_95 + Dimens.H_42 + Dimens.H_16 + Dimens.H_8 + Dimens.H_95}`}
                    rx="2"
                    ry="2"
                    width={`${Dimens.W_28}`}
                    height={`${Dimens.H_8}`}
                />
                <Rect
                    x={`${Dimens.W_38 + Dimens.W_160}`}
                    y={`${Dimens.H_95 + Dimens.H_40 + Dimens.H_16 + Dimens.H_95 + Dimens.H_42 + Dimens.H_16 + Dimens.H_8 + Dimens.H_95 + Dimens.H_8 + Dimens.H_8}`}
                    rx="2"
                    ry="2"
                    width={`${Dimens.W_160}`}
                    height={`${Dimens.H_8}`}
                />

                {/* --- */}

                <Rect
                    x={`${Dimens.W_18}`}
                    y={`${Dimens.H_95 + Dimens.H_40 + Dimens.H_16 + Dimens.H_95 + Dimens.H_42 + Dimens.H_16 + Dimens.H_8 + Dimens.H_95 + Dimens.H_8 + Dimens.H_42}`}
                    rx="5"
                    ry="5"
                    width={`${Dimens.W_160}`}
                    height={`${Dimens.H_95}`}
                />
                <Rect
                    x={`${Dimens.W_18}`}
                    y={`${Dimens.H_95 + Dimens.H_40 + Dimens.H_16 + Dimens.H_95 + Dimens.H_42 + Dimens.H_16 + Dimens.H_8 + Dimens.H_95 + Dimens.H_8 + Dimens.H_42 + Dimens.H_95 + Dimens.H_8}`}
                    rx="2"
                    ry="2"
                    width={`${Dimens.W_71}`}
                    height={`${Dimens.H_8}`}
                />

                {/* --- */}

                <Rect
                    x={`${Dimens.W_38 + Dimens.W_160}`}
                    y={`${Dimens.H_95 + Dimens.H_40 + Dimens.H_16 + Dimens.H_95 + Dimens.H_42 + Dimens.H_16 + Dimens.H_8 + Dimens.H_95 + Dimens.H_8 + Dimens.H_42}`}
                    rx="5"
                    ry="5"
                    width={`${Dimens.W_160}`}
                    height={`${Dimens.H_95}`}
                />
                <Rect
                    x={`${Dimens.W_38 + Dimens.W_160}`}
                    y={`${Dimens.H_95 + Dimens.H_40 + Dimens.H_16 + Dimens.H_95 + Dimens.H_42 + Dimens.H_16 + Dimens.H_8 + Dimens.H_95 + Dimens.H_8 + Dimens.H_42 + Dimens.H_95 + Dimens.H_8}`}
                    rx="2"
                    ry="2"
                    width={`${Dimens.W_71}`}
                    height={`${Dimens.H_8}`}
                />
            </ContentLoader>
        </View>
    );
};

export default memo(LoadingPlaceHolder);