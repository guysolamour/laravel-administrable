@extends(back_view_path('layouts.base'))

@section('title', 'Publicités')

@section('content')
<div class="row mb-5">
    <div class="col-12">
        <div class="d-flex justify-content-between">
            <ol class="breadcrumb breadcrumb-arrows" aria-label="breadcrumbs">
                <li class="breadcrumb-item"><a href="{{ route(config('administrable.guard') . '.dashboard') }}">Tableau de bord</a></li>
                <li class="breadcrumb-item active"><a href="#">Publicités</a></li>
            </ol>

            <a href="{{ back_route('extensions.ads.ad.create') }}" class="btn btn-success">
                <i class="fa fa-plus"></i>&nbsp; Ajouter</a>
        </div>
    </div>
</div>


<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between mb-3">
            <h3> Publicités </h3>
            <a href="#" class="btn btn-danger d-none" data-model="{{ config('administrable.extensions.ad.models.ad') }}" id="delete-all">
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
                <th>Code</th>
                <th>Nom</th>
                <th>Lien</th>
                <th>Groupe</th>
                <th>Date de début</th>
                <th>Date de fin</th>
                <th>Actions</th>
            </thead>
            <tbody>
                @foreach($ads as $ad)
                <tr class="tr-shadow">
                    <td></td>
                    <td>
                        <label class="form-check" for="check-{{ $ad->id }}">
                            <input class="form-check-input" type="checkbox" data-check data-id="{{ $ad->id }}"
                                id="check-{{ $ad->id }}"> <span class="form-check-label"></span>
                        </label>
                    </td>
                     <td>{{ $ad->getKey() }}</td>
                    <td>{{ $ad->name }}</td>
                    <td>{{ $ad->link ?? '-' }}</td>
                    <td>{{ $ad->group->name ?? '-' }}</td>

                    <td>{{ format_date($ad->started_at) }}</td>
                    <td>{{ format_date($ad->ended_at) }}</td>
                    <td>
                        <div class="btn-group" role="group">

                            <a href="{{ back_route('model.clone', get_clone_model_params($ad)) }}" class="btn btn-secondary"
                            title="Cloner"><i class="fas fa-clone"></i></a>

                            <a href="{{ back_route('extensions.ads.ad.edit', $ad) }}" class="btn btn-info"
                            title="Editer"><i class="fas fa-edit"></i></a>

                            <a href="{{ back_route('extensions.ads.ad.destroy', $ad) }}" data-method="delete"
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
@deleteall()

@endsection
