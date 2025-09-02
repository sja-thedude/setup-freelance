import {
    useCallback,
    useEffect,
} from 'react';

import {
    StatusBar,
    StatusBarStyle,
} from 'react-native';

import { useNavigation } from '@react-navigation/native';

interface Params {
    focus?: StatusBarStyle;
    blur?: StatusBarStyle;
    onFocus?: () => void;
    onBlur?: () => void;
}

const useChangeStatusBar = ({ focus, blur, onBlur, onFocus }: Params = {}) => {
    const navigation = useNavigation();

    const focusIOSStatusBar = useCallback(() => {
        !!focus && StatusBar.setBarStyle(focus, true);
        !!onFocus && onFocus();
    }, [focus, onFocus]);

    const unFocusIOSStatusBar = useCallback(() => {
        !!blur && StatusBar.setBarStyle(blur, true);
        !!onBlur && onBlur();
    }, [blur, onBlur]);

    useEffect(() => {
        const unSubscribe1 = navigation.addListener('focus', focusIOSStatusBar);
        const unSubscribe2 = navigation.addListener('blur', unFocusIOSStatusBar);

        return () => {
            unSubscribe1();
            unSubscribe2();
            unFocusIOSStatusBar();
        };
    }, [focusIOSStatusBar, navigation, unFocusIOSStatusBar]);
};

export default useChangeStatusBar;
