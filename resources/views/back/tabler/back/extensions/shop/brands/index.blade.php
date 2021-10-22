@extends(back_view_path('layouts.base'))

@section('title', 'Marques')

@section('content')


<div class="row mb-5">
    <div class="col-12">
        <div class="d-flex justify-content-between">
            <ol class="breadcrumb breadcrumb-arrows" aria-label="breadcrumbs">
                <li class="breadcrumb-item"><a href="{{ route(config('administrable.guard') . '.dashboard') }}">Tableau de bord</a></li>
                <li class="breadcrumb-item"><a href="{{ back_route('extensions.shop.statistic.index') }}">Boutique</a></a></li>
                <li class="breadcrumb-item active"><a href="#">Marques</a></li>
            </ol>

            <a href="{{ back_route('extensions.shop.brand.create') }}" class="btn btn-success">
                <i class="fa fa-plus"></i>&nbsp; Ajouter</a>
        </div>
    </div>
</div>


<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between mb-3">
            <h3> Marques </h3>
            <a href="#" class="btn btn-danger d-none" data-model="{{ config('administrable.extensions.shop.models.brand') }}" id="delete-all">
            <i class="fa fa-trash"></i> Tous supprimer</a>
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
                <th>Nom</th>
                <th>Description</th>
                <th>Actions</th>

            </thead>
            <tbody>
                @foreach($brands as $brand)
                <tr class="tr-shadow">
                    <td></td>
                    <td>
                        <label class="form-check" for="check-{{ $brand->id }}">
                            <input class="form-check-input" type="checkbox" data-check data-id="{{ $brand->id }}"
                                id="check-{{ $brand->id }}"> <span class="form-check-label"></span>
                        </label>
                    </td>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $brand->name }}</td>
                    <td>{{ Str::limit($brand->description,50) }}</td>
                    <td>
                        <div class="btn-group" role="group">
                            <a href="{{ back_route('extensions.shop.brand.show', $brand) }}" class="btn btn-primary"
                            title="Afficher"><i class="fas fa-eye"></i></a>

                            {{-- <a href="{{ back_route('model.clone', get_clone_model_params($brand)) }}" class="btn btn-secondary"
                            title="Cloner"><i class="fas fa-clone"></i></a> --}}

                            <a href="{{ back_route('extensions.shop.brand.edit', $brand) }}" class="btn btn-info"
                            title="Editer"><i class="fas fa-edit"></i></a>

                            <a href="{{ back_route('extensions.shop.brand.destroy',$brand) }}" data-method="delete"
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


<x-administrable::datatable />
@include(back_view_path('partials._deleteAll'))

@endsection
