@extends(back_view_path('layouts.app'))

@section('title', Lang::get('administrable::messages.view.auth.login'))

@section('content')

<div class="container-tight py-6">
    <div class="text-center mb-4">
        <img src="{{ configuration('logo') }}" onerror="this.src='{{ config('administrable.logo_url') }}'" height="36" alt="{{ config('administrable.name')}}">
    </div>
    <form class="card card-md" action="{{ route(config('administrable.guard') . '.login') }}" method="post">
        <div class="card-body">
            @csrf
            @honeypot
            <h2 class="mb-5 text-center">{{ Lang::get('administrable::messages.view.auth.login') }}</h2>
            <div class="form-group">
                <label>{{ Lang::get('administrable::messages.view.guard.pseudo') }}  - {{ Lang::get('administrable::messages.view.guard.email') }} </label>
                <input
                    class="form-control {{ $errors->has('login') || $errors->has('email') || $errors->has('pseudo') ? 'is-invalid' : '' }}"
                    type="text" name="login" id="login" placeholder="Pseudo ou Email" value="{{ old('login') }}" required>
                @if ($errors->has('login') || $errors->has('email') || $errors->has('pseudo'))
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $errors->first('login') ?: $errors->first('email') ?: $errors->first('pseudo')  }}</strong>
                </span>
                @endif
            </div>
            <div class="form-group">
                <label>{{ Lang::get('administrable::messages.view.auth.password') }}</label>
                <input class="form-control {{ $errors->has('password') ? ' is-invalid' : '' }}"
                    type="password" name="password" id="password" placeholder="{{ Lang::get('administrable::messages.view.auth.password') }}" required>
                @if ($errors->has('password'))
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $errors->first('password') }}</strong>
                </span>
                @endif
            </div>

            <div class="form-group">
                <label class="form-check">
                    <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                    <span class="form-check-label">
                       {{ Lang::get('administrable::messages.view.auth.remember') }}
                    </span>
                </label>
            </div>
            <div class="form-footer">
                <button type="submit" class="btn btn-primary btn-block">{{ Lang::get('administrable::messages.view.auth.login') }}</button>
            </div>
        </div>
    </form>
    <div class="text-center text-muted">
        <a href="{{ route(config('administrable.guard') . '.password.request') }}"> {{ Lang::get('administrable::messages.view.auth.forgotpassword') }}</a>
    </div>
</div>
@endsection
