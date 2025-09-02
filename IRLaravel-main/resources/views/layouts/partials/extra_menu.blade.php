<ul class="nav navbar-nav navbar-right">
    <li class="dropdown">
        <a href="javascript:;" class="user-profile dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
            <img src="{{ $auth->photo }}" alt="">
            <span class="profile-name">{{ $auth->name }}</span>
            <span class=" fa fa-angle-down"></span>
        </a>

        <ul class="dropdown-menu dropdown-usermenu pull-right">
            <li>
                <a class="cursor-pointer" data-toggle="modal" data-target="#modal_manager_profile">
                    @lang('strings.menu_profile')
                </a>
            </li>
            <li>
                <a href="{{ route($guard.'.logout') }}">
                    @lang('strings.menu_logout')
                </a>
            </li>
        </ul>
    </li>

    <li class="dropdown">
        @include('layouts.partials.switch_lang')
    </li>

    <li role="presentation" class="dropdown" style="display: none;">
        <a href="javascript:;" class="dropdown-toggle info-number" data-toggle="dropdown" aria-expanded="false">
            <i class="fa fa-envelope-o"></i>
            <span class="badge bg-green">6</span>
        </a>

        @include('layouts.partials.msg_list')
    </li>
</ul>