<div class="list-responsive black">
    <div class="list-header list-header-manager">
        <div class="row">
            <div class="col-item col-sm-2 col-xs-12">
                <a href="{{Helper::getFullSortUrl('name')}}">
                    @lang('user.name') {{Helper::getIconSort('name')}}
                </a>
            </div>
            <div class="col-item col-sm-2 col-xs-12">
                <a href="{{Helper::getFullSortUrl('email')}}">
                    @lang('user.email') {{Helper::getIconSort('email')}}
                </a>
            </div>
            <div class="col-item col-sm-1-5 col-xs-12">
                @lang('user.label_phone')
            </div>
            <div class="col-item col-sm-1-5 col-xs-12">
                <a href="{{Helper::getFullSortUrl('birthday')}}">
                    @lang('user.birthday') {{Helper::getIconSort('birthday')}}
                </a>
            </div>
            <div class="col-item col-sm-2 col-xs-12">
{{--                <a href="{{Helper::getFullSortUrl('gender')}}">--}}
{{--                    @lang('user.credits') {{Helper::getIconSort('gender')}}--}}
{{--                </a>--}}
                @lang('user.credits')
            </div>
            <div class="col-item col-sm-1-5 col-xs-12 text-right">
                <a href="{{Helper::getFullSortUrl('last_order_date')}}">
                    @lang('user.last_order') {{Helper::getIconSort('last_order_date')}}
                </a>
            </div>
            <div class="col-item col-sm-1-5 col-xs-12"></div>
        </div>
    </div>
    <div class="list-body white list-body-manager">
        @foreach ($model as $data)
            <div id="tr-{{ $data->id }}" class="row pdb-6">
                <div class="col-item col-sm-2 col-xs-12">
                    {!! $data->name !!}
                </div>
                <div class="col-item col-sm-2 col-xs-12">
                    {!! $data->email !!}
                </div>
                <div class="col-item col-sm-1-5 col-xs-12">
                    {!! $data->gsm !!}
                </div>
                <div class="col-item col-sm-1-5 col-xs-12">
                    {!! Helper::getDateFromFormat($data->birthday, null, $guard) !!}
                </div>
                <div class="col-item col-sm-2 col-xs-12">
                    @php
                        $loyalty = $data->loyalties($tmpWorkspace->id)->first();
                        if (!empty($loyalty) && !empty($rewardMax)) {
                            $temp = ((int) $loyalty->point * 100 /(int) $rewardMax->score);
                            $percentage = round($temp);
                        }
                        $percentage = isset($percentage) && $percentage > 0 ? $percentage : 0;
                    @endphp
                    <span class="bar-percent-text">{{!empty($loyalty) ? $loyalty->point : 0 }} ({{$percentage}}%)</span>
                    <div class="bar">
                        <div class="bar-percent" style="width: {{$percentage >= 100 ? 100 : $percentage}}%;"></div>
                    </div>
                </div>
                <div class="col-item col-sm-1-5 col-xs-12 text-right">
                        {!! date(config('datetime.dateFormat'), strtotime($data->last_order_date)) !!}
                </div>
                <div class="col-item col-sm-1-5 col-xs-12 text-right">
                    <a href="javascript:;" class="dropdown-toggle ir-actions" data-toggle="dropdown" aria-expanded="false">
                        @lang('workspace.actions')
                        <i class=" fa fa-angle-down"></i>
                    </a>
                    <ul class="dropdown-menu pull-right ir-dropdown-actions">
                        <li>
                            <a href="#" data-toggle="modal" data-target="#detail-{{$data->id}}">
                                @lang('user.details')
                            </a>
                        </li>
                        @php
                            $isShowCredit = !empty($tmpWorkspace->workspaceExtras) ? $tmpWorkspace->workspaceExtras->where('type', \App\Models\WorkspaceExtra::CUSTOMER_CARD)->first() : null
                        @endphp
                        @if (isset($tmpWorkspace) && $isShowCredit && $isShowCredit->active)
                            <li>
                                <a href="#" data-toggle="modal" data-target="#credit-{{$data->id}}">
                                    @lang('user.change_credits')
                                </a>
                            </li>
                        @endif
                    </ul>
                </div>
                
                @include($guard.'.users.partials.modal_detail')
                @include($guard.'.users.partials.modal_credit')
            </div>
        @endforeach
    </div>
</div>

@if(!empty($model))
    {{ $model->appends(request()->all())->links() }}
@endif