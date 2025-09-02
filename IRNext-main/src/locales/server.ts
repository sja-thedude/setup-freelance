import { createI18nServer } from 'next-international/server'
 
export const { getI18n, getScopedI18n, getStaticParams } = createI18nServer({
    en: () => import('@/langs/en/index'),
    nl: () => import('@/langs/nl/index'),
    fr: () => import('@/langs/fr/index'),
    de: () => import('@/langs/de/index')
})