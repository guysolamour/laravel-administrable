@extends(back_view_path('layouts.app'))

@section('title', Lang::get('administrable::messages.view.auth.login'))

@section('content')

<div class="row min-h-fullscreen center-vh p-20 m-0">
    <div class="col-12">
        <div class="card card-shadowed px-50 py-30 w-400px mx-auto" style="max-width: 100%">
            <h5 class="text-uppercase">{{ Lang::get('administrable::messages.view.auth.login') }}</h5>
            <br>

            <form class="form-type-material" action="{{ route(config('administrable.guard') . '.login') }}" method="post">
                @csrf
                @honeypot
                <div class="form-group">
                    <input type="text" class="form-control {{ $errors->has('login') || $errors->has('email') || $errors->has('pseudo') ? 'is-invalid' : '' }}" id="login" name="login" value="{{ old('login') }}">
                    <label for="login">{{ Lang::get('administrable::messages.view.guard.pseudo') }}  - {{ Lang::get('administrable::messages.view.guard.email') }}</label>
                    @if ($errors->has('login') || $errors->has('email') || $errors->has('pseudo'))
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('login') ?: $errors->first('email') ?: $errors->first('pseudo')  }}</strong>
                        </span>
                    @endif
                </div>

                <div class="form-group">
                    <input type="password" class="form-control {{ $errors->has('password') ? ' is-invalid' : '' }}" id="password" name="password">
                    <label for="password">{{ Lang::get('administrable::messages.view.auth.password') }}</label>
                    @if ($errors->has('password'))
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $errors->first('password') }}</strong>
                    </span>
                    @endif
                </div>

                <div class="form-group flexbox flex-column flex-md-row">
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                        <label class="custom-control-label">{{ Lang::get('administrable::messages.view.auth.remember') }}</label>
                    </div>

                    <a class="text-muted hover-primary fs-13 mt-2 mt-md-0" href="{{ route(config('administrable.guard') . '.password.request') }}">{{ Lang::get('administrable::messages.view.auth.forgotpassword') }}</a>
                </div>

                <div class="form-group">
                    <button class="btn btn-bold btn-block btn-primary" type="submit">{{ Lang::get('administrable::messages.view.auth.login') }}</button>
                </div>
            </form>

            {{-- <div class="divider">Or Sign In With</div>
            <div class="text-center">
                <a class="btn btn-square btn-facebook" href="#"><i class="fa fa-facebook"></i></a>
                <a class="btn btn-square btn-google" href="#"><i class="fa fa-google"></i></a>
                <a class="btn btn-square btn-twitter" href="#"><i class="fa fa-twitter"></i></a>
            </div> --}}
        </div>
        {{-- <p class="text-center text-muted fs-13 mt-20">Don't have an account? <a class="text-primary fw-500"
                href="#">Sign up</a></p> --}}
    </div>


    <footer class="col-12 align-self-end text-center fs-13">
        <p class="mb-0"><small>Copyright © {{ date('Y') }} <a href="https://aswebagency.com">AswebAgency</a>.
             {{ Lang::get("All rights reserved.") }}</small></p>
    </footer>
</div>
@endsection
