export const log = (title: any = '', message: any = '') => {
    __DEV__ && console.log(title, message);
};

export const logError = (title: any = '', message: any = '') => {
    __DEV__ && console.error(title, message);
};

export const logWarning = (title: any = '', message: any = '') => {
    __DEV__ && console.warn(title, message);
};