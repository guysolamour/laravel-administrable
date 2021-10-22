@extends(back_view_path('layouts.base'))

@section('title', $category->name)

@section('content')
<div class="main-content">
    <div class="card ">
        <nav aria-label="breadcrumb" class="d-flex justify-content-end" style="padding-top:10px;padding-right:20px;">
            <ol class="breadcrumb">
                 <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route(config('administrable.guard') . '.dashboard') }}">Tableau de bord</a></li>
                    <li class="breadcrumb-item"><a href="{{ back_route('extensions.shop.statistic.index') }}">Boutique</a></li>
                    <li class="breadcrumb-item"><a href="{{ back_route('extensions.shop.category.index') }}">Catégories</a></li>
                    <li class="breadcrumb-item active"><a href="{{ back_route('extensions.shop.category.show', $category) }}">{{ $category->name }}</a></li>
                  </ol>
            </ol>
        </nav>

    </div>

    <div class="card">
        {{-- <h4 class="card-title">
                Categories
            </h4> --}}

        <div class="card-body">
            <div class="row">
                <div class="col-md-12">
                    <div class="btn-group float-right">
                        <a href="{{ back_route('extensions.shop.category.edit', $category) }}" class="btn btn-primary">
                            <i class="fas fa-edit"></i> Editer</a>


                        <a href="{{ back_route('extensions.shop.category.destroy', $category) }}" class="btn btn-danger" data-method="delete"
                        data-confirm="Etes vous sûr de bien vouloir procéder à la suppression ?">
                        <i class="fas fa-trash"></i> Supprimer</a>
                    </div>
                </div>
                <div class="col-md-8">
                    <p class="pb-2"><b>Nom: </b>{{ $category->name }}</p>
                    <p class="pb-2"><b>Description: </b>{{ $category->description }}</p>
                    <p class="pb-2"><b>Date ajout: </b>{{ $category->created_at->format('d/m/Y h:i') }}</p>

                    @if($category->children->isNotEmpty())
                    <p class="pb-2"><b>Enfants: </b>{{ $category->children->implode('name', ', ') }}</p>
                    @endif
                </div>
                <div class="col-md-4">
                    @filemanagerShow([
                        'collection' => 'front-image',
                        'model'      =>  $category,
                        'label'      =>  'Image mise en avant',
                    ])
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
