import React, {
    useCallback,
    useMemo,
    useRef,
    useState,
} from 'react';

import {
    StyleSheet,
    View,
} from 'react-native';
import { TabView } from 'react-native-tab-view';

import { AppTextIcon } from '@src/assets/svg';
import ButtonComponent from '@src/components/ButtonComponent';
import ImageComponent from '@src/components/ImageComponent';
import ShadowView from '@src/components/ShadowView';
import TextComponent from '@src/components/TextComponent';
import { Colors } from '@src/configs';
import {
    useAppDispatch,
    useAppSelector,
} from '@src/hooks';
import useDimens, { DimensType } from '@src/hooks/useDimens';
import { StorageActions } from '@src/redux/toolkit/actions/storageActions';
import useThemeColors from '@src/themes/useThemeColors';
import {
    isGroupApp,
    isTemplateApp,
    isTemplateOrGroupApp,
} from '@src/utils';
import { getBottomSpace, getStatusBarHeight } from '@src/utils/iPhoneXHelper';

import FirstStep from './component/FirstStep';
import SecondStep from './component/SecondStep';
import ThirstStep from './component/ThirstStep';
import { useTranslation } from 'react-i18next';

const WalkThroughScreen = () => {
    const Dimens = useDimens();
    const styles = stylesF(Dimens);
    const { t } = useTranslation();

    const { themeColors } = useThemeColors();

    const dispatch = useAppDispatch();

    const workspaceDetail = useAppSelector((state) => state.storageReducer.templateWorkspaceDetail);
    const groupAppDetail = useAppSelector((state) => state.storageReducer.groupAppDetail);

    const [index, setIndex] = useState(0);
    const [routes] = React.useState([
        { key: 'first', title: '' },
        { key: 'second', title: '' },
        { key: 'thirst', title: '' },
    ]);

    const jumpToTab = useRef<Function>(() => {});

    const textRes = useMemo(() => [
        {
            title: t('walk_through_title_1'),
            desc: t('walk_through_description_1')
        },
        {
            title: t('walk_through_title_2'),
            desc: t(isTemplateApp() ? 'walk_through_description_group_2' : isGroupApp() ? 'walk_through_description_group_2' : 'walk_through_description_2')
        },
        {
            title: t('walk_through_title_3'),
            desc: t(isTemplateOrGroupApp() ? 'walk_through_description_template_3' : 'walk_through_description_3', { value: isTemplateApp() ? workspaceDetail?.setting_generals?.title : isGroupApp() ? groupAppDetail?.name : '', interpolation: { escapeValue: false } })
        },
    ], [groupAppDetail?.name, t, workspaceDetail?.setting_generals?.title]);

    const handleNextButton = useCallback(() => {
        if (index !== 2) {
            jumpToTab.current(
                index === 0 ? 'second' : 'thirst'
            );
        } else {
            dispatch(StorageActions.setShowWalkThrough(false));
        }
    }, [dispatch, index]);

    const handleBackButton = useCallback(() => {
        jumpToTab.current(
            index === 2 ? 'second' : 'first'
        );
    }, [index]);

    const renderScene = useCallback(({ route, jumpTo }: {route: any, jumpTo: Function}) => {
        jumpToTab.current = jumpTo;
        switch (route.key) {
            case 'first':
                return <FirstStep />;
            case 'second':
                return <SecondStep focus={index === 1}/>;
            case 'thirst':
                return <ThirstStep />;
        }
    }, [index]);

    const renderLogo = useMemo(() => {
        if (isTemplateOrGroupApp()) {
            return (
                <ShadowView
                    style={{ shadowRadius: Dimens.H_15 }}
                >
                    <ImageComponent
                        resizeMode='cover'
                        source={{ uri: isGroupApp() ? groupAppDetail?.group_restaurant_avatar : workspaceDetail?.photo }}
                        style={styles.resLogo}
                    />
                </ShadowView>
            );
        }

        return (
            <AppTextIcon
                width={Dimens.H_130}
                height={Dimens.H_130 / 1.45}
            />
        );
    }, [Dimens.H_130, Dimens.H_15, groupAppDetail?.group_restaurant_avatar, styles.resLogo, workspaceDetail?.photo]);

    const renderIcons = useMemo(() => (
        <View style={[styles.tabContainer, { backgroundColor: isGroupApp() ? themeColors.group_color : themeColors.color_primary }]}>
            <TabView
                navigationState={{ index, routes }}
                onIndexChange={setIndex}
                renderTabBar={() => null}
                renderScene={renderScene}
            />
            <View style={styles.appIconContainer}>
                {renderLogo}
            </View>
        </View>
    ), [index, renderLogo, renderScene, routes, styles.appIconContainer, styles.tabContainer, themeColors.color_primary, themeColors.group_color]);

    const renderStepIndicator = useMemo(() => (
        <View style={styles.indicatorContainer}>
            <View
                style={[styles.indicator, index === 0 && styles.indicatorActive]}
            />
            <View
                style={[styles.indicator, index === 1 && styles.indicatorActive]}
            />
            <View
                style={[styles.indicator, index === 2 && styles.indicatorActive]}
            />
        </View>
    ), [index, styles.indicator, styles.indicatorActive, styles.indicatorContainer]);

    const renderTextDesc = useMemo(() => (
        <View style={styles.textContainer}>
            <TextComponent style={styles.titleText}>
                {textRes[index].title}
            </TextComponent>
            <TextComponent style={styles.descText}>
                {textRes[index].desc}
            </TextComponent>
        </View>
    ), [index, styles.descText, styles.textContainer, styles.titleText, textRes]);

    const renderButons = useMemo(() => (
        <View style={styles.buttonContainer}>
            {index > 0 && (
                <ButtonComponent
                    title={t('walk_through_back')}
                    style={{ width: index !== 0 ? '50%' : '80%', marginRight: index !== 0 ? Dimens.W_8 : 0, backgroundColor: themeColors.color_button_dark, height: Dimens.W_48 }}
                    onPress={handleBackButton}
                />
            )}
            <ButtonComponent
                title={index !== 2 ? t('walk_through_next') : t('walk_through_next_1')}
                style={{ width: index === 0 ? '80%' : '50%', marginLeft: index !== 0 ? Dimens.W_8 : 0, backgroundColor: themeColors.color_button_dark, height: Dimens.W_48 }}
                onPress={handleNextButton}
            />
        </View>
    ), [Dimens.W_48, Dimens.W_8, handleBackButton, handleNextButton, index, styles.buttonContainer, t, themeColors.color_button_dark]);

    return (
        <View style={styles.container}>
            {renderIcons}
            {renderStepIndicator}
            {renderTextDesc}
            {renderButons}
        </View>
    );
};

export default WalkThroughScreen;

const stylesF = (Dimens: DimensType) => StyleSheet.create({
    buttonContainer: {
        flex: 1,
        flexDirection: 'row',
        alignItems: 'flex-end',
        justifyContent: 'center',
        paddingBottom: getBottomSpace() || 15,
        marginHorizontal: Dimens.W_50,
    },
    descText: {
        color: Colors.COLOR_WHITE,
        fontSize: Dimens.FONT_16,
        textAlign: 'center',
        marginTop: Dimens.H_10,
    },
    titleText: {
        color: Colors.COLOR_WHITE,
        fontSize: Dimens.FONT_26,
        fontWeight: '700',
    },
    textContainer: {
        alignItems: 'center',
        paddingTop: Dimens.H_38,
        paddingHorizontal: Dimens.W_50,
    },
    indicatorContainer: {
        flexDirection: 'row',
        marginTop: Dimens.H_12,
        justifyContent: 'center',
        alignItems: 'center'
    },
    appIconContainer: {
        position: 'absolute',
        alignSelf: 'center',
        top: getStatusBarHeight() + Dimens.H_20,
    },
    tabContainer: {
        width: '100%',
        height: Dimens.SCREEN_HEIGHT / 1.55,
        borderBottomLeftRadius: Dimens.RADIUS_22,
        borderBottomRightRadius: Dimens.RADIUS_22,
    },
    container: { flex: 1, backgroundColor: '#413E38' },
    indicator: {
        width: Dimens.W_4,
        height: Dimens.W_4,
        borderRadius: Dimens.W_4,
        backgroundColor: Colors.COLOR_WHITE_50,
        marginHorizontal: Dimens.W_3,
    },
    indicatorActive: {
        width: Dimens.W_5,
        height: Dimens.W_5,
        borderRadius: Dimens.W_5,
        backgroundColor: Colors.COLOR_WHITE
    },
    resLogo: {
        width: Dimens.W_130,
        height: Dimens.W_130,
        borderRadius: Dimens.W_130,
    },
});
