@extends(back_view_path('layouts.base'))


@section('title', 'Produits')



@section('content')
<div class="main-content">
    <div class="card ">
        <nav aria-label="breadcrumb" class="d-flex justify-content-end" style="padding-top:10px;padding-right:20px;">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route(config('administrable.guard') . '.dashboard') }}">Tableau de bord</a></li>
                <li class="breadcrumb-item"><a href="{{ back_route('extensions.shop.statistic.index') }}">Boutique</a></li>
                <li class="breadcrumb-item active" aria-current="page">Produits</li>
            </ol>
        </nav>

    </div>

    <div class="card">
        {{-- <h4 class="card-title">
                Produits
            </h4> --}}

        <div class="card-body">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title h3">
                        Produits
                    </h5>
                    <div class="btn-group">
                        <a href="{{ back_route('extensions.shop.product.create') }}"
                            class="btn btn-sm btn-label btn-round btn-primary"><label><i class="fa fa-plus"></i></label>
                            Ajouter</a>
                        <a href="#" data-model="{{ config('administrable.extensions.shop.models.product') }}" id="delete-all" class="btn btn-sm btn-label btn-round btn-danger d-none"><label><i
                                    class="fa fa-trash"></i></label> Tous Supprimer</a>

                    </div>
                </div>

                <table class="table table-hover table-has-action" id='list'>
                    <thead>
                        <tr>
                            <th>
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="check-all">
                                    <label class="form-check-label" for="check-all"></label>
                                </div>
                            </th>
                            <th>#</th>

                            <th>Nom</th>
                            <th>Type</th>
                            <th>prix</th>
                            <th>Nbr. achats</th>
                            <th>En ligne</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                         @foreach($products as $product)
                        <tr>
                            <td>
                                <div class="form-check">
                                    <input type="checkbox" data-check class="form-check-input" data-id="{{ $product->id }}"
                                        id="check-{{ $product->id }}">
                                    <label class="form-check-label" for="check-{{ $product->id }}"></label>
                                </div>
                            </td>
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
                               <nav class="nav no-gutters gap-2 fs-16">

                                    <a class="nav-link hover-primary" href="{{ back_route('extensions.shop.product.edit', $product) }}"
                                        data-provide="tooltip" title="Editer"><i
                                            class="ti-pencil"></i></a>
                                    <a class="nav-link hover-danger" href="{{ back_route('extensions.shop.product.destroy', $product) }}" data-provide="tooltip" title=""
                                        data-method="delete"
                                        data-confirm="Etes vous sûr de bien vouloir procéder à la suppression ?"
                                        title='Supprimer'
                                        data-original-title="Supprimer"><i class="ti-close"></i></a>
                                </nav>
                            </td>

                        </tr>

                       @endforeach
                    </tbody>
                </table>

            </div>
        </div>
    </div>


</div>


<x-administrable::datatable />
@include(back_view_path('partials._deleteAll'))

@endsection
