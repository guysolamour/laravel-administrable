@extends(back_view_path('layouts.base'))


@section('title', 'Catégories')

@section('content')

<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    {{-- <h1>Zones de livraison</h1> --}}
                </div>
                <div class="col-sm-6">
                    <div class='float-sm-right'>
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route(config('administrable.guard') . '.dashboard') }}">Tableau de bord</a></li>
                            <li class="breadcrumb-item"><a href="{{ back_route('extensions.shop.statistic.index') }}">Boutique</a></li>
                            <li class="breadcrumb-item active"><a href="#">Catégories</a></li>
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
                <h3 class="card-title">Catégories</h3>
                      <div class="btn-group float-right">
                        <a href="{{ back_route('extensions.shop.category.create') }}" class="btn  btn-primary"> <i
                                class="fa fa-plus"></i> Ajouter</a>

                        <a href="#" class="btn btn-danger d-none" data-model="{{ config('administrable.extensions.shop.models.category') }}" id="delete-all">
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
                                        <th>Description</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($categories as $category)
                                    <tr>
                                        <td>
                                            <div class="custom-control custom-checkbox">
                                                <input type="checkbox" data-check
                                                class="custom-control-input"
                                                data-id="{{ $category->id }}"
                                                id="check-{{ $category->id }}">
                                                <label class="custom-control-label"
                                                for="check-{{ $category->id }}"></label>
                                            </div>
                                        </td>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>
                                            {{ $category->name }}
                                            @if($category->children->isNotEmpty())
                                                (
                                                    {{ $category->children->implode('name', ', ') }}
                                                )
                                            @endif
                                        </td>
                                        <td>{{ Str::limit($category->description,50) }}</td>
                                        <td>
                                           <div class="btn-group" role="group">
                                                <a href="{{ back_route('extensions.shop.category.show', $category) }}" class="btn btn-primary"
                                                    title="Afficher"><i class="fas fa-eye"></i></a>

                                                <a href="{{ back_route('model.clone', get_clone_model_params($category)) }}" class="btn btn-secondary"
                                                    title="Cloner"><i class="fas fa-clone"></i></a>

                                                <a href="{{ back_route('extensions.shop.category.edit', $category) }}" class="btn btn-info"
                                                    title="Editer"><i class="fas fa-edit"></i></a>

                                                <a href="{{ back_route('extensions.shop.category.destroy',$category) }}" data-method="delete"
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
