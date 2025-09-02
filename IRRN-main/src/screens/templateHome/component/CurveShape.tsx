import React, { memo } from 'react';

import { StyleSheet, View } from 'react-native';
import FastImage from 'react-native-fast-image';

import useDimens, { DimensType } from '@src/hooks/useDimens';
import useThemeColors from '@src/themes/useThemeColors';
import { Images } from '@src/assets/images';

const CurveShape = () => {
    const { themeColors } = useThemeColors();

    const Dimens = useDimens();
    const styles = stylesF(Dimens);

    return (
        <View style={[styles.curveShapeContainer]}>
            <FastImage
                tintColor={themeColors.color_app_background}
                resizeMode="stretch"
                source={Images.footer_workspace}
                style={{ height: 100, width: Dimens.SCREEN_WIDTH }}
            />
        </View>
    );
};

export default memo(CurveShape);

const stylesF = (_Dimens: DimensType) =>
    StyleSheet.create({
        curveShapeContainer: {
            position: 'absolute',
            bottom: 0,
            right: 0,
            left: 0,
        },
    });
