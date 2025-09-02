@foreach($models as $value)
    <div class="col-md-3">
        <div class="thumbnail theme-item {{ $value['active'] == 1 ? 'active' : '' }}">
            <div class="image view view-first">
                <img style="width: 100%; display: block;"
                     src="{{ url('/themes/'.$value->name) }}/images/{{ $value->image_preview }}" alt="image">
                <div class="mask">
                    <p>{{ $value->name }}</p>
                    <div class="tools tools-bottom">
                        <a href="{{ Admin::route('contentManager.theme.view',['id'=>$value->id]) }}"><i
                                    class="fa fa-eye"></i></a>
                        @if(!$value->status)
                            <a href="#"
                               data-role="uninstall-theme"
                               data-theme_id="{{$value->id}}"
                               data-url="{{ Admin::route('contentManager.theme.uninstall', ['themeName' => $value->name]) }}"><i
                                        class="fa fa-times"></i></a>
                        @endif
                    </div>
                </div>
            </div>
            <div class="caption">
                @if($value->status)
                    <a href="#" class="btn btn-disabled btn-block btn-sm">Active</a>
                @else
                    <a href="{{ Admin::route('contentManager.theme.active',['id'=>$value->id]) }}"
                       class="btn btn-success btn-block btn-sm">Active Theme</a>
                @endif
            </div>
        </div>
    </div>
@endforeach

@push('scripts')
@include('ContentManager::theme.partials.script_uninstall')
@endpush