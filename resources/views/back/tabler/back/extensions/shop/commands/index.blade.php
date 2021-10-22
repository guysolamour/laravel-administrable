@extends(back_view_path('layouts.base'))


@section('title', 'Commandes')


@section('content')


<div class="row mb-5">
    <div class="col-12">
        <div class="d-flex justify-content-between">
            <ol class="breadcrumb breadcrumb-arrows" aria-label="breadcrumbs">
                <li class="breadcrumb-item"><a href="{{ route(config('administrable.guard') . '.dashboard') }}">Tableau de bord</a></li>
                <li class="breadcrumb-item"><a href="{{ back_route('extensions.shop.statistic.index') }}">Boutique</a></a></li>
                <li class="breadcrumb-item active">Commandes</li>
            </ol>

            <a href="{{ back_route('extensions.shop.command.create') }}" class="btn btn-success">
                <i class="fa fa-plus"></i>&nbsp; Ajouter</a>
        </div>
    </div>
</div>


<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between mb-3">
            <h3> Commandes </h3>
            <a href="#" class="btn btn-danger d-none" data-model="{{ config('administrable.extensions.shop.models.command') }}"
                id="delete-all"><i class="fa fa-trash"></i> &nbsp; Tous supprimer</a>
        </div>

        <table class="table table-vcenter card-table" id='list'>
            <thead>
                <th></th>
                <th>
                    <label class="form-check" for="check-all">
                        <input class="form-check-input" type="checkbox" id="check-all">
                        <span class="form-check-label"></span>
                    </label>
                </th>
                <th></th>
                <th>Reference</th>
                <th>Payé</th>
                <th>Nom</th>
                <th>Numéro de téléphone</th>
                <th>Montant</th>
                <th>Etat</th>
                <th>Actions</th>

            </thead>
            <tbody>
               @foreach($commands as $command)
                <tr class="tr-shadow">
                    <td></td>
                    <td>
                        <label class="form-check" for="check-{{ $command->id }}">
                            <input class="form-check-input" type="checkbox" data-check data-id="{{ $command->id }}"
                                id="check-{{ $command->id }}" <span class="form-check-label"></span>
                        </label>
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
                        <div class="btn-group" role="group">
                            @if($command->isNotPaid())
                            <a href="{{ back_route('extensions.shop.command.confirm', $command) }}" class="btn btn-success"
                                data-type="success" data-method="post" data-title="Confirmation"
                                data-confirm="Etes vous sûr de confirmer le paiement de cette commande ? Ce qui signifie qui signifie que le client a soldé sa commande qui s'élève à {{ format_price($command->amount) }}"
                                title="Valider le paiement"><i class="fas fa-check"></i></a>
                            @endif
                            <a href="{{ back_route('extensions.shop.command.edit', $command) }}" class="btn btn-info"
                                title="Editer"><i class="fas fa-edit"></i></a>
                            @if($command->isNotPaid() || $command->isNotCompleted())
                            <a href="{{ back_route('extensions.shop.command.destroy',$command) }}" data-method="delete"
                                data-confirm="Etes vous sûr de bien vouloir procéder à la suppression ?" class="btn btn-danger"
                                title="Supprimer"><i class="fas fa-trash"></i></a>
                            @endif

                        </div>
                    </td>
                </tr>
                @endforeach

            </tbody>
        </table>
    </div>
</div>


<x-administrable::datatable />
@include(back_view_path('partials._deleteAll'))

@endsection
