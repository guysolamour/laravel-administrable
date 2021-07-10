@extends(back_view_path('layouts.base'))

@section('title','Edition ' . $post->title)


@section('content')
<div class="row mb-5">
    <div class="col-12">
        <div class="d-flex justify-content-between">
            <ol class="breadcrumb breadcrumb-arrows" aria-label="breadcrumbs">
                <li class="breadcrumb-item"><a href="{{ route(config('administrable.guard') . '.dashboard') }}">Tableau de bord</a></li>
                <li class="breadcrumb-item"><a href="{{ back_route('extensions.blog.post.index') }}">Articles</a></li>
                <li class="breadcrumb-item"><a
                        href="{{ back_route('extensions.blog.post.show', $post) }}">{{ $post->title }}</a></li>
                <li class="breadcrumb-item active" aria-current="page"><a href="#">Edition</a></li>
            </ol>

            <div class="btn-group">
                <a href="{{ back_route('extensions.blog.post.destroy', $post) }}" class="btn btn-danger"
                    data-method="delete" data-confirm="Etes vous sûr de bien vouloir procéder à la suppression ?">
                    <i class="fas fa-trash"></i>&nbsp; Supprimer</a>
            </div>
        </div>
    </div>
</div>


<div class="row">
    <div class="col-12">
        <h3 class="title-5 m-b-35">
            Edition
        </h3>
        @include(back_view_path('extensions.blog.posts._form'))
    </div>
</div>
@endsection

