@extends(back_view_path('layouts.app'))

@section('title', Lang::get('administrable::messages.view.auth.login'))

@section('content')
<div class="container-fluid h-100">
    <div class="row flex-row h-100 bg-white">
        <div class="col-xl-8 col-lg-6 col-md-5 p-0 d-md-block d-lg-block d-sm-none d-none">
            <div class="lavalite-bg" style="background-image: url('/vendor/themekit/img/auth/login-bg.jpg')">
                <div class="lavalite-overlay"></div>
            </div>
        </div>
        <div class="col-xl-4 col-lg-6 col-md-7 my-auto p-0">
            <div class="authentication-form mx-auto">
                <div class="logo-centered text-center" style="width: auto">
                <a href="#">
                  <img src="{{ configuration('logo') }}" onerror="this.src='{{ config('administrable.logo_url') }}'" alt="{{ config('app.name') }}" width='200'>
                </a>
                </div>
                <h3>{{ Lang::get('administrable::messages.view.auth.login') }}</h3>
                <form action="{{ route(config('administrable.guard') . '.login') }}" method="post">
                    @csrf
                    @honeypot
                    <div class="form-group">
                        <input
                            class="form-control {{ $errors->has('login') || $errors->has('email') || $errors->has('pseudo') ? 'is-invalid' : '' }}"
                            type="text" name="login" id="login" placeholder="{{ Lang::get('administrable::messages.view.guard.pseudo') }}  - {{ Lang::get('administrable::messages.view.guard.email') }}" value="{{ old('login') }}" required>
                            <i class="ik ik-user"></i>
                        @if ($errors->has('login') || $errors->has('email') || $errors->has('pseudo'))
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('login') ?: $errors->first('email') ?: $errors->first('pseudo')  }}</strong>
                        </span>
                        @endif
                    </div>
                    <div class="form-group">
                        <input class="form-control {{ $errors->has('password') ? ' is-invalid' : '' }}" type="password" name="password"
                            id="password" placeholder="{{ Lang::get('administrable::messages.view.auth.password') }}" required>
                            <i class="ik ik-lock"></i>
                        @if ($errors->has('password'))
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('password') }}</strong>
                        </span>
                        @endif
                    </div>

                    <div class="row">
                        <div class="col text-left">
                            <label class="custom-control custom-checkbox">
                                <input class="custom-control-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>

                                <span class="custom-control-label">&nbsp;{{ Lang::get('administrable::messages.view.auth.remember') }}</span>
                            </label>
                        </div>
                        <div class="col text-right">
                            <a href="{{ route(config('administrable.guard') . '.password.request') }}">{{ Lang::get('administrable::messages.view.auth.forgotpassword') }}</a>
                        </div>
                    </div>
                    <div class="sign-btn text-center">
                        <button class="btn btn-theme" type="submit">{{ Lang::get('administrable::messages.view.auth.login') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection
