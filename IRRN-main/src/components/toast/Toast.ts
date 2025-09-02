import { InteractionManager } from 'react-native';

export const TOAST_DURATION = {
    SHORT: 1000,
    NORMAl: 2500,
    LONG: 3000,
    EXTRA_LONG: 10000,
    INFINITE: 0,
};

const defaultConfig = {
    placement: 'bottom',
    duration: TOAST_DURATION.NORMAl,
    animationType: 'slide-in',
};

export default class Toast {
    static toast: any;

    static setToast(toast: any) {
        this.toast = toast;
    }

    static getToast() {
        return this.toast;
    }

    static showToast = (message: any, config?: object) => {
        InteractionManager.runAfterInteractions(() => {
            this.toast?.hideAll();
            this.toast?.show(message, {
                type: 'normal',
                ...defaultConfig,
                ...config,
            });
        });
    };

    static showToastError = (message: string, config?: object) => {
        InteractionManager.runAfterInteractions(() => {
            this.toast?.hideAll();
            this.toast?.show(message, {
                type: 'danger',
                ...defaultConfig,
                ...config,
            });
        });
    };

    static showToastInfo = (message: string, config?: object) => {
        InteractionManager.runAfterInteractions(() => {
            this.toast?.hideAll();
            this.toast?.show(message, {
                type: 'info',
                ...defaultConfig,
                ...config,
            });
        });
    };

    static showToastWarn = (message: string, config?: object) => {
        InteractionManager.runAfterInteractions(() => {
            this.toast?.hideAll();
            this.toast?.show(message, {
                type: 'warning',
                ...defaultConfig,
                ...config,
            });
        });
    };

    static showToastSuccess = (message: string, config?: object) => {
        InteractionManager.runAfterInteractions(() => {
            this.toast?.hideAll();
            this.toast?.show(message, {
                type: 'success',
                ...defaultConfig,
                ...config,
            });
        });
    };

    static hideAllToast = () => {
        InteractionManager.runAfterInteractions(() => {
            this.toast?.hideAll();
        });
    };
}