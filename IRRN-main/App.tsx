import 'dayjs/locale/vi';
import '@languages/index';

import React, { useCallback } from 'react';

import dayjs from 'dayjs';
import {
    StatusBar,
    StyleSheet,
} from 'react-native';
import codePush from 'react-native-code-push';
import { Settings } from 'react-native-fbsdk-next';
import { GestureHandlerRootView } from 'react-native-gesture-handler';
import { SafeAreaProvider } from 'react-native-safe-area-context';
import { enableScreens } from 'react-native-screens';
import { Provider } from 'react-redux';
import { PersistGate } from 'redux-persist/integration/react';

import { ActionSheetProvider } from '@expo/react-native-action-sheet';
import NavigationWrapper from '@navigation/index';
import { GoogleSignin } from '@react-native-google-signin/google-signin';
import store, { persistor } from '@redux/store';
import { IS_ANDROID } from '@src/configs/constants';
import {
    setCustomFlatList,
    setCustomScrollView,
    setCustomSectionList,
    setCustomText,
    setCustomTextInput,
} from '@utils/customs/defaultPropsComponent';

Settings.initializeSDK();
GoogleSignin.configure({});

dayjs.locale('vi');

enableScreens();

setCustomFlatList({
    keyExtractor: (item: any, index: number) => index.toString(),
    showsHorizontalScrollIndicator: false,
});
setCustomSectionList({
    keyExtractor: (item: any, index: number) => index.toString(),
    stickySectionHeadersEnabled: true,
    showsHorizontalScrollIndicator: false,
});
setCustomScrollView({
    showsHorizontalScrollIndicator: false
});
setCustomText({
    allowFontScaling: false
});
setCustomTextInput({
    autoCorrect: false,
    spellCheck: false,
    allowFontScaling: false,
    underlineColorAndroid: 'transparent',
});

const App = () => {
    if (IS_ANDROID) {
        StatusBar.setBackgroundColor('transparent');
        StatusBar.setTranslucent(true);
    }

    const onBeforeLift = useCallback(async () => {
    }, []);

    return (
        <Provider store={store}>
            <PersistGate
                loading={null}
                onBeforeLift={onBeforeLift}
                persistor={persistor}
            >
                <SafeAreaProvider>
                    <ActionSheetProvider>
                        <GestureHandlerRootView style={styles.container}>
                            <NavigationWrapper />
                        </GestureHandlerRootView>
                    </ActionSheetProvider>
                </SafeAreaProvider>
            </PersistGate>
        </Provider>
    );
};

const styles = StyleSheet.create({
    container: {
        flex: 1
    },
});

export default codePush({
    updateDialog: !__DEV__,
    installMode: !__DEV__ ? codePush.InstallMode.IMMEDIATE : codePush.CheckFrequency.ON_APP_RESUME,
})(App);
