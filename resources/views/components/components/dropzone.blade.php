@props([
    'collection', 'message', 'label', 'model', 'maxFiles', 'key', 'paramName', 'uploadMultiple',
    'maxFileSize', 'addRemoveLinks', 'formName'
])

@php
    $selector = 'dropzone' . strtolower(Str::random(20));

    if (isset($model) && is_string($model)){
        $model = new $model;
    }

    $model_classname = base64_encode(is_object($model) ? get_class($model) : (string) $model);

    if (!isset($formName)){
        $formName = $model->form_name ;
    }

    $collection ??= config('administrable.media.collections.images.label');

    if (!isset($key) && is_object($model) && method_exists($model, 'getKey')){
        $key = $model->getKey();
    }

    $url = isset($key) && $key ? route('dropzone.media.store', [$model_classname, $key, $collection]) : route('dropzone.temporarymedia.store', [$model_classname, $collection, base64_encode(request()->path())]);

    if ($key){
        if (!isset($files) && is_object($model)){
            $files = $model->getMedia($collection)->toArray();
        }
    }else {
        $files = config("administrable.modules.filemanager.temporary_model")::fetchMediaInOptions($collection, $model_classname, request()->path())->toArray();
    }
@endphp


@once
    @push('css')
    <link rel="stylesheet" href="{{ asset('vendor/components/dropzone/css/dropzone.min.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/components/dropzone/css/custom.min.css') }}">
    @endpush

    @push('js')
    <script src="{{ asset('js/vendor/axios.js') }}"></script>
    <script src="{{ asset('vendor/components/dropzone/js/dropzone.min.js') }}"></script>
    @endpush
@endonce

<div class="form-group">
    <label for="">{{ $label ?? 'Photos' }} <span id="counter"></span></label>
    <div class="form-item dropzone {{ $selector }}"></div>
</div>

<div id="{{ $selector }}" style="display: none;">
    <div class="dz-preview dz-file-preview">
        <div class="dz-image"><img data-dz-thumbnail /></div>

        <div class="dz-details">
            <div class="dz-size"><span data-dz-size></span></div>
            <div class="dz-filename"><span data-dz-name></span></div>
        </div>
        <div class="dz-progress"><span class="dz-upload" data-dz-uploadprogress></span></div>
        <div class="dz-error-message"><span data-dz-errormessage></span></div>
    </div>
</div>

@once
    @push('js')
    <script>
        Dropzone.autoDiscover = false;

        function dropzoneEditMode(){
            const key =  @json($key);
            return key != null;
        }

        let dropzone = {
            removePreviewedElement(file){
                let _ref;
                if (file.previewElement) {
                    if ((_ref = file.previewElement) != null) {
                        _ref.parentNode.removeChild(file.previewElement);
                    }
                }
            },
            isNotPersistedFile(file){
                return !file.id
            },
            removeFileOnServer(file){
                if (dropzoneEditMode()) {
                    axios.delete("/dropzone/" + file.id)
                        .then(({ data }) => {
                            this.removePreviewedElement(file)
                        })
                }else {
                    axios.delete("/dropzone/temporarymedia/" + file.id)
                        .then(({ data }) => {
                            this.removePreviewedElement(file)
                        })
                }

            },
            sweetalertIsLoaded(){
                return typeof window.swal == 'function'
            },
            maxFilesExceeded(file){
                if (this.sweetalertIsLoaded()){
                    swal({
                        title: "Limite atteinte",
                        text: "Vous ne pouvez pas transferer d'image de plus. Cette image ne sera donc pas ajoutée.",
                        icon: 'info',
                    })
                    .then((isConfirm) => {
                        this.removePreviewedElement(file)
                    })
                }else {
                    window.alert("Vous ne pouvez pas transferer d'image de plus. Cette image ne sera donc pas ajoutée.")
                    this.removePreviewedElement(file)
                }
            },
            removeFile: function(file){
                if (this.isNotPersistedFile(file)){
                    this.removePreviewedElement(file)
                    return
                }

                if (this.sweetalertIsLoaded()){
                    swal({
                        title: 'Suppression !',
                        text: 'Etes vous sûr de vouloir supprimer ce fichier ? ',
                        icon: 'warning',
                        dangerMode: true,
                        buttons: {
                            cancel: 'Annulez',
                            confirm: 'Confirmez!'
                        }
                    })
                    .then((isConfirm) => {
                        if (isConfirm){
                            this.removeFileOnServer(file)

                        }
                    })
                }else {
                    if(window.confirm('Etes vous sûr de vouloir supprimer ce fichier ? ')){
                        this.removeFileOnServer(file)
                    }
                }
            },
        };
    </script>
    @endpush
@endonce

@push('js')
<script>
    new Dropzone(".{{ $selector }}",{
        url: "{{ $url }}",
        createImageThumbnails: @json($createImageThumbnails ?? true),
        paramName: @json($paramName ?? 'file'), // The name that will be used to transfer the file
        uploadMultiple: @json($uploadMultiple ?? true),
        method: 'POST',
        headers:{'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        parallelUploads: 1,
        maxFilesize: @json($maxFileSize ?? 10),
        previewTemplate: document.querySelector('#{{ $selector }}').innerHTML,
        maxFiles: null,
        acceptedFiles: 'image/*',
        addRemoveLinks: @json($addRemoveLinks ?? true),
        dictDefaultMessage: @json($message ?? "Cliquer ou glisser déposer l'image ici"),
        dictCancelUpload: "Annuler Envoyer",
        dictRemoveFile: 'Supprimer le fichier',
        dictInvalidFileType: 'Ce type de fichier n\'est pas autorisé (uniquement les images)',
        dictCancelUploadConfirmation: "Etes vous sur de vouloir avorter cet transfert?",
        dictRemoveFile: "Supprimer ce fichier",
        dictUploadCanceled: "Le transfert a été avorté.",
        dictMaxFilesExceeded: "Vous ne pouvez pas transferer d'image de plus. Retirez l'ancienne avant d'ajouter une nouvelle.",
        dictFileTooBig: 'L\'image ne doit pas dépasser {{ $max_file_size ?? 2 }}MO',
        timeout: 10000,
        uploadedFilesOnServer: @json(Arr::wrap($files)),
        formName: @json($formName),
        fileKeys: [],

        init: function(){
            let myDropzone = this;

            myDropzone.options.uploadedFilesOnServer.forEach(file => {
                myDropzone.options.fileKeys = [...myDropzone.options.fileKeys, file.id]
                myDropzone.displayExistingFile( { name: file.name, size: file.size, id: file.id }, file.url);
            })

            if (myDropzone.options.maxFiles){
                myDropzone.options.maxFiles = myDropzone.options.maxFiles - myDropzone.options.uploadedFilesOnServer.length
            }

            if (!dropzoneEditMode()){
                document.querySelector(`form[name=${myDropzone.options.formName}]`).addEventListener('submit', function(event){
                    const input = document.createElement('input')
                    input.type  = 'hidden'
                    input.name  = `filemanager[{{ $collection }}]`
                    input.value = JSON.stringify(myDropzone.options.fileKeys)

                    event.target.append(input)
                })
            }
        },
        success(file, uploadedFile){
            file.id = uploadedFile.id

            this.options.fileKeys = [...this.options.fileKeys, file.id]
        },
        removedfile(file) {
            dropzone.removeFile(file)
        },
    });
</script>
@endpush



