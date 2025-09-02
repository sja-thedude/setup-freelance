import { DATE_FORMAT } from '@src/configs/constants';
import moment from '@utils/moment';

export const getTimeByTimeZone = (time?: string, timeFormat?: string, outputFormat?: string) => {

    let hourOffset = new Date().getTimezoneOffset() / 60;

    if (hourOffset < 0) {
        return moment(time, timeFormat).add(-hourOffset, 'hours').format(outputFormat);
    } else {
        return moment(time, timeFormat).subtract(hourOffset, 'hours').format(outputFormat);
    }
};

export const getDaysBetweenDates = function(startDate: string, endDate: string) {
    const now = moment(startDate, DATE_FORMAT).clone();
    const dates = [];

    while (now.isSameOrBefore(moment(endDate, DATE_FORMAT))) {
        dates.push(now.format(DATE_FORMAT));
        now.add(1, 'days');
    }
    return dates;
};