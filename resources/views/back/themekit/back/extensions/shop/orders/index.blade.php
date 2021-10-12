@extends('back.layouts.base')

@section('title', 'Paiements')


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
                        <li class="breadcrumb-item active" aria-current="page">Paiements</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <h3 class="card-title">Paiements ({{ $orders->count() }})</h3>
                    <div class="btn-group float-right">
                    </div>
                </div>

                <div class="card-body">
                    <table class="table table-vcenter card-table" id='list'>
                        <thead>
                            <th>#</th>

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
