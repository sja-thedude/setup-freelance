import { TFunction } from 'i18next';
import { Platform } from 'react-native';
import * as RNLocalize from 'react-native-localize';

import { DEFAULT_LANGUAGE } from '@src/languages';

export const IS_ANDROID = Platform.OS === 'android';
export const IS_IOS = Platform.OS === 'ios';

export const EMAIL_REGEX = /^[a-zA-Z0-9!#$%&'*+/=?^_`{|}~-]+(?:\.[a-zA-Z0-9!#$%&'*+/=?^_`{|}~-]+)*@(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\.)+[a-z0-9](?:[a-z0-9-]*[a-z0-9])?$/;

export const ITS_READY_MORE_INFO_LINK = 'https://itsready.be/';
export const ITS_READY_TERM_AND_CONDITION_LINK = 'https://b2b.itsready.be/algemene-voorwaarden';
export const ITS_READY_PRIVACY_LINK = 'https://b2b.itsready.be/privacyverklaring';

export const MIN_PASSWORD_LENGTH = 6;
export const MAX_PHONE_LENGTH = 18;
export const MIN_PHONE_LENGTH = 11;
export const PHONE_MASK = ['+', /\d/, /\d/, /\d/, /\d/, /\d/, /\d/, /\d/, /\d/, /\d/, /\d/, /\d/, /\d/, /\d/, /\d/, /\d/, /\d/, /\d/, /\d/];

export const SOCIAL_PROVIDER = {
    FACEBOOK: 'facebook',
    GOOGLE: 'google',
    APPLE: 'apple',
};

export const DEFAULT_HOME_ADDRESS = {
    lat: 50.92758786546253,
    lng: 5.338539271587612,
    address: 'Limburgplein 1, 3500 Hasselt',
};

export const DEFAULT_DISTANCE_UNIT = 'Km';
export const DEFAULT_DISTANCE = 20000;

export const PAGE_SIZE = 20;
export const LARGE_PAGE_SIZE = 200;

export const RESTAURANT_EXTRA_TYPE = {
    PAY_CON_IQ: 0,
    GROUP_ORDER: 1,
    CUSTOMER_CARD: 2,
    ALLERGENEN: 3,
    SMS_WHATS_APP: 4,
    DISPLAY_IN_APP: 5,
    OWN_MOBILE_APP: 6,
    STICKER: 7,
    SERVICE_COST: 13,
};

export const DEVICE_LANGUAGE_CODE = RNLocalize.getLocales()[0].languageCode || DEFAULT_LANGUAGE;

export const ORDER_TYPE = {
    TAKE_AWAY: 0,
    DELIVERY: 1,
    GROUP_ORDER: 2
};

export const RESTAURANT_SORT_TYPE = {
    DISTANCE: 'distance',
    AMOUNT: 'amount',
    DELIVERY_FEE: 'delivery_fee',
    WAITING_TIME: 'waiting_time',
    NAME: 'name',
};

export const DEFAULT_CURRENCY = 'EUR';

export const PAYMENT_METHOD = {
    MOLLIE: 0,
    PAYCON_IQ: 1,
    CASH: 2,
    INVOICE: 3,
};

export const PAYMENT_STATUS = {
    PENDING: 1,
    PAID: 2,
    CANCEL: 3,
    FAILED: 4,
};

export const ORDER_STATUS = {
    OPEN: 1,
    PAID: 2,
    CANCEL: 3,
    FAILED: 4,
    EXPIRED: 5,
};

export const getProductLabel = (t: TFunction) => ({
    VEGGIE: {
        type: 1,
        label: t('text_veggie').toUpperCase()
    },
    VEGAN: {
        type: 2,
        label: t('text_vegan').toUpperCase()
    },
    SPICY: {
        type: 3,
        label: t('text_spicy').toUpperCase()
    },
    NEW: {
        type: 4,
        label: t('text_new').toUpperCase()
    },
    PROMO: {
        type: 5,
        label: t('text_promo').toUpperCase()
    },
});

export const OPENING_TIME_TYPE = {
    TAKE_AWAY: 0,
    DELIVERY: 1,
    IN_HOUSE: 2
};

export const DATE_FORMAT = 'YYYY-MM-DD';
export const MONTH_FORMAT = 'YYYY-MM';

export const REWARD_TYPE = {
    DISCOUNT: 1,
    PHYSICAL_GIFT: 2,
};

export const VALUE_DISCOUNT_TYPE = {
    NO_DISCOUNT: 0,
    FIXED_AMOUNT: 1,
    PERCENTAGE: 2,
};

export const CART_DISCOUNT_TYPE = {
    GROUP_DISCOUNT: 'GROUP_DISCOUNT',
    COUPON_DISCOUNT: 'COUPON_DISCOUNT',
    LOYALTY_DISCOUNT: 'LOYALTY_DISCOUNT',
};

export const VAT_TYPE = {
    DELIVERY: 'DELIVERY',
    TAKE_OUT: 'TAKE_OUT',
};

export const ADDRESS_TYPE = {
    PROFILE_ADDRESS: 0,
    SELECTED_ADDRESS: 1,
};

export const DEFAULT_FUNC_KEY = {
    JOBS: 'jobs',
    REVIEWS: 'reviews',
    RESERVE: 'reserve',
    ROUTE: 'route',
    RECENT: 'recent',
    FAVORITES: 'favorites',
    ACCOUNT: 'account',
    SHARE: 'share',
    LOYALTY: 'loyalty',
    MENU: 'menu'
};

export const LOCALES = {
    NL: 'nl',
    EN: 'en',
    FR: 'fr',
    DE: 'de',
};
