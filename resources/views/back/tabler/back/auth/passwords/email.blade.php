@extends(back_view_path('layouts.app'))

@section('title', Lang::get('administrable::messages.view.auth.passwordupdate'))

@section('content')
<div class="container-tight py-6">
    <div class="text-center mb-4">
        <img src="{{ configuration('logo') }}" onerror="this.src='{{ config('administrable.logo_url') }}'" height="36" alt="{{ config('administrable.name')}}">
    </div>
    @if (session('status'))
        <div class="alert alert-success" role="alert">
            {{ session('status') }}
        </div>
    @endif
   <form action="{{ route(config('administrable.guard') .'.password.email') }}" method="post">
        <div class="card-body">
            @csrf
            @honeypot
            <div class="form-group">
                <label>{{ Lang::get('administrable::messages.view.auth.email') }} </label>

                <input class="form-control {{ $errors->has('email') ? 'is-invalid' : '' }}" type="text"
                    name="email" id="email" required placeholder="{{ Lang::get('administrable::messages.view.auth.email') }}" value="{{ old('email') }}">
                @if ($errors->has('email'))
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $errors->first('email') }}</strong>
                </span>
                @endif
            </div>

            <div class="form-footer">
                <button type="submit" class="btn btn-primary btn-block">
                    {{ Lang::get('administrable::messages.view.auth.sendupdatelink') }}
                </button>
            </div>
        </div>
    </form>
</div>
@endsection
