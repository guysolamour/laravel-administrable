@extends(back_view_path('layouts.base'))

@section('title', 'Produits')

@section('content')


<div class="row mb-5">
    <div class="col-12">
        <div class="d-flex justify-content-between">
            <ol class="breadcrumb breadcrumb-arrows" aria-label="breadcrumbs">
                <li class="breadcrumb-item"><a href="{{ route(config('administrable.guard') . '.dashboard') }}">Tableau de bord</a></li>
                <li class="breadcrumb-item"><a href="{{ back_route('extensions.shop.statistic.index') }}">Boutique</a></a></li>
                <li class="breadcrumb-item active"><a href="#">Produits</a></li>
            </ol>

            <a href="{{ back_route('extensions.shop.product.create') }}" class="btn btn-success">
                <i class="fa fa-plus"></i>&nbsp; Ajouter</a>
        </div>
    </div>
</div>


<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between mb-3">
            <h3> Produits </h3>

              <a href="#" class="btn btn-danger d-none" data-model="{{ config('administrable.extensions.shop.models.product') }}" id="delete-all">
                                <i class="fa fa-trash"></i> Tous supprimer</a>
        </div>

        <table class="table table-vcenter card-table" id='list'>
            <thead>
                <th></th>
                <th>
                    <label class="form-check" for="check-all">
                        <input class="form-check-input" type="checkbox" id="check-all">
                        <span class="form-check-label"></span>
                    </label>
                </th>
                <th>#</th>
                <th>Nom</th>
                <th>Type</th>
                <th>prix</th>
                <th>Nbr. achats</th>
                <th>En ligne</th>
                <th>Actions</th>

            </thead>
            <tbody>
                @foreach($products as $product)
                <tr class="tr-shadow">
                    <td></td>
                    <td>
                        <label class="form-check" for="check-{{ $product->id }}">
                            <input class="form-check-input" type="checkbox" data-check data-id="{{ $product->id }}"
                                id="check-{{ $product->id }}" <span class="form-check-label"></span>
                        </label>
                    </td>
                    <td>{{ $loop->iteration }}</td>
                    <td>
                        <img src="{{ $product->image }}" alt="{{ $product->name }}" width="50">
                    </td>
                    <td>{{ $product->name }}</td>
                    <td>{{ $product->type_label }}</td>
                    <td>{{ format_price($product->price) }}</td>
                    <td>{{ $product->sold_count }}</td>
                    <td>
                        <i class="fa fa-circle {{ $product->online ? 'text-success' : 'danger' }}"></i>
                    </td>
                    <td>
                        <div class="btn-group" role="group">
                            {{--  <a href="{{ back_route('extensions.shop.product.show', $product) }}" class="btn btn-primary"
                                title="Afficher"><i class="fas fa-eye"></i></a>  --}}

                            {{-- <a href="{{ back_route('model.clone', get_clone_model_params($product)) }}" class="btn btn-secondary"
                                title="Cloner"><i class="fas fa-clone"></i></a> --}}

                            <a href="{{ back_route('extensions.shop.product.edit', $product) }}" class="btn btn-info"
                                title="Editer"><i class="fas fa-edit"></i></a>

                            <a href="{{ back_route('extensions.shop.product.destroy', $product) }}" data-method="delete"
                                data-confirm="Etes vous sûr de bien vouloir procéder à la suppression ?" class="btn btn-danger"
                                title="Supprimer"><i class="fas fa-trash"></i></a>

                        </div>
                    </td>
                </tr>

                @endforeach

            </tbody>
        </table>
    </div>
</div>


<x-administrable::datatable />
@include(back_view_path('partials._deleteAll'))

@endsection
