@extends(back_view_path('layouts.base'))

@section('title', Lang::get('administrable::messages.view.guard.plural'))

@section('content')
<div class="main-content">
    <div class="card ">
        <nav aria-label="breadcrumb" class="d-flex justify-content-end" style="padding-top:10px;padding-right:20px;">
            <ol class="breadcrumb breadcrumb-arrow">
                <li class="breadcrumb-item"><a href="{{ route(config('administrable.guard') . '.dashboard') }}">{{ Lang::get("administrable::messages.default.dashboard") }}</a></li>
                <li class="breadcrumb-item active" aria-current="page"><a href="#">{{ Lang::get('administrable::messages.view.guard.plural') }}</a></li>
            </ol>
        </nav>

    </div>

  <div class="row">
      @foreach ($guards as $guard)
        <div class="col-12 col-md-6 col-lg-4 col-xl-3">
            <div class="card card-body">
                <div class="flexbox align-items-center">
                    <label class="toggler toggler-yellow fs-16">
                        <input type="checkbox" checked>
                        <i class="fa fa-star"></i>
                    </label>
                    <div class="dropdown">
                        <a data-toggle="dropdown" href="#" aria-expanded="false"><i
                                class="ti-more-alt rotate-90 text-muted"></i></a>
                        <div class="dropdown-menu dropdown-menu-right">
                            <a class="dropdown-item" href="{{ back_route(config('administrable.guard') . '.profile', $guard) }}"><i class="fa fa-fw fa-user"></i> {{ Lang::get('administrable::messages.view.guard.profil') }}</a>

                            @if (get_guard()->can('create-' . config('administrable.guard')))
                            <div class="dropdown-divider"></div>
                              <a class="dropdown-item" href="{{ back_route(config('administrable.guard') . '.delete', $guard) }}" data-method="delete"
                                  data-confirm="{{ Lang::get('administrable::messages.view.guard.destroy') }}">
                                  <i class="fa fa-fw fa-remove"></i> {{ Lang::get('administrable::messages.default.delete') }}</a>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="text-center pt-3">
                    <a href="#">
                        <img class="avatar avatar-xxl" src="{{ $guard->avatar }}">
                    </a>
                    <h5 class="mt-3 mb-0"><a class="hover-primary">{{ $guard->full_name }}</a></h5>
                    <span>{{ $guard->role }}</span>
                </div>
            </div>
        </div>
        @endforeach
    </div>


</div>
@endsection
