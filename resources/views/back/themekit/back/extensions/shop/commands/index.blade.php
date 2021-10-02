@extends('back.layouts.base')

@section('title','Commandes')


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
                        <li class="breadcrumb-item">
                            <a href="{{ back_route('extensions.shop.statistic.index') }}">Boutique</a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">Commandes</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <h3 class="card-title">Commandes ({{ $commands->count() }})</h3>
                    <div class="btn-group float-right">
                        <a href="{{ back_route('extensions.shop.command.create') }}" class="btn  btn-primary"> <i
                                class="fa fa-plus"></i> Ajouter</a>

                        <a href="#" class="btn btn-danger d-none" data-model="{{ config('administrable.extensions.shop.models.command') }}" id="delete-all">
                            <i class="fa fa-trash"></i> Tous supprimer</a>
                    </div>
                </div>

                <div class="card-body">
                    <table class="table table-vcenter card-table" id='list'>
                        <thead>
                            <th>
                                <div class="checkbox-fade fade-in-success ">
                                    <label for="check-all">
                                        <input type="checkbox" value=""  id="check-all">
                                        <span class="cr">
                                            <i class="cr-icon ik ik-check txt-success"></i>
                                        </span>
                                    </label>
                                </div>
                            </th>
                            <th>#</th>

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
                                <td>
                                <div class="checkbox-fade fade-in-success ">
                                        <label for="check-{{ $command->id }}">
                                            <input type="checkbox" data-check data-id="{{ $command->id }}"  id="check-{{ $command->id }}">
                                            <span class="cr">
                                                <i class="cr-icon ik ik-check txt-success"></i>
                                            </span>
                                            {{-- <span>Default</span> --}}
                                        </label>
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
        </div>

    </div>
    </div>
</div>

<x-administrable::datatable />
@include('back.partials._deleteAll')
@endsection
