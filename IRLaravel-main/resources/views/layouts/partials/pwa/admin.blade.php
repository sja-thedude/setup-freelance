@push('pwa_link')
    <link rel="manifest" href="{!! url('admin_manifest.json') !!}">
@endpush
@push('pwa_script')
    <script src="{!! url('assets/pwa/admin/main.js') !!}"></script>
@endpush