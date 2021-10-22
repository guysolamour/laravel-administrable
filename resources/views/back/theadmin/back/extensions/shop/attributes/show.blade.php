@extends(back_view_path('layouts.base'))

@section('title', $attribute->name)

@section('content')
<div class="main-content">
    <div class="card ">
        <nav aria-label="breadcrumb" class="d-flex justify-content-end" style="padding-top:10px;padding-right:20px;">
            <ol class="breadcrumb">
                 <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route(config('administrable.guard') . '.dashboard') }}">Tableau de bord</a></li>
                    <li class="breadcrumb-item"><a href="{{ back_route('extensions.shop.statistic.index') }}">Boutique</a></li>

                    <li class="breadcrumb-item"><a href="{{ back_route('extensions.shop.attribute.index') }}">Attributs</a></li>
                    <li class="breadcrumb-item active"><a href="{{ back_route('extensions.shop.attribute.show', $attribute) }}">{{ $attribute->name }}</a></li>
                </ol>
            </ol>
        </nav>
    </div>

    <div class="card">
        {{-- <h4 class="card-title">
                Attributs
            </h4> --}}

        <div class="card-body">
            <div class="row">
                <div class="col-md-12">
                    <div class="btn-group float-right">
                        <a href="{{ back_route('extensions.shop.attribute.edit', $attribute) }}" class="btn btn-primary">
                            <i class="fas fa-edit"></i> Editer</a>

                        <a href="{{ back_route('extensions.shop.attribute.destroy', $attribute) }}" class="btn btn-danger" data-method="delete"
                        data-confirm="Etes vous sûr de bien vouloir procéder à la suppression ?">
                        <i class="fas fa-trash"></i> Supprimer</a>
                    </div>
                </div>
                <div class="col-md-12">
                    <p class="pb-2"><b>Nom: </b>{{ $attribute->name }}</p>
                    <p class="pb-2"><b>Description: </b>{{ $attribute->description }}</p>
                    <p class="pb-2"><b>Date ajout: </b>{{ $attribute->created_at->format('d/m/Y h:i') }}</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
