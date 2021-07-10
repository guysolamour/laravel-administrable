@extends(back_view_path('layouts.base'))

@section('title', Lang::get('administrable::messages.view.guard.plural'))

@section('content')
    <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>{{ Lang::get('administrable::messages.view.guard.plural') }}</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="{{ route(config('administrable.guard') . '.dashboard') }}">{{ Lang::get("administrable::messages.default.dashboard") }}</a></li>
              <li class="breadcrumb-item active">{{ Lang::get('administrable::messages.view.guard.plural') }}</li>
            </ol>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content">
      <!-- Default box -->
      <div class="card card-solid">
        <div class="card-body pb-0">
          <div class="row d-flex align-items-stretch">
            @foreach ($guards as $guard)
            <div class="col-12 col-sm-6 col-md-4 d-flex align-items-stretch">
              <div class="card bg-light">
                <div class="card-header text-muted border-bottom-0">
                  {{ $guard->role }}
                </div>
                <div class="card-body pt-0">
                  <div class="row">
                    <div class="col-7">
                      <h2 class="lead"><b>{{ $guard->full_name }}</b></h2>
                      <p class="text-muted text-sm"><b>{{ Lang::get('administrable::messages.view.guard.about') }}: </b> {{ Str::limit($guard->about, 25) }} </p>
                      <ul class="ml-4 mb-0 fa-ul text-muted">
                        <li class="small"><span class="fa-li"><i class="fas fa-lg fa-phone"></i></span> {{ Lang::get('administrable::messages.view.guard.telephone') }} #: {{ $guard->phone_number }}</li>
                      </ul>
                    </div>
                    <div class="col-5 text-center">
                      <img src="{{ $guard->getFrontImageUrl() }}" alt="{{ $guard->full_name }}" class="img-circle img-fluid">
                    </div>
                  </div>
                </div>
                <div class="card-footer">
                  <div class="text-right">
                     @if (get_guard()->can('delete-' . config('administrable.guard'), $guard))
                      <a href="{{ back_route( config('administrable.guard') . '.delete', $guard) }}" data-method="delete" data-confirm="{{ Lang::get('administrable::messages.view.guard.destroy') }}" class="btn btn-danger btn-sm">
                        <i class="fas fa-trash"></i> {{ Lang::get('administrable::messages.default.delete') }}
                      </a>
                    @endif
                    <a href="{{ back_route( config('administrable.guard') .'.profile', $guard) }}" class="btn btn-sm btn-primary">
                      <i class="fas fa-user"></i>  {{ Lang::get('administrable::messages.view.guard.profil') }}
                    </a>
                  </div>
                </div>
              </div>
            </div>
            @endforeach
          </div>
        </div>

      </div>
      <!-- /.card -->

    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->
@endsection
