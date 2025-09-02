import React, {
    FC,
    memo,
} from 'react';

import {
    StyleSheet,
    TouchableOpacity,
    useWindowDimensions,
    View,
} from 'react-native';

import { InfoIcon } from '@src/assets/svg';
import TextComponent from '@src/components/TextComponent';
import { Colors } from '@src/configs';
import useDimens, { DimensType } from '@src/hooks/useDimens';
import { CouponRestaurant } from '@src/network/dataModels/CouponModal';

interface IProps {
    item?: CouponRestaurant;
    onClickItem: any;
}

const ItemCoupon: FC<IProps> = ({ item, onClickItem }) => {
    const Dimens = useDimens();
    const styles = stylesF(Dimens);

    const { width } = useWindowDimensions();

    return (
        <TouchableOpacity
            onPress={onClickItem}
            style={[styles.container, { width }]}
        >
            <View style={styles.viewFlexRow}>
                <InfoIcon
                    width={Dimens.H_20}
                    height={Dimens.H_20}
                />
                <TextComponent
                    numberOfLines={2}
                    style={styles.textPromoName}
                >
                    {item?.promo_name}
                </TextComponent>

                <TextComponent style={styles.textCode}>{item?.code}</TextComponent>
            </View>
        </TouchableOpacity>
    );
};

const stylesF = (Dimens: DimensType) => StyleSheet.create({
    container: {
        height: Dimens.H_44,
        flexDirection: 'row',
        alignItems: 'center',
        paddingHorizontal: Dimens.W_10,
    },
    viewFlexRow: {
        flexDirection: 'row',
        alignItems: 'center',
        flex: 1,
    },
    textPromoName: {
        marginLeft: Dimens.W_7,
        fontSize: Dimens.FONT_14,
        fontWeight: '400',
        color: Colors.COLOR_WHITE,
        flex: 1,
    },
    textCode: {
        color: Colors.COLOR_WHITE,
        textTransform: 'uppercase',
        fontSize: Dimens.FONT_16,
        fontWeight: '700',
        marginLeft: Dimens.W_10,
    },
});

export default memo(ItemCoupon);
