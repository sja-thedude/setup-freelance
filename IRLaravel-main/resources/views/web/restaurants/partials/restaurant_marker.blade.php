<div class="restaurant-marker">
    @php
        $workspaceCategories = $workspace->workspaceCategories;
        $typeZaaks = $workspaceCategories->pluck('name')->toArray();
    @endphp
    <h3 class="restaurant-name">
        <span>{{$workspace->name}}</span>
        <a href="{{$subDomain}}" target="_blank">
            <i class="icon-chevron-right"></i>
        </a>
    </h3>
    <p class="type-zaak">{{implode(', ', $typeZaaks)}}</p>
    <p class="address">{{$workspace->address}}</p>
</div>