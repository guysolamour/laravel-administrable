@extends(back_view_path('layouts.base'))

@section('title', Lang::get('administrable::messages.view.guard.plural'))

@section('content')
<div class="row mb-5">
    <div class="col-12">
        <div class="d-flex justify-content-between">
            <ol class="breadcrumb breadcrumb-arrows" aria-label="breadcrumbs">
                <li class="breadcrumb-item"><a href="{{ route(config('administrable.guard') . '.dashboard') }}">{{ Lang::get("administrable::messages.default.dashboard") }}</a></li>
                <li class="breadcrumb-item active" aria-current="page"><a href="#">{{ Lang::get('administrable::messages.view.guard.plural') }}</a></li>

            </ol>
            @if (get_guard()->can('create-' . config('administrable.guard')))
            <a href="{{ back_route(config('administrable.guard') . '.create') }}" class="btn btn-success">
                <i class="fa fa-plus"></i>&nbsp; {{ Lang::get('administrable::messages.view.guard.add') }}</a>
            @endif
        </div>
    </div>
</div>

<div class="row">
    @foreach ($guards as $guard)
    <div class="col-12 col-md-6 col-lg-4 col-xl-3">
        <div class="card">
            <div class="card-body text-center">
                <div class="mb-3">
                    <span class="avatar avatar-xl" style="background-image: url({{ $guard->avatar }})"></span>
                </div>
                <div class="card-title mb-1">
                    <a href="{{ back_route(config('administrable.guard') . '.profile', $guard) }}">{{ $guard->full_name }}</a>
                </div>
                <div class="text-muted">{{ $guard->role }}</div>
            </div>
            <a href="{{ back_route(config('administrable.guard') . '.profile', $guard) }}" class="card-btn">
                    {{ Lang::get('administrable::messages.view.guard.profil') }}
            </a>
        </div>
    </div>
    @endforeach
</div>
@endsection


