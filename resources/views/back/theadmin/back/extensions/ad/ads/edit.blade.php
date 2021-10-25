@extends(back_view_path('layouts.base'))


@section('title', 'Edition ' . $ad->name)

@section('content')
<div class="main-content">
    <div class="card ">
        <nav aria-label="breadcrumb" class="d-flex justify-content-end" style="padding-top:10px;padding-right:20px;">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route(config('administrable.guard') . '.dashboard') }}">Tableau de bord</a></li>
                <li class="breadcrumb-item"><a href="{{ back_route('extensions.ads.ad.index') }}">Publicités</a></li>
                <li class="breadcrumb-item"><a href="#">{{ $ad->name }}</a></li>
                <li class="breadcrumb-item active" aria-current="page"><a href="#">Edition</a></li>
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
                    <div class="mb-4 btn-group float-right">
                        <a href="{{ back_route('extensions.ads.ad.destroy', $ad) }}" class="btn btn-danger" data-method="delete"
                        data-confirm="Etes vous sûr de bien vouloir procéder à la suppression ?">
                        <i class="fas fa-trash"></i> Supprimer</a>
                    </div>
                </div>
                <div class='col-md-8'>
                    @includeback(['view' => 'extensions.ad.ads._form', 'edit' => true])
                </div>
                <div class="col-md-4">
                    @imagemanagerfront([
                        'label'      => 'Image mise en avant',
                        'model'      => $form->getModel(),
                    ])
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
