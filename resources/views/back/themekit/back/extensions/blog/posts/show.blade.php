@extends(back_route_path('layouts.base'))


@section('title', $post->title)


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
                            <li class="breadcrumb-item">
                                <a href="{{ back_route('extensions.blog.post.index') }}">Articles</a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">{{ $post->title }}</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <h3 class="card-title">Articles</h3>
                        <div class="btn-group float-right">
                            <a href="{{ back_route('extensions.blog.post.edit', $post) }}" class="btn btn-primary">
                                <i class="fas fa-edit"></i> Editer</a>
                            <a href="{{ back_route('extensions.blog.post.destroy', $post) }}" class="btn btn-danger" data-method="delete"
                                data-confirm="Etes vous sûr de bien vouloir procéder à la suppression ?">
                                <i class="fas fa-trash"></i> Supprimer</a>
                        </div>
                    </div>

                    <div class="card-body row">
                        <div class="col-md-8 row">
                            {{-- add fields here --}}
                            <div class="col-12">
                                <div class="pb-2">
                                    <p><span class="font-weight-bold">Titre:</span></p>
                                    <p>
                                        {{ $post->title }}
                                    </p>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="pb-2">
                                    <p><span class="font-weight-bold">Catégories:</span></p>
                                    <p>
                                        @forelse ($post->categories as $category)
                                            <a href="{{ back_route('extensions.blog.category.show', $category) }}"
                                                class=" bg-azure p-2">{{ $category->name }}</a>
                                        @empty
                                            -
                                        @endforelse
                                    </p>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="pb-2">
                                    <p><span class="font-weight-bold">Etiquettes:</span></p>
                                    <p>
                                        @forelse ($post->tags as $tag)
                                            <a href="{{ back_route('extensions.blog.tag.show', $tag) }}"
                                                class=" bg-azure p-2">{{ $tag->name }}</a>
                                        @empty
                                            -
                                        @endforelse
                                    </p>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="pb-2">
                                    <p><span class="font-weight-bold">Contenu:</span></p>
                                    <p>
                                        {!! $post->content !!}
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-header bg-secondary">
                                    <h3 class="card-title text-white">Publication</h3>
                                </div>
                                <div class="card-body">
                                    <p>En ligne : {{ $post->online ? 'Oui' : 'Non' }}</p>
                                    <p>Autoriser les commentaires : {{ $post->allow_comment ? 'Oui' : 'Non' }}</p>
                                </div>
                            </div>
                            <div class="card">
                                <div class="card-body">
                                    <div class="form-group">
                                        <label for="publish_date">Date de publication:</label>
                                        <p>{{ $post->created_at->format('d/m/Y') }}</p>
                                    </div>
                                    <div class="form-group">
                                        <label for="publish_time">Heure de publication: </label>
                                        <p>{{ $post->created_at->format('h:i:s') }}</p>
                                    </div>
                                </div>
                            </div>
                            @filemanagerShow([
                                'collection' => 'front-image',
                                'label'      => 'Image à la une',
                                'model'      => $post,
                            ])
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection













