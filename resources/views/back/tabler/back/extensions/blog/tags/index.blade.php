@extends(back_view_path('layouts.base'))

@section('title','Etiquettes')

@section('content')
<div class="row mb-5">
    <div class="col-12">
        <div class="d-flex justify-content-between">
            <ol class="breadcrumb breadcrumb-arrows" aria-label="breadcrumbs">
                <li class="breadcrumb-item"><a href="{{ route(config('administrable.guard') . '.dashboard') }}">Tableau de bord</a></li>
                <li class="breadcrumb-item active" aria-current="page"><a href="#">Etiquettes</a></li>
            </ol>

            <a href="{{ back_route('extensions.blog.tag.create') }}" class="btn btn-success">
                <i class="fa fa-plus"></i>&nbsp; Ajouter</a>
        </div>
    </div>
</div>


<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between mb-3">
            <h3> Etiquettes </h3>
            <a href="#" class="btn btn-danger d-none" data-model="{{ config('administrable.extensions.blog.tag.model') }}"
                id="delete-all"><i class="fa fa-trash"></i> &nbsp; Tous supprimer</a>
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

                <th>#</>
                <th>Nom</th>
                <th>Date ajout</th>
                {{-- add fields here --}}

                <th>Actions</th>
            </thead>
            <tbody>
                @foreach($tags as $tag)
                <tr class="tr-shadow">
                    <td></td>
                    <td>
                        <label class="form-check" for="check-{{ $tag->id }}">
                            <input class="form-check-input" type="checkbox" data-check data-id="{{ $tag->id }}"
                                id="check-{{ $tag->id }}" <span class="form-check-label"></span>
                        </label>
                    </td>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $tag->name }}</td>
                    <td>{{ $tag->created_at->format('d/m/Y h:i') }}</td>
                    {{-- add values here --}}
                    <td>

                        <div class="btn-group" role="group">
                            <a href="{{ back_route('extensions.blog.tag.show', $tag) }}" class="btn btn-primary"
                                data-toggle="tooltip" data-placement="top" title="Afficher"><i
                                    class="fas fa-eye"></i></a>

                                <a href="{{ back_route('model.clone', get_clone_model_params($tag)) }}" class="btn btn-secondary" data-toggle="tooltip"
                              data-placement="top" title="Cloner"><i class="fas fa-clone"></i></a>

                            <a href="{{ back_route('extensions.blog.tag.edit', $tag) }}" class="btn btn-info"
                                data-toggle="tooltip" data-placement="top" title="Editer"><i
                                    class="fas fa-edit"></i></a>
                            <a href="{{ back_route('extensions.blog.tag.destroy', $tag) }}" data-method="delete"
                                data-confirm="Etes vous sûr de bien vouloir procéder à la suppression ?"
                                class="btn btn-danger" data-toggle="tooltip" data-placement="top"
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