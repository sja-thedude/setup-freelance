import React, {
    forwardRef,
    Fragment,
    memo,
    MutableRefObject,
    useCallback,
    useImperativeHandle,
    useRef,
} from 'react';

import dayjs from 'dayjs';
import debounce from 'lodash/debounce';
import {
    FlatList,
    FlatListProps,
} from 'react-native';
import {
    KeyboardAwareFlatList,
    KeyboardAwareFlatListProps,
} from 'react-native-keyboard-aware-scroll-view';

import ListFooterLoading from './ListFooterLoading';

interface IProps extends FlatListProps<any>, KeyboardAwareFlatListProps<any> {
    isKeyboardAware?: boolean;
    hasNext?: boolean;
}

const FlatListComponent = forwardRef<FlatList, IProps>(({ isKeyboardAware, hasNext, ListFooterComponent, onEndReached, ...rest }, ref) => {

    const keyExtractor = useCallback((_: any, index: number) => index.toString(), []);
    const refFlatList = useRef<FlatList>();

    useImperativeHandle(ref, () => refFlatList.current as FlatList);

    const onScrollToIndexFailed = useCallback(
            (info: { index: number; highestMeasuredFrameIndex: number; averageItemLength: number }) => {
                refFlatList.current?.scrollToOffset({
                    offset: info.averageItemLength * info.index,
                    animated: true,
                });
                debounce(() => refFlatList.current?.scrollToIndex({ index: info.index, animated: true }), 100)();
            },
            [],
    );

    const innerRef = useCallback((e: any) => {
        refFlatList.current = e;
    }, []);

    const listFooterComponent = useCallback(
            () =>
                onEndReached ? (
                    <ListFooterLoading canLoadMore={hasNext}/>
                ) : (
                    <Fragment />
                ),
            [hasNext, onEndReached],
    );

    return isKeyboardAware ? (
            <KeyboardAwareFlatList
                showsHorizontalScrollIndicator={false}
                showsVerticalScrollIndicator={false}
                keyExtractor={keyExtractor}
                listKey={dayjs().valueOf().toString()}
                innerRef={innerRef}
                onScrollToIndexFailed={onScrollToIndexFailed}
                ListFooterComponent={ListFooterComponent || listFooterComponent}
                onEndReached={onEndReached}
                {...rest}
            />
        ) : (
            <FlatList
                showsHorizontalScrollIndicator={false}
                showsVerticalScrollIndicator={false}
                keyExtractor={keyExtractor}
                listKey={dayjs().valueOf().toString()}
                ref={refFlatList as MutableRefObject<FlatList>}
                onScrollToIndexFailed={onScrollToIndexFailed}
                ListFooterComponent={ListFooterComponent || listFooterComponent}
                onEndReached={onEndReached}
                {...rest}
            />
        );
},
);

export default memo(FlatListComponent);
