"use client";
import { useEffect, useState, useMemo, memo } from "react";
import { Button, Modal } from "react-bootstrap";
import "public/assets/css/popup.scss";
import { useI18n } from "@/locales/client";
import variables from "/public/assets/css/home.module.scss";
import { useSelector } from "react-redux";
import { useGetWorkspaceOpenHoursByIdQuery } from "@/redux/services/workspace/workspaceOpenHoursApi";
import { selectWorkspaceOpenHours } from "@/redux/slices/workspace/workspaceOpenHoursSlice";
import { useGetWorkspaceDeliveryConditionsByIdQuery } from "@/redux/services/workspace/workspaceDeliveryConditionsApi";
import { selectWorkspaceDeliveryConditions } from "@/redux/slices/workspace/workspaceDeliveryConditionsSlice";
import Map from "../../map/page";
import Cookies from "js-cookie";
import styled from "styled-components";
import { api } from "@/utils/axios";
import { useAppSelector } from "@/redux/hooks";
import { useGetWorkspaceDataByIdQuery } from "@/redux/services/workspace/workspaceDataApi";

function LoadingSpinner() {
  const trans = useI18n();
  const workspaceId = useAppSelector(
    (state) => state.workspaceData.globalWorkspaceId
  );
  const { data: apiDataToken } = useGetWorkspaceDataByIdQuery({
    id: workspaceId,
  });
  const apiData = apiDataToken?.data?.setting_generals;
  return (
    <div
      className={`spinner-border mx-auto ${variables.centeredDiv}`}
      style={{ color: apiData ? apiData?.primary_color : "rgba(0, 0, 0, 0.5)" }}
      role="status"
    >
      <span className="sr-only">{trans('lang_loading')}...</span>
    </div>
  );
}

const CustomScrollbar = styled.div`
  max-height: ${screen.height * 0.85 > 570 ? "85vh" : "570px"};
  overflow-y: auto;
  overflow-x: hidden;
  position: relative;
  border-radius: 15px;
  box-shadow: 0px 0px 30px 0px #00000033;

  scrollbar-color: ${(props) => props.color} #e3e3e3; /* Màu của thanh cuộn và phần track */
  scrollbar-width: thin; /* Độ rộng của thanh cuộn */

  &::-webkit-scrollbar {
    width: 5px;
    height: 10px;
  }

  /* Handle khi rê chuột qua */
  &::-webkit-scrollbar-thumb {
    background: ${(props) => props.color};
  }

  /* Track */
  &::-webkit-scrollbar-track {
    background: #e3e3e3;
  }

  /* Handle khi hover */
  &::-webkit-scrollbar-thumb:hover {
    background: #555;
  }
`;

const Maping = memo(
  ({
    data,
    workspaceId,
    color,
    apiData,
  }: {
    data: any;
    workspaceId: any;
    color: any;
    apiData: any;
  }) => {
    const trans = useI18n();
    const [show, setShow] = useState(false);
    const language = Cookies.get("Next-Locale");
    const [workspaceDataFinal, setWorkspaceDataFinal] = useState<any | null>(
      null
    );
    const handleShow = () => setShow(true);
    const tokenLoggedInCookie = Cookies.get("loggedToken");
    const [statusEnableTableOrdering, setStatusEnableTableOrdering] = useState<
      any | null
    >(false);
    const [statusEnableSelfOrdering, setStatusEnableSelfOrdering] = useState<
      any | null
    >(false);
    const [loading, setLoading] = useState(true);
    const [selectedType, setSelectedType] = useState(null);

    useEffect(() => {
      // const hasShownPopup = localStorage.getItem('hasShownPopup');
      const hasShownPopup = false;

      if (!hasShownPopup) {
        setShow(true);
      }
    }, []);

    const currency = "€";
    const apiSliceWorkspaceOpenHours = useSelector(selectWorkspaceOpenHours);
    var {
      data: workspaceOpenHours,
      isLoading: workspaceLoading,
      isError: workspaceError,
    } = useGetWorkspaceOpenHoursByIdQuery({ id: workspaceId, lang: language });
    const workspaceOpenHoursFinal =
      apiSliceWorkspaceOpenHours?.data || workspaceOpenHours?.data;

    const apiSliceWorkspaceDC = useSelector(selectWorkspaceDeliveryConditions);
    var {
      data: workspaceDC,
      isLoading: workspaceDCLoading,
      isError: workspaceDCError,
    } = useGetWorkspaceDeliveryConditionsByIdQuery({ id: workspaceId });
    const workspaceDCFinal = apiSliceWorkspaceDC?.data || workspaceDC?.data;

    function translateWeek(type: any) {
      switch (type) {
        case "maandag":
          return trans("monday");
        case "dinsdag":
          return trans("tuesday");
        case "woensdag":
          return trans("wednesday");
        case "donderdag":
          return trans("thursday");
        case "vrijdag":
          return trans("friday");
        case "zaterdag":
          return trans("saturday");
        case "zondag":
          return trans("sunday");
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

    if (workspaceOpenHoursFinal) {
      var isDeleveryActive = workspaceOpenHoursFinal.find(
        (item: any) => item.type == 1 && item.active === true
      );
    }

    const handleTagClick = (e: any, type: any) => {
      setSelectedType(type);
    };

    useEffect(() => {
      setTimeout(function () {
        workspaceId &&
          api
            .get(`workspaces/` + workspaceId, {
              headers: {
                Authorization: `Bearer ${tokenLoggedInCookie}`,
                'Content-Language': language
              },
            })
            .then((res) => {
              const json = res.data;
              setWorkspaceDataFinal(json.data);
              setLoading(false);
            })
            .catch((error) => {
              // console.log(error)
              setLoading(false);
            });
      }, 1000);
    }, [workspaceId]);

    useEffect(() => {
      workspaceDataFinal?.extras.map((item: any) => {
        if (item?.type === 10) {
          if (item.active !== true) {
            setStatusEnableTableOrdering(false);
          } else {
            setStatusEnableTableOrdering(true);
          }
        } else if (item?.type === 12) {
          if (item.active !== true) {
            setStatusEnableSelfOrdering(false);
          } else {
            setStatusEnableSelfOrdering(true);
          }
        }
      });
    }, [workspaceDataFinal]);

    const filteredItems = useMemo(() => {
      return (
        workspaceOpenHoursFinal?.filter((item: any) => {
          if (!item.active) return false;
          if (item.type === 2 && !statusEnableTableOrdering) return false;
          if (item.type === 3 && !statusEnableSelfOrdering) return false;
          return true;
        }) || []
      );
    }, [
      workspaceOpenHoursFinal,
      statusEnableTableOrdering,
      statusEnableSelfOrdering,
    ]);

    useEffect(() => {
      if (filteredItems.length > 0) {
        setSelectedType(filteredItems[0].type_display);
      }
    }, [filteredItems]);

    return (
      <>
        <div className={variables.popupMapHeader}>
          <div
            className={variables.top}
            style={{
              backgroundColor: apiData ? apiData?.primary_color : "#D87833",
            }}
          ></div>
          <CustomScrollbar color={color ? color : "#413E38"}>
            <div className={variables.popupMapHeaderContent}>
              <div
                className={variables.popupMapHeaderGroup}
                style={{
                  backgroundColor: apiData ? apiData?.primary_color : "#D87833",
                }}
              >
                <div className={`${variables.name} row mt-2 my-2 mx-2`}>
                  <h1>{data ? data?.setting_generals.title : ""}</h1>
                  <div className={`${variables.addressMap}`}>
                    {data ? data?.address : ""}
                  </div>
                  <div
                    className={`${variables.btw}`}
                    style={{ marginBottom: "0px" }}
                  >
                    {trans('lang_btw')}: {data ? data?.btw_nr : ""}
                  </div>
                  <div className={`${variables.btw}`}>
                    {trans('lang_tel')}: {data ? data?.gsm : ""}
                  </div>
                </div>

                {isDeleveryActive ? (
                  <div className={`${variables.delevery}`}>
                    <div className="row">
                      <div
                        className={`${variables.content}`}
                        style={{ flexWrap: "wrap" }}
                      >
                        <p className={variables.condition}>
                          {trans("shipping-fee")}:
                        </p>
                        <p className={variables.price} style={{ color: color }}>
                          {workspaceDCFinal
                            ? workspaceDCFinal.price == 0
                              ? trans("free")
                              : currency + " " + workspaceDCFinal.price
                            : ""}
                        </p>
                      </div>
                    </div>

                    <div className="row">
                      <div
                        className={`${variables.content}`}
                        style={{ flexWrap: "wrap" }}
                      >
                        <p className={variables.condition}>
                          {trans("min-order")}:
                        </p>
                        <p className={variables.price} style={{ color: color }}>
                          {currency}{" "}
                          {workspaceDCFinal ? workspaceDCFinal.price_min : ""}
                        </p>
                      </div>
                    </div>

                    <div className="row">
                      <div
                        className={`${variables.content}`}
                        style={{ flexWrap: "wrap" }}
                      >
                        <p className={variables.condition}>
                          {trans("min-waiting")}:
                        </p>
                        <p className={variables.price} style={{ color: color }}>
                          {workspaceDCFinal
                            ? workspaceDCFinal.delivery_min_time
                            : ""}{" "}
                          {trans('lang_min')}.
                        </p>
                      </div>
                    </div>
                  </div>
                ) : null}
              </div>

              <div>
                <div
                  className={`${variables.popupMapHeaderIframe} popupMapHeaderIframe`}
                >
                  <Map data={data}></Map>
                </div>
                {loading ? (
                  <div
                    className="d-flex justify-content-center"
                    style={{ width: "100%" }}
                  >
                    <LoadingSpinner />
                  </div>
                ) : (
                  <>
                    {filteredItems.length > 0 && (
                      <div
                        className={`${variables.tags} d-flex`}
                        style={{
                          marginTop: !isDeleveryActive ? "25px" : "",
                          paddingLeft: "20px",
                          paddingRight: "20px",
                        }}
                      >
                        {filteredItems.map(
                          (item: any, index: any, array: any) => (
                            <h6
                              key={index}
                              className={`tag ${variables.h6} ${
                                selectedType === item.type_display
                                  ? `${variables.active}`
                                  : ""
                              }`}
                              style={{
                                color:
                                  selectedType === item.type_display
                                    ? color
                                    : "",
                                visibility:
                                  item.active === true ? "visible" : "hidden",
                                textAlign:
                                  array.length === 2 ? "left" : "center",
                                width: array.length === 2 ? "50%" : "",
                              }}
                              onClick={(e) =>
                                handleTagClick(e, item.type_display)
                              }
                            >
                              {item.type_display}
                            </h6>
                          )
                        )}
                      </div>
                    )}
                    {selectedType && filteredItems && (
                      <div
                        className={`${variables.timslots} ${variables.timslots_new}`}
                        style={{ paddingLeft: "20px", paddingRight: "20px" }}
                      >
                        {filteredItems
                          .find(
                            (item: any) => item.type_display === selectedType
                          )
                          ?.timeslots.map((timeslot: any, index: any) => {
                            // Take value day_number_display of previous (nếu có)
                            const previousDayNumberDisplay = index > 0
                                ? filteredItems.find(
                                    (item: any) => item.type_display === selectedType
                                  ).timeslots[index - 1].day_number_display
                                : null;
                            return (
                              <div
                                className={`${variables.timeslot} row`}
                                key={index}
                              >
                                <div
                                  className={`${variables.timenn} col-sm-3 col-3`}
                                >
                                  {timeslot.day_number_display !==
                                  previousDayNumberDisplay
                                    ? translateWeek(
                                        timeslot.day_number_display
                                          .charAt(0)
                                          .toUpperCase() +
                                          timeslot.day_number_display.slice(1)
                                      )
                                    : ""}
                                </div>
                                <div
                                  className={`${variables.timese} col-sm-7 col-7`}
                                >
                                  {timeslot.start_time && timeslot.end_time
                                    ? `${timeslot.start_time
                                        .split(":")
                                        .slice(0, 2)
                                        .join(":")} - ${timeslot.end_time
                                        .split(":")
                                        .slice(0, 2)
                                        .join(":")}`
                                    : trans("stolen")}
                                </div>
                              </div>
                            );
                          })}
                      </div>
                    )}
                  </>
                )}
              </div>
            </div>
          </CustomScrollbar>
        </div>
      </>
    );
  }
);

Maping.displayName = "Maping";

export default Maping;
