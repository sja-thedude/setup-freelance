import React, {
    useEffect,
    useState,
} from 'react';

import {
    StyleSheet,
    View,
} from 'react-native';
import MapView, {
    Marker,
    PROVIDER_GOOGLE,
} from 'react-native-maps';

import BackButton from '@src/components/header/BackButton';
import HeaderComponent from '@src/components/header/HeaderComponent';
import TextComponent from '@src/components/TextComponent';
import { Colors } from '@src/configs';
import { useAppSelector } from '@src/hooks';
import useDimens, { DimensType } from '@src/hooks/useDimens';
import { isEmptyOrUndefined } from '@src/utils';
import { useTranslation } from 'react-i18next';

type IMarkerProps = {
    lat: number, lng: number
}

const RestaurantMapScreen = () => {
    const Dimens = useDimens();
    const styles = stylesF(Dimens);
    const { t } = useTranslation();

    const restaurantData = useAppSelector((state) => state.restaurantReducer.allRestaurant.data);

    const [markers, setMarkers] = useState<Array<IMarkerProps>>([]);

    useEffect(() => {
        const allMarkers = restaurantData.filter((res) => !isEmptyOrUndefined(res.lat) && !isEmptyOrUndefined(res.lng)).map((item) => ({
            lat: Number(item.lat),
            lng: Number(item.lng)
        }));

        setMarkers(allMarkers);
    }, [restaurantData]);

    return (
        <View style={{ flex: 1 }}>
            <HeaderComponent >
                <View style={{ flexDirection: 'row', alignItems: 'center', paddingBottom: Dimens.H_8 }}>
                    <BackButton/>
                    <TextComponent style={styles.headerText}>
                        {t('map_view_back_to_list_model_label')}
                    </TextComponent>
                </View>
            </HeaderComponent>
            <MapView
                style={styles.map}
                provider={PROVIDER_GOOGLE}
                showsMyLocationButton
                showsUserLocation
                loadingEnabled
                region={{
                    latitude: markers[0]?.lat || 37.78825,
                    longitude: markers[0]?.lng || -122.4324,
                    latitudeDelta: 0.0922,
                    longitudeDelta: 0.0421,
                }}
            >
                {markers.map((marker: IMarkerProps, index: number) => (
                    <Marker
                        key={index}
                        coordinate={{ latitude : marker.lat , longitude : marker.lng }}
                        image={{ uri: 'map_pin' }}
                    />
                ))}
            </MapView>
        </View>
    );
};

export default RestaurantMapScreen;

const stylesF = (Dimens: DimensType) => StyleSheet.create({
    headerText: {
        color: Colors.COLOR_WHITE,
        fontSize: Dimens.FONT_22,
    },
    map: {
        flex: 1
    },
});