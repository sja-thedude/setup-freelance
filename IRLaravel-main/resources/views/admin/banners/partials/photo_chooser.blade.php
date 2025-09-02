@php
    $defaultPhoto = Helper::getLinkFromDataSource(config('common.default_banner_image'));
@endphp

<div id="photo_container" data-src="@if(!empty($banner) && !empty($banner->photo)){{$banner->photo}}@endif">
    <div id="photo_box">
        {!! Form::hidden('photo', null, ['name' => 'photo']) !!}
        <img id="photo_preview"
             src="@if(!empty($banner) && !empty($banner->photo)){{$banner->photo}}@else{{ $defaultPhoto }}@endif"
             data-origin="@if(!empty($banner) && !empty($banner->photo)){{$banner->photo}}@endif"
             data-placeholder="{{ $defaultPhoto }}"
             alt="">
    </div>

    <div class="pd-top-10">
        <button id="change_photo" class="btn btn-primary no-radius"
            onclick="return false;">@lang('strings.change')</button>
        <button id="remove_photo" class="btn btn-danger no-radius"
            style="display: none;"
            onclick="return false;">@lang('strings.remove')</button>
    </div>
</div>

@push('scripts')
    <script type="text/javascript">
        $(document).ready(function () {
            let baseURL = "{{URL::to('/')}}/";

            let photoContainer = $('#photo_container');
            let btnChangePhoto = $('#change_photo');
            let btnRemovePhoto = $('#remove_photo');
            let imgPhoto = $('#photo_preview');
            let placeholder = imgPhoto.data('placeholder');
            let txtPhoto = $('[name="photo"]');

            // Init effect
            toggleEffect();

            // Change photo
            btnChangePhoto.on('click', function () {
                openKCFinder(imgPhoto);
            });
            imgPhoto.on('click', function () {
                openKCFinder(imgPhoto);
            });

            // Remove photo
            btnRemovePhoto.on('click', function () {
                imgPhoto.attr('src', placeholder);
                photoContainer.data('src', '');
                txtPhoto.val('');
            });

            /**
             * Open KCFinder
             */
            function openKCFinder() {
                window.KCFinder = {
                    callBack: function (url) {
                        imgPhoto.attr('src', url);
                        photoContainer.data('src', url);
                        txtPhoto.val(url);
                        toggleEffect();

                        // Cleanup
                        window.KCFinder = null;
                    }
                };
                window.open(baseURL + 'assets/kcfinder/browse.php?opener=ckeditor&type=images', 'kcfinder_textbox',
                    'status=0, toolbar=0, location=0, menubar=0, directories=0, ' +
                    'resizable=1, scrollbars=0, width=800, height=600'
                );
            }

            /**
             * Toggle effect
             */
            function toggleEffect() {
                let photo = photoContainer.data('src');

                if (photo) {
                    btnRemovePhoto.show();
                } else {
                    btnRemovePhoto.hide();
                }
            }
        });
    </script>
@endpush