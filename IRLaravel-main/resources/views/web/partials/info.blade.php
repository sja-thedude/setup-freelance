<div class="wrap-info-map-header">
    <h5>{{!empty($webWorkspace->settingGeneral) ? $webWorkspace->settingGeneral->title : null}}</h5>
    <h6>{{!empty($webWorkspace->address) ? $webWorkspace->address : null}}</h6>
    <h6><span class="text-uppercase">@lang('order.btw'):</span> {{!empty($webWorkspace->btw_nr) ? $webWorkspace->btw_nr : null}}</h6>
    @php
        $_settingTakeout = $webWorkspace->settingOpenHours->where('type', \App\Models\SettingOpenHour::TYPE_TAKEOUT)->first();
        $_settingDelivery = $webWorkspace->settingOpenHours->where('type', \App\Models\SettingOpenHour::TYPE_DELIVERY)->first();
        $_settingInHouse = $webWorkspace->settingOpenHours->where('type', \App\Models\SettingOpenHour::TYPE_IN_HOUSE)->first();
    @endphp

    @if(!empty($_settingDelivery) && $_settingDelivery->active)
        <div class="wrap-table-co-2 ">
            @php
                $priceMin = $priceMax = "";
                $settingDeliveryConditions = !empty($webWorkspace->settingDeliveryConditions) ? $webWorkspace->settingDeliveryConditions->toArray() : null;
                if (!empty($settingDeliveryConditions)) {
                    $newSettingDeliveryConditions = $settingDeliveryConditions;
                    $priceMin = array_reduce($newSettingDeliveryConditions, function($a, $b){
                        return $a['price_min'] < $b['price_min'] ? $a : $b;
                    }, array_shift($newSettingDeliveryConditions));

                    $priceMax = array_reduce($settingDeliveryConditions, function($a, $b){
                        return $a['price'] < $b['price'] ? $a : $b;
                    }, array_shift($settingDeliveryConditions));
                }
            @endphp
            <div class="row display-flex">
                <div class="col-xs-9 col-sm-9 col-md-9">
                    <h6>@lang('frontend.shipping_fee'): </h6>
                </div>
                <div class="col-xs-3 col-sm-3 col-md-3">
                    <span>@if(!empty($priceMax) && ($priceMax['price'] > 0)) €{{number_format($priceMax['price'], 2)}} @else @lang('frontend.free') @endif</span>
                </div>
            </div>
            <div class="row display-flex">
                <div class="col-xs-9 col-sm-9 col-md-9">
                    <h6>@lang('frontend.minimum_order_price'):</h6>
                </div>
                <div class="col-xs-3 col-sm-3 col-md-3">
                    <span>€{{!empty($priceMin) ? number_format($priceMin['price_min'], 2) : 0}}</span>
                </div>
            </div>
            <div class="row display-flex">
                <div class="col-xs-9 col-sm-9 col-md-9">
                    <h6>@lang('frontend.minimum_waiting_time_for_delivery'):</h6>
                </div>
                <div class="col-xs-3 col-sm-3 col-md-3">
                    <span>{{!empty($webWorkspace->settingPreference) ? $webWorkspace->settingPreference->delivery_min_time : 0}} @lang('frontend.min').</span>
                </div>
            </div>
        </div>
    @endif

    <div class="wrap-table-header">
        <div class="row tab-index">
            @php
                $defaultActiveTakeout = $defaultActiveDelivery = $defaultActiveInHouse = false;
                if (
                    !empty($_settingTakeout) && $_settingTakeout->active
                ) {
                    $defaultActiveTakeout = true;
                } elseif (
                    !empty($_settingTakeout) && !$_settingTakeout->active &&
                    !empty($_settingDelivery) && $_settingDelivery->active
                ) {
                    $defaultActiveDelivery = true;
                } elseif (
                    !empty($_settingInHouse) && $_settingInHouse->active &&
                    !empty($_settingDelivery) && !$_settingDelivery->active &&
                    !empty($_settingTakeout) && !$_settingTakeout->active
                ) {
                    $defaultActiveInHouse = true;
                }
            @endphp

            @if(!empty($_settingTakeout) && $_settingTakeout->active)
                <div class="col-md-4" >
                    <h6 class="@if($defaultActiveTakeout) color @endif" data-tab="1">@lang('vat.take_out')</h6>
                </div>
            @endif
            @if(!empty($_settingDelivery) && $_settingDelivery->active)
                <div class="col-md-4">
                    <h6 class="@if($defaultActiveDelivery) color @endif" data-tab="2">@lang('vat.delivery')</h6>
                </div>
            @endif
            @if(!empty($_settingInHouse) && $_settingInHouse->active)
                <div class="col-md-4 text-right">
                    <h6 class="@if($defaultActiveInHouse) color @endif" data-tab="3">@lang('vat.in_house')</h6>
                </div>
            @endif
        </div>
    </div>
    <div class="wrap-table-time">
        @php
            $dayInWeek = config('common.day_in_week');
        @endphp

        @if(!$webWorkspace->settingOpenHours->isEmpty())
            @foreach($webWorkspace->settingOpenHours as $key => $settingOpenHour)
                @php
                    $dbTimeSlots = [];
                    if(!empty($settingOpenHour->openTimeSlots) && !$settingOpenHour->openTimeSlots->isEmpty()) {
                        $openTimeSlots = $settingOpenHour->openTimeSlotsOrderStartTime;
            
                        foreach($openTimeSlots as $openTimeSlot) {
                            if(!empty($dbTimeSlots[$openTimeSlot->day_number])) {
                                array_push($dbTimeSlots[$openTimeSlot->day_number], $openTimeSlot);
                            } else {
                                $dbTimeSlots[$openTimeSlot->day_number] = [$openTimeSlot];
                            }
                        }
                    }

                    $defaultActive = false;

                    if (
                        !empty($_settingTakeout) && $_settingTakeout->active &&
                        $key == 0
                    ) {
                        $defaultActive = true;
                    } elseif (
                        !empty($_settingTakeout) && !$_settingTakeout->active &&
                        !empty($_settingDelivery) && $_settingDelivery->active &&
                        $key == 1
                    ) {
                        $defaultActive = true;
                    } elseif (
                        !empty($_settingInHouse) && $_settingInHouse->active &&
                        !empty($_settingDelivery) && !$_settingDelivery->active &&
                        !empty($_settingTakeout) && !$_settingTakeout->active &&
                        $key == 2
                    ) {
                        $defaultActive = true;
                    }
                @endphp

                <div class="content-tab @if($defaultActive) active @endif" data-tab="{{$key+1}}">
                    @foreach($dayInWeek as $day)
                        <div class="row">
                        <div class="col-md-6">
                            <h6>@lang('common.days.'.$day):</h6>
                        </div>
                        <div class="col-md-6">
                            @if(!empty($dbTimeSlots[$day]))
                                @foreach($dbTimeSlots[$day] as $openTimeSlot)
                                    @if(!empty($openTimeSlot))
                                        <span>{!! date('H:i', strtotime($openTimeSlot->start_time)) !!} - {!! date('H:i', strtotime($openTimeSlot->end_time)) !!}</span>
                                    @else
                                        <span>@lang('setting_open_hour.closed')</span>
                                    @endif
                                @endforeach
                            @else
                                <span>@lang('setting_open_hour.closed')</span>
                            @endif

                        </div>
                    </div>
                    @endforeach
                </div>
            @endforeach
        @endif
    </div>
    <div class="wrap-map">
        <div class="g-map" data-address="{{$webWorkspace->address}}" data-lat="{{$webWorkspace->address_lat}}" data-long="{{$webWorkspace->address_long}}"></div>
        <script>
            window.mapLocationsJson = [{"marker":"","latitude":"{{$webWorkspace->address_lat}}","longitude":"{{$webWorkspace->address_long}}","description":"{{$webWorkspace->address}}"}];
        </script>
    </div>
</div>

<script>
    let isGoogleMapsInitialized = false;

    /**
     * Copy from resources/assets/web/js/script.js
     */
    function initializeLocationsGoogleMaps() {
        const gmapElements = document.getElementsByClassName('g-map');

        document.querySelector('.icon-information').addEventListener('mouseover', function () {
            if (!isGoogleMapsInitialized) {
                generateGmap(gmapElements);
                isGoogleMapsInitialized = true;
            }
        });
    }

    function generateGmap(gmapElements = []) {
        Array.from(gmapElements).forEach(
            element => {
                var googleMap = new google.maps.Map(element, {
                mapTypeId: google.maps.MapTypeId.ROADMAP,
                disableAutoPan: false,
                navigationControl: true,
                mapTypeControl: false,
                mapTypeControlOptions: {
                    style: google.maps.MapTypeControlStyle.DROPDOWN_MENU,
                },
                disableDefaultUI: true,
                gestureHandling: "cooperative"
            });
            var bounds = new google.maps.LatLngBounds();
            var geocoder = new google.maps.Geocoder();
            var infowindow = new google.maps.InfoWindow();
            var pinIcon = new google.maps.MarkerImage();

            for (var i = 0; i < window.mapLocationsJson.length; i++) {
                var location = window.mapLocationsJson[i];
                var latlng = new google.maps.LatLng(location.latitude, location.longitude);
                //pinIcon.url = location.marker;
                var mapMarker = new google.maps.Marker({
                    position: latlng,
                    map: googleMap,
                    //icon: pinIcon
                });
                bounds.extend(mapMarker.position);
                googleMap.fitBounds(bounds);
                googleMap.panToBounds(bounds);
                google.maps.event.addListener(
                    mapMarker,
                    "click",
                    (function (mapMarker, location) {
                        return function () {
                            infowindow.setContent(location.description);
                            infowindow.open(googleMap, mapMarker);
                        };
                    })(mapMarker, location)
                );
            }

            var listener = google.maps.event.addListener(googleMap, "idle", function () {
                if (googleMap.getZoom() > 14) googleMap.setZoom(14);
                google.maps.event.removeListener(listener);
            });
            }
        )
    }
</script>
