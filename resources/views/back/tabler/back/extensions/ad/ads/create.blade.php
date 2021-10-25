@extends(back_view_path('layouts.base'))

@section('title', 'Ajout')

@section('content')

<div class="row mb-5">
    <div class="col-12">
        <div class="d-flex justify-content-between">
            <ol class="breadcrumb breadcrumb-arrows" aria-label="breadcrumbs">
                <li class="breadcrumb-item"><a href="{{ route(config('administrable.guard') . '.dashboard') }}">Tableau de bord</a></li>
                <li class="breadcrumb-item"><a href="{{ back_route('extensions.ads.ad.index') }}">Publicités</a></li>
                <li class="breadcrumb-item active" aria-current="page"><a href="#">Ajout</a></li>
            </ol>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <h3 class="title-5 m-b-35">
            Ajout
        </h3>
        <div class="row">
            <div class='col-md-8'>
                @includeback(['view' => 'extensions.ad.ads._form'])
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
@endsection
