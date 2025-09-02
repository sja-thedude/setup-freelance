import React, {
    FC,
    memo,
    ReactNode,
} from 'react';

import {
    StyleSheet,
    View,
} from 'react-native';

import { Colors } from '@src/configs';
import useDimens, { DimensType } from '@src/hooks/useDimens';
import useThemeColors from '@src/themes/useThemeColors';

import BackButton from './header/BackButton';
import HeaderComponent from './header/HeaderComponent';
import TextComponent from './TextComponent';

interface IProps {
    hiddenBack?: boolean;
    title?: string;
    children?: ReactNode;
    numberOfLines?: number;
}

const ActivityPanel: FC<IProps> = ({
    title,
    hiddenBack,
    children,
    numberOfLines = 1,
}) => {
    const { themeColors } = useThemeColors();
    const Dimens = useDimens();
    const styles = stylesF(Dimens);

    return (
        <View
            style={[
                styles.container,
                { backgroundColor: themeColors?.color_app_background },
            ]}
        >
            <HeaderComponent>
                <View style={styles.header}>
                    {!hiddenBack && <BackButton />}
                    <TextComponent
                        numberOfLines={numberOfLines}
                        style={styles.headerText}
                    >
                        {title}
                    </TextComponent>
                </View>
            </HeaderComponent>

            <View style={styles.content}>{children}</View>
        </View>
    );
};

const stylesF = (Dimens: DimensType) => StyleSheet.create({
    container: { flex: 1 },
    header: { flexDirection: 'row', alignItems: 'center' },
    headerText: {
        color: Colors.COLOR_WHITE,
        fontSize: Dimens.FONT_26,
        fontWeight: '700',
    },
    content: { flex: 1 },
});

export default memo(ActivityPanel);
