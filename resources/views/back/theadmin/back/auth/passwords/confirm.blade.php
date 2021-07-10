@extends(back_view_path('layouts.app'))

@section('title', Lang::get('administrable::messages.view.auth.passwordconfirmation'))

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ Lang::get('administrable::messages.view.auth.passwordconfirmation') }}</div>

                <div class="card-body">
                    <form method="POST" action="{{ route(config('administrable.guard') . '.password.confirm') }}">
                        @csrf
                        @honeypot
                        <div class="form-group row">
                            <label for="password" class="col-md-4 col-form-label text-md-right">
                                {{ Lang::get('administrable::messages.view.auth.password') }}
                            </label>

                            <div class="col-md-6">
                                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password">

                                @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row mb-0">
                            <div class="col-md-8 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                     {{  Lang::get('administrable::messages.view.auth.confirmpassword') }}
                                </button>

                                @if (Route::has(config('administrable.guard') . '.password.request'))
                                    <a class="btn btn-link" href="{{ route(config('administrable.guard') . '.password.request') }}">
                                        {{  Lang::get('administrable::messages.view.auth.forgotpassword') }}
                                    </a>
                                @endif
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
