@extends(back_view_path('layouts.app'))

@section('title', Lang::get('administrable::messages.view.auth.passwordconfirmation'))

@section('content')

<div class="container-tight py-6">
    <div class="text-center mb-4">
        <img src="{{ configuration('logo') }}" onerror="this.src='{{ config('administrable.logo_url') }}'" height="36" alt="{{ config('administrable.name')}}">
    </div>
    <form class="card card-md" action="{{ route(config('administrable.guard') . '.password.confirm') }}" method="post">
        <div class="card-body">
            @csrf
            @honeypot
            <div class="form-group">
                <label for="password" class="form-control col-md-4 col-form-label text-md-right">
                    {{ Lang::get('administrable::messages.view.auth.password') }}
                </label>
                <input id="password" type="password"
                        class="form-control @error('password') is-invalid @enderror" name="password"
                        required autocomplete="current-password">
                @error('password')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div>

            <button class="btn btn-success" type="submit">
                {{  Lang::get('administrable::messages.view.auth.confirmpassword') }}
            </button>

            @if (Route::has(config('administrable.guard') . '.password.request'))
            <a class="btn btn-success" href="{{ route(config('administrable.guard') . '.password.request') }}">
               {{  Lang::get('administrable::messages.view.auth.forgotpassword') }}
            </a>
            @endif

        </div>
    </form>
</div>
@endsection
