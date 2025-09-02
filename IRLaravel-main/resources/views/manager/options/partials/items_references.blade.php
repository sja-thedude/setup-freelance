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
                                        <strong>
                                            {{ $option->translate(app()->getLocale()) ? $option->translate(app()->getLocale())->name : $option->translate('en')->name }}
                                        </strong>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row label-item-option">
                            <div class="col-xs-3"></div>
                            @if(!empty($connectorsList) && !$connectorsList->isEmpty())
                                @foreach($connectorsList as $connectorItem)
                                    <div class="col-xs-2 text-center">
                                        <label>
                                            {{ $connectorItem->getProviders($connectorItem->provider) }}
                                        </label>
                                    </div>
                                @endforeach
                            @endif
                        </div>

                        <div id="optie-items" class=" ui-sortable">
                            @if (isset($option))
                                @php
                                    $optionItems = $option->optionItems->sortBy('order');
                                @endphp

                                @foreach($optionItems as $k => $item)
                                    <div class="row opties-sortable-handle">
                                        <div class="col-xs-3">
                                            <div class="form-group flex naam_keuzeoptie">
                                                <input class="form-control name_item" disabled="disabled" type="text" value="{{ $item->name }}"/>
                                            </div>
                                        </div>
                                        @if(!empty($connectorsList) && !$connectorsList->isEmpty())
                                            @foreach($connectorsList as $connectorItem)
                                                @php

                                                $optionItemReference = null;
                                                if(!empty($item)):
                                                    if(!empty($optionItemReferences)):
                                                        foreach($optionItemReferences as $optionItemReferenceItem):
                                                            if(
                                                                !empty($item)
                                                                && $optionItemReferenceItem->provider == $connectorItem->provider
                                                                && $optionItemReferenceItem->local_id == $item->id
                                                            ):
                                                                $optionItemReference = $optionItemReferenceItem;
                                                                break;
                                                            endif;
                                                        endforeach;
                                                    endif;
                                                endif;

                                                @endphp
                                                <div class="col-xs-3">
                                                    <div class="form-group flex">
                                                        {!! Form::text('orderItemReferences['.$item->id.']['.$connectorItem->id.'][remote_id]', !empty($optionItemReference->remote_id) ? $optionItemReference->remote_id : null, [
                                                            'class' => 'form-control',
                                                            'placeholder' => trans('setting.more.remote-id')
                                                            ]) !!}
                                                    </div>
                                                </div>
                                            @endforeach
                                        @endif
                                    </div>
                                @endforeach
                            @endif
                        </div>

                        <div class="clearfix"></div>

                        <div class="row text-center mgt-50">
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


