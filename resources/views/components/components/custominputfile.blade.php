@once
    @push('css')
    <link rel="stylesheet" href="{{ asset('vendor/components/custominputfile/css/jquery.simplefileinput.css') }}">
    @endpush

    @push('js')
    <script src="{{ asset('vendor/components/custominputfile/js/jquery.simplefileinput.js') }}"></script>
    @endpush
@endonce

@push('js')
<script>
    $('{{ $selector ?? ".simple-file-input" }}').simpleFileInput({
        placeholder : "{{ $placeholder ?? 'Choisir une image' }}",
        buttonText : '{{ $buttonText ?? "Choisir" }}',
        width: '{{ $width ?? '100%' }}',
        allowedExts : {!! $allowedExts ?? "['png', 'gif', 'jpg', 'jpeg']" !!} ,
    });
</script>
@endpush


