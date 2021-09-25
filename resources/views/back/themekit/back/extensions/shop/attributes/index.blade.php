@extends(back_view_path('layouts.base'))


@section('title', 'Attributs')


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
                            <li class="breadcrumb-item active"><a href="{{ back_route('extensions.shop.attribute.index') }}">Attributs</a></li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <h3 class="card-title">Attributs</h3>
                        <div class="btn-group float-right">
                            <a href="{{ back_route('extensions.shop.attribute.create') }}" class="btn  btn-primary"> <i
                                class="fa fa-plus"></i> Ajouter</a>

                                <a href="#" class="btn btn-danger d-none" data-model="{{ config('administrable.extensions.shop.models.attribute') }}" id="delete-all">
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

                                        <th>Nom</th>
                                        <th>valeur</th>
                                        <th>Actions</th>
                                    </thead>
                                    <tbody>
                                        @foreach($attributes as $attribute)
                                        <tr class="tr-shadow">
                                            <td>
                                                <div class="checkbox-fade fade-in-success ">
                                                    <label for="check-{{ $attribute->id }}">
                                                        <input type="checkbox" data-check data-id="{{ $attribute->id }}"  id="check-{{ $attribute->id }}">
                                                        <span class="cr">
                                                            <i class="cr-icon ik ik-check txt-success"></i>
                                                        </span>
                                                        {{-- <span>Default</span> --}}
                                                    </label>
                                                </div>
                                            </td>

                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{$attribute->name }}</td>
                                            <td>{{$attribute->terms_list }}</td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ back_route('extensions.shop.attribute.show', $attribute) }}" class="btn btn-primary"
                                                    title="Afficher"><i class="fas fa-eye"></i></a>

                                                    {{-- <a href="{{ back_route('model.clone', get_clone_model_params($attribute)) }}" class="btn btn-secondary"
                                                    title="Cloner"><i class="fas fa-clone"></i></a> --}}

                                                    <a href="{{ back_route('extensions.shop.attribute.edit', $attribute) }}" class="btn btn-info"
                                                    title="Editer"><i class="fas fa-edit"></i></a>

                                                    <a href="{{ back_route('extensions.shop.attribute.destroy',$attribute) }}" data-method="delete"
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
