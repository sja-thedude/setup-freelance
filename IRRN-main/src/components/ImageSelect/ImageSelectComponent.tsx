import React, {
    memo,
    useCallback,
} from 'react';

import {
    InteractionManager,
    View,
} from 'react-native';

import useBoolean from '@src/hooks/useBoolean';
import { isEmptyOrUndefined } from '@src/utils';
import {
    AVATAR_UPLOAD_SIZE,
    IMAGE_SOURCE,
    openAndCropImage,
} from '@src/utils/cropImageUtil';
import { logError } from '@src/utils/logger';

import ImageComponent, { ImageComponentProps } from '../ImageComponent';
import TouchableComponent from '../TouchableComponent';
import SelectPhotoOptionDialog from './SelectPhotoOptionDialog';

export const SELECT_OPTIONS = {
    FROM_GALLERY: 1,
    FROM_CAMERA: 2,
    DELETE_PHOTO: 3,
};

interface ImageSelectComponentProps extends ImageComponentProps {
    onSelectImage: any,
    onDeleteImage: any,
    containerStyle: any,
}

const ImageSelectComponent = ({ onSelectImage, onDeleteImage, containerStyle, ...rest } : ImageSelectComponentProps) => {

    const [isShowDialog, showDialog, hideDialog] = useBoolean(false);

    const handleSelectOption = useCallback(async (option: any) => {
        try {
            let result: any;

            if (option === SELECT_OPTIONS.FROM_CAMERA) {
                result = await openAndCropImage(IMAGE_SOURCE.CAMERA, AVATAR_UPLOAD_SIZE.WIDTH, AVATAR_UPLOAD_SIZE.HEIGHT);
            } else if (option === SELECT_OPTIONS.FROM_GALLERY) {
                result = await openAndCropImage(IMAGE_SOURCE.GALLERY, AVATAR_UPLOAD_SIZE.WIDTH, AVATAR_UPLOAD_SIZE.HEIGHT);
            } else {
                onDeleteImage();
            }

            hideDialog();

            if (result) {
                InteractionManager.runAfterInteractions(() => {
                    onSelectImage(result);
                });
            }
        } catch (error) {
            hideDialog();
            logError('error', error);
        }
    }, [hideDialog, onDeleteImage, onSelectImage]);

    return (
        <View style={containerStyle}>
            <TouchableComponent
                onPress={showDialog}
            >
                <ImageComponent
                    {...rest}
                />
            </TouchableComponent>

            <SelectPhotoOptionDialog
                hideModal={hideDialog}
                isShow={isShowDialog}
                showDeleteButton={!isEmptyOrUndefined(rest?.source?.uri)}
                onSelectOption={(option: any) => handleSelectOption(option)}
            />
        </View>
    );
};

export default memo(ImageSelectComponent);
