@php
    $collection ??= config('administrable.media.collections.front.label');
@endphp

@if (get_guard()->can('change-' . config('administrable.guard') . '-avatar', $guard))
    @filemanagerButton([
        'target'     => "#changeavatar",
        'collection' => $collection,
        'model'      => $model,
    ])
@endif

@push('js')
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('changeavatar', () => ({
            collection: @json($collection),
            model: @json($model),
            avatar: {},
            init(){
                document.addEventListener('filemanagerclosed-' + this.collection, ({detail}) => {
                    this.avatar  = detail.selected[0]

                    this.updateAllAvatarsUrl()
                })
            },

            updateAllAvatarsUrl(){
                jQuery(`[data-avatar='${this.model.id}']`)
                    .attr('src', this.avatar.url)
                    .attr('style', `background-image: url(${this.avatar.url}`)
            },

            get avatar_url(){
                if (jQuery.isEmptyObject(this.avatar)){
                    return this.model.avatar
                }

                return this.avatar.url
            },

        }));
    });
</script>
@endpush
