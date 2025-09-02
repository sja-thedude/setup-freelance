process.env.DISABLE_NOTIFIER = true;
var elixir = require('laravel-elixir');
var gulp = require('gulp');
var concat = require('gulp-concat');
var spritesmith = require('gulp.spritesmith');
var shell = require('gulp-shell');

require('laravel-elixir-livereload');

/*
 |--------------------------------------------------------------------------
 | Elixir Asset Management
 |--------------------------------------------------------------------------
 |
 | Elixir provides a clean, fluent API for defining some basic Gulp tasks
 | for your Laravel application. By default, we are compiling the Sass
 | file for our application, as well as publishing vendor resources.
 |
 */

elixir(function (mix) {
    //===== combine general style =====
    mix.sass([
        './resources/assets/sass/'
    ], 'public/builds/css/all.css');

    //===== combine general scripts =====
    mix.scriptsIn('resources/assets/js', 'public/builds/js/all.js', 'resources/assets');

    //===== combine vendor admin scripts =====
    mix.scripts([
        '/assets/jquery/dist/jquery.min.js',
        '/assets/bootstrap/dist/js/bootstrap.min.js',
        '/assets/jasny-bootstrap/dist/js/jasny-bootstrap.min.js',
        // '/assets/sweetalert/sweetalert.js',
        '/assets/moment/min/moment-with-locales.min.js',
        '/assets/moment-timezone/moment-timezone-with-data.js',
        '/assets/daterangepicker/daterangepicker.js',
        '/assets/masonry/masonry.pkgd.min.js',
        '/assets/treeview-cb/script.js',
        '/assets/switchery/dist/switchery.min.js',
        '/assets/iCheck/icheck.min.js',
        '/assets/bootbox.js/bootbox.js',
        '/assets/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js',
        '/assets/eonasdan-bootstrap-datetimepicker/build/js/bootstrap-datetimepicker.min.js',
        '/assets/select2/dist/js/select2.full.min.js',
        '/assets/select2/dist/js/i18n/de.js',
        '/assets/select2/dist/js/i18n/en.js',
        '/assets/select2/dist/js/i18n/fr.js',
        '/assets/select2/dist/js/i18n/nl.js',
        '/assets/taggle/src/taggle.js',
        '/assets/toastr/toastr.min.js',
        '/assets/jquery-loading/dist/jquery.loading.min.js',
        '/assets/dropzone/dist/min/dropzone.min.js',
        '/assets/bootstrap3-typeahead/bootstrap3-typeahead.min.js',
        // '/assets/sweetalert/sweetalert.min.js',
        '/assets/jquery-sortable/source/js/jquery-sortable-min.js',
        '/assets/sweetalert2/sweetalert2.min.js',
        '/assets/lang-messages/langs.js',
        '/assets/jquery-ui/jquery-ui.js',
        '/assets/print/jquery.print.min.js',
        '/assets/print/print.min.js',
        '/assets/jquery-validation/dist/jquery.validate.min.js',
        '/assets/colorpicker/js/colorpicker.js',
        '/assets/jquery-validation/dist/additional-methods.min.js',
        '/assets/touch-punch/jquery.ui.touch-punch.min.js',
        '/assets/highcharts/highcharts.js',
        '/themes/dashboard/js/main.js',
    ], 'public/builds/js/vendor.admin.js', 'public');

    //===== combine vendor admin style =====
    mix.styles([
        '/assets/bootstrap/dist/css/bootstrap.min.css',
        '/assets/fontawesome/css/font-awesome.min.css',
        '/assets/jasny-bootstrap/dist/css/jasny-bootstrap.min.css',
        '/assets/switchery/dist/switchery.min.css',
        '/assets/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css',
        '/assets/eonasdan-bootstrap-datetimepicker/build/css/bootstrap-datetimepicker.css',
        '/assets/select2/dist/css/select2.min.css',
        '/assets/toastr/toastr.min.css',
        '/assets/jquery-loading/dist/jquery.loading.min.css',
        '/assets/dropzone/dist/min/dropzone.min.css',
        // '/assets/sweetalert/sweetalert.css',
        '/assets/daterangepicker/daterangepicker.css',
        '/assets/jquery-ui/jquery-ui.min.css',
        '/assets/treeview-cb/style.css',
        '/assets/sweetalert2/sweetalert2.min.css',
        '/assets/colorpicker/css/colorpicker.css',
        '/assets/print/print.min.css',
        '/themes/dashboard/css/style.css'
    ], 'public/builds/css/vendor.admin.css', 'public');

    //===== combine manual admin scripts =====
    mix.scriptsIn('./resources/assets/backend/js', 'public/builds/js/main.admin.js', 'resources/assets');

    //===== combine manual admin style =====
    mix.sass(['./resources/assets/backend/sass/'], 'public/builds/css/main.admin.css');

    //===== combine manual manager scripts =====
    mix.scriptsIn('./resources/assets/manager/js', 'public/builds/js/main.manager.js', 'resources/assets');

    //===== combine manual manager style =====
    mix.sass(['./resources/assets/manager/sass/'], 'public/builds/css/main.manager.css');
    
    //===== combine vendor web scripts =====
    mix.scripts([
        '/assets/web/jquery.min.js',
        '/assets/jquery-ui/jquery-ui.js',
        '/assets/bootstrap/dist/js/bootstrap.min.js',
        '/assets/web/modernizr.min.js',
        '/assets/moment/min/moment-with-locales.min.js',
        '/assets/moment-timezone/moment-timezone-with-data.js',
        '/assets/jquery-loading/dist/jquery.loading.min.js',
        '/assets/web/php-date-formatter.min.js',
        '/assets/web/owl.carousel.min.js',
        '/assets/web/jquery.datetimepicker.js',
        './resources/assets/js/maps.js',
        '/assets/lang-messages/langs.js',
        '/assets/sweetalert2/sweetalert2.min.js',
        '/assets/jquery-validation/dist/jquery.validate.min.js',
        '/assets/jquery-validation/dist/additional-methods.min.js',
        '/js/common.js',
        '/assets/web/jquery.session.js',
    ], 'public/builds/js/vendor.web.js', 'public');
    
    //===== combine manual web scripts =====
    mix.scriptsIn('./resources/assets/web/js', 'public/builds/js/main.web.js', 'resources/assets');

    //===== combine vendor admin style =====
    mix.styles([
        '/assets/bootstrap/dist/css/bootstrap.min.css',
        '/assets/fontawesome/css/font-awesome.min.css',
        '/assets/sweetalert2/sweetalert2.min.css',
        '/assets/jquery-loading/dist/jquery.loading.min.css',
    ], 'public/builds/css/vendor.web.css', 'public');

    //===== combine manual web style =====
    mix.sass(['./resources/assets/web/sass-core/main.scss'], 'public/builds/css/main-core.web.css');
    mix.sass(['./resources/assets/web/sass/'], 'public/builds/css/main.web.css');

    //===== Web Loyalty =====
    mix.sass(['./resources/assets/web_loyalty/sass/'], 'public/builds/css/web.loyalty.css');
    mix.scriptsIn('./resources/assets/web_loyalty/js', 'public/builds/js/web.loyalty.js', 'resources/assets');
    //=====/ Web Loyalty =====

    //==== livereload ====
    mix.livereload([
        'resources/assets/**/*',
        'resources/assets/!**!/!**!/!*.scss',
    ]);
});

//---- fonts ----
gulp.task('fonts-fontawesome', function () {
    return gulp.src('./public/assets/fontawesome/fonts/*').pipe(gulp.dest('./public/builds/fonts/'));
});

gulp.task('fonts-bootstrap', function () {
    return gulp.src('./public/assets/bootstrap/dist/fonts/*').pipe(gulp.dest('./public/builds/fonts/'));
});

gulp.task('ir-fonts', function () {
    return gulp.src('./public/assets/ir-fonts/*').pipe(gulp.dest('./public/builds/fonts/'));
});

gulp.task('images-colorpicker', function () {
    return gulp.src('./public/assets/colorpicker/images/*').pipe(gulp.dest('./public/builds/images/'));
});

gulp.task('all-images', function () {
    return gulp.src('./public/images/*').pipe(gulp.dest('./public/builds/images/'));
});

gulp.task('fonts', ['fonts-fontawesome', 'fonts-bootstrap', 'ir-fonts']);

gulp.task('images', ['images-colorpicker', 'all-images']);

// gulp.task('images', ['images-colorpicker']);

gulp.task('langjs', shell.task('php artisan lang:js-generate -c'));
