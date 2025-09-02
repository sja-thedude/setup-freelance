<div class="show-img form-group" style="width:{{ $width ?? 210 }}px;height:{{ $height ?? 122 }}px">
    @if ($media)
        <img width="100%" height="100%" src="{{ $media->file_path ? url(\Storage::url($media->file_path)) : $media->full_path }}">
    @endif
</div>

<div class="help-block">@lang('workspace.image_note') 600x400</div>

<div class="upload form-group">

    <label style="display: flex" for="upload-avatar">
        <img src="{{ asset('/assets/images/icons/upload.svg') }}"> &nbsp;
        <span>
            @lang('category.uploaden')
        </span>
    </label>

    <input type="file" name="uploadAvatar" class="upload-avatar hidden" id="upload-avatar"/>

    @if ($media)
        <input type="text" name="mediaId" class="mediaId hidden" value="{{ $media->id }}"/>
    @endif
</div>