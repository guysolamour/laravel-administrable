@extends(back_view_path('layouts.base'))


@section('title', 'Statistiques')


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
                            <li class="breadcrumb-item"><a href="{{ back_route('extensions.shop.statistic.index') }}">Boutique</a></li>
                            <li class="breadcrumb-item active" aria-current="page"><a href="#">Boutique</a></li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <h3 class="card-title">Statistiques</h3>
                    </div>

                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="card text-white bg-primary">
                                    <div class="card-body">
                                        <div class="card-title">Entrées (mois en cours)</div>
                                        <p class="card-text">{{ format_price($current_month_amount) }}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card text-white bg-primary">
                                    <div class="card-body">
                                        <div class="card-title">Entrées (année en cours)</div>
                                        <p class="card-text">{{ format_price($current_year_amount) }}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card text-white bg-primary">
                                    <div class="card-body">
                                        <div class="card-title">Total entrées</div>
                                        <p class="card-text">{{ format_price($total_orders) }}</p>
                                    </div>
                                </div>
                            </div>

                        </div>
                       <div>
                           <h6>Liste des paiements</h6>
                            <table class="table table-striped">
                                <thead>
                                    <th>#</th>
                                    <th>Commande</th>
                                    <th>Nbr. produits</th>
                                    <th>Nom du Livreur</th>
                                    <th>Prix du Livreur</th>
                                    <th>Total</th>
                                    <th>Actions</th>
                                </thead>
                                <tbody>
                                    @foreach($orders as $order)
                                    <tr>
                                        <td>{{ $order->command->reference }}</td>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $order->products_count }}</td>
                                        <td>{{ $order->deliver_name }}</td>
                                        <td>{{ format_price($order->deliver_price) }}</td>
                                        <td>{{ format_price($order->total) }}</td>
                                        <td>
                                            <div class="btn-group">
                                                <a href="{{ back_route('extensions.shop.command.edit', $order->command) }}" class="btn btn-info ">Voir la
                                                commande</a>
                                                <a href="{{ asset($order->invoice) }}" class="btn btn-primary " target="_blank">Voir la
                                                facture</a>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                       </div>
                       <hr>
                       <div>
                           <h6>Meilleurs clients</h6>
                            <table class="table table-striped">
                                <thead>
                                    <th>#</th>
                                    <th>Nom</th>
                                    <th>Email</th>
                                    <th>Numéro téléphone</th>
                                    <th>Total dépensé</th>
                                </thead>
                                <tbody>
                                    @foreach ($users_sorted_by_expense as $user)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $user->name }}</td>
                                        <td>{{ $user->email }}</td>
                                        <td>{{ $user->phone_number }}</td>
                                        <td>{{ format_price($user->total_expense) }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                       </div>
                       <hr>
                       <div>
                           <h6>Les produits les plus vendus</h6>
                            <table class="table table-striped">
                                <thead>
                                    <th>#</th>
                                    <th>Nom</th>
                                    <th>Nbr d'achat</th>
                                    <th>Montant total</th>
                                </thead>
                                <tbody>
                                    @foreach ($most_sales_products as $product)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td><a href="{{ back_route('extensions.shop.product.edit', $product) }}">{{ $product->name }}</a></td>
                                        <td>{{ $product->sold_count }}</td>
                                        <td>{{ format_price($product->sold_amount) }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>

                       </div>
                       <div>
                           <h6>Stock des produits faible</h6>
                            <table class="table table-striped">
                                <thead>
                                    <th>#</th>
                                    <th>Nom</th>
                                    <th>Qté stock</th>
                                    <th>Stock Min</th>
                                    <th>Etat</th>
                                </thead>
                                <tbody>
                                    @foreach ($sold_out_products as $product)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $product->name }}</td>
                                        <td>{{ $product->stock }}</td>
                                        <td>{{ $product->safety_stock }}</td>
                                        <td><span class="alert alert-danger">Rupture de stock</span></td>
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
</div>

<x-administrable::datatable />
@include('back.partials._deleteAll')
@endsection
