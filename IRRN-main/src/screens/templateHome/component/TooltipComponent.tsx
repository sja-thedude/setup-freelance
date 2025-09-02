import React, { memo, useCallback } from 'react';

import {
    StyleSheet,
    View,
} from 'react-native';
import Svg, {
    Circle,
    Defs,
    Mask,
    Rect,
} from 'react-native-svg';
import { useDispatch } from 'react-redux';

import { CloseIcon } from '@src/assets/svg';
import TextComponent from '@src/components/TextComponent';
import TouchableComponent from '@src/components/TouchableComponent';
import { useAppSelector } from '@src/hooks';
import useDimens, { DimensType } from '@src/hooks/useDimens';
import { RestaurantActions, } from '@src/redux/toolkit/actions/restaurantActions';
import { StorageActions } from '@src/redux/toolkit/actions/storageActions';
import { isGroupApp } from '@src/utils';
import { useTranslation } from 'react-i18next';

const TooltipComponent = () => {
    const DimensScreen = useDimens('screen');
    const Dimens = useDimens();
    const styles = stylesF(Dimens);
    const { t } = useTranslation();

    const dispatch = useDispatch();
    const showTooltipTime = useAppSelector((state) => state.storageReducer.showTooltipTime);
    const showTooltip = useAppSelector((state) => state.restaurantReducer.showTooltip);

    const onCloseTooltip = useCallback(() => {
        dispatch(RestaurantActions.setShowTooltip(false));
        dispatch(StorageActions.setStorageTooltipTime(showTooltipTime + 1));
    }, [dispatch, showTooltipTime]);

    if (!isGroupApp() || showTooltipTime > 3 || !showTooltip) {
        return null;
    }

    return (
        <View
            style={styles.mainContainer}
        >
            <Svg
                height="100%"
                width="100%"
            >
                <Defs>
                    <Mask
                        id="mask"
                        x="0"
                        y="0"
                        height="100%"
                        width="100%"
                    >
                        <Rect
                            height="100%"
                            width="100%"
                            fill="#fff"
                        />
                        <Circle
                            r={`${DimensScreen.W_4}%`}
                            x={`${DimensScreen.W_20 + DimensScreen.W_20 + DimensScreen.W_16 + DimensScreen.W_22 / 2}`}
                            y={`${DimensScreen.COMMON_HEADER_PADDING - DimensScreen.H_8 + DimensScreen.W_22 / 2}`}
                            fill="black"
                        />
                    </Mask>
                </Defs>
                <Rect
                    height="100%"
                    width="100%"
                    fill="rgba(0, 0, 0, 0.6)"
                    mask="url(#mask)"
                    fill-opacity="0"
                />
            </Svg>

            <View style={styles.tooltipContainer}>
                <TextComponent style={styles.tooltipText}>
                    {t('tooltip_group_description')}
                </TextComponent>
                <View style={styles.arrow}/>
                <TouchableComponent
                    onPress={onCloseTooltip}
                    style={styles.closeIcon}
                >
                    <CloseIcon
                        width={Dimens.W_14}
                        height={Dimens.W_14}
                        stroke={'black'}
                    />
                </TouchableComponent>
            </View>
        </View>
    );
};

export default memo(TooltipComponent);

const stylesF = (Dimens: DimensType) => StyleSheet.create({
    closeIcon: {
        position: 'absolute',
        backgroundColor: 'white',
        alignItems: 'center',
        justifyContent: 'center',
        borderRadius: Dimens.W_20,
        padding: Dimens.W_5,
        right: -Dimens.W_12,
        top: -Dimens.W_10,
    },
    arrow: {
        position: 'absolute',
        left: -Dimens.H_3,
        top: -Dimens.H_6,
        width: 0,
        height: 0,
        borderTopColor: 'transparent',
        borderTopWidth: Dimens.H_8,
        borderRightWidth: Dimens.H_16,
        borderRightColor: 'white',
        borderBottomWidth: Dimens.H_8,
        borderBottomColor: 'transparent',
        transform: [{ rotate: '50deg' }],
    },
    tooltipText: { fontSize: Dimens.FONT_15, textAlign: 'center' },
    mainContainer: {
        position: 'absolute',
        top: 0,
        bottom: 0,
        left: 0,
        right: 0,
    },
    tooltipContainer: {
        position: 'absolute',
        top: Dimens.COMMON_HEADER_PADDING + Dimens.H_8 + Dimens.W_22,
        left: Dimens.W_74 + Dimens.W_22 / 2,
        backgroundColor: 'white',
        paddingVertical: Dimens.H_8,
        paddingHorizontal: Dimens.W_12,
        borderRadius: Dimens.RADIUS_10,
    },
});

