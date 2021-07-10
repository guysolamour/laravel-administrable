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
                <h3>{{ Lang::get('administrable::messages.view.auth.passwordupdate') }}</h3>
                @if (session('status'))
                <div class="alert alert-success" role="alert">
                    {{ session('status') }}
                </div>
                @endif
                <form action="{{ route(config('administrable.guard') .'.password.email') }}" method="post">
                    @csrf
                    @honeypot
                    <div class="form-group">
                        <input class="form-control {{ $errors->has('email') ? 'is-invalid' : '' }}" type="text" name="email" id="email"
                            required placeholder="{{ Lang::get('administrable::messages.view.auth.email') }}" value="{{ old('email') }}">
                            <i class="ik ik-mail"></i>
                        @if ($errors->has('email'))
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('email') }}</strong>
                        </span>
                        @endif
                    </div>

                    <div class="sign-btn text-center">
                        <button class="btn btn-theme" type="submit">{{ Lang::get('administrable::messages.view.auth.sendupdatelink') }}</button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>

@endsection
