@extends(back_view_path('layouts.app'))

@section('title', Lang::get('administrable::messages.view.auth.emailverification'))

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    {{ Lang::get('administrable::messages.view.auth.emailverification') }}
                </div>

                <div class="card-body">
                    @if (session('resent'))
                        <div class="alert alert-success" role="alert">
                            {{ Lang::get('administrable::messages.view.auth.newverificationlink') }}
                        </div>
                    @endif
                    {{ Lang::get('administrable::messages.view.auth.emailverificationlink') }}
                    <form class="d-inline" method="POST" action="{{ route(config('administrable.guard') . '.verification.resend') }}">
                        @csrf
                        @honeypot
                        <button type="submit" class="btn btn-link p-0 m-0 align-baseline">
                            {{ Lang::get('administrable::messages.view.auth.sendanotheremaillink') }}
                        </button>.
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
