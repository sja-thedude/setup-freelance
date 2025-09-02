import style from 'public/assets/css/cart.module.scss'
import Menu from '@/app/[locale]/components/menu/menu'
import { getCouponsList } from '@/services/coupon'
import { getWorkspaceById } from '@/services/workspace'
import { headers } from 'next/headers'
import TableSelfOrderingCart from '@/app/[locale]/components/ordering/cart/tableSelfOrderingCart'

export default async function Page() {
    const workspaceId = headers().get('x-next-workspace');
    const apiData = await getWorkspaceById({ id: workspaceId });
    const color = apiData?.setting_generals?.primary_color;
    const coupons = await getCouponsList({ workspaceId: apiData?.id });

    return (
        <div className={`cart-container table-self-ordering ${style.cart}`}>
            <TableSelfOrderingCart
                origin='self_ordering'
                color={color}
                coupons={coupons}
                workspace={apiData}
                workspaceId={apiData?.id ? apiData?.id : ''}/>

            <div style={{ position: 'fixed', bottom: 0, left: 0, width: '100%', zIndex: 100 }}>
                <Menu />
            </div>
        </div>
    )
}