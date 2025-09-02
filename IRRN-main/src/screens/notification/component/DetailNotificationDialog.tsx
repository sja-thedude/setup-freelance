import React, { useCallback } from 'react';

import {
    StyleSheet,
    View,
} from 'react-native';
import { useDispatch } from 'react-redux';

import ButtonComponent from '@src/components/ButtonComponent';
import DialogComponent from '@src/components/DialogComponent';
import TextComponent from '@src/components/TextComponent';
import { useAppSelector } from '@src/hooks';
import useDimens, { DimensType } from '@src/hooks/useDimens';
import { NotificationActions, } from '@src/redux/toolkit/actions/notificationActions';
import useThemeColors from '@src/themes/useThemeColors';
import { getTimeByTimeZone } from '@src/utils/dateTimeUtil';
import { useTranslation } from 'react-i18next';

const DetailNotificationDialog = () => {
    const Dimens = useDimens();
    const styles = stylesF(Dimens);
    const { t } = useTranslation();

    const { themeColors } = useThemeColors();
    const dispatch = useDispatch();

    const notificationsDetailData = useAppSelector((state) => state.notificationReducer.notificationDetail);

    const handleModalHide = useCallback(() => {
        dispatch(NotificationActions.clearNotificationDetail());
    }, [dispatch]);

    return (
        <DialogComponent
            hideModal={handleModalHide}
            isVisible={!!notificationsDetailData.id}
            onModalHide={handleModalHide}
            onBackButtonPress={handleModalHide}
            onBackdropPress={handleModalHide}
            onSwipeComplete={handleModalHide}
        >
            <View style={{ paddingHorizontal: Dimens.W_14 }}>
                <TextComponent style={styles.textTitle}>
                    {notificationsDetailData.title}
                </TextComponent>

                <TextComponent style={[styles.timeText, { color: themeColors.color_common_description_text }]}>
                    {`${getTimeByTimeZone(notificationsDetailData.sent_time, 'YYYY-MM-DD hh:mm:ss', `DD/MM/YYYY [${t('text_date_order_history')}] hh:mm A`)}`}
                </TextComponent>

                <TextComponent style={styles.descText}>
                    {notificationsDetailData.description}
                </TextComponent>
                <ButtonComponent
                    title={t('text_close')}
                    style={styles.textAButton}
                    onPress={handleModalHide}
                />
            </View>
        </DialogComponent>
    );
};

export default DetailNotificationDialog;

const stylesF = (Dimens: DimensType) => StyleSheet.create({
    timeText: { fontSize: Dimens.FONT_14 },
    descText: { fontSize: Dimens.FONT_14, marginTop: Dimens.H_16 },
    textAButton: {
        width: '75%',
        alignSelf: 'center',
        marginTop: Dimens.H_40,
    },
    textTitle: {
        fontSize: Dimens.FONT_24,
        fontWeight: '700',
        marginTop: Dimens.H_16,
        marginBottom: Dimens.H_3,
    },
});