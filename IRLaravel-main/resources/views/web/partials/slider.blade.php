@if(!empty($webWorkspace) && !$webWorkspace->workspaceGalleries->isEmpty())
    <div class="wp-image owl-carousel owl-theme">
        @foreach($webWorkspace->workspaceGalleries as $gallery)
            @if($gallery->active)
                <div class="row">
                    <div class="slide-item owl-lazy" data-src="{{Picture::get(Picture::getImageFolder($gallery->file_path), '1900x715', Picture::getImageName($gallery->file_path), null, 'c', 'c')}}"></div>
                    <div class="overlay"></div>
                </div>
            @endif
        @endforeach
    </div>
@endif