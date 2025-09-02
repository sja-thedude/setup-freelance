<div class="change-password-fields">
    <div class="row">
        <div class="col-sm-12 col-xs-12 mgb-10">
            <!-- Name Field -->
            <div class="row form-group">
                <div class="col-sm-12 col-xs-12 text-uppercase">
                    {!! Html::decode(Form::label('current_password', trans('strings.label_current_password'), [
                        'class' => 'ir-h5 mgb-10'
                    ])) !!}
                </div>
                <div class="col-sm-12 col-xs-12">
                    {!! Form::password('current_password', ['class' => 'form-control need-required', 'required' => 'required']) !!}
                </div>
            </div>
        </div>
        <div class="col-sm-12 col-xs-12 mgb-10">
            <!-- Name Field -->
            <div class="row form-group">
                <div class="col-sm-12 col-xs-12 text-uppercase">
                    {!! Html::decode(Form::label('new_password', trans('strings.label_new_password'), [
                        'class' => 'ir-h5 mgb-10'
                    ])) !!}
                </div>
                <div class="col-sm-12 col-xs-12">
                    {!! Form::password('new_password', ['class' => 'form-control need-required', 'required' => 'required']) !!}
                </div>
            </div>
        </div>
        <div class="col-sm-12 col-xs-12 mgb-10">
            <!-- Name Field -->
            <div class="row form-group">
                <div class="col-sm-12 col-xs-12 text-uppercase">
                    {!! Html::decode(Form::label('password_confirmation', trans('strings.label_confirm_password'), [
                        'class' => 'ir-h5 mgb-10'
                    ])) !!}
                </div>
                <div class="col-sm-12 col-xs-12">
                    {!! Form::password('password_confirmation', ['class' => 'form-control need-required', 'required' => 'required']) !!}
                </div>
            </div>
        </div>
    </div>
</div>