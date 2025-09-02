import React, {
    FC,
    Fragment,
    memo,
    useCallback,
    useEffect,
    useState,
} from 'react';

import dayjs from 'dayjs';
import {
    ScrollView,
    StyleSheet,
    useWindowDimensions,
    View,
} from 'react-native';

import { CalendarIcon } from '@src/assets/svg';
import TextComponent from '@src/components/TextComponent';
import { Colors } from '@src/configs';
import { useAppSelector } from '@src/hooks';
import useCallAPI from '@src/hooks/useCallAPI';
import useDimens, { DimensType } from '@src/hooks/useDimens';
import { fetchSettingHolidayException, } from '@src/network/services/restaurantServices';
import { isTemplateOrGroupApp } from '@src/utils';

interface IProps {
    topSpace?: number,
    isInHome: boolean,
}

const HolidayException: FC<IProps> = ({ topSpace, isInHome }) => {
    const Dimens = useDimens();
    const styles = stylesF(Dimens);

    const restaurantData = useAppSelector((state) => state.restaurantReducer.restaurantDetail.data);
    const { height } = useWindowDimensions();

    const [dataException, setDataException] = useState<any>();

    const { callApi: getSettingHolidayException } = useCallAPI(
            fetchSettingHolidayException,
            undefined,
            useCallback((data: any) => {
                const newData = data?.find((i: any) => dayjs().toDate().getTime() >= dayjs(i?.start_time)
                        .startOf('day')
                        .toDate()
                        .getTime()
                        && dayjs().toDate().getTime() <= dayjs(i?.end_time).endOf('day').toDate().getTime(),
                );

                setDataException(newData);
            }, []),
            undefined,
            true,
            false
    );

    useEffect(() => {
        if (restaurantData?.id) {
            getSettingHolidayException(restaurantData?.id);
        }
    }, [getSettingHolidayException, restaurantData?.id]);

    return (
        <Fragment>
            {!!dataException && (
                <View style={[styles.container, {
                    top: isTemplateOrGroupApp() ? isInHome ? topSpace : undefined : undefined,
                    bottom: isInHome ? undefined : Dimens.H_10,
                }]}
                >
                    <CalendarIcon
                        width={Dimens.H_24}
                        height={Dimens.H_24}
                    />
                    <View
                        style={[
                            styles.viewContent,
                            { maxHeight: height / 1.5 },
                        ]}
                    >
                        <ScrollView
                            style={styles.scrolView}
                            showsVerticalScrollIndicator={false}
                        >
                            <TextComponent style={styles.textValue}>
                                {dataException?.description}
                            </TextComponent>
                        </ScrollView>
                    </View>
                </View>
            )}
        </Fragment>
    );
};

const stylesF = (Dimens: DimensType) => StyleSheet.create({
    container: {
        marginHorizontal: Dimens.W_16,
        position: 'absolute',
        flexDirection: 'row',
        alignItems: 'center',
        borderRadius: Dimens.RADIUS_10,
        paddingVertical: Dimens.H_10,
        paddingHorizontal: Dimens.W_12,
        backgroundColor: 'rgba(0, 0, 0, 0.8)',
        left: 0,
        right: 0,
    },
    textValue: {
        fontSize: Dimens.FONT_14,
        flex: 1,
        color: Colors.COLOR_WHITE,
    },
    viewContent: { flex: 1, paddingLeft: Dimens.W_12 },
    scrolView: { flexGrow: 0 },
});

export default memo(HolidayException);
