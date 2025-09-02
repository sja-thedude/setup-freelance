import Cookies from "js-cookie";
import {usePathname} from "next/navigation";
import { api } from "@/utils/axios";
import {TIMEZONE, OPENING_HOUR_TABLE_ORDERING_TYPE} from "@/config/constants"
const useValidateSecurity = () => {
    const pathName = usePathname();
    const accessOrdering = Cookies.get('accessOrdering');
    const isTableOrdering = pathName.includes('table-ordering');
    const isSelfOrdering = pathName.includes('self-ordering');

    if (((isTableOrdering || isSelfOrdering) && window.innerWidth > 1280) || accessOrdering != 'true') {
        return false;
    }

    return true;
};

export const useValidateToTriggerClosedScreen = async (
    router: any,
    workspaceId: any, 
    openingHourType: any,
    extraSettingType: any
) => {
    if (workspaceId) {    
        const res = await api.get(`workspaces/` + workspaceId);
        const json = res.data;
        let flagCheckTime = true;

        // validate in admin extra setting
        json.data?.extras.map((item: any) => {
            if (item?.type === extraSettingType) {
                if (item.active != true) {
                    flagCheckTime = false;
                    triggerCloseRestaurant(router, openingHourType);
                }
            }
        });
        
        // validate in manager opening hours setting
        json.data?.setting_open_hours.map((item: any) => {
            if (item?.type === openingHourType) {
                if (item.active != true) {
                    flagCheckTime = false;
                    triggerCloseRestaurant(router, openingHourType);
                }
            }
        });

        if(flagCheckTime === true) {
            checkTime(router, json.data, openingHourType);
        }
    }
}

export const getCurrentDateInTimeZone = (timezone: string): string => {
    const currentDate = new Date();
    const options: Intl.DateTimeFormatOptions = {
        timeZone: timezone,
        year: 'numeric',
        month: 'numeric',
        day: 'numeric',
        hour: 'numeric',
        minute: 'numeric',
        second: 'numeric',
    };

    return currentDate.toLocaleString('en-US', options);
};

export const timezone: any = TIMEZONE;

export const getDayInTimeZone = (timezone: any) => {
    const currentDate = new Date();
    const options = {
        timeZone: timezone,
    };
    const dateInTimeZone = new Date(currentDate.toLocaleString('en-US', options));

    return dateInTimeZone.getDay();
};

const checkTime = (router: any, dataFinal: any, openingHourType: any) => {
    const now: any = new Date(getCurrentDateInTimeZone(timezone));
    const dayName: any = getDayInTimeZone(timezone);
    dataFinal?.setting_open_hours.map((item: any, index: any) => {
        if (item.type === openingHourType) {
            const hasDayName = (obj: any) => obj.day_number === dayName;
            // check if has day name or not
            if (!item.open_time_slots.some(hasDayName)) {
                triggerCloseRestaurant(router, openingHourType);
            } else {
                item?.open_time_slots.map((range: any, time_index: any) => {
                    if (range.day_number === dayName) {
                        const startTime = new Date(now);
                        const endTime = new Date(now);
                        const startHoursMinutesSeconds = range.start_time.split(':').map((val: string) => parseInt(val, 10));
                        const endHoursMinutesSeconds = range.end_time.split(':').map((val: string) => parseInt(val, 10));

                        startTime.setHours(startHoursMinutesSeconds[0], startHoursMinutesSeconds[1], startHoursMinutesSeconds[2]);
                        endTime.setHours(endHoursMinutesSeconds[0], endHoursMinutesSeconds[1], endHoursMinutesSeconds[2]);

                        if (!(now >= startTime && now <= endTime)) {
                            triggerCloseRestaurant(router, openingHourType);
                        }
                    }
                })
            }
        }
    })
};

const triggerCloseRestaurant = (router: any, openingHourType: any) => {
    Cookies.set('from'+ (openingHourType === OPENING_HOUR_TABLE_ORDERING_TYPE ? 'Table' : 'Self') +'Cart', 'true');    
    router.push('/'+ (openingHourType === OPENING_HOUR_TABLE_ORDERING_TYPE ? 'table' : 'self') +'-ordering/closed');
};

export default useValidateSecurity;