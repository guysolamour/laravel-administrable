@extends(back_view_path('layouts.base'))

@section('title', 'Paiements')

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
                            <li class="breadcrumb-item active">Paiements</li>
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
                                        <h3 class="card-title">Paiements ({{ $orders->count() }})</h3>

                                    </div>
                                    <!-- /.card-header -->
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table table-hover table-striped" id="list">
                                                <thead>
                                                    <tr>
                                                        <th></th>
                                                        <th>Commande</th>
                                                        <th>Montant</th>
                                                        <th>Livraison</th>
                                                        <th>Total</th>
                                                        <th>Client</th>
                                                        <th>Date de paiement</th>

                                                        <th>Actions</th>

                                                    </tr>
                                                </thead>
                                                <tbody>
                                                   @foreach($orders as $order)
                                                    <tr>
                                                         <td>{{ $loop->iteration }}</td>
                                                        <td>
                                                            <a href="{{ route('back.extensions.shop.command.edit', $order->command) }}">{{ $order->command->reference }}</a>
                                                        </td>
                                                        <td>{{ format_price($order->amount) }}</td>
                                                        <td>{{ format_price($order->deliver_price) }}</td>
                                                        <td>{{ format_price($order->total) }}</td>
                                                        <td>
                                                            <a href="{{ route('back.extensions.shop.user.show', $order->client) }}">{{ $order->client->name }}</a>
                                                        </td>
                                                        <td>{{ format_date($order->created_at) }}</td>
                                                        <td>
                                                            <div class="btn-group" role="group">
                                                                <a href="{{ route('back.extensions.shop.command.edit', $order->command) }}" class="btn btn-success btn-sm"><i class="fa fa-eye"></i> Commande </a>
                                                                <a href="{{ Storage::url($order->invoice) }}" target="_blank" class="btn btn-secondary btn-sm"><i class="fa fa-eye"></i> Facture</a>

                                                            </div>
                                                        </td>
                                                        <td>
                                                        </td>
                                                    </tr>
                                                    @endforeach
                                                </tbody>

                                            </table>
                                        </div>



                                        <!-- /.card-body -->
                                    </div>
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
