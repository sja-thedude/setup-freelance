<!-- Modal -->
<div id="detail-{{$data->id}}" class="ir-modal modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
        <!-- Modal content-->
        <div class="modal-content">
            <button type="button" class="close" data-dismiss="modal">
                <img src="{!! url('assets/images/icons/close.png') !!}"/>
            </button>
            <div class="modal-body">
                <div class="form-detail push-notifications">
                    <div class="row">
                        <div class="col-md-12 text-center">
                            <h4 class="modal-title ir-h4 inline-block">
                                @lang('notification.push_notification_detail')
                            </h4>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group">
                            <div class="col-md-6 text-left">
                                {!! Html::decode(Form::label('name', trans('notification.label_title').":", ['class' => 'ir-label'])) !!}
                            </div>
                            <div class="col-md-6 text-right">
                                <div class="text-view">
                                    {{$data->title}}
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-md-6 text-left">
                                {!! Html::decode(Form::label('name', trans('notification.label_description').":", ['class' => 'ir-label'])) !!}
                            </div>
                            <div class="col-md-6 text-right">
                                <div class="text-view">
                                    {{$data->description}}
                                </div>
                            </div>
                        </div>
                        @if(!empty($data->location))
                        <div class="form-group">
                            <div class="col-md-6 text-left">
                                {!! Html::decode(Form::label('name', trans('notification.label_location').":", ['class' => 'ir-label'])) !!}
                            </div>
                            <div class="col-md-6 text-right">
                                <div class="text-view">
                                    {{$data->location}} ({{$data->location_radius."km"}})
                                </div>
                            </div>
                        </div>
                        @endif
{{--                        @if(!empty($data->gender_dest_male) || !empty($data->gender_dest_female))--}}
{{--                        <div class="form-group">--}}
{{--                            <div class="col-md-6 text-left">--}}
{{--                                {!! Html::decode(Form::label('gender', trans('notification.gender'), ['class' => 'ir-label'])) !!}--}}
{{--                            </div>--}}
{{--                            <div class="col-md-6 text-right">--}}
{{--                                <div class="text-view">--}}
{{--                                    @if(!empty($data->gender_dest_male))--}}
{{--                                        @lang('notification.man')@if(!empty($data->gender_dest_male) && !empty($data->gender_dest_female)), @endif--}}
{{--                                    @endif--}}
{{--                                    @if(!empty($data->gender_dest_female))--}}
{{--                                        @lang('notification.woman')--}}
{{--                                    @endif--}}
{{--                                </div>--}}
{{--                            </div>--}}
{{--                        </div>--}}
{{--                        @endif--}}
{{--                        @if(!empty($data->start_age_dest) || !empty($data->end_age_dest))--}}
{{--                        <div class="form-group">--}}
{{--                            <div class="col-md-6 text-left">--}}
{{--                                {!! Html::decode(Form::label('age', trans('notification.age'), ['class' => 'ir-label'])) !!}--}}
{{--                            </div>--}}
{{--                            <div class="col-md-6 text-right">--}}
{{--                                <div class="text-view">--}}
{{--                                    {{$data->start_age_dest ." - ". $data->end_age_dest}}--}}
{{--                                </div>--}}
{{--                            </div>--}}
{{--                        </div>--}}
{{--                        @endif--}}
                        <div class="form-group">
                            <div class="col-md-6 text-left">
                                {!! Html::decode(Form::label('name', trans('notification.date_time_send').":", ['class' => 'ir-label'])) !!}
                            </div>
                            <div class="col-md-6 text-right">
                                <div class="text-view time-convert"
                                     data-format="{!! config('datetime.jsDateTimeShortFormat') !!}"
                                     data-datetime="{!! $data->send_datetime !!}">
                                </div>
                            </div>
                        </div>
                        <div class="form-group mgt-50">
                            <div class="col-md-12 text-center">
                                <a href="#" class="ir-btn ir-btn-primary" data-dismiss="modal">
                                    @lang('strings.close')
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>