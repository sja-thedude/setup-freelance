@if (count($breadcrumbs))
    <div class="row">
        <div class="col-sm-12 col-xs-12">
            <ol class="breadcrumb mgb-10 pdl-0 pdr-0">
                @foreach ($breadcrumbs as $breadcrumb)
                    @if ($breadcrumb->url && !$loop->last)
                        @if($loop->first)
                            <li class="breadcrumb-item"><i class="fa fa-home"></i> <a href="{{ $breadcrumb->url }}">{{ $breadcrumb->title }}</a></li>
                        @else
                            <li class="breadcrumb-item"><a href="{{ $breadcrumb->url }}">{{ $breadcrumb->title }}</a></li>
                        @endif
                    @else
                        @if($loop->first)
                            <li class="breadcrumb-item active"><i class="fa fa-home"></i> {{ $breadcrumb->title }}</li>
                        @else
                            <li class="breadcrumb-item active">{{ $breadcrumb->title }}</li>
                        @endif
                    @endif
                @endforeach
            </ol>
        </div>
    </div>
@endif