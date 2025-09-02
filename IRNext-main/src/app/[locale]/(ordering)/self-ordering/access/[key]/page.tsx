import { headers } from 'next/headers'
import * as globalConfig from '@/config/constants'
import Access from '@/app/[locale]/components/ordering/table-ordering/access'

export default async function Page({
    params
}: {
    params: { locale: string, key: string }
}) {
    const workspaceId = headers().get('x-next-workspace')
    const validation = await fetch(`${globalConfig.API_URL}workspaces/${workspaceId}/validate-order-access-key`, {
        cache: 'no-store',
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Content-Language': params?.locale
        },
        body: JSON.stringify({ access_key: params?.key })
    });

    const response = await validation.json();

    return (
        response && <Access valid={response?.data?.valid ?? false} origin="self-ordering"/>
    )
}
