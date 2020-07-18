
class ImageManager {

  static init(opts) {
    new ImageManager(opts)
  }

  constructor(opts) {
    this.binds()

    // default config
    this.config = {
      image: true, closemodal: '[data-modal="close"]',
      modalimagescontainer: '#modal-images-container',
      dropzone: '.dropzone', modalfooter: '[data-modal="footer"]',
      modalalert: '[data-modal="alert"]', copyurl: '[data-copyurl]',
      downloadimage: '[data-download]', chooseimage: 'choosed-image',
      modalselectedimage: '[data-modal="selected"]',
      imageproperty: '[data-property]', viewimage: '[data-view]',
      deleteimage: '[data-delete]', deleteallimages: '[data-deleteall]',
      viewmodal: '#viewModal',
      checkimage: '[data-choose]', uncheckimage: '[data-unchoose]',
      dropzoneclass: '.dropzone',
      uploadmodal: '#uploadModal', uploadimage: '[data-upload]', checkall: '[data-checkall]', uncheckall: '[data-uncheckall]',
      'refresh': '[data-refresh]', 'search': '[data-search]', 'sort': '[data-sort]', 'sorter': '[data-sorter]'
    }

    this.collections = {
      front: 'front-image',
      back: 'back-image',
      images: 'images',
    }


    this.alerts = {
      rename: {
        success: { message: `L'image a bien été renommée`, type: 'success' },
        error: { message: `Le nom de l'image ne peut pas être vide`, type: 'danger' },
      },
      upload: {
        success: { message: `L'image a bien été renommée`, type: 'success' },
        error: { message: `Le nom de l'image ne peut pas être vide`, type: 'danger' },
      },
      delete: {
        success: { message: `L'image a bien été supprimée`, type: 'success' },
        error: { message: `Erreur lors de la suppression de l'image`, type: 'danger' },
        swal: { message: `Etes de vous sûr de bien vouloir supprimer l'image ? Cette action est irréversible.`, type: 'warning' },
      },
      size: {
        error: { message: `Image trop grande, la taille ne peut dépasser 1024 Ko.`, type: 'danger' }
      }
    }


    this.authorized_extensions = ['png', 'jpg', 'gif', 'jpeg', 'svg']

    this.limit_size = 1024000;


    this.uploadingImages = []
    this.selectedImages = []
    this.tempImages = {}

    this.images = {}

    this.setOptions(opts)


    this.init()

    this.addImagesEvents()

    this.handleSortable()
  }

  binds() {
    // Superbe technique pour binder this aux différentes méthodes
    const methods = [
      'setOptions', 'handleClick', 'closeModal', 'deleteAllSelectedImage',
      'handleCloseIconClick', 'handleSortable', 'chooseImage', 'unChooseImage', 'sort',
      'handleChange', 'renameImage', 'downloadImage', 'getImageProperties', 'refresh', 'search', 'resetSearch',
      'viewImage', 'deleteImage', 'deleteAllImages', 'handleDrop', 'uploadModal', 'downloadAllImage', 'uncheckAll',
      'checkAll'
    ]

    methods.forEach((fn) => this[fn] = this[fn].bind(this))
  }

  init() {
    $(this.config.selector).on('click', this.handleClick)

    this.getModal().find(this.config.closemodal).on('click', this.closeModal)

    this.getModal().find(this.config.uploadimage).on('click', this.uploadModal)

    this.getModal().find(this.config.downloadimage).on('click', this.downloadAllImage)
    this.getModal().find(this.config.deleteallimages).on('click', this.deleteAllImages)
    this.getModal().find(this.config.refresh).on('click', this.refresh)
    this.getModal().find(this.config.search).on('keydown', this.search)
    this.getModal().find(this.config.search).on('blur', this.resetSearch)


    this.getModal().find(this.config.sort).on('click', this.sort)


    this.getModalContainer().on('click', this.config.checkimage, this.chooseImage)

    this.getModalContainer().on('click', this.config.uncheckimage, this.unChooseImage)
    this.getModalContainer().on('click', this.config.downloadimage, this.downloadImage)



    this.getModalContainer().on('click', this.config.imageproperty, this.getImageProperties)
    this.getModalContainer().on('click', this.config.viewimage, this.viewImage)
    this.getModalContainer().on('click', this.config.deleteimage, this.deleteImage)

    /**
     * Ajout des champs dans le formulaire pour la création
     */
    if (this.isEmptyModel()) {
      this.form = $(`form[name=${this.config.form_name}]`)
      this.appendCollectionsFormFields()
    }


    this.getRenameModal().on('show.bs.modal', this.renameImage)

    this.setDropZone()
  }

  addCheckUncheckAll() {

    const checkAll = this.getModal().find(this.config.checkall)
    const uncheckAll = this.getModal().find(this.config.uncheckall)

    /**
    * On cache les deux boutons si ce n'est pas une collection de type image
    */
    if (!this.isImagesCollection()) {
      checkAll.addClass('d-none')
      uncheckAll.addClass('d-none')
    } else {
      if (this.images.length) {
        checkAll.removeClass('d-none')
        uncheckAll.removeClass('d-none')

        checkAll.on('click', this.checkAll)
        uncheckAll.on('click', this.uncheckAll)
      } else {
        checkAll.addClass('d-none')
        uncheckAll.addClass('d-none')
      }
    }



  }

  appendCollectionsFormFields() {

    this.form.attr('enctype', 'multipart/form-data')

    this.form.prepend(
      this.generateCollectionsFormFields()
    )

  }


  generateCollectionsFormFields() {

    let html = ``

    for (const collection in this.collections) {
      const name = this.collections[collection]

      if (this.config[name]) {
        html += `
                <div class='d-none'>
                    <input type='file' id='${name}' class='${name}' name='${name}[]' accept="image/*" multiple>
                    <input type='text' id="${name}-attributes" class='${name}-attributes' name='${name}-attributes' >
                </div>
                `
      }

    }
    return html;

  }


  setDropZone() {
    const dropArea = $(this.config.dropzoneclass);

    dropArea.on('dragenter dragover dragleave drop', (event) => {
      event.preventDefault()
      event.stopPropagation()
    })

    dropArea.on('dragenter dragover', (event) => {
      dropArea.addClass('highlight')
    })

    dropArea.on('dragleave drop', (event) => {
      dropArea.removeClass('highlight')
    })


    dropArea.on('drop', this.handleDrop);

  }

  previewDroppedImage(parent, images, prepend = true) {

    for (let i = 0; i < images.length; i++) {
      const reader = new FileReader()
      const image = images[i]

      /**
       * Le continue permet de passer à l'élément suivant tandis que le return aurait stopper la fonction
       * et les autres images ne seront pas envoyés au serveur
       */
      if (!this.validateFile(image)) {
        continue
      }

      reader.readAsDataURL(image)

      reader.onload = (e) => {
        const html = (`
                    <div class="imagebox col-12 col-sm-6" data-name='${image.name}'>
                        <div class="card">
                            <a href="#" class="file-close">
                                <i class="fa fa-times"></i>
                            </a>
                            <img src="${ e.target.result}" class="card-img-top" alt="${image.name}">
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                Nom:
                                <span> ${ image.name}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                Taille:
                                <span>${this.getFileSize(image.size)}</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                `)

        prepend ? parent.prepend(html) : parent.append(html)
      };
    }
  }

  renderUploadModalFooter() {
    const footer = this.getUploadModal().find('.modal-footer')

    if (this.uploadingImages.length) {
      footer.removeClass('d-none')
    } else {
      footer.addClass('d-none')
    }
  }

  renderUploadModalTitle() {
    this.getUploadModal().find('.modal-title span').text(`(${this.uploadingImages.length})`)
  }


  resetUploadModal() {

    this.uploadingImages = []

    this.getUploadModal().find('.preview').empty()

    this.renderUploadModalTitle()

    this.renderUploadModalFooter()
  }


  previewDropOrSelectImages(images) {

    this.uploadingImages = [...this.uploadingImages, ...images]

    const preview = this.getUploadModal().find('.preview')

    this.renderUploadModalTitle()
    // Afficher les boutons d'envoi dans le modal footer
    this.renderUploadModalFooter()

    this.previewDroppedImage(preview, images)

    preview.on('click', '.file-close', (event) => {
      event.preventDefault()

      this.swal(() => {
        const imageBox = $(event.target).parents('.imagebox')
        imageBox.fadeOut(600, () => {
          imageBox.remove()
          // retirer l'image dans la liste
          this.uploadingImages = this.uploadingImages.filter((image) => imageBox.data('name') != image.name)

          this.renderUploadModalTitle()

          this.renderUploadModalFooter()

        })
        this.alert(this.alerts.delete.success)
      })

    })

    this.getUploadModal().find('.modal-footer').off('click').bind('click', 'button', (event) => {
      event.preventDefault()

      this.upload(this.uploadingImages, this.collection)

      this.resetUploadModal()
      this.getUploadModal().modal('hide')

    })

  }

  handleDrop(event) {
    event.preventDefault()
    event.stopPropagation()

    const images = event.originalEvent.dataTransfer.files

    this.previewDropOrSelectImages(images)

  }

  downloadAllImage(event) {
    event.preventDefault()

    if (this.isEmptyModel()) {
      swal({
        title: 'Téléchargement !',
        text: 'Les images non persistées ne sont pas téléchargeable pour le moment!',
        icon: 'warning',
        dangerMode: true,
      })
      return
    }

    swal({
      title: 'Téléchargement !',
      text: 'Etes vous sûr de vouloir télécharger toutes les images ? ',
      icon: 'warning',
      dangerMode: true,
      buttons: {
        cancel: 'Annulez',
        confirm: 'Confirmez!'
      }
    })
      .then((isConfirm) => {
        if (isConfirm) {
          this.getModalContainer().find('[data-download]').trigger('click')
        }
      })

  }

  downloadImage(event) {
    event.preventDefault()
    const button = $(event.target)

    const a = $(`
        <a href='${button.data('download')}' download='${button.data('name')}'></a>
        `).appendTo('body')

    // important afin de lancer le click natif du navigateur
    a[0].click()

    a.remove()
  }

  getImageBox(id) {
    return this.getModalContainer().find('[data-id=' + id + ']')
  }


  swal(success, failure) {
    swal({
      title: 'Suppression !',
      text: this.alerts.delete.swal.message,
      icon: this.alerts.delete.swal.type,
      dangerMode: true,
      buttons: {
        cancel: 'Annulez',
        confirm: 'Confirmez!'
      }
    })
      .then((isConfirm) => {
        if (isConfirm) {
          if (success) {
            success()
          }
        } else {
          if (failure) {
            failure()
          }
        }
      })
  }


  hideBoxWhenDeleteAll() {
    let imageBoxes = this.getModalContainer().children(),
      count = imageBoxes.length,
      total = imageBoxes.length

    imageBoxes.each((index, box) => {
      $(box).fadeOut(600 * index, () => {
        $(box).remove()

        if (!--count) {
          this.alert({
            message: 'Les ' + total + ' images ont bien été supprimées',
            type: 'success'
          })
          this.renderEmptyImageboxContainer()
        }
      })
    })
  }

  deleteAllImages(event) {
    event.preventDefault()

    swal({
      title: 'Suppression !',
      text: "Etes de vous sûr de bien vouloir supprimer toutes les images? Cette action est irréversible.",
      icon: 'warning',
      dangerMode: true,
      buttons: {
        cancel: 'Annulez',
        confirm: 'Confirmez!'
      }
    })
      .then((isConfirm) => {
        if (isConfirm) {

          if (this.isEmptyModel()) {

            this.selectedImages = []
            this.images = []

            this.hideBoxWhenDeleteAll()

            this.addCheckUncheckAll();

            this.renderHeaderButtons()

          } else {
            axios.delete(`${this.getUrl()}/${this.collection}/all`)
              .then(({ data }) => {
                this.hideBoxWhenDeleteAll()

                this.addCheckUncheckAll();

                this.renderHeaderButtons()
              })
          }

        }
      })
  }

  renderEmptyImageboxContainer() {
    this.getModalContainer()
      .append(`
                <div class='d-flex justify-content-center align-items-center w-100 h4 text-secondary'>
                    <p><i class='fa fa-empty-set'></i> La collection est vide</p>
                </div>
            `)

    this.selectedImage = null
    this.setSelectedModalImage()

  }

  removeDeleteInModalImageBox(image) {
    const imageBox = this.getImageBox(image.id)
    imageBox.fadeOut(600, () => {
      imageBox.remove()

      // on retire l'image de la liste
      this.images = this.images.filter(img => img.id != image.id)

      this.addCheckUncheckAll();

      this.renderHeaderButtons()

      // remettre le message disant que la boite est vide si vide
      if (!this.getModalContainer().children().length) {
        this.renderEmptyImageboxContainer()
      }

    })
  }

  deleteImage(event) {
    event.preventDefault()

    const button = $(event.target)
    const image = this.getImage(button.data('delete'))


    swal({
      title: 'Suppression !',
      text: `Etes de vous sûr de bien vouloir supprimer l'image ${image.name}. Cette action est irréversible.`,
      icon: 'warning',
      dangerMode: true,
      buttons: {
        cancel: 'Annulez',
        confirm: 'Confirmez!'
      }
    })
      .then((isConfirm) => {
        if (isConfirm) {
          if (this.isEmptyModel()) {
            // Désélectionner l'élément
            this.getImageBox(image.id).find(this.config.uncheckimage).trigger('click')

            this.removeDeleteInModalImageBox(image)



          } else {
            axios.delete(this.getUrl(), { data: { image_id: image.id } })
              .then((data) => {
                this.removeDeleteInModalImageBox(image)

                this.alert(this.alerts.delete.success)

              })

          }
        } else {
          // swal('suppression désapprouvé')
          // $(event.target).parents('.imagebox').find('img').css('border', 'none')
        }
      })
  }

  alert(key, duration = 5000) {

    const modal = this.getModalAlert()

    modal.empty()

    modal.append(`
        <div class="border p-2 bg-${key.type} text-center">
        <i class='fas ${key.type == 'success' ? 'fa-check' : 'fa-exclamation-circle'}'></i>
        &nbsp; ${key.message}
        </div>
        `)

    setTimeout(() => {
      modal.empty()
    }, duration)
  }

  viewImage(event) {
    event.preventDefault()

    const button = $(event.target)

    const image = this.getImage(button.data('view'))

    const modal = this.getViewModal()
    modal.modal('show')

    const img = modal.find('.modal-body img')
    img.attr('src', image.url)
    img.attr('alt', image.name)


    const title = modal.find('.modal-header .modal-title')
    title.text('Prévisualisation: ' + image.name)

    modal.find('.modal-body').zoom()


    modal.find('.modal-footer button:first-child').off('click').bind('click', (event) => {
      event.preventDefault()
      this.getImageBox(image.id).find(this.config.checkimage).trigger('click')

      modal.find('.modal-body').trigger('zoom.destroy')
      modal.find('.modal-body').empty()

      modal.modal('hide')
    })
  }

  getImageByName(name) {
    return this.images.filter(image => image.name == name)[0]
  }

  getImage(id) {
    return this.images.filter(image => image.id == id)[0]
  }

  getImageProperties(event) {
    event.preventDefault()

    const image = this.getImage(event.target.dataset.property)


    this.setSelectedModalImage(null, image)
  }

  getModal() {
    return $(this.config.modal)
  }

  getRenameModal() {
    return $(this.config.renamemodal)
  }

  getViewModal() {
    return $(this.config.viewmodal)
  }

  getUploadModal() {
    return $(this.config.uploadmodal)
  }


  modal(action) {
    this.getModal().modal(action)
  }


  sort(event) {
    event.preventDefault()

    const sorter = event.target.dataset.sort

    if (!this.images.length) {
      return
    }

    if (sorter === 'default') {
      this.addImages(this.images)
      return
    }

    const images = [...this.images]

    if (sorter === 'asc') {
      images.sort((a, b) => {
        if (a.name < b.name) { return -1 }
        if (a.name > b.name) { return 1 }

        return 0
      })

    } else if (sorter === 'desc') {
      images.sort((a, b) => {
        if (a.name < b.name) { return 1 }
        if (a.name > b.name) { return -1 }

        return 0
      })
    }
    else if (sorter === 'datedesc') {
      images.sort((a, b) => {
        return new Date(b.created_at) - new Date(a.created_at)
      })
    }
    else if (sorter === 'dateasc') {
      images.sort((a, b) => {
        return new Date(a.created_at) - new Date(b.created_at)
      })
    }


    this.emptyModalContainer()
    images.forEach(image => {
      this.getModalContainer().append(this.getPreviewedModalImageTemplate(image))
    })
  }

  resetSearch(event) {
    event.preventDefault()

    const value = event.target.value

    if (!value) {
      this.addImages(this.images)
      return
    }
  }

  search(event) {
    const value = $(event.target).val().toLowerCase()

    if (event.key === 'Enter') {

      const filtered = this.images.filter((image) => {
        if (image.name.includes(value)) {
          return image
        }
      })

      if (!value) {
        this.addImages(this.images)
        return
      }


      if (!filtered.length) {
        this.emptyModalContainer()
        this.getModalContainer()
          .append(`
                    <div class='d-flex justify-content-center align-items-center w-100 h4 text-secondary'>
                        <p><i class='fa fa-empty-set'></i> Aucune image pour cette recherche '${value}'</p>
                    </div>
                `)
        return
      }


      this.emptyModalContainer()

      filtered.forEach(image => {
        this.getModalContainer().append(this.getPreviewedModalImageTemplate(image))
      })
    }

  }

  refresh() {
    // on ne peut pas rafraichir des éléments qui ne sont pas persistés
    if (this.isEmptyModel()) {
      swal({
        title: 'Rafraichissement !',
        text: 'Le rafraichissement est impossible tant que les images ne sont pas encore persistées',
        icon: 'warning',
        dangerMode: true,
      })
      return
    }

    // Récupérer les fichiers pour le modal
    this.getModalContainer().empty()
    this.images = []

    axios.get(`${this.getUrl()}/${this.collection}`)
      .then(({ data }) => {
        this.addImages(data)

        this.setSelectedImage()
        // this.addChooseClass()

        this.setSelectedModalImage()
      })
  }

  isEmptyModel() {
    return $.isEmptyObject(this.config.model)
  }

  handleClick(event) {
    event.preventDefault()

    this.button = $(event.target)

    /**
     * Puisque on recupere les images déjà envoyées
     * Il faudra réinitialiser le tableau si ce n'est pas la même collection
     */
    if (this.isEmptyModel()) {
      if (this.collection && this.collection != this.button.data('image')) {
        this.images = []
      }
    }





    this.collection = this.button.data('image')

    this.generateFormFor(this.collection)


    this.addCheckUncheckAll();




    // Récupérer les fichiers pour le modal
    if (!this.isEmptyModel()) {
      axios.get(`${this.getUrl()}/${this.collection}`)
        .then(({ data }) => {
          this.addImages(data)

          // On désactive les boutons de téléchargement, rafraichir, et suppression  si la collection est vide
          this.renderHeaderButtons()
        })
    } else {
      /**
       * Récupérer les images déjà envoyés s'ils existent
       */
      // this.images ? this.addImages(this.images, false) : this.addImages([])

      if (this.tempImages[this.collection]) {
        this.images = this.tempImages[this.collection]
        this.addImages(this.images, false)
      } else {
        this.addImages([])
      }

    }

    // On désactive les boutons de téléchargement, rafraichir, et suppression  si la collection est vide
    this.renderHeaderButtons()

    this.modal('show')


  }

  /**
   * On désactive les boutons de téléchargement, rafraichir, et suppression  si la collection est vide
   * @memberof ImageManager
   */
  renderHeaderButtons() {
    const buttons = this.getModal().find(`
        ${this.config.downloadimage}, ${this.config.deleteallimages},
        ${this.config.refresh},${this.config.sorter}`
    )

    if (this.images.length) {
      buttons.removeClass('disabled').removeAttr('disabled')
    } else {
      buttons.addClass('disabled').attr('disabled', 'disabled')
    }

  }

  checkAll(event) {
    event.preventDefault()
    this.getModalContainer().children().find(this.config.checkimage).trigger('click')
  }

  uncheckAll(event) {
    event.preventDefault()
    this.getModalContainer().children().find(this.config.uncheckimage).trigger('click')
  }

  generateFormFor(collection) {

    const formName = collection + '-form'

    /**
     * Ne rien faire si le form existe deja bien
     */
    if ($('form[name=' + formName + ']').length) {
      return
    }


    this.insertFormTemplate(formName, this.getDropzone())

    const input = $('form[name=' + formName + '] input[type=file]')

    this.getDropzone().on('click', function (ev) {
      ev.preventDefault()
      input.click()
    })

    input.on('change', this.handleChange)
  }

  handleChange(event) {
    event.preventDefault()
    event.stopPropagation()

    const images = $(event.target)[0].files

    this.previewDropOrSelectImages(images)
  }

  getDropzone() {
    return this.getUploadModal().find(this.config.dropzone)
  }

  getModalContainer() {
    return $(this.config.modalimagescontainer)
  }

  getCollectionContainer() {
    return $(this.button.data('container'))
  }

  closeModal() {

    if (this.config.collectiontype === 'avatar') {
      if (this.isFrontImageCollection() && typeof window.fnAvatarCommit === 'function' && this.selectedImage) {
        fnAvatarCommit(this.selectedImage)
      }
      this.modal('hide')
      return
    }


    if (this.isEmptyModel()) {
      const input = this.form.find(`input.${this.collection}`)
      const input_attributes = this.form.find(`input.${this.collection}-attributes`)


      const dT = new DataTransfer()
      const attr = []

      this.images.forEach(image => {
        attr.push({
          [image.name]: { order: image.order, select: image.select, name: image.new_name }
        })
        dT.items.add(image)
      })

      input.prop('files', dT.files)
      input_attributes.val(JSON.stringify(attr))


      this.tempImages[this.collection] = this.images

    }

    if (this.isImagesCollection(this.collection)) {

      this.getCollectionContainer().empty()

      this.selectedImages.forEach(image => {
        this.getCollectionContainer().append(this.getPreviewedImageTemplate(image))
      })

      if (this.selectedImages.length) {
        this.getCollectionContainer().prev().removeClass('d-none')
        $('[data-delete=all]').show()
      } else {
        this.getCollectionContainer().prev().addClass('d-none')
      }
    } else {
      this.previewImageInCollectionContainer(this.selectedImage)
    }


    this.modal('hide')
  }

  uploadModal(event) {
    event.preventDefault()
    this.getUploadModal().modal('show')
  }

  getModalFooter() {
    return $(this.config.modalfooter)
  }

  getModalAlert() {
    return $(this.config.modalalert)
  }

  emptyModalContainer() {
    this.getModalContainer().empty()
  }

  getImagesSize(images) {
    return images.reduce(function (size, image) {
      return size + image.size
    }, 0)
  }

  getImagesCollectionSelectedImage() {
    return this.selectedImages[this.selectedImages.length - 1]
  }

  getSelectedImageTemplate(image) {

    let html = ''

    if (!image) {

      html = `
                 <div class='d-flex justify-content-center align-items-center w-100 h4 text-secondary h-100'>
                    <p class='text-center'><i class='fa fa-times'></i> <br> Aucune image sélectionnée pour cette collection</p>
                </div>
            `
    } else {
      html = `
                <div class="card">
                    <img src="${image.url}" class="card-img-top" alt="${this.isEmptyModel() ? image.new_name : image.name}">

                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Nom:
                            <span>${ this.isEmptyModel() ? image.new_name : image.name}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Taille:
                            <span>${this.getFileSize(image.size)}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Téléversée le:
                            <span>${image.date_for_humans}</span>
                        </li>
                    </ul>
                    ${!this.isEmptyModel() ? `
                    <div class="card-body text-center">
                        <button class='btn btn-success btn-sm'  data-copyurl='${image.url}'><i class='fas fa-copy'></i> Copier le lien</button>
                    </div>

                    ` : ''}

                </div>
        `
    }

    return html
  }

  setSelectedModalImage(name = null, image = null, push = true) {

    /**
     * Passer manuellement le select à true puisque rien n'est persisté
     */
    if (image && this.isEmptyModel()) {
      image.select = true
    }

    this.getSelectedModalImage().empty()

    if (this.isImagesCollection(this.collection)) {
      /**
       * On push l'élément si ce dernier n'est pas déjà dans la liste
       */
      if (image && push) {
        this.selectedImages.push(image)
      }

      this.getSelectedModalImage().append(this.getSelectedImageTemplate(this.getImagesCollectionSelectedImage()))
    } else {
      if (image) {
        this.selectedImage = image
      }

      if (name) {
        if (!this.isEmptyModel()) {
          this.selectedImage.name = name
        }
      }

      this.getSelectedModalImage().append(this.getSelectedImageTemplate(this.selectedImage))
    }



    /**
     * Faire la copie si seulement on a un model
     */
    if (!this.isEmptyModel()) {
      this.getModal().find(this.config.copyurl).on('click', this.copyurl)
    }
  }

  copyurl(event) {
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
    // button.prev().select()

    input.value = button.data('copyurl')


    button.parent().prepend(input)
    input.focus()
    input.select()

    document.execCommand('copy')

    button.parent().children().first().remove();
  }

  getSelectedModalImage() {
    return $(this.config.modalselectedimage)
  }

  setModalFooter() {
    this.getModalFooter().empty()

    const size = this.images.length ? this.getFileSize(this.getImagesSize(this.images)) : 0

    this.getModalFooter().append(`
                <div class="col-12 border p-2 bg-secondary text-center" >
                <i class='fas fa-clock'></i> Collection: ${this.config[this.collection + '-label']} | Images: ${this.images.length} | Taille images: ${size}
                </div>
                `)
  }

  addImages(images, push = true) {

    if (push) {
      this.images = images
    }

    this.emptyModalContainer()


    if (this.images.length) {
      images.forEach(image => {
        this.getModalContainer().append(this.getPreviewedModalImageTemplate(image))
      })

      // add choosed class to the last element
      this.setSelectedImage()
      // this.addChooseClass()

      this.setSelectedModalImage()
    } else {
      this.renderEmptyImageboxContainer()
    }


    // data footer
    this.setModalFooter()
  }

  getSelectedImage(multiple = false) {

    /**
     * Si les images sélectionnées sont vide alors on rajoute dans le cas contraire on ne fait rien
     */
    if (!this.selectedImages.length) {
      this.images.forEach(image => {
        if (image.select) {
          /**
           * Si la collection est de type image nous poussons chaque image sélectionné dans la tableau selectedImages
           */
          multiple ? this.selectedImages.push(image) : this.selectedImage = image
        }
      });
    }

    /**
     * En fonction de la collection on retourne la valeur
     */
    if (multiple) {
      return this.selectedImages
    }
    return this.selectedImage || {}
  }

  setSelectedImage() {

    if (this.isImagesCollection(this.collection)) {
      const selectedImages = this.getSelectedImage(true)

      selectedImages.forEach(image => this.getImageBox(image.id).addClass(this.config.chooseimage))

    } else {

      this.getImageBox(this.getSelectedImage().id).addClass(this.config.chooseimage)
    }
  }

  addButtonsClickHandle(button, input) {
    button.on('click', function (ev) {
      ev.preventDefault()
      input.click()
    })
  }

  insertFormTemplate(name, target) {
    $(`<form name='${name}'  enctype="multipart/form-data" class='d-none'>
                <input type="file" name='${name}' accept="image/*" multiple>
                </form>
                `).insertBefore(target)
  }

  getFileExtension(name) {
    return name.substring(name.lastIndexOf('.') + 1).toLowerCase()
  }

  validateFile(image) {

    const ext = this.getFileExtension(image.name)

    if (this.authorized_extensions.includes(ext)) {
      return true
    } else {
      swal({
        title: 'Ajout de média !',
        text: "Erreur lors du traitement de l'image `" + image.name + '`. Veuillez choisir une image de type (jpg, jpeg, png, svg).',
        icon: 'error'
      })
      return false
    }
  }

  getCreatedDate(date = null) {

    date = date ? date : new Date()

    const month = (date.getMonth() > 8) ? (date.getMonth() + 1) : ('0' + (date.getMonth() + 1)),
      day = (date.getDate() > 9) ? date.getDate() : ('0' + date.getDate()),
      year = date.getFullYear(),
      hour = date.getHours(),
      minute = date.getMinutes()

    return `${day}/${month}/${year} ${hour}:${minute}`
  }

  generateImageId() {
    return Math.round(Math.random() * Math.random() * 10000000)
  }

  previewImage(image, event = false) {

    // ajouter manuellement les attributs si nous sommes en phase de création
    if (this.isEmptyModel()) {
      image.id = this.generateImageId()
      image.order = this.images.length + 1
      image.new_name = image.name
      image.url = event.target.result
      image.select = false
      image.created_at = new Date()
      image.date_for_humans = this.getCreatedDate()
      image.thumb_url = event.target.result
    }

    // Prévisualisation des différentes images

    this.getModalContainer().prepend(
      this.getPreviewedModalImageTemplate(image, event)
    )

    this.images.push(image)

    this.addCheckUncheckAll();

    this.renderHeaderButtons()

    // Sélectionner l'image
    this.getImageBox(image.id).find(this.config.checkimage).trigger('click')

    this.setModalFooter()
  }

  isFrontImageCollection(collection = null) {
    return (collection || this.collection) === this.collections.front
  }

  isBackImageCollection(collection = null) {
    return (collection || this.collection) === this.collections.back
  }

  isImagesCollection(collection = null) {
    return (collection || this.collection) === this.collections.images
  }

  getOrderedImageBox(element) {
    return this.getImageBox($(element).parents('.imagebox').data('id'))
    // return this.getModalContainer().find('[data-id=' + $(element).parents('.imagebox').data('id') + ']')
  }

  removeChooseClass(box = null) {
    if (box) {
      box.removeClass(this.config.chooseimage)
    } else {
      this.getModalContainer().find('.' + this.config.chooseimage).removeClass(this.config.chooseimage)
    }
  }

  renameImage(event) {
    const button = $(event.relatedTarget)
    const id = button.data('id')

    const image = this.getImage(id)

    const input = this.getRenameModal().find('.modal-body input')
    // input.trigger('focus')
    input.val(button.data('name'))

    this.getRenameModal().find('.modal-footer button').unbind('click').bind('click', (event) => {
      event.preventDefault()

      const value = input.val()

      if (!value) {
        this.alert(this.alerts.rename.error)
        return
      }

      if (this.isEmptyModel()) {
        image.new_name = value
      } else {
        axios.post(`/${this.config.prefix}/media/${id}/rename`, { name: value })
      }

      button.parents('.imagebox').find('.filename').text(value)
      button.data('name', value)


      this.setSelectedModalImage(value, image)

      this.alert(this.alerts.rename.success)

      this.getRenameModal().modal('hide')



    })
  }

  chooseImage(event) {
    event.preventDefault()

    /**
     * On retire la classe de sélection à l'élément actuel et de le mettre au box qui vient d'être sélectionné
     * si ce n'est pas une collection de type images (qui autorise la sélection de plusieurs images)
     */
    if (!this.isImagesCollection(this.collection)) {
      this.removeChooseClass()
    }


    const imageBox = this.getOrderedImageBox(event.target)

    imageBox.addClass(this.config.chooseimage)
    const image = this.getImage(imageBox.data('id'))

    imageBox.addClass(this.config.chooseimage)


    if (this.isEmptyModel()) {
      if (!this.isImagesCollection(this.collection)) {
        // trouver l'ancienne box selectionne et retirer le deselectionner
        this.renderOldImage()
      }

      /**
       * On séletionne le nouveau modal
       */
      this.setSelectedModalImage(null, image)

      /**
       * remplacer le contenu de la box pour que les liens soient mis à jour (
       * sélectionner en désélectionner
       * )
       */
      imageBox.replaceWith(this.getPreviewedModalImageTemplate(image))

      return
    } else {
      // const imageBox = this.getOrderedImageBox(event.target)

      // imageBox.addClass(this.config.chooseimage)
      // const image = this.getImage(imageBox.data('id'))

      // imageBox.addClass(this.config.chooseimage)

      axios.post(`/${this.config.prefix}/media/${image.id}/select`)
        .then(({ data }) => {


          if (!this.isImagesCollection(this.collection)) {
            // trouver l'ancienne box selectionne et retirer le deselectionner
            this.renderOldImage()
          }

          /**
           * On séletionne le nouveau modal
           */
          this.setSelectedModalImage(null, data.media)

          /**
           * remplacer le contenu de la box pour que les liens soient mis à jour (
           * sélectionner en désélectionner
           * )
           */
          imageBox.replaceWith(this.getPreviewedModalImageTemplate(data.media))
        })

    }

  }


  unChooseImage(event) {
    event.preventDefault()

    const imageBox = this.getOrderedImageBox(event.target)

    if (this.isImagesCollection(this.collection)) {
      // retirer l'image désélectionner de la collection
      // const selectedImages = this.selectedImages.filter(image => image.id != imageBox.data('id'))
      const selectedImages = this.removeItemInSelectedImage(imageBox.data('id'), false)

      this.selectedImages = [...selectedImages]

      this.setSelectedModalImage(null, this.getImagesCollectionSelectedImage(), false)
    } else {
      this.selectedImage = null
      this.setSelectedModalImage()
    }


    this.removeChooseClass(imageBox)

    if (this.isEmptyModel()) {
      // Désélectionner manuellement
      const image = this.getImage(imageBox.data('id'))
      image.select = false

      imageBox.replaceWith(this.getPreviewedModalImageTemplate(image))
    } else {
      axios.post(`/${this.config.prefix}/media/${imageBox.data('id')}/unselect`)
        .then(({ data }) => {
          imageBox
            .replaceWith(this.getPreviewedModalImageTemplate(data.media))
        })
    }


  }

  renderOldImage() {
    const old_image = this.selectedImage

    if (!old_image) return

    old_image.select = false

    this.getImageBox(old_image.id).replaceWith(
      this.getPreviewedModalImageTemplate(old_image)
    )
  }



  previewImageInCollectionContainer(image) {

    /**
     * Si aucune image n'a été sélectionné alors on ne fait rien
     */
    const imageBox = this.getCollectionContainer().children('.imagebox')
    imageBox.empty()

    if (image) {
      imageBox.append(
        this.getPreviewedImageTemplate(image)
      )
      this.selectedImage = image
    }
  }

  getPreviewedImageTemplate(image) {


    const height = this.isImagesCollection(this.collection) ? 100 : 250

    const html = `
            <i class="close-icon fa fa-times" aria-hidden="true"
                title="Supprimer l'image" style="font-size: 1.5rem; display: none;cursor: pointer;color: red">
            </i>
            <a href="${image.url}" title='${this.isEmptyModel() ? image.new_name : image.name}' data-fancybox="gallery"
                            data-caption="${ this.isEmptyModel() ? image.new_name : image.name}">
                <img style="height:${height}px;" class="img-thumbnail" src='${this.isEmptyModel() ? image.url : image.url}'
                data-id="${image.id}"
                alt="${ this.isEmptyModel() ? image.new_name : image.name}">
            </a>
            `


    if (this.isImagesCollection(this.collection)) {
      return `
                <div class="col-4 imagebox pb-2" data-id='${image.id}'>
                    <div class="lightbox imagethumbnail">
                        ${html}
                    </div>
                </div>
                `
    }

    return html


  }

  getFileSize(aSize) {
    aSize = Math.abs(parseInt(aSize, 10))
    var def = [[1, 'octets'], [1024, 'ko'], [1024 * 1024, 'Mo'], [1024 * 1024 * 1024, 'Go'], [1024 * 1024 * 1024 * 1024, 'To']]
    for (var i = 0; i < def.length; i++) {
      if (aSize < def[i][0]) return (aSize / def[i - 1][0]).toFixed(2) + ' ' + def[i - 1][1]
    }
  }

  getPreviewedImageTemplateForEmptyModel(image, event = false) {
    return `
                <div class="imagebox  col-12 col-sm-12 col-md-6 col-lg-4 ${ (image.select) ? this.config.chooseimage : ''}" data-id="${image.id}">
                    <div class="file-man-box">
                        <a href="#" class="file-close">
                            <i class="fa fa-check"></i>
                        </a>
                        <div class="file-img-box">
                            <img src="${event ? event.target.result : image.url}" alt="${this.isEmptyModel() ? image.new_name : image.name}">
                        </div>

                        <a href="#" class="file-download dropdown" >
                            <i class="fa fa-tools dropdown-toggle" data-toggle="dropdown" data-offset="10,30"></i>
                                <div class="dropdown-menu ">
                                    <button class="dropdown-item" type="button" data-view='${image.id}'>
                                        <i class="fa fa-image"></i> &nbsp;
                                        Voir
                                    </button>

                            ${image.select ? `
                                <button class="dropdown-item " type="button" data-unchoose >
                                    <i class="fa fa-times"></i> &nbsp;
                                    Désélectionner
                                </button>

                            ` : `
                                 <button class="dropdown-item " type="button" data-choose >
                                    <i class="fa fa-check"></i> &nbsp;
                                    Sélectionner
                                </button>
                            `}

                            <button class="dropdown-item" type="button"  data-rename data-toggle="modal"
                                data-target="${ this.config.renamemodal}" data-id='${image.id}' data-name='${image.new_name}'>
                                <i class="fa fa-edit"></i> &nbsp;
                                Renommer
                            </button>



                            <button class="dropdown-item" type="button" data-property='${image.id}'>
                                <i class="fa fa-info-circle"></i> &nbsp;
                                Propriétés
                            </button>


                            ${!image.select ? `
                                <div class="dropdown-divider"></div>
                                    <button class="dropdown-item text-danger " type="button" data-delete='${image.id}'><i class="fa fa-trash"></i>
                                    &nbsp; Effacer</button>

                                ` : `

                                `}
                             </div>
                        </a>
                        <div class="file-man-title">
                            <h5 class="mb-0 text-overflow filename">${ this.isEmptyModel() ? image.new_name : image.name}</h5>
                            <p class="mb-0"><small>${this.getFileSize(image.size)}</small></p>
                        </div>

                    </div>
                </div>


            `
  }

  getPreviewedModalImageTemplate(image, event = false) {

    if (this.isEmptyModel()) {
      return this.getPreviewedImageTemplateForEmptyModel(image, event)
    }

    return `
                <div class="imagebox  col-12 col-sm-12 col-md-6 col-lg-4 ${ (image.select) ? this.config.chooseimage : ''}" ${event ? '' : `data-id="${image.id}"`}>
                    <div class="file-man-box">
                        <a href="#" class="file-close">
                            <i class="fa fa-check"></i>
                        </a>
                        <div class="file-img-box">
                            <img src="${event ? event.target.result : image.url}" alt="${image.name}">
                        </div>

                        <a href="#" class="file-download dropdown">
                            <i class="fa fa-tools dropdown-toggle" data-toggle="dropdown" data-offset="10,30"></i>
                                <div class="dropdown-menu ">
                                    <button class="dropdown-item" type="button" data-view='${image.id}'>
                                        <i class="fa fa-image"></i> &nbsp;
                                        Voir
                                    </button>

                            ${image.select ? `
                                <button class="dropdown-item " type="button" data-unchoose >
                                    <i class="fa fa-times"></i> &nbsp;
                                    Désélectionner
                                </button>

                            ` : `
                                 <button class="dropdown-item " type="button" data-choose >
                                    <i class="fa fa-check"></i> &nbsp;
                                    Sélectionner
                                </button>
                            `}

                            <button class="dropdown-item" type="button"  data-rename data-toggle="modal"
                                data-target="${ this.config.renamemodal}" data-id='${image.id}' data-name='${image.name}'>
                                <i class="fa fa-edit"></i>
                                Renommer
                            </button>

                            <button class="dropdown-item" type="button"
                                data-download='${image.url}' data-name='${image.name}.${this.getFileExtension(image.file_name || image.name)}'>
                                <i class="fa fa-download"></i> &nbsp; Télécharger
                            </button>

                            <button class="dropdown-item" type="button" data-property='${image.id}'>
                                <i class="fa fa-info-circle"></i> &nbsp;
                                Propriétés
                            </button>

                            <button class="dropdown-item" type="button" data-copyurl='${image.url}'>
                                <i class="fa fa-copy"></i> &nbsp;
                                Copier le lien
                            </button>
                               ${!image.select ? `
                                    <div class="dropdown-divider"></div>
                                    <button class="dropdown-item text-danger " type="button" data-delete='${image.id}'><i class="fa fa-trash"></i>
                                    &nbsp; Effacer</button>

                                ` : `

                                `}
                            </div>

                        </a>
                        <div class="file-man-title">
                            <h5 class="mb-0 text-overflow filename">${image.name}</h5>
                            <p class="mb-0"><small>${this.getFileSize(image.size)}</small></p>
                        </div>
                    </div>
                </div>

                            `
  }

  addImagesEvents() {
    const container = $(this.config.images_container)

    container.on('mouseenter', '.imagebox', this.handleMouseEnter)
    container.on('mouseleave', '.imagebox', this.handleMouseLeave)
    container.on('click', '.close-icon', this.handleCloseIconClick)

    container.prev().on('click', '[data-delete=all]', this.deleteAllSelectedImage)
  }

  deleteAllSelectedImage(event) {
    event.preventDefault()


    const collection = $(event.target).parent().parent().find('[data-image]').data('image')
    const imageBoxes = $(event.target).parent().next().children()

    swal({
      title: 'Suppression !',
      text: 'Etes de vous sûr de bien vouloir supprimer toutes les images sélectionnées. Cette action est irréversible.',
      icon: 'warning',
      dangerMode: true,
      buttons: {
        cancel: 'Annulez',
        confirm: 'Confirmez!'
      }
    })
      .then((isConfirm) => {

        if (isConfirm) {


          if (this.isEmptyModel()) {
            imageBoxes.each((index, box) => {
              $(box).fadeOut(600 * index, () => {
                $(box).remove()
              })
            })
            $(event.target).parent().addClass('d-none')

            this.selectedImages = []
            this.images = []
            this.tempImages[this.collection] = this.images

            this.addCheckUncheckAll();

            this.renderHeaderButtons()

          } else {
            axios.delete(`${this.getUrl()}/${collection}/all`)
              .then((data) => {


                imageBoxes.each((index, box) => {
                  $(box).fadeOut(600 * index, () => {
                    $(box).remove()
                  })
                })

                $(event.target).parent().addClass('d-none')

                this.selectedImages = []
                this.images = []

                this.addCheckUncheckAll();

                this.renderHeaderButtons()
              })
          }
        }
      })


  }


  hideBoxWhenDeleteByCloseIcon(imageBox, collection) {
    /**
     * On masque la box, on supprime les enfants et après on l'affiche pour que les
     * sélections suivantes puissent s'afficher
     */
    if (this.isImagesCollection(collection)) {
      let imageContainerLength = imageBox.parent().children().length

      imageBox.fadeOut(600, _ => {
        imageBox.remove()

        if (!(imageContainerLength -= 1)) {
          $('[data-delete=all]').hide()
        }

      })

    } else {
      imageBox.fadeOut(600, function () {
        imageBox.children().remove()
      }).fadeIn()

      this.selectedImage = null
      this.setSelectedModalImage()
    }
  }

  /**
   * Permet de retirer une image dans la liste des tableaux ou retourner la nouvelle liste une fois filtré
   * @param {int} image_id
   * @param {bool} remove
   */
  removeItemInSelectedImage(image_id, remove = true) {
    // Retirer l'image sélectionné de la liste des images
    if (!remove) {
      return this.selectedImages.filter(image => image.id != image_id)
    }
    this.selectedImages = [...this.selectedImages.filter(image => image.id != image_id)]
  }

  handleCloseIconClick(event) {
    event.stopPropagation()

    const imageBox = $(event.target).parents('.imagebox'),
      collection = imageBox.parent().next().find('[data-image]').data('image'),
      image_id = imageBox.find('[data-id]').data('id')


    swal({
      title: 'Suppression !',
      text: 'Etes de vous sûr de bien vouloir supprimer cette image. Cette action est irréversible.',
      icon: 'warning',
      dangerMode: true,
      buttons: {
        cancel: 'Annulez',
        confirm: 'Confirmez!'
      }
    })
      .then((isConfirm) => {
        if (isConfirm) {
          if (this.isEmptyModel()) {

            // Désélectionner l'élément
            this.getImageBox(image_id).find(this.config.uncheckimage).trigger('click')

            this.removeItemInSelectedImage(image_id)

            this.hideBoxWhenDeleteByCloseIcon(imageBox, collection)

            // supprimer l'image dans la liste
            this.images = this.images.filter(image => image.id != image_id)
            this.tempImages[this.collection] = this.images

            this.addCheckUncheckAll();

            this.renderHeaderButtons()


          } else {
            axios.delete(this.getUrl(), { data: { image_id } })
              .then((data) => {
                this.removeItemInSelectedImage(image_id)
                this.hideBoxWhenDeleteByCloseIcon(imageBox, collection)
                this.images = this.images.filter(image => image.id != image_id)

                this.addCheckUncheckAll();

                this.renderHeaderButtons()
              })
          }
        } else {
          $(event.target).parents('.imagebox').find('img').css('border', 'none')
        }
      })
  }

  handleMouseEnter(event) {
    if ($(this).hasClass('selected')) {
      $(this).find('.close-icon').hide()
      return
    }
    $(this).find('.close-icon').show()
    $(this).find('img').css('border', '3px solid red')
  }

  handleMouseLeave(event) {
    if ($(this).hasClass('selected')) {
      $(this).find('.close-icon').hide()
      return
    }
    $(this).find('.close-icon').hide()
    $(this).find('img').css('border', 'none')
  }


  orderSortable(elements) {
    if (!this.isImagesCollection(this.collection)) {
      return
    }

    const elmts = [...elements]

    /**
     * On ordonne la liste des images sélectionnés
     */
    this.selectedImages = []
    elmts.forEach(elm => {
      if ($(elm).hasClass(this.config.chooseimage)) {
        this.selectedImages.push(this.getImage($(elm).data('id')))
      }
    })
  }

  saveSortableOrder(items) {

    if (this.isEmptyModel()) {
      for (let i = 0; i < items.length; i++) {

        const id = parseInt(items[i].dataset.id, 10)
        this.getImage(id).order = i + 1
      }
    } else {
      const ids = []
      for (let i = 0; i < items.length; i++) {
        ids.push(
          {
            id: parseInt(items[i].dataset.id, 10),
            order: i + 1
          }
        )
      }
      axios.post(`/${this.config.prefix}/media/order`, { ids })
    }
  }


  handleSortable() {
    const that = this

    const model_container = this.getModalContainer()[0]
    if (model_container) {
      const modal_sorter = new Sortable(model_container, {
        multiDrag: true,
        selectedClass: 'selected',
        animation: 150,

        onEnd(event) {
          that.orderSortable(modal_sorter.el.children)
        },

        store: {
          set(sortable) {
            that.saveSortableOrder(sortable.el.children)
          }
        }
      })
    }

    const images_container = $(this.config.images_sortable_container)[0]
    if (images_container) {
      const images_sortener = new Sortable($(this.config.images_sortable_container)[0], {
        multiDrag: true,
        selectedClass: 'selected',
        animation: 150,

        onEnd(event) {
          that.orderSortable(images_sortener.el.children)
        },

        store: {
          set(sortable) {
            that.saveSortableOrder(sortable.el.children)
          }
        }
      })
    }
  }

  getUrl() {
    return `/${this.config.prefix}/media/${btoa(this.config.model_name)}/${this.config.model.id}`
  }

  upload(images, collection) {
    for (let i = 0; i < images.length; i++) {
      const image = images[i]


      if (image.size > this.limit_size) {
        this.alert(this.alerts.size.error)
        return
      }


      if (this.isEmptyModel()) {
        const reader = new FileReader()

        /**
         * Le continue permet de passer à l'élément suivant tandis que le return aurait stopper la fonction
         * et les autres images ne seront pas envoyés au serveur
         */
        if (!this.validateFile(image)) {
          continue
        }

        reader.readAsDataURL(image)

        reader.onload = (e) => {

          this.removeEmptyMesage()

          this.previewImage(
            image, e
          )
        };

      } else {
        const formData = new FormData()
        formData.append('image', image)
        formData.append('collection', collection)
        formData.append('order', this.getModalContainer().children().length + 1)

        axios.post(this.getUrl(), formData, {
          headers: {
            'Content-Type': 'multipart/form-data'
          }
        })
          .then(({ data }) => {
            this.removeEmptyMesage()

            this.previewImage(
              data.media
            )

            this.alert({
              message: "L'image " + image.name + ' a bien été téléversée!',
              type: 'success'
            })



          })
      }

    }
  }

  removeEmptyMesage() {
    if (!this.images.length) {
      this.getModalContainer().empty()
    }
  }

  setOptions(opts) {
    const options = opts || {}
    if (typeof options === 'object' && Object.keys(options).length > 0) {
      for (const property in options) {
        if (typeof this.config[property] !== undefined) {
          this.config[property] = options[property]
        }
      }
    }
  }
}
