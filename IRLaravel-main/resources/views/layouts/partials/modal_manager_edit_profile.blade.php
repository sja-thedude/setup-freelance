<fieldset class="form-edit show-hide-area" data-id="edit-profile-area" style="display: none;" disabled="disabled">
    <div class="row mgb-10">
        <div class="col-sm-6 col-xs-12">
            <!-- Name Field -->
            <div class="row form-group">
                <div class="col-sm-12 col-xs-12 text-uppercase">
                    {!! Html::decode(Form::label('last_name', trans('manager.name'), [
                        'class' => 'ir-h5 mgb-10'
                    ])) !!}
                </div>
                <div class="col-sm-12 col-xs-12">
                    {!! Form::text('last_name', null, [
                        'class' => 'form-control check-change',
                        'required' => 'required',
                        'data-origin' => !empty($user->last_name) ? $user->last_name : ''
                    ]) !!}
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xs-12">
            <!-- Name Field -->
            <div class="row form-group">
                <div class="col-sm-12 col-xs-12 text-uppercase">
                    {!! Html::decode(Form::label('first_name', trans('manager.first_name'), [
                        'class' => 'ir-h5 mgb-10'
                    ])) !!}
                </div>
                <div class="col-sm-12 col-xs-12">
                    {!! Form::text('first_name', null, [
                        'class' => 'form-control check-change',
                        'required' => 'required',
                        'data-origin' => !empty($user->first_name) ? $user->first_name : ''
                    ]) !!}
                </div>
            </div>
        </div>
    </div>
    <div class="row mgb-10">
        <div class="col-sm-6 col-xs-12">
            <!-- Name Field -->
            <div class="row form-group">
                <div class="col-sm-12 col-xs-12 text-uppercase">
                    {!! Html::decode(Form::label('email', trans('manager.email'), [
                        'class' => 'ir-h5'
                    ])) !!}
                </div>
                <div class="col-sm-12 col-xs-12">
                    {!! Form::email('email', null, [
                        'class' => 'form-control check-change',
                        'required' => 'required',
                        'data-origin' => !empty($user->email) ? $user->email : ''
                    ]) !!}
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xs-12">
            <!-- Name Field -->
            <div class="row form-group">
                <div class="col-sm-12 col-xs-12 text-uppercase">
                    {!! Html::decode(Form::label('gsm', trans('manager.gsm'), [
                        'class' => 'ir-h5'
                    ])) !!}
                </div>
                <div class="col-sm-12 col-xs-12">
                    {!! Form::text('gsm', null, [
                        'class' => 'form-control check-change keyup-gsm',
                        'required' => 'required',
                        'data-origin' => !empty($user->gsm) ? $user->gsm : ''
                    ]) !!}
                    <p class="mgl-20">vb: +32484/835621</p>
                </div>
            </div>
        </div>
        <div class="col-sm-12">
            <!-- Locale Field -->
            <div class="row form-group">
                <div class="col-sm-12 col-xs-12 text-uppercase">
                    {!! Html::decode(Form::label('locale', trans('manager.language'), [
                        'class' => 'ir-h5'
                    ])) !!}
                </div>
                <div class="col-sm-12 col-xs-12">
                    @foreach(config('languages') as $locale => $language)
                        <label for="locale_{{ $locale }}">
                            <input class="check-change" type="radio" style="height:15px;width:15px;margin-left:8px;" id="locale_{{ $locale }}" name="locale" value="{{ $locale }}" {{ $locale == $user->getLocale() ? 'checked' : '' }}>
                            {{ $language }}
                        </label>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</fieldset>