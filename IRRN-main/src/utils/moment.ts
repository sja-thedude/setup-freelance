import moment from 'moment/min/moment-with-locales';

import { DEFAULT_LANGUAGE } from '@src/languages';

moment.locale(DEFAULT_LANGUAGE);

export default moment;