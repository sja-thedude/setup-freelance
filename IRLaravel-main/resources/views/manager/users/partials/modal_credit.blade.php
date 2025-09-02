<!-- Modal -->
<div id="credit-{{$data->id}}" class="ir-modal modal fade" role="dialog">
    <div class="modal-dialog modal-medium">
        <!-- Modal content-->
        <div class="modal-content">
            <button type="button" class="close" data-dismiss="modal">
                <img src="{!! url('assets/images/icons/close.png') !!}"/>
            </button>
            <div class="modal-body">
                <div class="form-detail">
                    <div class="row">
                        <div class="col-md-12">
                            <h4 class="modal-title ir-h4 text-center">
                                @lang('user.credit_title')
                            </h4>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            {!! Form::open(['route' => [$guard.'.user.credit', $data->id], 'files' => true, 'class' => 'update-credit']) !!}
                                {!! Form::hidden('user_id', $data->id) !!}
                                {!! Form::hidden('sent_time', date(config('datetime.dateTimeDb'))) !!}
                                {!! Form::hidden('send_now', 1) !!}
                                <input type="hidden" name="timezone" class="auto-detect-timezone"/>
                            
                                <!-- Credit bar -->
                                <div class="row form-group">
                                    <div class="col-sm-12 col-xs-12">
                                        @if(!$listReward->isEmpty())
                                            <div class="row">
                                                @php
                                                    if ($listReward->count() == 1) {
                                                        $col = 12;
                                                    } elseif ($listReward->count() == 2) {
                                                        $col = 6;
                                                    } elseif ($listReward->count() == 3) {
                                                        $col = 4;
                                                    } elseif ($listReward->count() == 4) {
                                                        $col = 3;
                                                    } else {
                                                        $col = 2;
                                                    }
                                                @endphp
                                                @foreach($listReward as $key => $item)
                                                <div class="col-custom-{{$col}} text-right">
                                                    <img src="{{url('/images/credit-l'.($key+1).'.svg')}}" alt="{{$item->title}}">
                                                </div>
                                                @endforeach
                                            </div>
                                        @endif
                                        <div class="bar mgl-0 mgt--1">
                                            <div class="bar-percent" style="width: {{$percentage >= 100 ? 100 : $percentage}}%;"></div>
                                        </div>
                                    </div>
                                    <div class="col-sm-12 col-xs-12 text-center mgt-10">
                                        <strong>{{!empty($loyalty) ? $loyalty->point : 0 }} / {{!empty($rewardMax) ? $rewardMax->score : 0}}</strong>
                                    </div>
                                </div>
                                <!-- Credit number -->
                                <div class="row form-group">
                                    <div class="col-sm-4 col-xs-12"></div>
                                    <div class="col-sm-2 col-xs-12 pdl-10">
                                        {!! Form::hidden('max_point', !empty($rewardMax) ? $rewardMax->score : 0) !!}
                                        {!! Form::number('point', !empty($loyalty) ? $loyalty->point : 0, [
                                        'class' => 'form-control text-center',
                                        'placeholder' => trans('user.push_title')
                                        ]) !!}
                                    </div>
                                    <div class="col-sm-2 col-xs-12 text-left mgt-8">
                                        <strong>@lang('user.credits')</strong>
                                    </div>
                                    <div class="col-sm-4 col-xs-12"></div>
                                </div>
                                <div class="row form-group mgt-10">
                                    <div class="col-sm-12 col-xs-12 text-center">
                                        {!! Form::submit(trans('user.wijzigen'), [
                                        'class' => 'ir-btn ir-btn-primary save-form submit'
                                        ]) !!}
                                        {!! Form::button(trans('common.reset'), [
                                        'class' => 'ir-btn ir-btn-primary save-form mgl-20 reset-0'
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