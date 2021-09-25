@extends('back.layouts.base')


@section('title','Commande ' . $command->reference)

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
                            <li class="breadcrumb-item">
                                <a href="{{ back_route('extensions.shop.statistic.index') }}">Boutique</a>
                            </li>
                            <li class="breadcrumb-item">
                                <a href="{{ back_route('extensions.shop.command.index') }}">Commandes</a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">{{ $command->reference }}</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <h3 class="card-title">Edition de commande</h3>
                        <div class="btn-group float-right">
                            @if($command->isNotPaid() || $command->isNotCompleted())
                            <a href="{{ back_route('extensions.shop.command.destroy', $command) }}" class="btn btn-danger" data-method="delete"
                                data-confirm="Etes vous sûr de bien vouloir procéder à la suppression ?">
                                <i class="fas fa-trash"></i> Supprimer</a>
                            @endif
                        </div>
                    </div>

                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12">
                                @include(back_view_path('extensions.shop.commands._form'), ['edit' => true])
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection





