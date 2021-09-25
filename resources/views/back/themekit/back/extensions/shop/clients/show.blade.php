@extends(back_view_path('layouts.base'))

@section('title', $user->name)


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
                                <a href="{{ route('admin.dashboard') }}"><i class="ik ik-home"></i></a>
                            </li>
                            <li class="breadcrumb-item"><a href="{{ back_route('extensions.shop.statistic.index') }}">Boutique</a></li>

                            <li class="breadcrumb-item">
                                <a href="{{  back_route('user.index') }}">Utilisateurs</a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">{{ $user->name }}</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <h3 class="card-title">Témoignages</h3>
                        <div class="btn-group float-right">
                            <a href="{{ back_route('user.edit', $user) }}" class="btn btn-primary">
                                <i class="fas fa-edit"></i> Editer</a>
                            <a href="{{ back_route('user.destroy', $user) }}" class="btn btn-danger" data-method="delete"
                                data-confirm="Etes vous sûr de bien vouloir procéder à la suppression ?">
                                <i class="fas fa-trash"></i> Supprimer</a>
                        </div>
                    </div>

                    <div class="card-body row">
                        <div class="col-md-8">
                            {{-- add fields here --}}
                            <div class="pb-2">
                                <p><span class="font-weight-bold">Nom:</span></p>
                                <p>
                                    {{ $user->name }}
                                </p>
                            </div>

                              <div class="pb-2">
                                <p><span class="font-weight-bold">Job:</span></p>
                                <p>
                                    {{ $user->tinymce }}
                                </p>
                            </div>

                            <div class="pb-2">
                                <p><span class="font-weight-bold">Email:</span></p>
                                <p>
                                    {{ $user->email }}
                                </p>
                            </div>



                        </div>
                        <div class="col-md-4">
                            @filemanagerShow([
                                'model' => $user,
                                'label' => 'Photo de profil',
                            ])
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
