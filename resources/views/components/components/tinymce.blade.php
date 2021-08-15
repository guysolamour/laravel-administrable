@once
    @push('js')
    <script src="{{ asset('vendor/tinymce/tinymce.min.js') }}"></script>
    @endpush
@endonce

@push('js')
<script>
    var tinymce_config = {
        model: @json($model),
        model_name: @json($model_name ?? get_class($model)),
        prefix: @json(config('administrable.auth_prefix_path')),
    }

    tinymce.init({
        selector: @json($selector ?? '[data-tinymce]'),
        language: 'fr_FR',

        plugins: [
            "imagemanager",
            "advlist autolink lists link image autoresize autosave charmap print preview hr anchor pagebreak",
            "searchreplace wordcount visualblocks visualchars code fullscreen",
            "insertdatetime media nonbreaking save table contextmenu directionality",
            "emoticons autoresize autosave template paste textcolor colorpicker textpattern"
        ],
        toolbar: "insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image media colorpicker | imagemanager",
        relative_urls: false,
    });
</script>
@endpush