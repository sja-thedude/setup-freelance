<h3 class="title" data-id="{{ $products->first()->category_id }}">
    {{ $products->first()->category_name }}
    ( {{ $productGroup->count() }} )
    <i class=" fa fa-angle-down"></i>
</h3>

<div class="content-wrap list-product-{{ $categoryId }}">
    <div class="list-responsive">
        @if (count($productGroup) > 0)
            <div class="list-header" style="margin-top:-30px">
                <div class="row">
                    <div class="col-item col-sm-1 col-xs-12">
                    </div>
                    <div class="col-item col-sm-3 col-xs-12">
                    </div>
                    <div class="col-item col-sm-2 col-xs-12">
                    </div>
                    <div class="col-item col-sm-3 col-xs-12">
                    </div>
                    <div class="col-item col-sm-1 col-xs-12" style="margin-left:-35px">
                        <span>@lang('category.beschikbaar')</span>
                    </div>
                    <div class="col-item col-sm-2 col-xs-12">
                    </div>
                </div>
            </div>
        @endif
        <div class="list-body ui-sortable">
            @foreach($productGroup as $productId => $products)

                @php($countOpties = $products->unique('opties_id')->filter(function ($item) {
                    return $item->is_checked === 1;
                })->count())

                @foreach($products as $k => $item)
                    @if ($k || !$item->id) @continue @endif

                    <div id="tr-{{ $item->id }}" class="row ui-sortable-handle"
                         data-id="{{ $item->id }}"
                         data-route="{{ route($guard . '.products.updateOrder') }}"
                    >
                        <a href="javascript:;" class="btn-order">
                            <img src="{!! url('assets/images/icons/drag-drop.svg') !!}" />
                        </a>

                        <div class="col-md-12 data-item" data-route="{{ route($guard.'.products.updatePrice', [$item->id]) }}">
                            <div class="col-item col-sm-3 col-xs-12">
                                <input name="fast_name" class="product_name input-ajax" value="{{ $item->product_name }}" />
                            </div>
                            <div class="col-item col-sm-2 col-xs-12 wrap-price">
                                <span>€</span> &nbsp;
                                <input name="fast_price" class="price input-ajax is-number" autocomplete="off" value="{{ $item->price }}" />
                            </div>

                            <div class="col-item col-sm-2 col-xs-12 cut-text">
                                {!! $item->category_name !!}
                            </div>
                            <div class="col-item col-sm-2 col-xs-12">
                                {!! $countOpties !!} @lang('category.txt_option')
                            </div>
                            <div class="col-item col-sm-1 col-xs-12">
                                <input type="checkbox" id="switch-{{ $item->id }}"
                                       value="{{ !$item->active }}"
                                       class="switch-input" {{$item->active ? "checked" : NULL}} />
                                <label data-route="{{ route($guard.'.products.updateStatus', [$item->id]) }}"
                                       data-id="{{ $item->id }}"
                                       for="switch-{{ $item->id }}" class="switch update-status"></label>
                            </div>
                            <div class="col-item col-sm-2 col-xs-12 text-right">
                                <a href="javascript:;" class="dropdown-toggle ir-actions" data-toggle="dropdown" aria-expanded="false">
                                    @lang('workspace.actions')
                                    <i class=" fa fa-angle-down"></i>
                                </a>
                                <ul class="dropdown-menu pull-right ir-dropdown-actions">
                                    <li>
                                        <a href="javascript:;" class="showItem"
                                           data-route="{{ route($guard.'.products.edit', [$item->id]) }}"
                                           data-id="{{ $item->id }}">@lang('product.edit')</a>
                                    </li>
                                    <li>
                                        <a href="javascript:;" class="show-confirm"
                                           data-route="{{ route($guard.'.products.destroy', [$item->id]) }}"
                                           data-title="{{trans('workspace.are_you_sure_delete', ['name' => $item->name])}}"
                                           data-id="{{ $item->id }}"
                                           data-deleted_success="@lang('product.deleted_successfully')"
                                           data-close_label="@lang('workspace.close')"
                                           data-yes_label="@lang('common.yes_delete')"
                                           data-no_label="@lang('common.no_cancel')">@lang('product.delete')</a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                @endforeach
            @endforeach
        </div>
    </div>
</div>
