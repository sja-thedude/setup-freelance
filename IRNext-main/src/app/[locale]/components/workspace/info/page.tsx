// Popup component
'use client'
import React, { useState, useEffect } from 'react';
import variables from '/public/assets/css/home.module.scss';
import { useI18n } from '@/locales/client'
import { useSelector } from "react-redux";
import { useGetWorkspaceOpenHoursByIdQuery } from "@/redux/services/workspace/workspaceOpenHoursApi";
import { selectWorkspaceOpenHours } from "@/redux/slices/workspace/workspaceOpenHoursSlice";
import { useGetWorkspaceDeliveryConditionsByIdQuery } from "@/redux/services/workspace/workspaceDeliveryConditionsApi";
import { selectWorkspaceDeliveryConditions } from "@/redux/slices/workspace/workspaceDeliveryConditionsSlice";
import Map from "../../map/page";
import Cookies from 'js-cookie';

export default function Popup({ data, workspaceId, isPopupOpen, togglePopup, color }: any) {
    const currency = '€';
    const language = Cookies.get('Next-Locale');
    const apiSliceWorkspaceOpenHours = useSelector(selectWorkspaceOpenHours);
    var { data: workspaceOpenHours, isLoading: workspaceLoading, isError: workspaceError } = useGetWorkspaceOpenHoursByIdQuery({ id: workspaceId, lang: language });
    const workspaceOpenHoursFinal = apiSliceWorkspaceOpenHours?.data || workspaceOpenHours?.data;

    const apiSliceWorkspaceDC = useSelector(selectWorkspaceDeliveryConditions);
    var { data: workspaceDC, isLoading: workspaceDCLoading, isError: workspaceDCError } = useGetWorkspaceDeliveryConditionsByIdQuery({ id: workspaceId });
    const workspaceDCFinal = apiSliceWorkspaceDC?.data || workspaceDC?.data;

    const [selectedType, setSelectedType] = useState(null);

    function translateType(type: any) {
        switch (type) {
            case 'Takeout':
                return trans('take-out');
            case 'Delivery':
                return trans('delivery');
            case 'In-house':
                return trans('in-house');
            default:
                return type;
        }
    }

    function translateWeek(type: any) {
        switch (type) {
            case 'maandag':
                return trans('monday');
            case 'dinsdag':
                return trans('tuesday');
            case 'woensdag':
                return trans('wednesday');
            case 'donderdag':
                return trans('thursday');
            case 'vrijdag':
                return trans('friday');
            case 'zaterdag':
                return trans('saturday');
            case 'zondag':
                return trans('sunday');
            default:
                return type;
        }
    }

    useEffect(() => {
        if (workspaceOpenHoursFinal && workspaceOpenHoursFinal.length > 0) {
            if (workspaceOpenHoursFinal[0].active === true) {
                setSelectedType(workspaceOpenHoursFinal[0].type_display);
            }
        }
    }, [workspaceOpenHoursFinal]);

    const handleTagClick = (e: any, type: any) => {
        e.stopPropagation();
        setSelectedType(type);
    };

    const trans = useI18n()
    return (
        <>
            {isPopupOpen && (
                <div className={variables.popupOverlay} onClick={togglePopup}>
                    <div className={variables.popupContent}>
                        <div className={`${variables.name} row mt-2 my-2 mx-2`}>
                            <h1>
                                {data ? data?.setting_generals.title : ''}
                            </h1>
                            <div className={`${variables.addressMap}`}>
                                {data ? data?.address : ''}
                            </div>
                            <div className={`${variables.btw}`}>
                                {trans('lang_btw')}: {data ? data?.btw_nr : ''}
                            </div>
                        </div>

                        <div className='row mt-2 mx-3'>
                            <div className={`${variables.content} col-sm-12 col-xs-12 d-flex`} style={{ flexWrap: 'wrap' }}>
                                <div className="col-sm-8 col-xs-8">
                                    <p className={variables.condition}>{trans('shipping-fee')}:</p>
                                </div>
                                <div className="col-sm-4 col-xs-4 text-end ">
                                    <p className={variables.price} style={{ color: color }}>
                                        {workspaceDCFinal ? (workspaceDCFinal.price == 0 ? trans("free") : currency + ' ' + workspaceDCFinal.price) : ''}
                                    </p>

                                </div>
                            </div>
                        </div>

                        <div className='row mx-3'>
                            <div className={`${variables.content} col-sm-12 col-xs-12 d-flex`} style={{ flexWrap: 'wrap' }}>
                                <div className="col-sm-8 col-xs-8"> <p className={variables.condition}>{trans('min-order')}: </p></div>
                                <div className="col-sm-4 col-xs-4"> <p className={variables.price} style={{ color: color }}>{currency} {workspaceDCFinal ? workspaceDCFinal.price_min : ''}</p></div>
                            </div>
                        </div>
                        <div className='row mb-3 mx-3'>
                            <div className={`${variables.content} col-sm-12 col-xs-12 d-flex`} style={{ flexWrap: 'wrap' }}>
                                <div className="col-sm-8 col-xs-8"> <p className={variables.condition}>{trans('min-waiting')}:</p></div>
                                <div className="col-sm-4 col-xs-4"> <p className={variables.price} style={{ color: color }}>{workspaceDCFinal ? workspaceDCFinal.delivery_min_time : ''} min.</p></div>
                            </div>
                        </div>

                        <div>
                            {workspaceOpenHoursFinal && (
                                <div className={`${variables.tags} d-flex`}>
                                    {workspaceOpenHoursFinal.map((item: any, index: number) => (
                                        <h6
                                            key={index}
                                            className={`tag mx-1 ${selectedType === item.type_display ? `${variables.active}` : ''}`}
                                            style={{ color: selectedType === item.type_display ? color : '' }}
                                            onClick={(e) => handleTagClick(e, item.type_display)}
                                        >
                                            {translateType(item.type_display)}
                                        </h6>
                                    ))}
                                </div>
                            )}
                            {selectedType && workspaceOpenHoursFinal && (
                                <div className={`${variables.timslots}`}>
                                    {workspaceOpenHoursFinal
                                        .find((item: any) => item.type_display === selectedType)
                                        ?.timeslots.map((timeslot: any, index: any) => {
                                            // Take value day_number_display of previous (nếu có)
                                            const previousDayNumberDisplay =
                                                index > 0 ? workspaceOpenHoursFinal[0].timeslots[index - 1].day_number_display : null;
                                            return (
                                                <div className={`${variables.timeslot} row`} key={index}>
                                                    <div className={`${variables.timenn} col-sm-3 col-3`}>
                                                        {timeslot.day_number_display !== previousDayNumberDisplay ?
                                                            translateWeek(timeslot.day_number_display.charAt(0).toUpperCase() + timeslot.day_number_display.slice(1)) :
                                                            ''
                                                        }
                                                    </div>
                                                    <div className={`${variables.timese} col-sm-7 col-7`}>
                                                        {timeslot.start_time && timeslot.end_time ?
                                                            `${timeslot.start_time.split(':').slice(0, 2).join(':')} - ${timeslot.end_time.split(':').slice(0, 2).join(':')}` :
                                                            trans('stolen')
                                                        }
                                                    </div>
                                                </div>
                                            );
                                        })
                                    }
                                </div>
                            )}
                        </div>
                        <Map data={data}></Map>
                    </div>
                </div>
            )}
        </>
    );
}
