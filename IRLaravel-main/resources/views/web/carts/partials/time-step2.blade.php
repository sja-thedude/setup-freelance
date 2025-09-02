<div class="item">
    @foreach($blockTimes as $time)
        <div class="sub-item">
            <input class="display-none" type="radio" name="countMax" value="{{ $time->countMax }}"/>
            <input class="display-none" type="radio" name="countMoney" value="{{ $time->countMoney }}"/>
            <input class="display-none" type="radio" name="max" value="{{ $time->max }}"/>
            <input class="display-none" type="radio" name="money" value="{{ $time->maxPricePerSlot }}"/>
            <input class="display-none" type="radio" name="idTimeSlot" value="{{ $time->id }}"/>
            <input type="radio" id="check{{ $time->id }}" name="settingTimeslot"
                {{ ($time->countMoney <= $time->maxPricePerSlot && $time->countMax < $time->max) ? "" : "disable" }}
                value="{{ $time->timeDisplay }}" class="check-selection">
            <label for="check{{ ($time->countMoney <= $time->maxPricePerSlot && $time->countMax < $time->max) ? $time->id : $time->id . "-" }}"
                class="{{ ($time->countMoney <= $time->maxPricePerSlot && $time->countMax < $time->max) ? "" : "disable" }}">
                {{ $time->timeDisplay }}
            </label>
        </div>
    @endforeach
</div>
