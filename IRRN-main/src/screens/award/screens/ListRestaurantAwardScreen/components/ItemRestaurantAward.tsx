import React, {
    FC,
    memo,
    useCallback,
    useMemo,
} from 'react';

import {
    StyleSheet,
    TouchableOpacity,
    View,
} from 'react-native';

import ShadowView from '@src/components/ShadowView';
import TextComponent from '@src/components/TextComponent';
import { Colors } from '@src/configs';
import useDimens, { DimensType } from '@src/hooks/useDimens';
import { SCREENS } from '@src/navigation/config/screenName';
import NavigationService from '@src/navigation/NavigationService';
import { Loyalty } from '@src/network/dataModels/LoyaltyModal';
import useThemeColors from '@src/themes/useThemeColors';

interface IProps {
    item?: Loyalty;
}

const ItemRestaurantAward: FC<IProps> = ({ item }) => {
    const { themeColors } = useThemeColors();
    const Dimens = useDimens();
    const styles = stylesF(Dimens);

    const percent = useMemo(
            () =>
                (Number(item?.point || 0) / Number(item?.highest_point || 1)) * 100,
            [item?.highest_point, item?.point],
    );

    const goToDetail = useCallback(() => {
        NavigationService.navigate(SCREENS.DETAIL_LOYALTY_RESTAURANT_SCREEN, {
            id: item?.id,
        });
    }, [item]);

    return (
        <TouchableOpacity
            onPress={goToDetail}
            style={styles.mainContainer}
        >
            <ShadowView
                style={styles.shadowStyle}
            >
                <View
                    style={[
                        styles.container,
                        { backgroundColor: themeColors?.color_card_background },
                    ]}
                >
                    <TextComponent
                        style={StyleSheet.flatten([
                            styles.textName,
                            { color: themeColors?.color_text },
                        ])}
                    >
                        {item?.workspace?.title || item?.workspace?.name}
                    </TextComponent>

                    <View style={styles.viewValue}>
                        <View style={[styles.viewWapperValue, { backgroundColor: themeColors.color_primary }]}>
                            <TextComponent style={styles.textPoint}>
                                {item?.point || 0}/{item?.highest_point || 0}
                            </TextComponent>
                        </View>
                    </View>

                    <View
                        style={[
                            styles.viewProgress,
                            { backgroundColor: themeColors?.color_progress },
                        ]}
                    >
                        <View
                            style={[
                                styles.progress,
                                { backgroundColor: themeColors.color_primary },
                                {
                                    width: `${percent > 100 ? 100 : percent}%`,
                                },
                            ]}
                        />
                    </View>
                </View>
            </ShadowView>
        </TouchableOpacity>
    );
};

const stylesF = (Dimens: DimensType) => StyleSheet.create({
    shadowStyle: { width: '100%', shadowColor: 'rgba(0, 0, 0, 0.04)', shadowRadius: Dimens.H_15 },
    mainContainer: { marginBottom: Dimens.H_20, marginHorizontal: Dimens.W_8 },
    container: {
        paddingHorizontal: Dimens.W_16,
        paddingTop: Dimens.H_18,
        paddingBottom: Dimens.H_22,
        borderRadius: Dimens.RADIUS_6,
    },
    textName: { fontSize: Dimens.FONT_20, fontWeight: '700' },
    viewValue: {
        flexDirection: 'row',
        justifyContent: 'flex-end',
        marginTop: -Dimens.H_5,
    },
    viewWapperValue: {
        height: Dimens.H_22,
        flexDirection: 'row',
        alignItems: 'center',
        justifyContent: 'center',
        minWidth: Dimens.W_57,
        paddingHorizontal: Dimens.W_6,
        borderRadius: Dimens.RADIUS_22,
    },
    textPoint: {
        fontSize: Dimens.FONT_16,
        fontWeight: '500',
        color: Colors.COLOR_WHITE,
    },
    viewProgress: {
        height: Dimens.H_8,
        borderRadius: Dimens.RADIUS_22,
        marginTop: Dimens.H_6,
    },
    progress: {
        position: 'absolute',
        top: 0,
        bottom: 0,
        left: 0,
        borderRadius: Dimens.RADIUS_22,
    },
});

export default memo(ItemRestaurantAward);
