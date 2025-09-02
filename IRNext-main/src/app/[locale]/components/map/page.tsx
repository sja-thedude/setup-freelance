"use client";
import React, { memo, useEffect, useRef } from "react";
import { GoogleMap, useLoadScript, Marker } from "@react-google-maps/api";
import * as config from "@/config/constants";
import { useI18n } from '@/locales/client';

const Map = memo((props: any) => {
  const trans = useI18n();
  const { data, setExistedMap } = props;
  const mapLoadedRef = useRef(false);
  const { isLoaded, loadError } = useLoadScript({
    googleMapsApiKey: `${config.PUBLIC_GOOGLE_MAPS_API_KEY}`,
    libraries: ["places"],
  });

  const { lat, lng, center } = React.useMemo(() => {
    const lat = data ? Number(data?.lat) : 20;
    const lng = data ? Number(data?.lng) : 100;
    const center = { lat, lng };
    return { lat, lng, center };
  }, [data]);

  useEffect(() => {
    // Check if map was already loaded to avoid reloading
    if (isLoaded && !mapLoadedRef.current) {
      mapLoadedRef.current = true;
      setExistedMap && setExistedMap(true); // Set map existence if needed
    }
  }, [isLoaded, setExistedMap]);

  if (loadError) {
    return (<div>Error loading Google Maps API</div>);
  }

  if (!isLoaded) {
    return <div>{trans('lang_loading')}...</div>;
  }

  if (isLoaded) {
    if (setExistedMap) {
      setExistedMap(true);
    }

    return (
      <div
        className="google-map"
        style={{
          display: "flex",
          flexDirection: "column",
          justifyContent: "center",
          alignItems: "center",
          gap: "20px",
          marginTop: "5%",
        }}
      >
        <GoogleMap
          options={{
            zoomControl: false,
            fullscreenControl: false,
            mapTypeControl: false,
            streetViewControl: false,
          }}
          zoom={16}
          center={center}
          mapContainerClassName="map"
          mapContainerStyle={{
            width: "100%",
            height: "200px",
            borderTopLeftRadius: "30px",
            borderTopRightRadius: "30px",
          }}
        >
          {/* Marker */}
          <Marker position={{ lat, lng }} />
        </GoogleMap>
      </div>
    );
  }
});

Map.displayName = "Map";

export default Map;
