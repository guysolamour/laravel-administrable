@extends(back_view_path('layouts.base'))

@section('title', $tag->name)

@section('content')

<div class="row mb-5">
    <div class="col-12">
        <div class="d-flex justify-content-between">
            <ol class="breadcrumb breadcrumb-arrows" aria-label="breadcrumbs">
                <li class="breadcrumb-item"><a href="{{ route(config('administrable.guard') . '.dashboard') }}">Tableau de bord</a></li>
                <li class="breadcrumb-item"><a href="{{ back_route('extensions.blog.tag.index') }}">Etiquettes</a></li>
                <li class="breadcrumb-item active" aria-current="page"><a href="#">{{ $tag->name }}</a></li>
            </ol>

            <div class="btn-group">
                <a href="{{ back_route('extensions.blog.tag.edit', $tag) }}" class="btn btn-primary">
                    <i class="fas fa-edit"></i>&nbsp;  Editer</a>
                <a href="{{ back_route('extensions.blog.tag.destroy', $tag) }}" class="btn btn-danger" data-method="delete"
                    data-confirm="Etes vous sûr de bien vouloir procéder à la suppression ?">
                    <i class="fas fa-trash"></i>&nbsp;  Supprimer</a>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <h3 class="title-5 m-b-35">
            Articles
        </h3>
        <div class="row">
            <div class="col-md-12">
                {{-- add fields here --}}
                <div class="pb-2">
                    <p><span class="font-weight-bold">Nom:</span></p>
                    <p>
                        {{ $tag->name }}
                    </p>
                </div>

                <div class="pb-2">
                    <p><span class="font-weight-bold">Date ajout:</span></p>
                    <p>
                        {{ $tag->created_at->format('d/m/Y h:i') }}
                    </p>
                </div>
            </div>

        </div>
    </div>
</div>

@endsection
