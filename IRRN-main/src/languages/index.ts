import i18next, { Resource } from 'i18next';
import { initReactI18next } from 'react-i18next';

import en from './locales/en.json';
import fr from './locales/fr.json';
import nl from './locales/nl.json';
import de from './locales/de.json';

// const languageDetector: LanguageDetectorAsyncModule = {
//     type: 'languageDetector',
//     async: true,
//     detect: async (cb) => {
//         let language = 'nl';
//         try {
//             const response = await AsyncStorage.getItem('language');
//             if (response) {
//                 language = response;
//             }
//         } catch (error) {
//             //
//         }
//         return cb(language);
//     },
//     init: () => {},
//     cacheUserLanguage: () => {},
// };

const resources: Resource = {
    nl: {
        translation: nl
    },
    en: {
        translation: en
    },
    fr: {
        translation: fr
    },
    de: {
        translation: de
    },
};

i18next.use(initReactI18next).init({
    lng: 'nl',
    fallbackLng: 'nl',
    debug: false,
    resources: resources,
    compatibilityJSON: 'v3',
});

const I18nApp = i18next;

export default I18nApp;

export const DEFAULT_LANGUAGE = 'nl';
