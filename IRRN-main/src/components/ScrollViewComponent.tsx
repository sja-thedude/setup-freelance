import React, { forwardRef, memo, useImperativeHandle, useRef } from 'react';

import {
    ScrollView,
    ScrollViewProps,
    ScrollViewPropsAndroid,
    ScrollViewPropsIOS,
} from 'react-native';

interface IProps
    extends ScrollViewProps,
        ScrollViewPropsAndroid,
        ScrollViewPropsIOS {}

const ScrollViewComponent = forwardRef<any, IProps>(
    ({ children, ...rest }, ref) => {
        const refScrollView = useRef<any>();

        useImperativeHandle(ref, () => refScrollView.current);

        return (
            <ScrollView
                showsHorizontalScrollIndicator={false}
                showsVerticalScrollIndicator={false}
                {...rest}
                ref={refScrollView}>
                {children}
            </ScrollView>
        );
    },
);

export default memo(ScrollViewComponent);
