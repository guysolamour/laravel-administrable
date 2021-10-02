@once
    @push('css')
    <link rel="stylesheet" href="{{ asset('vendor/filemanager/css/filemanager.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/filemanager/css/cropper.min.css') }}">
    @endpush
@endonce
@once
    @push('js')
    <script src="{{ asset('vendor/filemanager/js/cropper.min.js') }}"></script>
    <script src="{{ asset('vendor/filemanager/js/jquery.zoom.min.js') }}"></script>
    <script src="{{ asset('vendor/filemanager/js/sortable.js') }}"></script>

    <script>
        $(document).on('show.bs.modal', '.modal', function () {
            const zIndex = 2000 + (10 * $('.modal:visible').length);
            $(this).css('z-index', zIndex);
            setTimeout(function() {
                $('.modal-backdrop').not('.modal-stack').css('z-index', zIndex - 1).addClass('modal-stack');
            }, 0);
        });

        $(document).on('hidden.bs.modal', '.modal', function () {
            $('.modal:visible').length && $(document.body).addClass('modal-open');
        });
    </script>
    @endpush
@endonce

@push('js')
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('{{ $name }}', () => ({
        // Data
        model: @json($model),
        model_name: @json($model_name),
        collection: '{{ $collection }}',
        type:       '{{ $type }}',
        config:     @json($config),
        search: '',
        files: {
            droped: [],
            uploaded: [],
        },
        routeprefix: "{{ $routeprefix }}",
        isDropping: false,
        isUploading: false,
        uploadMessage: null,
        uploadMessageType: 'success',
        uploadingPercentage: 0,
        uploadingFilename: null,
        croping_file: {},
        selected_file: {},
        renaming_file: null,
        property_file: null,
        view_image: {},
        init(){
            this.$nextTick( _ => {
                this.handleCrop()
                this.handleSortable()
            })

            this.fetchUploadedMedia()

            window.addEventListener('filemanagerrefresh' + this.collection, (event) => {
                this.refresh()
                this.setSelectedImage()
            })
        },
        // Methods
        handleSortable() {
            const that = this

            const modal_sorter = new Sortable(this.getModal('media').querySelector('.modal-container'), {
                multiDrag: true,
                selectedClass: 'selected',
                animation: 150,
                store: {
                    set(sortable) {
                        that.saveSortableOrder(sortable.el.children)
                    }
                }
            })
        },
        getAllFilesIds(){
            return this.files.uploaded.map(file => file.id)
        },
        saveSortableOrder(items) {
            const ids = []

            for (let i = 1; i < items.length; i++) {
                ids.push({
                    id: parseInt(items[i].dataset.id, 10),
                    order: i
                })
            }
            const url = this.isCreateMode() ?
                        `/${this.routeprefix}/temporarymedia/order` :
                        `/${this.routeprefix}/media/order`

            axios.post(url, { ids })
        },
        download(file){
            const a = $(`
                <a href='${file.url}' download='${file.file_name}'></a>
                `).appendTo('body')

            // important afin de lancer le click natif du navigateur
            a[0].click()

            a.remove()
        },
        downloadAll() {
            swal({
                title: 'Téléchargement !',
                text: 'Etes vous sûr de vouloir télécharger tous les fichiers ? ',
                icon: 'warning',
                dangerMode: true,
                buttons: {
                    cancel: 'Annulez',
                    confirm: 'Confirmez!'
                }
            })
            .then((isConfirm) => {
                if (isConfirm) {
                    this.files.uploaded.forEach(file => this.download(file))
                }
            })
        },
        selectAll(){
            const url = this.isCreateMode() ?
                    `/${this.routeprefix}/temporarymedia/selectall` :
                    this.getUrl('/selectall')

            axios.post(url, {
                ids: this.getAllFilesIds(),
            })
            .then(({ data }) => {
                this.fetchUploadedMedia()
                this.selected_file = data[0]
            })

        },
        aFileIsSelected(){
            for (let i = 0; i < this.files.uploaded.length; i++) {
                const file = this.files.uploaded[i]

                if (file.select){
                    return true
                }
            }
            return
        },
        select(file){
            const url = this.isCreateMode() ?
                            `/${this.routeprefix}/temporarymedia/${file.id}/select` :
                            `/${this.routeprefix}/media/${file.id}/select`


            if (!this.isMultipleCollection && this.aFileIsSelected() && this.isCreateMode()){
                this.unSelectAll()
            }

             axios.post(url)
                .then(({ data }) => {
                    this.fetchUploadedMedia(false)

                    this.selected_file = data
                })
        },
        unSelect(file, fetch = true){
            const url = this.isCreateMode() ?
                            `/${this.routeprefix}/temporarymedia/${file.id}/unselect` :
                            `/${this.routeprefix}/media/${file.id}/unselect`

            axios.post(url)
                .then(({ data }) => {
                    this.selected_file = {}

                    if (fetch){
                        this.fetchUploadedMedia()
                    }
                })
        },
        toggleSelect(file){
            file.select ? this.unSelect(file) : this.select(file)
        },
        unSelectAll(){
            const url = this.isCreateMode() ?
                    `/${this.routeprefix}/temporarymedia/unselectall`:
                    this.getUrl('/unselectall')

            axios.post(url, {
                ids: this.getAllFilesIds(),
            })
            .then(({ data }) => {
                this.selected_file = {}
                this.fetchUploadedMedia()
            })
        },
        refresh(){
           this.fetchUploadedMedia()
        },
        sort(sorter){
            if (!this.filteredUploadFiles.length){
                return
            }

            if (sorter === 'asc') {
                this.filteredUploadFiles.sort((a, b) => {
                    if (a.name < b.name) { return -1 }
                    if (a.name > b.name) { return 1 }

                    return 0
                })
            } else if (sorter === 'desc') {
                this.filteredUploadFiles.sort((a, b) => {
                    if (a.name < b.name) { return 1 }
                    if (a.name > b.name) { return -1 }

                    return 0
                })
            }else if (sorter === 'datedesc') {
                this.filteredUploadFiles.sort((a, b) => {
                    return new Date(b.created_at) - new Date(a.created_at)
                })
            }
            else if (sorter === 'dateasc') {
                this.filteredUploadFiles.sort((a, b) => {
                    return new Date(a.created_at) - new Date(b.created_at)
                })
            }
            else if (sorter === 'default') {
                this.refresh()
            }
        },
        getModal(name){
            return document.querySelector('#' + this[name + 'Modal'])
        },
        fetchTemporaryMedia(select_file = true){
            axios.post(`/${this.routeprefix}/temporarymedia/option`, { collection: this.collection, model: this.model_name })
                .then(({ data }) => {
                   if (!data){
                       return
                   }

                   if (
                        data.path       !== window.location.pathname ||
                        data.collection !== this.collection ||
                        data.model_name !== this.model_name
                    ){
                        return
                    }

                   axios.post(`/${this.routeprefix}/temporarymedia/${this.collection}`, {
                        keys: data.keys
                    })
                    .then(({ data }) => {
                        this.files.uploaded = data

                        if (select_file){
                            this.setSelectedImage()
                        }
                        // event
                        this.dispatchFilemanagerClosedEvent();
                    })
                })
        },
        dispatchFilemanagerClosedEvent(){
            const files = JSON.parse(JSON.stringify(this.files.uploaded))
            const selected = JSON.parse(JSON.stringify(this.getSelectedFiles()))

            this.dispatchCustomEvent('filemanagerclosed', { files, selected })
        },
        setSelectedImage(){
            for (let i = 0; i < this.files.uploaded.length; i++) {
                const file = this.files.uploaded[i]

                if (file.select){
                    this.selected_file = file
                    return
                }

                this.selected_file = {}
            }
        },
        fetchUploadedMedia(select_file = true){
            if (this.isCreateMode()){
                this.fetchTemporaryMedia(select_file)
                return;
            }

            axios.get(this.getUrl())
              .then(({ data }) => {
                this.files.uploaded = data

                if (select_file){
                    this.setSelectedImage()
                }
              })
        },
        handleCrop(){
            const crop_modal  = jQuery('#' + this.cropModal)
            let cropper;
            const image       = crop_modal.find('.modal-body img')[0]
            const result      = crop_modal.find('.modal-body .result')[0]
            const button      = crop_modal.find('.modal-footer [data-crop="button"]')[0]

            crop_modal.on('shown.bs.modal',   () => {
                cropper = new Cropper(image, {
                    aspectRatio: 1,
                    viewMode: 2,
                    crop(event) {
                        result.src = cropper.getCroppedCanvas().toDataURL('image/jpeg');
                    },
                });
            }).on('hidden.bs.modal', function () {
                cropper.destroy();
                cropper = null;
                image.src = '';
                result.src = '';
            });

            button.onclick =  () => {
                const canvas = cropper.getCroppedCanvas()

                const formData = new FormData()
                formData.append('file', canvas.toDataURL('image/jpeg'))

                const url = this.isCreateMode() ?
                        `/${this.routeprefix}/temporarymedia/${this.croping_file.id}/modify` :
                        this.getUrl(`/modify/${this.croping_file.id}`)

                axios.post(url, formData)
                    .then(({ data }) => {
                        this.closeCropModal()
                        this.fetchUploadedMedia()
                    })
            };
        },
        modify(file){
            this.showCropModal(file)
        },
        handleRightClick(event){
            event.preventDefault()

            if ($(event.currentTarget).find('.dropdown-menu').is(":hidden")){
                $(event.currentTarget).find('.dropdown-toggle').dropdown('toggle')
            }
        },
        uploadProgress(progressEvent){
            this.uploadingPercentage = Math.round((progressEvent.loaded * 100) / progressEvent.total)
        },
        getUrl(url = null){
            let base_uri =  `/${this.routeprefix}/media/${btoa(this.model_name)}/${this.model.id}/${this.collection}`

            if (url){
                base_uri += url
            }

            return base_uri
        },
        copyUrl(event, file) {
            const button = $(event.target)

            const input = document.createElement('input')

            // Place in top-left corner of screen regardless of scroll position.
            input.style.position = 'fixed'
            input.style.top = 0
            input.style.left = '-100px'

            // Ensure it has a small width and height. Setting to 1px / 1em
            // doesn't work as this gives a negative w/h on some browsers.
            input.style.width = '2em'
            input.style.height = '2em'

            // We don't need padding, reducing the size if it does flash render.
            input.style.padding = 0

            // Clean up any borders.
            input.style.border = 'none'
            input.style.outline = 'none'
            input.style.boxShadow = 'none'

            // Avoid flash of white box if rendered for any reason.
            input.style.background = 'transparent'

            input.value = file.url


            button.parent().prepend(input)
            input.focus()
            input.select()

            document.execCommand('copy')

            button.parent().children().first().remove();
        },
        viewImage(file){
            this.openViewimageModal(file)
        },
        rename(file){
            const url = this.isCreateMode() ?
                            `/${this.routeprefix}/temporarymedia/${file.id}/rename` :
                            `/${this.routeprefix}/media/${file.id}/rename`

            axios.post(url, { name: this.renaming_file.name })
                .then(({ data }) => {
                    this.fetchUploadedMedia()
                    this.closeRenameModal()
                })
        },
        remove(file){
            if (!confirm("voulez-vous supprimer le fichier ?")){
                return
            }

            const url = this.isCreateMode() ?
                            `/${this.routeprefix}/temporarymedia/${file.id}` :
                            `/${this.routeprefix}/media/${file.id}`

            axios.delete(url)
                .then(({ data }) => {
                    this.fetchUploadedMedia()
                })
        },
        getUploadUrl(){
            if (this.isEditMode()){
                return this.getUrl()
            }else {
                return `/${this.routeprefix}/temporarymedia`
            }
        },
        uploadFile(file, resolve, reject){
            this.isUploading = true
            this.uploadingFilename = file.name

            const formData = new FormData()
            formData.append('file', file)
            formData.append('model', this.model_name)
            formData.append('type', this.type)
            formData.append('collection', this.collection)
            formData.append('order',  this.files.uploaded.length + 1)
            formData.append('path', window.location.pathname)

            axios.post(this.getUploadUrl(), formData, {
                headers: {'Content-Type': 'multipart/form-data'},
                onUploadProgress: this.uploadProgress.bind(this),
            })
            .then(({ data }) => {
                this.uploadMessage = 'Téléversement: ' + this.uploadingFilename + '-' + this.uploadingPercentage + '%';
                this.files.droped  = this.files.droped.filter((item, i) => item.name != file.name)
                this.files.uploaded = [data, ...this.files.uploaded]
                this.resetUpload()

                if (data.select){
                    this.selected_file = data
                }

                if (typeof resolve === 'function'){
                    resolve(data)
                }
            })
            .catch(data => {
                this.uploadMessage       = "Erreur lors de l'ajout de !" + this.uploadingFilename
                this.uploadMessageType   = "danger"

                if (typeof reject === 'function'){
                    reject(data)
                }
            })

        },
        upload(){
            if (this.files.droped.length == 0){
                this.closeUploadModal()
                return;
            }

            const file = this.files.droped[0]

            this.uploadFile(file, () => {
                this.upload()
            })
        },
        resetUpload(){
            this.isUploading         = false
            this.uploadingFilename   = null
            this.uploadingPercentage = 0
        },
        openUploadModal(){
            jQuery('#' + this.uploadModal).modal('show')
        },
        openViewimageModal(file){
            this.view_image = file

            const modal = jQuery('#' + this.imageviewModal)
            modal.modal('show')

            this.$nextTick(_ => {
                modal.find('.modal-body').zoom()
            })
        },
        openPropertyModal(file){
            this.property_file = file
            jQuery('#' + this.propertyModal).modal('show')
        },
        openRenameModal(file){
            this.renaming_file = JSON.parse(JSON.stringify(file))
            jQuery('#' + this.renameModal).modal('show')
        },
        closeRenameModal(){
            jQuery('#' + this.renameModal).modal('hide')
        },
        getSelectedFiles(){
            return this.files.uploaded.filter(file => file.select);
        },
        closeMediaModel(){
            this.dispatchFilemanagerClosedEvent();

            jQuery('#' + this.mediaModal).modal('hide')
        },
        dispatchCustomEvent(event_name, data){
            const event = new CustomEvent(event_name + '-' + this.collection, {
                detail: data
            })
            document.dispatchEvent(event)
        },
        closeViewImageModal(){
            const modal = jQuery('#' + this.imageviewModal)
            modal.find('.modal-body').trigger('zoom.destroy')
            modal.modal('hide')

            this.view_image = {}
        },
        closePropertyModal(){
            jQuery('#' + this.propertyModal).modal('hide')
        },
        closeUploadModal(){
            jQuery('#' + this.uploadModal).modal('hide')
        },
        closeCropModal(){
            this.croping_file = {}
            jQuery('#' + this.cropModal).modal('hide')
        },
        showCropModal(file){
            this.croping_file = file
            jQuery('#' + this.cropModal).modal('show')
        },
        handleDrop(event) {
            this.previewDropedFiles(event.dataTransfer.files)
        },
        handleDropClick(event){
            this.previewDropedFiles(event.target.files)
        },
        previewDropedFiles(files){
            for (let i = 0; i < files.length; i++) {
                const file = files[i]
                if (!this.validateFile(file.type)){
                    continue;
                }
                this.files.droped.unshift(file)
            }
            this.isDropping = false;
        },
        removeDropedFile(index){
            if (confirm("voulez-vous retirer le fichier de la liste ?")){
                this.files.droped = this.files.droped.filter((file, i) => i != index)
            }
        },
        getFileSize(bytes, decimals = 2) {
            if (bytes === 0){
                return '0 Octets'
            }
            const k = 1024
            const dm = decimals < 0 ? 0 : decimals
            const sizes = ['Octets', 'Ko', 'Mo', 'Go']

            const i = Math.floor(Math.log(bytes) / Math.log(k))

            return parseFloat((bytes / Math.pow(k, i)).toFixed(dm)) + ' ' + sizes[i]

        },
        getFileExtension(name) {
             if (!name){
                return
            }
            return name.substring(name.lastIndexOf('.') + 1).toLowerCase()
        },
        validateFile(type){
            let mimetypes = this.config.valid_mimetypes[this.type];
            if (!mimetypes.includes(type)) {
                swal({
                    title: 'Ajout de média !',
                    text: `
                        Erreur lors de l'ajout du fichier ${file.name} .
                        Veuillez choisir une image de type (jpg, jpeg, png, svg).
                        ${mimetypes.map(i => `[${i}]`).join()}
                        `,
                    icon: 'error'
                })
                return false
            }

            return true
        },
        isImage(file){
            if (!file){
                return
            }
            return this.config.valid_mimetypes['image'].includes(file.mime_type || file.type)
        },
        isFile(file){
            return !this.isImage(file)
        },
        isEditMode(){
            return this.model.id != null
        },
        isCreateMode(){
            return this.model.id == null
        },
        getImageThumbnail(file, callback){
            const reader = new FileReader()
            reader.readAsDataURL(file)
            reader.onload = callback
        },
        getFileThumbnail(file){
             if (!file){
                return
            }
            const ext = this.getFileExtension(file.name)
            return this.config.file_icon_array[ext] || 'fa-file'
        },
        getCollection(){
            const collection_name = this.collection.split('-')[0]

            return this.config.collections[collection_name];
        },
        // Computed
        get isMultipleCollection(){
            return this.getCollection()['multiple'] || false;
        },
        get allFilesChecked(){
            for (let i = 0; i < this.filteredUploadFiles.length; i++) {
                const file = this.filteredUploadFiles[i]

                if (!file.select){
                    return false
                }
            }

            return true
        },
        get allFilesUnChecked(){
            for (let i = 0; i < this.filteredUploadFiles.length; i++) {
                const file = this.filteredUploadFiles[i]

                if (file.select){
                    return false
                }
            }

            return true
        },
        get filteredUploadFiles(){
            const search = this.search.toLowerCase()

            if (!this.search){
                return this.files.uploaded
            }

            return this.files.uploaded.filter(file => file.name.includes(search))
        },
        get uploadedFileSize() {
             return this.files.uploaded.reduce(function (size, file) {
                return size + file.size
            }, 0)
        },
        get propertyModal(){
            return "propertyModal" + this.collection
        },
        get imageviewModal(){
            return "imageviewModal" + this.collection
        },
        get mediaModal(){
            return "mediaModal" + this.collection
        },
        get renameModal(){
            return "renameModal" + this.collection
        },
        get uploadModal(){
            return "uploadModal" + this.collection
        },
        get form(){
            return document.querySelector(`form[name=${this.form_name}]`)
        },
        get cropModal(){
            return "cropModal" + this.collection
        }
    }));
});
</script>
@endpush
<div x-ref="root" x-data="{{ $name }}">
    <!-- Modal -->
    <div class="modal fade" :id="mediaModal" tabindex="-1" role="dialog" data-backdrop="static" aria-hidden="true">
        <div class="modal-dialog" role="document" style="max-width: 90%">
            <div class="modal-content">
                <div class="modal-header" style="background-color: #7d7ddb; color: white;">
                    <h5 class="modal-title font-weight-bold" id="mediaModalLabel">Gestionnaire de média</h5>
                    <button type="button" class="close" @click="closeMediaModel">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-12  pb-4">
                            <div class="row">
                                <div class="col-sm-12 col-lg-6">
                                    <div class="input-group flex-fill">
                                        <div class="input-group-prepend">
                                            <div class="input-group-text" id="btnGroupAddon2" style="background-color: #6c757d;color: white;">
                                                <i class="fa fa-search"></i>
                                            </div>
                                        </div>
                                        <input type="text" class="form-control" x-model.debounce="search" placeholder="Rechercher">
                                    </div>
                                </div>
                                <div class="col-sm-12 mt-sm-2 mt-lg-0 col-lg-6 d-lg-flex justify-content-lg-end">
                                    <div class="btn-group" role="group">
                                        <button type="button" class="btn btn-secondary" @click.prevent="openUploadModal" title="Envoyer"><i
                                            class="fa fa-upload"></i></button>

                                        <button type="button" class="btn btn-secondary" @click.prevent="downloadAll"  title="Tous télécharger"><i
                                            class="fa fa-download"></i></button>


                                        <button x-show="isMultipleCollection && !allFilesChecked" type="button" @click.prevent="selectAll" class="btn btn-secondary"
                                            title="Tous sélectionner">
                                            <i class="fa fa-check"></i>
                                        </button>

                                        <button x-show="!allFilesUnChecked" type="button"  @click.prevent="unSelectAll" class="btn btn-secondary" data-uncheckall
                                        title="Tous décocher">
                                        <i class="fa fa-times"></i>
                                        </button>

                                        <button type="button" class="btn btn-secondary" @click.prevent="refresh"  title="Rafraîchir"><i
                                            class="fa fa-sync-alt"></i></button>

                                        <div class="btn-group" role="group">
                                            <button  type="button" class="btn btn-secondary dropdown-toggle" data-toggle="dropdown" title="Trier">
                                                Trier
                                            </button>
                                            <div class="dropdown-menu dropdown-menu-right"  style="z-index: 10000;">
                                                <button class="dropdown-item" @click.prevent="sort('default')" type="button"><i class="fa fa-sort-amount-down"></i>
                                                    Trier par défaut
                                                </button>
                                                <button class="dropdown-item" @click.prevent="sort('asc')" type="button"><i class="fa fa-sort-alpha-up"></i>
                                                    Trier de A - Z
                                                </button>
                                                <button class="dropdown-item" @click.prevent="sort('desc')" type="button"><i class="fa fa-sort-alpha-down"></i>
                                                    Trier de Z - A
                                                </button>
                                                <button class="dropdown-item" @click.prevent="sort('dateasc')" type="button"><i class="fa fa-sort-amount-down"></i> Trier par
                                                    date (Ancien)
                                                </button>
                                                <button class="dropdown-item" @click.prevent="sort('datedesc')" type="button"><i class="fa fa-sort-amount-down"></i> Trier par
                                                    date (Récent)
                                                </button>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                        <hr>

                        <div class="col-md-12 pl-2">
                            <div class="row mt-4">
                                <div x-show="filteredUploadFiles.length > 0" class="col-12 col-md-8 col-xl-8 row order-sm-2 order-md-1  order-xl-1 modal-zone modal-container">
                                    <template x-for="(file, index) in filteredUploadFiles" :key="index">
                                        <div @contextmenu="handleRightClick" @dblclick="toggleSelect(file)" :data-id="file.id" class="imagebox col-12 col-sm-6 col-md-6 col-lg-4"  :class="{ 'choosed-image': file.select }">
                                            <div class="file-man-box">
                                                <a href="#" class="file-close">
                                                    <i class="fa fa-check"></i>
                                                </a>

                                                <div class="file-img-box">
                                                    <img x-show="isImage(file)" :src="file.url" :alt="file.name">
                                                    <i x-show="isFile(file)" :class="'fa file-preview ' + getFileThumbnail(file)"></i>
                                                </div>
                                                <a href="#" class="file-download dropdown">
                                                    <i class="fa fa-tools dropdown-toggle" data-toggle="dropdown" data-offset="10,30"></i>
                                                    <div class="dropdown-menu ">
                                                        <template x-if="isImage(file)">
                                                            <button class="dropdown-item" type="button" @click='viewImage(file)'>
                                                                <i class="fa fa-image"></i> &nbsp;
                                                                Voir
                                                            </button>
                                                        </template>

                                                        <button x-show="file.select" @click="unSelect(file)" class="dropdown-item " type="button">
                                                            <i class="fa fa-times"></i> &nbsp;
                                                            Désélectionner
                                                        </button>

                                                        <template x-if="isImage(file)">
                                                            <button @click="modify(file)" class="dropdown-item " type="button">
                                                                <i class="fa fa-tools"></i> &nbsp;
                                                                Modifier
                                                            </button>
                                                        </template>

                                                        <button x-show="!file.select"  @click="select(file)" class="dropdown-item " type="button">
                                                            <i class="fa fa-check"></i> &nbsp;
                                                            Sélectionner
                                                        </button>
                                                        <button   @click="copyUrl($event, file)" class="dropdown-item " type="button">
                                                            <i class="fas fa-copy"></i> &nbsp;
                                                            Copier le lien
                                                        </button>

                                                        <button @click.prevent="openRenameModal(file)" class="dropdown-item" type="button">
                                                            <i class="fa fa-edit"></i> &nbsp;
                                                            Renommer
                                                        </button>

                                                        <button class="dropdown-item" type="button" @click.prevent="download(file)">
                                                            <i class="fa fa-download"></i> &nbsp; Télécharger
                                                        </button>

                                                        <button class="dropdown-item" type="button" @click.prevent="openPropertyModal(file)">
                                                            <i class="fa fa-info-circle"></i> &nbsp;
                                                            Propriétés
                                                        </button>

                                                        <div class="dropdown-divider"></div>
                                                            <button class="dropdown-item text-danger"  @click.prevent='remove(file)' type="button" ><i class="fa fa-trash"></i>
                                                            &nbsp; Effacer</button>
                                                    </div>
                                                </a>
                                                <div class="file-man-title">
                                                    <h5 @dblclick.prevent.stop="openRenameModal(file)" class="mb-0 text-overflow filename" x-text="file.name"></h5>
                                                    <p class="mb-0"><small x-text="getFileSize(file.size)"></small></p>
                                                </div>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                                <div x-show="filteredUploadFiles.length == 0" class="col-12 col-md-8 col-xl-8 row order-sm-2 order-md-1  order-xl-1 modal-zone">
                                    <div class='d-flex justify-content-center align-items-center w-100 h4 text-secondary'>
                                        <p><i class='fa fa-empty-set'></i> Aucun fichier trouvé</p>
                                    </div>
                                </div>

                                <div class="col-12 col-md-4 col-xl-4 border-left order-sm-1 order-md-2 order-xl-2 mb-sm-4 modal-zone mt-4 mt-md-2">
                                    <template x-if="!jQuery.isEmptyObject(selected_file)">
                                        <div class="card" >
                                            <img x-show="isImage(selected_file)" :src="selected_file.url" class="card-img-top" :alt="selected_file.name">
                                            <i x-show="isFile(selected_file)" :class="'fa file-preview ' + getFileThumbnail(selected_file)"></i>

                                            <ul class="list-group list-group-flush">
                                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                                    Nom:
                                                    <span x-text="selected_file.name"></span>
                                                </li>
                                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                                    Taille:
                                                    <span x-text="getFileSize(selected_file.size)"></span>
                                                </li>
                                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                                    Téléversé le:
                                                    <span x-text="selected_file.date_for_humans"></span>
                                                </li>
                                            </ul>

                                            <div class="card-body text-center">
                                               <div class="btn-group">
                                                    <template x-if="isImage(selected_file)">
                                                        <button @click.prevent="modify(selected_file)" class='btn btn-info btn-sm'>
                                                            <i class='fas fa-edit'></i> <span>Modifier</span>
                                                        </button>
                                                    </template>
                                                    <button @click.prevent="copyUrl($event, selected_file)" class='btn btn-success btn-sm'>
                                                        <i class='fas fa-copy'></i> <span>Copier le lien</span>
                                                    </button>
                                               </div>
                                            </div>
                                        </div>
                                    </template>
                                    <template x-if="allFilesUnChecked || jQuery.isEmptyObject(selected_file)">
                                        <div  class='d-flex justify-content-center align-items-center w-100 h4 text-secondary h-100'>
                                            <p class='text-center'><i class='fa fa-times'></i> <br> Aucune fichier sélectionné pour cette collection</p>
                                        </div>
                                    </template>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light text-white">
                    <div class="col-12 col-md-6 col-lg-6 order-md-2 order-lg-1">
                        <div class="col-12 border p-2 bg-secondary text-center" >
                            <i class='fas fa-clock'></i> Collection: {{ $collection }} | <span x-text="' Fichiers: ' + files.uploaded.length "></span> | <span x-text="' Taille: ' + getFileSize(uploadedFileSize) "></span>
                        </div>
                    </div>
                    <div class="col-12 col-md-6 col-lg-6 order-md-1 order-lg-2">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" :id="renameModal" tabindex="-1" data-backdrop="static" role="dialog" aria-labelledby="renameModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
           <template x-if='renaming_file'>
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title font-weight-bold" id="renameModalLabel">Renommage</span></h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close" title="Fermer">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label :for="'name' + collection" class="col-form-label">Nom du fichier:</label>
                            <input x-model="renaming_file.name" type="text" class="form-control" :class="{ 'is-invalid': renaming_file.name.length == 0 }" :id="'name' + collection">
                             <div class="invalid-feedback">
                                Le nom du fichier ne peut être vide.
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button :disabled="renaming_file.name.length == 0"  @click.prevent="rename(renaming_file)" type="button" class="btn btn-success btn-block">Renommer</button>
                    </div>
                </div>
           </template>
        </div>
    </div>

    <div :id="uploadModal" class="modal  fade upload-modal"  tabindex="-1" data-backdrop="static" role="dialog" :aria-labelledby="'uploadModalLabel' + collection"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title font-weight-bold text-center text-uppercase" :id="'uploadModalLabel' + collection">
                        Téléversement (<span x-text="files.droped.length"></span>)
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" title="Fermer">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="mt-2 col-12 preview row" >
                        <div class="col-12 col-sm-4">
                            <div class="card dropzone" style="cursor: pointer" :class="{ 'highlight': isDropping }"
                            @dragenter.prevent.stop="isDropping = true"
                            @dragover.prevent.stop="isDropping = true"
                            @dragleave.prevent.stop="isDropping = false"
                            @drop.prevent.stop="handleDrop"
                            @click="document.getElementById(collection + '-input').click()"
                            >
                                <div>
                                    <i class="fa fa-upload fa-3x" style="opacity: .5;"></i>
                                    <p> Relacher les images ici </p>
                                </div>
                                <div >
                                    <input @change="handleDropClick" type="file" :id="collection + '-input'" class="d-none" accept="image/*" multiple>
                                    Cliquer ou glisser-déposer les images ici
                                </div>
                            </div>
                        </div>
                        <template x-for="(file, index) in files.droped" :key="index">
                            <div class="imagebox col-12 col-sm-4">
                                <div class="card">
                                    <a @click.prevent="removeDropedFile(index)" href="#" class="file-close">
                                        <i class="fa fa-times"></i>
                                    </a>
                                    <template x-if="isImage(file)">
                                        <img :src="getImageThumbnail(file, (e) => $el.setAttribute('src', e.target.result))" class="card-img-top" alt="image name">
                                    </template>
                                    <template x-if="isFile(file)">
                                        <i :class="'fa file-preview ' + getFileThumbnail(file)"></i>
                                    </template>

                                    <ul class="list-group list-group-flush">
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                        Nom:
                                        <span x-text="file.name"></span>
                                        </li>
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                        Taille:
                                        <span x-text="getFileSize(file.size)"></span>
                                        </li>
                                         <li class="list-group-item d-flex justify-content-between align-items-center">
                                            <button class="btn btn-info btn-sm" @click.prevent="uploadFile(file)">Envoyer</button>
                                            <button class="btn btn-danger btn-sm btn-block" @click.prevent="removeDropedFile(index)">Supprimer</button>
                                        </li>
                                    </ul>

                                </div>
                            </div>
                        </template>

                    </div>


                </div>
                <div class="modal-footer">
                    <button :disabled="files.droped.length == 0" type="button" @click.prevent="upload" class="btn btn-primary btn-sm btn-block"><i class="fa fa-location-arrow"></i> Téléverser</button>
                    <div x-show="isUploading" x.transition class="progress" style="width: 100%; height: 28px;">
                        <div :class="'progress-bar bg-' + uploadMessageType"  role="progressbar" :style="{ width: uploadingPercentage + '%' }" :aria-valuenow="uploadingPercentage" aria-valuemin="0"
                            aria-valuemax="100" x-text="uploadMessage"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div :id="propertyModal" class="modal  fade "  tabindex="-1" data-backdrop="static" role="dialog" :aria-labelledby="'propertyModalLabel' + collection"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title font-weight-bold text-center text-uppercase" :id="'uploadModalLabel' + collection">
                        Propriétés
                    </h5>
                    <button type="button" class="close" @click.prevent="closePropertyModal" aria-label="Close" title="Fermer">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="mt-2 row" >
                        <div class="col-12">
                            <template x-if='property_file'>
                                <ul class="list-group list-group-flush">
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        Nom:
                                        <span x-text="property_file.name"></span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        Taille:
                                        <span x-text="getFileSize(property_file.size)"></span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        Url:
                                        <span @dblclick.prevent="copyUrl($event, property_file)" x-text="property_file.url"></span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        Type:
                                        <span x-text="property_file.mime_type"></span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        Sélectionné:
                                        <span x-text="property_file.select ? 'Oui': 'Non'"></span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        Ordre:
                                        <span x-text="property_file.order"></span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        Téléversé le:
                                        <span x-text="property_file.date_for_humans"></span>
                                    </li>
                                </ul>
                            </template>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                   <button type="button" class="btn btn-info" @click.prevent="copyUrl($event, property_file)">Copier le lien</button>
                   <button type="button" class="btn btn-secondary" @click.prevent="closePropertyModal">Annuler</button>
                </div>
            </div>
        </div>
    </div>

    <div :id="cropModal" class="modal  fade crop-modal"  tabindex="-1" data-backdrop="static" role="dialog" :aria-labelledby="'cropModalLabel' + collection"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title font-weight-bold text-center text-uppercase" :id="'cropModalLabel' + collection">
                        Redimensionner <span x-text="croping_file ? croping_file.name : ''"></span>
                    </h5>
                    <button type="button" class="close" @click.prevent="closeCropModal" aria-label="Close" title="Fermer">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-7">
                            <div class="img-container">
                                <img  :src="croping_file.url" style="height: 350px">
                            </div>
                        </div>
                        <div class="col-md-5">
                            <img src="" class="img-fluid result">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" @click.prevent="closeCropModal">Annuler</button>
                    <button type="button" class="btn btn-primary" data-crop="button">Redimensionner</button>
                </div>
            </div>
        </div>
    </div>

    <div :id="imageviewModal" class="modal fade"  tabindex="-1" data-backdrop="static" role="dialog" aria-labelledby="viewModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content" x-show='view_image'>
                    <div class="modal-header">
                        <h5 class="modal-title font-weight-bold" id="viewModalLabel" x-text="'Prévisualisation ' + view_image.name"></h5>
                        <button type="button" class="close" @click.prevent="closeViewImageModal" aria-label="Close" title="Fermer">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div  class="modal-body text-center">;
                        <img :src="view_image.url" :alt="view_image.name" class="img-fluid img-thumbnail">
                    </div>
                    <div class="modal-footer">
                        <button x-show="view_image.select" @click='unSelect(view_image)' class="btn btn-secondary btn-sm" type="button">
                            <i class="fa fa-times"></i> &nbsp;
                            Désélectionner
                        </button>

                        <button x-show="!view_image.select"  @click='select(view_image)' class="btn btn-secondary btn-sm" type="button">
                            <i class="fa fa-check"></i> &nbsp;
                            Sélectionner
                        </button>
                        <button type="button" class="btn btn-secondary btn-sm" @click.prevent="closeViewImageModal"><i class="fa fa-times"></i> Fermer</button>
                    </div>
                </div>
        </div>
    </div>
</div>
