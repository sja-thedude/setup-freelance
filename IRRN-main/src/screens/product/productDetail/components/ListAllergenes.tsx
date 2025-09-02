import React, {
    FC,
    Fragment,
    memo,
    useCallback,
} from 'react';

import {
    StyleSheet,
    View,
} from 'react-native';

import FlatListComponent from '@src/components/FlatListComponent';
import ImageComponent from '@src/components/ImageComponent';
import useDimens, { DimensType } from '@src/hooks/useDimens';
import { Product } from '@src/network/dataModels/ProductSectionModel';

interface IProps {
    item?: Product;
}

const ListAllergenes: FC<IProps> = ({ item }) => {
    const Dimens = useDimens();
    const styles = stylesF(Dimens);

    const renderItem = useCallback(
            ({ item }: any) => (
                <ImageComponent
                    style={styles.viewItem}
                    source={{ uri: item?.icon }}
                />
            ),
            [styles.viewItem],
    );

    return (
        <Fragment>
            {!!item?.allergenens && item?.allergenens?.length > 0 && (
                <View style={styles.container}>
                    <FlatListComponent
                        contentContainerStyle={styles.flastList}
                        renderItem={renderItem}
                        data={item?.allergenens}
                        horizontal
                    />
                </View>
            )}
        </Fragment>
    );
};

const stylesF = (Dimens: DimensType) => StyleSheet.create({
    container: { marginTop: Dimens.H_12 },
    flastList: { paddingHorizontal: Dimens.W_20 },
    viewItem: {
        width: Dimens.H_44,
        height: Dimens.H_44,
        alignItems: 'center',
    },
});

export default memo(ListAllergenes);
