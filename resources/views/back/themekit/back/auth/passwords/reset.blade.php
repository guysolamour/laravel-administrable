@extends(back_view_path('layouts.app'))

@section('title', Lang::get('administrable::messages.view.auth.passwordupdate'))

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
                    <a href="#"><img src="{{ configuration('logo') }}" onerror="this.src='{{ config('administrable.logo_url') }}'" alt="{{ config('app.name') }}" width='200'></a>
                </div>
                @if (session('status'))
                <div class="alert alert-success" role="alert">
                    {{ session('status') }}
                </div>
                @endif
                <form action="{{ route(config('administrable.guard') . '.password.update') }}" method="post">
                    <input type="hidden" name="token" value="{{ $token }}">
                    @csrf
                    @honeypot
                    <div class="form-group">
                        <input type="text" class="form-control @error('email') is-invalid @enderror" id="email" name="email"
                            value="{{ $email ?? old('email') }}" placeholder="{{ Lang::get('administrable::messages.view.auth.email') }}" required autocomplete="email">
                            <i class="ik ik-mail"></i>
                        @error('email')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                    </div>
                    <div class="form-group">
                        <input class="form-control {{ $errors->has('password') ? ' is-invalid' : '' }}" type="password" name="password"
                            id="password" placeholder={{ Lang::get('administrable::messages.view.auth.password') }}" required>
                        <i class="ik ik-lock"></i>
                        @if ($errors->has('password'))
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('password') }}</strong>
                        </span>
                        @endif
                    </div>
                    <div class="form-group">
                        <input class="form-control" type="password" name="password_confirmation" id="password_confirmation"
                            placeholder="{{ Lang::get('administrable::messages.view.auth.passwordconfirmation') }}" required>
                        <i class="ik ik-lock"></i>
                    </div>

                    <div class="sign-btn text-center">
                        <button class="btn btn-theme" type="submit">{{ Lang::get('administrable::messages.view.auth.reset') }}</button>
                    </div>
                </form>
        </div>
    </div>
</div>
</div>
@endsection

