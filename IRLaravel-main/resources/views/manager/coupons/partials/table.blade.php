<div class="list-responsive black">
    <div class="list-header list-header-manager">
        <div class="col-item col-sm-2 col-xs-12">
            <a href="javascript:;">
                @lang('coupon.table.code')
            </a>
        </div>
        <div class="col-item col-sm-2 col-xs-12 text-center">
            <a href="javascript:;">
                @lang('coupon.table.due_date')
            </a>
        </div>
        <div class="col-item col-sm-2 col-xs-12 text-center">
            <a href="javascript:;">
                @lang('coupon.table.active')
            </a>
        </div>
        <div class="col-item col-sm-2 col-xs-12 text-center">
            <a href="javascript:;">
                @lang('coupon.lb_discount')
            </a>
        </div>
        <div class="col-item col-sm-2 col-xs-12 text-center">
            <a href="javascript:;">
                @lang('coupon.table.min_max')
            </a>
        </div>
        <div class="col-item col-sm-2 col-xs-12 text-center">
        </div>
    </div>
    <div class="list-body list-body-manager">
        @foreach($coupons as $k => $item)
            @php
                $now = \Carbon\Carbon::now();
            @endphp
            <div id="tr-{{ $item->id }}" class="row pdl-10 font-size-14 @if($item->expire_time < $now)expired @endif" data-id="{{ $item->id }}" >
                <div class="col-item col-sm-2 col-xs-12 cut-text">
                    <b>{!! $item->code !!}</b>
                </div>
                <div class="col-item col-sm-2 col-xs-12 text-center">
                    <div class="text-view time-convert"
                         data-format="{!! config('datetime.jsDateTimeShortFormat') !!}"
                         data-datetime="{!! $item->expire_time !!}">
                    </div>
                </div>
                <div class="col-item col-sm-2 col-xs-12 text-center">
                    {!! $item->active_display !!}
                </div>
                <div class="col-item col-sm-2 col-xs-12 text-center">
                    {!! \App\Helpers\Helper::showCouponDiscount($item) !!}
                </div>
                <div class="col-item col-sm-2 col-xs-12 text-center">
                    {!! $item->count_orders ?? 0 !!}/{!! $item->max_time_all !!}
                </div>
                <div class="col-item col-sm-2 col-xs-12 text-right">
                    <a href="javascript:;" class="dropdown-toggle ir-actions" data-toggle="dropdown" aria-expanded="false">
                        @lang('workspace.actions')
                        <i class=" fa fa-angle-down"></i>
                    </a>
                    <ul class="dropdown-menu pull-right ir-dropdown-actions">
                        <li>
                            <a href="javascript:;" class="showItem"
                               data-route="{{ route($guard.'.coupons.edit', [$item->id]) }}"
                               data-id="{{ $item->id }}">@lang('category.edit')</a>
                        </li>
                        <li>
                            <a href="javascript:;" class="show-confirm"
                               data-route="{{ route($guard.'.coupons.destroy', [$item->id]) }}"
                               data-title="{{trans('workspace.are_you_sure_delete', ['name' => trans('coupon.deze_coupon')])}}"
                               data-id="{{ $item->id }}"
                               data-deleted_success="@lang('coupon.deleted_successfully')"
                               data-close_label="@lang('workspace.close')"
                               data-yes_label="@lang('common.yes_delete')"
                               data-no_label="@lang('common.no_cancel')">@lang('category.delete')</a>
                        </li>
                    </ul>
                </div>
            </div>
        @endforeach
    </div>
</div>

@if(!empty($coupons))
    {{ $coupons->appends(request()->all())->links() }}
@endif