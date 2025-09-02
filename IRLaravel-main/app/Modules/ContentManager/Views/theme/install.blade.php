@extends(Admin::theme())

@section('content')
    <div class="row">
        <div class="x_panel">
            <div class="x_title">
                <h2>Theme Manager</h2>
                <ul class="nav navbar-right panel_toolbox">

                </ul>
                <div class="clearfix"></div>
            </div>
            <div class="x_content">
                <div class="x_panel">
                    <div class="x_title">
                        <h2>Install new theme</h2>
                        <div class="clearfix"></div>
                    </div>
                    <div class="x_content">
                        <p class="help-block text-center">If you have a theme in a .zip format, you may install it by uploading it here.</p>
                        <form id="install_theme" name="install_theme"
                              action="{{ Admin::route('contentManager.theme.install') }}"
                              enctype="multipart/form-data"
                              method="post">
                            {{ csrf_field() }}
                            <div class="form-group">
                                <input class="form-ctrl" name="theme_zip" type="file">
                                <button class="btn btn-success" type="submit">Install Now</button>
                            </div>
                        </form>
                    </div>
                </div><!-- /.form install -->
                <div class="x_panel">
                    <div class="x_title">
                        <h2>Installed themes</h2>
                        <div class="clearfix"></div>
                    </div>
                    <div class="x_content">
                        @include("ContentManager::theme.partials.x_list")
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection