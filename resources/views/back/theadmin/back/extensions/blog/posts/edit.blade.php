@extends(back_view_path('layouts.base'))

@section('title','Edition ' . $post->title)



@section('content')
<div class="main-content">
    <div class="card ">
        <nav aria-label="breadcrumb" class="d-flex justify-content-end" style="padding-top:10px;padding-right:20px;">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route(config('administrable.guard') . '.dashboard') }}">Tableau de bord</a></li>
                <li class="breadcrumb-item"><a href="{{ back_route('extensions.blog.post.index') }}">Articles</a></li>
                <li class="breadcrumb-item"><a href="{{ back_route('extensions.blog.post.show', $post) }}">{{ $post->title }}</a></li>
                <li class="breadcrumb-item active">Edition</li>
            </ol>
        </nav>

    </div>

    <div class="card">
        <h4 class="card-title">
            Edition
        </h4>

        <div class="card-body">
            @include(back_view_path('extensions.blog.posts._form'))
        </div>
    </div>
</div>
@endsection
