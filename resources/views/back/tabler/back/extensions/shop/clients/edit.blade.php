@extends(back_view_path('layouts.base'))

@section('title','Edition ' . $user->name)


@section('content')

<div class="row mb-5">
    <div class="col-12">
        <div class="d-flex justify-content-between">
            <ol class="breadcrumb breadcrumb-arrows" aria-label="breadcrumbs">
                <li class="breadcrumb-item"><a href="{{ route(config('administrable.guard') . '.dashboard') }}">Tableau de bord</a></li>
                <li class="breadcrumb-item"><a href="{{ back_route('extensions.shop.statistic.index') }}">Boutique</a></li>
                <li class="breadcrumb-item"><a href="{{ back_route('user.index') }}">Clients</a></li>
                <li class="breadcrumb-item"><a href="{{ back_route('user.show', $user) }}">{{ $user->name }}</a></li>
                <li class="breadcrumb-item active">Edition</li>
            </ol>

             <div class="btn-group">
                <a href="{{ back_route('user.destroy', $user) }}" class="btn btn-danger" data-method="delete"
                data-confirm="Etes vous sûr de bien vouloir procéder à la suppression ?">
                <i class="fas fa-trash"></i>&nbsp; Supprimer </a>
            </div>

        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <h3 class="title-5 m-b-35">
            Edition de commande
        </h3>
        <div class="row">
            <div class="col-md-8">
                @include(back_view_path('users._form'),['edit' => true])
            </div>
            <div class="col-md-4">
                @imagemanager([
                    'collection' => 'front-image',
                    'label'      => 'Photo de profil',
                    'model'      => $form->getModel(),
                ])
            </div>
        </div>
    </div>
</div>
@endsection



