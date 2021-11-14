@php
    $componentName = 'addpagemeta' . Str::random(10) . now()->format('dmY');
@endphp

@if(get_guard()->hasRole('super-' . config('administrable.guard'), config('administrable.guard')))
    @if(isset($group) && $group)
    <button class="btn btn-secondary" data-toggle="modal" title="{{ Lang::get('administrable::messages.view.pagemeta.addgroup') }}"
        data-target="#addPageMetaDataModalGroup{{ $group->getKey() }}">
        <i class="fas fa-plus"></i> &nbsp;</button>
    @else
    <button class="btn btn-primary" data-toggle="modal" title={{ Lang::get('administrable::messages.view.pagemeta.addfield') }}"
        data-target="#addPageMetaDataModal">
        <i class="fas fa-plus"></i> &nbsp;{{ Lang::get('administrable::messages.view.pagemeta.addfield') }}</button>
    @endif

<!-- Modal -->
@if(isset($group) && $group)

<div  class="modal fade" id="addPageMetaDataModalGroup{{ $group->getKey() }}" tabindex="-1" role="dialog"
    aria-labelledby="addPageMetaDataModalLabel{{ $group->getKey() }}" aria-hidden="true">
    @else
    <div   class="modal fade" id="addPageMetaDataModal" tabindex="-1" role="dialog"
        aria-labelledby="addPageMetaDataModalLabel" aria-hidden="true">
        @endif
        >
        <div x-data="{{ $componentName }}"  class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    @if(isset($group) && $group)
                    <h5 class="modal-title" id="addPageMetaDataModalLabel{{ $group->getKey() }}">
                        {{ Lang::get('administrable::messages.view.pagemeta.addfield') }}
                        : {{ $group->name }} </h5>
                    @else
                    <h5 class="modal-title" id="addPageMetaDataModalLabel">{{ Lang::get('administrable::messages.view.pagemeta.addfield') }}</h5>
                    @endif
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form @submit='handleSubmit'  action="{{ back_route('pagemeta.store', $page) }}" method="post" name="addPageMeta"
                    enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body" style="max-height: 34.375rem">
                        <input type="hidden" name="page_id" value="{{ $page->getKey() }}">

                        <div class="form-group d-flex justify-content-end">
                            <div class="btn-group">
                                <button type="submit" class="btn btn-primary ">&nbsp;<i class="fa fa-save"></i>
                                    &nbsp; {{ Lang::get('administrable::messages.default.save') }}</button>
                                <button type="button" class="btn btn-secondary" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true"><i class="fa fa-times"></i> &nbsp;{{ Lang::get('administrable::messages.default.cancel') }}</span>
                                </button>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="col">
                                <div class="form-group">
                                    <label for="code{{ $page->getKey() }}">{{ Lang::get('administrable::messages.view.pagemeta.code') }} <span class="text-red">*</span>
                                    </label>
                                    <input x-model='code'  type="text"  class="form-control" name="code" id="code{{ $page->id }}">
                                </div>
                            </div>
                            <div class="col">
                                <div class="form-group">
                                    <label for="name{{ $page->getKey() }}">{{ Lang::get('administrable::messages.view.pagemeta.name') }} <span class="text-red">*</span> </label>
                                    <input type="text" class="form-control" name="name" id="name{{ $page->id }}">
                                </div>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="col">
                                <div class="form-group">
                                    <label for="title{{ $page->getKey() }}">{{ Lang::get('administrable::messages.view.pagemeta.title') }} </label>
                                    <input type="text" class="form-control" name="title" id="title{{ $page->id }}">
                                </div>
                            </div>
                            <div class="col">
                                <div class="form-group">
                                    <label for="image_field{{ $page->id }}">{{ Lang::get('administrable::messages.view.pagemeta.type') }}</label>
                                    <select x-model.number='current_type'  name="type" id="image_field{{ $page->id }}" class="custom-select">
                                        <template x-for="type in meta_types">
                                            <option :value="type.value" x-text="type.label"></option>
                                        </template>
                                    </select>
                                </div>
                            </div>

                            <div class="col">
                                <div class="form-group">
                                    <label for="">{{ Lang::get('administrable::messages.view.pagemeta.group') }}</label>
                                    <select name="parent_id" id="group_field{{ $page->getKey() }}" class="custom-select ">
                                        @if(isset($group) && $group)
                                        <option value="{{ $group['id'] }}" selected>{{ $group['name'] }}</option>
                                        @else
                                        @foreach($page->metatags as $group_field)
                                        <option value="{{ $group_field['id'] }}" {{
                                            (isset($group) && $group && $group['code'] === $group_field['code']) ||
                                            ($group_field['code'] === AdminModule::model('pagemeta')::DEFAULT_GROUP_CODE) ? 'selected' : ''
                                        }}>
                                            {{ $group_field['name'] }}
                                        </option>
                                        @endforeach

                                        @endif
                                    </select>
                                </div>
                            </div>
                        </div>

                       <div x-show="!isGroupType()">
                            <div class="form-group" x-show="isTextType">
                                <label for="content{{ $page->getKey() }}">{{ Lang::get('administrable::messages.view.pagemeta.content') }} </label>
                                <textarea name="textcontent" id="content{{ $page->getKey() }}" class="form-control"
                                    data-tinymce></textarea>
                            </div>

                            <div class="form-group" x-show="isSimpleTextType">
                                <label for="simpletextcontent{{ $page->getKey() }}">{{ Lang::get('administrable::messages.view.pagemeta.content') }} </label>
                                <textarea name="simpletextcontent" id="simpletextcontent{{ $page->getKey() }}"
                                    class="form-control"></textarea>
                            </div>
                            <div class="form-group"  x-show="isImageType">
                                <label for="imageField">{{ Lang::get('administrable::messages.view.pagemeta.chooseimage') }}</label>
                                <input @change='handleImage' type="file" accept="image/*" class="form-control" id="imageField"
                                    name="imagecontent">
                            </div>
                            <div class="form-group"  x-show="isVideoType">
                                <label for="videoField">{{ Lang::get('administrable::messages.view.pagemeta.videourl') }}</label>
                                <input x-model='video_url' type="url" class="form-control" id="videoField" name="videocontent"
                                    placeholder="https://youtube.com?v=74df8g585">
                            </div>
                            <div class="form-group"  x-show="isAttachedType">
                                <label for="attachedField">{{ Lang::get('administrable::messages.view.pagemeta.otherfiles') }}</label>
                                <input type="file" class="form-control" id="attachedField" name="attachedfilecontent">
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
                    </div>
                    <div class="modal-footer btn-group">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fa fa-times"></i>
                            &nbsp;{{ Lang::get('administrable::messages.default.cancel') }}</button>
                        <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i>
                            &nbsp;{{ Lang::get('administrable::messages.default.save') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif

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
            current_type: null,
            image_url: null,
            video_url: null,
            code: '',
            meta_types: @json(AdminModule::model('pagemeta')::TYPES),

            init(){
                this.$nextTick(() => {
                    this.current_type = this.meta_types['text'].value
                })
            },
            handleImage(){
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

                if (!this.code){
                    alert("Le champ code est obligatoire.")
                    event.preventDefault();
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
                 return  this.current_type == this.meta_types[key].value
            },
            isGroupType(){
               return this.isType('group')
            }
        }))
    })
</script>
@endpush




