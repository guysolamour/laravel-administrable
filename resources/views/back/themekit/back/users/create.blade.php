@extends(back_view_path('layouts.base'))

@section('title', Lang::get('administrable::messages.default.add'))

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
                            <li class="breadcrumb-item">
                                <a href="{{ back_route('user.index') }}">{{ Lang::get('administrable::messages.view.user.plural') }}</a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page"><a href="#">{{ Lang::get('administrable::messages.default.add') }}</a></li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <h3 class="card-title">{{ Lang::get('administrable::messages.default.add') }}</h3>
                    </div>

                    <div class="card-body">
                        <div class="row">
                            <div class='col-md-8'>
                                @include(back_view_path('users._form'))
                            </div>
                            <div class='col-md-4'>
                                @include(back_view_path('media._imagemanager'), [
                                    'front_image_label'      => Lang::get('administrable::messages.view.user.avatar'),
                                    'model'                  => new (AdminModule::getUserModel()),
                                    'front_image'            => true,
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