<div class="card">
    <div class="card-body">
        <div class="mx-auto d-block">
            <img data-avatar="{{ $model->id }}" class="rounded-circle mx-auto d-block" src="{{ $model->getFrontImageUrl() }}"
                alt="{{ $model->full_name }}">
            <h5 class="text-sm-center mt-2 mb-1">
                <a href="{{ back_route( config('administrable.guard') . '.profile', $model) }}" title="Profil">{{ $model->full_name }}</a>
            </h5>
            <div class="location text-sm-center">
                {{ $model->role }}</div>
        </div>
        <div class="card-text text-sm-center">
            @if($model->facebook)
            <a href="{{ $model->facebook }}" target="_blank">
                <i class="fab fa-facebook pr-1 fa-2x"></i>
            </a>
            @endif
            @if($model->twitter)
            <a href="{{ $model->twitter }}" target="_blank">
                <i class="fab fa-twitter pr-1 fa-2x"></i>
            </a>
            @endif
            @if($model->linkedin)
            <a href="{{ $model->linkedin }}" target="_blank">
                <i class="fab fa-linkedin pr-1 fa-2x"></i>
            </a>
            @endif
        </div>
    </div>
    <div class="card-footer text-center">
        @if (get_guard()->can('change-' . config('administrable.guard') . '-avatar', $guard))
        <button type="button" class="btn btn-primary text-white btn-sm" data-image='front-image'
            data-container='.front-image-box'>
            <i class="fa fa-image"></i>&nbsp; {{ Lang::get('administrable::messages.view.guard.changeavatar') }}
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
        $('[data-avatar="{{ $model->id }}"]').not('.avatar[data-avatar="{{ $model->id }}"]').attr('src', image.url).attr('alt', image.name)
        $('.avatar[data-avatar="{{ $model->id }}"]').attr('style', `background-image: url(${image.url}`)
    }


</script>

@endpush
