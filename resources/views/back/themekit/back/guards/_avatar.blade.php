
<div class="card-body">
    <div class="text-center">
        <img data-avatar="{{$model->id}}" src="{{ $model->getFrontImageUrl() }}" class="rounded-circle" width="150"
            alt="{{ $model->full_name }}">
        <h4 class="card-title mt-10">{{ $model->full_name }}</h4>
        <p class="card-subtitle">{{ $model->role }}</p>
       @if (get_guard()->can('change-' . config('administrable.guard') . '-avatar', $guard))
        <button type="button" class="btn btn-primary text-white " data-image='front-image'
            data-container='.front-image-box'>
            <i class="fa fa-image"></i> {{ Lang::get('administrable::messages.view.guard.changeavatar') }}
        </button>
        @endif
    </div>
</div>



@include(back_view_path('media._modal'))

@push('css')
<link rel="stylesheet" href="{{ asset('vendor/imagemanager/css/ImageManager.css') }}">
<link rel="stylesheet" href="{{ asset('css/vendor/jquery.fancybox.min.css') }}">
@endpush

@push('js')
<script src="{{ asset('vendor/imagemanager/js/ImageManager.js') }}"></script>
<script src="{{ asset('js/vendor/Sortable.js') }}"></script>
<script src="{{ asset('js/vendor/jquery.zoom.min.js') }}"></script>
<script src="{{ asset('js/vendor/jquery.fancybox.min.js') }}"></script>
<script>
    ImageManager.init({
        model                        : @json($model),
        collectiontype               : 'avatar',
        model_name                   : @json($model_name),
        form_name                    : @json($form_name),
        prefix                       : '{{ config('administrable.auth_prefix_path') }}',
        'front-image'                : @json($front_image),
        'back-image'                 : @json($back_image),
        images                       : @json($images),
        @if(isset($front_image_label))
        'front-image-label'          : @json($front_image_label),
        @endif
        @if(isset($back_image_label))
        'back-image-label'           : @json($back_image_label),
        @endif
        @if(isset($images_label))
        'images-label'               : @json($images_label),
        @endif
        images_container             : '.image-container',
        selector                     : '[data-image]',
        modal                        : '#mediaModal',
        renamemodal                  : '#renameModal',
        images_sortable_container    : '.images-box',
    });


    function fnAvatarCommit(image){
        $('[data-avatar={{$model->id}}]').attr('src', image.url).attr('alt', image.name)
    }


</script>

@endpush