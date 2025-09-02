<div class="col-sm-12 col-xs-12">
    <!-- Name Field -->
    <div class="row form-group">
        <div class="col-sm-2 col-xs-12">
            {!! Html::decode(Form::label('name', trans('strings.role.label_name') . '<span class="required">*</span>')) !!}
        </div>
        <div class="col-sm-10 col-xs-12">
            {!! Form::text('name', null, ['class' => 'form-control', 'required' => 'required']) !!}
        </div>
    </div>

    <!-- Submit Field -->
    <div class="row form-group">
        <div class="col-sm-12 col-xs-12 text-center">
            <hr/>
            <a href="{!! route($guard.'.restaurants.index') !!}" class="btn btn-default">@lang('strings.cancel')</a>
            {!! Form::button(Lang::get('common.save_and_close'), ['class' => 'btn btn-primary clear-border-radius save-and-close']) !!}
            {!! Form::submit((!empty($workspace) ? Lang::get('common.save_and_stay') : Lang::get('common.save_and_create')), ['class' => 'btn btn-primary clear-border-radius']) !!}
        </div>
    </div>
</div>

