<div class="header-info mb-0">
    <div class="media align-items-end">
        <img data-avatar="{{ $model->id }}" class="avatar avatar-xl avatar-bordered" src="{{ $model->getFrontImageUrl() }}" alt="{{ $model->full_name }}">
        <div class="media-body">
            <p class="stext-white opacity-90"><strong>{{ $model->full_name }}</strong></p>
            <small class=" opacity-60">{{ $model->role }}</small>
        </div>
        @if (get_guard()->can('change-' . config('administrable.guard') . '-avatar', $guard))
        <button type="button" class="btn btn-primary text-white btn-sm" data-image='front-image'
            data-container='.front-image-box'>
            <i class="fa fa-image"></i>&nbsp; {{ Lang::get('administrable::messages.view.guard.changeavatar') }}
        </button>
        @endif
    </div>
</div>

@section('modal')
    @include(back_view_path('media._modal'))
@stop

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
          $('[data-avatar="{{ $model->id }}"]').attr('src', image.url).attr('alt', image.name)
    }

</script>

@endpush
