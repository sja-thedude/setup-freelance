import React, { useMemo } from 'react';

import {
    StyleSheet,
    View,
} from 'react-native';

import { Images } from '@src/assets/images';
import {
    AppTextBlackIcon,
    AppTextWhiteIcon,
} from '@src/assets/svg';
import ImageComponent from '@src/components/ImageComponent';
import LoadingIndicatorComponent
    from '@src/components/LoadingIndicatorComponent';
import { useAppSelector } from '@src/hooks';
import useDimens, { DimensType } from '@src/hooks/useDimens';
import useThemeColors from '@src/themes/useThemeColors';
import {
    isGroupApp,
    isTemplateApp,
    isTemplateOrGroupApp,
} from '@src/utils';

const SplashScreen = () => {
    const Dimens = useDimens();
    const styles = stylesF(Dimens);

    const { themeColors, isDarkMode } = useThemeColors();
    const workspaceDetail = useAppSelector((state) => state.storageReducer.templateWorkspaceDetail);
    const groupAppDetail = useAppSelector((state) => state.storageReducer.groupAppDetail);

    const renderLogo = useMemo(() => {
        if (isGroupApp()) {
            return (
                groupAppDetail ? (
                    <ImageComponent
                        resizeMode='cover'
                        defaultImage={Images.image_placeholder}
                        source={{ uri: groupAppDetail.group_restaurant_avatar }}
                        style={styles.logo}
                    />
                ) : (
                    <LoadingIndicatorComponent/>
                )
            );
        }

        if (isTemplateApp()) {
            return (
                workspaceDetail ? (
                    <ImageComponent
                        resizeMode='cover'
                        defaultImage={Images.image_placeholder}
                        source={{ uri: workspaceDetail.photo }}
                        style={styles.logo}
                    />
                ) : (
                    <LoadingIndicatorComponent/>
                )
            );
        }

        return (
            isDarkMode ? (
                <AppTextWhiteIcon
                    width={Dimens.H_127}
                    height={Dimens.H_88}
                />
            ) : (
                <AppTextBlackIcon
                    width={Dimens.H_127}
                    height={Dimens.H_88}
                />
            )
        );
    }, [Dimens.H_127, Dimens.H_88, groupAppDetail, isDarkMode, styles.logo, workspaceDetail]);

    return (
        <View style={[styles.splashContainer, { backgroundColor: isTemplateOrGroupApp() ? 'white' : themeColors.color_app_background, }]}>
            {renderLogo}
        </View>
    );
};

export default SplashScreen;

const stylesF = (Dimens: DimensType) => StyleSheet.create({
    splashContainer: {
        flex: 1,
        alignItems: 'center',
        justifyContent: 'center',
    },
    logo: {
        width: Dimens.SCREEN_WIDTH / 2.5,
        height: Dimens.SCREEN_WIDTH / 2.5,
        borderRadius: Dimens.SCREEN_WIDTH
    },
});
