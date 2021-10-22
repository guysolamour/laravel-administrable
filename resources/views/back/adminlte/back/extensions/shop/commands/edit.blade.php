@extends(back_view_path('layouts.base'))

@section('title', 'Commande ' . $command->reference)

@section('content')

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    {{-- <h1>Edition</h1> --}}
                </div>
                <div class="col-sm-6">
                    <div class="float-sm-right">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route(config('administrable.guard') . '.dashboard') }}">Tableau de bord</a></li>
                            <li class="breadcrumb-item"><a href="{{ back_route('extensions.shop.statistic.index') }}">Boutique</a></li>
                            <li class="breadcrumb-item"><a href="{{ back_route('extensions.shop.command.index') }}">Commandes</a></li>
                            <li class="breadcrumb-item"><a href="{{ back_route('extensions.blog.category.show', $category) }}">{{ $command->reference }}</a></li>
                            <li class="breadcrumb-item active">Edition</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content">

        <!-- Default box -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Edition de commande</h3>
                
                <div class="btn-group float-right">
                    @if($command->isNotPaid() || $command->isNotCompleted())
                    <a href="{{ back_route('extensions.shop.command.destroy', $command) }}" class="btn btn-danger" data-method="delete"
                    data-confirm="Etes vous sûr de bien vouloir procéder à la suppression ?">
                    <i class="fas fa-trash"></i> Supprimer</a>
                    @endif
                </div>
            </div>
            <div class="card-body">
                        <div class="row">
                            <div class="col-md-12">
                                <form action="{{ back_route('extensions.shop.command.update', $command) }}" method="post" x-data="command">
                                    @method('PUT')
                                    @csrf
                                    <div class="row">
                                        <div class="col-md-9 ">
                                            <div class="card">
                                                <div class="card-header bg-secondary">
                                                    <h3 class="card-title text-white">
                                                        Commande: {{ $command->reference }}
                                                    </h3>
                                                </div>
                                                <div class="card-body">
                                                    @if($command->isPaid() || $command->isCompleted())
                                                    <div class="alert alert-warning">
                                                        Cette commande a été complétée et/ou payé, vous ne devriez plus la modifier.
                                                    </div>
                                                    @endif
                                                    <div class="d-flex justify-content-between">
                                                        <p>
                                                            Mode de paiement: Paiement à la livraison
                                                        </p>
                                                        @if ($command->ip)
                                                        <p>
                                                            Adresse IP du client : {{ $command->ip }}
                                                        </p>
                                                        @endif
                                                    </div>
                                                    <hr>
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <div class="row">
                                                                <div class="col-md-4  ">
                                                                    <div>
                                                                        <h5>Général</h5>
                                                                    </div>
                                                                    <hr>
                                                                    <div class="form-group">
                                                                        <label for="command_state">Etat:</label>
                                                                        <select name="command_state" id="command_state" class="form-control">
                                                                            <template x-for="state in states">
                                                                                <option :value="state.name" x-text="state.label" :selected="command.state == state.name"></option>
                                                                            </template>
                                                                        </select>
                                                                    </div>
                                                                    <div class="form-group">
                                                                        <label for="commmand_client">Client: </label>
                                                                        <select id="commmand_client" name="client[id]" class="form-control" @change="chooseClient($event.target.value)">
                                                                            <option value="0" :selected="form.client == 0">Choisir le client</option>
                                                                            <template x-for="client in clients">
                                                                                <option :value="client.id" x-text="client.name" :selected="client.id == form.client.id"></option>
                                                                            </template>
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-4 border-right border-left">
                                                                    <div>
                                                                        <h5>Facturation</h5>
                                                                    </div>
                                                                    <hr>
                                                                    <div class="form-group">
                                                                        <label for="name">Nom</label>
                                                                        <input type="text" name="client[name]" id="name" class="form-control" x-model='form.client.name'>
                                                                    </div>
                                                                    <div class="form-group">
                                                                        <label for="city">Ville</label>
                                                                        <input type="text" name="client[city]" id="city" class="form-control" x-model='form.client.city'>
                                                                    </div>
                                                                    <div class="form-group">
                                                                        <label for="country">Pays</label>
                                                                        <input type="text" name="client[country]" id="country" class="form-control" x-model='form.client.country'>
                                                                    </div>
                                                                    <div class="form-group">
                                                                        <label for="email">Email</label>
                                                                        <input type="text" name="client[email]" id="email" class="form-control" x-model='form.client.email'>
                                                                    </div>
                                                                    <div class="form-group">
                                                                        <label for="phone_number">Numéro de téléphone</label>
                                                                        <input type="text" name="client[phone_number]" id="phone_number" class="form-control" x-model='form.client.phone_number'>
                                                                    </div>


                                                                </div>
                                                                <div class="col-md-4">
                                                                    <div>
                                                                        <h5>Livraison</h5>
                                                                    </div>
                                                                    <hr>
                                                                    <div class="form-group">
                                                                        <label for="deliver_id">Livreur: </label>
                                                                        <select id="deliver_id" name="deliver[id]" class="form-control" @change="chooseDeliver($event.target.value)">
                                                                            <option value="0" :selected="form.deliver_id == 0">Choisir le livreur</option>
                                                                            <template x-for="deliver in delivers">
                                                                                <option :value="deliver.id" x-text="deliver.name" :selected="deliver.id == form.deliver_id"></option>
                                                                            </template>
                                                                        </select>

                                                                    </div>
                                                                    <div class="form-group" x-show="areas.length > 0" x-transition>
                                                                        <label for="">Zone de livraison</label>
                                                                        <select id="deliver_area" name="deliver[area]" class="form-control">
                                                                            <option value="0" :selected="form.deliver_area == 0">Choisir la zone</option>
                                                                            <template x-for="area in areas">
                                                                                <option :value="area.id" x-text="area.name" :selected="area.id == form.deliver_area"></option>
                                                                            </template>
                                                                        </select>
                                                                    </div>

                                                                    <div class="form-group">
                                                                        <label for="address">Lieu de livraison:</label>
                                                                        <textarea  id="address" name="deliver[address]" class="form-control" x-model='form.client.address'></textarea>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <hr>
                                                            <div class="row">
                                                                <div class="col-md-12">
                                                                    <div class="table-responsive">
                                                                        <template x-if="cart.items && cart.items.length != 0">
                                                                            <table class="table table-light" >
                                                                                <thead>
                                                                                    <th scope="col"></th>
                                                                                    <th scope="col">Article</th>
                                                                                    <th scope="col">Prix</th>
                                                                                    <th scope="col" width="100">Qté</th>
                                                                                    <th scope="col">Total</th>
                                                                                    <th scope="col">Actions</th>
                                                                                </thead>
                                                                                <tbody>
                                                                                    <template x-for="item in cart.items">
                                                                                        <tr>
                                                                                            <td>
                                                                                                <img :src="item.image" :alt="item.name" width="50">
                                                                                            </td>
                                                                                            <td>
                                                                                                <a :href="'/administrable/extensions/shop/products/' + item.model.slug + '/edit'" x-text="item.name"></a>
                                                                                            </td>
                                                                                            <td x-text="Helper.formatPrice(item.price)"></td>
                                                                                            <td>
                                                                                                <input @input="changeQuantity($event.target.value, item.rowId)" class="form-control" type="number"
                                                                                                :value="item.quantity">
                                                                                            </td>
                                                                                            <td x-text="Helper.formatPrice(item.subtotal)"></td>
                                                                                            <td>
                                                                                                <button @click.prevent="removeProduct(item.rowId)" class="btn btn-danger btn-sm"
                                                                                                title="Retirer le produit de la commande">X</button>
                                                                                            </td>
                                                                                        </tr>
                                                                                    </template>
                                                                                    <tr>
                                                                                        <td></td>
                                                                                        <td></td>
                                                                                        <td></td>
                                                                                        <td>Sous-total</td>
                                                                                        <td x-text="Helper.formatPrice(cart.subtotal)"></td>
                                                                                    </tr>
                                                                                    <tr x-show="cart.all_discount != 0">
                                                                                        <td></td>
                                                                                        <td></td>
                                                                                        <td></td>
                                                                                        <td>Remise</td>
                                                                                        <td x-text="Helper.formatPrice(cart.all_discount)"></td>
                                                                                    </tr>
                                                                                    @if(shop_settings('tva'))
                                                                                    <tr>
                                                                                        <td></td>
                                                                                        <td></td>
                                                                                        <td></td>
                                                                                        <td>Tva</td>
                                                                                        <td x-text="Helper.formatPrice(cart.tax)"></td>
                                                                                    </tr>
                                                                                    @endif
                                                                                    <tr>
                                                                                        <td></td>
                                                                                        <td></td>
                                                                                        <td></td>
                                                                                        <td>Livraison</td>
                                                                                        <td x-text="Helper.formatPrice( command.deliver.price || 0)"></td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td></td>
                                                                                        <td></td>
                                                                                        <td></td>
                                                                                        <td>Total</td>
                                                                                        <td x-text="Helper.formatPrice(cart.total + parseInt(command.deliver.price || 0))"
                                                                                        class="bg-success font-weight-bold text-center">
                                                                                    </td>
                                                                                </tr>

                                                                            </tbody>
                                                                        </table>
                                                                    </template>
                                                                </div>
                                                                <div class="col-md-12">
                                                                    <div x-show="product.add" class="clearfix">
                                                                        <div>
                                                                            <select  class="custom-select" x-model="product.current">
                                                                                <option value="0" selected>Choisir un produit</option>
                                                                                <template x-for="prod in products">
                                                                                    <option :value="prod.id" x-text="prod.name"></option>
                                                                                </template>
                                                                            </select>
                                                                        </div>
                                                                        <div class="btn-group mt-2 mb-2 float-right">
                                                                            <button @click.prevent="product.add = false" class="btn btn-secondary btn-sm">Annuler</button>
                                                                            <button @click.prevent="addProduct" class="btn btn-primary btn-sm">Ajouter</button>
                                                                        </div>
                                                                    </div>
                                                                    <div x-show="apply_discount" class="clearfix">
                                                                        <input type="text" class="form-control" placeholder="Entrez le montant de la remise" x-model.number="form.discount">
                                                                        <div class="btn-group mt-2 mb-2 float-right">
                                                                            <button @click.prevent="apply_discount = false" class="btn btn-secondary btn-sm">Annuler</button>
                                                                            <button @click.prevent="applyDiscount" :disabled='form.discount <= 0' class="btn btn-primary btn-sm">Appliquer la remise</button>
                                                                        </div>
                                                                    </div>

                                                                    <div class="btn-group">
                                                                        <button @click.prevent="product.add = true" class="btn btn-outline-primary"><i class="fa fa-plus"></i> Ajouter produits</button>
                                                                        <button :disabled="apply_discount" @click.prevent="apply_discount = true" class="btn btn-outline-primary"><i class="fa fa-check"></i> Appliquer une remise</button>
                                                                    </div>
                                                                </div>
                                                                <div class="col-12 mt-4">
                                                                    <button type="submit" :disabled="command.paid" class="btn btn-success btn-block"><i class="fa fa-save"></i> Enregistrer</button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <hr>

                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3 ">
                                        <div class="card">
                                            <div class="card-header bg-secondary">
                                                <h3 class="card-title text-white">
                                                    Actions
                                                </h3>
                                            </div>
                                            <div class="card-body">
                                                <div>
                                                    <div class="form-group">
                                                        <label for="created_at">Date de création</label>
                                                        <input type="string" name="created_at" id="created_at" class="form-control">
                                                    </div>
                                                    {{-- <div class="form-group">
                                                        <label>Actions de la commande</label>
                                                        <select name="actions" id="actions" class="custom-select">
                                                            <option value="">Email de facturation / détails de la commande au client</option>
                                                            <option value="">Envoyer à nouveau une notification de mouvelle commande</option>
                                                            <option value="">Régénérer les autorisations de téléchargement</option>
                                                        </select>
                                                    </div> --}}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card">
                                            <div class="card-header bg-secondary">
                                                <h3 class="card-title text-white">
                                                    Notes
                                                </h3>
                                            </div>
                                            <div class="card-body">
                                                <div>
                                                    <div class="form-group mt-2">
                                                        <textarea x-model="form.note.add" id="command_note" cols="30" rows="10" class="form-control" :class="{'is-invalid': form.note.add.length == 0 }" placeholder="Ajouter une note"></textarea>
                                                        <span class="invalid-feedback" role="alert" x-show="form.note.add.length == 0">Le contenu de la note est obligatoire</span>
                                                    </div>
                                                    <div class="form-group">
                                                        <button @click.prevent="addNote" type="submit" :disabled='form.note.add.length == 0'  class="btn btn-info btn-block"><i class="fa fa-save"></i> Enregistrer</button>
                                                    </div>

                                                    <ul class="list-group">
                                                        <template x-for="note in notes">
                                                            <div>
                                                                <li class="list-group-item mt-2">
                                                                    <blockquote x-text="note.comment" @dblclick="openEditNoteModal(note)"></blockquote>

                                                                    <small> <i class="fa fa-user"></i> <span x-text="note.commenter.name"></span>
                                                                        &nbsp;&nbsp;|&nbsp;&nbsp;
                                                                        <i class="fa fa-clock"></i> <span x-text="note.formated_create_date"></span>
                                                                        <div class="float-right">
                                                                            <a @click="openEditNoteModal(note)" href="#" class="text-secondary" title="Editer"><i
                                                                                class="fa fa-edit"></i></a>
                                                                                <a :href="`/${auth_prefix_path}/comments/notes/${note.id}`" data-confirm="Etes vous sûr de bien vouloir procéder à la suppression ?" data-method="delete"
                                                                                class="text-danger" data-toggle="tooltip" data-placement="top" title="Supprimer"><i
                                                                                class="fa fa-trash"></i></a>
                                                                            </div>
                                                                        </small>
                                                                    </li>
                                                                    <!-- Modal -->
                                                                    <div class="modal fade" :id="'note' + note.id" tabindex="-1" :aria-labelledby="'#note' + note.id + 'Label'"
                                                                    aria-hidden="true">
                                                                    <div class="modal-dialog modal-dialog-centered modal-lg">
                                                                        <div class="modal-content">
                                                                            <div class="modal-header">
                                                                                <h5 class="modal-title" id="'#note' + note.id + 'Label'">Edition de
                                                                                    note</h5>
                                                                                    <button @click="closeEditNoteModal(note)" type="button" class="close" aria-label="Close">
                                                                                        <span aria-hidden="true">&times;</span>
                                                                                    </button>
                                                                                </div>
                                                                                <div class="modal-body">
                                                                                    <div class="form-group">
                                                                                        <label for="'content' + note.id ">Contenu</label>
                                                                                        <textarea  :class="{'is-invalid': form.note.edit.length == 0 }" class="form-control"  id="'content' + note.id "
                                                                                        x-model='form.note.edit'></textarea>
                                                                                        <span class="invalid-feedback" role="alert" x-show="form.note.edit.length == 0">Le contenu de la note est obligatoire</span>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="modal-footer">
                                                                                    <button @click="closeEditNoteModal(note)" type="button" class="btn btn-secondary">Annuler</button>
                                                                                    <button type="submit" @click.prevent="updateNote(note)" :disabled='form.note.edit.length == 0'  class="btn btn-success">Enregistrer</button>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </template>


                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

        </div>
    </section>
</div>
@endsection



<x-administrable::daterangepicker
    fieldname='created_at'
    :startdate='$command->created_at'
    :timepicker='true'
    :timepicker24hour='true'
    :singledatepicker='true'
    drops='down'
    opens='right'
/>

@push('js')
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('command', () => ({
            // data
            command:  @json($command),
            commentable: @json(get_class($command)),
            products: @json($products),
            clients:  @json($clients),
            states:   @json($states),
            notes:    @json($notes),
            delivers: @json($delivers),
            areas: [],
            auth_prefix_path: @json(config('administrable.auth_prefix_path')),
            cart: [],
            product: {
                add: false,
                current: null,
            },
            apply_discount: false,
            form: {
                client: {
                    id: 0
                },
                deliver_id: 0,
                discount: null,
                note: {
                    add: ' ',
                    edit: ' ',
                }
            },
            // x-init
            init(){
                this.cart            = this.command.formated_products
                this.form.client     = this.command.client || {}
                this.form.deliver_id = this.command.deliver.deliver.id

                if (this.form.deliver_id){
                    this.chooseDeliver(this.form.deliver_id)
                    this.form.deliver_area = this.command.deliver.area.id
                }
            },
            // Methods
            resetClient(){
                this.form.client = this.command.client
            },
            chooseClient(id){
                const client = this.clients.filter(item => item.id == id)[0]

                if (!client) {
                    this.resetClient()
                    return
                }

                this.form.client = client
            },
            resetDelivers(){
                this.areas = []
            },
            chooseDeliver(id){
                const deliver = this.delivers.filter(item => item.id == id)[0]

                if (!deliver){
                    this.resetDelivers()
                    return
                }

                this.areas = deliver.areas
            },
            addProduct(){
                axios.post(this.route(this.command.id + '/addproduct'), { rowId: this.product.current })
                .then(({data}) => {
                    this.cart = data.cart
                })

                this.product.current = null
                this.product.add = false
            },
            removeProduct(rowId){
                if (confirm("Voulez-vous retirer le produit de la commande ?")){
                    axios.delete(this.route(this.command.id + '/products'), {data: {rowId}})
                    .then(({data}) => {
                        this.cart = data.cart
                    })
                }
            },
            changeQuantity(quantity, rowId){
                axios.put(this.route(this.command.id + '/products'), {rowId, quantity})
                .then(({data}) => {
                    this.cart = data.cart
                })
            },
            applyDiscount(){
                axios.post(this.route(this.command.id + "/applydiscount/" ), {
                    discount: this.form.discount,
                })
                .then(({data}) => {
                    this.cart           = data.cart
                    this.form.discount  = null
                    this.apply_discount = false
                })
            },
            openEditNoteModal(note){
                this.form.note.edit = Helper.clone(note.comment)
                jQuery('#note' + note.id).modal('show')
            },
            closeEditNoteModal(note){
                this.form.note.edit =  ''
                jQuery('#note' + note.id).modal('hide')
            },
            updateNote(note){
                axios.put(`/${this.auth_prefix_path}/comments/notes/${note.id}`, { comment: this.form.note.edit })
                .then(({data}) => {
                    this.closeEditNoteModal(note)
                    note.comment = data.comment
                })
            },
            addNote(){
                axios.post(`/${this.auth_prefix_path}/comments/notes`, {
                    comment: this.form.note.add,
                    commentable_id: this.command.id,
                    commentable_type: this.commentable,
                })
                .then(({data}) => {
                    this.notes = [...this.notes, data]
                    this.form.note.add = ' '
                })
            },
            route(url){
                return `/${this.auth_prefix_path}/extensions/shop/commands/${url}`
            }

        }))
    })

</script>
@endpush



