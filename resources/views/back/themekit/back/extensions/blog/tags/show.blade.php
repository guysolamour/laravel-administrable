@extends(back_view_path('layouts.base'))


@section('title', $tag->name)


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
                           <li class="breadcrumb-item"><a href="{{ back_route('extensions.blog.tag.index') }}">Etiquettes</a></li>
                            <li class="breadcrumb-item active" aria-current="page"><a href="#">{{ $tag->name }}</a></li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <h3 class="card-title">Catégories</h3>
                        <div class="btn-group float-right">
                           <a href="{{ back_route('extensions.blog.tag.edit', $tag) }}" class="btn btn-primary">
                            <i class="fas fa-edit"></i> Editer</a>
                        <a href="{{ back_route('extensions.blog.tag.destroy', $tag) }}" class="btn btn-danger" data-method="delete"
                            data-confirm="Etes vous sûr de bien vouloir procéder à la suppression ?">
                            <i class="fas fa-trash"></i> Supprimer</a>
                        </div>
                    </div>

                    <div class="card-body row">
                        <div class="col-md-8">
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
                        <div class="col-md-4">
                            @include(back_view_path('media._show'), [
                              'model' => $tag,
                            ])
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection