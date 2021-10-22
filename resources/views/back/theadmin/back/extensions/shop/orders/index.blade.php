@extends(back_view_path('layouts.base'))


@section('title', 'Paiements')


@section('content')
<div class="main-content">
    <div class="card ">
        <nav aria-label="breadcrumb" class="d-flex justify-content-end" style="padding-top:10px;padding-right:20px;">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route(config('administrable.guard') . '.dashboard') }}">Tableau de bord</a></li>
                <li class="breadcrumb-item"><a href="{{ back_route('extensions.shop.statistic.index') }}">Boutique</a></li>
                <li class="breadcrumb-item active">Paiements</li>
            </ol>
        </nav>

    </div>

    <div class="card">
        {{-- <h4 class="card-title">
                Paiements
            </h4> --}}

        <div class="card-body">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title h3">
                        Paiements ({{ $orders->count() }})
                    </h5>
                </div>

                <table class="table table-hover table-has-action" id='list'>
                    <thead>
                        <tr>
                            <th>#</th>
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
                                <nav class="nav no-gutters gap-2 fs-16">

                                    <a class="nav-link hover-primary" href="{{ back_route('extensions.blog.command.edit', $order->command) }}"
                                        data-provide="tooltip" title="Editer"><i
                                            class="ti-pencil"></i></a>

                                    <a href="{{ Storage::url($order->invoice) }}" target="_blank" class="nav-link hover-primary"><i class="fa fa-eye"></i> Facture</a>
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
