@extends(back_view_path('layouts.base'))


@section('title', 'Ajout')


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
                            <li class="breadcrumb-item"><a href="{{ back_route('extensions.ads.ad.index') }}">Publicités</a></li>
                            <li class="breadcrumb-item active" aria-current="page"><a href="#">Ajout</a></li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <h3 class="card-title">Ajout</h3>
                    </div>

                    <div class="card-body">
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
            </div>

        </div>
    </div>
</div>
@endsection
