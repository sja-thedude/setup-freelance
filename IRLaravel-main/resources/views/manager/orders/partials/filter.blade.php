<div class="filter-bar">
    <div class="row">
        <div class="col-sm-3 col-xs-12">
            <h3 class="mgt-i-10 check-day"></h3>
        </div>
        <div class="col-sm-7 col-xs-12">
            <div class="row">
                <div class="col-sm-3 col-xs-12">
                    <div class="custom-select">
                        <div class="s-icon">
                            <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M5 1.66663L2.5 4.99996V16.6666C2.5 17.1087 2.67559 17.5326 2.98816 17.8451C3.30072 18.1577 3.72464 18.3333 4.16667 18.3333H15.8333C16.2754 18.3333 16.6993 18.1577 17.0118 17.8451C17.3244 17.5326 17.5 17.1087 17.5 16.6666V4.99996L15 1.66663H5Z" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M2.5 5H17.5" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M13.3327 8.33337C13.3327 9.21743 12.9815 10.0653 12.3564 10.6904C11.7313 11.3155 10.8834 11.6667 9.99935 11.6667C9.11529 11.6667 8.26745 11.3155 7.64233 10.6904C7.0172 10.0653 6.66602 9.21743 6.66602 8.33337" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </div>

                        {{ Form::select('filter_transform_type', \App\Helpers\OrderHelper::getTypes(), request('filter_transform_type'), [
                            'class' => 'form-control select-not-search order-auto-submit',
                            'placeholder' => trans('order.all')
                        ]) }}
                    </div>
                </div>
                <div class="col-sm-3 col-xs-12">
                    <div class="custom-select">
                        <div class="s-icon">
                            <svg width="21" height="17" viewBox="0 0 21 17" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M12.75 10C12.75 13.3308 10.1028 16 6.875 16C3.64724 16 1 13.3308 1 10C1 6.66923 3.64724 4 6.875 4C10.1028 4 12.75 6.66923 12.75 10Z" fill="#B5B268" stroke="white" stroke-width="2"/>
                                <path d="M6.74512 7.81299C6.46712 7.81299 6.23812 7.89388 6.05811 8.05566C5.87809 8.21517 5.76188 8.44759 5.70947 8.75293H7.08691V9.35449H5.6582L5.65137 9.47412V9.63477L5.6582 9.74756H6.87158V10.356H5.71631C5.83252 10.9097 6.19824 11.1865 6.81348 11.1865C7.13932 11.1865 7.45264 11.1216 7.75342 10.9917V11.8667C7.4891 12.0011 7.15527 12.0684 6.75195 12.0684C6.19368 12.0684 5.73454 11.9168 5.37451 11.6138C5.01449 11.3107 4.78776 10.8914 4.69434 10.356H4.22607V9.74756H4.62939C4.62028 9.69515 4.61572 9.62451 4.61572 9.53564L4.62256 9.35449H4.22607V8.75293H4.68066C4.76497 8.2015 4.9917 7.76628 5.36084 7.44727C5.72998 7.12598 6.19141 6.96533 6.74512 6.96533C7.1735 6.96533 7.57454 7.05876 7.94824 7.24561L7.61328 8.03857C7.45605 7.96794 7.30908 7.91325 7.17236 7.87451C7.03564 7.8335 6.89323 7.81299 6.74512 7.81299Z" fill="white"/>
                                <path d="M20 7C20 10.3308 17.3528 13 14.125 13C10.8972 13 8.25 10.3308 8.25 7C8.25 3.66923 10.8972 1 14.125 1C17.3528 1 20 3.66923 20 7Z" fill="#B5B268" stroke="white" stroke-width="2"/>
                                <path d="M14.7451 4.81299C14.4671 4.81299 14.2381 4.89388 14.0581 5.05566C13.8781 5.21517 13.7619 5.44759 13.7095 5.75293H15.0869V6.35449H13.6582L13.6514 6.47412V6.63477L13.6582 6.74756H14.8716V7.35596H13.7163C13.8325 7.90967 14.1982 8.18652 14.8135 8.18652C15.1393 8.18652 15.4526 8.12158 15.7534 7.9917V8.8667C15.4891 9.00114 15.1553 9.06836 14.752 9.06836C14.1937 9.06836 13.7345 8.91683 13.3745 8.61377C13.0145 8.31071 12.7878 7.89144 12.6943 7.35596H12.2261V6.74756H12.6294C12.6203 6.69515 12.6157 6.62451 12.6157 6.53564L12.6226 6.35449H12.2261V5.75293H12.6807C12.765 5.2015 12.9917 4.76628 13.3608 4.44727C13.73 4.12598 14.1914 3.96533 14.7451 3.96533C15.1735 3.96533 15.5745 4.05876 15.9482 4.24561L15.6133 5.03857C15.4561 4.96794 15.3091 4.91325 15.1724 4.87451C15.0356 4.8335 14.8932 4.81299 14.7451 4.81299Z" fill="white"/>
                            </svg>
                        </div>

                        {{ Form::select('filter_payment_method', \App\Helpers\OrderHelper::getPaymentMethods(), request('filter_payment_method'), [
                            'class' => 'form-control select-not-search order-auto-submit',
                            'placeholder' => trans('order.all')
                        ]) }}
                    </div>
                </div>
                <div class="col-sm-3 col-xs-12">
                    <div class="custom-select">
                        <div class="s-icon">
                            <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <g clip-path="url(#clip0)">
                                    <path d="M14.1673 17.5V15.8333C14.1673 14.9493 13.8161 14.1014 13.191 13.4763C12.5659 12.8512 11.718 12.5 10.834 12.5H4.16732C3.28326 12.5 2.43542 12.8512 1.8103 13.4763C1.18517 14.1014 0.833984 14.9493 0.833984 15.8333V17.5" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M7.49935 9.16667C9.3403 9.16667 10.8327 7.67428 10.8327 5.83333C10.8327 3.99238 9.3403 2.5 7.49935 2.5C5.6584 2.5 4.16602 3.99238 4.16602 5.83333C4.16602 7.67428 5.6584 9.16667 7.49935 9.16667Z" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M19.166 17.4999V15.8333C19.1655 15.0947 18.9196 14.3773 18.4672 13.7935C18.0147 13.2098 17.3811 12.7929 16.666 12.6083" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M13.334 2.60828C14.051 2.79186 14.6865 3.20886 15.1403 3.79353C15.5942 4.37821 15.8405 5.0973 15.8405 5.83744C15.8405 6.57758 15.5942 7.29668 15.1403 7.88135C14.6865 8.46603 14.051 8.88303 13.334 9.06661" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </g>
                                <defs>
                                    <clipPath id="clip0">
                                        <rect width="20" height="20" fill="white"/>
                                    </clipPath>
                                </defs>
                            </svg>
                        </div>
                        <select class="form-control select-not-search order-auto-submit" name="filter_order_type">
                            <option {!! isset($filter_order_type) && $filter_order_type == '' ? 'selected="selected"' : '' !!}
                                    value="">
                                @lang('order.all')
                            </option>
                            <option {!! isset($filter_order_type) && $filter_order_type == \App\Models\Order::ORDER_TYPE_INDIVIDUAL ? 'selected="selected"' : '' !!}
                                    value="{!! \App\Models\Order::ORDER_TYPE_INDIVIDUAL !!}">
                                @lang('order.individual_orders')
                            </option>
                            <option {!! isset($filter_order_type) && $filter_order_type == \App\Models\Order::ORDER_TYPE_GROUP ? 'selected="selected"' : '' !!}
                                    value="{!! \App\Models\Order::ORDER_TYPE_GROUP !!}">
                                @lang('order.group_orders')
                            </option>
                        </select>
                    </div>
                </div>
                <div class="col-sm-3 col-xs-12">
                    <div class="custom-select">
                        <div class="s-icon">
                            <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M9.99935 18.3333C14.6017 18.3333 18.3327 14.6023 18.3327 9.99996C18.3327 5.39759 14.6017 1.66663 9.99935 1.66663C5.39698 1.66663 1.66602 5.39759 1.66602 9.99996C1.66602 14.6023 5.39698 18.3333 9.99935 18.3333Z" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M10 5V10L13.3333 11.6667" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </div>
                        <select class="form-control select-not-search order-auto-submit" name="filter_type_datetime">
                            <option {!! isset($filter_type_datetime) && $filter_type_datetime == '' ? 'selected="selected"' : '' !!}
                                    value="">
                                @lang('order.all')
                            </option>
                            <option {!! isset($filter_type_datetime) && $filter_type_datetime == \App\Models\Order::ORDER_TIME_FUTURE ? 'selected="selected"' : '' !!}
                                    value="{!! \App\Models\Order::ORDER_TIME_FUTURE !!}">
                                @lang('order.future_orders')
                            </option>
                            <option {!! isset($filter_type_datetime) && $filter_type_datetime == \App\Models\Order::ORDER_TIME_PAST ? 'selected="selected"' : '' !!}
                                    value="{!! \App\Models\Order::ORDER_TIME_PAST !!}">
                                @lang('order.closed_orders')
                            </option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-2 col-xs-12">
            <div class="dropdown dropdown-print order-print-multi pull-right mgt--5 mgb--5">
                <a class="btn-hover dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                    <svg class="mgr-10" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M6 9V2H18V9" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M6 18H4C3.46957 18 2.96086 17.7893 2.58579 17.4142C2.21071 17.0391 2 16.5304 2 16V11C2 10.4696 2.21071 9.96086 2.58579 9.58579C2.96086 9.21071 3.46957 9 4 9H20C20.5304 9 21.0391 9.21071 21.4142 9.58579C21.7893 9.96086 22 10.4696 22 11V16C22 16.5304 21.7893 17.0391 21.4142 17.4142C21.0391 17.7893 20.5304 18 20 18H18" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M18 14H6V22H18V14Z" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    <span>
                        @lang('common.print')
                    </span>
                </a>

                <ul class="dropdown-menu pull-right">
                    <li class="print-multi"
                        data-type="a4"
                        data-url="{!! route('manager.orders.printMultiple', [
                            'type' => 'a4'
                        ]) !!}">
                        <a class="cursor-pointer">
                            @lang('order.a4')
                        </a>
                    </li>
                    @if (isset($tmpWorkspace) && $isShowSticker && $isShowSticker->active)
                        <li class="print-multi"
                            data-type="sticker"
                            data-url="{!! route('manager.orders.printMultiple', [
                                'type' => 'sticker'
                            ]) !!}">
                            <a class="cursor-pointer">
                                @lang('order.sticker')
                            </a>
                        </li>
                    @endif
                </ul>
            </div>
        </div>
    </div>
</div>