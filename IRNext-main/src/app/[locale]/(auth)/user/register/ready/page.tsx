'use client'
import React from 'react';
import variables from '/public/assets/css/register.module.scss'
import { useI18n } from '@/locales/client'
import Redirect from "../../../../components/redirect/page";
import { useAppSelector } from '@/redux/hooks'
import { useGetWorkspaceDataByIdQuery } from '@/redux/services/workspace/workspaceDataApi'

export default function Register() {
    const workspaceId = useAppSelector((state:any) => state.workspaceData.globalWorkspaceId)
    const { data: apiDataToken } = useGetWorkspaceDataByIdQuery({id: workspaceId})
    const workspaceInfo = apiDataToken?.data;
    const apiData = workspaceInfo?.setting_generals;
    const color = useAppSelector((state) => state.workspaceData.globalWorkspaceColor)
    const trans = useI18n();
    
    return (
        <div style = {{backgroundColor: workspaceId ? color : '#B5B268', minHeight: '100vh', width: '100%'}}>
            <div className='row'>
                <div className="row mt-4">
                    <div className="col-sm-12 col-xs-12 d-flex align-items-center" style={{marginLeft: "20px"}}>
                       <Redirect from = {null} />
                    </div>
                </div>
                <div className='row' style={{marginTop:"15%"}}>
                    <div className='col-sm-12 col-xs-12' style={{ margin: "auto" }}> <h1 className={`${variables.register} ms-3`}>{trans('register-almost')}</h1></div>
                </div>
                <div className={`${variables.title} row mb-2 mt-1`}><p>{trans('register-check')}</p></div>
                <div className='row' style = {{marginBottom: '450px'}}>
                    <svg xmlns="http://www.w3.org/2000/svg" width="111" height="101" viewBox="0 0 111 101" fill="none">
                        <path d="M15.6931 38.3904C14.5981 42.3356 14.0447 46.4087 14.0479 50.5C14.0479 75.6297 34.6314 96 60.0241 96C85.4168 96 106 75.6297 106 50.5C106 25.3704 85.4206 5.00004 60.0241 5.00004C47.9294 4.98448 36.3188 9.70078 27.7229 18.121" stroke="white" strokeWidth="10" strokeMiterlimit="22.93" strokeLinejoin="round" />
                        <path fillRule="evenodd" clipRule="evenodd" d="M0 49.0886H29.434L14.7177 30.5111L0 49.0886Z" fill="white" />
                        <path d="M38.793 43.8031L60.1159 70.2685L102.826 11.9745" stroke="white" strokeWidth="10" strokeMiterlimit="22.93" strokeLinejoin="round" />
                        <path d="M38.793 43.8031L60.1159 70.2685L102.826 11.9745" stroke="white" strokeWidth="10" strokeMiterlimit="22.93" strokeLinejoin="round" />
                    </svg>
                </div>
            </div>
        </div>
    );
};
