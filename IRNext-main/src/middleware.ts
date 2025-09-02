import { createI18nMiddleware } from 'next-international/middleware'
import { NextRequest, NextResponse } from 'next/server'
import * as locales from '@/config/locales'
import * as globalConfig from '@/config/constants'

const I18nMiddleware = createI18nMiddleware(locales.LOCALES ?? [] as const, locales.LOCALE_FALLBACK, {
    resolveLocaleFromRequest: () => {
        return locales.LOCALE_FALLBACK
    }
})

export async function middleware(request: NextRequest) {
    const response = I18nMiddleware(request)
    const pageUrl = request.url
    const configPortalHost = (globalConfig.PORTAL_WEBSITE).trim()
    const domain = request.headers.get('host')
    const requestDomain = request.headers.get('x-forwarded-host')

    if((domain && domain == configPortalHost) || (requestDomain && requestDomain == configPortalHost)) {
        response.headers.set('x-next-workspace', '');
        response.headers.set('x-next-workspace-token', '');   
        response.headers.set('x-next-workspace-color', '');   
    } else if(!globalConfig.WORKSPACE_TOKEN) {
        let domainReplace: any = requestDomain ? requestDomain : domain

        if(globalConfig?.EXCEPT_DOMAIN && globalConfig.EXCEPT_DOMAIN != null && (globalConfig.EXCEPT_DOMAIN)?.trim() != '') {
            domainReplace = domainReplace?.replace(globalConfig.EXCEPT_DOMAIN + '.', '')
        }

        const domainSplit = domainReplace?.split('.')
        let flag = false

        if(domainSplit && domainSplit?.length > 1) {
            const slugWorkSpace = domainSplit[0]
            const getWorkspace = await fetch(`${globalConfig.API_URL}workspaces/domain/${slugWorkSpace}`, { cache: 'no-store' })
            const workspace = await getWorkspace.json()
            response.headers.set('x-next-workspace', workspace?.data?.id);
            response.headers.set('x-next-workspace-token', workspace?.data?.token);
            response.headers.set('x-next-workspace-color', workspace?.data?.setting_generals?.primary_color ? workspace?.data?.setting_generals?.primary_color : '');   

            if(workspace?.data?.id && workspace?.data?.token) {
                flag = true                
            }
        }   

        if(flag == false && !pageUrl.includes('404')) {
            return NextResponse.redirect(new URL('/404', request.url))
        }
    } else {
        const getWorkspace = await fetch(`${globalConfig.API_URL}workspaces/token/${globalConfig.WORKSPACE_TOKEN}`, { cache: 'no-store' })
        const workspace = await getWorkspace.json()
        response.headers.set('x-next-workspace', workspace?.data?.id);
        response.headers.set('x-next-workspace-token', String(globalConfig.WORKSPACE_TOKEN));
        response.headers.set('x-next-workspace-color', workspace?.data?.setting_generals?.primary_color ? workspace?.data?.setting_generals?.primary_color : '');   
    }         

    return response
}

export const config = {
    matcher: ['/((?!api|static|.*\\..*|_next|favicon.ico|robots.txt).*)'],
}