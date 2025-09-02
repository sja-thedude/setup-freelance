<fieldset class="form-detail show-hide-area" data-id="view-profile-area">
    <div class="row mgb-10">
        <div class="col-sm-6 col-xs-12">
            <!-- Name Field -->
            <div class="row form-group">
                <div class="col-sm-12 col-xs-12 text-uppercase">
                    {!! Html::decode(Form::label('last_name', trans('manager.name'), ['class' => 'ir-h5 mgb-10'])) !!}
                </div>
                <div class="col-sm-12 col-xs-12">
                    {!! $user->last_name !!}
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xs-12">
            <!-- Name Field -->
            <div class="row form-group">
                <div class="col-sm-12 col-xs-12 text-uppercase">
                    {!! Html::decode(Form::label('first_name', trans('manager.first_name'), ['class' => 'ir-h5 mgb-10'])) !!}
                </div>
                <div class="col-sm-12 col-xs-12">
                    {!! $user->first_name !!}
                </div>
            </div>
        </div>
    </div>
    <div class="row mgb-10">
        <div class="col-sm-6 col-xs-12">
            <!-- Name Field -->
            <div class="row form-group">
                <div class="col-sm-12 col-xs-12 text-uppercase">
                    {!! Html::decode(Form::label('email', trans('manager.email'), ['class' => 'ir-h5'])) !!}
                </div>
                <div class="col-sm-12 col-xs-12">
                    {!! $user->email !!}
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xs-12">
            <!-- Name Field -->
            <div class="row form-group">
                <div class="col-sm-12 col-xs-12 text-uppercase">
                    {!! Html::decode(Form::label('gsm', trans('manager.gsm'), ['class' => 'ir-h5'])) !!}
                </div>
                <div class="col-sm-12 col-xs-12">
                    {!! $user->gsm !!}
                </div>
            </div>
        </div>
    </div>
    <div class="row mgb-10">
        <div class="col-sm-6 col-xs-12">
            <div class="row form-group">
                <div class="col-sm-12 col-xs-12">
                    {!! Html::decode(Form::label('label_language', trans('manager.language'), ['class' => 'ir-h5'])) !!}
                </div>
                <div class="col-sm-12 col-xs-12">
                    {!! config('languages')[$user->getLocale()] ?? 'Dutch' !!}
                </div>
            </div>
        </div>
    </div>
</fieldset>