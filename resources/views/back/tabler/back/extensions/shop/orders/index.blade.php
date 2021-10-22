@extends(back_view_path('layouts.base'))

@section('title', 'Paiements')


@section('content')


<div class="row mb-5">
    <div class="col-12">
        <div class="d-flex justify-content-between">
            <ol class="breadcrumb breadcrumb-arrows" aria-label="breadcrumbs">
                <li class="breadcrumb-item"><a href="{{ route(config('administrable.guard') . '.dashboard') }}">Tableau de bord</a></li>
                <li class="breadcrumb-item"><a href="{{ back_route('extensions.shop.statistic.index') }}">Boutique</a></a></li>
                <li class="breadcrumb-item active">Paiements</li>
            </ol>
{{--
            <a href="{{ back_route('extensions.shop.command.create') }}" class="btn btn-success">
                <i class="fa fa-plus"></i>&nbsp; Ajouter</a>  --}}
        </div>
    </div>
</div>


<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between mb-3">
            <h3> Paiements ({{ $orders->count() }}) </h3>
        </div>

        <table class="table table-vcenter card-table" id='list'>
            <thead>
                <th></th>
                <th>Commande</th>
                <th>Montant</th>
                <th>Livraison</th>
                <th>Total</th>
                <th>Client</th>
                <th>Date de paiement</th>

                <th>Actions</th>

            </thead>
            <tbody>
                @foreach($orders as $order)
                <tr class="tr-shadow">
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
</div>


<x-administrable::datatable />
@include(back_view_path('partials._deleteAll'))

@endsection
