import React, {
    forwardRef,
    memo,
    useCallback,
    useImperativeHandle,
    useMemo,
} from 'react';

import {
    StyleSheet,
    TouchableOpacity,
    View,
} from 'react-native';
import Popover from 'react-native-popover-view';
import { Placement } from 'react-native-popover-view/dist/Types';

import {
    BelgiumFlag,
    DropDownIcon,
    NetherlandFlag,
} from '@src/assets/svg';
import ShadowView from '@src/components/ShadowView';
import TextComponent from '@src/components/TextComponent';
import { Colors } from '@src/configs';
import useBoolean from '@src/hooks/useBoolean';
import useDimens, { DimensType } from '@src/hooks/useDimens';
import useThemeColors from '@src/themes/useThemeColors';

interface IProps {
    error?: any,
    areaCode?: any,
    setAreaCode: (_code: string) => void,
}

const SelectAreaCodeComponent = forwardRef<any, IProps>(({ error, areaCode, setAreaCode }, ref: any) => {
    const Dimens = useDimens();
    const styles = stylesF(Dimens);
    const { themeColors } = useThemeColors();

    const [isShowPopup, showPopup, hidePopup] = useBoolean(false);

    const areaCodes = useMemo(() => [

        {
            code: '+32',
            flag: (
                <BelgiumFlag
                    width={Dimens.W_25}
                    height={Dimens.W_25}
                />
            )
        },
        {
            code: '+31',
            flag: (
                <NetherlandFlag
                    width={Dimens.W_25}
                    height={Dimens.W_25}
                />
            )
        },
    ], [Dimens.W_25]);

    const handleSelectCode = useCallback((code: string) => {
        setAreaCode(code);
        hidePopup();
    }, [hidePopup, setAreaCode]);

    useImperativeHandle(ref, () => ({
        showPopup
    }), [showPopup]);

    const renderPopup = useCallback(() => (
        <Popover
            statusBarTranslucent
            placement={Placement.BOTTOM}
            backgroundStyle={{ opacity: 0 }}
            isVisible={isShowPopup}
            onRequestClose={hidePopup}
            offset={Dimens.H_3}
            popoverStyle={[styles.popOverStyle]}
            arrowSize={{ height: 0, width: 0 }}
            from={(
                <View
                    renderToHardwareTextureAndroid
                    collapsable={false}
                    style={styles.mainContainer}
                >
                    {areaCodes.find((i) => i.code === areaCode)?.flag}
                    <TextComponent style={[styles.areaCode, { color: error ? themeColors.color_input_border_error : Colors.COLOR_DEFAULT_TEXT_INPUT }]}>
                        {areaCode}
                    </TextComponent>
                    <DropDownIcon
                        stroke={error ? themeColors.color_input_border_error : Colors.COLOR_DEFAULT_TEXT_INPUT}
                        width={Dimens.H_10}
                        height={Dimens.H_10}
                    />
                </View>
            )}
        >
            <ShadowView style={{ shadowColor: 'rgba(0,0,0,0.1)'  }}>
                <View style={{ backgroundColor: themeColors.color_card_background, borderRadius: Dimens.RADIUS_4, paddingVertical: Dimens.H_4 }}>
                    {areaCodes.map((item, index) => (
                        <TouchableOpacity
                            key={index}
                            onPress={() => handleSelectCode(item.code)}
                            style={[styles.flagItemContainer, { backgroundColor: areaCode === item.code ? '#E6E6E6' : undefined, }]}
                        >
                            {item.flag}
                            <TextComponent style={styles.areaCode}>
                                {item.code}
                            </TextComponent>
                        </TouchableOpacity>
                    ))}
                </View>
            </ShadowView>
        </Popover>
    ), [Dimens.H_10, Dimens.H_3, Dimens.H_4, Dimens.RADIUS_4, areaCode, areaCodes, error, handleSelectCode, hidePopup, isShowPopup, styles.areaCode, styles.flagItemContainer, styles.mainContainer, styles.popOverStyle, themeColors.color_card_background, themeColors.color_input_border_error]);

    return (
        <View>
            {renderPopup()}
        </View>
    );
});

export default memo(SelectAreaCodeComponent);

const stylesF = (Dimens: DimensType) =>
    StyleSheet.create({
        flagItemContainer: {
            flexDirection: 'row',
            alignItems: 'center',
            marginTop: Dimens.H_4,
            paddingVertical: Dimens.H_8,
            paddingHorizontal: Dimens.W_16,
        },
        areaCode: {
            fontSize: Dimens.FONT_14,
            fontWeight: '700',
            marginLeft: Dimens.W_8,
            marginRight: Dimens.W_4,
        },
        mainContainer: { flexDirection: 'row', alignItems: 'center' },
        popOverStyle: {
            borderRadius: Dimens.RADIUS_3,
            paddingBottom: Dimens.H_30,
            paddingTop: Dimens.H_8,
            paddingHorizontal: Dimens.W_24,
            alignItems: 'center',
            justifyContent: 'center',
            backgroundColor: 'transparent',
        },
    });