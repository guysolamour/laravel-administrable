@extends(back_view_path('layouts.base'))


@section('title', 'Coupons')


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
                        <li class="breadcrumb-item active"><a href="#">Coupons</a></li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <h3 class="card-title">Coupons</h3>
                    <div class="btn-group float-right">
                        <a href="{{ back_route('extensions.shop.coupon.create') }}" class="btn  btn-primary"> <i
                                class="fa fa-plus"></i> Ajouter</a>



                        <a href="#" class="btn btn-danger d-none" data-model="{{ config('administrable.extensions.shop.models.coupon') }}" id="delete-all">
                            <i class="fa fa-trash"></i> Tous supprimer</a>
                    </div>
                </div>

                <div class="card-body">
                    <table class="table table-vcenter card-table" id='list'>
                        <thead>
                            <th>
                                <div class="checkbox-fade fade-in-success ">
                                    <label for="check-all">
                                        <input type="checkbox" value=""  id="check-all">
                                        <span class="cr">
                                            <i class="cr-icon ik ik-check txt-success"></i>
                                        </span>
                                    </label>
                                </div>
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
                                <td>
                                    <div class="checkbox-fade fade-in-success ">
                                        <label for="check-{{ $coupon->id }}">
                                            <input type="checkbox" data-check data-id="{{ $coupon->id }}"  id="check-{{ $coupon->id }}">
                                            <span class="cr">
                                                <i class="cr-icon ik ik-check txt-success"></i>
                                            </span>
                                            {{-- <span>Default</span> --}}
                                        </label>
                                    </div>
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
                                            title="Editer"><i class="fas fa-edit"></i></a>

                                        <a href="{{ back_route('extensions.shop.coupon.destroy',$coupon) }}" data-method="delete"
                                            data-confirm="Etes vous sûr de bien vouloir procéder à la suppression ?" class="btn btn-danger"
                                            title="Supprimer"><i class="fas fa-trash"></i></a>

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
