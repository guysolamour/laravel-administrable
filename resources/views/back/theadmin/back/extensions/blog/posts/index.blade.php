@extends(back_view_path('layouts.base'))

@section('title','Articles')

@section('content')
<div class="main-content">
    <div class="card ">
        <nav aria-label="breadcrumb" class="d-flex justify-content-end" style="padding-top:10px;padding-right:20px;">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route(config('administrable.guard') . '.dashboard') }}">Tableau de bord</a></li>
                <li class="breadcrumb-item active">Articles</li>
            </ol>
        </nav>

    </div>

    <div class="card">
        {{-- <h4 class="card-title">
                Articles
            </h4> --}}

        <div class="card-body">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title h3">
                        Articles
                    </h5>
                    <div class="btn-group">
                        <a href="{{ back_route('extensions.blog.post.create') }}"
                            class="btn btn-sm btn-label btn-round btn-primary"><label><i class="fa fa-plus"></i></label>
                            Ajouter</a>
                        <a href="#" data-model="{{ config('administrable.extensions.blog.post.model') }}" id="delete-all"
                            class="btn btn-sm btn-label btn-round btn-danger d-none"><label><i class="fa fa-trash"></i></label> Tous
                            Supprimer</a>

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
                            <th></th>
                            <th>Titre</th>
                            <th>Contenu</th>
                            <th>Status</th>
                            <th>Catégories</th>
                            <th>Etiquettes</th>
                            <th>Date ajout</th>
                            {{-- add fields here --}}

                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($posts as $post)
                        <tr>
                            <td>
                                <div class="form-check">
                                    <input type="checkbox" data-check class="form-check-input" data-id="{{ $post->id }}"
                                        id="check-{{ $post->id }}">
                                    <label class="form-check-label" for="check-{{ $post->id }}"></label>
                                </div>
                            </td>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $post->title }}</td>
                            <td>{!! Str::limit(strip_tags($post->content),50) !!}</td>
                            <td>
                                @if ($post->isOnline())
                                <a data-provide="tooltip" title="En ligne"><i class="fas fa-circle text-success"></i></a>
                                @else
                                <a data-provide="tooltip" title="Hors ligne"><i class="fas fa-circle text-secondary"></i></a>
                                @endif
                            </td>
                            {{-- <td>
                                <a href="" class="bdage badge-dark badge-pill p-2">Santé</a>
                            </td> --}}
                            <td>
                                @forelse ($post->categories as $category)
                                    <a href="{{ back_route('extensions.blog.category.show', $category) }}"
                                        class=" bg-azure p-2">{{ $category->name }}</a>
                                @empty
                                    -
                                @endforelse
                            </td>
                            <td>
                                @forelse ($post->tags as $tag)
                                    <a href="{{ back_route('extensions.blog.tag.show', $tag) }}"
                                        class=" bg-azure p-2">{{ $tag->name }}</a>
                                @empty
                                    -
                                @endforelse
                            </td>
                            <td>{{ $post->created_at?->format('d/m/Y h:i') }}</td>
                            {{-- add values here --}}

                            <td>
                                <nav class="nav no-gutters gap-2 fs-16">
                                    <a class="nav-link hover-primary" href="{{ back_route('extensions.blog.post.show', $post) }}" data-provide="tooltip"
                                        title="Afficher"><i class="ti-eye"></i></a>
                                    <a class="nav-link hover-primary" href="{{ back_route('model.clone', get_clone_model_params($post)) }}" data-provide="tooltip"
                                        title="Cloner"><i class="ti-layers"></i></a>
                                    <a class="nav-link hover-primary" href="{{ back_route('extensions.blog.post.edit', $post) }}" data-provide="tooltip"
                                        title="Editer"><i class="ti-pencil"></i></a>
                                    <a class="nav-link hover-danger" href="{{ back_route('extensions.blog.post.destroy',$post) }}" data-provide="tooltip"
                                    data-method="delete"
                                    data-confirm="Etes vous sûr de bien vouloir procéder à la suppression ?"
                                        title="Supprimer" data-original-title="Supprimer"><i class="ti-close"></i></a>
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
@include(back_view_path('partials._deleteAll'))

@endsection
