@extends(back_view_path('layouts.base'))

@section('title', 'Edition ' . $group->name)

@section('content')

<div class="row mb-5">
    <div class="col-12">
        <div class="d-flex justify-content-between">
            <ol class="breadcrumb breadcrumb-arrows" aria-label="breadcrumbs">
                <li class="breadcrumb-item"><a href="{{ route(Str::lower(config('administrable.guard')) . '.dashboard') }}">Tableau de bord</a></li>
                <li class="breadcrumb-item"><a href="{{ back_route('extensions.ads.ad.index') }}">Publicités</a></li>
                <li class="breadcrumb-item"><a href="{{ back_route('extensions.ads.group.index') }}">Groupes</a></li>
                <li class="breadcrumb-item"><a href="#">{{ $group->name }}</a></li>
                <li class="breadcrumb-item active">Edition</li>
            </ol>

            <div class="btn-group">
                <a href="{{ back_route('extensions.ads.group.destroy', $group) }}" class="btn btn-danger" data-method="delete"
                data-confirm="Etes vous sûr de bien vouloir procéder à la suppression ?">
                <i class="fas fa-trash"></i>&nbsp; Supprimer </a>
            </div>

        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <h3 class="title-5 m-b-35">
            Edition
        </h3>
        <div class="row">
            <div class='col-md-12'>
                @includeback(['view' => 'extensions.ad.groups._form'])
            </div>
        </div>
    </div>
</div>
@endsection



