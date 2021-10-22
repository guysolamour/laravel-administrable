@extends(back_view_path('layouts.base'))


@section('title','Commandes')

@section('content')
<div class="main-content">
    <div class="card ">
        <nav aria-label="breadcrumb" class="d-flex justify-content-end" style="padding-top:10px;padding-right:20px;">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route(config('administrable.guard') . '.dashboard') }}">Tableau de bord</a></li>
                <li class="breadcrumb-item"><a href="{{ back_route('extensions.shop.statistic.index') }}">Boutique</a></li>
                <li class="breadcrumb-item active">Commandes</li>
            </ol>
        </nav>

    </div>

    <div class="card">
        {{-- <h4 class="card-title">
                Commandes
            </h4> --}}

        <div class="card-body">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title h3">
                        Commandes ({{ $commands->count() }})
                    </h5>
                    <div class="btn-group">
                        <a href="{{ back_route('extensions.shop.command.create') }}"
                            class="btn btn-sm btn-label btn-round btn-primary"><label><i class="fa fa-plus"></i></label>
                            Ajouter</a>
                        <a href="#" data-model="{{ config('administrable.extensions.shop.models.command') }}" id="delete-all" class="btn btn-sm btn-label btn-round btn-danger d-none"><label><i
                                    class="fa fa-trash"></i></label> Tous Supprimer</a>

                    </div>
                </div>

                <table class="table table-hover table-has-action" id='list'>
                    <thead>
                        <tr>
                            <th>
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="check-all">
                                    <label class="form-check-label" for="check-all"></label>
                                </div>
                            </th>
                            <th>#</th>
                            <th>Référence</th>
                            <th>Payé</th>
                            <th>Nom</th>
                            <th>Numéro de téléphone</th>
                            <th>Montant</th>
                            <th>Etat</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($commands as $command)
                        <tr>
                            <td>
                                 <div class="form-check">
                                    <input type="checkbox" data-check class="form-check-input" data-id="{{ $command->id }}"
                                        id="check-{{ $command->id }}">
                                    <label class="form-check-label" for="check-{{ $command->id }}"></label>
                                </div>
                            </td>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $command->reference }}</td>

                            <td>
                                @if($command->isPaid())
                                    <span class="badge badge-success">Payé</span>
                                @else
                                    <span class="badge badge-danger">Impayé</span>
                                @endif
                            </td>
                            <td>{{ $command->name }}</td>
                            <td>{{ $command->phone_number }}</td>
                            <td>{{ format_price($command->amount) }}</td>
                            <td>{{ $command->state_label }}</td>

                            <td>
                                <nav class="nav no-gutters gap-2 fs-16">
                                    @if($command->isNotPaid())
                                    <a href="{{ back_route('extensions.shop.command.confirm', $command) }}" class="nav-link hover-primary"
                                        data-type="success" data-method="post" data-title="Confirmation"
                                        data-confirm="Etes vous sûr de confirmer le paiement de cette commande ? Ce qui signifie qui signifie que le client a soldé sa commande qui s'élève à {{ format_price($command->amount) }}"
                                        title="Valider le paiement"><i class="fas fa-check"></i></a>
                                    @endif

                                    <a class="nav-link hover-primary" href="{{ back_route('extensions.blog.command.edit', $command) }}"
                                        data-provide="tooltip" title="Editer"><i
                                            class="ti-pencil"></i></a>

                                    @if($command->isNotPaid() || $command->isNotCompleted())
                                    <a class="nav-link hover-danger" href="{{ back_route('extensions.blog.command.destroy', $command) }}" data-provide="tooltip" title=""
                                        data-method="delete"
                                        data-confirm="Etes vous sûr de bien vouloir procéder à la suppression ?"
                                        title='Supprimer'
                                        data-original-title="Supprimer"><i class="ti-close"></i></a>
                                    @endif
                                </nav>
                            </td>

                        </tr>
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
