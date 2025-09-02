import React from 'react';

import { StyleSheet } from 'react-native';

import { BackIcon } from '@src/assets/svg';
import useDimens, { DimensType } from '@src/hooks/useDimens';
import NavigationService from '@src/navigation/NavigationService';

import TouchableComponent from '../TouchableComponent';

const BackButton = (props: any) => {

    const Dimens = useDimens();
    const styles = stylesF(Dimens);

    return (
        <TouchableComponent
            style={[styles.icon, props.style]}
            onPress={props.onPress || NavigationService.goBack}
            hitSlop={Dimens.DEFAULT_HIT_SLOP}
        >
            <BackIcon
                width={Dimens.W_16}
                height={Dimens.W_16}
            />
        </TouchableComponent>
    );
};

export default BackButton;

const stylesF = (Dimens: DimensType) => StyleSheet.create({
    icon: {
        marginRight: Dimens.W_14
    }
});