<!-- Modal -->
@php
    $componentName = 'editpage' . Str::random(10) . now()->format('dmY');
@endphp
<div class="modal fade" id="editMetaModal{{ $meta->getKey() }}" tabindex="-1" role="dialog"
    aria-labelledby="addPageMetaDataModalLabel" aria-hidden="true">

    <div  x-data="{{ $componentName }}" class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addPageMetaDataModalLabel">{{ Lang::get('administrable::messages.default.edition') }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form @submit='handleSubmit' action="{{ back_route('pagemeta.update', [$page, $meta]) }}" method="post" name="addPageMeta"
                enctype="multipart/form-data">
                @csrf
                <div class="modal-body" style="max-height: 34.375rem">
                    <input type="hidden" name="page_id" value="{{ $page->getKey() }}">

                    <div class="form-group d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary ">&nbsp;<i class="fa fa-save"></i>&nbsp;
                            &nbsp;{{ Lang::get('administrable::messages.default.save') }}</button>
                    </div>
                    <div class="form-row">

                        <div class="col">
                            <div class="form-group">
                                <label>{{ Lang::get('administrable::messages.view.pagemeta.code') }} <span class="text-red">*</span></label>
                                <input type="text" class="form-control" name="code" value="{{ $meta->code }}" readonly>
                            </div>
                        </div>
                        <div class="col">
                            <div class="form-group">
                                <label>{{ Lang::get('administrable::messages.view.pagemeta.name') }} <span class="text-red">*</span></label>
                                <input type="text" class="form-control" name="name" value="{{ $meta->name }}">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>{{ Lang::get('administrable::messages.view.pagemeta.title') }} </label>
                                <input type="text" class="form-control" name="title" value="{{ $meta->title }}">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="">{{ Lang::get('administrable::messages.view.pagemeta.type') }}</label>
                                <select name="type" class="custom-select" disabled>
                                    @foreach(AdminModule::model('pagemeta')::TYPES as $type)
                                    <option value="{{ $type['value'] }}"
                                        {{ $type['value'] == $meta->type ? 'selected' : '' }}>{{ $type['label'] }}
                                    </option>
                                    @endforeach
                                </select>
                                <input type="hidden" name="type" value="{{ $meta->type }}">
                            </div>
                        </div>
                        @if($meta->parent)
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="">{{ Lang::get('administrable::messages.view.pagemeta.group') }}
                                    @role('super-' . config('administrable.guard'), config('administrable.guard'))
                                        @if ($meta->parent)
                                        | {{ $meta->parent->code }}
                                        @endif
                                    @endrole
                                </label>
                                <select name="parent_id" class="custom-select" disabled>
                                    <option value="{{ $meta->parent->getKey() }}" selected>
                                        {{ $meta->parent->title }}
                                    </option>
                                </select>
                                <input type="hidden" name="parent_id" value="{{ $meta->parent->getKey() }}">
                            </div>
                        </div>
                        @endif
                    </div>

                    <div class="form-group" x-show="isTextType">
                        <label for="content{{ $page->id }}">{{ Lang::get('administrable::messages.view.pagemeta.content') }} </label>
                        <textarea name="textcontent" id="content{{ $page->id }}" class="form-control"
                            data-tinymce>@if($meta->isText()){{ $meta->content }}@endif</textarea>
                    </div>

                    <div class="form-group" x-show="isSimpleTextType">
                        <label for="simpletextcontent{{ $page->id }}">{{ Lang::get('administrable::messages.view.pagemeta.content') }} </label>
                        <textarea name="simpletextcontent" id="simpletextcontent{{ $page->id }}"
                            class="form-control">@if($meta->isSimpleText()){{ $meta->content }}@endif</textarea>
                    </div>


                    <div class="form-group" x-show="isImageType">
                        <label for="imageField">{{ Lang::get('administrable::messages.view.pagemeta.chooseimage') }}</label>
                        <input @change='handleImage' type="file" accept="image/*" class="form-control" id="imageField" name="imagecontent">
                        <input type="hidden" name="imagecontent" value="{{ $meta->content }}">
                    </div>

                    <div class="form-group" x-show="isVideoType">
                        <label for="videoField">{{ Lang::get('administrable::messages.view.pagemeta.videourl') }}</label>
                        <input x-model='video_url' type="url" class="form-control" id="videoField" name="videocontent"
                            placeholder="https://youtube.com?v=74df8g585" value="{{ $meta->video_url }}">
                    </div>

                    <div class="form-group" x-show="isAttachedType">
                        <label for="attachedField">{{ Lang::get('administrable::messages.view.pagemeta.otherfiles') }}</label>
                        <input type="file" class="form-control" id="attachedField" name="attachedfilecontent">
                        <input type="hidden" name="videocontent" value="{{ $meta->content }}">
                        @if($meta->isAttachedFile())
                        <a class="badge badge-primary pt-2" href="{{ $meta?->attachedfile?->getUrl() }}" title="{{ $meta->attachedfile->getUrl() }}" target="_blank">Ouvrir le fichier dans un nouvel onglet</a>
                        @endif
                    </div>

                    <div x-show='isImageType' class="thumbnail text-center" >
                        <img :src="image_url" alt="" class="img-fluid img-thumbnail" style="height: 145px; overflow: scroll;">
                    </div>

                    <div x-show='isVideoType' class="thumbnail">
                        <div class="embed-responsive embed-responsive-16by9" style="height: 200px;">
                            <iframe class="embed-responsive-item" :src="video_url" allowfullscreen></iframe>
                        </div>
                    </div>

        </div>
        <div class="modal-footer btn-group">
            <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fa fa-undo"></i>
                &nbsp;{{ Lang::get('administrable::messages.default.cancel') }}</button>
            <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> &nbsp;{{ Lang::get('administrable::messages.default.save') }}</button>
        </div>
        </form>
    </div>
</div>
</div>
@push('css')
<style>
    [data-tinymce] {
        height: 270px !important;
    }
</style>
@endpush
@push('js')
<script>
     document.addEventListener('alpine:init', () => {
        Alpine.data('{{ $componentName }}', () => ({
            image_url: null,
            video_url: null,
            meta: @json($meta),
            meta_types: @json(AdminModule::model('pagemeta')::TYPES),

            init(){
                this.$nextTick(() => {
                    if (this.isImageType())
                        this.image_url = this.meta.image_url

                    if (this.isVideoType()){
                        this.video_url = this.meta.video_url
                    }
                })
            },
            handleSubmit(event){
                if (this.isImageType()){
                    if (!this.image_url){
                        alert("Ce type d'image n'est pas autorisé.")
                        event.preventDefault();
                    }
                }

                if (this.isVideoType()){
                    if (!this.video_url){
                        alert("Ce type de vidéo n'est pas autorisé.")
                        event.preventDefault();
                    }
                }

            },
            handleImage(event){
                const reader = new FileReader()
                const image = event.target.files[0]

                if (!this.validateFile(image)) {
                    return
                }

                reader.readAsDataURL(image)

                reader.onload = (event) => {
                    this.image_url = event.target.result
                }
            },
            validateFile(image) {
                const ext = image.name.substring(image.name.lastIndexOf('.') + 1).toLowerCase()

                if (['png', 'jpg', 'gif', 'jpeg', 'svg'].includes(ext)) {
                    return true
                } else {
                    alert("Erreur lors du traitement de l'image `" + image.name + '`. Veuillez choisir une image de type (jpg, jpeg, png,svg).',)
                    return false
                }
            },
            isTextType(){
                return this.isType('text')
            },
            isSimpleTextType(){
                return this.isType('simpletext')
            },
            isImageType(){
                return this.isType('image')
            },
            isVideoType(){
                return this.isType('video')
            },
            isAttachedType(){
                return this.isType('attachedfile')
            },
            isType(key){
                 return this.meta.type == this.meta_types[key].value
            },
        }))
    })
</script>
@endpush
