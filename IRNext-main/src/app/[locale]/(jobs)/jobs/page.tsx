'use client'
import React from 'react';
import Header from '@/app/[locale]/components/layouts/jobs/header';
import JobForm from '@/app/[locale]/components/layouts/jobs/form';
import JobDescription from '@/app/[locale]/components/layouts/jobs/description';
import '@/app/[locale]/components/layouts/jobs/style.scss';
import { useAppSelector } from '@/redux/hooks';
import { useGetWorkspaceDataByIdQuery } from '@/redux/services/workspace/workspaceDataApi';

export default function Jobs() {
    const workspaceId = useAppSelector((state) => state.workspaceData.globalWorkspaceId)
    const { data: apiDataToken } = useGetWorkspaceDataByIdQuery({id: workspaceId})
    const apiData = apiDataToken?.data?.setting_generals;
    
    return (
        <>
            <div id="job_container">
                <Header apiColor={apiData ? apiData?.primary_color : 'white'}/>
                <div id="job_content" className="container-fluid">
                    <JobDescription apiColor={apiData ? apiData?.primary_color : 'white'} />
                    <JobForm apiColor={apiData ? apiData?.primary_color : 'white'}/>
                </div>
            </div>
        </>
    );
}
