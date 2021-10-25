@extends(back_view_path('layouts.base'))

@section('title', 'Groupes')

@section('content')
<div class="main-content">
    <div class="card ">
        <nav aria-label="breadcrumb" class="d-flex justify-content-end" style="padding-top:10px;padding-right:20px;">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route(Str::lower(config('administrable.guard')) . '.dashboard') }}">Tableau de bord</a></li>
                <li class="breadcrumb-item active" aria-current="page">Groupes</li>
            </ol>
        </nav>

    </div>

    <div class="card">
        {{-- <h4 class="card-title">
                Groupes
            </h4> --}}

        <div class="card-body">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title h3">
                        Groupes
                    </h5>
                    <div class="btn-group">
                        <a href="{{ back_route('extensions.ads.group.create') }}"
                            class="btn btn-sm btn-label btn-round btn-primary"><label><i class="fa fa-plus"></i></label>
                            Ajouter</a>
                        <a href="#" data-model="{{ config('administrable.extensions.ad.models.group') }}" id="delete-all" class="btn btn-sm btn-label btn-round btn-danger d-none"><label><i
                                    class="fa fa-trash"></i></label> Tous Supprimer</a>

                    </div>
                </div>

                <table class="table table-hover table-has-action" id='list'>
                    <thead>
                        <tr>
                            <th>
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="check-all">
                                    <label class="form-check-label" for="check-all"></label>
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
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($groups as $group)
                        <tr>
                            <td>
                                <div class="form-check">
                                    <input type="checkbox" data-check class="form-check-input" data-id="{{ $group->id }}"
                                        id="check-{{ $group->id }}">
                                    <label class="form-check-label" for="check-{{ $group->id }}"></label>
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
                               <nav class="nav no-gutters gap-2 fs-16">

                                    <a class="nav-link hover-primary" href="{{ back_route('model.clone', get_clone_model_params($group)) }}" data-provide="tooltip"
                                        title="Cloner"><i class="ti-layers"></i></a>

                                    <a class="nav-link hover-primary" href="{{ back_route('extensions.ads.group.edit', $group) }}"
                                        data-provide="tooltip" title="Editer"><i
                                            class="ti-pencil"></i></a>
                                    <a class="nav-link hover-danger" href="{{ back_route('extensions.ads.group.destroy', $group) }}" data-provide="tooltip" title=""
                                        data-method="delete"
                                        data-confirm="Etes vous sûr de bien vouloir procéder à la suppression ?"
                                        title='Supprimer'
                                        data-original-title="Supprimer"><i class="ti-close"></i></a>
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
@deleteall

@endsection
