@push('pwa_link')
    <link rel="manifest" href="{!! url('manager_manifest.json') !!}">
@endpush
@push('pwa_script')
    <script src="{!! url('assets/pwa/manager/main.js') !!}"></script>
@endpush