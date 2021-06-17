@once
    @push('css')
        <link rel="stylesheet" href="{{ asset('vendor/components/select2/select2/css/select2.min.css') }}">
        <link rel="stylesheet" href="{{ asset('vendor/components/select2/select2-bootstrap4-theme/select2-bootstrap4.min.css') }}">
    @endpush

    @push('js')
    <script src="{{ asset('vendor/components/select2/select2/js/select2.full.min.js') }}"></script>
    @endpush
@endonce

@push('js')
<script>
    $('{{ $selector ?? ".select2" }}').select2({
        theme: 'bootstrap4'
    });
</script>
@endpush

