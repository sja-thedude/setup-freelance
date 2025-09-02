@if(!empty($webWorkspace) && !$webWorkspace->workspaceAPIGalleries->isEmpty())
    <div class="wp-image owl-carousel owl-theme">
        @foreach($webWorkspace->workspaceAPIGalleries as $gallery)
            @php
                $gallery = (object)  $gallery;
            @endphp
            @if($gallery->active)
                <div class="row">
                    <div class="slide-item owl-lazy" src="data:image/svg+xml,%3Csvg%20xmlns='http://www.w3.org/2000/svg'%20viewBox='0%200%20255%20330'%3E%3C/svg%3E" 
                         data-src="{{Picture::get(Picture::getImageFolder($gallery->file_path), '390x400', Picture::getImageName($gallery->file_path), null, 'c', 'c')}}"></div>
                    <div class="overlay"></div>
                </div>
            @endif
        @endforeach
    </div>
@endif