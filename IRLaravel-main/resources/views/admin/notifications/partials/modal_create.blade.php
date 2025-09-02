<!-- Modal -->
<div id="form-create" class="ir-modal modal fade" role="dialog">
    {!! Form::open(['route' => $guard.'.notifications.store', 'files' => true, 'class' => 'create-notification', 'autocomplete' => 'off']) !!}
        <input type="hidden" name="timezone" class="auto-detect-timezone"/>
        <div class="modal-dialog modal-lg">
            <!-- Modal content-->
            <div class="modal-content">
                <button type="button" class="close reset-form" data-dismiss="modal">
                    <img src="{!! url('assets/images/icons/close.png') !!}"/>
                </button>
                <div class="modal-body">
                    <div class="form-create">
                        <div class="row">
                            <div class="col-md-12">
                                <h4 class="modal-title ir-h4 inline-block">
                                    @lang('notification.new_message')
                                </h4>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-11">
                                <div class="row">
                                    <!-- title Field -->
                                    <div class="col-md-6">
                                        <div class="row form-group">
                                            <div class="col-sm-12 col-xs-12">
                                                {{ Form::label('label_title', trans('notification.label_title'), ['class' => 'ir-label'], false) }}
                                            </div>
                                            <div class="col-sm-12 col-xs-12">
                                                {!! Form::text('title', null, ['class' => 'form-control', 'required' => 'required', 'placeholder' => trans('notification.text_field')]) !!}
                                            </div>
                                        </div>
                                    </div>
                                    <!-- types Field -->
                                    <div class="col-md-6">
                                        <div class="row form-group">
                                            <div class="col-sm-12 col-xs-12">
                                                {{ Form::label('label_surname', trans('notification.order_from'), ['class' => 'ir-label'], false) }}
                                            </div>
                                            <div class="col-sm-12 col-xs-12">
                                                {!! Form::select('types[]', $types, null, [
                                                'id' => 'types',
                                                'class' => 'form-control select2-type', 
                                                'multiple' => true,
                                                'required' => 'required'
                                                ]) !!}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <!-- description Field -->
                                    <div class="col-md-6">
                                        <div class="row form-group">
                                            <div class="col-sm-12 col-xs-12">
                                                {!! Html::decode(Form::label('label_description', trans('notification.label_description'), ['class' => 'ir-label'])) !!}
                                            </div>
                                            <div class="col-sm-12 col-xs-12">
                                                {!! Form::text('description', null, ['class' => 'form-control', 'required' => 'required', 'placeholder' => trans('notification.text_field')]) !!}
                                            </div>
                                        </div>
                                    </div>
                                    <!-- send_time Field -->
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <span class="noti-span">
                                                <input name="send_now" type="radio" class="flat radio" value="1" id="send-now" checked>
                                                <label for="send-now" class="noti-label-custom">
                                                    @lang('notification.send_now')
                                                </label>
                                            </span>
                                            <span class="noti-span">
                                                <input name="send_now" type="radio" class="flat radio" value="0" id="plans-for-later">
                                                <label for="plans-for-later" class="noti-label-custom">
                                                    @lang('notification.plans_for_later')
                                                </label>
                                            </span>
                                            <div class="noti-group disable">
                                                <div class="ir-group-date-range full-width ir-group-datepicker">
                                                    {{ Form::text('range_send_datetime', null, [
                                                        'class' => 'ir-input ir-datepicker',
                                                        'disabled'
                                                    ])}}
                                                    {{ Form::hidden('send_datetime', null, [
                                                        'class' => 'start_date'
                                                    ])}}
                                                    <span class="ir-input-group-btn ir-btn-date-range">
                                                        <button class="ir-btn-search" type="button">
                                                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                <path d="M19 4H5C3.89543 4 3 4.89543 3 6V20C3 21.1046 3.89543 22 5 22H19C20.1046 22 21 21.1046 21 20V6C21 4.89543 20.1046 4 19 4Z" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                                                <path d="M16 2V6" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                                                <path d="M8 2V6" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                                                <path d="M3 10H21" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                                            </svg>
                                                        </button>
                                                    </span>
                                                </div>
                                            </div>
                                        </div> 
                                    </div>
                                </div>
                                <div class="clearfix"></div>
{{--                                <div class="row">--}}
{{--                                    <div class="col-md-12 mgt--20">--}}
{{--                                        <div class="form-group">--}}
{{--                                            <span class="noti-span">--}}
{{--                                                <input name="is_send_everyone" type="radio" class="flat radio" value="1" id="send-to-everyone" checked>--}}
{{--                                                <label for="send-to-everyone" class="noti-label-custom">--}}
{{--                                                    @lang('notification.send_to_everyone')--}}
{{--                                                </label>--}}
{{--                                            </span>--}}
{{--                                            <span class="noti-span">--}}
{{--                                                <input name="is_send_everyone" type="radio" class="flat radio" value="0" id="send-location-targeted">--}}
{{--                                                <label for="send-location-targeted" class="noti-label-custom">--}}
{{--                                                    @lang('notification.send_location_targeted')--}}
{{--                                                </label>--}}
{{--                                            </span>--}}
{{--                                            <div class="row noti-group mgt-25 disable">--}}
{{--                                                <div class="col-sm-12 col-xs-12">--}}
{{--                                                    {!! Html::decode(Form::label('label_description', trans('notification.user_location'), ['class' => 'ir-label'])) !!}--}}
{{--                                                    <b class="ml-2 font-weight-normal" data-toggle="tooltip" data-placement="bottom"--}}
{{--                                                        title="@lang('workspace.location_helper')">--}}
{{--                                                        <i class="fa fa-question-circle" aria-hidden="true"></i>--}}
{{--                                                    </b>--}}
{{--                                                </div>--}}
{{--                                                <div class="col-md-6">--}}
{{--                                                    <div class="row form-group use-maps">--}}
{{--                                                        <div class="maps">--}}
{{--                                                            <div class="col-sm-12 col-xs-12">--}}
{{--                                                                {!! Form::text('location', null, [--}}
{{--                                                                'class' => 'form-control location', --}}
{{--                                                                'placeholder' => trans('workspace.placeholder_address'),--}}
{{--                                                                'disabled'--}}
{{--                                                                ]) !!}--}}
{{--                                                                --}}
{{--                                                                <img class="maps-marker event-default" src="{!! asset('assets/images/map-marker-line.svg') !!}"/>--}}
{{--                                                                <div id="modal-box-map" class="modal fade signin-frm" tabindex="-1" role="dialog" aria-modal="true">--}}
{{--                                                                    <div class="modal-dialog modal-dialog-centered modal-medium" role="document">--}}
{{--                                                                        <div class="modal-content">--}}
{{--                                                                            <div class="modal-body">--}}
{{--                                                                                <div id="modal-box-map-view" style="height: 500px;"></div>--}}
{{--                                                                            </div>--}}
{{--                                                                        </div>--}}
{{--                                                                    </div>--}}
{{--                                                                </div>--}}
{{--                                                                {!! Form::hidden('location_lat', null, ['class' => 'latitude','required' => 'required']) !!}--}}
{{--                                                                {!! Form::hidden('location_long', null, ['class' => 'longitude','required' => 'required']) !!}--}}
{{--                                                            </div>--}}
{{--                                                        </div>--}}
{{--                                                        --}}
{{--                                                        <ul class="place-results"></ul>--}}
{{--                                                    </div>--}}
{{--                                                </div>--}}
{{--                                                <div class="col-md-6">--}}
{{--                                                    <div class="row form-group">--}}
{{--                                                        <div class="col-sm-12 col-xs-12">--}}
{{--                                                            {!! Form::number('location_radius', null, [--}}
{{--                                                            'class' => 'form-control w-60 inline-block', --}}
{{--                                                            'disabled'--}}
{{--                                                            ]) !!}--}}
{{--                                                            <span class="text-block font-size-18 mgl-10">@lang('notification.km_round_location')</span>--}}
{{--                                                        </div>--}}
{{--                                                    </div>--}}
{{--                                                </div>--}}
{{--                                            </div>--}}
{{--                                        </div> --}}
{{--                                    </div>--}}
{{--                                </div>--}}
                            </div>
                            <div class="col-md-12 mgt-20 text-left">
                                <a href="javascript:;" class="ir-btn ir-btn-secondary mgr-20 reset-form" data-dismiss="modal">
                                    @lang('strings.cancel')
                                </a>
                                {!! Form::submit(trans('strings.send'), ['class' => 'ir-btn ir-btn-primary save-form submit1']) !!}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    {{Form::close()}}
</div>
