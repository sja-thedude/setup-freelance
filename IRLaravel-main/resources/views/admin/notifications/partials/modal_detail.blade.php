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
{{--                        @if(!empty($data->location))--}}
{{--                        <div class="form-group">--}}
{{--                            <div class="col-md-6 text-left">--}}
{{--                                {!! Html::decode(Form::label('name', trans('notification.label_location').":", ['class' => 'ir-label'])) !!}--}}
{{--                            </div>--}}
{{--                            <div class="col-md-6 text-right">--}}
{{--                                <div class="text-view">--}}
{{--                                    {{$data->location}} ({{$data->location_radius."km"}})--}}
{{--                                </div>--}}
{{--                            </div>--}}
{{--                        </div>--}}
{{--                        @endif--}}
                        @if(!empty($data->notificationCategories) && $data->notificationCategories->count() > 0)
                        <div class="form-group">
                            <div class="col-md-6 text-left">
                                {!! Html::decode(Form::label('order_from', trans('notification.order_from'), ['class' => 'ir-label'])) !!}
                            </div>
                            <div class="col-md-6 text-right">
                                <div class="text-view">
                                    {{
                                        !empty($data->notificationCategories) && $data->notificationCategories->count() > 0 ? 
                                        implode(', ', $data->notificationCategories->pluck('name')->toArray()) : null
                                    }}
                                </div>
                            </div>
                        </div>
                        @endif
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