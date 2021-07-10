@extends(back_view_path('layouts.app'))


@section('title', Lang::get('administrable::messages.view.auth.passwordupdate'))

@section('content')
   <div class="register-box">
        <div class="register-logo">
          <img src="{{ configuration('logo') }}" onerror="this.src='{{ config('administrable.logo_url') }}'" alt="{{ config('administrable.name')}} Logo" class="brand-image" width='200'>
        </div>

        <div class="card">
            <div class="card-body register-card-body">
                <p class="login-box-msg">{{ Lang::get('administrable::messages.view.auth.passwordupdate') }}</p>
                @if (session('status'))
                    <div class="alert alert-success" role="alert">
                        {{ session('status') }}
                    </div>
                @endif

                <form action="{{ route( config('administrable.guard') . '.password.update') }}" method="post">
                        <input type="hidden" name="token" value="{{ $token }}">
                    @csrf
                    @honeypot
                    <div class="input-group mb-3">
                        <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ $email ?? old('email') }}" required autocomplete="email">
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-envelope"></span>
                            </div>
                        </div>
                        @error('email')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror

                    </div>
                    <div class="input-group mb-3">
                            <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="new-password" placeholder="Mot de passe">
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-lock"></span>
                            </div>
                        </div>
                        @error('password')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror

                    </div>
                    <div class="input-group mb-3">
                            <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required autocomplete="new-password"  placeholder="Confirmation mot de passe">
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-lock"></span>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary btn-block btn-flat">{{ Lang::get('administrable::messages.view.auth.reset') }}</button>
                        </div>
                    </div>
                </form>
            </div>
        </div><!-- /.card -->
    </div>
@endsection

