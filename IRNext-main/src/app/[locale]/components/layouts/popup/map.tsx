'use client'
import { useEffect, useState, useRef, useMemo } from 'react';
import { Button, Modal } from 'react-bootstrap';
import 'public/assets/css/popup.scss';
import { useI18n } from '@/locales/client'
import variables from '/public/assets/css/home.module.scss';
import { useSelector } from "react-redux";
import { useGetWorkspaceOpenHoursByIdQuery } from "@/redux/services/workspace/workspaceOpenHoursApi";
import { selectWorkspaceOpenHours } from "@/redux/slices/workspace/workspaceOpenHoursSlice";
import { useGetWorkspaceDeliveryConditionsByIdQuery } from "@/redux/services/workspace/workspaceDeliveryConditionsApi";
import { selectWorkspaceDeliveryConditions } from "@/redux/slices/workspace/workspaceDeliveryConditionsSlice";
import Map from "../../map/page";
import Cookies from 'js-cookie';
import { api } from "@/utils/axios";

export default function Maping(props: any) {
    const { data, workspaceId, color, togglePopup, origin } = props;
    const [show, setShow] = useState(false);
    const handleClose = () => {
        togglePopup();
        setShow(false);
    };
    const [workspaceDataFinal, setWorkspaceDataFinal] = useState<any | null>(null);
    const tokenLoggedInCookie = Cookies.get('loggedToken');
    const [statusEnableTableOrdering, setStatusEnableTableOrdering] = useState<any | null>(false);
    const [statusEnableSelfOrdering, setStatusEnableSelfOrdering] = useState<any | null>(false);
    const language = Cookies.get('Next-Locale');
    const handleShow = () => setShow(true);

    useEffect(() => {
        const hasShownPopup = false;

        if (!hasShownPopup) {
            setShow(true);
        }
    }, []);

    const currency = '€';
    const apiSliceWorkspaceOpenHours = useSelector(selectWorkspaceOpenHours);
    var { data: workspaceOpenHours, isLoading: workspaceLoading, isError: workspaceError } = useGetWorkspaceOpenHoursByIdQuery({ id: workspaceId, lang: language });
    const workspaceOpenHoursFinal = apiSliceWorkspaceOpenHours?.data || workspaceOpenHours?.data;
    const apiSliceWorkspaceDC = useSelector(selectWorkspaceDeliveryConditions);
    var { data: workspaceDC, isLoading: workspaceDCLoading, isError: workspaceDCError } = useGetWorkspaceDeliveryConditionsByIdQuery({ id: workspaceId });
    const workspaceDCFinal = apiSliceWorkspaceDC?.data || workspaceDC?.data;
    const [selectedType, setSelectedType] = useState(null);

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

    if (workspaceOpenHoursFinal) {
        var isDeleveryActive = workspaceOpenHoursFinal.find((item: any) => item.type == 1 && item.active === true);
    }

    const trans = useI18n()
    const handleTagClick = (e: any, type: any) => {
        e.stopPropagation();
        setSelectedType(type);
    };

    const introRef = useRef<null | HTMLDivElement>(null);
    const introScroll = () => {
        introRef?.current?.scrollIntoView({ behavior: "instant", block: "start"});
    };

    const scrollRef = useRef<null | HTMLDivElement>(null);
    const mapRef = useRef<null | HTMLDivElement>(null);
    const [existedMap, setExistedMap] = useState(false);
    const [showMore, setShowMore] = useState(false);

    useEffect(() => {
        if (scrollRef?.current) {
            setTimeout(() => {
                const heightContent = scrollRef?.current?.clientHeight
                const heightMap = mapRef?.current?.clientHeight
                const { innerHeight } = window;
                let maxHeightContent = 90 * innerHeight / 100

                if(heightMap === 200) {
                    maxHeightContent = maxHeightContent - 240
                } else {
                    maxHeightContent = maxHeightContent - 40
                }

                if(heightContent && heightContent > maxHeightContent) {
                    setShowMore(true)
                } else {
                    setShowMore(false)
                }
            }, 500)
        }
    }, [show, existedMap])

    useEffect(() => {
        setTimeout(function () {
            workspaceId && api.get(`workspaces/` + workspaceId, {
                headers: {
                    'Authorization': `Bearer ${tokenLoggedInCookie}`,
                }
            }).then(res => {
                const json = res.data;
                setWorkspaceDataFinal(json.data);
            }).catch(error => {
                // console.log(error)
            });
        }, 1000);
    }, [workspaceId]);

    useEffect(() => {
        workspaceDataFinal?.extras.map((item: any) => {
            if (item?.type === 10) {
                if (item.active !== true) {
                    setStatusEnableTableOrdering(false)
                } else {
                    setStatusEnableTableOrdering(true)
                }
            } else if (item?.type === 12) {
                if (item.active !== true) {
                    setStatusEnableSelfOrdering(false)
                } else {
                    setStatusEnableSelfOrdering(true)
                }
            }
        });
    }, [workspaceDataFinal]);

    const filteredItems = useMemo(() => {
        return workspaceOpenHoursFinal?.filter((item: any) => {
            if (!item.active) return false;
            if (item.type === 2 && !statusEnableTableOrdering) return false;
            if (item.type === 3 && !statusEnableSelfOrdering) return false;
            return true;
        }) || [];
    }, [workspaceOpenHoursFinal, statusEnableTableOrdering, statusEnableSelfOrdering]);

    useEffect(() => {
        if (filteredItems && filteredItems.length > 0) {
            if(filteredItems[0].active === true) {
                setSelectedType(filteredItems[0].type_display);
            }
        }
    }, [workspaceOpenHoursFinal, filteredItems]);

    return (
        <>
            <Button variant="primary" onClick={handleShow} style={{ display: 'none' }}></Button>

            <Modal className={origin === 'home' ? 'home-intro-popup' : ''}
                show={show}
                onHide={handleClose}
                aria-labelledby="contained-modal-title-vcenter"
                centered id="map">
                <Modal.Header></Modal.Header>
                <Modal.Body style={{ overflowX: "hidden" }}>
                    <div className={`${variables.popupOverlay} popup-overlay`}>
                        <div className={`${variables.popupContent} popup-content`}>
                            <div className="mx-auto text-center">
                                <svg xmlns="http://www.w3.org/2000/svg" width="101" height="3" viewBox="0 0 101 3" fill="none">
                                    <path d="M2 1.5H99" stroke="#5C5C5C" strokeWidth="3" strokeLinecap="round" />
                                </svg>
                            </div>

                            <div className={`scroll-area ${!existedMap ? 'not-map' : ''}`}>
                                <div ref={scrollRef} className="scroll-wrapper">
                                    <div className="container">
                                        <div className={`${variables.name} row my-2`}>
                                            <h1>
                                                {data ? data?.setting_generals.title : ''}
                                            </h1>
                                            <div className={`${variables.addressMap}`}>
                                                {data ? data?.address : ''}
                                            </div>
                                            <div className={`${variables.btw}`}>
                                                BTW: {data ? data?.btw_nr : ''}
                                            </div>
                                        </div>
                                    </div>

                                    {isDeleveryActive ? (
                                        <div className="container">
                                            <div className={`${variables.content} mt-2 col-sm-12 col-xs-12 d-flex`} style={{ flexWrap: 'wrap' }}>
                                                <div className="col-sm-8 col-xs-8">
                                                    <p className={variables.condition}>{trans('shipping-fee')}:</p>
                                                </div>
                                                <div className="col-sm-4 col-xs-4 text-end">
                                                    <p className={variables.price} style={{ color: color }}>
                                                        {workspaceDCFinal ? (workspaceDCFinal.price == 0 ? trans("free") : currency + ' ' + workspaceDCFinal.price) : ''}
                                                    </p>
                                                </div>
                                            </div>

                                            <div className={`${variables.content} col-sm-12 col-xs-12 d-flex`} style={{ flexWrap: 'wrap' }}>
                                                <div className="col-sm-8 col-xs-8">
                                                    <p className={variables.condition}>{trans('min-order')}:</p>
                                                </div>
                                                <div className="col-sm-4 col-xs-4">
                                                    <p className={variables.price} style={{ color: color }}>{currency} {workspaceDCFinal ? workspaceDCFinal.price_min : ''}</p>
                                                </div>
                                            </div>
                                            <div className={`${variables.content} mb-3 col-sm-12 col-xs-12 d-flex`} style={{ flexWrap: 'wrap' }}>
                                                <div className="col-sm-8 col-xs-8">
                                                    <p className={variables.condition}>{trans('min-waiting')}:</p>
                                                </div>
                                                <div className="col-sm-4 col-xs-4">
                                                    <p className={variables.price} style={{ color: color }}>{workspaceDCFinal ? workspaceDCFinal.delivery_min_time : ''} {trans('lang_min')}.</p>
                                                </div>
                                            </div>
                                        </div>
                                    ) : null}

                                    <div className="container">
                                        <div className={`${variables.tags} d-flex`} style={{ marginTop: !isDeleveryActive ? '25px' : '' }}>
                                            {filteredItems && filteredItems.filter((item: any) => item?.active === true).map((item: any, index: number, array: any) => (
                                                <h6
                                                    key={index}
                                                    className={`tag px-0 ${selectedType === item.type_display ? `${variables.active}` : ''}`}
                                                    style={{
                                                        color: selectedType === item.type_display ? color : '',
                                                        visibility: item.active === true ? 'visible' : 'hidden',
                                                        textAlign: (array.length) == 2 ? 'left' : 'center',
                                                        width: (array.length) == 2 ? '50%' : ''
                                                    }}
                                                    onClick={(e) => handleTagClick(e, item.type_display)}
                                                >
                                                    {item.type_display}
                                                </h6>
                                            ))}
                                        </div>
                                        <div className={`${variables.timslots} time-slots`}>
                                            {selectedType && filteredItems &&
                                                filteredItems
                                                    .find((item: any) => item.type_display === selectedType)
                                                    ?.timeslots.map((timeslot: any, index: any) => {
                                                        // Take value day_number_display of previous (nếu có)
                                                        const previousDayNumberDisplay = index > 0
                                                                ? filteredItems.find(
                                                                    (item: any) => item.type_display === selectedType
                                                                ).timeslots[index - 1].day_number_display
                                                                : null;
                                                        return (
                                                            <div className={`${variables.timeslot} row`} key={index}>
                                                                <div className={`${variables.timenn} col-sm-6 col-6 text-lowercase`}>
                                                                    {timeslot.day_number_display !== previousDayNumberDisplay ?
                                                                        translateWeek(timeslot.day_number_display.charAt(0).toUpperCase() + timeslot.day_number_display.slice(1)) :
                                                                        ''
                                                                    }
                                                                </div>
                                                                <div className={`${variables.timese} col-sm-6 col-6`}>
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
                                    </div>
                                </div>

                                {showMore && (
                                    <div className="show-more-toggle text-center"
                                        style={{ color: color, fontSize: '16px' }}
                                        onClick={introScroll}>
                                        <span>{trans('show_more')}</span>
                                        <svg width="12" height="8" viewBox="0 0 12 8" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M5.41602 6.95898L0.541016 2.08398C0.210938 1.7793 0.210938 1.24609 0.541016 0.941406C0.845703 0.611328 1.37891 0.611328 1.68359 0.941406L6 5.23242L10.291 0.941406C10.5957 0.611328 11.1289 0.611328 11.4336 0.941406C11.7637 1.24609 11.7637 1.7793 11.4336 2.08398L6.55859 6.95898C6.25391 7.28906 5.7207 7.28906 5.41602 6.95898Z"
                                                fill={color ? color : "#A3AED0"} />
                                        </svg>
                                    </div>
                                )}
                                
                                <div ref={introRef} className="hidden"></div>
                            </div>

                            <div ref={mapRef}>
                                <Map data={data} setExistedMap={setExistedMap} />
                            </div>
                        </div>
                    </div>
                </Modal.Body>
            </Modal>
        </>
    );
}
