@extends(back_view_path('layouts.app'))

@section('title', Lang::get('administrable::messages.view.auth.passwordconfirmation'))

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
                Merci de confirmer votre mot de passe avant de continuer
                <form  action="{{ route(config('administrable.guard') . '.password.confirm') }}" method="post">
                    @csrf
                    @honeypot
                   <div class="form-group">
                        <input
                            id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password"
                            required autocomplete="current-password">
                            <i class="ik ik-lock"></i>
                        @error('password')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                    </div>

                    <div class="sign-btn text-center">
                        <button class="btn btn-theme" type="submit">
                            {{  Lang::get('administrable::messages.view.auth.confirmpassword') }}
                        </button>
                    </div>

                    @if (Route::has(config('administrable.guard') . '.password.request'))
                    <a class="btn btn-success" href="{{ route(config('administrable.guard') . 'password.request') }}">
                        {{ Lang::get('administrable::messages.view.auth.forgotpassword') }}
                    </a>
                    @endif
                </form>
        </div>
    </div>
</div>
</div>

@endsection
