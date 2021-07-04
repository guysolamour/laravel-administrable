<div class="card">
    <div class="card-body box-profile">
        <div class="text-center">
            <img id="holder" data-avatar="{{ $model->id }}" class="profile-user-img img-fluid img-circle" src="{{ $model->getFrontImageUrl() }}"
                alt="{{ $model->name }}" style="cursor: pointer;">
        </div>

        <h3 class="profile-username text-center">{{ $model->full_name }}</h3>

        <p class="text-muted text-center">{{ $model->role }}</p>

        @if (get_guard()->can('change-' . config('administrable.guard') . '-avatar', $guard))
        <div class="text-center">
            <button type="button" class="btn btn-primary text-white " data-image='front-image' data-container='.front-image-box'>
                <i class="fa fa-image"></i> Changer d'avatar
            </button>
        </div>
        @endif
    </div>
    <!-- /.card-body -->
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
        collectiontype: 'avatar',
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
    })


    function fnAvatarCommit(image){
        $(`[data-avatar={{$model->id}}]`).attr('src', image.url).attr('alt', image.name);
    }


</script>

@endpush
