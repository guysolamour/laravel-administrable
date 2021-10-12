@extends(back_view_path('layouts.base'))


@section('title', $deliver->name)


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
                            <li class="breadcrumb-item"><a href="{{ back_route('extensions.shop.deliver.index') }}">Livraisons</a></li>
                            <li class="breadcrumb-item active"><a href="{{ back_route('extensions.shop.deliver.show', $deliver) }}">{{ $deliver->name }}</a></li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <h3 class="card-title">Livreurs</h3>
                        <div class="btn-group float-right">
                             <a href="{{ back_route('extensions.shop.deliver.edit', $deliver) }}" class="btn btn-primary">
                                <i class="fas fa-edit"></i> Editer</a>

                             <a href="{{ back_route('extensions.shop.deliver.destroy', $deliver) }}" class="btn btn-danger" data-method="delete"
                                data-confirm="Etes vous sûr de bien vouloir procéder à la suppression ?">
                                <i class="fas fa-trash"></i> Supprimer</a>
                        </div>
                    </div>

                    <div class="card-body row">
                        <div class="col-md-8">
                            <p class="pb-2"><b>Nom: </b>{{ $deliver->name }}</p>
                            <p class="pb-2"><b>Numéro de téléphone: </b>{{ $deliver->phone_number }}</p>
                            <p class="pb-2"><b>Email: </b>{{ $deliver->email }}</p>
                            <p class="pb-2"><b>Description: </b>{{ $deliver->description }}</p>
                            <p class="pb-2"><b>Date ajout: </b>{{ $deliver->created_at->format('d/m/Y h:i') }}</p>
                            <hr>
                            <p>
                                <b>Les zones couvertes</b>
                            </p>
                            <ul class="list-group">
                                @foreach($deliver->areas as $area)
                                <li class="list-group-item">{{ $area->name }}  <span class="float-right font-weight-bold">{{ $area->pivot->price }} F</span></li>
                                @endforeach
                            </ul>
                        </div>
                        <div class="col-md-4">
                            @filemanagerShow([
                                'collection' => 'front-image',
                                'model' => $deliver,
                                'label' => 'Photo',
                            ])
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
