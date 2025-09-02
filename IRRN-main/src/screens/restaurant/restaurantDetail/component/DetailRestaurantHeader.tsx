import React, {
    FC,
    memo,
    useMemo,
    useRef,
} from 'react';

import {
    Animated as RNAnimated,
    LayoutChangeEvent,
    StyleSheet,
    View,
} from 'react-native';
import LinearGradient from 'react-native-linear-gradient';

import { useLayout } from '@react-native-community/hooks';
import AnimatedTextComponent from '@src/components/AnimatedTextComponent';
import BackButton from '@src/components/header/BackButton';
import { Colors } from '@src/configs';
import { useAppSelector } from '@src/hooks';
import useDimens, { DimensType } from '@src/hooks/useDimens';
import useThemeColors from '@src/themes/useThemeColors';
import { isTemplateOrGroupApp } from '@src/utils';
import { getStatusBarHeight } from '@src/utils/iPhoneXHelper';

import RestaurantInfoIcon from './RestaurantInfoIcon';

interface IProps {
    animatedValue: RNAnimated.Value,
}

const AllRestaurantHeader: FC<IProps> = ({ animatedValue }) => {
    const Dimens = useDimens();
    const styles = stylesF(Dimens);
    const { themeColors } = useThemeColors();

    const textLayout = useLayout();

    const runOnLayout = useRef(0);

    const restaurantData = useAppSelector((state) => state.restaurantReducer.restaurantDetail.data);

    const headerHeight = useMemo(() => animatedValue.interpolate({
        inputRange: [0, 150],
        outputRange: [Dimens.SCREEN_WIDTH / 2.84, getStatusBarHeight() + Dimens.H_48],
        extrapolate: 'clamp',
    }), [Dimens.H_48, Dimens.SCREEN_WIDTH, animatedValue]);

    const headerOpacity = useMemo(() => animatedValue.interpolate({
        inputRange: [0, 100],
        outputRange: [1, 0],
        extrapolate: 'clamp',
    }), [animatedValue]);

    const titleFontSize = useMemo(() => animatedValue.interpolate({
        inputRange: [0, 100],
        outputRange: [Dimens.FONT_30, Dimens.FONT_20],
        extrapolate: 'clamp',
    }), [Dimens.FONT_20, Dimens.FONT_30, animatedValue]);

    const arrowBottom = useMemo(() => animatedValue.interpolate({
        inputRange: [0, 100],
        outputRange: [textLayout.height + Dimens.H_20, Dimens.H_16],
        extrapolate: 'clamp',
    }), [Dimens.H_16, Dimens.H_20, animatedValue, textLayout.height]);

    const titleBottom = useMemo(() => animatedValue.interpolate({
        inputRange: [0, 100],
        outputRange: [Dimens.H_10, Dimens.H_14],
        extrapolate: 'clamp',
    }), [Dimens.H_10, Dimens.H_14, animatedValue]);

    const titleLeft = useMemo(() => animatedValue.interpolate({
        inputRange: [0, 100],
        outputRange: [Dimens.W_14, isTemplateOrGroupApp() ? Dimens.W_14 : Dimens.W_40],
        extrapolate: 'clamp',
    }), [Dimens.W_14, Dimens.W_40, animatedValue]);

    return (
        <RNAnimated.View
            style={[styles.headerContainer, { height: headerHeight }]}
        >
            <View
                style={[styles.header, { backgroundColor: themeColors.color_primary }]}
            >
                <RNAnimated.View
                    style={{ height: headerHeight, }}
                >
                    {restaurantData?.gallery ? (
                        <RNAnimated.Image
                            source={{ uri: restaurantData?.gallery[0]?.full_path }}
                            style={[styles.image, { height: headerHeight, opacity: headerOpacity }]}
                        />
                    ) : null}

                    <RNAnimated.View
                        style={[styles.gradientContainer, { opacity: headerOpacity, }]}
                    >
                        <LinearGradient
                            colors={['#00000000', '#00000040', '#00000080']}
                            style={styles.gradient}
                        />
                    </RNAnimated.View>

                    <RNAnimated.View
                        style={styles.titleMainContainer}
                    >
                        {!isTemplateOrGroupApp() ? (
                            <RNAnimated.View style={[styles.backIconContainer, { bottom: arrowBottom }]}>
                                <BackButton />
                            </RNAnimated.View>
                        ) : null}

                        <RNAnimated.View style={[styles.titleWrapper, { left: titleLeft, bottom: titleBottom }]}>
                            <AnimatedTextComponent
                                numberOfLines={2}
                                style={{ ...styles.title, fontSize: titleFontSize }}
                                onLayout={(e: LayoutChangeEvent) => {
                                    runOnLayout.current = runOnLayout.current + 1;
                                    if (runOnLayout.current <= 2) {
                                        textLayout.onLayout(e);
                                    }
                                }}
                            >
                                {restaurantData?.setting_generals?.title}
                            </AnimatedTextComponent>
                            <RNAnimated.View style={{
                                opacity: headerOpacity,
                            }}
                            >
                                <RestaurantInfoIcon/>
                            </RNAnimated.View>
                        </RNAnimated.View>
                    </RNAnimated.View>
                </RNAnimated.View>
            </View>
        </RNAnimated.View>
    );
};

export default memo(AllRestaurantHeader);

const stylesF = (Dimens: DimensType) =>
    StyleSheet.create({
        headerContainer: {
            zIndex: 99,
        },
        title: {
            fontWeight: '700',
            color: Colors.COLOR_WHITE,
            flex: 1,
        },
        titleWrapper: {
            position: 'absolute',
            flexDirection: 'row',
            alignItems: 'center',
            justifyContent: 'space-between',
            flex: 1,
            right: Dimens.W_14,
        },
        backIconContainer: {
            position: 'absolute',
            left: Dimens.W_14,
        },
        titleMainContainer: {
            position: 'absolute',
            flexDirection: 'row',
            bottom: 0,
            left: 0,
            right: 0,
            top: getStatusBarHeight(),
        },
        gradient: {
            position: 'absolute',
            top: 0,
            bottom: 0,
            left: 0,
            right: 0,
            borderBottomLeftRadius: Dimens.HEADER_BORDER_RADIUS,
            borderBottomRightRadius: Dimens.HEADER_BORDER_RADIUS,
        },
        gradientContainer: {
            position: 'absolute',
            top: 0,
            bottom: 0,
            left: 0,
            right: 0,
            borderBottomLeftRadius: Dimens.HEADER_BORDER_RADIUS,
            borderBottomRightRadius: Dimens.HEADER_BORDER_RADIUS,
            overflow: 'hidden',
        },
        image: {
            width: Dimens.SCREEN_WIDTH,
            borderBottomLeftRadius: Dimens.HEADER_BORDER_RADIUS,
            borderBottomRightRadius: Dimens.HEADER_BORDER_RADIUS,
        },
        header: {
            paddingTop: 0,
            paddingBottom: 0,
            paddingHorizontal: 0,
            borderBottomLeftRadius: Dimens.HEADER_BORDER_RADIUS,
            borderBottomRightRadius: Dimens.HEADER_BORDER_RADIUS,
        },
    });
