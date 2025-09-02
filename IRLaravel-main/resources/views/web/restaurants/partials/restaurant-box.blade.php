@php
    $workspaceCategories = $workspace->workspaceCategories;
    $typeZaaks = $workspaceCategories->pluck('name')->toArray();
    $settingPreference = $workspace->settingPreference;
    $settingDeliveryConditions = $workspace->settingDeliveryConditions;
@endphp
<div class="restaurant-box">
    <div class="logo-workspace">
        @php
            $image = !empty($workspace->workspaceAvatar) ? $workspace->workspaceAvatar->full_path : url('assets/images/no-image.svg');
        @endphp
        <a href="{!! \Helper::getSubDomainOfWorkspace($workspace->id) !!}" target="_blank">
            <div class="show-image show-pending" style="background-image: url('{{$image}}'); background-size: cover"></div>
        </a>
    </div>
    <div class="info-delivery">
        <div class="info-status">
            <div class="restaurant-info">
                <h4 class="show-pending">
                    <a href="{!! \Helper::getSubDomainOfWorkspace($workspace->id) !!}" target="_blank">{{$workspace->name}}</a>
                </h4>
                <p class="show-pending">{{implode(', ', $typeZaaks)}}</p>
            </div>
            <div class="delivery-status">
                <p class="distance show-pending">{{(int)$workspace->getDistanceFormat($workspace->distance)}} km</p>
                <p class="status show-pending">@if($workspace->status == 1) {{trans('dashboard.open')}} @else {{trans('dashboard.closed')}} @endif</p>
                <div class="award show-pending"></div>
            </div>
        </div>
        @if($currentType != \App\Models\SettingOpenHour::GROUP)
            <div class="delivery-info">
                <div class="display-flex">
                    <div class="time show-pending display-flex">
                        <div class="waiting-time-icon"></div>
                        @php
                            $minimumWaitingTime = $settingPreference->delivery_min_time;
                            if ($currentType == \App\Models\SettingOpenHour::TAKEOUT) {
                                $minimumWaitingTime = $settingPreference->takeout_min_time;
                            }
                        @endphp
                        <span>±{{$minimumWaitingTime}} min</span>
                    </div>
                    @if($currentType == \App\Models\SettingOpenHour::LEVERING)
                        @php
                        $minimumAmount = $settingDeliveryConditions->min('price_min');
                        $deliveryCharge = $settingDeliveryConditions->min('price');
                        @endphp
                        <div class="delivery show-pending display-flex">
                            <div class="icon-motobike"></div>
                            <span>€{{\App\Helpers\Helper::formatPrice($deliveryCharge)}}</span>
                        </div>
                        <div class="price show-pending display-flex">
                            <div class="icon-money-custom"></div>
                            <span class="min-title">Min</span>
                            <span>€{{\App\Helpers\Helper::formatPrice($minimumAmount)}}</span>
                        </div>
                    @endif
                </div>
                <div>
                    @if($workspace->workspaceExtraLoyalty->count() > 0)
                        <div class="float-right show-pending">
                            <div class="icon-loyalty"></div>
                        </div>
                    @endif
                </div>
            </div>
        @endif
    </div>
</div>