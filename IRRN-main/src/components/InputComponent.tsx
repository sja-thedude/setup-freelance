import React, {
    FC,
    memo,
    ReactNode,
    useCallback,
    useEffect,
    useMemo,
    useState,
} from 'react';

import {
    StyleSheet,
    TextInput,
    TextInputProps,
    TextStyle,
    ViewStyle,
} from 'react-native';

import {
    EyeIcon,
    EyeSlashIcon,
} from '@src/assets/svg';
import { IS_ANDROID } from '@src/configs/constants';
import useDimens, { DimensType } from '@src/hooks/useDimens';
import useThemeColors from '@src/themes/useThemeColors';
import { convertFontWeightToFontFamily } from '@src/utils/fontUtil';

import ShadowView from './ShadowView';
import TouchableComponent from './TouchableComponent';

export interface InputComponentProps extends TextInputProps {
    children?: string | ReactNode;
    style?: TextStyle;
    containerStyle?: ViewStyle;
    inputContainerStyle?: ViewStyle;
    leftIconContainerStyle?: ViewStyle;
    rightIcon?: ReactNode;
    leftIcon?: ReactNode;
    value?: any;
    placeholderTextColor?: any;
    secureTextEntry?: boolean;
    error?: any;
    leftIconPress?: Function;
    rightIconPress?: Function;
    inputPress?: any;
    backgroundInput?: any;
    borderInput?: any;
    textColorInput?: any;
    errorBackgroundInput?: any;
    eyeColor?: any;
    inputBorderRadius?: any;
}

const InputComponent: FC<InputComponentProps> = ({ containerStyle, style, rightIcon, leftIcon, value, placeholderTextColor, inputPress, leftIconPress, rightIconPress, secureTextEntry, error, backgroundInput, borderInput, textColorInput, errorBackgroundInput, eyeColor, inputBorderRadius, inputContainerStyle, leftIconContainerStyle, ...rest }) => {
    const { themeColors } = useThemeColors();
    const Dimens = useDimens();
    const styles = stylesF(Dimens);

    const [isTextPassWord, setIsTextPassWord] = useState(secureTextEntry);
    const [mIconRight, setIconRight] = useState(rightIcon);

    const onRightIconPress = useCallback(() => {
        rightIconPress &&  rightIconPress();
        if (secureTextEntry) {
            setIsTextPassWord(!isTextPassWord);
        }
    }, [isTextPassWord, rightIconPress, secureTextEntry]);

    const disableInput = useMemo(() => typeof inputPress === 'function', [inputPress]);

    useEffect(() => {
        if (secureTextEntry) {
            setIconRight(isTextPassWord ? (
                <EyeIcon
                    width={Dimens.H_24}
                    height={Dimens.H_25}
                    stroke={error ? themeColors.color_error : (eyeColor || themeColors.color_text)}
                />
            ) : (
                <EyeSlashIcon
                    width={Dimens.H_24}
                    height={Dimens.H_25}
                    stroke={error ? themeColors.color_error : (eyeColor || themeColors.color_text)}
                />
            ));

        } else {
            setIconRight(rightIcon);
        }
    }, [secureTextEntry, rightIcon, isTextPassWord, value, themeColors.color_text, eyeColor, error, themeColors.color_error, Dimens.H_24, Dimens.H_25]);

    return (
        <ShadowView
            style={{ ...styles.inputWrapper, ...containerStyle }}
        >
            <TouchableComponent
                activeOpacity={0.9}
                disabled={!disableInput}
                onPress={inputPress}
                style={{
                    ...styles.inputContainer,
                    ...inputContainerStyle,
                    borderRadius: inputBorderRadius || Dimens.RADIUS_10,
                    borderColor: error ? themeColors.color_input_border_error : (borderInput || themeColors.color_input_background),
                    backgroundColor: error ? (errorBackgroundInput || themeColors.color_input_error_background) : (backgroundInput || themeColors.color_input_background)
                }}
            >
                {leftIcon && (
                    <TouchableComponent
                        disabled={disableInput}
                        onPress={leftIconPress}
                        style={[styles.leftIcon, leftIconContainerStyle]}
                        hitSlop={Dimens.DEFAULT_HIT_SLOP}
                    >
                        {leftIcon}
                    </TouchableComponent>
                )}

                <TextInput
                    editable={!disableInput}
                    pointerEvents={disableInput ? 'none' : 'auto'}
                    style={[
                        styles.inputStyle,
                        style,
                        { color: error ? themeColors.color_input_border_error : (textColorInput || themeColors.color_text) },
                        { fontFamily: convertFontWeightToFontFamily(style) },
                        { ...(IS_ANDROID && { fontWeight: undefined }) }
                    ]}
                    allowFontScaling={false}
                    placeholderTextColor={error ? themeColors.color_input_border_error : (placeholderTextColor || themeColors.color_input_place_holder)}
                    secureTextEntry={isTextPassWord}
                    value={value}
                    {...rest}
                />
                {mIconRight && (
                    <TouchableComponent
                        disabled={disableInput}
                        onPress={onRightIconPress}
                        style={styles.rightIcon}
                        hitSlop={Dimens.DEFAULT_HIT_SLOP}
                    >
                        {mIconRight}
                    </TouchableComponent>
                )}

            </TouchableComponent>
        </ShadowView>
    );
};

const stylesF = (Dimens: DimensType) => StyleSheet.create({
    rightIcon: {
        paddingRight: Dimens.W_12,
        justifyContent: 'center',
        alignItems: 'center',
    },
    leftIcon: {
        paddingLeft: Dimens.W_12,
        justifyContent: 'center',
        alignItems: 'center',
    },
    inputWrapper: {
        height: Dimens.W_46,
        shadowColor: '#00000003'
    },
    inputContainer: {
        flexDirection: 'row',
        width: '100%',
        height: '100%',
        borderWidth: 1,
        borderRadius: Dimens.RADIUS_10,
        overflow: 'hidden',
    },
    inputStyle: {
        flex: 1,
        includeFontPadding: false,
        fontSize: Dimens.FONT_16,
        paddingHorizontal: Dimens.W_16,
    },
});

export default memo(InputComponent);
