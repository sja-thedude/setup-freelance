import React, { FC, memo } from 'react';

import {
    StyleSheet,
    View,
    ViewProps,
} from 'react-native';
import {
    ShadowedView,
    shadowStyle,
} from 'react-native-fast-shadow';

import { IS_ANDROID } from '@src/configs/constants';
import useDimens from '@src/hooks/useDimens';

interface ShadowProps extends ViewProps {
    disabledShadow?: boolean
}

const ShadowView: FC<ShadowProps> = ({ disabledShadow, style, ...props }) => {
    const Dimens = useDimens();
    const styleObj = StyleSheet.flatten(style);

    const shadowRadius = styleObj?.shadowRadius || Dimens.H_15;

    if (disabledShadow) {
        return (
            <View
                {...props}
            >
                {props.children}
            </View>
        );
    }

    return (
        <ShadowedView
            {...props}
            style={[
                shadowStyle({
                    color: 'rgba(0,0,0,0.05)',
                    radius: Dimens.H_15,
                    offset: [0, Dimens.H_10],
                    opacity: 1,
                }),
                styleObj,
                { shadowRadius: IS_ANDROID ? shadowRadius : shadowRadius / 2  },
            ]}
        >
            {props.children}
        </ShadowedView>
    );
};

export default memo(ShadowView);