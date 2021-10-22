<div class="card" x-data="category">
    <div class="card-header bg-secondary">
        <h3 class="card-title text-white">
            Catégories du produit
        </h3>
    </div>
    <div class="card-body">
    <div class="form-group">
        <select x-ref="categories" name="categories[]" id="product_categories" class="form-control select2" multiple>
            <option value="">Choisir les catégories</option>
            @foreach($categories as $category)
            <option {{ $product->categories->contains($category) ? "selected='selected'" : '' }}
                value="{{ $category->getKey() }}">{{ $category->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="border p-3" x-show="add">
            <div class="form-group">
                <label for="add_category_name">Nom <span class="text-danger">*</span></label>
                <input x-model="form.name" type="text" id="add_category_name" class="form-control" :class="{ 'is-invalid': form.name.length == 0 }">
                <div class="invalid-feedback" :show="form.name.length == 0">
                    Le nom de la marque est obligatoire
                </div>
            </div>
            <div class="form-group">
                <label for="add_category_description">Description</label>
                <textarea x-model="form.description" type="text" id="add_category_description" class="form-control" rows="2"></textarea>
            </div>
            <div class="d-flex justify-content-end">
                <div class="btn-group">
                    <button class="btn btn-secondary btn-sm" @click.prevent="cancel"><i class="fa fa-times"></i> Annuler</button>
                    <button class="btn btn-success btn-sm" :disabled="form.name.length == 0"  @click.prevent="save"><i class="fa fa-save"></i> Enregistrer</button>
                </div>
            </div>
        </div>
        <div class="mt-4 text-center float-none" x-show="!add" x-transition  role="group" aria-label="add new order">
            <button class="btn btn-info btn-sm" @click.prevent="add = true">
                <i class="fa fa-plus"></i> Ajouter une catégorie
            </button>
        </div>
    </div>
</div>


@push('js')
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('category', () => ({
            route: @json(back_route('extensions.shop.category.store')),
            add: false,
            form: {
                name: '',
                description: ''
            },
            cancel(){
                this.form.name        = ''
                this.form.description = ''

                this.add = false
            },
            validate(){
                return this.form.name.length > 0
            },
            save(){
                if (!this.validate()){
                    return
                }

                axios.post(this.route, this.form )
                    .then(({data}) => {

                        const newOption = new Option(data.name, data.id, true, true);
                        jQuery(this.$refs.categories).append(newOption).trigger('change');

                        this.cancel()
                    })
            }
        }))
    })
</script>
@endpush
