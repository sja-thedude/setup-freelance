'use client'

import style from 'public/assets/css/self-service.module.scss'
import { useI18n } from '@/locales/client'
import {useRouter} from "next/navigation";
import { useAppSelector } from '@/redux/hooks'
import { useGetWorkspaceDataByIdQuery } from '@/redux/services/workspace/workspaceDataApi'
import variables from '/public/assets/css/intro-table.module.scss'
import { hexToRgb } from "@/utils/rgb";
import IntroBackground from '@/app/[locale]/components/share/introBackground';
export default function Failed() {
    // Get workspace info
    const workspaceId = useAppSelector((state) => state.workspaceData.globalWorkspaceId)
    const { data: apiDataToken } = useGetWorkspaceDataByIdQuery({id: workspaceId})
    const apiData = apiDataToken?.data?.setting_generals;
    const color = useAppSelector((state) => state.workspaceData.globalWorkspaceColor)
    const trans = useI18n();
    const router = useRouter();
    let rgbColor = hexToRgb('#FFFFFF')

    if (color) {
        rgbColor = hexToRgb(color);
    }

    return (
        <>
            <div style={{ display: 'block' }}>
                <IntroBackground position="top" color={color} rgbColor={rgbColor}/>
                <div className={`${variables.heying} heying row  text-center justify-content-center ps-2 pe-2`}>
                    <div className={`row justify-content-center ${variables.table_ordering_notfound}`}>
                        <div className="col-sm-12 col-xs-12">
                            <div className={`${style['title-confirmation']}`}>
                                {trans('problem-occurred')}
                            </div>
                        </div>
                        <div className="col-sm-12 col-xs-12">
                            <div className={`${style['sub-title-confirmation']}`}>
                                {trans('problem-occurred-and-report')}
                            </div>
                        </div>
                        <div className="col-sm-12 col-xs-12">
                            <div className={`${style['btn-confirmation']}`}
                                 onClick={() => {
                                     history.pushState({activeStep: 4}, "reorder", "/table-ordering/cart?activeStep=4");
                                     router.push('/table-ordering/cart?activeStep=4')
                                 }}>
                                {trans('try-again')}
                            </div>
                        </div>
                    </div>
                </div>
                <IntroBackground position="bottom" color={color} rgbColor={rgbColor}/>
            </div>
        </>
    )
}