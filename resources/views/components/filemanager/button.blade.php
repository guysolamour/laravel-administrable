<x-administrable-filemanager
    :model="$model" :target="$target" :collection="$collection" :type="$type"
/>


@push('js')
    <script>
        (function($){
            $.fn.showfilemanagermodal = function (collection) {
                $(this).on('click', function () {
                    $('#mediaModal'+ collection).modal('show')
                })
            }
        })(jQuery)

    </script>
@endpush

@push('js')
    <script>
        (function($){
            $('{{ $target }}').showfilemanagermodal('{{ $collection }}')
        })(jQuery)
    </script>
@endpush
