@extends(back_view_path('layouts.app'))

@section('title', Lang::get('administrable::messages.view.auth.inscription'))

@section('content')
  <div class="register-box">
        <div class="register-logo">
            <img src="{{ configuration('logo') }}" onerror="this.src='{{ config('administrable.logo_url') }}'" alt="{{ config('administrable.name')}} Logo" class="brand-image" width='200'>
        </div>

        <div class="card">
            <div class="card-body register-card-body">
                <p class="login-box-msg">{{ Lang::get('administrable::messages.view.auth.inscription') }}</p>

                <form action="{{ route( config('administrable.guard') . '.register') }}" method="post" aria-label="{{ __('Register') }}">
                    @csrf
                    @honeypot
                    <div class="input-group mb-3">
                        <input name="pseudo" type="text" class="form-control {{ $errors->has('pseudo') ? ' is-invalid' : '' }}" placeholder="{{ Lang::get('administrable::messages.view.guard.pseudo') }}" value="{{ old('pseudo') }}">
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-user"></span>
                            </div>
                        </div>
                        @if ($errors->has('pseudo'))
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('pseudo') }}</strong>
                            </span>
                        @endif
                    </div>
                        <div class="input-group mb-3">
                            <input name="last_name" type="text" class="form-control {{ $errors->has('last_name') ? ' is-invalid' : '' }}" placeholder="{{ Lang::get('administrable::messages.view.guard.lastname') }}" value="{{ old('last_name') }}">
                            <div class="input-group-append">
                                <div class="input-group-text">
                                    <span class="fas fa-user"></span>
                                </div>
                            </div>
                            @if ($errors->has('last_name'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('last_name') }}</strong>
                                </span>
                            @endif
                        </div>
                    <div class="input-group mb-3">
                        <input name="first_name" type="text" class="form-control {{ $errors->has('first_name') ? ' is-invalid' : '' }}" placeholder="{{ Lang::get('administrable::messages.view.guard.firstname') }}" value="{{ old('first_name') }}">
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-user"></span>
                            </div>
                        </div>
                        @if ($errors->has('first_name'))
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('first_name') }}</strong>
                            </span>
                        @endif
                    </div>
                    <div class="input-group mb-3">
                        <input name="email" type="email" class="form-control {{ $errors->has('email') ? ' is-invalid' : '' }}" placeholder="{{ Lang::get('administrable::messages.view.guard.email') }}" value="{{ old('email') }}">
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-envelope"></span>
                            </div>
                        </div>
                        @if ($errors->has('email'))
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('email') }}</strong>
                            </span>
                        @endif
                    </div>
                    <div class="input-group mb-3">
                        <input name="password" type="password" class="form-control" placeholder="{{ Lang::get('administrable::messages.view.guard.password') }}">
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-lock"></span>
                            </div>
                        </div>
                        @if ($errors->has('password'))
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('password') }}</strong>
                            </span>
                        @endif

                    </div>
                    <div class="input-group mb-3">
                        <input name="password_confirmation" type="password" class="form-control" placeholder="{{ Lang::get('administrable::messages.view.guard.passwordconfirmation') }}">
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-lock"></span>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <!-- /.col -->
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary btn-block btn-flat">
                                {{ Lang::get('administrable::messages.view.auth.register') }}
                            </button>
                        </div>
                        <!-- /.col -->
                    </div>
                </form>

                <a href="{{ route( config('administrable.guard') .'.login') }}" class="text-center">
                    {{ Lang::get('administrable::messages.view.auth.startsession') }}
                </a>
            </div>
            <!-- /.form-box -->
        </div><!-- /.card -->
    </div>
@endsection
