import React, { memo, useEffect } from 'react';

import {
    InteractionManager,
    PermissionsAndroid,
    StyleSheet,
    View,
} from 'react-native';

import FireBaseMessaging from '@react-native-firebase/messaging';
import { WalkThroughSteps1Icon } from '@src/assets/svg';
import { IS_ANDROID, } from '@src/configs/constants';
import useDimens, { DimensType } from '@src/hooks/useDimens';
import { logError } from '@src/utils/logger';

const FirstStep = () => {
    const Dimens = useDimens();
    const styles = stylesF(Dimens);

    useEffect(() => {
        const onEffect = async () => {
            try {
                if (IS_ANDROID) {
                    PermissionsAndroid.request(PermissionsAndroid.PERMISSIONS.POST_NOTIFICATIONS); // if react-native >= 0.70.7 => PermissionsAndroid.PERMISSIONS.POST_NOTIFICATIONS
                } else {
                    FireBaseMessaging().requestPermission({
                        provisional: false,
                    });
                }

            } catch (error) {
                logError('Request Permission Error', error);
            }
        };

        InteractionManager.runAfterInteractions(() => {
            onEffect();
        });
    }, []);

    return (
        <View style={styles.container} >
            <WalkThroughSteps1Icon
                width={Dimens.H_199}
                height={Dimens.H_206}
            />
        </View>
    );
};

export default memo(FirstStep);

const stylesF = (_Dimens: DimensType) => StyleSheet.create({
    container: {
        position: 'absolute',
        alignItems: 'center',
        left: 0,
        right: 0,
        bottom: 35,
    },
});