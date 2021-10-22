@extends(back_view_path('layouts.base'))



@section('title', $attribute->name)


@section('content')
<div class="row mb-5">
    <div class="col-12">
        <div class="d-flex justify-content-between">
            <ol class="breadcrumb breadcrumb-arrows" aria-label="breadcrumbs">
                <li class="breadcrumb-item"><a href="{{ route(config('administrable.guard') . '.dashboard') }}">Tableau de bord</a></li>
                <li class="breadcrumb-item"><a href="{{ back_route('extensions.shop.statistic.index') }}">Boutiques</a></li>
                          <li class="breadcrumb-item"><a href="{{ back_route('extensions.shop.attribute.index') }}">Attributs</a></li>
                            <li class="breadcrumb-item active"><a href="{{ back_route('extensions.shop.attribute.show', $attribute) }}">{{ $attribute->name }}</a></li>
            </ol>

            <div class="btn-group">
                <a href="{{ back_route('extensions.shop.attribute.edit', $attribute) }}" class="btn btn-primary">
                    <i class="fas fa-edit"></i>&nbsp;  Editer</a>

                <a href="{{ back_route('extensions.shop.attribute.destroy', $attribute) }}}" class="btn btn-danger" data-method="delete"
                    data-confirm="Etes vous sûr de bien vouloir procéder à la suppression ?">
                    <i class="fas fa-trash"></i> Supprimer</a>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <h3 class="title-5 m-b-35">
            Attributs
        </h3>
        <div class="row">
            <div class="col-md-12">
                <p class="pb-2"><b>Nom: </b>{{ $attribute->name }}</p>
                <p class="pb-2"><b>Description: </b>{{ $attribute->description }}</p>
                <p class="pb-2"><b>Date ajout: </b>{{ $attribute->created_at->format('d/m/Y h:i') }}</p>
            </div>
        </div>
    </div>
</div>

@endsection
