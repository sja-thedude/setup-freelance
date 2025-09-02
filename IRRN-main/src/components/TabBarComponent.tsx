import React, {
    FC,
    memo,
} from 'react';

import PropTypes from 'prop-types';
import {
    Animated,
    StyleSheet,
    ViewStyle,
} from 'react-native';
import {
    TabBar,
    TabBarProps,
} from 'react-native-tab-view';

import useDimens, { DimensType } from '@src/hooks/useDimens';
import useThemeColors from '@src/themes/useThemeColors';

import TextComponent from './TextComponent';

interface IProps extends TabBarProps<any> {
    tabContainerStyle?: Animated.AnimatedProps<ViewStyle>
}

const TabBarComponent: FC<IProps> = (props) => {
    const { themeColors } = useThemeColors();
    const Dimens = useDimens();
    const styles = stylesF(Dimens);

    return (
        <Animated.View style={props.tabContainerStyle}>
            <TabBar
                pressColor='transparent'
                pressOpacity={0.5}
                indicatorStyle={{ backgroundColor: themeColors.color_primary }}
                activeColor={themeColors.color_primary}
                inactiveColor={themeColors.color_text}
                renderLabel={({ route, focused }) => (
                    <TextComponent
                        style={[props.labelStyle, { fontSize: Dimens.FONT_14, fontWeight: '500', color: focused ? themeColors.color_primary : themeColors.color_text }]}
                    >
                        {route.title.toUpperCase()}
                    </TextComponent>
                )}
                {...props}
                style={[styles.tabBarStyle, props.style]}
                tabStyle={[styles.tabStyle, props.tabStyle]}
            />
        </Animated.View>
    );
};

TabBarComponent.propTypes = {
    labelStyle: PropTypes.any
};

const stylesF = (Dimens: DimensType) => StyleSheet.create({
    tabBarStyle: {
        elevation: 0,
    },
    labelStyle: { textTransform: 'none' },
    tabStyle: { width: 'auto', padding: Dimens.W_6 },
});

export default memo(TabBarComponent);
