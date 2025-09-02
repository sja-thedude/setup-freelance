@if (isset($action))

    {!! Form::open(['url' => $action, 'method' => $method, 'files' => TRUE, 'id' => $idForm]) !!}
        <button type="button" class="close" data-dismiss="modal">&times;</button>

        <div class="modal-body">

            <div class="clear"></div>
            <h4 class="modal-title ir-h4">{{ $titleModal }}</h4>

            <input type="hidden" name="order" value="{{ isset($option) ? $option->order : 100000000 }}" />

            <div id="data-show">
                <div class="row">
                    <div class="col-md-12">

                        <div class="row mgb-20">
                            <div class="col-md-12">
                                <div class="col-xs-8">
                                    <div class=" form-group">
                                        <input class="form-control" name="name" type="text" placeholder="@lang('option.naam_optie')"
                                               value="{{ isset($option) ? ($option->translate(app()->getLocale()) ? $option->translate(app()->getLocale())->name : $option->translate('en')->name) : NULL }}"/>
                                    </div>
                                </div>
                                <div class="col-xs-2">
                                    <div class=" form-group">
                                        <input class="form-control" name="min" type="text" placeholder="@lang('option.min')"
                                               value="{{ isset($option) ? $option->min : NULL }}"/>
                                    </div>
                                </div>
                                <div class="col-xs-2">
                                    <div class=" form-group">
                                        <input class="form-control" name="max" type="text" placeholder="@lang('option.max')"
                                               value="{{ isset($option) ? $option->max : NULL }}"/>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row label-item-option">
                            <div class="col-xs-4"></div>
                            <div class="col-xs-2"></div>
                            <div class="col-xs-2 text-center">
                                <label>@lang('option.beschikbaar')</label>
                            </div>
                            <div class="col-xs-2 text-center">
                                <label>
                                    @lang('option.master')
                                    <i class="fa fa-info-circle" data-toggle="tooltip" data-placement="bottom" title="@lang('option.tooltip_master')"></i>
                                </label>
                            </div>
                            <div class="col-xs-2 text-center">
                                <label>@lang('option.verwijder')</label>
                            </div>
                        </div>

                        <div id="optie-items" class=" ui-sortable">
                            @if (isset($option))
                                @php($optionItems = $option->optionItems->sortBy('order'))
                                @foreach($optionItems as $k => $item)
                                    @include('manager.options.partials.item', [
                                        'key'  => $k,
                                        'item' => $item
                                    ])
                                @endforeach
                            @else
                                @include('manager.options.partials.item', [
                                    'key'  => 0,
                                    'item' => new \App\Models\OptionItem()
                                ])
                            @endif
                        </div>
                        <p class="full-width pull-left mgl-20">
                            <a id="createFormItem" href="javascript:;" class="color-B5B268"
                               data-route="{{ route($guard.'.options.createItem') }}">
                                + @lang('option.keuze_toevoegen')
                            </a>
                        </p>

                        <div class="clearfix"></div>

                        <div class="row text-center mgt-50">
                            <div class="wrap-is_ingredient_deletion">
                                <input name="is_ingredient_deletion" type="checkbox" class="flat checkbox" value="1" id="is_ingredient_deletion"
                                       {{ isset($option) && $option->is_ingredient_deletion ? "checked" : "" }} />
                                <label class="is_ingredient_deletion" for="is_ingredient_deletion">
                                    @lang('option.is_deze_optie')
                                    <i class="fa fa-info-circle" data-toggle="tooltip" data-placement="bottom"
                                       data-html="true" title="<img width='100%' src='{{ url('/images/tooltip_keuze.png') }}'/>"></i>
                                </label>
                            </div>

                            <button type="submit" class="ir-btn ir-btn-primary opslaan submit1 mgt-30" style="width:160px" aria-label="">
                                @lang('category.btn_opslaan')
                            </button>
                        </div>

                    </div>
                </div>
            </div>
        </div>

        <div class="clearfix"></div>
    {!! Form::close() !!}
@endif


