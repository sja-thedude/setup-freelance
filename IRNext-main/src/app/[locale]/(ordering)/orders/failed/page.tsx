'use client'

import variables from '/public/assets/css/notfound.module.scss'
import { useRouter } from 'next/navigation'
import { useI18n } from '@/locales/client'
import { useAppSelector } from '@/redux/hooks'
import { useGetWorkspaceDataByIdQuery } from '@/redux/services/workspace/workspaceDataApi'

const header = variables.header;
const icon = variables["center-text"];
const warning = variables.warning;
const disciption = variables.disciption;
const containButton = variables.containButton;
const btnDark = `btn btn-dark ${variables['btn-dark']}`;

export default function NotFound() {
    const trans = useI18n()    
    // Get workspace info
    const workspaceId = useAppSelector((state) => state.workspaceData.globalWorkspaceId)
    const { data: apiDataToken } = useGetWorkspaceDataByIdQuery({id: workspaceId})
    const apiData = apiDataToken?.data?.setting_generals;
    const color = useAppSelector((state) => state.workspaceData.globalWorkspaceColor)
    const router = useRouter()

    return (
        <div className="container">
            <div className='row'>
                <div className={`${header}`} style={{ backgroundColor: color ? color : 'white' }}>
                    <div>{trans('failed')}</div>
                </div>
                <div className={`${icon}`}>
                    <svg xmlns="http://www.w3.org/2000/svg" width="113" height="110" viewBox="0 0 113 110" fill="none">
                        <path d="M102.76 10L10 100" stroke="url(#paint0_linear_297_8794)" strokeWidth="20" strokeLinecap="round" strokeLinejoin="round" />
                        <path d="M10 10L102.76 100" stroke="url(#paint1_linear_297_8794)" strokeWidth="20" strokeLinecap="round" strokeLinejoin="round" />
                        <defs>
                            <linearGradient id="paint0_linear_297_8794" x1="56.3799" y1="10" x2="56.3799" y2="100" gradientUnits="userSpaceOnUse">
                                <stop stopColor="#EE0C0C" />
                                <stop offset="1" stopColor="#911515" />
                            </linearGradient>
                            <linearGradient id="paint1_linear_297_8794" x1="56.3799" y1="10" x2="56.3799" y2="100" gradientUnits="userSpaceOnUse">
                                <stop stopColor="#EE0C0C" />
                                <stop offset="1" stopColor="#911515" />
                            </linearGradient>
                        </defs>
                    </svg>
                </div>
                <div className={`${warning}`}><h1>{trans('invalid-order')}</h1></div>
                <div className={`${disciption} d-flex justify-content-center`}><p>{trans('try-again-order')}</p></div>
                <div className={`${containButton} d-flex justify-content-center`} onClick = {() => {router.push('/category/cart?activeStep=3')}}><div className={`${btnDark}`}>{trans('back')}</div></div>
            </div>
        </div>
    )
}
