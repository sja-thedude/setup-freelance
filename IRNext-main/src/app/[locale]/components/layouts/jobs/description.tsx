'use client'

import React, { useState } from 'react';
import { useSelector } from 'react-redux';
import { useI18n } from '@/locales/client';
import { useGetWorkspaceSettingByIdQuery } from '@/redux/services/workspace/workspaceSettingApi';
import { selectWorkspaceSetting } from '@/redux/slices/workspace/workdspaceSettingSlice';
import { selectWorkspaceData } from '@/redux/slices/workspace/workspaceDataSlice';
import { useAppSelector } from '@/redux/hooks'
import { useGetWorkspaceDataByIdQuery } from '@/redux/services/workspace/workspaceDataApi'

type ReadMoreProps = {
    children: string;
}

const ReadMore = ({ children }: ReadMoreProps) => {
    const trans = useI18n()
    const workspaceId = useAppSelector((state) => state.workspaceData.globalWorkspaceId)
    const { data: apiDataToken } = useGetWorkspaceDataByIdQuery({id: workspaceId})
    const apiData = apiDataToken?.data?.setting_generals;
    const text = children ?? '';
    const [isReadMore, setIsReadMore] = useState(true);
    const toggleReadMore = () => {
        setIsReadMore(!isReadMore);
    };
    return (
        <p className="text">
            {isReadMore && text.length > 290 ? text.slice(0, 290)+" ..." : text}
            <span
                onClick={toggleReadMore}
                className="read-or-hide"
                style={{
                    color: apiData ? apiData?.primary_color : 'white',
                }}
            >
                {isReadMore  && text.length > 290 ? trans('read_more') : ''}
            </span>
        </p>

    );
};

export default function JobDescription({apiColor}:any) {
    // Get api data
    const workspaceId = useAppSelector((state) => state.workspaceData.globalWorkspaceId)

    // Get workspace setting
    const apiSliceWorkspaceSettings = useSelector(selectWorkspaceSetting);
    const { data: workspaceSettingData, isLoading: workspaceLoading, isError: workspaceError } =
        useGetWorkspaceSettingByIdQuery({ id: workspaceId });
    const workspaceSettingFinal = apiSliceWorkspaceSettings?.data?.meta || workspaceSettingData?.data?.meta;

    // Use lodash to get item from array which has item.key = 'jobs'
    const jobsItem = workspaceSettingFinal?.find((item: any) => item.key === 'jobs');

    // Get workspace data
    const apiSliceWorkspaceData = useSelector(selectWorkspaceData);
    const { data: workspaceData, isLoading: workspaceDataLoading, isError: workspaceDataError } =
        useGetWorkspaceDataByIdQuery({ id: workspaceId });
    const workspaceDataFinal = apiSliceWorkspaceData?.data || workspaceData?.data;

    // Get i18n
    const trans = useI18n();

    return (
        <div className="row">
            <div className="col-auto">
                <h2 id='job_description_title'>{jobsItem?.title}</h2>
                <div id="job_description_content">
                    {jobsItem ? <ReadMore>{jobsItem.content}</ReadMore> : ''}
                </div>
            </div>
        </div>
    );
}