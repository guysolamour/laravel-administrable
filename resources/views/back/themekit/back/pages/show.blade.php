@extends(back_view_path('layouts.base'))

@section('title', $page->name)

@section('content')
<div class="main-content">
    <div class="container-fluid">
        <div class="page-header">
            <div class="row align-items-end">
                <div class="col-lg-8">
                </div>
                <div class="col-lg-4">
                    <nav class="breadcrumb-container" aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item">
                                <a href="{{ route(config('administrable.guard') . '.dashboard') }}"><i class="ik ik-home"></i></a>
                            </li>
                            <li class="breadcrumb-item">
                                <a href="{{ back_route('page.index') }}">{{ Lang::get("administrable::messages.view.page.plural") }}</a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">{{ $page->name }}</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <h3 class="card-title">Page: {{ $page->name }}</h3>
                        <div class="btn-group float-right">
                            @include(back_view_path('pages._addmetataform'))

                            <a class="btn btn-info" href="{{ back_route('page.edit', $page) }}">
                                <i class="fas fa-edit"></i> {{ Lang::get("administrable::messages.default.modify") }}</a>
                        </div>
                    </div>

                    <div class="card-body row">
                        <div class="col-md-12">
                            <div class="row">
                                {{-- add fields here --}}
                               <div class="col-md-6">
                                    <p><span class="font-weight-bold">{{ Lang::get("administrable::messages.view.page.name") }}:</span></p>
                                    <p>
                                        {{ $page->name }}
                                    </p>
                                </div>
                                <div class="col-md-6">
                                    <p><span class="font-weight-bold">{{ Lang::get("administrable::messages.view.page.route") }}:</span></p>
                                    <p>
                                        {{ $page->route }}
                                    </p>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <p><span class="font-weight-bold">{{ Lang::get("administrable::messages.view.page.route") }}:</span></p>
                                    @if($page->uri)
                                    <p class="pb-2 "><b>{{ Lang::get("administrable::messages.view.page.uri") }}: </b><a href="{{ $page->uri }}" class="text-primary" target="_blank">{{ $page->uri }}</a>
                                    </p>
                                    @else
                                    {{ Lang::get("administrable::messages.view.page.nouri") }}
                                    @endif
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

        </div>

        <div class="row">
            @foreach($page->metatags as $group)
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                       <button class="btn btn-link font-weight-bold text-uppercase text-dark" type="button">
                            {{ Lang::get('administrable::messages.view.pagemeta.group') }}: {{ $group->name }} ({{ $group->children->count() }})
                            @role('super-' . config('administrable.guard'), config('administrable.guard'))
                            | {{ $group->code }}
                            @endrole
                        </button>
                        <div class="btn-group float-right">
                            <button class="btn btn-secondary btn-sm" title="Voir le contenu du groupe" data-toggle="collapse"
                                data-target="#collapseExample{{ $group->id }}" aria-expanded="false"
                                aria-controls="collapseExample{{ $group->id }}"><i class="fa fa-eye"></i></button>
                            <button class="btn btn-info btn-sm" data-toggle="modal" data-target="#editMetaModal{{ $group->id }}">
                                <i class="fas fa-edit"></i> </button>
                            @include(back_view_path('pages._updatemetaform'), ['meta' => $group])
                            @include(back_view_path('pages._addmetataform'), ['group' => $group])
                            <a href="{{ back_route('pagemeta.destroy', [$page, $group]) }}" data-method="delete"
                                data-confirm="{{ Lang::get('administrable::messages.view.pagemeta.destroy') }}"
                                class="btn btn-danger btn-sm" data-toggle="tooltip" data-placement="top" title="{{ Lang::get('administrable::messages.default.delete') }}"><i
                                    class="fas fa-trash"></i> </a>
                        </div>
                    </div>

                    <div class="card-body">
                        <div class="collapse" id="collapseExample{{ $group->id }}">
                            <div class="row">
                                @forelse($group->children->reverse() as $meta)
                                <div class="col-md-6 mb-4">
                                    <div>
                                        <h5 class="font-weight-bold ">
                                            {{ Lang::get('administrable::messages.view.pagemeta.group') }}: {{ $group->name }} ({{ $group->children->count() }})
                                            @role('super-' . config('administrable.guard'), config('administrable.guard'))
                                            | {{ $group->code }}
                                            @endrole
                                        </h5>

                                        <p>
                                            @if ($meta->isImage())
                                            <img src="{{ $meta->image_url }}" alt="{{ $meta->title }}" class="img-fluid img-thumbnail"
                                                style="height: 300px">
                                            @elseif($meta->isVideo())
                                            <div class="embed-responsive embed-responsive-16by9"
                                                style="display: {{ $meta->isVideo() ? 'block' : 'none' }}"">
                                                                                                                                                                                                                                                                <iframe class="
                                                embed-responsive-item" src="{{$meta->video_url }}" allowfullscreen>
                                                </iframe>
                                            </div>
                                            @elseif($meta->isVideo())
                                            <div class="embed-responsive embed-responsive-16by9"
                                                style="display: {{ $meta->isVideo() ? 'block' : 'none' }}"">
                                                                                                                                                                                                                                                                        <iframe class="
                                                embed-responsive-item" src="{{$meta->video_url }}" allowfullscreen>
                                                </iframe>
                                            </div>
                                            @elseif($meta->isAttachedFile())
                                            <a href="{{ $meta->attachedfile->getUrl() }}" target="_blank">{{ $meta->attachedfile->name}}</a>
                                            @else
                                            {!! $meta->content !!}
                                            @endif
                                        </p>
                                        <div class="btn-group float-right">
                                            <a class="btn btn-info btn-sm" href="#" data-toggle="modal"
                                                data-target="#editMetaModal{{ $meta->id }}">
                                                <i class="fas fa-edit"></i> &nbsp;{{ Lang::get('administrable::messages.default.modify') }} </a>

                                            @role('super-' . config('administrable.guard'), config('administrable.guard'))
                                            <a href="{{ back_route('pagemeta.destroy', [$page, $meta]) }}" data-method="delete"
                                                data-confirm="{{ Lang::get('administrable::messages.view.pagemeta.destroy') }}"
                                                class="btn btn-danger btn-sm" data-toggle="tooltip" data-placement="top" title="{{ Lang::get('administrable::messages.default.delete') }}"><i
                                                    class="fas fa-trash"></i>
                                                &nbsp;{{ Lang::get('administrable::messages.default.delete') }}</a>
                                            @endrole
                                        </div>

                                    </div>
                                    @include(back_view_path('pages._updatemetaform'), ['meta' => $meta])
                                    <hr>
                                </div>
                                @empty
                                <div class="col-12 text-center">
                                   {{ Lang::get('administrable::messages.view.pagemeta.emptygroup') }}
                                </div>
                                @endforelse
                            </div>
                        </div>

                    </div>
                </div>
            </div>
            @endforeach
        </div>
        </div>
    </div>
</div>

<x-administrable::tinymce :model="$page" />

@endsection

@push('js')
<script>
    class PageMeta
       {
        static init (){
            $('form[name=addPageMeta]').each((index,form) => new PageMeta($(form)))
            // new PageMeta()
        }

        constructor(form){
            this.binds()

            this.form = form //$('form[name=addPageMeta]')


            this.selectField = this.form.find('select[name=type]')
            this.groupField = this.form.find('select[name=parent_id]')
            this.textareaField = this.form.find('textarea[name=textcontent]')
            this.simpletextareaField = this.form.find('textarea[name=simpletextcontent]')
            this.imageField = this.form.find('input[name=imagecontent]')
            this.attachedFileField = this.form.find('input[name=attachedfilecontent]')
            this.videoField = this.form.find('input[name=videocontent]')
            this.thumbnail = this.form.find('.thumbnail')
            this.types = @json(\Guysolamour\Administrable\Models\PageMeta::TYPES) // render by blade


            this.currentType = 'text'

            // debugger

            this.addEvents()

        }

        binds() {
            const methods = [
                'addEvents', 'handleChange', 'showImageField', 'showTextField', 'showVideoField',
                'validateFile', 'handleSubmit', 'handleImage', 'handleVideo'
            ]

            methods.forEach((fn) => this[fn] = this[fn].bind(this))
        }

        addEvents(){
            this.selectField.on('change', this.handleChange)
            this.form.on('submit', this.handleSubmit)
            this.imageField.on('change', this.handleImage)
            this.videoField.on('blur', this.handleVideo)
        }

        handleSubmit(event){

            if (this.currentType === 'image'){
                if (!this.thumbnail.find('img').attr('src')){
                    alert("Ce type d'image n'est pas autorisé.")
                    return false
                }
            }

            if(this.currentType === 'video'){
                if (!this.thumbnail.find('div').find('iframe').attr('src')){
                    alert("Ce type de vidéo n'est pas autorisé.")
                    return false
                }
            }

            if (!this.form.find('input[name=code]').val()){
                alert("Le champ code est obligatoire.")
                return false
            }
        }

        handleChange(event){
            const value = event.target.value

            if (value == this.types.text.value) {
                this.showTextField()
            }
            else if (value == this.types.image.value) {
                this.showImageField()
            }
            else if (value == this.types.video.value) {
                this.showVideoField()
            }
            else if (value == this.types.simpletext.value) {
                this.showSimpleTextField()
            }
            else if (value == this.types.attachedfile.value) {
                this.showAttachedFileField()
            }
            else if (value == this.types.group.value) {
                this.showGroupFileField()
            }


        }

        showImageField(){
            this.currentType = 'image'
            this.textareaField.attr('disabled', true).parents('.form-group').hide()
            this.simpletextareaField.attr('disabled', true).parents('.form-group').hide()
            this.videoField.attr('disabled', true).parents('.form-group').hide()
            this.attachedFileField.attr('disabled', true).parents('.form-group').hide()
            this.imageField.attr('disabled', false).parents('.form-group').show()
            this.groupField.parent().show()
            this.thumbnail.find('div').hide()
            this.thumbnail.find('img').show()
            // this.previewImage()

        }

        showAttachedFileField(){
            this.currentType = 'attachedfile'
            this.textareaField.attr('disabled', true).parents('.form-group').hide()
            this.simpletextareaField.attr('disabled', true).parents('.form-group').hide()
            this.videoField.attr('disabled', true).parents('.form-group').hide()
            this.imageField.attr('disabled', false).parents('.form-group').hide()
            this.attachedFileField.attr('disabled', false).parents('.form-group').show()
            this.groupField.parent().show()
        this.thumbnail.find('div').hide()
        this.thumbnail.find('img').hide()
            // this.previewImage()

        }

        showGroupFileField(){
            this.currentType = 'group'
            this.textareaField.attr('disabled', true).parents('.form-group').hide()
            this.simpletextareaField.attr('disabled', true).parents('.form-group').hide()
            this.videoField.attr('disabled', true).parents('.form-group').hide()
            this.imageField.attr('disabled', false).parents('.form-group').hide()
            this.attachedFileField.attr('disabled', false).parents('.form-group').hide()
            this.groupField.parent().hide()
           this.thumbnail.find('div').hide()
           this.thumbnail.find('img').hide()
            // this.previewImage()

        }

        handleImage(){
            const reader = new FileReader()
            const image = event.target.files[0]


            if (!this.validateFile(image)) {
                this.thumbnail.find('img').attr('src', '').hide()
                return
            }

            reader.readAsDataURL(image)

            reader.onload = (event) => {
                this.thumbnail.find('div').hide()
                this.thumbnail.find('img').attr('src', event.target.result).show()
            }
        }

        validateFile(image) {

            const ext = image.name.substring(image.name.lastIndexOf('.') + 1).toLowerCase()
            // alert(ext)

            if (['png', 'jpg', 'gif', 'jpeg', 'svg'].includes(ext)) {
                return true
            } else {
                alert("Erreur lors du traitement de l'image `" + image.name + '`. Veuillez choisir une image de type (jpg, jpeg, png,svg).',)
                return false
            }
        }

        validURL(str) {
            try {
                new URL(str);
                return true;
            } catch (_) {
                return false;
            }
        }

        showTextField(){
            this.currentType = 'text'
            this.videoField.attr('disabled', true).parents('.form-group').hide()
            this.imageField.attr('disabled', true).parents('.form-group').hide()
            this.simpletextareaField.attr('disabled', true).parents('.form-group').hide()
            this.attachedFileField.attr('disabled', true).parents('.form-group').hide()
            this.thumbnail.find('div').hide()
            this.thumbnail.find('img').hide()
            this.textareaField.attr('disabled', false).parents('.form-group').show()
            this.groupField.parent().show()


        }

        showSimpleTextField(){
            this.currentType = 'simpletext'
            this.videoField.attr('disabled', true).parents('.form-group').hide()
            this.imageField.attr('disabled', true).parents('.form-group').hide()
            this.attachedFileField.attr('disabled', true).parents('.form-group').hide()
            this.textareaField.attr('disabled', true).parents('.form-group').hide()
            this.thumbnail.find('div').hide()
            this.thumbnail.find('img').hide()
            this.simpletextareaField.attr('disabled', false).parents('.form-group').show()

        }

        showVideoField(){
            this.currentType = 'video'
            this.imageField.attr('disabled', true).parents('.form-group').hide()
            this.attachedFileField.attr('disabled', true).parents('.form-group').hide()
            this.textareaField.attr('disabled', true).parents('.form-group').hide()
            this.simpletextareaField.attr('disabled', true).parents('.form-group').hide()
            this.videoField.attr('disabled', false).parents('.form-group').show()
            this.thumbnail.find('div').show()
            this.thumbnail.find('img').hide()

            // this.previewVideo()
        }

        handleVideo() {
            if(!this.validURL(event.target.value)) {
                this.thumbnail.find('div').find('iframe').attr('src','')
                alert("Le lien de la vidéo n'est pas valide")
                return
            }
            this.thumbnail.find('img').hide()
            this.thumbnail.find('div').find('iframe').attr('src', event.target.value).show()
        }

    }

    PageMeta.init();

</script>
@endpush
