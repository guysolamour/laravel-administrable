@extends(back_view_path('layouts.base'))

@section('title','Edition ' . $category->name)


@section('content')
<div class="main-content">
    <div class="card ">
        <nav aria-label="breadcrumb" class="d-flex justify-content-end" style="padding-top:10px;padding-right:20px;">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route(config('administrable.guard') . '.dashboard') }}">Tableau de bord</a></li>
                <li class="breadcrumb-item"><a href="{{ back_route('extensions.blog.category.index') }}">Catégories</a></li>
                <li class="breadcrumb-item"><a href="{{ back_route('extensions.blog.category.show', $category) }}">{{ $category->name }}</a></li>
                <li class="breadcrumb-item active">Edition</li>
            </ol>
        </nav>


    </div>

    <div class="card">
        <h4 class="card-title">
            Edition
        </h4>

        <div class="card-body">
            <div class="row">
                <div class="col-md-12">
                    @include('extensions.blog.categories._form', ['edit' => true])
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
