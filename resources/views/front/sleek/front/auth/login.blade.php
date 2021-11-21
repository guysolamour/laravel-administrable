@extends('front.layouts.auth')

@section('content')
<div class="container d-flex flex-column justify-content-between vh-100">
    <div class="row justify-content-center mt-5">
        <div class="col-xl-5 col-lg-6 col-md-10">
            <div class="card">
                <div class="card-header bg-primary">
                    <div class="app-brand">
                        <a href="{{ route('home') }}">
                            <svg class="brand-icon" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="xMidYMid" width="30" height="33"
                            viewBox="0 0 30 33">
                            <g fill="none" fill-rule="evenodd">
                                <path class="logo-fill-blue" fill="#7DBCFF" d="M0 4v25l8 4V0zM22 4v25l8 4V0z" />
                                <path class="logo-fill-white" fill="#FFF" d="M11 4v25l8 4V0z" />
                            </g>
                        </svg>
                        <span class="brand-name">{{ config('app.name') }}</span>
                    </a>
                </div>
            </div>
            <div class="card-body p-5">

                <h4 class="text-dark mb-5">Connectez-vous</h4>
                @error('email')
                <div class="alert alert-danger">
                    {{ $message }}
                </div>
                @enderror

                <form action="{{ route('login') }}" method="POST">
                    @csrf
                    @honeypot
                    <div class="row">
                        <div class="form-group col-md-12 mb-4">
                            <input type="login" value="{{ old('login') }}"  name="login" class="form-control input-lg @error('login') is-invalid @enderror" id="login" aria-describedby="loginHelp" placeholder="Pseudo ou Email" required autofocus>
                            @error('login')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>
                        <div class="form-group col-md-12 ">
                            <input type="password" name="password" class="form-control input-lg @error('password') is-invalid @enderror" id="password" placeholder="Mot de passe" required>
                            @error('password')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>
                        <div class="col-md-12">
                            <div class="d-flex my-2 justify-content-between">
                                <div class="d-inline-block mr-3">
                                    <label class="control control-checkbox"> Se souvenir de moi
                                        <input type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }} />
                                        <div class="control-indicator"></div>
                                    </label>

                                </div>
                                @if (Route::has('password.request'))
                                <p><a class="text-blue" href="{{ route('password.request') }}"> Mot de passe oublié ?</a></p>
                                @endif
                            </div>
                            <button type="submit" class="btn btn-lg btn-primary btn-block mb-4">Connexion</button>
                            <p>Vous n'avez pas encore de compte ?
                                <a class="text-blue" href="{{ route('register') }}">Inscrivez vous</a>
                            </p>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

