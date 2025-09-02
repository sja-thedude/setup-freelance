@if($workspaces->count() > 0)
    <input type="hidden" id="workspaceIdList" value="{{$workspaceIdList}}">
    @foreach($workspaces as $workspace)
        @include('web.restaurants.partials.restaurant-box')
    @endforeach
@else
    <h3 class="no-found">{{trans('dashboard.no-found-restaurant')}}</h3>
@endif