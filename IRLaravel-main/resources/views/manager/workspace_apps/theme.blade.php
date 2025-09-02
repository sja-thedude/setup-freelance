@extends('layouts.manager')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="ir-panel">
                <div class="ir-title">
                    <h2 class="ir-h2">
                        @lang('workspace_app.title')
                    </h2>
                    <div class="clearfix"></div>
                    @include('ContentManager::partials.errormessage')
                </div>
                <div class="ir-content">
                    <div class="row">
                        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                            <h4 class="workspace_app_description">@lang('workspace_app.description')</h4>
                        </div>
                    </div>

                    <div class="row workspace_app_theme">

                        @for($i = 1; $i <= 3; $i++)

                        <div class="col-xs-12 col-sm-4 col-md-4 col-lg-4 workspace_app_theme_container"
                             data-url="{{ route('manager.apps.change_theme', ['id' => $i]) }}">
                            <div class="workspace_app_image_container">
                                <img src="{{ asset('images/workspace_app/theme_' . $i . '.svg') }}" alt="">
                            </div>

                            <div class="workspace_app_theme_chosen">
                                <label>
                                    <input type="radio" name="theme" value="{{ $i }}" @if($workspaceApp->theme == $i) checked @endif>
                                    <span>@lang('workspace_app.theme.' . $i)</span>
                                </label>
                            </div>
                        </div>

                        @endfor

                        <div class="clearfix"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('style')
    <style>
        .workspace_app_description {
            font-weight: bold;
        }

        .workspace_app_theme {
            margin-bottom: 20px;
        }

        .workspace_app_theme_chosen {
            margin-top: 20px;
            margin-bottom: 20px;
            text-align: center;
        }

        .workspace_app_image_container {
            text-align: center;
        }

        .workspace_app_image_container img {
            width: 80%;
        }
    </style>
@endpush

@push('scripts')
    <script>
        $(function () {
            $('input[type=radio][name=theme]').on('change', function() {
                var radio = $(this);
                var container = radio.closest('.workspace_app_theme_container');
                var url = container.data('url');
                var data = {
                    '_method': 'PUT',
                };

                $.ajax({
                    type: 'POST',
                    url: url,
                    headers: {
                        'X-CSRF-TOKEN': '{!! csrf_token() !!}'
                    },
                    data: data,
                    dataType: 'json',
                    success: function (response) {
                        if (response.success) {
                            // window.location.reload();
                        } else {
                            alert(response.message);
                        }
                    },
                    error: function (error) {
                        var response = error.responseJSON;

                        // Show error
                        console.log('error response:', response);
                    },
                });
            });
        });
    </script>
@endpush