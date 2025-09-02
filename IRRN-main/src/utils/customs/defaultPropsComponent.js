import {
    FlatList,
    Platform,
    ScrollView,
    SectionList,
    StyleSheet,
    Text,
    TextInput,
} from 'react-native';

import { convertFontWeightToFontFamily } from '@utils/fontUtil';

export const setCustomFlatList = (customProps = {}) => {
    let sourceRender = FlatList.render;
    FlatList.render = function render(props, ref) {
        return sourceRender.apply(this, [
            {
                ...customProps,
                ...props,
                style: StyleSheet.flatten([customProps.style, props.style]),
            },
            ref,
        ]);
    };
};

export const setCustomScrollView = (customProps = {}) => {
    let sourceRender = ScrollView.render;
    ScrollView.render = function render(props, ref) {
        return sourceRender.apply(this, [
            {
                ...customProps,
                ...props,
                style: StyleSheet.flatten([customProps.style, props.style]),
            },
            ref,
        ]);
    };
};

export const setCustomSectionList = (customProps = {}) => {
    let sourceRender = SectionList.render;
    SectionList.render = function render(props, ref) {
        return sourceRender.apply(this, [
            {
                ...customProps,
                ...props,
                style: StyleSheet.flatten([customProps.style, props.style]),
            },
            ref,
        ]);
    };
};

const patchStyles = (style = {}) => StyleSheet.flatten([
    { fontFamily: convertFontWeightToFontFamily(style) },
    style,
    { ...(Platform.OS === 'android' && { fontWeight: undefined }) },
]);

export const setCustomText = (customProps = {}) => {
    let sourceRender = Text.render;
    Text.render = function render(props, ref) {
        return sourceRender.apply(this, [
            { ...customProps, ...props, style: patchStyles(props.style) },
            ref,
        ]);
    };
};

export const setCustomTextInput = (customProps = {}) => {
    let sourceRender = TextInput.render;
    TextInput.render = function render(props, ref) {
        return sourceRender.apply(this, [
            { ...customProps, ...props, style: patchStyles(props.style) },
            ref,
        ]);
    };
};
