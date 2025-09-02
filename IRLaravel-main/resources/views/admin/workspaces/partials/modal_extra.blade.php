<!-- Modal -->
<div id="workspace-extra-{{$workspace->id}}" class="ir-modal modal fade" role="dialog">
    <div class="modal-dialog modal-medium">
        <!-- Modal content-->
        <div class="modal-content">
            <button type="button" class="close" data-dismiss="modal">
                <img src="{!! url('assets/images/icons/close.png') !!}"/>
            </button>
            <div class="modal-body">
                <div class="clear"></div>
                <h4 class="modal-title ir-h4">@lang('workspace.extras')</h4>
                
                @php
                    $listTypes = $workspace->workspaceExtras->pluck('active', 'type')->all();
                    $enableTableOrdering = array_get($listTypes, \App\Models\WorkspaceExtra::TABLE_ORDERING, false);
                @endphp
                @foreach(\App\Models\WorkspaceExtra::getTypes() as $type => $value)
                    <div class="row form-group" data-type="{{ \App\Models\WorkspaceExtra::getTypeString($type) }}"
                        style=" @if($type == \App\Models\WorkspaceExtra::SELF_SERVICE && !$enableTableOrdering) display: none; @endif ">
                        {{-- This second (Self service) switch is only visible if the first one (Table Ordering) is enabled. --}}
                        <div class="col-sm-8 col-xs-12">
                            <label class="extra-label" for="{{$value}}">{{$value}}</label>
                        </div>
                        <div class="col-sm-4 col-xs-12 text-right">
                            <input type="checkbox" id="switch-extra-{{ $workspace->id."-". $type}}"
                                value="{{!empty($listTypes[$type]) && ($listTypes[$type] == true) ? \App\Models\WorkspaceExtra::INACTIVE : \App\Models\WorkspaceExtra::ACTIVE}}"
                                class="switch-input" {{!empty($listTypes[$type]) && $listTypes[$type] == \App\Models\WorkspaceExtra::ACTIVE ? 'checked' : null}} />
                            <label 
                                data-route="{{route($guard.'.workspaceExtras.updateOrCreate', [$workspace->id, 'type' => $type])}}"
                                for="switch-extra-{{ $workspace->id."-". $type}}" class="switch update-status"></label>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>