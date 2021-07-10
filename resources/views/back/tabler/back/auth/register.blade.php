@extends(back_view_path('layouts.app'))

@section('title', Lang::get('administrable::messages.view.auth.inscription'))

@section('content')

<div class="container-tight py-6">
    <div class="text-center mb-4">
        <img src="{{ configuration('logo') }}" onerror="this.src='{{ config('administrable.logo_url') }}'" height="36" alt="{{ config('administrable.name')}}">
    </div>
    <form class="card card-md" action="{{ route(config('administrable.guard') .'.login') }}" method="post">
        <div class="card-body">
            @csrf
            @honeypot
            <h2 class="mb-5 text-center">{{ Lang::get('administrable::messages.view.auth.inscription') }}</h2>

            <div class="form-group">
                <label>{{ Lang::get('administrable::messages.view.guard.pseudo') }} </label>

                <input class="form-control {{ $errors->has('pseudo') ? ' is-invalid' : '' }}" type="text"
                    name="pseudo" id="pseudo" placeholder="{{ Lang::get('administrable::messages.view.guard.pseudo') }}" value="{{ old('pseudo') }}" required>
                @if ($errors->has('pseudo'))
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $errors->first('pseudo') }}</strong>
                </span>
                @endif
            </div>
            <div class="form-group">
                <label>{{ Lang::get('administrable::messages.view.guard.lastname') }} </label>

                <input class="form-control {{ $errors->has('last_name') ? ' is-invalid' : '' }}" type="text"
                    name="last_name" id="last_name" placeholder="{{ Lang::get('administrable::messages.view.guard.lastname') }}" value="{{ old('last_name') }}" required>
                @if ($errors->has('last_name'))
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $errors->first('last_name') }}</strong>
                </span>
                @endif
            </div>
            <div class="form-group">
                <label>{{ Lang::get('administrable::messages.view.guard.firstname') }}</label>

                <input class="form-control {{ $errors->has('first_name') ? ' is-invalid' : '' }}"
                    type="text" name="first_name" id="first_name" placeholder="{{ Lang::get('administrable::messages.view.guard.firstname') }}" value="{{ old('first_name') }}" required>
                @if ($errors->has('first_name'))
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $errors->first('first_name') }}</strong>
                </span>
                @endif
            </div>
            <div class="form-group">
                <label>{{ Lang::get('administrable::messages.view.guard.email') }} </label>

                <input class="form-control {{ $errors->has('email') ? ' is-invalid' : '' }}" type="text"
                    name="email" id="email" placeholder="{{ Lang::get('administrable::messages.view.guard.email') }}" value="{{ old('email') }}" required>
                @if ($errors->has('email'))
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $errors->first('email') }}</strong>
                </span>
                @endif
            </div>
            <div class="form-group">
                <label>{{ Lang::get('administrable::messages.view.guard.password') }}</label>
                <input class="form-control {{ $errors->has('password') ? ' is-invalid' : '' }}"
                    type="password" name="password" id="password" placeholder="{{ Lang::get('administrable::messages.view.guard.password') }}" required>
                @if ($errors->has('password'))
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $errors->first('password') }}</strong>
                </span>
                @endif
            </div>
            <div class="form-group">
                <label>{{ Lang::get('administrable::messages.view.guard.passwordconfirmation') }}</label>
                <input class="form-control" type="password" name="password_confirmation"
                    id="password_confirmation" placeholder="{{ Lang::get('administrable::messages.view.guard.passwordconfirmation') }}" required>

            </div>

            <div class="form-footer">
                <button type="submit" class="btn btn-primary btn-block">
                    {{ Lang::get('administrable::messages.view.auth.register') }}
                </button>
            </div>
        </div>
    </form>
    <div class="text-center text-muted">
        <a  href="{{ route(config('administrable.guard') . '.login') }}">{{ Lang::get('administrable::messages.view.auth.startsession') }}</a>
    </div>
</div>

@endsection
