@extends(back_view_path('layouts.app'))

@section('title', Lang::get('administrable::messages.view.auth.passwordupdate'))

@section('content')

<div class="container-tight py-6">
    <div class="text-center mb-4">
        <img src="{{ configuration('logo') }}" onerror="this.src='{{ config('administrable.logo_url') }}'"height="36" alt="{{ config('administrable.name')}}">
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
                value="{{ $email ?? old('email') }}" required autocomplete="email">
            <label for="email">{{ Lang::get('administrable::messages.view.auth.email') }}</label>
            @error('email')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
            @enderror
        </div>

        <div class="form-group">
            <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" required
                name="password" required>
            <label for="email">{{ Lang::get('administrable::messages.view.auth.password') }}</label>
            @error('password')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
            @enderror
        </div>

        <div class="form-group">
            <input type="password" class="form-control @error('password_confirmation') is-invalid @enderror"
                id="password_confirmation" required name="password_confirmation" required>
            <label for="password_confirmation">{{ Lang::get('administrable::messages.view.auth.passwordconfirmation') }}</label>
        </div>
        <div class="form-footer">
            <button type="submit" class="btn btn-primary btn-block">{{ Lang::get('administrable::messages.view.auth.reset') }}</button>
        </div>
    </form>
</div>
@endsection

