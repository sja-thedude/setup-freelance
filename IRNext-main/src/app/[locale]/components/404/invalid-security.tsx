"use client";

import style from 'public/assets/css/self-service.module.scss'
import { useI18n } from '@/locales/client'
import Image from "next/image";
import { useAppSelector } from '@/redux/hooks'
import { useGetWorkspaceDataByIdQuery } from '@/redux/services/workspace/workspaceDataApi'
import variables from '/public/assets/css/intro-table.module.scss'
import { hexToRgb } from "@/utils/rgb";
import {capitalize, toLower} from "lodash";
import IntroBackground from '@/app/[locale]/components/share/introBackground';

export default function InvalidSecurity() {
    const workspaceId = useAppSelector((state) => state.workspaceData.globalWorkspaceId)
    const { data: apiDataToken } = useGetWorkspaceDataByIdQuery({ id: workspaceId })
    const color = useAppSelector((state) => state.workspaceData.globalWorkspaceColor)
    const apiPhoto = apiDataToken?.data?.photo;
    const trans = useI18n();
    let rgbColor = hexToRgb('#FFFFFF')

    if (color) {
        rgbColor = hexToRgb(color);
    }

    return (
        <>
            <div style={{ display: 'block' }}>
                <IntroBackground position="top" color={color} rgbColor={rgbColor}/>
                <div className={`${variables.heying} heying row text-center justify-content-center ps-2 pe-2`}>
                    <div className={`row justify-content-center ${variables.table_ordering_notfound}`}>
                        <div className="col-sm-12 col-xs-12">
                            <div className={`${style['logo-confirmation']}`}>
                                <Image
                                    alt='intro'
                                    src={apiPhoto ? apiPhoto : ''}
                                    width={130}
                                    height={130}
                                    style={{ borderRadius: '50%' }}
                                />
                            </div>
                        </div>
                        <div className="col-sm-12 col-xs-12">
                            <div className={`${style['title-confirmation']} lowercase mt-4`}>
                                {capitalize(toLower(trans('invalid-desktop-title')))}
                            </div>
                        </div>
                        <div className="col-sm-12 col-xs-12">
                            <div className={`${style['sub-title-confirmation']}`}>
                                {trans('security_invalid_description')}
                            </div>
                        </div>
                    </div>
                </div>
                <IntroBackground position="bottom" color={color} rgbColor={rgbColor}/>
            </div>
        </>
    )
}
