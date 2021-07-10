@extends(back_view_path('layouts.app'))

@section('title', Lang::get('administrable::messages.view.auth.emailverification'))

@section('content')

<div class="container-tight py-6">
    <div class="text-center mb-4">
        <img src="{{ configuration('logo') }}" onerror="this.src='{{ config('administrable.logo_url') }}'" height="36" alt="{{ config('administrable.name')}}">
    </div>
    <div class="card-body">
        @if (session('resent'))
        <div class="alert alert-success" role="alert">
            {{ Lang::get('administrable::messages.view.auth.newverificationlink') }}
        </div>
        @endif
        {{ Lang::get('administrable::messages.view.auth.emailverificationlink') }}
        <form class="card card-md" method="POST" action="{{ route(config('administrable.guard') . '.verification.resend') }}">
          <div class="card-body">
            @csrf
            @honeypot
            <button type="submit" class="btn btn-primary btn-block">
                {{ Lang::get('administrable::messages.view.auth.sendanotheremaillink') }}
            </button>
          </div>
        </form>
    </div>
</div>
@endsection
