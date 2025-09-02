import React, { memo, useEffect } from 'react';

import {
    InteractionManager,
    StyleSheet,
    View,
} from 'react-native';
import { RESULTS } from 'react-native-permissions';

import { WalkThroughSteps2Icon } from '@src/assets/svg';
import useBoolean from '@src/hooks/useBoolean';
import useDimens, { DimensType } from '@src/hooks/useDimens';
import { checkLocationPermission } from '@src/utils/locationUtil';

import OpenSettingDialog from './OpenSettingDialog';

const SecondStep = ({ focus }: {focus: boolean}) => {
    const Dimens = useDimens();
    const styles = stylesF(Dimens);

    const [isShowModal, showModal, hideModal] = useBoolean(false);

    useEffect(() => {
        if (focus) {
            const onEffect = async () => {
                const resultPermission = await checkLocationPermission();
                if (resultPermission === RESULTS.BLOCKED) {
                    showModal();
                }
            };

            InteractionManager.runAfterInteractions(() => {
                onEffect();
            });
        }
    }, [focus, showModal]);

    return (
        <View style={styles.container} >
            <WalkThroughSteps2Icon
                width={Dimens.H_199}
                height={Dimens.H_206}
            />
            <OpenSettingDialog
                hideModal={hideModal}
                isShow={isShowModal}
            />
        </View>
    );
};

export default memo(SecondStep);

const stylesF = (_Dimens: DimensType) => StyleSheet.create({
    container: {
        position: 'absolute',
        alignItems: 'center',
        left: 0,
        right: 0,
        bottom: 35,
    },
});