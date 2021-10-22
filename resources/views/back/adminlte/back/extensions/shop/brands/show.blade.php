@extends(back_view_path('layouts.base'))


@section('title', $brand->name)

@section('content')

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    {{-- <h1>Ajout</h1> --}}
                </div>
                <div class="col-sm-6">
                    <div class="float-sm-right">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route(config('administrable.guard') . '.dashboard') }}">Tableau de bord</a></li>
                            <li class="breadcrumb-item"><a href="{{ back_route('extensions.shop.statistic.index') }}">Boutiques</a></li>

                            <li class="breadcrumb-item"><a href="{{ back_route('extensions.shop.statistic.index') }}">Marques</a></li>
                            <li class="breadcrumb-item active"><a href="{{ back_route('extensions.shop.brand.show', $brand) }}">{{ $brand->name }}</a></li>
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
                       <h3 class="card-title">Marques</h3>
                         <div class="btn-group float-right">
                            <a href="{{ back_route('extensions.shop.brand.edit', $brand) }}" class="btn btn-primary">
                                <i class="fas fa-edit"></i> Editer</a>

                                <a href="{{ back_route('extensions.shop.brand.destroy', $brand) }}" class="btn btn-danger" data-method="delete"
                                data-confirm="Etes vous sûr de bien vouloir procéder à la suppression ?">
                                <i class="fas fa-trash"></i> Supprimer</a>

                            </div>


                    </div>
                   <div class="card-body row">
                        <div class="col-md-8">
                                <p class="pb-2"><b>Nom: </b>{{ $brand->name }}</p>
                                <p class="pb-2"><b>Description: </b>{{ $brand->description }}</p>
                                <p class="pb-2"><b>Date ajout: </b>{{ $brand->created_at->format('d/m/Y h:i') }}</p>
                            </div>
                            <div class="col-md-4">
                                @filemanagerShow([
                                    'model'      => $brand,
                                    'collection' => 'front-image',
                                    'label'      => 'Image mise en avant'
                                ])
                            </div>
                    </div>
                <!-- /.card-body -->

                <!-- /.card -->

            </section>
            <!-- /.content -->
        </div>
        <!-- /.content-wrapper -->

        @endsection
