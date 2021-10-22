@extends(back_view_path('layouts.base'))



@section('title', 'Coupons')

@section('content')


<div class="row mb-5">
    <div class="col-12">
        <div class="d-flex justify-content-between">
            <ol class="breadcrumb breadcrumb-arrows" aria-label="breadcrumbs">
                <li class="breadcrumb-item"><a href="{{ route(config('administrable.guard') . '.dashboard') }}">Tableau de bord</a></li>
                <li class="breadcrumb-item"><a href="{{ back_route('extensions.shop.statistic.index') }}">Boutique</a></a></li>
                <li class="breadcrumb-item active"><a href="#">Coupons</a></li>
            </ol>

            <a href="{{ back_route('extensions.shop.coupon.create') }}" class="btn btn-success">
                <i class="fa fa-plus"></i>&nbsp; Ajouter</a>
        </div>
    </div>
</div>


<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between mb-3">
            <h3> Zones de livraison </h3>

            <a href="#" class="btn btn-danger d-none" data-model="{{ config('administrable.extensions.shop.models.coupon') }}" id="delete-all">
                <i class="fa fa-trash"></i> &nbsp; Tous supprimer
            </a>
        </div>

        <table class="table table-vcenter card-table" id='list'>
            <thead>
                <th></th>
                <th>
                    <label class="form-check" for="check-all">
                        <input class="form-check-input" type="checkbox" id="check-all">
                        <span class="form-check-label"></span>
                    </label>
                </th>
                <th>#</th>
                <th>Code</th>
                <th>Type de remise</th>
                <th>Valeur</th>
                <th>Utilisé</th>
                <th>Expire le</th>
                <th>Actions</th>

            </thead>
            <tbody>
                @foreach($coupons as $coupon)
                <tr class="tr-shadow">
                    <td></td>
                    <td>
                        <label class="form-check" for="check-{{ $coupon->id }}">
                            <input class="form-check-input" type="checkbox" data-check data-id="{{ $coupon->id }}"
                                id="check-{{ $coupon->id }}"> <span class="form-check-label"></span>
                        </label>
                    </td>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $coupon->code }}</td>
                    <td>{{ Str::ucfirst($coupon->remise_type_label) }}</td>
                    <td>{{ $coupon->formated_value }}</td>
                    <td>{{ $coupon->used_count }}</td>
                    <td>{{ $coupon->expires_at?->format('d/m/y h:i') }}</td>
                    <td>
                        <div class="btn-group" role="group">
                            <a href="{{ back_route('extensions.shop.coupon.edit', $coupon) }}" class="btn btn-info"
                                title="Editer"><i class="fas fa-edit"></i> &nbsp;</a>

                            <a href="{{ back_route('extensions.shop.coupon.destroy', $coupon) }}" data-method="delete"
                                data-confirm="Etes vous sûr de bien vouloir procéder à la suppression ?" class="btn btn-danger"
                                title="Supprimer"><i class="fas fa-trash"></i> &nbsp;</a>

                        </div>
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
