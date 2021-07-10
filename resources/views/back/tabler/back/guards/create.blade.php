@extends(back_view_path('layouts.base'))

@section('title', Lang::get('administrable::messages.default.add'))

@section('content')
<div class="row mb-5">
    <div class="col-12">
        <div class="d-flex justify-content-between">
            <ol class="breadcrumb breadcrumb-arrows" aria-label="breadcrumbs">
                <li class="breadcrumb-item"><a href="{{ route(config('administrable.guard') . '.dashboard') }}">{{ Lang::get("administrable::messages.default.dashboard") }}</a></li>
                <li class="breadcrumb-item"><a href="{{ back_route( config('administrable.guard') . '.index') }}">{{ Lang::get('administrable::messages.view.guard.plural') }}</a></li>
                <li class="breadcrumb-item active" aria-current="page"><a href="#">{{ Lang::get('administrable::messages.default.add') }}</a></li>
            </ol>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <h3 class="title-5 m-b-35">
            {{ Lang::get('administrable::messages.default.add') }}
        </h3>
        <div class="card-body p-0">
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
