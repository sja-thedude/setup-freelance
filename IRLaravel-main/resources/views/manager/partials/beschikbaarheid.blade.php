<div class="beschikbaarheid">
    <h3>@lang('category.beschikbaarheid')</h3>

    <div class=" form-group mgr-bottom-3">
        <input name="time_no_limit" type="radio" class="flat checkbox" value="0" id="altijd" {{ !$timeNoLimit ? "checked" : "" }}>
        <label for="altijd" class="font-size-16">
            @lang('category.altijd_beschikbaar')
        </label>
    </div>

    <div class=" form-group">
        <input name="time_no_limit" type="radio" class="flat checkbox" value="1" id="specifieke" {{ $timeNoLimit ? "checked" : "" }}>
        <label for="specifieke" class="font-size-16">
            @lang('category.specifieke_beschikbaarheid')
        </label>
    </div>

    <div class="days">
        <ul class="{{ !$timeNoLimit ? "disable" : "" }}">
            @php($listDay = array_values(trans('category.days')))

            @foreach($listDay as $k => $day)
                @php($status = isset($openTime) && isset($openTime[$k + 1]) && $openTime[$k + 1]['status'])
                @php($startTime = isset($openTime) && isset($openTime[$k + 1]) && $openTime[$k + 1]['start_time'])
                @php($endTime = isset($openTime) && isset($openTime[$k + 1]) && $openTime[$k + 1]['end_time'])
                <li>
                    <input type="checkbox" class="flat checkbox" name="days[{{ $k }}][status]"
                           data-role="checkbox"
                           value="1" {{ $status ? "checked" : "" }} />

                    <span class="day">
                        {{ $day }}
                    </span>

                    <input type="hidden" class="day_number" name="days[{{ $k }}][day_number]" value="{{ $k + 1 }}"/>

                    <span>
                        <input type="time" class="time {{ $status ? "" : "boder-none" }} start_time" name="days[{{ $k }}][start_time]" {{ $status ? "" : "readonly" }}
                           value="{{ $startTime ? \Carbon\Carbon::parse($openTime[$k + 1]['start_time'])->format('H:i') : "00:00" }}"/>
                    </span>

                    <span> - </span>

                    <span>
                        <input type="time" class="time {{ $status ? "" : "boder-none" }} end_time" name="days[{{ $k }}][end_time]" {{ $status ? "" : "readonly" }}
                           value="{{ $endTime ? \Carbon\Carbon::parse($openTime[$k + 1]['end_time'])->format('H:i') : "23:59" }}"/>
                    </span>
                    <div class="wrapError">
                        <input type="hidden" name="days.{{ $k }}.end_time" />
                    </div>
                </li>
            @endforeach

        </ul>
    </div>
</div>