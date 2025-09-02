export const LOCALES = process.env.NEXT_PUBLIC_LOCALES ? process.env.NEXT_PUBLIC_LOCALES.split(',') : ['en', 'nl', 'de', 'fr']
export const LOCALE_FALLBACK = process.env.NEXT_PUBLIC_LOCALE_FALLBACK || 'nl'
export const LOCALES_WITH_NAMES = {
    nl: 'Nederlands (BE)',
    fr: 'Frans (BE)',
    de: 'Duits',
    en: 'Engels',
};