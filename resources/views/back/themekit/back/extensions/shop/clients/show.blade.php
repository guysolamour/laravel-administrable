@extends(back_view_path('layouts.base'))

@section('title', $user->name)


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

                            <li class="breadcrumb-item">
                                <a href="{{  back_route('extensions.shop.user.index') }}">Clients</a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">{{ $user->name }}</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <h3 class="card-title">Client</h3>
                        <div class="btn-group float-right">
                            <a href="{{ back_route('extensions.shop.user.edit', $user) }}" class="btn btn-primary">
                                <i class="fas fa-edit"></i> Editer</a>
                        </div>
                    </div>

                    <div class="card-body row">
                        <div class="col-md-8">
                            {{-- add fields here --}}
                            <div class="pb-2">
                                <p><span class="font-weight-bold">Nom:</span></p>
                                <p>
                                    {{ $user->name }}
                                </p>
                            </div>

                              <div class="pb-2">
                                <p><span class="font-weight-bold">Numéro de téléphone:</span></p>
                                <p>
                                    {{ $user->phone_number }}
                                </p>
                            </div>

                            <div class="pb-2">
                                <p><span class="font-weight-bold">Email:</span></p>
                                <p>
                                    {{ $user->email }}
                                </p>
                            </div>

                            <div class="pb-2">
                                <p><span class="font-weight-bold">Total achat:</span></p>
                                <p>
                                    {{ format_price($user->orders->sum('total')) }}
                                </p>
                            </div>

                            <table class="table table-stripped">
                                <thead>
                                    <th></th>
                                    <th>Montant</th>
                                    <th>Livraison</th>
                                    <th>Total</th>
                                    <th>Date de paiement</th>
                                    <th>Actions</th>
                                </thead>
                                <tbody>
                                    @foreach ($user->orders as $order)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ format_price($order->amount) }}</td>
                                        <td>{{ format_price($order->deliver_price) }}</td>
                                        <td>{{ format_price($order->total) }}</td>
                                        <td>{{ format_date($order->created_at) }}</td>
                                        <td>
                                            <div class="btn-group">
                                                <a href="{{ route('back.extensions.shop.command.edit', $order->command) }}" class="btn btn-success btn-sm"><i class="fa fa-eye"></i> Commande</a>
                                                <a href="{{ Storage::url($order->invoice) }}" target="_blank" class="btn btn-secondary btn-sm"><i class="fa fa-eye"></i> Facture</a>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>


                        </div>
                        <div class="col-md-4">
                            @filemanagerShow([
                                    'model'      => $user,
                                    'collection' => 'front-image',
                                    'label'      => 'Photo de profil'
                                ])

                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
