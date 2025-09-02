<!-- Modal -->
<div id="upload-api-galleries" class="ir-modal dropzone-box modal fade" data-gallery="{{\App\Models\Media::API_GALLERIES}}" role="dialog">
    <div class="modal-dialog modal-lg">
        <!-- Modal content-->
        <div class="modal-content">
            <button type="button" class="close reset-form" data-dismiss="modal">
                <img src="{!! url('assets/images/icons/close.png') !!}"/>
            </button>
            <div class="modal-body">
                <h3>{{trans('setting.general.api_galleries')}}</h3>
                <div class="dropzone-manual">
                    <div class="fallback">
                        <div class="dropzone" data-url="{!! route($guard.'.settings.uploadGallery', [$tmpWorkspace->id]) !!}"></div>
                    </div>

                    <div class="gridly row" data-route="{{route($guard . '.settings.updateGalleryOrder', [$tmpWorkspace->id])}}">
                        @php
                            $workspaceAPIGalleries = $tmpWorkspace->workspaceAPIGalleries->sortBy('order');
                        @endphp
                        @foreach ($workspaceAPIGalleries as $image)
                            <div class="brick small col-sm-3 text-center row" data-id="{{$image->id}}">
                                <div class="brick-image" style="background-image: url('{{ $image->full_path }}'); background-size: cover;"></div>
                                <div class="dr-act">
                                    <a class="btn {!! !empty($image->active) ? 'btn-success' : 'btn-danger' !!} dr-act-status btn-min-w64"
                                       data-status="{!! !empty($image->active) ? 'active' : 'inactive' !!}">
                                        {!! !empty($image->active) ? Lang::get('common.active') : Lang::get('common.inactive') !!}
                                    </a>
                                    <a class="btn btn-danger dr-act-delete btn-min-w64">@lang('common.delete')</a>
                                </div>

                                <input type="hidden" value="{!! $image->size !!}" name="file_size[]">
                                <input type="hidden" value="{!! $image->mime_type !!}" name="file_mime_type[]">
                                <input type="hidden" value="{!! $image->name !!}" name="file_name[]">
                                <input type="hidden" value="{!! $image->path !!}" name="file_path[]">
                                <input type="hidden" value="{!! $image->active !!}" name="file_active[]">
                                <input type="hidden" value="{!! $image->type !!}" name="file_type[]">
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>