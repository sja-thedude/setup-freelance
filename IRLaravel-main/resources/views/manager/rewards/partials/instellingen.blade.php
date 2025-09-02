<div class="list-responsive black">
    {!! Form::open(['url' => route('manager.rewards.setting'), 'method' => "POST"]) !!}
        <div class="list-body list-body-manager instellingen">
            <h3>@lang('reward.instellingen')</h3>
            <p class="mgt-20 mgb-0 title"><b>@lang('reward.credit_waarde')</b></p>

            <div class="row unset-row">
                <div class="col-md-3">
                    <p><b>(@lang('reward.hoeveel_uitgegeven'))</b></p>
                </div>
                <div class="col-md-1">
                    <input class="form-control mgt--10" name="instellingen" type="number" min="0" step="0.01" required
                           value="{{ $setting ? $setting->instellingen : config('loyalty.rewards.instellingen') }}"/>
                </div>
            </div>

            <button type="submit" class="ir-btn ir-btn-primary opslaan mgb-30" style="width:160px;padding:20px" aria-label="">
                @lang('category.btn_opslaan')
            </button>
        </div>
    {!! Form::close() !!}
</div>