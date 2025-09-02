@php($workspaces = Helper::getActiveWorkspaces())
@php($selected = config('workspace.active'))

@if (count($workspaces) > 0 && !empty($selected))
    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
        <span class="glyphicon glyphicon-briefcase" aria-hidden="true"></span> 
        <span>{{ $workspaces[$selected] }}</span>
        <span class="fa fa-angle-down"></span>
    </a>

    @if (count($workspaces) > 1)
        <ul class="dropdown-menu">
            @foreach ($workspaces as $id => $name)
                @if ($id != $selected)
                    <li><a href="{{ route($guard.'.users.profile', ['workspace' => $id]) }}">{{$name}}</a></li>
                @endif
            @endforeach
        </ul>
    @endif
@endif