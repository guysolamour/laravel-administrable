@php
    $tags = $model->seo ?:  new (config('administrable.modules.seo.model'));
@endphp
<div class="card" x-data="seoable">
    <h5 class="card-header">Référencement </h5>
    <div class="card-body">
        <nav>
            <div class="nav nav-tabs" id="nav-tab" role="tablist">
                <a class="nav-item nav-link active" id="nav-home-tab" data-toggle="tab" href="#nav-home" role="tab"
                    aria-controls="nav-home" aria-selected="true">
                    <i class="fas fa-home"></i> Accueil
                </a>
                <a class="nav-item nav-link" id="nav-facebook-tab" data-toggle="tab" href="#nav-facebook" role="tab"
                    aria-controls="nav-facebook" aria-selected="false">
                    <i class="fab fa-facebook"></i> Facebook
                </a>
                <a class="nav-item nav-link" id="nav-twitter-tab" data-toggle="tab" href="#nav-twitter" role="tab"
                    aria-controls="nav-twitter" aria-selected="false">
                    <i class="fab fa-twitter"></i> Twitter
                </a>
                <a class="nav-item nav-link" id="nav-others-tab" data-toggle="tab" href="#nav-others" role="tab"
                    aria-controls="nav-others" aria-selected="false">
                    <i class="fa fa-user"></i> Auteur
                </a>
            </div>
        </nav>
        <div class="tab-content p-2 pt-4" id="nav-tabContent">
            <div class="tab-pane fade show active" id="nav-home" role="tabpanel" aria-labelledby="nav-home-tab">
                <div class="row">
                    <div class="form-group col-md-6">
                        <label for="page:title">Titre</label>
                        <input type="text" @change="handleTitleChange" x-model="form['page:title']" class="form-control" id="page:title" name="seo[page:title]"
                        placeholder="Titre de la page" value="{{ $tags['page:title'] }}">
                        <small class="form-text text-muted">
                            Le titre est très important pour les moteurs de recherche car c'est lui qui
                            incite les internautes à cliquer
                        </small>
                    </div>
                    <div class="form-group col-md-3">
                        <label for="robots:index">Permettre aux robots d'indéxer la page</label>
                        <select class="form-control" id="robots:index" name="seo[robots:index]">
                            <option {{ $tags['robots:index'] === 'index' ? 'selected' : '' }}  value="index">Oui</option>
                            <option {{ $tags['robots:index'] === 'noindex' ? 'selected' : '' }} value="noindex">Non</option>
                        </select>
                    </div>
                    <div class="form-group col-md-3">
                        <label for="robots:follow">Permettre aux robots de suivre les liens</label>
                        <select class="form-control" id="robots:follow" name="seo[robots:follow]">
                            <option {{ $tags['robots:follow'] === 'follow' ? 'selected' : '' }} value="follow">Oui</option>
                            <option {{ $tags['robots:follow'] === 'nofollow' ? 'selected' : '' }} value="nofollow">Non</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label for="page:canonical:url">Url canonique</label>
                    <input type="url" @change="handleCanonicalUrlChange" x-model="form['page:canonical:url']" class="form-control" placeholder="https://aswebagency.com/website" id="page:canonical:url" name="seo[page:canonical:url]" value="{{ $tags['page:canonical:url'] }}">
                    <small class="form-text text-muted">Laisser vide à moins de savoir ce que vous faites</small>
                </div>
                <div class="form-group">
                    <label for="page:meta:description">Description</label>
                    <textarea @change="handleMetadescriptionChange" x-model="form['page:meta:description']" class="form-control" id="page:meta:description" data-meta="description" name="seo[page:meta:description]"
                    placeholder="Meta description" :maxlength="options.meta_description_length">{{ $tags['page:meta:description'] }}</textarea>
                    <small class="form-text text-muted">
                        La meta description est le texte qui accompagne le titre lors de l'affichage sur facebook
                        (<mark><span x-text="metaDescriptionLength"></span> caractère(s) restant(s)</mark>)
                    </small>
                </div>
            </div>
            <div class="tab-pane fade" id="nav-facebook" role="tabpanel" aria-labelledby="nav-facebook-tab">
                <div class="row">
                    <div class="form-group col-md-6">
                        <label for="og:title">Titre de la page</label>
                        <input type="text" x-model="form['og:title']" class="form-control"  id="og:title" placeholder="Titre de la page"
                        name="seo[og:title]" value="{{ $tags['og:title'] }}">
                        <small class="form-text text-muted">
                            Le titre est très important pour les moteurs de recherche car c'est lui qui
                            incite les internautes à cliquer
                        </small>
                    </div>
                    <div class="form-group col-md-6">
                        <label for="og:type">Type de page</label> <br>
                        <select name="seo[og:type]" id="og:type" class="form-control">
                            <option value="webpage" {{ $tags['og:type'] === 'webpage' ? 'selected' : '' }}>page</option>
                            <option value="article" {{ $tags['og:type'] === 'article' ? 'selected' : '' }}>article</option>
                        </select>
                        <small class="form-text text-muted">
                            Les valeurs acceptées sont <mark>article</mark> pour les articles de blog, produit et <mark>webpage</mark>
                            pour une page internet classique
                        </small>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-md-6">
                        <label for="og:description">Description</label>
                        <textarea x-model="form['og:description']" class="form-control" placeholder="Description" name="seo[og:description]" id="og:description">{{ $tags['og:description'] }}</textarea>
                        <small class="form-text text-muted">
                            La meta description est le texte qui accompagne le titre lors de l'affichage sur les moteurs de recherche
                        </small>
                    </div>
                    <div class="form-group col-md-6">
                        <label for="og:url">Url</label>
                        <input type="url"  x-model="form['og:url']" class="form-control" name="seo[og:url]" id="og:url" value="{{ $tags['og:url'] }}" placeholder="https://aswebagency.com/website">
                        <small class="form-text text-muted">Laisser vide à moins de savoir ce que vous faites</small>

                    </div>

                </div>
                <div class="row">
                    <div class="form-group col-md-6">
                        <label for="og:image">Image</label>
                        <div class="custom-file">
                            <input type="file" class="simple-file-input" id="og:image" name="seo[og:image]" @change="handleImageChange($event)" />
                            <small class="form-text text-muted">Cette image sera utilisée lors du partage sur Facebook</small>
                        </div>
                        <div class="seoimagebox mt-3">
                            @if($tags['og:image'])
                            <i class="close-icon fa fa-trash" aria-hidden="true" title="Supprimer l'image"
                            style="font-size: 1rem; cursor: pointer; color: red; display: none;"></i>
                            <a href="{{ $tags['og:image'] }}" data-fancybox="gallery">
                                <img data-key="og:image" style="width: 120px; border: medium none;" class="img-thumbnail " src="{{ $tags['og:image'] }}">
                            </a>
                            @endif
                        </div>

                    </div>
                    <div class="form-group col-md-6">
                        <label for="og:locale">Locale (langue)</label>
                        <input type="url" class="form-control" name="seo[og:locale]" id="og:locale" value="{{ $tags['og:locale'] }}">
                        <span class="text-muted form-text">Si vide la locale (langue) par défaut sera utilisée</span>
                    </div>

                </div>
            </div>
            <div class="tab-pane fade" id="nav-twitter" role="tabpanel" aria-labelledby="nav-twitter-tab">
                <div class="row">
                    <div class="form-group col-md-6">
                        <label for="twitter:title">Titre</label> <br>
                        <input type="text" x-model="form['twitter:title']" name="seo[twitter:title]" placeholder="Titre de la page"
                        id="twitter:title" class="form-control" value="{{ $tags['twitter:title'] }}">
                        <small class="form-text text-muted">
                            Le titre est très important pour les moteurs de recherche car c'est lui qui
                            incite les internautes à cliquer
                        </small>
                    </div>
                    <div class="form-group col-md-6">
                        <label for="twitter:type">Carte Twitter</label> <br>
                        <select name="seo[twitter:type]" id="twitter:type" class="form-control">
                            <option value="summary" {{ $tags['twitter:type'] === 'summary' ? 'selected' : '' }}>Summary</option>
                            <option value="summary_large_image" {{ $tags['twitter:type'] === 'summary_large_image' ? 'selected' : '' }}>Summary large image</option>
                        </select>
                        <small class="form-text text-muted">
                            Les valeurs acceptées sont <mark>summary</mark> pour les articles de blog, produit et <mark>summary_large_image</mark>
                            pour une page internet classique
                        </small>
                    </div>
                    <div class="form-group col-sm-6">
                        <label for="twitter:description">Description</label> <br>
                        <textarea name="seo[twitter:description]" x-model="form['twitter:description']" id="twitter:description" placeholder="Description"
                        class="form-control">{{ $tags['twitter:description'] }}</textarea>
                        <small class="form-text text-muted">
                            La meta description est le texte qui accompagne le titre lors de l'affichage sur les moteurs de recherche
                        </small>
                    </div>

                    <div class="form-group col-sm-6">
                        <label for="twitter:image">Image Twitter</label> <br>
                        <div>
                            <div class="custom-file">
                                <input type="file" class="simple-file-input" id="twitter:image" name="seo[twitter:image]" @change="handleImageChange($event)" />
                                <small class="form-text text-muted">Cette image sera utilisée lors du partage sur Facebook</small>
                            </div>
                            <div class="seoimagebox mt-3">
                                @if($tags['twitter:image'])
                                <i class="close-icon fa fa-trash" aria-hidden="true" title="Supprimer l'image"
                                style="font-size: 1rem; cursor: pointer; color: red; display: none;"></i>
                                <a  href="{{ $tags['twitter:image'] }}" data-fancybox="gallery">
                                    <img style="width: 120px; border: medium none;" class="img-thumbnail" data-key="twitter:image"
                                    src="{{ $tags['twitter:image'] }}">
                                </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="tab-pane fade" id="nav-others" role="tabpanel" aria-labelledby="nav-others-tab">
                <div class="row">
                    <div class="form-group col-sm-12">
                        <label for="page:author">Auteur de cet article / page web</label> <br>
                        <input type="text" class="form-control" id="page:author" name="seo[page:author]" value="{{ $tags['page:author'] }}">
                    </div>
                    <div class="form-group col-sm-12">
                        <label for="page:meta:keywords">Les mots clés</label> <br>
                        <textarea name="seo[page:meta:keywords]" id="page:meta:keywords" class="form-control">{{ $tags['page:meta:keywords'] }}</textarea>
                        <span class="text-muted form-text">Les mots clés doivent être séparés par une virgule</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<x-administrable::custominputfile />

@once
    @push('css')
    <style>
        .seoimagebox {
            position: relative;
        }
        .seoimagebox .close-icon {
            position: absolute;
            top: 5%;
            left: 2%;
            font-size: 14px;
        }
    </style>
    @endpush
@endonce

@push('js')
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('seoable', () => ({
             // Data
            form: @json($tags),
            options: {
                form: "form[name={{ $model->form_name }}]",
                model: @json($model),
                model_name: @json(get_class($model)),
                model_id: {{ $model->id ?? 0 }},
                prefix: "{{ config('administrable.auth_prefix_path') }}",
                meta_description_length: 160,
            },
            init(){
                this.addEnctypeFormDataToParentForm()

                this.addRemoveImageIcon()
            },
            // Computed
            get metaDescriptionLength(){
                const meta_description = this.form['page:meta:description']
                const length = meta_description ? meta_description.length : 0

                return this.options.meta_description_length - length
            },
            get parentForm(){
                return document.querySelector(this.options.form)
            },
            // Methods
            handleTitleChange(){
                const value = this.form['page:title']

                this.form['og:title']      = value
                this.form['twitter:title'] = value
            },
            handleCanonicalUrlChange(){
                const value = this.form['page:canonical:url']

                this.form['og:url'] = value
            },
            handleMetadescriptionChange(){
                const value = this.form['page:meta:description']

                this.form['og:description']      = value
                this.form['twitter:description'] = value
            },
            addRemoveImageIcon(){
                const $form = $(this.parentForm)

                $form.find('.seoimagebox').hover(function(){
                    $(this).find('img').css('border','2px solid red');
                    $(this).find('.close-icon').css('display','block');
                },function(){
                    $(this).find('img').css('border','none');
                    $(this).find('.close-icon').css('display','none');
                })

                $form.find('.seoimagebox').on('click', '.close-icon', this.handleCloseIcon.bind(this))
            },
            addEnctypeFormDataToParentForm(){
                const form = this.parentForm

                if (!form.getAttribute('enctype')){
                    form.setAttribute('enctype', 'multipart/form-data')
                }
            },
            handleImageChange(event){
                const imgPath = event.target.value;
                const image = event.target.files[0]
                const ext = imgPath.substring(imgPath.lastIndexOf('.') + 1).toLowerCase()

                let reader = new FileReader()

                // validate
                if (ext == "gif" || ext == "png" || ext == "jpg" || ext == "jpeg" || ext == "svg"){
                    reader.readAsDataURL(image)
                }else {
                    alert("Veuillez choisir une image (jpg, jpeg, png, svg).")
                    return
                }
                // preview image
                reader.onload = function (e) {
                    const imagebox = $(event.target).parent().next('.seoimagebox')
                        imagebox.empty()
                        imagebox.append(`
                        <i class="close-icon fa fa-trash" aria-hidden="true" title="Supprimer l'image"
                        style="font-size: 1rem; cursor: pointer; color: red; display: none;"></i>
                        <a href="${e.target.result}" data-fancybox="gallery">
                            <img style="width: 120px; border: medium none;" class="img-thumbnail " src="${e.target.result}">
                        </a>
                    `)
                };
            },
            removeBox(box){
                box.fadeOut(600, () => {
                    box.children().remove()
                    box.fadeIn()
                })
            },
            handleCloseIcon (event)  {
                event.preventDefault()
                swal({
                    title: 'Suppression !',
                    text: 'Etes vous sûr de vouloir supprimer cette image ? ',
                    icon: 'warning',
                    dangerMode: true,
                    buttons: {
                        cancel: 'Annulez',
                        confirm: 'Confirmez!'
                    }
                })
                .then((isConfirm) => {
                    if (isConfirm) {
                        // const $box = $(event.target)
                        const $box = $(event.target).parent()
                        const url = `/${this.options.prefix}/media/seo/${btoa(this.options.model_name)}/${this.options.model_id}`


                        if (this.options.model_id) {
                            axios.delete(url, { data: { field: $box.find('img').data('key') } })
                            .then((data) => {
                                this.removeBox($box)
                            })
                        }else {
                            this.removeBox($box)
                        }

                    }
                })

            }
        }));
    });

</script>
@endpush
