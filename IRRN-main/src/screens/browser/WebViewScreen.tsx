import React from 'react';

import {
    StyleSheet,
    View,
} from 'react-native';
import WebView from 'react-native-webview';

import { useRoute } from '@react-navigation/native';
import BackButton from '@src/components/header/BackButton';
import HeaderComponent from '@src/components/header/HeaderComponent';
import LoadingIndicatorComponent from '@src/components/LoadingIndicatorComponent';
import TextComponent from '@src/components/TextComponent';
import { Colors } from '@src/configs';
import { useAppDispatch } from '@src/hooks';
import useDimens, { DimensType } from '@src/hooks/useDimens';
import { WebViewScreenProps } from '@src/navigation/NavigationRouteProps';
import { LoadingActions } from '@src/redux/toolkit/actions/loadingActions';

const WebViewScreen = () => {
    const Dimens = useDimens();
    const styles = stylesF(Dimens);

    const { params } = useRoute<WebViewScreenProps>();
    const { url, title } = params;
    const dispatch = useAppDispatch();

    return (
        <View style={{ flex: 1 }}>
            <HeaderComponent >
                <View style={styles.header}>
                    <BackButton/>
                    <TextComponent style={styles.headerText}>
                        {title}
                    </TextComponent>
                </View>
            </HeaderComponent>
            <WebView
                renderLoading={() => <LoadingIndicatorComponent/>}
                onLoadStart={() => {
                    dispatch(LoadingActions.showGlobalLoading(true));
                }}
                onLoadEnd={() => {
                    dispatch(LoadingActions.showGlobalLoading(false));
                }}
                source={{ uri: url }}
            />
        </View>
    );
};

export default WebViewScreen;

const stylesF = (Dimens: DimensType) => StyleSheet.create({
    headerText: {
        color: Colors.COLOR_WHITE,
        fontSize: Dimens.FONT_26,
        fontWeight: '700',
    },
    header: { flexDirection: 'row', alignItems: 'center' },

});