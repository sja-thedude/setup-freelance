"use client"

import variables from '/public/assets/css/menu.module.scss'
import { useI18n } from '@/locales/client'
import Link from 'next/link'
import { usePathname, useRouter } from 'next/navigation'
import _ from 'lodash'
import { useAppSelector } from '@/redux/hooks'
import { useGetWorkspaceDataByIdQuery } from '@/redux/services/workspace/workspaceDataApi'

const text = variables.text;

export default function Menu() {
    const workspaceId = useAppSelector((state) => state.workspaceData.globalWorkspaceId)
    const { data: apiDataToken } = useGetWorkspaceDataByIdQuery({ id: workspaceId })
    const apiData = apiDataToken?.data?.setting_generals;
    const color = !workspaceId ? '#B5B268' : apiData?.primary_color;
    const routerPath = usePathname()
    const router = useRouter()
    const trans = useI18n()
    let activeMenu = 'home'
    let cart = useAppSelector((state) => _.includes(routerPath, '/table-ordering') ? state.cart.data :  _.includes(routerPath, '/self-ordering') ? state.cart.selfOrderingData : state.cart.rootData)
    const convertStrToNumber = _.map(cart, 'productTotal').map(i => Number(i))
    const cartTotal = _.sum(convertStrToNumber)

    if (_.includes(routerPath, '/table-ordering/cart') || _.includes(routerPath, '/category/cart') || _.includes(routerPath, '/self-ordering/cart')) {
        activeMenu = 'cart'
    } else if (_.includes(routerPath, '/table-ordering/products') || _.includes(routerPath, '/self-ordering/products')) {
        activeMenu = 'products'
    }

    const handleOrdering = () => {
        if (routerPath.includes('table-ordering')) {
            router.push("/table-ordering")
        } else if (routerPath.includes('self-ordering')) {
            router.push("/self-ordering")
        }
    }

    const handleOrderingProducts = () => {
        if (routerPath.includes('table-ordering')) {
            router.push("/table-ordering/products")
        } else if (routerPath.includes('self-ordering')) {
            router.push("/self-ordering/products")
        }
    }

    const handleOrderingCart = () => {
        if (routerPath.includes('table-ordering')) {
            router.push("/table-ordering/cart")
        } else if (routerPath.includes('self-ordering')) {
            router.push("/self-ordering/cart")
        }
    }

    return (
        <>
            <div className={variables.container}>
                <div className={variables.home}>
                    <div style={{ textDecoration: 'none', textAlign: 'center', width: '58px' }} onClick={handleOrdering}>
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                            <path d="M4 7.65L12.5 1L21 7.65V18.1C21 18.6039 20.801 19.0872 20.4468 19.4435C20.0925 19.7998 19.6121 20 19.1111 20H5.88889C5.38792 20 4.90748 19.7998 4.55324 19.4435C4.19901 19.0872 4 18.6039 4 18.1V7.65Z" stroke={apiData && activeMenu == 'home' ? color : '#413E38'} strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" />
                            <path d="M10 20V10H15V20" stroke={apiData && activeMenu == 'home' ? color : '#413E38'} strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"
                            />
                        </svg>
                        <p className={`${text} mt-1 text-uppercase`} style={{ color: (apiData && activeMenu == 'home') ? color : '#413E38' }}>{trans('home')}</p>
                    </div>
                </div>
                <div className={variables.menu}>
                    <div style={{ textDecoration: 'none', textAlign: 'center', width: '58px' }} onClick={handleOrderingProducts}>
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="18" viewBox="0 0 20 18" fill="none">
                            <line x1="19" y1="17" x2="1" y2="17" stroke={apiData && activeMenu == 'products' ? color : '#413E38'} strokeWidth="2" strokeLinecap="round" />
                            <line x1="19" y1="9" x2="1" y2="9" stroke={apiData && activeMenu == 'products' ? color : '#413E38'} strokeWidth="2" strokeLinecap="round" />
                            <line x1="19" y1="1" x2="1" y2="1" stroke={apiData && activeMenu == 'products' ? color : '#413E38'} strokeWidth="2" strokeLinecap="round" />
                        </svg>
                        <p className={`${text} mt-1 text-uppercase ${variables.paddingTop2}`} style={{ color: (apiData && activeMenu == 'products') ? color : '#413E38' }}>{trans('menu')}</p>
                    </div>
                </div>
                <div className={`${variables.cart} mt-1`} style={{ display: 'flex', alignItems: 'center', justifyContent: 'space-between' }}>
                    {cartTotal > 0 && (
                        <span className="badge" style={{ background: color ?? '#413E38' }}>
                            {cartTotal}
                        </span>
                    )}
                    <div style={{ textDecoration: 'none', textAlign: 'center', width: '58px' }} onClick = {handleOrderingCart}>
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                            <path d="M6 2L3 6V20C3 20.5304 3.21071 21.0391 3.58579 21.4142C3.96086 21.7893 4.46957 22 5 22H19C19.5304 22 20.0391 21.7893 20.4142 21.4142C20.7893 21.0391 21 20.5304 21 20V6L18 2H6Z" stroke={apiData && activeMenu == 'cart' ? color : '#413E38'} strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" />
                            <path d="M3 6H21" stroke={apiData && activeMenu == 'cart' ? color : '#413E38'} strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" />
                            <path d="M16 10C16 11.0609 15.5786 12.0783 14.8284 12.8284C14.0783 13.5786 13.0609 14 12 14C10.9391 14 9.92172 13.5786 9.17157 12.8284C8.42143 12.0783 8 11.0609 8 10" stroke={apiData && activeMenu == 'cart' ? color : '#413E38'} strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" />
                        </svg>
                        <p className={`${text} mt-1 text-uppercase ${variables.paddingTop1}`} style={{ color: (apiData && activeMenu == 'cart') ? color : '#413E38' }}>{trans('shopping-cart')}</p>
                    </div>
                </div>
            </div>
        </>
    )
}
