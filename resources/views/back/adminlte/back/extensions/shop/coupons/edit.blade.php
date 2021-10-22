@extends(back_view_path('layouts.base'))

@section('title', 'Edition coupon ' . $coupon->code)

@section('content')

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                </div>
                <div class="col-sm-6">
                    <div class="float-sm-right">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route(config('administrable.guard') . '.dashboard') }}">Tableau de bord</a></li>
                            <li class="breadcrumb-item"><a href="{{ back_route('extensions.shop.statistic.index') }}">Boutique</a></li>
                            <li class="breadcrumb-item"><a href="{{ back_route('extensions.shop.coupon.index') }}">Coupons</a></li>
                            <li class="breadcrumb-item"><a href="{{ back_route('extensions.shop.coupon.show', $coupon) }}">{{ $coupon->code }}</a></li>
                            <li class="breadcrumb-item active">Edition</li>
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
                <h3 class="card-title">Edition</h3>
                <div class="btn-group float-right">
                    <a href="{{ back_route('extensions.shop.coupon.destroy', $coupon) }}" class="btn btn-danger" data-method="delete"
                        data-confirm="Etes vous sûr de bien vouloir procéder à la suppression ?">
                        <i class="fas fa-trash"></i> Supprimer</a>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-12">
                        @include(back_view_path('extensions.shop.coupons._form'),['edit' => true])
                    </div>
                </div>
            </div>

        </div>
    </section>
</div>
@endsection
