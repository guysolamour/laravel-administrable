{!! form_start($form)!!}
<div class="row" x-data="createpost">
    <div class="col-md-8">
        {!! form_row($form->title) !!}
        {!! form_row($form->content) !!}

        <div class="card" x-show.transition="form.type === 'video'">
            <h5 class="card-header">Contenu vidéo </h5>
            <div class="card-body">
                <div class="form-group">
                    <label for="video_link">Lien de la vidéo</label>
                    <input x-model="form.video_link" type="text" name="video_link" id="video_link" class="form-control" placeholder="https://youtube.com" value="{{ $form->getModel()->video_link }}">
                    <small  class="form-text text-muted">
                        Vous pouvez coller le lien de la vidéo entière ou l'identifiant qui vient après <b>?v=</b>
                    </small>

                </div>
                <iframe x-show="form.video_link.length > 0" :src="embedvideo"  frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
            </div>
        </div>

        <x-administrable::seoform :model="$form->getModel()" />

        <div class="form-group">
            <button type="button" @click.prevent="sendForm" class="btn btn-success"> <i class="fa fa-save"></i> Enregistrer</button>
        </div>
    </div>
    <div class="col-md-4">

        <div class="card card-secondary">
            <div class="card-header">
              <h3 class="card-title">Publication</h3>
            </div>
            <div class="card-body ">
              <div class="text-center py-2">
                  <div>
                    <button @click.prevent="saveDraft"  class="btn btn-success "> <i class="fa fa-save"></i> Brouillon</button>

                    <button @click.prevent="sendForm" type="submit"  class="btn  bg-info text-white"> <i class="fas fa-location-arrow"></i>
                        @if($form->getModel()->getKey())
                        Mettre à jour
                        @else
                        Publier
                        @endif
                    </button>
                  </div>
              </div>
              <div class="form-group">
                  <label for="type">Type de contenu</label>
                  <select x-model="form.type" @change="changeType"  name="type" id="type" required class="custom-select">
                    @foreach (config('administrable.extensions.blog.post.model')::TYPES as $type)
                        <option value="{{ $type['name'] }}" {{ $form->getModel()->type === $type['name'] ? 'selected' : '' }}>{{ $type['label'] }}</option>
                    @endforeach
                  </select>
              </div>

              <hr class="m-0">
              <div class="mt-2">
                 <label for="created_at">Date de création : </label> &nbsp;&nbsp;&nbsp;
                  <input type="text" name="created_at" id="created_at" class="form-control">
             </div>

             <div class="my-2">
                <button x-show="!form.schedule && !form.published_at" @click.prevent="form.schedule = true" class="btn btn-info btn-block">
                    <i class="fa fa-clock"></i> Programmez la publication
                </button>
                <div x-show="form.published_at || form.schedule">
                    <hr>
                    <div class="form-group">
                        <label for="published_at">Date de publication prévue </label>
                        <input type="text" id="published_at" x-ref="published_at_field"  class="form-control" >
                    </div>
                    {{--  <div class="btn-group float-right">
                        <button @click.prevent="cancelSchedule"  class="btn btn-secondary btn-sm"><i class="fa fa-times"></i> Annulez</button>
                    </div>  --}}
                </div>
             </div>

              <div class="form-group">
                  <label for="online">Visibilité</label>
                  <select x-model="form.online" name="online" id="online" class="custom-select" required>
                      <option value="0">Hors ligne</option>
                      <option value="1">En ligne</option>
                  </select>
              </div>
              {!! form_row($form->allow_comment) !!}

            </div>
            <!-- /.card-body -->
          </div>
          <div class="card card-secondary">
            <div class="card-header">
                <h3 class="card-title">Catégories</h3>
            </div>
            <div class="card-body" style="height: 200px; overflow: scroll;">
                <div x-show="!categories.add">
                    <template  x-for="category in categories.list" :key="category.id">
                        <div class="custom-control custom-checkbox">
                            <input @change='selectCategory(category.id)' :checked="checkCategory(category.id)"  type="checkbox"  class="custom-control-input" :id="category.slug">
                            <label class="custom-control-label font-weight-normal" :for="category.slug" x-text="category.name"></label>
                        </div>
                    </template>
                </div>
                <div x-show="categories.add">
                    <div class="form-group">
                        <label for="category-name" class="font-weight-normal">Nom</label>
                        <input type="text" id="category-name" x-model="categories.name"  class="form-control" :class="{'is-invalid': categories.errors.name.length != 0 }" placeholder="Catégorie">
                        <p class="invalid-feedback"  x-text="categories.errors.name[0]"></p>

                    </div>

                    <div class="btn-group d-flex justify-content-end">
                        <button type="button" @click.prevent="cancelAddCategory" class="btn btn-cancel btn-sm"> <i class="fa fa-times"></i> Annuler</button>
                        <button type="button" @click.prevent="saveCategory" class="btn btn-success btn-sm"> <i class="fa fa-save"></i> Ajouter</button>
                    </div>
                </div>

            </div>
            <div class="card-footer">
                <button @click.prevent="categories.add = true" class="btn btn-info btn-sm float-right"><i class="fa fa-plus"></i> Ajouter une catégorie</button>
            </div>
        <!-- /.card-body -->
        </div>
          <div class="card card-secondary collapsed-card">
            <div class="card-header">
                <h3 class="card-title">Etiquettes</h3>
            </div>
            <div class="card-body" style="height: 150px; overflow: scroll;">
                <div x-show="!tags.add">
                    <template  x-for="tag in tags.list" :key="tag.id">
                        <div class="custom-control custom-checkbox">
                            <input @change='selectTag(tag.id)' :checked="checkTag(tag.id)"  type="checkbox"  class="custom-control-input" :id="'tag' + tag.slug">
                            <label class="custom-control-label font-weight-normal" :for="'tag' + tag.slug" x-text="tag.name"></label>
                        </div>
                    </template>
                </div>
                <div x-show="tags.add">
                    <div class="form-group">
                        <label for="tag-name" class="font-weight-normal">Nom</label>
                        <input type="text" id="tag-name" x-model="tags.name"  class="form-control" :class="{'is-invalid': tags.errors.name.length != 0 }" placeholder="Etiquette">
                        <p class="invalid-feedback"  x-text="tags.errors.name[0]"></p>
                    </div>
                    <div class="btn-group d-flex justify-content-end">
                        <button type="button" @click.prevent="cancelAddTag" class="btn btn-cancel btn-sm"> <i class="fa fa-times"></i> Annuler</button>
                        <button type="button" @click.prevent="saveTag" class="btn btn-success btn-sm"> <i class="fa fa-save"></i> Ajouter</button>
                    </div>
                </div>

            </div>
            <div class="card-footer">
                <button @click.prevent="tags.add = true" class="btn btn-info btn-sm float-right"><i class="fa fa-plus"></i> Ajouter une étiquette</button>
            </div>
        <!-- /.card-body -->
        </div>

        <div class="card">
            <div class="card-body">
                {!! form_row($form->author_id) !!}
            </div>
        </div>
       @imagemanager([
            'collection'  => 'front-image',
            'type'        => 'image',
            'label'       => 'Image à la une',
            'model'       => $form->getModel(),
        ])
    </div>
</div>
{!! form_end($form) !!}

<x-administrable::tinymce :model="$form->getModel()" />

<x-administrable::select2 />

<x-administrable::daterangepicker
    fieldname="created_at"
    :model="$form->getModel()"
    :singledatepicker="true"
    opens="right"
    drops="bottom"
/>

<x-administrable::daterangepicker
    selector="#published_at"
    fieldname="published_at"
    :model="$form->getModel()"
    :singledatepicker="true"
    opens="right"
    drops="bottom"
/>


@push('js')
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('createpost', () => ({
            // data
            form: {
                schedule: false,
                online: 1,
                type: null,
                published_at: null,
                video_link: '',
            },
            post: @json($form->getModel()),
            categories: {
                add: false,
                name: '',
                list: @json($categories),
                selected: [],
                errors: { name: [] },
            },
            tags: {
                add: false,
                name: '',
                list: @json($tags),
                selected: [],
                errors: { name: [] },
            },
            init(){
                if (this.post.categories.length != 0){
                    this.categories.selected = this.post.categories.map(category => category.id)
                }

                if (this.post.tags.length != 0){
                    this.tags.selected = this.post.tags.map(tag => tag.id)
                }

                this.form.type = this.post.type

                if (this.post.video_link){
                    this.form.video_link = this.post.video_link
                }

                this.form.online = this.post.online ? 1 : 0

                this.handlePublishedAtField()
            },

            // methods
            handlePublishedAtField(){
                const $published_at = $(this.$refs.published_at_field)

                $published_at.on('apply.daterangepicker', (ev, picker) => {
                    this.form.published_at = picker.startDate.format('DD/MM/YYYY HH:mm')
                    this.form.online = 0
                });

                $published_at.on('cancel.daterangepicker', (ev, picker) => {
                    $published_at.val('');
                    this.form.schedule = false
                    this.form.published_at = null
                    this.form.online = 1
                });
            },
            saveDraft(){
                this.appendDataToRequest([
                    { name: 'online', value: 0 },
                    { name: 'published_at', value: null },
                    { name: 'schedule', value: 0 },
                ])
                this.sendForm()
            },
            sendForm(){
                if (this.categories.selected.length != 0){
                    this.appendDataToRequest([
                        { name: 'categories', value: this.categories.selected },
                    ])
                }

                if (this.tags.selected.length != 0){
                    this.appendDataToRequest([
                        { name: 'tags', value: this.tags.selected }
                    ])
                }

                this.appendDataToRequest([
                    { name: 'schedule', value: this.form.schedule ? 1 : 0 },
                ])

                if (this.form.published_at){
                    this.appendDataToRequest([
                        { name: 'published_at', value: this.form.published_at, encode: false },
                    ])
                }

                this.currentForm.submit()
            },
            /** CATEGORIES */
            saveCategory(){
                if (!this.categories.name){
                    this.categories.errors = {name: ['Le nom est requis pour l\'ajout']}
                    return;
                }

                axios.post('/administrable/extensions/blog/posts/category', { name: this.categories.name })
                    .then(({data}) => {
                        this.categories.list.push(data)

                        this.selectCategory(data.id)

                        this.cancelAddCategory()

                    }).catch((err) => {
                        this.categories.errors = err.response.data.errors;
                    });
            },
            checkCategory(categoryId){
                return this.categories.selected.includes(categoryId)
            },
            selectCategory(categoryId){
                if (this.categories.selected.includes(categoryId)){
                    this.categories.selected = this.categories.selected.filter(category => category != categoryId)
                }else {
                    this.categories.selected = [...this.categories.selected, categoryId]
                }
            },
            changeType(){
                this.selectCategory(this.categories.tv.id)
            }
            ,
            cancelAddCategory(){
                this.categories.add    = false
                this.categories.name   = ''
                this.categories.errors.name = [];
            },
            /** TAGS */
            saveTag(){
                if (!this.tags.name){
                    this.tags.errors = {name: ['Le nom est requis pour l\'ajout']}
                    return;
                }

                axios.post('/administrable/extensions/blog/posts/tag', { name: this.tags.name })
                    .then(({data}) => {
                        this.tags.list.push(data)

                        this.selectTag(data.id)

                        this.cancelAddTag()

                    }).catch((err) => {
                        this.tags.errors = err.response.data.errors;
                    });
            },
            checkTag(tagId){
                return this.tags.selected.includes(tagId)
            },
            selectTag(tagId){
                if (this.tags.selected.includes(tagId)){
                    this.tags.selected = this.tags.selected.filter(tag => tag != tagId)
                }else {
                    this.tags.selected = [...this.tags.selected, tagId]
                }
            },
            cancelAddTag(){
                this.tags.add    = false
                this.tags.name   = ''
                this.tags.errors.name = [];
            },
            /** HELPERS */
            appendDataToRequest(data){
                data.forEach(item => {
                    const input = document.createElement('input')
                    input.type  = 'hidden'
                    input.name  = item.name
                    input.value = item.encode === false ? item.value : JSON.stringify(item.value)
                    this.currentForm.appendChild(input)
                })
            },
            /** COMPUTED */
            get currentForm(){
                return document.querySelector('form[name={{$form->getModel()->form_name}}]')
            },
            get embedvideo(){
                if (!this.form.video_link){
                    return;
                }
                const link = this.form.video_link.split('?v=').pop()

                return 'https://www.youtube.com/embed/' + link
            },
        }));
    });
</script>
@endpush
