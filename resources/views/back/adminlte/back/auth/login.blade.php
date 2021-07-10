@extends(back_view_path('layouts.app'))

@section('title', Lang::get('administrable::messages.view.auth.login'))

@section('content')
    <div class="login-box">
        <div class="login-logo">
          <img src="{{ configuration('logo') }}" onerror="this.src='{{ config('administrable.logo_url') }}'" alt="{{ config('administrable.name')}} Logo" class="brand-image" width='200'>
        </div>
        <!-- /.login-logo -->
        <div class="card">
            <div class="card-body login-card-body">
                <p class="login-box-msg">{{ Lang::get('administrable::messages.view.auth.startsession') }}</p>

                <form action="{{ route( config('administrable.guard') . '.login') }}" method="post">
                    @csrf
                    @honeypot
                    <div class="input-group mb-3">
                        <input type="text" name="login" class="form-control {{ $errors->has('login') || $errors->has('email') || $errors->has('pseudo') ? 'is-invalid' : '' }}" placeholder="{{ Lang::get('administrable::messages.view.guard.pseudo') }} - {{ Lang::get('administrable::messages.view.guard.email') }}" value="{{ old('login') }}">
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-envelope"></span>
                            </div>
                        </div>
                        @if ($errors->has('login') || $errors->has('email') || $errors->has('pseudo'))
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('login') ?: $errors->first('email') ?: $errors->first('pseudo')  }}</strong>
                            </span>
                        @endif
                    </div>
                    <div class="input-group mb-3">
                        <input type="password" name="password" class="form-control {{ $errors->has('password') ? ' is-invalid' : '' }}" placeholder="{{ Lang::get('administrable::messages.view.guard.password') }}">
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
                    <div class="row">
                        <div class="col-12 py-2">
                            <div class="icheck-primary">
                                <input type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                                <label for="remember">
                                    {{ Lang::get('administrable::messages.view.auth.remember') }}
                                </label>
                            </div>
                        </div>
                        <!-- /.col -->
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary btn-block btn-flat">
                                {{ Lang::get('administrable::messages.view.auth.login') }}
                            </button>
                        </div>
                        <!-- /.col -->
                    </div>
                </form>

                <p class="mb-2">
                    <a href="{{ route( config('administrable.guard') . '.password.request') }}">
                        {{ Lang::get('administrable::messages.view.auth.forgotpassword') }}
                    </a>
                </p>

            </div>
            <!-- /.login-card-body -->
        </div>
    </div>
@endsection
