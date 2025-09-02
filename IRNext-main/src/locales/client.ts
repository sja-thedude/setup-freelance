import { createI18nClient } from 'next-international/client'

export const { useI18n, useScopedI18n, I18nProviderClient } = createI18nClient({
    en: () => import('@/langs/en/index'),
    nl: () => import('@/langs/nl/index'),
    fr: () => import('@/langs/fr/index'),
    de: () => import('@/langs/de/index')
})