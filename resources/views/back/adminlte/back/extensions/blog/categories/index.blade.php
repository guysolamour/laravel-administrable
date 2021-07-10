@extends(back_view_path('layouts.base'))

@section('title', 'Catégories')

@section('content')

<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    {{-- <h1>Catégories</h1> --}}
                </div>
                <div class="col-sm-6">
                    <div class='float-sm-right'>
                         <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route(config('administrable.guard') . '.dashboard') }}">Tableau de bord</a></li>
                            <li class="breadcrumb-item active">Catégories</li>
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
                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse" data-toggle="tooltip"
                        title="Réduire">
                        <i class="fas fa-minus"></i></button>
                </div>
            </div>
            <div class="card-body">
                <div class='row'>
                    <div class="col-md-12">
                        <div class="card">

                            <!-- /.card-header -->
                            <div class="card-body p-0">
                                <div class="card">
                                    <div class="card-header">
                                        <h3 class="card-title">Catégories</h3>
                                        <div class="btn-group float-right">
                                            <a href="{{ back_route('extensions.blog.category.create') }}" class="btn  btn-primary"> <i
                                                    class="fa fa-plus"></i> Ajouter</a>
                                            <a href="#" class="btn btn-danger d-none" data-model="{{ config('administrable.extensions.blog.category.model') }}"
                                                id="delete-all"> <i class="fa fa-trash"></i> Tous supprimer</a>
                                        </div>
                                    </div>
                                    <!-- /.card-header -->
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
                                                        <th></th>
                                                        <th>Nom</th>
                                                        <th>Date ajout</th>
                                                        {{-- add fields here --}}
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
                                                        <td>{{ $category->name }}</td>

                                                        <td>{{ $category->created_at->format('d/m/Y h:i') }}</td>
                                                        {{-- add values here --}}
                                                        <td>
                                                            <div class="btn-group" role="group">
                                                                <a href="{{ back_route('extensions.blog.category.show', $category) }}"
                                                                    class="btn btn-primary" data-toggle="tooltip"
                                                                    data-placement="top" title="Afficher"><i
                                                                        class="fas fa-eye"></i></a>

                                                                <a href="{{ back_route('model.clone', get_clone_model_params($category)) }}"
                                                                class="btn btn-secondary" data-toggle="tooltip"
                                                                data-placement="top" title="Cloner"><i
                                                                    class="fas fa-clone"></i></a>

                                                                <a href="{{ back_route('extensions.blog.category.edit', $category) }}"
                                                                    class="btn btn-info" data-toggle="tooltip"
                                                                    data-placement="top" title="Editer"><i
                                                                        class="fas fa-edit"></i></a>
                                                                <a href="{{ back_route('extensions.blog.category.destroy',$category) }}"
                                                                    data-method="delete"
                                                                    data-confirm="Etes vous sûr de bien vouloir procéder à la suppression ?"
                                                                    class="btn btn-danger" data-toggle="tooltip"
                                                                    data-placement="top" title="Supprimer"><i
                                                                        class="fas fa-trash"></i></a>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    @endforeach
                                                </tbody>

                                            </table>
                                        </div>
                                    </div>
                                    <!-- /.card-body -->
                                </div>
                                <!-- /.mail-box-messages -->
                            </div>

                        </div>
                        <!-- /.card -->
                    </div>
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
