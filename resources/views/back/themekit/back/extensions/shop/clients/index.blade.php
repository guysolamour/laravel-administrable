@extends(back_view_path('layouts.base'))


@section('title', 'Clients')


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

                        <li class="breadcrumb-item active" aria-current="page">Clients</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <h3 class="card-title">Clients</h3>
                    <div class="btn-group float-right">
                        <a href="{{ back_route('user.create') }}" class="btn  btn-primary"> <i
                                class="fa fa-plus"></i> Ajouter</a>
                        <a href="#" class="btn btn-danger d-none" data-model="\App\Models\User" id="delete-all">
                            <i class="fa fa-trash"></i> Tous supprimer</a>
                    </div>
                </div>

                <div class="card-body">
                    <table class="table table-vcenter card-table" id='list'>
                        <thead>
                            <th>#</th>
                            <th>Nom</th>
                            <th>Email</th>
                            <th>Numéro</th>
                            <th>Ville</th>
                            <th>Commandes</th>
                            <th>Dépense totale</th>
                            <th>Inscrit le</th>

                            {{-- add fields here --}}

                        </thead>
                        <tbody>
                            @foreach($users as $user)
                            <tr class="tr-shadow">

                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $user->name }}</td>
                                <td>{{ $user->email }}</td>
                                <td>{{ $user->phone_number }}</td>
                                <td>{{ $user->city }}</td>
                                <td>{{ $user->commands->count() }}</td>
                                <td>{{ format_price($user->total_expense) }}</td>
                                <td>{{ $user->created_at->format('d/m/Y') }}</td>
                                {{-- add values here --}}

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
    </div>
</div>

<x-administrable::datatable />
@include('back.partials._deleteAll')
@endsection
