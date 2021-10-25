@extends(back_view_path('layouts.base'))

@section('title', 'Groupes')

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
                            <li class="breadcrumb-item"><a href="{{ back_route('extensions.ads.ad.index') }}">Publicités</a></li>
                            <li class="breadcrumb-item active">Groupes</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <h3 class="card-title">Groupes</h3>
                        <div class="btn-group float-right">
                            <a href="{{ back_route('extensions.ads.group.create') }}" class="btn  btn-primary"> <i
                                class="fa fa-plus"></i> Ajouter</a>



                                <a href="#" class="btn btn-danger d-none" data-model="{{ config('administrable.extensions.ad.models.group') }}" id="delete-all">
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
                                        <th>Code</th>
                                        <th>Nom</th>
                                        <th>Type</th>
                                        <th>Pubs visible</th>
                                        <th>Largeur</th>
                                        <th>Hauteur</th>
                                        <th>Date ajout</th>
                                        <th>Actions</th>
                                    </thead>
                                    <tbody>
                                        @foreach($groups as $group)
                                        <tr class="tr-shadow">
                                            <td>
                                                <div class="checkbox-fade fade-in-success ">
                                                    <label for="check-{{ $group->id }}">
                                                        <input type="checkbox" data-check data-id="{{ $group->id }}"  id="check-{{ $group->id }}">
                                                        <span class="cr">
                                                            <i class="cr-icon ik ik-check txt-success"></i>
                                                        </span>
                                                        {{-- <span>Default</span> --}}
                                                    </label>
                                                </div>
                                            </td>
                                            <td>{{ $group->getKey() }}</td>
                                            <td>{{ $group->name }}</td>
                                            <td>
                                                {{ $group->type->label }}
                                            </td>
                                            <td>{{ $group->visible_ads == 0 ? 'Toutes' : $group->visible_ads }}</td>
                                            <td>{{ $group->width }}</td>
                                            <td>{{ $group->height }}</td>

                                            <td>{{ $group->created_at->format('d/m/Y h:i') }}</td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ back_route('model.clone', get_clone_model_params($group)) }}" class="btn btn-secondary"
                                                    title="Cloner"><i class="fas fa-clone"></i></a>

                                                    <a href="{{ back_route('extensions.ads.group.edit', $group) }}" class="btn btn-info"
                                                    title="Editer"><i class="fas fa-edit"></i></a>

                                                    <a href="{{ back_route('extensions.ads.group.destroy', $group) }}" data-method="delete"
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
@deleteall
@endsection
