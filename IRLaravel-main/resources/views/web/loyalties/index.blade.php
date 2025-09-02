@extends('layouts.web-user')
@section('content')
@php
    $secondColor = !empty($webWorkspace) && !empty($webWorkspace->settingGeneral) ? $webWorkspace->settingGeneral->second_color : null;
@endphp
    <div id="container" >
        <div id="main-body" class="loyalties">

            <div class="wrap-page-card web-content">
                <div class="row">
                    <div class="col-md-12 custom-col">
                        <div class="wrap-content-card">
                            <h2>@lang('loyalty.singular')</h2>
                            <div class="row align-item">
                                <div class="col-md-3">
                                    <div class="wrap-rectangle">
                                        <div class="main-rectangular">
                                            <div class="wrap-label">
                                                @if(!empty($rewards) && count($rewards) > 0)
                                                    @php($lastReward = $rewards->last())
                                                    @php($percentOne = 100 / $lastReward->score)

                                                    @foreach($rewards as $k => $reward)
                                                        @php($index = $k + 1)
                                                        @php($top = 'top-' . $index)
                                                        @php($label = 'label-' . (($index % 2 == 0) ? 'even' : 'odd'))
                                                        @php($percentCurrent = $percentOne * $reward->score)
                                                        @php($cssPercent =
                                                                "/* Firefox */
                                                                bottom: -moz-calc({$percentCurrent}% - 18px);
                                                                /* WebKit */
                                                                bottom: -webkit-calc({$percentCurrent}% - 18px);
                                                                /* Opera */
                                                                bottom: -o-calc({$percentCurrent}% - 18px);
                                                                /* Standard */
                                                                bottom: calc({$percentCurrent}% - 18px);")
                                                        <span class="{{ $label }} {{ $top }}" style="{!! $cssPercent !!}">{{ $index }}</span>
                                                    @endforeach
                                                @endif
                                            </div>
                                            {{-- Get percent from loyalty point and max score --}}
                                            @php($height = ($loyalty->point / (!empty($rewardMax->score) ? $rewardMax->score : 1)) * 100)
                                            {{-- Limit 100% --}}
                                            @php($height = ($height > 100) ? 100 : $height)
                                            {{-- Get display percent in progress bar --}}
                                            @php($heightDisplay = (($height > 0) ? $height : 0) . '%')
                                            <div class="active-rectangular top-2" style="height: {{ $heightDisplay }}; background: {{$secondColor}}">
                                                <div class="wrap-span">
                                                    <span>{{ ($loyalty->point > 0) ? $loyalty->point : '' }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-9 mg-top-15">
                                    <div class="wrap-text">
                                        @foreach($rewards as $k => $reward)
                                            @php($isRepeat = ($reward->is_used && !$reward->repeat))
                                            @php($btnClass = ($reward->is_used) ? "already-redeem" : "button_redeem")
                                            @php($lastItem = count($rewards) == $k + 1 ? 'last-item' : '')
                                            @php($isAllowBtn = true)
                                            @php($isAllowClass = true)
                                            @php($isDataUrl = true)
                                            @if($reward->is_used)
                                                @if($reward->type == \App\Models\Reward::KORTING) 
                                                    @php($isAllowBtn = false)
                                                    @php($isAllowClass = false)

                                                    @if($reward->repeat && $loyalty->reward_level_id != $reward->id)
                                                        @php($isAllowBtn = true)
                                                        @php($isAllowClass = true)
                                                    @elseif(!$reward->repeat)
                                                        @php($isDataUrl = false)
                                                    @endif
                                                @elseif($reward->type == \App\Models\Reward::FYSIEK_CADEAU && !$reward->repeat)
                                                    @php($isAllowBtn = false)
                                                    @php($isAllowClass = false)
                                                    @php($isDataUrl = false)
                                                @endif
                                            @endif
                                            <div class="row reward-item {!! $lastItem !!} @if($isRepeat) row-disabled @endif">
                                                <div class="row margin-bottom-5">
                                                    <div class="col-xs-5 col-sm-5 col-md-5 col-lg-5">
                                                        <h5 class="title">{{ $k + 1 }}: {!! \App\Helpers\Helper::showTitleLoyalties($reward) !!} </h5>
                                                    </div>
                                                    <div class="col-xs-7 col-sm-7 col-md-7 col-lg-7"></div>
                                                </div>
                                                <div class="row reward-second-line">
                                                    <p class="description">{{ $reward->description }}</p>
                                                    <span class="credit">@lang('loyalty.text_credits', ['number' => $reward->score])</span>
                                                    <div class="loyalty-buttons">
                                                        <a href="javascript: void(0);" class="btn btn-redeem {!! $btnClass !!} @if($isAllowClass) btn-andere btn-pr-custom  @else btn-andere-gray @endif"
                                                            @if($isDataUrl) data-url="{{ route('api.loyalties.redeem', ['loyalty' => $loyalty->id, 'reward_id' => $reward->id]) }}" @endif>
                                                            @if($isAllowBtn)
                                                                @lang('loyalty.button_redeem')
                                                            @else 
                                                                @lang('loyalty.already_redeem')
                                                            @endif
                                                        </a>

                                                        @if($isRepeat)
                                                            <a href="javascript: void(0);" class="btn-redeem-history"
                                                               data-url="{{ route('api.loyalties.redeem.last_reward', ['loyalty' => $loyalty->id, 'reward_id' => $reward->id]) }}"
                                                            ><img src="{{ asset('images/is_redeem.svg') }}" class="success" alt="success"></a>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>

                                        @endforeach

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @include('web.loyalties.partials.wrap_page_card')
            
        </div>
    </div>

    {{-- Modal success --}}
    @include('web.loyalties.partials.popup_success')

    {{-- Modal failed --}}
    @include('web.loyalties.partials.popup_failed')
@endsection

@push('style')
    {!! Html::style('builds/css/web.loyalty.css') !!}
@endpush

{{-- @push('scripts')
    {!! Html::script('builds/js/web.loyalty.js') !!}
@endpush --}}