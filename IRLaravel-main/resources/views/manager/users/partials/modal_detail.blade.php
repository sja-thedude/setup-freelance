<!-- Modal -->
<div id="detail-{{$data->id}}" class="ir-modal modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
        <!-- Modal content-->
        <div class="modal-content">
            <button type="button" class="close" data-dismiss="modal">
                <img src="{!! url('assets/images/icons/close.png') !!}"/>
            </button>
            <div class="modal-body">
                <div class="form-detail">
                    <div class="row">
                        <div class="col-md-12">
                            <h4 class="modal-title ir-h4 inline-block">
                                {{$data->name}}
                            </h4>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <!-- account manager Field -->
                            <div class="row form-group">
                                <div class="col-sm-12 col-xs-12">
                                    {!! Html::decode(Form::label('name', trans('user.name'), ['class' => 'ir-label'])) !!}
                                </div>
                                <div class="col-sm-12 col-xs-12">
                                    <div class="text-view">
                                        {{$data->name}}
                                    </div>
                                </div>
                            </div>
                            <!-- manager Field -->
                            <div class="row form-group">
                                <div class="col-sm-12 col-xs-12">
                                    {!! Html::decode(Form::label('birthday', trans('user.birthday'), ['class' => 'ir-label'])) !!}
                                </div>
                                <div class="col-sm-12 col-xs-12">
                                    <div class="text-view">
                                        {!! Helper::getDateFromFormat($data->birthday, null, $guard) !!}
                                    </div>
                                </div>
                            </div>
                            <!-- address Field -->
                            <div class="row form-group">
                                <div class="col-sm-12 col-xs-12">
                                    {!! Html::decode(Form::label('address', trans('user.address'), ['class' => 'ir-label'])) !!}
                                </div>
                                <div class="col-sm-12 col-xs-12">
                                    <div class="text-view">
                                        {{$data->address}}
                                    </div>
                                </div>
                            </div>
                            <!-- type Field -->
                            <div class="row form-group">
                                <div class="col-sm-12 col-xs-12">
                                    {!! Html::decode(Form::label('gender', trans('user.gender'), ['class' => 'ir-label'])) !!}
                                </div>
                                <div class="col-sm-12 col-xs-12">
                                    <div class="text-view">
                                        {{ (array_key_exists($data->gender, App\Models\User::genders())) ? App\Models\User::genders($data->gender) : '' }}
                                    </div>
                                </div>
                            </div>
                            <!-- email Field -->
                            <div class="row form-group">
                                <div class="col-sm-12 col-xs-12">
                                    {!! Html::decode(Form::label('email', trans('user.email'), ['class' => 'ir-label'])) !!}
                                </div>
                                <div class="col-sm-12 col-xs-12">
                                    <div class="text-view">
                                        {{$data->email}}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <!-- gsm Field -->
                            <div class="row form-group">
                                <div class="col-sm-12 col-xs-12">
                                    {!! Html::decode(Form::label('gsm', trans('user.gsm'), ['class' => 'ir-label'])) !!}
                                </div>
                                <div class="col-sm-12 col-xs-12">
                                    <div class="text-view">
                                        {{$data->gsm}}
                                    </div>
                                </div>
                            </div>
                            <!-- created_at Field -->
                            <div class="row form-group">
                                <div class="col-sm-12 col-xs-12">
                                    {!! Html::decode(Form::label('label_created_at', trans('workspace.label_created_at'), ['class' => 'ir-label'])) !!}
                                </div>
                                <div class="col-sm-12 col-xs-12">
                                    <div class="text-view">
                                        {!! Helper::getDateFromFormat($data->created_at, null, $guard) !!}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <!-- logo Field -->
                            <div class="row form-group" style="margin-bottom: 0 !important;">
                                <div class="col-sm-12 col-xs-12">
                                    {!! Html::decode(Form::label('profile_picture', trans('user.profile_picture'), ['class' => 'ir-label'])) !!}
                                </div>
                                <div class="col-sm-12 col-xs-12">
                                    <div class="text-view">
                                        @php
                                            //$image = !empty($data->photo) ? $data->photo : url('assets/images/logo.svg');
                                            $image = url('assets/images/logo.svg');
                                        @endphp
                                        <img src="{{$image}}" alt="{{$data->name}}">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-8">
                            {!! Form::open(['route' => $guard.'.notifications.store', 'files' => true, 'class' => 'create-notification']) !!}
                                {!! Form::hidden('user_id', $data->id) !!}
                                {!! Form::hidden('sent_time', date(config('datetime.dateTimeDb'))) !!}
                                {!! Form::hidden('send_now', 1) !!}
                                <input type="hidden" name="timezone" class="auto-detect-timezone"/>
                                <!-- title Field -->
                                <div class="row form-group">
                                    <div class="col-sm-12 col-xs-12">
                                        {{ Form::label('push_message', trans('user.push_message'), ['class' => 'ir-label']) }}
                                    </div>
                                    <div class="col-sm-12 col-xs-12">
                                        {!! Form::text('title', null, [
                                        'class' => 'form-control',
                                        'required' => 'required',
                                        'placeholder' => trans('user.push_title')
                                        ]) !!}
                                    </div>
                                </div>
                                <!-- description Field -->
                                <div class="row form-group">
                                    <div class="col-sm-12 col-xs-12">
                                        {!! Form::textarea('description', null, [
                                        'class' => 'form-control', 
                                        'required' => 'required', 
                                        'placeholder' => trans('user.push_description'),
                                        'rows' => 4
                                        ]) !!}
                                    </div>
                                </div>
                                <div class="row form-group mgt-10">
                                    <div class="col-sm-12 col-xs-12">
                                        {!! Form::submit(trans('strings.send'), [
                                        'class' => 'ir-btn ir-btn-primary save-form submit1'
                                        ]) !!}
                                    </div>
                                </div>
                            {{Form::close()}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>