{!! Form::open(['route' => [$guard.'.vats.store'], 'method' => 'post', 'files' => true, 'class' => 'vat-form']) !!}
    <fieldset class="hidden-error">
        <div class="row">
            <div class="col-sm-2 col-xs-12">
                <select class="form-control select2 vat-country" name="country_id"
                    data-route="{!! route($guard.'.vats.index') !!}">
                    @foreach($countries as $country)
                        <option value="{!! $country->id !!}" {!! $country->id == $countryId ? 'selected="selected"' : '' !!}>
                            {!! $country->name !!}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-sm-2 col-xs-12">                 
                <a class="edit-vat cursor-pointer show-hide-actions hide-when-click" data-target="vat-name">
                    <svg class="mgt-8" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M11 4H4C3.46957 4 2.96086 4.21071 2.58579 4.58579C2.21071 4.96086 2 5.46957 2 6V20C2 20.5304 2.21071 21.0391 2.58579 21.4142C2.96086 21.7893 3.46957 22 4 22H18C18.5304 22 19.0391 21.7893 19.4142 21.4142C19.7893 21.0391 20 20.5304 20 20V13" stroke="#B5B268" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M18.5 2.50023C18.8978 2.1024 19.4374 1.87891 20 1.87891C20.5626 1.87891 21.1022 2.1024 21.5 2.50023C21.8978 2.89805 22.1213 3.43762 22.1213 4.00023C22.1213 4.56284 21.8978 5.1024 21.5 5.50023L12 15.0002L8 16.0002L9 12.0002L18.5 2.50023Z" stroke="#B5B268" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </a>
            </div>
        </div>
        <div class="list-responsive">
            <div class="list-header list-header-non-bg">
                <div class="row">
                    <div class="col-item col-sm-3 col-xs-12"></div>
                    <div class="col-item col-sm-1 col-xs-12 text-center">
                        @lang('vat.id')
                    </div>
                    <div class="col-item col-sm-2 col-xs-12 text-center">
                        @lang('vat.take_out')
                    </div>
                    <div class="col-item col-sm-2 col-xs-12 text-center">
                        @lang('vat.delivery')
                    </div>
                    <div class="col-item col-sm-2 col-xs-12 text-center">
                        @lang('vat.in_house')
                    </div>
                </div>
            </div>
            <fieldset class="list-body">
                @include('admin.vats.partials.fields')
            </fieldset>
            <div class="list-footer">
                @include('admin.vats.partials.field')
                
                <div class="row mgb-20">
                    <div class="col-item col-sm-12 col-xs-12">
                        <a class="ir-a ir-add-more show-hide-area mg-8" data-id="vat-name" style="display: none;">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M12 22C17.5228 22 22 17.5228 22 12C22 6.47715 17.5228 2 12 2C6.47715 2 2 6.47715 2 12C2 17.5228 6.47715 22 12 22Z" stroke="#B5B268" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M12 8V16" stroke="#B5B268" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M8 12H16" stroke="#B5B268" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            <span>@lang('common.add')</span>
                        </a>
                    </div>
                </div>
                <div class="row">
                    <div class="col-item col-sm-12 col-xs-12">
                        <div class="show-hide-area vat-submit" data-id="vat-name" style="display: none;">
                            <button type="submit"
                                    class="ir-btn ir-btn-primary inline-block text-center mgl-10"
                                    disabled="disabled">
                                @lang('common.save')
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </fieldset>
{!! Form::close() !!}