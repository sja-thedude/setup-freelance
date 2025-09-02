import React, {
    FC,
    memo,
    useCallback,
    useMemo,
} from 'react';

import {
    NativeScrollEvent,
    NativeSyntheticEvent,
    StyleSheet,
} from 'react-native';

import FlatListComponent from '@src/components/FlatListComponent';
import ImageComponent from '@src/components/ImageComponent';
import { useAppSelector } from '@src/hooks';
import useDimens, { DimensType } from '@src/hooks/useDimens';
import { ApiGallery } from '@src/network/dataModels/RestaurantDetailModel';
import { Images } from '@src/assets/images';

interface IProps {
    setCurrentImageIndex: Function
}

const ImageSlideList: FC<IProps> = ({ setCurrentImageIndex }) => {
    const Dimens = useDimens();
    const styles = stylesF(Dimens);

    const api_gallery = useAppSelector((state) => state.storageReducer.templateWorkspaceDetail?.api_gallery);

    const listImage = useMemo(() => {
        const images = api_gallery?.filter((i) => i.active);
        return images?.length ? images : [{}];
    }, [api_gallery]);

    const handleOnScroll = useCallback((event: NativeSyntheticEvent<NativeScrollEvent>) => {
        const currentIndex = Number((event.nativeEvent.contentOffset.x / Dimens.SCREEN_WIDTH).toFixed(0));
        setCurrentImageIndex(currentIndex);
    }, [Dimens.SCREEN_WIDTH, setCurrentImageIndex]);

    const renderItem = useCallback(({ item } : {item: ApiGallery}) => (
        <ImageComponent
            resizeMode='cover'
            defaultImage={Images.image_placeholder}
            source={{ uri: item.full_path }}
            style={styles.image}
            hiddenLoading
        />
    ), [styles.image]);

    return (
        <FlatListComponent
            bounces={false}
            horizontal
            pagingEnabled
            data={listImage}
            renderItem={renderItem}
            showsVerticalScrollIndicator={false}
            snapToAlignment={'center'}
            decelerationRate={'fast'}
            onScroll={handleOnScroll}
        />
    );
};

export default memo(ImageSlideList);

const stylesF = (Dimens: DimensType) => StyleSheet.create({
    image: { width: Dimens.SCREEN_WIDTH, height: Dimens.SCREEN_HEIGHT / 1.7 },
});