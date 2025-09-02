import React, {
    FC,
    memo,
    useCallback,
    useEffect,
    useMemo,
} from 'react';

import {
    StyleSheet,
    TouchableOpacity,
    View,
} from 'react-native';
import FastImage from 'react-native-fast-image';
import LinearGradient from 'react-native-linear-gradient';

import { useLayout } from '@react-native-community/hooks';
import TextComponent from '@src/components/TextComponent';
import { Colors } from '@src/configs';
import useDimens, { DimensType } from '@src/hooks/useDimens';
import {
    Loyalty,
    Reward,
} from '@src/network/dataModels/LoyaltyModal';
import useThemeColors from '@src/themes/useThemeColors';

interface IProps {
    item?: Loyalty;
    reward?: Reward;
    setReward?: React.Dispatch<React.SetStateAction<Reward | undefined>>;
    setRewardWidth: React.Dispatch<React.SetStateAction<number | undefined>>;
}

const ViewReward: FC<IProps> = ({ item, reward, setReward, setRewardWidth }) => {
    const { themeColors } = useThemeColors();
    const Dimens = useDimens();
    const styles = stylesF(Dimens);

    const { onLayout, height } = useLayout();
    const { onLayout: onContainerLayout, width: containerWidth } = useLayout();
    const percent = useMemo(
            () =>
                (Number(item?.point || 0) / Number(item?.highest_point || 1)) * 100,
            [item?.highest_point, item?.point],
    );

    const handleSelectReward = useCallback(
            (value: Reward) => () => {
                !!setReward && setReward(value);
            },
            [setReward],
    );

    useEffect(() => {
        if (containerWidth) {
            setRewardWidth(containerWidth);
        }
    }, [containerWidth, setRewardWidth]);

    return (
        <View
            onLayout={onContainerLayout}
            style={styles.container}
        >
            <View style={styles.viewPoint}>
                <TextComponent
                    style={StyleSheet.flatten([
                        styles.textPoint,
                        { color: themeColors?.color_text },
                    ])}
                >
                    {item?.point || 0}/{item?.highest_point}
                </TextComponent>

                <View
                    onLayout={onLayout}
                    style={[
                        styles.viewProgress,
                        { backgroundColor: themeColors?.color_card_background },
                    ]}
                >
                    {item?.rewards?.map((i, idx) => (
                        <TouchableOpacity
                            onPress={handleSelectReward(i)}
                            style={[
                                styles.viewWapperImage,
                                {
                                    zIndex: i?.id === reward?.id ? 99 : 1,
                                    bottom:
                                        Number(i?.score || 0) *
                                            (height / (item?.highest_point || 1)) -
                                            Dimens.H_34 / 2,
                                },
                            ]}
                            key={idx}
                        >
                            <FastImage
                                tintColor={
                                    i?.id === reward?.id
                                        ? themeColors.color_primary
                                        : i?.score <= item?.point
                                        ? '#3C3C3C'
                                        : '#CCCCCC'
                                }
                                style={styles.viewImage}
                                source={require('@assets/images/loyalty.png')}
                            >
                                <TextComponent style={styles.textImage}>{idx + 1}</TextComponent>
                            </FastImage>
                        </TouchableOpacity>
                    ))}

                    <View
                        style={[
                            styles.progress,
                            { backgroundColor: themeColors.color_primary },
                            { height: `${percent > 100 ? 100 : (percent || 1)}%` },
                        ]}
                    >
                        <LinearGradient
                            colors={['#00000000', '#00000010', '#00000020', '#00000040', '#00000060']}
                            style={styles.gradient}
                        />
                    </View>
                </View>
            </View>
        </View>
    );
};

const stylesF = (Dimens: DimensType) => StyleSheet.create({
    container: {
        paddingLeft: Dimens.W_14,
        paddingTop: Dimens.H_8,
        paddingBottom: Dimens.H_14,
        paddingRight: Dimens.W_50,
    },
    viewPoint: { flex: 1 },
    textPoint: {
        fontSize: Dimens.FONT_16,
        fontWeight: '700',
        textAlign: 'center',
    },
    viewProgress: {
        width: Dimens.W_50,
        borderRadius: Dimens.RADIUS_6,
        flex: 1,
        marginTop: Dimens.H_6,
        shadowColor: '#000',
        shadowOffset: {
            width: 0,
            height: 1,
        },
        shadowOpacity: 0.22,
        shadowRadius: 2.22,

        elevation: 3,
    },
    progress: {
        position: 'absolute',
        bottom: 0,
        left: 0,
        right: 0,
        borderRadius: Dimens.RADIUS_6,
    },
    viewImage: {
        width: Dimens.H_34 * 1.2,
        height: Dimens.H_34,
        alignItems: 'center',
        justifyContent: 'center',
        paddingLeft: Dimens.W_5,
    },
    viewWapperImage: {
        position: 'absolute',
        left: Dimens.W_14 / 2 + Dimens.W_50,
    },
    textImage: {
        alignSelf: 'center',
        textAlign: 'center',
        fontSize: Dimens.FONT_16,
        fontWeight: '700',
        color: Colors.COLOR_WHITE,
    },
    gradient: {
        position: 'absolute',
        top: 0,
        bottom: 0,
        left: 0,
        right: 0,
        borderRadius: Dimens.RADIUS_6,
    },
});

export default memo(ViewReward);
