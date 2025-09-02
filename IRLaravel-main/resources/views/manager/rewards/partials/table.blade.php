<div class="list-responsive black">
    <div class="list-header list-header-manager">
        <div class="col-sm-1 col-xs-12 level">
            @lang('reward.table.level')
        </div>
        <div class="col-sm-11">
            <div class="row">
                <div class="col-item col-sm-4 col-xs-12 text-center">
                    <a href="javascript:;">
                        @lang('reward.table.titel')
                    </a>
                </div>
                <div class="col-item col-sm-2 col-xs-12 text-center">
                    <a href="javascript:;">
                        @lang('reward.table.credits_nodig')
                    </a>
                </div>
                <div class="col-item col-sm-2 col-xs-12 text-center">
                    <a href="javascript:;">
                        @lang('reward.table.waarde')
                    </a>
                </div>
                <div class="col-item col-sm-2 col-xs-12 text-center">
                    <a href="javascript:;">
                        @lang('reward.table.fysiek_cadeau')
                    </a>
                </div>
                <div class="col-item col-sm-2 col-xs-12 text-center">
                </div>
            </div>
        </div>
    </div>
    <div class="list-body list-body-manager">
        @foreach($rewards as $k => $item)
            @php
                $now = \Carbon\Carbon::now();
            @endphp
            <div id="tr-{{ $item->id }}" class="row pdl-10 font-size-14 unset-row @if($item->expire_date < $now)expired @endif" data-id="{{ $item->id }}" >
                <div class="col-sm-1 col-xs-12 level">
                    <b>@lang('reward.table.level') {{ $k + 1 }}</b>
                </div>
                <div class="col-sm-11">
                    <div class="row">
                        <div class="col-item col-sm-4 col-xs-12 text-center cut-text">
                            {!! $item->title !!}
                        </div>
                        <div class="col-item col-sm-2 col-xs-12 text-center">
                            {!! $item->score !!}
                        </div>
                        <div class="col-item col-sm-2 col-xs-12 text-center">
                            {!! \App\Helpers\Helper::showRedeemDiscount($item) !!}
                        </div>
                        <div class="col-item col-sm-2 col-xs-12 text-center">
                            {!! $item->type === \App\Models\Reward::FYSIEK_CADEAU ? trans('reward.ja') : trans('reward.nee') !!}
                        </div>
                        <div class="col-item col-sm-2 col-xs-12 text-right">
                            <a href="javascript:;" class="dropdown-toggle ir-actions" data-toggle="dropdown" aria-expanded="false">
                                @lang('workspace.actions')
                                <i class=" fa fa-angle-down"></i>
                            </a>
                            <ul class="dropdown-menu pull-right ir-dropdown-actions">
                                <li>
                                    <a href="javascript:;" class="showItem"
                                       data-route="{{ route($guard.'.rewards.edit', [$item->id]) }}"
                                       data-id="{{ $item->id }}">@lang('category.edit')</a>
                                </li>
                                <li>
                                    <a href="javascript:;" class="show-confirm"
                                       data-route="{{ route($guard.'.rewards.destroy', [$item->id]) }}"
                                       data-title="{{trans('workspace.are_you_sure_delete', ['name' => $item->promo_name])}}"
                                       data-id="{{ $item->id }}"
                                       data-deleted_success="@lang('reward.deleted_successfully')"
                                       data-close_label="@lang('workspace.close')"
                                       data-yes_label="@lang('common.yes_delete')"
                                       data-no_label="@lang('common.no_cancel')">@lang('category.delete')</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>

@if(!empty($rewards))
    {{ $rewards->appends(request()->all())->links() }}
@endif