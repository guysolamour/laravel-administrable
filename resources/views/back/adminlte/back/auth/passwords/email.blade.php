@extends(back_view_path('layouts.app'))


@section('title', Lang::get('administrable::messages.view.auth.passwordupdate'))

@section('content')
   <div class="login-box">
        <div class="login-logo">
            <img src="{{ configuration('logo') }}" onerror="this.src='{{ config('administrable.logo_url') }}'" alt="{{ config('administrable.name')}} Logo" class="brand-image" width='200'>
        </div>
        <!-- /.login-logo -->
        <div class="card">
            <div class="card-body login-card-body">
                <p class="login-box-msg">{{ Lang::get('administrable::messages.view.auth.passwordupdate') }}</p>
                @if (session('status'))
                    <div class="alert alert-success" role="alert">
                        {{ session('status') }}
                    </div>
                @endif

                <form action="{{ route( config('administrable.guard') . '.password.email') }}" method="post">
                    @csrf
                    @honeypot
                    <div class="input-group mb-3">

                        <input type="email" name="email" class="form-control {{ $errors->has('email') ? ' is-invalid' : '' }}" placeholder="{{ Lang::get('administrable::messages.view.auth.email') }}" value="{{ old('email') }}">
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

                    <div class="row">
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary btn-block btn-flat">{{ Lang::get('administrable::messages.view.auth.sendupdatelink') }}</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
