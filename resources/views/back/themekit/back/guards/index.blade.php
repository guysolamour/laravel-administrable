@extends(back_view_path('layouts.base'))

@section('title', Lang::get('administrable::messages.view.guard.plural'))

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
                           <li class="breadcrumb-item"><a href="{{ route(config('administrable.guard') . '.dashboard') }}">{{ Lang::get("administrable::messages.default.dashboard") }}</a></li>
                            <li class="breadcrumb-item active" aria-current="page"><a href="#">{{ Lang::get('administrable::messages.view.guard.plural') }}</a></li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <h3 class="card-title">{{ Str::plural(config('administrable.guard')) }}</h3>
                        @if (get_guard()->can('create-' . config('administrable.guard')))
                        <div class="btn-group float-right">
                            <a href="{{ back_route(config('administrable.guard') . '.create') }}" class="btn btn-success">
                                <i class="fa fa-plus"></i>&nbsp; {{ Lang::get('administrable::messages.default.add') }}</a>
                        </div>
                        @endif
                    </div>

                    <div class="card-body row">
                       @foreach ($guards as $guard)
                        <div class="col-12 col-md-6 col-lg-4 col-xl-3">
                            <div class="card">
                                <div class="card-body text-center">
                                    <div class="profile-pic mb-20">
                                        <img src="{{ $guard->avatar }}" width="150" class="rounded-circle" alt="user">
                                        <h4 class="mt-20 mb-0">{{ $guard->full_name }}</h4>
                                        <a href="#">{{ $guard->email }}</a>
                                    </div>
                                </div>
                                <div class="p-4 border-top mt-15">
                                    <div class="row text-center">
                                        @if (get_guard()->can('delete-' . config('administrable.guard'), $guard))
                                        <div class="col-6 border-right">
                                            <a href="{{ back_route( config('administrable.guard') . '.profile', $guard) }}"
                                                class="link d-flex align-items-center justify-content-center"><i class="ik ik-message-square f-20 mr-5"></i>
                                                {{ Lang::get('administrable::messages.view.guard.profil') }}
                                            </a>
                                        </div>
                                        <div class="col-6">
                                            <a href="{{ back_route( config('administrable.guard') . '.delete', $guard) }}" class="link d-flex align-items-center justify-content-center text-danger"
                                            data-method="delete" data-confirm="{{ Lang::get('administrable::messages.view.guard.destroy') }}">
                                            <i class="ik ik-box f-20 mr-5 "></i>{{ Lang::get('administrable::messages.default.delete') }}</a>
                                        </div>
                                        @else
                                        <div class="col-12">
                                            <a href="{{ back_route( config('administrable.guard') .'.profile', $guard) }}"
                                                class="link d-flex align-items-center justify-content-center"><i class="ik ik-message-square f-20 mr-5"></i>
                                                {{ Lang::get('administrable::messages.view.guard.profil') }}
                                            </a>
                                        </div>
                                        @endif
                                    </div>
                                </div>

                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection


