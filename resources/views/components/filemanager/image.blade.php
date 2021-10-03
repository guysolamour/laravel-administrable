@php
    $component_name = 'image' . strtolower(Str::random(20));
@endphp
<div class="card" x-data="{{ $component_name }}" x-ref="root">
    <div class="card-header bg-secondary">
        <h3 class="card-title text-white">
            {{ $label }}
        </h3>
    </div>
    <div class="card-body">
        <template x-if="isMultipleCollection">
            <div>
                <div class="float-right pb-2" x-show="image.length > 0">
                    <button class="btn btn-sm btn-danger" @click.prevent="unSelectAll">
                        <i class="fas fa-trash"></i> Tous supprimer
                    </button>
                </div>

                <div class="row" style="clear: both"  x-show="image.length > 0">
                    <template x-for="(item, index) in image">
                        <div class="col-6 imagebox pb-2">
                            <div class="lightbox">
                                <i class="close-icon fa fa-times" aria-hidden="true" data-toggle="tooltip" data-placement="top"
                                    title="Supprimer l'image" @click.prevent="unSelect(item)" style="font-size: 1.5rem;display: none;cursor: pointer;color: red"></i>
                                <a :href="item.url" data-fancybox="gallery" :data-caption="item.name">
                                    <img style="height:100px" class="img-thumbnail" :src="item.url" alt="item.name">
                                </a>
                            </div>
                        </div>
                    </template>

                </div>
            </div>
        </template>
        <template x-if="!isMultipleCollection">
            <div class="image-container" x-show="!jQuery.isEmptyObject(image)" x-transition>
                <div class="imagebox text-center" >
                    <i @click.prevent="unSelect(image)" class="close-icon fa fa-times" aria-hidden="true" title="Supprimer l'image" style="font-size: 1.5rem;display: none;cursor: pointer;color: red"></i>
                        <a :href="image.url" id="front-image" data-fancybox="gallery" :data-caption="image.name">
                            <img style="height:250px" class="img-thumbnail " :src="image.url">
                        </a>
                </div>
            </div>
        </template>
        <div class="mt-4 d-flex justify-content-center">
            <button  type="button" class="btn btn-info btn-sm" id="change-{{ $collection }}">
                <i class="fa fa-undo"></i> <span x-text="getButtonLabel()"></span>
            </button>
        </div>

        @filemanagerButton([
            'target'     => "#change-{$collection}",
            'form_name'  => $form_name ?? $model->form_name,
            'collection' => $collection,
            'model'      => $model,
            'type'       => $type ?? 'image', // image or file
        ])
    </div>
</div>

@once
    @push('css')
    <link rel="stylesheet" href="{{ asset('css/vendor/jquery.fancybox.min.css') }}">
    @endpush
@endonce

@once
    @push('js')
    <script src="{{ asset('js/vendor/jquery.fancybox.min.js') }}"></script>
    @endpush
@endonce

@push('js')
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('{{ $component_name }}', () => ({
            image: @json($model->$collection ?? []),
            model: @json($model),
            form_name: @json($form_name ?? $model->form_name),
            collection: @json($collection),
            button_label: @json($button_label ?? ''),
            routeprefix: "{{ config('administrable.auth_prefix_path') }}",
            config:     @json(config('administrable.media')),
            files: [],
            selected_files : [],

            init(){
                document.addEventListener('filemanagerclosed-' + this.collection, ({detail}) => {
                    this.files          = detail.files
                    this.selected_files = detail.selected

                    this.image = this.getSelectedFiles() || {}
                })


                this.$nextTick(() => {
                    this.attachUnselectEvents()
                });

                this.appendDataToForm()
            },

            getButtonLabel(){
                if (this.button_label){
                    return this.button_label
                }

                if (this.isMultipleCollection){
                    return "Ajouter une image"
                }

                return "Changer l'image"
            },
            attachUnselectEvents(){
                const $root = $(this.$refs.root)

                $root.on('mouseenter', '.imagebox', this.handleMouseEnter)
                $root.on('mouseleave', '.imagebox', this.handleMouseLeave)
            },
            dispatchRefreshEvent(){
                this.$dispatch('filemanagerrefresh' + this.collection)
            },
            unSelect(file){
                const url = this.isCreateMode() ?
                                `/${this.routeprefix}/temporarymedia/${file.id}/unselect` :
                                `/${this.routeprefix}/media/${file.id}/unselect`

                axios.post(url)
                    .then(({ data }) => {
                        if (this.isMultipleCollection){
                            this.image = this.image.filter(image => image.id != file.id)
                        }else {
                            this.image = {}
                        }
                    })

                this.dispatchRefreshEvent()
            },
            unSelectAll(){
                this.image.forEach(image => this.unSelect(image))
            },
            isCreateMode(){
                return this.model.id == null
            },
            getCollection(){
                const collection_name = this.collection.split('-')[0]

                return this.config.collections[collection_name];
            },
            getSelectedFiles(){
                const selected_images = this.selected_files

                if (!this.isMultipleCollection){
                    return selected_images[0]
                }
                return selected_images
            },
            handleMouseEnter(event) {
                const target = jQuery(event.currentTarget)

                if (target.hasClass('selected')) {
                    target.find('.close-icon').hide()
                    return
                }

                target.find('.close-icon').show()
                target.find('img').css('border', '3px solid red')
            },
            handleMouseLeave(event) {
                const target = jQuery(event.currentTarget)

                if (target.hasClass('selected')) {
                    target.find('.close-icon').hide()
                    return
                }

                target.find('.close-icon').hide()
                target.find('img').css('border', 'none')
            },

            appendDataToRequest(data){
                if (!Array.isArray(data)){
                    data = [data]
                }
                data.forEach(item => {
                    const input = document.createElement('input')
                    input.type  = 'hidden'
                    input.name  = item.name
                    input.value = JSON.stringify(item.value)

                    this.form.append(input)
                })
            },
            appendDataToForm(){
                this.form.on('submit', () => {
                    if (this.files.length == 0){
                        return
                    }

                    this.appendDataToRequest(
                        { name: `filemanager[${this.collection}]`, value: this.files.map(file => file.id)
                    })
                })
            },
            get form(){
                return jQuery(`form[name=${this.form_name}]`)
            },
            get isMultipleCollection(){
                return this.getCollection()['multiple'] || false;
            },
        }));
    });
</script>
@endpush
