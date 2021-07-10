@extends(back_view_path('layouts.base'))

@section('title', Lang::get('administrable::messages.default.add'))

@section('content')
<div class="main-content">
    <div class="card ">
        <nav aria-label="breadcrumb" class="d-flex justify-content-end" style="padding-top:10px;padding-right:20px;">
            <ol class="breadcrumb breadcrumb-arrow">
                <li class="breadcrumb-item"><a href="{{ route(config('administrable.guard') . '.dashboard') }}">{{ Lang::get("administrable::messages.default.dashboard") }}</a></li>
                <li class="breadcrumb-item"><a href="{{ back_route( config('administrable.guard') . '.index') }}">{{ Lang::get('administrable::messages.view.guard.plural') }}</a></li>
                <li class="breadcrumb-item active" aria-current="page"><a href="#">{{ Lang::get('administrable::messages.default.add') }}</a></li>
            </ol>
        </nav>
    </div>

    <div class="card">
        <h4 class="card-title">
            {{ Lang::get('administrable::messages.default.add') }}
        </h4>

        <div class="card-body">
            <div class="row">
                <div class="col-md-8">
                    @include(back_view_path('guards._form'))
                </div>
                <div class="col-md-4">
                    @include(back_view_path('media._imagemanager'), [
                        'front_image_label' => Lang::get('administrable::messages.view.guard.avatar'),
                        'model'             => new (AdminModule::getGuardModel()),
                        'front_image'       => true,
                        'back_image'        => false,
                        'images'            => false,
                    ])
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
