class TinymceImageManager {

  static init(opts) {
    new TinymceImageManager(opts)
  }

  constructor(opts) {

    this.binds()

    this.config = {
      images_container: '.image-container', selector: '[data-image]',
      modal: '#mediaModal', renamemodal: '#renameModal',
      modalimagescontainer: '#modal-images-container',
      images_sortable_container: '.images-box',
      uploadimage: '[data-upload]', uploadmodal: '#uploadModal',
      dropzone: '.dropzone', modalfooter: '[data-modal="footer"]',
      modalselectedimage: '[data-modal="selected"]', copyurl: '[data-copyurl]',
      viewimage: '[data-view]', viewmodal: '#viewModal',
      checkimage: '[data-choose]', uncheckimage: '[data-unchoose]',
      chooseimage: 'choosed-image', dropzoneclass: '.dropzone',
      modalalert: '[data-modal="alert"]', downloadimage: '[data-download]',
      deleteimage: '[data-delete]', checkall: '[data-checkall]', uncheckall: '[data-uncheckall]',
      'refresh': '[data-refresh]', 'search': '[data-search]', 'sort': '[data-sort]',
      deleteallimages: '[data-deleteall]',
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
    }



    this.authorized_extensions = ['png', 'jpg', 'gif', 'jpeg', 'svg']

    this.collection = 'attachments'
    this.uploadingImages = []
    this.selectedImages = []
    this.tempImages = {}
    this.images = []

    this.collections = {
      attachments: 'attachments',
    }

    this.setOptions(opts)

    this.init()



  }
  binds() {
    // Superbe technique pour binder this aux différentes méthodes
    const methods = [
      'setOptions', 'uploadModal', 'handleChange', 'copyurl', 'viewImage', 'chooseImage',
      'unChooseImage', 'renameImage', 'handleDrop', 'downloadAllImage', 'downloadImage',
      'deleteImage', 'uncheckAll',
      'checkAll', 'refresh', 'search', 'resetSearch', 'sort', 'deleteAllImages'
    ]

    methods.forEach((fn) => this[fn] = this[fn].bind(this))
  }

  init() {
    // fecth images
    this.getModal().find(this.config.uploadimage).on('click', this.uploadModal)
    this.generateFormFor(this.collection)


    this.getImages()

    this.getModalContainer().on('click', this.config.viewimage, this.viewImage)
    this.getModalContainer().on('click', this.config.checkimage, this.chooseImage)

    this.getModalContainer().on('click', this.config.uncheckimage, this.unChooseImage)

    this.getModal().find(this.config.downloadimage).on('click', this.downloadAllImage)
    this.getModalContainer().on('click', this.config.downloadimage, this.downloadImage)

    this.getModalContainer().on('click', this.config.deleteimage, this.deleteImage)
    this.getModal().find(this.config.deleteallimages).on('click', this.deleteAllImages)

    this.getModal().find(this.config.refresh).on('click', this.refresh)
    this.getModal().find(this.config.search).on('keydown', this.search)
    this.getModal().find(this.config.search).on('blur', this.resetSearch)

    this.getModal().find(this.config.sort).on('click', this.sort)


    this.addCheckUncheckAll();

    this.getRenameModal().on('show.bs.modal', this.renameImage)

    this.setDropZone()

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


  refresh() {
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

  resetSearch(event) {
    event.preventDefault()

    const value = event.target.value

    if (!value) {
      this.addImages(this.images)
      event.target.value = ''
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


  addCheckUncheckAll() {

    const checkAll = this.getModal().find(this.config.checkall)
    const uncheckAll = this.getModal().find(this.config.uncheckall)

    if (!this.isAttachmentsCollection()) {
      checkAll.addClass('d-none')
      uncheckAll.addClass('d-none')
      return
    }

    checkAll.removeClass('d-none')
    uncheckAll.removeClass('d-none')

    checkAll.on('click', this.checkAll)
    uncheckAll.on('click', this.uncheckAll)
  }


  checkAll(event) {
    event.preventDefault()
    this.getModalContainer().children().find(this.config.checkimage).trigger('click')
  }

  uncheckAll(event) {
    event.preventDefault()
    this.getModalContainer().children().find(this.config.uncheckimage).trigger('click')
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
      text: "Etes de vous sûr de bien vouloir supprimer toutes les images. Cette action est irréversible.",
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

          } else {
            axios.delete(`${this.getUrl()}/${this.collection}/all`)
              .then(({ data }) => {
                this.hideBoxWhenDeleteAll()
              })
          }

        }
      })
  }



  deleteImage(event) {
    event.preventDefault()

    const button = $(event.target)
    const image = this.getImage(button.data('delete'))

    swal({
      title: 'Suppression !',
      text: "Etes de vous sûr de bien vouloir supprimer le média '<b>" + image.name + "'</b>. Cette action est irréversible.",
      icon: 'warning',
      dangerMode: true,
      buttons: {
        cancel: 'Annulez',
        confirm: 'Confirmez!'
      }
    })
      .then((isConfirm) => {
        if (isConfirm) {
          axios.delete(this.getUrl(), { data: { image_id: image.id } })
            .then((data) => {
              const imageBox = this.getImageBox(image.id)
              imageBox.fadeOut(600, () => {
                imageBox.remove()
              })
              this.alert(this.alerts.delete.success)
            })
        } else {
          // swal('suppression désapprouvé')
          // $(event.target).parents('.imagebox').find('img').css('border', 'none')
        }
      })
  }

  downloadAllImage(event) {
    event.preventDefault()

    swal({
      title: 'Téléchargement !',
      text: 'Etes vous sûr de vouloir télécharger tous les images ? ',
      icon: 'warning',
      dangerMode: true,
      buttons: {
        cancel: 'Annulez',
        confirm: 'Confirmez!'
      }
    })
      .then((isConfirm) => {
        if (isConfirm) {
          this.getModalContainer().find(this.config.downloadimage).trigger('click')
        }
      })

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

  handleDrop(event) {
    event.preventDefault()
    event.stopPropagation()

    const images = event.originalEvent.dataTransfer.files

    this.previewDropOrSelectImages(images)

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

  getRenameModal() {
    return $(this.config.renamemodal)
  }

  getImageByName(name) {
    return this.images.filter(image => image.name == name)[0]
  }


  getViewModal() {
    return $(this.config.viewmodal)
  }


  getImage(id) {
    return this.images.filter(image => image.id == id)[0]
  }

  getImageProperties(event) {
    event.preventDefault()

    const image = this.getImage(event.target.dataset.property)


    this.setSelectedModalImage(null, image)
  }


  getImages() {
    // Récupérer les fichiers pour le modal
    if (!this.isEmptyModel()) {
      axios.get(`${this.getUrl()}/${this.collection}`)
        .then(({ data }) => {
          this.addImages(data)
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
  }

  isAttachmentsCollection(collection = null) {
    return (collection || this.collection) === this.collections.attachments
  }

  removeChooseClass(box = null) {
    if (box) {
      box.removeClass(this.config.chooseimage)
    } else {
      this.getModalContainer().find('.' + this.config.chooseimage).removeClass(this.config.chooseimage)
    }
  }

  getOrderedImageBox(element) {
    return this.getImageBox($(element).parents('.imagebox').data('id'))
    // return this.getModalContainer().find('[data-id=' + $(element).parents('.imagebox').data('id') + ']')
  }


  chooseImage(event) {
    event.preventDefault()

    /**
     * On retire la classe de sélection à l'élément actuel et de le mettre au box qui vient d'être sélectionné
     * si ce n'est pas une collection de type images (qui autorise la sélection de plusieurs images)
     */
    if (!this.isAttachmentsCollection()) {
      this.removeChooseClass()
    }


    const imageBox = this.getOrderedImageBox(event.target)

    imageBox.addClass(this.config.chooseimage)
    const image = this.getImage(imageBox.data('id'))

    imageBox.addClass(this.config.chooseimage)


    if (this.isEmptyModel()) {
      if (!this.isAttachmentsCollection()) {
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

      // axios.post(`/${this.config.prefix}/media/${image.id}/select`)
      //     .then(({ data }) => {


      //         if (!this.isAttachmentsCollection()) {
      //             // trouver l'ancienne box selectionne et retirer le deselectionner
      //             this.renderOldImage()
      //         }

      //         /**
      //          * On séletionne le nouveau modal
      //          */
      //         this.setSelectedModalImage(null, data.media)

      //         /**
      //          * remplacer le contenu de la box pour que les liens soient mis à jour (
      //          * sélectionner en désélectionner
      //          * )
      //          */
      //         imageBox.replaceWith(this.getPreviewedModalImageTemplate(data.media))
      //     })

      if (!this.isAttachmentsCollection()) {
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



  unChooseImage(event) {
    event.preventDefault()

    const imageBox = this.getOrderedImageBox(event.target)

    if (this.isAttachmentsCollection()) {
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
      // axios.post(`/${this.config.prefix}/media/${imageBox.data('id')}/unselect`)
      //     .then(({ data }) => {
      //         imageBox
      //             .replaceWith(this.getPreviewedModalImageTemplate(data.media))
      //     })
      imageBox
        .replaceWith(this.getPreviewedModalImageTemplate(data.media))
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
  renderEmptyImageboxContainer() {
    this.getModalContainer()
      .append(`
                        <div class='d-flex justify-content-center align-items-center w-100 h4 text-secondary'>
                            <p><i class='fa fa-empty-set'></i> La liste est vide</p>
                        </div>
                    `)

    this.selectedImage = null
    this.setSelectedModalImage()

  }

  emptyModalContainer() {
    this.getModalContainer().empty()
  }

  setSelectedImage() {

    const selectedImages = this.getSelectedImage(true)

    selectedImages.forEach(image => this.getImageBox(image.id).addClass(this.config.chooseimage))
  }

  getSelectedImage(multiple = false) {

    /**
     * Si les images sélectionnés sont vide alors on rajoute dans le cas contraire on ne fait rien
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

  setSelectedModalImage(name = null, image = null, push = true) {

    /**
     * Passer manuellement le select à true puisque rien n'est persisté
     */
    if (image && this.isEmptyModel()) {
      image.select = true
    }

    this.getSelectedModalImage().empty()

    /**
         * On push l'élément si ce dernier n'est pas déjà dans la liste
         */
    if (image && push) {
      this.selectedImages.push(image)
    }

    // this.getSelectedModalImage()
    // .append(this.getSelectedImageTemplate(this.getImagesCollectionSelectedImage()))

    /**
     * Faire la copie si seulement on a un model
     */
    if (!this.isEmptyModel()) {
      this.getModal().find(this.config.copyurl).on('click', this.copyurl)
    }
  }



  uploadModal(event) {
    event.preventDefault();
    this.getUploadModal().modal('show')
  }

  getImagesCollectionSelectedImage() {
    return this.selectedImages[this.selectedImages.length - 1]
  }

  getUploadModal() {
    return $(this.config.uploadmodal)
  }

  getSelectedModalImage() {
    return $(this.config.modalselectedimage)
  }



  getModal() {
    return $(this.config.modal)
  }

  insertFormTemplate(name, target) {
    $(`<form name='${name}'  enctype="multipart/form-data" class='d-none'>
                <input type="file" name='${name}' accept="image/*" multiple>
                </form>
                `).insertBefore(target)
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

      this.upload(this.uploadingImages)

      this.resetUploadModal()
      this.getUploadModal().modal('hide')

    })

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
          if (success)
            success()
        } else {
          if (failure)
            failure()
        }
      })
  }

  resetUploadModal() {

    this.uploadingImages = []

    this.getUploadModal().find('.preview').empty()

    this.renderUploadModalTitle()

    this.renderUploadModalFooter()
  }


  alert(key, duration = 3000) {

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

  isEmptyModel() {
    return $.isEmptyObject(this.config.model)
  }

  removeEmptyMesage() {
    if (!this.images.length) {
      this.getModalContainer().empty()
    }
  }

  generateImageId() {
    return Math.round(Math.random() * Math.random() * 10000000)
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

    // Sélectionner l'image
    this.getImageBox(image.id).find(this.config.checkimage).trigger('click')

    this.setModalFooter()
  }

  getImageBox(id) {
    return this.getModalContainer().find('[data-id=' + id + ']')
  }

  getModalFooter() {
    return $(this.config.modalfooter)
  }

  getModalAlert() {
    return $(this.config.modalalert)
  }


  setModalFooter() {

    this.getModalFooter().empty()

    const size = this.images.length ? this.getFileSize(this.getImagesSize(this.images)) : 0

    this.getModalFooter().append(`
                <div class="col-12 border p-2 bg-secondary text-center" >
                <i class='fas fa-clock'></i> Dossiers: Images liées | Images: ${this.images.length} | Taille images: ${size}
                </div>
                `)
  }

  getImagesSize(images) {
    return images.reduce(function (size, image) {
      return size + image.size
    }, 0)
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

                        <a href="#" class="file-download">
                            <i class="fa fa-tools dropdown-toggle" data-toggle="dropdown"></i>
                                <div class="dropdown-menu ">
                                    <button class="dropdown-item" type="button" data-view='${image.id}'>
                                        <i class="fa fa-image"></i>
                                        Voir
                                    </button>

                            ${image.select ? `
                                <button class="dropdown-item " type="button" data-unchoose >
                                    <i class="fa fa-times"></i>
                                    Désélectionner
                                </button>

                            ` : `
                                 <button class="dropdown-item " type="button" data-choose >
                                    <i class="fa fa-check"></i>
                                    Sélectionner
                                </button>
                            `}

                            <button class="dropdown-item" type="button"  data-rename data-toggle="modal"
                                data-target="${ this.config.renamemodal}" data-id='${image.id}' data-name='${image.new_name}'>
                                <i class="fa fa-edit"></i>
                                Renommer
                            </button>


                            <div class="dropdown-divider"></div>
                                <button class="dropdown-item text-danger  ${image.select ? 'disabled' : ''} " type="button" data-delete='${image.id}'><i class="fa fa-trash"></i>
                                Effacer</button>
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

                        <a href="#" class="file-download">
                            <i class="fa fa-tools dropdown-toggle" data-toggle="dropdown"></i>
                                <div class="dropdown-menu ">
                                    <button class="dropdown-item" type="button" data-view='${image.id}'>
                                        <i class="fa fa-image"></i>
                                        Voir
                                    </button>

                            ${image.select ? `
                                <button class="dropdown-item " type="button" data-unchoose >
                                    <i class="fa fa-times"></i>
                                    Désélectionner
                                </button>

                            ` : `
                                 <button class="dropdown-item " type="button" data-choose >
                                    <i class="fa fa-check"></i>
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
                                <i class="fa fa-download"></i> Télécharger
                            </button>

                            <div class="dropdown-divider"></div>
                                <button class="dropdown-item text-danger  ${image.select ? 'disabled' : ''} " type="button" data-delete='${image.id}'><i class="fa fa-trash"></i>
                                Effacer</button>
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

  upload(images) {
    for (let i = 0; i < images.length; i++) {
      const image = images[i]

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
        formData.append('collection', this.collection)

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

  getUrl() {
    return `/${this.config.prefix}/media/${btoa(this.config.model_name)}/${this.config.model.id}`
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

  renderUploadModalTitle() {
    this.getUploadModal().find('.modal-title span').text(`(${this.uploadingImages.length})`)
  }

  renderUploadModalFooter() {
    const footer = this.getUploadModal().find('.modal-footer')

    if (this.uploadingImages.length) {
      footer.removeClass('d-none')
    } else {
      footer.addClass('d-none')
    }
  }


  getDropzone() {
    return this.getUploadModal().find(this.config.dropzone)
  }


  getModalContainer() {
    return $(this.config.modalimagescontainer)
  }



  getFileSize(aSize) {
    aSize = Math.abs(parseInt(aSize, 10))
    var def = [[1, 'octets'], [1024, 'ko'], [1024 * 1024, 'Mo'], [1024 * 1024 * 1024, 'Go'], [1024 * 1024 * 1024 * 1024, 'To']]
    for (var i = 0; i < def.length; i++) {
      if (aSize < def[i][0]) return (aSize / def[i - 1][0]).toFixed(2) + ' ' + def[i - 1][1]
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
