@extends(back_view_path('layouts.base'))

@section('title', Lang::get('administrable::messages.default.add'))

@section('content')
<div class="row mb-5">
    <div class="col-12">
        <div class="d-flex justify-content-between">
            <ol class="breadcrumb breadcrumb-arrows" aria-label="breadcrumbs">
                <li class="breadcrumb-item"><a href="{{ route(config('administrable.guard') . '.dashboard') }}">{{ Lang::get("administrable::messages.default.dashboard") }}</a></li>
                <li class="breadcrumb-item"><a href="{{ back_route('user.index') }}">{{ Lang::get('administrable::messages.view.user.plural') }}</a></li>
                <li class="breadcrumb-item active">{{ Lang::get('administrable::messages.default.add') }}</li>
            </ol>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-12">
        <h3 class="title-5 m-b-35">
            {{ Lang::get('administrable::messages.default.add') }}
        </h3>
        <div class="row">
            <div class="col-md-8">
               @include(back_view_path('users._form'))
            </div>
            <div class="col-md-4">
                @imagemanager([
                    'model'             =>  new (AdminModule::getUserModel()),
                    'label'             =>  Lang::get('administrable::messages.view.user.avatar'),
                    'type'              =>  'image',
                    'collection'        => 'front-image',
                ])
            </div>
        </div>
    </div>
</div>
@endsection
