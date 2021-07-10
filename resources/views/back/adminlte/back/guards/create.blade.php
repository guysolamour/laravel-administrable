@extends(back_view_path('layouts.base'))

@section('title', Lang::get('administrable::messages.default.add'))

@section('content')

     <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>{{ Lang::get('administrable::messages.default.add') }}</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route(config('administrable.guard') . '.dashboard') }}">{{ Lang::get("administrable::messages.default.dashboard") }}</a></li>
                            <li class="breadcrumb-item"><a href="{{ back_route(config('administrable.guard') . '.index') }}">{{ Lang::get('administrable::messages.view.guard.plural') }}</a></li>
                            <li class="breadcrumb-item active">{{ Lang::get('administrable::messages.default.add') }}</li>
                        </ol>
                    </div>
                </div>
            </div><!-- /.container-fluid -->
        </section>

        <!-- Main content -->
        <section class="content">

            <!-- Default box -->
            <div class="card">
                <div class="card-header">
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse" data-toggle="tooltip">
                            <i class="fas fa-minus"></i></button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-8">
                          @include(back_view_path('guards._form'))
                        </div>
                        <div class="col-4">
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
            <!-- /.card -->

        </section>
        <!-- /.content -->
    </div>
@stop
