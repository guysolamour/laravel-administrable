@extends(back_view_path('layouts.base'))

@section('title','Edition ' . $user->name)


@section('content')
<div class="main-content">
    <div class="card ">
        <nav aria-label="breadcrumb" class="d-flex justify-content-end" style="padding-top:10px;padding-right:20px;">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route(config('administrable.guard') . '.dashboard') }}">Tableau de bord</a></li>
                <li class="breadcrumb-item"><a href="{{ back_route('extensions.shop.statistic.index') }}">Boutique</a></li>
                <li class="breadcrumb-item">
                    <a href="{{ back_route('user.index') }}">Clients</a>
                </li>
                <li class="breadcrumb-item">
                    <a href="{{ back_route('user.show', $user) }}">{{ $user->name }}
                    </a>
                </li>
                <li class="breadcrumb-item active" aria-current="page"><a href="#">Edition</a></li>
            </ol>
        </nav>


    </div>

    <div class="card">
        <h4 class="card-title">
            Edition
        </h4>

        <div class="card-body">
            <div class="row">
                <div class="col-md-12">
                    <div class="mb-4 btn-group float-right">
                        <a href="{{ back_route('user.destroy', $user) }}" class="btn btn-danger" data-method="delete"
                        data-confirm="Etes vous sûr de bien vouloir procéder à la suppression ?">
                        <i class="fas fa-trash"></i> Supprimer</a>
                    </div>
                </div>
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
</div>
@endsection
