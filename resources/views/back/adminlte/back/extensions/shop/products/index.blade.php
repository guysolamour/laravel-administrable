@extends(back_view_path('layouts.base'))

@section('title', 'Produits')

@section('content')

<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    {{-- <h1>Produits</h1> --}}
                </div>
                <div class="col-sm-6">
                    <div class='float-sm-right'>
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route(config('administrable.guard') . '.dashboard') }}">Tableau de bord</a></li>
                            <li class="breadcrumb-item"><a href="{{ back_route('extensions.shop.statistic.index') }}">Boutique</a></li>
                            <li class="breadcrumb-item active" aria-current="page"><a href="#">Produits</a></li>
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
                <h3 class="card-title">Produits</h3>
                        <div class="btn-group float-right">
                            <a href="{{ back_route('extensions.shop.product.create') }}" class="btn  btn-primary"> <i
                                    class="fa fa-plus"></i> Ajouter</a>

                            <a href="#" class="btn btn-danger d-none" data-model="{{ config('administrable.extensions.shop.models.product') }}" id="delete-all">
                                <i class="fa fa-trash"></i> Tous supprimer</a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover table-striped" id="list">
                                <thead>
                                    <tr>
                                        <th>
                                            <div class="custom-control custom-checkbox">
                                                <input type="checkbox" class="custom-control-input"
                                                id="check-all">
                                                <label class="custom-control-label"
                                                for="check-all"></label>
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
                                            <div class="custom-control custom-checkbox">
                                                <input type="checkbox" data-check
                                                class="custom-control-input"
                                                data-id="{{ $product->id }}"
                                                id="check-{{ $product->id }}">
                                                <label class="custom-control-label"
                                                for="check-{{ $product->id }}"></label>
                                            </div>
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

                </div>
                <!-- /.card-body -->

                <!-- /.card -->

            </section>
            <!-- /.content -->
        </div>

        <x-administrable::datatable />
        @include(back_view_path('partials._deleteAll'))
        @endsection
