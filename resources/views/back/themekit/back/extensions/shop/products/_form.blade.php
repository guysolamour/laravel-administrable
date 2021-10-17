<div class="card">
    <div class="card-header d-flex justify-content-between">
         
        @if(isset($edit) && $edit)
        <div class="btn-group float-right">
            <a href="{{ back_route('extensions.shop.product.destroy', $product) }}" class="btn btn-danger" data-method="delete"
            data-confirm="Etes vous sûr de bien vouloir procéder à la suppression ?">
            <i class="fas fa-trash"></i> Supprimer</a>
        </div>
        @endif
    </div>

    <div class="card-body">
        @if($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif
        <form @submit="saveProduct" novalidate name="{{ get_form_name($product) }}" id="product-form" class="row" x-ref="product_form" x-data="Product"
        method="POST"
        @if(isset($edit) && $edit)
        action="{{ back_route('extensions.shop.product.update', $product) }}"
        @else
        action="{{ back_route('extensions.shop.product.store') }}"
        @endif
        >
        @csrf

        @if(isset($edit) && $edit)
        @method('PUT')
        @endif
        <div class="col-md-8">
            <div class="form-group">
                <label for="product_name">Nom</label>
                <input x-model="form.name" type="text" id="product_name" class="form-control"
                :class="{ 'is-invalid': form.name.length == 0 }" required name="name">
                <div class="invalid-feedback" :show="form.name.length == 0">
                    Le nom du produit ne peut pas être vide.
                </div>
            </div>

            <div class="form-group">
                <label for="product_description">Description</label>
                <textarea type="text" id="product_description" rows="2" name="description" data-tinymce
                class="form-control" :value="product.description"></textarea>
            </div>

            <div class="form-group">
                <label for="short_description">Description courte</label>
                <textarea class="form-control" rows="4"  name="short_description"
                id="product_short_description" :value="product.short_description"></textarea>
            </div>
            <hr>
            <div class="card">
                <div class="card-header" style="pading: 10px 10px">
                    <h6>Données sur le produit</h6>
                </div>
                <div class="card-body">
                    <div class="row" id="product-tabs">
                        <div class="col-12 col-md-3" style="padding: 0; background-color: grey;">
                            <nav class="nav flex-column nav-pills" id="v-pills-tab" role="tablist" role="navigation"
                            aria-orientation="vertical">
                            <a href="#" class="nav-link" :class="{'active': tab === 'tab-general'}"
                            @click.prevent="tab = 'tab-general'"><i class="fa fa-tools"></i> Général</a>
                            <a href="#" class="nav-link" :class="{'active': tab === 'tab-type'}"
                            @click.prevent="tab = 'tab-type'"><i class="fa fa-certificate"></i> Type</a>

                            <a href="#" class="nav-link" x-show="shop_settings.stock_management" :class="{'active': tab === 'tab-inventory'}"
                            @click.prevent="tab = 'tab-inventory'"> <i class="fas fa-chart-line"></i> Inventaire</a>
                            <a href="#" class="nav-link" :class="{'active': tab === 'tab-expedition'}"
                            @click.prevent="tab = 'tab-expedition'"><i class="fa fa-truck-container"></i>
                            Expédition</a>
                            <a href="#" class="nav-link" :class="{'active': tab === 'tab-links-products'}"
                            @click.prevent="tab = 'tab-links-products'"><i class="fa fa-compress-arrows-alt"></i> Produits liés</a>
                            <a href="#" class="nav-link" :class="{'active': tab === 'tab-attributes'}"
                            @click.prevent="tab = 'tab-attributes'"><i class="fa fa-folder-plus"></i> Attributs</a>
                            <a href="#" x-show="form.variable" class="nav-link"
                            :class="{'active': tab === 'tab-variations'}"
                            @click.prevent="tab = 'tab-variations'"><i class="fa fa-window-restore"></i> Variations</a>
                            <a href="#" class="nav-link" :class="{'active': tab === 'tab-advanced'}"
                            @click.prevent="tab = 'tab-advanced'"> <i class="fa fa-clone"></i> Avancé</a>
                        </nav>
                    </div>
                    <div class="col-12 col-md-9">
                        <div class="tab-content" id="v-pills-tabContent">
                            <div class="tab-pane fade" :class="{'active show': tab === 'tab-general'}"
                            x-show="tab === 'tab-general'" id="v-pills-home" role="tabpanel"
                            aria-labelledby="v-pills-home-tab">
                            <div class="form-group">
                                <label for="product_price">Prix</label>
                                <input type="number" id="product_price" class="form-control"
                                :class="{ 'is-invalid': form.price.length == 0 }" name="price"
                                x-model.number='form.price'>
                                <div class="invalid-feedback" :show="form.price.length == 0">
                                    Le prix du produit est obligatoire.
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="custom-control custom-checkbox">
                                    <input x-model="form.is_in_promotion" type="checkbox"
                                    name="is_in_promotion" id="is_in_promotion"
                                    class="custom-control-input">
                                    <label class="custom-control-label" for="is_in_promotion">Mettre cet
                                        article en
                                        promotion</label>
                                    </div>
                                </div>

                                <div x-show.transition.duration.300ms="form.is_in_promotion">
                                    <div class="form-group">
                                        <label for="product_promotion_price">Prix de
                                            promotion</label>
                                            <input type="number" class="form-control"
                                            :class="{ 'is-invalid': form.is_in_promotion && form.promotion_price >= form.price }"
                                            id="product_promotion_price" name='promotion_price'
                                            x-model.number="form.promotion_price">
                                            <div class="invalid-feedback"
                                            :show="form.is_in_promotion && form.promotion_price >= form.price">
                                            Le prix de promotion ne peut pas être supérieur au
                                            prix du produit.
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="promotion_start_end_date">Date de début et
                                            de fin de la promotion</label>
                                            <input type="string" name="promotion_start_end_date"
                                            id="promotion_start_end_date"
                                            {{--  class="form-control" value="{{ $product->promotion_start_at->format('d/m/Y h:i:s') }}
                                            - {{ $product->promotion_end_at->format('d/m/Y h:i:s') }}">
                                            --}}
                                            class="form-control">
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane fade" :class="{'active show': tab === 'tab-type'}"
                                x-show="tab === 'tab-type'" id="v-pills-home" role="tabpanel"
                                aria-labelledby="v-pills-home-tab">
                                <div class="form-group">
                                    <label for="product_type">Type de produit</label>
                                    <template x-if="edit_mode">
                                        <div>
                                            <input type="text" class="form-control"  id="product_type"
                                            :value="getProductTypeLabel(product.type)" readonly>

                                            <input type="hidden"  name="type" :value="product.type" >
                                        </div>
                                    </template>

                                    <template x-if="!edit_mode">
                                        <select name="type" id="product_type" class="form-control">
                                            <template x-for="type in types">
                                                <option :value="type.name" x-text="type.label"></option>
                                            </template>
                                        </select>
                                    </template>
                                </div>
                                <div class="form-group">
                                    <div class="custom-control custom-checkbox">
                                        <input x-model="form.variable" name="variable" type="checkbox"
                                        id="product_variable" class="custom-control-input">
                                        <label class="custom-control-label" for="product_variable">Produit
                                            variable</label>
                                        </div>
                                    </div>
                                    <template x-if="product.type === 'virtual'">
                                        <div class="form-group">
                                            <div class="custom-control custom-checkbox">
                                                <input x-model="form.download" type="checkbox" name="download"
                                                id="product_download" class="custom-control-input">
                                                <label class="custom-control-label" for="product_download">Produit
                                                    téléchargeable</label>
                                                </div>
                                            </div>
                                        </template>

                                    </div>

                                    <div class="tab-pane fade" x-show="shop_settings.stock_management" :class="{'active show': tab === 'tab-inventory'}"
                                    x-show="tab === 'tab-inventory'" id="v-pills-messages" role="tabpanel"
                                    aria-labelledby="v-pills-messages-tab">
                                    <div>
                                        <div class="form-group">
                                            <label for="product_stock_management">Gestion de
                                                stock</label>
                                                <select x-model="form.stock_management" name="stock_management"
                                                id="product_stock_management" class="form-control">
                                                <option value="1">oui</option>
                                                <option value="0">non</option>
                                            </select>
                                        </div>
                                        <div x-show="form.stock_management == 1 == 1" x-transition.duration.300ms
                                        style="border: .5px solid grey; padding: 5px;">
                                        <div class="form-group">
                                            <label for="product_stock">Quantité en stock</label>
                                            <input type="text" name="stock" id="product_stock" class="form-control"
                                            x-bind:value="product.stock">
                                        </div>
                                        <div class="form-group">
                                            <label for="product_safety_stock">Stock de
                                                sécurité</label>
                                                <input type="text" name="safety_stock" id="product_safety_stock" class="form-control"
                                                x-bind:value="product.safety_stock">
                                            </div>
                                        </div>
                                    </div>


                                </div>
                                <div class="tab-pane fade" :class="{'active show': tab === 'tab-expedition'}"
                                x-show="tab === 'tab-expedition'" id="v-pills-settings" role="tabpanel"
                                aria-labelledby="v-pills-settings-tab">

                                <div class="row">
                                    <div class="col-md-4">

                                        <div class="form-group">
                                            <label for="product_weight">Poids (kg):</label>
                                            <input type="text" name="weight" class="form-control"
                                            :value="product.weight">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="product_height">Longeur (cm):</label>
                                            <input type="text" name="height" class="form-control"
                                            :value="product.height">
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="product_width">Largeur (cm):</label>
                                            <input type="text" name="width" class="form-control"
                                            :value="product.width">
                                        </div>
                                    </div>
                                </div>
                                <div class="accordion" id="accordionCoverageAreas">
                                    <template
                                    x-for="(new_deliver_coverage, index) in new_deliver_coverage_areas">
                                    <div class="card">
                                        <div class="card-header"
                                        :id="'heading'+ new_deliver_coverage.area.slug + index">
                                        <h5 class="mb-0">
                                            <button class="btn btn-link" type="button"
                                            data-toggle="collapse"
                                            :data-target="'#' + new_deliver_coverage.area.slug + index"
                                            aria-expanded="true" aria-controls="collapseOne">
                                            Zone: <span
                                            x-text="new_deliver_coverage.area.name"></span>

                                        </button>
                                    </h5>
                                </div>

                                <div :id="new_deliver_coverage.area.slug + index" class="collapse"
                                :aria-labelledby="'heading'+ new_deliver_coverage.area.name + index"
                                data-parent="#accordionCoverageAreas">
                                <div class="card-body">
                                    <button class="btn btn-danger float-right ml-2"
                                    @click.prevent="removeCoverageArea(new_deliver_coverage.area.id)"
                                    title="retirer la zobe de livraison">x</button>
                                    <ul class="list-group">
                                        <template
                                        x-for="cov_deliver in new_deliver_coverage.delivers">
                                        <li class="list-group-item">
                                            <span x-text="cov_deliver.name"></span>
                                            <span class="font-weight-bold float-right"
                                            x-text="cov_deliver.areas.filter(area => area.id == new_deliver_coverage.area.id)[0].pivot.formated_price"></span>
                                        </li>
                                    </template>
                                </ul>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
            <p class="form-group">
                <button @click.prevent="form.show_deliver_coverage_areas = true"
                class="btn btn-info btn-xs float-right">
                <i class="fa fa-plus"></i> Ajouter des zones de livraison
            </button>
        </p>
        <br>

        <div x-show="form.show_deliver_coverage_areas">
            <div class="form-group mt-4" x-show="!form.add_coverage.show_form">
                <label for="product_coverage_areas">Zones de
                    livraison:</label>
                    <select @change="chooseCoverageArea" x-model="form.coverage.areas"
                    id="product_coverage_areas" class="custom-select">
                    <option value="0">Choisissez une zone de livraison</option>
                    <template x-for="coverage_area in coverage_areas">
                        <option :value="coverage_area.id" x-text="coverage_area.name">
                        </option>
                    </template>
                </select>
            </div>
            <div class="form-group" x-show="form.show_delivers_list">
                <label for="product_delivers">Livreurs:</label>
                <select id="product_delivers" class="custom-select"
                x-model="form.coverage.delivers" multiple>
                <template x-for="deliver in form.delivers">
                    <option :value="deliver.id" x-text="deliver.name">
                    </option>
                </template>
            </select>
        </div>
        <div x-show="form.add_coverage.show_form">
            <hr>
            <div class="form-group">
                <label for="coverage_area_name">Nom de la zone de livraison</label>
                <input x-model="form.add_coverage.name" type="text"
                class="form-control"
                :class="{ 'is-invalid': form.add_coverage.error.show}"
                id="coverage_area_name">
                <small x-show="form.add_coverage.error.message.length == 0"
                class="form-text text-muted">Le nom de la zone est
                obligatoire.</small>
                <div class="invalid-feedback"
                x-text="form.add_coverage.error.message"></div>
            </div>
            <div class="form-group">
                <label for="coverage_area_description">Description de la zone de
                    livraison</label>
                    <input x-model="form.add_coverage.description" type="text"
                    class="form-control" id="coverage_area_description">
                </div>
                <div class="form-group">
                    <button @click.prevent="saveCoverageArea"
                    class="btn btn-success btn-xs float-right"><i
                    class="fa fa-plus"></i> Ajouter</button>
                </div>
            </div>
            <div x-show="!form.add_coverage.show_form">
                <button @click.prevent="form.add_coverage.show_form = true"
                class="btn btn-secondary btn-xs float-left">
                <i class="fa fa-plus"></i> Ajoutez une zone</button>

                <button :disabled="form.coverage.delivers == null"
                @click.prevent="addDeliverCoverageArea"
                class="btn btn-info btn-xs float-right">
                <i class="fa fa-save"></i> Enregistrez</button>
            </div>
        </div>


    </div>
    <div class="tab-pane fade" :class="{'active show': tab === 'tab-links-products'}"
    x-show="tab === 'tab-links-products'" id="v-pills-links-products"
    role="tabpanel">
    <div class="form-group">
        <label for="">Produits suggéres:</label>
        <select name="complementary_products[]" class="select2" multiple>
            <template x-for="prod in products" :key="prod.id">
                <option :value="prod.id"
                :selected="product.complementary_products.includes(prod.id)"
                x-text="prod.name"></option>
            </template>
        </select>
        {{--  <input type="text" class="form-control select2">  --}}
        <small>Les produits suggérés sont des produits que vous
            recommandez à la place de ceux
            actuellement vus, par exemple, les produits qui sont
            plus rentables, de meilleure qualité ou plus
            chers.</small>
        </div>

    </div>
    <div class="tab-pane fade" :class="{'active show': tab === 'tab-attributes'}"
    x-show="tab === 'tab-attributes'" id="v-pills-advance" role="tabpanel">
    <div class="form-group">
        <select x-model="current_attribute" class="form-control form-control-sm">
            <option value="0">Attribut personnalisé du produit
            </option>
            <template x-for="attribute in attributes">
                <option :value="attribute.id" x-text="attribute.name"></option>
            </template>
        </select>
        <button x-text="current_attribute == 0 ? 'Ajoutez' : 'Modifiez'"
        :disabled="show_attribute_form" class="btn btn-outline-secondary mt-2"
        @click.prevent="addAttribute"></button>
    </div>
    <div>
        <template x-if="new_attributes.length == 0">
            <p>Pas d'attributs ajoutés pour le moment</p>
        </template>
        <ul class="list-group">
            <template x-for="(newattribute, index) in new_attributes" :key="index">
                <li class="list-group-item">
                    <span @dblclick.stop="editAttribute(newattribute)"
                    x-text="newattribute.terms_list == null ? newattribute.name + ' | ' + newattribute.value : newattribute.name  + ' | ' + newattribute.terms_list"></span>
                    <button title="supprimer"
                    @click.prevent="removeAttribute(newattribute)"
                    class="float-right btn btn-danger btn-xs">x</button>
                </li>
            </template>
        </ul>
    </div>
    <div class="mt-4" x-show="show_attribute_form">
        <div class="form-group">
            <input x-model='add_attribute_form.name' type="text"
            class="form-control" placeholder="Entrez le nom de l'attribut"
            :disabled="add_attribute_form.id || is_editing_attribute ">
        </div>
        <div class="form-group">
            <textarea class="form-control" cols="2"
            x-model="add_attribute_form.value"
            placeholder="Entez les valeurs séparées par une virgule"></textarea>
        </div>
        <div class="btn-group float-right">
            <button class="btn btn-info btn-xs"
            @click.prevent="saveAttribute">Enregistrer</button>
            <button class="btn btn-secondary btn-xs"
            @click.prevent="cancelAttributeAdding">Annuler</button>

        </div>
    </div>
</div>
<div class="tab-pane fade" :class="{'active show': tab === 'tab-variations'}"
x-show="tab === 'tab-variations'" id="v-pills-variations" role="tabpanel">

<div class="form-group">
    <select x-model="current_variations" multiple class="custom-select">
        <option value="">Sélectionner l'attribut de variation
        </option>
        <template x-for="attribute in new_attributes">
            <option :value="attribute.name" x-text="attribute.name"></option>
        </template>
    </select>
    <button type="button" class="btn btn-outline-secondary"
    @click.prevent="addVariation">Utiliser comme variations
</button>
</div>
<div class="accordion" id="variation_accordion">
    <template x-for="(variation, index) in new_variations" :key="index">
        <div class="card">
            <div class="card-header" id="headingOne">
                <h2 class="mb-0">
                    <button class="btn btn-link btn-block text-left"
                    type="button" data-toggle="collapse"
                    :data-target=" variation.id == null ? '#'+ variation.attribute + index : '#'+ variation.slug"
                    aria-expanded="true" aria-controls="collapseOne">
                    Variation:
                    <template x-if="variation.id == null">
                        <span>
                            <span x-text="variation.attribute"></span>
                            - <span x-text="variation.value"></span>
                        </span>
                    </template>
                    <template x-if="variation.id != null">
                        <span>
                            <span x-text="variation.attribute.name"></span>
                        </span>
                    </template>
                </button>
            </h2>
        </div>
    </p>
    <div :id="variation.id == null ? variation.attribute + index : variation.slug"
    class="collapse" aria-labelledby="headingOne"
    data-parent="#variation_accordion">
    <div class="card-body">
        <form action="" :name="'form-variation' + index">
            <div class="form-group text-danger float-right">
                <button class="btn btn-danger"
                @click.prevent="removeVariation(variation)"
                title="retirer la variation">x</button>
            </div>
            <div class="form-group">
                <label for="">Nom</label>
                <input x-model="variation.name" type="text"
                class="form-control">
            </div>
            <div class="form-group">
                <label for="">Prix</label>
                <input x-model="variation.price" type="number"
                class="form-control">
            </div>
            <div class="form-group" x-show="form.is_in_promotion">
                <label for="">Prix
                    promotionnel</label>
                    <input x-model="variation.promotion_price"
                    type="number" class="form-control">
                </div>
                <div x-show="form.stock_management == 1">
                    <div class="form-group">
                        <label for="">Quantité en
                            stock</label>
                            <input type="number" class="form-control"
                            x-model.number="variation.stock">
                        </div>
                        <div class="form-group">
                            <label for="">Stock de
                                sécurité</label>
                                <input type="number" class="form-control"
                                x-model.number="variation.safety_stock">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="">Description</label>
                            <textarea x-model="variation.description" name=""
                            id="" rows="2" class="form-control"></textarea>
                        </div>

                        <div class="row">
                            <div class="card-group">
                                @php
                                $frontimage = config('administrable.extensions.shop.media_collections', [])['front-image'];
                                @endphp
                                @if(is_string($frontimage) && !empty($frontimage))
                                <div class="card">
                                    <img x-show="variation.gallery.front.url.length != 0" x-transition
                                    class="card-img-top"
                                    :src="variation.gallery.front.url"
                                    :alt="variation.name" title="Image du produit">

                                    <div class="card-footer text-center"
                                    style="padding: 5px 5px">
                                    <input
                                    @change="addFile($event, variation, 'front')"
                                    class="d-none" type="file"
                                    :name="variation.gallery.front.collection.label + index"
                                    accept="image/*">
                                    <button
                                    @click.prevent='addVariationImage(variation.gallery.front.collection.label + index)'
                                    type="button" title="Image du produit"
                                    class="btn btn-info btn-sm">
                                    <i class="fa fa-image"></i>
                                    <span
                                    x-show="variation.gallery.front.url.length == 0">Ajoutez</span>
                                    <span
                                    x-show="variation.gallery.front.url.length != 0">Modifiez</span>
                                </button>
                                <button
                                x-show="variation.gallery.front.url.length != 0"
                                class="btn btn-danger btn-sm"
                                @click.prevent="removeImage(variation, 'front')">X</button>
                            </div>
                        </div>
                        @endif

                        @php
                        $backimage = config('administrable.extensions.shop.media_collections', [])['back-image'];
                        @endphp
                        @if(is_string($backimage) && !empty($backimage))
                        <div class="card">
                            <img x-show="variation.gallery.back.url.length != 0" x-transition
                            class="card-img-top" title="Seconde image du produit"
                            :src="variation.gallery.back.url"
                            :alt="variation.name">
                            <div class="card-footer text-center"
                            style="padding: 5px 5px">
                            <input
                            @change="addFile($event, variation, 'back')"
                            class="d-none" type="file" title="Seconde image du produit"
                            :name="variation.gallery.back.collection.label + index"
                            accept="image/*">
                            <button
                            @click.prevent='addVariationImage(variation.gallery.back.collection.label + index)'
                            type="button"
                            class="btn btn-info btn-sm">
                            <i class="fa fa-image"></i>
                            <span
                            x-show="variation.gallery.back.url.length == 0">Ajoutez</span>
                            <span
                            x-show="variation.gallery.back.url.length != 0">Remplacez</span>
                        </button>
                        <button
                        x-show="variation.gallery.back.url.length != 0"
                        class="btn btn-danger btn-sm" title="Seconde image à la une"
                        @click.prevent="removeImage(variation, 'back')">X</button>
                    </div>
                </div>
                @endif
            </div>
        </div>
        @php
        $images = config('administrable.extensions.shop.media_collections', [])['images'];
        @endphp
        @if(is_string($images) && !empty($images))
        <div class="mt-2">
            <template
            x-for="chunk in array_chunk(variation.gallery.images.urls, 3)">
            <div class="row mt-2" title="Gallerie produit">
                <div class="card-group ">
                    <template
                    x-for="(image, index) in chunk">
                    <div
                    class="card variation-imagebox">
                    <i @click.prevent="removeImage(variation, 'images', image)"
                    class="close-icon fa fa-times"
                    aria-hidden="true"
                    title="Supprimer l'image"></i>
                    <img x-show="image.url.length != 0" x-transition
                    class="card-img-top"
                    :src="image.url"
                    :alt="variation.name">
                </div>
            </template>
        </div>
    </div>
</template>
</div>
<div class="mt-2">
    <input
    @change="addFile($event, variation, 'images')"
    class="d-none" type="file"
    :name="variation.gallery.images.collection.label + index"
    accept="image/*" multiple>
    <button
    @click.prevent='addVariationImage(variation.gallery.images.collection.label + index)'
    type="button"
    class="btn btn-info btn-sm btn-block">
    <i class="fa fa-image"></i>
    <span>Ajoutez</span>
</button>
</div>
@endif
</form>
</div>
</div>
</div>
</template>

</div>
</div>
<div class="tab-pane fade" :class="{'active show': tab === 'tab-advanced'}"
x-show="tab === 'tab-advanced'" id="v-pills-advance" role="tabpanel">
<div class="form-group">
    <div class="custom-control custom-checkbox">
        <input x-model="form.has_review" type="checkbox" name="has_review"
        id="product_has_review" class="custom-control-input">
        <label class="custom-control-label" for="product_has_review">Activer les
            avis:</label>
        </div>
    </div>
    <div class="form-group">
        <label for="product_command_note">Note de commande</label>
        <textarea x-model="form.command_note" name="command_note"
        id="product_command_note" cols="30" rows="5"
        class="form-control"></textarea>
        <small class="form-text text-muted">
            Ajouter une note à envoyer au client lors de l'achat
        </small>
    </div>
</div>
</div>
</div>
</div>
</div>
</div>

<hr>
<x-administrable::seoform :model='$product' />
<button type="submit" class="btn btn-secondary" :disabled="validForm">
    @if(isset($edit) && $edit)
    <i class="fa fa-edit"></i> Mettre à jour
    @else
    <i class="fa fa-save"></i> Enregistrer
    @endif
</button>
</div>
<div class="col-md-4">
    <div class="card">
        <div class="card-body">
            <div class="custom-control custom-switch">
                <input name="online" value="0" type="hidden" >
                <input name="online" type="checkbox" @if($product->online) checked @endif class="custom-control-input" id="onlineSwitch">
                <label class="custom-control-label" for="onlineSwitch">Afficher le produit dans la boutique pour les clients ?</label>
            </div>
            <div class="mt-4 d-flex justify-content-center">
                <button type="submit" :disabled="validForm" form="product-form" class="btn btn-info btn-block btn-lg">
                    @if(isset($edit) && $edit)
                    <i class="fa fa-edit"></i> Mettre à jour
                    @else
                    <i class="fa fa-save"></i> Enregistrer
                    @endif
                </button>
            </div>
        </div>
    </div>


    @include(back_view_path('extensions.shop.products._category'), [
        'categories'  => $categories,
        'product' => $product,
    ])

    @include(back_view_path('extensions.shop.products._brand'), [
        'brands'  => $brands,
        'product' => $product,
    ])

    @if(config('administrable.extensions.shop.custom_fields.product', []))
    <div class="card">
        <div class="card-header bg-secondary">
            <h3 class="card-title text-white">
                Champs personnalisés
            </h3>
        </div>
        <div class="card-body">
            <div class="front-image-box image-container">
                @foreach (config('administrable.extensions.shop.custom_fields.product', []) as $field)
                @if(Arr::get($field, 'type') === 'boolean')
                <div class="form-group">
                    <label for="{{  Arr::get($field, 'name') }}">{{  Arr::get($field, 'label') }}</label>
                    <select name="custom_fields[{{  Arr::get($field, 'name') }}]" id="{{  Arr::get($field, 'name') }}" class="custom-select">
                        <option value="0" @if($product->getCustomField(Arr::get($field, 'name')) == 0) selected @endif>Non</option>
                        <option value="1" @if($product->getCustomField(Arr::get($field, 'name')) == 1) selected @endif>Oui</option>
                    </select>
                </div>
                @else
                <div class="form-group">
                    <label for="{{  Arr::get($field, 'name') }}">{{  Arr::get($field, 'label') }}</label>
                    <input type="{{ Arr::get($field, 'type') }}" name="custom_fields[{{  Arr::get($field, 'name') }}]" class="form-control" value="{{ $product->getCustomField(Arr::get($field, 'name')) }}">
                </div>
                @endif
                @endforeach
            </div>
        </div>
    </div>
    @endif

    @foreach (config('administrable.extensions.shop.media_collections', []) as $collection => $label)
        @if(is_string($label) && !empty($label))
            @imagemanager([
                'collection' => $collection,
                'label'      => $label,
                'model'      => $product,
            ])
        @endif
    @endforeach
</div>
</form>

</div>
</div>

<x-administrable::tinymce :model='$product' />
<x-administrable::select2  />
<x-administrable::daterangepicker
fieldname='promotion_start_end_date'
:startdate="$product->promotion_start_at"
:enddate="$product->promotion_end_at"

/>

@push('css')
<style>
    .select2-container {
        width: 100% !important;
    }

    #product-tabs nav a {
        color: white !important;
    }

    .variation-imagebox {
        position: relative;
    }

    #product_coverage_areas option[disabled] {
        display: none;
    }

    .variation-imagebox .close-icon {
        position: absolute;
        top: 5%;
        right: 10%;
        font-size: 1.5rem;
        cursor: pointer;
        color: red;
        display: none;
    }

    .variation-imagebox:hover {
        border: 5px solid red;
    }



    .variation-imagebox:hover .close-icon {
        display: block;
    }
</style>
@endpush

@push('js')

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('Product', () => ({
            show_attribute_form: false,
            show_variations_form: false,
            tab: 'tab-general',
            add_attribute_form: { id: null, name: null, value: null },
            // append
            new_attributes: [],
            new_variations: [],
            new_deliver_coverage_areas: [],
            shop_settings: @json(shop_settings()),

            variations: [],
            current_attribute : 0,
            current_variations : [],

            // deleted Ids
            deleted_variation_images_id : [],
            deleted_attributes_id : [],
            deleted_variations_id : [],
            is_editing_attribute: false,
            // is_in_promotion: false,
            edit_mode: @json(isset($edit)),
            attributes: @json($product_attributes),
            product: @json($product),
            products: @json($products),
            categories: @json($categories),
            coverage_areas: @json($coverage_areas),
            delivers: @json($delivers),
            brands: @json($brands),
            types: @json($types),
            form: {
                is_in_promotion: null,
                download: null,
                variable: null,
                stock_management: null,
                categories: null,
                brand: null,
                name: null,
                price: null,
                promotion_price: null,
                has_review: null,
                command_note: null,
                stock: null,
                safety_stock: null,
                coverage_areas: null,
                delivers: [],
                show_deliver_coverage_areas: false,
                show_delivers_list: false,
                coverage: {
                    areas: null,
                    delivers: null
                },
                add_coverage: { name: '', description: '', show_form: false, error:  { message: '', show: false} }
            },


            init(){
                this.form.is_in_promotion  = this.product.is_in_promotion
                this.form.download         = this.product.download
                this.form.name             = this.product.name
                this.form.price            = this.product.price
                this.form.promotion_price  = this.product.promotion_price
                this.form.variable         = this.product.parent_id != null
                this.form.stock_management = this.product.stock_management ? 1 : 0
                this.form.has_review       = this.product.has_review
                this.form.stock            = this.product.stock
                this.form.safety_stock     = this.product.safety_stock
                this.form.command_note     = this.product.command_note
                this.form.variable         = this.product.variable
                this.new_variations        = this.product.children
                this.new_attributes        = this.edit_mode ? this.product.attributes : this.attributes

                this.new_deliver_coverage_areas = this.product.delivers_coverage_areas
            },

            // methods
            addVariationImage(input){
                document.querySelector(`input[name=${input}]`).click()
            },
            addFile(event, variation, collection){
                const images = event.target.files

                for (let i = 0; i < images.length; i++){
                    const reader = new FileReader()

                    const image = images[i]

                    reader.readAsDataURL(image)

                    reader.onload = (e) => {
                        const media = variation.gallery[collection]

                        if (collection === 'images'){
                            media.urls.push({
                                id: null,
                                name: image.name,
                                url: e.target.result,
                            })
                        }else {
                            media.id = null
                            media.name = image.name
                            media.url = e.target.result
                        }

                    }
                }


            },
            validForm(){
                return this.form.name.length == 0 ||
                (this.form.is_in_promotion && this.form.promotion_price >= this.form.price)
            },
            addVariation(){
                this.current_variations.forEach(variation => {
                    const attribute = this.new_attributes.filter(attr => attr.name == variation)[0]
                    attribute.value.split(',').forEach(value => {

                        if (!confirm("Voulez-vous enregistrer une variation pour " + value)){
                            return
                        }

                        this.new_variations = [
                        {
                            name: this.form.name + ' ' + value,
                            description: '',
                            // price: this.form.price,
                            promotion_price: this.form.promotion_price,
                            stock: this.form.stock,
                            safety_stock: this.form.safety_stock,
                            gallery: {
                                front:   {id: null, name: '', url: '', collection: {label: 'front-image'}},
                                back:    {id: null, name: '', url: '', collection: {label: 'back-image'}},
                                images:  {urls: [], collection: {label: 'images'}},
                            },
                            value,
                            attribute: attribute.name,
                            term: value,
                        }, ...this.new_variations
                        ]
                    })
                })

                this.show_variations_form = true
            },
            chooseCoverageArea(){
                this.form.delivers = this.delivers.filter(deliver => {
                    return deliver.areas.some(area => area.id == this.form.coverage.areas)
                })

                if (this.form.delivers.length == 0){
                    this.form.delivers.push({ id: 0, name: 'Pas de livreurs pour cette zone' })
                    this.form.show_delivers_list = false
                }

                this.form.show_delivers_list = true
            },
            addDeliverCoverageArea(){
                if (this.form.coverage.delivers == null || this.form.coverage.areas == null ){
                    return;
                }

                const delivers = []

                this.delivers.forEach(item => {
                    if (this.form.coverage.delivers.map(item => parseInt(item, 10)).includes(item.id)){
                        delivers.push(item)
                    }
                })

                this.new_deliver_coverage_areas.push({
                    area: this.clone(this.coverage_areas.filter(area => area.id == this.form.coverage.areas)[0]),
                    delivers: this.clone(delivers),
                })

                this.form.coverage.delivers = null
                this.form.coverage.areas = null
                this.form.delivers = []

                this.form.show_delivers_list = false
                this.form.show_deliver_coverage_areas = false
            },
            saveCoverageArea(){
                // valide
                if (this.form.add_coverage.name.length == 0){
                    this.form.add_coverage.error.message = 'La zone de livraision est obligatoire'
                    this.form.add_coverage.error.show = true
                    return
                }

                axios.post('/administrable/extensions/shop/coverageareas', {name: this.form.add_coverage.name, description: this.form.add_coverage.description })
                .then(({data}) => {
                    this.coverage_areas.push(data)
                    // renitialiser le formulaire
                    this.form.add_coverage.name = ''
                    this.form.add_coverage.description = ''

                    // hide form
                    this.form.add_coverage.show_form = false
                })

            },
            addAttribute(){
                if (this.current_attribute){

                    //verifier si l'option n'a pas été defini
                    if(this.new_attributes.some(item => item.id == this.current_attribute)){
                        // alert("ce attribut a deja ete ajoute. DOuble cliquez sur l'attribut pour le modifier");
                        // return
                        this.is_editing_attribute = true
                    }

                    // on recupere l'attribut pour populer le formulaire d'ajout
                    const attribute = this.attributes.filter(item => item.id == this.current_attribute)[0]

                    // on rempli le champ
                    if (attribute){
                        this.add_attribute_form.id = attribute.id
                        this.add_attribute_form.name = attribute.name
                        this.add_attribute_form.value = attribute.terms_list
                    }

                }
                this.show_attribute_form = true
            },
            cancelAttributeAdding(){
                this.add_attribute_form.id = null
                this.add_attribute_form.name = null
                this.add_attribute_form.value = null

                this.is_editing_attribute = false

                this.show_attribute_form = false
            },
            saveAttribute(){
                if (this.is_editing_attribute){
                    // on modifie le champ
                    this.new_attributes = this.new_attributes.map((item) => {
                        if (item.name == this.add_attribute_form.name){
                            return {
                                id : this.add_attribute_form.id,
                                name : this.add_attribute_form.name,
                                value : this.add_attribute_form.value,
                            }
                        }
                        return item
                    })



                }else {
                    if(this.new_attributes.some(item => item.name == this.add_attribute_form.name)){
                        alert(`L'attribut ${this.add_attribute_form.name} est deja dans la liste`);
                        return
                    }
                    // ajoit de l'attribut si pas d'erreur
                    this.new_attributes = [...this.new_attributes, this.clone(this.add_attribute_form)]

                    this.current_attribute = null
                }


                this.cancelAttributeAdding()
            },
            saveProduct(event){
                this.appendDataToRequest([
                { name: 'new_attributes',              value: this.new_attributes },
                { name: 'new_variations',              value: this.new_variations },
                { name: 'deleted_variation_images_id', value: this.deleted_variation_images_id },
                { name: 'deleted_attributes_id',       value: this.deleted_attributes_id },
                { name: 'deleted_variations_id',       value: this.deleted_variations_id },
                { name: 'new_deliver_coverage_areas',  value: this.new_deliver_coverage_areas },
                ])
            },
            removeAttribute(attribute){
                this.deleted_attributes_id.push(attribute.id)
                this.new_attributes = this.new_attributes.filter(item => item.name != attribute.name)

                // trouver les variations liés a ceta tattribut et les supprimer
                this.new_variations.filter(item => item.attribute == attribute.name).forEach(item => this.removeVariation(item))
            },
            removeVariation(variation){
                if (!confirm('Etes vous sur de supprimer la variation')){
                    return
                }

                this.deleted_variations_id.push(variation.id)
                this.new_variations = this.new_variations.filter(item => item.name != variation.name)
            },
            removeCoverageArea(coverage_id){
                if (confirm('Voulez-vous supprimer cette zone de livraison et les livreurs qu\'elle contient')){
                    this.new_deliver_coverage_areas = this.new_deliver_coverage_areas.filter(item => item.area.id != coverage_id)
                }
            },
            removeImage(variation, collection, image = null){
                if (!confirm("Voulez-vous supprimer cette image")){
                    return;
                }

                image = image || variation.gallery[collection]

                this.deleted_variation_images_id.push(image.id)

                if (collection === 'images'){
                    variation.gallery[collection]['urls'] = variation.gallery[collection]['urls'].filter(item => item.name != image.name)
                }
                else {
                    image.id   = null
                    image.name = ''
                    image.url  = ''
                }
            },
            editAttribute(attribute){
                this.is_editing_attribute     = true
                this.add_attribute_form.id    = attribute.id
                this.add_attribute_form.name  = attribute.name
                this.add_attribute_form.value = attribute.value
                this.show_attribute_form      = true
            },
            getProductTypeLabel(value){
                // alert(value)
                // console.log(this.types);
                const type = this.types.filter(item => item.name == value)[0]

                return type.label
            },
            appendDataToRequest(data){
                data.forEach(item => {
                    const input = document.createElement('input')
                    input.type = 'hidden'
                    input.name = item.name
                    input.value = JSON.stringify(item.value)
                    this.$refs.product_form.appendChild(input)
                })
            },
            clone(data){
                return JSON.parse(JSON.stringify(data))
            },
            array_chunk (arr, size) {
                return Array.from({ length: Math.ceil(arr.length / size) }, (v, i) =>
                arr.slice(i * size, i * size + size)
                );
            }
        }))
    })

</script>
@endpush
