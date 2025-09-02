/**
 * @format
 */

import 'react-native-gesture-handler';

import {
    AppRegistry,
    LogBox,
} from 'react-native';

import notifee, { EventType } from '@notifee/react-native';
import FireBaseMessaging from '@react-native-firebase/messaging';
import { log } from '@src/utils/logger';

import App from './App';
import { name as appName } from './app.json';

LogBox.ignoreAllLogs();
LogBox.ignoreLogs(['Non-serializable values were found in the navigation state']);

// handle notification when app in background or closed
FireBaseMessaging().setBackgroundMessageHandler(async (remoteMessage) => {
    log('__________setBackgroundMessageHandler', remoteMessage);
});
notifee.onBackgroundEvent(async ({ type, detail }) => {
    log('__________onBackgroundEvent', detail);
    log('__________onBackgroundEvent_type', type);
    const { pressAction } = detail;
    if (type === EventType.ACTION_PRESS && pressAction.id === 'mark-as-read') {
        //
    }
});

AppRegistry.registerComponent(appName, () => App);
