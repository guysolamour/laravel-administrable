<div class="card front-image-collection">
    <div class="card-header bg-secondary">
        <h3 class="card-title text-white">
            {{ $label ?? filemanager_collection_label($collection) }}
        </h3>
    </div>
    <div class="card-body">
        @if(filemanager_is_multiple_collection($collection))
        <div class="row image-container" style="clear: both">
            @foreach ($model->$collection as $media)
            <div class="col-6 imagebox pb-2" data-id="{{ $media->id }}">
                <div class="lightbox imagethumbnail">
                    <a href="{{ $media->getUrl() }}" data-fancybox="gallery" data-caption="{{ $model->name }}">
                        <img style="height:100px;width:100%" class="img-thumbnail" data-id="{{ $media->id }}"
                            src="{{ $media->getUrl('thumb-sm') }}" alt="{{ $media->name }}">
                    </a>
                </div>
            </div>
            @endforeach
        </div>
        @else
        <div class="front-image-box image-container">
            <div class="imagebox text-center">
                <a href="{{ $model->$collection->getUrl() }}" id="front-image" data-fancybox="gallery" data-caption="{{ $model->name }}">
                    <img style="height:250px;width:100%" class="img-thumbnail "
                        src="{{ $model->$collection->getUrl('thumb-sm') }}"
                        data-id="{{ $model->$collection->id }}" alt="{{ $model->$collection->name }}">
                </a>
            </div>
        </div>
        @endif
    </div>
</div>


@push('css')
<link rel="stylesheet" href="{{ asset('css/vendor/jquery.fancybox.min.css') }}">
@endpush

@push('js')
<script src="{{ asset('js/vendor/jquery.fancybox.min.js') }}"></script>
@endpush
