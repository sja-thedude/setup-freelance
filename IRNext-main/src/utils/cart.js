import { addStepRoot, changeCartLimitTimeToPayment } from '@/redux/slices/cartSlice';
import moment from 'moment';
import { toNumber } from 'lodash';

export const checkCartLimitTimeAndValidateStep = (dispatch) => {
    let flag = true;
    const startTimeLimit = localStorage.getItem('cartLimitTimeToPayment');
    const numberOfTimeLimit = 300000;

    if(startTimeLimit) {
        const pastTime = moment(toNumber(startTimeLimit));
        const diffInMiliSeconds = moment().diff(pastTime, 'milliseconds');
        
        if(diffInMiliSeconds >= numberOfTimeLimit) {
            flag = false;
            dispatch(changeCartLimitTimeToPayment(true));
            dispatch(addStepRoot(2));
        }
    }

    return flag;
}