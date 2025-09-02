import React, { memo } from 'react';

import {
    StyleSheet,
    View,
} from 'react-native';

import { WalkThroughSteps3Icon } from '@src/assets/svg';
import useDimens, { DimensType } from '@src/hooks/useDimens';

const ThirstStep = () => {
    const Dimens = useDimens();
    const styles = stylesF(Dimens);

    return (
        <View style={styles.container} >
            <WalkThroughSteps3Icon
                width={Dimens.H_199}
                height={Dimens.H_206}
            />
        </View>
    );
};

export default memo(ThirstStep);

const stylesF = (_Dimens: DimensType) => StyleSheet.create({
    container: {
        position: 'absolute',
        alignItems: 'center',
        left: 0,
        right: 0,
        bottom: 35,
    },
});
