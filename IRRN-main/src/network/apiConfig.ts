import Config from 'react-native-config';

export const API_URL = Config.ENV_API_URL;

export const API_TIMEOUT = 60 * 1000;

export const LOGIN = 'login';
export const LOGIN_WITH_SOCIAL = 'login/social';
export const LOGOUT = 'logout';
export const REGISTER = 'register';
export const LOGIN_WITH_TOKEN = 'auth/token';
export const SEND_EMAIL_RESET_PASSWORD = 'password/email';
export const RESET_PASSWORD = 'password/reset';

export const PROFILE = 'profile';
export const DELETE_USER = 'profile/delete';
export const CHANGE_AVATAR = 'profile/change_avatar';
export const REMOVE_AVATAR = 'profile/remove_avatar';
export const CHANGE_LANGUAGE = 'profile/change_language';

export const RESTAURANT_NEARBY = 'workspaces';
export const RESTAURANT_RECENT = 'workspaces/ordered';
export const RESTAURANT_FAVORITE = 'workspaces/liked';

export const LIST_NOTIFICATION = 'notifications';
export const DETAIL_NOTIFICATION = 'notifications';
export const MARK_NOTIFICATION_AS_READ = 'notifications/read';

export const ORDER_HISTORY = 'orders/history';
export const ORDER_DETAIL = 'orders';

export const RESTAURANT_DETAIL = 'workspaces';

export const PRODUCT_LIST = 'categories/products';
export const PRODUCT_TOGGLE_FAVORITE = 'products';
export const PRODUCT_FAVORITE_PRODUCTS = 'products/liked';
export const LIST_LOYALTY = 'loyalties';
export const COUPON = 'coupons';

export const LIST_GROUP = 'groups';
export const GROUP_DETAIL = 'groups';
export const PRODUCT_DETAIL = 'products';
export const PRODUCT_OPTIONS = 'products';
export const PAYMENT_METHOD = 'workspaces';
export const CREATE_ORDER = 'orders';
export const MOLLIE = 'mollie';
export const CANCEL_ORDER = 'orders';
export const UPDATE_ORDER_PAYMENT = 'orders';

export const VALIDATE_TIME_SLOT = 'products/validate_available_timeslot';
export const CHECK_PRODUCT_AVAILABLE = 'products/check_available';
export const VALIDATE_PRODUCT_AVAILABLE_DELIVERY = 'products/validate_available_delivery';

export const SUGGESTION_PRODUCTS = 'categories';

export const VALIDATE_COUPON_CODE = 'coupons/validate_code';
export const VALIDATE_PRODUCT_COUPON = 'products/validate_coupon';

export const NOTIFICATION_REGISTER_TOKEN = 'notifications/device';
export const NOTIFICATION_UNSUBSCRIBE_TOKEN = 'notifications/device/unsubscribe';

export const GROUP_APP_DETAIL = 'grouprestaurant';

export const REFRESH_TOKEN = 'auth/token/refresh';

// keep this at end of file
export const CHECK_TOKEN_EXPIRED_WHITE_LIST = [
    LOGIN,
    LOGIN_WITH_SOCIAL,
    REGISTER,
    LOGIN_WITH_TOKEN,
    SEND_EMAIL_RESET_PASSWORD,
    RESET_PASSWORD,
];