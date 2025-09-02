<div class="user-modal menu-user hidden">
    <div class="bg"></div>
    <!-- Modal content -->
    <div class="modal-content text-center">
        <a href="javascript:;" class="close icon-close-mobile"
           data-dismiss="popup" data-target=".menu-user"><i class="icn-close"></i></a>
        <ul class="sub-menu">
            <li>
                <a href="javascript:;"
                   class="messages-button" data-route="{!! route($guard.'.notification.index') !!}">
                    @lang('frontend.messages')
                </a>
            </li>
            <li>
                <a href="javascript:;"
                   class="order-history-button">
                    @lang('frontend.order_history')
                </a>
            </li>
            <li>
                <a href="javascript:;"
                   class="profile-user-button">
                    @lang('frontend.mijn_profiel')
                </a>
            </li>
            <li>
                <a href="{{ route('logout') }}">
                    @lang('frontend.uitloggen')
                </a>
            </li>
        </ul>
    </div>
</div>