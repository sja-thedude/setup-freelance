import ImagePicker from 'react-native-image-crop-picker';

import { Colors } from '@src/configs';
import I18nApp from '@src/languages';

// import ImageResizer from 'react-native-image-resizer';

export const IMAGE_SOURCE = {
    CAMERA: 0,
    GALLERY: 1,
};

export const AVATAR_UPLOAD_SIZE = {
    WIDTH: 600,
    HEIGHT: 600,
};

const openAndCropImageFromGallery = async (width, height) => await ImagePicker.openPicker({
    mediaType: 'photo',
    useFrontCamera: true,
    multiple: false,
    maxFiles: 1,
    cropping: true,
    showCropFrame: true,
    freeStyleCropEnabled: true,
    showCropGuidelines: true,
    compressImageQuality: 1,
    cropperCancelText: I18nApp.t('text_close'),
    cropperChooseText: I18nApp.t('choose'),
    width: width,
    height: height,
    cropperActiveWidgetColor: Colors.COLOR_PRIMARY,
    cropperStatusBarColor: Colors.COLOR_BLACK,
    cropperToolbarColor: Colors.COLOR_BLACK,
    cropperToolbarWidgetColor: Colors.COLOR_WHITE,
    cropperCircleOverlay: true,
});

const openAndCropImageFromCamera = async (width, height) => await ImagePicker.openCamera({
    mediaType: 'photo',
    useFrontCamera: true,
    multiple: false,
    cropping: true,
    showCropFrame: true,
    freeStyleCropEnabled: true,
    showCropGuidelines: true,
    compressImageQuality: 1,
    cropperCancelText: I18nApp.t('text_close'),
    cropperChooseText: I18nApp.t('choose'),
    width: width,
    height: height,
    cropperActiveWidgetColor: Colors.COLOR_PRIMARY,
    cropperStatusBarColor: Colors.COLOR_BLACK,
    cropperToolbarColor: Colors.COLOR_BLACK,
    cropperToolbarWidgetColor: Colors.COLOR_WHITE,
    cropperCircleOverlay: true,
});

// const resizeImage = async (imageObj, width, height) => await ImageResizer.createResizedImage(
//         imageObj.path,
//         width,
//         height,
//         'JPEG',
//         100,
//         0)
//         .then((res) => ({ ...res, type: imageObj.mime }))
//         .catch(() => ({
//             ...imageObj,
//             uri: imageObj?.path,
//             name: imageObj?.filename || 'image.jpg',
//             type: imageObj.mine,
//         }));

export const openAndCropImage = async (imageFrom, width, height) => {
    let response;

    if (imageFrom === IMAGE_SOURCE.CAMERA) {
        response = await openAndCropImageFromCamera(width, height);
    } else {
        response = await openAndCropImageFromGallery(width, height);
    }

    return response;
};