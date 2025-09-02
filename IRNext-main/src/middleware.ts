import { createI18nMiddleware } from 'next-international/middleware'
import { NextRequest, NextResponse } from 'next/server'
import * as locales from '@/config/locales'
import * as globalConfig from '@/config/constants'

const I18nMiddleware = createI18nMiddleware(
    locales.LOCALES ?? [] as const,
    locales.LOCALE_FALLBACK,
    {
        resolveLocaleFromRequest: () => locales.LOCALE_FALLBACK
    }
)

export async function middleware(request: NextRequest) {
    const response = I18nMiddleware(request)
    const pageUrl = request.url
    const configPortalHost = (globalConfig.PORTAL_WEBSITE ?? '').trim()
    const domain = request.headers.get('host')
    const requestDomain = request.headers.get('x-forwarded-host')

    // Case 1: Portal domain
    if ((domain && domain === configPortalHost) || (requestDomain && requestDomain === configPortalHost)) {
        response.headers.set('x-next-workspace', '')
        response.headers.set('x-next-workspace-token', '')
        response.headers.set('x-next-workspace-color', '')

    // Case 2: Workspace token is missing
    } else if (!globalConfig.WORKSPACE_TOKEN) {
        let domainReplace = requestDomain ?? domain ?? ''

        if (globalConfig.EXCEPT_DOMAIN?.trim()) {
            domainReplace = domainReplace.replace(globalConfig.EXCEPT_DOMAIN + '.', '')
        }

        const domainSplit = domainReplace.split('.')
        let flag = false

        if (domainSplit.length > 1) {
            const slugWorkSpace = domainSplit[0]

            let workspace: any = null
            try {
                const getWorkspace = await fetch(
                    `${globalConfig.API_URL}workspaces/domain/${slugWorkSpace}`,
                    { cache: 'no-store' }
                )
                if (getWorkspace.ok) {
                    workspace = await getWorkspace.json()
                } else {
                    console.error('Workspace fetch failed with status:', getWorkspace.status)
                }
            } catch (err) {
                console.error('Fetch error (domain workspace):', err)
            }

            response.headers.set('x-next-workspace', workspace?.data?.id ?? '')
            response.headers.set('x-next-workspace-token', workspace?.data?.token ?? '')
            response.headers.set(
                'x-next-workspace-color',
                workspace?.data?.setting_generals?.primary_color ?? ''
            )

            if (workspace?.data?.id && workspace?.data?.token) flag = true
        }

        if (!flag && !pageUrl.includes('404')) {
            return NextResponse.redirect(new URL('/404', request.url))
        }

    // Case 3: Workspace token exists
    } else {
        let workspace: any = null
        try {
            const getWorkspace = await fetch(
                `${globalConfig.API_URL}workspaces/token/${globalConfig.WORKSPACE_TOKEN}`,
                { cache: 'no-store' }
            )
            if (getWorkspace.ok) {
                workspace = await getWorkspace.json()
            } else {
                console.error('Workspace fetch failed with status:', getWorkspace.status)
            }
        } catch (err) {
            console.error('Fetch error (token workspace):', err)
        }

        response.headers.set('x-next-workspace', workspace?.data?.id ?? '')
        response.headers.set('x-next-workspace-token', workspace?.data?.token ?? String(globalConfig.WORKSPACE_TOKEN ?? ''))
        response.headers.set(
            'x-next-workspace-color',
            workspace?.data?.setting_generals?.primary_color ?? ''
        )
    }

    return response
}

export const config = {
    matcher: ['/((?!api|static|.*\\..*|_next|favicon.ico|robots.txt).*)'],
}