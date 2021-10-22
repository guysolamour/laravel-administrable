@extends(back_view_path('layouts.base'))


@section('title', 'Clients')



@section('content')
<div class="main-content">
    <div class="card ">
        <nav aria-label="breadcrumb" class="d-flex justify-content-end" style="padding-top:10px;padding-right:20px;">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route(config('administrable.guard') . '.dashboard') }}">Tableau de bord</a></li>
                <li class="breadcrumb-item"><a href="{{ back_route('extensions.shop.statistic.index') }}">Boutique</a></li>
                <li class="breadcrumb-item active" aria-current="page">Clients</li>
            </ol>
        </nav>

    </div>

    <div class="card">
        {{-- <h4 class="card-title">
                Clients
            </h4> --}}

        <div class="card-body">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title h3">
                        Clients
                    </h5>
                    <div class="btn-group">
                        <a href="{{ back_route('user.create') }}"
                            class="btn btn-sm btn-label btn-round btn-primary"><label><i class="fa fa-plus"></i></label>
                            Ajouter</a>
                        {{-- <a href="#" data-model="\App\Models\User" id="delete-all" class="btn btn-sm btn-label btn-round btn-danger d-none"><label><i
                                    class="fa fa-trash"></i></label> Tous Supprimer</a> --}}

                    </div>
                </div>

                <table class="table table-hover table-has-action" id='list'>
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Nom</th>
                            <th>Email</th>
                            <th>Numéro</th>
                            <th>Ville</th>
                            <th>Commandes</th>
                            <th>Dépense totale</th>
                            <th>Inscrit le</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $user)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>{{ $user->phone_number }}</td>
                            <td>{{ $user->city }}</td>
                            <td>{{ $user->commands->count() }}</td>
                            <td>{{ format_price($user->total_expense) }}</td>
                            <td>{{ $user->created_at->format('d/m/Y') }}</td>

                            <td>
                                <nav class="nav no-gutters gap-2 fs-16">
                                    <a class="nav-link hover-primary" href="{{ back_route('extensions.shop.user.show', $user) }}"
                                        data-provide="tooltip" title="{{ Lang::get('administrable::messages.default.show') }}"><i
                                            class="ti-eye"></i></a>

                                    <a class="nav-link hover-primary" href="{{ back_route('extensions.shop.user.edit', $user) }}"
                                        data-provide="tooltip" title="{{ Lang::get('administrable::messages.default.edit') }}"><i
                                            class="ti-pencil"></i></a>
                                </nav>
                            </td>

                        </tr>
                         <!-- Modal -->
                        <div class="modal fade" id="changePassword{{ $user->id }}" tabindex="-1" role="dialog"
                            aria-labelledby="changePassword{{ $user->id }}Title" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="changePassword{{ $user->id }}Title">Changer le mot de passe <span class="font-weight-bold">({{ $user->name }})</span></h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <form action="{{ back_route('user.changepassword', $user) }}" method="POST">
                                    <div class="modal-body">
                                            @method('put')
                                            @csrf
                                            <div class="form-group">
                                                <label for="">Utilisateur </label>
                                                <input type="text" class="form-control" value="{{ $user->name }}" disabled>
                                            </div>
                                            <div class="form-group">
                                                <label for="">Nouveau mot de passe <span class="text-danger">*</span></label>
                                                <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" required minlength="8">
                                                @error('password')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                                @enderror
                                            </div>
                                            <div class="form-group">
                                                <label for="">Confirmer le mot de passe <span class="text-danger">*</span> </label>
                                                <input type="password" name="password_confirmation" class="form-control @error('password_confirmation') is-invalid @enderror" required minlength="8">
                                                @error('password_confirmation')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                                @enderror
                                            </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuler</button>
                                        <button type="submit" class="btn btn-primary">Enregistrer</button>
                                    </div>
                                </form>
                                </div>
                            </div>
                        </div>
                       @endforeach
                    </tbody>
                </table>

            </div>
        </div>
    </div>


</div>


<x-administrable::datatable />
@include(back_view_path('partials._deleteAll'))

@endsection
