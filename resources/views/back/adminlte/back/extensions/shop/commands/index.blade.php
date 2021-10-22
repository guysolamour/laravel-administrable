@extends(back_view_path('layouts.base'))

@section('title', 'Commandes')

@section('content')

<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    {{-- <h1>Catégories</h1> --}}
                </div>
                <div class="col-sm-6">
                    <div class='float-sm-right'>
                         <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route(config('administrable.guard') . '.dashboard') }}">Tableau de bord</a></li>
                            <li class="breadcrumb-item"><a href="{{ back_route('extensions.shop.statistic.index') }}">Boutique</a></a></li>
                            <li class="breadcrumb-item active">Commandes</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content">

        <!-- Default box -->
        <div class="card">
            <div class="card-header">
                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse" data-toggle="tooltip"
                        title="Réduire">
                        <i class="fas fa-minus"></i></button>
                </div>
            </div>
            <div class="card-body">
                <div class='row'>
                    <div class="col-md-12">
                        <div class="card">

                            <!-- /.card-header -->
                            <div class="card-body p-0">
                                <div class="card">
                                    <div class="card-header">
                                        <h3 class="card-title">Commandes ({{ $commands->count() }})</h3>
                                        <div class="btn-group float-right">
                                            <a href="{{ back_route('extensions.shop.command.create') }}" class="btn  btn-primary"> <i
                                                    class="fa fa-plus"></i> Ajouter</a>

                                            <a href="#" class="btn btn-danger d-none" data-model="{{ config('administrable.extensions.shop.models.command') }}" id="delete-all">
                                                <i class="fa fa-trash"></i> Tous supprimer</a>
                                        </div>
                                    </div>
                                    <!-- /.card-header -->
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table table-hover table-striped" id="list">
                                                <thead>
                                                    <tr>
                                                        <th>
                                                            <div class="custom-control custom-checkbox">
                                                                <input type="checkbox" class="custom-control-input"
                                                                    id="check-all">
                                                                <label class="custom-control-label"
                                                                    for="check-all"></label>
                                                            </div>
                                                        </th>
                                                        <th></th>
                                                        <th>Reference</th>
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
                                                            <div class="custom-control custom-checkbox">
                                                                <input type="checkbox" data-check
                                                                    class="custom-control-input"
                                                                    data-id="{{ $command->getKey() }}"
                                                                    id="check-{{ $command->getKey() }}">
                                                                <label class="custom-control-label"
                                                                    for="check-{{ $command->getKey() }}"></label>
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
                                    <!-- /.card-body -->
                                </div>
                                <!-- /.mail-box-messages -->
                            </div>

                        </div>
                        <!-- /.card -->
                    </div>
                </div>
            </div>

        </div>
        <!-- /.card-body -->

        <!-- /.card -->

    </section>
    <!-- /.content -->
</div>

<x-administrable::datatable />
@include(back_view_path('partials._deleteAll'))
@endsection
