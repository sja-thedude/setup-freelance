@php
    $secondColor = !empty($webWorkspace) && !empty($webWorkspace->settingGeneral) ? $webWorkspace->settingGeneral->second_color : null;
@endphp

<div class="loyalties">
    <div class="mobile-content">
        <div class="singular">
            <h2>@lang('loyalty.singular')</h2>
            <div class="">
                <div class="m-wrap-rectangle">
                    <div class="m-wrap-label">
                        {{-- Get percent from loyalty point and max score --}}
                        @php($width = ($loyalty->point / (!empty($rewardMax->score) ? $rewardMax->score : 1)) * 100)
                        {{-- Limit 100% --}}
                        @php($width = ($width > 100) ? 100 : $width)
                        {{-- Get display percent in progress bar --}}
                        @php($widthDisplay = (($width > 0) ? $width : 0) . '%')

                        <div class="m-active-rectangular" style="width: {{ $widthDisplay }}; background: {{$secondColor}}">
                            <div class="wrap-span">
                                <span>{{ ($loyalty->point > 0) ? $loyalty->point : '' }}</span>
                            </div>
                        </div>
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
                                        left: -moz-calc({$percentCurrent}% - 18px);
                                        /* WebKit */
                                        left: -webkit-calc({$percentCurrent}% - 18px);
                                        /* Opera */
                                        left: -o-calc({$percentCurrent}% - 18px);
                                        /* Standard */
                                        left: calc({$percentCurrent}% - 18px);")
                                <span class="{{ $label }} {{ $top }}" style="{!! $cssPercent !!}">{{ $index }}
                                    <hr class="@if($reward->score > $loyalty->point) line-process @endif">
                                </span>
                            @endforeach
                        @endif
                    </div>
                </div>
            </div>
            <div class="m-card-reward">
                <div class="wrap-text">
                    @foreach($rewards as $k => $reward)
                        @php($isRepeat = ($reward->is_used && !$reward->repeat))
                        @php($btnClass = ($reward->is_used) ? "already-redeem" : "button_redeem")
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

                        <div class="row reward-item @if($isRepeat) row-disabled @endif">
                            <div class="row">
                                <div class="col-xs-7 col-sm-7 col-md-7 col-lg-7">
                                    <h5 class="title">{{ $k + 1 }}: {{ $reward->title }} </h5>
                                </div>
                                <div class="col-xs-5 col-sm-5 col-md-5 col-lg-5">
                                    <span class="credit">@lang('loyalty.text_credits', ['number' => $reward->score])</span>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <p class="description">{{ $reward->description }}</p>
                                </div>
                            </div>
                            <div class="row card-bottom">
                                <div class="col-md-12 loyalty-buttons">
                                    <a href="javascript: void(0);" class="btn btn-redeem {!! $btnClass !!} @if($isAllowClass) btn-andere @else btn-andere-gray @endif"
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
                    <div class="clearfix"></div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modal success --}}
@include('web.loyalties.partials.popup_success')

{{-- Modal failed --}}
@include('web.loyalties.partials.popup_failed')

@push('style')
    {!! Html::style('builds/css/web.loyalty.css') !!}
@endpush
