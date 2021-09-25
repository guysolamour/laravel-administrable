@extends(back_view_path('layouts.base'))

@section('title', $coveragearea->name)


@section('content')

<div class="main-content">
    <div class="container-fluid">
        <div class="page-header">
            <div class="row align-items-end">
                <div class="col-lg-8">
                </div>
                <div class="col-lg-4">
                    <nav class="breadcrumb-container" aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item">
                                <a href="{{ route(config('administrable.guard') . '.dashboard') }}"><i class="ik ik-home"></i></a>
                            </li>
                            <li class="breadcrumb-item"><a href="{{ back_route('extensions.shop.statistic.index') }}">Boutique</a></li>
                            <li class="breadcrumb-item"><a href="{{ back_route('extensions.shop.coveragearea.index') }}">Zones</a></li>
                            <li class="breadcrumb-item active"><a href="{{ back_route('extensions.shop.coveragearea.show', $coveragearea) }}">{{ $coveragearea->name }}</a></li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <h3 class="card-title">Zone de couverture</h3>
                        <div class="btn-group float-right">
                            <a href="{{ back_route('extensions.shop.coveragearea.edit', $coveragearea) }}" class="btn btn-primary">
                                <i class="fas fa-edit"></i> Editer</a>


                                <a href="{{ back_route('extensions.shop.coveragearea.destroy', $coveragearea) }}" class="btn btn-danger" data-method="delete"
                                data-confirm="Etes vous sûr de bien vouloir procéder à la suppression ?">
                                <i class="fas fa-trash"></i> Supprimer</a>

                            </div>
                        </div>

                        <div class="card-body row">
                            <div class="col-md-12">

                                <p class="pb-2"><b>Nom: </b>{{ $coveragearea->name }}</p>
                                <p class="pb-2"><b>Description: </b>{{ $coveragearea->description }}</p>
                                <p class="pb-2"><b>Date ajout: </b>{{ $coveragearea->created_at->format('d/m/Y h:i') }}</p>
                            </div>

                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
    @endsection
