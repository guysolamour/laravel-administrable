@extends(back_view_path('layouts.base'))

@section('title', $category->name)

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
                            <li class="breadcrumb-item"><a href="{{ back_route('extensions.blog.category.index') }}">Catégories</a></li>
                            <li class="breadcrumb-item active">{{ $category->name }}</li>
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
                    <div class='col-md-8'>
                        {{-- add fields here --}}
                        <div>
                            <p><span class="font-weight-bold">Nom:</span></p>
                            <p>
                                {{ $category->name }}
                            </p>
                        </div>

                        <div>
                            <p><span class="font-weight-bold">Date ajout:</span></p>
                            <p>
                                {{ $category->created_at->format('d/m/Y h:i') }}
                            </p>
                        </div>
                    </div>
                    <div class='col-md-4'>
                        <section style="margin-bottom: 2rem;">

                            <div class="btn-group-horizontal">
                                <a href="{{ back_route('extensions.blog.category.edit', $category) }}" class="btn btn-info" data-toggle="tooltip"
                                    data-placement="top" title="Editer"><i class="fas fa-edit"></i>Editer</a>
                                <a href="{{ back_route('extensions.blog.category.destroy',$category) }}" data-method="delete" data-toggle="tooltip"
                                    data-placement="top" title="Supprimer"
                                    data-confirm="Etes vous sûr de bien vouloir procéder à la suppression ?" class="btn btn-danger"><i
                                        class="fa fa-trash"></i> Supprimer</a>
                            </div>
                        </section>
                    </div>

                </div>
            </div>

        </div>
        <!-- /.card-body -->

        <!-- /.card -->

    </section>
    <!-- /.content -->
</div>
<!-- /.content-wrapper -->

@endsection