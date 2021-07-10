@extends(back_view_path('layouts.app'))

@section('title', Lang::get('administrable::messages.view.auth.inscription'))

@section('content')
<div class="container-fluid h-100">
    <div class="row flex-row h-100 bg-white">
        <div class="col-xl-8 col-lg-6 col-md-5 p-0 d-md-block d-lg-block d-sm-none d-none">
            <div class="lavalite-bg" style="background-image: url('/vendor/themekit/img/auth/register-bg.jpg')">
                <div class="lavalite-overlay"></div>
            </div>
        </div>
        <div class="col-xl-4 col-lg-6 col-md-7 my-auto p-0">
            <div class="authentication-form mx-auto">
                <div class="logo-centered text-center" style="width: auto">
                    <a href="#"><img src="{{ configuration('logo') }}" onerror="this.src='{{ config('administrable.logo_url') }}'" alt="{{ config('app.name') }}" width='200'></a>
                </div>
                <h3>{{ Lang::get('administrable::messages.view.auth.inscription') }}</h3>
                <form class="card card-md" action="{{ route(config('administrable.guard') .'.login') }}" method="post">
                    @csrf
                    @honeypot
                    <div class="form-group">
                        <input class="form-control {{ $errors->has('pseudo') ? ' is-invalid' : '' }}" type="text" name="pseudo" id="pseudo"
                            placeholder="{{ Lang::get('administrable::messages.view.guard.pseudo') }}" value="{{ old('pseudo') }}" required>
                            <i class="ik ik-user"></i>
                        @if ($errors->has('pseudo'))
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('pseudo') }}</strong>
                        </span>
                        @endif
                    </div>
                    <div class="form-group">
                        <input class="form-control {{ $errors->has('last_name') ? ' is-invalid' : '' }}" type="text" name="last_name"
                            id="last_name" placeholder="{{ Lang::get('administrable::messages.view.guard.lastname') }}" value="{{ old('last_name') }}" required>
                            <i class="ik ik-user"></i>
                        @if ($errors->has('last_name'))
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('last_name') }}</strong>
                        </span>
                        @endif
                    </div>
                    <div class="form-group">
                        <input class="form-control {{ $errors->has('first_name') ? ' is-invalid' : '' }}" type="text" name="first_name"
                            id="first_name" placeholder="{{ Lang::get('administrable::messages.view.guard.firstname') }}" value="{{ old('first_name') }}" required>
                        <i class="ik ik-user"></i>
                        @if ($errors->has('first_name'))
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('first_name') }}</strong>
                        </span>
                        @endif
                    </div>
                    <div class="form-group">
                        <input class="form-control {{ $errors->has('email') ? ' is-invalid' : '' }}" type="text" name="email" id="email"
                            placeholder="{{ Lang::get('administrable::messages.view.guard.email') }}" value="{{ old('email') }}" required>
                            <i class="ik ik-user"></i>
                        @if ($errors->has('email'))
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('email') }}</strong>
                        </span>
                        @endif
                    </div>
                    <div class="form-group">
                        <input class="form-control {{ $errors->has('password') ? ' is-invalid' : '' }}" type="password" name="password"
                            id="password" placeholder="{{ Lang::get('administrable::messages.view.guard.password') }}" required>
                            <i class="ik ik-lock"></i>
                        @if ($errors->has('password'))
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('password') }}</strong>
                        </span>
                        @endif
                    </div>
                    <div class="form-group">
                        <input class="form-control" type="password" name="password_confirmation" id="password_confirmation"
                            placeholder="{{ Lang::get('administrable::messages.view.guard.passwordconfirmation') }}" required>
                            <i class="ik ik-lock"></i>
                    </div>

                    <div class="sign-btn text-center">
                        <button class="btn btn-theme" type="submit">
                            {{ Lang::get('administrable::messages.view.auth.register') }}
                        </button>
                    </div>
                </form>
                <div class="register">
                    <p><a href="{{ route(config('administrable.guard') . '.login') }}">{{ Lang::get('administrable::messages.view.auth.startsession') }}</a></p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
